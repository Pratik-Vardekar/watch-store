(function () {
    const form = document.querySelector('.searchForm');
    if (!form) return;

    const input = form.querySelector('.js-search-input');
    const clearBtn = form.querySelector('.js-search-clear');
    const box = form.querySelector('#searchSuggestBox');
    const list = form.querySelector('.js-search-suggest-list');
    const suggestUrl = form.dataset.suggestUrl;
    const HISTORY_KEY = 'cheriks_search_history';
    const HISTORY_MAX = 8;

    if (!input || !box || !list || !suggestUrl) return;

    let timer = null;
    let activeIndex = -1;
    let items = [];
    let controller = null;
    let discoverCache = null;
    let keepPanelOpen = false;
    let keepPanelTimer = null;

    const icons = {
        trending: '<svg class="searchPanelIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>',
        grid: '<svg class="searchPanelIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>',
        clock: '<svg class="searchPanelIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
    };

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getHistory() {
        try {
            const raw = localStorage.getItem(HISTORY_KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed.filter((item) => item && item.name) : [];
        } catch (e) {
            return [];
        }
    }

    function saveHistory(term) {
        const name = String(term || '').trim();
        if (!name) return;

        const next = [{ name: name, url: form.action + '?search=' + encodeURIComponent(name) }]
            .concat(getHistory().filter((item) => item.name.toLowerCase() !== name.toLowerCase()))
            .slice(0, HISTORY_MAX);

        localStorage.setItem(HISTORY_KEY, JSON.stringify(next));
    }

    function removeHistory(term) {
        const next = getHistory().filter((item) => item.name.toLowerCase() !== String(term || '').toLowerCase());
        localStorage.setItem(HISTORY_KEY, JSON.stringify(next));
    }

    function clearHistory() {
        localStorage.removeItem(HISTORY_KEY);
    }

    function syncClearBtn() {
        if (!clearBtn) return;
        clearBtn.hidden = !input.value.trim();
    }

    function hideBox() {
        if (keepPanelOpen) return;
        box.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
        items = [];
    }

    function showBox() {
        box.hidden = false;
        input.setAttribute('aria-expanded', 'true');
    }

    function lockPanelOpen() {
        keepPanelOpen = true;
    
        clearTimeout(keepPanelTimer);
    
        keepPanelTimer = setTimeout(function () {
            keepPanelOpen = false;
        }, 1000);
    }
    function setActive(index) {
        items.forEach(function (el, i) {
            el.classList.toggle('is-active', i === index);
        });
        activeIndex = index;
    }

    function renderDiscover(data) {
        const trending = data.trending || [];
        const topCategories = data.top_categories || [];
        const history = getHistory();
        let html = '';

        html += '<div class="searchPanelSection">' +
            '<div class="searchPanelHead">' + icons.trending + '<span>Trending Searches</span></div>' +
            '<div class="searchTrendList">' +
            (trending.length
                ? trending.map(function (item) {
                    return '<a class="searchTrendPill" href="' + item.url + '" data-suggest-link data-history-term="' + escapeHtml(item.name) + '">' + escapeHtml(item.name) + '</a>';
                }).join('')
                : '<span class="searchHistoryEmpty">No trending searches</span>') +
            '</div></div>';

        html += '<div class="searchPanelSection">' +
            '<div class="searchPanelHead">' + icons.grid + '<span>Top Categories</span></div>' +
            '<div class="searchCatGrid">' +
            (topCategories.length
                ? topCategories.map(function (item) {
                    return '<a class="searchCatCard" href="' + item.url + '" data-suggest-link data-history-term="' + escapeHtml(item.name) + '">' +
                        '<img src="' + item.image + '" alt="" width="32" height="32">' +
                        '<span>' + escapeHtml(item.name) + '</span></a>';
                }).join('')
                : '<span class="searchHistoryEmpty">No categories found</span>') +
            '</div></div>';

        html += '<div class="searchPanelSection">' +
            '<div class="searchPanelHead">' + icons.clock + '<span>Search History</span>';

        if (history.length) {
            html += '<button type="button" class="searchHistoryClear searchPanelHead-right js-clear-history">Clear All</button>';
        }

        html += '</div>';

        if (history.length) {
            html += '<div class="searchHistoryList">' +
                history.map(function (item) {
                    return '<div class="searchHistoryItem">' +
                        '<a href="' + item.url + '" data-suggest-link>' + escapeHtml(item.name) + '</a>' +
                        '<button type="button" class="searchHistoryRemove js-remove-history" data-term="' + escapeHtml(item.name) + '" aria-label="Remove">&times;</button>' +
                        '</div>';
                }).join('') +
                '</div>';
        } else {
            html += '<p class="searchHistoryEmpty">There is no search history</p>';
        }

        html += '</div>';

        list.innerHTML = html;
        items = Array.prototype.slice.call(list.querySelectorAll('[data-suggest-link]'));
        showBox();
    }

    function renderResults(data, query) {
        const results = data.results || [];

        if (!results.length) {
            list.innerHTML = '<div class="searchResultEmpty">No matches for “' + escapeHtml(query) + '”</div>';
            items = [];
            showBox();
            return;
        }

        list.innerHTML = '<div class="searchResultList">' +
            results.map(function (item) {
                return '<a class="searchResultItem" href="' + item.url + '" data-suggest-link data-history-term="' + escapeHtml(item.name) + '">' +
                    '<img class="searchResultThumb" src="' + item.image + '" alt="" width="42" height="42">' +
                    '<span class="searchResultName">' + escapeHtml(item.name) + '</span></a>';
            }).join('') +
            '</div>';

        items = Array.prototype.slice.call(list.querySelectorAll('[data-suggest-link]'));
        showBox();
    }

    function fetchSuggestions(query) {
        if (controller) controller.abort();
        controller = new AbortController();

        const isDiscover = !query;
        if (isDiscover && discoverCache) {
            renderDiscover(discoverCache);
            return;
        }

        fetch(suggestUrl + '?q=' + encodeURIComponent(query || ''), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            signal: controller.signal,
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.mode === 'discover') {
                    discoverCache = data;
                    renderDiscover(data);
                } else {
                    renderResults(data, query);
                }
            })
            .catch(function (err) {
                if (err.name !== 'AbortError') hideBox();
            });
    }

    function refreshDiscoverPanel() {
        if (discoverCache) {
            renderDiscover(discoverCache);
            return;
        }
        fetchSuggestions('');
    }

    function isHistoryControl(target) {
        return !!(target && target.closest && (
            target.closest('.js-clear-history') ||
            target.closest('.js-remove-history')
        ));
    }

    function isInsideSearchForm(event) {
        if (typeof event.composedPath === 'function') {
            return event.composedPath().indexOf(form) !== -1;
        }
        return form.contains(event.target);
    }

    function handleHistoryAction(e) {
        const clearHistoryBtn = e.target.closest('.js-clear-history');
        const removeBtn = e.target.closest('.js-remove-history');

        if (!clearHistoryBtn && !removeBtn) return false;

        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') {
            e.stopImmediatePropagation();
        }

        lockPanelOpen();

        if (clearHistoryBtn) {
            clearHistory();
        } else {
            removeHistory(removeBtn.getAttribute('data-term'));
        }

        setTimeout(function () {
            refreshDiscoverPanel();
            showBox();
            input.focus();
        
            // allow outside click after panel is stable
            setTimeout(function () {
                keepPanelOpen = false;
            }, 200);
        }, 0);
        
        return true;
    }

    input.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(timer);
        syncClearBtn();
        timer = setTimeout(function () { fetchSuggestions(query); }, 180);
    });

    input.addEventListener('focus', function () {
        fetchSuggestions(this.value.trim());
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            input.value = '';
            syncClearBtn();
            input.focus();
            fetchSuggestions('');
        });
    }

    form.addEventListener('submit', function () {
        saveHistory(input.value);
    });

    // Capture early so re-render + document outside-click cannot close the panel.
    list.addEventListener('pointerdown', function (e) {
        if (isHistoryControl(e.target)) {
            e.preventDefault();
            e.stopPropagation();
            lockPanelOpen();
        }
    }, true);

    list.addEventListener('mousedown', function (e) {
        if (isHistoryControl(e.target)) {
            e.preventDefault();
            e.stopPropagation();
            lockPanelOpen();
        }
    }, true);

    list.addEventListener('click', function (e) {
        if (handleHistoryAction(e)) return;

        const link = e.target.closest('[data-suggest-link]');
        if (link && link.getAttribute('data-history-term')) {
            saveHistory(link.getAttribute('data-history-term'));
        }
    }, true);

    input.addEventListener('keydown', function (e) {
        if (box.hidden || !items.length) {
            if (e.key === 'Escape') hideBox();
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive(activeIndex < items.length - 1 ? activeIndex + 1 : 0);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(activeIndex > 0 ? activeIndex - 1 : items.length - 1);
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            items[activeIndex].click();
        } else if (e.key === 'Escape') {
            hideBox();
        }
    });

    document.addEventListener('click', function (e) {
        if (isHistoryControl(e.target)) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }
    
        if (keepPanelOpen) {
            return;
        }
    
        if (!isInsideSearchForm(e)) {
            hideBox();
        }
    }, true);

    syncClearBtn();
})();
