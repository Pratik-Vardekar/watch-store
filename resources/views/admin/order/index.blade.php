@extends('layouts.admin')

@section('content')
    <div class="container-fluid my-2" id="orders-container">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Orders</h4>
                <p class="text-muted mb-0">Manage all customer orders</p>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#csvImportModal">
                    <i class="ti ti-file-import me-1"></i>
                    Import CSV
                </button>
                <a href="{{ route('order.export', request()->all()) }}" class="btn btn-success">
                    <i class="ti ti-file-export me-1"></i>
                    Export CSV
                </a>
            </div>
        </div>

        <div id="session-alerts">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Quick Status Filters -->
        <div class="d-flex flex-wrap gap-2 mb-4" id="status-filters">
            <a href="#" data-status=""
                class="btn btn-sm {{ !request('status') && !request('request_status') ? 'btn-primary' : 'btn-light' }} status-filter">
                All (<span class="order-count-total">{{ $orders->total() }}</span>)
            </a>
            <a href="#" data-request-status="pending"
                class="btn btn-sm {{ request('request_status') == 'pending' ? 'btn-warning' : 'btn-light' }} request-filter">
                Pending Requests ({{ $pendingRequestCount ?? 0 }})
            </a>
            @foreach (['processing', 'shipped', 'delivered', 'returns', 'cancelled'] as $status)
                <a href="#" data-status="{{ $status }}"
                    class="btn btn-sm {{ request('status') == $status ? 'btn-primary' : 'btn-light' }} status-filter">
                    {{ ucfirst($status) }}
                </a>
            @endforeach
        </div>

        <!-- Search & Filter Bar -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="order-filter-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Order ID / Customer name / Email" value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Payment Type</label>
                            <select name="payment_status" class="form-select">
                                <option value="">All Payments</option>
                                <option value="full" {{ request('payment_status') == 'full' ? 'selected' : '' }}>
                                    Full Payment
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-filter me-1"></i>
                                Filter
                            </button>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">&nbsp;</label>
                            <a href="#" id="reset-filters" class="btn btn-secondary w-100">
                                <i class="ti ti-refresh me-1"></i>
                                Reset filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Dynamic content will be loaded here -->
        <div id="orders-table-area">
            @include('admin.order.partials.order-table', ['orders' => $orders])
        </div>
    </div>

    @include('admin.partials.csv-import-modal', [
        'importRoute' => route('order.import'),
        'sampleRoute' => route('order.import.sample'),
        'title' => 'Import Orders CSV',
    ])
@endsection

@push('styles')
    <style>
        .bg-success-subtle {
            background-color: #d1fae5 !important;
        }

        .bg-danger-subtle {
            background-color: #fee2e2 !important;
        }

        .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            color: #4f5d73;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            min-height: 34px;
            border-radius: 0.375rem;
            background: #fff;
        }

        .page-link:hover {
            background-color: #f1f5f9;
            color: #1e293b;
            border-color: #cbd5e1;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }

        .rounded-3 {
            border-radius: 0.5rem !important;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 1rem;
            vertical-align: middle;
        }

        .table thead th {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .badge {
            font-weight: 500;
            padding: 0.35rem 0.65rem;
        }

        .table td .ti {
            opacity: 0.6;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }

            .btn-sm {
                font-size: 0.75rem;
            }

            .badge {
                font-size: 0.7rem;
            }

            .table> :not(caption)>*>* {
                padding: 0.75rem 0.5rem;
            }

            .page-link {
                min-width: 28px;
                min-height: 28px;
                font-size: 0.8rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderTableArea = document.getElementById('orders-table-area');
            const orderFilterForm = document.getElementById('order-filter-form');
            const statusFilters = document.querySelectorAll('.status-filter');
            const requestFilters = document.querySelectorAll('.request-filter');
            const resetBtn = document.getElementById('reset-filters');
            let currentParams = new URLSearchParams(window.location.search);

            // Update order table HTML
            function updateOrdersTable(html) {
                orderTableArea.innerHTML = html;

                // Update order-count-total from data-count attribute if provided
                const countSpan = document.querySelector('.order-count-total');
                const countVal = orderTableArea.querySelector('.order-table-root')?.getAttribute('data-count');
                if (countSpan && countVal) countSpan.textContent = countVal;

                // Re-attach pagination listeners
                attachPaginationAjax();
            }

            // AJAX Load of orders
            function fetchOrders(params = '') {
                let url = "{{ route('order.index') }}";
                if (params) {
                    url += '?' + params;
                }

                const exportBtn = document.querySelector(
                    'a.btn-success[href*="order-export"], a[href*="order-export"]');
                if (exportBtn) {
                    exportBtn.href = "{{ route('order.export') }}" + (params ? '?' + params : '');
                }

                orderTableArea.classList.add('opacity-50', 'pointer-events-none');
                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.ok ? res.text() : Promise.reject(res))
                    .then(html => {
                        updateOrdersTable(html);
                        orderTableArea.classList.remove('opacity-50', 'pointer-events-none');
                        window.history.replaceState(null, '', url);
                    })
                    .catch(() => {
                        orderTableArea.classList.remove('opacity-50', 'pointer-events-none');
                        alert('Failed to load orders. Please try again.');
                    });
            }

            // Form filter AJAX submit
            orderFilterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                let params = new URLSearchParams(new FormData(this));
                const activeStatus = document.querySelector('.status-filter.btn-primary');
                const activeRequest = document.querySelector('.request-filter.btn-warning');
                if (activeStatus && activeStatus.dataset.status) {
                    params.set('status', activeStatus.dataset.status);
                }
                if (activeRequest && activeRequest.dataset.requestStatus) {
                    params.set('request_status', activeRequest.dataset.requestStatus);
                }
                fetchOrders(params.toString());
            });

            // Status filter (all/processing/...)
            statusFilters.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    statusFilters.forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-light');
                    });
                    requestFilters.forEach(b => {
                        b.classList.remove('btn-warning');
                        b.classList.add('btn-light');
                    });
                    this.classList.remove('btn-light');
                    this.classList.add('btn-primary');
                    let params = new URLSearchParams(new FormData(orderFilterForm));
                    if (this.dataset.status) {
                        params.set('status', this.dataset.status);
                    } else {
                        params.delete('status');
                    }
                    params.delete('request_status');
                    fetchOrders(params.toString());
                });
            });

            requestFilters.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    statusFilters.forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-light');
                    });
                    requestFilters.forEach(b => {
                        b.classList.remove('btn-warning');
                        b.classList.add('btn-light');
                    });
                    this.classList.remove('btn-light');
                    this.classList.add('btn-warning');
                    let params = new URLSearchParams(new FormData(orderFilterForm));
                    params.delete('status');
                    if (this.dataset.requestStatus) {
                        params.set('request_status', this.dataset.requestStatus);
                    } else {
                        params.delete('request_status');
                    }
                    fetchOrders(params.toString());
                });
            });

            // Pagination AJAX
            function attachPaginationAjax() {
                let pagLinks = orderTableArea.querySelectorAll('.order-pagination-link');
                pagLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        let url = this.getAttribute('href');
                        if (!url) return;
                        let paramIdx = url.indexOf('?');
                        let params = paramIdx >= 0 ? url.slice(paramIdx + 1) : '';
                        fetchOrders(params);
                    });
                });
            }
            attachPaginationAjax();

            // Reset filters
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                orderFilterForm.reset();
                statusFilters.forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-light');
                });
                requestFilters.forEach(b => {
                    b.classList.remove('btn-warning');
                    b.classList.add('btn-light');
                });
                statusFilters[0].classList.remove('btn-light');
                statusFilters[0].classList.add('btn-primary');
                fetchOrders('');
            });

        });
    </script>
@endpush
