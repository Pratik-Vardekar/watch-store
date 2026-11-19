<header class="pageHeader">

    <div class="headerMain px-lg-5 px-sm-4 px-3 py-2 py-xxl-3">
 
        <div class="row align-items-center justify-content-between">
            <div class="col-auto order-lg-1">
                <a href="{{route('home')}}">
                    <img src="{{ asset('frontend/assets/images/tws_logo.png') }}" title="The Watch Store - Index Page" width="245" height="140" class="pageLogo">
                </a>
            </div>
 
            <div class="col-auto order-lg-3"> 
                <div class="row gx-xl-3 gx-lg-2 gx-sm-3 gx-1 siteMenu">
                    <div class="col">
                        <button class="siteMenuBtn searchMenuBtn" type="button" data-bs-toggle="collapse" data-bs-target="#searchWrapper" aria-expanded="false">
                            <img src="{{ asset('frontend/assets/images/icons/loupe.svg') }}" alt="Search">
                        </button>
                    </div>
                    <div class="col">
                        @auth
                            <div class="profileMenuWrap">
                                <a href="{{ route('profile.profile') }}" class="siteMenuBtn" title="My Profile">
                                    <img src="{{ asset('frontend/assets/images/icons/user.svg') }}" alt="My Profile">
                                </a>
                                <div class="myAccountWrap profileHoverMenu">
                                    <ul class="list-unstyled mb-0 p-3 d-flex flex-column gap-2">
                                        <li>
                                            <a href="{{ route('profile.profile') }}" class="profileHoverLink">Profile</a>
                                        </li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="profileHoverLogout">Logout</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="siteMenuBtn"><img src="{{ asset('frontend/assets/images/icons/user.svg') }}" alt="Login/Signup"></a>
                        @endauth
                    </div>
                    <div class="col">
                        <a href="{{ route('wishlist') }}" class="siteMenuBtn" title="Wishlist">
                            <img src="{{ asset('frontend/assets/images/icons/heart.svg') }}" alt="Wishlist">
                            <span class="badge wishlist-count hnbBadge" @if((int)($wishlistCount ?? 0) <= 0) style="display:none;" @endif>{{ (int)($wishlistCount ?? 0) }}</span>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('cart') }}" class="siteMenuBtn" title="My Bag">
                            <img src="{{ asset('frontend/assets/images/icons/bag.svg') }}" alt="My Bag">
                            <span class="badge cart-count hnbBadge" @if((int)($cartCount ?? 0) <= 0) style="display:none;" @endif>{{ (int)($cartCount ?? 0) }}</span>
                        </a>
                    </div>
                    <div class="col d-lg-none">
                        <button class="siteMenuBtn menuBtn"><img src="{{ asset('frontend/assets/images/icons/menu.svg') }}" alt="Menu"></button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg align-self-lg-stretch order-lg-2 siteNaviMain">
                <!-- Navigation Menu -->
                <div class="NavigHolder d-flex flex-column flex-lg-row h-100 justify-content-lg-center align-items-lg-stretch">
                    <div class="naviActionBar d-flex justify-content-between align-items-center">
                        <div class="col"> <button class="menuBackBtn">Back</button> </div>
                        <div class="col-auto"> <button class="menuBtn menuCloseBtn">&times;</button> </div>
                    </div>
                    <ul class="siteNavi siteNavi_L1">
                        @php
                            $menuAllCategories = \App\Models\Category::where('status', 1)
                                ->orderBy('sort_order')
                                ->orderBy('id')
                                ->get();
 
                            $menuWomenCategories = \App\Models\Category::where('status', 1)
                                ->forMenuGender('women')
                                ->orderBy('sort_order')
                                ->orderBy('id')
                                ->get();
 
                            $menuMenCategories = \App\Models\Category::where('status', 1)
                                ->forMenuGender('men')
                                ->orderBy('sort_order')
                                ->orderBy('id')
                                ->get();
 
                            $menuWomenStyleCategories = $menuWomenCategories->isNotEmpty()
                                ? $menuWomenCategories
                                : $menuAllCategories;
 
                            $menuMenStyleCategories = $menuMenCategories->isNotEmpty()
                                ? $menuMenCategories
                                : $menuAllCategories;
 
                            $headerMenuSetting = null;
                            try {
                                if (\Illuminate\Support\Facades\Schema::hasTable('header_menu_settings')) {
                                    $headerMenuSetting = \App\Models\HeaderMenuSetting::first();
                                }
                            } catch (\Throwable $e) {
                                $headerMenuSetting = null;
                            }
                        @endphp
                        <li><a href="{{ route('product.listing') }}">Brands</a>
                            <div class="siteNavi_L2 p-lg-5">
                                <div class="row">
                                    <div class="col-lg-4 pe-xxl-5 pe-xl-4">
                                        <a href="{{ route('product.listing') }}" class="menuImgBox">
                                            <img src="{{ $headerMenuSetting?->brands_image ? asset($headerMenuSetting->brands_image) : asset('frontend/assets/images/menuImg_brands.jpg') }}" class="w-100" alt="Exclusive Brands">
                                            <div class="mibText p-sm-4 p-3">
                                                <h2 class="mibHead titleFont">Exclusive Brands</h2>
                                                <span class="mibSubtext">Shop Now</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-8">
                                        <h3 class="csmHead">Featured Brands</h3>
                                        <div class="menubrandList row g-0 row-cols-xxl-6 row-cols-xl-5 row-cols-lg-4 row-cols-2">
                                            @php
                                                $menuBrands = \App\Models\Brand::where('status', 1)
                                                    ->orderBy('position')
                                                    ->get();
                                            @endphp
                                            @foreach($menuBrands as $brand)
                                                <div class="col">
                                                    <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" title="{{ $brand->name }}">
                                                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li><a href="{{ route('product.listing', ['gender' => 'women']) }}">Women</a>
                            <div class="siteNavi_L2 p-lg-5">
                                <div class="row">
                                    <div class="col-lg-4 pe-xxl-5 pe-xl-4">
                                        @php
                                            $womenMenuImage = $headerMenuSetting?->women_image
                                                ? asset($headerMenuSetting->women_image)
                                                : ($menuWomenCategories->firstWhere('image')?->image_url ?? asset('frontend/assets/images/menuImg_women.jpg'));
                                        @endphp
                                        <a href="{{ route('product.listing', ['gender' => 'women']) }}" class="menuImgBox">
                                            <img src="{{ $womenMenuImage }}" class="w-100" alt="Womens Collection">
                                            <div class="mibText p-sm-4 p-3">
                                                <h2 class="mibHead titleFont">Womens Collection</h2>
                                                <span class="mibSubtext">Shop Now</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row row-cols-lg-4 row-cols-1 subcatWrap">
                                            <div class="col catSubMenu">
                                                <h3 class="csmHead">Explore</h3>
                                                <ul class="csmList">
                                                    <li><a href="{{ route('product.listing', ['gender' => 'women']) }}">All Women Watches</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'women', 'filter' => 'best_selling']) }}">Best Sellers</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'women', 'filter' => 'new_arrival']) }}">New Arrivals</a></li>
                                                </ul>
                                            </div>
                                            <div class="col catSubMenu">
                                                <h3 class="csmHead">By Price</h3>
                                                <ul class="csmList">
                                                    <li><a href="{{ route('product.listing', ['gender' => 'women', 'max_price' => 5000]) }}">Under ₹5 K</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'women', 'min_price' => 5001, 'max_price' => 10000]) }}">Upto ₹10 K</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'women', 'min_price' => 10001, 'max_price' => 25000]) }}">Upto ₹25 k</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'women', 'min_price' => 25001, 'max_price' => 50000]) }}">Upto ₹50 K</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'women', 'min_price' => 50001, 'max_price' => 100000]) }}">Upto ₹1 L</a></li>
                                                </ul>
                                            </div>
                                            <div class="col catSubMenu">
                                                <h3 class="csmHead">Style Statement</h3>
                                                <ul class="csmList">
                                                    @php
                                                        $displayCategories = $menuWomenStyleCategories->take(4);
                                                    @endphp
                                                    @forelse($displayCategories as $menuCategory)
                                                        <li>
                                                            <a href="{{ route('product.listing', ['gender' => 'women', 'category' => $menuCategory->id]) }}">
                                                                {{ $menuCategory->name }}
                                                            </a>
                                                        </li>
                                                    @empty
                                                        <li><span class="text-muted">Add categories from admin</span></li>
                                                    @endforelse
                                                    @if($menuWomenStyleCategories->count() > 7)
                                                        <li>
                                                            <a href="{{ route('product.listing', ['gender' => 'women']) }}" class="fw-semibold text-primary">
                                                                View All
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li><a href="{{ route('product.listing', ['gender' => 'men']) }}">Men</a>
                            <div class="siteNavi_L2 p-lg-5">
                                <div class="row">
                                    <div class="col-lg-4 pe-xxl-5 pe-xl-4">
                                        @php
                                            $menMenuImage = $headerMenuSetting?->men_image
                                                ? asset($headerMenuSetting->men_image)
                                                : ($menuMenCategories->firstWhere('image')?->image_url ?? asset('frontend/assets/images/menuImg_men.jpg'));
                                        @endphp
                                        <a href="{{ route('product.listing', ['gender' => 'men']) }}" class="menuImgBox">
                                            <img src="{{ $menMenuImage }}" class="w-100" alt="Mens Collection">
                                            <div class="mibText p-sm-4 p-3">
                                                <h2 class="mibHead titleFont">Mens Collection</h2>
                                                <span class="mibSubtext">Shop Now</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row row-cols-lg-4 row-cols-1 subcatWrap">
                                            <div class="col catSubMenu">
                                                <h3 class="csmHead">Explore</h3>
                                                <ul class="csmList">
                                                    <li><a href="{{ route('product.listing', ['gender' => 'men']) }}">All Men Watches</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'men', 'filter' => 'best_selling']) }}">Best Sellers</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'men', 'filter' => 'new_arrival']) }}">New Arrivals</a></li>
                                                </ul>
                                            </div>
                                            <div class="col catSubMenu">
                                                <h3 class="csmHead">By Price</h3>
                                                <ul class="csmList">
                                                    <li><a href="{{ route('product.listing', ['gender' => 'men', 'max_price' => 5000]) }}">Under ₹5 K</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'men', 'min_price' => 5001, 'max_price' => 10000]) }}">Upto ₹10 K</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'men', 'min_price' => 10001, 'max_price' => 25000]) }}">Upto ₹25 k</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'men', 'min_price' => 25001, 'max_price' => 50000]) }}">Upto ₹50 K</a></li>
                                                    <li><a href="{{ route('product.listing', ['gender' => 'men', 'min_price' => 50001, 'max_price' => 100000]) }}">Upto ₹1 L</a></li>
                                                </ul>
                                            </div>
                                            <div class="col catSubMenu">
                                                <h3 class="csmHead">Style Statement</h3>
                                                <ul class="csmList">
                                                    @php
                                                        $displayMenCategories = $menuMenStyleCategories->take(7);
                                                    @endphp
                                                    @forelse($displayMenCategories as $menuCategory)
                                                        <li>
                                                            <a href="{{ route('product.listing', ['gender' => 'men', 'category' => $menuCategory->id]) }}">
                                                                {{ $menuCategory->name }}
                                                            </a>
                                                        </li>
                                                    @empty
                                                        <li><span class="text-muted">Add categories from admin</span></li>
                                                    @endforelse
                                                    @if($menuMenStyleCategories->count() > 7)
                                                        <li>
                                                            <a href="{{ route('product.listing', ['gender' => 'men']) }}" class="fw-semibold text-primary">
                                                                View All
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @php
                            $exploreMenu = \App\Models\SubCategory::forExploreMenu()->get();
                        @endphp
                        @if($exploreMenu->isNotEmpty())
                        <li>
                            <a href="#">Explore</a>
                            <div class="siteNavi_L2 p-lg-5">
                                <div class="row row-cols-lg-4 row-cols-1">
                                    @foreach($exploreMenu as $item)
                                        <div class="col">
                                            <a href="{{ route('product.listing', array_filter([
                                                'category' => $item->category_id,
                                                'subcategory' => $item->id,
                                            ])) }}" class="menuImgBox">
                                                <img src="{{ $item->image_url }}" class="w-100" alt="{{ $item->name }}">
                                                <div class="mibText p-sm-4 p-3">
                                                    <h2 class="mibHead titleFont">{{ $item->name }}</h2>
                                                    <span class="mibSubtext">Shop Now</span>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        @endif
                        <li><a href="{{ route('product.listing', ['on_sale' => 1]) }}" class="highlight">Sale</a></li>
                        <li><a href="{{route('about')}}">About Us</a>
                            <div class="siteNavi_L2 p-lg-5">
                                <div class="row justify-content-center">
                                    <div class="col-lg-4">
                                        <div class="menuImgBox">
                                            <img src="{{ $headerMenuSetting?->about_image ? asset($headerMenuSetting->about_image) : asset('frontend/assets/images/menuImg_store.webp') }}" class="w-100" alt=" ">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 ps-lg-5">
                                        <div class="row row-cols-1 subcatWrap">
                                            <div class="col catSubMenu">
                                                <ul class="csmList">
                                                    <li><a href="{{ route('about') }}">Our Legacy</a></li>
                                                    <li><a href="{{ route('our.stores') }}">Our Stores</a></li>
                                                    <li><a href="{{ route('contact.us') }}">Contact Us</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
 
    </div>
 
    <!-- ===== SEARCH WRAPPER - SIMPLE LAYOUT ===== -->
    <div class="searchWrapper collapse" id="searchWrapper">
        <div class="px-md-5 px-3 searchContainer">
            <div class="py-5 pb-md-5 pb-4 px-0 px-lg-3 container-xxl">
 
                <button type="button" class="searchClose" data-bs-toggle="collapse" data-bs-target="#searchWrapper" aria-expanded="true">Close</button>
 
                <form class="searchForm" action="{{ route('search') }}" method="GET" data-suggest-url="{{ route('search.suggest') }}" autocomplete="off">
                    <div class="searchBarWrap mb-4 overflow-hidden d-flex align-items-center">
                        <input 
                            type="search" 
                            name="search" 
                            placeholder="Search for watches, brands and more" 
                            class="w-100 h-100 rounded-pill searchBar js-search-input" 
                            role="combobox" 
                            aria-expanded="false" 
                            aria-controls="searchSuggestBox" 
                            aria-autocomplete="list"
                            id="searchInput"
                            autocomplete="off"
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div id="searchSuggestBox" class="searchSuggestBox" style="display:none;">
                        <div class="js-search-suggest-list"></div>
                    </div>
                </form>
 
            </div>
        </div>
    </div>
 
 </header>
 
 @push('styles')
 <style>
 /* ============================================================
    SEARCH WRAPPER - OVERLAY CONTAINER
    ============================================================ */
 .searchWrapper {
     background: #ffffff;
     border-top: 1px solid #e9ecef;
     position: relative;
     z-index: 1050;
     box-shadow: 0 4px 20px rgba(0,0,0,0.06);
 }
 
 .searchWrapper .searchContainer {
     max-width: 900px;
     margin: 0 auto;
 }
 
 .searchClose {
     background: none;
     border: none;
     font-size: 14px;
     color: #6c757d;
     text-decoration: underline;
     cursor: pointer;
     float: right;
     padding: 8px 0;
 }
 
 .searchClose:hover {
     color: #dc3545;
 }
 
 /* ============================================================
    SEARCH FORM - KEEP CONSISTENT WITH EXTERNAL CSS
    ============================================================ */
 .searchForm {
     position: relative;
     z-index: 1100;
 }
 
 .searchBarWrap {
     height: 2.813rem;
     border: 1px solid #d9dadd;
     background: #fff;
     padding: 0.25rem;
     border-radius: 50rem;
     overflow: hidden;
     display: flex;
     align-items: center;
     transition: border-color 0.2s, box-shadow 0.2s;
 }
 
 .searchBarWrap:focus-within {
     border-color: var(--orange, #f88d0d);
     box-shadow: 0 0 0 3px rgba(248, 141, 13, 0.18);
 }
 
 .searchBarWrap .searchBar {
     border: 0 !important;
     background: transparent !important;
     box-shadow: none !important;
     font-size: 0.95rem;
     color: #333;
     padding-left: 0.5rem;
     padding-right: 0.5rem;
     height: 100%;
     width: 100%;
     outline: none;
 }
 
 .searchBarWrap .searchBar::placeholder {
     color: #9aa0a6;
 }
 
 /* ============================================================
    SEARCH SUGGEST BOX - FROM EXTERNAL CSS
    ============================================================ */
 .searchSuggestBox {
     position: absolute !important;
     left: 0;
     right: 0;
     top: calc(100% + 0.45rem);
     width: 100%;
     max-width: 100%;
     background: #fff;
     border: 1px solid #ececec;
     border-radius: 1rem;
     box-shadow: 0 16px 40px rgba(0, 0, 0, 0.14);
     z-index: 1200 !important;
     overflow: hidden;
     display: none;
 }
 
 .searchSuggestBox[hidden],
 .searchSuggestBox[style*="display:none"],
 .searchSuggestBox[style*="display: none"] {
     display: none !important;
 }
 
 .searchSuggestBox:not([hidden]) {
     display: block !important;
 }
 
 .searchSuggestInner {
     max-height: min(70vh, 28rem);
     overflow-x: hidden;
     overflow-y: auto;
     padding: 1rem 1.05rem 1.1rem;
     scrollbar-width: thin;
     scrollbar-color: var(--orange, #f88d0d) #f0f0f0;
 }
 
 .searchSuggestInner::-webkit-scrollbar {
     width: 6px;
 }
 
 .searchSuggestInner::-webkit-scrollbar-track {
     background: #f0f0f0;
     border-radius: 10px;
 }
 
 .searchSuggestInner::-webkit-scrollbar-thumb {
     background: var(--orange, #f88d0d);
     border-radius: 10px;
 }
 
 /* Panel Sections */
 .searchPanelSection + .searchPanelSection {
     margin-top: 1.2rem;
 }
 
 .searchPanelHead {
     display: flex;
     align-items: center;
     gap: 0.45rem;
     font-size: 0.95rem;
     font-weight: 700;
     color: #374151;
     margin-bottom: 0.75rem;
     line-height: 1.2;
 }
 
 .searchPanelHead .searchPanelIcon,
 .searchPanelIcon {
     width: 18px !important;
     height: 18px !important;
     max-width: 18px !important;
     max-height: 18px !important;
     min-width: 18px !important;
     min-height: 18px !important;
     color: var(--orange, #f88d0d);
     flex: 0 0 18px;
     display: inline-block;
     vertical-align: middle;
 }
 
 .searchPanelHead-right {
     margin-left: auto;
 }
 
 /* Trending Pills */
 .searchTrendList {
     display: flex;
     flex-wrap: wrap;
     gap: 0.5rem;
 }
 
 .searchTrendPill {
     display: inline-flex;
     align-items: center;
     padding: 0.38rem 0.85rem;
     border: 1px solid var(--purpleSubtle, #ebe2ff);
     border-radius: 50rem;
     background: #fff;
     color: var(--purple, #7846f0);
     font-size: 0.85rem;
     text-decoration: none !important;
     line-height: 1.25;
     white-space: nowrap;
 }
 
 .searchTrendPill:hover {
     background: var(--purpleSubtle, #ebe2ff);
     border-color: var(--purple, #7846f0);
     color: var(--purple, #7846f0);
 }
 
 /* Category Grid */
 .searchCatGrid {
     display: grid;
     grid-template-columns: repeat(3, minmax(0, 1fr));
     gap: 0.55rem;
 }
 
 .searchCatCard {
     display: flex;
     align-items: center;
     gap: 0.55rem;
     padding: 0.45rem 0.55rem;
     border: 1px solid #e5e7eb;
     border-radius: 0.55rem;
     text-decoration: none !important;
     color: #111 !important;
     background: #fff;
     min-width: 0;
 }
 
 .searchCatCard:hover {
     border-color: var(--orange, #f88d0d);
     background: var(--orangeSubtle, #ffe0b8);
 }
 
 .searchCatCard img {
     width: 32px !important;
     height: 32px !important;
     object-fit: cover;
     border-radius: 0.3rem;
     flex: 0 0 32px;
     background: #f3f4f6;
 }
 
 .searchCatCard span {
     font-size: 0.82rem;
     font-weight: 500;
     white-space: nowrap;
     overflow: hidden;
     text-overflow: ellipsis;
 }
 
 /* History */
 .searchHistoryEmpty {
     color: #9ca3af;
     font-size: 0.9rem;
     margin: 0;
 }
 
 .searchHistoryList {
     display: flex;
     flex-direction: column;
     gap: 0.2rem;
 }
 
 .searchHistoryItem {
     display: flex;
     align-items: center;
     justify-content: space-between;
     gap: 0.5rem;
     padding: 0.4rem 0.15rem;
     text-decoration: none !important;
     color: #4b5563 !important;
     font-size: 0.9rem;
 }
 
 .searchHistoryItem:hover {
     color: #111 !important;
 }
 
 .searchHistoryRemove,
 .searchHistoryClear {
     border: 0;
     background: transparent;
     color: var(--orange, #f88d0d);
     font-size: 0.82rem;
     font-weight: 600;
     padding: 0;
     cursor: pointer;
 }
 
 .searchHistoryRemove {
     color: #9ca3af;
     font-size: 1rem;
     line-height: 1;
 }
 
 .searchHistoryRemove:hover {
     color: #ef4444;
 }
 
 .searchHistoryItem a {
     display: flex;
     flex-direction: column;
     gap: 0.1rem;
     flex: 1;
     min-width: 0;
     text-decoration: none !important;
     color: inherit !important;
 }
 
 .searchHistoryName {
     font-weight: 500;
     color: #374151;
 }
 
 .searchHistoryType,
 .searchResultType {
     font-size: 0.72rem;
     color: #9ca3af;
     font-weight: 500;
     text-transform: uppercase;
     letter-spacing: 0.03em;
 }
 
 /* Results */
 .searchResultMeta {
     display: flex;
     flex-direction: column;
     gap: 0.15rem;
     min-width: 0;
     flex: 1;
 }
 
 .searchResultList {
     display: flex;
     flex-direction: column;
     margin: -0.25rem -0.15rem;
 }
 
 .searchResultItem {
     display: flex;
     align-items: center;
     gap: 0.85rem;
     padding: 0.7rem 0.4rem;
     text-decoration: none !important;
     color: #111 !important;
     border-radius: 0.4rem;
 }
 
 .searchResultItem:hover,
 .searchResultItem.is-active {
     background: var(--orangeSubtle, #ffe0b8);
 }
 
 .searchResultThumb {
     width: 42px !important;
     height: 42px !important;
     border-radius: 0.4rem;
     object-fit: cover;
     background: #eee;
     flex: 0 0 42px;
 }
 
 .searchResultName {
     flex: 1;
     min-width: 0;
     font-size: 0.92rem;
     font-weight: 500;
     display: -webkit-box;
     -webkit-line-clamp: 2;
     -webkit-box-orient: vertical;
     overflow: hidden;
     line-height: 1.35;
 }
 
 .searchResultEmpty {
     padding: 0.75rem 0.25rem;
     color: #6b7280;
     font-size: 0.92rem;
 }
 
 /* ============================================================
    MOBILE RESPONSIVE
    ============================================================ */
 @media (max-width: 767.98px) {
     .searchCatGrid {
         grid-template-columns: repeat(2, minmax(0, 1fr));
     }
 
     .searchSuggestBox {
         position: fixed !important;
         left: 0.75rem;
         right: 0.75rem;
         top: 4.5rem;
         width: auto;
     }
     
     .searchWrapper .searchContainer {
         padding: 0 16px;
     }
     
     .searchBarWrap .searchBar {
         padding: 12px 20px;
         height: 50px;
         font-size: 15px;
     }
 }
 </style>
 @endpush
 
 @push('scripts')
 <script>
 document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const suggestBox = document.getElementById('searchSuggestBox');
    const suggestList = document.querySelector('.js-search-suggest-list');
    const searchForm = document.querySelector('.searchForm');
    const suggestUrl = searchForm?.dataset.suggestUrl;
 
    if (!searchInput || !suggestBox || !suggestList) return;
 
    let debounceTimer = null;
    let currentQuery = '';
    let selectedIndex = -1;
 
    // Render suggestions - Simple Layout
    function renderSuggestions(data) {
        if (!suggestList) return;
 
        let html = '';
 
        if (data.mode === 'discover') {
            // Trending Products
            if (data.trending && data.trending.length > 0) {
                html += `
                    <div class="suggestSection">
                        <div class="suggestSectionTitle">⚡ Trending Products</div>
                        <div class="suggestGrid">
                            ${data.trending.map(item => `
                                <a href="${item.url}" class="suggestGridItem" data-type="${item.type}">
                                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestGridImage" onerror="this.style.display='none'">` : ''}
                                    <div class="suggestGridName">${item.name}</div>
                                    ${item.price ? `<div class="suggestGridPrice">₹${Math.round(item.price).toLocaleString('en-IN')}</div>` : ''}
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
 
        } else if (data.mode === 'results') {
            if (data.results && data.results.length > 0) {
                html += `
                    <div class="suggestSection">
                        <div class="suggestSectionTitle">🔍 Results for "${currentQuery}"</div>
                        <div class="suggestResults">
                            ${data.results.map(item => `
                                <a href="${item.url}" class="suggestResultItem" data-type="${item.type}">
                                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestResultImage" onerror="this.style.display='none'">` : ''}
                                    <div class="suggestResultInfo">
                                        <div class="suggestResultName">${highlightMatch(item.name, currentQuery)}</div>
                                        ${item.price ? `<div class="suggestResultPrice">₹${Math.round(item.price).toLocaleString('en-IN')}</div>` : ''}
                                        <div class="suggestResultType">${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</div>
                                    </div>
                                </a>
                            `).join('')}
                        </div>
                        ${data.search_url ? `
                            <a href="${data.search_url}" class="suggestViewAll">View All Results →</a>
                        ` : ''}
                    </div>
                `;
            } else {
                html += `
                    <div class="suggestEmpty">
                        <p>No products found for "<strong>${currentQuery}</strong>"</p>
                        <p class="text-muted small">Try checking your spelling or use more general terms.</p>
                    </div>
                `;
            }
        }
 
        suggestList.innerHTML = html;
        suggestBox.style.display = 'block';
        selectedIndex = -1;
    }
 
    // Highlight matching text
    function highlightMatch(text, query) {
        if (!query || !text) return text;
        try {
            const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp('(' + escaped + ')', 'gi');
            return text.replace(regex, '<strong class="suggestHighlight">$1</strong>');
        } catch {
            return text;
        }
    }
 
    // Fetch suggestions
    function fetchSuggestions(query) {
        if (!suggestUrl) return;
 
        currentQuery = query.trim();
 
        const url = currentQuery === '' 
            ? suggestUrl + '?q=' 
            : suggestUrl + '?q=' + encodeURIComponent(currentQuery);
 
        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderSuggestions(data);
            })
            .catch(() => {
                suggestBox.style.display = 'none';
            });
    }
 
    // Input event with debounce
    searchInput.addEventListener('input', function() {
        const query = this.value;
 
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }
 
        if (query.trim() === '') {
            suggestBox.style.display = 'block';
            fetchSuggestions('');
            return;
        }
 
        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });
 
    // Focus event
    searchInput.addEventListener('focus', function() {
        if (this.value.trim() === '') {
            fetchSuggestions('');
        } else {
            fetchSuggestions(this.value);
        }
    });
 
    // Click outside to close
    document.addEventListener('click', function(e) {
        const searchWrapper = document.querySelector('.searchWrapper');
        if (searchWrapper && !searchWrapper.contains(e.target)) {
            suggestBox.style.display = 'none';
        }
    });
 
    // Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = suggestList.querySelectorAll('.suggestResultItem, .suggestGridItem');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.key === 'Enter') {
            if (selectedIndex >= 0 && items[selectedIndex]) {
                e.preventDefault();
                const item = items[selectedIndex];
                if (item.href) {
                    window.location.href = item.href;
                }
            }
        } else if (e.key === 'Escape') {
            suggestBox.style.display = 'none';
            selectedIndex = -1;
        }
    });
 
    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('suggestSelected');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('suggestSelected');
            }
        });
    }
 
    // Form submit
    searchForm.addEventListener('submit', function(e) {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            e.preventDefault();
            return;
        }
    });
 
    // Initial load - show trending
    setTimeout(function() {
        if (searchInput.value.trim() === '') {
            fetchSuggestions('');
        }
    }, 100);
 });
 </script>
 @endpush