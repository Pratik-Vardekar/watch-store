<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesCsv;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    use HandlesCsv;

    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('link', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $brands = $query->withCount('products')->ordered()->get();
        return view('admin.brand.index', compact('brands'));
    }

    public function export(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('link', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $brands = $query->withCount('products')->ordered()->get();
        $fileName = 'brands_' . date('Y-m-d_His') . '.csv';

        $headers = ['ID', 'Brand Name', 'Products Count', 'Banner', 'Description', 'Status', 'Created At'];

        return $this->streamCsvDownload($fileName, $headers, function ($file) use ($brands) {
            foreach ($brands as $brand) {
                fputcsv($file, [
                    $brand->id,
                    $brand->name,
                    $brand->products_count ?? 0,
                    $brand->banner ? 'Yes' : 'No',
                    $brand->description ?? '',
                    $brand->status == 1 ? 'Active' : 'Inactive',
                    $brand->created_at ? $brand->created_at->format('d-m-Y H:i:s') : '',
                ]);
            }
        });
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'banner'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'description' => 'nullable|string|max:1000',
            'link'        => 'nullable|url|max:512',
            'status'      => 'required|boolean',
        ]);

        $baseDir = base_path('backend/assets/images/brands');
        $publicDir = public_path('backend/assets/images/brands');
        foreach ([$baseDir, $publicDir] as $dir) {
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
                @chmod($dir, 0755);
            }
        }

        $logo = '';
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $logo = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move($baseDir, $logo);
            @chmod($baseDir . '/' . $logo, 0644);
            if ($publicDir !== $baseDir) {
                @copy($baseDir . '/' . $logo, $publicDir . '/' . $logo);
                @chmod($publicDir . '/' . $logo, 0644);
            }
        }

        $banner = '';
        if ($request->hasFile('banner')) {
            $bFile = $request->file('banner');
            $banner = 'banner_' . time() . '_' . Str::slug($request->name) . '.' . $bFile->getClientOriginalExtension();
            $bFile->move($baseDir, $banner);
            @chmod($baseDir . '/' . $banner, 0644);
            if ($publicDir !== $baseDir) {
                @copy($baseDir . '/' . $banner, $publicDir . '/' . $banner);
                @chmod($publicDir . '/' . $banner, 0644);
            }
        }

        $nextPosition = (int) Brand::max('position') + 1;
        $slugInput = $request->filled('slug') ? $request->slug : $request->name;

        self::ensureBannerColumnExists();

        Brand::create([
            'name'        => $request->name,
            'slug'        => Brand::generateUniqueSlug($slugInput),
            'logo'        => $logo,
            'banner'      => $banner ?: null,
            'description' => $request->description,
            'link'        => $request->link,
            'position'    => $nextPosition,
            'status'      => $request->status,
        ]);

        return redirect()->route('brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brand.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'banner'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'description' => 'nullable|string|max:1000',
            'link'        => 'nullable|url|max:512',
            'status'      => 'required|boolean',
        ]);

        $baseDir = base_path('backend/assets/images/brands');
        $publicDir = public_path('backend/assets/images/brands');
        foreach ([$baseDir, $publicDir] as $dir) {
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
                @chmod($dir, 0755);
            }
        }

        $logo = $brand->logo;
        if ($request->hasFile('logo')) {
            // Delete old logo
            foreach ([$baseDir, $publicDir] as $d) {
                if ($brand->logo && file_exists($d . '/' . $brand->logo)) {
                    @unlink($d . '/' . $brand->logo);
                }
            }

            $file = $request->file('logo');
            $logo = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move($baseDir, $logo);
            @chmod($baseDir . '/' . $logo, 0644);
            if ($publicDir !== $baseDir) {
                @copy($baseDir . '/' . $logo, $publicDir . '/' . $logo);
                @chmod($publicDir . '/' . $logo, 0644);
            }
        }

        $banner = $brand->banner;
        if ($request->hasFile('banner')) {
            // Delete old banner
            foreach ([$baseDir, $publicDir] as $d) {
                if ($brand->banner && file_exists($d . '/' . $brand->banner)) {
                    @unlink($d . '/' . $brand->banner);
                }
            }

            $bFile = $request->file('banner');
            $banner = 'banner_' . time() . '_' . Str::slug($request->name) . '.' . $bFile->getClientOriginalExtension();
            $bFile->move($baseDir, $banner);
            @chmod($baseDir . '/' . $banner, 0644);
            if ($publicDir !== $baseDir) {
                @copy($baseDir . '/' . $banner, $publicDir . '/' . $banner);
                @chmod($publicDir . '/' . $banner, 0644);
            }
        } elseif ($request->boolean('remove_banner')) {
            foreach ([$baseDir, $publicDir] as $d) {
                if ($brand->banner && file_exists($d . '/' . $brand->banner)) {
                    @unlink($d . '/' . $brand->banner);
                }
            }
            $banner = null;
        }

        $slugInput = $request->filled('slug')
            ? $request->slug
            : ($brand->name !== $request->name || empty($brand->slug) ? $request->name : $brand->slug);

        $slug = Brand::generateUniqueSlug($slugInput, $brand->id);

        self::ensureBannerColumnExists();

        $brand->update([
            'name'        => $request->name,
            'slug'        => $slug,
            'logo'        => $logo,
            'banner'      => $banner,
            'description' => $request->description,
            'link'        => $request->link,
            'status'      => $request->status,
        ]);

        return redirect()->route('brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $targetDir = base_path('backend/assets/images/brands');

        // Delete logo file
        if ($brand->logo && file_exists($targetDir . '/' . $brand->logo)) {
            @unlink($targetDir . '/' . $brand->logo);
        }

        // Delete banner file
        if (!empty($brand->banner) && file_exists($targetDir . '/' . $brand->banner)) {
            @unlink($targetDir . '/' . $brand->banner);
        }

        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    public static function ensureBannerColumnExists(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('brands') && !\Illuminate\Support\Facades\Schema::hasColumn('brands', 'banner')) {
                \Illuminate\Support\Facades\Schema::table('brands', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('banner')->nullable()->after('logo');
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders'            => 'required|array',
            'orders.*.id'       => 'required|integer|exists:brands,id',
            'orders.*.position' => 'required|integer|min:1',
        ]);

        foreach ($request->orders as $item) {
            Brand::where('id', $item['id'])->update([
                'position' => $item['position'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Brand order updated successfully',
        ]);
    }
}
