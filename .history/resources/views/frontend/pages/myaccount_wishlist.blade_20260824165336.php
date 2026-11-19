@extends('layouts.front')
@section('content')
<main class="pageWrapper">

    <section class="py-5 bgGray">
        <div class="container">

            <div class="row justify-content-between mb-5">
                @auth
                    @include('frontend.partials.myaccount_nav', ['active' => 'wishlist', 'user' => $user])
                @endauth

                <div class="{{ auth()->check() ? 'col-lg-9' : 'col-12' }} mb-lg-4">

                    <h2 class="sectTitle lh-1 mb-4 d-inline-flex align-items-center gap-3">
                        My Wishlist
                        <small class="bagCount bgRed text-white rounded-pill py-1 px-2 fs-6" id="wishlistCount">
                            {{ $wishlistItems->count() }} {{ $wishlistItems->count() === 1 ? 'item' : 'items' }}
                        </small>
                    </h2>

                    <div id="wishlistMessage"></div>

                    <div class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2" id="wishlistContainer">
                        @forelse($wishlistItems as $item)
                            @if($item->product)
                            @php
                                $product = $item->product;
                                $sellingPrice = (float) ($product->selling_price ?? $product->price ?? 0);
                                $originalPrice = (float) ($product->price ?? 0);
                                $discount = (int) ($product->discount_percent ?? $product->discount ?? 0);
                                if ($discount <= 0 && $originalPrice > $sellingPrice && $originalPrice > 0) {
                                    $discount = (int) round((($originalPrice - $sellingPrice) / $originalPrice) * 100);
                                }
                                $image = $product->getProductImageUrl($product->thumbnail);
                                $isNew = $product->is_new ?? false;
                                $avgRating = $product->reviews()->where('status', 1)->avg('rating') ?? 0;
                                $reviewsCount = $product->reviews()->where('status', 1)->count();
                            @endphp
                            <div class="col wishlist-item" data-product-id="{{ $product->id }}" data-wishlist-id="{{ $item->id }}">
                                <div class="productItem text-center animateThis fadeIn">
                                    <div class="pi_topBar d-flex justify-content-between">
                                        <div class="col-auto">
                                            @if($isNew)
                                                <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                                            @endif
                                            @if($discount > 0)
                                                <div class="pi_offer d-flex flex-column justify-content-center">{{ $discount }}% <small>Off</small></div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="wishlistBtn saved remove-wishlist-btn" data-product-id="{{ $product->id }}" title="Remove"></button>
                                        </div>
                                    </div>
                                    <div class="pi_imgbox mb-4">
                                        <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                            <img src="{{ $image }}" alt="{{ $product->name }}" class="pi_img">
                                        </a>
                                    </div>
                                    <div class="pi_details">
                                        <h2 class="pi_prodBrand fw-bold mb-2">{{ $product->brand->name ?? '' }}</h2>
                                        <h3 class="pi_prodName mb-3" title="{{ $product->name }}">{{ $product->name }}</h3>
                                        <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                            <div class="ratingBox">
                                                <div class="ratedStars star{{ round($avgRating * 2) / 2 }}"></div>
                                            </div>
                                            <div class="pi_reviewCount fw-semibold">({{ $reviewsCount }})</div>
                                        </div>
                                        <div class="pi_price">
                                            @if($originalPrice > $sellingPrice)
                                                <small class="pip_strike me-2">₹ {{ number_format($originalPrice, 2) }}</small>
                                            @endif
                                            <strong class="pip_ProdPrice fw-bolder">₹ {{ number_format($sellingPrice, 2) }}</strong>
                                        </div>
                                    </div>
                                    <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="stretched-link z-3" title="{{ $product->name }}"></a>
                                    <div class="pt-3 position-relative z-3">
                                        <button type="button" class="olBtn rounded mx-auto add-to-cart-btn" data-product-id="{{ $product->id }}" data-add-url="{{ route('cart.add', $product->id) }}">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                        <div class="col-12" id="emptyWishlist">
                            <div class="rounded-3 p-5 text-center bg-white">
                                <h5 class="mb-2">Your wishlist is empty</h5>
                                <p class="text-secondary mb-4">Save watches you love and shop them later.</p>
                                <a href="{{ route('product.listing') }}" class="ctaBtn">
                                    <span class="btnTxt">Browse Watches</span>
                                    <span class="arw"></span>
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </section>

</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // ===== REMOVE FROM WISHLIST (AJAX) =====
    document.querySelectorAll('.remove-wishlist-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var productId = this.getAttribute('data-product-id');
            var itemElement = this.closest('.wishlist-item');
            var messageContainer = document.getElementById('wishlistMessage');

            if (!productId || !itemElement) return;

            if (!confirm('Remove this item from wishlist?')) return;

            var removeUrl = '{{ route("wishlist.remove", "") }}/' + productId;

            fetch(removeUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.status) {
                    // Remove the item from DOM
                    itemElement.remove();

                    // Update wishlist count
                    var countEl = document.getElementById('wishlistCount');
                    var remainingItems = document.querySelectorAll('.wishlist-item').length;

                    if (countEl) {
                        countEl.textContent = remainingItems + ' ' + (remainingItems === 1 ? 'item' : 'items');
                    }

                    // Show empty message if no items left
                    var container = document.getElementById('wishlistContainer');
                    if (remainingItems === 0 && container) {
                        var emptyHtml = `
                            <div class="col-12" id="emptyWishlist">
                                <div class="rounded-3 p-5 text-center bg-white">
                                    <h5 class="mb-2">Your wishlist is empty</h5>
                                    <p class="text-secondary mb-4">Save watches you love and shop them later.</p>
                                    <a href="{{ route('product.listing') }}" class="ctaBtn">
                                        <span class="btnTxt">Browse Watches</span>
                                        <span class="arw"></span>
                                    </a>
                                </div>
                            </div>
                        `;
                        container.innerHTML = emptyHtml;
                    }

                    showMessage(data.message || 'Item removed from wishlist.', 'success');
                } else {
                    showMessage(data.message || 'Failed to remove item.', 'danger');
                }
            })
            .catch(function() {
                showMessage('Something went wrong. Please try again.', 'danger');
            });
        });
    });

    // ===== ADD TO CART (AJAX) =====
    document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var productId = this.getAttribute('data-product-id');
            var addUrl = this.getAttribute('data-add-url');
            var btnElement = this;

            if (!productId || !addUrl) return;

            // Disable button and show loading state
            btnElement.disabled = true;
            btnElement.textContent = 'Adding...';

            fetch(addUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                btnElement.disabled = false;
                btnElement.textContent = 'Add to Cart';

                if (data.status) {
                    showMessage(data.message || 'Added to cart successfully!', 'success');

                    // Update cart count if badge exists
                    var cartBadge = document.querySelector('.cartCount, .hnbBadge');
                    if (cartBadge && data.cart_count !== undefined) {
                        cartBadge.textContent = data.cart_count;
                    }

                    // Change button text temporarily
                    btnElement.textContent = 'Added ✓';

                    setTimeout(function() {
                        btnElement.textContent = 'Add to Cart';
                    }, 2000);
                } else {
                    showMessage(data.message || 'Failed to add to cart.', 'danger');
                }
            })
            .catch(function() {
                btnElement.disabled = false;
                btnElement.textContent = 'Add to Cart';
                showMessage('Something went wrong. Please try again.', 'danger');
            });
        });
    });

    // ===== SHOW MESSAGE FUNCTION =====
    function showMessage(message, type) {
        var messageContainer = document.getElementById('wishlistMessage');
        if (!messageContainer) return;

        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon = type === 'success' ? '✓' : '✕';

        messageContainer.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <span>${icon}</span>
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        // Auto dismiss after 5 seconds
        setTimeout(function() {
            var alert = messageContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(function() {
                    messageContainer.innerHTML = '';
                }, 300);
            }
        }, 5000);
    }

});
</script>
@endpush