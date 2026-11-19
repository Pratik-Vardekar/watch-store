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
    <section class="py-5 overflow-hidden bg-light">
        <div class="container">
            <h2 class="sectTitle text-center mb-5">Our <span class="txtRed">Brands</span></h2>
            
            @if(isset($brands) && $brands->isNotEmpty())
                @php
                    $half = ceil($brands->count() / 2);
                    $firstRow = $brands->slice(0, $half);
                    $secondRow = $brands->slice($half);
                @endphp

                <!-- First Row -->
                <div class="brand-slider-wrapper">
                    <div class="swiper brandSlider">
                        <div class="swiper-wrapper">
                            @foreach($firstRow as $brand)
                                <div class="swiper-slide">
                                    <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" 
                                       class="brand-card"
                                       title="View {{ $brand->name }} products">
                                        <img src="{{ $brand->logo_url }}" 
                                             alt="{{ $brand->name }}"
                                             class="brand-logo"
                                             onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/brands/default.jpg') }}';">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="brand-slider-wrapper mt-2">
                    <div class="swiper brandSliderReverse">
                        <div class="swiper-wrapper">
                            @foreach($secondRow as $brand)
                                <div class="swiper-slide">
                                    <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" 
                                       class="brand-card"
                                       title="View {{ $brand->name }} products">
                                        <img src="{{ $brand->logo_url }}" 
                                             alt="{{ $brand->name }}"
                                             class="brand-logo"
                                             onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/brands/default.jpg') }}';">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center my-5">
                    <p class="text-muted">No brands available at the moment.</p>
                </div>
            @endif
        </div>
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

@push('styles')
<style>
    /* Brand Slider Styles - Matching the image design */
    .brand-slider-wrapper {
        overflow: hidden;
        position: relative;
        padding: 10px 0;
    }

    .brandSlider,
    .brandSliderReverse {
        overflow: visible;
    }

    .brandSlider .swiper-wrapper,
    .brandSliderReverse .swiper-wrapper {
        transition-timing-function: linear !important;
        align-items: center;
    }

    .brandSlider .swiper-slide,
    .brandSliderReverse .swiper-slide {
        width: auto !important;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 20px;
    }

    .brand-card {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px 10px;
        background: transparent;
        transition: all 0.3s ease;
        text-decoration: none;
        min-width: 100px;
    }

    .brand-card:hover {
        transform: scale(1.05);
    }

    .brand-card .brand-logo {
        max-height: 50px;
        width: auto;
        object-fit: contain;
        filter: grayscale(0%);
        transition: filter 0.3s ease;
        display: block;
    }

    .brand-card:hover .brand-logo {
        filter: grayscale(0%);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .brand-card {
            min-width: 80px;
            padding: 10px 8px;
        }
        .brand-card .brand-logo {
            max-height: 40px;
        }
        .brandSlider .swiper-slide,
        .brandSliderReverse .swiper-slide {
            padding: 0 15px;
        }
    }

    @media (max-width: 768px) {
        .brand-card {
            min-width: 60px;
            padding: 8px 5px;
        }
        .brand-card .brand-logo {
            max-height: 30px;
        }
        .brandSlider .swiper-slide,
        .brandSliderReverse .swiper-slide {
            padding: 0 10px;
        }
    }

    @media (max-width: 480px) {
        .brand-card {
            min-width: 50px;
            padding: 5px 3px;
        }
        .brand-card .brand-logo {
            max-height: 25px;
        }
        .brandSlider .swiper-slide,
        .brandSliderReverse .swiper-slide {
            padding: 0 8px;
        }
        .brand-slider-wrapper {
            padding: 5px 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Brand Slider - First Row (Left to Right)
        const brandSlider = document.querySelector('.brandSlider');
        if (brandSlider) {
            new Swiper(brandSlider, {
                slidesPerView: 'auto',
                spaceBetween: 5,
                loop: true,
                speed: 3000,
                autoplay: {
                    delay: 0,
                    disableOnInteraction: false,
                },
                freeMode: true,
                freeModeMomentum: false,
                watchSlidesProgress: true,
                breakpoints: {
                    320: {
                        slidesPerView: 3,
                        spaceBetween: 5,
                    },
                    480: {
                        slidesPerView: 4,
                        spaceBetween: 8,
                    },
                    768: {
                        slidesPerView: 6,
                        spaceBetween: 10,
                    },
                    992: {
                        slidesPerView: 8,
                        spaceBetween: 15,
                    },
                    1200: {
                        slidesPerView: 10,
                        spaceBetween: 20,
                    }
                }
            });
        }

        // Initialize Brand Slider - Second Row (Right to Left)
        const brandSliderReverse = document.querySelector('.brandSliderReverse');
        if (brandSliderReverse) {
            new Swiper(brandSliderReverse, {
                slidesPerView: 'auto',
                spaceBetween: 5,
                loop: true,
                speed: 3000,
                autoplay: {
                    delay: 0,
                    disableOnInteraction: false,
                    reverseDirection: true,
                },
                freeMode: true,
                freeModeMomentum: false,
                watchSlidesProgress: true,
                breakpoints: {
                    320: {
                        slidesPerView: 3,
                        spaceBetween: 5,
                    },
                    480: {
                        slidesPerView: 4,
                        spaceBetween: 8,
                    },
                    768: {
                        slidesPerView: 6,
                        spaceBetween: 10,
                    },
                    992: {
                        slidesPerView: 8,
                        spaceBetween: 15,
                    },
                    1200: {
                        slidesPerView: 10,
                        spaceBetween: 20,
                    }
                }
            });
        }

        // Initialize Testimonials Slider
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