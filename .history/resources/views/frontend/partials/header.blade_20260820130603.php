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
                       <button class="siteMenuBtn searchMenuBtn" type="button" data-bs-toggle="collapse" data-bs-target="#searchWrapper" aria-expanded="false"><img src="{{ asset('frontend/assets/images/icons/loupe.svg') }}" alt="Search"></button>
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

   <!-- ===== SEARCH WRAPPER ===== -->
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
/* Search Wrapper */
.searchWrapper {
   background: #f8f9fa;
   border-top: 1px solid #e9ecef;
   position: relative;
   z-index: 1050;
}

.searchWrapper .searchContainer {
   max-width: 800px;
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

.searchBarWrap {
   background: #ffffff;
   border: 2px solid #e9ecef;
   border-radius: 50px;
   transition: border-color 0.3s ease;
}

.searchBarWrap:focus-within {
   border-color: #0d6efd;
}

.searchBar {
   border: none;
   outline: none;
   padding: 14px 24px;
   font-size: 16px;
   background: transparent;
   height: 56px;
}

.searchBar::placeholder {
   color: #adb5bd;
}

/* Suggest Box */
.searchSuggestBox {
   display: none;
   background: #ffffff;
   border-radius: 12px;
   box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
   max-height: 500px;
   overflow-y: auto;
   margin-top: 8px;
   padding: 16px 0;
   position: relative;
   z-index: 1060;
}

.searchSuggestBox::-webkit-scrollbar {
   width: 4px;
}

.searchSuggestBox::-webkit-scrollbar-track {
   background: #f1f1f1;
}

.searchSuggestBox::-webkit-scrollbar-thumb {
   background: #c1c7cd;
   border-radius: 4px;
}

/* Suggest Sections */
.suggestSection {
   padding: 0 20px 16px 20px;
}

.suggestSection:not(:last-child) {
   border-bottom: 1px solid #f0f0f0;
}

.suggestSectionTitle {
   font-size: 13px;
   font-weight: 600;
   color: #6c757d;
   text-transform: uppercase;
   letter-spacing: 0.5px;
   margin-bottom: 12px;
   padding-top: 8px;
}

/* Tags - Trending */
.suggestTags {
   display: flex;
   flex-wrap: wrap;
   gap: 8px;
}

.suggestTag {
   display: inline-block;
   padding: 6px 16px;
   background: #f1f3f5;
   color: #495057;
   border-radius: 20px;
   font-size: 14px;
   text-decoration: none;
   transition: all 0.2s ease;
}

.suggestTag:hover {
   background: #e9ecef;
   color: #0d6efd;
   text-decoration: none;
}

/* Grid - Categories & Brands */
/* Grid - Categories & Brands */
.suggestGrid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 12px;
}

.suggestGridItem {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    text-decoration: none;
    padding: 12px 8px;
    border-radius: 8px;
    transition: background 0.2s ease;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    min-height: 90px;
    justify-content: center;
}

.suggestGridItem:hover {
    background: #e9ecef;
    text-decoration: none;
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.suggestGridImage {
    width: 48px;
    height: 48px;
    object-fit: contain;
    margin-bottom: 6px;
    border-radius: 4px;
}

.suggestGridName {
    font-size: 13px;
    color: #212529;
    font-weight: 500;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}
/* Results */
.suggestResults {
   display: flex;
   flex-direction: column;
}

.suggestResultItem {
   display: flex;
   align-items: center;
   gap: 12px;
   padding: 8px 12px;
   border-radius: 8px;
   text-decoration: none;
   transition: background 0.2s ease;
   cursor: pointer;
}

.suggestResultItem:hover {
   background: #f8f9fa;
   text-decoration: none;
}

.suggestResultItem.suggestSelected {
   background: #f1f3f5;
}

.suggestResultImage {
   width: 40px;
   height: 40px;
   object-fit: contain;
   flex-shrink: 0;
   border-radius: 4px;
}

.suggestResultInfo {
   flex: 1;
   min-width: 0;
}

/* Results */
.suggestResults {
    display: flex;
    flex-direction: column;
}

.suggestResultItem {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.2s ease;
    cursor: pointer;
}

.suggestResultItem:hover {
    background: #f8f9fa;
    text-decoration: none;
}

.suggestResultItem.suggestSelected {
    background: #f1f3f5;
}

.suggestResultImage {
    width: 48px;
    height: 48px;
    object-fit: contain;
    flex-shrink: 0;
    border-radius: 4px;
    background: #f8f9fa;
    padding: 4px;
}

.suggestResultInfo {
    flex: 1;
    min-width: 0;
}

.suggestResultName {
    font-size: 14px;
    color: #212529;
    font-weight: 500;
    word-wrap: break-word;
    word-break: break-word;
    line-height: 1.4;
}

.suggestResultType {
    font-size: 12px;
    color: #6c757d;
    text-transform: capitalize;
    margin-top: 2px;
}

.suggestHighlight {
    background: #fff3cd;
    padding: 0 2px;
    border-radius: 2px;
}

/* View All */
.suggestViewAll {
   display: block;
   text-align: center;
   padding: 12px;
   margin-top: 8px;
   color: #0d6efd;
   font-weight: 500;
   text-decoration: none;
   border-top: 1px solid #f0f0f0;
}

.suggestViewAll:hover {
   background: #f8f9fa;
   text-decoration: none;
}

/* History */
.suggestHistory {
   display: flex;
   flex-wrap: wrap;
   gap: 8px;
}

.suggestHistoryItem {
   display: inline-block;
   padding: 6px 14px;
   background: #f8f9fa;
   color: #495057;
   border-radius: 6px;
   font-size: 14px;
   text-decoration: none;
   cursor: pointer;
   transition: all 0.2s ease;
}

.suggestHistoryItem:hover {
   background: #e9ecef;
   color: #0d6efd;
   text-decoration: none;
}

.suggestClearHistory {
   background: none;
   border: none;
   color: #dc3545;
   font-size: 12px;
   cursor: pointer;
   padding: 0;
}

.suggestClearHistory:hover {
   text-decoration: underline;
}

/* Empty State */
.suggestEmpty {
   padding: 30px 20px;
   text-align: center;
   color: #6c757d;
}

.suggestEmpty p {
   margin-bottom: 4px;
}

/* Mobile Responsive */
@media (max-width: 767px) {
   .searchWrapper .searchContainer {
       padding: 0 12px;
   }
   
   .searchSuggestBox {
       max-height: 400px;
       margin: 4px -8px 0 -8px;
       border-radius: 0 0 12px 12px;
   }
   
   .suggestGrid {
       grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
   }
   
   .suggestGridName {
       font-size: 12px;
   }
   
   .suggestResultItem {
       padding: 8px 12px;
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

   // Search History functions
   function getSearchHistory() {
       try {
           return JSON.parse(localStorage.getItem('searchHistory') || '[]');
       } catch {
           return [];
       }
   }

   function addSearchHistory(query) {
       if (!query || query.trim().length < 2) return;
       let history = getSearchHistory();
       history = history.filter(item => item.toLowerCase() !== query.toLowerCase());
       history.unshift(query);
       if (history.length > 10) history = history.slice(0, 10);
       localStorage.setItem('searchHistory', JSON.stringify(history));
   }

   window.clearSearchHistory = function() {
       localStorage.removeItem('searchHistory');
       if (searchInput.value.trim() === '') {
           fetchSuggestions('');
       }
   };

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

   // Render suggestions
// Render suggestions - Updated version
// Render suggestions - Updated version with better product display
// Render suggestions - Updated version with proper images and categories
function renderSuggestions(data) {
    if (!suggestList) return;

    let html = '';

    if (data.mode === 'discover') {
        // ... trending, categories, brands, history code ...
    } else if (data.mode === 'results') {
        if (data.results && data.results.length > 0) {
            // Group results by type
            const products = data.results.filter(r => r.type === 'product');
            const brands = data.results.filter(r => r.type === 'brand');
            const categories = data.results.filter(r => r.type === 'category');
            const subcategories = data.results.filter(r => r.type === 'subcategory');

            // Show Products
            if (products.length > 0) {
                html += `
                    <div class="suggestSection">
                        <div class="suggestSectionTitle">Products (${products.length})</div>
                        <div class="suggestResults">
                            ${products.map(item => `
                                <a href="${item.url}" class="suggestResultItem" data-type="product">
                                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestResultImage" onerror="this.src='${fallbackImage}'">` : ''}
                                    <div class="suggestResultInfo">
                                        <div class="suggestResultName">${highlightMatch(item.name, currentQuery)}</div>
                                        <div class="suggestResultType">Product</div>
                                    </div>
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // Show Brands
            if (brands.length > 0) {
                html += `
                    <div class="suggestSection">
                        <div class="suggestSectionTitle">Brands (${brands.length})</div>
                        <div class="suggestResults">
                            ${brands.map(item => `
                                <a href="${item.url}" class="suggestResultItem" data-type="brand">
                                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestResultImage" onerror="this.src='${fallbackImage}'">` : ''}
                                    <div class="suggestResultInfo">
                                        <div class="suggestResultName">${highlightMatch(item.name, currentQuery)}</div>
                                        <div class="suggestResultType">Brand</div>
                                    </div>
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // Show Categories
            if (categories.length > 0) {
                html += `
                    <div class="suggestSection">
                        <div class="suggestSectionTitle">Categories (${categories.length})</div>
                        <div class="suggestResults">
                            ${categories.map(item => `
                                <a href="${item.url}" class="suggestResultItem" data-type="category">
                                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestResultImage" onerror="this.src='${fallbackImage}'">` : ''}
                                    <div class="suggestResultInfo">
                                        <div class="suggestResultName">${highlightMatch(item.name, currentQuery)}</div>
                                        <div class="suggestResultType">Category</div>
                                    </div>
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // Show Subcategories
            if (subcategories.length > 0) {
                html += `
                    <div class="suggestSection">
                        <div class="suggestSectionTitle">Subcategories (${subcategories.length})</div>
                        <div class="suggestResults">
                            ${subcategories.map(item => `
                                <a href="${item.url}" class="suggestResultItem" data-type="subcategory">
                                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestResultImage" onerror="this.src='${fallbackImage}'">` : ''}
                                    <div class="suggestResultInfo">
                                        <div class="suggestResultName">${highlightMatch(item.name, currentQuery)}</div>
                                        <div class="suggestResultType">Subcategory</div>
                                    </div>
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // View All link
            if (data.search_url) {
                html += `
                    <div class="suggestSection">
                        <a href="${data.search_url}" class="suggestViewAll">View All ${data.total_count || data.results.length} Results →</a>
                    </div>
                `;
            }

        } else {
            // No results found - show helpful message with search suggestions
            html += `
                <div class="suggestEmpty">
                    <p>No results found for "<strong>${currentQuery}</strong>"</p>
                    <p class="text-muted small">Try checking your spelling or use more general terms.</p>
                    <div style="margin-top: 12px;">
                        <small class="text-muted">Try searching for:</small>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px;">
                            <a href="#" class="suggestTag" onclick="document.getElementById('searchInput').value='watch'; document.querySelector('.searchForm').submit(); return false;">watch</a>
                            <a href="#" class="suggestTag" onclick="document.getElementById('searchInput').value='titan'; document.querySelector('.searchForm').submit(); return false;">titan</a>
                            <a href="#" class="suggestTag" onclick="document.getElementById('searchInput').value='casual'; document.querySelector('.searchForm').submit(); return false;">casual</a>
                            <a href="#" class="suggestTag" onclick="document.getElementById('searchInput').value='men'; document.querySelector('.searchForm').submit(); return false;">men</a>
                            <a href="#" class="suggestTag" onclick="document.getElementById('searchInput').value='women'; document.querySelector('.searchForm').submit(); return false;">women</a>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    suggestList.innerHTML = html;
    suggestBox.style.display = 'block';
    selectedIndex = -1;
}
   // Fetch suggestions
// Fetch suggestions with better error handling
function fetchSuggestions(query) {
    if (!suggestUrl) return;

    currentQuery = query.trim();

    const url = currentQuery === '' 
        ? suggestUrl + '?q=' 
        : suggestUrl + '?q=' + encodeURIComponent(currentQuery);

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            renderSuggestions(data);
        })
        .catch(error => {
            console.error('Search error:', error);
            suggestList.innerHTML = `
                <div class="suggestEmpty">
                    <p>Something went wrong. Please try again.</p>
                </div>
            `;
            suggestBox.style.display = 'block';
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
       const items = suggestList.querySelectorAll('.suggestResultItem, .suggestHistoryItem, .suggestTag, .suggestGridItem');
       
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
               if (item.dataset.search) {
                   const query = item.dataset.search;
                   searchInput.value = query;
                   addSearchHistory(query);
                   searchForm.submit();
               } else if (item.href) {
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

   // Form submit - add to history
   searchForm.addEventListener('submit', function(e) {
       const query = searchInput.value.trim();
       if (query.length >= 2) {
           addSearchHistory(query);
       }
   });

   // Click handler for history items (delegated)
   suggestList.addEventListener('click', function(e) {
       const target = e.target.closest('[data-search]');
       if (target) {
           e.preventDefault();
           const query = target.dataset.search;
           searchInput.value = query;
           addSearchHistory(query);
           searchForm.submit();
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