@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="mb-1">Page & Listing Banners</h4>
            <p class="text-muted mb-0">
                Manage top header banners for Men, Women, Brands, and General Product Listing pages.
                Recommended Intrinsic Size: <span class="badge bg-primary text-white fs-6">2513 × 400 px</span>
            </p>
        </div>
        <div>
            <span class="badge bg-info-subtle text-info border border-info px-3 py-2">
                <i class="ti ti-aspect-ratio me-1"></i> Aspect Ratio: ~6.28 : 1 (Wide Banner)
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form action="{{ route('home-banner.update') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                @foreach($banners as $banner)
                    @php
                        $targetUrl = match($banner->slug) {
                            'men' => route('product.listing', ['gender' => 'men']),
                            'women' => route('product.listing', ['gender' => 'women']),
                            'brands' => route('brands'),
                            'default' => route('product.listing'),
                            default => url($banner->slug),
                        };

                        $customImageUrl = $banner->image_url;
                        $hasCustomImage = !empty($customImageUrl);
                        $defaultImageUrl = asset('frontend/assets/images/prodListBanner.jpg');
                        $currentImageUrl = $hasCustomImage ? $customImageUrl : $defaultImageUrl;
                    @endphp

                    <div class="card shadow-sm border mb-4">

                        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h5 class="mb-1 text-dark fw-bold">
                                    <i class="ti ti-photo me-1 text-primary"></i>
                                    {{ $banner->name }}
                                </h5>
                                <div class="small text-muted">
                                    Displays on:
                                    <a href="{{ $targetUrl }}" target="_blank" class="text-decoration-underline text-primary">
                                        {{ $targetUrl }} <i class="ti ti-external-link ms-1"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                @if($hasCustomImage)
                                    <span class="badge bg-success-subtle text-success border border-success">
                                        <i class="ti ti-check me-1"></i> Custom Banner Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary">
                                        <i class="ti ti-info-circle me-1"></i> Using Default Banner
                                    </span>
                                @endif

                                <span class="badge bg-primary fs-7">
                                    {{ $banner->recommended_size ?? '2513 × 400 px' }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">

                            <input type="hidden"
                                   name="banner_ids[]"
                                   value="{{ $banner->id }}">

                            <div class="row align-items-center g-4">

                                <!-- Current Preview -->
                                <div class="col-lg-7">
                                    <label class="form-label fw-bold mb-2 text-secondary">
                                        Banner Preview
                                    </label>

                                    <div class="border rounded bg-dark p-2 text-center position-relative overflow-hidden shadow-sm"
                                         style="background-color: #1a1a1a !important;">
                                        <img
                                            id="preview-{{ $banner->id }}"
                                            src="{{ $currentImageUrl }}"
                                            class="img-fluid rounded"
                                            alt="{{ $banner->name }}"
                                            onerror="this.onerror=null;this.src='{{ $defaultImageUrl }}';"
                                            style="width: 100%; max-height: 180px; aspect-ratio: 2513 / 400; object-fit: cover;">
                                    </div>

                                    @if($hasCustomImage)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="remove_image[{{ $banner->id }}]"
                                                   id="remove-{{ $banner->id }}"
                                                   value="1">
                                            <label class="form-check-label text-danger small" for="remove-{{ $banner->id }}">
                                                <i class="ti ti-trash me-1"></i> Remove custom banner and reset to default
                                            </label>
                                        </div>
                                    @endif
                                </div>

                                <!-- Upload New -->
                                <div class="col-lg-5">

                                    <label class="form-label fw-bold mb-2">
                                        Upload New Banner Image
                                    </label>

                                    <div class="input-group mb-2">
                                        <input
                                            type="file"
                                            class="form-control banner-input"
                                            name="image[{{ $banner->id }}]"
                                            id="input-{{ $banner->id }}"
                                            data-preview="preview-{{ $banner->id }}"
                                            accept="image/jpeg,image/png,image/webp,image/jpg">
                                    </div>

                                    <div class="small text-muted mb-2">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Recommended Size: <strong>2513 × 400 px</strong>. Formats: JPG, PNG, WEBP. Max: 5MB.
                                    </div>

                                    <div class="p-2 bg-light border rounded small text-muted">
                                        Tip: High resolution wide graphics (2513x400) ensure sharp rendering on retina & desktop screens without stretching.
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                        <i class="ti ti-device-floppy me-1"></i>
                        Save All Banners
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.banner-input').forEach(input => {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const preview = document.getElementById(this.dataset.preview);
            if (preview) {
                preview.src = URL.createObjectURL(file);
            }
        });
    });
});
</script>
@endpush