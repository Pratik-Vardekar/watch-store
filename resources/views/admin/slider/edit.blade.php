@extends('layouts.admin')

@section('content')
    <div class="container-fluid my-2">

        <div class="mb-3">
            <h4 class="mb-1">Edit Slider</h4>
            <p class="text-muted mb-0">Update slider details</p>
        </div>

        <div class="card border mb-3">
            <div class="card-body py-2 px-3 bg-light rounded">
                <div class="small text-dark">
                    <span class="fw-semibold">Desktop Image:</span> 1920 × 825 px <span class="text-primary">(aspect ratio 128:55)</span>
                </div>
                <div class="small text-dark mt-1">
                    <span class="fw-semibold">Mobile Image:</span> 750 × 800 px or 600 × 600 px <span class="text-muted">(optional, falls back to mobile banner default)</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('slider.update', $slider->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $slider->title) }}" placeholder="Slider Title">
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ old('status', $slider->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $slider->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $slider->sort_order ?? 0) }}" placeholder="1">
                    </div>

                    <!-- Link -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Link</label>
                        <input type="url" name="link" class="form-control" value="{{ old('link', $slider->link) }}" placeholder="https://example.com">
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter slider description...">{{ old('description', $slider->description) }}</textarea>
                    </div>

                    <!-- Desktop Image -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold mb-0" for="desktop_image">Desktop Image</label>
                            <span class="text-muted small">1920 × 825 px</span>
                        </div>
                        <input type="file" name="image" id="desktop_image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">Leave blank to keep current desktop image.</small>

                        <div class="mt-3">
                            <label class="form-label small fw-semibold text-muted mb-1 d-block">Current Desktop Image</label>
                            <div class="d-inline-block p-1 border rounded bg-light">
                                <img id="preview-desktop-image"
                                    src="{{ $slider->image_url }}"
                                    class="rounded border shadow-sm"
                                    style="width: 190px; height: 95px; object-fit: cover;"
                                    alt="Desktop Slider Image"
                                    onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/heroBanner_1.jpg') }}';">
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Image -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold mb-0" for="mobile_image">
                                Mobile Image
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 0.72rem;">Optional</span>
                            </label>
                            <span class="text-muted small">750 × 800 px or 600 × 600 px</span>
                        </div>
                        <input type="file" name="mobile_image" id="mobile_image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">Leave blank to keep current. Desktop image will never be shown on mobile screens.</small>

                        <div class="mt-3">
                            <label class="form-label small fw-semibold text-muted mb-1 d-block">Current Mobile Image</label>
                            <div class="d-inline-block p-1 border rounded bg-light">
                                @if ($slider->mobile_image)
                                    <img id="preview-mobile-image"
                                        src="{{ $slider->mobile_image_url }}"
                                        class="rounded border shadow-sm"
                                        style="width: 95px; height: 95px; object-fit: cover;"
                                        alt="Mobile Slider Image"
                                        onerror="this.onerror=null;this.src='{{ $slider->effective_mobile_image_url }}';">
                                @else
                                    <img id="preview-mobile-image"
                                        src="{{ $slider->effective_mobile_image_url }}"
                                        class="rounded border shadow-sm opacity-75"
                                        style="width: 95px; height: 95px; object-fit: cover;"
                                        alt="Mobile Fallback Image"
                                        title="Currently using default/fallback mobile banner">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4 pt-2 border-top d-flex align-items-center">
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-device-floppy me-1"></i>
                            Update Slider
                        </button>
                        <a href="{{ route('slider.index') }}" class="btn btn-light ms-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Desktop Image Preview
        document.getElementById('desktop_image').addEventListener('change', function(e) {
            let file = e.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    let preview = document.getElementById('preview-desktop-image');
                    preview.src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Mobile Image Preview
        document.getElementById('mobile_image').addEventListener('change', function(e) {
            let file = e.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    let preview = document.getElementById('preview-mobile-image');
                    preview.src = event.target.result;
                    preview.classList.remove('opacity-75');
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
