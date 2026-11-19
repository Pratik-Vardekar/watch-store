@extends('layouts.front')
@section('content')
    <main class="pageWrapper">

        <section class="py-5 bgGray">
            <div class="container">

                <div class="row justify-content-between mb-5">
                    @auth
                        @include('frontend.partials.myaccount_nav', [
                            'active' => 'wishlist',
                            'user' => $user,
                        ])
                    @endauth

                    <div class="{{ auth()->check() ? 'col-lg-9' : 'col-12' }} mb-lg-4">

                        <h2 class="sectTitle lh-1 mb-4 d-inline-flex align-items-center gap-3">
                            My Wishlist
                            <small class="bagCount bgRed text-white rounded-pill py-1 px-2 fs-6" id="wishlistCount">
                                {{ $wishlistItems->count() }}
                            </small>
                        </h2>

                        <div id="wishlistMessage"></div>

                        @if ($wishlistItems->isNotEmpty())
                            <div class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2"
                                id="wishlistContainer">
                                @foreach ($wishlistItems as $item)
                                    @if ($item->product)
                                        @php
                                            $product = $item->product;
                                            $mrp = (float) ($product->price ?? 0);
                                            $sellingPrice = (float) ($product->selling_price ?? 0);
                                            if ($sellingPrice <= 0) {
                                                $sellingPrice = $mrp;
                                            }
                                            $hasDiscount = $mrp > 0 && $sellingPrice > 0 && $mrp > $sellingPrice;
                                            $discount = 0;
                                            if ($hasDiscount) {
                                                $discount = (int) ($product->discount_percent ?? ($product->discount ?? 0));
                                                if ($discount <= 0) {
                                                    $discount = (int) round((($mrp - $sellingPrice) / $mrp) * 100);
                                                }
                                            }
                                            $image = $product->getProductImageUrl($product->thumbnail);
                                            $isNew = $product->is_new ?? false;
                                            $avgRating = $product->reviews()->where('status', 1)->avg('rating') ?? 0;
                                            $reviewsCount = $product->reviews()->where('status', 1)->count();
                                        @endphp
                                        <div class="col wishlist-item" data-product-id="{{ $product->id }}"
                                            data-wishlist-id="{{ $item->id }}">
                                            <div class="productItem text-center animateThis fadeIn">
                                                <div class="pi_topBar d-flex justify-content-between">
                                                    <div class="col-auto">
                                                        @if ($isNew)
                                                            <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                                                        @endif
                                                        @if ($hasDiscount && $discount > 0)
                                                            <div class="pi_offer d-flex flex-column justify-content-center">
                                                                {{ $discount }}% <small>Off</small></div>
                                                        @endif
                                                    </div>
                                                    <div class="col-auto">
                                                        <button type="button" class="remove-wishlist-btn rounded-circle d-inline-flex align-items-center justify-content-center"
                                                            data-product-id="{{ $product->id }}" title="Remove from wishlist" aria-label="Remove"
                                                            style="position: relative; z-index: 10; width: 30px; height: 30px; background-color: #ffffff; color: #e40025; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 2px 5px rgba(0,0,0,0.12); cursor: pointer; transition: all 0.2s ease;"
                                                            onmouseover="this.style.color='#ffffff'; this.style.backgroundColor='#e40025'; this.style.borderColor='#e40025'; this.style.transform='scale(1.08)';"
                                                            onmouseout="this.style.color='#e40025'; this.style.backgroundColor='#ffffff'; this.style.borderColor='rgba(0,0,0,0.08)'; this.style.transform='scale(1)';">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none; display: block;">
                                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="pi_imgbox mb-4">
                                                    <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                                        <img src="{{ $image }}" alt="{{ $product->name }}"
                                                            class="pi_img">
                                                    </a>
                                                </div>
                                                <div class="pi_details">
                                                    <h2 class="pi_prodBrand fw-bold mb-2">{{ $product->brand->name ?? '' }}</h2>
                                                    <h3 class="pi_prodName mb-3" title="{{ $product->name }}">
                                                        {{ $product->name }}</h3>
                                                    <div
                                                        class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                                        <div class="ratingBox">
                                                            <div class="ratedStars star{{ round($avgRating * 2) / 2 }}"></div>
                                                        </div>
                                                        <div class="pi_reviewCount fw-semibold">({{ $reviewsCount }})</div>
                                                    </div>
                                                    <div class="pi_price">
                                                        @if ($hasDiscount)
                                                            <small class="pip_strike me-2">₹
                                                                {{ number_format($mrp, 2) }}</small>
                                                        @endif
                                                        <strong class="pip_ProdPrice fw-bolder">₹
                                                            {{ number_format($sellingPrice, 2) }}</strong>
                                                    </div>
                                                </div>
                                                <a href="{{ route('product.details', ['slug' => $product->slug]) }}"
                                                    class="stretched-link z-3" title="{{ $product->name }}"></a>
                                                <div class="pt-3 position-relative z-3">
                                                    @if ((int) ($product->quantity ?? 0) <= 0)
                                                        <button type="button" class="olBtn rounded mx-auto js-out-of-stock-btn"
                                                            data-oos-message="Product is out of stock"
                                                            style="opacity: 0.7; cursor: pointer;">
                                                            Out of Stock
                                                        </button>
                                                    @else
                                                        <button type="button" class="olBtn rounded mx-auto add-to-cart-btn"
                                                            data-product-id="{{ $product->id }}"
                                                            data-add-url="{{ route('cart.add', $product->id) }}">
                                                            Add to Cart
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-12 d-none" id="emptyWishlist">
                                <div class="rounded-3 p-5 text-center bg-white shadow-sm">
                                    <h5 class="mb-2">Your wishlist is empty</h5>
                                    <p class="text-secondary mb-4">Save watches you love and shop them later.</p>
                                    <a href="{{ route('product.listing') }}" class="ctaBtn">
                                        <span class="btnTxt">Browse Watches</span>
                                        <span class="arw"></span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="col-12" id="emptyWishlist">
                                <div class="rounded-3 p-5 text-center bg-white shadow-sm">
                                    <h5 class="mb-2">Your wishlist is empty</h5>
                                    <p class="text-secondary mb-4">Save watches you love and shop them later.</p>
                                    <a href="{{ route('product.listing') }}" class="ctaBtn">
                                        <span class="btnTxt">Browse Watches</span>
                                        <span class="arw"></span>
                                    </a>
                                </div>
                            </div>
                        @endif
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

            // ===== REMOVE FROM WISHLIST (AJAX - DIRECT REMOVAL NO RELOAD) =====
            document.querySelectorAll('.remove-wishlist-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var productId = this.getAttribute('data-product-id') || btn.getAttribute('data-product-id');
                    var itemElement = this.closest('.wishlist-item') || btn.closest('.wishlist-item');

                    if (!productId || !itemElement) return;

                    // Immediate visual feedback
                    this.style.opacity = '0.5';
                    this.style.pointerEvents = 'none';

                    var removeUrl = '{{ route('wishlist.remove', ':id') }}'.replace(':id',
                        productId);

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
                            if (data.status || data.success) {
                                // Animate and directly remove item
                                itemElement.style.transition = 'all 0.3s ease';
                                itemElement.style.opacity = '0';
                                itemElement.style.transform = 'scale(0.8)';

                                setTimeout(function() {
                                    itemElement.remove();

                                    var remainingItems = document.querySelectorAll(
                                        '.wishlist-item').length;
                                    var newCount = data.wishlist_count !== undefined ?
                                        data.wishlist_count : remainingItems;

                                    // Update page count
                                    var countEl = document.getElementById(
                                        'wishlistCount');
                                    if (countEl) {
                                        countEl.textContent = newCount;
                                    }

                                    // Update header badges
                                    if (typeof updateWishlistBadge === 'function') {
                                        updateWishlistBadge(newCount);
                                    }
                                    document.querySelectorAll(
                                        '.wishlist-count, .wishlistCount').forEach(
                                        function(el) {
                                            el.textContent = newCount;
                                        });

                                    // Show empty state if no items left
                                    var container = document.getElementById('wishlistContainer');
                                    var emptyEl = document.getElementById('emptyWishlist');
                                    if (remainingItems === 0) {
                                        if (container) {
                                            container.classList.add('d-none');
                                        }
                                        if (emptyEl) {
                                            emptyEl.classList.remove('d-none');
                                        } else if (container) {
                                            container.classList.remove('d-none');
                                            container.innerHTML = `
                                                <div class="col-12" id="emptyWishlist">
                                                    <div class="rounded-3 p-5 text-center bg-white shadow-sm">
                                                        <h5 class="mb-2">Your wishlist is empty</h5>
                                                        <p class="text-secondary mb-4">Save watches you love and shop them later.</p>
                                                        <a href="{{ route('product.listing') }}" class="ctaBtn">
                                                            <span class="btnTxt">Browse Watches</span>
                                                            <span class="arw"></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            `;
                                        }
                                    }

                                    showMessage(data.message ||
                                        'Item removed from wishlist.', 'success');
                                }, 250);
                            } else {
                                btn.style.opacity = '1';
                                btn.style.pointerEvents = 'auto';
                                showMessage(data.message || 'Failed to remove item.', 'danger');
                            }
                        })
                        .catch(function(error) {
                            btn.style.opacity = '1';
                            btn.style.pointerEvents = 'auto';
                            showMessage('Something went wrong. Please try again.', 'danger');
                            console.error('Error:', error);
                        });
                });
            });

            // ===== ADD TO CART (AJAX - NO PAGE RELOAD) =====
            document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var btnElement = this;
                    if (btnElement.getAttribute('data-busy') === '1') {
                        return false;
                    }

                    var productId = this.getAttribute('data-product-id');
                    var addUrl = this.getAttribute('data-add-url');
                    var originalText = 'Add to Cart';

                    if (!productId || !addUrl) return;

                    btnElement.setAttribute('data-busy', '1');
                    btnElement.disabled = true;
                    btnElement.style.pointerEvents = 'none';
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
                            if (data.status || data.success) {
                                var msg = data.message || 'Product added to cart successfully';

                                // Update cart count badge in header
                                var newCount = data.cart_count !== undefined ? data.cart_count :
                                    1;
                                if (typeof window.updateCartBadge === 'function') {
                                    window.updateCartBadge(newCount);
                                } else {
                                    document.querySelectorAll('.cart-count, .cartCount')
                                        .forEach(function(el) {
                                            el.textContent = newCount;
                                            if (parseInt(newCount, 10) > 0) {
                                                el.style.display = 'flex';
                                            } else {
                                                el.style.display = 'none';
                                            }
                                        });
                                }

                                if (data.already_in_cart) {
                                    showMessage(msg, 'info');
                                    if (typeof window.showToast === 'function') {
                                        window.showToast(msg, 'warning');
                                    }
                                    btnElement.textContent = 'Already in Cart';
                                } else {
                                    showMessage(msg, 'success');
                                    if (typeof window.showToast === 'function') {
                                        window.showToast(msg, 'success');
                                    }
                                    btnElement.textContent = 'Added ✓';
                                    btnElement.style.background = '#28a745';
                                    btnElement.style.borderColor = '#28a745';
                                    btnElement.style.color = '#fff';
                                }

                                // Reset button back to "Add to Cart" after 2 seconds
                                setTimeout(function() {
                                    btnElement.textContent = originalText;
                                    btnElement.style.background = '';
                                    btnElement.style.borderColor = '';
                                    btnElement.style.color = '';
                                    btnElement.disabled = false;
                                    btnElement.style.pointerEvents = 'auto';
                                    btnElement.removeAttribute('data-busy');
                                }, 2000);
                            } else {
                                btnElement.textContent = originalText;
                                btnElement.disabled = false;
                                btnElement.style.pointerEvents = 'auto';
                                btnElement.removeAttribute('data-busy');
                                showMessage(data.message || 'Failed to add to cart.', 'danger');
                                if (typeof window.showToast === 'function') {
                                    window.showToast(data.message || 'Failed to add to cart.',
                                        'error');
                                }
                            }
                        })
                        .catch(function(error) {
                            btnElement.disabled = false;
                            btnElement.style.pointerEvents = 'auto';
                            btnElement.removeAttribute('data-busy');
                            btnElement.textContent = originalText;
                            showMessage('Something went wrong. Please try again.', 'danger');
                            console.error('Error:', error);
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
