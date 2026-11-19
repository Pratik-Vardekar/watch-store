<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesCsv;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    use HandlesCsv;

    private function subcategoryExportHeaders(): array
    {
        return ['Category', 'Name', 'Slug', 'Status'];
    }
   public function index(Request $request)
{
    $query = SubCategory::with('category');

    // Search - NOW INCLUDES CATEGORY NAME SEARCH
    if ($request->search) {

        $query->where(function ($q) use ($request) {

            $q->where('name', 'LIKE', '%' . $request->search . '%')
              ->orWhere('slug', 'LIKE', '%' . $request->search . '%')
              ->orWhereHas('category', function($catQuery) use ($request) {
                  $catQuery->where('name', 'LIKE', '%' . $request->search . '%');
              });

        });
    }

    // Category Filter
    if ($request->category_id) {

        $query->where('category_id', $request->category_id);
    }

    // Status Filter
    if ($request->status !== null && $request->status !== '') {

        $query->where('status', $request->status);
    }

    // Date Filter
    if ($request->date) {

        $query->whereDate('created_at', $request->date);
    }

    // Changed pagination from 30 to 10
    $subcategories = $query
        ->latest()
        ->paginate(10);

    $categories = Category::where('status', 1)->get();

    return view(
        'admin.subcategory.index',
        compact('subcategories', 'categories')
    );
}

public function export(Request $request)
{
    $query = SubCategory::with('category');

    // Search - NOW INCLUDES CATEGORY NAME SEARCH FOR EXPORT AS WELL
    if ($request->search) {

        $query->where(function ($q) use ($request) {

            $q->where('name', 'LIKE', '%' . $request->search . '%')
              ->orWhere('slug', 'LIKE', '%' . $request->search . '%')
              ->orWhereHas('category', function($catQuery) use ($request) {
                  $catQuery->where('name', 'LIKE', '%' . $request->search . '%');
              });

        });
    }

    // Category Filter
    if ($request->category_id) {

        $query->where('category_id', $request->category_id);
    }

    // Status Filter
    if ($request->status !== null && $request->status !== '') {

        $query->where('status', $request->status);
    }

    // Date Filter
    if ($request->date) {

        $query->whereDate('created_at', $request->date);
    }

    $subcategories = $query->latest()->get();

    $fileName = 'subcategories.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$fileName",
    ];

    $callback = function () use ($subcategories) {

        $file = fopen('php://output', 'w');

        fputcsv($file, [
            'ID',
            'Category',
            'Name',
            'Slug',
            'Status',
            'Created At'
        ]);

        foreach ($subcategories as $subcategory) {

            fputcsv($file, [
                $subcategory->id,
                $subcategory->category->name ?? '-',
                $subcategory->name,
                $subcategory->slug,
                $subcategory->status == 1 ? 'Active' : 'Inactive',
                $subcategory->created_at->format('d-m-Y'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function importSample()
{
    return $this->streamCsvDownload('subcategories_import_sample.csv', $this->subcategoryExportHeaders(), function ($file) {
        fputcsv($file, ['Seeds', 'Vegetable Seeds', 'vegetable-seeds', 'Active']);
        fputcsv($file, ['Seeds', 'Flower Seeds', 'flower-seeds', 'Active']);
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
            $categoryName = $this->csvValue($row, 'category');
            $name = $this->csvValue($row, 'name');

            if (!$categoryName || !$name) {
                throw new \RuntimeException("Row {$rowNumber}: Category and Name are required.");
            }

            $category = Category::where(function ($q) use ($categoryName) {
                $q->where('name', $categoryName)
                    ->orWhere('slug', Str::slug($categoryName));
            })->first();

            if (!$category) {
                throw new \RuntimeException("Row {$rowNumber}: category '{$categoryName}' not found.");
            }

            $slug = $this->csvValue($row, 'slug') ?: Str::slug($name);
            $status = $this->normalizeStatusValue($this->csvValue($row, 'status'), 1);

            $existing = SubCategory::where('slug', $slug)->first();
            if ($existing) {
                $existing->update([
                    'category_id' => $category->id,
                    'name' => $name,
                    'status' => $status,
                ]);
                $updated++;
            } else {
                SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => $slug,
                    'status' => $status,
                ]);
                $imported++;
            }
        } catch (\Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }

    return redirect()
        ->route('subcategory.index')
        ->with('success', "Import complete: {$imported} created, {$updated} updated.")
        ->with('import_errors', $errors);
}

    public function create(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        
        // Pass the current page and filters to the create view
        return view('admin.subcategory.add', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name'        => 'required',
            'slug'        => 'required|unique:sub_categories,slug',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status'      => 'required'
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {

            $imageName = time() . '.' . $request->image->extension();

            $request->image->move(
                base_path('backend/assets/images/subcategory'),
                $imageName
            );
        }

        SubCategory::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->slug),
            'image'       => $imageName,
            'status'      => $request->status,
        ]);

        // Build query parameters to preserve page and filters
        $queryParams = [];
        if ($request->page) $queryParams['page'] = $request->page;
        if ($request->search) $queryParams['search'] = $request->search;
        if ($request->category_id) $queryParams['category_id'] = $request->category_id;
        if ($request->status) $queryParams['status'] = $request->status;
        if ($request->date) $queryParams['date'] = $request->date;

        return redirect()
            ->route('subcategory.index', $queryParams)
            ->with('success', 'Sub Category Added Successfully');
    }

    public function edit(SubCategory $subcategory, Request $request)
    {
        $categories = Category::where('status', 1)->get();

        return view(
            'admin.subcategory.edit',
            compact('subcategory', 'categories')
        );
    }

    public function update(Request $request, SubCategory $subcategory)
    {
        $request->validate([
            'category_id' => 'required',
            'name'        => 'required',
            'slug'        => 'required|unique:sub_categories,slug,' . $subcategory->id,
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status'      => 'required'
        ]);

        $imageName = $subcategory->image;

        if ($request->hasFile('image')) {

            if (
                $subcategory->image &&
                file_exists(base_path('backend/assets/images/subcategory/' . $subcategory->image))
            ) {
                unlink(base_path('backend/assets/images/subcategory/' . $subcategory->image));
            }

            $imageName = time() . '.' . $request->image->extension();

            $request->image->move(
                base_path('backend/assets/images/subcategory'),
                $imageName
            );
        }

        $subcategory->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->slug),
            'image'       => $imageName,
            'status'      => $request->status,
        ]);

        // Build query parameters to preserve page and filters
        $queryParams = [];
        if ($request->page) $queryParams['page'] = $request->page;
        if ($request->search) $queryParams['search'] = $request->search;
        if ($request->category_id) $queryParams['category_id'] = $request->category_id;
        if ($request->status) $queryParams['status'] = $request->status;
        if ($request->date) $queryParams['date'] = $request->date;

        return redirect()
            ->route('subcategory.index', $queryParams)
            ->with('success', 'Sub Category Updated Successfully');
    }

    public function destroy(SubCategory $subcategory, Request $request)
    {
        if (
            $subcategory->image &&
            file_exists(base_path('backend/assets/images/subcategory/' . $subcategory->image))
        ) {
            unlink(base_path('backend/assets/images/subcategory/' . $subcategory->image));
        }

        $subcategory->delete();

        // Build query parameters to preserve page and filters
        $queryParams = [];
        if ($request->page) $queryParams['page'] = $request->page;
        if ($request->search) $queryParams['search'] = $request->search;
        if ($request->category_id) $queryParams['category_id'] = $request->category_id;
        if ($request->status) $queryParams['status'] = $request->status;
        if ($request->date) $queryParams['date'] = $request->date;

        return redirect()
            ->route('subcategory.index', $queryParams)
            ->with('success', 'Sub Category Deleted Successfully');
    }
    
    public function status($id, Request $request)
    {
        $subcategory = SubCategory::findOrFail($id);

        $subcategory->status = $subcategory->status == 1 ? 0 : 1;

        $subcategory->save();

        // Build query parameters to preserve page and filters
        $queryParams = [];
        if ($request->page) $queryParams['page'] = $request->page;
        if ($request->search) $queryParams['search'] = $request->search;
        if ($request->category_id) $queryParams['category_id'] = $request->category_id;
        if ($request->status) $queryParams['status'] = $request->status;
        if ($request->date) $queryParams['date'] = $request->date;

        return redirect()
            ->route('subcategory.index', $queryParams)
            ->with('success', 'Sub category status updated successfully');
    }
}