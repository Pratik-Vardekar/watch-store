@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Category</h4>
        <p class="text-muted mb-0">Update category details</p>
    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('category.update', $category->id) }}"
                method="POST"
                enctype="multipart/form-data"
                class="row g-3 needs-validation"
                novalidate>

                @csrf
                @method('PUT')

                <div class="col-md-6">

                    <label class="form-label">Category Name</label>

                    <input type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name', $category->name) }}"
                        required>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Slug</label>

                    <input type="text"
                        name="slug"
                        id="slug"
                        class="form-control"
                        value="{{ old('slug', $category->slug) }}">

                </div>

                <div class="col-md-6">

                    <label class="form-label">Image</label>

                    <input type="file"
                        name="image"
                        id="image"
                        class="form-control">

                </div>

                <div class="col-md-6">

                    <label class="form-label d-block">
                        Preview Image
                    </label>

                    <img id="preview-image"
                        src="{{ $category->image ? asset('backend/assets/images/category/' . $category->image) : asset('backend/assets/images/no-image.png') }}"
                        class="rounded border"
                        style="width:120px;height:120px;object-fit:cover;">

                </div>

                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
                </div>
                
                <div class="col-md-3">

                    <label class="form-label">Status</label>

                    <select name="status"
                        class="form-select"
                        required>

                        <option value="1"
                            {{ old('status', $category->status) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0"
                            {{ old('status', $category->status) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>

                </div>

                <div class="col-md-3">
                    <label class="form-label">Show in Header Menu</label>
                    <select name="gender" class="form-select">
                        <option value="">None</option>
                        @foreach(\App\Models\Category::GENDERS as $value => $label)
                            <option value="{{ $value }}" {{ old('gender', $category->gender) === $value ? 'selected' : '' }}>
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
                        value="{{ old('sort_order', $category->sort_order) }}">
                    <small class="text-muted">Lower number shows first.</small>
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">

                    <button class="btn btn-primary" type="submit">

                        <i class="ti ti-device-floppy me-1"></i>
                        Update Category

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

        let reader = new FileReader();

        reader.onload = function(event) {

            document.getElementById('preview-image').src = event.target.result;

        }

        reader.readAsDataURL(e.target.files[0]);

    });

</script>

@endsection