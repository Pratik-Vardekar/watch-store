@extends('layouts.admin')

@section('title', 'Customer Report')

@section('content')
@php
    $avgOrders = $customers->total() > 0 ? round($customers->sum('orders_count') / $customers->total(), 1) : 0;
    $avgSpend  = $customers->total() > 0 ? round($customers->sum('orders_sum_grand_total') / $customers->total(), 0) : 0;
@endphp

<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-light border">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <h4 class="page-main-title m-0">Customer Report</h4>
                <p class="text-muted mb-0">Purchase history, frequency &amp; spending analysis</p>
            </div>
        </div>
    </div>
    <div class="text-end">
        <a href="{{ route('reports.customers.export', request()->all()) }}" class="btn btn-success">
            <i class="ti ti-download me-1"></i> Export
        </a>
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
    $activePeriodLabel = $periodLabels[$period ?? 'all'] ?? 'All Time';
@endphp

<!-- Filter Card -->
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.customers') }}" id="customersFilterForm">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-18">
                            <i class="ti ti-filter"></i>
                        </span>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold">Customer Purchase Period Filter</h6>
                        <small class="text-muted">Filter customer spending and orders by day, week, month, or custom period</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-xs">
                        <i class="ti ti-calendar me-1 text-primary"></i> Active Period: <strong>{{ $activePeriodLabel }}</strong>
                        @if(($period ?? '') === 'custom' && !empty($fromDate) && !empty($toDate))
                            ({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})
                        @endif
                    </span>
                </div>
            </div>

            <!-- Quick Filter Pills -->
            <div class="d-flex flex-wrap align-items-center gap-1 my-2">
                <a href="{{ route('reports.customers', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'all'])) }}" 
                   class="btn btn-sm {{ ($period ?? 'all') === 'all' ? 'btn-primary' : 'btn-light border' }}">All Time</a>
                <a href="{{ route('reports.customers', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'today'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'today' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-event me-1"></i>Today
                </a>
                <a href="{{ route('reports.customers', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'yesterday'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'yesterday' ? 'btn-primary' : 'btn-light border' }}">Yesterday</a>
                <a href="{{ route('reports.customers', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_week'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_week' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-week me-1"></i>This Week
                </a>
                <a href="{{ route('reports.customers', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'last_7_days'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'last_7_days' ? 'btn-primary' : 'btn-light border' }}">Last 7 Days</a>
                <a href="{{ route('reports.customers', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_month'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_month' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-month me-1"></i>This Month
                </a>
                <a href="{{ route('reports.customers', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'last_month'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'last_month' ? 'btn-primary' : 'btn-light border' }}">Last Month</a>
                <a href="{{ route('reports.customers', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_year'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_year' ? 'btn-primary' : 'btn-light border' }}">This Year</a>
                <button type="button" class="btn btn-sm {{ ($period ?? '') === 'custom' ? 'btn-primary' : 'btn-light border' }}" 
                        onclick="document.getElementById('customersCustomRangeRow').classList.toggle('d-none')">
                    <i class="ti ti-calendar-due me-1"></i>Custom Range...
                </button>
            </div>

            <!-- Custom Date Inputs -->
            <div class="row g-2 align-items-end pt-2 border-top mt-2 {{ ($period ?? '') === 'custom' ? '' : 'd-none' }}" id="customersCustomRangeRow">
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
                    <a href="{{ route('reports.customers') }}" class="btn btn-sm btn-light border">
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
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="text-muted fs-sm text-uppercase">Total Customers</h5>
                    <h3 class="mb-0 fw-bold text-primary">{{ number_format($customers->total()) }}</h3>
                    <small class="text-muted">With purchases</small>
                </div>
                <span class="avatar-md">
                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                        <i class="ti ti-users"></i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="text-muted fs-sm text-uppercase">Total Orders</h5>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($customers->sum('orders_count')) }}</h3>
                    <small class="text-muted">Completed orders</small>
                </div>
                <span class="avatar-md">
                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-22">
                        <i class="ti ti-shopping-cart"></i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="text-muted fs-sm text-uppercase">Total Revenue</h5>
                    <h3 class="mb-0 fw-bold text-warning">₹{{ number_format($customers->sum('orders_sum_grand_total'), 0) }}</h3>
                    <small class="text-muted">From all orders</small>
                </div>
                <span class="avatar-md">
                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-22">
                        <i class="ti ti-currency-rupee"></i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="text-muted fs-sm text-uppercase">Avg. Orders / Customer</h5>
                    <h3 class="mb-0 fw-bold text-info">{{ $avgOrders }}</h3>
                    <small class="text-muted">Per customer</small>
                </div>
                <span class="avatar-md">
                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-22">
                        <i class="ti ti-chart-bar"></i>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="ti ti-user text-primary fs-22"></i>
                    <div>
                        <div class="text-muted fs-sm">Active customers</div>
                        <div class="fw-semibold">{{ number_format($customers->where('orders_count', '>', 0)->count()) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="ti ti-trophy text-warning fs-22"></i>
                    <div>
                        <div class="text-muted fs-sm">Top spender</div>
                        <div class="fw-semibold">₹{{ number_format($customers->max('orders_sum_grand_total') ?? 0, 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="ti ti-shopping-cart text-success fs-22"></i>
                    <div>
                        <div class="text-muted fs-sm">Most orders</div>
                        <div class="fw-semibold">{{ $customers->max('orders_count') ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="ti ti-calendar text-info fs-22"></i>
                    <div>
                        <div class="text-muted fs-sm">Avg spend</div>
                        <div class="fw-semibold">₹{{ number_format($avgSpend, 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="card-title mb-1">Top customers by spend</h4>
            <p class="text-muted mb-0 fs-sm">Based on total purchase value</p>
        </div>
        <span class="badge badge-soft-primary"><i class="ti ti-trophy me-1"></i> Top 10</span>
    </div>
    <div class="card-body">
        @if($customers->count() > 0)
            <canvas id="topCustomersChart" height="120"></canvas>
        @else
            <div class="text-center py-5 text-muted">
                <i class="ti ti-users fs-1 d-block mb-2"></i>
                No customer data available
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="card-title mb-1">All Customers</h4>
            <p class="text-muted mb-0 fs-sm">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</p>
        </div>
        <span class="badge badge-soft-primary"><i class="ti ti-users me-1"></i> {{ $customers->total() }} total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom table-nowrap table-centered table-hover w-100 mb-0">
            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                <tr class="text-uppercase fs-xxs">
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th class="text-center">Orders</th>
                    <th class="text-end">Total Spend</th>
                    <th class="text-center">Last Order</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                @php
                    $avatarColors = ['bg-primary', 'bg-info', 'bg-success', 'bg-warning', 'bg-secondary'];
                    $avatarBg = $avatarColors[$loop->index % count($avatarColors)];
                    $ranks = ['🥇', '🥈', '🥉'];
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar-xs">
                                <span class="avatar-title {{ $avatarBg }} text-white rounded-circle">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </span>
                            </span>
                            <span class="fw-semibold">{{ $customer->name }}</span>
                            @if($loop->index < 3)
                                <span>{{ $ranks[$loop->index] }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-muted">{{ $customer->email }}</td>
                    <td class="text-muted">{{ $customer->phone ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge badge-soft-primary">{{ $customer->orders_count }}</span>
                    </td>
                    <td class="text-end fw-semibold">₹{{ number_format($customer->orders_sum_grand_total ?? 0, 2) }}</td>
                    <td class="text-center text-muted">
                        @if($customer->last_order_date)
                            {{ \Carbon\Carbon::parse($customer->last_order_date)->format('d M Y') }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">No customers with orders found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer border-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="text-muted small">
                <i class="ti ti-info-circle me-1"></i>
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
            </div>
            <div>{{ $customers->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const topCustomers = @json(
        $customers->sortByDesc('orders_sum_grand_total')->take(10)->values()->map(fn($c) => [
            'name' => $c->name,
            'spend' => (float)($c->orders_sum_grand_total ?? 0),
        ])
    );

    const ctx = document.getElementById('topCustomersChart');
    if (ctx && topCustomers.length > 0) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: topCustomers.map(c => c.name.length > 22 ? c.name.slice(0, 22) + '…' : c.name),
                datasets: [{
                    label: 'Total Spend (₹)',
                    data: topCustomers.map(c => c.spend),
                    backgroundColor: '#39afd1',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: c => ' ₹' + Number(c.raw).toLocaleString('en-IN') }
                    }
                },
                scales: {
                    x: {
                        ticks: { callback: v => '₹' + Number(v / 1000).toFixed(0) + 'k' }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
