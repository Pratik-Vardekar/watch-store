@extends('layouts.admin')
@section('content')
<div class="container-fluid my-2">
<div class="mb-4">
   <h4 class="mb-1">Add Product</h4>
   <p class="text-muted mb-0">Create new product</p>
</div>
<div class="card">
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
   <form action="{{ route('product.store') }}"
      method="POST"
      enctype="multipart/form-data"
      >
      <div class="row g-3">
      @csrf
      <div class="col-md-4">
         <label class="form-label">Product Name</label>
         <input type="text"
            name="name"
            id="name"
            class="form-control"
            value="{{ old('name') }}"
            required>
         @error('name')
         <small class="text-danger">
         {{ $message }}
         </small>
         @enderror
      </div>
      <div class="col-md-4">
         <label class="form-label">Slug</label>
         <input type="text"
            name="slug"
            id="slug"
            value="{{ old('slug') }}"
            class="form-control">
      </div>
      <div class="col-md-4">
         <label class="form-label">SKU</label>
         <input type="text"
            name="sku"
            class="form-control"
            value="{{ old('sku') }}"
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
            {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
            {{ old('gender') === $value ? 'selected' : '' }}>
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
         $selectedCategoryIds = collect(old('category_ids', old('category_id') ? [old('category_id')] : []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();
         $initialSubcategories = $subcategories
            ->when(!empty($selectedCategoryIds), fn ($q) => $q->whereIn('category_id', $selectedCategoryIds))
            ->values();
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
      {{-- <div class="col-md-6">
         <label class="form-label">Subcategory</label>
         <select name="subcategory_id" id="subcategory_id" class="form-select">
            <option value="">Select Subcategory</option>
            @foreach($initialSubcategories as $subcategory)
               <option value="{{ $subcategory->id }}" {{ (string) old('subcategory_id') === (string) $subcategory->id ? 'selected' : '' }}>
                  {{ $subcategory->name }}
               </option>
            @endforeach
         </select>
         @error('subcategory_id')
         <small class="text-danger">{{ $message }}</small>
         @enderror
      </div> --}}
      <div class="col-md-4">
         <label class="form-label">Price</label>
         <input type="number"
            step="0.01"
            name="price"
            value="{{ old('price') }}"
            class="form-control"
            id="price"
            required>
      </div>
      <div class="col-md-4">
         <label class="form-label">Selling Price</label>
         <input type="number"
            step="0.01"
            name="selling_price"
            value="{{ old('selling_price') }}"
            class="form-control"
            id="selling_price"
            required>
      </div>
      @include('admin.product.partials.gst-tax-fields', ['selectedGstId' => old('gst_setting_id')])
      <div class="col-md-4">
         <label class="form-label">Quantity</label>
         <input type="number"
            name="quantity"
            value="{{ old('quantity') }}"
            class="form-control"
            required>
      </div>
      <div class="col-md-4">
         <label class="form-label">Product Type</label>
         <select name="product_type" class="form-select">
            <option value="simple">Simple Product</option>
            <option value="variable">Variable Product</option>
         </select>
      </div>
      <div id="variant-section" class="col-12 d-none">
         <div class="card border">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Product Variants</h5>
               <button type="button"
                  class="btn btn-sm btn-primary"
                  id="addVariantBtn">
               Add Variant
               </button>
            </div>
            <div class="card-body">
               <div id="variant-wrapper"></div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <label class="form-label">Status</label>
         <select name="status" class="form-select">
         <option value="1"
         {{ old('status', '1') == '1' ? 'selected' : '' }}>
         Active
         </option>
         <option value="0"
         {{ old('status') == '0' ? 'selected' : '' }}>
         Inactive
         </option>
         </select>
      </div>
       <div class="col-md-4">
         <label class="form-label">Discount %</label>
         <div class="input-group">
            <input type="number"
               name="discount"
               class="form-control"
               value="{{ old('discount',0) }}"
               id="discount"
               readonly>
            <span class="input-group-text bg-light" id="discountDynamicInfo" style="min-width:100px;"></span>
         </div>
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
               {{ old('is_trending') ? 'checked' : '' }}>
               <label class="form-check-label">
               Trending
               </label>
            </div>
            <div class="form-check">
               <input class="form-check-input"
               type="checkbox"
               name="is_featured"
               value="1"
               {{ old('is_featured') ? 'checked' : '' }}>
               <label class="form-check-label">
               Featured
               </label>
            </div>
            <div class="form-check">
               <input class="form-check-input"
               type="checkbox"
               name="is_best_selling"
               value="1"
               {{ old('is_best_selling') ? 'checked' : '' }}>
               <label class="form-check-label">
               Best Selling
               </label>
            </div>
            <div class="form-check">
               <input class="form-check-input"
               type="checkbox"
               name="is_top_rated"
               value="1"
               {{ old('is_top_rated') ? 'checked' : '' }}>
               <label class="form-check-label">
               Top Rated
               </label>
            </div>
            <div class="form-check">
               <input class="form-check-input"
               type="checkbox"
               name="is_new_arrival"
               value="1"
               {{ old('is_new_arrival') ? 'checked' : '' }}>
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
            value="{{ old('badge') }}"
            placeholder="BESTSELLER">
      </div>
            </div>
            <div class="col-md-12">
    <label class="form-label">Product Video</label>
    <input type="file"
           name="video"
           class="form-control"
           accept="video/*">
</div>
@if(!empty($product->video))
    <video width="100%" controls>
        <source src="{{ asset('storage/' . $product->video) }}" type="video/mp4">
    </video>
@endif

      <div class="col-md-12">
         <label class="form-label">Thumbnail</label>
         <input type="file"
            name="thumbnail"
            id="thumbnail"
            class="form-control"
            accept="image/*">
      </div>
      <div class="col-md-12">
         <div id="thumbnail-preview-container"
            class="mt-2 d-none">
            <img id="thumbnail-preview"
               src=""
               class="rounded border"  
               style="width:120px;height:120px;object-fit:cover;">
         </div>
      </div>
      <div class="col-md-12">
         <label class="form-label">Gallery Images</label>
         <input type="file"
            name="gallery[]"
            id="images"
            class="form-control"
            multiple
            accept="image/*">
      </div>
      <div class="col-12">
         <label class="form-label d-none"
            id="preview-label">
         Gallery Preview
         </label>
         <div id="preview-container"
            class="d-flex flex-wrap gap-3"></div>
      </div>
      {{-- <div class="col-12">
         <label class="form-label">Short Details</label>
         <textarea name="details"
            id="details"
            class="form-control"
            rows="4">{{ old('details') }}</textarea>
      </div> --}}
      <div class="col-12">
         <label class="form-label">Description</label>
         <textarea name="description"
            id="description"
            class="form-control"
            rows="6">{{ old('description') }}</textarea>
         @error('description')
         <small class="text-danger">
         {{ $message }}
         </small>
         @enderror
      </div>
      <div class="col-12">
         <label class="form-label">Specification</label>
         <textarea name="specification"
            id="specification"
            class="form-control"
            rows="6">{{ old('specification') }}</textarea>
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
                    <i class="ti ti-info-circle"></i> No FAQs added yet. Click "Add FAQ" to add frequently asked questions about this product.
                </div>
            </div>
        </div>
    </div>
</div>
      <div class="col-12 mt-2">
         <button type="submit"
            class="btn btn-primary">
         <i class="ti ti-device-floppy me-1"></i>
         Save Product
         </button>
         <a href="{{ route('product.index') }}"
            class="btn btn-light">
         Cancel
         </a>
        </div>
   </form>
   </div>
</div>
@endsection
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet"/>
@endpush
@push('scripts')
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

       // ======= DISCOUNT CALCULATION - LIVE =======
       function calcDiscount() {
           let price = parseFloat(document.getElementById('price').value) || 0;
           let sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
           let discount = 0;
           let displayText = '';

           if (price > 0 && sellingPrice > 0 && sellingPrice < price) {
               discount = Math.round(((price - sellingPrice) / price) * 100);
               document.getElementById('discount').value = discount;
               displayText = discount + '% OFF';
           } else {
               document.getElementById('discount').value = 0;
               displayText = '0%';
           }

           // Show extra info live
           document.getElementById('discountDynamicInfo').textContent = displayText;
       }

       document.getElementById('price').addEventListener('input', calcDiscount);
       document.getElementById('selling_price').addEventListener('input', calcDiscount);

       // Run once on page load in case of old() filled values
       calcDiscount();

       // =========================================
       // AUTO SLUG
       // =========================================
   
       document.getElementById('name').addEventListener('keyup', function () {
   
           let slug = this.value
               .toLowerCase()
               .trim()
               .replace(/[^a-z0-9\s-]/g, '')
               .replace(/\s+/g, '-')
               .replace(/-+/g, '-');
   
           document.getElementById('slug').value = slug;
   
       });
   
       // =========================================
       // THUMBNAIL PREVIEW
       // =========================================
   
       document.getElementById('thumbnail')
           .addEventListener('change', function(e) {
   
           let file = e.target.files[0];
   
           if(file) {
   
               let reader = new FileReader();
   
               reader.onload = function(event) {
   
                   document.getElementById('thumbnail-preview').src =
                       event.target.result;
   
                   document.getElementById('thumbnail-preview-container')
                       .classList.remove('d-none');
   
               };
   
               reader.readAsDataURL(file);
   
           }
   
       });
   
       // =========================================
       // GALLERY PREVIEW + REMOVE + SORT
       // =========================================
   
        // =========================================
    // GALLERY PREVIEW + REMOVE + SORT + PERSIST
    // =========================================
   
   let selectedFiles = [];
   
   const imagesInput = document.getElementById('images');
   
   const previewContainer = document.getElementById('preview-container');
   
   // LOAD SAVED PREVIEW AFTER VALIDATION ERROR
   // LOAD ONLY AFTER VALIDATION ERROR 
   @if ($errors->any())
   
       loadSavedGallery();
   
   @else
   
       localStorage.removeItem('product_gallery');
   
   @endif
   
   imagesInput.addEventListener('change', function(e) {
   
       let newFiles = Array.from(e.target.files);
   
       selectedFiles = [...selectedFiles, ...newFiles];
   
       saveGalleryToStorage();
   
       rebuildInputFiles();
   
       renderGallery();
   
   });
   
   function renderGallery() {
   
       previewContainer.innerHTML = '';
   
       if(selectedFiles.length > 0) {
   
           document.getElementById('preview-label')
               .classList.remove('d-none');
   
       } else {
   
           document.getElementById('preview-label')
               .classList.add('d-none');
   
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
   
           // REMOVE IMAGE
           div.querySelector('.remove-image')
               .addEventListener('click', function() {
   
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
   
   // =========================================
   // REBUILD FILE INPUT
   // =========================================
   
   function rebuildInputFiles() {
   
       let dataTransfer = new DataTransfer();
   
       selectedFiles.forEach(file => {
   
           dataTransfer.items.add(file);
   
       });
   
       imagesInput.files = dataTransfer.files;
   
   }
   
   // =========================================
   // SORTABLE
   // =========================================
   
   new Sortable(previewContainer, {
   
       animation: 150,
   
       onEnd: function () {
   
           let newOrder = [];
   
           document.querySelectorAll('.gallery-item')
               .forEach((item) => {
   
               let currentIndex = parseInt(item.dataset.index);
   
               newOrder.push(selectedFiles[currentIndex]);
   
           });
   
           selectedFiles = newOrder;
   
           saveGalleryToStorage();
   
           rebuildInputFiles();
   
           renderGallery();
   
       }
   
   });
   
   // =========================================
   // SAVE GALLERY TO STORAGE
   // =========================================
   
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
   
           localStorage.setItem(
               'product_gallery',
               JSON.stringify(images)
           );
   
       });
   
   }
   
   // =========================================
   // LOAD SAVED GALLERY
   // =========================================
   
   function loadSavedGallery() {
   
       let saved = localStorage.getItem('product_gallery');
   
       if(!saved) return;
   
       let images = JSON.parse(saved);
   
       images.forEach(image => {
   
           let arr = image.data.split(',');
           let mime = arr[0].match(/:(.*?);/)[1];
           let bstr = atob(arr[1]);
   
           let n = bstr.length;
   
           let u8arr = new Uint8Array(n);
   
           while(n--) {
   
               u8arr[n] = bstr.charCodeAt(n);
   
           }
   
           let file = new File(
               [u8arr],
               image.name,
               { type: mime }
           );
   
           selectedFiles.push(file);
   
       });
   
       rebuildInputFiles();
   
       renderGallery();
   
   }
   
   // =========================================
   // CLEAR STORAGE AFTER SUCCESS SUBMIT
   // =========================================
   
   document.querySelector('form')
       .addEventListener('submit', function() {
   
       localStorage.removeItem('product_gallery');
   
   });
   
       // =========================================
       // CKEDITOR
       // =========================================
   
       ClassicEditor
           .create(document.querySelector('#details'));
   
       ClassicEditor
           .create(document.querySelector('#description'));
   
       ClassicEditor
           .create(document.querySelector('#specification'));
   
   });
   </script>
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

    // Function to add new attribute row to a variant
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

    addVariantBtn.addEventListener('click', function () {
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
                        <!-- Add Attribute button placed BELOW the attributes -->
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

    // Handle adding new attribute rows (delegation)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-attribute-btn')) {
            const variantDiv = e.target.closest('.variant-item');
            const variantIndex = variantDiv.getAttribute('data-variant-index');
            addAttributeRow(variantDiv, variantIndex);
        }
        
        // Handle removing attribute rows
        if (e.target.classList.contains('remove-attribute-btn')) {
            const attributeRow = e.target.closest('.attribute-row');
            const attributesContainer = attributeRow.closest('.attributes-container');
            
            // Don't remove if it's the last attribute row
            if (attributesContainer.querySelectorAll('.attribute-row').length > 1) {
                attributeRow.remove();
            } else {
                alert('At least one attribute is required per variant. You can edit the existing one instead.');
            }
        }
        
        // Handle removing entire variant
        if (e.target.classList.contains('removeVariant')) {
            if (confirm('Are you sure you want to remove this variant?')) {
                e.target.closest('.variant-item').remove();
            }
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    if (!categorySelect || !subcategorySelect) {
        return;
    }

    const selectedSubcategoryId = "{{ old('subcategory_id') }}";
    const allSubcategories = @json($subcategories);

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

        const filtered = allSubcategories.filter(function (item) {
            return String(item.category_id) === String(categoryId);
        });

        renderSubcategories(filtered, selectedId);
    };

    categorySelect.addEventListener('change', function () {
        loadSubcategories(this.value);
    });

    if (categorySelect.value && subcategorySelect.options.length <= 1) {
        loadSubcategories(categorySelect.value, selectedSubcategoryId);
    }
});
</script>
<script>
// FAQ Management
document.addEventListener('DOMContentLoaded', function () {
    let faqIndex = 0;
    const faqWrapper = document.getElementById('faq-wrapper');
    const addFaqBtn = document.getElementById('addFaqBtn');
    const noFaqMessage = document.getElementById('noFaqMessage');
    
    function updateNoFaqMessage() {
        const faqItems = document.querySelectorAll('.faq-item');
        if (faqItems.length === 0) {
            if (!noFaqMessage) {
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
    
    function addFaq(faqData = null) {
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
                <!-- Order field commented out
                <div class="col-md-6">
                    <label class="form-label">Order</label>
                    <input type="number" 
                           name="faqs[${currentIndex}][order]" 
                           class="form-control" 
                           value="${faqData ? faqData.order : currentIndex + 1}"
                           placeholder="Display order">
                </div>
                -->
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
        
        // Initialize CKEditor for the answer textarea
        if (typeof ClassicEditor !== 'undefined') {
            const textarea = faqDiv.querySelector('.faq-answer');
            ClassicEditor.create(textarea).catch(error => {
                console.error(error);
            });
        }
        
        updateNoFaqMessage();
    }
    
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
@endpush