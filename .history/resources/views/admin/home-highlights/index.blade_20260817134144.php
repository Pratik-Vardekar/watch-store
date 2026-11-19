@extends('layouts.admin')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h4>Homepage Cards</h4>
    <p class="text-muted mb-4">
        Change images for the "More Than Just Watches" section on the homepage
    </p>

    <form
        action="{{ route('admin.home-highlights.update') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        @forelse($highlights as $highlight)
            <div class="border rounded mb-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>{{ $highlight->title }}</strong>
                    <span class="text-muted small">
                        Last updated: {{ optional($highlight->image_changed_at)->format('d M Y, h:i A') ?? '-' }}
                    </span>
                </div>
                <input type="hidden" name="highlight_ids[]" value="{{ $highlight->id }}">

                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label">Current Image</label>
                        <div class="bg-light rounded p-2 text-center border">
                            @if($highlight->image_url)
                                <img
                                    id="preview-{{ $highlight->id }}"
                                    src="{{ $highlight->image_url }}"
                                    alt="{{ $highlight->title }}"
                                    style="max-height:180px; width:100%; object-fit:cover; border-radius:3px;"
                                >
                            @else
                                <div class="text-muted small">No image uploaded</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <label class="form-label">Upload New Image</label>
                        <input
                            type="file"
                            class="form-control highlight-input"
                            name="image[{{ $highlight->id }}]"
                            data-preview="preview-{{ $highlight->id }}"
                            accept="image/*"
                        >
                        <small class="text-muted mt-2">
                            JPG, PNG, WEBP or GIF. Max 5MB.<br>
                            Leave empty to keep the current image.
                        </small>
                        <a href="{{ route('admin.home-highlights.edit', $highlight) }}" class="btn btn-outline-primary btn-sm mt-3 align-self-start">
                            Change This Image
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">No homepage cards found.</p>
        @endforelse

        @if($highlights->isNotEmpty())
            <div class="text-end">
                <button type="submit" class="btn btn-success">
                    Save Image Changes
                </button>
            </div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.highlight-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const file = this.files[0];
            const preview = document.getElementById(this.dataset.preview);

            if (!file || !preview) {
                return;
            }

            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image.');
                this.value = '';
                return;
            }

            preview.src = URL.createObjectURL(file);
        });
    });
});
</script>
@endpush