<style>
.searchWrapper {
    background: #ffffff;
    border-top: 1px solid #eee;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1050;
    max-height: calc(100vh - 50px);
    overflow: auto;
}
.searchWrapper .searchContainer {
    max-width: 760px;
    margin: 0 auto;
    position: relative;
    z-index: 5;
    background: #fff;
}
.searchWrapper .searchClose {
    position: absolute;
    right: 0;
    top: 0;
    background: none;
    border: 0;
    padding: 0;
    font-size: 15px;
    font-weight: 500;
    color: #222;
    text-decoration: none;
    cursor: pointer;
}
.searchWrapper .searchClose:hover {
    color: #000;
    text-decoration: underline;
}
.searchWrapper .searchBarWrap {
    height: auto !important;
    overflow: visible !important;
    background: transparent;
    border: 0;
    margin-top: 10px;
}
.searchWrapper .searchBar {
    height: 52px;
    border: 1px solid #2b2b2b !important;
    border-radius: 50px;
    background-color: #fff !important;
    background-repeat: no-repeat;
    background-position: 18px center;
    background-size: 18px auto;
    background-image: url("data:image/svg+xml,<svg viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'><path d='M16 24.7c5.6 0 10.2-4.6 10.2-10.2S21.6 4.3 16 4.3 5.8 8.9 5.8 14.5 10.4 24.7 16 24.7Z' fill='none' stroke='%231A1A1A' stroke-width='2'/><path d='M23.3 21.8L27.6 26.2' stroke='%231A1A1A' stroke-width='2' stroke-linecap='round'/></svg>");
    padding: 0 22px 0 48px;
    font-size: 15px;
    font-weight: 400;
    color: #222;
    outline: none;
    box-shadow: none;
}
.searchWrapper .searchBar::placeholder {
    color: #9aa0a6 !important;
    opacity: 1;
    font-weight: 400;
}
.searchWrapper .searchBar:focus {
    border: 1px solid #111 !important;
    background-color: #fff !important;
    box-shadow: none;
}
.searchWrapper .searchSuggestBox {
    display: block;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    margin-top: 8px;
    margin-bottom: 28px;
    padding: 22px 18px 12px 22px;
    max-height: min(52vh, 430px);
    overflow-y: auto;
    position: relative;
}
.searchWrapper .searchSuggestBox::-webkit-scrollbar {
    width: 8px;
}
.searchWrapper .searchSuggestBox::-webkit-scrollbar-track {
    background: transparent;
}
.searchWrapper .searchSuggestBox::-webkit-scrollbar-thumb {
    background: #E85D04;
    border-radius: 8px;
}
.searchPanelSection {
    margin-bottom: 1.4rem;
}
.searchPanelHead {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .85rem;
    font-size: 15px;
    font-weight: 700;
    color: #1f2937;
}
.searchPanelHead-left {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
}
.searchPanelHead svg {
    width: 18px;
    height: 18px;
    color: #E85D04;
    flex-shrink: 0;
}
.searchSuggestPills {
    display: flex;
    flex-wrap: wrap;
    gap: .65rem;
}
.searchWrapper .searchSuggestPills .suggestTag {
    display: inline-block;
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 50rem;
    padding: .35rem 1rem;
    font-size: .9rem;
    font-weight: 500;
    color: #6b46c1;
    text-decoration: none;
    white-space: nowrap;
    transition: none;
}
.searchWrapper .searchSuggestPills .suggestTag:hover,
.searchWrapper .searchSuggestPills .suggestTag.suggestSelected {
    background: #f8f5ff;
    color: #553c9a;
    border-color: #c4b5fd;
}
.searchCategoryGrid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .75rem;
}
.searchCategoryCard {
    display: flex;
    align-items: center;
    gap: .7rem;
    min-height: 58px;
    padding: .45rem .7rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fff;
    color: #374151;
    text-decoration: none;
}
.searchCategoryCard:hover,
.searchCategoryCard.suggestSelected {
    border-color: #E85D04;
    color: #111;
    text-decoration: none;
    background: #fffaf6;
}
.searchCategoryCard img {
    width: 36px;
    height: 36px;
    object-fit: cover;
    border-radius: 6px;
    background: #f3f4f6;
    flex-shrink: 0;
}
.searchCategoryCard span {
    font-size: .9rem;
    font-weight: 500;
    line-height: 1.25;
}
.searchHistoryClear {
    border: 0;
    background: none;
    padding: 0;
    font-size: .85rem;
    font-weight: 600;
    color: #E85D04;
    cursor: pointer;
}
.searchHistoryList {
    display: flex;
    flex-direction: column;
    border-top: 0;
}
.searchHistoryItem {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .7rem 0;
    border-bottom: 0;
}
.searchHistoryItem a {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    color: #1f2937;
    text-decoration: none;
}
.searchHistoryName {
    font-weight: 600;
    font-size: .95rem;
    line-height: 1.35;
}
.searchHistoryType {
    font-size: .72rem;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #9ca3af;
}
.searchHistoryRemove {
    width: 1.5rem;
    height: 1.5rem;
    border: 0;
    border-radius: 50%;
    background: transparent !important;
    color: #9ca3af;
    line-height: 1;
    font-size: 1.15rem;
    flex-shrink: 0;
    cursor: pointer;
}
.searchHistoryRemove:hover {
    color: #111;
    background: transparent !important;
}
.searchHistoryEmpty,
.searchResultEmpty {
    color: #6b7280;
    font-size: .9rem;
    margin: 0;
}
.searchTrendingProducts {
    padding: 4px 4px 12px;
}
.trendingProductList {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.trendingProductItem {
    display: block;
    color: #222;
    text-decoration: none;
}
.trendingProductItem:hover,
.trendingProductItem.suggestSelected {
    color: #111;
    text-decoration: none;
}
.trendingProductName {
    display: block;
    font-size: 15px;
    font-weight: 400;
    line-height: 1.35;
    color: #222;
}
.trendingProductPrice {
    display: block;
    margin-top: 2px;
    font-size: 15px;
    color: #222;
}
.suggestViewAll {
    display: inline-block;
    margin: .25rem 0 1rem;
    font-weight: 700;
    font-size: .9rem;
    color: #E85D04;
    text-decoration: none;
}
@media (max-width: 767px) {
    .searchCategoryGrid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .searchWrapper .searchBar {
        height: 46px;
        font-size: 14px;
    }
    .searchWrapper .searchSuggestBox {
        padding: 16px 12px 8px 14px;
        max-height: min(48vh, 360px);
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const suggestBox = document.getElementById('searchSuggestBox');
    const suggestList = document.querySelector('.js-search-suggest-list');
    const trendingBox = document.getElementById('searchTrendingProducts');
    const trendingList = document.querySelector('.js-search-trending-list');
    const searchForm = document.querySelector('.searchForm');
    const searchWrapper = document.getElementById('searchWrapper');
    const suggestUrl = searchForm?.dataset.suggestUrl;
    const HISTORY_KEY = 'tws_search_history';

    if (!searchInput || !suggestBox || !suggestList || !searchForm || !trendingBox || !trendingList) return;

    let debounceTimer = null;
    let currentQuery = '';
    let selectedIndex = -1;
    let lastDiscover = { trending: [], trending_searches: [], categories: [] };

    const iconTrend = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg>';
    const iconGrid = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>';
    const iconClock = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>';

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, function(s) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[s];
        });
    }

    function formatPrice(price) {
        const n = Number(price);
        if (!Number.isFinite(n) || n <= 0) return '';
        return '₹' + Math.round(n).toLocaleString('en-IN');
    }

    function getHistory() {
        try {
            const items = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
            return Array.isArray(items) ? items : [];
        } catch (e) {
            return [];
        }
    }

    function setHistory(items) {
        localStorage.setItem(HISTORY_KEY, JSON.stringify(items.slice(0, 8)));
    }

    function saveHistoryItem(item) {
        if (!item || !item.name) return;
        const items = getHistory().filter(function(row) {
            return String(row.name).toLowerCase() !== String(item.name).toLowerCase();
        });
        items.unshift({
            name: item.name,
            url: item.url || searchForm.action + '?search=' + encodeURIComponent(item.name),
            type: (item.type || 'search').toUpperCase()
        });
        setHistory(items);
    }

    function historyHtml() {
        const items = getHistory();
        if (!items.length) return '';

        return `
            <div class="searchPanelSection">
                <div class="searchPanelHead">
                    <span class="searchPanelHead-left">${iconClock} Search History</span>
                    <button type="button" class="searchHistoryClear js-clear-history">Clear All</button>
                </div>
                <div class="searchHistoryList">
                    ${items.map(function(item, index) {
                        return `
                            <div class="searchHistoryItem">
                                <a href="${escapeHtml(item.url)}" data-history-name="${escapeHtml(item.name)}" data-type="${escapeHtml((item.type || 'search').toLowerCase())}">
                                    <span class="searchHistoryName">${escapeHtml(item.name)}</span>
                                    <span class="searchHistoryType">${escapeHtml(item.type || 'SEARCH')}</span>
                                </a>
                                <button type="button" class="searchHistoryRemove js-remove-history" data-index="${index}" aria-label="Remove">×</button>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }

    function productListHtml(items, query) {
        return `
            <div class="searchPanelSection">
                <div class="searchPanelHead">
                    <span class="searchPanelHead-left">⚡ Trending Products</span>
                </div>
                <div class="trendingProductList">
                    ${items.map(function(item) {
                        const price = formatPrice(item.price);
                        const name = query ? highlightMatch(item.name, query) : escapeHtml(item.name);
                        return `
                            <a href="${escapeHtml(item.url)}" class="trendingProductItem" data-type="product" data-history-name="${escapeHtml(item.name)}">
                                <span class="trendingProductName">${name}</span>
                                ${price ? `<span class="trendingProductPrice">${price}</span>` : ''}
                            </a>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }

    function setPanel(el, html) {
        if (!el) return;
        if (html) {
            el.innerHTML = html;
            el.hidden = false;
            el.style.display = '';
        } else {
            el.innerHTML = '';
            el.hidden = true;
        }
    }

    function renderSuggestions(data) {
        if (data.mode === 'discover') {
            lastDiscover = data;
            let panelHtml = '';
            const searches = data.trending_searches || [];
            const categories = data.categories || [];
            const trending = data.trending || [];

            if (searches.length) {
                panelHtml += `
                    <div class="searchPanelSection">
                        <div class="searchPanelHead">
                            <span class="searchPanelHead-left">${iconTrend} Trending Searches</span>
                        </div>
                        <div class="searchSuggestPills">
                            ${searches.map(function(item) {
                                return `<a href="${escapeHtml(item.url)}" class="suggestTag" data-type="search" data-history-name="${escapeHtml(item.name)}">${escapeHtml(item.name)}</a>`;
                            }).join('')}
                        </div>
                    </div>
                `;
            }

            if (categories.length) {
                panelHtml += `
                    <div class="searchPanelSection">
                        <div class="searchPanelHead">
                            <span class="searchPanelHead-left">${iconGrid} Top Categories</span>
                        </div>
                        <div class="searchCategoryGrid">
                            ${categories.map(function(item) {
                                return `
                                    <a href="${escapeHtml(item.url)}" class="searchCategoryCard" data-type="category" data-history-name="${escapeHtml(item.name)}">
                                        ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}">` : ''}
                                        <span>${escapeHtml(item.name)}</span>
                                    </a>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }

            panelHtml += historyHtml();
            setPanel(suggestList, panelHtml);
            suggestBox.hidden = !panelHtml;
            suggestBox.style.display = panelHtml ? 'block' : 'none';
            setPanel(trendingList, trending.length ? productListHtml(trending) : '');
            trendingBox.hidden = !trending.length;
        } else if (data.mode === 'results') {
            suggestBox.hidden = true;
            suggestBox.style.display = 'none';
            setPanel(suggestList, '');

            if (data.results && data.results.length) {
                let html = productListHtml(data.results, currentQuery).replace('⚡ Trending Products', '⚡ Results');
                if (data.search_url) {
                    html += `<a href="${escapeHtml(data.search_url)}" class="suggestViewAll">View all results →</a>`;
                }
                setPanel(trendingList, html);
                trendingBox.hidden = false;
            } else {
                setPanel(trendingList, `<p class="searchResultEmpty">No products found for “${escapeHtml(currentQuery)}”.</p>`);
                trendingBox.hidden = false;
            }
        }

        selectedIndex = -1;
    }

    function highlightMatch(text, query) {
        const safe = escapeHtml(text);
        if (!query) return safe;
        try {
            const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return safe.replace(new RegExp('(' + escaped + ')', 'gi'), '<strong>$1</strong>');
        } catch (e) {
            return safe;
        }
    }

    function fetchSuggestions(query) {
        if (!suggestUrl) return;
        currentQuery = query.trim();
        const url = suggestUrl + '?q=' + encodeURIComponent(currentQuery);

        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(data) { renderSuggestions(data); })
            .catch(function() {});
    }

    function selectableItems() {
        return document.querySelectorAll('#searchWrapper .trendingProductItem, #searchWrapper .searchCategoryCard, #searchWrapper .suggestTag, #searchWrapper .searchHistoryItem a');
    }

    searchInput.addEventListener('input', function() {
        const query = this.value;
        if (debounceTimer) clearTimeout(debounceTimer);

        if (query.trim() === '') {
            renderSuggestions(Object.assign({ mode: 'discover' }, lastDiscover));
            fetchSuggestions('');
            return;
        }

        debounceTimer = setTimeout(function() {
            fetchSuggestions(query);
        }, 300);
    });

    searchInput.addEventListener('focus', function() {
        fetchSuggestions(this.value);
    });

    searchInput.addEventListener('keydown', function(e) {
        const items = selectableItems();
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.key === 'Enter' && selectedIndex >= 0 && items[selectedIndex]) {
            e.preventDefault();
            items[selectedIndex].click();
        } else if (e.key === 'Escape' && searchWrapper) {
            bootstrap.Collapse.getOrCreateInstance(searchWrapper).hide();
        }
    });

    function updateSelection(items) {
        items.forEach(function(item, index) {
            item.classList.toggle('suggestSelected', index === selectedIndex);
            if (index === selectedIndex) item.scrollIntoView({ block: 'nearest' });
        });
    }

    function bindPanelClicks(root) {
        root.addEventListener('click', function(e) {
            const clearBtn = e.target.closest('.js-clear-history');
            if (clearBtn) {
                e.preventDefault();
                setHistory([]);
                renderSuggestions(Object.assign({ mode: 'discover' }, lastDiscover));
                return;
            }

            const removeBtn = e.target.closest('.js-remove-history');
            if (removeBtn) {
                e.preventDefault();
                const items = getHistory();
                items.splice(Number(removeBtn.dataset.index), 1);
                setHistory(items);
                renderSuggestions(Object.assign({ mode: 'discover' }, lastDiscover));
                return;
            }

            const link = e.target.closest('a[href]');
            if (!link) return;
            saveHistoryItem({
                name: link.dataset.historyName || link.textContent.trim(),
                url: link.getAttribute('href'),
                type: link.dataset.type || 'search'
            });
        });
    }

    bindPanelClicks(suggestList);
    bindPanelClicks(trendingList);

    searchForm.addEventListener('submit', function(e) {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            e.preventDefault();
            return;
        }
        saveHistoryItem({
            name: query,
            url: searchForm.action + '?search=' + encodeURIComponent(query),
            type: 'search'
        });
    });

    if (searchWrapper) {
        searchWrapper.addEventListener('shown.bs.collapse', function() {
            searchInput.focus();
            fetchSuggestions(searchInput.value);
        });
    }
});
</script>
@endpush
