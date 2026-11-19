@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <h4 class="page-main-title m-0">Reports</h4>
        <p class="text-muted mb-0">Analytics &amp; business insights</p>
    </div>
</div>

@php
    $periodLabels = [
        'all' => 'All Time',
        'today' => 'Today (Day-wise)',
        'yesterday' => 'Yesterday',
        'this_week' => 'This Week (Week-wise)',
        'last_week' => 'Last Week',
        'last_7_days' => 'Last 7 Days',
        'this_month' => 'This Month (Month-wise)',
        'last_month' => 'Last Month',
        'this_year' => 'This Year',
        'custom' => 'Custom Date Range',
    ];
    $activeLabel = $periodLabels[$period ?? 'all'] ?? 'All Time';
@endphp

<!-- Filter Card -->
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-18">
                            <i class="ti ti-filter"></i>
                        </span>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold">Date Range Filter</h6>
                        <small class="text-muted">Filter report analytics across days, weeks, months &amp; custom periods</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-xs">
                        <i class="ti ti-calendar me-1 text-primary"></i>
                        Active Filter: <strong>{{ $activeLabel }}</strong>
                        @if(($period ?? '') === 'custom' && !empty($fromDate) && !empty($toDate))
                            ({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})
                        @endif
                    </span>
                </div>
            </div>

            <!-- Quick Filter Pills -->
            <div class="d-flex flex-wrap align-items-center gap-1 my-2">
                <a href="{{ route('reports.index', ['period' => 'all']) }}" 
                   class="btn btn-sm {{ ($period ?? 'all') === 'all' ? 'btn-primary' : 'btn-light border' }}">All Time</a>
                <a href="{{ route('reports.index', ['period' => 'today']) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'today' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-event me-1"></i>Today (Day)
                </a>
                <a href="{{ route('reports.index', ['period' => 'yesterday']) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'yesterday' ? 'btn-primary' : 'btn-light border' }}">Yesterday</a>
                <a href="{{ route('reports.index', ['period' => 'this_week']) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_week' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-week me-1"></i>This Week (Week)
                </a>
                <a href="{{ route('reports.index', ['period' => 'last_7_days']) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'last_7_days' ? 'btn-primary' : 'btn-light border' }}">Last 7 Days</a>
                <a href="{{ route('reports.index', ['period' => 'this_month']) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_month' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-month me-1"></i>This Month (Month)
                </a>
                <a href="{{ route('reports.index', ['period' => 'last_month']) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'last_month' ? 'btn-primary' : 'btn-light border' }}">Last Month</a>
                <a href="{{ route('reports.index', ['period' => 'this_year']) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_year' ? 'btn-primary' : 'btn-light border' }}">This Year</a>
                <button type="button" class="btn btn-sm {{ ($period ?? '') === 'custom' ? 'btn-primary' : 'btn-light border' }}" 
                        onclick="document.getElementById('customRangeRow').classList.toggle('d-none')">
                    <i class="ti ti-calendar-due me-1"></i>Custom Range...
                </button>
            </div>

            <!-- Custom Date Inputs -->
            <div class="row g-2 align-items-end pt-2 border-top mt-2 {{ ($period ?? '') === 'custom' ? '' : 'd-none' }}" id="customRangeRow">
                <input type="hidden" name="period" value="custom">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label fs-xs mb-1 text-muted">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate ?? '' }}">
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label fs-xs mb-1 text-muted">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate ?? '' }}">
                </div>
                <div class="col-sm-4 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ti ti-check me-1"></i>Apply Filter
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-light border">
                        <i class="ti ti-refresh me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body">
                <h5 class="text-muted fs-sm text-uppercase text-truncate">Total Orders</h5>
                <h3 class="mb-0 fw-bold text-primary">{{ number_format($totalOrders ?? 0) }}</h3>
                <small class="text-muted">{{ $activeLabel }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body">
                <h5 class="text-muted fs-sm text-uppercase text-truncate">Total Revenue</h5>
                <h3 class="mb-0 fw-bold text-success">₹{{ number_format($totalRevenue ?? 0, 0) }}</h3>
                <small class="text-muted">{{ $activeLabel }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body">
                <h5 class="text-muted fs-sm text-uppercase text-truncate">Customers</h5>
                <h3 class="mb-0 fw-bold text-warning">{{ number_format($totalCustomers ?? 0) }}</h3>
                <small class="text-muted">{{ ($period ?? 'all') === 'all' ? 'Registered' : 'Registered in period' }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body">
                <h5 class="text-muted fs-sm text-uppercase text-truncate">Active Orders</h5>
                <h3 class="mb-0 fw-bold text-info">{{ number_format(($processingOrders ?? 0) + ($shippedOrders ?? 0)) }}</h3>
                <small class="text-muted">Processing &amp; shipped ({{ $activeLabel }})</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card card-h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="avatar-md flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                            <i class="ti ti-chart-line"></i>
                        </span>
                    </span>
                    <div>
                        <h5 class="mb-1">Sales Report</h5>
                        <p class="text-muted mb-0 fs-sm">Revenue &amp; collections</p>
                    </div>
                </div>
                <p class="text-muted flex-grow-1">View day-wise, week-wise &amp; monthly sales performance, revenue trends, paid &amp; due amounts.</p>
                <a href="{{ route('reports.sales', request()->except('page')) }}" class="btn btn-primary btn-sm">
                    View Report <i class="ti ti-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="avatar-md flex-shrink-0">
                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-22">
                            <i class="ti ti-shopping-cart"></i>
                        </span>
                    </span>
                    <div>
                        <h5 class="mb-1">Order Report</h5>
                        <p class="text-muted mb-0 fs-sm">Status &amp; delivery</p>
                    </div>
                </div>
                <p class="text-muted flex-grow-1">Analyze orders by status, track delivery performance and identify trends.</p>
                <a href="{{ route('reports.orders', request()->except('page')) }}" class="btn btn-success btn-sm">
                    View Report <i class="ti ti-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="avatar-md flex-shrink-0">
                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-22">
                            <i class="ti ti-users"></i>
                        </span>
                    </span>
                    <div>
                        <h5 class="mb-1">Customer Report</h5>
                        <p class="text-muted mb-0 fs-sm">Spending &amp; frequency</p>
                    </div>
                </div>
                <p class="text-muted flex-grow-1">Analyze customer purchase history, order frequency and total spending.</p>
                <a href="{{ route('reports.customers', request()->except('page')) }}" class="btn btn-info btn-sm">
                    View Report <i class="ti ti-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card card-h-100">
            <div class="card-header border-0">
                <h4 class="card-title mb-1">Order status breakdown</h4>
                <p class="text-muted mb-0 fs-sm">Current distribution</p>
            </div>
            <div class="card-body text-center">
                <canvas id="statusDonut" height="200" role="img" aria-label="Donut chart of order statuses"></canvas>
                <div class="mt-3" id="statusLegend"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-h-100">
            <div class="card-header border-0">
                <h4 class="card-title mb-1">Revenue snapshot</h4>
                <p class="text-muted mb-0 fs-sm">Total vs paid vs due</p>
            </div>
            <div class="card-body">
                <canvas id="revenueBar" height="120" role="img" aria-label="Bar chart comparing total revenue, paid, and due amounts"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const statusColors = ['#39afd1', '#8790f6', '#dc3545', '#fa5c7c'];
    const statusLabels = ['Processing', 'Shipped', 'Delivered', 'Cancelled'];
    const statusData = [{{ $processingOrders ?? 0 }}, {{ $shippedOrders ?? 0 }}, {{ $deliveredOrders ?? 0 }}, {{ $cancelledOrders ?? 0 }}];
    const total = statusData.reduce((a, b) => a + b, 0);

    new Chart(document.getElementById('statusDonut'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: statusColors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (c) {
                            const p = total > 0 ? Math.round((c.raw / total) * 100) : 0;
                            return ' ' + c.label + ': ' + c.raw + ' (' + p + '%)';
                        }
                    }
                }
            }
        }
    });

    const leg = document.getElementById('statusLegend');
    statusLabels.forEach(function (lbl, i) {
        const p = total > 0 ? Math.round((statusData[i] / total) * 100) : 0;
        leg.innerHTML += '<div class="d-flex align-items-center justify-content-between py-1">' +
            '<div class="d-flex align-items-center gap-2">' +
            '<span class="badge rounded-circle p-1" style="background:' + statusColors[i] + '">&nbsp;</span>' +
            '<span class="text-muted">' + lbl + '</span>' +
            '</div>' +
            '<div class="d-flex align-items-center gap-2">' +
            '<span class="fw-semibold">' + statusData[i].toLocaleString() + '</span>' +
            '<span class="text-muted fs-sm">' + p + '%</span>' +
            '</div></div>';
    });

    new Chart(document.getElementById('revenueBar'), {
        type: 'bar',
        data: {
            labels: ['Total Sales', 'Paid', 'Due'],
            datasets: [{
                label: 'Amount (₹)',
                data: [{{ $totalRevenue ?? 0 }}, {{ $totalPaid ?? 0 }}, {{ $totalDue ?? 0 }}],
                backgroundColor: ['#39afd1', '#dc3545', '#fa5c7c'],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (c) {
                            return ' ₹' + Number(c.raw).toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function (v) { return '₹' + Number(v / 1000).toFixed(0) + 'k'; }
                    }
                }
            }
        }
    });
})();
</script>
@endpush
@endsection
