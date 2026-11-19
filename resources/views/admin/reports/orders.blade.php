@extends('layouts.admin')

@section('title', 'Order Report')

@section('content')
<div class="page-title-head d-flex align-items-center mb-3">
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-light border">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <h4 class="page-main-title m-0">Order Report</h4>
                <p class="text-muted mb-0">Status breakdown &amp; delivery performance</p>
            </div>
        </div>
    </div>
    <div class="text-end">
        <a href="{{ route('reports.orders.export', request()->all()) }}" class="btn btn-success">
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
        <form method="GET" action="{{ route('reports.orders') }}" id="ordersFilterForm">
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-18">
                            <i class="ti ti-filter"></i>
                        </span>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold">Order Reports Filter</h6>
                        <small class="text-muted">Filter orders by day, week, month, or custom period</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-light text-dark border px-3 py-2 fs-xs">
                        <i class="ti ti-calendar me-1 text-primary"></i> Active Period: <strong>{{ $activePeriodLabel }}</strong>
                        @if(($period ?? '') === 'custom' && !empty($fromDate) && !empty($toDate))
                            ({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})
                        @endif
                        @if(request()->filled('status'))
                            &bull; Status: <strong>{{ ucfirst(request('status')) }}</strong>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Quick Filter Pills -->
            <div class="d-flex flex-wrap align-items-center gap-1 my-2">
                <a href="{{ route('reports.orders', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'all'])) }}" 
                   class="btn btn-sm {{ ($period ?? 'all') === 'all' ? 'btn-primary' : 'btn-light border' }}">All Time</a>
                <a href="{{ route('reports.orders', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'today'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'today' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-event me-1"></i>Today
                </a>
                <a href="{{ route('reports.orders', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'yesterday'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'yesterday' ? 'btn-primary' : 'btn-light border' }}">Yesterday</a>
                <a href="{{ route('reports.orders', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_week'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_week' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-week me-1"></i>This Week
                </a>
                <a href="{{ route('reports.orders', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'last_7_days'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'last_7_days' ? 'btn-primary' : 'btn-light border' }}">Last 7 Days</a>
                <a href="{{ route('reports.orders', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_month'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_month' ? 'btn-primary' : 'btn-light border' }}">
                   <i class="ti ti-calendar-month me-1"></i>This Month
                </a>
                <a href="{{ route('reports.orders', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'last_month'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'last_month' ? 'btn-primary' : 'btn-light border' }}">Last Month</a>
                <a href="{{ route('reports.orders', array_merge(request()->except(['page', 'from_date', 'to_date']), ['period' => 'this_year'])) }}" 
                   class="btn btn-sm {{ ($period ?? '') === 'this_year' ? 'btn-primary' : 'btn-light border' }}">This Year</a>
                <button type="button" class="btn btn-sm {{ ($period ?? '') === 'custom' ? 'btn-primary' : 'btn-light border' }}" 
                        onclick="document.getElementById('ordersCustomRangeRow').classList.toggle('d-none')">
                    <i class="ti ti-calendar-due me-1"></i>Custom Range...
                </button>
            </div>

            <!-- Custom Date Inputs -->
            <div class="row g-2 align-items-end pt-2 border-top mt-2 {{ ($period ?? '') === 'custom' ? '' : 'd-none' }}" id="ordersCustomRangeRow">
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
                    <a href="{{ route('reports.orders') }}" class="btn btn-sm btn-light border">
                        <i class="ti ti-refresh me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@php
$stats = [
    ['label' => 'Processing', 'count' => $processing, 'color' => 'primary', 'icon' => 'ti-refresh', 'foot' => 'Awaiting fulfillment'],
    ['label' => 'Shipped', 'count' => $shipped, 'color' => 'info', 'icon' => 'ti-truck', 'foot' => 'In transit'],
    ['label' => 'Delivered', 'count' => $delivered, 'color' => 'success', 'icon' => 'ti-circle-check', 'foot' => 'Successfully completed'],
    ['label' => 'Cancelled', 'count' => $cancelled, 'color' => 'danger', 'icon' => 'ti-circle-x', 'foot' => 'Orders voided'],
];
$total = $processing + $shipped + $delivered + $cancelled;
@endphp

<div class="row g-3 mb-3">
    @foreach($stats as $s)
    @php
        $percentage = $total > 0 ? round(($s['count'] / $total) * 100, 1) : 0;
    @endphp
    <div class="col-md-6 col-xxl-3">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="avatar-sm">
                        <span class="avatar-title bg-{{ $s['color'] }}-subtle text-{{ $s['color'] }} rounded-circle">
                            <i class="ti {{ $s['icon'] }}"></i>
                        </span>
                    </span>
                    <span class="badge bg-light text-muted">{{ $percentage }}%</span>
                </div>
                <h5 class="text-muted fs-sm text-uppercase mb-1">{{ $s['label'] }}</h5>
                <h3 class="mb-1 fw-bold text-{{ $s['color'] }}">{{ number_format($s['count']) }}</h3>
                <small class="text-muted d-block mb-2">{{ $s['foot'] }}</small>
                <div class="progress" style="height:6px;">
                    <div class="progress-bar bg-{{ $s['color'] }}" role="progressbar" style="width: {{ max($percentage, 0) }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-xl-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-1">All Orders</h4>
                    <p class="text-muted mb-0 fs-sm">List of all orders excluding pending</p>
                </div>
                <span class="badge badge-soft-primary">
                    <i class="ti ti-shopping-cart me-1"></i> Total: {{ $orders->total() }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-nowrap table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th class="text-center">Payment</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="fw-semibold text-primary">#{{ $order->order_id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-xs">
                                        <span class="avatar-title bg-primary text-white rounded-circle">
                                            {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                                        </span>
                                    </span>
                                    <span>{{ $order->user->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($order->grand_total, 2) }}</td>
                            <td class="text-end text-success">₹{{ number_format($order->paid_amount, 2) }}</td>
                            <td class="text-end">
                                @if($order->due_amount > 0)
                                    <span class="text-danger">₹{{ number_format($order->due_amount, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                $pc = match($order->payment_status) {
                                    'paid' => 'badge-soft-success',
                                    'partial' => 'badge-soft-warning',
                                    default => 'badge-soft-danger',
                                };
                                @endphp
                                <span class="badge {{ $pc }}">{{ ucfirst($order->payment_status) }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                $sc = match($order->order_status) {
                                    'delivered' => 'badge-soft-success',
                                    'shipped' => 'badge-soft-info',
                                    'processing' => 'badge-soft-primary',
                                    'cancelled' => 'badge-soft-danger',
                                    default => 'badge-soft-warning',
                                };
                                @endphp
                                <span class="badge {{ $sc }}">{{ ucfirst($order->order_status) }}</span>
                            </td>
                            <td class="text-end text-muted">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">No orders found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
            <div class="card-footer border-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted small">
                        <i class="ti ti-info-circle me-1"></i>
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                    </div>
                    <div>{{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card card-h-100">
            <div class="card-header border-0">
                <h4 class="card-title mb-1">Status distribution</h4>
                <p class="text-muted mb-0 fs-sm">Order breakdown by status</p>
            </div>
            <div class="card-body text-center">
                <canvas id="orderDonut" height="200" role="img" aria-label="Donut chart showing order status distribution"></canvas>
                <div class="mt-3" id="donutLegend"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = ['Processing', 'Shipped', 'Delivered', 'Cancelled'];
    const data = [{{ $processing }}, {{ $shipped }}, {{ $delivered }}, {{ $cancelled }}];
    const colors = ['#39afd1', '#8790f6', '#dc3545', '#fa5c7c'];
    const total = data.reduce((a, b) => a + b, 0);

    const ctx = document.getElementById('orderDonut');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: c => {
                                const pct = total > 0 ? Math.round((c.raw / total) * 100) : 0;
                                return ` ${c.label}: ${c.raw} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    const legend = document.getElementById('donutLegend');
    if (legend) {
        labels.forEach((lbl, i) => {
            const pct = total > 0 ? Math.round((data[i] / total) * 100) : 0;
            legend.innerHTML += `
                <div class="d-flex align-items-center justify-content-between py-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-1" style="background:${colors[i]}">&nbsp;</span>
                        <span class="text-muted">${lbl}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold">${data[i].toLocaleString()}</span>
                        <span class="text-muted fs-sm">${pct}%</span>
                    </div>
                </div>`;
        });
    }
});
</script>
@endpush
@endsection
