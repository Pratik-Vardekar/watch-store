@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Homepage Card</h4>
        <p class="text-muted mb-0">Update title or image for this homepage card</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form
                action="{{ route('admin.home-highlights.update-card', $homeHighlight) }}"
                method="POST"
                enctype="multipart/form-data"
                class="row g-3"
            >
                @csrf

                <div class="col-12">
                    <label class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $homeHighlight->title) }}"
                        maxlength="255"
                        required
                    >
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Image</label>
                    <input
                        type="file"
                        name="image"
                        id="highlight-image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp,.gif"
                    >
                    <small class="text-muted d-block mt-1">
                        JPG, PNG, WEBP or GIF. Max 5MB. Leave empty to keep the current image.
                    </small>
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="mt-2">
                        @if($homeHighlight->image_url)
                            <img
                                src="{{ $homeHighlight->image_url }}"
                                id="highlight-preview"
                                alt="{{ $homeHighlight->title }}"
                                class="rounded border"
                                style="max-height:160px; object-fit:cover;"
                            >
                        @else
                            <img
                                src=""
                                id="highlight-preview"
                                alt="{{ $homeHighlight->title }}"
                                class="rounded border d-none"
                                style="max-height:160px; object-fit:cover;"
                            >
                            <div id="highlight-preview-empty" class="text-muted small">No image uploaded</div>
                        @endif

                        @if($homeHighlight->image_changed_at)
                            <div class="small text-muted mt-1">
                                Last changed: {{ $homeHighlight->image_changed_at->format('d M Y, h:i A') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input
                        type="number"
                        name="sort_order"
                        min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $homeHighlight->sort_order) }}"
                    >
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ (string) old('status', $homeHighlight->status ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('status', $homeHighlight->status ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Card</button>
                    <a href="{{ route('admin.home-highlights.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('highlight-image')?.addEventListener('change', function () {
    const file = this.files[0];
    const preview = document.getElementById('highlight-preview');
    const empty = document.getElementById('highlight-preview-empty');

    if (!file || !preview) return;

    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image.');
        this.value = '';
        return;
    }

    preview.src = URL.createObjectURL(file);
    preview.classList.remove('d-none');
    if (empty) {
        empty.classList.add('d-none');
    }
});
</script>
@endpush
