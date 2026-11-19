<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderPayment;
use App\Models\OrderAddress;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'invoice_no',

        'subtotal',
        'discount',
        'shipping_charge',
        'grand_total',

        'paid_amount',
        'due_amount',

        'payment_method',
        'payment_type',
        'payment_status',
        'order_status',

        'transaction_id',
        'payment_gateway',
        'payment_id',
        'razorpay_order_id',

        'invoice_date',
        'due_date',

        'tracking_id',
        'courier_name',
        'tracking_url',

        'admin_note',
        'notes',

        'delivered_at',
        'cancelled_at',
        'shipped_at',

        // Cancel request workflow
        'cancel_requested_at',
        'cancel_request_reason',
        'cancel_request_note',
        'cancel_approved_at',
        'cancel_rejected_at',
        'cancel_decision_note',

        // Return/exchange workflow
        'return_requested_at',
        'return_request_type',
        'return_reason',
        'return_request_note',
        'return_approved_at',
        'return_rejected_at',
        'return_decision_note',
        'returnable_until',
        'pickup_date',
        'returned_at',

        'full_payment_discount',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',

        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'shipped_at' => 'datetime',

        'cancel_requested_at' => 'datetime',
        'cancel_approved_at' => 'datetime',
        'cancel_rejected_at' => 'datetime',

        'return_requested_at' => 'datetime',
        'return_approved_at' => 'datetime',
        'return_rejected_at' => 'datetime',
        'returned_at' => 'datetime',

        'returnable_until' => 'date',
        'pickup_date' => 'date',
    ];

    protected $appends = [
        'discount_amount',
        'total_paid',
        'payment_status_auto',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function notes()
    {
        return $this->hasMany(OrderNote::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', 'billing');
    }

    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', 'shipping');
    }
    public function firstProduct()
    {
        $item = $this->relationLoaded('items')
            ? $this->items->first()
            : $this->items()->with('product')->first();

        return $item?->product;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDiscountAmountAttribute()
    {
        return (float) (
            ($this->discount ?? 0)
            + ($this->full_payment_discount ?? 0)
        );
    }

    public function getTotalPaidAttribute()
    {
        return $this->orderPayments()->sum('amount');
    }

    public function getPaymentStatusAutoAttribute()
    {
        if ($this->total_paid <= 0) {
            return 'pending';
        }

        if ($this->total_paid < $this->grand_total) {
            return 'partial';
        }

        return 'paid';
    }

    public function invoicePdfFilename(): string
    {
        return 'Invoice-' . ($this->display_order_id ?? $this->id) . '.pdf';
    }
    public function returnDeadline()
    {
        if ($this->returnable_until) {
            return $this->returnable_until;
        }

        if ($this->delivered_at) {
            return $this->delivered_at->copy()->addDays(7);
        }

        if (strtolower($this->order_status ?? '') === 'delivered' && $this->created_at) {
            return $this->created_at->copy()->addDays(7);
        }

        return null;
    }

    public function expectedDeliveryDate()
    {
        if ($this->delivered_at) {
            return $this->delivered_at;
        }

        if ($this->shipped_at) {
            return $this->shipped_at->copy()->addDays(3);
        }

        if ($this->created_at) {
            return $this->created_at->copy()->addDays(7);
        }

        return null;
    }

    public function hasPendingCancelRequest(): bool
    {
        return (bool) ($this->cancel_requested_at && ! $this->cancel_approved_at && ! $this->cancel_rejected_at);
    }

    public function hasPendingReturnRequest(): bool
    {
        return (bool) ($this->return_requested_at && ! $this->return_approved_at && ! $this->return_rejected_at);
    }

    public function canRequestReturn(): bool
    {
        if (strtolower($this->order_status ?? '') !== 'delivered') {
            return false;
        }

        if ($this->hasPendingReturnRequest() || $this->return_approved_at) {
            return false;
        }

        $deadline = $this->returnDeadline();

        return $deadline && now()->lte($deadline);
    }

    public function canRequestCancel(): bool
    {
        return in_array(strtolower($this->order_status ?? ''), ['pending', 'confirmed', 'processing'], true)
            && ! $this->hasPendingCancelRequest()
            && ! $this->cancel_approved_at;
    }

    public function gstBreakdown(): array
    {
        $this->loadMissing('items.product.gstSetting');

        $gstTotal = 0.0;
        $gstRates = [];

        foreach ($this->items as $item) {
            if (! $item->product) {
                continue;
            }

            $price = (float) ($item->price ?? 0);
            $qty = (int) ($item->quantity ?? 0);
            if ($price <= 0 || $qty <= 0) {
                continue;
            }

            $taxInfo = $item->product->getInclusiveTaxInfo($price);
            $gstTotal += ((float) ($taxInfo['tax_amount'] ?? 0)) * $qty;

            $rate = (float) ($taxInfo['gst_percentage'] ?? 0);
            if ($rate > 0) {
                $gstRates[] = $rate;
            }
        }

        $gstTotal = round($gstTotal, 2);
        $gstRateLabel = 'GST (inclusive)';

        if (! empty($gstRates)) {
            $uniqueRates = array_values(array_unique($gstRates));
            if (count($uniqueRates) === 1) {
                $gstRateLabel = $uniqueRates[0] . '% GST (inclusive)';
            }
        }

        return [
            'gst_total' => $gstTotal,
            'gst_rate_label' => $gstRateLabel,
        ];
    }
}