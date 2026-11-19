@extends('layouts.front')

@section('title', 'Explore Collections & Services')

@section('content')

    <main class="pageWrapper explore-page">

        {{-- HERO SECTION --}}
        <section class="inBannerSecton position-relative overflow-hidden w-100 d-flex">
            <img src="{{ asset('frontend/assets/images/prodListBanner.jpg') }}" class="inBanImg"
                alt="{{ $listingTitle ?? 'Explore Our Product & Services' }}">
            <div class="container position-relative z-1 d-flex flex-column justify-content-center align-items-start">
                <nav class="breadcrumb mb-2 text-white animateThis slideTop">
                    <a href="{{ route('home') }}" class="breadcrumb-item text-white-50 text-decoration-none">Home</a>
                    <span class="breadcrumb-item active text-white">Explore</span>
                </nav>

                <h1 class="sectTitle text-white animateThis slideTop mb-0">
                    {{ $listingTitle ?? 'Explore Our Product & Services' }}</h1>
            </div>
        </section>


        {{-- SECTION 1: CURATED COLLECTIONS (EXPLORE SUBCATEGORIES) --}}
        @if ($exploreSubcategories->isNotEmpty())
            <section class="py-5 bg-white" id="featured-collections">
                <div class="container py-3">
                    <div class="explore-section-header">
                        <div class="explore-section-eyebrow">Distinctive Styles</div>
                        <h2 class="explore-section-title">Curated Collections</h2>
                        <p class="explore-section-sub">
                            Explore masterfully styled collections tailored for every aesthetic, movement, and occasion.
                        </p>
                    </div>

                    <div class="row g-4">
                        @foreach ($exploreSubcategories as $subcat)
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('product.listing', ['subcategory' => $subcat->id]) }}"
                                    class="explore-collection-card">
                                    <img src="{{ $subcat->image_url }}" alt="{{ $subcat->name }}" loading="lazy">
                                    <div class="overlay">
                                        @if ($subcat->category)
                                            <div class="explore-collection-tag">{{ $subcat->category->name }}</div>
                                        @endif
                                        <h3 class="explore-collection-name">{{ $subcat->name }}</h3>
                                        <div class="explore-collection-cta">
                                            <span>Explore Collection</span>
                                            <span>&rarr;</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif


        {{-- SECTION 2: BROWSE BY CATEGORY --}}
        {{-- @if ($categories->isNotEmpty())
            <section class="py-5" style="background-color: #fbf9f6;" id="categories">
                <div class="container py-3">
                    <div class="explore-section-header">
                        <div class="explore-section-eyebrow">Shop By Category</div>
                        <h2 class="explore-section-title">Designed For Every Collector</h2>
                        <p class="explore-section-sub">
                            Discover timepieces sculpted for women, men, and universal elegance.
                        </p>
                    </div>

                    <div class="row g-4 justify-content-center">
                        @foreach ($categories as $category)
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('product.listing', ['category' => $category->id]) }}"
                                    class="explore-cat-card">
                                    <div class="explore-cat-imgbox">
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" loading="lazy">
                                    </div>
                                    <h3 class="explore-cat-title">{{ $category->name }}</h3>
                                    <div class="explore-cat-count">
                                        {{ $category->products_count ?? 0 }}
                                        {{ Str::plural('Watch', $category->products_count ?? 0) }} Available
                                    </div>
                                    <span class="explore-cat-btn">
                                        View Watches &rarr;
                                    </span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif --}}


        {{-- SECTION 3: FEATURED TIMEPIECES --}}
        {{-- @if ($featuredProducts->isNotEmpty())
            <section class="py-5 bg-white" id="featured-watches">
                <div class="container py-3">
                    <div class="explore-section-header">
                        <div class="explore-section-eyebrow">Handpicked Highlights</div>
                        <h2 class="explore-section-title">Featured Timepieces</h2>
                        <p class="explore-section-sub">
                            A selection of the most coveted models, trending icons, and bestsellers.
                        </p>
                    </div>

                    <div class="row g-4">
                        @foreach ($featuredProducts as $product)
                            <div class="col-xl-3 col-lg-4 col-sm-6">
                                @include('frontend.partials.home-product-slide', [
                                    'product' => $product,
                                    'wrapperClass' => 'h-100',
                                    'itemClass' =>
                                        'productItem text-center h-100 p-3 bg-white border rounded-3 d-flex flex-column justify-content-between position-relative',
                                ])
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('product.listing') }}" class="ctaBtn">
                            View Complete Watch Catalog
                        </a>
                    </div>
                </div>
            </section>
        @endif --}}


        {{-- SECTION 4: HOROLOGICAL CARE & SERVICES --}}
        {{-- @if ($services->isNotEmpty())
            <section class="py-5" style="background-color: #1a1918; color: #fff;" id="expert-services">
                <div class="container py-4">
                    <div class="explore-section-header">
                        <div class="explore-section-eyebrow text-warning">Authorized Horological Care</div>
                        <h2 class="explore-section-title text-white">Expert Precision Services</h2>
                        <p class="explore-section-sub text-white-50">
                            Comprehensive maintenance, restoration, battery testing, and genuine strap fittings by certified
                            horologists.
                        </p>
                    </div>

                    <div class="row g-4">
                        @foreach ($services as $service)
                            <div class="col-lg-4 col-md-6">
                                <div class="explore-service-card bg-dark border-secondary border-opacity-25 text-white">
                                    <div class="explore-service-imgwrap">
                                        @if ($service->images && $service->images->count())
                                            <img src="{{ asset('backend/assets/images/services/' . $service->images->first()->image) }}"
                                                alt="{{ $service->name }}" loading="lazy">
                                        @else
                                            <img src="{{ asset('frontend/assets/images/services.jpg') }}"
                                                alt="{{ $service->name }}" loading="lazy">
                                        @endif
                                    </div>
                                    <div class="explore-service-body bg-dark text-white">
                                        <h3 class="explore-service-name text-white">{{ $service->name }}</h3>
                                        <div class="explore-service-desc text-white-50">
                                            {!! Str::limit(strip_tags($service->description ?? ''), 130) !!}
                                        </div>
                                        <div>
                                            <a href="{{ $service->url ?: route('our.services') }}"
                                                class="btn btn-outline-light btn-sm rounded-pill px-3">
                                                Learn More
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('our.services') }}" class="ctaBtn ghost">
                            Explore All Services &rarr;
                        </a>
                    </div>
                </div>
            </section>
        @endif --}}


        {{-- SECTION 5: AUTHORIZED BRANDS --}}
        @if ($brands->isNotEmpty())
            <section class="py-5 bg-white" id="authorized-brands">
                <div class="container py-3">
                    <div class="explore-section-header">
                        <div class="explore-section-eyebrow">Official Partners</div>
                        <h2 class="explore-section-title">World Renowned Brands</h2>
                        <p class="explore-section-sub">
                            Proud authorized retailer for iconic horological houses and trendsetting brands.
                        </p>
                    </div>

                    <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 justify-content-center">
                        @foreach ($brands as $brand)
                            <div class="col">
                                <a href="{{ route('brand.products', ['slug' => $brand->slug]) }}"
                                    class="explore-brand-card" title="{{ $brand->name }}">
                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif


        {{-- SECTION 6: WHY EXPLORE WITH US --}}
        {{-- <section class="py-5" style="background-color: #faf7f2;">
            <div class="container py-3">
                <div class="row g-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="explore-pillar-card">
                            <div class="explore-pillar-icon">✓</div>
                            <h4 class="explore-pillar-title">100% Genuine</h4>
                            <p class="explore-pillar-desc">Direct from authorized brands with manufacturer certificates.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="explore-pillar-icon">🛡</div>
                        <h4 class="explore-pillar-title">Official Warranty</h4>
                        <p class="explore-pillar-desc">Full international and brand-backed warranty coverage.</p>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="explore-pillar-icon">🚚</div>
                        <h4 class="explore-pillar-title">Insured Delivery</h4>
                        <p class="explore-pillar-desc">Free, expedited, and fully insured door-to-door courier service.</p>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="explore-pillar-icon">⚙</div>
                        <h4 class="explore-pillar-title">Expert Service</h4>
                        <p class="explore-pillar-desc">In-house certified watchmakers ready for servicing & care.</p>
                    </div>
                </div>
            </div>
        </section> --}}


        {{-- SECTION 7: PROMOTIONAL CALLOUT BANNER --}}
        {{-- <section class="py-5 bg-white">
            <div class="container">
                <div class="explore-sale-banner">
                    <div class="row align-items-center position-relative z-1">
                        <div class="col-lg-8 mb-4 mb-lg-0">
                            <span class="text-uppercase fw-bold text-warning small mb-2 d-block"
                                style="letter-spacing: 2px;">
                                Special Opportunities
                            </span>
                            <h2 class="display-6 fw-bold mb-2">Looking For Exclusive Offers & Sales?</h2>
                            <p class="text-white-50 mb-0 fs-6">
                                Browse limited-time horological deals, festive discounts, and exclusive offers on selected
                                watch models.
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                <a href="{{ route('product.listing', ['on_sale' => 1]) }}" class="ctaBtn">
                                    Shop Sale Watches
                                </a>
                                <a href="{{ route('special.offers') }}"
                                    class="btn btn-outline-light rounded-pill px-4 py-2">
                                    Special Offers
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> --}}

    </main>

@endsection