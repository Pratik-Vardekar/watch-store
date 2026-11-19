@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="mb-4">
        <h4 class="mb-1">Add Service</h4>
        <p class="text-muted mb-0">Create a new service</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('services.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="serviceForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Enter service name"
                               required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text"
                               name="slug"
                               id="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}"
                               placeholder="Auto-generated from name if empty">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  id="description"
                                  rows="12"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Images (multiple)</label>
                        <input type="file"
                               name="images[]"
                               id="images"
                               class="form-control @error('images.*') is-invalid @enderror"
                               accept="image/*"
                               multiple>
                        <small class="text-muted">You can select multiple images (jpg, jpeg, png, webp).</small>
                        @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number"
                               name="sort_order"
                               class="form-control"
                               value="{{ old('sort_order', 0) }}"
                               min="0">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Service</button>
                    <a href="{{ route('services.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
(function () {
    let serviceEditor = null;

    ClassicEditor
        .create(document.querySelector('#description'))
        .then(editor => { serviceEditor = editor; })
        .catch(error => console.error(error));

    document.getElementById('serviceForm').addEventListener('submit', function () {
        if (serviceEditor) {
            document.querySelector('#description').value = serviceEditor.getData();
        }
    });

    document.getElementById('name').addEventListener('input', function () {
        const slugInput = document.getElementById('slug');
        if (!slugInput.dataset.manual) {
            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });

    document.getElementById('slug').addEventListener('input', function () {
        this.dataset.manual = this.value ? '1' : '';
    });

    document.getElementById('images').addEventListener('change', function (e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        Array.from(e.target.files || []).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (event) {
                const img = document.createElement('img');
                img.src = event.target.result;
                img.className = 'rounded border';
                img.style.cssText = 'width:100px;height:100px;object-fit:cover;';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
})();
</script>
@endsection
