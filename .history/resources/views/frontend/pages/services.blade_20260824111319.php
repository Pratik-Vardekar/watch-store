@extends('layouts.front')

@section('title', 'Our Services')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="display-4 fw-bold">Our Services</h1>
            <p class="text-muted">Discover the services we offer to make your experience better</p>
        </div>
    </div>

    <div class="row">
        @forelse($services as $service)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                @php
                    // Debug: Check what's in the database
                    // Uncomment to see the actual image value
                    // dd($service->image);
                    
                    // Get the image URL
                    $imageUrl = asset('frontend/assets/images/no-image.png');
                    
                    if ($service->image) {
                        // If image is a full URL
                        if (filter_var($service->image, FILTER_VALIDATE_URL)) {
                            $imageUrl = $service->image;
                        }
                        // If image path starts with 'uploads/'
                        elseif (strpos($service->image, 'uploads/') === 0) {
                            $imageUrl = asset($service->image);
                        }
                        // If image path starts with '/'
                        elseif (strpos($service->image, '/') === 0) {
                            $imageUrl = asset($service->image);
                        }
                        // If image path starts with 'backend/'
                        elseif (strpos($service->image, 'backend/') === 0) {
                            $imageUrl = asset($service->image);
                        }
                        // Default: image is just a filename in uploads/services/
                        else {
                            $imageUrl = asset('uploads/services/' . $service->image);
                        }
                    }
                @endphp

                <img src="{{ $imageUrl }}"
                     class="card-img-top"
                     alt="{{ $service->name }}"
                     style="height: 200px; object-fit: cover;"
                     onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/no-image.png') }}';">

                <div class="card-body">
                    <h5 class="card-title">{{ $service->name }}</h5>
                    <p class="card-text text-muted">
                        {{ Str::limit($service->description ?? '', 150) }}
                    </p>
                    @if($service->url)
                        <a href="{{ $service->url }}" class="btn btn-primary btn-sm">
                            Learn More
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <p class="mb-0">No services available at the moment.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection