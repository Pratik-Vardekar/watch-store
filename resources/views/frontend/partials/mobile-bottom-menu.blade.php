@php
    $isHomeActive = request()->routeIs('home');
    $isShopActive = request()->routeIs(
        'product.listing',
        'category.show',
        'brands',
        'brand.products',
        'product.details',
        'explore',
        'super.deals'
    );
    $isWishlistActive = request()->routeIs('wishlist');
    $isCartActive = request()->routeIs('cart', 'checkout.*');
    $isAccountActive = request()->routeIs('profile.*', 'frontend.order.details', 'login', 'register');

    $mobileWishlistCount = (int) ($wishlistCount ?? 0);
    $mobileCartCount = (int) ($cartCount ?? 0);
@endphp

<nav class="mobileBottomNav d-lg-none" aria-label="Mobile Bottom Navigation">
    <div class="mobileBottomNavInner">
        <a href="{{ route('home') }}"
            class="mobileBottomNavItem {{ $isHomeActive ? 'is-active' : '' }}"
            aria-label="Home">
            <span class="mobileBottomNavIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 10.5L12 3l9 7.5" />
                    <path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5" />
                </svg>
            </span>
            <span class="mobileBottomNavLabel">Home</span>
        </a>

        <a href="{{ route('product.listing') }}"
            class="mobileBottomNavItem {{ $isShopActive ? 'is-active' : '' }}"
            aria-label="Shop">
            <span class="mobileBottomNavIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5" />
                    <rect x="14" y="3" width="7" height="7" rx="1.5" />
                    <rect x="14" y="14" width="7" height="7" rx="1.5" />
                    <rect x="3" y="14" width="7" height="7" rx="1.5" />
                </svg>
            </span>
            <span class="mobileBottomNavLabel">Shop</span>
        </a>

        <a href="{{ route('wishlist') }}"
            class="mobileBottomNavItem {{ $isWishlistActive ? 'is-active' : '' }}"
            aria-label="Wishlist">
            <span class="mobileBottomNavIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
                <span class="mobileBottomNavBadge wishlist-count"
                    @if ($mobileWishlistCount <= 0) style="display:none;" @endif>{{ $mobileWishlistCount }}</span>
            </span>
            <span class="mobileBottomNavLabel">Wishlist</span>
        </a>

        <a href="{{ route('cart') }}"
            class="mobileBottomNavItem {{ $isCartActive ? 'is-active' : '' }}"
            aria-label="My Bag">
            <span class="mobileBottomNavIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
                <span class="mobileBottomNavBadge cart-count"
                    @if ($mobileCartCount <= 0) style="display:none;" @endif>{{ $mobileCartCount }}</span>
            </span>
            <span class="mobileBottomNavLabel">Bag</span>
        </a>

        <a href="@auth{{ route('profile.profile') }}@else{{ route('login') }}@endauth"
            class="mobileBottomNavItem {{ $isAccountActive ? 'is-active' : '' }}"
            aria-label="Account">
            <span class="mobileBottomNavIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            </span>
            <span class="mobileBottomNavLabel">{{ auth()->check() ? 'Account' : 'Login' }}</span>
        </a>
    </div>
</nav>
