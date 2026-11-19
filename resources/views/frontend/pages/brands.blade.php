@extends('layouts.front')

@section('title', 'Brands')

@section('content')
    <main class="pageWrapper">

        {{-- Banner Section --}}
        <section class="inBannerSecton position-relative overflow-hidden w-100 d-flex">
            <img src="{{ asset('frontend/assets/images/prodListBanner.jpg') }}" class="inBanImg" alt="Brands">
            <div class="container position-relative z-1 d-flex flex-column justify-content-center align-items-start">
                <nav class="breadcrumb mb-0 text-white animateThis slideTop">
                    <a class="breadcrumb-item" href="{{ url('/') }}">Home</a>
                    <span class="breadcrumb-item text-white">Brands</span>
                </nav>
                <h1 class="sectTitle text-white animateThis slideTop">Brands</h1>
            </div>
        </section>

        {{-- Main Catalog Section --}}
        <section class="py-sm-5 py-4 bgGray">
            <div class="container">

                {{-- Toolbar / Filter Bar matching Product Listing --}}
                <div
                    class="filterSortWrap d-flex flex-wrap align-items-stretch bg-white bg-opacity-75 overflow-hidden mb-sm-5 mb-4 rounded shadow">

                    {{-- Brand Count Display --}}
                    <div class="prodListCount col-sm col-12 order-sm-2 p-2 d-flex justify-content-center align-items-center text-center"
                        style="font-size:85%;">
                        <span>Showing <strong class="txtRed">{{ $totalCount }}</strong> Brands</span>
                    </div>

                    {{-- Filter Button --}}
                    <div class="col-sm col-6 order-sm-1">
                        <button class="filterSortBtn" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#filterMain">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z" />
                            </svg>
                            Filters
                        </button>
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="col-sm col-6 order-sm-3 text-end">
                        <form method="GET" action="{{ route('brands') }}" id="sortForm">
                            @if (!empty($search))
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            @if (!empty($letter))
                                <input type="hidden" name="letter" value="{{ $letter }}">
                            @endif
                            <select name="sort" class="form-select rounded-0 filterSortBtn"
                                onchange="this.form.submit()">
                                <option value="name_asc" {{ empty($sort) || $sort === 'name_asc' ? 'selected' : '' }}>
                                    Brands (A-Z)</option>
                                <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Brands (Z-A)
                                </option>
                                <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Most Watches</option>
                                <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Recently Added</option>
                            </select>
                        </form>
                    </div>

                </div>

                {{-- Brands Grid --}}
                <div class="productListing mb-5 row g-xl-4 g-md-3 g-2 row-cols-xl-4 row-cols-md-3 row-cols-2">
                    @forelse($brands as $brand)
                        @php
                            $targetUrl = route('brand.products', ['slug' => $brand->slug]);
                        @endphp

                        <div class="col">
                            <div
                                class="brandCardItem p-4 h-100 d-flex flex-column justify-content-between text-center position-relative">

                                <a href="{{ $targetUrl }}"
                                    class="brandCardLogo d-flex align-items-center justify-content-center p-3 text-decoration-none"
                                    title="View {{ $brand->name }} Watches">

                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="img-fluid brandImg"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/tws_logo.png') }}';">
                                </a>

                            </div>
                        </div>

                    @empty
                        <div class="col-12">
                            <div class="bg-white rounded shadow-sm p-5 text-center">
                                <p class="mb-0 text-muted">No brands found matching your selection.</p>
                                <a href="{{ route('brands') }}" class="btn btn-outline-dark mt-3">
                                    Reset Filters
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>



            </div>
        </section>

        {{-- Filter Offcanvas --}}
        <div class="offcanvas offcanvas-start filterMain" tabindex="-1" id="filterMain">
            <form method="GET" action="{{ route('brands') }}" class="d-flex flex-column h-100" id="listingFilterForm">
                @if ($sort)
                    <input type="hidden" name="sort" value="{{ $sort }}">
                @endif

                <div class="offcanvas-header" style="background-color:#f3f3f3;">
                    <h5 class="offcanvas-title fs-6" style="font-weight:800;">FILTERS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body">
                    <div class="filterBox mb-3">
                        <ul class="accdFilters clearfix" id="filterWrap">

                            {{-- Search Filter --}}
                            <li>
                                <button class="rfi_head" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#fltr_Search" aria-expanded="true" aria-controls="fltr_Search">Search
                                    Brand</button>
                                <div class="collapse show" id="fltr_Search" data-bs-parent="#filterWrap">
                                    <div class="p-3">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search brand name..." value="{{ $search ?? '' }}">
                                    </div>
                                </div>
                            </li>

                            {{-- Alphabet A-Z Filter --}}
                            <li>
                                <button class="rfi_head" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#fltr_Letter" aria-expanded="true"
                                    aria-controls="fltr_Letter">Alphabet (A–Z)</button>
                                <div class="collapse show" id="fltr_Letter" data-bs-parent="#filterWrap">
                                    <ul class="afiContent">
                                        <li>
                                            <input class="form-check-input" type="radio" name="letter" value=""
                                                id="letter_all" {{ empty($letter) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="letter_all">All Brands</label>
                                        </li>
                                        @foreach ($availableLetters as $char)
                                            <li>
                                                <input class="form-check-input" type="radio" name="letter"
                                                    value="{{ $char }}" id="letter_{{ $char }}"
                                                    {{ $letter === $char ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="letter_{{ $char }}">{{ $char }}</label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>

                <div class="d-flex px-3 py-2 gap-3 flex-nowrap" style="background-color:#f3f3f3;">
                    <a href="{{ route('brands') }}" class="col p-3 ctaBtn dark text-center text-decoration-none">Clear
                        All</a>
                    <button type="submit" class="col p-3 ctaBtn">Apply</button>
                </div>
            </form>
        </div>

    </main>
@endsection