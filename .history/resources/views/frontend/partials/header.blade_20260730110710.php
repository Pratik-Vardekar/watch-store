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
                           <a href="{{ route('profile.profile') }}" class="siteMenuBtn"><img src="{{ asset('frontend/assets/images/icons/user.svg') }}" alt="My Profile"></a>
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
                           @endphp
                           <li><a href="{{ route('product.listing') }}">Brands</a>
                              <div class="siteNavi_L2 p-lg-5">

                                 <div class="row">
                                    <div class="col-lg-4 pe-xxl-5 pe-xl-4">
                                       <a href="{{ route('product.listing') }}" class="menuImgBox">
                                          <img src="{{ asset('frontend/assets/images/menuImg_brands.jpg') }}" class="w-100" alt="Exclusive Brands">
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
                                                   ->take(12)
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
                                           $womenMenuImage = $menuWomenCategories->firstWhere('image')?->image_url
                                               ?? asset('frontend/assets/images/menuImg_women.jpg');
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
                                                @forelse($menuWomenStyleCategories as $menuCategory)
                                                    <li>
                                                        <a href="{{ route('product.listing', ['gender' => 'women', 'category' => $menuCategory->id]) }}">
                                                            {{ $menuCategory->name }}
                                                        </a>
                                                    </li>
                                                @empty
                                                    <li><span class="text-muted">Add categories from admin</span></li>
                                                @endforelse
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
                                           $menMenuImage = $menuMenCategories->firstWhere('image')?->image_url
                                               ?? asset('frontend/assets/images/menuImg_men.jpg');
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
                                                @forelse($menuMenStyleCategories as $menuCategory)
                                                    <li>
                                                        <a href="{{ route('product.listing', ['gender' => 'men', 'category' => $menuCategory->id]) }}">
                                                            {{ $menuCategory->name }}
                                                        </a>
                                                    </li>
                                                @empty
                                                    <li><span class="text-muted">Add categories from admin</span></li>
                                                @endforelse
                                             </ul>
                                          </div>
                                       </div>

                                    </div>
                                 </div>

                              </div>
                           </li>
                           <li><a href="">Explore</a>
                              <div class="siteNavi_L2 p-lg-5">

                                 <div class="row row-cols-lg-4 row-cols-1">
                                    <div class="col">
                                       <a href="{{ route('product.listing') }}" class="menuImgBox">
                                          <img src="{{ asset('frontend/assets/images/menuImg_wallclock.jpg') }}" class="w-100" alt=" ">
                                          <div class="mibText p-sm-4 p-3">
                                             <h2 class="mibHead titleFont">Wall Clocks</h2>
                                             <span class="mibSubtext">Shop Now</span>
                                          </div>
                                       </a>
                                    </div>
                                    <div class="col">
                                       <a href="{{ route('product.listing') }}" class="menuImgBox">
                                          <img src="{{ asset('frontend/assets/images/menuImg_accessories.jpg') }}" class="w-100" alt=" ">
                                          <div class="mibText p-sm-4 p-3">
                                             <h2 class="mibHead titleFont">Watch Accessories</h2>
                                             <span class="mibSubtext">Shop Now</span>
                                          </div>
                                       </a>
                                    </div>
                                    <div class="col">
                                       <a href="{{ route('product.listing') }}" class="menuImgBox">
                                          <img src="{{ asset('frontend/assets/images/os_all.jpg') }}" class="w-100" alt=" ">
                                          <div class="mibText p-sm-4 p-3">
                                             <h2 class="mibHead titleFont">Gifting</h2>
                                             <span class="mibSubtext">Shop Now</span>
                                          </div>
                                       </a>
                                    </div>
                                    <div class="col">
                                       <a href="{{ route('product.listing') }}" class="menuImgBox">
                                          <img src="{{ asset('frontend/assets/images/serv_Mechanical.jpg') }}" class="w-100" alt=" ">
                                          <div class="mibText p-sm-4 p-3">
                                             <h2 class="mibHead titleFont">Servicing</h2>
                                             <span class="mibSubtext">Learn More</span>
                                          </div>
                                       </a>
                                    </div>
                                 </div>

                              </div>
                           </li>
                           <li><a href="" class="highlight">Sale</a></li>
                           <li><a href="{{route('about')}}">About Us</a>
                              <div class="siteNavi_L2 p-lg-5">

                                 <div class="row justify-content-center">
                                    <div class="col-lg-4">
                                       <div class="menuImgBox">
                                          <img src="{{ asset('frontend/assets/images/menuImg_store.webp') }}" class="w-100" alt=" ">
                                       </div>
                                    </div>
                                    <div class="col-lg-3 ps-lg-5">

                                       <div class="row row-cols-1 subcatWrap">
                                          <div class="col catSubMenu">
                                             <ul class="csmList">
                                                <li><a href="">Our Legacy</a></li>
                                                <li><a href="our_stores.html">Our Stores</a></li>
                                                <li><a href="">Careers</a></li>
                                                <li><a href="">Contact Us</a></li>
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

                     <button class="searchClose" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#searchWrapper" aria-expanded="true">Close</button>

                     <div class="searchBarWrap mb-4 overflow-hidden d-flex align-items-center">
                         <input type="search" placeholder="Search for watches, brands and more" class="w-100 h-100  rounded-pill searchBar">
                     </div>

                     <div class="row align-items-center suggestWrap mb-4 mx-0 flex-md-nowrap">
                         <div class="col-md-auto ps-0 mb-2 mb-md-0 suggestTitle">Suggested Search :</div>
                         <div class="col-md suggestTagList swiper mx-0">
                             <div class="swiper-wrapper">
                                 <div class="swiper-slide"><a href="" class="suggestTag">Waterproof</a></div>
                                 <div class="swiper-slide"><a href="" class="suggestTag">Exclusive</a></div>
                                 <div class="swiper-slide"><a href="" class="suggestTag">Cloudvista</a></div>
                                 <div class="swiper-slide"><a href="" class="suggestTag">New York</a></div>
                                 <div class="swiper-slide"><a href="" class="suggestTag">Sportswear</a></div>
                             </div>
                         </div>
                     </div>

                 </div>
             </div>
         </div>

      </header>