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
        <h2 class="sectTitle text-center mb-4">Our <span class="txtRed">Brands</span></h2>
        
        @if(isset($brands) && $brands->isNotEmpty())
            @php
                $half = ceil($brands->count() / 2);
                $firstRow = $brands->slice(0, $half);
                $secondRow = $brands->slice($half);
            @endphp

            <!-- First Row -->
            <div class="brand-marquee-wrapper">
                <div class="swiper brandMarquee">
                    <div class="swiper-wrapper">
                        @foreach($firstRow as $brand)
                            <div class="swiper-slide">
                                <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" 
                                   class="brand-item"
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

            <!-- Second Row (Reverse Direction) -->
            <div class="brand-marquee-wrapper mt-2">
                <div class="swiper brandMarqueeReverse">
                    <div class="swiper-wrapper">
                        @foreach($secondRow as $brand)
                            <div class="swiper-slide">
                                <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" 
                                   class="brand-item"
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
    /* Brand Marquee Styles */
    .brand-marquee-wrapper {
        overflow: hidden;
        position: relative;
        padding: 5px 0;
        background: #f5f5f5;
    }

    .brand-marquee-wrapper:first-child {
        background: #ffffff;
    }

    .brand-marquee-wrapper:last-child {
        background: #f5f5f5;
    }

    .brandMarquee,
    .brandMarqueeReverse {
        overflow: visible;
    }

    .brandMarquee .swiper-wrapper,
    .brandMarqueeReverse .swiper-wrapper {
        transition-timing-function: linear !important;
        align-items: center;
    }

    .brandMarquee .swiper-slide,
    .brandMarqueeReverse .swiper-slide {
        width: auto !important;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 20px;
    }

    .brand-item {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px 10px;
        background: transparent;
        transition: all 0.3s ease;
        text-decoration: none;
        min-width: 120px;
        min-height: 70px;
    }

    .brand-item .brand-logo {
        max-height: 50px;
        width: auto;
        object-fit: contain;
        filter: grayscale(0%);
        transition: all 0.3s ease;
        display: block;
    }

    .brand-item:hover {
        transform: scale(1.05);
    }

    .brand-item:hover .brand-logo {
        filter: grayscale(0%);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .brand-item {
            min-width: 90px;
            padding: 10px 8px;
            min-height: 60px;
        }
        .brand-item .brand-logo {
            max-height: 40px;
        }
        .brandMarquee .swiper-slide,
        .brandMarqueeReverse .swiper-slide {
            padding: 0 15px;
        }
    }

    @media (max-width: 768px) {
        .brand-item {
            min-width: 70px;
            padding: 8px 5px;
            min-height: 50px;
        }
        .brand-item .brand-logo {
            max-height: 32px;
        }
        .brandMarquee .swiper-slide,
        .brandMarqueeReverse .swiper-slide {
            padding: 0 10px;
        }
    }

    @media (max-width: 480px) {
        .brand-item {
            min-width: 60px;
            padding: 5px 3px;
            min-height: 40px;
        }
        .brand-item .brand-logo {
            max-height: 25px;
        }
        .brandMarquee .swiper-slide,
        .brandMarqueeReverse .swiper-slide {
            padding: 0 8px;
        }
        .brand-marquee-wrapper {
            padding: 3px 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Brand Marquee - First Row (Left to Right)
        const brandMarquee = document.querySelector('.brandMarquee');
        if (brandMarquee) {
            new Swiper(brandMarquee, {
                slidesPerView: 'auto',
                spaceBetween: 5,
                loop: true,
                speed: 3500,
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

        // Initialize Brand Marquee - Second Row (Right to Left)
        const brandMarqueeReverse = document.querySelector('.brandMarqueeReverse');
        if (brandMarqueeReverse) {
            new Swiper(brandMarqueeReverse, {
                slidesPerView: 'auto',
                spaceBetween: 5,
                loop: true,
                speed: 3500,
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