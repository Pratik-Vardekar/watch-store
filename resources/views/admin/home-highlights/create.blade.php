@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Add Homepage Card</h4>
        <p class="text-muted mb-0">Create a new card for the “More Than Just Watches” section</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.home-highlights.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Card Title</label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}" placeholder="Corporate Gifting" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Card Image</label>
                    <input type="file" name="image" id="highlight-image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,.jpg,.jpeg,.png,.webp,.gif,.svg">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Optional. Upload PNG, JPG, SVG or WebP image. Leave empty to use the default image.</small>
                    <div class="mt-2 d-none" id="highlight-preview-wrap">
                        <img src="" id="highlight-preview" alt="Preview" class="rounded border" style="max-height:160px;object-fit:cover;">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', 0) }}">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Card</button>
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
        const file = this.files && this.files[0];
        const preview = document.getElementById('highlight-preview');
        const wrap = document.getElementById('highlight-preview-wrap');
        if (!file || !preview || !wrap) {
            return;
        }
        wrap.classList.remove('d-none');
        preview.src = URL.createObjectURL(file);
    });
</script>
@endpush
