@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Add Category</h4>
        <p class="text-muted mb-0">Create new category</p>
    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('category.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="row g-3 needs-validation"
                novalidate>

                @csrf

                <div class="col-md-6">

                    <label class="form-label">Category Name</label>

                    <input type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        required>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Slug</label>

                    <input type="text"
                        name="slug"
                        id="slug"
                        class="form-control"
                        value="{{ old('slug') }}">

                </div>

                <div class="col-md-6">

                    <label class="form-label">Image</label>

                    <input type="file"
                        name="image"
                        id="image"
                        class="form-control"
                        accept="image/*">

                </div>

                <div class="col-md-6">

    <div id="preview-wrapper" class="d-none">

        <label class="form-label mb-2" id="preview-label">
            Preview Image
        </label>

        <img id="preview-image"
            class="rounded border"
            style="width:120px;height:120px;object-fit:cover;">

    </div>

</div>

                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-3">

                    <label class="form-label">Status</label>

                    <select name="status"
                        class="form-select"
                        required>

                        <option value="1">Active</option>
                        <option value="0">Inactive</option>

                    </select>

                </div>

                <div class="col-md-3">
                    <label class="form-label">Show in Header Menu</label>
                    <select name="gender" class="form-select">
                        <option value="">None</option>
                        @foreach(\App\Models\Category::GENDERS as $value => $label)
                            <option value="{{ $value }}" {{ old('gender') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Women / Men mega menu on the storefront.</small>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Display Order</label>
                    <input type="number"
                        name="sort_order"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        min="0"
                        value="{{ old('sort_order') }}"
                        placeholder="Auto">
                    <small class="text-muted">Lower number shows first. Leave blank to auto-assign.</small>
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">

                    <button class="btn btn-primary" type="submit">

                        <i class="ti ti-device-floppy me-1"></i>
                        Save Category

                    </button>

                    <a href="{{ route('category.index') }}"
                        class="btn btn-light">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('name').addEventListener('keyup', function () {

        let slug = this.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        document.getElementById('slug').value = slug;

    });

    document.getElementById('image').addEventListener('change', function(e) {

        let file = e.target.files[0];

        if(file) {

            let reader = new FileReader();

            reader.onload = function(event) {

                document.getElementById('preview-image').src = event.target.result;

                document.getElementById('preview-wrapper')
                    .classList.remove('d-none');

            }

            reader.readAsDataURL(file);

        }

    });

});

</script>

@endsection