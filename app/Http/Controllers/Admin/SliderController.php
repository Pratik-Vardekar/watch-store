<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SliderController extends Controller
{
    /**
     * Ensure database columns exist dynamically in case migrations haven't run on production
     */
    protected function ensureSliderColumns(): void
    {
        try {
            $cols = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `sliders`");
            $existing = [];
            foreach ($cols as $col) {
                $f = $col->Field ?? $col->field ?? null;
                if ($f) {
                    $existing[strtolower($f)] = true;
                }
            }

            if (!isset($existing['mobile_image'])) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `mobile_image` VARCHAR(255) NULL AFTER `image`");
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `mobile_image` VARCHAR(255) NULL");
                }
            }

            if (!isset($existing['sort_order'])) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `sort_order` INT NOT NULL DEFAULT 0 AFTER `status`");
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `sort_order` INT NOT NULL DEFAULT 0");
                }
            }

            if (!isset($existing['subtitle'])) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `subtitle` VARCHAR(255) NULL AFTER `title`");
                } catch (\Throwable $e) {}
            }

            if (!isset($existing['link'])) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `link` TEXT NULL AFTER `description`");
                } catch (\Throwable $e) {}
            }
        } catch (\Throwable $e) {
            // Silently continue
        }
    }

    public function index(Request $request)
    {
        $this->ensureSliderColumns();

        $query = Slider::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('subtitle', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Date Filter
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $sliders = $query->orderBy('sort_order', 'asc')->latest()->paginate(10);

        return view('admin.slider.index', compact('sliders'));
    }

    public function create()
    {
        $this->ensureSliderColumns();

        return view('admin.slider.add');
    }

    public function store(Request $request)
    {
        $this->ensureSliderColumns();

        $request->validate([
            'title'        => 'nullable|string|max:255',
            'subtitle'     => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'link'         => 'nullable|url',
            'sort_order'   => 'nullable|integer',
            'image'        => 'required|image|mimes:jpg,jpeg,png,webp,svg',
            'mobile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'status'       => 'required',
        ]);

        $publicDir = public_path('backend/assets/images/slider');
        $baseDir = base_path('backend/assets/images/slider');

        if (!file_exists($publicDir)) {
            @mkdir($publicDir, 0777, true);
        }
        if (!file_exists($baseDir)) {
            @mkdir($baseDir, 0777, true);
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . rand(111, 999) . '.' . $image->extension();
            $image->move($publicDir, $imageName);

            if ($baseDir !== $publicDir) {
                @copy($publicDir . '/' . $imageName, $baseDir . '/' . $imageName);
            }
        }

        $mobileImageName = null;
        if ($request->hasFile('mobile_image')) {
            $mobileImage = $request->file('mobile_image');
            $mobileImageName = 'mob_' . time() . rand(111, 999) . '.' . $mobileImage->extension();
            $mobileImage->move($publicDir, $mobileImageName);

            if ($baseDir !== $publicDir) {
                @copy($publicDir . '/' . $mobileImageName, $baseDir . '/' . $mobileImageName);
            }
        }

        $data = [
            'title'        => $request->title,
            'subtitle'     => $request->subtitle,
            'description'  => $request->description,
            'link'         => $request->link,
            'sort_order'   => $request->sort_order ?? 0,
            'image'        => $imageName,
            'status'       => $request->status,
        ];

        try {
            $hasCol = !empty(\Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `sliders` LIKE 'mobile_image'"));
            if (!$hasCol) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `mobile_image` VARCHAR(255) NULL AFTER `image`");
                } catch (\Throwable $e2) {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `mobile_image` VARCHAR(255) NULL");
                }
                $hasCol = true;
            }
            if ($hasCol) {
                $data['mobile_image'] = $mobileImageName;
            }
        } catch (\Throwable $e) {
            $data['mobile_image'] = $mobileImageName;
        }

        Slider::create($data);

        return redirect()
            ->route('slider.index')
            ->with('success', 'Slider Added Successfully');
    }

    public function edit(Slider $slider)
    {
        $this->ensureSliderColumns();

        return view('admin.slider.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $this->ensureSliderColumns();

        $request->validate([
            'title'        => 'nullable|string|max:255',
            'subtitle'     => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'link'         => 'nullable|url',
            'sort_order'   => 'nullable|integer',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'mobile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'status'       => 'required',
        ]);

        $publicDir = public_path('backend/assets/images/slider');
        $baseDir = base_path('backend/assets/images/slider');

        if (!file_exists($publicDir)) {
            @mkdir($publicDir, 0777, true);
        }
        if (!file_exists($baseDir)) {
            @mkdir($baseDir, 0777, true);
        }

        $imageName = $slider->image;

        if ($request->hasFile('image')) {
            if ($slider->image) {
                @unlink($publicDir . '/' . $slider->image);
                @unlink($baseDir . '/' . $slider->image);
            }

            $image = $request->file('image');
            $imageName = time() . rand(111, 999) . '.' . $image->extension();
            $image->move($publicDir, $imageName);

            if ($baseDir !== $publicDir) {
                @copy($publicDir . '/' . $imageName, $baseDir . '/' . $imageName);
            }
        }

        $mobileImageName = $slider->mobile_image;

        if ($request->hasFile('mobile_image')) {
            if ($slider->mobile_image) {
                @unlink($publicDir . '/' . $slider->mobile_image);
                @unlink($baseDir . '/' . $slider->mobile_image);
            }

            $mobileImage = $request->file('mobile_image');
            $mobileImageName = 'mob_' . time() . rand(111, 999) . '.' . $mobileImage->extension();
            $mobileImage->move($publicDir, $mobileImageName);

            if ($baseDir !== $publicDir) {
                @copy($publicDir . '/' . $mobileImageName, $baseDir . '/' . $mobileImageName);
            }
        }

        $data = [
            'title'        => $request->title,
            'subtitle'     => $request->subtitle,
            'description'  => $request->description,
            'link'         => $request->link,
            'sort_order'   => $request->sort_order ?? 0,
            'image'        => $imageName,
            'status'       => $request->status,
        ];

        try {
            $hasCol = !empty(\Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `sliders` LIKE 'mobile_image'"));
            if (!$hasCol) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `mobile_image` VARCHAR(255) NULL AFTER `image`");
                } catch (\Throwable $e2) {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sliders` ADD `mobile_image` VARCHAR(255) NULL");
                }
                $hasCol = true;
            }
            if ($hasCol) {
                $data['mobile_image'] = $mobileImageName;
            }
        } catch (\Throwable $e) {
            $data['mobile_image'] = $mobileImageName;
        }

        $slider->update($data);

        return redirect()
            ->route('slider.index')
            ->with('success', 'Slider Updated Successfully');
    }

    public function destroy(Slider $slider)
    {
        $publicDir = public_path('backend/assets/images/slider');
        $baseDir = base_path('backend/assets/images/slider');

        if ($slider->image) {
            @unlink($publicDir . '/' . $slider->image);
            @unlink($baseDir . '/' . $slider->image);
        }

        if ($slider->mobile_image) {
            @unlink($publicDir . '/' . $slider->mobile_image);
            @unlink($baseDir . '/' . $slider->mobile_image);
        }

        $slider->delete();

        return redirect()
            ->route('slider.index')
            ->with('success', 'Slider Deleted Successfully');
    }

    public function toggleStatus(Slider $slider)
    {
        $slider->status = $slider->status == 1 ? 0 : 1;

        $slider->save();

        return back()->with('success', 'Slider status updated successfully');
    }

    public function export(Request $request)
    {
        $this->ensureSliderColumns();

        $query = Slider::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('subtitle', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Date Filter
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $sliders = $query->orderBy('sort_order', 'asc')->latest()->get();

        $fileName = 'sliders.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () use ($sliders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Title',
                'Subtitle',
                'Description',
                'Sort Order',
                'Desktop Image',
                'Mobile Image',
                'Status',
                'Created At'
            ]);

            foreach ($sliders as $slider) {
                fputcsv($file, [
                    $slider->id,
                    $slider->title ?? '-',
                    $slider->subtitle ?? '-',
                    $slider->description ?? '-',
                    $slider->sort_order ?? 0,
                    $slider->image,
                    $slider->mobile_image ?? '-',
                    $slider->status == 1 ? 'Active' : 'Inactive',
                    $slider->created_at->format('d-m-Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
