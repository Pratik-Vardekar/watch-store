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

                <!-- Background Image -->
                <div class="col-md-6">
                    <label class="form-label">Background Image</label>
                    <input
                        type="file"
                        name="background_image"
                        id="background_image"
                        class="form-control @error('background_image') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp,.gif"
                    >
                    <small class="text-muted d-block mt-1">
                        JPG, PNG, WEBP or GIF. Max 5MB.
                    </small>
                    @error('background_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="mt-2">
                        @if($specialOffer->image_url)
                            <img src="{{ $specialOffer->image_url }}" id="background-preview" alt="{{ $specialOffer->title }}" class="rounded border" style="max-height:180px; object-fit:cover;">
                        @else
                            <img src="" id="background-preview" alt="{{ $specialOffer->title }}" class="rounded border d-none" style="max-height:180px; object-fit:cover;">
                            <div id="background-preview-empty" class="text-muted small">No image uploaded</div>
                        @endif
                    </div>
                </div>

                <!-- Main Logo -->
                <div class="col-md-6">
                    <label class="form-label">Main Logo</label>
                    <input
                        type="file"
                        name="logo"
                        id="logo_input"
                        class="form-control @error('logo') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp,.gif,.svg"
                    >
                    <small class="text-muted d-block mt-1">
                        JPG, PNG, WEBP, GIF or SVG. Max 2MB.
                    </small>
                    @error('logo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="mt-2">
                        @if($specialOffer->logo_url)
                            <img src="{{ $specialOffer->logo_url }}" id="logo-preview" alt="Logo" class="rounded border" style="max-height:80px; object-fit:contain; padding:5px; background:#f8f9fa;">
                        @else
                            <img src="" id="logo-preview" alt="Logo" class="rounded border d-none" style="max-height:80px; object-fit:contain; padding:5px; background:#f8f9fa;">
                            <div id="logo-preview-empty" class="text-muted small">No logo uploaded</div>
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
                    <h5 class="mb-2">Features with Icons</h5>
                    <p class="text-muted small mb-3">Each feature can have its own icon. You can upload an image file or paste SVG code.</p>
                </div>

                @php
                    $featureItems = old('features', $specialOffer->features ?? []);
                    $featureIcons = old('feature_icons', $specialOffer->feature_icons ?? []);
                    
                    if (empty($featureItems)) {
                        $featureItems = [
                            ['title' => 'Free Shipping', 'description' => 'for all orders above ₹ 2000'],
                            ['title' => 'Free Returns', 'description' => 'within 10 days'],
                            ['title' => 'Secure shopping', 'description' => "you're in safe hands"],
                            ['title' => 'Over 10,000 Styles', 'description' => 'to match your unique style'],
                        ];
                    }
                    
                    // Ensure icons array matches features count
                    while (count($featureIcons) < count($featureItems)) {
                        $featureIcons[] = '';
                    }
                @endphp

                @foreach($featureItems as $index => $item)
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <div class="row g-3">
                                <!-- Icon Upload -->
                                <div class="col-md-3">
                                    <label class="form-label">Feature #{{ $index + 1 }} Icon</label>
                                    <input
                                        type="file"
                                        name="feature_icon_files[{{ $index }}]"
                                        class="form-control feature-icon-upload"
                                        accept=".jpg,.jpeg,.png,.webp,.gif,.svg"
                                        data-index="{{ $index }}"
                                    >
                                    <small class="text-muted d-block mt-1">Upload image (JPG, PNG, SVG)</small>
                                    
                                    <!-- Or SVG Input -->
                                    <div class="mt-2">
                                        <label class="form-label small">OR Paste SVG Code</label>
                                        <input
                                            type="text"
                                            name="feature_icons[{{ $index }}]"
                                            class="form-control form-control-sm feature-icon-text"
                                            value="{{ $featureIcons[$index] ?? '' }}"
                                            placeholder='<svg>...</svg>'
                                            data-index="{{ $index }}"
                                        >
                                    </div>
                                    
                                    <!-- Current Icon Preview -->
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Current Icon:</small>
                                        <div class="mt-1 feature-icon-preview" id="feature-icon-preview-{{ $index }}">
                                            @if(!empty($featureIcons[$index] ?? ''))
                                                @if(filter_var($featureIcons[$index], FILTER_VALIDATE_URL) || 
                                                    (str_starts_with($featureIcons[$index], 'uploads/')))
                                                    <img src="{{ asset($featureIcons[$index]) }}" alt="Feature Icon" style="max-height:50px; max-width:50px; object-fit:contain;">
                                                @else
                                                    {!! $featureIcons[$index] !!}
                                                @endif
                                            @else
                                                <span class="text-muted small">No icon uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Feature Title -->
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
                                
                                <!-- Feature Description -->
                                <div class="col-md-5">
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
                    <button type="submit" class="btn btn-primary">
                        Update Special Offer
                    </button>
                    <a href="{{ route('admin.special-offers.edit', 1) }}" class="btn btn-secondary">Back</a>
                </div>
           
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Background image preview
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

// Logo preview
document.getElementById('logo_input')?.addEventListener('change', function () {
    const file = this.files[0];
    const preview = document.getElementById('logo-preview');
    const empty = document.getElementById('logo-preview-empty');

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

// Feature icon file preview
document.querySelectorAll('.feature-icon-upload').forEach(input => {
    input.addEventListener('change', function () {
        const file = this.files[0];
        const index = this.dataset.index;
        const preview = document.getElementById('feature-icon-preview-' + index);

        if (!file || !preview) return;

        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image.');
            this.value = '';
            return;
        }

        // Clear the text input when file is selected
        const textInput = document.querySelector(`.feature-icon-text[data-index="${index}"]`);
        if (textInput) {
            textInput.value = '';
        }

        // Show preview
        preview.innerHTML = `<img src="${URL.createObjectURL(file)}" alt="Feature Icon" style="max-height:50px; max-width:50px; object-fit:contain;">`;
    });
});

// Feature icon text input preview
document.querySelectorAll('.feature-icon-text').forEach(input => {
    input.addEventListener('input', function () {
        const index = this.dataset.index;
        const preview = document.getElementById('feature-icon-preview-' + index);
        const value = this.value.trim();

        if (!preview) return;

        if (value) {
            // Clear the file input when text is entered
            const fileInput = document.querySelector(`.feature-icon-upload[data-index="${index}"]`);
            if (fileInput) {
                fileInput.value = '';
            }

            // Check if it's a URL or SVG
            if (value.startsWith('http://') || value.startsWith('https://') || value.startsWith('uploads/')) {
                preview.innerHTML = `<img src="{{ asset('') }}${value}" alt="Feature Icon" style="max-height:50px; max-width:50px; object-fit:contain;" onerror="this.style.display='none'">`;
            } else if (value.startsWith('<svg')) {
                preview.innerHTML = value;
            } else {
                preview.innerHTML = `<span class="text-muted small">Invalid format</span>`;
            }
        } else {
            // If empty, show default message
            preview.innerHTML = `<span class="text-muted small">No icon uploaded</span>`;
        }
    });
});
</script>
@endpush