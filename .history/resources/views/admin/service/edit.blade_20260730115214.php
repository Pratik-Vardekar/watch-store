@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="mb-4">
        <h4 class="mb-1">Edit Service</h4>
        <p class="text-muted mb-0">Update service details</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('services.update', $service) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="serviceForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $service->name) }}"
                               required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text"
                               name="slug"
                               id="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $service->slug) }}"
                               data-manual="1">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  id="description"
                                  rows="12"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $service->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Existing Images</label>
                        <div class="d-flex flex-wrap gap-3">
                            @forelse($service->images as $image)
                                <div class="position-relative border rounded p-1 text-center">
                                    <img src="{{ $image->image_url }}"
                                         alt="Service image"
                                         style="width:110px;height:110px;object-fit:cover;"
                                         class="rounded d-block">
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger w-100 mt-1"
                                            form="delete-image-{{ $image->id }}"
                                            onclick="return confirm('Delete this image?')">
                                        Delete
                                    </button>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No images uploaded yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Add More Images (multiple)</label>
                        <input type="file"
                               name="images[]"
                               id="images"
                               class="form-control @error('images.*') is-invalid @enderror"
                               accept="image/*"
                               multiple>
                        @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number"
                               name="sort_order"
                               class="form-control"
                               value="{{ old('sort_order', $service->sort_order) }}"
                               min="0">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ (string) old('status', $service->status ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (string) old('status', $service->status ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Service</button>
                    <a href="{{ route('services.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>

            @foreach($service->images as $image)
                <form id="delete-image-{{ $image->id }}"
                      action="{{ route('services.images.destroy', [$service, $image]) }}"
                      method="POST"
                      class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
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
