@extends('layouts.front')
@section('content')

    <main class="pageWrapper about-wrapper">

        {{-- HERO SECTION WITH DYNAMIC IMAGE --}}
        <section class="py-5">
            <div class="container d-flex justify-content-center">
                <div class="col-lg-9">
                    <div class="mx-auto generalContent text-center">

                        <div class="position-relative rounded-4 mb-4 overflow-hidden">
                            {{-- Dynamic Image --}}
                            <img src="{{ $aboutSection->image_url ?? asset('frontend/assets/images/heroBanner_2.jpg') }}"
                                alt="{{ $aboutSection->title ?? 'About Us' }}" class="w-100 about-hero-img">

                            {{-- Overlay Title --}}
                            <h1
                                class="sectTitle text-center text-white position-absolute top-50 start-50 translate-middle about-hero-title">
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

        <section class="bg-black">
            <div class="servBanWrap overflow-hidden py-sm-4">
                <img src="{{ $homeServiceSectionSetting?->background_image_url ?? asset('frontend/assets/images/textureBg.jpg') }}"
                    alt="" class="servBg">

                <div class="container py-5 position-relative z-1 text-center animateThis slideTop">
                    <div class="txtGold sectSubTxt mb-3">Authorised Service Center</div>
                    <h2 class="sectTitle text-white">Expert Care. Original Precision.</h2>

                    <div class="mt-5 mx-auto servCanvas position-relative">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 548.5 376.4" class="svgRoute">
                            <path class="travelPath" fill-rule="evenodd" clip-rule="evenodd" fill="none"
                                stroke="rgba(255,255,255,.15)" stroke-width="15" stroke-linecap="round"
                                stroke-miterlimit="10"
                                d="M411.2,123.3c0,0-171-138.5-210.6-109.3c-39.3,29,36.7,79.1,96.9,125.5c65.2,50.2,299.5,144.7,227.3,215c-76.1,74.1-299.4-221.4-410.4-306.1c-111-84.7-147.5,28.1-40,106.6s284.1,198.3,284.1,198.3" />
                        </svg>
                        <img src="{{ $homeServiceSectionSetting?->watch_image_1_url ?? asset('frontend/assets/images/servWatch_1.png') }}"
                            alt="" class="servWatch_1 servWatch animateThis">
                        <img src="{{ $homeServiceSectionSetting?->watch_image_2_url ?? asset('frontend/assets/images/servWatch_2.png') }}"
                            alt="" class="servWatch_2 servWatch animateThis">
                    </div>
                </div>
            </div>
            <div class="servList row row-cols-lg-4 row-cols-sm-2 row-cols-2 g-0 text-white text-center">
                @forelse(($homeServiceItems ?? collect()) as $index => $serviceItem)
                    <div class="col animateThis fadeIn"
                        @if ($index > 0) style="transition-delay:{{ 0.3 + $index * 0.1 }}s;" @endif>
                        <div class="servItem d-flex flex-column">
                            <div class="servImgBox">
                                <img src="{{ $serviceItem->image_url }}" alt="{{ $serviceItem->title }}" class="servImg"
                                    onerror="this.onerror=null;this.src='{{ $serviceItem->default_image_url }}';">
                                <div class="servHead">{{ $serviceItem->title }}</div>
                            </div>
                            <div class="servText flex-grow-1">
                                {{ $serviceItem->description }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-4 text-center text-white-50">Service items coming soon.</div>
                @endforelse
            </div>
        </section>

        {{-- Our Brands --}}
        <section class="py-5 overflow-hidden">
            <h2 class="sectTitle text-center mb-4">
                Our Brands
            </h2>
            @php
                $half = ceil($brands->count() / 2);
            @endphp

            @if ($brands->isNotEmpty())
                <div class="container">
                    <div class="row g-2 justify-content-center align-items-center">
                        @foreach ($brands as $brand)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div
                                    class="brand-tile bg-white border rounded-2 d-flex align-items-center justify-content-center py-3 h-100">
                                    <a href="{{ route('brand.products', ['slug' => $brand->slug]) }}"
                                        title="View {{ $brand->name }} products"
                                        class="w-100 d-flex align-items-center justify-content-center">
                                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}"
                                            class="img-fluid brand-logo">
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
        {{-- <section class="py-4 overflow-hidden bgGray">
            <div class="container py-4 animateThis slideTop">
                <h2 class="sectTitle text-center mb-3">Watch lovers, loves us too!</h2>
                <div class="text-center sectSubTxt mb-5">Read their sweet stories about our timepieces and heartwarming
                    service.</div>

                @if (isset($homeTestimonials) && $homeTestimonials->isNotEmpty())
                    <div class="swiper pb-sm-4 pb-3 testimSwiper" data-slides="{{ $homeTestimonials->count() }}">
                        <div class="swiper-wrapper">
                            @foreach ($homeTestimonials as $testimonial)
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
                                    $avatarUrl =
                                        optional($testimonial->user)->avatar_url ??
                                        asset(
                                            'backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg',
                                        );
                                @endphp
                                <div class="swiper-slide">
                                    <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4 h-100">
                                        <div class="ti_rating">
                                            <div class="ratingBox">
                                                <div class="ratedStars {{ $testimonialRatingClass }}"></div>
                                            </div>
                                        </div>
                                        <div class="ti_review">
                                            <p class="mb-0">{{ $testimonial->review }}</p>
                                        </div>
                                        <div class="ti_user pt-4 d-flex align-items-center gap-3 mt-auto">
                                            <div class="ti_userImg rounded-circle overflow-hidden flex-shrink-0">
                                                <img src="{{ $avatarUrl }}"
                                                    alt="{{ $testimonial->user->name ?? 'Customer' }}" class="w-100 h-100">
                                            </div>
                                            <div class="ti_userInfo">
                                                <strong
                                                    class="d-block fw-bold">{{ $testimonial->user->name ?? 'Customer' }}</strong>
                                                <small>
                                                    @if ($testimonial->product)
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
                    <div class="text-center text-secondary py-4">
                        Reviews Coming Soon
                    </div>
                @endif
            </div>
        </section> --}}

    </main>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const relatedEl = document.querySelector('.relatedProductSwiper');

            if (relatedEl) {

                const relatedSlideCount =
                    relatedEl.querySelectorAll('.swiper-slide').length;

                new Swiper(".relatedProductSwiper", {

                    slidesPerView: 1.5,
                    spaceBetween: 0,
                    speed: 1000,

                    loop: relatedSlideCount > 3,

                    grabCursor: true,
                    centeredSlides: true,

                    autoplay: relatedSlideCount > 1 ? {
                        delay: 5000,
                        disableOnInteraction: false
                    } : false,

                    pagination: {
                        el: ".relatedProductSwiper .swiper-pagination",
                        clickable: true
                    },

                    breakpoints: {

                        576: {
                            slidesPerView: Math.min(2, relatedSlideCount),
                            slidesPerGroup: Math.min(2, relatedSlideCount),
                            centeredSlides: false
                        },

                        992: {
                            slidesPerView: Math.min(3, relatedSlideCount),
                            slidesPerGroup: Math.min(3, relatedSlideCount),
                            centeredSlides: false
                        }

                    }

                });
            }

        });
    </script>
@endpush