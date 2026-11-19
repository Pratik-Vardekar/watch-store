<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Slider;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Basic counts ──────────────────────────────────────────────
        $totalUsers         = User::count();
        $totalProducts      = Product::count();
        $totalCategories    = Category::count();
        $totalSubCategories = SubCategory::count();
        $totalOrders        = Order::count();
        $totalReviews       = Review::count();
        $totalCoupons       = Coupon::count();
        $totalBlogs         = Blog::count();
        $totalRevenue       = Order::sum('grand_total');
        $totalSliders       = Slider::where('status', '1')->count();

        // ── Recent records ────────────────────────────────────────────
        $recentOrders   = Order::with('user')->latest()->take(10)->get();
        $recentUsers    = User::latest()->take(5)->get();
        $latestProducts = Product::with('category')->latest()->take(5)->get();

        // ── Monthly Revenue & Orders chart (last 12 months) ───────────
        $monthlyData = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at)  as year'),
                DB::raw('SUM(grand_total)  as revenue'),
                DB::raw('COUNT(*)          as orders')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(fn($r) => $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT));

        $revenueChartData = [];
        $ordersChartData  = [];
        $revenueMonths    = [];

        for ($i = 11; $i >= 0; $i--) {
            $date  = Carbon::now()->subMonths($i);
            $key   = $date->format('Y-m');

            $revenueMonths[]    = $date->format('M');
            $revenueChartData[] = isset($monthlyData[$key]) ? round($monthlyData[$key]->revenue, 2) : 0;
            $ordersChartData[]  = isset($monthlyData[$key]) ? (int) $monthlyData[$key]->orders      : 0;
        }

        // ── Orders by order_status donut ──────────────────────────────
        $ordersByStatus = Order::select('order_status as status', DB::raw('COUNT(*) as total'))
            ->groupBy('order_status')
            ->pluck('total', 'status')
            ->toArray();

        $donutLabels = array_keys($ordersByStatus);
        $donutSeries = array_map('intval', array_values($ordersByStatus));

        // ── Payment status breakdown (second donut / extra insight) ───
        $ordersByPayment = Order::select('payment_status as status', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_status')
            ->pluck('total', 'status')
            ->toArray();

        $paymentLabels = array_keys($ordersByPayment);
        $paymentSeries = array_map('intval', array_values($ordersByPayment));

        // ── Sparklines — last 40 days per model ───────────────────────
        $categorySparkline = $this->buildSparkline(Category::class);
        $productSparkline  = $this->buildSparkline(Product::class);
        $blogSparkline     = $this->buildSparkline(Blog::class);
        $orderSparkline    = $this->buildSparkline(Order::class);

        // ── New users per month (last 6) ──────────────────────────────
        $newUsersPerMonth = User::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at)  as year'),
                DB::raw('COUNT(*)          as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // ========== 4 DISTINCT VISUAL METRICS FOR STAT CARDS ==========
        
        // VISUAL 1: CIRCULAR PROGRESS RING - Category completion vs target
        $categoryTarget = 25;
        $categoryProgress = $totalCategories > $categoryTarget ? 100 : round(($totalCategories / $categoryTarget) * 100);
        
        // VISUAL 2: SMALL BAR GRAPH - Last 7 days product additions
        $weeklyProducts = Product::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        
       $barLabels = Category::pluck('name')->toArray();

$barValues = Category::withCount('products')
    ->get()
    ->pluck('products_count')
    ->toArray();
        
        
        // VISUAL 3: SPARKLINE - Already have $blogSparkline, using for blogs card
        
        // VISUAL 4: MINI DONUT - Review ratings distribution
        $ratingDistribution = Review::select('rating', DB::raw('COUNT(*) as total'))
            ->whereNotNull('rating')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('total', 'rating')
            ->toArray();
        
        $donutRatingLabels = [];
        $donutRatingValues = [];
        for ($i = 5; $i >= 1; $i--) {
            $donutRatingLabels[] = $i . '★';
            $donutRatingValues[] = $ratingDistribution[$i] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalCategories',
            'totalSubCategories',
            'totalOrders',
            'totalReviews',
            'totalCoupons',
            'totalBlogs',
            'totalRevenue',
            'totalSliders',
            'recentOrders',
            'recentUsers',
            'latestProducts',
            'revenueMonths',
            'revenueChartData',
            'ordersChartData',
            'donutLabels',
            'donutSeries',
            'paymentLabels',
            'paymentSeries',
            'categorySparkline',
            'productSparkline',
            'blogSparkline',
            'orderSparkline',
            'newUsersPerMonth',
            // new variables for 4 distinct visuals
            'categoryProgress',
            'categoryTarget',
            'barLabels',
            'barValues',
            'donutRatingLabels',
            'donutRatingValues'
        ));
    }

    private function buildSparkline(string $model): array
    {
        $start = Carbon::now()->subDays(40)->startOfDay();
        $end   = Carbon::now()->endOfDay();

        $counts = $model::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date')
            ->toArray();

        $sparkline = [];

        for ($i = 40; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $value = (int) ($counts[$date] ?? 0);
            $sparkline[] = $value;
        }

        return $sparkline;
    }
}