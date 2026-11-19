@extends('layouts.front')

@section('content')
@php
    $search = $search ?? '';
    $products = $products ?? collect();
    
    $shownCount = method_exists($products, 'count') ? $products->count() : 0;
    $totalCount = method_exists($products, 'total') ? $products->total() : $shownCount;
@endphp

<main class="pageWrapper">

    <section class="inBannerSecton position-relative overflow-hidden w-100 d-flex">
       <img src="{{ asset('frontend/assets/images/searchBanner.jpg') }}" class="inBanImg" alt="Search Results">
       <div class="container position-relative z-1 d-flex flex-column justify-content-center align-items-start">
          <nav class="breadcrumb mb-0 text-white animateThis slideTop">
             <a class="breadcrumb-item" href="{{ url('/') }}">Home</a>
             <span class="breadcrumb-item active">Search Results</span>
          </nav>
          <h1 class="sectTitle text-white animateThis slideTop">Search Results</h1>
       </div>
    </section>

    <section class="py-sm-5 py-4 bgGray">
       <div class="container">
          
          <!-- Search Header -->
          <div class="mb-4 bg-white p-3 rounded shadow-sm">
             <h4 class="fw-bold mb-2">Search Results for "<span class="txtRed">{{ $search }}</span>"</h4>
             <p class="text-muted mb-0">{{ $totalCount }} products found</p>
          </div>

          <!-- Product Grid - Only Products -->
          <div class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2">
             @forelse($products as $product)
                @include('frontend.partials.home-product-slide', [
                   'product' => $product,
                   'wrapperClass' => 'col',
                   'itemClass' => 'productItem text-center animateThis fadeIn in-view',
                   'showNewBadge' => (bool) ($product->is_new_arrival ?? false),
                ])
             @empty
                <div class="col-12">
                   <div class="bg-white rounded shadow-sm p-5 text-center">
                      <p class="mb-0 text-muted">No products found for "<strong>{{ $search }}</strong>"</p>
                      <p class="text-muted small mt-2">Try checking your spelling or use more general terms.</p>
                   </div>
                </div>
             @endforelse
          </div>

          <!-- Pagination -->
          @if(method_exists($products, 'links'))
             <div class="d-flex justify-content-center">
                {{ $products->links() }}
             </div>
          @elseif(method_exists($products, 'hasMorePages') && $products->hasMorePages())
             <div class="text-center">
                <a href="{{ $products->nextPageUrl() }}" class="ctaBtn">Show More</a>
             </div>
          @elseif(method_exists($products, 'lastPage') && $products->lastPage() > 1)
             <div class="text-center text-muted small">
                You're viewing all {{ $totalCount }} products
             </div>
          @endif

       </div>
    </section>

 </main>
@endsection

@push('styles')
<style>
    .inBannerSecton .inBanImg {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    @media (max-width: 768px) {
        .inBannerSecton .inBanImg {
            height: 150px;
        }
    }
</style>
@endpush