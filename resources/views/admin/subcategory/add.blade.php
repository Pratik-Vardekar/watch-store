@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">

        <h4 class="mb-1">Add Sub Category</h4>
        <p class="text-muted mb-0">Create new sub category</p>

    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('subcategory.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="row g-3">

                @csrf

                <div class="col-md-6">

                    <label class="form-label">Category</label>

                    <select name="category_id"
                        class="form-select"
                        required>

                        <option value="">Select Category</option>

                        @foreach($categories as $category)

                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Sub Category Name</label>

                    <input type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        required>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Slug</label>

                    <input type="text"
                        name="slug"
                        id="slug"
                        class="form-control">

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

                        <label class="form-label mb-2">
                            Preview Image
                        </label>

                        <img id="preview-image"
                            class="rounded border"
                            style="width:120px;height:120px;object-fit:cover;">

                    </div>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Status</label>

                    <select name="status"
                        class="form-select">

                        <option value="1">Active</option>
                        <option value="0">Inactive</option>

                    </select>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Show in Explore Menu</label>

                    <select name="show_in_explore"
                        class="form-select @error('show_in_explore') is-invalid @enderror">

                        <option value="0" {{ old('show_in_explore', '0') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('show_in_explore') == '1' ? 'selected' : '' }}>Yes</option>

                    </select>
                    <small class="text-muted">Shows as a tile in the storefront Explore menu. Maximum 4 subcategories.</small>
                    @error('show_in_explore')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                </div>

                <div class="col-12">

                    <button class="btn btn-primary">

                        <i class="ti ti-device-floppy me-1"></i>
                        Save Sub Category

                    </button>

                    <a href="{{ route('subcategory.index') }}"
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

                document.getElementById('preview-image').src =
                    event.target.result;

                document.getElementById('preview-wrapper')
                    .classList.remove('d-none');

            }

            reader.readAsDataURL(file);

        }

    });

});

</script>

@endsection