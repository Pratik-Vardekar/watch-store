@extends('layouts.front')
@section('content')
<main class="pageWrapper">

   <section class="py-5 bgGray">

      <div class="container">

         <div class="row justify-content-between mb-5">

            {{-- Sidebar for desktop only --}}
            <div class="col-lg-3 mb-lg-4 d-none d-lg-block">

               <div class="myAccLinkWrap shadow-lg rounded overflow-hidden">

                  <div class="d-flex p-3 mb-4 w-100 bg-black text-white">
                     <div class="col">
                        <small class="d-block">Welcome,</small>
                        @auth
                            <strong>{{ auth()->user()->name ?? 'User' }}</strong>
                        @else
                            <strong>Guest</strong>
                        @endauth
                     </div>
                  </div>

                  <ul class="myAccLinks p-3 d-flex flex-column gap-4 mb-4">
                     <li><a href="{{ route('myaccount.profile') }}">Profile</a></li>
                     <li><a href="{{ route('myaccount.orders') }}">Orders</a></li>
                     <li><a href="{{ route('myaccount.wishlist') }}" class="active">Wishlist</a></li>
                     <li><a href="{{ route('myaccount.addresses') }}">Addresses</a></li>
                     <li><a href="{{ route('myaccount.change-password') }}">Change Password</a></li>
                  </ul>

                  <div class="p-3 text-center w-100">
                     @auth
                        <form action="{{ route('logout') }}" method="POST">
                           @csrf
                           <button type="submit" class="ctaBtn w-100 p-2">Logout</button>
                        </form>
                     @else
                        <a href="{{ route('login') }}" class="ctaBtn w-100 p-2">Login</a>
                     @endauth
                  </div>

               </div>

            </div>

            <div class="col-lg-9 mb-lg-4">

               <h2 class="sectTitle lh-1 mb-4">My Wishlist</h2>

               @if(session('success'))
                  <div class="alert alert-success">{{ session('success') }}</div>
               @endif

               @if($wishlistItems->count())
               <div class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2">
                  @foreach($wishlistItems as $item)
                     @php
                        $product = $item->product;
                        if (!$product) continue;
                        $sellingPrice = (float) ($product->selling_price ?? $product->price ?? 0);
                        $originalPrice = (float) ($product->price ?? 0);
                        $discount = (int) ($product->discount_percent ?? $product->discount ?? 0);
                        if ($discount <= 0 && $originalPrice > $sellingPrice && $originalPrice > 0) {
                            $discount = (int) round((($originalPrice - $sellingPrice) / $originalPrice) * 100);
                        }
                        $image = $product->getProductImageUrl($product->thumbnail);
                        // Rating & Brand
                        $brand = $product->brand->name ?? '';
                        $rating = $product->averageRating() ?? null;
                        $reviewCount = $product->reviews()->count();
                        $isNew = $product->is_new ?? false;
                     @endphp
                     <div class="col">
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
                                 <form action="{{ route('wishlist.remove', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="wishlistBtn saved" title="Remove"></button>
                                 </form>
                              </div>
                           </div>

                           <div class="pi_imgbox mb-4">
                              <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                 <img src="{{ $image }}" alt="{{ $product->name }}" class="pi_img">
                              </a>
                           </div>

                           <div class="pi_details">
                              @if($brand)
                                 <h2 class="pi_prodBrand fw-bold mb-2">{{ $brand }}</h2>
                              @endif
                              <h3 class="pi_prodName mb-3" title="{{ $product->name }}">{{ $product->name }}</h3>
                              <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                 @if($rating)
                                    @php
                                       // This logic mirrors the star rating classes from context
                                       $starClass = match(true) {
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
                                    @endphp
                                    <div class="ratingBox"> <div class="ratedStars {{ $starClass }}"></div> </div>
                                 @else
                                    <div class="ratingBox"> <div class="ratedStars star0"></div> </div>
                                 @endif
                                 <div class="pi_reviewCount fw-semibold">({{ $reviewCount }})</div>
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
                              <form action="{{ route('wishlist.remove', $product) }}" method="POST" class="d-inline">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="olBtn rounded mx-auto">Remove</button>
                              </form>
                           </div>
                        </div>
                     </div>
                  @endforeach
               </div>
               @else
               <div class="rounded-3 p-5 text-center bg-white">
                  <h5 class="mb-2">Your wishlist is empty</h5>
                  <p class="text-secondary mb-4">Save watches you love and shop them later.</p>
                  <a href="{{ route('product.listing') }}" class="ctaBtn">
                     <span class="btnTxt">Browse Watches</span>
                     <span class="arw"></span>
                  </a>
               </div>
               @endif

            </div>

         </div>

      </div>

   </section>

</main>
@endsection
