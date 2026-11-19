@extends('layouts.front')
@section('content')
<main class="pageWrapper">

    <section class="py-5 bgGray">
       <div class="container">

           <div class="row justify-content-between mb-5">
               @include('frontend.partials.myaccount_nav', ['active' => 'wishlist', 'user' => $user])

               <div class="col-lg-9 mb-lg-4">

                   <h2 class="sectTitle lh-1 mb-4">My Wishlist</h2>

                   @if(session('success'))
                       <div class="alert alert-success">{{ session('success') }}</div>
                   @endif

                   <div class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2">
                      @forelse($wishlistItems as $item)
                         @if($item->product)
                         <div class="col">
                            @include('frontend.partials.home-product-slide', [
                                'product' => $item->product,
                                'wrapperClass' => '',
                                'showNewBadge' => (bool) ($item->product->is_new_arrival ?? false),
                            ])
                            <div class="d-flex flex-wrap justify-content-center gap-2 pt-3 position-relative z-3">
                               <form action="{{ route('wishlist.remove', $item->product) }}" method="POST">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" class="olBtn rounded">Remove</button>
                               </form>
                               <button type="button"
                                  class="olBtn rounded dark js-add-to-cart"
                                  data-product-id="{{ $item->product->id }}"
                                  data-add-url="{{ route('cart.add', $item->product->id) }}">
                                  Add to Cart
                               </button>
                            </div>
                         </div>
                         @endif
                      @empty
                      <div class="col-12">
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
