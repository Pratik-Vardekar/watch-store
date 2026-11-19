@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-light border">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <h4 class="page-main-title m-0">Sales Report</h4>
                <p class="text-muted mb-0">Revenue, collections &amp; performance analysis</p>
            </div>
        </div>
    </div>
    <div class="text-end">
        <a href="{{ route('reports.sales.export', request()->all()) }}" class="btn btn-success">
            <i class="ti ti-download me-1"></i> Export ({{ strtoupper($groupBy ?? 'day') }})
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
    $groupLabels = [
        'day' => 'Day-wise (Daily)',
        'week' => 'Week-wise (Weekly)',
        'month' => 'Monthly-wise (Monthly)',
    ];
    $activeGroupLabel = $groupLabels[$groupBy ?? 'day'] ?? 'Day-wise (Daily)';
@endphp

<!-- Filter Card with Period & Grouping -->
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.sales') }}" id="salesFilterForm">
            <input type="hidden" name="group_by" id="inputGroupBy" value="{{ $groupBy ?? 'day' }}">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-18">
                            <i class="ti ti-filter"></i>
                        </span>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold">Sales Filters &amp; Grouping</h6>
                        <small class="text-muted">Analyze sales day-wise, week-wise, or monthly-wise across any period</small>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2 fs-xs">
                        <i class="ti ti-layout-grid me-1 text-primary"></i> Grouping: <strong>{{ $activeGroupLabel }}</strong>
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-xs">
                        <i class="ti ti-calendar me-1 text-success"></i> Period: <strong>{{ $activePeriodLabel }}</strong>
                        @if(($period ?? '') === 'custom' && !empty($fromDate) && !empty($toDate))
                            ({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})
                        @endif
                    </span>
                </div>
            </div>

            <!-- Grouping Selection (Day-wise / Week-wise / Monthly-wise) -->
            <div class="mb-2">
                <span class="fs-xs text-uppercase text-muted fw-bold d-block mb-1">1. View Grouping:</span>
                <div class="btn-group" role="group" aria-label="Group by selection">
                    <a href="{{ route('reports.sales', array_merge(request()->except('page'), ['group_by' => 'day'])) }}" 
                       class="btn btn-sm {{ ($groupBy ?? 'day') === 'day' ? 'btn-primary' : 'btn-light border' }}">
                        <i class="ti ti-calendar me-1"></i> Day-wise (Daily)
                    </a>
                    <a href="{{ route('reports.sales', array_merge(request()->except('page'), ['group_by' => 'week'])) }}" 
                       class="btn btn-sm {{ ($groupBy ?? '') === 'week' ? 'btn-primary' : 'btn-light border' }}">
                        <i class="ti ti-calendar-week me-1"></i> Week-wise (Weekly)
                    </a>
                    <a href="{{ route('reports.sales', array_merge(request()->except('page'), ['group_by' => 'month'])) }}" 
                       class="btn btn-sm {{ ($groupBy ?? '') === 'month' ? 'btn-primary' : 'btn-light border' }}">
                        <i class="ti ti-calendar-month me-1"></i> Monthly-wise (Monthly)
                    </a>
                </div>
            </div>

            <!-- Date Range Quick Pills -->
            <div class="mb-2">
                <span class="fs-xs text-uppercase text-muted fw-bold d-block mb-1">2. Date Period Filter:</span>
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <a href="{{ route('reports.sales', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'all'])) }}" 
                       class="btn btn-sm {{ ($period ?? 'all') === 'all' ? 'btn-dark' : 'btn-light border' }}">All Time</a>
                    <a href="{{ route('reports.sales', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'today'])) }}" 
                       class="btn btn-sm {{ ($period ?? '') === 'today' ? 'btn-dark' : 'btn-light border' }}">
                       <i class="ti ti-calendar-event me-1"></i>Today
                    </a>
                    <a href="{{ route('reports.sales', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'yesterday'])) }}" 
                       class="btn btn-sm {{ ($period ?? '') === 'yesterday' ? 'btn-dark' : 'btn-light border' }}">Yesterday</a>
                    <a href="{{ route('reports.sales', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_week'])) }}" 
                       class="btn btn-sm {{ ($period ?? '') === 'this_week' ? 'btn-dark' : 'btn-light border' }}">
                       <i class="ti ti-calendar-week me-1"></i>This Week
                    </a>
                    <a href="{{ route('reports.sales', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'last_7_days'])) }}" 
                       class="btn btn-sm {{ ($period ?? '') === 'last_7_days' ? 'btn-dark' : 'btn-light border' }}">Last 7 Days</a>
                    <a href="{{ route('reports.sales', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_month'])) }}" 
                       class="btn btn-sm {{ ($period ?? '') === 'this_month' ? 'btn-dark' : 'btn-light border' }}">
                       <i class="ti ti-calendar-month me-1"></i>This Month
                    </a>
                    <a href="{{ route('reports.sales', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'last_month'])) }}" 
                       class="btn btn-sm {{ ($period ?? '') === 'last_month' ? 'btn-dark' : 'btn-light border' }}">Last Month</a>
                    <a href="{{ route('reports.sales', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_year'])) }}" 
                       class="btn btn-sm {{ ($period ?? '') === 'this_year' ? 'btn-dark' : 'btn-light border' }}">This Year</a>
                    <button type="button" class="btn btn-sm {{ ($period ?? '') === 'custom' ? 'btn-dark' : 'btn-light border' }}" 
                            onclick="document.getElementById('salesCustomRangeRow').classList.toggle('d-none')">
                        <i class="ti ti-calendar-due me-1"></i>Custom Range...
                    </button>
                </div>
            </div>

            <!-- Custom Date Inputs -->
            <div class="row g-2 align-items-end pt-2 border-top mt-2 {{ ($period ?? '') === 'custom' ? '' : 'd-none' }}" id="salesCustomRangeRow">
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
                    <a href="{{ route('reports.sales', ['group_by' => $groupBy ?? 'day']) }}" class="btn btn-sm btn-light border">
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
                    <h5 class="text-muted fs-sm text-uppercase text-truncate">Total Orders</h5>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalOrders) }}</h3>
                    <small class="text-muted">{{ $activePeriodLabel }}</small>
                </div>
                <span class="avatar-md">
                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
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
                    <h5 class="text-muted fs-sm text-uppercase text-truncate">Total Sales</h5>
                    <h3 class="mb-0 fw-bold text-success">₹{{ number_format($totalSales, 0) }}</h3>
                    <small class="text-muted">Revenue ({{ $activePeriodLabel }})</small>
                </div>
                <span class="avatar-md">
                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-22">
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
                    <h5 class="text-muted fs-sm text-uppercase text-truncate">Total Paid</h5>
                    <h3 class="mb-0 fw-bold text-success">₹{{ number_format($totalPaid, 0) }}</h3>
                    <small class="text-muted">Collected amount</small>
                </div>
                <span class="avatar-md">
                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-22">
                        <i class="ti ti-wallet"></i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="text-muted fs-sm text-uppercase text-truncate">Total Due</h5>
                    <h3 class="mb-0 fw-bold text-danger">₹{{ number_format($totalDue, 0) }}</h3>
                    <small class="text-muted">Pending collection</small>
                </div>
                <span class="avatar-md">
                    <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-22">
                        <i class="ti ti-clock"></i>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar-md">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-22">
                            <i class="ti ti-currency-rupee"></i>
                        </span>
                    </span>
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1 fs-sm">Collection Rate</h6>
                        @php
                            $collectionRate = $totalSales > 0 ? round(($totalPaid / $totalSales) * 100, 1) : 0;
                        @endphp
                        <h4 class="mb-1 fw-bold">
                            {{ $collectionRate }}%
                            <small class="text-muted fs-sm">of total revenue</small>
                        </h4>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-{{ $collectionRate >= 80 ? 'success' : ($collectionRate >= 50 ? 'warning' : 'danger') }}"
                                 role="progressbar" style="width: {{ $collectionRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar-md">
                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-22">
                            <i class="ti ti-shopping-cart"></i>
                        </span>
                    </span>
                    <div>
                        <h6 class="text-muted mb-1 fs-sm">Average Order Value</h6>
                        @php
                            $avgOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 0) : 0;
                        @endphp
                        <h4 class="mb-0 fw-bold text-success">₹{{ number_format($avgOrderValue, 0) }}</h4>
                        <small class="text-muted">per order</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar-md">
                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-22">
                            <i class="ti ti-calendar"></i>
                        </span>
                    </span>
                    <div>
                        @php
                            $groupSingular = ($groupBy ?? 'day') === 'week' ? 'Week' : (($groupBy ?? 'day') === 'month' ? 'Month' : 'Day');
                            $slotsWithData = $salesByDate->count();
                            $slotAvg = $slotsWithData > 0 ? round($totalSales / $slotsWithData, 0) : 0;
                        @endphp
                        <h6 class="text-muted mb-1 fs-sm">{{ $groupSingular }} Average</h6>
                        <h4 class="mb-0 fw-bold text-info">₹{{ number_format($slotAvg, 0) }}</h4>
                        <small class="text-muted">per {{ strtolower($groupSingular) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="card-title mb-1">{{ ($groupBy ?? 'day') === 'week' ? 'Weekly' : (($groupBy ?? 'day') === 'month' ? 'Monthly' : 'Daily') }} Sales Trend</h4>
            <span class="text-muted fs-sm">Sales, paid &amp; due over time ({{ $activeGroupLabel }})</span>
        </div>
        <div class="d-flex gap-3 flex-wrap">
            <span class="badge badge-soft-primary">Sales</span>
            <span class="badge badge-soft-success">Paid</span>
            <span class="badge badge-soft-danger">Due</span>
        </div>
    </div>
    <div class="card-body">
        <canvas id="salesTrend" height="100"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-header border-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="card-title mb-1">Sales Summary by {{ ($groupBy ?? 'day') === 'week' ? 'Week' : (($groupBy ?? 'day') === 'month' ? 'Month' : 'Date') }}</h4>
            @if($salesByDate instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <span class="text-muted fs-sm">Showing {{ $salesByDate->firstItem() ?? 0 }} to {{ $salesByDate->lastItem() ?? 0 }} of {{ $salesByDate->total() }} records</span>
            @else
                <span class="text-muted fs-sm">Showing {{ $salesByDate->count() }} records</span>
            @endif
        </div>
        <span class="badge badge-soft-primary">
            <i class="ti ti-calendar me-1"></i> {{ $activeGroupLabel }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom table-nowrap table-centered table-hover w-100 mb-0">
            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                <tr class="text-uppercase fs-xxs">
                    <th>{{ ($groupBy ?? 'day') === 'week' ? 'Week Period' : (($groupBy ?? 'day') === 'month' ? 'Month' : 'Date') }}</th>
                    <th class="text-center">Orders</th>
                    <th class="text-end">Sales</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Due</th>
                    <th class="text-center">Collection Rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesByDate as $sale)
                @php
                    $collectionRate = $sale->sales > 0 ? round(($sale->paid / $sale->sales) * 100, 1) : 0;
                    $rateColor = $collectionRate >= 80 ? 'success' : ($collectionRate >= 50 ? 'warning' : 'danger');
                    $displayDate = $sale->formatted_label ?? \Carbon\Carbon::parse($sale->date)->format('d M Y');
                @endphp
                <tr>
                    <td class="fw-medium">{{ $displayDate }}</td>
                    <td class="text-center">
                        <span class="badge badge-soft-primary fw-semibold px-3">{{ $sale->orders_count }}</span>
                    </td>
                    <td class="text-end fw-semibold">₹{{ number_format($sale->sales, 2) }}</td>
                    <td class="text-end text-success fw-medium">₹{{ number_format($sale->paid, 2) }}</td>
                    <td class="text-end">
                        @if($sale->due > 0)
                            <span class="text-danger fw-medium">₹{{ number_format($sale->due, 2) }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="progress flex-grow-1" style="max-width:80px;height:6px;">
                                <div class="progress-bar bg-{{ $rateColor }}" role="progressbar" style="width: {{ $collectionRate }}%"></div>
                            </div>
                            <span class="text-{{ $rateColor }} fw-semibold small">{{ $collectionRate }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">No sales data available for this filter</td></tr>
                @endforelse
            </tbody>
            @if($salesByDate->count() > 0)
            <tfoot class="bg-light bg-opacity-50">
                <tr class="fw-semibold">
                    <td class="text-uppercase fs-xxs">Total</td>
                    <td class="text-center">{{ $salesByDate->sum('orders_count') }}</td>
                    <td class="text-end">₹{{ number_format($salesByDate->sum('sales'), 2) }}</td>
                    <td class="text-end text-success">₹{{ number_format($salesByDate->sum('paid'), 2) }}</td>
                    <td class="text-end text-danger">₹{{ number_format($salesByDate->sum('due'), 2) }}</td>
                    <td class="text-center">
                        @php
                            $totalSalesSum = $salesByDate->sum('sales');
                            $totalPaidSum = $salesByDate->sum('paid');
                            $overallRate = $totalSalesSum > 0 ? round(($totalPaidSum / $totalSalesSum) * 100, 1) : 0;
                        @endphp
                        <span class="badge bg-{{ $overallRate >= 80 ? 'success' : ($overallRate >= 50 ? 'warning' : 'danger') }}">
                            {{ $overallRate }}% collection
                        </span>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    @if($salesByDate instanceof \Illuminate\Pagination\LengthAwarePaginator && $salesByDate->hasPages())
    <div class="card-footer border-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="text-muted small">
                <i class="ti ti-info-circle me-1"></i>
                Showing {{ $salesByDate->firstItem() }} to {{ $salesByDate->lastItem() }} of {{ $salesByDate->total() }} records
            </div>
            <div>{{ $salesByDate->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawLabels = @json($salesByDate->pluck('chart_label')->reverse()->values());
    const salesData = @json($salesByDate->pluck('sales')->reverse()->values());
    const paidData = @json($salesByDate->pluck('paid')->reverse()->values());
    const dueData = @json($salesByDate->pluck('due')->reverse()->values());

    const formattedLabels = rawLabels.length > 0 ? rawLabels : @json($salesByDate->pluck('date')->reverse()->values());

    const ctx = document.getElementById('salesTrend');
    if (!ctx) return;

    if (salesData.length === 0 || salesData.every(v => v === 0)) {
        ctx.parentElement.innerHTML = '<div class="text-center py-5 text-muted"><i class="ti ti-chart-bar fs-1 d-block mb-2"></i>No sales data available to display</div>';
        return;
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: formattedLabels,
            datasets: [
                {
                    label: 'Sales',
                    data: salesData,
                    borderColor: '#39afd1',
                    backgroundColor: 'rgba(57,175,209,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                },
                {
                    label: 'Paid',
                    data: paidData,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,0.05)',
                    fill: true,
                    borderDash: [6, 4],
                    tension: 0.4,
                    borderWidth: 2
                },
                {
                    label: 'Due',
                    data: dueData,
                    borderColor: '#fa5c7c',
                    backgroundColor: 'rgba(250,92,124,0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₹' + Number(context.raw).toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return '₹' + Number(value).toLocaleString('en-IN');
                        }
                    },
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
@endsection
