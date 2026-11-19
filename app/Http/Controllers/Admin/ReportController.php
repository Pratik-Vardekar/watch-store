<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Admin\Concerns\HandlesCsv;

class ReportController extends Controller
{
    use HandlesCsv;

    /**
     * Resolve date range based on period or custom from/to dates.
     */
    protected function resolveDateRange(Request $request): array
    {
        $period = $request->get('period', 'all');
        $fromDate = null;
        $toDate = null;

        switch ($period) {
            case 'today':
                $fromDate = now()->startOfDay();
                $toDate = now()->endOfDay();
                break;
            case 'yesterday':
                $fromDate = now()->subDay()->startOfDay();
                $toDate = now()->subDay()->endOfDay();
                break;
            case 'this_week':
                $fromDate = now()->startOfWeek();
                $toDate = now()->endOfWeek();
                break;
            case 'last_week':
                $fromDate = now()->subWeek()->startOfWeek();
                $toDate = now()->subWeek()->endOfWeek();
                break;
            case 'last_7_days':
                $fromDate = now()->subDays(6)->startOfDay();
                $toDate = now()->endOfDay();
                break;
            case 'this_month':
                $fromDate = now()->startOfMonth();
                $toDate = now()->endOfMonth();
                break;
            case 'last_month':
                $fromDate = now()->subMonth()->startOfMonth();
                $toDate = now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $fromDate = now()->startOfYear();
                $toDate = now()->endOfYear();
                break;
            case 'custom':
                if ($request->filled('from_date')) {
                    $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                }
                if ($request->filled('to_date')) {
                    $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                }
                break;
            default:
                if ($request->filled('from_date') || $request->filled('to_date')) {
                    $period = 'custom';
                    if ($request->filled('from_date')) {
                        $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                    }
                    if ($request->filled('to_date')) {
                        $toDate = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                    }
                } else {
                    $period = 'all';
                }
                break;
        }

        return [$fromDate, $toDate, $period];
    }

    /**
     * Apply date range condition to a query.
     */
    protected function applyDateFilter($query, $fromDate, $toDate, $column = 'created_at')
    {
        if ($fromDate) {
            $query->where($column, '>=', $fromDate);
        }
        if ($toDate) {
            $query->where($column, '<=', $toDate);
        }
        return $query;
    }

    public function index(Request $request)
    {
        [$fromDate, $toDate, $period] = $this->resolveDateRange($request);

        $ordersQuery = Order::with('user')->latest();
        $this->applyDateFilter($ordersQuery, $fromDate, $toDate);
        $orders = $ordersQuery->paginate(10);

        // Sales by date for charts
        $salesQuery = Order::selectRaw('
                DATE(created_at) as date,
                COUNT(*) as orders_count,
                SUM(grand_total) as sales,
                SUM(paid_amount) as paid,
                SUM(due_amount) as due
            ')
            ->groupBy('date')
            ->orderBy('date', 'desc');

        $this->applyDateFilter($salesQuery, $fromDate, $toDate);
        $salesByDate = $salesQuery->limit(30)->get();

        // Summary metrics
        $baseOrderQuery = Order::query();
        $this->applyDateFilter($baseOrderQuery, $fromDate, $toDate);

        $totalOrders      = (clone $baseOrderQuery)->count();
        $totalRevenue     = (clone $baseOrderQuery)->sum('grand_total');
        $totalPaid        = (clone $baseOrderQuery)->sum('paid_amount');
        $totalDue         = (clone $baseOrderQuery)->sum('due_amount');
        $pendingOrders    = (clone $baseOrderQuery)->where('order_status', 'pending')->count();
        $processingOrders = (clone $baseOrderQuery)->where('order_status', 'processing')->count();
        $shippedOrders    = (clone $baseOrderQuery)->where('order_status', 'shipped')->count();
        $deliveredOrders  = (clone $baseOrderQuery)->where('order_status', 'delivered')->count();
        $cancelledOrders  = (clone $baseOrderQuery)->where('order_status', 'cancelled')->count();

        // Customer count (registered users or users with orders in period)
        $customerQuery = User::where('role', 'user');
        if ($period !== 'all') {
            $this->applyDateFilter($customerQuery, $fromDate, $toDate);
        }
        $totalCustomers = $customerQuery->count();

        return view('admin.reports.index', [
            'totalOrders'      => $totalOrders,
            'totalRevenue'     => $totalRevenue,
            'totalPaid'        => $totalPaid,
            'totalDue'         => $totalDue,
            'totalCustomers'   => $totalCustomers,
            'pendingOrders'    => $pendingOrders,
            'processingOrders' => $processingOrders,
            'shippedOrders'    => $shippedOrders,
            'deliveredOrders'  => $deliveredOrders,
            'cancelledOrders'  => $cancelledOrders,
            'orders'           => $orders,
            'salesByDate'      => $salesByDate,
            'period'           => $period,
            'fromDate'         => $request->from_date ?? ($fromDate ? $fromDate->format('Y-m-d') : ''),
            'toDate'           => $request->to_date ?? ($toDate ? $toDate->format('Y-m-d') : ''),
        ]);
    }

    public function sales(Request $request)
    {
        [$fromDate, $toDate, $period] = $this->resolveDateRange($request);
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        $baseQuery = Order::where('order_status', '!=', 'pending');
        $this->applyDateFilter($baseQuery, $fromDate, $toDate);

        $totalSales  = (clone $baseQuery)->sum('grand_total');
        $totalPaid   = (clone $baseQuery)->sum('paid_amount');
        $totalDue    = (clone $baseQuery)->sum('due_amount');
        $totalOrders = (clone $baseQuery)->count();

        // Dynamic grouping expression for MySQL
        if ($groupBy === 'week') {
            $dateExpr = "DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY)";
        } elseif ($groupBy === 'month') {
            $dateExpr = "DATE_FORMAT(created_at, '%Y-%m-01')";
        } else {
            $groupBy = 'day';
            $dateExpr = "DATE(created_at)";
        }

        $salesQuery = Order::where('order_status', '!=', 'pending')
            ->selectRaw("
                {$dateExpr} as date_key,
                COUNT(*) as orders_count,
                SUM(grand_total) as sales,
                SUM(paid_amount) as paid,
                SUM(due_amount) as due
            ")
            ->groupBy('date_key')
            ->orderBy('date_key', 'desc');

        $this->applyDateFilter($salesQuery, $fromDate, $toDate);

        $salesByDate = $salesQuery->paginate(15);

        // Transform collection to attach formatted labels
        $salesByDate->getCollection()->transform(function ($sale) use ($groupBy) {
            $sale->date = $sale->date_key;
            if ($groupBy === 'week') {
                $start = \Carbon\Carbon::parse($sale->date_key);
                $end = (clone $start)->addDays(6);
                $sale->formatted_label = $start->format('d M') . ' – ' . $end->format('d M Y');
                $sale->chart_label = $start->format('d M');
            } elseif ($groupBy === 'month') {
                $d = \Carbon\Carbon::parse($sale->date_key);
                $sale->formatted_label = $d->format('F Y');
                $sale->chart_label = $d->format('M Y');
            } else {
                $d = \Carbon\Carbon::parse($sale->date_key);
                $sale->formatted_label = $d->format('d M Y');
                $sale->chart_label = $d->format('d M');
            }
            return $sale;
        });

        return view('admin.reports.sales', compact(
            'totalSales',
            'totalPaid',
            'totalDue',
            'totalOrders',
            'salesByDate',
            'period',
            'groupBy'
        ))->with([
            'fromDate' => $request->from_date ?? ($fromDate ? $fromDate->format('Y-m-d') : ''),
            'toDate'   => $request->to_date ?? ($toDate ? $toDate->format('Y-m-d') : ''),
        ]);
    }

    public function exportSales(Request $request)
    {
        [$fromDate, $toDate, $period] = $this->resolveDateRange($request);
        $groupBy = $request->get('group_by', 'day');

        $fileName = 'sales_report_' . $groupBy . '_' . date('Y-m-d_His') . '.csv';

        if ($groupBy === 'week') {
            $dateExpr = "DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY)";
            $colTitle = 'Week Period';
        } elseif ($groupBy === 'month') {
            $dateExpr = "DATE_FORMAT(created_at, '%Y-%m-01')";
            $colTitle = 'Month';
        } else {
            $groupBy = 'day';
            $dateExpr = "DATE(created_at)";
            $colTitle = 'Date';
        }

        $query = Order::where('order_status', '!=', 'pending')
            ->selectRaw("
                {$dateExpr} as date_key,
                COUNT(*) as orders_count,
                SUM(grand_total) as sales,
                SUM(paid_amount) as paid,
                SUM(due_amount) as due
            ")
            ->groupBy('date_key')
            ->orderBy('date_key', 'desc');

        $this->applyDateFilter($query, $fromDate, $toDate);

        $salesByDate = $query->get();

        $headers = [
            $colTitle,
            'Orders Count',
            'Sales (INR)',
            'Paid (INR)',
            'Due (INR)',
            'Collection Rate (%)',
        ];

        return $this->streamCsvDownload($fileName, $headers, function ($file) use ($salesByDate, $groupBy) {
            foreach ($salesByDate as $sale) {
                $collectionRate = $sale->sales > 0 ? round(($sale->paid / $sale->sales) * 100, 1) : 0;

                if ($groupBy === 'week') {
                    $start = \Carbon\Carbon::parse($sale->date_key);
                    $end = (clone $start)->addDays(6);
                    $displayDate = $start->format('d M Y') . ' - ' . $end->format('d M Y');
                } elseif ($groupBy === 'month') {
                    $displayDate = \Carbon\Carbon::parse($sale->date_key)->format('F Y');
                } else {
                    $displayDate = \Carbon\Carbon::parse($sale->date_key)->format('d-m-Y');
                }

                fputcsv($file, [
                    $displayDate,
                    $sale->orders_count,
                    number_format($sale->sales ?? 0, 2, '.', ''),
                    number_format($sale->paid ?? 0, 2, '.', ''),
                    number_format($sale->due ?? 0, 2, '.', ''),
                    $collectionRate . '%',
                ]);
            }
        });
    }

    public function orders(Request $request)
    {
        [$fromDate, $toDate, $period] = $this->resolveDateRange($request);
        $status = $request->get('status');

        $baseQuery = Order::query();
        $this->applyDateFilter($baseQuery, $fromDate, $toDate);

        $pending    = (clone $baseQuery)->where('order_status', 'pending')->count();
        $processing = (clone $baseQuery)->where('order_status', 'processing')->count();
        $shipped    = (clone $baseQuery)->where('order_status', 'shipped')->count();
        $delivered  = (clone $baseQuery)->where('order_status', 'delivered')->count();
        $cancelled  = (clone $baseQuery)->where('order_status', 'cancelled')->count();

        // Exclude pending orders from the table
        $query = Order::with('user')
            ->where('order_status', '!=', 'pending');

        $this->applyDateFilter($query, $fromDate, $toDate);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.reports.orders', compact(
            'pending',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
            'orders',
            'period'
        ))->with([
            'fromDate'       => $request->from_date ?? ($fromDate ? $fromDate->format('Y-m-d') : ''),
            'toDate'         => $request->to_date ?? ($toDate ? $toDate->format('Y-m-d') : ''),
            'selectedStatus' => $status,
        ]);
    }

    public function exportOrders(Request $request)
    {
        [$fromDate, $toDate, $period] = $this->resolveDateRange($request);

        $fileName = 'order_report_' . date('Y-m-d_His') . '.csv';

        $query = Order::with('user')
            ->where('order_status', '!=', 'pending');

        $this->applyDateFilter($query, $fromDate, $toDate);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->get();

        $headers = [
            'Order ID',
            'Invoice No',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Subtotal (INR)',
            'Discount (INR)',
            'Shipping (INR)',
            'Grand Total (INR)',
            'Paid Amount (INR)',
            'Due Amount (INR)',
            'Payment Method',
            'Payment Status',
            'Order Status',
            'Order Date',
            'Order Time',
        ];

        return $this->streamCsvDownload($fileName, $headers, function ($file) use ($orders) {
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_id,
                    $order->invoice_no ?? '',
                    $order->user->name ?? 'Guest',
                    $order->user->email ?? '',
                    $order->user->phone ?? '',
                    number_format($order->subtotal ?? 0, 2, '.', ''),
                    number_format($order->discount ?? 0, 2, '.', ''),
                    number_format($order->shipping_charge ?? 0, 2, '.', ''),
                    number_format($order->grand_total ?? 0, 2, '.', ''),
                    number_format($order->paid_amount ?? 0, 2, '.', ''),
                    number_format($order->due_amount ?? 0, 2, '.', ''),
                    strtoupper($order->payment_method ?? '-'),
                    ucfirst($order->payment_status ?? '-'),
                    ucfirst($order->order_status ?? '-'),
                    $order->created_at ? $order->created_at->format('d-m-Y') : '',
                    $order->created_at ? $order->created_at->format('H:i:s') : '',
                ]);
            }
        });
    }

    public function customers(Request $request)
    {
        [$fromDate, $toDate, $period] = $this->resolveDateRange($request);

        // Get customers with order count & spend filtered by date range if specified
        $customersQuery = User::where('role', 'user')
            ->whereHas('orders', function($query) use ($fromDate, $toDate) {
                $query->where('order_status', '!=', 'cancelled');
                $this->applyDateFilter($query, $fromDate, $toDate);
            })
            ->withCount(['orders' => function($query) use ($fromDate, $toDate) {
                $query->where('order_status', '!=', 'cancelled');
                $this->applyDateFilter($query, $fromDate, $toDate);
            }])
            ->withSum(['orders' => function($query) use ($fromDate, $toDate) {
                $query->where('order_status', '!=', 'cancelled');
                $this->applyDateFilter($query, $fromDate, $toDate);
            }], 'grand_total')
            ->orderBy('orders_count', 'desc');

        $customers = $customersQuery->paginate(20);

        return view('admin.reports.customers', compact('customers', 'period'))->with([
            'fromDate' => $request->from_date ?? ($fromDate ? $fromDate->format('Y-m-d') : ''),
            'toDate'   => $request->to_date ?? ($toDate ? $toDate->format('Y-m-d') : ''),
        ]);
    }

    public function exportCustomers(Request $request)
    {
        [$fromDate, $toDate, $period] = $this->resolveDateRange($request);

        $fileName = 'customer_report_' . date('Y-m-d_His') . '.csv';

        $customers = User::where('role', 'user')
            ->whereHas('orders', function ($query) use ($fromDate, $toDate) {
                $query->where('order_status', '!=', 'cancelled');
                $this->applyDateFilter($query, $fromDate, $toDate);
            })
            ->withCount(['orders' => function ($query) use ($fromDate, $toDate) {
                $query->where('order_status', '!=', 'cancelled');
                $this->applyDateFilter($query, $fromDate, $toDate);
            }])
            ->withSum(['orders' => function ($query) use ($fromDate, $toDate) {
                $query->where('order_status', '!=', 'cancelled');
                $this->applyDateFilter($query, $fromDate, $toDate);
            }], 'grand_total')
            ->with(['orders' => function ($query) use ($fromDate, $toDate) {
                $query->where('order_status', '!=', 'cancelled');
                $this->applyDateFilter($query, $fromDate, $toDate);
                $query->latest();
            }])
            ->orderBy('orders_count', 'desc')
            ->get();

        $headers = [
            'Customer ID',
            'Customer Name',
            'Email',
            'Phone',
            'Total Orders',
            'Total Spend (INR)',
            'Average Spend (INR)',
            'Last Order Date',
            'Registered Date',
        ];

        return $this->streamCsvDownload($fileName, $headers, function ($file) use ($customers) {
            foreach ($customers as $customer) {
                $ordersCount = $customer->orders_count ?? 0;
                $totalSpend = (float) ($customer->orders_sum_grand_total ?? 0);
                $avgSpend = $ordersCount > 0 ? round($totalSpend / $ordersCount, 2) : 0;
                $lastOrder = $customer->orders->first();

                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone ?? '',
                    $ordersCount,
                    number_format($totalSpend, 2, '.', ''),
                    number_format($avgSpend, 2, '.', ''),
                    $lastOrder && $lastOrder->created_at ? $lastOrder->created_at->format('d-m-Y') : 'N/A',
                    $customer->created_at ? $customer->created_at->format('d-m-Y') : '',
                ]);
            }
        });
    }
}