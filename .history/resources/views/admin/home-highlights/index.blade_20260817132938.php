@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Homepage Cards</h4>
            <p class="text-muted mb-0 mt-1">
                Change images for the "More Than Just Watches" section on the homepage
            </p>
        </div>

        <div class="card-body">
            <form
                action="{{ route('admin.home-highlights.update') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                @forelse($highlights as $highlight)
                    <div class="card shadow-sm border mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $highlight->title }}</h5>
                            <span class="small text-muted">
                                Last updated:
                                {{ optional($highlight->image_changed_at)->format('d M Y, h:i A') ?? '-' }}
                            </span>
                        </div>

                        <div class="card-body">
                            <input type="hidden" name="highlight_ids[]" value="{{ $highlight->id }}">

                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Current Image</label>
                                    <div class="border rounded bg-light p-3 text-center">
                                        @if($highlight->image_url)
                                            <img
                                                id="preview-{{ $highlight->id }}"
                                                src="{{ $highlight->image_url }}"
                                                alt="{{ $highlight->title }}"
                                                class="img-fluid rounded shadow-sm"
                                                style="max-height:220px; width:100%; object-fit:cover;"
                                            >
                                        @else
                                            <p class="text-muted mb-0">No image uploaded</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-2">Upload New Image</label>
                                    <input
                                        type="file"
                                        class="form-control highlight-input"
                                        name="image[{{ $highlight->id }}]"
                                        data-preview="preview-{{ $highlight->id }}"
                                        accept="image/*"
                                    >
                                    <small class="text-muted mt-2 d-block">
                                        JPG, PNG, WEBP or GIF. Maximum 5MB. Leave empty to keep the current image.
                                    </small>
                                    <a href="{{ route('admin.home-highlights.edit', $highlight) }}" class="btn btn-sm btn-outline-primary mt-3">
                                        <i class="ti ti-photo me-1"></i> Change This Image
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No homepage cards found.</p>
                @endforelse

                @if($highlights->isNotEmpty())
                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="ti ti-device-floppy me-1"></i>
                            Save Image Changes
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

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