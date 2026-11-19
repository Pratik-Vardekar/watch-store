@extends('layouts.admin')
@section('content')
    <div class="container-fluid my-2">
        <div class="mb-4">
            <h4 class="mb-1">Add Product</h4>
            <p class="text-muted mb-0">Create new product</p>
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
                <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}">
                            @error('name')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                class="form-control">
                            @error('slug')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Model No.</label>
                            <input type="text" name="model_no" class="form-control" value="{{ old('model_no') }}"
                                placeholder="Enter Model No.">
                            @error('model_no')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}"
                                placeholder="Enter SKU (optional)">
                            @error('sku')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Brand <span class="text-danger">*</span></label>

                            <div class="d-flex align-items-center gap-2">
                                <select name="brand_id" id="brand_id" class="form-select">
                                    <option value="">Select Brand</option>

                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <a href="{{ route('brands.create') }}"
                                    class="btn btn-primary d-flex align-items-center justify-content-center"
                                    style="height: 46px; width: 46px; min-width: 46px; padding: 0;" title="Add Brand">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>

                            @error('brand_id')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="gender" class="form-select">
                                <option value="">Gender Choose</option>
                                @foreach (\App\Models\Product::GENDERS as $value => $label)
                                    <option value="{{ $value }}" {{ old('gender') === $value ? 'selected' : '' }}>
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

                            <div class="d-flex align-items-center gap-2">
                                <div class="flex-grow-1">
                                    <select name="category_ids[]" id="category_ids" class="form-select" multiple
                                        placeholder="Select categories" style="min-width: 0;">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ in_array((int) $category->id, $selectedCategoryIds, true) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <a href="{{ route('category.create') }}"
                                    class="btn btn-primary d-flex align-items-center justify-content-center"
                                    style="height: 46px; width: 46px; min-width: 46px; padding: 0;" title="Add Category">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>

                            <small class="text-muted d-block mt-2">
                                Select one or more categories. The first selected category loads subcategories.
                            </small>

                            @error('category_ids')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Subcategory --}}
                        <div class="col-md-6">
                            <label class="form-label">Subcategory</label>

                            <div class="d-flex align-items-center gap-2">
                                <select name="subcategory_id" id="subcategory_id" class="form-select">
                                    <option value="">Select Subcategory</option>

                                    @foreach ($initialSubcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}"
                                            {{ (string) old('subcategory_id') === (string) $subcategory->id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <a href="{{ route('subcategory.create') }}"
                                    class="btn btn-primary d-flex align-items-center justify-content-center"
                                    style="height: 46px; width: 46px; min-width: 46px; padding: 0;"
                                    title="Add Sub Category">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>

                            @error('subcategory_id')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Pricing, Discount & GST Section --}}
                        <div class="col-md-3">
                            <label class="form-label">Price (MRP) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" name="price"
                                    value="{{ old('price') }}" class="form-control" id="price" placeholder="0.00">
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
                                <input type="number" step="0.01" min="0" name="selling_price"
                                    value="{{ old('selling_price') }}" class="form-control" id="selling_price"
                                    placeholder="0.00">
                            </div>
                            <small class="text-muted">Auto-calculated or custom</small>
                            @error('selling_price')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Discount Type</label>
                            <select name="discount_type" id="discount_type" class="form-select">
                                <option value="percent"
                                    {{ old('discount_type', 'percent') === 'percent' ? 'selected' : '' }}>Percentage (%)
                                </option>
                                <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Flat
                                    Amount (₹)</option>
                            </select>
                            <small class="text-muted">Choose % or Flat ₹</small>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Discount</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="discount_amount"
                                    class="form-control" value="{{ old('discount_amount', old('discount')) }}"
                                    id="discount_amount" placeholder="0">
                                <span class="input-group-text fw-bold text-success" id="discountTypeSymbol">%</span>
                            </div>
                            <input type="hidden" name="discount" id="discount" value="{{ old('discount', 0) }}">
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
                                    {{ empty(old('gst_setting_id')) ? 'selected' : '' }}>
                                    18% GST (Default Included)
                                </option>
                                @if (isset($gstSettings) && count($gstSettings) > 0)
                                    @foreach ($gstSettings as $gst)
                                        <option value="{{ $gst->id }}" data-rate="{{ $gst->gst_percentage }}"
                                            {{ (string) old('gst_setting_id') === (string) $gst->id ? 'selected' : '' }}>
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
                            <input type="number" min="0" name="quantity" value="{{ old('quantity', 1) }}"
                                class="form-control">
                            @error('quantity')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Product Type</label>
                            <select name="product_type" class="form-select">
                                <option value="simple" {{ old('product_type') === 'simple' ? 'selected' : '' }}>Simple
                                    Product</option>
                                <option value="variable" {{ old('product_type') === 'variable' ? 'selected' : '' }}>
                                    Variable Product</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div id="variant-section" class="col-12 d-none">
                            <div class="card border">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Product Variants</h5>
                                    <button type="button" class="btn btn-sm btn-primary" id="addVariantBtn">
                                        Add Variant
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="variant-wrapper"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label d-block mb-2">Product Labels</label>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_best_selling"
                                        value="1" {{ old('is_best_selling') ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        Best Selling
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1"
                                        {{ old('is_new_arrival') ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        New Arrival
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Video</label>
                            <input type="file" name="video" id="videoInput" class="form-control" accept="video/*">
                            <div id="videoPreview" class="mt-2"></div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Thumbnail <span class="text-danger">*</span></label>
                            <input type="file" name="thumbnail" id="thumbnail" class="form-control"
                                accept="image/*">
                        </div>
                        <div class="col-md-12">
                            <div id="thumbnail-preview-container" class="mt-2 d-none">
                                <img id="thumbnail-preview" src="" class="rounded border"
                                    style="width:120px;height:120px;object-fit:cover;">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Gallery Images</label>
                            <input type="file" name="gallery[]" id="images" class="form-control" multiple
                                accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label d-none" id="preview-label">
                                Gallery Preview
                            </label>
                            <div id="preview-container" class="d-flex flex-wrap gap-3"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="6">{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Specification <span class="text-danger">*</span></label>
                            <textarea name="specification" id="specification" class="form-control" rows="6">{{ old('specification') }}</textarea>
                            @error('specification')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
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
                                        <div class="alert alert-info" id="noFaqMessage">
                                            <i class="ti ti-info-circle"></i> No FAQs added yet. Click "Add FAQ" to add
                                            frequently asked questions about this product.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>
                                Save Product
                            </button>
                            <a href="{{ route('product.index') }}" class="btn btn-light">
                                Cancel
                            </a>
                        </div>
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

            // Run once on page load
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
                    if (!preview) return;
                    preview.innerHTML = '';
                    if (file) {
                        const url = URL.createObjectURL(file);
                        preview.innerHTML = `
                       <video width="200" controls class="rounded border mt-2">
                           <source src="${url}" type="${file.type}">
                           Your browser does not support video.
                       </video>
                   `;
                    }
                });
            }

            // =========================================
            // THUMBNAIL PREVIEW
            // =========================================
            const thumbInput = document.getElementById('thumbnail');
            if (thumbInput) {
                thumbInput.addEventListener('change', function(e) {
                    let file = e.target.files[0];
                    if (file) {
                        let reader = new FileReader();
                        reader.onload = function(event) {
                            document.getElementById('thumbnail-preview').src = event.target.result;
                            document.getElementById('thumbnail-preview-container').classList.remove(
                                'd-none');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // =========================================
            // GALLERY PREVIEW + REMOVE + SORT + PERSIST
            // =========================================
            let selectedFiles = [];
            const imagesInput = document.getElementById('images');
            const previewContainer = document.getElementById('preview-container');

            @if ($errors->any())
                loadSavedGallery();
            @else
                localStorage.removeItem('product_gallery');
            @endif

            if (imagesInput) {
                imagesInput.addEventListener('change', function(e) {
                    let newFiles = Array.from(e.target.files);
                    selectedFiles = [...selectedFiles, ...newFiles];
                    saveGalleryToStorage();
                    rebuildInputFiles();
                    renderGallery();
                });
            }

            function renderGallery() {
                if (!previewContainer) return;
                previewContainer.innerHTML = '';

                const previewLabel = document.getElementById('preview-label');
                if (selectedFiles.length > 0) {
                    if (previewLabel) previewLabel.classList.remove('d-none');
                } else {
                    if (previewLabel) previewLabel.classList.add('d-none');
                }

                selectedFiles.forEach((file, index) => {
                    const div = document.createElement('div');
                    div.classList.add('gallery-item');
                    div.dataset.index = index;
                    div.style.position = 'relative';
                    div.style.cursor = 'move';

                    div.innerHTML = `
                   <button type="button"
                       class="btn btn-danger btn-sm remove-image"
                       style="
                           position:absolute;
                           top:-10px;
                           right:-10px;
                           border-radius:50%;
                           width:28px;
                           height:28px;
                           padding:0;
                           z-index:5;
                       ">
                       ×
                   </button>
                   <span class="image-order"
                       style="
                           position:absolute;
                           top:5px;
                           left:5px;
                           background:#000;
                           color:#fff;
                           padding:2px 8px;
                           border-radius:20px;
                           font-size:12px;
                           z-index:2;
                       ">
                       ${index + 1}
                   </span>
                   <img
                       class="rounded border"
                       style="
                           width:120px;
                           height:120px;
                           object-fit:cover;
                       ">
               `;

                    const img = div.querySelector('img');

                    div.querySelector('.remove-image').addEventListener('click', function() {
                        let removeIndex = parseInt(div.dataset.index);
                        selectedFiles.splice(removeIndex, 1);
                        saveGalleryToStorage();
                        rebuildInputFiles();
                        renderGallery();
                    });

                    previewContainer.appendChild(div);

                    let reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            function rebuildInputFiles() {
                if (!imagesInput) return;
                let dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                imagesInput.files = dataTransfer.files;
            }

            if (previewContainer && typeof Sortable !== 'undefined') {
                new Sortable(previewContainer, {
                    animation: 150,
                    onEnd: function() {
                        let newOrder = [];
                        document.querySelectorAll('.gallery-item').forEach((item) => {
                            let currentIndex = parseInt(item.dataset.index);
                            newOrder.push(selectedFiles[currentIndex]);
                        });
                        selectedFiles = newOrder;
                        saveGalleryToStorage();
                        rebuildInputFiles();
                        renderGallery();
                    }
                });
            }

            function saveGalleryToStorage() {
                let promises = selectedFiles.map(file => {
                    return new Promise(resolve => {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            resolve({
                                name: file.name,
                                type: file.type,
                                data: e.target.result
                            });
                        };
                        reader.readAsDataURL(file);
                    });
                });

                Promise.all(promises).then(images => {
                    localStorage.setItem('product_gallery', JSON.stringify(images));
                });
            }

            function loadSavedGallery() {
                let saved = localStorage.getItem('product_gallery');
                if (!saved) return;
                try {
                    let images = JSON.parse(saved);
                    images.forEach(image => {
                        let arr = image.data.split(',');
                        let mime = arr[0].match(/:(.*?);/)[1];
                        let bstr = atob(arr[1]);
                        let n = bstr.length;
                        let u8arr = new Uint8Array(n);
                        while (n--) {
                            u8arr[n] = bstr.charCodeAt(n);
                        }
                        let file = new File([u8arr], image.name, {
                            type: mime
                        });
                        selectedFiles.push(file);
                    });
                    rebuildInputFiles();
                    renderGallery();
                } catch (e) {
                    console.error(e);
                }
            }

            document.querySelector('form')?.addEventListener('submit', function() {
                localStorage.removeItem('product_gallery');
            });

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
                newAttributeRow.className = 'row g-3 attribute-row mt-2';
                newAttributeRow.innerHTML = `
            <div class="col-md-5">
                <input type="text"
                    name="variants[${variantIndex}][attributes][${currentAttrCount}][attribute]"
                    class="form-control"
                    placeholder="Attribute (e.g., Size, Color, Material)">
            </div>
            <div class="col-md-5">
                <input type="text"
                    name="variants[${variantIndex}][attributes][${currentAttrCount}][value]"
                    class="form-control"
                    placeholder="Value (e.g., XL, Red, Steel)">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-attribute-btn">
                    Remove
                </button>
            </div>
        `;

                attributesContainer.appendChild(newAttributeRow);
            }

            if (addVariantBtn && variantWrapper) {
                addVariantBtn.addEventListener('click', function() {
                    let index = document.querySelectorAll('.variant-item').length;

                    variantWrapper.insertAdjacentHTML('beforeend', `
                <div class="variant-item border rounded p-3 mb-4" data-variant-index="${index}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>SKU</label>
                            <input type="text"
                                name="variants[${index}][sku]"
                                class="form-control"
                                placeholder="Unique SKU for this variant">
                        </div>
                        <div class="col-md-2">
                            <label>Price</label>
                            <input type="number"
                                step="0.01"
                                name="variants[${index}][price]"
                                class="form-control"
                                placeholder="Original price">
                        </div>
                        <div class="col-md-2">
                            <label>Selling Price</label>
                            <input type="number"
                                step="0.01"
                                name="variants[${index}][selling_price]"
                                class="form-control"
                                placeholder="Discounted price">
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number"
                                name="variants[${index}][quantity]"
                                class="form-control"
                                placeholder="Stock quantity">
                        </div>
                        <div class="col-md-2">
                            <label>Image</label>
                            <input type="file"
                                name="variants[${index}][image]"
                                class="form-control"
                                accept="image/*">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button"
                                class="btn btn-danger removeVariant">
                               X
                            </button>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="fw-bold mb-2">Attributes</label>
                            <div class="attributes-container">
                                <div class="row g-3 attribute-row mt-2">
                                    <div class="col-md-5">
                                        <input type="text"
                                            name="variants[${index}][attributes][0][attribute]"
                                            class="form-control"
                                            placeholder="Attribute (e.g., Size, Color, Material)">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text"
                                            name="variants[${index}][attributes][0][value]"
                                            class="form-control"
                                            placeholder="Value (e.g., XL, Red, Steel)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-attribute-btn">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" 
                                class="btn btn-sm btn-primary mt-3 add-attribute-btn"
                                data-variant-index="${index}">
                                + Add More Attributes
                            </button>
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
                        alert(
                            'At least one attribute is per variant. You can edit the existing one instead.');
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
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_ids');
            const subcategorySelect = document.getElementById('subcategory_id');
            if (!categorySelect || !subcategorySelect) {
                return;
            }

            const selectedSubcategoryId = "{{ old('subcategory_id') }}";
            const allSubcategories = @json($subcategories);

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

                const filtered = allSubcategories.filter(function(item) {
                    return String(item.category_id) === String(categoryId);
                });

                renderSubcategories(filtered, selectedId);
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
    <script>
        // FAQ Management
        document.addEventListener('DOMContentLoaded', function() {
            const faqWrapper = document.getElementById('faq-wrapper');
            const addFaqBtn = document.getElementById('addFaqBtn');
            const noFaqMessage = document.getElementById('noFaqMessage');

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

            function addFaq(faqData = null) {
                if (!faqWrapper) return;
                const currentIndex = document.querySelectorAll('.faq-item').length;

                const faqDiv = document.createElement('div');
                faqDiv.className = 'faq-item border rounded p-3 mb-3';
                faqDiv.setAttribute('data-faq-index', currentIndex);

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
                           value="${faqData ? faqData.question.replace(/"/g, '&quot;') : ''}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Answer</label>
                    <textarea name="faqs[${currentIndex}][answer]" 
                              class="form-control faq-answer" 
                              rows="3" 
                              placeholder="Provide a detailed answer to the question...">${faqData ? faqData.answer.replace(/"/g, '&quot;') : ''}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="faqs[${currentIndex}][status]" class="form-select">
                        <option value="1" ${faqData && faqData.status == 1 ? 'selected' : 'selected'}>Active</option>
                        <option value="0" ${faqData && faqData.status == 0 ? 'selected' : ''}>Inactive</option>
                    </select>
                </div>
            </div>
        `;

                faqWrapper.appendChild(faqDiv);

                if (typeof ClassicEditor !== 'undefined') {
                    const textarea = faqDiv.querySelector('.faq-answer');
                    if (textarea) {
                        ClassicEditor.create(textarea).catch(error => {
                            console.error(error);
                        });
                    }
                }

                updateNoFaqMessage();
            }

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
@endpush
