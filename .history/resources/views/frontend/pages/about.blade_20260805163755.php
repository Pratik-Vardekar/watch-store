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
    <section class="py-md-5 py-4 overflow-hidden bg-light">
        <div class="container">
            <h2 class="sectTitle text-center mb-4">Our <span class="txtRed">Brands</span></h2>
            
            @if(isset($brands) && $brands->isNotEmpty())
                @php
                    // Split brands into two equal groups for two rows
                    $half = ceil($brands->count() / 2);
                    $firstRow = $brands->slice(0, $half);
                    $secondRow = $brands->slice($half);
                @endphp

                <!-- First Row -->
                <div class="brand-marquee-wrapper mb-3">
                    <div class="swiper brandMarquee">
                        <div class="swiper-wrapper">
                            @foreach($firstRow as $brand)
                                <div class="swiper-slide">
                                    <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" 
                                       class="brand-item d-flex flex-column align-items-center justify-content-center p-3 rounded-3 text-decoration-none"
                                       title="View {{ $brand->name }} products">
                                        <img src="{{ $brand->logo_url }}" 
                                             alt="{{ $brand->name }}"
                                             class="brand-logo"
                                             onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/brands/default.jpg') }}';">
                                        <span class="brand-name mt-2 small text-muted">{{ $brand->name }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Second Row (Reverse Direction) -->
                <div class="brand-marquee-wrapper">
                    <div class="swiper brandMarqueeReverse">
                        <div class="swiper-wrapper">
                            @foreach($secondRow as $brand)
                                <div class="swiper-slide">
                                    <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" 
                                       class="brand-item d-flex flex-column align-items-center justify-content-center p-3 rounded-3 text-decoration-none"
                                       title="View {{ $brand->name }} products">
                                        <img src="{{ $brand->logo_url }}" 
                                             alt="{{ $brand->name }}"
                                             class="brand-logo"
                                             onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/brands/default.jpg') }}';">
                                        <span class="brand-name mt-2 small text-muted">{{ $brand->name }}</span>
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
    /* Brand Marquee Styles */
    .brand-marquee-wrapper {
        overflow: hidden;
        position: relative;
        padding: 10px 0;
    }

    .brandMarquee,
    .brandMarqueeReverse {
        overflow: visible;
    }

    .brandMarquee .swiper-wrapper,
    .brandMarqueeReverse .swiper-wrapper {
        transition-timing-function: linear !important;
    }

    .brandMarquee .swiper-slide,
    .brandMarqueeReverse .swiper-slide {
        width: auto !important;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 15px;
    }

    .brand-item {
        background: #ffffff;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        padding: 15px 25px;
        min-width: 120px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        min-height: 100px;
    }

    .brand-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        border-color: #d4a373;
    }

    .brand-item .brand-logo {
        max-height: 50px;
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .brand-item:hover .brand-logo {
        transform: scale(1.05);
    }

    .brand-item .brand-name {
        font-size: 11px;
        font-weight: 500;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 8px;
        white-space: nowrap;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .brand-item {
            padding: 10px 15px;
            min-width: 80px;
            min-height: 70px;
        }

        .brand-item .brand-logo {
            max-height: 35px;
        }

        .brand-item .brand-name {
            font-size: 9px;
        }

        .brandMarquee .swiper-slide,
        .brandMarqueeReverse .swiper-slide {
            padding: 0 8px;
        }
    }

    @media (max-width: 480px) {
        .brand-item {
            padding: 8px 10px;
            min-width: 60px;
            min-height: 60px;
        }

        .brand-item .brand-logo {
            max-height: 25px;
        }

        .brand-item .brand-name {
            font-size: 8px;
            margin-top: 4px;
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
                spaceBetween: 10,
                loop: true,
                speed: 4000,
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
                        spaceBetween: 8,
                    },
                    480: {
                        slidesPerView: 4,
                        spaceBetween: 10,
                    },
                    768: {
                        slidesPerView: 6,
                        spaceBetween: 15,
                    },
                    1024: {
                        slidesPerView: 8,
                        spaceBetween: 20,
                    },
                    1200: {
                        slidesPerView: 10,
                        spaceBetween: 25,
                    }
                }
            });
        }

        // Initialize Brand Marquee - Second Row (Right to Left)
        const brandMarqueeReverse = document.querySelector('.brandMarqueeReverse');
        if (brandMarqueeReverse) {
            new Swiper(brandMarqueeReverse, {
                slidesPerView: 'auto',
                spaceBetween: 10,
                loop: true,
                speed: 4000,
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
                        spaceBetween: 8,
                    },
                    480: {
                        slidesPerView: 4,
                        spaceBetween: 10,
                    },
                    768: {
                        slidesPerView: 6,
                        spaceBetween: 15,
                    },
                    1024: {
                        slidesPerView: 8,
                        spaceBetween: 20,
                    },
                    1200: {
                        slidesPerView: 10,
                        spaceBetween: 25,
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