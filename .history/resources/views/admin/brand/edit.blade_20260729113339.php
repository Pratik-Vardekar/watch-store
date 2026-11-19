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

    <form action="{{ route('brands.update',$brand->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="rounded shadow p-4 bg-white">

        @csrf
        @method('PUT')

        <div class="row gy-3">
            <div class="col-md-6">
                <label for="brand-name" class="form-label">Name <span class="text-danger">*</span></label>
                <input
                    type="text"
                    id="brand-name"
                    name="name"
                    value="{{ old('name', $brand->name) }}"
                    class="form-control"
                    required
                    maxlength="255"
                >
            </div>
            <div class="col-md-6">
                <label for="brand-link" class="form-label">Website / Link</label>
                <input
                    type="url"
                    id="brand-link"
                    name="link"
                    value="{{ old('link', $brand->link) }}"
                    class="form-control"
                    placeholder="https://example.com"
                    maxlength="512"
                >
            </div>
        </div>

        <div class="row mt-3 gy-3 align-items-center">
            <div class="col-md-6">
                <label class="form-label mb-2">Current Logo</label><br>
                <img src="{{ $brand->logo_url }}" width="120" class="img-thumbnail" alt="Current Brand Logo">
            </div>
            <div class="col-md-6">
                <label for="brand-logo" class="form-label">Change Logo</label>
                <input
                    type="file"
                    id="brand-logo"
                    name="logo"
                    class="form-control"
                    accept="image/*"
                >
                <small class="text-muted">Acceptable formats: jpeg, png, svg. Max size: 1MB.</small>
            </div>
        </div>

        <div class="row mt-3 gy-3">
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
