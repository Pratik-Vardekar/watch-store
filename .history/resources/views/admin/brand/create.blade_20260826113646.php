@extends('layouts.admin')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Add Brand</h4>
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

    <form action="{{ route('brands.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="rounded shadow p-4 bg-white">

        @csrf

        <div class="row gy-3">
            <div class="col-md-6">
                <label for="brand-name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text"
                       id="brand-name"
                       name="name"
                       value="{{ old('name') }}"
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
                          placeholder="Enter brand description (max 1000 characters)">{{ old('description') }}</textarea>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">Optional</small>
                    <small class="text-muted">
                        <span id="charCount">0</span> / 1000 characters
                    </small>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label for="brand-logo" class="form-label">Logo <span class="text-danger">*</span></label>
                <input type="file"
                       id="brand-logo"
                       name="logo"
                       class="form-control"
                       accept="image/*"
                       required>
                <small class="text-muted">Acceptable formats: jpeg, png, svg, webp. Max size: 2MB.</small>
            </div>

            <div class="col-md-6">
                <label for="brand-status" class="form-label">Status</label>
                <select name="status" id="brand-status" class="form-control">
                    <option value="1" selected>Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save Brand
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
        // Initial count
        charCount.textContent = textarea.value.length;
        
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