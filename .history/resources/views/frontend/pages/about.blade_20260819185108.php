@extends('layouts.front')
@section('content')

<style>
    /* Inherit font family from site */
    .about-wrapper,
    .about-wrapper * {
        font-family: inherit !important;
    }
</style>

<main class="pageWrapper about-wrapper">

    {{-- HERO SECTION WITH DYNAMIC IMAGE --}}
    <section class="py-5">
        <div class="container d-flex justify-content-center">
            <div class="col-lg-9">
                <div class="mx-auto generalContent text-center">

                    <div class="position-relative rounded-4 mb-4 overflow-hidden">
                        {{-- Dynamic Image --}}
                        <img src="{{ $aboutSection->image_url ?? asset('frontend/assets/images/heroBanner_2.jpg') }}" 
                             alt="{{ $aboutSection->title ?? 'About Us' }}" 
                             class="w-100"
                             style="max-height:400px; object-fit:cover;">
                        
                        {{-- Overlay Title --}}
                        <h1 class="sectTitle text-center text-white position-absolute top-50 start-50 translate-middle" 
                            style="text-shadow:0 0 20px rgba(0,0,0,0.8);">
                            {{ $aboutSection->title ?? 'About Us' }}
                        </h1>
                    </div>

                    {{-- Dynamic Content --}}
                    <div class="about-content" style="font-family: ;">
                        {!! nl2br(e($aboutSection->content ?? '')) !!}
                    </div>
               

                </div>
            </div>
        </div>
    </section>

    {{-- Our Brands --}}
    <section class="py-4 overflow-hidden">
        <h2 class="sectTitle text-center mb-4">
            Our Brands
        </h2>
        @php
            $half = ceil($brands->count() / 2);
        @endphp

        @if($brands->isNotEmpty())
            <div class="container">
                <div class="row g-2 justify-content-center align-items-center">
                    @foreach($brands as $brand)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="brand-tile bg-white border rounded-2 d-flex align-items-center justify-content-center py-3 h-100" style="min-height:80px;">
                                <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" 
                                   title="View {{ $brand->name }} products"
                                   class="w-100 d-flex align-items-center justify-content-center" 
                                   style="min-height:40px;">
                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="img-fluid brand-logo" style="max-height:32px;object-fit:contain;max-width:90%;">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center my-5">
                <p>No brands available at the moment.</p>
            </div>
        @endif
    </section>

    {{-- TESTIMONIALS SECTION --}}
    <section class="py-4 overflow-hidden bgGray">
        <div class="container py-4 animateThis slideTop">
            <h2 class="sectTitle text-center mb-3">Watch lovers, loves us too!</h2>
            <div class="text-center sectSubTxt mb-5">Read their sweet stories about our timepieces and heartwarming service.</div>

            @if(isset($homeTestimonials) && $homeTestimonials->isNotEmpty())
                <div class="swiper pb-sm-4 pb-3 testimSwiper" data-slides="{{ $homeTestimonials->count() }}">
                    <div class="swiper-wrapper">
                        @foreach($homeTestimonials as $testimonial)
                            @php
                                $testimonialRating = (float) ($testimonial->rating ?? 0);
                                $testimonialRatingClass = match (true) {
                                    $testimonialRating >= 4.75 => 'star5',
                                    $testimonialRating >= 4.25 => 'star4h',
                                    $testimonialRating >= 3.75 => 'star4',
                                    $testimonialRating >= 3.25 => 'star3h',
                                    $testimonialRating >= 2.75 => 'star3',
                                    $testimonialRating >= 2.25 => 'star2h',
                                    $testimonialRating >= 1.75 => 'star2',
                                    $testimonialRating >= 1.25 => 'star1h',
                                    $testimonialRating >= 0.75 => 'star1',
                                    default => 'star0',
                                };
                                $avatarUrl = optional($testimonial->user)->avatar_url
                                    ?? asset('backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg');
                            @endphp
                            <div class="swiper-slide">
                                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4 h-100">
                                    <div class="ti_rating">
                                        <div class="ratingBox"><div class="ratedStars {{ $testimonialRatingClass }}"></div></div>
                                    </div>
                                    <div class="ti_review">
                                        <p class="mb-0">{{ $testimonial->review }}</p>
                                    </div>
                                    <div class="ti_user pt-4 d-flex align-items-center gap-3 mt-auto">
                                        <div class="ti_userImg rounded-circle overflow-hidden flex-shrink-0">
                                            <img src="{{ $avatarUrl }}" alt="{{ $testimonial->user->name ?? 'Customer' }}" class="w-100 h-100" style="object-fit:cover;">
                                        </div>
                                        <div class="ti_userInfo">
                                            <strong class="d-block fw-bold">{{ $testimonial->user->name ?? 'Customer' }}</strong>
                                            <small>
                                                @if($testimonial->product)
                                                    {{ \Illuminate\Support\Str::limit($testimonial->product->name, 40) }}
                                                @else
                                                    Verified Buyer
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            @else
                <div class="text-center text-secondary py-4">Customer reviews will appear here once approved from Admin → Reviews.</div>
            @endif
        </div>
    </section>

</main>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Brand grid: no JS/swiper needed for static grid version

        // Testimonials slider remains
        const testimSlider = document.querySelector('.testimSwiper');
        if (testimSlider) {
            var aboutTestimCount = testimSlider.querySelectorAll('.swiper-slide').length;
            new Swiper(testimSlider, {
                slidesPerView: 1.5,
                spaceBetween: 0,
                loop: aboutTestimCount > 4,
                grabCursor: true,
                centeredSlides: true,
                autoplay: aboutTestimCount > 1 ? { delay: 5000, disableOnInteraction: false } : false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    576: { slidesPerView: Math.min(2, aboutTestimCount), slidesPerGroup: Math.min(2, aboutTestimCount), centeredSlides: false },
                    992: { slidesPerView: Math.min(3, aboutTestimCount), slidesPerGroup: Math.min(3, aboutTestimCount), centeredSlides: false },
                    1400: { slidesPerView: Math.min(4, aboutTestimCount), slidesPerGroup: Math.min(4, aboutTestimCount), centeredSlides: false },
                }
            });
        }
    });
</script>
@endpush