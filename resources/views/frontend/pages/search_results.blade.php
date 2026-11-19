@extends('layouts.front')

@section('title', 'Search Results for "' . $search . '"')

@section('content')
    @php
        $search = $search ?? request('search', '');
        $gender = $gender ?? request('gender');
        $filter = $filter ?? request('filter');
        $maxPrice = $maxPrice ?? (request()->filled('max_price') ? (float) request('max_price') : null);
        $minPrice = $minPrice ?? (request()->filled('min_price') ? (float) request('min_price') : null);
        $sort = $sort ?? request('sort');
        $selectedBrandSlugs = $selectedBrandSlugs ?? (array) request('brand', []);
        $brandIds = $brandIds ?? [];
        $categoryId = $categoryId ?? (request()->filled('category') ? (int) request('category') : null);
        $subcategoryId = $subcategoryId ?? (request()->filled('subcategory') ? (int) request('subcategory') : null);
        $brands = $brands ?? collect();
        $products = $products ?? collect();
        $categories = $categories ?? collect();

        $shownCount = method_exists($products, 'count') ? $products->count() : 0;
        $totalCount = method_exists($products, 'total') ? $products->total() : $shownCount;

        $sliderMin = 0;
        $sliderMax = 100000;
        $sliderStartMin = $minPrice !== null && $minPrice > 0 ? (int) $minPrice : $sliderMin;
        $sliderStartMax = $maxPrice !== null && $maxPrice > 0 ? (int) $maxPrice : $sliderMax;

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

        $activeTierKey = null;
        foreach ($priceTiers as $index => $tier) {
            $tierMin = $tier['min'];
            $tierMax = $tier['max'];
            $minMatches =
                ($tierMin === null && $activeMinPrice === null) ||
                ($tierMin !== null && $activeMinPrice === (int) $tierMin);
            $maxMatches = $activeMaxPrice === (int) $tierMax;
            if ($minMatches && $maxMatches) {
                $activeTierKey = $index;
                break;
            }
        }

        $activeFilterCount = count($brandIds) + ($categoryId ? 1 : 0) + ($gender ? 1 : 0) + ($hasPriceFilter ? 1 : 0);
    @endphp

    <main class="pageWrapper">

        <!-- Top Banner -->
        <section class="inBannerSecton position-relative overflow-hidden w-100 d-flex">
            <img src="{{ asset('frontend/assets/images/prodListBanner.jpg') }}" class="inBanImg" alt="Search Results">
            <div class="container position-relative z-1 d-flex flex-column justify-content-center align-items-start">
                <nav class="breadcrumb mb-0 text-white animateThis slideTop">
                    <a class="breadcrumb-item text-white text-decoration-none" href="{{ url('/') }}">Home</a>
                    <a class="breadcrumb-item text-white text-decoration-none"
                        href="{{ route('search', ['search' => $search]) }}">Search</a>
                    <span class="breadcrumb-item text-white opacity-75">"{{ $search }}"</span>
                </nav>
                <h1 class="sectTitle text-white animateThis slideTop mb-0">Search Results</h1>
            </div>
        </section>

        <section class="py-sm-5 py-4 bgGray">
            <div class="container">

                <!-- Filter & Sort Bar (Identical to Product Listing Page) -->
                <div
                    class="filterSortWrap d-flex flex-wrap align-items-stretch bg-white bg-opacity-75 overflow-hidden mb-sm-5 mb-4 rounded shadow">
                    <!-- Product Count Display -->
                    <div class="prodListCount col-sm col-12 order-sm-2 p-2 d-flex justify-content-center align-items-center text-center"
                        style="font-size:85%;">
                        @if ($totalCount > 0)
                            @if (method_exists($products, 'hasPages') && $products->hasPages())
                                <span>Showing <strong
                                        class="txtRed">{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong> out
                                    of <strong>{{ $totalCount }}</strong> Watches</span>
                            @else
                                <span>Showing <strong class="txtRed">{{ $shownCount }}</strong> out of
                                    <strong>{{ $totalCount }}</strong> Watches</span>
                            @endif
                        @else
                            <span>Showing <strong class="txtRed">0</strong> Watches</span>
                        @endif
                    </div>
                    <div class="col-sm col-6 order-sm-1">
                        <button class="filterSortBtn" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#filterMain">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z" />
                            </svg>
                            Filters
                            @if ($activeFilterCount > 0)
                                <span class="badge bg-danger ms-1 rounded-pill"
                                    style="font-size: 0.75rem;">{{ $activeFilterCount }}</span>
                            @endif
                        </button>
                    </div>
                    <div class="col-sm col-6 order-sm-3 text-end">
                        <form method="GET" action="{{ route('search') }}" id="sortForm">
                            @foreach (request()->except(['sort', 'page']) as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $item)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <select name="sort" class="form-select rounded-0 filterSortBtn"
                                onchange="this.form.submit()">
                                <option value="relevance" {{ empty($sort) || $sort === 'relevance' ? 'selected' : '' }}>Relevance</option>
                                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price (low-high)
                                </option>
                                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price (high-low)
                                </option>
                                <option value="brand_asc" {{ $sort === 'brand_asc' ? 'selected' : '' }}>Brands (A-Z)
                                </option>
                                <option value="brand_desc" {{ $sort === 'brand_desc' ? 'selected' : '' }}>Brands (Z-A)
                                </option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="productListing mb-5 row g-xl-4 g-md-3 g-2 row-cols-xl-4 row-cols-md-3 row-cols-2">
                    @forelse($products as $product)
                        @include('frontend.partials.home-product-slide', [
                            'product' => $product,
                            'wrapperClass' => 'col',
                            'itemClass' => 'productItem text-center animateThis fadeIn in-view',
                            'showNewBadge' => (bool) ($product->is_new_arrival ?? false),
                        ])
                    @empty
                        <div class="col-12 w-100">
                            <!-- Empty State Card -->
                            <div class="search-empty-card bg-white rounded-3 shadow-sm p-4 p-md-5 text-center my-3 my-md-4 mx-auto"
                                style="max-width: 760px;">
                                <div
                                    class="search-empty-icon mx-auto mb-4 d-flex align-items-center justify-content-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        <line x1="8" y1="11" x2="14" y2="11"></line>
                                    </svg>
                                </div>

                                <h3 class="fw-bold mb-2 text-dark">No watches found for "<span
                                        class="txtRed">{{ $search }}</span>"</h3>
                                @if ($activeFilterCount > 0)
                                    <p class="text-muted mb-3 mx-auto"
                                        style="max-width: 520px; font-size: 0.95rem; line-height: 1.6;">
                                        No results match your search keywords and selected filters. Try clearing some
                                        filters or searching with different keywords.
                                    </p>
                                    <div class="mb-4">
                                        <a href="{{ route('search', ['search' => $search]) }}"
                                            class="btn btn-sm btn-outline-danger px-4 py-2 rounded-pill fw-semibold">
                                            Clear Selected Filters
                                        </a>
                                    </div>
                                @else
                                    <p class="text-muted mb-4 mx-auto"
                                        style="max-width: 520px; font-size: 0.95rem; line-height: 1.6;">
                                        We couldn't find any watches matching your query. Please double-check your spelling,
                                        try different keywords, or explore our collections below.
                                    </p>
                                @endif

                                <!-- Search Retry Form -->
                                <form action="{{ route('search') }}" method="GET" class="search-empty-form mx-auto mb-4"
                                    style="max-width: 480px;">
                                    <div class="input-group input-group-lg shadow-sm">
                                        <input type="text" name="search" class="form-control fs-6"
                                            value="{{ $search }}" placeholder="Search by brand, model, or style..."
                                            required autofocus>
                                        <button class="btn btn-dark px-4 fs-6 fw-semibold" type="submit">Search</button>
                                    </div>
                                </form>

                                <!-- Quick Links -->
                                <div class="pt-4 border-top">
                                    <p class="small text-uppercase fw-bold text-muted mb-3"
                                        style="letter-spacing: 0.08em;">Popular Categories & Quick Links</p>
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <a href="{{ route('product.listing') }}"
                                            class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 fw-semibold">Browse
                                            All Watches</a>
                                        <a href="{{ route('product.listing', ['gender' => 'men']) }}"
                                            class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">Men's
                                            Watches</a>
                                        <a href="{{ route('product.listing', ['gender' => 'women']) }}"
                                            class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">Women's
                                            Watches</a>
                                        <a href="{{ route('product.listing', ['filter' => 'best_selling']) }}"
                                            class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2">Bestsellers</a>
                                        <a href="{{ url('/') }}"
                                            class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">Home</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if (method_exists($products, 'hasPages') && $products->hasPages())
                    <div class="custom-pagination">
                        {{ $products->onEachSide(1)->links('frontend.partials.pagination') }}
                        <div class="custom-pagination-info">
                            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $totalCount }}
                            watches
                        </div>
                    </div>
                @endif

            </div>
        </section>

        <!-- Filter Offcanvas (Same as Product Listing Page) -->
        <div class="offcanvas offcanvas-start filterMain" tabindex="-1" id="filterMain">
            <form method="GET" action="{{ route('search') }}" class="d-flex flex-column h-100"
                id="listingFilterForm">
                <input type="hidden" name="search" value="{{ $search }}">
                @if ($filter)
                    <input type="hidden" name="filter" value="{{ $filter }}">
                @endif
                @if ($sort)
                    <input type="hidden" name="sort" value="{{ $sort }}">
                @endif
                @if (!empty($subcategoryId))
                    <input type="hidden" name="subcategory" value="{{ $subcategoryId }}">
                @endif

                <div class="offcanvas-header" style="background-color:#f3f3f3;">
                    <h5 class="offcanvas-title fs-6" style="font-weight:800;">FILTERS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">

                    <div class="filterBox mb-3">
                        <ul class="accdFilters clearfix" id="filterWrap">
                            <li>
                                <button class="rfi_head" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#fltr_Brands" aria-expanded="true"
                                    aria-controls="fltr_Brands">Brands</button>
                                <div class="collapse show" id="fltr_Brands" data-bs-parent="#filterWrap">
                                    <ul class="afiContent">
                                        @forelse($brands as $brand)
                                            <li>
                                                <input class="form-check-input" type="checkbox" name="brand[]"
                                                    value="{{ $brand->slug }}" id="brand_{{ $brand->slug }}"
                                                    {{ in_array($brand->slug, (array) $selectedBrandSlugs, true) || in_array($brand->id, (array) $brandIds, true) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="brand_{{ $brand->slug }}">{{ $brand->name }}</label>
                                            </li>
                                        @empty
                                            <li class="text-muted small">No brands available</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <button class="rfi_head" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#fltr_Category" aria-expanded="true"
                                    aria-controls="fltr_Category">Category</button>
                                <div class="collapse show" id="fltr_Category" data-bs-parent="#filterWrap">
                                    <ul class="afiContent">
                                        <li>
                                            <input class="form-check-input" type="radio" name="category"
                                                value="" id="category_all"
                                                {{ empty($categoryId) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="category_all">All Categories</label>
                                        </li>
                                        @forelse($categories as $category)
                                            <li>
                                                <input class="form-check-input" type="radio" name="category"
                                                    value="{{ $category->id }}" id="category_{{ $category->id }}"
                                                    {{ (int) $categoryId === (int) $category->id ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="category_{{ $category->id }}">{{ $category->name }}</label>
                                            </li>
                                        @empty
                                            <li class="text-muted small">No categories available</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <button class="rfi_head" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#fltr_Price" aria-expanded="{{ $hasPriceFilter ? 'true' : 'false' }}"
                                    aria-controls="fltr_Price">Price</button>
                                <div class="collapse {{ $hasPriceFilter ? 'show' : '' }}" id="fltr_Price"
                                    data-bs-parent="#filterWrap">
                                    <ul class="afiContent mb-3">
                                        <li>
                                            <input class="form-check-input js-price-tier" type="radio"
                                                name="price_tier" value="" id="price_all" data-min=""
                                                data-max="">
                                            <label class="form-check-label" for="price_all">All Prices</label>
                                        </li>
                                        @foreach ($priceTiers as $index => $tier)
                                            <li>
                                                <input class="form-check-input js-price-tier" type="radio"
                                                    name="price_tier" value="{{ $index }}"
                                                    id="price_tier_{{ $index }}"
                                                    data-min="{{ $tier['min'] ?? '' }}" data-max="{{ $tier['max'] }}"
                                                    {{ $activeTierKey === $index ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="price_tier_{{ $index }}">{{ $tier['label'] }}</label>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="w-100 rangeSliderBox">
                                        <div class="row g-0">
                                            <div class="col-12 mb-2">
                                                <div id="slider-price"></div>
                                            </div>
                                            <div class="col-6"><span class="price" id="slider-price-min"></span></div>
                                            <div class="col-6 text-end"><span class="price"
                                                    id="slider-price-max"></span></div>
                                            <input type="hidden" name="min_price" id="min-pricevalue"
                                                value="{{ $activeMinPrice ?? '' }}">
                                            <input type="hidden" name="max_price" id="max-pricevalue"
                                                value="{{ $activeMaxPrice ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <button class="rfi_head" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#fltr_Gender"
                                    aria-expanded="{{ in_array($gender, ['women', 'men', 'both'], true) ? 'true' : 'false' }}"
                                    aria-controls="fltr_Gender">Gender</button>
                                <div class="collapse {{ in_array($gender, ['women', 'men', 'both'], true) ? 'show' : '' }}"
                                    id="fltr_Gender" data-bs-parent="#filterWrap">
                                    <ul class="afiContent">
                                        <li>
                                            <input class="form-check-input" type="radio" name="gender" value=""
                                                id="genderAll" {{ empty($gender) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="genderAll">All Genders</label>
                                        </li>
                                        <li>
                                            <input class="form-check-input" type="radio" name="gender" value="men"
                                                id="genderMale" {{ $gender === 'men' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="genderMale">Men</label>
                                        </li>
                                        <li>
                                            <input class="form-check-input" type="radio" name="gender" value="women"
                                                id="genderWomen" {{ $gender === 'women' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="genderWomen">Women</label>
                                        </li>
                                        <li>
                                            <input class="form-check-input" type="radio" name="gender" value="both"
                                                id="genderBoth" {{ $gender === 'both' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="genderBoth">Both (Women & Men)</label>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="d-flex px-3 py-2 gap-3 flex-nowrap" style="background-color:#f3f3f3;">
                    <a href="{{ route('search', ['search' => $search]) }}"
                        class="col p-3 ctaBtn dark text-center text-decoration-none">Clear All</a>
                    <button type="submit" class="col p-3 ctaBtn">Apply</button>
                </div>
            </form>
        </div>

    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var rangePrice = document.getElementById('slider-price');
            var filterForm = document.getElementById('listingFilterForm');
            var minInput = document.getElementById('min-pricevalue');
            var maxInput = document.getElementById('max-pricevalue');
            var priceTiers = filterForm ?
                filterForm.querySelectorAll('.js-price-tier') : [];
            var syncingTier = false;

            if (!rangePrice || typeof noUiSlider === 'undefined') {
                return;
            }

            var moneyFormat = typeof wNumb !== 'undefined' ?
                wNumb({
                    decimals: 0,
                    thousand: ','
                }) : {
                    to: function(value) {
                        return Math.round(value).toLocaleString('en-IN');
                    },
                    from: function(value) {
                        return Number(String(value).replace(/,/g, ''));
                    }
                };

            noUiSlider.create(rangePrice, {
                start: [{{ $sliderStartMin }}, {{ $sliderStartMax }}],
                step: 100,
                range: {
                    min: {{ $sliderMin }},
                    max: {{ $sliderMax }}
                },
                format: moneyFormat,
                connect: true
            });

            function isAllPricesSelected() {
                var allRadio = document.getElementById('price_all');
                return allRadio && allRadio.checked;
            }

            rangePrice.noUiSlider.on('update', function(values) {
                var minEl = document.getElementById('slider-price-min');
                var maxEl = document.getElementById('slider-price-max');
                if (minEl) minEl.innerHTML = values[0];
                if (maxEl) maxEl.innerHTML = values[1];

                if (syncingTier && isAllPricesSelected()) {
                    if (minInput) minInput.value = '';
                    if (maxInput) maxInput.value = '';
                    return;
                }
                var minVal = moneyFormat.from(values[0]);
                var maxVal = moneyFormat.from(values[1]);
                if (minInput) {
                    minInput.value = minVal > 0 ? minVal : '';
                }
                if (maxInput) {
                    maxInput.value = maxVal > 0 ? maxVal : '';
                }
            });

            rangePrice.noUiSlider.on('change', function() {
                if (!syncingTier) {
                    priceTiers.forEach(function(radio) {
                        radio.checked = false;
                    });
                }
            });

            priceTiers.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    if (!radio.checked) {
                        return;
                    }
                    var minVal = radio.getAttribute('data-min');
                    var maxVal = radio.getAttribute('data-max');
                    var sMin = minVal === '' || minVal === null ? 0 : Number(minVal);
                    var sMax = maxVal === '' || maxVal === null ? {{ $sliderMax }} : Number(
                        maxVal);
                    syncingTier = true;
                    rangePrice.noUiSlider.set([sMin, sMax]);
                    syncingTier = false;
                    if (minInput) {
                        minInput.value = minVal || '';
                    }
                    if (maxInput) {
                        maxInput.value = maxVal || '';
                    }
                });
            });

            if (filterForm) {
                filterForm.addEventListener('submit', function() {
                    if (isAllPricesSelected()) {
                        if (minInput) minInput.value = '';
                        if (maxInput) maxInput.value = '';
                    }
                });
            }
        });
    </script>
@endpush