<div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 gx-4 gy-5 mb-5 productResult">

    @forelse($products as $product)

    @php
    $image = $product->thumbnail
    ? asset('backend/assets/images/product/' . $product->thumbnail)
    : asset('frontend/assets/images/no-image.png');

    $mrp = (float) ($product->price ?? 0);
    $sellingPrice = (float) ($product->selling_price ?? 0);
    if ($sellingPrice <= 0) {
        $sellingPrice = $mrp;
    }

    $hasDiscount = ($mrp > 0 && $sellingPrice > 0 && $mrp > $sellingPrice);
    $discount = 0;
    if ($hasDiscount) {
        $discount = (int) ($product->discount ?? 0);
        if ($discount <= 0) {
            $discount = (int) round((($mrp - $sellingPrice) / $mrp) * 100);
        }
    }

            $rating = $product->rating ?? 0;
            $reviewsCount = $product->reviews_count ?? 0;
            $soldCount = $product->sold_count ?? 0;
            @endphp

            <div class="col">
                <div class="prodItemBox text-center d-flex flex-column h-100">

                    <div class="pibImgBox mx-auto mb-4">
                        <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                            <img src="{{ $image }}" class="pibProdImg" alt="{{ $product->name }}">
                        </a>

                        @include('frontend.partials.product_labels', [
                        'product' => $product,
                        'discount' => $discount,
                        'discountOnly' => $showDiscountTagOnly ?? false,
                        ])
                    </div>

                    <div class="pibDetail">
                        <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                            <h2 class="pibProdName mb-3">{{ $product->name }}</h2>
                        </a>

                        @if($reviewsCount > 0)
                        <div class="pibReview mb-2 d-flex align-items-center justify-content-center gap-2">
                            <div class="ratingBox">
                                <div class="ratedStars star{{ round($rating * 2) / 2 }}"></div>
                            </div>
                            <small class="bgGreen text-white lh-1 rounded-1 p-1">
                                {{ number_format($rating, 1) }}
                            </small>
                            <span>({{ $reviewsCount }} {{ $reviewsCount == 1 ? 'Review' : 'Reviews' }})</span>
                        </div>
                        @endif

                        <div class="pibPriceBox mb-0 d-flex flex-column justify-content-center align-items-center gap-1">
                            <strong class="pibProdPrice">₹{{ number_format($sellingPrice, 2) }}</strong>

                            @if($hasDiscount)
                            <span class="pibStikePrice text-decoration-line-through text-muted small">
                                ₹{{ number_format($mrp, 2) }}
                            </span>
                            @endif

                            @if($hasDiscount && $discount > 0)
                            <div class="pibDiscount txtgreen fw-semibold">
                                Save {{ $discount }}%
                            </div>
                            @endif
                        </div>

                        @if($soldCount > 0)
                        <div class="text-muted small mt-2">
                            Sold: {{ $soldCount }}
                        </div>
                        @endif
                    </div>

                    <div class="pibBtns mt-auto px-2 pt-4 d-flex justify-content-center gap-3">
                        @php
                            $inCart = in_array((int) $product->id, $cartProductIds ?? [], true);
                            $isOutOfStock = (int) ($product->quantity ?? 0) <= 0;
                        @endphp

                        @if ($isOutOfStock)
                            <button type="button"
                                class="pibBtn cartBtn js-out-of-stock-btn"
                                data-oos-message="Product is out of stock"
                                style="opacity: 0.7; cursor: pointer;">
                                <span class="btnTxt">Add to Cart</span>
                            </button>

                            <button type="button"
                                class="pibBtn buyBtn js-out-of-stock-btn"
                                data-oos-message="Product is out of stock"
                                style="opacity: 0.7; cursor: pointer; background-color: #6c757d; border-color: #6c757d;">
                                <span class="btnTxt">Buy Now</span>
                            </button>
                        @else
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
                        @endif
                    </div>

                </div>
            </div>

            @empty

            <div class="col-12 w-100 text-center py-5 mx-auto" style="flex: 0 0 100%; max-width: 100%;">
                <h4 class="mb-0">{{ ($filterAction ?? null) ? 'No discounted products found' : 'No products found in this category' }}</h4>
            </div>

            @endforelse

</div>

@if(method_exists($products, 'hasPages') && $products->hasPages())
<div class="custom-pagination product-listing-pagination">
    {{ $products->onEachSide(1)->links('frontend.partials.pagination') }}
</div>
@endif