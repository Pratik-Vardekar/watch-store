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
                       @if($wishlistItems->count())
                           <small class="bagCount bgOrange text-white rounded-pill py-1 px-2 fs-6">
                               {{ $wishlistItems->count() }} {{ $wishlistItems->count() === 1 ? 'item' : 'items' }}
                           </small>
                       @endif
                   </h2>

                   @if(session('success'))
                       <div class="alert alert-success">{{ session('success') }}</div>
                   @endif

                       <ul class="bagListWrap mb-4 d-flex flex-column gap-3">
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
                             @endphp
                             <li class="bagItem rounded-3 p-sm-3 p-2 d-flex gap-xl-4 gap-3 bg-white">
                                <div class="col-auto flex-shrink-1">
                                   <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                      <img src="{{ $image }}" class="bgiImg" alt="{{ $product->name }}">
                                   </a>
                                </div>
                                <div class="col">
                                   <div class="row h-100 flex-column">
                                      <div class="col-12 mb-md-3 mb-2 d-flex align-items-center justify-content-between">
                                         <div class="col">
                                            <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="bgiProdname mb-0">{{ $product->name }}</a>
                                         </div>
                                         <div class="col-auto">
                                            <form action="{{ route('wishlist.remove', $product) }}" method="POST">
                                               @csrf
                                               @method('DELETE')
                                               <button type="submit" class="btn p-0 bgiDeleteBtn" title="Remove">&times;</button>
                                            </form>
                                         </div>
                                      </div>
                                      <div class="col-12 mt-auto d-flex flex-wrap align-items-center justify-content-between gap-3">
                                         <div class="d-flex align-items-center gap-2">
                                            @if($originalPrice > $sellingPrice)
                                               <span class="pibStikePrice">₹{{ number_format($originalPrice, 2) }}</span>
                                            @endif
                                            <strong class="bgiProdPrice">₹{{ number_format($sellingPrice, 2) }}</strong>
                                            @if($discount > 0)
                                               <span class="txtgreen fw-semibold">{{ $discount }}% Off</span>
                                            @endif
                                         </div>
                                         <button type="button"
                                            class="olBtn rounded dark js-add-to-cart"
                                            data-product-id="{{ $product->id }}"
                                            data-add-url="{{ route('cart.add', $product->id) }}">
                                            Add to Cart
                                         </button>
                                      </div>
                                   </div>
                                </div>
                             </li>
                             @endif
                          @empty
                          <li class="rounded-3 p-5 text-center bg-white">
                             <h5 class="mb-2">Your wishlist is empty</h5>
                             <p class="text-secondary mb-4">Save watches you love and shop them later.</p>
                             <a href="{{ route('product.listing') }}" class="ctaBtn">
                                <span class="btnTxt">Browse Watches</span>
                                <span class="arw"></span>
                             </a>
                          </li>
                          @endforelse
                       </ul>

               </div>
           </div>

       </div>
    </section>

 </main>
 @endsection
