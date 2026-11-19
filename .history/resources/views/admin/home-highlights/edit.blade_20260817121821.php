@extends('layouts.admin')

@section('content')

<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Homepage Card</h4>
        <p class="text-muted mb-0">Update the "More Than Just Watches" card</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
        <div class="card-body">

            {{-- ✅ Use $home_highlight (snake_case) --}}
            <form
                action="{{ route('admin.home-highlights.update', $home_highlight) }}"
                method="POST"
                enctype="multipart/form-data"
                class="row g-3"
            >

                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="col-12">
                    <label class="form-label">Card Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $home_highlight->title) }}"
                        required
                    >
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Image --}}
                <div class="col-12">
                    <label class="form-label">Card Image</label>
                    <input
                        type="file"
                        name="image"
                        id="highlight-image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp,.gif,.svg"
                    >
                    <small class="text-muted">
                        Leave empty to keep the current image.
                        JPG, PNG, WEBP, GIF or SVG. Maximum 5MB.
                    </small>
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="mt-3">
                        <img
                            src="{{ $home_highlight->image_url }}"
                            id="highlight-preview"
                            alt="{{ $home_highlight->title }}"
                            class="img-thumbnail"
                            style="width:250px; height:160px; object-fit:cover;"
                        >
                    </div>

                    {{-- ✅ Show image updated timestamp --}}
                    @if($home_highlight->image_changed_at)
                        <div class="small text-muted mt-2">
                            <strong>Last changed:</strong>
                            {{ $home_highlight->image_changed_at->format('d M Y, h:i A') }}
                        </div>
                    @endif
                </div>

                {{-- Sort Order --}}
                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input
                        type="number"
                        name="sort_order"
                        min="0"
                        class="form-control"
                        value="{{ old('sort_order', $home_highlight->sort_order) }}"
                    >
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ old('status', $home_highlight->status) ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ old('status', $home_highlight->status) ? '' : 'selected' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        Update Card
                    </button>
                    <a href="{{ route('admin.home-highlights.index') }}" class="btn btn-secondary">
                        Back
                    </a>
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

    if (!file || !preview) return;

    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image.');
        this.value = '';
        return;
    }

    const objectUrl = URL.createObjectURL(file);
    preview.src = objectUrl;
    preview.onload = function () {
        URL.revokeObjectURL(objectUrl);
    };
});
</script>
@endpush