@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="mb-4">
        <h4 class="mb-1">About Page Content</h4>
        <p class="text-muted mb-0">Manage the About page title, content, and image</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('about-us.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">About Page Settings</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Title --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Page Title</label>
                        <input type="text" 
                               name="title" 
                               class="form-control" 
                               value="{{ old('title', $about->title ?? 'About Us') }}"
                               placeholder="Enter page title">
                        <small class="text-muted">This title appears in the hero section</small>
                    </div>

                    {{-- Image Upload --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Hero Image</label>
                        <input type="file" 
                               name="image" 
                               id="imageInput"
                               class="form-control" 
                               accept="image/*">
                        <small class="text-muted">Recommended size: 1920x600px. Max: 2MB (jpg, jpeg, png, webp)</small>
                        
                        {{-- Current Image Preview --}}
                        <div id="imagePreview" class="mt-3">
                            @if($about->image)
                                <div class="position-relative d-inline-block">
                                    <img src="{{ asset('backend/assets/images/about/' . $about->image) }}" 
                                         alt="About Page Image"
                                         class="rounded border"
                                         style="width:300px; height:auto; max-height:200px; object-fit:cover;">
                                    <span class="badge bg-info position-absolute top-0 start-0 m-2">Current</span>
                                </div>
                            @else
                                <p class="text-muted">No image uploaded. Default image will be used.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" 
                                  id="content" 
                                  class="form-control" 
                                  rows="10"
                                  placeholder="Enter your about page content here...">{{ old('content', $about->content ?? '') }}</textarea>
                        <small class="text-muted">This content will appear below the hero section</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i> Save About Page
            </button>
            <a href="{{ route('about-us.index') }}" class="btn btn-light">Cancel</a>
        </div>

    </form>
</div>

@push('scripts')
<script>
    // Image Preview
    document.getElementById('imageInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.innerHTML = `
                    <div class="position-relative d-inline-block">
                        <img src="${event.target.result}" 
                             alt="Preview"
                             class="rounded border"
                             style="width:300px; height:auto; max-height:200px; object-fit:cover;">
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">New</span>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    // CKEditor for content
    ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold', 'italic', 'underline',
                    '|',
                    'bulletedList', 'numberedList',
                    '|',
                    'link',
                    '|',
                    'undo', 'redo'
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endpush
@endsection