@extends('layouts.admin')
@section('content')
    <div class="container-fluid my-2">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
            <div>
                <h4 class="mb-1">Edit Product</h4>
                <p class="text-muted mb-0">Update product details</p>
            </div>
            @if ($product->slug)
                <div>
                    <a href="{{ route('product.details', ['slug' => $product->slug]) }}" target="_blank"
                        class="btn btn-outline-primary">
                        <i class="ti ti-external-link me-1"></i>
                        View on Website
                    </a>
                </div>
            @endif
        </div>
        <div class="card product-form-card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data"
                    class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-4">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', $product->name) }}">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug" class="form-control"
                            value="{{ old('slug', $product->slug) }}">
                        @error('slug')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Model No.</label>
                        <input type="text" name="model_no" id="model_no" class="form-control"
                            value="{{ old('model_no', $product->model_no) }}" placeholder="Enter Model No.">
                        @error('model_no')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" id="sku" class="form-control"
                            value="{{ old('sku', $product->sku) }}" placeholder="Enter SKU (optional)">
                        @error('sku')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" id="brand_id" class="form-select">
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ (string) old('brand_id', $product->brand_id) === (string) $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-select">
                            <option value="">No Gender</option>
                            @foreach (\App\Models\Product::GENDERS as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('gender', $product->gender) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Used for Women / Men product listing.</small>
                        @error('gender')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Categories --}}
                    <div class="col-md-6 product-category-field">
                        <label class="form-label">Categories <span class="text-danger">*</span></label>
                        <select name="category_ids[]" id="category_ids" class="form-select" multiple
                            placeholder="Select categories">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ in_array((int) $category->id, $selectedCategoryIds, true) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select one or more categories. The first selected category loads
                            subcategories.</small>
                        @error('category_ids')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Subcategory --}}
                    <div class="col-md-6">
                        <label class="form-label">Subcategory</label>
                        <select name="subcategory_id" id="subcategory_id" class="form-select">
                            <option value="">Select Subcategory</option>
                            @foreach ($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}"
                                    {{ (string) old('subcategory_id', $product->subcategory_id) === (string) $subcategory->id ? 'selected' : '' }}>
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subcategory_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Pricing, Discount & GST Section --}}
                    <div class="col-md-3">
                        <label class="form-label">Price (MRP) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="price" id="price" class="form-control"
                                value="{{ old('price', $product->price) }}" step="0.01" min="0"
                                placeholder="0.00">
                        </div>
                        <small class="text-muted">MRP / Original Regular Price</small>
                        @error('price')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Selling Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="selling_price" id="selling_price" class="form-control"
                                value="{{ old('selling_price', $product->selling_price) }}" step="0.01"
                                min="0" placeholder="0.00">
                        </div>
                        <small class="text-muted">Auto-calculated or custom</small>
                        @error('selling_price')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Discount Type</label>
                        <select name="discount_type" id="discount_type" class="form-select">
                            <option value="percent" {{ old('discount_type', 'percent') === 'percent' ? 'selected' : '' }}>
                                Percentage (%)</option>
                            <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Flat Amount
                                (₹)</option>
                        </select>
                        <small class="text-muted">Choose % or Flat ₹</small>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Discount</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="discount_amount"
                                class="form-control" value="{{ old('discount_amount', $product->discount) }}"
                                id="discount_amount" placeholder="0">
                            <span class="input-group-text fw-bold text-success" id="discountTypeSymbol">%</span>
                        </div>
                        <input type="hidden" name="discount" id="discount"
                            value="{{ old('discount', $product->discount ?? 0) }}">
                        <small class="text-muted" id="discountSavingsText">Enter discount to calculate selling
                            price</small>
                        @error('discount')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">GST Slab</label>
                        <select name="gst_setting_id" id="gst_setting_id" class="form-select">
                            <option value="" data-rate="18"
                                {{ empty(old('gst_setting_id', $product->gst_setting_id)) ? 'selected' : '' }}>
                                18% GST (Default Included)
                            </option>
                            @if (isset($gstSettings) && count($gstSettings) > 0)
                                @foreach ($gstSettings as $gst)
                                    <option value="{{ $gst->id }}" data-rate="{{ $gst->gst_percentage }}"
                                        {{ (string) old('gst_setting_id', $product->gst_setting_id) === (string) $gst->id ? 'selected' : '' }}>
                                        {{ $gst->gst_percentage }}% GST (Included)
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted">GST 18% is included product-wise</small>
                        @error('gst_setting_id')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Pricing & Tax Breakdown Preview Card --}}
                    <div class="col-12">
                        <div class="p-3 bg-light rounded border" id="pricingBreakdownBox">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-3 col-6">
                                    <div class="small text-muted">MRP / Regular Price</div>
                                    <div class="fw-bold fs-6" id="previewMrp">₹0.00</div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="small text-muted">Selling Price (Incl. GST)</div>
                                    <div class="fw-bold fs-6 text-primary" id="previewSellingPrice">₹0.00</div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="small text-muted">Discount</div>
                                    <div class="fw-bold fs-6 text-success" id="previewDiscount">0% (Save ₹0.00)</div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="small text-muted" id="previewGstRateLabel">GST (18% Included)</div>
                                    <div class="fw-bold fs-6 text-dark" id="previewGstAmount">₹0.00 <span
                                            class="text-muted fw-normal small">(Base: ₹0.00)</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" min="0" name="quantity" class="form-control"
                            value="{{ old('quantity', $product->quantity) }}">
                        @error('quantity')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Product Type</label>
                        <select name="product_type" class="form-select">
                            <option value="simple"
                                {{ old('product_type', $product->product_type) == 'simple' ? 'selected' : '' }}>
                                Simple Product
                            </option>
                            <option value="variable"
                                {{ old('product_type', $product->product_type) == 'variable' ? 'selected' : '' }}>
                                Variable Product
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    {{-- VARIANT SECTION --}}
                    <div id="variant-section"
                        class="col-12 {{ old('product_type', $product->product_type) == 'variable' ? '' : 'd-none' }}">
                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Product Variants</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="addVariantBtn">
                                    Add Variant
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="variant-wrapper">
                                    @if ($product->variants && $product->variants->count())
                                        @foreach ($product->variants as $index => $variant)
                                            <div class="variant-item border rounded p-3 mb-3"
                                                data-variant-index="{{ $index }}">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label>SKU</label>
                                                        <input type="text" name="variants[{{ $index }}][sku]"
                                                            class="form-control" value="{{ $variant->sku }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Price</label>
                                                        <input type="number" step="0.01"
                                                            name="variants[{{ $index }}][price]"
                                                            class="form-control" value="{{ $variant->price }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Selling Price</label>
                                                        <input type="number" step="0.01"
                                                            name="variants[{{ $index }}][selling_price]"
                                                            class="form-control" value="{{ $variant->selling_price }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Quantity</label>
                                                        <input type="number"
                                                            name="variants[{{ $index }}][quantity]"
                                                            class="form-control" value="{{ $variant->quantity }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Image</label>
                                                        <input type="file" name="variants[{{ $index }}][image]"
                                                            class="form-control">
                                                        @if ($variant->image)
                                                            <small class="text-muted d-block">Current:
                                                                {{ $variant->image }}</small>
                                                            <input type="hidden"
                                                                name="variants[{{ $index }}][old_image]"
                                                                value="{{ $variant->image }}">
                                                        @endif
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-end">
                                                        <button type="button"
                                                            class="btn btn-danger removeVariant">X</button>
                                                    </div>
                                                    <hr>
                                                    {{-- Display existing attributes --}}
                                                    <div class="col-12">
                                                        <label class="fw-bold mb-2">Attributes</label>
                                                        <div class="attributes-container">
                                                            @php
                                                                $attributeCount = $variant->variantAttributes
                                                                    ? $variant->variantAttributes->count()
                                                                    : 0;
                                                            @endphp
                                                            @if ($attributeCount > 0)
                                                                @foreach ($variant->variantAttributes as $attrIndex => $variantAttr)
                                                                    <div class="row g-3 mt-2 attribute-row">
                                                                        <div class="col-md-5">
                                                                            <input type="text"
                                                                                name="variants[{{ $index }}][attributes][{{ $attrIndex }}][attribute]"
                                                                                class="form-control"
                                                                                placeholder="Attribute (e.g., Size)"
                                                                                value="{{ $variantAttr->attribute->name ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <input type="text"
                                                                                name="variants[{{ $index }}][attributes][{{ $attrIndex }}][value]"
                                                                                class="form-control"
                                                                                placeholder="Value (e.g., XL)"
                                                                                value="{{ $variantAttr->attributeValue->value ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-danger remove-attribute-btn">Remove</button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="row g-3 mt-2 attribute-row">
                                                                    <div class="col-md-5">
                                                                        <input type="text"
                                                                            name="variants[{{ $index }}][attributes][0][attribute]"
                                                                            class="form-control"
                                                                            placeholder="Attribute (e.g., Size)">
                                                                    </div>
                                                                    <div class="col-md-5">
                                                                        <input type="text"
                                                                            name="variants[{{ $index }}][attributes][0][value]"
                                                                            class="form-control"
                                                                            placeholder="Value (e.g., XL)">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-danger remove-attribute-btn">Remove</button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary mt-3 add-attribute-btn"
                                                            data-variant-index="{{ $index }}">+ Add More
                                                            Attributes</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block mb-2">Product Labels</label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_best_selling" value="1"
                                    {{ old('is_best_selling', $product->is_best_selling) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Best Selling
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1"
                                    {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    New Arrival
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Product Video</label>
                        <input type="file" name="video" id="videoInput" class="form-control" accept="video/*">

                        <div class="mt-2">
                            <div id="currentVideo">
                                @if ($product->video)
                                    <video width="200" controls class="rounded border">
                                        <source src="{{ asset('backend/assets/videos/product/' . $product->video) }}">
                                    </video>
                                @else
                                    <p class="text-muted small mb-0">No video uploaded</p>
                                @endif
                            </div>
                            <div id="videoPreview"></div>
                        </div>
                    </div>

                    <input type="hidden" name="page" value="{{ $page }}">

                    {{-- THUMBNAIL --}}
                    <div class="col-md-12">
                        <label class="form-label">Thumbnail</label>
                        <input type="file" name="thumbnail" id="thumbnailInput" class="form-control"
                            accept="image/*">
                    </div>
                    <div class="col-md-12">
                        <div id="thumbnailPreview">
                            @if ($product->thumbnail)
                                <img src="{{ asset('backend/assets/images/product/' . $product->thumbnail) }}"
                                    class="rounded border" style="width:120px;height:120px;object-fit:cover;">
                            @endif
                        </div>
                    </div>

                    {{-- GALLERY --}}
                    <div class="col-md-12">
                        <label class="form-label">Add More Images</label>
                        <input type="file" name="images[]" id="galleryInput" class="form-control" multiple
                            accept="image/*">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Current Gallery</label>
                        <div id="sortable-gallery" class="d-flex flex-wrap gap-3">
                            @php
                                $galleryImages = $product->gallery;
                                if (is_string($galleryImages)) {
                                    $galleryImages = json_decode($galleryImages, true);
                                }
                                $galleryImages = is_array($galleryImages) ? $galleryImages : [];
                            @endphp

                            @foreach ($galleryImages as $image)
                                <div class="gallery-item position-relative" data-image="{{ $image }}">
                                    <span class="gallery-order badge bg-dark position-absolute"
                                        style="top:5px;left:5px;z-index:2;">
                                        {{ $loop->iteration }}
                                    </span>
                                    <img src="{{ asset('backend/assets/images/product/' . $image) }}"
                                        class="rounded border"
                                        style="width:120px;height:120px;object-fit:cover;cursor:move;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_images[]"
                                            value="{{ $image }}" id="remove{{ $loop->index }}">
                                        <label class="form-check-label text-danger" for="remove{{ $loop->index }}">
                                            Remove
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="5">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Specification <span class="text-danger">*</span></label>
                        <textarea name="specification" id="specification" class="form-control" rows="5">{{ old('specification', $product->specification) }}</textarea>
                        @error('specification')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- FAQ SECTION -->
                    <div class="col-12 mt-4">
                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Product FAQs (Frequently Asked Questions)</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="addFaqBtn">
                                    + Add FAQ
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="faq-wrapper">
                                    @if ($product->faqs && $product->faqs->count() > 0)
                                        @foreach ($product->faqs as $index => $faq)
                                            <div class="faq-item border rounded p-3 mb-3"
                                                data-faq-id="{{ $faq->id }}">
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <label class="form-label fw-bold">Question
                                                                {{ $loop->iteration }}</label>
                                                            <div>
                                                                <input type="hidden"
                                                                    name="faqs[{{ $index }}][id]"
                                                                    value="{{ $faq->id }}">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger remove-faq-btn">
                                                                    <i class="ti ti-trash"></i> Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <input type="text" name="faqs[{{ $index }}][question]"
                                                            class="form-control"
                                                            placeholder="e.g., What is the warranty period?"
                                                            value="{{ $faq->question }}">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Answer</label>
                                                        <textarea name="faqs[{{ $index }}][answer]" class="form-control faq-answer" rows="3"
                                                            placeholder="Provide a detailed answer to the question...">{!! $faq->answer !!}</textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Status</label>
                                                        <select name="faqs[{{ $index }}][status]"
                                                            class="form-select">
                                                            <option value="1"
                                                                {{ $faq->status == 1 ? 'selected' : '' }}>Active</option>
                                                            <option value="0"
                                                                {{ $faq->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info" id="noFaqMessage">
                                            <i class="ti ti-info-circle"></i> No FAQs added yet. Click "Add FAQ" to add
                                            frequently asked questions about this product.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>
                            Update Product
                        </button>
                        <a href="{{ route('product.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_ids');
            if (categorySelect && typeof Choices !== 'undefined') {
                new Choices(categorySelect, {
                    removeItemButton: true,
                    searchEnabled: true,
                    shouldSort: false,
                    placeholder: true,
                    placeholderValue: 'Select categories',
                    itemSelectText: '',
                    position: 'bottom'
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ======= DYNAMIC DISCOUNT & GST CALCULATION =======
            let isUpdatingPricing = false;

            function updatePricingAndGst(source) {
                if (isUpdatingPricing) return;
                isUpdatingPricing = true;

                const priceInput = document.getElementById('price');
                const discountTypeSelect = document.getElementById('discount_type');
                const discountAmountInput = document.getElementById('discount_amount');
                const discountTypeSymbol = document.getElementById('discountTypeSymbol');
                const sellingPriceInput = document.getElementById('selling_price');
                const discountInput = document.getElementById('discount');
                const discountSavingsText = document.getElementById('discountSavingsText');
                const gstSelect = document.getElementById('gst_setting_id');

                const previewMrp = document.getElementById('previewMrp');
                const previewSellingPrice = document.getElementById('previewSellingPrice');
                const previewDiscount = document.getElementById('previewDiscount');
                const previewGstRateLabel = document.getElementById('previewGstRateLabel');
                const previewGstAmount = document.getElementById('previewGstAmount');

                if (!priceInput) {
                    isUpdatingPricing = false;
                    return;
                }

                const price = parseFloat(priceInput.value) || 0;
                const discountType = discountTypeSelect ? discountTypeSelect.value : 'percent';

                if (discountTypeSymbol) {
                    discountTypeSymbol.textContent = discountType === 'fixed' ? '₹' : '%';
                }

                let discountAmount = discountAmountInput ? (parseFloat(discountAmountInput.value) || 0) : 0;
                let sellingPrice = sellingPriceInput ? (parseFloat(sellingPriceInput.value) || 0) : 0;

                if (source === 'discount' || source === 'price') {
                    if (price > 0 && discountAmount > 0) {
                        if (discountType === 'percent') {
                            if (discountAmount > 100) {
                                discountAmount = 100;
                                if (discountAmountInput) discountAmountInput.value = 100;
                            }
                            sellingPrice = Math.max(0, price - (price * (discountAmount / 100)));
                        } else { // fixed
                            if (discountAmount > price) {
                                discountAmount = price;
                                if (discountAmountInput) discountAmountInput.value = price.toFixed(2);
                            }
                            sellingPrice = Math.max(0, price - discountAmount);
                        }
                        if (sellingPriceInput) {
                            sellingPriceInput.value = sellingPrice > 0 ? sellingPrice.toFixed(2) : (price > 0 ?
                                '0.00' : '');
                        }
                    } else if (price > 0 && (!discountAmountInput || !discountAmountInput.value)) {
                        // If discount is empty but selling price was already filled, re-derive discount
                        if (sellingPrice > 0 && sellingPrice < price) {
                            const savings = price - sellingPrice;
                            if (discountType === 'percent') {
                                const pct = Math.round((savings / price) * 100);
                                if (discountAmountInput) discountAmountInput.value = pct;
                            } else {
                                if (discountAmountInput) discountAmountInput.value = savings.toFixed(2);
                            }
                        }
                    }
                } else if (source === 'type') {
                    // Discount type changed: convert existing discount amount based on price & selling price
                    if (price > 0 && sellingPrice > 0 && sellingPrice < price) {
                        const savings = price - sellingPrice;
                        if (discountType === 'percent') {
                            const pct = Math.round((savings / price) * 100);
                            if (discountAmountInput) discountAmountInput.value = pct;
                        } else {
                            if (discountAmountInput) discountAmountInput.value = savings.toFixed(2);
                        }
                    } else if (price > 0 && discountAmount > 0) {
                        // Recalculate selling price with new type
                        if (discountType === 'percent') {
                            sellingPrice = Math.max(0, price - (price * (Math.min(100, discountAmount) / 100)));
                        } else {
                            sellingPrice = Math.max(0, price - Math.min(price, discountAmount));
                        }
                        if (sellingPriceInput) {
                            sellingPriceInput.value = sellingPrice > 0 ? sellingPrice.toFixed(2) : '0.00';
                        }
                    }
                } else if (source === 'selling_price') {
                    if (price > 0 && sellingPrice > 0) {
                        if (sellingPrice < price) {
                            const savings = price - sellingPrice;
                            if (discountType === 'percent') {
                                const pct = Math.round((savings / price) * 100);
                                if (discountAmountInput) discountAmountInput.value = pct;
                            } else {
                                if (discountAmountInput) discountAmountInput.value = savings.toFixed(2);
                            }
                        } else {
                            if (discountAmountInput) discountAmountInput.value = 0;
                        }
                    } else if (!sellingPriceInput.value) {
                        if (discountAmountInput) discountAmountInput.value = '';
                    }
                }

                // Sync hidden discount percent for database
                let effectiveDiscountPercent = 0;
                let effectiveSavings = 0;
                if (price > 0 && sellingPrice > 0 && sellingPrice < price) {
                    effectiveSavings = price - sellingPrice;
                    effectiveDiscountPercent = Math.round((effectiveSavings / price) * 100);
                } else if (price > 0 && discountAmount > 0 && discountType === 'percent') {
                    effectiveDiscountPercent = Math.min(100, Math.round(discountAmount));
                    effectiveSavings = price * (effectiveDiscountPercent / 100);
                }

                if (discountInput) {
                    discountInput.value = effectiveDiscountPercent;
                }

                if (discountSavingsText) {
                    if (effectiveDiscountPercent > 0) {
                        discountSavingsText.textContent = 'Save ₹' + effectiveSavings.toFixed(2) + ' (' +
                            effectiveDiscountPercent + '% off MRP)';
                        discountSavingsText.className = 'text-success small d-block mt-1';
                    } else if (price > 0 && sellingPrice >= price) {
                        discountSavingsText.textContent =
                            'Selling price is equal to or higher than MRP (No discount)';
                        discountSavingsText.className = 'text-muted small d-block mt-1';
                    } else {
                        discountSavingsText.textContent = 'Auto-calculates Selling Price from Discount';
                        discountSavingsText.className = 'text-muted small d-block mt-1';
                    }
                }

                // 2. GST Inclusive breakdown: Tax = (SellingPrice * Rate) / (100 + Rate)
                let gstRate = 18;
                if (gstSelect && gstSelect.selectedIndex >= 0) {
                    const selectedOpt = gstSelect.options[gstSelect.selectedIndex];
                    const rateAttr = selectedOpt ? selectedOpt.getAttribute('data-rate') : null;
                    if (rateAttr !== null && rateAttr !== '') {
                        gstRate = parseFloat(rateAttr) || 0;
                    }
                }

                const effectiveFinalPrice = sellingPrice > 0 ? sellingPrice : price;
                let taxAmount = 0;
                let basePrice = 0;

                if (effectiveFinalPrice > 0 && gstRate > 0) {
                    taxAmount = (effectiveFinalPrice * gstRate) / (100 + gstRate);
                    basePrice = effectiveFinalPrice - taxAmount;
                } else if (effectiveFinalPrice > 0) {
                    basePrice = effectiveFinalPrice;
                }

                // 3. Update preview box
                if (previewMrp) {
                    previewMrp.textContent = '₹' + price.toFixed(2);
                }
                if (previewSellingPrice) {
                    previewSellingPrice.textContent = sellingPrice > 0 ? '₹' + sellingPrice.toFixed(2) : (price >
                        0 ? '₹' + price.toFixed(2) + ' (Same as MRP)' : '—');
                }
                if (previewDiscount) {
                    if (effectiveDiscountPercent > 0) {
                        previewDiscount.innerHTML = '<span class="text-success">' + effectiveDiscountPercent +
                            '% OFF</span> <span class="text-muted small">(Save ₹' + effectiveSavings.toFixed(2) +
                            ')</span>';
                    } else {
                        previewDiscount.innerHTML = '<span class="text-muted">0% (No discount)</span>';
                    }
                }
                if (previewGstRateLabel) {
                    previewGstRateLabel.textContent = 'GST (' + gstRate + '% Included)';
                }
                if (previewGstAmount) {
                    if (effectiveFinalPrice > 0) {
                        previewGstAmount.innerHTML = '₹' + taxAmount.toFixed(2) +
                            ' <span class="text-muted fw-normal small">(Base: ₹' + basePrice.toFixed(2) +
                            ')</span>';
                    } else {
                        previewGstAmount.innerHTML =
                            '₹0.00 <span class="text-muted fw-normal small">(Base: ₹0.00)</span>';
                    }
                }

                isUpdatingPricing = false;
            }

            const priceEl = document.getElementById('price');
            const discountTypeEl = document.getElementById('discount_type');
            const discountAmountEl = document.getElementById('discount_amount');
            const sellingPriceEl = document.getElementById('selling_price');
            const gstSelectEl = document.getElementById('gst_setting_id');

            if (priceEl) {
                priceEl.addEventListener('input', function() {
                    updatePricingAndGst('price');
                });
                priceEl.addEventListener('change', function() {
                    updatePricingAndGst('price');
                });
            }
            if (discountTypeEl) {
                discountTypeEl.addEventListener('change', function() {
                    updatePricingAndGst('type');
                });
            }
            if (discountAmountEl) {
                discountAmountEl.addEventListener('input', function() {
                    updatePricingAndGst('discount');
                });
                discountAmountEl.addEventListener('change', function() {
                    updatePricingAndGst('discount');
                });
            }
            if (sellingPriceEl) {
                sellingPriceEl.addEventListener('input', function() {
                    updatePricingAndGst('selling_price');
                });
                sellingPriceEl.addEventListener('change', function() {
                    updatePricingAndGst('selling_price');
                });
            }
            if (gstSelectEl) {
                gstSelectEl.addEventListener('change', function() {
                    updatePricingAndGst('gst');
                });
            }

            // Run on page load
            updatePricingAndGst('init');

            // =========================================
            // AUTO SLUG
            // =========================================
            const nameInput = document.getElementById('name');
            if (nameInput) {
                nameInput.addEventListener('keyup', function() {
                    let slug = this.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                    document.getElementById('slug').value = slug;
                });
            }

            // =========================================
            // VIDEO PREVIEW
            // =========================================
            const videoInput = document.getElementById('videoInput');
            if (videoInput) {
                videoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('videoPreview');
                    const currentVideo = document.getElementById('currentVideo');

                    if (preview) preview.innerHTML = '';

                    if (file) {
                        if (currentVideo) currentVideo.innerHTML = '';
                        const url = URL.createObjectURL(file);
                        if (preview) {
                            preview.innerHTML = `
                        <video width="200" controls class="rounded border mt-2">
                            <source src="${url}" type="${file.type}">
                            Your browser does not support video.
                        </video>
                    `;
                        }
                    }
                });
            }

            // =========================================
            // THUMBNAIL PREVIEW
            // =========================================
            const thumbnailInput = document.getElementById('thumbnailInput');
            if (thumbnailInput) {
                thumbnailInput.addEventListener('change', function(e) {
                    const preview = document.getElementById('thumbnailPreview');
                    if (!preview) return;
                    preview.innerHTML = '';
                    if (e.target.files[0]) {
                        let reader = new FileReader();
                        reader.onload = function(event) {
                            preview.innerHTML = `
                        <img src="${event.target.result}"
                            class="rounded border"
                            style="width:120px;height:120px;object-fit:cover;">
                    `;
                        };
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });
            }

            // =========================================
            // GALLERY PREVIEW + SORTABLE
            // =========================================
            const galleryInput = document.getElementById('galleryInput');
            const sortableGallery = document.getElementById('sortable-gallery');

            if (galleryInput && sortableGallery) {
                galleryInput.addEventListener('change', function(e) {
                    Array.from(e.target.files).forEach((file, index) => {
                        let reader = new FileReader();
                        reader.onload = function(event) {
                            let uniqueId = 'new_' + Date.now() + index;
                            sortableGallery.insertAdjacentHTML('beforeend', `
                        <div class="gallery-item position-relative" data-image="${uniqueId}">
                            <span class="gallery-order badge bg-dark position-absolute" style="top:5px;left:5px;z-index:2;">
                                ${document.querySelectorAll('.gallery-item').length + 1}
                            </span>
                            <img src="${event.target.result}"
                                class="rounded border"
                                style="width:120px;height:120px;object-fit:cover;cursor:move;">
                        </div>
                    `);
                            updateGalleryOrder();
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

            if (sortableGallery && typeof Sortable !== 'undefined') {
                new Sortable(sortableGallery, {
                    animation: 150,
                    onEnd: function() {
                        updateGalleryOrder();
                    }
                });
            }

            function updateGalleryOrder() {
                if (!sortableGallery) return;
                document.querySelectorAll('input[name="gallery_order[]"]').forEach(el => el.remove());

                document.querySelectorAll('.gallery-item').forEach((item, index) => {
                    const orderBadge = item.querySelector('.gallery-order');
                    if (orderBadge) orderBadge.innerText = index + 1;

                    let image = item.dataset.image;
                    if (image && !image.startsWith('new_')) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'gallery_order[]';
                        input.value = image;
                        sortableGallery.appendChild(input);
                    }
                });
            }

            // =========================================
            // CKEDITOR (SAFE INITIALIZATION)
            // =========================================
            if (typeof ClassicEditor !== 'undefined') {
                const detailsEl = document.querySelector('#details');
                if (detailsEl) {
                    ClassicEditor.create(detailsEl).catch(error => console.error(error));
                }
                const descEl = document.querySelector('#description');
                if (descEl) {
                    ClassicEditor.create(descEl).catch(error => console.error(error));
                }
                const specEl = document.querySelector('#specification');
                if (specEl) {
                    ClassicEditor.create(specEl).catch(error => console.error(error));
                }
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productType = document.querySelector('select[name="product_type"]');
            const variantSection = document.getElementById('variant-section');
            const variantWrapper = document.getElementById('variant-wrapper');
            const addVariantBtn = document.getElementById('addVariantBtn');

            function toggleVariantSection() {
                if (!productType || !variantSection) return;
                if (productType.value === 'variable') {
                    variantSection.classList.remove('d-none');
                } else {
                    variantSection.classList.add('d-none');
                }
            }

            if (productType && variantSection) {
                toggleVariantSection();
                productType.addEventListener('change', toggleVariantSection);
            }

            function addAttributeRow(variantDiv, variantIndex) {
                const attributesContainer = variantDiv.querySelector('.attributes-container');
                if (!attributesContainer) return;

                const currentAttrCount = attributesContainer.querySelectorAll('.attribute-row').length;

                const newAttributeRow = document.createElement('div');
                newAttributeRow.className = 'row g-3 mt-2 attribute-row';
                newAttributeRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" 
                    name="variants[${variantIndex}][attributes][${currentAttrCount}][attribute]" 
                    class="form-control" 
                    placeholder="Attribute (e.g., Size, Color)">
            </div>
            <div class="col-md-5">
                <input type="text" 
                    name="variants[${variantIndex}][attributes][${currentAttrCount}][value]" 
                    class="form-control" 
                    placeholder="Value (e.g., XL, Red)">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger remove-attribute-btn">Remove</button>
            </div>
        `;

                attributesContainer.appendChild(newAttributeRow);
            }

            if (addVariantBtn && variantWrapper) {
                addVariantBtn.addEventListener('click', function() {
                    let index = document.querySelectorAll('.variant-item').length;
                    variantWrapper.insertAdjacentHTML('beforeend', `
                <div class="variant-item border rounded p-3 mb-3" data-variant-index="${index}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>SKU</label>
                            <input type="text" name="variants[${index}][sku]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Price</label>
                            <input type="number" step="0.01" name="variants[${index}][price]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Selling Price</label>
                            <input type="number" step="0.01" name="variants[${index}][selling_price]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" name="variants[${index}][quantity]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Image</label>
                            <input type="file" name="variants[${index}][image]" class="form-control">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger removeVariant">X</button>
                        </div>
                        <hr>
                        <div class="col-12">
                            <label class="fw-bold mb-2">Attributes</label>
                            <div class="attributes-container">
                                <div class="row g-3 mt-2 attribute-row">
                                    <div class="col-md-5">
                                        <input type="text" name="variants[${index}][attributes][0][attribute]" class="form-control" placeholder="Attribute (e.g., Size)">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="variants[${index}][attributes][0][value]" class="form-control" placeholder="Value (e.g., XL)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-attribute-btn">Remove</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary mt-3 add-attribute-btn" data-variant-index="${index}">+ Add More Attributes</button>
                        </div>
                    </div>
                </div>
            `);
                });
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-attribute-btn')) {
                    const variantDiv = e.target.closest('.variant-item');
                    const variantIndex = variantDiv.getAttribute('data-variant-index');
                    addAttributeRow(variantDiv, variantIndex);
                }

                if (e.target.classList.contains('remove-attribute-btn')) {
                    const attributeRow = e.target.closest('.attribute-row');
                    const attributesContainer = attributeRow.closest('.attributes-container');
                    if (attributesContainer.querySelectorAll('.attribute-row').length > 1) {
                        attributeRow.remove();
                    } else {
                        alert('At least one attribute row is per variant.');
                    }
                }

                if (e.target.classList.contains('removeVariant')) {
                    if (confirm('Are you sure you want to remove this variant?')) {
                        e.target.closest('.variant-item').remove();
                    }
                }
            });
        });
    </script>
    <script>
        // FAQ Management
        document.addEventListener('DOMContentLoaded', function() {
            const faqWrapper = document.getElementById('faq-wrapper');
            const addFaqBtn = document.getElementById('addFaqBtn');

            function decodeHtml(html) {
                if (!html) return '';
                const txt = document.createElement('textarea');
                txt.innerHTML = html;
                return txt.value;
            }

            function updateNoFaqMessage() {
                if (!faqWrapper) return;
                const faqItems = document.querySelectorAll('.faq-item');
                if (faqItems.length === 0) {
                    if (!document.getElementById('noFaqMessage')) {
                        const message = document.createElement('div');
                        message.id = 'noFaqMessage';
                        message.className = 'alert alert-info';
                        message.innerHTML =
                            '<i class="ti ti-info-circle"></i> No FAQs added yet. Click "Add FAQ" to add frequently asked questions about this product.';
                        faqWrapper.appendChild(message);
                    }
                } else {
                    const existingMessage = document.getElementById('noFaqMessage');
                    if (existingMessage) {
                        existingMessage.remove();
                    }
                }
            }

            function initializeExistingFaqs() {
                document.querySelectorAll('.faq-item .faq-answer').forEach(textarea => {
                    if (typeof ClassicEditor !== 'undefined') {
                        ClassicEditor.create(textarea, {
                            toolbar: {
                                items: [
                                    'bold', 'italic', 'underline', '|',
                                    'bulletedList', 'numberedList', '|',
                                    'link', '|',
                                    'undo', 'redo'
                                ]
                            }
                        }).catch(error => {
                            console.error('FAQ CKEditor error:', error);
                        });
                    }
                });
            }

            function addFaq(faqData = null) {
                if (!faqWrapper) return;
                const currentIndex = document.querySelectorAll('.faq-item').length;

                const faqDiv = document.createElement('div');
                faqDiv.className = 'faq-item border rounded p-3 mb-3';
                faqDiv.setAttribute('data-faq-index', currentIndex);

                const questionValue = faqData ? faqData.question.replace(/"/g, '&quot;') : '';
                const answerValue = faqData ? decodeHtml(faqData.answer) : '';
                const statusValue = faqData && faqData.status == 1 ? 1 : 1;

                faqDiv.innerHTML = `
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-start">
                        <label class="form-label fw-bold">Question ${currentIndex + 1}</label>
                        <button type="button" class="btn btn-sm btn-danger remove-faq-btn">
                            <i class="ti ti-trash"></i> Remove
                        </button>
                    </div>
                    <input type="text" 
                           name="faqs[${currentIndex}][question]" 
                           class="form-control" 
                           placeholder="e.g., What is the warranty period?"
                           value="${questionValue}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Answer</label>
                    <textarea name="faqs[${currentIndex}][answer]" 
                              class="form-control faq-answer" 
                              rows="3" 
                              placeholder="Provide a detailed answer to the question...">${answerValue}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="faqs[${currentIndex}][status]" class="form-select">
                        <option value="1" ${statusValue == 1 ? 'selected' : ''}>Active</option>
                        <option value="0" ${statusValue == 0 ? 'selected' : ''}>Inactive</option>
                    </select>
                </div>
            </div>
        `;

                faqWrapper.appendChild(faqDiv);

                const textarea = faqDiv.querySelector('.faq-answer');
                if (typeof ClassicEditor !== 'undefined' && textarea) {
                    ClassicEditor.create(textarea, {
                        toolbar: {
                            items: [
                                'bold', 'italic', 'underline', '|',
                                'bulletedList', 'numberedList', '|',
                                'link', '|',
                                'undo', 'redo'
                            ]
                        }
                    }).catch(error => {
                        console.error('FAQ CKEditor error:', error);
                    });
                }

                updateNoFaqMessage();
            }

            initializeExistingFaqs();

            if (addFaqBtn) {
                addFaqBtn.addEventListener('click', function() {
                    addFaq();
                });
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-faq-btn') || e.target.closest('.remove-faq-btn')) {
                    const btn = e.target.classList.contains('remove-faq-btn') ? e.target : e.target.closest(
                        '.remove-faq-btn');
                    const faqItem = btn.closest('.faq-item');
                    if (faqItem && confirm('Are you sure you want to remove this FAQ?')) {
                        faqItem.remove();
                        updateNoFaqMessage();

                        document.querySelectorAll('.faq-item').forEach((item, idx) => {
                            const label = item.querySelector('.fw-bold');
                            if (label) {
                                label.textContent = `Question ${idx + 1}`;
                            }
                        });
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_ids');
            const subcategorySelect = document.getElementById('subcategory_id');
            if (!categorySelect || !subcategorySelect) {
                return;
            }

            const selectedSubcategoryId = "{{ old('subcategory_id', $product->subcategory_id) }}";
            const urlTemplate = "{{ route('get.subcategories', ':category') }}";

            const renderSubcategories = function(items, selectedId = '') {
                subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                items.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    if (String(selectedId) === String(item.id)) {
                        option.selected = true;
                    }
                    subcategorySelect.appendChild(option);
                });
            };

            const getPrimaryCategoryId = function() {
                const selected = Array.from(categorySelect.selectedOptions).map(function(opt) {
                    return opt.value;
                });
                return selected[0] || '';
            };

            const loadSubcategories = function(categoryId, selectedId = '') {
                if (!categoryId) {
                    renderSubcategories([]);
                    return;
                }

                fetch(urlTemplate.replace(':category', categoryId))
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        renderSubcategories(Array.isArray(data) ? data : [], selectedId);
                    })
                    .catch(function() {
                        renderSubcategories([]);
                    });
            };

            categorySelect.addEventListener('change', function() {
                loadSubcategories(getPrimaryCategoryId());
            });

            const primaryId = getPrimaryCategoryId();
            if (primaryId && subcategorySelect.options.length <= 1) {
                loadSubcategories(primaryId, selectedSubcategoryId);
            }
        });
    </script>
@endpush
