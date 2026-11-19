@extends('layouts.front')

@section('content')
@php
    $search = $search ?? '';
    $products = $products ?? collect();
    $matchedCategories = $matchedCategories ?? collect();
    $matchedBrands = $matchedBrands ?? collect();
    
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
          <div class="mb-4">
             <h4 class="fw-bold mb-2">Search Results for "<span class="txtRed">{{ $search }}</span>"</h4>
             <p class="text-muted">{{ $totalCount }} products found</p>
          </div>

          <!-- Matched Categories (Optional) -->
          @if($matchedCategories->isNotEmpty())
          <div class="mb-4">
             <h6 class="fw-semibold mb-2">Categories</h6>
             <div class="d-flex flex-wrap gap-2">
                @foreach($matchedCategories as $category)
                   <a href="{{ route('product.listing', ['category' => $category->id]) }}" class="badge bg-light text-dark p-2 text-decoration-none">
                      {{ $category->name }} ({{ $category->products_count ?? 0 }})
                   </a>
                @endforeach
             </div>
          </div>
          @endif

          <!-- Matched Brands (Optional) -->
          @if($matchedBrands->isNotEmpty())
          <div class="mb-4">
             <h6 class="fw-semibold mb-2">Brands</h6>
             <div class="d-flex flex-wrap gap-2">
                @foreach($matchedBrands as $brand)
                   <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" class="badge bg-light text-dark p-2 text-decoration-none">
                      {{ $brand->name }}
                   </a>
                @endforeach
             </div>
          </div>
          @endif

          <!-- Product Grid -->
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
          @endif

       </div>
    </section>

 </main>
@endsection