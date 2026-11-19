@extends('layouts.admin')
@section('content')
<div class="container-fluid my-2">
   <div class="mb-4">
      <h4 class="mb-1">Edit Product</h4>
      <p class="text-muted mb-0">Update product details</p>
   </div>
   <div class="card">
      <div class="card-body">
         <form action="{{ route('product.update', $product->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-4">
               <label class="form-label">Product Name</label>
               <input type="text"
                  name="name"
                  id="name"
                  class="form-control"
                  value="{{ $product->name }}"
                  required>
            </div>
            <div class="col-md-4">
               <label class="form-label">Slug</label>
               <input type="text"
                  name="slug"
                  id="slug"
                  class="form-control"
                  value="{{ $product->slug }}">
            </div>
            <div class="col-md-4">

                <label class="form-label">SKU</label>
                <input type="text"
                    name="sku"
                    class="form-control"
                    value="{{ $product->sku }}"
                    placeholder="Enter SKU">

            </div>
            <div class="col-md-6">
               <label class="form-label">Brand</label>
               <select name="brand_id"
                  id="brand_id"
                  class="form-select"
                  required>
                  <option value="">Select Brand</option>
                  @foreach($brands as $brand)
                  <option value="{{ $brand->id }}"
                  {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
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
               <select name="gender"
                  id="gender"
                  class="form-select"
                  required>
                  <option value="">Select Gender</option>
                  @foreach(\App\Models\Product::GENDERS as $value => $label)
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
            @php
               $selectedCategoryIds = collect(
                     old(
                        'category_ids',
                        $product->relationLoaded('categories') && $product->categories->isNotEmpty()
                           ? $product->categories->pluck('id')->all()
                           : ($product->category_id ? [$product->category_id] : [])
                     )
                  )
                  ->filter()
                  ->map(fn ($id) => (int) $id)
                  ->all();
            @endphp
            <div class="col-md-6">
               <label class="form-label">Categories</label>
               <select name="category_ids[]" id="category_ids" class="form-select" multiple placeholder="Select categories">
                  @foreach($categories as $category)
                     <option value="{{ $category->id }}" {{ in_array((int) $category->id, $selectedCategoryIds, true) ? 'selected' : '' }}>
                        {{ $category->name }}
                     </option>
                  @endforeach
               </select>
               <small class="text-muted">Hold Ctrl/Cmd to select multiple, or use the search box.</small>
               @error('category_ids')
               <small class="text-danger d-block">{{ $message }}</small>
               @enderror
               @error('category_ids.*')
               <small class="text-danger d-block">{{ $message }}</small>
               @enderror
            </div>
            <div class="col-md-6">
               <label class="form-label">Subcategory</label>
               <select name="subcategory_id" id="subcategory_id" class="form-select">
                  <option value="">Select Subcategory</option>
                  @foreach($subcategories as $subcategory)
                     <option value="{{ $subcategory->id }}" {{ (string) old('subcategory_id', $product->subcategory_id) === (string) $subcategory->id ? 'selected' : '' }}>
                        {{ $subcategory->name }}
                     </option>
                  @endforeach
               </select>
               @error('subcategory_id')
               <small class="text-danger">{{ $message }}</small>
               @enderror
            </div>
            <!-- Price, Selling Price, Discount Dynamic Calculation -->
            <div class="col-md-4">
               <label class="form-label">Price</label>
               <input type="number"
                  name="price"
                  id="price"
                  class="form-control"
                  value="{{ $product->price }}"
                  required
                  step="0.01"
                  min="0">
            </div>
            <div class="col-md-4">
               <label class="form-label">Selling Price</label>
               <input type="number"
                  name="selling_price"
                  id="selling_price"
                  class="form-control"
                  value="{{ old('selling_price', $product->selling_price) }}"
                  required
                  step="0.01"
                  min="0">
            </div>
            <div class="col-md-4">
               <label class="form-label">Discount %</label>
               <div class="input-group">
                  <input type="number"
                     name="discount"
                     class="form-control"
                     id="discount"
                     value="{{ old('discount', $product->discount) }}"
                     readonly>
                  <span class="input-group-text bg-light" id="discountDynamicInfo" style="min-width:100px;"></span>
               </div>
            </div>
            <script>
                function updateDiscountPercentage() {
                    const price = parseFloat(document.getElementById('price').value) || 0;
                    const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
                    const discountInput = document.getElementById('discount');
                    const discountInfo = document.getElementById('discountDynamicInfo');
                    let discount = 0;

                    if (price > 0 && sellingPrice > 0 && sellingPrice < price) {
                        discount = Math.round(((price - sellingPrice) / price) * 100);
                        discountInput.value = discount;
                        discountInfo.textContent = discount + '% OFF';
                    } else {
                        discountInput.value = 0;
                        discountInfo.textContent = '0%';
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('price').addEventListener('input', updateDiscountPercentage);
                    document.getElementById('selling_price').addEventListener('input', updateDiscountPercentage);
                    updateDiscountPercentage();
                });
            </script>
            <!-- /Price, Selling Price, Discount Dynamic Calculation -->

            @include('admin.product.partials.gst-tax-fields', ['selectedGstId' => old('gst_setting_id', $product->gst_setting_id)])
            <div class="col-md-4">
               <label class="form-label">Quantity</label>
               <input type="number"
                  name="quantity"
                  class="form-control"
                  value="{{ $product->quantity }}"
                  required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Product Type</label>
                <select name="product_type" class="form-select" required>
                    <option value="simple"
                        {{ $product->product_type == 'simple' ? 'selected' : '' }}>
                        Simple Product
                    </option>
                    <option value="variable"
                        {{ $product->product_type == 'variable' ? 'selected' : '' }}>
                        Variable Product
                    </option>
                </select>
            </div>
{{-- VARIANT SECTION --}}
<div id="variant-section" class="col-12 {{ $product->product_type == 'variable' ? '' : 'd-none' }}">
    <div class="card border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Product Variants</h5>
            <button type="button" class="btn btn-sm btn-primary" id="addVariantBtn">
                Add Variant
            </button>
        </div>
        <div class="card-body">
            <div id="variant-wrapper">
                @if($product->variants && $product->variants->count())
                    @foreach($product->variants as $index => $variant)
                        <div class="variant-item border rounded p-3 mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label>SKU</label>
                                    <input type="text" name="variants[{{ $index }}][sku]" class="form-control" value="{{ $variant->sku }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Price</label>
                                    <input type="number" step="0.01" name="variants[{{ $index }}][price]" class="form-control" value="{{ $variant->price }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Selling Price</label>
                                    <input type="number" step="0.01" name="variants[{{ $index }}][selling_price]" class="form-control" value="{{ $variant->selling_price }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Quantity</label>
                                    <input type="number" name="variants[{{ $index }}][quantity]" class="form-control" value="{{ $variant->quantity }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Image</label>
                                    <input type="file" name="variants[{{ $index }}][image]" class="form-control">
                                    @if($variant->image)
                                        <small class="text-muted">Current: {{ $variant->image }}</small>
                                    @endif
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger removeVariant">X</button>
                                </div>
                                <hr>
                                {{-- Display existing attributes --}}
                                <div class="col-12">
                                    <label class="fw-bold">Attributes</label>
                                    @php
                                        $attributeCount = $variant->variantAttributes->count();
                                    @endphp
                                    @if($attributeCount > 0)
                                        @foreach($variant->variantAttributes as $attrIndex => $variantAttr)
                                            <div class="row mt-2 attribute-row">
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
                                                    <button type="button" class="btn btn-sm btn-danger remove-attribute">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row mt-2 attribute-row">
                                            <div class="col-md-5">
                                                <input type="text" name="variants[{{ $index }}][attributes][0][attribute]" class="form-control" placeholder="Attribute (e.g., Size)">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="variants[{{ $index }}][attributes][0][value]" class="form-control" placeholder="Value (e.g., XL)">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-sm btn-danger remove-attribute">Remove</button>
                                            </div>
                                        </div>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-secondary mt-2 add-attribute-btn" data-index="{{ $index }}">+ Add Attribute</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
            <div class="col-md-4">
               <label class="form-label">Status</label>
               <select name="status"
                  class="form-select">
               <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>
               Active
               </option>
               <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>
               Inactive
               </option>
               </select>
            </div>
            <div class="col-md-6">
               <label class="form-label d-block mb-2">
               Product Labels
               </label>
               <div class="d-flex flex-wrap gap-4">
                  <div class="form-check">
                     <input class="form-check-input"
                     type="checkbox"
                     name="is_trending"
                     value="1"
                     {{ $product->is_trending ? 'checked' : '' }}>
                     <label class="form-check-label">
                     Trending
                     </label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input"
                     type="checkbox"
                     name="is_featured"
                     value="1"
                     {{ $product->is_featured ? 'checked' : '' }}>
                     <label class="form-check-label">
                     Featured
                     </label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input"
                     type="checkbox"
                     name="is_best_selling"
                     value="1"
                     {{ $product->is_best_selling ? 'checked' : '' }}>
                     <label class="form-check-label">
                     Best Selling
                     </label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input"
                     type="checkbox"
                     name="is_top_rated"
                     value="1"
                     {{ $product->is_top_rated ? 'checked' : '' }}>
                     <label class="form-check-label">
                     Top Rated
                     </label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input"
                     type="checkbox"
                     name="is_new_arrival"
                     value="1"
                     {{ $product->is_new_arrival ? 'checked' : '' }}>
                     <label class="form-check-label">
                     New Arrival
                     </label>
                  </div>
               </div>
            </div>
             
            <div class="col-md-6">
                <label class="form-label">Badge</label>
                <input type="text"
                    name="badge"
                    class="form-control"
                    placeholder="BESTSELLER" value="{{ $product->badge }}"> 
            </div>
            <div class="col-md-6">
                <label class="form-label">Product Video</label>
                <input type="file"
                    name="video"
                    id="videoInput"
                    class="form-control"
                    accept="video/*">
            </div>

            <div class="col-md-6">
                <label class="form-label d-block">Video Preview</label>

                <div id="currentVideo">
                    @if($product->video)
                        <video width="200" controls class="rounded border">
                            <source src="{{ asset('backend/assets/videos/product/' . $product->video) }}">
                        </video>
                    @else
                        <p class="text-muted">No video uploaded</p>
                    @endif
                </div>

                <div id="videoPreview"></div>
            </div>
            <input type="hidden" name="page" value="{{ $page }}">

            {{-- THUMBNAIL --}}
            <div class="col-md-12">
               <label class="form-label">Thumbnail</label>
               <input type="file"
                  name="thumbnail"
                  id="thumbnailInput"
                  class="form-control"
                  accept="image/*">
            </div>
            <div class="col-md-12">
               <div id="thumbnailPreview">
                  @if($product->thumbnail)
                  <img src="{{ asset('backend/assets/images/product/' . $product->thumbnail) }}"
                     class="rounded border"
                     style="
                     width:120px;
                     height:120px;
                     object-fit:cover;
                     ">
                  @endif
               </div>
            </div>
            {{-- GALLERY --}}
            <div class="col-md-12">
               <label class="form-label">Add More Images</label>
               <input type="file"
                  name="images[]"
                  id="galleryInput"
                  class="form-control"
                  multiple
                  accept="image/*">
            </div>
            <div class="col-12">
               <label class="form-label">Current Gallery</label>
               <div id="sortable-gallery"
                  class="d-flex flex-wrap gap-3">
                  @php
                    $galleryImages = $product->gallery;
                    if (is_string($galleryImages)) {
                        $galleryImages = json_decode($galleryImages, true);
                    }
                    $galleryImages = is_array($galleryImages) ? $galleryImages : [];
                  @endphp

                  @foreach($galleryImages as $image)
                  <div class="gallery-item position-relative"
                     data-image="{{ $image }}">
                     <span class="gallery-order badge bg-dark position-absolute"
                        style="
                        top:5px;
                        left:5px;
                        z-index:2;
                        ">
                     {{ $loop->iteration }}
                     </span>
                     <img src="{{ asset('backend/assets/images/product/' . $image) }}"
                        class="rounded border"
                        style="
                        width:120px;
                        height:120px;
                        object-fit:cover;
                        cursor:move;
                        ">
                     <div class="form-check mt-2">
                        <input class="form-check-input"
                           type="checkbox"
                           name="remove_images[]"
                           value="{{ $image }}"
                           id="remove{{ $loop->index }}">
                        <label class="form-check-label text-danger"
                           for="remove{{ $loop->index }}">
                        Remove
                        </label>
                     </div>
                  </div>
                  @endforeach
               </div>
            </div>
            {{-- SCRIPTS --}}
            <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet"/>
            <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
            <script>
               document.addEventListener('DOMContentLoaded', function () {
                   if (document.getElementById('category_ids')) {
                       new TomSelect('#category_ids', {
                           plugins: ['remove_button'],
                           create: false,
                           maxItems: null,
                           hidePlaceholder: true,
                           placeholder: 'Select categories',
                           dropdownParent: 'body'
                       });
                   }
               });
            </script>
            <script>
               document.addEventListener('DOMContentLoaded', function () {
               
                   // =========================================
                   // THUMBNAIL PREVIEW
                   // =========================================
               
                   const thumbnailInput = document.getElementById('thumbnailInput');
               
                   if (thumbnailInput) {
               
                       thumbnailInput.addEventListener('change', function(e){
               
                           const preview = document.getElementById('thumbnailPreview');
               
                           preview.innerHTML = '';
               
                           if(e.target.files[0]){
               
                               let reader = new FileReader();
               
                               reader.onload = function(event){
               
                                   preview.innerHTML = `
                                       <img src="${event.target.result}"
                                           class="rounded border"
                                           style="
                                               width:120px;
                                               height:120px;
                                               object-fit:cover;
                                           ">
                                   `;
               
                               };
               
                               reader.readAsDataURL(e.target.files[0]);
               
                           }
               
                       });
               
                   }
               
                   // =========================================
                   // GALLERY PREVIEW
                   // =========================================
               
                   const galleryInput = document.getElementById('galleryInput');
               
                   if (galleryInput) {
               
                       galleryInput.addEventListener('change', function(e){
               
                           const gallery = document.getElementById('sortable-gallery');
               
                           Array.from(e.target.files).forEach((file, index) => {
               
                               let reader = new FileReader();
               
                               reader.onload = function(event){
               
                                   let uniqueId = 'new_' + Date.now() + index;
               
                                   gallery.insertAdjacentHTML('beforeend', `
               
                                       <div class="gallery-item position-relative"
                                           data-image="${uniqueId}">
               
                                           <span class="gallery-order badge bg-dark position-absolute"
                                               style="
                                                   top:5px;
                                                   left:5px;
                                                   z-index:2;
                                               ">
               
                                               ${document.querySelectorAll('.gallery-item').length + 1}
               
                                           </span>
               
                                           <img src="${event.target.result}"
                                               class="rounded border"
                                               style="
                                                   width:120px;
                                                   height:120px;
                                                   object-fit:cover;
                                                   cursor:move;
                                               ">
               
                                       </div>
               
                                   `);
               
                                   updateGalleryOrder();
               
                               };
               
                               reader.readAsDataURL(file);
               
                           });
               
                       });
               
                   }
               
                   // =========================================
                   // SORTABLE
                   // =========================================
               
                   let sortableGallery = document.getElementById('sortable-gallery');
               
                   if(sortableGallery){
               
                       new Sortable(sortableGallery, {
               
                           animation: 150,
               
                           onEnd: function () {
               
                               updateGalleryOrder();
               
                           }
               
                       });
               
                   }
               
                   // =========================================
                   // UPDATE ORDER
                   // =========================================
               
                 function updateGalleryOrder() {
               
                   // Remove old hidden inputs
               
                   document.querySelectorAll('input[name="gallery_order[]"]')
                       .forEach(el => el.remove());
               
                   // Update Order Numbers
               
                   document.querySelectorAll('.gallery-item')
                       .forEach((item, index) => {
               
                           item.querySelector('.gallery-order')
                               .innerText = index + 1;
               
                           let image = item.dataset.image;
               
                           // Only existing images need hidden input
               
                           if(!image.startsWith('new_')) {
               
                               let input = document.createElement('input');
               
                               input.type = 'hidden';
               
                               input.name = 'gallery_order[]';
               
                               input.value = image;
               
                               sortableGallery.appendChild(input);
                           }
               
                       });
               
               }
               
               });
               
            </script>
      
      {{-- <div class="col-12">
      <label class="form-label">Short Details</label>
      <textarea name="details"
         id="details"
         class="form-control"
         rows="3">{{ $product->details }}</textarea>
      </div> --}}
      <div class="col-12">
      <label class="form-label">Description</label>
      <textarea name="description"
         id="description"
         class="form-control"
         rows="5">{{ $product->description }}</textarea>
      </div>
      <div class="col-12">
      <label class="form-label">Specification</label>
      <textarea name="specification"
         id="specification"
         class="form-control"
         rows="5">{{ $product->specification }}</textarea>
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
                @if($product->faqs && $product->faqs->count() > 0)
                    @foreach($product->faqs as $index => $faq)
                        <div class="faq-item border rounded p-3 mb-3" data-faq-id="{{ $faq->id }}">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <label class="form-label fw-bold">Question {{ $loop->iteration }}</label>
                                        <div>
                                            <input type="hidden" name="faqs[{{ $index }}][id]" value="{{ $faq->id }}">
                                            <button type="button" class="btn btn-sm btn-danger remove-faq-btn">
                                                <i class="ti ti-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    <input type="text" 
                                           name="faqs[{{ $index }}][question]" 
                                           class="form-control" 
                                           placeholder="e.g., What is the warranty period?"
                                           value="{{ $faq->question }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Answer</label>
                                    <textarea name="faqs[{{ $index }}][answer]" 
                                              class="form-control faq-answer" 
                                              rows="3" 
                                              placeholder="Provide a detailed answer to the question...">{{ $faq->answer }}</textarea>
                                </div>
                                {{-- Order field commented out
                                <div class="col-md-6">
                                    <label class="form-label">Order</label>
                                    <input type="number" 
                                           name="faqs[{{ $index }}][order]" 
                                           class="form-control" 
                                           value="{{ $faq->order ?? $loop->iteration }}"
                                           placeholder="Display order">
                                </div>
                                --}}
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="faqs[{{ $index }}][status]" class="form-select">
                                        <option value="1" {{ $faq->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $faq->status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info" id="noFaqMessage">
                        <i class="ti ti-info-circle"></i> No FAQs added yet. Click "Add FAQ" to add frequently asked questions about this product.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add the FAQ JavaScript (same as above) -->
      <div class="col-12 mt-2">
      <button type="submit"
         class="btn btn-primary">
      <i class="ti ti-device-floppy me-1"></i>
      Update Product
      </button>
      <a href="{{ route('product.index') }}"
         class="btn btn-light">
      Cancel
      </a>
      </div>
      </form>
   </div>
 </div>
<script>
   document.addEventListener('DOMContentLoaded', function () {
   
       let sortableGallery = document.getElementById('sortable-gallery');
   
       if(sortableGallery){
   
           new Sortable(sortableGallery, {
   
               animation: 150,
   
               onEnd: function () {
   
                   // Update Order Numbers
   
                   document.querySelectorAll('.gallery-item')
                       .forEach((item, index) => {
   
                       item.querySelector('.gallery-order')
                           .innerText = index + 1;
   
                   });
   
                   // Update Hidden Inputs Order
   
                   let inputs = [];
   
                   document.querySelectorAll('.gallery-item')
                       .forEach((item) => {
   
                       let image = item.dataset.image;
   
                       inputs.push(
                           `<input type="hidden" name="gallery_order[]" value="${image}">`
                       );
   
                   });
   
                   document.querySelectorAll('input[name="gallery_order[]"]')
                       .forEach(el => el.remove());
   
                   sortableGallery.insertAdjacentHTML(
                       'beforeend',
                       inputs.join('')
                   );
   
               }
   
           });
   
       }
   
   });
   
</script>
<script>
   document.getElementById('name').addEventListener('keyup', function () {
   
       let slug = this.value
           .toLowerCase()
           .trim()
           .replace(/[^a-z0-9\s-]/g, '')
           .replace(/\s+/g, '-')
           .replace(/-+/g, '-');
   
       document.getElementById('slug').value = slug;
   
   });
   
</script>
<script>
   document.addEventListener('DOMContentLoaded', function () {
   
       // Auto Slug
   
       document.getElementById('name').addEventListener('keyup', function () {
   
           let slug = this.value
               .toLowerCase()
               .trim()
               .replace(/[^a-z0-9\s-]/g, '')
               .replace(/\s+/g, '-')
               .replace(/-+/g, '-');
   
           document.getElementById('slug').value = slug;
   
       });
   
       // CKEditor
   
       ClassicEditor
           .create(document.querySelector('#details'))
           .catch(error => {
               console.error(error);
           });
   
       ClassicEditor
           .create(document.querySelector('#description'))
           .catch(error => {
               console.error(error);
           });
   
       ClassicEditor
           .create(document.querySelector('#specification'))
           .catch(error => {
               console.error(error);
           });
   
   });
   
</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productType = document.querySelector('select[name="product_type"]');
    const variantSection = document.getElementById('variant-section');
    const variantWrapper = document.getElementById('variant-wrapper');
    const addVariantBtn = document.getElementById('addVariantBtn');

    function toggleVariantSection() {
        if(productType.value === 'variable') {
            variantSection.classList.remove('d-none');
        } else {
            variantSection.classList.add('d-none');
        }
    }

    toggleVariantSection();
    productType.addEventListener('change', toggleVariantSection);

    // Add new variant
    addVariantBtn.addEventListener('click', function () {
        let index = document.querySelectorAll('.variant-item').length;
        variantWrapper.insertAdjacentHTML('beforeend', `
            <div class="variant-item border rounded p-3 mb-3">
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
                        <label class="fw-bold">Attributes</label>
                        <div class="row mt-2 attribute-row">
                            <div class="col-md-5">
                                <input type="text" name="variants[${index}][attributes][0][attribute]" class="form-control" placeholder="Attribute (e.g., Size)">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="variants[${index}][attributes][0][value]" class="form-control" placeholder="Value (e.g., XL)">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-attribute">Remove</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2 add-attribute-btn" data-index="${index}">+ Add Attribute</button>
                    </div>
                </div>
            </div>
        `);
    });

    // Remove variant
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('removeVariant')){
            e.target.closest('.variant-item').remove();
        }
    });

    // Add attribute to variant (dynamic)
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('add-attribute-btn')){
            let variantIndex = e.target.getAttribute('data-index');
            let variantItem = e.target.closest('.variant-item');
            let attributeCount = variantItem.querySelectorAll('.attribute-row').length;
            
            let newAttributeRow = document.createElement('div');
            newAttributeRow.className = 'row mt-2 attribute-row';
            newAttributeRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="variants[${variantIndex}][attributes][${attributeCount}][attribute]" class="form-control" placeholder="Attribute (e.g., Size)">
                </div>
                <div class="col-md-5">
                    <input type="text" name="variants[${variantIndex}][attributes][${attributeCount}][value]" class="form-control" placeholder="Value (e.g., XL)">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-attribute">Remove</button>
                </div>
            `;
            
            e.target.parentElement.insertBefore(newAttributeRow, e.target);
        }
    });

    // Remove attribute row
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('remove-attribute')){
            e.target.closest('.attribute-row').remove();
        }
    });
});
</script>
<script>
// FAQ Management
document.addEventListener('DOMContentLoaded', function () {
    let faqIndex = 0;
    const faqWrapper = document.getElementById('faq-wrapper');
    const addFaqBtn = document.getElementById('addFaqBtn');
    const noFaqMessage = document.getElementById('noFaqMessage');
    
    // Helper function to decode HTML entities
    function decodeHtml(html) {
        if (!html) return '';
        const txt = document.createElement('textarea');
        txt.innerHTML = html;
        return txt.value;
    }
    
    function updateNoFaqMessage() {
        const faqItems = document.querySelectorAll('.faq-item');
        if (faqItems.length === 0) {
            if (!document.getElementById('noFaqMessage')) {
                const message = document.createElement('div');
                message.id = 'noFaqMessage';
                message.className = 'alert alert-info';
                message.innerHTML = '<i class="ti ti-info-circle"></i> No FAQs added yet. Click "Add FAQ" to add frequently asked questions about this product.';
                faqWrapper.appendChild(message);
            }
        } else {
            const existingMessage = document.getElementById('noFaqMessage');
            if (existingMessage) {
                existingMessage.remove();
            }
        }
    }
    
    // Initialize existing FAQs with CKEditor
    function initializeExistingFaqs() {
        document.querySelectorAll('.faq-item .faq-answer').forEach(textarea => {
            // Decode HTML content if present
            if (textarea.value) {
                textarea.value = decodeHtml(textarea.value);
            }
            
            ClassicEditor
                .create(textarea, {
                    toolbar: {
                        items: [
                            'bold', 'italic', 'underline', '|',
                            'bulletedList', 'numberedList', '|',
                            'link', '|',
                            'undo', 'redo'
                        ]
                    }
                })
                .catch(error => {
                    console.error('FAQ CKEditor error:', error);
                });
        });
    }
    
    function addFaq(faqData = null) {
        const currentIndex = document.querySelectorAll('.faq-item').length;
        
        const faqDiv = document.createElement('div');
        faqDiv.className = 'faq-item border rounded p-3 mb-3';
        faqDiv.setAttribute('data-faq-index', currentIndex);
        
        const questionValue = faqData ? faqData.question.replace(/"/g, '&quot;') : '';
        const answerValue = faqData ? decodeHtml(faqData.answer) : '';
        // const orderValue = faqData ? faqData.order : currentIndex + 1;
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
                <!-- Order field commented out
                <div class="col-md-6">
                    <label class="form-label">Order</label>
                    <input type="number" 
                           name="faqs[${currentIndex}][order]" 
                           class="form-control" 
                           value=""
                           placeholder="Display order">
                </div>
                -->
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
        
        // Initialize CKEditor for the new FAQ answer
        const textarea = faqDiv.querySelector('.faq-answer');
        ClassicEditor
            .create(textarea, {
                toolbar: {
                    items: [
                        'bold', 'italic', 'underline', '|',
                        'bulletedList', 'numberedList', '|',
                        'link', '|',
                        'undo', 'redo'
                    ]
                }
            })
            .catch(error => {
                console.error('FAQ CKEditor error:', error);
            });
        
        updateNoFaqMessage();
    }
    
    // Initialize existing FAQs on page load
    initializeExistingFaqs();
    
    // Add FAQ button click
    if (addFaqBtn) {
        addFaqBtn.addEventListener('click', function() {
            addFaq();
        });
    }
    
    // Remove FAQ
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-faq-btn') || e.target.closest('.remove-faq-btn')) {
            const btn = e.target.classList.contains('remove-faq-btn') ? e.target : e.target.closest('.remove-faq-btn');
            const faqItem = btn.closest('.faq-item');
            if (faqItem && confirm('Are you sure you want to remove this FAQ?')) {
                faqItem.remove();
                updateNoFaqMessage();
                
                // Update question numbers
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
document.getElementById('videoInput')?.addEventListener('change', function (e) {

    const file = e.target.files[0];
    const preview = document.getElementById('videoPreview');
    const currentVideo = document.getElementById('currentVideo');

    preview.innerHTML = '';

    if (file) {

        // Completely remove old video preview
        currentVideo.innerHTML = '';

        const url = URL.createObjectURL(file);

        preview.innerHTML = `
            <video width="200" controls class="rounded border">
                <source src="${url}" type="${file.type}">
                Your browser does not support video.
            </video>
        `;

    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    if (!categorySelect || !subcategorySelect) {
        return;
    }

    const selectedSubcategoryId = "{{ old('subcategory_id', $product->subcategory_id) }}";
    const urlTemplate = "{{ route('get.subcategories', ':category') }}";

    const renderSubcategories = function (items, selectedId = '') {
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        items.forEach(function (item) {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            if (String(selectedId) === String(item.id)) {
                option.selected = true;
            }
            subcategorySelect.appendChild(option);
        });
    };

    const loadSubcategories = function (categoryId, selectedId = '') {
        if (!categoryId) {
            renderSubcategories([]);
            return;
        }

        fetch(urlTemplate.replace(':category', categoryId))
            .then(function (response) { return response.json(); })
            .then(function (data) { renderSubcategories(Array.isArray(data) ? data : [], selectedId); })
            .catch(function () { renderSubcategories([]); });
    };

    categorySelect.addEventListener('change', function () {
        loadSubcategories(this.value);
    });

    if (categorySelect.value && subcategorySelect.options.length <= 1) {
        loadSubcategories(categorySelect.value, selectedSubcategoryId);
    }
});
</script>
@endsection