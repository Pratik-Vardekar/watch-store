@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h4 class="mb-1">How To Play Section</h4>
        <p class="text-muted mb-0">Edit the homepage video section. Only one YouTube link is shown on the frontend.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.how-to-play-section.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Video Content</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Title</label>
                        <textarea name="title" rows="2" class="form-control @error('title') is-invalid @enderror"
                            placeholder="How To Play The Word Hunt Challenge!!!">{{ old('title', $section->title) }}</textarea>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Use a new line if you want the title to wrap like the design.</small>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">YouTube URL</label>
                        <input type="url" name="youtube_url" class="form-control @error('youtube_url') is-invalid @enderror"
                            value="{{ old('youtube_url', $section->youtube_url) }}"
                            placeholder="https://www.youtube.com/watch?v=...">
                        @error('youtube_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Paste a full YouTube watch or share link. Only this one video is shown on the homepage.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Video Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($section->thumbnail)
                            <div class="mt-2">
                                <img src="{{ asset($section->thumbnail) }}" alt="Thumbnail" style="max-height:120px;" class="rounded border">
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Side Image (Robot PNG)</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($section->image)
                            <div class="mt-2">
                                <img src="{{ asset($section->image) }}" alt="Side image" style="max-height:120px;" class="rounded border">
                            </div>
                        @else
                            <small class="text-muted d-block mt-1">Default: frontend robot image is used if empty.</small>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Button Text</label>
                        <input type="text" name="button_text" class="form-control"
                            value="{{ old('button_text', $section->button_text) }}"
                            placeholder="View All">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Button URL</label>
                        <input type="text" name="button_url" class="form-control"
                            value="{{ old('button_url', $section->button_url) }}"
                            placeholder="https://youtube.com/... or #">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update How To Play Section</button>
    </form>

</div>
@endsection
