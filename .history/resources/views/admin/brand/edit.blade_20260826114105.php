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
});
</script>
@endpush