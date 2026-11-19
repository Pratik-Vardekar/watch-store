@extends('layouts.front')
@section('content')
    <main class="pageWrapper">

        {{-- Slider --}}
        <section class="homeBanner swiper overflow-hidden mb-4">
            <div class="swiper-wrapper">

                @forelse($sliders as $slider)
                    <div class="swiper-slide">
                        @if ($slider->link)
                            <a href="{{ $slider->link }}"
                                class="d-block w-100 h-100 position-relative text-decoration-none text-white">
                        @endif

                        {{-- Desktop Image --}}
                        <img src="{{ $slider->image_url }}" alt="{{ $slider->title ?? 'Slider' }}"
                            class="hbImg w-100 d-none d-md-block"
                            loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                            @if ($loop->first) fetchpriority="high" @endif
                            data-swiper-parallax="20%">

                        {{-- Mobile Image --}}
                        <img src="{{ $slider->effective_mobile_image_url }}" alt="{{ $slider->title ?? 'Slider' }}"
                            class="hbImg w-100 d-block d-md-none"
                            loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                            @if ($loop->first) fetchpriority="high" @endif
                            data-swiper-parallax="20%">

                        @if ($slider->title || $slider->subtitle || $slider->description)
                            <div class="heroContent text-white">
                                <div class="container h-100 d-flex" data-swiper-parallax-x="100%"
                                    data-swiper-parallax-opacity="0">

                                    <div
                                        class="col-xl-6 col-md-7 pe-xxl-5 align-self-md-center align-self-end d-flex flex-column gap-md-4 gap-3">

                                        <div class="hbHead titleFont">
                                            @if ($slider->title)
                                                <span class="txtGold d-block">
                                                    {{ $slider->title }}
                                                </span>
                                            @endif

                                            @if ($slider->subtitle)
                                                <strong>{{ $slider->subtitle }}</strong>
                                            @endif
                                        </div>

                                        @if ($slider->description)
                                            <div class="hbPara">
                                                {{ $slider->description }}
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($slider->link)
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="swiper-slide">
                        {{-- Desktop Image --}}
                        <img src="{{ asset('frontend/assets/images/heroBanner_1.jpg') }}" class="hbImg w-100 d-none d-md-block"
                            alt="No Slider">

                        {{-- Mobile Image --}}
                        <img src="{{ asset('frontend/assets/images/heroBanner_mob1.jpg') }}" class="hbImg w-100 d-block d-md-none"
                            alt="No Slider">

                        <div class="heroContent text-white">
                            <div class="container h-100 d-flex">
                                <div class="col-xl-6 col-md-7 align-self-center">
                                    <div class="hbHead titleFont">
                                        <span class="txtGold d-block">
                                            Welcome
                                        </span>
                                        <strong>No Slider Available</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse

            </div>

            <div class="hbPagination swiper-pagination text-end text-md-start container mx-auto"></div>
        </section>

        {{-- Our Brands --}}
        <section class="py-md-5 py-4 overflow-hidden">
            <div class="container d-flex justify-content-between align-items-center mb-4">
                <div class="d-none d-sm-block" style="width: 80px;"></div>
                <h2 class="sectTitle text-center mb-0 flex-grow-1">Our Brands</h2>
                {{-- <a href="{{ route('brands') }}" class="text-decoration-none small fw-semibold text-end" style="color: #bf9a7c; min-width: 80px;">
                    View All <i class="ti ti-arrow-right"></i>
                </a> --}}
            </div>
            @if ($brands->isNotEmpty())
                @if ($brands->count() < 16)
                    {{-- Single Line Marquee (When fewer than 16 brands exist) --}}
                    @php
                        $repeat = max(2, (int) ceil(24 / max(1, $brands->count())));
                    @endphp
                    <div class="swiper marqueStrip overflow-visible">
                        <div class="swiper-wrapper">
                            @for ($i = 0; $i < $repeat; $i++)
                                @foreach ($brands as $brand)
                                    <div class="swiper-slide">
                                        <a href="{{ route('brand.products', ['slug' => $brand->slug]) }}"
                                            title="View {{ $brand->name }} products">
                                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}">
                                        </a>
                                    </div>
                                @endforeach
                            @endfor
                        </div>
                    </div>
                @else
                    {{-- Two Lines Marquee (When 16 or more brands are completed) --}}
                    @php
                        $half = ceil($brands->count() / 2);
                        $firstHalf = $brands->slice(0, $half);
                        $secondHalf = $brands->slice($half);
                        $repeatFirst = max(2, (int) ceil(24 / max(1, $firstHalf->count())));
                        $repeatSecond = max(2, (int) ceil(24 / max(1, $secondHalf->count())));
                    @endphp

                    <!-- First Row -->
                    <div class="swiper marqueStrip overflow-visible">
                        <div class="swiper-wrapper">
                            @for ($i = 0; $i < $repeatFirst; $i++)
                                @foreach ($firstHalf as $brand)
                                    <div class="swiper-slide">
                                        <a href="{{ route('brand.products', ['slug' => $brand->slug]) }}"
                                            title="View {{ $brand->name }} products">
                                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}">
                                        </a>
                                    </div>
                                @endforeach
                            @endfor
                        </div>
                    </div>

                    <!-- Second Row -->
                    <div class="swiper marqueStrip overflow-visible" dir="rtl">
                        <div class="swiper-wrapper">
                            @for ($i = 0; $i < $repeatSecond; $i++)
                                @foreach ($secondHalf as $brand)
                                    <div class="swiper-slide">
                                        <a href="{{ route('brand.products', ['slug' => $brand->slug]) }}"
                                            title="View {{ $brand->name }} products">
                                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}">
                                        </a>
                                    </div>
                                @endforeach
                            @endfor
                        </div>
                    </div>
                @endif
            @else
                <!-- Fallback if no brands exist -->
                <div class="text-center my-5">
                    <p>No brands available at the moment.</p>
                </div>
            @endif
        </section>

        <section class="py-md-5 py-4 mb-sm-5 mb-4 overflow-hidden">
            <div class="container">

                <div class="row g-lg-5 g-4">
                    @forelse($watchBanners as $index => $banner)
                        @php
                            $animation = $index % 2 === 0 ? 'slideLeft' : 'slideRight';
                            $titleParts = preg_split('/\s+/', trim((string) $banner->name), 2);
                            $labelSmall = count($titleParts) > 1 ? $titleParts[0] : 'Watches';
                            $labelLarge = count($titleParts) > 1 ? $titleParts[1] : $banner->name;
                            // Keep classic "For Him / For Her" layout when name is only that phrase.
                            if (preg_match('/^for\s+(him|her)$/i', (string) $banner->name)) {
                                $labelSmall = 'Watches';
                                $labelLarge = $banner->name;
                            }
                        @endphp
                        <div class="col-md animateThis {{ $animation }}">
                            <div class="catgBox rounded-3 animateThis">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->name }}" class="catgImg">
                                <div
                                    class="p-xl-5 p-4 d-flex justify-content-between align-items-center text-white w-100 position-absolute bottom-0">
                                    <h2 class="catgHead mb-0">
                                        <small class="fw-light">{{ $labelSmall }}</small>
                                        <span class="titleFont fst-italic d-block">{{ $labelLarge }}</span>
                                    </h2>
                                    <a href="{{ $banner->listing_url }}" class="ctaBtn ghost mt-3">Shop Now</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-md animateThis slideLeft">
                            <div class="catgBox rounded-3 animateThis">
                                <img src="{{ asset('frontend/assets/images/catg_Mens.jpg') }}" alt="Watches For Him"
                                    class="catgImg">
                                <div
                                    class="p-xl-5 p-4 d-flex justify-content-between align-items-center text-white w-100 position-absolute bottom-0">
                                    <h2 class="catgHead mb-0">
                                        <small class="fw-light">Watches</small>
                                        <span class="titleFont fst-italic d-block">For Him</span>
                                    </h2>
                                    <a href="{{ route('product.listing', ['gender' => 'men']) }}"
                                        class="ctaBtn ghost mt-3">Shop Now</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md animateThis slideRight">
                            <div class="catgBox rounded-3 animateThis">
                                <img src="{{ asset('frontend/assets/images/catg_Womens.jpg') }}" alt="Watches For Her"
                                    class="catgImg">
                                <div
                                    class="p-xl-5 p-4 d-flex justify-content-between align-items-center text-white w-100 position-absolute bottom-0">
                                    <h2 class="catgHead mb-0">
                                        <small class="fw-light">Watches</small>
                                        <span class="titleFont fst-italic d-block">For Her</span>
                                    </h2>
                                    <a href="{{ route('product.listing', ['gender' => 'women']) }}"
                                        class="ctaBtn ghost mt-3">Shop Now</a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </section>

        {{-- <section class="bg-black">
            <div class="servBanWrap overflow-hidden py-sm-4">
                <img src="{{ asset('frontend/assets/images/textureBg.jpg') }}" alt="" class="servBg">

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
                        <img src="{{ asset('frontend/assets/images/servWatch_1.png') }}" alt=""
                            class="servWatch_1 servWatch animateThis">
                        <img src="{{ asset('frontend/assets/images/servWatch_2.png') }}" alt=""
                            class="servWatch_2 servWatch animateThis">
                    </div>
                </div>
            </div>
            <div class="servList row row-cols-lg-4 row-cols-sm-2 row-cols-2 g-0 text-white text-center">
                @forelse(($homeServiceItems ?? collect()) as $index => $serviceItem)
                    <div class="col animateThis fadeIn"
                        @if ($index > 0) style="transition-delay:{{ 0.3 + $index * 0.1 }}s;" @endif>
                        <div class="servItem d-flex flex-column">
                            <div class="servImgBox">
                                <img src="{{ $serviceItem->image_url }}" alt="{{ $serviceItem->title }}"
                                    class="servImg"
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
        </section> --}}

        <section class="py-md-5 py-4 overflow-hidden bgGray">
            <div class="container py-4">
                <h2 class="sectTitle text-center mb-4">Our Bestsellers</h2>

                <div class="swiper productSlider pb-sm-5 pb-3 mb-4">
                    <div class="swiper-wrapper">
                        @forelse($bestSellingProducts as $product)
                            @include('frontend.partials.home-product-slide', ['product' => $product])
                        @empty
                            <div class="swiper-slide">
                                <div class="productItem text-center py-5">
                                    <p class="mb-0 text-muted">No bestsellers available yet.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

                <div class="text-center">
                    <a href="{{ route('product.listing', ['filter' => 'best_selling']) }}" class="ctaBtn">View All</a>
                </div>
            </div>
        </section>

        {{-- More Than Just Watches --}}
        <section class="py-md-5 py-4">
            <div class="container py-4">
                <h2 class="sectTitle text-center mb-2">
                    More Than Just Watches
                </h2>

                <div class="text-center sectSubTxt">
                    Explore our extended range of products and services.
                </div>
            </div>

            @php
                $homepageCards = ($homeHighlights ?? collect())->where('status', true)->sortBy('sort_order')->values();
            @endphp

            @if ($homepageCards->isNotEmpty())
                <div class="osWrap text-white text-center row row-cols-lg-4 row-cols-sm-2 row-cols-2 g-0 pb-4">

                    @foreach ($homepageCards as $card)
                        <div class="col animateThis fadeGrow">
                            <div class="osBox d-flex flex-column justify-content-center">

                                <img src="{{ $card->image_url }}" alt="{{ $card->title }}" class="osImg">

                                <div class="col-auto osHead d-flex align-items-center justify-content-center">
                                    {{ $card->title }}
                                </div>

                            </div>
                        </div>
                    @endforeach

                </div>
            @else
                <div class="osWrap text-white text-center row row-cols-lg-4 row-cols-sm-2 row-cols-2 g-0 pb-4">
                    @foreach (\App\Models\HomeHighlight::DEFAULT_CARDS as $card)
                        <div class="col animateThis fadeGrow">
                            <div class="osBox d-flex flex-column justify-content-center">
                                <img src="{{ asset($card['default_image']) }}" alt="{{ $card['title'] }}"
                                    class="osImg">
                                <div class="col-auto osHead d-flex align-items-center justify-content-center">
                                    {{ $card['title'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="py-md-5 py-4 overflow-hidden bgGray">
            <div class="container py-sm-4">
                <h2 class="sectTitle text-center mb-4">New Arrivals</h2>

                <div class="swiper productSlider pb-sm-5 pb-3 mb-4">
                    <div class="swiper-wrapper">
                        @forelse($newArrivalsProducts as $product)
                            @include('frontend.partials.home-product-slide', [
                                'product' => $product,
                                'showNewBadge' => true,
                            ])
                        @empty
                            <div class="swiper-slide">
                                <div class="productItem text-center py-5">
                                    <p class="mb-0 text-muted">No new arrivals available yet.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

                <div class="text-center">
                    <a href="{{ route('product.listing', ['filter' => 'new_arrival']) }}" class="ctaBtn">View All</a>
                </div>
            </div>
        </section>

        {{-- @if ($specialOffer)
    <section class="offerSection overflow-hidden">
    
        <div class="parallax"
            style="
                background-image:url('{{ $specialOffer->image_url ?? asset('frontend/assets/images/offerImg.jpg') }}');
                {{ $specialOffer->bg_color ? 'background-color:'.$specialOffer->bg_color.';' : '' }}
            ">
        </div>
    
        <div class="container">
    
            <div class="row offerContent position-relative z-3">
    
                <div class="col-xl-4 col-lg-6 col-sm-6 col-9 animateThis slideRight">
    
                    @if ($specialOffer->badge_text)
                        <div class="txtGold sectSubTxt fw-medium mb-3">
                            {{ $specialOffer->badge_text }}
                        </div>
                    @endif
    
                    <h2 class="sectTitle titleFont mb-4"
                        style="color:{{ $specialOffer->text_color ?? '#fff' }}">
                        {{ $specialOffer->title }}
                    </h2>

                    @if ($specialOffer->subtitle)
                        <p class="mb-3 fw-medium"
                           style="color:{{ $specialOffer->text_color ?? '#fff' }};">
                            {{ $specialOffer->subtitle }}
                        </p>
                    @endif
    
                    @if ($specialOffer->description)
                        <p class="offerTxt lh-lg mb-4"
                           style="color:{{ $specialOffer->text_color ?? '#fff' }};">
                            {{ $specialOffer->description }}
                        </p>
                    @endif
    
                    @if ($specialOffer->button_link)
                        <a href="{{ $specialOffer->button_link }}"
                           class="ctaBtn">
                            {{ $specialOffer->button_text }}
                        </a>
                    @endif
    
                </div>
    
            </div>
    
            @if (!empty($specialOffer->features))
                <div class="row justify-content-between g-md-0 gx-3 gy-4 py-xl-5 py-md-4 pb-4 mt-0 border-top border-white text-white position-relative z-2"
                    style="--bs-border-opacity:.5;">
    
                    @foreach ($specialOffer->features as $feature)
    
                        <div class="col-md-auto col-6 py-1">
    
                            <div class="uspItem d-flex flex-lg-row flex-md-column flex-sm-row flex-column gap-3 align-items-center">
    
                                <div class="col-auto">
    
                                    @if (!empty($feature['icon']))
                                        {!! $feature['icon'] !!}
                                    @endif
    
                                </div>
    
                                <div class="col text-lg-start text-md-center text-sm-start text-center">
    
                                    <strong class="d-block fw-bold text-uppercase lh-lg">
                                        {{ $feature['title'] ?? '' }}
                                    </strong>
    
                                    {{ $feature['description'] ?? $feature['text'] ?? '' }}
    
                                </div>
    
                            </div>
    
                        </div>
    
                        @if (!$loop->last)
                            <div class="col-auto vr border-white d-none d-md-block"></div>
                        @endif
    
                    @endforeach
    
                </div>
            @endif
    
        </div>
    
    </section>
    @endif --}}
        @if ($specialOffer)
            <section class="offerSection overflow-hidden">

                <div class="parallax"
                    style="
                background-image:url('{{ $specialOffer->image_url ?? asset('frontend/assets/images/offerImg.jpg') }}');
                {{ $specialOffer->bg_color ? 'background-color:' . $specialOffer->bg_color . ';' : '' }}
            ">
                </div>

                <div class="container">

                    <div class="row offerContent position-relative z-3">

                        <div class="col-xl-4 col-lg-6 col-sm-6 col-9 animateThis slideRight">

                            {{-- Display Main Logo --}}
                            {{-- @if ($specialOffer->logo_url)
                        <div class="mb-3">
                            <img
                                src="{{ $specialOffer->logo_url }}"
                                alt="Offer Logo"
                                style="max-height:80px; width:auto; object-fit:contain;"
                            >
                        </div>
                    @endif --}}

                            @if ($specialOffer->badge_text)
                                <div class="txtGold sectSubTxt fw-medium mb-3">
                                    {{ $specialOffer->badge_text }}
                                </div>
                            @endif

                            <h2 class="sectTitle titleFont mb-4" style="color:{{ $specialOffer->text_color ?? '#fff' }}">
                                {{ $specialOffer->title }}
                            </h2>

                            @if ($specialOffer->subtitle)
                                <p class="mb-3 fw-medium" style="color:{{ $specialOffer->text_color ?? '#fff' }};">
                                    {{ $specialOffer->subtitle }}
                                </p>
                            @endif

                            @if ($specialOffer->description)
                                <p class="offerTxt lh-lg mb-4" style="color:{{ $specialOffer->text_color ?? '#fff' }};">
                                    {{ $specialOffer->description }}
                                </p>
                            @endif

                            @if ($specialOffer->button_link)
                                <a href="{{ $specialOffer->button_link }}" class="ctaBtn">
                                    {{ $specialOffer->button_text }}
                                </a>
                            @endif

                        </div>

                    </div>

                    @if (!empty($specialOffer->features))
                        <div class="row justify-content-between g-md-0 gx-3 gy-4 py-xl-5 py-md-4 pb-4 mt-0 border-top border-white text-white position-relative z-2"
                            style="--bs-border-opacity:.5;">

                            @foreach ($specialOffer->features as $index => $feature)
                                <div class="col-md-auto col-6 py-1">

                                    <div
                                        class="uspItem d-flex flex-lg-row flex-md-column flex-sm-row flex-column gap-1 align-items-center">

                                        {{-- Feature Icon with Circle Background --}}
                                        <div class="col-auto">
                                            @php
                                                $icon = $specialOffer->feature_icons[$index] ?? null;
                                            @endphp
                                            <div class="feature-icon-wrapper"
                                                style="
                                        width: 60px;
                                        height: 50px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        transition: all 0.3s ease;
                                        backdrop-filter: blur(2px);
                                        flex-shrink: 0;
                                    ">
                                                @if (!empty($icon))
                                                    @if (filter_var($icon, FILTER_VALIDATE_URL) || str_starts_with($icon, 'uploads/'))
                                                        <img src="{{ asset($icon) }}"
                                                            alt="{{ $feature['title'] ?? 'Feature' }}"
                                                            style="width: 32px; height: 32px; object-fit:contain; filter: brightness(0) invert(1);"
                                                            onerror="this.style.display='none'">
                                                    @elseif(str_starts_with($icon, '<'))
                                                        <div
                                                            style="color: #fff; width: 39px; height: 32px; display: flex; align-items: center; justify-content: center; line-height: 30px;">
                                                            {!! $icon !!}
                                                        </div>
                                                    @else
                                                        <span class="text-white-50"
                                                            style="font-size: 12px;">{{ $icon }}</span>
                                                    @endif
                                                @else
                                                    {{-- Default icons based on feature index --}}
                                                    @switch($index)
                                                        @case(0)
                                                            {{-- Delivery/Truck Icon --}}
                                                            <svg width="32" height="32" viewBox="0 0 24 24"
                                                                fill="none" stroke="#fff" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <rect x="1" y="3" width="15" height="13" />
                                                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                                                                <circle cx="5.5" cy="18" r="2.5" />
                                                                <circle cx="18.5" cy="18" r="2.5" />
                                                            </svg>
                                                        @break

                                                        @case(1)
                                                            {{-- Return/Exchange Icon --}}
                                                            <svg width="32" height="32" viewBox="0 0 24 24"
                                                                fill="none" stroke="#fff" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M3 12a9 9 0 1 0 9-9m0 0v6m0-6h-6" />
                                                                <path d="M21 12a9 9 0 1 1-9 9m0 0v-6m0 6h6" />
                                                            </svg>
                                                        @break

                                                        @case(2)
                                                            {{-- Secure/Shield Icon --}}
                                                            <svg width="32" height="32" viewBox="0 0 24 24"
                                                                fill="none" stroke="#fff" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                                                <path d="M9 12l2 2 4-4" />
                                                            </svg>
                                                        @break

                                                        @case(3)
                                                            {{-- Watch/Collection Icon --}}
                                                            <svg width="32" height="32" viewBox="0 0 24 24"
                                                                fill="none" stroke="#fff" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="7" />
                                                                <polyline points="12 9 12 12 13.5 13.5" />
                                                                <path
                                                                    d="M16.51 17.35l-.35 3.83a2 2 0 0 1-2 1.82H9.83a2 2 0 0 1-2-1.82l-.35-3.83m.01-10.7l.35-3.83A2 2 0 0 1 9.83 1h4.35a2 2 0 0 1 2 1.82l.35 3.83" />
                                                            </svg>
                                                        @break

                                                        @default
                                                            {{-- Default Star Icon --}}
                                                            <svg width="32" height="32" viewBox="0 0 24 24"
                                                                fill="none" stroke="#fff" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <polygon
                                                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                            </svg>
                                                    @endswitch
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Feature Content --}}
                                        <div class="col text-lg-start text-md-center text-sm-start text-center">

                                            <strong class="d-block fw-bold text-uppercase"
                                                style="font-size: 0.85rem; letter-spacing: 0.5px; line-height: 1.4;">
                                                {{ $feature['title'] ?? '' }}
                                            </strong>

                                            <span style="font-size: 0.8rem; opacity: 0.85; line-height: 1.4;">
                                                {{ $feature['description'] ?? ($feature['text'] ?? '') }}
                                            </span>

                                        </div>

                                    </div>

                                </div>

                                @if (!$loop->last)
                                    <div class="col-auto vr border-white d-none d-md-block"
                                        style="opacity: 0.2; height: 60px;"></div>
                                @endif
                            @endforeach

                        </div>
                    @endif

                </div>

            </section>
        @endif
        {{-- <section class="py-md-5 py-4 overflow-hidden bgGray">
       <div class="container py-4 animateThis slideTop">
          <h2 class="sectTitle text-center mb-3">Watch lovers, loves us too!</h2>
          <div class="text-center sectSubTxt mb-5">Read their sweet stories about our timepieces and heartwarming service.</div>

          @if (($homeTestimonials ?? collect())->isNotEmpty())
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
          <div class="text-center text-secondary py-4">Customer reviews will appear here once approved from Admin → Reviews.</div>
          @endif
       </div>
    </section> --}}

        <section class="py-md-5 py-4 overflow-hidden bgGray">
            <div class="container py-4 animateThis slideTop">

                <h2 class="sectTitle text-center mb-3">
                    Watch lovers, loves us too!
                </h2>

                <div class="text-center sectSubTxt mb-5">
                    Read their sweet stories about our timepieces and heartwarming service.
                </div>

                @if (($homeTestimonials ?? collect())->isNotEmpty())
                    <div class="swiper pb-sm-4 pb-3 testimSwiper" data-slides="{{ $homeTestimonials->count() }}">

                        <div class="swiper-wrapper">
                            @foreach ($homeTestimonials as $testimonial)
                                @php
                                    $rating = (float) ($testimonial->rating ?? 0);

                                    $ratingClass = match (true) {
                                        $rating >= 4.75 => 'star5',
                                        $rating >= 4.25 => 'star4h',
                                        $rating >= 3.75 => 'star4',
                                        $rating >= 3.25 => 'star3h',
                                        $rating >= 2.75 => 'star3',
                                        $rating >= 2.25 => 'star2h',
                                        $rating >= 1.75 => 'star2',
                                        $rating >= 1.25 => 'star1h',
                                        $rating >= 0.75 => 'star1',
                                        default => 'star0',
                                    };

                                    $user = $testimonial->user;

                                    $avatar =
                                        optional($user)->avatar_url ??
                                        asset(
                                            'backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg',
                                        );

                                    $userName = optional($user)->name ?? 'Customer';
                                @endphp

                                <div class="swiper-slide">

                                    <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">

                                        {{-- Rating --}}
                                        <div class="ti_rating">
                                            <div class="ratingBox">
                                                <div class="ratedStars {{ $ratingClass }}"></div>
                                            </div>
                                        </div>

                                        {{-- Quote --}}
                                        <div class="ti_review">

                                            <p class="mb-0">
                                                {{ $testimonial->review }}
                                            </p>

                                        </div>

                                        {{-- Customer --}}
                                        <div class="ti_user pt-4 d-flex align-items-center gap-3">

                                            <div class="ti_userImg rounded-circle overflow-hidden">

                                                <img src="{{ $avatar }}" alt="{{ $userName }}"
                                                    class="w-100 mt-auto" style="object-fit: cover;">

                                            </div>

                                            <div class="ti_userInfo">

                                                <strong class="d-block fw-bold">
                                                    {{ $userName }}
                                                </strong>

                                                <small>
                                                    @if ($testimonial->product)
                                                        {{ \Illuminate\Support\Str::limit($testimonial->product->name, 35) }}
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

                        {{-- Pagination --}}
                        <div class="swiper-pagination"></div>

                    </div>
                @endif

            </div>
        </section>

    </main>
@endsection
