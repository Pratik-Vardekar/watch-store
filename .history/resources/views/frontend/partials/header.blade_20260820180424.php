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
                        <button class="siteMenuBtn searchMenuBtn" type="button" data-bs-toggle="collapse" data-bs-target="#searchWrapper" aria-expanded="false">
                            <img src="{{ asset('frontend/assets/images/icons/loupe.svg') }}" alt="Search">
                        </button>
                    </div>
                    <div class="col">
                        @auth
                            <div class="profileMenuWrap">
                                <a href="{{ route('profile.profile') }}" class="siteMenuBtn" title="My Profile">
                                    <img src="{{ asset('frontend/assets/images/icons/user.svg') }}" alt="My Profile">
                                </a>
                                <div class="myAccountWrap profileHoverMenu">
                                    <ul class="list-unstyled mb-0 p-3 d-flex flex-column gap-2">
                                        <li>
                                            <a href="{{ route('profile.profile') }}" class="profileHoverLink">Profile</a>
                                        </li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="profileHoverLogout">Logout</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
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
                <!-- Navigation Menu - Keep your existing menu here -->
                <div class="NavigHolder d-flex flex-column flex-lg-row h-100 justify-content-lg-center align-items-lg-stretch">
                    <!-- Your existing navigation code -->
                </div>
            </div>
        </div>
 
    </div>
 
    <!-- ===== SEARCH WRAPPER - LINE WISE LAYOUT ===== -->
    <div class="searchWrapper collapse" id="searchWrapper">
        <div class="px-md-5 px-3 searchContainer">
            <div class="py-5 pb-md-5 pb-4 px-0 px-lg-3 container-xxl">
 
                <button type="button" class="searchClose" data-bs-toggle="collapse" data-bs-target="#searchWrapper" aria-expanded="true">Close</button>
 
                <form class="searchForm" action="{{ route('search') }}" method="GET" data-suggest-url="{{ route('search.suggest') }}" autocomplete="off">
                    <div class="searchBarWrap mb-4 overflow-hidden d-flex align-items-center">
                        <input 
                            type="search" 
                            name="search" 
                            placeholder="Search for watches, brands and more" 
                            class="w-100 h-100 rounded-pill searchBar js-search-input" 
                            role="combobox" 
                            aria-expanded="false" 
                            aria-controls="searchSuggestBox" 
                            aria-autocomplete="list"
                            id="searchInput"
                            autocomplete="off"
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div id="searchSuggestBox" class="searchSuggestBox" style="display:none;">
                        <div class="js-search-suggest-list"></div>
                    </div>
                </form>
 
            </div>
        </div>
    </div>
 
 </header>
 
 @push('styles')
 <style>
 /* ============================================================
    SEARCH WRAPPER
    ============================================================ */
 .searchWrapper {
    background: #ffffff;
    border-top: 1px solid #e9ecef;
    position: relative;
    z-index: 1050;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
 }
 
 .searchWrapper .searchContainer {
    max-width: 900px;
    margin: 0 auto;
 }
 
 .searchClose {
    background: none;
    border: none;
    font-size: 14px;
    color: #6c757d;
    text-decoration: underline;
    cursor: pointer;
    float: right;
    padding: 8px 0;
    transition: color 0.2s ease;
 }
 
 .searchClose:hover {
    color: #E85D04;
 }
 
 /* Search Bar */
 .searchBarWrap {
    background: #f8f9fa;
    border-radius: 50px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
 }
 
 .searchBarWrap:focus-within {
    border-color: #E85D04;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(232, 93, 4, 0.1);
 }
 
 .searchBar {
    border: none;
    outline: none;
    padding: 16px 28px;
    font-size: 16px;
    background: transparent;
    height: 60px;
    color: #212529;
 }
 
 .searchBar::placeholder {
    color: #adb5bd;
 }
 
 /* ============================================================
    SEARCH SUGGEST BOX - LINE WISE LAYOUT
    ============================================================ */
 .searchSuggestBox {
    display: none;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
    max-height: 550px;
    overflow-y: auto;
    margin-top: 8px;
    padding: 0;
    position: relative;
    z-index: 1060;
    border: 1px solid #f0f0f0;
 }
 
 .searchSuggestBox::-webkit-scrollbar {
    width: 4px;
 }
 
 .searchSuggestBox::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
 }
 
 .searchSuggestBox::-webkit-scrollbar-thumb {
    background: #c1c7cd;
    border-radius: 4px;
 }
 
 /* ============================================================
    SECTION HEADER
    ============================================================ */
 .suggestSection {
    padding: 16px 20px 8px 20px;
 }
 
 .suggestSection:last-child {
    padding-bottom: 16px;
 }
 
 .suggestSection:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
 }
 
 .suggestSectionTitle {
    font-size: 13px;
    font-weight: 700;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 12px;
    padding-top: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
 }
 
 .suggestSectionTitle .title-icon {
    font-size: 16px;
 }
 
 /* ============================================================
    TRENDING PRODUCTS - LINE WISE
    ============================================================ */
 .trendingList {
    display: flex;
    flex-direction: column;
    gap: 6px;
 }
 
 .trendingItem {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px 14px;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    background: #fafbfc;
    border: 1px solid transparent;
 }
 
 .trendingItem:hover {
    background: #f8f9fa;
    border-color: #e9ecef;
    text-decoration: none;
    transform: translateX(4px);
 }
 
 .trendingItem.suggestSelected {
    background: #f1f3f5;
    border-color: #dee2e6;
 }
 
 .trendingItem .trendingNumber {
    font-size: 13px;
    font-weight: 700;
    color: #adb5bd;
    min-width: 24px;
    text-align: center;
 }
 
 .trendingItem .trendingImage {
    width: 48px;
    height: 48px;
    object-fit: contain;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px;
    flex-shrink: 0;
    border: 1px solid #f1f3f5;
 }
 
 .trendingItem .trendingInfo {
    flex: 1;
    min-width: 0;
 }
 
 .trendingItem .trendingName {
    font-size: 14px;
    color: #212529;
    font-weight: 500;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
 }
 
 .trendingItem .trendingBrand {
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
 }
 
 .trendingItem .trendingPrice {
    font-size: 15px;
    color: #E85D04;
    font-weight: 700;
    flex-shrink: 0;
    margin-left: 8px;
    background: #fff5f0;
    padding: 4px 12px;
    border-radius: 6px;
 }
 
 /* ============================================================
    SEARCH RESULTS - LINE WISE
    ============================================================ */
 .searchResultItem {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px 14px;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
 }
 
 .searchResultItem:hover {
    background: #f8f9fa;
    text-decoration: none;
 }
 
 .searchResultItem.suggestSelected {
    background: #f1f3f5;
 }
 
 .searchResultItem .resultImage {
    width: 48px;
    height: 48px;
    object-fit: contain;
    flex-shrink: 0;
    border-radius: 8px;
    background: #f8f9fa;
    padding: 4px;
    border: 1px solid #f1f3f5;
 }
 
 .searchResultItem .resultInfo {
    flex: 1;
    min-width: 0;
 }
 
 .searchResultItem .resultName {
    font-size: 14px;
    color: #212529;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
 }
 
 .searchResultItem .resultPrice {
    font-size: 14px;
    color: #E85D04;
    font-weight: 600;
 }
 
 .searchResultItem .resultType {
    font-size: 12px;
    color: #6c757d;
    text-transform: capitalize;
 }
 
 .suggestHighlight {
    background: #fff3cd;
    padding: 0 2px;
    border-radius: 2px;
 }
 
 /* View All Link */
 .suggestViewAll {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px;
    margin-top: 4px;
    color: #E85D04;
    font-weight: 600;
    text-decoration: none;
    border-top: 1px solid #f0f0f0;
    border-radius: 0 0 16px 16px;
    transition: all 0.2s ease;
 }
 
 .suggestViewAll:hover {
    background: #f8f9fa;
    text-decoration: none;
    color: #d35400;
 }
 
 .suggestViewAll .arrow {
    transition: transform 0.2s ease;
 }
 
 .suggestViewAll:hover .arrow {
    transform: translateX(4px);
 }
 
 /* Empty State */
 .suggestEmpty {
    padding: 40px 20px;
    text-align: center;
    color: #6c757d;
 }
 
 .suggestEmpty .emptyIcon {
    font-size: 40px;
    margin-bottom: 12px;
    display: block;
 }
 
 .suggestEmpty p {
    margin-bottom: 4px;
 }
 
 .suggestEmpty .subtext {
    font-size: 13px;
    color: #adb5bd;
 }
 
 /* ============================================================
    MOBILE RESPONSIVE
    ============================================================ */
 @media (max-width: 767px) {
    .searchWrapper .searchContainer {
        padding: 0 16px;
    }
    
    .searchBar {
        padding: 12px 20px;
        height: 50px;
        font-size: 15px;
    }
    
    .searchSuggestBox {
        max-height: 400px;
        margin: 4px -12px 0 -12px;
        border-radius: 0 0 16px 16px;
    }
    
    .suggestSection {
        padding: 12px 16px 6px 16px;
    }
    
    .suggestSection:last-child {
        padding-bottom: 12px;
    }
    
    .trendingItem {
        padding: 8px 10px;
        gap: 10px;
    }
    
    .trendingItem .trendingImage {
        width: 40px;
        height: 40px;
    }
    
    .trendingItem .trendingName {
        font-size: 13px;
    }
    
    .trendingItem .trendingPrice {
        font-size: 13px;
        padding: 3px 10px;
    }
    
    .searchResultItem {
        padding: 8px 10px;
        gap: 10px;
    }
    
    .searchResultItem .resultImage {
        width: 40px;
        height: 40px;
    }
    
    .searchResultItem .resultName {
        font-size: 13px;
    }
 }
 </style>
 @endpush
 
 @push('scripts')
 <script>
 document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const suggestBox = document.getElementById('searchSuggestBox');
    const suggestList = document.querySelector('.js-search-suggest-list');
    const searchForm = document.querySelector('.searchForm');
    const suggestUrl = searchForm?.dataset.suggestUrl;
 
    if (!searchInput || !suggestBox || !suggestList) return;
 
    let debounceTimer = null;
    let currentQuery = '';
    let selectedIndex = -1;
 
    // ============================================================
    // RENDER SUGGESTIONS - LINE WISE LAYOUT
    // ============================================================
    function renderSuggestions(data) {
        if (!suggestList) return;
 
        let html = '';
 
        if (data.mode === 'discover') {
            // Trending Products - Line Wise
            if (data.trending && data.trending.length > 0) {
                html += `
                    <div class="suggestSection">
                        <div class="suggestSectionTitle">
                            <span class="title-icon">⚡</span> Trending Products
                        </div>
                        <div class="trendingList">
                            ${data.trending.map((item, index) => `
                                <a href="${item.url}" class="trendingItem" data-type="${item.type}" data-index="${index}">
                                    <span class="trendingNumber">${String(index + 1).padStart(2, '0')}</span>
                                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="trendingImage" onerror="this.style.display='none'">` : ''}
                                    <div class="trendingInfo">
                                        <div class="trendingName">${item.name}</div>
                                        ${item.brand ? `<div class="trendingBrand">${item.brand}</div>` : ''}
                                    </div>
                                    ${item.price ? `<div class="trendingPrice">₹${Math.round(item.price).toLocaleString('en-IN')}</div>` : ''}
                                </a>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
 
        } else if (data.mode === 'results') {
            if (data.results && data.results.length > 0) {
                html += `
                    <div class="suggestSection">
                        <div class="suggestSectionTitle">
                            <span class="title-icon">🔍</span> Results for "${currentQuery}"
                        </div>
                        <div class="searchResults">
                            ${data.results.map(item => `
                                <a href="${item.url}" class="searchResultItem" data-type="${item.type}">
                                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="resultImage" onerror="this.style.display='none'">` : ''}
                                    <div class="resultInfo">
                                        <div class="resultName">${highlightMatch(item.name, currentQuery)}</div>
                                        ${item.price ? `<div class="resultPrice">₹${Math.round(item.price).toLocaleString('en-IN')}</div>` : ''}
                                        <div class="resultType">${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</div>
                                    </div>
                                </a>
                            `).join('')}
                        </div>
                        ${data.search_url ? `
                            <a href="${data.search_url}" class="suggestViewAll">
                                View All Results <span class="arrow">→</span>
                            </a>
                        ` : ''}
                    </div>
                `;
            } else {
                html += `
                    <div class="suggestEmpty">
                        <span class="emptyIcon">🔍</span>
                        <p>No products found for "<strong>${currentQuery}</strong>"</p>
                        <p class="subtext">Try checking your spelling or use more general terms.</p>
                    </div>
                `;
            }
        }
 
        suggestList.innerHTML = html;
        suggestBox.style.display = 'block';
        selectedIndex = -1;
    }
 
    // Highlight matching text
    function highlightMatch(text, query) {
        if (!query || !text) return text;
        try {
            const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp('(' + escaped + ')', 'gi');
            return text.replace(regex, '<strong class="suggestHighlight">$1</strong>');
        } catch {
            return text;
        }
    }
 
    // Fetch suggestions
    function fetchSuggestions(query) {
        if (!suggestUrl) return;
 
        currentQuery = query.trim();
 
        const url = currentQuery === '' 
            ? suggestUrl + '?q=' 
            : suggestUrl + '?q=' + encodeURIComponent(currentQuery);
 
        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderSuggestions(data);
            })
            .catch(() => {
                suggestBox.style.display = 'none';
            });
    }
 
    // Input event with debounce
    searchInput.addEventListener('input', function() {
        const query = this.value;
 
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }
 
        if (query.trim() === '') {
            suggestBox.style.display = 'block';
            fetchSuggestions('');
            return;
        }
 
        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });
 
    // Focus event
    searchInput.addEventListener('focus', function() {
        if (this.value.trim() === '') {
            fetchSuggestions('');
        } else {
            fetchSuggestions(this.value);
        }
    });
 
    // Click outside to close
    document.addEventListener('click', function(e) {
        const searchWrapper = document.querySelector('.searchWrapper');
        if (searchWrapper && !searchWrapper.contains(e.target)) {
            suggestBox.style.display = 'none';
        }
    });
 
    // Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = suggestList.querySelectorAll('.trendingItem, .searchResultItem');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.key === 'Enter') {
            if (selectedIndex >= 0 && items[selectedIndex]) {
                e.preventDefault();
                const item = items[selectedIndex];
                if (item.href) {
                    window.location.href = item.href;
                }
            }
        } else if (e.key === 'Escape') {
            suggestBox.style.display = 'none';
            selectedIndex = -1;
        }
    });
 
    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('suggestSelected');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('suggestSelected');
            }
        });
    }
 
    // Form submit
    searchForm.addEventListener('submit', function(e) {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            e.preventDefault();
            return;
        }
    });
 
    // Initial load - show trending
    setTimeout(function() {
        if (searchInput.value.trim() === '') {
            fetchSuggestions('');
        }
    }, 100);
 });
 </script>
 @endpush