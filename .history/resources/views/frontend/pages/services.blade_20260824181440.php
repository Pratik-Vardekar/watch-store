@extends('layouts.front')

@section('title', 'Our Services')

@section('content')

<div class="container py-5">

    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="display-4 fw-bold">Our Services</h1>

            <p class="text-muted">
                Discover the services we offer to make your experience better
            </p>
        </div>
    </div>

    <div class="row">

        @forelse($services as $service)

            <div class="col-md-4 mb-4">

                <div class="card h-100 shadow-sm border-0">

                    {{-- Service Image --}}
                    @if($service->images->count())

                        <img src="{{ asset('backend/assets/images/services/' . $service->images->first()->image) }}"
                             class="card-img-top"
                             alt="{{ $service->name }}"
                             style="height: 200px; object-fit: cover;">

                    @else

                        <img src="{{ asset('frontend/assets/images/no-image.png') }}"
                             class="card-img-top"
                             alt="{{ $service->name }}"
                             style="height: 200px; object-fit: cover;">

                    @endif


                    <div class="card-body">

                        <h5 class="card-title">
                            {{ $service->name }}
                        </h5>

                        <p class="card-text text-muted">
                            {{ !!Str::limit($service->description ?? '', 150)  !!}}
                        </p>

                        @if($service->url)

                            <a href="{{ $service->url }}"
                               class="btn btn-primary btn-sm">
                                Learn More
                            </a>

                        @endif

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-info text-center">
                    No services available at the moment.
                </div>

            </div>

        @endforelse

    </div>

</div>

@endsection