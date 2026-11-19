@extends('layouts.front')
@section('content')

<main class="pageWrapper">

    {{-- HERO SECTION --}}
    <section class="py-5">
        <div class="container d-flex justify-content-center">
            <div class="col-lg-9">
                <div class="mx-auto generalContent text-center">

                    <div class="position-relative rounded-4 mb-4 overflow-hidden">
                        <img src="{{ $aboutSection->image_url ?? asset('frontend/assets/images/heroBanner_2.jpg') }}" 
                             alt="{{ $aboutSection->title ?? 'About Us' }}" 
                             class="w-100"
                             style="max-height:400px; object-fit:cover;">
                        <h1 class="sectTitle text-center text-white position-absolute top-50 start-50 translate-middle" 
                            style="text-shadow:0 0 20px rgba(0,0,0,0.8);">
                            {{ $aboutSection->title ?? 'About Us' }}
                        </h1>
                    </div>
                    
                    <div class="about-content">
                        {!! nl2br(e($aboutSection->content ?? '')) !!}
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    
    {{-- OUR BRANDS WITH BOX DESIGN --}}
    <section class="py-4 overflow-hidden">
        <h2 class="sectTitle text-center mb-4">Our <span class="txtRed">Brands</span></h2>

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
                                    <div class="brand-card-inner">
                                        <img src="{{ $brand->logo_url }}" 
                                             alt="{{ $brand->name }}"
                                             class="brand-logo"
                                             onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/brands/default.jpg') }}';">
                                        <span class="brand-name">{{ $brand->name }}</span>
                                    </div>
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
                                    <div class="brand-card-inner">
                                        <img src="{{ $brand->logo_url }}" 
                                             alt="{{ $brand->name }}"
                                             class="brand-logo"
                                             onerror="this.onerror=null; this.src='{{ asset('frontend/assets/images/brands/default.jpg') }}';">
                                        <span class="brand-name">{{ $brand->name }}</span>
                                    </div>
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
                <div class="swiper pb-sm-4 pb-3 testimSwiper">
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
                                    ?? asset('frontend/assets/images/user_default.jpg');
                            @endphp
                            <div class="swiper-slide">
                                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">
                                    <div class="ti_rating">
                                        <div class="ratingBox"> 
                                            <div class="ratedStars {{ $testimonialRatingClass }}"></div> 
                                        </div>
                                    </div>
                                    <div class="ti_review">
                                        <p>{{ $testimonial->review }}</p>
                                    </div>
                                    <div class="ti_user pt-4 d-flex align-items-center gap-3">
                                        <div class="ti_userImg rounded-circle overflow-hidden">
                                            <img src="{{ $avatarUrl }}" 
                                                 alt="{{ $testimonial->user->name ?? 'Customer' }}" 
                                                 class="ratio ratio-1x1"
                                                 style="width:50px; height:50px; object-fit:cover;">
                                        </div>
                                        <div class="ti_userInfo">
                                            <strong class="d-block fw-bold">{{ $testimonial->user->name ?? 'Anonymous' }}</strong>
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
    /* Brand Slider Wrapper */
    .brand-slider-wrapper {
        overflow: hidden;
        position: relative;
        padding: 15px 0;
        background: #f5f5f5;
    }

    .brand-slider-wrapper:first-child {
        background: #ffffff;
    }

    .brand-slider-wrapper:last-child {
        background: #f5f5f5;
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
        padding: 0 12px;
    }

    /* Brand Card */
    .brand-card {
        display: block;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .brand-card-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        padding: 20px 30px;
        min-width: 150px;
        min-height: 110px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .brand-card:hover .brand-card-inner {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        border-color: var(--redColor, #ee0000);
    }

    .brand-card .brand-logo {
        max-height: 50px;
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
        display: block;
    }

    .brand-card:hover .brand-logo {
        transform: scale(1.05);
    }

    .brand-card .brand-name {
        font-size: 12px;
        font-weight: 600;
        color: #444;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 10px;
        text-align: center;
        line-height: 1.3;
    }

    .brand-card:hover .brand-name {
        color: var(--redColor, #ee0000);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .brand-card-inner {
            padding: 16px 22px;
            min-width: 130px;
            min-height: 95px;
        }
        .brand-card .brand-logo {
            max-height: 42px;
        }
        .brand-card .brand-name {
            font-size: 11px;
        }
    }

    @media (max-width: 992px) {
        .brand-card-inner {
            padding: 14px 18px;
            min-width: 110px;
            min-height: 80px;
            border-radius: 10px;
        }
        .brand-card .brand-logo {
            max-height: 35px;
        }
        .brand-card .brand-name {
            font-size: 10px;
            margin-top: 8px;
        }
        .brandSlider .swiper-slide,
        .brandSliderReverse .swiper-slide {
            padding: 0 8px;
        }
    }

    @media (max-width: 768px) {
        .brand-card-inner {
            padding: 12px 14px;
            min-width: 85px;
            min-height: 65px;
            border-radius: 8px;
        }
        .brand-card .brand-logo {
            max-height: 28px;
        }
        .brand-card .brand-name {
            font-size: 8px;
            margin-top: 6px;
        }
        .brandSlider .swiper-slide,
        .brandSliderReverse .swiper-slide {
            padding: 0 5px;
        }
        .brand-slider-wrapper {
            padding: 10px 0;
        }
    }

    @media (max-width: 480px) {
        .brand-card-inner {
            padding: 8px 10px;
            min-width: 65px;
            min-height: 50px;
            border-radius: 6px;
        }
        .brand-card .brand-logo {
            max-height: 20px;
        }
        .brand-card .brand-name {
            font-size: 7px;
            margin-top: 4px;
            letter-spacing: 0.2px;
        }
        .brandSlider .swiper-slide,
        .brandSliderReverse .swiper-slide {
            padding: 0 3px;
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
                        spaceBetween: 3,
                    },
                    480: {
                        slidesPerView: 4,
                        spaceBetween: 5,
                    },
                    768: {
                        slidesPerView: 6,
                        spaceBetween: 8,
                    },
                    992: {
                        slidesPerView: 8,
                        spaceBetween: 10,
                    },
                    1200: {
                        slidesPerView: 10,
                        spaceBetween: 12,
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
                        spaceBetween: 3,
                    },
                    480: {
                        slidesPerView: 4,
                        spaceBetween: 5,
                    },
                    768: {
                        slidesPerView: 6,
                        spaceBetween: 8,
                    },
                    992: {
                        slidesPerView: 8,
                        spaceBetween: 10,
                    },
                    1200: {
                        slidesPerView: 10,
                        spaceBetween: 12,
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