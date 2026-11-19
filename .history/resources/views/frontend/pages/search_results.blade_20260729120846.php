@extends('layouts.front')

@section('content')

<main class="pageWrapper">

    <section class="inBannerSecton position-relative overflow-hidden w-100 mb-5">
        <img src="{{ asset('frontend/assets/images/prodlistBanner.jpg') }}" class="inBanImg" alt="">

        <div class="container h-100 position-relative z-1 d-flex flex-column justify-content-center align-items-center">

            <nav class="breadcrumb mb-0 fw-semibold animateThis slideTop">
                <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                <span class="breadcrumb-item active">Search</span>
            </nav>

            <h1 class="inBanTitle fw-bold txtred animateThis slideTop">
                Search Results
            </h1>

            <p class="text-white mb-0 animateThis slideTop">
                Showing results for “{{ $search }}”
            </p>

        </div>
    </section>

    <section>
        <div class="container">

            @if($matchedCategories->count())
                <div class="mb-5">
                    <h2 class="fw-bold mb-4">Matching Categories</h2>
                    <div class="row row-cols-lg-4 row-cols-sm-2 row-cols-1 g-4">
                        @foreach($matchedCategories as $category)
                            <div class="col">
                                <a href="{{ route('product_listing', $category->slug) }}" class="d-block text-decoration-none text-dark">
                                    <div class="text-center">
                                        @if($category->image)
                                            <img
                                                src="{{ asset('backend/assets/images/category/' . $category->image) }}"
                                                alt="{{ $category->name }}"
                                                class="img-fluid mb-3 rounded"
                                                style="max-height: 140px; object-fit: contain;"
                                            >
                                        @endif
                                        <h3 class="h5 mb-1">{{ $category->name }}</h3>
                                        <small class="text-muted">{{ $category->products_count }} products</small>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="fw-bold mb-0">Products</h2>
                <span class="text-muted">{{ $products->total() }} result(s)</span>
            </div>

            <div class="row row-cols-lg-4 row-cols-sm-2 row-cols-1 gx-4 gy-5 mb-5 productResult">

                @forelse($products as $product)

                    @php
                        $image = $product->thumbnail
                            ? asset('backend/assets/images/product/' . $product->thumbnail)
                            : asset('frontend/assets/images/no-image.png');

                        $sellingPrice = $product->selling_price ?? $product->price ?? 0;
                        $originalPrice = $product->price ?? 0;

                        $discount = (int) ($product->discount ?? 0);
                        if ($discount <= 0 && $originalPrice > $sellingPrice && $originalPrice > 0) {
                            $discount = (int) round((($originalPrice - $sellingPrice) / $originalPrice) * 100);
                        }

                        $rating = $product->rating ?? 0;
                        $reviewsCount = $product->reviews_count ?? 0;
                    @endphp

                    <div class="col">
                        <div class="prodItemBox text-center d-flex flex-column h-100">

                            <div class="pibImgBox mx-auto mb-4">
                                <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                    <img src="{{ $image }}" class="pibProdImg" alt="{{ $product->name }}">
                                </a>

                                @include('frontend.partials.product_labels', ['product' => $product, 'discount' => $discount])
                            </div>

                            <div class="pibDetail">
                                @if($product->category)
                                    <small class="text-muted d-block mb-1">{{ $product->category->name }}</small>
                                @endif

                                <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                    <h2 class="pibProdName mb-3">{{ $product->name }}</h2>
                                </a>

                                <div class="pibReview mb-2 d-flex align-items-center justify-content-center gap-2">
                                    <div class="ratingBox">
                                        <div class="ratedStars star{{ round($rating * 2) / 2 }}"></div>
                                    </div>
                                    <small class="bgGreen text-white lh-1 rounded-1 p-1">
                                        {{ number_format($rating, 1) }}
                                    </small>
                                    <span>({{ $reviewsCount }} Reviews)</span>
                                </div>

                                <div class="pibPriceBox mb-0 d-flex flex-column justify-content-center align-items-center gap-1">
                                    <strong class="pibProdPrice">₹{{ number_format($sellingPrice, 2) }}</strong>

                                    @if($originalPrice > $sellingPrice)
                                        <span class="pibStikePrice text-decoration-line-through text-muted small">
                                            ₹{{ number_format($originalPrice, 2) }}
                                        </span>
                                    @endif

                                    @if($discount > 0)
                                        <div class="pibDiscount txtgreen fw-semibold">
                                            Save {{ $discount }}%
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="pibBtns mt-auto px-2 pt-4 d-flex justify-content-center gap-3">
                                @php $inCart = in_array((int) $product->id, $cartProductIds ?? [], true); @endphp
                                <button type="button"
                                    class="pibBtn cartBtn js-add-to-cart"
                                    data-product-id="{{ $product->id }}"
                                    @if($inCart) data-in-cart="1" @endif>
                                    <span class="btnTxt">Add to Cart</span>
                                </button>

                                <a href="{{ route('checkout.buy', $product->id) }}"
                                    class="pibBtn buyBtn js-buy-now"
                                    data-buy-url="{{ route('checkout.buy', $product->id) }}">
                                    <span class="btnTxt">Buy Now</span>
                                </a>
                            </div>

                        </div>
                    </div>

                @empty
                    <div class="col-12 text-center py-5">
                        <h4>No products found for “{{ $search }}”</h4>
                        <p class="text-muted mb-0">Try a different product name, SKU, or category.</p>
                    </div>
                @endforelse

            </div>

            <div class="d-flex justify-content-center mb-5">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </section>

</main>

@endsection
