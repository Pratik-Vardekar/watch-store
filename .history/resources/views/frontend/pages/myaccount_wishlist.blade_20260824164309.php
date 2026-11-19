@extends('layouts.front')
@section('content')
<main class="pageWrapper">

   <section class="py-5 bgGray">
      <div class="container">

          <div class="row justify-content-between mb-5">
              <div class="col-lg-3 mb-lg-4 d-none d-lg-block">

                  <div class="myAccLinkWrap shadow-lg rounded overflow-hidden">
                     <div class="d-flex p-3 mb-4 w-100 bg-black text-white">
                        <div class="col">
                           <small class="d-block">Welcome,</small>
                           <strong>{{ Auth::user()->name ?? 'User' }}</strong>
                        </div>
                     </div>
                     <ul class="myAccLinks p-3 d-flex flex-column gap-4 mb-4">
                        <li><a href="{{ route('myaccount.profile') }}">Profile</a></li>
                        <li><a href="{{ route('myaccount.orders') }}">Orders</a></li>
                        <li><a href="{{ route('myaccount.wishlist') }}" class="active">Wishlist</a></li>
                        <li><a href="{{ route('myaccount.addresses') }}">Addresses</a></li>
                        <li><a href="{{ route('myaccount.changepassword') }}">Change Password</a></li>
                     </ul>
                     <div class="p-3 text-center w-100">
                        <form action="{{ route('logout') }}" method="POST">
                           @csrf
                           <button type="submit" class="ctaBtn w-100 p-2">Logout</button>
                        </form>
                     </div>
                  </div>

              </div>
              <div class="col-lg-9 mb-lg-4">

                  <h2 class="sectTitle lh-1 mb-4">My Wishlist</h2>

                  <div class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2">

                     @forelse($wishlist as $wish)
                        @php
                           $product = $wish->product ?? null;
                        @endphp

                        @if($product)
                           <div class="col">
                              <div class="productItem text-center animateThis fadeIn">
                                 <div class="pi_topBar d-flex justify-content-between">
                                    <div class="col-auto">
                                       @if($product->is_new ?? false)
                                          <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                                       @endif
                                       @if($product->discount ?? false)
                                          <div class="pi_offer d-flex flex-column justify-content-center">
                                             {{ $product->discount }}% <small>Off</small>
                                          </div>
                                       @endif
                                    </div>
                                    <div class="col-auto">
                                       <form action="{{ route('wishlist.remove', $wish->id) }}" method="POST">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="wishlistBtn saved" title="Remove"></button>
                                       </form>
                                    </div>
                                 </div>
                                 <div class="pi_imgbox mb-4">
                                    <img src="{{ $product->getProductImageUrl($product->thumbnail) }}" alt="{{ $product->name }}" class="pi_img">
                                 </div>
                                 <div class="pi_details">
                                    <h2 class="pi_prodBrand fw-bold mb-2">{{ $product->brand->name ?? '' }}</h2>
                                    <h3 class="pi_prodName mb-3" title="{{ $product->name }}">{{ $product->name }}</h3>
                                    <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                       <div class="ratingBox"> 
                                          <div class="ratedStars star{{ round($product->averageRating * 2) / 2 }}"></div>
                                       </div>
                                       <div class="pi_reviewCount fw-semibold">({{ $product->reviews_count ?? 0 }})</div>
                                    </div>
                                    <div class="pi_price">
                                       @if($product->price > $product->selling_price)
                                          <small class="pip_strike me-2">₹ {{ number_format($product->price, 2) }}</small>
                                       @endif
                                       <strong class="pip_ProdPrice fw-bolder">₹ {{ number_format($product->selling_price, 2) }}</strong>
                                    </div>
                                 </div>
                                 <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="stretched-link z-3" title="{{ $product->name }}"></a>
                                 <div class="pt-3 position-relative z-3">
                                    <form action="{{ route('wishlist.remove', $wish->id) }}" method="POST">
                                       @csrf
                                       @method('DELETE')
                                       <button type="submit" class="olBtn rounded mx-auto">Remove</button>
                                    </form>
                                 </div>
                              </div>
                           </div>
                        @endif
                     @empty
                        <div class="col-12">
                           <div class="alert alert-info text-center">Your wishlist is empty.</div>
                        </div>
                     @endforelse

                  </div>

              </div>
          </div>

      </div>
   </section>

</main>
@endsection
