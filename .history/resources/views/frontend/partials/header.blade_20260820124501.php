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

                              // Header dropdown banner images (admin editable).
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
                            >
                        </div>
        
                        <div id="searchSuggestBox" class="searchSuggestBox">
                            <!-- Search suggestions will be rendered here by JavaScript -->
                            <div class="js-search-suggest-list"></div>
                        </div>
                    </form>
        
                </div>
            </div>
        </div>
      </header>
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
         
             // Function to render search suggestions
             function renderSuggestions(data) {
                 if (!suggestList) return;
         
                 let html = '';
         
                 if (data.mode === 'discover') {
                     // Trending Searches Section
                     if (data.trending && data.trending.length > 0) {
                         html += `
                             <div class="suggestSection">
                                 <div class="suggestSectionTitle">Trending Searches</div>
                                 <div class="suggestTags">
                                     ${data.trending.map(item => `
                                         <a href="${item.url}" class="suggestTag">${item.name}</a>
                                     `).join('')}
                                 </div>
                             </div>
                         `;
                     }
         
                     // Top Categories
                     if (data.top_categories && data.top_categories.length > 0) {
                         html += `
                             <div class="suggestSection">
                                 <div class="suggestSectionTitle">Top Categories</div>
                                 <div class="suggestGrid">
                                     ${data.top_categories.map(item => `
                                         <a href="${item.url}" class="suggestGridItem">
                                             ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestGridImage">` : ''}
                                             <span class="suggestGridName">${item.name}</span>
                                         </a>
                                     `).join('')}
                                 </div>
                             </div>
                         `;
                     }
         
                     // Top Brands
                     if (data.top_brands && data.top_brands.length > 0) {
                         html += `
                             <div class="suggestSection">
                                 <div class="suggestSectionTitle">Top Brands</div>
                                 <div class="suggestGrid">
                                     ${data.top_brands.map(item => `
                                         <a href="${item.url}" class="suggestGridItem">
                                             ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestGridImage">` : ''}
                                             <span class="suggestGridName">${item.name}</span>
                                         </a>
                                     `).join('')}
                                 </div>
                             </div>
                         `;
                     }
         
                     // Search History (if available)
                     const history = getSearchHistory();
                     if (history.length > 0) {
                         html += `
                             <div class="suggestSection">
                                 <div class="suggestSectionTitle d-flex justify-content-between align-items-center">
                                     <span>Search History</span>
                                     <button type="button" class="suggestClearHistory" onclick="clearSearchHistory()">Clear All</button>
                                 </div>
                                 <div class="suggestHistory">
                                     ${history.map(item => `
                                         <a href="#" class="suggestHistoryItem" data-search="${item}">${item}</a>
                                     `).join('')}
                                 </div>
                             </div>
                         `;
                     }
         
                 } else if (data.mode === 'results') {
                     if (data.results && data.results.length > 0) {
                         html += `
                             <div class="suggestSection">
                                 <div class="suggestResults">
                                     ${data.results.map(item => `
                                         <a href="${item.url}" class="suggestResultItem">
                                             ${item.image ? `<img src="${item.image}" alt="${item.name}" class="suggestResultImage">` : ''}
                                             <div class="suggestResultInfo">
                                                 <div class="suggestResultName">${highlightMatch(item.name, currentQuery)}</div>
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
                                 <p>No results found for "<strong>${currentQuery}</strong>"</p>
                                 <p class="text-muted small">Try checking your spelling or use more general terms.</p>
                             </div>
                         `;
                     }
                 }
         
                 suggestList.innerHTML = html;
                 suggestBox.style.display = 'block';
             }
         
             // Highlight matching text
             function highlightMatch(text, query) {
                 if (!query || !text) return text;
                 const regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                 return text.replace(regex, '<strong class="suggestHighlight">$1</strong>');
             }
         
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
                 // Re-trigger search to refresh suggestions
                 if (searchInput.value.trim() === '') {
                     fetchSuggestions('');
                 }
             };
         
             // Fetch suggestions
             function fetchSuggestions(query) {
                 if (!suggestUrl) return;
         
                 currentQuery = query.trim();
         
                 if (currentQuery === '') {
                     // Fetch trending/discover data
                     fetch(suggestUrl + '?q=')
                         .then(response => response.json())
                         .then(data => {
                             renderSuggestions(data);
                         })
                         .catch(() => {
                             suggestBox.style.display = 'none';
                         });
                     return;
                 }
         
                 fetch(suggestUrl + '?q=' + encodeURIComponent(currentQuery))
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
         
             // Focus event - show suggestions
             searchInput.addEventListener('focus', function() {
                 if (this.value.trim() === '') {
                     fetchSuggestions('');
                 } else {
                     fetchSuggestions(this.value);
                 }
             });
         
             // Click outside to close
             document.addEventListener('click', function(e) {
                 if (!searchInput.contains(e.target) && !suggestBox.contains(e.target)) {
                     suggestBox.style.display = 'none';
                 }
             });
         
             // Keyboard navigation
             let selectedIndex = -1;
         
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
                         items[selectedIndex].click();
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
                     // Submit the form to perform search
                     searchForm.submit();
                 }
             });
         
             // Initial load - show trending
             fetchSuggestions('');
         });
         </script>