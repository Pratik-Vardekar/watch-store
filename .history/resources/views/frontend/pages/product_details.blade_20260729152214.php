@extends('layouts.front')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css" />
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
    $reviewsCount = (int) ($product->reviews_count ?? 0);
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
                                            <a href="{{ $product->getProductImageUrl($image) }}" data-fancybox="prodGallery" class="picBox">
                                                <img src="{{ $product->getProductImageUrl($image) }}" alt="{{ $product->name }}" loading="lazy">
                                                <div class="swiper-lazy-preloader"></div>
                                            </a>
                                        </div>
                                    @empty
                                        <div class="swiper-slide">
                                            <div class="picBox">
                                                <img src="{{ $product->getProductImageUrl() }}" alt="{{ $product->name }}">
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
                            <div class="pi_reviewCount fw-semibold">{{ number_format($avgRating, 1) }} ({{ $reviewsCount }} Reviews)</div>
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

                        <div class="col">
                            <div class="d-flex flex-row flex-wrap gap-3">
                                <div class="col">
                                    <a href="{{ route('checkout.buy', $product->id) }}" class="ctaBtn w-100 text-center text-decoration-none d-inline-flex justify-content-center align-items-center">Buy Now</a>
                                </div>
                                <div class="col">
                                    <button type="button"
                                        class="ctaBtn dark w-100 js-add-to-cart"
                                        data-product-id="{{ $product->id }}"
                                        data-add-url="{{ route('cart.add', $product->id) }}">
                                        Add to Cart
                                    </button>
                                </div>
                                <div class="col-12 text-center hstack gap-3">
                                    <hr class="w-100">
                                    @auth
                                        <button type="button"
                                            class="wishlistBtn prodDetWish flex-shrink-0 js-wishlist-toggle {{ $isWishlisted ? 'saved' : '' }}"
                                            data-product-id="{{ $product->id }}"
                                            data-toggle-url="{{ route('wishlist.toggle', $product->id) }}"
                                            data-label-add="Add to Wishlist"
                                            data-label-remove="Remove from Wishlist">
                                            {{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="wishlistBtn prodDetWish flex-shrink-0 text-decoration-none">Add to Wishlist</a>
                                    @endauth
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

            new Swiper('.prodImgSwiper', {
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
