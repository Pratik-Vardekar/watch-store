{{-- Product card (home slider + listing grid) --}}
@php
    $mrp = (float) ($product->price ?? 0);
    $sellingPrice = (float) ($product->selling_price ?? 0);
    $storedDiscount = (int) ($product->discount_percent ?? ($product->discount ?? 0));

    // If selling price is 0 or equal/greater than MRP but discount is set, calculate selling price
    if (($sellingPrice <= 0 || $sellingPrice >= $mrp) && $storedDiscount > 0 && $mrp > 0) {
        $sellingPrice = max(0, round($mrp - ($mrp * ($storedDiscount / 100)), 2));
    }

    // If selling price is 0, fallback to MRP
    if ($sellingPrice <= 0) {
        $sellingPrice = $mrp;
    }

    $hasDiscount = ($mrp > 0 && $sellingPrice > 0 && $mrp > $sellingPrice) || ($storedDiscount > 0 && $mrp > 0);
    $discount = 0;
    if ($hasDiscount) {
        $discount = $storedDiscount > 0 ? $storedDiscount : (int) round((($mrp - $sellingPrice) / $mrp) * 100);
    }

    $image = $product->thumbnail
        ? asset('backend/assets/images/product/' . $product->thumbnail)
        : asset('frontend/assets/images/product_1.jpg');

    $brandLabel = optional($product->brand)->name ?? (optional($product->category)->name ?? 'Watch');
    $rating = (float) ($product->rating ?? 0);
    $reviewsCount = (int) ($product->reviews_count ?? 0);

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

    $showNewBadge = $showNewBadge ?? (bool) ($product->is_new_arrival ?? false);
    $detailsUrl = route('product.details', ['slug' => $product->slug]);
    $wrapperClass = $wrapperClass ?? 'swiper-slide';
    $itemClass = $itemClass ?? 'productItem text-center animateThis fadeIn';
@endphp

<div class="{{ $wrapperClass }}">
    <div class="{{ $itemClass }}">
        <div class="pi_topBar d-flex justify-content-between">
            <div class="col-auto">
                @if ((int) ($product->quantity ?? 0) <= 0)
                    <div class="pi_newBadge bg-danger text-white fw-medium mb-2 px-2 py-1 rounded" style="font-size: 0.72rem;">Out of Stock</div>
                @elseif ($showNewBadge)
                    <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                @endif
                @if ($hasDiscount && $discount > 0)
                    <div class="pi_offer d-flex flex-column justify-content-center">{{ $discount }}%
                        <small>Off</small></div>
                @endif
            </div>
            <div class="col-auto">
                @php
                    $wishlistIds = $wishlistIds ?? [];
                    $isWishlisted = in_array((int) $product->id, $wishlistIds, true);
                @endphp
                <button type="button" class="wishlistBtn js-wishlist-toggle {{ $isWishlisted ? 'saved' : '' }}"
                    title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
                    data-product-id="{{ $product->id }}" data-toggle-url="{{ route('wishlist.toggle', $product->id) }}"
                    aria-label="Wishlist"></button>
            </div>
        </div>
        <div class="pi_imgbox mb-4">
            <img src="{{ $image }}" alt="{{ $product->name }}" class="pi_img">
        </div>
        <div class="pi_details">
            <h2 class="pi_prodBrand fw-bold mb-2">{{ $brandLabel }}</h2>
            <h3 class="pi_prodName {{ !empty($product->model_no) ? 'mb-1' : 'mb-3' }}" title="{{ $product->name }}">{{ $product->name }}</h3>
            @if (!empty($product->model_no))
                <div class="pi_prodModel text-muted mb-2" style="font-size: 0.78rem;">
                    Model: <span class="fw-medium text-dark">{{ $product->model_no }}</span>
                </div>
            @endif
            @if ($reviewsCount > 0)
                <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                    <div class="ratingBox">
                        <div class="ratedStars {{ $ratingClass }}"></div>
                    </div>
                    <div class="pi_reviewCount fw-semibold">({{ $reviewsCount }})</div>
                </div>
            @endif
            <div class="pi_price">
                @if ($hasDiscount && $sellingPrice < $mrp)
                    <small class="pip_strike me-2">₹ {{ number_format($mrp, 2) }}</small>
                @endif
                <strong class="pip_ProdPrice fw-bolder">₹ {{ number_format($sellingPrice, 2) }}</strong>
            </div>
        </div>
        <a href="{{ $detailsUrl }}" class="stretched-link z-3" title="{{ $product->name }}"></a>
    </div>
</div>
