@extends('layouts.admin')

@section('content')
<div class="container my-4">

    <h4>Change Card Image</h4>
    <p class="text-muted mb-3">Update the image for "<strong>{{ $homeHighlight->title }}</strong>"</p>

    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size:0.96em;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.home-highlights.update-card', $homeHighlight) }}"
        method="POST"
        enctype="multipart/form-data"
        style="max-width: 500px; margin: 0 auto;"
    >
        @csrf

        <div class="mb-3">
            <label class="form-label">Card Title</label>
            <input type="text" class="form-control" value="{{ $homeHighlight->title }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Card Image</label>
            <input
                type="file"
                name="image"
                id="highlight-image"
                class="form-control @error('image') is-invalid @enderror"
                accept=".jpg,.jpeg,.png,.webp,.gif"
                required
            >
            <small class="text-muted d-block mb-2">JPG, PNG, WEBP or GIF. Max 5MB.</small>
            @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            <div class="my-2">
                <img
                    src="{{ $homeHighlight->image_url }}"
                    id="highlight-preview"
                    alt="{{ $homeHighlight->title }}"
                    style="width: 240px; height: 150px; object-fit: cover; border: 1px solid #ddd; border-radius: 3px;"
                >
            </div>

            @if($homeHighlight->image_changed_at)
                <div class="small text-muted mt-2">
                    Last changed: {{ $homeHighlight->image_changed_at->format('d M Y, h:i A') }}
                </div>
            @endif
        </div>

        <div>
            <button type="submit" class="btn btn-primary me-2">Update Image</button>
            <a href="{{ route('admin.home-highlights.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('highlight-image')?.addEventListener('change', function () {
    const file = this.files[0];
    const preview = document.getElementById('highlight-preview');

    if (!file || !preview) return;

    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image.');
        this.value = '';
        return;
    }

    preview.src = URL.createObjectURL(file);
});
</script>
@endpush
