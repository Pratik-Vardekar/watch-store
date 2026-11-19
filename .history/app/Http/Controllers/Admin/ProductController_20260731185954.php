<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesCsv;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\GSTSetting;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductFaq;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HandlesCsv;

    /**
     * Persist discount dynamically from MRP vs selling price (backend JS may also send it).
     */
    private function resolveProductDiscount(Request $request): int
    {
        return Product::calculateDiscountPercent(
            (float) ($request->price ?? 0),
            (float) ($request->selling_price ?? 0),
            $request->discount
        );
    }

    /**
     * Normalize selected category IDs from the multi-select (category_ids[]).
     * Falls back to legacy single category_id when present.
     */
    private function resolveCategoryIds(Request $request): array
    {
        $ids = $request->input('category_ids', $request->input('category_id'));

        if (! is_array($ids)) {
            $ids = $ids !== null && $ids !== '' ? [$ids] : [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    private function productExportHeaders(): array
    {
        return [
            'SKU',
            'Name',
            'Slug',
            'Brand',
            'Gender',
            'Price',
            'Selling Price',
            'GST Percentage',
            'Quantity',
            'Product Type',
            'Status',
            'Details',
            'Description',
            'Specification',
        ];
    }

    private function buildProductQuery(Request $request)
    {
        $query = Product::with(['brand', 'category', 'subcategory', 'gstSetting']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('sku', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $products = $this->buildProductQuery($request)->latest()->paginate(10);

        $brands = Brand::where('status', 1)->orderBy('name')->get();

        return view(
            'admin.product.index',
            compact('products', 'brands')
        );
    }

    public function create()
    {
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $categories = Category::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $subcategories = SubCategory::where('status', 1)
            ->orderBy('name')
            ->get(['id', 'category_id', 'name']);
        $gstSettings = GSTSetting::where('status', 1)->orderBy('gst_percentage')->get();

        return view(
            'admin.product.add',
            compact('brands', 'categories', 'subcategories', 'gstSettings')
        );
    }

    public function getSubcategories($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)
            ->where('status', 1)
            ->get();

        return response()->json($subcategories);
    }

    public function store(Request $request)
    {
        $request->validate([

            'brand_id'      => 'required|exists:brands,id',
            'category_ids'  => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'category_id'   => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:sub_categories,id',

            'gender'        => 'required|in:women,men,both',

            'name'          => 'required',

            'slug'          => 'required|unique:products,slug',

            'sku'           => 'required|unique:products,sku',

            'price'         => 'required|numeric',

            'selling_price' => 'required|numeric',

            'gst_setting_id' => 'nullable|exists:gst_settings,id',

            'quantity'      => 'required|numeric',

            'details'       => 'nullable',

            'description'   => 'required',

            'specification' => 'required',

            'thumbnail'     => 'required|image|mimes:jpg,jpeg,png,webp',

            'gallery'       => 'required|array|min:1',

            'gallery.*'     => 'required|image|mimes:jpg,jpeg,png,webp',

            'product_type'  => 'required|in:simple,variable',

            'variants' => 'nullable|array',

            'variants.*.sku' => 'nullable',

            'variants.*.price' => 'nullable|numeric',

            'variants.*.selling_price' => 'nullable|numeric',

            'variants.*.quantity' => 'nullable|numeric',

            'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

            'variants.*.attributes' => 'nullable|array',

            'variants.*.attributes.*.attribute' =>
                'required_if:product_type,variable',

            'variants.*.attributes.*.value' =>
                'required_if:product_type,variable',
                  'faqs' => 'nullable|array',
        'faqs.*.question' => 'required_with:faqs.*|string|max:500',
        'faqs.*.answer' => 'required_with:faqs.*|string',
        'video' => 'nullable|mimes:mp4,webm,ogg|max:51200',

        ]);

        $categoryIds = $this->resolveCategoryIds($request);
        $primaryCategoryId = $categoryIds[0] ?? null;

        if ($request->filled('subcategory_id') && $primaryCategoryId) {
            $validSubcategory = SubCategory::where('id', $request->subcategory_id)
                ->where('category_id', $primaryCategoryId)
                ->exists();

            if (! $validSubcategory) {
                return back()
                    ->withInput()
                    ->withErrors(['subcategory_id' => 'Selected subcategory does not belong to selected category.']);
            }
        }

        DB::beginTransaction();

        try {

            // =========================================
            // THUMBNAIL
            // =========================================

            $thumbnail = null;

            if ($request->hasFile('thumbnail')) {

                $thumb = $request->file('thumbnail');

                $thumbnail = time() . '_thumb.' . $thumb->extension();

                $thumb->move(
                    public_path('backend/assets/images/product'),
                    $thumbnail
                );
            }

            // =========================================
            // GALLERY
            // =========================================

            $gallery = [];

            if ($request->hasFile('gallery')) {

                foreach ($request->file('gallery') as $image) {

                    $imageName = time() . rand(111,999) . '.' . $image->extension();

                    $image->move(
                        public_path('backend/assets/images/product'),
                        $imageName
                    );

                    $gallery[] = $imageName;
                }
            }
$video = null;

if ($request->hasFile('video')) {
    $vid = $request->file('video');
    $video = time() . '_video.' . $vid->extension();

    $vid->move(
        public_path('backend/assets/videos/product'),
        $video
    );
}
            // =========================================
            // PRODUCT SAVE
            // =========================================

            $product = Product::create([

                'brand_id'        => $request->brand_id,

                'gender'          => $request->gender,

                'category_id'     => $primaryCategoryId,

                'subcategory_id'  => $request->subcategory_id ?: null,

                'name'            => $request->name,

                'slug'            => Str::slug($request->slug),

                'sku'             => $request->sku,

                'product_type'    => $request->product_type,

                'thumbnail'       => $thumbnail,

                'price'           => $request->price,

                'selling_price'   => $request->selling_price,

                'gst_setting_id'  => $request->gst_setting_id ?: null,

                'details'         => $request->details,

                'description'     => $request->description,

                'specification'   => $request->specification,

                'quantity'        => $request->quantity,

                'gallery'         => $gallery,

                'gallery_order'   => $gallery,

                'status'          => $request->status,

                'is_trending'     => $request->is_trending ?? 0,

                'is_featured'     => $request->is_featured ?? 0,

                'is_best_selling' => $request->is_best_selling ?? 0,

                'is_top_rated'    => $request->is_top_rated ?? 0,

                'is_new_arrival'  => $request->is_new_arrival ?? 0,

                'badge'           => $request->badge,

                'discount'        => $this->resolveProductDiscount($request),
                'video' => $video,

            ]);

            $product->categories()->sync($categoryIds);

            // =========================================
            // SAVE VARIANTS
            // =========================================

            if (
                $request->product_type == 'variable' &&
                $request->has('variants')
            ) {

                foreach ($request->variants as $index => $variant) {

                    // VARIANT IMAGE

                    $variantImage = null;

                    if (
                        isset($variant['image']) &&
                        $variant['image']
                    ) {

                        $image = $variant['image'];

                        $variantImage = time() . rand(111,999) . '.' . $image->extension();

                        $image->move(
                            public_path('backend/assets/images/product'),
                            $variantImage
                        );
                    }

                    // CREATE VARIANT

                    $productVariant = ProductVariant::create([

                        'product_id'     => $product->id,

                        'sku'            => $variant['sku'] ?? null,

                        'price'          => $variant['price'] ?? 0,

                        'selling_price'  => $variant['selling_price'] ?? 0,

                        'quantity'       => $variant['quantity'] ?? 0,

                        'image'          => $variantImage,

                        'status'         => 1,

                    ]);

                    // SAVE ATTRIBUTES

                    if (isset($variant['attributes'])) {

                        foreach ($variant['attributes'] as $attr) {

                            $attribute = ProductAttribute::firstOrCreate([
                                'name' => $attr['attribute']
                            ]);

                            $attributeValue = ProductAttributeValue::firstOrCreate([

                                'attribute_id' => $attribute->id,

                                'value'        => $attr['value']

                            ]);

                            ProductVariantAttribute::create([

                                'variant_id'         => $productVariant->id,

                                'attribute_id'       => $attribute->id,

                                'attribute_value_id' => $attributeValue->id,

                            ]);
                        }
                    }
                }
            }
            if ($request->has('faqs') && !empty($request->faqs)) {
            foreach ($request->faqs as $index => $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $product->faqs()->create([
                        'question' => $faq['question'],
                        'answer' => $faq['answer'],
                        'order' => $faq['order'] ?? $index,
                        'status' => $faq['status'] ?? 1,
                    ]);
                }
            }
        }

            DB::commit();

            return redirect()
                ->route('product.index')
                ->with('success', 'Product Added Successfully');

        } catch (\Exception $e) {

            DB::rollback();

            return back()->withErrors($e->getMessage());
        }
    }

    public function edit(Request $request, Product $product)
    {
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $categories = Category::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $subcategories = SubCategory::where('status', 1)
            ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
            ->orderBy('name')
            ->get();

        $product->load([
            'variants.variantAttributes.attribute',
            'variants.variantAttributes.attributeValue',
            'gstSetting',
            'brand',
            'categories',
        ]);

        $gstSettings = GSTSetting::where('status', 1)->orderBy('gst_percentage')->get();

        $page = $request->page;

        return view(
            'admin.product.edit',
            compact(
                'product',
                'brands',
                'categories',
                'subcategories',
                'gstSettings',
                'page'
            )
        );
    }

 public function update(Request $request, Product $product)
{
    $request->validate([
        'brand_id'      => 'required|exists:brands,id',
        'category_ids'  => 'nullable|array',
        'category_ids.*' => 'integer|exists:categories,id',
        'category_id'   => 'nullable|exists:categories,id',
        'subcategory_id' => 'nullable|exists:sub_categories,id',
        'gender'        => 'required|in:women,men,both',
        'name'          => 'required',
        'slug'          => 'required|unique:products,slug,' . $product->id,
        'sku'           => 'required|unique:products,sku,' . $product->id,
        'price'         => 'required|numeric',
        'selling_price' => 'required|numeric',
        'gst_setting_id' => 'nullable|exists:gst_settings,id',
        'quantity'      => 'required|numeric',
        'thumbnail'     => 'nullable|image|mimes:jpg,jpeg,png,webp',
        'images.*'      => 'nullable|image|mimes:jpg,jpeg,png,webp',
        'product_type'  => 'required|in:simple,variable',
        'variants' => 'nullable|array',
        'variants.*.sku' => 'nullable',
        'variants.*.price' => 'nullable|numeric',
        'variants.*.selling_price' => 'nullable|numeric',
        'variants.*.quantity' => 'nullable|numeric',
        'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        'variants.*.attributes' => 'nullable|array',
        'variants.*.attributes.*.attribute' => 'required_if:product_type,variable',
        'variants.*.attributes.*.value' => 'required_if:product_type,variable',
         'faqs' => 'nullable|array',
        'faqs.*.question' => 'required_with:faqs.*|string|max:500',
        'faqs.*.answer' => 'required_with:faqs.*|string',
        'video' => 'nullable|mimes:mp4,webm,ogg|max:51200',
    ]);

    DB::beginTransaction();

    $categoryIds = $this->resolveCategoryIds($request);
    $primaryCategoryId = $categoryIds[0] ?? null;

    if ($request->filled('subcategory_id') && $primaryCategoryId) {
        $validSubcategory = SubCategory::where('id', $request->subcategory_id)
            ->where('category_id', $primaryCategoryId)
            ->exists();

        if (! $validSubcategory) {
            return back()
                ->withInput()
                ->withErrors(['subcategory_id' => 'Selected subcategory does not belong to selected category.']);
        }
    }

    try {
        // =========================================
        // THUMBNAIL
        // =========================================
        $thumbnail = $product->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail && file_exists(public_path('backend/assets/images/product/' . $product->thumbnail))) {
                unlink(public_path('backend/assets/images/product/' . $product->thumbnail));
            }
            $thumb = $request->file('thumbnail');
            $thumbnail = time() . '_thumb.' . $thumb->extension();
            $thumb->move(public_path('backend/assets/images/product'), $thumbnail);
        }

        // =========================================
        // OLD GALLERY
        // =========================================
        $gallery = is_array($product->gallery) ? $product->gallery : json_decode($product->gallery, true);
        $gallery = $gallery ?? [];

        // =========================================
        // REMOVE IMAGES
        // =========================================
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $removeImage) {
                $path = public_path('backend/assets/images/product/' . $removeImage);
                if (file_exists($path)) {
                    unlink($path);
                }
                $gallery = array_filter($gallery, function ($img) use ($removeImage) {
                    return $img != $removeImage;
                });
            }
            $gallery = array_values($gallery);
        }

        // =========================================
        // ADD NEW IMAGES
        // =========================================
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . rand(111, 999) . '.' . $image->extension();
                $image->move(public_path('backend/assets/images/product'), $imageName);
                $gallery[] = $imageName;
            }
        }

        // =========================================
        // GALLERY ORDER
        // =========================================
        if ($request->has('gallery_order')) {
            $orderedGallery = [];
            foreach ($request->gallery_order as $imageName) {
                if (in_array($imageName, $gallery)) {
                    $orderedGallery[] = $imageName;
                }
            }
            foreach ($gallery as $img) {
                if (!in_array($img, $orderedGallery)) {
                    $orderedGallery[] = $img;
                }
            }
            $gallery = $orderedGallery;
        }$video = $product->video;

if ($request->hasFile('video')) {

    if ($product->video && file_exists(public_path('backend/assets/videos/product/' . $product->video))) {
        unlink(public_path('backend/assets/videos/product/' . $product->video));
    }

    $vid = $request->file('video');

    $video = time() . '_video.' . $vid->extension();

    $vid->move(
        public_path('backend/assets/videos/product'),
        $video
    );
}

// dd($video);
        // =========================================
        // PRODUCT UPDATE
        // =========================================
        $product->update([
            'brand_id'        => $request->brand_id,
            'gender'          => $request->gender,
            'category_id'     => $primaryCategoryId,
            'subcategory_id'  => $request->subcategory_id ?: null,
            'name'            => $request->name,
            'slug'            => Str::slug($request->slug),
            'sku'             => $request->sku,
            'product_type'    => $request->product_type,
            'thumbnail'       => $thumbnail,
            'price'           => $request->price,
            'selling_price'   => $request->selling_price,
            'gst_setting_id'  => $request->gst_setting_id ?: null,
            'details'         => $request->details,
            'description'     => $request->description,
            'specification'   => $request->specification,
            'quantity'        => $request->quantity,
            'gallery'         => $gallery,
            'gallery_order'   => $gallery,
            'status'          => $request->status,
            'is_trending'     => $request->is_trending ?? 0,
            'is_featured'     => $request->is_featured ?? 0,
            'is_best_selling' => $request->is_best_selling ?? 0,
            'is_top_rated'    => $request->is_top_rated ?? 0,
            'is_new_arrival'  => $request->is_new_arrival ?? 0,
            'badge'           => $request->badge,
            'discount'        => $this->resolveProductDiscount($request),
            'video' => $video,
        ]);

        $product->categories()->sync($categoryIds);

        // =========================================
        // DELETE OLD VARIANTS AND THEIR ATTRIBUTES
        // =========================================
        $variantIds = ProductVariant::where('product_id', $product->id)->pluck('id');
        ProductVariantAttribute::whereIn('variant_id', $variantIds)->delete();
        ProductVariant::where('product_id', $product->id)->delete();

        // =========================================
        // SAVE NEW VARIANTS
        // =========================================
        if ($request->product_type == 'variable' && $request->has('variants')) {
            foreach ($request->variants as $variant) {
                $variantImage = null;
                if (isset($variant['image']) && $variant['image']) {
                    $image = $variant['image'];
                    $variantImage = time() . rand(111, 999) . '.' . $image->extension();
                    $image->move(public_path('backend/assets/images/product'), $variantImage);
                }

                $productVariant = ProductVariant::create([
                    'product_id'     => $product->id,
                    'sku'            => $variant['sku'] ?? null,
                    'price'          => $variant['price'] ?? 0,
                    'selling_price'  => $variant['selling_price'] ?? 0,
                    'quantity'       => $variant['quantity'] ?? 0,
                    'image'          => $variantImage,
                    'status'         => 1,
                ]);

                // SAVE ATTRIBUTES FOR THIS VARIANT
                if (isset($variant['attributes'])) {
                    foreach ($variant['attributes'] as $attr) {
                        $attribute = ProductAttribute::firstOrCreate([
                            'name' => $attr['attribute']
                        ]);
                        $attributeValue = ProductAttributeValue::firstOrCreate([
                            'attribute_id' => $attribute->id,
                            'value'        => $attr['value']
                        ]);
                        ProductVariantAttribute::create([
                            'variant_id'         => $productVariant->id,
                            'attribute_id'       => $attribute->id,
                            'attribute_value_id' => $attributeValue->id,
                        ]);
                    }
                }
            }
        }
  if ($request->has('remove_faqs')) {
            ProductFaq::whereIn('id', $request->remove_faqs)->delete();
        }
        
        // Update existing FAQs and add new ones
        if ($request->has('faqs')) {
            foreach ($request->faqs as $index => $faq) {
                if (isset($faq['id']) && !empty($faq['id'])) {
                    // Update existing FAQ
                    $productFaq = ProductFaq::find($faq['id']);
                    if ($productFaq && $productFaq->product_id == $product->id) {
                        $productFaq->update([
                            'question' => $faq['question'],
                            'answer' => $faq['answer'],
                            'order' => $faq['order'] ?? $index,
                            'status' => $faq['status'] ?? 1,
                        ]);
                    }
                } else {
                    // Create new FAQ
                    if (!empty($faq['question']) && !empty($faq['answer'])) {
                        $product->faqs()->create([
                            'question' => $faq['question'],
                            'answer' => $faq['answer'],
                            'order' => $faq['order'] ?? $index,
                            'status' => $faq['status'] ?? 1,
                        ]);
                    }
                }
            }
        }
        DB::commit();
        return redirect()->route('product.index', ['page' => $request->page])
            ->with('success', 'Product Updated Successfully');
    } catch (\Exception $e) {
        DB::rollback();
        return back()->withErrors($e->getMessage());
    }
}



    public function destroy(Product $product)
    {
        $gallery = is_array($product->gallery)
            ? $product->gallery
            : json_decode($product->gallery, true);

        if ($gallery) {

            foreach ($gallery as $image) {

                $path = public_path(
                    'backend/assets/images/product/' . $image
                );

                if (file_exists($path)) {

                    unlink($path);
                }
            }
        }

        $product->delete();

        return redirect()
            ->route('product.index')
            ->with('success', 'Product Deleted Successfully');
    }

    public function toggleStatus(Product $product)
    {
        $product->status = $product->status == 1 ? 0 : 1;

        $product->save();

        return redirect()->back()->with(
            'success',
            $product->status == 1
                ? 'Product activated successfully.'
                : 'Product deactivated successfully.'
        );
    }

    public function export(Request $request)
    {
        $products = $this->buildProductQuery($request)->latest()->get();
        $fileName = 'products_' . date('Y-m-d_His') . '.csv';

        return $this->streamCsvDownload($fileName, $this->productExportHeaders(), function ($file) use ($products) {
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->sku,
                    $product->name,
                    $product->slug,
                    $product->brand->name ?? '',
                    \App\Models\Product::GENDERS[$product->gender] ?? ($product->gender ?? ''),
                    $product->price,
                    $product->selling_price,
                    $product->gstSetting->gst_percentage ?? '',
                    $product->quantity,
                    $product->product_type ?? 'simple',
                    $product->status == 1 ? 'Active' : 'Inactive',
                    $product->details,
                    $product->description,
                    $product->specification,
                ]);
            }
        });
    }

    public function importSample()
    {
        $fileName = 'products_import_sample.csv';

        return $this->streamCsvDownload($fileName, $this->productExportHeaders(), function ($file) {
            fputcsv($file, [
                'SKU-001',
                'Sample Product',
                'sample-product',
                'Titan',
                'men',
                '500',
                '450',
                '5',
                '100',
                'simple',
                'Active',
                'Short details',
                'Full description',
                'Specifications here',
            ]);
        });
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
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
                DB::transaction(function () use ($row, $rowNumber, &$imported, &$updated) {
                    $sku = $this->csvValue($row, 'sku');
                    $name = $this->csvValue($row, 'name');

                    if (!$sku || !$name) {
                        throw new \RuntimeException("Row {$rowNumber}: SKU and Name are required.");
                    }

                    $brandName = $this->csvValue($row, 'brand');
                    $brand = null;

                    if ($brandName) {
                        $brand = Brand::where('name', $brandName)->first();
                    }

                    if (!$brand) {
                        throw new \RuntimeException("Row {$rowNumber}: brand '{$brandName}' not found.");
                    }

                    $gender = strtolower((string) $this->csvValue($row, 'gender', ''));
                    if ($gender && ! array_key_exists($gender, Product::GENDERS)) {
                        throw new \RuntimeException("Row {$rowNumber}: gender must be women, men, or both.");
                    }

                    $gstSettingId = null;
                    $gstPercentage = $this->csvValue($row, 'gst_percentage');
                    if ($gstPercentage !== '') {
                        $gstSetting = GSTSetting::where('gst_percentage', $gstPercentage)->first();
                        $gstSettingId = $gstSetting?->id;
                    }

                    $slug = $this->csvValue($row, 'slug') ?: Str::slug($name);
                    $productType = $this->csvValue($row, 'product_type', 'simple');
                    if (!in_array($productType, ['simple', 'variable'], true)) {
                        $productType = 'simple';
                    }

                    $data = [
                        'brand_id' => $brand->id,
                        'gender' => $gender ?: null,
                        'name' => $name,
                        'slug' => $slug,
                        'price' => (float) $this->csvValue($row, 'price', 0),
                        'selling_price' => (float) $this->csvValue($row, 'selling_price', 0),
                        'gst_setting_id' => $gstSettingId,
                        'quantity' => (float) $this->csvValue($row, 'quantity', 0),
                        'product_type' => $productType,
                        'status' => $this->normalizeStatusValue($this->csvValue($row, 'status'), 1),
                        'details' => $this->csvValue($row, 'details', '-'),
                        'description' => $this->csvValue($row, 'description', '-'),
                        'specification' => $this->csvValue($row, 'specification', '-'),
                        'gallery' => [],
                    ];

                    $existing = Product::where('sku', $sku)->first();
                    if ($existing) {
                        $existing->update($data);
                        $updated++;
                        return;
                    }

                    if (Product::where('slug', $slug)->exists()) {
                        $data['slug'] = $slug . '-' . Str::lower(Str::random(4));
                    }

                    Product::create(array_merge($data, ['sku' => $sku]));
                    $imported++;
                });
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }

        $message = "Import complete: {$imported} created, {$updated} updated.";

        return redirect()
            ->route('product.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    public function duplicate($id)
{
    $product = Product::findOrFail($id);

    $newProduct = $product->replicate(); // copy all fields
    $newProduct->name = $product->name . ' (Copy)';
    $newProduct->slug = $product->slug . '-copy-' . time();
    $newProduct->save();

    return redirect()->back()->with('success', 'Product duplicated successfully!');
}
}