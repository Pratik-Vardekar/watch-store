@extends('layouts.admin')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Edit Brand</h4>
        <a href="{{ route('brands.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Brands
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('brands.update', $brand->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="rounded shadow p-4 bg-white">

        @csrf
        @method('PUT')

        <div class="row gy-3">
            <div class="col-md-6">
                <label for="brand-name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text"
                       id="brand-name"
                       name="name"
                       value="{{ old('name', $brand->name) }}"
                       class="form-control"
                       required
                       maxlength="255">
            </div>

            <div class="col-md-6">
                <label for="brand-slug" class="form-label">Slug</label>
                <input type="text"
                       id="brand-slug"
                       name="slug"
                       value="{{ old('slug', $brand->slug) }}"
                       class="form-control"
                       maxlength="255"
                       placeholder="auto-generated from name">
                <small class="text-muted">Unique URL identifier</small>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <label for="brand-description" class="form-label">Description</label>
                <textarea id="brand-description"
                          name="description"
                          class="form-control"
                          rows="4"
                          maxlength="1000"
                          placeholder="Enter brand description (max 1000 characters)">{{ old('description', $brand->description) }}</textarea>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">Optional</small>
                    <small class="text-muted">
                        <span id="charCount">{{ strlen($brand->description ?? '') }}</span> / 1000 characters
                    </small>
                </div>
            </div>
        </div>

        <div class="row mt-3 align-items-center">
            <div class="col-md-6">
                <label class="form-label mb-2">Current Logo</label><br>
                <img src="{{ $brand->logo_url }}" 
                     width="120" 
                     class="img-thumbnail" 
                     alt="Current Brand Logo">
            </div>

            <div class="col-md-6">
                <label for="brand-logo" class="form-label">Change Logo</label>
                <input type="file"
                       id="brand-logo"
                       name="logo"
                       class="form-control"
                       accept="image/*">
                <small class="text-muted">Acceptable formats: jpeg, png, svg, webp. Max size: 2MB.</small>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label for="brand-status" class="form-label">Status</label>
                <select name="status" id="brand-status" class="form-control">
                    <option value="1" {{ old('status', $brand->status) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !old('status', $brand->status) ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="card border bg-light mt-4 p-3 rounded">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 text-dark fw-bold">
                    <i class="ti ti-photo me-1 text-primary"></i>
                    Brand Banner
                </h5>
                <span class="badge bg-primary text-white">Recommended Size: 2513 × 400 px</span>
            </div>

            <div class="row align-items-center g-3">
                <div class="col-lg-6">
                    <label class="form-label small fw-bold text-muted mb-1">Current Banner</label>
                    @if(!empty($brand->banner_url))
                        <div class="border rounded bg-dark p-2 text-center" style="background-color: #1a1a1a !important;">
                            <img id="brand-banner-preview"
                                 src="{{ $brand->banner_url }}" 
                                 class="img-fluid rounded" 
                                 alt="{{ $brand->name }} Banner"
                                 onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/prodListBanner.jpg') }}';"
                                 style="width: 100%; max-height: 160px; aspect-ratio: 2513 / 400; object-fit: cover;">
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_banner" id="remove_banner" value="1">
                            <label class="form-check-label text-danger small" for="remove_banner">
                                <i class="ti ti-trash me-1"></i> Remove custom banner and use default system banner
                            </label>
                        </div>
                    @else
                        <div class="border rounded bg-white p-3 text-center text-muted">
                            <i class="ti ti-photo-off fs-2 d-block mb-1 text-secondary"></i>
                            <span class="small">No custom banner uploaded. Using default system banner.</span>
                        </div>
                        <div id="banner-preview-wrapper" class="mt-2 d-none">
                            <label class="form-label small text-muted">New Banner Preview</label>
                            <div class="border rounded bg-dark p-2 text-center" style="background-color: #1a1a1a !important;">
                                <img id="brand-banner-preview"
                                     src="#"
                                     class="img-fluid rounded"
                                     alt="Brand Banner Preview"
                                     style="width: 100%; max-height: 160px; aspect-ratio: 2513 / 400; object-fit: cover;">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-6">
                    <label for="brand-banner" class="form-label fw-bold">Upload / Replace Banner</label>
                    <input type="file"
                           id="brand-banner"
                           name="banner"
                           class="form-control"
                           accept="image/jpeg,image/png,image/webp,image/jpg">
                    <small class="text-muted d-block mt-2">
                        Intrinsic Size: <strong>2513 × 400 px</strong>. Acceptable formats: jpeg, png, webp. Max size: 5MB.<br>
                        Displays across the top of the product listing when customers view /brand/{{ $brand->slug }}.
                    </small>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> Update Brand
            </button>
            <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary">
                Cancel
            </a>
        </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('brand-description');
    const charCount = document.getElementById('charCount');
    const nameInput = document.getElementById('brand-name');
    const slugInput = document.getElementById('brand-slug');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.value.trim()) {
                slugInput.value = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }
        });
    }
    
    if (textarea && charCount) {
        // Update on input
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Change color when near limit
            if (length > 900) {
                charCount.style.color = '#dc3545';
                charCount.style.fontWeight = 'bold';
            } else if (length > 800) {
                charCount.style.color = '#ffc107';
                charCount.style.fontWeight = 'bold';
            } else {
                charCount.style.color = '';
                charCount.style.fontWeight = '';
            }
        });
    }
    const bannerInput = document.getElementById('brand-banner');
    const bannerPreviewWrapper = document.getElementById('banner-preview-wrapper');
    const bannerPreview = document.getElementById('brand-banner-preview');

    if (bannerInput && bannerPreview) {
        bannerInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                bannerPreview.src = URL.createObjectURL(file);
                if (bannerPreviewWrapper) {
                    bannerPreviewWrapper.classList.remove('d-none');
                }
            }
        });
    }
});
</script>
@endpush