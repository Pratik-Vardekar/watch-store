<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\Product;
class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%');
                 
            });
        }

        // Type Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Sorting - Updated to match view options
        if ($request->sort == 'oldest') {
            $query->orderBy('created_at', 'ASC');
        } elseif ($request->sort == 'code_asc') {
            $query->orderBy('code', 'ASC');
        } elseif ($request->sort == 'code_desc') {
            $query->orderBy('code', 'DESC');
        } else {
            $query->latest(); // Latest First
        }

        $coupons = $query->paginate(10);

        return view('admin.coupon.index', compact('coupons'));
    }

    public function create()
    {
           $categories = Category::where('status',1)->get();
               $products = Product::where('status', 1)->get();

        return view('admin.coupon.add', compact('categories','products'));
    }

    public function store(Request $request)
{
    $request->validate([

        'code'              => 'required|unique:coupons,code',
        'type'              => 'required',
        'value'             => 'required|numeric',
        'minimum_amount'    => 'nullable|numeric',
        'maximum_discount'  => 'nullable|numeric',
        'usage_limit'       => 'nullable|numeric',
        'start_date'        => 'required',
        'end_date'          => 'required',
        'status'            => 'required',

    ]);

   $coupon = Coupon::create([
    'code'                 => strtoupper($request->code),
    'type'                 => $request->type,
    'value'                => $request->value,
    'minimum_amount'       => $request->minimum_amount,
    'maximum_discount'     => $request->maximum_discount,
    'usage_limit'          => $request->usage_limit,
    'used_count'           => 0,
    'start_date'           => $request->start_date,
    'end_date'             => $request->end_date,
    'status'               => $request->status,

    'category_ids'         => $request->categories ?? [],
    'product_ids'          => $request->products ?? [],
    'exclude_category_ids' => $request->excluded_categories ?? [],
    'exclude_product_ids'  => $request->excluded_products ?? [],
]);



    return redirect()
        ->route('coupon.index')
        ->with('success', 'Coupon Added Successfully');
}

    public function edit(Coupon $coupon)
    {
        $categories = Category::where('status',1)->get();
            $products = Product::where('status', 1)->get();

        return view('admin.coupon.edit', compact('coupon','categories','products'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            // 'title'          => 'required',
            'code'           => 'required|unique:coupons,code,' . $coupon->id,
            'type'           => 'required',
            'value'          => 'required|numeric',
            'minimum_amount' => 'nullable|numeric',
                'maximum_discount' => 'nullable|numeric',
            'usage_limit'    => 'nullable|numeric',
            'start_date'     => 'required',
            'end_date'       => 'required',
            'status'         => 'required',
        ]);

       $coupon->update([
    'code'                 => strtoupper($request->code),
    'type'                 => $request->type,
    'value'                => $request->value,
    'minimum_amount'       => $request->minimum_amount,
    'maximum_discount'     => $request->maximum_discount,
    'usage_limit'          => $request->usage_limit,
    'start_date'           => $request->start_date,
    'end_date'             => $request->end_date,
    'status'               => $request->status,

    'category_ids'         => $request->categories ?? [],
    'product_ids'          => $request->products ?? [],
    'exclude_category_ids' => $request->excluded_categories ?? [],
    'exclude_product_ids'  => $request->excluded_products ?? [],
]);

        return redirect()
            ->route('coupon.index')
            ->with('success', 'Coupon Updated Successfully');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('coupon.index')
            ->with('success', 'Coupon Deleted Successfully');
    }
    public function toggleStatus(Coupon $coupon)
{
    $coupon->status = $coupon->status == 1 ? 0 : 1;

    $coupon->save();

    return back()->with('success', 'Coupon status updated successfully');
}
public function export(Request $request)
{
    $query = Coupon::query();

    // Search
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('code', 'like', '%' . $request->search . '%')
              ->orWhere('title', 'like', '%' . $request->search . '%');
        });
    }

    // Type Filter
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    // Status Filter
    if ($request->status !== null && $request->status !== '') {
        $query->where('status', $request->status);
    }

    // Date Filter
    if ($request->filled('date')) {
        $query->whereDate('created_at', $request->date);
    }

    // Sorting
    if ($request->sort == 'oldest') {
        $query->orderBy('created_at', 'ASC');
    } elseif ($request->sort == 'code_asc') {
        $query->orderBy('code', 'ASC');
    } elseif ($request->sort == 'code_desc') {
        $query->orderBy('code', 'DESC');
    } else {
        $query->latest();
    }

    $coupons = $query->get();

    $fileName = 'coupons.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$fileName",
    ];

    $callback = function () use ($coupons) {
        $file = fopen('php://output', 'w');
        
        fputcsv($file, [
            'ID',
            'Code',
            'Title',
            'Type',
            'Value',
            'Minimum Amount',
            'Maximum Discount',
            'Usage Limit',
            'Used Count',
            'Start Date',
            'End Date',
            'Status',
            'Created At'
        ]);

        foreach ($coupons as $coupon) {
            fputcsv($file, [
                $coupon->id,
                $coupon->code,
                $coupon->title ?? '-',
                $coupon->type == 'percent' ? 'Percentage' : 'Fixed',
                $coupon->type == 'percent' ? $coupon->value . '%' : '₹' . $coupon->value,
                $coupon->minimum_amount ? '₹' . $coupon->minimum_amount : 'No minimum',
                $coupon->maximum_discount ? '₹' . $coupon->maximum_discount : 'N/A',
                $coupon->usage_limit ?? 'Unlimited',
                $coupon->used_count,
                $coupon->start_date ? date('d-m-Y', strtotime($coupon->start_date)) : '-',
                $coupon->end_date ? date('d-m-Y', strtotime($coupon->end_date)) : '-',
                $coupon->status == 1 ? 'Active' : 'Inactive',
                $coupon->created_at->format('d-m-Y'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}