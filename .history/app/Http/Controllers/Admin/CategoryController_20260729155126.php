<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesCsv;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use HandlesCsv;

    private function categoryExportHeaders(): array
    {
        // Add Description to headers
        return ['Name', 'Slug', 'Description', 'Status', 'Order'];
    }

    private function buildCategoryQuery(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('slug', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $this->buildCategoryQuery($request)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10);

        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:categories,slug',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status'      => 'required|in:0,1',
            'gender'      => 'nullable|in:women,men,both',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
        ], [
            'slug.unique' => 'Category slug already exists',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(
                public_path('backend/assets/images/category'),
                $imageName
            );
        }

        Category::create([
            'name'        => $request->name,
            'slug'        => $request->slug ?? Str::slug($request->name),
            'image'       => $imageName,
            'status'      => $request->status,
            'gender'      => $request->input('gender', 'both') ?: 'both',
            'description' => $request->description,
            'sort_order'  => $request->sort_order ?? ((Category::max('sort_order') ?? 0) + 1),
        ]);

        return redirect()
            ->route('category.index')
            ->with('success', 'Category added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('admin.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status'      => 'required|in:0,1',
            'gender'      => 'nullable|in:women,men,both',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
        ], [
            'slug.unique' => 'Category slug already exists',
        ]);

        $imageName = $category->image;

        if ($request->hasFile('image')) {
            if ($category->image && file_exists(public_path('backend/assets/images/category/' . $category->image))) {
                unlink(public_path('backend/assets/images/category/' . $category->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(
                public_path('backend/assets/images/category'),
                $imageName
            );
        }

        $category->update([
            'name'        => $request->name,
            'slug'        => $request->slug ?? Str::slug($request->name),
            'image'       => $imageName,
            'status'      => $request->status,
            'gender'      => $request->input('gender', 'both') ?: 'both',
            'description' => $request->description,
            'sort_order'  => $request->sort_order ?? $category->sort_order,
        ]);

        return redirect()
            ->route('category.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()
                ->route('category.index')
                ->with('error', 'Cannot delete category with associated products. Please delete or reassign products first.');
        }

        if ($category->image && file_exists(public_path('backend/assets/images/category/' . $category->image))) {
            unlink(public_path('backend/assets/images/category/' . $category->image));
        }

        $category->delete();

        return redirect()
            ->route('category.index')
            ->with('success', 'Category deleted successfully');
    }

    /**
     * Toggle category status.
     */
    public function status($id)
    {
        $category = Category::findOrFail($id);
        $category->status = $category->status == 1 ? 0 : 1;
        $category->save();

        return redirect()->back()
            ->with('success', 'Category status updated successfully');
    }

    /**
     * Update category display order (drag & drop).
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:categories,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->orders as $item) {
            Category::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category order updated successfully',
        ]);
    }

    /**
     * Export categories to CSV.
     */
    public function export(Request $request)
    {
        $categories = $this->buildCategoryQuery($request)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $fileName = 'categories.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () use ($categories) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for special characters
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID',
                'Name',
                'Slug',
                'Description',
                'Products Count',
                'Status',
                'Order',
                'Created At'
            ]);

            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->id,
                    $category->name,
                    $category->slug,
                    $category->description,
                    $category->products_count,
                    $category->status == 1 ? 'Active' : 'Inactive',
                    $category->sort_order,
                    $category->created_at->format('d-m-Y H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importSample()
    {
        return $this->streamCsvDownload('categories_import_sample.csv', $this->categoryExportHeaders(), function ($file) {
            fputcsv($file, ['Seeds', 'seeds', 'This is a seed category', 'Active', '1']);
            fputcsv($file, ['Fertilizers', 'fertilizers', 'This is a fertilizers category', 'Active', '2']);
        });
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $parsed = $this->parseCsvFile($request->file('file'));

        if (empty($parsed['rows'])) {
            return back()->with('error', 'CSV file is empty or invalid.');
        }

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($parsed['rows'] as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $name = $this->csvValue($row, 'name');
                if (!$name) {
                    throw new \RuntimeException("Row {$rowNumber}: Name is required.");
                }

                $slug = $this->csvValue($row, 'slug') ?: Str::slug($name);
                $description = $this->csvValue($row, 'description');
                $status = $this->normalizeStatusValue($this->csvValue($row, 'status'), 1);
                $sortOrder = $this->csvValue($row, 'order') ?? $this->csvValue($row, 'sort_order');
                $sortOrder = is_numeric($sortOrder) ? (int) $sortOrder : null;

                $existing = Category::where('slug', $slug)->first();
                if ($existing) {
                    $existing->update([
                        'name' => $name,
                        'description' => $description,
                        'status' => $status,
                        'sort_order' => $sortOrder ?? $existing->sort_order,
                    ]);
                    $updated++;
                } else {
                    Category::create([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                        'status' => $status,
                        'sort_order' => $sortOrder ?? ((Category::max('sort_order') ?? 0) + 1),
                    ]);
                    $imported++;
                }
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }

        return redirect()
            ->route('category.index')
            ->with('success', "Import complete: {$imported} created, {$updated} updated.")
            ->with('import_errors', $errors);
    }
}