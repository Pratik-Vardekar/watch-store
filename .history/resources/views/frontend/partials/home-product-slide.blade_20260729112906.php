{{-- Homepage product card (Bestsellers / New Arrivals) --}}
@php
    $sellingPrice = (float) ($product->selling_price ?? $product->price ?? 0);
    $originalPrice = (float) ($product->price ?? 0);
    $discount = (int) ($product->discount_percent ?? $product->discount ?? 0);
    if ($discount <= 0 && $originalPrice > $sellingPrice && $originalPrice > 0) {
        $discount = (int) round((($originalPrice - $sellingPrice) / $originalPrice) * 100);
    }

    $image = $product->thumbnail
        ? asset('backend/assets/images/product/' . $product->thumbnail)
        : asset('frontend/assets/images/product_1.jpg');

    $brandLabel = optional($product->brand)->name
        ?? optional($product->category)->name
        ?? 'Watch';
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
@endphp

<div class="swiper-slide">
    <div class="productItem text-center animateThis fadeIn">
        <div class="pi_topBar d-flex justify-content-between">
            <div class="col-auto">
                @if($showNewBadge)
                    <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                @endif
                @if($discount > 0)
                    <div class="pi_offer d-flex flex-column justify-content-center">{{ $discount }}% <small>Off</small></div>
                @endif
            </div>
            <div class="col-auto">
                <button type="button" class="wishlistBtn" title="Add to Wishlist"></button>
            </div>
        </div>
        <div class="pi_imgbox mb-4">
            <img src="{{ $image }}" alt="{{ $product->name }}" class="pi_img">
        </div>
        <div class="pi_details">
            <h2 class="pi_prodBrand fw-bold mb-2">{{ $brandLabel }}</h2>
            <h3 class="pi_prodName mb-3" title="{{ $product->name }}">{{ $product->name }}</h3>
            <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                <div class="ratingBox"> <div class="ratedStars {{ $ratingClass }}"></div> </div>
                <div class="pi_reviewCount fw-semibold">({{ $reviewsCount }})</div>
            </div>
            <div class="pi_price">
                @if($originalPrice > $sellingPrice)
                    <small class="pip_strike me-2">₹ {{ number_format($originalPrice, 2) }}</small>
                @endif
                <strong class="pip_ProdPrice fw-bolder">₹ {{ number_format($sellingPrice, 2) }}</strong>
            </div>
        </div>
        <a href="{{ $detailsUrl }}" class="stretched-link z-3" title="{{ $product->name }}"></a>
    </div>
</div>
