@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">
    <div class="mb-4">
        <h4 class="mb-1">Header Menu Images</h4>
        <p class="text-muted mb-0">Upload images shown in the header dropdown for Brands, Women, Men, and About Us.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $brandsImg = $setting->brands_image
            ? asset($setting->brands_image)
            : asset('frontend/assets/images/menuImg_brands.jpg');

        $womenImg = $setting->women_image
            ? asset($setting->women_image)
            : asset('frontend/assets/images/menuImg_women.jpg');

        $menImg = $setting->men_image
            ? asset($setting->men_image)
            : asset('frontend/assets/images/menuImg_men.jpg');

        $aboutImg = $setting->about_image
            ? asset($setting->about_image)
            : asset('frontend/assets/images/menuImg_store.webp');
    @endphp

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.header-menu-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            <strong class="text-dark">Recommended size:</strong> 1920 × 825 px (approx.)
                            <span class="text-secondary">Max 5MB per image.</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Brands Image</label>
                        <input type="file" name="brands_image" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img src="{{ $brandsImg }}" class="rounded border" style="max-height:120px; width:auto; object-fit:cover;" alt="Brands preview">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Women Image</label>
                        <input type="file" name="women_image" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img src="{{ $womenImg }}" class="rounded border" style="max-height:120px; width:auto; object-fit:cover;" alt="Women preview">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Men Image</label>
                        <input type="file" name="men_image" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img src="{{ $menImg }}" class="rounded border" style="max-height:120px; width:auto; object-fit:cover;" alt="Men preview">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">About Us Image</label>
                        <input type="file" name="about_image" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img src="{{ $aboutImg }}" class="rounded border" style="max-height:120px; width:auto; object-fit:cover;" alt="About preview">
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-device-floppy me-1"></i>
                        Save Header Menu Images
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

