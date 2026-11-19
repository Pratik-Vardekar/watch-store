@extends('layouts.front')

@section('content')
@php
    // Get parameters from request with proper defaults
    $gender = $gender ?? request('gender');
    $filter = $filter ?? request('filter');
    $maxPrice = $maxPrice ?? (request()->filled('max_price') ? (float) request('max_price') : null);
    $minPrice = $minPrice ?? (request()->filled('min_price') ? (float) request('min_price') : null);
    $sort = $sort ?? request('sort');
    $brandIds = $brandIds ?? array_map('intval', (array) request('brand', []));
    $categoryId = $categoryId ?? (request()->filled('category') ? (int) request('category') : null);
    $subcategoryId = $subcategoryId ?? (request()->filled('subcategory') ? (int) request('subcategory') : null);
    $listingTitle = $listingTitle ?? 'Watches';
    $breadcrumbLabel = $breadcrumbLabel ?? 'Watches';
    $brands = $brands ?? collect();
    $products = $products ?? collect();   
    $categories = $categories ?? collect();
    
    // Product count calculations
    $shownCount = method_exists($products, 'count') ? $products->count() : 0;
    $totalCount = method_exists($products, 'total') ? $products->total() : $shownCount;
    
    // Price slider configuration
    $sliderMin = 0;
    $sliderMax = 100000;
    $sliderStartMin = $minPrice !== null && $minPrice > 0 ? (int) $minPrice : $sliderMin;
    $sliderStartMax = $maxPrice !== null && $maxPrice > 0 ? (int) $maxPrice : $sliderMax;
    
    // Price tiers for quick selection
    $priceTiers = [
        ['min' => null, 'max' => 5000, 'label' => 'Under ₹5 K'],
        ['min' => 5001, 'max' => 10000, 'label' => 'Upto ₹10 K'],
        ['min' => 10001, 'max' => 25000, 'label' => 'Upto ₹25 K'],
        ['min' => 25001, 'max' => 50000, 'label' => 'Upto ₹50 K'],
        ['min' => 50001, 'max' => 100000, 'label' => 'Upto ₹1 L'],
    ];
    
    $activeMaxPrice = $maxPrice !== null && $maxPrice > 0 ? (int) $maxPrice : null;
    $activeMinPrice = $minPrice !== null && $minPrice > 0 ? (int) $minPrice : null;
    $hasPriceFilter = $activeMaxPrice !== null || $activeMinPrice !== null;
    
    // Determine active price tier
    $activeTierKey = null;
    foreach ($priceTiers as $index => $tier) {
        $tierMin = $tier['min'];
        $tierMax = $tier['max'];
        $minMatches = ($tierMin === null && $activeMinPrice === null)
            || ($tierMin !== null && $activeMinPrice === (int) $tierMin);
        $maxMatches = $activeMaxPrice === (int) $tierMax;
        if ($minMatches && $maxMatches) {
            $activeTierKey = $index;
            break;
        }
    }
    
    // Build filter URL for "Clear All" button
    $clearAllParams = array_filter([
        'gender' => $gender,
        'filter' => $filter,
        'sort' => $sort,
    ]);
    
    // Get selected brand names for display
    $selectedBrandNames = [];
    if (!empty($brandIds)) {
        $selectedBrandNames = \App\Models\Brand::whereIn('id', $brandIds)
            ->where('status', 1)
            ->pluck('name')
            ->toArray();
    }
@endphp

<main class="pageWrapper">

    <!-- Banner Section -->
    <section class="inBannerSecton position-relative overflow-hidden w-100 d-flex">
        <img src="{{ asset('frontend/assets/images/prodListBanner.jpg') }}" class="inBanImg" alt="{{ $listingTitle }}">
        <div class="container position-relative z-1 d-flex flex-column justify-content-center align-items-start">
            <nav class="breadcrumb mb-0 text-white animateThis slideTop">
                <a class="breadcrumb-item" href="{{ url('/') }}">Home</a>
                <span class="breadcrumb-item active">{{ $breadcrumbLabel }}</span>
            </nav>
            <h1 class="sectTitle text-white animateThis slideTop">{{ $listingTitle }}</h1>
        </div>
    </section>

    <!-- Product Listing Section -->
    <section class="py-sm-5 py-4 bgGray">
        <div class="container">

            <!-- Filter & Sort Header -->
            <div class="filterSortWrap d-flex flex-wrap align-items-stretch bg-white bg-opacity-75 overflow-hidden mb-sm-5 mb-4 rounded shadow">
                <!-- Product Count -->
                <div class="prodListCount col-sm col-12 order-sm-2 p-2 d-flex justify-content-center align-items-center text-center" style="font-size:85%;">
                    <span>Showing <strong class="txtRed">{{ $shownCount }}</strong> out of <strong>{{ $totalCount }}</strong> Watches</span>
                </div>
                
                <!-- Filter Button -->
                <div class="col-sm col-6 order-sm-1">
                    <button class="filterSortBtn" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterMain">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z"/>
                        </svg>
                        Filters
                        <span class="filter-count-badge" id="filterCountBadge"></span>
                    </button>
                </div>
                
                <!-- Sort Dropdown -->
                <div class="col-sm col-6 order-sm-3 text-end">
                    <form method="GET" action="{{ route('product.listing') }}" id="sortForm">
                        @foreach(request()->except(['sort', 'page']) as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <select name="sort" class="form-select rounded-0 filterSortBtn" onchange="this.form.submit()">
                            <option value="" {{ empty($sort) ? 'selected' : '' }}>Sort By</option>
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Recently Added</option>
                            <option value="relevance" {{ $sort === 'relevance' ? 'selected' : '' }}>Relevance</option>
                            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price (low-high)</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price (high-low)</option>
                            <option value="brand_asc" {{ $sort === 'brand_asc' ? 'selected' : '' }}>Brands (A-Z)</option>
                            <option value="brand_desc" {{ $sort === 'brand_desc' ? 'selected' : '' }}>Brands (Z-A)</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Horizontal Brand Filters - NEW LAYOUT -->
            @if($brands->isNotEmpty())
            <div class="brand-filters-wrapper mb-4">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="fw-bold text-muted small me-2">Brands:</span>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <!-- "All" Option -->
                        <a href="{{ route('product.listing', array_merge(request()->except(['brand']), ['brand' => []])) }}" 
                           class="brand-filter-chip {{ empty($brandIds) ? 'active' : '' }}">
                            All
                        </a>
                        
                        <!-- Brand Chips -->
                        @foreach($brands as $brand)
                            @php
                                $isActive = in_array($brand->id, $brandIds, true);
                                $brandParams = array_merge(request()->except(['brand']), [
                                    'brand' => $isActive 
                                        ? array_values(array_diff($brandIds, [$brand->id]))
                                        : array_merge($brandIds, [$brand->id])
                                ]);
                                // Remove empty brand array
                                if (empty($brandParams['brand'])) {
                                    unset($brandParams['brand']);
                                }
                            @endphp
                            <a href="{{ route('product.listing', $brandParams) }}" 
                               class="brand-filter-chip {{ $isActive ? 'active' : '' }}">
                                {{ $brand->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Active Filters Display -->
            @if($hasPriceFilter || !empty($brandIds) || $categoryId || $subcategoryId || $gender)
            <div class="active-filters mb-4 d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small me-2">Active Filters:</span>
                @if($categoryId)
                    @php $category = $categories->firstWhere('id', $categoryId); @endphp
                    @if($category)
                        <span class="badge bg-secondary d-flex align-items-center gap-1 py-2 px-3">
                            Category: {{ $category->name }}
                            <a href="{{ route('product.listing', array_merge(request()->except(['category']), ['category' => ''])) }}" class="text-white text-decoration-none ms-1">&times;</a>
                        </span>
                    @endif
                @endif
                @if($subcategoryId)
                    <span class="badge bg-secondary d-flex align-items-center gap-1 py-2 px-3">
                        Subcategory: {{ $subcategoryId }}
                        <a href="{{ route('product.listing', array_merge(request()->except(['subcategory']), ['subcategory' => ''])) }}" class="text-white text-decoration-none ms-1">&times;</a>
                    </span>
                @endif
                @if($gender)
                    <span class="badge bg-secondary d-flex align-items-center gap-1 py-2 px-3">
                        Gender: {{ ucfirst($gender) }}
                        <a href="{{ route('product.listing', array_merge(request()->except(['gender']), ['gender' => ''])) }}" class="text-white text-decoration-none ms-1">&times;</a>
                    </span>
                @endif
                @if($hasPriceFilter)
                    <span class="badge bg-secondary d-flex align-items-center gap-1 py-2 px-3">
                        Price: ₹{{ number_format($activeMinPrice ?? 0) }} - ₹{{ number_format($activeMaxPrice ?? 100000) }}
                        <a href="{{ route('product.listing', array_merge(request()->except(['min_price', 'max_price']), ['min_price' => '', 'max_price' => ''])) }}" class="text-white text-decoration-none ms-1">&times;</a>
                    </span>
                @endif
                @if(!empty($selectedBrandNames))
                    <span class="badge bg-secondary d-flex align-items-center gap-1 py-2 px-3">
                        Brands: {{ implode(', ', $selectedBrandNames) }}
                        <a href="{{ route('product.listing', array_merge(request()->except(['brand']), ['brand' => []])) }}" class="text-white text-decoration-none ms-1">&times;</a>
                    </span>
                @endif
            </div>
            @endif

            <!-- Product Grid -->
            <div class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2" id="productGrid">
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ddd" class="mb-3" viewBox="0 0 16 16">
                                <path d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z"/>
                            </svg>
                            <h5 class="mb-2">No watches found</h5>
                            <p class="mb-0 text-muted">Try adjusting your filters or search criteria.</p>
                            <a href="{{ route('product.listing') }}" class="ctaBtn mt-3 d-inline-block">View All Watches</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if(method_exists($products, 'hasPages') && $products->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            @elseif(method_exists($products, 'hasMorePages') && $products->hasMorePages())
                <div class="text-center">
                    <a href="{{ $products->nextPageUrl() }}" class="ctaBtn">Show More</a>
                </div>
            @elseif(method_exists($products, 'lastPage') && $products->lastPage() > 1)
                <div class="text-center text-muted small">
                    You're viewing all {{ $totalCount }} watches
                </div>
            @endif

        </div>
    </section>

    <!-- Filter Offcanvas -->
    <div class="offcanvas offcanvas-start filterMain" tabindex="-1" id="filterMain">
        <form method="GET" action="{{ route('product.listing') }}" class="d-flex flex-column h-100" id="listingFilterForm">
            @if($filter)
                <input type="hidden" name="filter" value="{{ $filter }}">
            @endif
            @if($sort)
                <input type="hidden" name="sort" value="{{ $sort }}">
            @endif
            @if(!empty($subcategoryId))
                <input type="hidden" name="subcategory" value="{{ $subcategoryId }}">
            @endif

            <div class="offcanvas-header" style="background-color:#f3f3f3;">
                <h5 class="offcanvas-title fs-6" style="font-weight:800;">FILTERS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            
            <div class="offcanvas-body">

                <div class="filterBox mb-3">
                    <ul class="accdFilters clearfix" id="filterWrap">
                        <!-- Brands Filter -->
                        <li>
                            <button class="rfi_head" type="button" data-bs-toggle="collapse" data-bs-target="#fltr_Brands" aria-expanded="true" aria-controls="fltr_Brands">Brands</button>
                            <div class="collapse show" id="fltr_Brands" data-bs-parent="#filterWrap">
                                <ul class="afiContent">
                                    @forelse($brands as $brand)
                                        <li>
                                            <input class="form-check-input" type="checkbox" name="brand[]" value="{{ $brand->id }}" id="brand_{{ $brand->id }}" {{ in_array($brand->id, $brandIds, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="brand_{{ $brand->id }}">{{ $brand->name }}</label>
                                        </li>
                                    @empty
                                        <li class="text-muted small">No brands available</li>
                                    @endforelse
                                </ul>
                            </div>
                        </li>
                        
                        <!-- Category Filter -->
                        <li>
                            <button class="rfi_head" type="button" data-bs-toggle="collapse" data-bs-target="#fltr_Category" aria-expanded="true" aria-controls="fltr_Category">Category</button>
                            <div class="collapse show" id="fltr_Category" data-bs-parent="#filterWrap">
                                <ul class="afiContent">
                                    <li>
                                        <input class="form-check-input" type="radio" name="category" value="" id="category_all" {{ empty($categoryId) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_all">All Categories</label>
                                    </li>
                                    @forelse($categories as $category)
                                        <li>
                                            <input class="form-check-input" type="radio" name="category" value="{{ $category->id }}" id="category_{{ $category->id }}" {{ (int) $categoryId === (int) $category->id ? 'checked' : '' }}>
                                            <label class="form-check-label" for="category_{{ $category->id }}">{{ $category->name }}</label>
                                        </li>
                                    @empty
                                        <li class="text-muted small">No categories available</li>
                                    @endforelse
                                </ul>
                            </div>
                        </li>
                        
                        <!-- Price Filter -->
                        <li>
                            <button class="rfi_head" type="button" data-bs-toggle="collapse" data-bs-target="#fltr_Price" aria-expanded="{{ $hasPriceFilter ? 'true' : 'false' }}" aria-controls="fltr_Price">Price</button>
                            <div class="collapse {{ $hasPriceFilter ? 'show' : '' }}" id="fltr_Price" data-bs-parent="#filterWrap">
                                <ul class="afiContent mb-3">
                                    <li>
                                        <input class="form-check-input js-price-tier" type="radio" name="price_tier" value="" id="price_all" data-min="" data-max="" {{ $activeTierKey === null && ! $hasPriceFilter ? 'checked' : '' }}>
                                        <label class="form-check-label" for="price_all">All Prices</label>
                                    </li>
                                    @foreach($priceTiers as $index => $tier)
                                        <li>
                                            <input class="form-check-input js-price-tier" type="radio" name="price_tier" value="{{ $index }}" id="price_tier_{{ $index }}" data-min="{{ $tier['min'] ?? '' }}" data-max="{{ $tier['max'] }}" {{ $activeTierKey === $index ? 'checked' : '' }}>
                                            <label class="form-check-label" for="price_tier_{{ $index }}">{{ $tier['label'] }}</label>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="w-100 rangeSliderBox">
                                    <div class="row g-0">
                                        <div class="col-12 mb-2">
                                            <div id="slider-price"></div>
                                        </div>
                                        <div class="col-6"><span class="price" id="slider-price-min"></span></div>
                                        <div class="col-6 text-end"><span class="price" id="slider-price-max"></span></div>
                                        <input type="hidden" name="min_price" id="min-pricevalue" value="{{ $activeMinPrice ?? '' }}">
                                        <input type="hidden" name="max_price" id="max-pricevalue" value="{{ $activeMaxPrice ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                        <!-- Gender Filter -->
                        <li>
                            <button class="rfi_head" type="button" data-bs-toggle="collapse" data-bs-target="#fltr_Gender" aria-expanded="{{ in_array($gender, ['women', 'men', 'both'], true) ? 'true' : 'false' }}" aria-controls="fltr_Gender">Gender</button>
                            <div class="collapse {{ in_array($gender, ['women', 'men', 'both'], true) ? 'show' : '' }}" id="fltr_Gender" data-bs-parent="#filterWrap">
                                <ul class="afiContent">
                                    <li>
                                        <input class="form-check-input" type="radio" name="gender" value="" id="genderAll" {{ empty($gender) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="genderAll">All Genders</label>
                                    </li>
                                    <li>
                                        <input class="form-check-input" type="radio" name="gender" value="men" id="genderMale" {{ $gender === 'men' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="genderMale">Men</label>
                                    </li>
                                    <li>
                                        <input class="form-check-input" type="radio" name="gender" value="women" id="genderWomen" {{ $gender === 'women' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="genderWomen">Women</label>
                                    </li>
                                    <li>
                                        <input class="form-check-input" type="radio" name="gender" value="both" id="genderBoth" {{ $gender === 'both' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="genderBoth">Both (Women & Men)</label>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
            
            <!-- Filter Actions -->
            <div class="d-flex px-3 py-2 gap-3 flex-nowrap" style="background-color:#f3f3f3;">
                <a href="{{ route('product.listing', $clearAllParams) }}" class="col p-3 ctaBtn dark text-center text-decoration-none">Clear All</a>
                <button type="submit" class="col p-3 ctaBtn">Apply</button>
            </div>
        </form>
    </div>

</main>
@endsection

@push('styles')
<style>
    /* Brand Filter Chips */
    .brand-filters-wrapper {
        background: #f8f9fa;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .brand-filter-chip {
        display: inline-block;
        padding: 4px 14px;
        font-size: 13px;
        font-weight: 500;
        color: #495057;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        white-space: nowrap;
    }
    
    .brand-filter-chip:hover {
        background: #f1f3f5;
        border-color: #ced4da;
        color: #212529;
        text-decoration: none;
    }
    
    .brand-filter-chip.active {
        background: #E85D04;
        color: #fff;
        border-color: #E85D04;
    }
    
    .brand-filter-chip.active:hover {
        background: #d35400;
        border-color: #d35400;
        color: #fff;
    }
    
    .filter-count-badge {
        display: inline-block;
        background: #E85D04;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 1px 8px;
        border-radius: 12px;
        margin-left: 6px;
        min-width: 20px;
        text-align: center;
    }
    
    .filter-count-badge:empty {
        display: none;
    }
    
    /* Active Filters Badges */
    .active-filters .badge {
        background: #6c757d !important;
        font-weight: 400;
        font-size: 13px;
        padding: 6px 12px;
    }
    
    .active-filters .badge a {
        font-weight: 700;
        font-size: 16px;
        line-height: 1;
        opacity: 0.8;
    }
    
    .active-filters .badge a:hover {
        opacity: 1;
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        .brand-filters-wrapper {
            padding: 10px;
            overflow-x: auto;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: touch;
        }
        .brand-filters-wrapper .d-flex {
            flex-wrap: nowrap !important;
        }
        .brand-filter-chip {
            font-size: 12px;
            padding: 3px 12px;
            flex-shrink: 0;
        }
        .active-filters .badge {
            font-size: 11px;
            padding: 4px 8px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Update filter count badge
    function updateFilterCount() {
        var badge = document.getElementById('filterCountBadge');
        if (!badge) return;
        
        var count = 0;
        
        // Count selected brands from chips (active class)
        var activeBrands = document.querySelectorAll('.brand-filter-chip.active:not(:contains("All"))');
        count += activeBrands.length;
        
        // Count selected brands from checkboxes (offcanvas)
        var brandChecks = document.querySelectorAll('input[name="brand[]"]:checked');
        if (brandChecks.length > count) {
            count = brandChecks.length;
        }
        
        // Category filter
        var categoryRadio = document.querySelector('input[name="category"]:checked');
        if (categoryRadio && categoryRadio.value !== '') count++;
        
        // Gender filter
        var genderRadio = document.querySelector('input[name="gender"]:checked');
        if (genderRadio && genderRadio.value !== '') count++;
        
        // Price filter
        var minPrice = document.getElementById('min-pricevalue');
        var maxPrice = document.getElementById('max-pricevalue');
        if (minPrice && maxPrice && (minPrice.value || maxPrice.value)) count++;
        
        // Count subcategory
        var subcategoryInput = document.querySelector('input[name="subcategory"]');
        if (subcategoryInput && subcategoryInput.value) count++;
        
        badge.textContent = count > 0 ? count : '';
    }
    
    // Update on page load
    updateFilterCount();
    
    // Update when brand chips are clicked
    document.querySelectorAll('.brand-filter-chip').forEach(function(chip) {
        chip.addEventListener('click', function(e) {
            // Let the link handle navigation, but update count on next page load
            setTimeout(updateFilterCount, 100);
        });
    });
    
    // Update when checkbox/radio changes in offcanvas
    document.querySelectorAll('#listingFilterForm input[type="checkbox"], #listingFilterForm input[type="radio"]').forEach(function(input) {
        input.addEventListener('change', updateFilterCount);
    });
    
    // Price slider initialization
    var rangePrice = document.getElementById('slider-price');
    var filterForm = document.getElementById('listingFilterForm');
    var minInput = document.getElementById('min-pricevalue');
    var maxInput = document.getElementById('max-pricevalue');
    var priceTiers = filterForm ? filterForm.querySelectorAll('.js-price-tier') : [];
    var syncingTier = false;

    if (typeof noUiSlider !== 'undefined' && rangePrice) {
        var moneyFormat = typeof wNumb !== 'undefined'
            ? wNumb({ decimals: 0, thousand: ',' })
            : {
                to: function (value) { return Math.round(value).toLocaleString('en-IN'); },
                from: function (value) { return Number(String(value).replace(/,/g, '')); }
            };

        noUiSlider.create(rangePrice, {
            start: [{{ $sliderStartMin }}, {{ $sliderStartMax }}],
            step: 100,
            range: { 
                min: {{ $sliderMin }}, 
                max: {{ $sliderMax }} 
            },
            format: moneyFormat,
            connect: true,
            tooltips: false,
            animate: true
        });

        function isAllPricesSelected() {
            var allRadio = document.getElementById('price_all');
            return allRadio && allRadio.checked;
        }

        function updatePriceDisplay(values) {
            var minVal = values ? moneyFormat.from(values[0]) : 0;
            var maxVal = values ? moneyFormat.from(values[1]) : {{ $sliderMax }};
            
            document.getElementById('slider-price-min').innerHTML = moneyFormat.to(minVal);
            document.getElementById('slider-price-max').innerHTML = moneyFormat.to(maxVal);
            
            if (!syncingTier && !isAllPricesSelected()) {
                if (minInput) {
                    minInput.value = minVal > 0 ? minVal : '';
                }
                if (maxInput) {
                    maxInput.value = maxVal > 0 ? maxVal : '';
                }
            }
            updateFilterCount();
        }

        rangePrice.noUiSlider.on('update', function (values) {
            updatePriceDisplay(values);
        });

        rangePrice.noUiSlider.on('change', function () {
            if (!syncingTier) {
                priceTiers.forEach(function (radio) { 
                    radio.checked = false; 
                });
                updateFilterCount();
            }
        });

        priceTiers.forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (!radio.checked) return;
                
                var minVal = radio.getAttribute('data-min');
                var maxVal = radio.getAttribute('data-max');
                var sliderMin = minVal === '' || minVal === null ? 0 : Number(minVal);
                var sliderMax = maxVal === '' || maxVal === null ? {{ $sliderMax }} : Number(maxVal);
                
                syncingTier = true;
                rangePrice.noUiSlider.set([sliderMin, sliderMax]);
                syncingTier = false;
                
                if (minInput) {
                    minInput.value = minVal || '';
                }
                if (maxInput) {
                    maxInput.value = maxVal || '';
                }
                updateFilterCount();
            });
        });

        if (filterForm) {
            filterForm.addEventListener('submit', function () {
                if (isAllPricesSelected()) {
                    if (minInput) minInput.value = '';
                    if (maxInput) maxInput.value = '';
                }
            });
        }
    }
});
</script>
@endpush