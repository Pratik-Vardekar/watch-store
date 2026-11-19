@extends('layouts.front')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css" />
<style>
    .productImgSlider .prodZoomWrap {
        position: absolute;
        inset: 0;
        overflow: hidden;
        border-radius: inherit;
        background: #fff;
        cursor: zoom-in;
        isolation: isolate;
    }

    .productImgSlider .prodZoomWrap .prodZoomImg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: block;
        transform-origin: center center;
        transform: scale(1);
        transition: transform .45s cubic-bezier(.22, 1, .36, 1);
        will-change: transform;
        user-select: none;
        -webkit-user-drag: none;
    }

    .prodImgSwiper .swiper-slide:not(.swiper-slide-active) .prodZoomWrap .prodZoomImg {
        transform: scale(.9);
    }

    .prodImgSwiper .swiper-slide-active .prodZoomWrap .prodZoomImg {
        transform: scale(1);
        transition-delay: .4s;
    }

    .productImgSlider .prodZoomWrap::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        border-radius: inherit;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.04);
        transition: box-shadow .35s ease, background .35s ease;
        z-index: 2;
    }

    .productImgSlider .prodZoomWrap.is-zooming::after {
        box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.06);
        background: radial-gradient(circle at var(--zoom-x, 50%) var(--zoom-y, 50%), transparent 18%, rgba(0, 0, 0, 0.045) 70%);
    }

    .productImgSlider .prodZoomWrap.is-zooming .prodZoomImg {
        transform: scale(2.15);
        transition: transform .08s linear;
        transition-delay: 0s;
    }

    .productImgSlider .prodZoomHint {
        position: absolute;
        left: 50%;
        bottom: 1rem;
        transform: translateX(-50%);
        z-index: 3;
        align-items: center;
        gap: .4rem;
        padding: .45rem .85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.92);
        color: #444;
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .02em;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        pointer-events: none;
        opacity: 1;
        transition: opacity .3s ease, transform .3s ease;
    }

    .productImgSlider .prodZoomWrap.is-zooming .prodZoomHint,
    .productImgSlider .prodZoomWrap:hover .prodZoomHint {
        opacity: 0;
        transform: translateX(-50%) translateY(.35rem);
    }

    .productImgSlider .prodZoomLens {
        position: absolute;
        width: 7.5rem;
        height: 7.5rem;
        border-radius: 50%;
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        box-shadow:
            0 0 0 1px rgba(0, 0, 0, 0.1),
            0 10px 28px rgba(0, 0, 0, 0.14),
            inset 0 0 0 1px rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(0.5px);
        pointer-events: none;
        opacity: 0;
        transform: translate(-50%, -50%) scale(.85);
        transition: opacity .2s ease, transform .2s ease;
        z-index: 4;
    }

    .productImgSlider .prodZoomWrap.is-zooming .prodZoomLens {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .productImgSlider .prodDetailFullView {
        position: absolute;
        top: .85rem;
        right: .85rem;
        z-index: 5;
        width: 2.25rem;
        height: 2.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.92);
        color: #333;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        transition: background .2s ease, color .2s ease, transform .2s ease;
    }

    .productImgSlider .prodDetailFullView:hover {
        background: var(--redColor, #d32f2f);
        color: #fff;
        transform: scale(1.05);
    }

    @media (max-width: 991.98px) {
        .productImgSlider .prodZoomWrap {
            cursor: default;
        }

        .productImgSlider .prodZoomWrap.is-zooming .prodZoomImg {
            transform: scale(1);
        }
    }

    .specBox {
        margin-top: 1.5rem;
        padding: 1.5rem 1.75rem;
        background: #fff;
        border: 1px solid #e7e7e7;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    }

    .specHead {
        margin: 0 0 1rem;
        padding-bottom: .45rem;
        border-bottom: 1px solid #2d2d2d;
        font-size: 22px;
        font-weight: 700;
        line-height: 1.2;
        color: #1d1d1d;
    }

    .specInfo,
    .specInfo p,
    .specInfo li,
    .specInfo td,
    .specInfo th,
    .specInfo span,
    .specInfo div {
        font-size: 14px;
        line-height: 1.45;
        color: #3d3d3d;
    }

    .specInfo table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .specInfo tr {
        border-bottom: 1px solid #dedede;
    }

    .specInfo tr:last-child {
        border-bottom: 0;
    }

    .specInfo td,
    .specInfo th {
        padding: .55rem .5rem;
        vertical-align: top;
        text-align: left;
        word-break: break-word;
    }

    .specInfo td:nth-child(odd),
    .specInfo th:nth-child(odd) {
        font-weight: 700;
        color: #1f1f1f;
        width: 22%;
    }

    .specInfo td:nth-child(even),
    .specInfo th:nth-child(even) {
        width: 28%;
    }

    @media (max-width: 767.98px) {
        .specBox {
            padding: 1.25rem;
        }

        .specHead {
            font-size: 19px;
        }

        .specInfo td,
        .specInfo th {
            display: block;
            width: 100% !important;
            padding-left: 0;
            padding-right: 0;
        }

        .specInfo td:nth-child(odd),
        .specInfo th:nth-child(odd) {
            padding-bottom: .15rem;
        }

        .specInfo td:nth-child(even),
        .specInfo th:nth-child(even) {
            padding-top: 0;
            padding-bottom: .75rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $wishlistIds = $wishlistIds ?? [];
    $related_products = $related_products ?? collect();
    $features = $features ?? collect();
    $sliderImages = $sliderImages ?? $product->getSliderImages();
    $isWishlisted = in_array((int) $product->id, $wishlistIds, true);
    $salePrice = (float) ($product->sale_price ?? $product->selling_price ?? $product->price ?? 0);
    $originalPrice = (float) ($product->original_price ?? $product->price ?? 0);
    $avgRating = (float) ($product->rating ?? $product->averageRating() ?? 0);
    $reviews = $reviews ?? collect();
    $reviewCount = (int) ($reviewCount ?? $product->reviews_count ?? 0);
    $reviewsCount = $reviewCount;
    $averageRatingValue = (float) ($averageRatingValue ?? $avgRating);
    $avgRating = $averageRatingValue > 0 ? $averageRatingValue : $avgRating;
    $ratingBreakdown = $ratingBreakdown ?? [5 => ['percent' => 0], 4 => ['percent' => 0], 3 => ['percent' => 0], 2 => ['percent' => 0], 1 => ['percent' => 0]];
    $userHasReviewed = (bool) ($userHasReviewed ?? false);
    $previewReviews = $reviews->take(3);
    $maxQty = min(10, max(1, (int) ($product->quantity ?? 1)));
    $ratingClass = match (true) {
        $avgRating >= 4.75 => 'star5',
        $avgRating >= 4.25 => 'star4h',
        $avgRating >= 3.75 => 'star4',
        $avgRating >= 3.25 => 'star3h',
        $avgRating >= 2.75 => 'star3',
        $avgRating >= 2.25 => 'star2h',
        $avgRating >= 1.75 => 'star2',
        $avgRating >= 1.25 => 'star1h',
        $avgRating >= 0.75 => 'star1',
        default => 'star0',
    };
    $discountPercent = ($originalPrice > $salePrice && $originalPrice > 0)
        ? (int) round((($originalPrice - $salePrice) / $originalPrice) * 100)
        : (int) ($product->discount_percent ?? $product->discount ?? 0);
@endphp

<main class="pageWrapper">

    <section class="bgGray py-3">
        <div class="container">
            <nav class="breadcrumb mb-0 animateThis slideTop">
                <a class="breadcrumb-item" href="{{ url('/') }}">Home</a>
                <a class="breadcrumb-item" href="{{ route('product.listing', array_filter(['category' => $product->category_id])) }}">{{ $product->category->name ?? 'Category' }}</a>
                <span class="breadcrumb-item active">{{ $product->brand->name ?? 'Brand' }}</span>
            </nav>
        </div>
    </section>

    <section class="bgGray mb-5">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-6 pe-lg-5">
                    <div class="row flex-column position-sticky mx-sm-auto me-xl-5 productImgSlider">
                        <div class="col mb-4 mb-lg-0 px-0">
                            <div class="swiper pb-4 pb-lg-0 prodImgSwiper">
                                <div class="swiper-wrapper">
                                    @forelse($sliderImages as $image)
                                        <div class="swiper-slide">
                                            <div class="picBox">
                                                <div class="prodZoomWrap productDetailZoomWrap">
                                                    <img src="{{ $product->getProductImageUrl($image) }}" class="prodZoomImg" alt="{{ $product->name }}" loading="lazy">
                                                    <span class="prodZoomHint d-none d-lg-flex">Hover to zoom</span>
                                                </div>
                                                <a href="{{ $product->getProductImageUrl($image) }}" data-fancybox="prodGallery" class="prodDetailFullView" aria-label="View full size image">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1zM10 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 16 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5M.5 10a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 14.5v-4a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5"/></svg>
                                                </a>
                                                <div class="swiper-lazy-preloader"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="swiper-slide">
                                            <div class="picBox">
                                                <div class="prodZoomWrap productDetailZoomWrap">
                                                    <img src="{{ $product->getProductImageUrl() }}" class="prodZoomImg" alt="{{ $product->name }}">
                                                    <span class="prodZoomHint d-none d-lg-flex">Hover to zoom</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                                <div class="swiper-button-next prodImgBtns prodImgNext"></div>
                                <div class="swiper-button-prev prodImgBtns prodImgPrev"></div>
                                <div class="swiper-pagination d-lg-none"></div>
                            </div>
                        </div>
                        @if(count($sliderImages) > 1)
                        <div class="col-auto d-none d-lg-block py-3">
                            <div class="swiper prodImgThumbSwiper">
                                <div class="swiper-wrapper">
                                    @foreach($sliderImages as $image)
                                        <div class="swiper-slide">
                                            <div class="pitBox"><img src="{{ $product->getProductImageUrl($image) }}" alt=""></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-5 col-lg-6 mb-5 productDetail">
                    <div class="row flex-column g-4">
                        <div class="col">
                            <div class="prodTitle d-flex flex-column gap-3">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="col-auto">
                                        <h1 class="sectTitle text-black text-uppercase mb-0">{{ $product->brand->name ?? 'Watch' }}</h1>
                                    </div>
                                    @if($product->is_new || $product->is_new_arrival)
                                        <div class="col-auto">
                                            <div class="pi_newBadge txtRed fw-medium">New Arrival</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="col">
                                        <h2 class="prod_Name">{{ $product->name }}</h2>
                                        <h3 class="prod_ModelNo mb-0">{{ $product->sku }}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="shareBtn rounded-circle" data-bs-toggle="modal" data-bs-target="#shareModal">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" class="shareIcon"><path d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5m-8.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m11 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col pi_rating mb-2 d-flex align-items-center gap-1">
                            <div class="ratingBox">
                                <div class="ratedStars {{ $ratingClass }}"></div>
                            </div>
                            @if($reviewsCount > 0)
                                <a href="#reviewSection" class="pi_reviewCount fw-semibold text-decoration-none text-dark">
                                    {{ number_format($avgRating, 1) }}
                                    ({{ $reviewsCount }} {{ $reviewsCount == 1 ? 'Review' : 'Reviews' }})
                                </a>
                            @else
                                <span class="pi_reviewCount fw-semibold text-secondary">
                                    No Reviews Yet
                                </span>
                            @endif
                        </div>
                   

                        <div class="col">
                            <div class="d-flex align-items-center pi_price gap-4">
                                <div class="col-auto">
                                    @if($originalPrice > $salePrice)
                                        <div class="pip_ProdStrike">₹ {{ number_format($originalPrice, 2) }}</div>
                                    @endif
                                    <div class="prod_Price fw-bolder">₹ {{ number_format($salePrice, 2) }}</div>
                                </div>
                                @if($discountPercent > 0)
                                <div class="col">
                                    <div class="offerTag">
                                        {{ $discountPercent }}%
                                        <small class="d-block">Off</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="col" id="productPurchaseBox">
                            <div class="d-flex flex-row flex-wrap gap-3 align-items-stretch">
                                <div class="col-auto align-self-center">
                                    <small class="d-block text-secondary mb-1">Quantity</small>
                                    <div class="quantitySpinner input-group border rounded d-inline-flex bg-white">
                                        <button class="btn border-0 minus" type="button" disabled aria-label="Decrease quantity">-</button>
                                        <input type="text" name="qty" value="1" min="1" max="{{ $maxQty }}" class="form-control border-0 text-center numData js-product-qty" readonly aria-label="Quantity">
                                        <button class="btn border-0 plus" type="button" {{ $maxQty <= 1 ? 'disabled' : '' }} aria-label="Increase quantity">+</button>
                                    </div>
                                </div>
                                <div class="col">
                                    <form action="{{ route('checkout.buy', $product->id) }}" method="GET" class="h-100 d-flex align-items-end">
                                        <input type="hidden" name="qty" value="1" class="js-buy-qty">
                                        <button type="submit" class="ctaBtn w-100">Buy Now</button>
                                    </form>
                                </div>
                                <div class="col">
                                    <div class="h-100 d-flex align-items-end">
                                        <button type="button"
                                            class="ctaBtn dark w-100 js-add-to-cart"
                                            data-product-id="{{ $product->id }}"
                                            data-add-url="{{ route('cart.add', $product->id) }}">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 text-center hstack gap-3">
                                    <hr class="w-100">
                                    <button type="button"
                                        class="wishlistBtn prodDetWish flex-shrink-0 js-wishlist-toggle {{ $isWishlisted ? 'saved' : '' }}"
                                        data-product-id="{{ $product->id }}"
                                        data-toggle-url="{{ route('wishlist.toggle', $product->id) }}"
                                        data-label-add="Add to Wishlist"
                                        data-label-remove="Remove from Wishlist">
                                        {{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                                    </button>
                                    <hr class="w-100">
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="deliveryCheck d-flex flex-wrap rounded shadow-sm p-3 my-1 gap-3 bg-white">
                                <div class="col-12 lh-base text-secondary">
                                    <strong class="d-block fw-bold text-uppercase text-dark">Check Delivery</strong>
                                    Enter your pincode to check delivery availability and estimated delivery time.
                                </div>
                                <div class="col-sm-9 col-12">
                                    <form class="input-group nlField rounded bg-secondary bg-opacity-10 overflow-hidden js-delivery-check-form">
                                        @csrf
                                        <input
                                            type="text"
                                            class="form-control bg-transparent nlInput border-0 fw-medium js-delivery-pincode"
                                            placeholder="Enter Pincode"
                                            aria-label="Enter Pincode"
                                            inputmode="numeric"
                                            pattern="[0-9]*"
                                            maxlength="6">
                                        <button class="ctaBtn nlBtn js-delivery-check-btn" type="submit">Check</button>
                                    </form>
                                    <div class="pt-2 small js-delivery-result d-none"></div>
                                </div>
                            </div>
                        </div>

                        @if($features->isNotEmpty())
                        <div class="col">
                            <div class="row mx-0 rounded">
                                <div class="col-sm-3 col-4 pe-4 pe-xl-5">
                                    <img src="{{ asset('frontend/assets/images/featureBg.svg') }}" class="w-100 h-auto" alt="">
                                </div>
                                <div class="col">
                                    <ul class="featureList row flex-column">
                                        @foreach($features as $feature)
                                            <li class="d-flex py-2">
                                                @if($feature->image)
                                                    <img src="{{ asset('backend/assets/images/features/' . $feature->image) }}" class="me-3" alt="" style="width:28px;height:28px;object-fit:contain;">
                                                @endif
                                                <div class="align-self-center">{{ $feature->title }}</div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-5 py-lg-4">
        <div class="container prodDetailWrap position-relative">
            <ul class="nav justify-content-center gap-5 pdwTabs gotoTarget d-none d-lg-flex" id="myTab" role="tablist" data-bs-target=".prodDetailWrap">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_Details" type="button" role="tab">Product Info</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_Brand" type="button" role="tab">About {{ $product->brand->name ?? 'Brand' }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="#reviewSection">Reviews ({{ $reviewCount }})</a>
                </li>
            </ul>

            <div class="tab-content accordion pdwContent rounded-3 p-lg-5" id="pdwAccordion">
                <div class="tab-pane clearfix fade show active" id="tab_Details" role="tabpanel">
                    <button class="accordion-button collapsed d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#tabAccd_1">Product Info</button>
                    <div class="collapse show" id="tabAccd_1" data-bs-parent="#pdwAccordion">
                        <div class="colInner tabInContent">
                            @if($product->description)
                                <div class="mb-4">{!! $product->description !!}</div>
                            @endif
                            @if($product->details)
                                <div class="mb-4">{!! $product->details !!}</div>
                            @endif
                            @if($product->specification)
                                <div class="specBox mb-4">
                                    <h3 class="specHead">Specifications</h3>
                                    <div class="specInfo">{!! $product->specification !!}</div>
                                </div>
                            @endif
                            @if(! $product->description && ! $product->details && ! $product->specification)
                                <p class="text-muted mb-0">Product details will be updated soon.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab_Brand" role="tabpanel">
                    <button class="accordion-button collapsed d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#tabAccd_3">About {{ $product->brand->name ?? 'Brand' }}</button>
                    <div class="collapse" id="tabAccd_3" data-bs-parent="#pdwAccordion">
                        <div class="tabInContent">
                            <div class="d-flex flex-wrap">
                                <div class="col-lg-4 col-12 mb-4 mb-lg-0 p-4 d-flex justify-content-center align-items-center bgGray">
                                    <img src="{{ optional($product->brand)->logo_url ?? asset('frontend/assets/images/product_1.jpg') }}" alt="{{ $product->brand->name ?? 'Brand' }}" style="max-width:180px;mix-blend-mode:multiply;">
                                </div>
                                <div class="col-lg col-12 ps-lg-5">
                                    <h3>{{ $product->brand->name ?? 'Brand' }}</h3>
                                    <p>Explore more watches from {{ $product->brand->name ?? 'this brand' }}.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-5 py-lg-4 reviewSection" id="reviewSection">
        <div class="container">
            <h2 class="sectTitle text-center mb-4">Customer Reviews</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="reviewSummary rounded-3 p-md-5 p-4 mb-4">
                @if($reviewCount === 0)
                    <div class="text-center py-2">
                        <h5 class="fw-bold mb-2">No reviews yet</h5>
                        <p class="text-secondary mb-4">Be the first to share your experience with {{ $product->name }}.</p>
                        <button type="button" class="ctaBtn" data-bs-toggle="collapse" data-bs-target="#reviewForm" aria-expanded="false">
                            Write a Review
                        </button>
                    </div>
                @else
                    <div class="row align-items-center g-4">
                        <div class="col-md">
                            <div class="starItemsWrap mx-auto pe-lg-4" style="max-width:420px;">
                                @foreach([5, 4, 3, 2, 1] as $star)
                                    <div class="row align-items-center gx-3 mb-2 starItem">
                                        <div class="col-auto text-secondary small">{{ $star }} ★</div>
                                        <div class="col px-0">
                                            <div class="progress" role="progressbar" aria-valuenow="{{ $ratingBreakdown[$star]['percent'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar" style="width:{{ $ratingBreakdown[$star]['percent'] ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-auto small text-secondary" style="min-width:2.5rem;">{{ $ratingBreakdown[$star]['percent'] ?? 0 }}%</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-auto d-none d-md-block"><div class="vr h-100"></div></div>

                        <div class="col-md">
                            <div class="text-center overallreview mx-auto">
                                <div class="prodRating">{{ number_format($averageRatingValue, 1) }}</div>
                                <div class="ratingBox mx-auto my-2"><div class="ratedStars {{ $ratingClass }}"></div></div>
                                <small class="d-block text-secondary mb-3">Based on {{ $reviewCount }} {{ $reviewCount === 1 ? 'Review' : 'Reviews' }}</small>
                                <button type="button" class="ctaBtn" data-bs-toggle="collapse" data-bs-target="#reviewForm" aria-expanded="false">
                                    Write a Review
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="reviewForm mx-auto collapse {{ $errors->any() ? 'show' : '' }}" id="reviewForm" style="max-width:720px;">
                    <div class="pt-4 mt-4 formFormat border-top">
                        @auth
                            @if($userHasReviewed)
                                <div class="alert alert-info mb-0">You have already submitted a review for this product.</div>
                            @else
                                <form action="{{ route('review.store', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3 fw-semibold text-uppercase">Your Review</div>

                                    <div class="form-group mb-4 d-flex flex-wrap align-items-center gap-3">
                                        <label class="mb-0">Select Stars <span class="txtRed">*</span></label>
                                        <div class="ratingScale">
                                            <input type="radio" name="rating" id="star1" value="1" {{ old('rating') == 1 ? 'checked' : '' }} required>
                                            <label for="star1" class="s1" title="1 Star">1 Star</label>
                                            <input type="radio" name="rating" id="star2" value="2" {{ old('rating') == 2 ? 'checked' : '' }}>
                                            <label for="star2" class="s2" title="2 Stars">2 Stars</label>
                                            <input type="radio" name="rating" id="star3" value="3" {{ old('rating') == 3 ? 'checked' : '' }}>
                                            <label for="star3" class="s3" title="3 Stars">3 Stars</label>
                                            <input type="radio" name="rating" id="star4" value="4" {{ old('rating') == 4 ? 'checked' : '' }}>
                                            <label for="star4" class="s4" title="4 Stars">4 Stars</label>
                                            <input type="radio" name="rating" id="star5" value="5" {{ old('rating') == 5 ? 'checked' : '' }}>
                                            <label for="star5" class="s5" title="5 Stars">5 Stars</label>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="mb-1">Your Review <span class="txtRed">*</span></label>
                                        <textarea class="form-control" name="comment" rows="4" placeholder="Share your experience with this product" required>{{ old('comment') }}</textarea>
                                    </div>

                                    <button type="submit" class="ctaBtn">Submit Review</button>
                                </form>
                            @endif
                        @else
                            <div class="text-center py-2">
                                <p class="mb-3 text-secondary">Please login to write a review.</p>
                                <a href="{{ route('login') }}" class="ctaBtn text-decoration-none">Login to Review</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            @if($previewReviews->isNotEmpty())
                <div class="row g-4 reviewBoxList">
                    @foreach($previewReviews as $review)
                        <div class="col-lg-4">
                            <div class="reviewBox rounded-3 d-flex flex-column p-4">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div class="ratingBox"><div class="ratedStars star{{ max(1, min(5, (int) $review->rating)) }}"></div></div>
                                    <small class="text-secondary">{{ optional($review->created_at)->format('d M Y') }}</small>
                                </div>
                                <div class="py-3 flex-grow-1">
                                    <p class="mb-0 text-secondary">{{ $review->review }}</p>
                                </div>
                                <div class="mt-auto reviewAuthor d-flex align-items-center gap-2">
                                    <img src="{{ optional($review->user)->avatar_url ?? asset('backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg') }}"
                                        alt="{{ $review->user->name ?? 'Anonymous' }}"
                                        class="reviewAuthorImg rounded-circle">
                                    <span>By: <strong>{{ $review->user->name ?? 'Anonymous' }}</strong></span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($reviewCount > 3)
                        <div class="col-12 text-center">
                            <button type="button" class="ctaBtn" data-bs-toggle="modal" data-bs-target="#moreReviews">
                                Show More Reviews
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if($reviewCount > 3)
        <div class="modal fade" id="moreReviews" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header bgGray border-0">
                        <h5 class="modal-title fw-bold">Reviews ({{ $reviewCount }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @foreach($reviews as $review)
                            <div class="reviewBox rounded-3 d-flex flex-column p-3 mb-3">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div class="ratingBox"><div class="ratedStars star{{ max(1, min(5, (int) $review->rating)) }}"></div></div>
                                    <small class="text-secondary">{{ optional($review->created_at)->format('d M Y') }}</small>
                                </div>
                                <div class="py-2">
                                    <p class="mb-0 text-secondary">{{ $review->review }}</p>
                                </div>
                                <div class="reviewAuthor d-flex align-items-center gap-2">
                                    <img src="{{ optional($review->user)->avatar_url ?? asset('backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg') }}"
                                        alt="{{ $review->user->name ?? 'Anonymous' }}"
                                        class="reviewAuthorImg rounded-circle">
                                    <span>By: <strong>{{ $review->user->name ?? 'Anonymous' }}</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($related_products->isNotEmpty())
    <section class="py-md-5 py-4 overflow-hidden bgGray">
        <div class="container">
            <h2 class="sectTitle text-center mb-4">More From {{ $product->brand->name ?? 'Brand' }}</h2>
            <div class="swiper productSlider pb-sm-5 pb-3 mb-4">
                <div class="swiper-wrapper">
                    @foreach($related_products as $related)
                        @include('frontend.partials.home-product-slide', [
                            'product' => $related,
                            'showNewBadge' => (bool) ($related->is_new_arrival ?? false),
                        ])
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="text-center">
                <a href="{{ route('product.listing', array_filter(['brand' => [$product->brand_id]])) }}" class="ctaBtn">View All</a>
            </div>
        </div>
    </section>
    @endif

    <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content shadow">
                <div class="modal-header bgGray border-0">
                    <h6 class="modal-title fw-bold">Share This Product</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row row-cols-3 justify-content-center g-4">
                        <div class="col">
                            <button class="shareItem hoverBtn rounded-3" onclick="window.open('https://wa.me/?text={{ urlencode(request()->fullUrl()) }}','_blank')">WhatsApp</button>
                        </div>
                        <div class="col">
                            <button class="shareItem hoverBtn rounded-3" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}','_blank')">Facebook</button>
                        </div>
                        <div class="col">
                            <button class="shareItem hoverBtn rounded-3" onclick="navigator.clipboard.writeText('{{ request()->fullUrl() }}'); alert('URL copied');">Copy URL</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js"></script>
<script>
    (function () {
        var thumbEl = document.querySelector('.prodImgThumbSwiper');
        var mainEl = document.querySelector('.prodImgSwiper');

        if (mainEl) {
            var thumbSwiper = null;
            if (thumbEl) {
                thumbSwiper = new Swiper('.prodImgThumbSwiper', {
                    speed: 1000,
                    loop: false,
                    slidesPerView: 5,
                    spaceBetween: 35,
                    watchSlidesProgress: true,
                });
            }

            var mainSwiper = new Swiper('.prodImgSwiper', {
                speed: 1000,
                loop: false,
                spaceBetween: 20,
                lazy: true,
                pagination: {
                    el: '.prodImgSwiper .swiper-pagination',
                    clickable: true,
                    dynamicBullets: false
                },
                navigation: {
                    nextEl: '.prodImgNext',
                    prevEl: '.prodImgPrev'
                },
                thumbs: thumbSwiper ? { swiper: thumbSwiper } : undefined,
                on: {
                    slideChange: function () {
                        document.querySelectorAll('.productImgSlider .prodZoomWrap.is-zooming').forEach(function (wrap) {
                            wrap.classList.remove('is-zooming');
                            var zoomImg = wrap.querySelector('.prodZoomImg');
                            if (zoomImg) {
                                zoomImg.style.transformOrigin = 'center center';
                            }
                        });
                    }
                }
            });
        }

        $('.pdwContent .collapse').on('shown.bs.collapse', function () {
            var $openTarget = $(this).siblings('.accordion-button');
            if ($openTarget.length) {
                $('html,body').animate({ scrollTop: $openTarget.offset().top - 75 }, 500);
            }
        });

        if (typeof Fancybox !== 'undefined') {
            Fancybox.bind('[data-fancybox="prodGallery"]', {
                groupAll: true,
                Carousel: {
                    Thumbs: false,
                    Toolbar: {
                        display: {
                            left: ['counter'],
                            middle: [],
                            right: ['close'],
                        },
                    },
                },
            });
        }

        function canProductZoom() {
            return window.matchMedia('(min-width: 992px) and (hover: hover)').matches;
        }

        function initProductDetailZoom() {
            document.querySelectorAll('.productImgSlider .prodZoomWrap').forEach(function (wrap) {
                if (wrap.dataset.zoomReady === '1') {
                    return;
                }

                wrap.dataset.zoomReady = '1';

                var img = wrap.querySelector('.prodZoomImg');
                if (!img) {
                    return;
                }

                var lens = wrap.querySelector('.prodZoomLens');
                if (!lens) {
                    lens = document.createElement('span');
                    lens.className = 'prodZoomLens';
                    wrap.appendChild(lens);
                }

                wrap.addEventListener('mousemove', function (event) {
                    if (!canProductZoom()) {
                        return;
                    }

                    var rect = wrap.getBoundingClientRect();
                    var x = Math.min(Math.max(event.clientX - rect.left, 0), rect.width);
                    var y = Math.min(Math.max(event.clientY - rect.top, 0), rect.height);
                    var xPercent = (x / rect.width) * 100;
                    var yPercent = (y / rect.height) * 100;

                    wrap.classList.add('is-zooming');
                    wrap.style.setProperty('--zoom-x', xPercent + '%');
                    wrap.style.setProperty('--zoom-y', yPercent + '%');
                    img.style.transformOrigin = xPercent + '% ' + yPercent + '%';
                    lens.style.left = x + 'px';
                    lens.style.top = y + 'px';
                });

                wrap.addEventListener('mouseleave', function () {
                    wrap.classList.remove('is-zooming');
                    img.style.transformOrigin = 'center center';
                });
            });
        }

        initProductDetailZoom();

        document.querySelectorAll('#productPurchaseBox .quantitySpinner').forEach(function (spinner) {
            var minusButton = spinner.querySelector('.minus');
            var plusButton = spinner.querySelector('.plus');
            var input = spinner.querySelector('.numData');
            var buyQtyInput = document.querySelector('#productPurchaseBox .js-buy-qty');

            if (!minusButton || !plusButton || !input) {
                return;
            }

            var minQty = 1;
            var maxQty = Math.min(10, parseInt(input.getAttribute('max') || '10', 10) || 10);

            var syncButtons = function (value) {
                minusButton.disabled = value <= minQty;
                plusButton.disabled = value >= maxQty;
                if (buyQtyInput) {
                    buyQtyInput.value = value;
                }
            };

            syncButtons(parseInt(input.value || '1', 10));

            minusButton.addEventListener('click', function () {
                var currentValue = parseInt(input.value || '1', 10);
                if (currentValue > minQty) {
                    currentValue -= 1;
                    input.value = currentValue;
                    syncButtons(currentValue);
                }
            });

            plusButton.addEventListener('click', function () {
                var currentValue = parseInt(input.value || '1', 10);
                if (currentValue < maxQty) {
                    currentValue += 1;
                    input.value = currentValue;
                    syncButtons(currentValue);
                }
            });
        });

        var deliveryForm = document.querySelector('.js-delivery-check-form');
        if (deliveryForm) {
            deliveryForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                var pincodeInput = deliveryForm.querySelector('.js-delivery-pincode');
                var resultEl = document.querySelector('.js-delivery-result');
                var submitBtn = deliveryForm.querySelector('.js-delivery-check-btn');
                var csrf = deliveryForm.querySelector('input[name="_token"]')?.value || '';
                var pincode = (pincodeInput?.value || '').trim();

                if (!resultEl || !pincodeInput || !submitBtn) {
                    return;
                }

                if (!/^\d{6}$/.test(pincode)) {
                    resultEl.classList.remove('d-none', 'text-success');
                    resultEl.classList.add('text-danger');
                    resultEl.textContent = 'Please enter a valid 6-digit pincode.';
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Checking...';

                try {
                    var response = await fetch('{{ route('product.check_delivery') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ pincode: pincode })
                    });

                    var payload = await response.json();
                    if (!response.ok || !payload.success) {
                        throw new Error(payload.message || 'Unable to check delivery right now.');
                    }

                    resultEl.classList.remove('d-none', 'text-danger');
                    resultEl.classList.add('text-success');
                    resultEl.innerHTML = 'Delivery available in <strong>' + payload.estimated_days + '</strong>' +
                        (payload.free_shipping_minimum > 0
                            ? '. Free shipping above ₹' + Number(payload.free_shipping_minimum).toFixed(2)
                            : '.');
                } catch (error) {
                    resultEl.classList.remove('d-none', 'text-success');
                    resultEl.classList.add('text-danger');
                    resultEl.textContent = error.message || 'Unable to check delivery right now.';
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Check';
                }
            });
        }
    })();
</script>
@endpush
