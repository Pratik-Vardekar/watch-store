@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Special Offer</h4>
        <p class="text-muted mb-0">Update the homepage special offer banner and features</p>
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
                action="{{ route('admin.special-offers.update', $specialOffer) }}"
                method="POST"
                enctype="multipart/form-data"
                class="row g-3"
            >
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Badge Text</label>
                    <input
                        type="text"
                        name="badge_text"
                        class="form-control @error('badge_text') is-invalid @enderror"
                        value="{{ old('badge_text', $specialOffer->badge_text) }}"
                    >
                    @error('badge_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $specialOffer->title) }}"
                        required
                    >
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Subtitle</label>
                    <input
                        type="text"
                        name="subtitle"
                        class="form-control @error('subtitle') is-invalid @enderror"
                        value="{{ old('subtitle', $specialOffer->subtitle) }}"
                    >
                    @error('subtitle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea
                        name="description"
                        rows="4"
                        class="form-control @error('description') is-invalid @enderror"
                    >{{ old('description', $specialOffer->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Button Text</label>
                    <input
                        type="text"
                        name="button_text"
                        class="form-control @error('button_text') is-invalid @enderror"
                        value="{{ old('button_text', $specialOffer->button_text) }}"
                        required
                    >
                    @error('button_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Button Link</label>
                    <input
                        type="text"
                        name="button_link"
                        class="form-control @error('button_link') is-invalid @enderror"
                        value="{{ old('button_link', $specialOffer->button_link) }}"
                        placeholder="/shop"
                    >
                    @error('button_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Background Image</label>
                    <input
                        type="file"
                        name="background_image"
                        id="background_image"
                        class="form-control @error('background_image') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp,.gif"
                    >
                    <small class="text-muted d-block mt-1">
                        JPG, PNG, WEBP or GIF. Max 5MB. Leave empty to keep the current image.
                    </small>
                    @error('background_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="mt-2">
                        @if($specialOffer->image_url)
                            <img
                                src="{{ $specialOffer->image_url }}"
                                id="background-preview"
                                alt="{{ $specialOffer->title }}"
                                class="rounded border"
                                style="max-height:180px; object-fit:cover;"
                            >
                        @else
                            <img
                                src=""
                                id="background-preview"
                                alt="{{ $specialOffer->title }}"
                                class="rounded border d-none"
                                style="max-height:180px; object-fit:cover;"
                            >
                            <div id="background-preview-empty" class="text-muted small">No image uploaded</div>
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
                        value="{{ old('sort_order', $specialOffer->sort_order) }}"
                    >
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ (string) old('is_active', $specialOffer->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('is_active', $specialOffer->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <hr class="my-2">
                    <h5 class="mb-2">Bottom Features</h5>
                    <p class="text-muted small mb-3">These appear as the feature items below the offer banner on the homepage.</p>
                </div>

                @php
                    $featureItems = old('features', $specialOffer->features ?? []);
                    if (empty($featureItems)) {
                        $featureItems = [
                            ['icon' => '', 'title' => 'Free Shipping', 'description' => 'for all orders above ₹ 2000'],
                            ['icon' => '', 'title' => 'Free Returns', 'description' => 'within 10 days'],
                            ['icon' => '', 'title' => 'Secure shopping', 'description' => "you're in safe hands"],
                            ['icon' => '', 'title' => 'Over 10,000 Styles', 'description' => 'to match your unique style'],
                        ];
                    }
                @endphp

                @foreach($featureItems as $index => $item)
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Feature Title</label>
                                    <input
                                        type="text"
                                        name="features[{{ $index }}][title]"
                                        class="form-control"
                                        value="{{ is_array($item) ? ($item['title'] ?? '') : $item }}"
                                        placeholder="e.g., Free Shipping"
                                    >
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Feature Description</label>
                                    <input
                                        type="text"
                                        name="features[{{ $index }}][description]"
                                        class="form-control"
                                        value="{{ is_array($item) ? ($item['description'] ?? '') : '' }}"
                                        placeholder="e.g., for all orders above ₹ 2000"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Special Offer</button>
                    <a href="{{ route('admin.special-offers.edit', 1) }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('background_image')?.addEventListener('change', function () {
    const file = this.files[0];
    const preview = document.getElementById('background-preview');
    const empty = document.getElementById('background-preview-empty');

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
