<div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 gx-4 gy-5 mb-5 productResult">

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
            $soldCount = $product->sold_count ?? 0;
        @endphp

        <div class="col">
            <div class="prodItemBox text-center d-flex flex-column h-100">

                <div class="pibImgBox mx-auto mb-4">
                    <a href="{{ route('product_details', $product->slug) }}">
                        <img src="{{ $image }}" class="pibProdImg" alt="{{ $product->name }}">
                    </a>

                    @include('frontend.partials.product_labels', [
                        'product' => $product,
                        'discount' => $discount,
                        'discountOnly' => $showDiscountTagOnly ?? false,
                    ])
                </div>

                <div class="pibDetail">
                    <a href="{{ route('product_details', $product->slug) }}">
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

                    @if($soldCount > 0)
                        <div class="text-muted small mt-2">
                            Sold: {{ $soldCount }}
                        </div>
                    @endif
                </div>

                <div class="pibBtns mt-auto px-2 pt-4 d-flex justify-content-center gap-3">
                    @php $inCart = in_array((int) $product->id, $cartProductIds ?? [], true); @endphp
                    <button type="button"
                        class="pibBtn cartBtn js-add-to-cart{{ $inCart ? ' is-in-cart' : '' }}"
                        data-product-id="{{ $product->id }}"
                        @if($inCart) data-in-cart="1" @endif>
                        <span class="btnTxt">{{ $inCart ? 'Already Added' : 'Add to Cart' }}</span>
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

        <div class="col-12 w-100 text-center py-5 mx-auto" style="flex: 0 0 100%; max-width: 100%;">
            <h4 class="mb-0">{{ ($filterAction ?? null) ? 'No discounted products found' : 'No products found in this category' }}</h4>
        </div>

    @endforelse

</div>

@if($products->isNotEmpty())
<div class="d-flex justify-content-center product-listing-pagination">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
@endif
