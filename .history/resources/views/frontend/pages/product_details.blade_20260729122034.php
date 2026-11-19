@extends('layouts.front')

@section('content')

<main class="pageWrapper">

    <section class="bgGray py-3">
        <div class="container">
            <nav class="breadcrumb mb-0 animateThis slideTop">
                <a class="breadcrumb-item" href="{{ url('/') }}">Home</a>
                <a class="breadcrumb-item" href="">{{ $product->category->name ?? 'Category' }}</a>
                <a class="breadcrumb-item active" href="">{{ $product->brand->name ?? 'Brand' }}</a>
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
                                    @foreach($product->images as $image)
                                        <div class="swiper-slide">
                                            <a href="{{ asset('storage/'.$image->image) }}" data-fancybox="prodGallery" class="picBox">
                                                <img src="{{ asset('storage/'.$image->image) }}" loading="lazy">
                                                <div class="swiper-lazy-preloader"></div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-button-next prodImgBtns prodImgNext"></div>
                                <div class="swiper-button-prev prodImgBtns prodImgPrev"></div>
                                <div class="swiper-pagination d-lg-none"></div>
                            </div>
                        </div>
                        <div class="col-auto d-none d-lg-block py-3">
                            <div class="swiper prodImgThumbSwiper">
                                <div class="swiper-wrapper">
                                    @foreach($product->images as $image)
                                        <div class="swiper-slide">
                                            <div class="pitBox"><img src="{{ asset('storage/'.$image->image) }}"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="col-xl-5 col-lg-6 mb-5 productDetail">

                    <div class="row flex-column g-4">

                        <div class="col">

                            <div class="prodTitle d-flex flex-column gap-3">

                                <div class="d-flex align-items-center gap-4">

                                    <div class="col-auto">
                                        <h1 class="sectTitle text-black text-uppercase mb-0">{{$product->brand->name ?? ''}}</h1>
                                    </div>
                                    @if($product->is_new)
                                        <div class="col-auto">
                                            <div class="pi_newBadge txtRed fw-medium">New Arrival</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex gap-2">

                                    <div class="col">

                                        <h2 class="prod_Name">{{$product->name}}</h2>

                                        <h3 class="prod_ModelNo mb-0">{{$product->model_no}}</h3>

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
                                <div class="ratedStars star{{ floor($product->averageRating*2)/2 }}"></div>
                            </div>
                            <div class="pi_reviewCount fw-semibold">{{ number_format($product->averageRating, 1) }} ({{ $product->reviews_count }} Reviews)</div>
                        </div>

                        <div class="col">

                            <div class="d-flex align-items-center pi_price gap-4">
                                <div class="col-auto">
                                    @if($product->original_price && $product->original_price > $product->sale_price)
                                        <div class="pip_ProdStrike">₹ {{ number_format($product->original_price,2) }}</div>
                                    @endif
                                    <div class="prod_Price fw-bolder">₹ {{ number_format($product->sale_price,2) }}</div>
                                </div>
                                <div class="col">
                                    @if($product->original_price && $product->original_price > $product->sale_price)
                                        <div class="offerTag">
                                            {{ round(100-($product->sale_price/$product->original_price)*100) }}% 
                                            <small class="d-block">Off</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col">

                            <div class="d-flex flex-row flex-wrap gap-3">

                                <div class="col"><button class="ctaBtn w-100">Buy Now</button></div>

                                <div class="col"><button class="ctaBtn dark w-100">Add to Cart</button></div>

                                <div class="col-12 text-center hstack gap-3">

                                    <hr class="w-100">

                                    <form method="POST" action="{{ route('wishlist.add', $product->id) }}">
                                        @csrf
                                        <button class="wishlistBtn prodDetWish flex-shrink-0" type="submit">
                                            {{ in_array($product->id, $wishlistIds ?? []) ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                                        </button>
                                    </form>

                                    <hr class="w-100">

                                </div>

                            </div>

                        </div>

                        <div class="col">

                            <div class="deliveryCheck d-flex flex-wrap rounded shadow-sm p-3 my-4 gap-3 bg-white">

                                <div class="col-12 lh-base text-secondary">

                                    <strong class="d-block fw-bold text-uppercase text-dark">Check Delivery</strong>

                                    Enter your pincode to check delivery availability and estimated delivery time.

                                </div>

                                <div class="col-sm-9 col-10">

                                    <div class="input-group nlField rounded bg-secondary bg-opacity-10 overflow-hidden">

                                        <input type="text" class="form-control bg-transparent nlInput border-0 fw-medium" placeholder="Enter Pincode" aria-label="Enter Pincode">

                                        <button class="ctaBtn nlBtn" type="button" data-bs-toggle="collapse" data-bs-target="#deliveryCheck">Check</button>

                                    </div>

                                    <div class="collapse" id="deliveryCheck">
                                        {{-- You may want to dynamically show pincode delivery info here --}}
                                        <div class="pt-2">Standard delivery available to this location within
                                        <strong class="txtRed d-inline-block">2 working days</strong></div>
                                    </div>
                                </div>
                                {{-- Keep SVG unchanged --}}
                                <svg class="deliveryBg" viewBox="0 0 682.66669 682.66669" xmlns="http://www.w3.org/2000/svg">
                                    ...{{-- SVG content omitted for brevity --}}
                                </svg>
                            </div>

                        </div>

                        <div class="col">

                            <div class="row mx-0 rounded">

                                <div class="col-sm-3 col-4 pe-4 pe-xl-5">
                                    <img src="{{ asset('frontend/assets/images/featureBg.svg') }}" class="w-100 h-auto">
                                </div>

                                <div class="col">

                                    <ul class="featureList row flex-column">
                                        @foreach($product->features as $feature)
                                            <li class="d-flex py-2">
                                                <img src="{{ asset('frontend/assets/images/'.$feature->icon) }}" class="me-3">
                                                <div class="align-self-center">{{ $feature->title }}</div>
                                            </li>
                                        @endforeach
                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </section>

    <section class="mb-5 py-lg-4">

        <div class="container prodDetailWrap position-relative">

            <ul class="nav justify-content-center gap-5 pdwTabs gotoTarget d-none d-lg-flex" id="myTab" role="tablist" data-bs-target=".prodDetailWrap">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_Details" type="button" role="tab" aria-controls="tab_Details" aria-selected="true">Product Info</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_Brand" type="button" role="tab" aria-controls="tab_Brand" aria-selected="false">About {{ $product->brand->name ?? 'Brand' }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_ShipReturn" type="button" role="tab" aria-controls="tab_ShipReturn" aria-selected="false">Shipping And Returns</button>
                </li>
            </ul>

            <div class="tab-content accordion pdwContent rounded-3 p-lg-5" id="pdwAccordion">

                <div class="tab-pane clearfix fade show active" id="tab_Details" role="tabpanel">

                    <button class="accordion-button collapsed d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#tabAccd_1" aria-expanded="false" aria-controls="tabAccd_1">Product Info</button>

                    <div class="collapse" id="tabAccd_1" data-bs-parent="#pdwAccordion">

                        <div class="colInner tabInContent">
                            @foreach($product->specifications as $specCategory => $specs)
                            <div class="specBox mb-4">
                                <h3 class="specHead">{{ $specCategory }}</h3>
                                <ul class="specList">
                                    @foreach($specs as $key => $value)
                                        <li class="d-flex">
                                            <div class="specSubHead">{{ $key }}</div> <div class="specInfo">{{ $value }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="tab-pane fade" id="tab_Brand" role="tabpanel">

                    <button class="accordion-button collapsed d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#tabAccd_3" aria-expanded="false" aria-controls="tabAccd_3">About {{ $product->brand->name ?? 'Brand' }}</button>
                    <div class="collapse" id="tabAccd_3" data-bs-parent="#pdwAccordion">
                        <div class="tabInContent">

                            <div class="d-flex flex-wrap">

                                <div class="col-lg-4 col-12 mb-4 mb-lg-0 p-4 d-flex justify-content-center align-items-center bgGray">

                                    <img src="{{ $product->brand->image ? asset('storage/'.$product->brand->image) : asset('frontend/assets/images/brands/default.jpg') }}" alt="" style="mix-blend-mode:multiply;">

                                </div>

                                <div class="col-lg col-12 ps-lg-5">

                                    <h3>{{ $product->brand->headline ?? ('Legacy of '.$product->brand->name) }}</h3>

                                    <p>{{ $product->brand->description ?? 'No description available.' }}</p>

                                    @if(!empty($product->brand->notes))
                                        <p>{!! nl2br(e($product->brand->notes)) !!}</p>
                                    @endif

                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab_ShipReturn" role="tabpanel">

                    <button class="accordion-button collapsed d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#tabAccd_2" aria-expanded="false" aria-controls="tabAccd_2">Shipping And Returns</button>
                    <div class="collapse" id="tabAccd_2" data-bs-parent="#pdwAccordion">

                        <div class="tabInContent">
                            {!! setting('shipping_and_returns') !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-md-5 py-4 overflow-hidden bgGray">
        <div class="container">

            <h2 class="sectTitle text-center mb-4">More From {{ $product->brand->name ?? 'Brand' }}</h2>
            <div class="swiper productSlider pb-sm-5 pb-3 mb-4">

                <div class="swiper-wrapper">

                    @foreach($related_products as $related)
                    <div class="swiper-slide">
                        <div class="productItem text-center animateThis fadeIn">
                            <div class="pi_topBar d-flex justify-content-between">
                                <div class="col-auto">
                                    @if($related->is_new)
                                        <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                                    @endif
                                    @if($related->original_price && $related->original_price > $related->sale_price)
                                        <div class="pi_offer d-flex flex-column justify-content-center">
                                            {{ round(100-($related->sale_price/$related->original_price)*100) }}% <small>Off</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-auto">
                                    <form method="POST" action="{{ route('wishlist.add', $related->id) }}">
                                        @csrf
                                        <button type="submit" class="wishlistBtn" title="Add to Wishlist"></button>
                                    </form>
                                </div>
                            </div>
                            <div class="pi_imgbox mb-4">
                                <img src="{{ $related->thumbnail ? asset('storage/'.$related->thumbnail) : asset('frontend/assets/images/no_image.png') }}" alt="{{ $related->name }}" class="pi_img">
                            </div>
                            <div class="pi_details">
                                <h2 class="pi_prodBrand fw-bold mb-2">{{ $related->brand->name ?? '' }}</h2>
                                <h3 class="pi_prodName mb-3" title="{{ $related->name }}">{{ $related->name }}</h3>
                                <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                    <div class="ratingBox">
                                        <div class="ratedStars star{{ floor($related->averageRating*2)/2 }}"></div>
                                    </div>
                                    <div class="pi_reviewCount fw-semibold">({{ $related->reviews_count }})</div>
                                </div>
                                <div class="pi_price">
                                    @if($related->original_price && $related->original_price > $related->sale_price)
                                        <small class="pip_strike me-2">₹ {{ number_format($related->original_price,2) }}</small>
                                    @endif
                                    <strong class="pip_ProdPrice fw-bolder">₹ {{ number_format($related->sale_price,2) }}</strong>
                                </div>
                            </div>
                            <a href="{{ route('frontend.product.details', $related->slug) }}" class="stretched-link z-3" title="{{ $related->name }}"></a>
                        </div>
                    </div>
                    @endforeach

                </div>

                <div class="swiper-pagination"></div>

            </div>

            <div class="text-center">
                <a href="{{ route('frontend.brand.products', $product->brand->slug ?? '') }}" class="ctaBtn">View All</a>
            </div>

        </div>
    </section>

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
                            <button class="shareItem hoverBtn rounded-3" onclick="window.open('https://wa.me/?text={{ urlencode(request()->fullUrl()) }}','_blank')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="currentColor">
                                    <path d="M25.873,6.069c-2.619-2.623-6.103-4.067-9.814-4.069C8.411,2,2.186,8.224,2.184,15.874c-.001,2.446,.638,4.833,1.852,6.936l-1.969,7.19,7.355-1.929c2.026,1.106,4.308,1.688,6.63,1.689h.006c7.647,0,13.872-6.224,13.874-13.874,.001-3.708-1.44-7.193-4.06-9.815h0Zm-9.814,21.347h-.005c-2.069,0-4.099-.557-5.87-1.607l-.421-.25-4.365,1.145,1.165-4.256-.274-.436c-1.154-1.836-1.764-3.958-1.763-6.137,.003-6.358,5.176-11.531,11.537-11.531,3.08,.001,5.975,1.202,8.153,3.382,2.177,2.179,3.376,5.077,3.374,8.158-.003,6.359-5.176,11.532-11.532,11.532h0Zm6.325-8.636c-.347-.174-2.051-1.012-2.369-1.128-.318-.116-.549-.174-.78,.174-.231,.347-.895,1.128-1.098,1.359-.202,.232-.405,.26-.751,.086-.347-.174-1.464-.54-2.788-1.72-1.03-.919-1.726-2.054-1.929-2.402-.202-.347-.021-.535,.152-.707,.156-.156,.347-.405,.52-.607,.174-.202,.231-.347,.347-.578,.116-.232,.058-.434-.029-.607-.087-.174-.78-1.88-1.069-2.574-.281-.676-.567-.584-.78-.595-.202-.01-.433-.012-.665-.012s-.607,.086-.925,.434c-.318,.347-1.213,1.186-1.213,2.892s1.242,3.355,1.416,3.587c.174,.232,2.445,3.733,5.922,5.235,.827,.357,1.473,.571,1.977,.73,.83,.264,1.586,.227,2.183,.138,.666-.1,2.051-.839,2.34-1.649,.289-.81,.289-1.504,.202-1.649s-.318-.232-.665-.405h0Z" fill-rule="evenodd"></path>
                                </svg>
                                WhatsApp
                            </button>
                        </div>
                        <div class="col">
                            <button class="shareItem hoverBtn rounded-3" onclick="window.open('https://www.instagram.com/?url={{ urlencode(request()->fullUrl()) }}','_blank')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="currentColor">
                                    <path d="M10.202,2.098c-1.49,.07-2.507,.308-3.396,.657-.92,.359-1.7,.84-2.477,1.619-.776,.779-1.254,1.56-1.61,2.481-.345,.891-.578,1.909-.644,3.4-.066,1.49-.08,1.97-.073,5.771s.024,4.278,.096,5.772c.071,1.489,.308,2.506,.657,3.396,.359,.92,.84,1.7,1.619,2.477,.779,.776,1.559,1.253,2.483,1.61,.89,.344,1.909,.579,3.399,.644,1.49,.065,1.97,.08,5.771,.073,3.801-.007,4.279-.024,5.773-.095s2.505-.309,3.395-.657c.92-.36,1.701-.84,2.477-1.62s1.254-1.561,1.609-2.483c.345-.89,.579-1.909,.644-3.398,.065-1.494,.081-1.971,.073-5.773s-.024-4.278-.095-5.771-.308-2.507-.657-3.397c-.36-.92-.84-1.7-1.619-2.477s-1.561-1.254-2.483-1.609c-.891-.345-1.909-.58-3.399-.644s-1.97-.081-5.772-.074-4.278,.024-5.771,.096m.164,25.309c-1.365-.059-2.106-.286-2.6-.476-.654-.252-1.12-.557-1.612-1.044s-.795-.955-1.05-1.608c-.192-.494-.423-1.234-.487-2.599-.069-1.475-.084-1.918-.092-5.656s.006-4.18,.071-5.656c.058-1.364,.286-2.106,.476-2.6,.252-.655,.556-1.12,1.044-1.612s.955-.795,1.608-1.05c.493-.193,1.234-.422,2.598-.487,1.476-.07,1.919-.084,5.656-.092,3.737-.008,4.181,.006,5.658,.071,1.364,.059,2.106,.285,2.599,.476,.654,.252,1.12,.555,1.612,1.044s.795,.954,1.051,1.609c.193,.492,.422,1.232,.486,2.597,.07,1.476,.086,1.919,.093,5.656,.007,3.737-.006,4.181-.071,5.656-.06,1.365-.286,2.106-.476,2.601-.252,.654-.556,1.12-1.045,1.612s-.955,.795-1.608,1.05c-.493,.192-1.234,.422-2.597,.487-1.476,.069-1.919,.084-5.657,.092s-4.18-.007-5.656-.071M21.779,8.517c.002,.928,.755,1.679,1.683,1.677s1.679-.755,1.677-1.683c-.002-.928-.755-1.679-1.683-1.677,0,0,0,0,0,0-.928,.002-1.678,.755-1.677,1.683m-12.967,7.496c.008,3.97,3.232,7.182,7.202,7.174s7.183-3.232,7.176-7.202c-.008-3.97-3.233-7.183-7.203-7.175s-7.182,3.233-7.174,7.203m2.522-.005c-.005-2.577,2.08-4.671,4.658-4.676,2.577-.005,4.671,2.08,4.676,4.658,.005,2.577-2.08,4.671-4.658,4.676-2.577,.005-4.671-2.079-4.676-4.656h0"></path>
                                </svg>
                                Instagram
                            </button>
                        </div>
                        <div class="col">
                            <button class="shareItem hoverBtn rounded-3" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}','_blank')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="currentColor">
                                    <path d="M16,2c-7.732,0-14,6.268-14,14,0,6.566,4.52,12.075,10.618,13.588v-9.31h-2.887v-4.278h2.887v-1.843c0-4.765,2.156-6.974,6.835-6.974,.887,0,2.417,.174,3.043,.348v3.878c-.33-.035-.904-.052-1.617-.052-2.296,0-3.183,.87-3.183,3.13v1.513h4.573l-.786,4.278h-3.787v9.619c6.932-.837,12.304-6.74,12.304-13.897,0-7.732-6.268-14-14-14Z"></path>
                                </svg>
                                Facebook
                            </button>
                        </div>
                        <div class="col">
                            <button class="shareItem hoverBtn rounded-3" onclick="window.open('https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}','_blank')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="currentColor">
                                    <path d="M18.42,14.009L27.891,3h-2.244l-8.224,9.559L10.855,3H3.28l9.932,14.455L3.28,29h2.244l8.684-10.095,6.936,10.095h7.576l-10.301-14.991h0Zm-3.074,3.573l-1.006-1.439L6.333,4.69h3.447l6.462,9.243,1.006,1.439,8.4,12.015h-3.447l-6.854-9.804h0Z"></path>
                                </svg>
                                Twitter
                            </button>
                        </div>
                        <div class="col">
                            <button class="shareItem hoverBtn rounded-3" onclick="window.location.href = 'mailto:?subject={{ urlencode($product->name) }}&body={{ urlencode(request()->fullUrl()) }}'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                                    <polyline points="30 9 16 16 2 9" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2" data-color="color-2"></polyline>
                                    <rect x="2" y="4" width="28" height="24" rx="3" ry="3" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></rect>
                                </svg>
                                Email
                            </button>
                        </div>
                        <div class="col">
                            <button class="shareItem hoverBtn rounded-3" onclick="navigator.clipboard.writeText('{{ request()->fullUrl() }}'); alert('URL copied to clipboard');">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                    <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                </svg>
                                Copy URL
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</main>

@endsection