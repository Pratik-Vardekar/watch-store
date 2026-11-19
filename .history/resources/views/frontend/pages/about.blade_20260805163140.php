@extends('layouts.front')
@section('content')

<main class="pageWrapper">

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
                    <div class="about-content">
                        {!! nl2br(e($aboutSection->content ?? '')) !!}
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- Our Brands --}}
    <section class="py-md-5 py-4 overflow-hidden">
        <h2 class="sectTitle text-center mb-4">Our Brands</h2>
        
        @if(isset($brands) && $brands->isNotEmpty())
            @php
                $half = ceil($brands->count() / 2);
            @endphp

            <!-- First Row -->
            <div class="swiper marqueStrip overflow-visible">
                <div class="swiper-wrapper">
                    @foreach($brands->slice(0, $half) as $brand)
                        <div class="swiper-slide">
                            <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" title="View {{ $brand->name }} products">
                                <img src="{{ $brand->logo_url ?? asset('frontend/assets/images/brands/default.jpg') }}" alt="{{ $brand->name }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Second Row -->
            <div class="swiper marqueStrip overflow-visible" dir="rtl">
                <div class="swiper-wrapper">
                    @foreach($brands->slice($half) as $brand)
                        <div class="swiper-slide">
                            <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" title="View {{ $brand->name }} products">
                                <img src="{{ $brand->logo_url ?? asset('frontend/assets/images/brands/default.jpg') }}" alt="{{ $brand->name }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center my-5">
                <p class="text-muted">No brands available at the moment.</p>
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
        // Initialize marquee sliders for brands
        const marqueeSliders = document.querySelectorAll('.marqueStrip');
        
        marqueeSliders.forEach(function(slider) {
            const isRTL = slider.getAttribute('dir') === 'rtl';
            
            new Swiper(slider, {
                slidesPerView: 'auto',
                spaceBetween: 20,
                loop: true,
                speed: 3000,
                autoplay: {
                    delay: 0,
                    disableOnInteraction: false,
                    reverseDirection: isRTL,
                },
                freeMode: true,
                freeModeMomentum: false,
                watchSlidesProgress: true,
                breakpoints: {
                    320: {
                        slidesPerView: 3,
                        spaceBetween: 10,
                    },
                    480: {
                        slidesPerView: 4,
                        spaceBetween: 15,
                    },
                    768: {
                        slidesPerView: 6,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 8,
                        spaceBetween: 25,
                    },
                    1200: {
                        slidesPerView: 10,
                        spaceBetween: 30,
                    }
                }
            });
        });

        // Initialize testimonials slider
        const testimSlider = document.querySelector('.testimSwiper');
        if (testimSlider) {
            new Swiper(testimSlider, {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: true,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 1,
                    },
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                }
            });
        }
    });
</script>
@endpush