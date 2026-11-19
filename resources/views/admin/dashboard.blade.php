@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')

@section('content')
    <style>
        .transition-card {
            transition: all 0.3s ease;
        }

        .transition-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .card .col-6.text-end {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
        }

        .card .col-6.text-end>div {
            width: 120px;
        }
    </style>

    <div class="container-fluid">

        {{-- ── Page Title ─────────────────────────────────────────── --}}
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1 d-flex align-items-center gap-2">
                <h4 class="page-main-title m-0" style="color: #dc3545; font-weight: 700;">Watch Store CMS</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" style="color: #dc3545;">Watch</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);" style="color: #6c757d;">Dashboard</a></li>
                    <li class="breadcrumb-item active" style="color: #dc3545;">Watch CMS</li>
                </ol>
            </div>
        </div>

        {{-- ── Stat Cards with 4 DISTINCT VISUALS ─────────────────────────── --}}
        <div class="row">

            {{-- CARD 1: Categories with CIRCULAR PROGRESS RING --}}
            <div class="col-md-6 col-xxl-3">
                <a href="{{ route('category.index') }}">
                    <div class="card card-h-100 border shadow-sm rounded-4 transition-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="text-muted fs-sm text-uppercase text-truncate">Product Categories</h5>
                                    <div class="d-flex align-items-center gap-2 my-3">
                                        <div class="avatar-md flex-shrink-0">
                                            <span class="avatar-title text-bg-light rounded-circle fs-22">
                                                <i class="ti ti-category"></i>
                                            </span>
                                        </div>
                                        <h3 class="mb-0 fw-bold">{{ $totalCategories }}</h3>
                                    </div>
                                    <p class="mb-0 text-muted">
                                        <span class="text-danger me-2">{{ $categoryProgress }}% <i
                                                class="ti ti-trending-up ms-1"></i></span>
                                        <span class="text-nowrap">vs {{ $categoryTarget }} target</span>
                                    </p>
                                </div>
                                <div class="col-6 text-end">
                                    <div id="circular-progress-card"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- CARD 2: Products with SMALL BAR GRAPH --}}
            <div class="col-md-6 col-xxl-3">
                <a href="{{ route('product.index') }}">
                    <div class="card card-h-100 border shadow-sm rounded-4 transition-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="text-muted fs-sm text-uppercase text-truncate">Total Products</h5>
                                    <div class="d-flex align-items-center gap-2 my-3">
                                        <div class="avatar-md flex-shrink-0">
                                            <span class="avatar-title text-bg-light rounded-circle fs-22">
                                                <i class="ti ti-package"></i>
                                            </span>
                                        </div>
                                        <h3 class="mb-0 fw-bold">{{ $totalProducts }}</h3>
                                    </div>
                                    <p class="mb-0 text-muted">
                                        <span class="text-danger me-2">+{{ array_sum($barValues) }} <i
                                                class="ti ti-trending-up ms-1"></i></span>
                                        <span class="text-nowrap">added this week</span>
                                    </p>
                                </div>
                                <div class="col-6 text-end">
                                    <div id="bar-chart-card"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- CARD 4: Reviews with MINI DONUT CHART --}}
            <div class="col-md-6 col-xxl-3">
                <a href="{{ route('admin.review.index') }}">
                    <div class="card card-h-100 border shadow-sm rounded-4 transition-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="text-muted fs-sm text-uppercase text-truncate">Customer Reviews</h5>
                                    <div class="d-flex align-items-center gap-2 my-3">
                                        <div class="avatar-md flex-shrink-0">
                                            <span class="avatar-title text-bg-light rounded-circle fs-22">
                                                <i class="ti ti-star"></i>
                                            </span>
                                        </div>
                                        <h3 class="mb-0 fw-bold">{{ $totalReviews }}</h3>
                                    </div>
                                    <p class="mb-0 text-muted">
                                        <span class="text-warning me-2">Ratings <i class="ti ti-star ms-1"></i></span>
                                        <span class="text-nowrap">distribution</span>
                                    </p>
                                </div>
                                <div class="col-6 text-end">
                                    <div id="donut-card"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            {{-- CARD 3: Blogs with SPARKLINE --}}
            <div class="col-md-6 col-xxl-3">
                <a href="{{ route('customer.index') }}">
                    <div class="card card-h-100 border shadow-sm rounded-4 transition-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="text-muted fs-sm text-uppercase text-truncate">Customers</h5>
                                    <div class="d-flex align-items-center gap-2 my-3">
                                        <div class="avatar-md flex-shrink-0">
                                            <span class="avatar-title text-bg-light rounded-circle fs-22">
                                                <i class="ti ti-users"></i>
                                            </span>
                                        </div>
                                        <h3 class="mb-0 fw-bold">{{ number_format($totalUsers) }}</h3>
                                    </div>
                                    <p class="mb-0 text-muted">
                                        <span class="text-danger me-2">{{ $customerGrowth ?? 0 }}% <i
                                                class="ti ti-trending-up ms-1"></i></span>
                                        <span class="text-nowrap">growth this month</span>
                                    </p>
                                </div>
                                <div class="col-6 text-end">
                                    <div id="customer-sparkline-card"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>


        </div>{{-- end stat cards --}}

        {{-- ── Overview + Order Status Donut ──────────────────────── --}}
        <div class="row">

            <div class="col-xl-8">
                <div class="card card-h-100 border shadow-sm rounded-4 transition-card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title d-flex align-items-center gap-2">
                            <img src="{{ asset('frontend/assets/images/tws_logo.png') }}" alt="logo"
                                style="height: 20px; width: auto; object-fit: contain;">
                            Overview <span class="text-muted fw-normal fs-base">(Current Year)</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-1">Revenue</p>
                                        <h4 class="mb-2">
                                            <i class="ti ti-cash text-danger me-1"></i>
                                            ₹{{ number_format($totalRevenue, 0) }}
                                        </h4>
                                        <span class="badge fs-12 badge-soft-danger text-danger"
                                            style="background-color: #fee7ed; color: #dc3545 !important; border: 1px solid rgba(220, 53, 69, 0.2); font-weight: 600; padding: 4px 8px; border-radius: 6px; display: inline-flex; align-items: center;">
                                            <img src="{{ asset('frontend/assets/images/tws_logo.png') }}"
                                                alt="watch logo"
                                                style="height: 13px; width: auto; vertical-align: middle; margin-right: 4px;"
                                                onerror="this.style.display='none';this.nextElementSibling.style.display='inline-block';"><i
                                                class="fa-solid fa-clock me-1" style="display:none; color: #dc3545;"></i>
                                            Total
                                        </span>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-1">Orders</p>
                                        <h4 class="mb-2">
                                            <i class="ti ti-truck text-info me-1"></i>
                                            {{ number_format($totalOrders) }}
                                        </h4>
                                        <span class="badge fs-12 badge-soft-danger text-danger"><i
                                                class="ti ti-chevron-up"></i> All
                                            Time</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="text-muted mt-3 mb-1">Total Users</p>
                                        <h4 class="mb-2">
                                            <i class="ti ti-users-group me-1"></i>
                                            {{ number_format($totalUsers) }}
                                        </h4>
                                        <span class="badge fs-12 badge-soft-danger text-danger"><i
                                                class="ti ti-chevron-up"></i>
                                            Registered</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted mt-3 mb-1">Coupons</p>
                                        <h4 class="mb-2">
                                            <i class="ti ti-ticket me-1"></i>
                                            {{ $totalCoupons }}
                                        </h4>
                                        <span class="badge fs-12 text-bg-light">Active</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="text-muted mt-3 mb-1">Sub-Categories</p>
                                        <h4 class="mb-2">
                                            <i class="ti ti-category-2 me-1 text-secondary"></i>
                                            {{ $totalSubCategories }}
                                        </h4>
                                        <span class="badge fs-12 text-bg-light">Total</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted mt-3 mb-1">Sliders</p>
                                        <h4 class="mb-2">
                                            <i class="ti ti-slideshow me-1 text-primary"></i>
                                            {{ $totalSliders ?? 0 }}
                                        </h4>
                                        <span class="badge fs-12 text-bg-light">Active</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div id="dash-revenue-chart" class="apex-charts"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Status Donut --}}
            <div class="col-xl-4">
                <div class="card card-h-100 border shadow-sm rounded-4 transition-card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title">Order Status</h4>
                        <div>
                            <button class="btn btn-sm btn-light donut-toggle active" data-type="order">Order</button>
                            <button class="btn btn-sm btn-light donut-toggle" data-type="payment">Payment</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="most-leads-chart" class="apex-charts"></div>
                        <div class="row mt-2" id="donut-legend-row"></div>
                    </div>
                </div>
            </div>

        </div>{{-- end overview row --}}

        {{-- ── Recent Orders Table ─────────────────────────────────── --}}
        <div class="row">
            <div class="col-xl-12">
                <div data-table data-table-rows-per-page="5" class="card">
                    <div class="card-header border-light justify-content-between">
                        <h4 class="card-title">Recent Orders</h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="me-2 fw-semibold">Filter By:</span>
                            <div class="app-search">
                                <select data-table-filter="order-status" class="form-select form-control my-1 my-md-0">
                                    <option value="All">Order Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <i class="ti ti-briefcase app-search-icon text-muted"></i>
                            </div>
                            <div class="app-search">
                                <input data-table-search type="search" class="form-control"
                                    placeholder="Search orders..." />
                                <i class="ti ti-search app-search-icon text-muted"></i>
                            </div>
                            <div>
                                <select data-table-set-rows-per-page class="form-select form-control my-1 my-md-0">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom table-nowrap table-centered table-select table-hover w-100 mb-0">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th data-table-sort>Order ID</th>
                                    <th data-table-sort>Customer</th>
                                    <th data-table-sort>Date</th>
                                    <th data-table-sort>Amount</th>
                                    <th data-table-sort>Payment</th>
                                    <th data-table-sort data-column="order-status">Order Status</th>
                                    <th>•••</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    @php
                                        $orderStatusMap = [
                                            'pending' => 'bg-warning-subtle text-warning',
                                            'processing' => 'bg-info-subtle text-info',
                                            'shipped' => 'bg-primary-subtle text-primary',
                                            'delivered' => 'bg-success-subtle text-success',
                                            'cancelled' => 'bg-danger-subtle text-danger',
                                        ];
                                        $paymentStatusMap = [
                                            'pending' => 'bg-warning-subtle text-warning',
                                            'paid' => 'bg-success-subtle text-success',
                                            'failed' => 'bg-danger-subtle text-danger',
                                            'refunded' => 'bg-secondary-subtle text-secondary',
                                        ];
                                        $oClass = $orderStatusMap[$order->order_status] ?? 'text-bg-light';
                                        $pClass = $paymentStatusMap[$order->payment_status] ?? 'text-bg-light';
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('order.show', $order->id) }}"
                                                class="fw-medium text-primary" title="View Order Details">
                                                #{{ $order->order_id }}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($order->user)
                                                <div class="avatar-xs d-inline-block me-1">
                                                    <span
                                                        class="avatar-title bg-primary-subtle text-primary fw-bold rounded-circle">
                                                        {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <a href="#!" class="text-reset">{{ $order->user->name }}</a>
                                            @else
                                                <span class="text-muted">Guest</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <div class="text-nowrap">₹{{ number_format($order->grand_total, 2) }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $pClass }} p-1 text-capitalize">
                                                {{ $order->payment_status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $oClass }} p-1 text-capitalize">
                                                {{ $order->order_status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('order.show', $order->id) }}" class="text-muted fs-20"
                                                title="View details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div data-table-pagination-info="orders"></div>
                            <div data-table-pagination></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- end orders --}}

        {{-- ── Latest Products + Recent Users + Activity ──────────── --}}
        <div class="row">

            {{-- Latest Products --}}
            <div class="col-xl-4 col-lg-12">
                <div data-table data-table-rows-per-page="5" class="card">
                    <div class="card-header border-light justify-content-between">
                        <h4 class="card-title">Latest Products</h4>
                        <div class="app-search">
                            <input data-table-search type="search" class="form-control" placeholder="Search..." />
                            <i class="ti ti-search app-search-icon text-muted"></i>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom table-nowrap table-centered table-select table-hover w-100 mb-0">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th>Product</th>
                                    <th data-table-sort>Price</th>
                                    <th data-table-sort>Stock</th>
                                    <th>•••</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestProducts as $product)
                                    <tr>
                                        <td>
                                            <h5 class="fs-14 mb-0 fw-normal">{{ Str::limit($product->name, 22) }}</h5>
                                            <span class="text-muted fs-12">{{ $product->category->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>₹{{ number_format($product->selling_price, 2) }}</td>
                                        <td>{{ $product->quantity ?? 0 }}</td>
                                        <td>
                                            <a href="{{ route('product.edit', $product->id) }}" class="text-muted fs-20"
                                                title="Edit product">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No products yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div data-table-pagination-info="products"></div>
                            <div data-table-pagination></div>
                        </div>
                    </div>
                </div>
            </div>



            {{-- Recent Users --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title">Recent Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-n1">
                            <table class="table table-nowrap table-borderless table-centered mb-0">
                                <thead class="bg-light bg-opacity-50 thead-sm">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="avatar-xs d-inline-block me-1">
                                                    <span
                                                        class="avatar-title bg-primary-subtle text-primary fw-bold rounded-circle">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <span class="align-middle">{{ $user->name }}</span>
                                            </td>
                                            <td class="text-muted fs-12">{{ Str::limit($user->email, 20) }}</td>
                                            <td class="text-muted fs-12">{{ $user->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">No users found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card card-h-100 border shadow-sm rounded-4 transition-card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title">Recent Activity</h4>

                    </div>
                    <div class="card-body" data-simplebar style="max-height: 426px">
                        <div class="timeline timeline-users">
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-dot text-bg-primary"><i class="ti ti-package fs-md"></i></div>
                                <div class="timeline-content ps-3 pb-4">
                                    <h5 class="mb-1">{{ $totalProducts }} Products Listed</h5>
                                    <p class="mb-1 text-muted">Total active products available in the store.</p>
                                    <span class="text-primary fw-semibold">Store Inventory</span>
                                </div>
                            </div>
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-dot text-bg-success"><i class="ti ti-truck fs-md"></i></div>
                                <div class="timeline-content ps-3 pb-4">
                                    <h5 class="mb-1">{{ $totalOrders }} Orders Placed</h5>
                                    <p class="mb-1 text-muted">Total orders received across all time.</p>
                                    <span class="text-primary fw-semibold">Order Management</span>
                                </div>
                            </div>
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-dot text-bg-warning"><i class="ti ti-users fs-md"></i></div>
                                <div class="timeline-content ps-3 pb-4">
                                    <h5 class="mb-1">{{ $totalUsers }} Registered Users</h5>
                                    <p class="mb-1 text-muted">Users who have signed up on the platform.</p>
                                    <span class="text-primary fw-semibold">User Management</span>
                                </div>
                            </div>
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-dot text-bg-info"><i class="ti ti-star fs-md"></i></div>
                                <div class="timeline-content ps-3 pb-4">
                                    <h5 class="mb-1">{{ $totalReviews }} Product Reviews</h5>
                                    <p class="mb-1 text-muted">Customer reviews submitted for products.</p>
                                    <span class="text-primary fw-semibold">Reviews</span>
                                </div>
                            </div>
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-dot text-bg-danger"><i class="ti ti-ticket fs-md"></i></div>
                                <div class="timeline-content ps-3 pb-4">
                                    <h5 class="mb-1">{{ $totalCoupons }} Coupons Available</h5>
                                    <p class="mb-1 text-muted">Active discount coupons in the system.</p>
                                    <span class="text-primary fw-semibold">Promotions</span>
                                </div>
                            </div>
                            <div class="timeline-item d-flex align-items-stretch">
                                <div class="timeline-dot text-bg-secondary"><i class="ti ti-article fs-md"></i></div>
                                <div class="timeline-content ps-3">
                                    <h5 class="mb-1">{{ $totalBlogs }} Blog Posts Published</h5>
                                    <p class="mb-1 text-muted">Published articles live on the website.</p>
                                    <span class="text-primary fw-semibold">Content</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end bottom row --}}

    </div>{{-- end container-fluid --}}

@endsection

@section('page-scripts')
    <script>
        // ── PHP → JS data bridge ───────────────────────────────────────────────
        const dashData = {
            months: @json($revenueMonths),
            revenue: @json($revenueChartData),
            orders: @json($ordersChartData),

            // order_status donut
            orderDonutLabels: @json($donutLabels),
            orderDonutSeries: @json($donutSeries),

            // payment_status donut
            paymentDonutLabels: @json($paymentLabels),
            paymentDonutSeries: @json($paymentSeries),

            // sparklines
            catSparkline: @json($categorySparkline),
            prodSparkline: @json($productSparkline),
            blogSparkline: @json($blogSparkline),
            orderSparkline: @json($orderSparkline),

            // new users bar
            newUsersData: @json($newUsersPerMonth->pluck('total')->toArray()),

            // 4 distinct visuals data
            categoryProgress: @json($categoryProgress),
            barLabels: @json($barLabels),
            barValues: @json($barValues),
            donutRatingLabels: @json($donutRatingLabels),
            donutRatingValues: @json($donutRatingValues),
        };

        // ── Color maps keyed by status value ──────────────────────────────────
        const orderColors = {
            pending: '#ffbc00',
            processing: '#39afd1',
            shipped: '#727cf5',
            delivered: '#dc3545',
            cancelled: '#fa5c7c',
        };
        const paymentColors = {
            pending: '#ffbc00',
            paid: '#dc3545',
            failed: '#fa5c7c',
            refunded: '#adb5bd',
        };

        // ========== 4 DISTINCT VISUALS FOR STAT CARDS ==========

        // VISUAL 1: CIRCULAR PROGRESS RING (Categories card)
        if (document.getElementById('circular-progress-card')) {
            new ApexCharts(document.querySelector('#circular-progress-card'), {
                series: [dashData.categoryProgress],
                chart: {
                    type: 'radialBar',
                    width: 90,
                    height: 90,
                    sparkline: {
                        enabled: true
                    }
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '60%'
                        },
                        track: {
                            background: '#e9ecef'
                        },
                        dataLabels: {
                            show: false
                        }
                    }
                },
                colors: ['#dc3545'],
            }).render();
        }

        // VISUAL 2: SMALL BAR GRAPH (Products card - weekly addition)
        if (document.getElementById('bar-chart-card')) {
            new ApexCharts(document.querySelector('#bar-chart-card'), {
                series: [{
                    name: 'Products',
                    data: dashData.barValues
                }],
                chart: {
                    type: 'bar',
                    width: 120,
                    height: 80,
                    sparkline: {
                        enabled: true
                    },
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        columnWidth: '70%',
                        borderRadius: 3
                    }
                },
                colors: ['#727cf5'],
                xaxis: {
                    categories: dashData.barLabels,
                    labels: {
                        show: false
                    }
                },
                yaxis: {
                    show: false
                },
                grid: {
                    show: false
                },
                tooltip: {
                    y: {
                        formatter: v => v + ' products'
                    }
                }
            }).render();
        }

        // VISUAL 3: SPARKLINE (Blogs card)
        if (document.getElementById('sparkline-card')) {
            new ApexCharts(document.querySelector('#sparkline-card'), {
                series: [{
                    data: dashData.blogSparkline
                }],
                chart: {
                    type: 'area',
                    width: 120,
                    height: 60,
                    sparkline: {
                        enabled: true
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.5,
                        opacityTo: 0.05
                    }
                },
                colors: ['#fa5c7c'],
                tooltip: {
                    y: {
                        formatter: v => v + ' blogs'
                    }
                }
            }).render();
        }

        // VISUAL 4: MINI DONUT CHART (Reviews card - rating distribution)
        if (document.getElementById('donut-card')) {
            new ApexCharts(document.querySelector('#donut-card'), {
                series: dashData.donutRatingValues,
                labels: dashData.donutRatingLabels,
                chart: {
                    type: 'donut',
                    width: 90,
                    height: 90,
                    sparkline: {
                        enabled: true
                    }
                },
                colors: ['#dc3545', '#39afd1', '#ffbc00', '#6c757d', '#727cf5'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        },
                        expandOnClick: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false
                },
                tooltip: {
                    y: {
                        formatter: v => v + ' reviews'
                    }
                }
            }).render();
        }

        // ── Main revenue + orders chart ────────────────────────────────────────
        const revenueChart = new ApexCharts(document.querySelector('#dash-revenue-chart'), {
            series: [{
                    name: 'Revenue (₹)',
                    type: 'area',
                    data: dashData.revenue
                },
                {
                    name: 'Orders',
                    type: 'bar',
                    data: dashData.orders
                }
            ],
            chart: {
                height: 280,
                type: 'line',
                toolbar: {
                    show: false
                }
            },
            stroke: {
                curve: 'smooth',
                width: [2, 0]
            },
            fill: {
                type: ['gradient', 'solid'],
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            colors: ['#727cf5', '#dc3545'],
            xaxis: {
                categories: dashData.months,
                labels: {
                    style: {
                        fontSize: '11px'
                    }
                }
            },
            yaxis: [{
                    title: {
                        text: 'Revenue (₹)',
                        style: {
                            fontSize: '11px'
                        }
                    },
                    labels: {
                        formatter: v => '₹' + (v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v)
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Orders',
                        style: {
                            fontSize: '11px'
                        }
                    },
                    labels: {
                        formatter: v => Math.round(v)
                    }
                }
            ],
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: '#f1f3fa'
            },
            tooltip: {
                shared: true,
                y: [{
                        formatter: v => '₹' + Number(v).toLocaleString('en-IN')
                    },
                    {
                        formatter: v => v + ' orders'
                    }
                ]
            }
        });
        revenueChart.render();

        // ── Overview filter buttons ────────────────────────────────────────────
        document.querySelectorAll('.overview-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.overview-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const r = parseInt(this.dataset.range);
                revenueChart.updateOptions({
                    xaxis: {
                        categories: dashData.months.slice(-r)
                    }
                });
                revenueChart.updateSeries([{
                        name: 'Revenue (₹)',
                        data: dashData.revenue.slice(-r)
                    },
                    {
                        name: 'Orders',
                        data: dashData.orders.slice(-r)
                    }
                ]);
            });
        });

        // ── Donut chart (order status / payment status toggle) ─────────────────
        let donutChart = null;

        function buildLegend(labels, series, colorMap) {
            const total = series.reduce((a, b) => a + b, 0);
            return labels.map((lbl, i) => {
                const color = colorMap[lbl] || '#adb5bd';
                const pct = total > 0 ? ((series[i] / total) * 100).toFixed(1) : 0;
                return `
            <div class="col-6">
                <div class="d-flex justify-content-between align-items-center p-1">
                    <div>
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${color};margin-right:5px;"></span>
                        <span class="fw-semibold text-capitalize">${lbl}</span>
                    </div>
                    <span class="fw-semibold text-muted">${series[i]} <small>(${pct}%)</small></span>
                </div>
            </div>`;
            }).join('');
        }

        function renderDonut(labels, series, colorMap, centerLabel) {
            const colors = labels.map(l => colorMap[l] || '#adb5bd');
            const safeSeries = series.length ? series : [1];
            const safeLabels = labels.length ? labels : ['No Data'];

            const opts = {
                series: safeSeries,
                labels: safeLabels,
                chart: {
                    type: 'donut',
                    height: 220
                },
                colors,
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: centerLabel,
                                    fontSize: '12px',
                                    formatter: () => safeSeries.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: v => v + ' orders'
                    }
                }
            };

            if (donutChart) {
                donutChart.updateOptions(opts, true, true);
            } else {
                donutChart = new ApexCharts(document.querySelector('#most-leads-chart'), opts);
                donutChart.render();
            }

            document.getElementById('donut-legend-row').innerHTML = buildLegend(labels, series, colorMap);
        }

        // Initial render — order_status
        renderDonut(dashData.orderDonutLabels, dashData.orderDonutSeries, orderColors, 'Total Orders');

        // Toggle buttons
        document.querySelectorAll('.donut-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.donut-toggle').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                if (this.dataset.type === 'payment') {
                    renderDonut(dashData.paymentDonutLabels, dashData.paymentDonutSeries, paymentColors,
                        'By Payment');
                } else {
                    renderDonut(dashData.orderDonutLabels, dashData.orderDonutSeries, orderColors,
                        'Total Orders');
                }
            });
        });

        // ── New users mini bar chart ───────────────────────────────────────────
        new ApexCharts(document.querySelector('#new-users-chart'), {
            series: [{
                name: 'New Users',
                data: dashData.newUsersData
            }],
            chart: {
                type: 'bar',
                height: 80,
                sparkline: {
                    enabled: true
                }
            },
            colors: ['#727cf5'],
            plotOptions: {
                bar: {
                    columnWidth: '60%',
                    borderRadius: 3
                }
            },
            tooltip: {
                fixed: {
                    enabled: false
                },
                x: {
                    show: false
                },
                y: {
                    formatter: v => v + ' users'
                }
            }
        }).render();
    </script>
@endsection
