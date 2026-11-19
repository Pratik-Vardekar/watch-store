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
     * Persist selling price dynamically, calculating from discount if selling_price was omitted.
     */
    private function resolveSellingPrice(Request $request): ?float
    {
        $price = (float) ($request->price ?? 0);
        $sellingPrice = ($request->filled('selling_price') && (float) $request->selling_price > 0) ? (float) $request->selling_price : null;

        if (($sellingPrice === null || $sellingPrice <= 0) && $price > 0 && $request->filled('discount_amount')) {
            $discType = $request->input('discount_type', 'percent');
            $discVal = (float) $request->input('discount_amount', 0);
            if ($discVal > 0) {
                if ($discType === 'percent') {
                    $sellingPrice = max(0, round($price - ($price * ($discVal / 100)), 2));
                } else {
                    $sellingPrice = max(0, round($price - $discVal, 2));
                }
            }
        }

        return $sellingPrice;
    }

    /**
     * Persist discount dynamically from MRP vs selling price (or discount_amount).
     */
    private function resolveProductDiscount(Request $request): int
    {
        $price = (float) ($request->price ?? 0);
        $sellingPrice = $this->resolveSellingPrice($request) ?? 0;

        if ($price > 0 && $sellingPrice > 0 && $sellingPrice < $price) {
            return (int) round((($price - $sellingPrice) / $price) * 100);
        }

        if ($request->filled('discount_amount')) {
            $discType = $request->input('discount_type', 'percent');
            $discVal = (float) $request->input('discount_amount', 0);
            if ($discType === 'percent') {
                return (int) round($discVal);
            }
            if ($price > 0 && $discVal > 0) {
                return (int) round(($discVal / $price) * 100);
            }
        }

        if ($request->filled('discount')) {
            return (int) $request->discount;
        }

        return 0;
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
            'Name',
            'Brand',
            'Category',
            'Gender',
            'MRP',
            'Selling Price',
            'Model No',
            'SKU No',
            'Quantity',
            'Description',
            'Specification',
            'Added Time',
        ];
    }

    private function buildProductQuery(Request $request)
    {
        $query = Product::with(['brand', 'categories', 'category', 'subcategory', 'gstSetting', 'variants']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('sku', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('model_no', 'LIKE', '%' . $request->search . '%');
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

        // For the create page, selectedCategoryIds should come from old input
        $selectedCategoryIds = collect(old('category_ids', old('category_id') ? [old('category_id')] : []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->all();

        $subcategories = SubCategory::where('status', 1)
            ->orderBy('name')
            ->get(['id', 'category_id', 'name']);

        // Filter subcategories based on selected categories
        $initialSubcategories = $subcategories
            ->when(!empty($selectedCategoryIds), function ($q) use ($selectedCategoryIds) {
                return $q->whereIn('category_id', $selectedCategoryIds);
            })
            ->values();

        $gstSettings = GSTSetting::where('status', 1)->orderBy('gst_percentage')->get();

        return view(
            'admin.product.add',
            compact('brands', 'categories', 'subcategories', 'gstSettings', 'selectedCategoryIds', 'initialSubcategories')
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

            'brand_id'      => 'nullable|exists:brands,id',
            'category_ids'  => 'nullable|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'subcategory_id' => 'nullable|exists:sub_categories,id',

            'gender'        => 'nullable|in:women,men,both',

            'name'          => 'nullable',

            'slug'          => 'nullable|unique:products,slug',

            'sku'           => 'nullable|unique:products,sku',
            'model_no'      => 'nullable|unique:products,model_no',

            'price'         => 'nullable|numeric',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric',

            'gst_setting_id' => 'nullable|exists:gst_settings,id',

            'quantity'      => 'nullable|numeric',

            'details'       => 'nullable',

            'description'   => 'nullable',

            'specification' => 'nullable',

            'thumbnail'     => 'nullable|image|mimes:jpg,jpeg,png,webp',

            'gallery'       => 'nullable|array',

            'gallery.*'     => 'nullable|image|mimes:jpg,jpeg,png,webp',

            'product_type'  => 'nullable|in:simple,variable',

            'variants' => 'nullable|array',

            'variants.*.sku' => 'nullable',

            'variants.*.price' => 'nullable|numeric',

            'variants.*.selling_price' => 'nullable|numeric',

            'variants.*.quantity' => 'nullable|numeric',

            'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

            'variants.*.attributes' => 'nullable|array',

            'variants.*.attributes.*.attribute' =>
            'nullable',

            'variants.*.attributes.*.value' =>
            'nullable',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:500',
            'faqs.*.answer' => 'nullable|string',
            'video' => 'nullable|mimes:mp4,webm,ogg,mov,m4v|max:51200',

        ]);

        $categoryIds = $this->resolveCategoryIds($request);
        $primaryCategoryId = $categoryIds[0] ?? null;

        // Validate subcategory only if selected and first selected category is present
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
                    base_path('backend/assets/images/product'),
                    $thumbnail
                );
            }

            // =========================================
            // GALLERY
            // =========================================

            $gallery = [];

            if ($request->hasFile('gallery')) {

                foreach ($request->file('gallery') as $image) {

                    $imageName = time() . rand(111, 999) . '.' . $image->extension();

                    $image->move(
                        base_path('backend/assets/images/product'),
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
                    base_path('backend/assets/videos/product'),
                    $video
                );
            }
            // =========================================
            // PRODUCT SAVE
            // =========================================

            $product = Product::create([

                'brand_id'        => $request->brand_id,

                'gender'          => $request->input('gender') ?: null,

                'category_id'     => $primaryCategoryId,

                'subcategory_id'  => $request->subcategory_id ?: null,

                'name'            => $request->name,

                'slug'            => Str::slug($request->slug),

                'sku'             => $request->filled('sku') ? trim($request->sku) : null,

                'model_no'        => $request->filled('model_no') ? trim($request->model_no) : null,

                'product_type'    => $request->product_type,

                'thumbnail'       => $thumbnail,

                'price'           => $request->filled('price') ? (float) $request->price : 0,

                'selling_price'   => $this->resolveSellingPrice($request),

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

            // Sync all selected categories for the product
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

                        $variantImage = time() . rand(111, 999) . '.' . $image->extension();

                        $image->move(
                            base_path('backend/assets/images/product'),
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

                    if (isset($variant['attributes']) && is_iterable($variant['attributes'])) {

                        foreach ($variant['attributes'] as $attr) {

                            $attrName = isset($attr['attribute']) ? trim((string)$attr['attribute']) : '';
                            $attrVal  = isset($attr['value']) ? trim((string)$attr['value']) : '';

                            if ($attrName === '' || $attrVal === '') {
                                continue;
                            }

                            $attribute = ProductAttribute::firstOrCreate([
                                'name' => $attrName
                            ]);

                            $attributeValue = ProductAttributeValue::firstOrCreate([

                                'attribute_id' => $attribute->id,

                                'value'        => $attrVal

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

        // Get selected category IDs from the product's categories
        $selectedCategoryIds = $product->categories()->pluck('categories.id')->toArray();

        // If no categories are selected yet, use the legacy category_id
        if (empty($selectedCategoryIds) && $product->category_id) {
            $selectedCategoryIds = [$product->category_id];
        }

        // Fetch all subcategories associated with all categories assigned to this product
        $categoryIdsForProduct = $selectedCategoryIds ?: [$product->category_id];
        $subcategories = SubCategory::where('status', 1)
            ->whereIn('category_id', $categoryIdsForProduct)
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
                'page',
                'selectedCategoryIds'  // <-- This is the fix
            )
        );
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'brand_id'      => 'nullable|exists:brands,id',
            'category_ids'  => 'nullable|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'subcategory_id' => 'nullable|exists:sub_categories,id',
            'gender'        => 'nullable|in:women,men,both',
            'name'          => 'nullable',
            'slug'          => 'nullable|unique:products,slug,' . $product->id,
            'sku'           => 'nullable|unique:products,sku,' . $product->id,
            'model_no'      => 'nullable|unique:products,model_no,' . $product->id,
            'price'         => 'nullable|numeric',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric',
            'gst_setting_id' => 'nullable|exists:gst_settings,id',
            'quantity'      => 'nullable|numeric',
            'thumbnail'     => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'images.*'      => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'product_type'  => 'nullable|in:simple,variable',
            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.selling_price' => 'nullable|numeric',
            'variants.*.quantity' => 'nullable|numeric',
            'variants.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'variants.*.old_image' => 'nullable|string',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.attribute' => 'nullable',
            'variants.*.attributes.*.value' => 'nullable',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:500',
            'faqs.*.answer' => 'nullable|string',
            'video' => 'nullable|mimes:mp4,webm,ogg,mov,m4v|max:51200',
        ]);

        DB::beginTransaction();

        $categoryIds = $this->resolveCategoryIds($request);
        $primaryCategoryId = $categoryIds[0] ?? null;

        // Validate subcategory belongs to ANY selected category
        if ($request->filled('subcategory_id') && !empty($categoryIds)) {
            $validSubcategory = SubCategory::where('id', $request->subcategory_id)
                ->whereIn('category_id', $categoryIds)
                ->exists();

            if (! $validSubcategory) {
                return back()
                    ->withInput()
                    ->withErrors(['subcategory_id' => 'Selected subcategory does not belong to any selected category.']);
            }
        }

        try {
            // =========================================
            // THUMBNAIL
            // =========================================
            $thumbnail = $product->thumbnail;
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail && file_exists(base_path('backend/assets/images/product/' . $product->thumbnail))) {
                    unlink(base_path('backend/assets/images/product/' . $product->thumbnail));
                }
                $thumb = $request->file('thumbnail');
                $thumbnail = time() . '_thumb.' . $thumb->extension();
                $thumb->move(base_path('backend/assets/images/product'), $thumbnail);
            }

            // =========================================
            // OLD GALLERY
            // =========================================
            $gallery = is_array($product->gallery) ? $product->gallery : json_decode($product->gallery, true);
            $gallery = $gallery ?? [];

            $removeImages = $request->input('remove_images', []);
            $remainingGallery = array_values(array_filter($gallery, function ($img) use ($removeImages) {
                return ! in_array($img, $removeImages);
            }));

            if (empty($thumbnail)) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['thumbnail' => 'Thumbnail is required.']);
            }

            // =========================================
            // REMOVE IMAGES
            // =========================================
            if ($request->has('remove_images')) {
                foreach ($request->remove_images as $removeImage) {
                    $path = base_path('backend/assets/images/product/' . $removeImage);
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
                    $image->move(base_path('backend/assets/images/product'), $imageName);
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
            }

            $video = $product->video;

            if ($request->hasFile('video')) {

                if ($product->video && file_exists(base_path('backend/assets/videos/product/' . $product->video))) {
                    unlink(base_path('backend/assets/videos/product/' . $product->video));
                }

                $vid = $request->file('video');

                $video = time() . '_video.' . $vid->extension();

                $vid->move(
                    base_path('backend/assets/videos/product'),
                    $video
                );
            }

            // =========================================
            // PRODUCT UPDATE
            // =========================================
            $product->update([
                'brand_id'        => $request->brand_id,
                'gender'          => $request->input('gender') ?: null,
                'category_id'     => $primaryCategoryId,
                'subcategory_id'  => $request->subcategory_id ?: null,
                'name'            => $request->name,
                'slug'            => Str::slug($request->slug),
                'sku'             => $request->filled('sku') ? trim($request->sku) : null,
                'model_no'        => $request->filled('model_no') ? trim($request->model_no) : null,
                'product_type'    => $request->product_type,
                'thumbnail'       => $thumbnail,
                'price'           => $request->filled('price') ? (float) $request->price : 0,
                'selling_price'   => $this->resolveSellingPrice($request),
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

            // Sync all selected categories for the product
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
                    $variantImage = $variant['old_image'] ?? null;
                    if (isset($variant['image']) && $variant['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $image = $variant['image'];
                        $variantImage = time() . rand(111, 999) . '.' . $image->extension();
                        $image->move(base_path('backend/assets/images/product'), $variantImage);
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
                    if (isset($variant['attributes']) && is_iterable($variant['attributes'])) {
                        foreach ($variant['attributes'] as $attr) {
                            $attrName = isset($attr['attribute']) ? trim((string)$attr['attribute']) : '';
                            $attrVal  = isset($attr['value']) ? trim((string)$attr['value']) : '';

                            if ($attrName === '' || $attrVal === '') {
                                continue;
                            }

                            $attribute = ProductAttribute::firstOrCreate([
                                'name' => $attrName
                            ]);
                            $attributeValue = ProductAttributeValue::firstOrCreate([
                                'attribute_id' => $attribute->id,
                                'value'        => $attrVal
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

                $path = base_path(
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
                // Category resolution: pivot first, then primary category fallback
                $categoryName = $product->categories->first()->name ?? ($product->category->name ?? '');

                $firstVariant = $product->variants->first();

                // MRP / Regular Price (handle simple and variable products)
                $rawPrice = (float) ($product->price ?? 0);
                if ($rawPrice <= 0 && $firstVariant) {
                    $rawPrice = (float) ($firstVariant->price ?? 0);
                }

                // Selling Price (defaults to regular price if no discounted selling price was set)
                $rawSelling = ($product->selling_price !== null && (float) $product->selling_price > 0)
                    ? (float) $product->selling_price
                    : ($firstVariant && (float) ($firstVariant->selling_price ?? 0) > 0
                        ? (float) $firstVariant->selling_price
                        : $rawPrice);

                $MRP = $rawPrice > 0 ? number_format($rawPrice, 2, '.', '') : '0.00';
                $sellingPrice = $rawSelling > 0 ? number_format($rawSelling, 2, '.', '') : $MRP;

                // Model No & SKU (with variant & cross fallbacks)
                $modelNo = $product->model_no ?: ($firstVariant?->sku ?? ($product->sku ?? ''));
                $sku = $product->sku ?: ($firstVariant?->sku ?? ($product->model_no ?? ''));

                $quantity = $product->quantity ?? ($firstVariant?->quantity ?? 0);

                // Strip HTML tags for clean CSV output
                $description = trim(strip_tags($product->description ?? ''));
                $specification = trim(strip_tags($product->specification ?? ''));

                fputcsv($file, [
                    $product->name,
                    $product->brand->name ?? '',
                    $categoryName,
                    \App\Models\Product::GENDERS[$product->gender] ?? ($product->gender ?? ''),
                    $MRP,
                    $sellingPrice,
                    $modelNo,
                    $sku,
                    $quantity,
                    $description,
                    $specification,
                    $product->created_at ? $product->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }
        });
    }

    public function importSample()
    {
        $fileName = 'products_import_sample.csv';

        return $this->streamCsvDownload($fileName, $this->productExportHeaders(), function ($file) {
            fputcsv($file, [
                'Sample Product',
                'Titan',
                'Watches',
                'men',
                '500.00',
                '450.00',
                'M-1234',
                'SKU-001',
                '5',
                'Full description',
                'Specifications here',
                date('Y-m-d H:i:s'),
            ]);
        });
    }

    public function import(Request $request)
    {
        $fileKey = $request->hasFile('excel_file') ? 'excel_file' : 'file';

        $request->validate([
            $fileKey => 'required|file|max:10240',
        ]);

        $parsed = $this->parseCsvFile($request->file($fileKey));

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
                    $sku = $this->csvValue($row, 'sku_no');
                    if (!$sku) {
                        $sku = $this->csvValue($row, 'sku');
                    }

                    $modelNo = $this->csvValue($row, 'model_no');
                    if (!$modelNo) {
                        $modelNo = $this->csvValue($row, 'model_no.');
                    }

                    $name = $this->csvValue($row, 'name');

                    if (!$sku || !$name) {
                        throw new \RuntimeException("Row {$rowNumber}: SKU and Name are required.");
                    }

                    $brandName = $this->csvValue($row, 'brand');
                    $brand = null;

                    if ($brandName) {
                        $brand = Brand::firstOrCreate(
                            ['name' => $brandName],
                            ['status' => 1]
                        );
                    } else {
                        throw new \RuntimeException("Row {$rowNumber}: Brand is required.");
                    }

                    $gender = strtolower((string) $this->csvValue($row, 'gender', ''));
                    if ($gender && ! array_key_exists($gender, Product::GENDERS)) {
                        throw new \RuntimeException("Row {$rowNumber}: gender must be women, men, or both.");
                    }

                    $gstSettingId = null;
                    $gstPercentage = $this->csvValue($row, 'gst_percentage');
                    if ($gstPercentage !== '') {
                        $gstSetting = GSTSetting::firstOrCreate(
                            ['gst_percentage' => (float)$gstPercentage],
                            ['status' => 1]
                        );
                        $gstSettingId = $gstSetting->id;
                    }

                    $categoryName = $this->csvValue($row, 'category');
                    $subcategoryName = $this->csvValue($row, 'subcategory');

                    $categoryId = null;
                    if ($categoryName) {
                        $category = \App\Models\Category::firstOrCreate(
                            ['name' => $categoryName],
                            ['slug' => Str::slug($categoryName), 'status' => 1]
                        );
                        $categoryId = $category->id;
                    }

                    $subcategoryId = null;
                    if ($subcategoryName && $categoryId) {
                        $subcategory = \App\Models\SubCategory::firstOrCreate(
                            ['name' => $subcategoryName, 'category_id' => $categoryId],
                            ['slug' => Str::slug($subcategoryName), 'status' => 1]
                        );
                        $subcategoryId = $subcategory->id;
                    }

                    $slug = Str::slug($name);
                    $productType = strtolower(trim($this->csvValue($row, 'product_type', 'simple')));
                    if (!in_array($productType, ['simple', 'variable'], true)) {
                        $productType = 'simple';
                    }

                    $data = [
                        'brand_id' => $brand->id,
                        'subcategory_id' => $subcategoryId,
                        'gender' => $gender ?: null,
                        'name' => $name,
                        'model_no' => $modelNo ?: null,
                        'slug' => $slug,
                        'price' => (float) ($this->csvValue($row, 'mrp') !== '' ? $this->csvValue($row, 'mrp') : $this->csvValue($row, 'price', 0)),
                        'selling_price' => (float) $this->csvValue($row, 'selling_price', 0),
                        'gst_setting_id' => $gstSettingId,
                        'quantity' => (float) $this->csvValue($row, 'quantity', 0),
                        'product_type' => $productType,
                        'status' => $this->normalizeStatusValue($this->csvValue($row, 'status'), 1),
                        'details' => $this->csvValue($row, 'details', '-'),
                        'description' => $this->csvValue($row, 'description', '-'),
                        'specification' => $this->csvValue($row, 'specification', '-'),
                    ];

                    $createdAtStr = $this->csvValue($row, 'added_time');
                    if (!$createdAtStr) {
                        $createdAtStr = $this->csvValue($row, 'created_at');
                    }
                    $parsedCreatedAt = null;
                    if ($createdAtStr) {
                        try {
                            $parsedCreatedAt = \Carbon\Carbon::parse($createdAtStr)->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                        }
                    }

                    $existing = Product::where('sku', $sku)->first();

                    if ($modelNo) {
                        $modelQuery = Product::where('model_no', $modelNo);
                        if ($existing) {
                            $modelQuery->where('id', '!=', $existing->id);
                        }
                        if ($modelQuery->exists()) {
                            throw new \RuntimeException("Row {$rowNumber}: model no '{$modelNo}' is already taken by another product.");
                        }
                    }

                    if ($existing) {
                        if ($existing->slug !== $slug && Product::where('slug', $slug)->exists()) {
                            $data['slug'] = $slug . '-' . Str::lower(Str::random(4));
                        }
                        $existing->update($data);
                        if ($parsedCreatedAt) {
                            $existing->created_at = $parsedCreatedAt;
                            $existing->save();
                        }
                        if ($categoryId) {
                            $existing->categories()->sync([$categoryId]);
                        }
                        $updated++;
                        return;
                    }

                    if (Product::where('slug', $slug)->exists()) {
                        $data['slug'] = $slug . '-' . Str::lower(Str::random(4));
                    }

                    $data['gallery'] = []; // Set empty gallery only for new products

                    $newProduct = new Product(array_merge($data, ['sku' => $sku]));
                    if ($parsedCreatedAt) {
                        $newProduct->created_at = $parsedCreatedAt;
                    }
                    $newProduct->save();

                    if ($categoryId) {
                        $newProduct->categories()->sync([$categoryId]);
                    }
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
        $productId = $id instanceof Product ? $id->id : $id;
        $product = Product::with(['categories', 'variants.variantAttributes', 'faqs'])->findOrFail($productId);

        DB::beginTransaction();
        try {
            $newProduct = $product->replicate();

            $newProduct->name = $product->name . ' (Copy)';

            // Generate unique slug
            $baseSlug = Str::slug($product->slug . '-copy');
            $candidateSlug = $baseSlug . '-' . time();
            $counter = 1;
            while (Product::where('slug', $candidateSlug)->exists()) {
                $candidateSlug = $baseSlug . '-' . time() . '-' . $counter;
                $counter++;
            }
            $newProduct->slug = $candidateSlug;

            // Generate unique model_no if original had one
            if (!empty($product->model_no)) {
                $baseModelNo = $product->model_no . '-COPY';
                $candidateModelNo = $baseModelNo;
                $counter = 1;
                while (Product::where('model_no', $candidateModelNo)->exists()) {
                    $candidateModelNo = $baseModelNo . '-' . $counter;
                    $counter++;
                }
                $newProduct->model_no = $candidateModelNo;
            } else {
                $newProduct->model_no = null;
            }

            // Generate unique sku if original had one
            if (!empty($product->sku)) {
                $baseSku = $product->sku . '-COPY';
                $candidateSku = $baseSku;
                $counter = 1;
                while (Product::where('sku', $candidateSku)->exists()) {
                    $candidateSku = $baseSku . '-' . $counter;
                    $counter++;
                }
                $newProduct->sku = $candidateSku;
            } else {
                $newProduct->sku = null;
            }

            // Always make duplicated product Inactive
            $newProduct->status = 0;

            $newProduct->save();

            // Sync categories
            if ($product->categories && $product->categories->isNotEmpty()) {
                $newProduct->categories()->sync(
                    $product->categories->pluck('id')->toArray()
                );
            }

            // Duplicate variants if any
            if ($product->variants && $product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    $newVariant = $variant->replicate();
                    $newVariant->product_id = $newProduct->id;
                    if (!empty($variant->sku)) {
                        $newVariant->sku = $variant->sku . '-COPY-' . time() . '-' . Str::random(4);
                    }
                    $newVariant->save();

                    // Replicate variant attributes
                    if ($variant->variantAttributes && $variant->variantAttributes->isNotEmpty()) {
                        foreach ($variant->variantAttributes as $attr) {
                            $newAttr = $attr->replicate();
                            $newAttr->variant_id = $newVariant->id;
                            $newAttr->save();
                        }
                    }
                }
            }

            // Duplicate FAQs if any
            if ($product->faqs && $product->faqs->isNotEmpty()) {
                foreach ($product->faqs as $faq) {
                    $newFaq = $faq->replicate();
                    $newFaq->product_id = $newProduct->id;
                    $newFaq->save();
                }
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                'Product duplicated successfully!'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(
                'error',
                'Failed to duplicate product: ' . $e->getMessage()
            );
        }
    }
}
