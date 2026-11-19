@extends('layouts.admin')

@section('content')

    <div class="container-fluid px-4 py-4" style="max-width:1100px">
        {{-- Header --}}
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-2 mb-3">
                <i class="ti ti-arrow-left"></i> Back
            </a>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <p class="text-muted mb-1" style="font-size:13px">Order Details</p>
                    <h3 class="fw-bold mb-0">Order #{{ $order->order_id }}</h3>
                    <p class="text-muted mb-0 mt-1" style="font-size:13px">
                        Placed on {{ $order->created_at->format('d M Y') }} at {{ $order->created_at->format('h:i A') }}
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('order.pdf', $order->id) }}" class="btn btn-primary d-flex align-items-center gap-2">
                        Invoice<i class="ti ti-download"></i>
                    </a>
                </div>
            </div>
        </div>

        @include('admin.order.partials.customer-requests', ['order' => $order])

        <div class="row g-3 mb-3">
            {{-- Column 1 --}}
            <div class="col-lg-4 d-flex flex-column gap-3">
                {{-- Delivery Address --}}
                @php
                    $shippingAddress = $order->addresses->where('type', 'shipping')->first();
                @endphp
                @if ($shippingAddress)
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom py-3 px-4">
                            <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                                <i class="ti ti-map-pin text-muted"></i> Delivery Address
                            </h6>
                        </div>
                        <div class="card-body px-4 py-3">
                            <p class="fw-semibold mb-1">{{ $shippingAddress->name }}</p>
                            <p class="text-muted mb-1" style="font-size:13px">{{ $shippingAddress->phone }}</p>
                            <p class="text-muted mb-1" style="font-size:13px">{{ $shippingAddress->address }}</p>
                            @if ($shippingAddress->landmark)
                                <p class="text-muted mb-1" style="font-size:13px">{{ $shippingAddress->landmark }}</p>
                            @endif
                            <p class="text-muted mb-0" style="font-size:13px">
                                {{ $shippingAddress->city }}, {{ $shippingAddress->state }} —
                                {{ $shippingAddress->pincode }}
                            </p>
                        </div>
                    </div>
                @endif
                @php
                    $grandTotal = (float) $order->grand_total;
                    $breakdown = $order->getPaymentBreakdown();
                    $paidAmount = $breakdown['paid'];
                    $dueAmount = $breakdown['due'];
                    $isPaid = $breakdown['is_paid'];
                    $isPartial = $breakdown['is_partial'];
                @endphp
                {{-- Payment Information --}}
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                            <i class="ti ti-credit-card text-muted"></i> Payment Information
                        </h6>
                        @if ($order->payment_status == 'paid')
                            <span class="badge bg-success" style="font-size:12px;padding:5px 12px">
                                Fully Paid
                            </span>
                        @elseif($order->payment_status == 'partial')
                            <span class="badge bg-warning text-dark" style="font-size:12px;padding:5px 12px">
                                Partial
                            </span>
                        @else
                            <span class="badge bg-secondary" style="font-size:12px;padding:5px 12px">
                                {{ ucfirst($order->payment_status ?? 'Pending') }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted" style="font-size:13px;">Method:</span>
                            <span class="badge bg-secondary" style="font-size:12px;padding:5px 10px">
                                {{ strtoupper($order->payment_method ?? '-') }}
                            </span>
                        </div>
                        @if ($order->payment_type)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted" style="font-size:13px;">Payment Type:</span>
                                <span class="fw-semibold"
                                    style="font-size:13px;">{{ ucfirst($order->payment_type) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted" style="font-size:13px;">Total Amount:</span>
                            <span class="fw-bold"
                                style="font-size:13px;">₹{{ number_format($order->grand_total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted" style="font-size:13px;">Paid Amount:</span>
                            <span class="fw-semibold text-success"
                                style="font-size:13px;">₹{{ number_format($paidAmount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted" style="font-size:13px;">Due Amount:</span>
                            <span class="fw-semibold {{ $dueAmount > 0 ? 'text-danger' : 'text-muted' }}"
                                style="font-size:13px;">₹{{ number_format($dueAmount, 2) }}</span>
                        </div>

                        {{-- Quick Action: Mark Remaining Payment as Paid --}}
                        @if ($dueAmount > 0 || $order->payment_status !== 'paid')
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <div class="small fw-semibold text-dark mb-2 d-flex justify-content-between">
                                    <span><i class="ti ti-cash me-1 text-danger"></i> Remaining Due:</span>
                                    <span class="text-danger fw-bold">₹{{ number_format($dueAmount, 2) }}</span>
                                </div>
                                <form action="{{ route('order.remaining.payment', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Confirm receipt of remaining payment of ₹{{ number_format($dueAmount, 2) }}? This will mark the order as Fully Paid.');">
                                    @csrf
                                    <input type="hidden" name="amount" value="{{ $dueAmount }}">
                                    <button type="submit"
                                        class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                                        <i class="ti ti-check"></i> Mark Remaining as Paid
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Update Payment Status --}}
                        <div>
                            <label class="form-label text-muted mb-1" style="font-size:12px;">Change Payment Status:</label>
                            @if ($order->payment_status == 'paid')
                                <div class="position-relative">
                                    <select class="form-select form-select-sm bg-light text-muted" disabled
                                        style="cursor: not-allowed;" title="Payment status is fully paid and locked">
                                        <option selected>Paid (Fully Paid)</option>
                                    </select>
                                </div>
                                <small class="text-muted d-flex align-items-center gap-1 mt-1" style="font-size:11px;">
                                    <i class="ti ti-lock text-success"></i> Fully paid orders cannot be changed
                                </small>
                            @else
                                <form action="{{ route('order.payment.status', $order->id) }}" method="POST"
                                    class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="payment_status" class="form-select form-select-sm"
                                        onchange="if(confirm('Change payment status to ' + this.value + '?')) { this.form.submit(); } else { location.reload(); }">
                                        <!--<option value="pending"-->
                                        <!--    {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>-->
                                        <option value="partial"
                                            {{ $order->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>
                                            Paid (Fully Paid)</option>
                                    </select>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Order Status & Shipping Details --}}
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                            <i class="ti ti-truck text-muted"></i> Order &amp; Shipping
                        </h6>
                        @php
                            $statusBadgeClass = match ($order->order_status) {
                                'pending' => 'bg-warning text-dark',
                                'confirmed' => 'bg-info text-dark',
                                'processing' => 'bg-info text-dark',
                                'shipped' => 'bg-primary',
                                'delivered' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                'returns' => 'bg-secondary',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusBadgeClass }}" style="font-size:11px;padding:4px 8px">
                            {{ ucfirst($order->order_status ?? 'Pending') }}
                        </span>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="mb-3">
                            <label class="form-label text-muted mb-1" style="font-size:12px;">Change Status:</label>
                            <form action="{{ route('order.status', $order->id) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                @method('PUT')
                                <select name="order_status" class="form-select form-select-sm" style="min-width:140px"
                                    onchange="this.form.submit()">
                                    <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="confirmed" {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>
                                        Confirmed</option>
                                    <option value="processing"
                                        {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>
                                        Shipped</option>
                                    <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>
                                        Delivered</option>
                                    <option value="returns" {{ $order->order_status == 'returns' ? 'selected' : '' }}>
                                        Returns</option>
                                    <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </form>
                        </div>

                        {{-- Tracking Info (if shipped or tracking exists) --}}
                        @if ($order->order_status === 'shipped' || $order->shipped_at || $order->tracking_id || $order->courier_name)
                            <div class="p-3 bg-light rounded-3 mb-3 border">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-semibold text-primary" style="font-size:13px;">
                                        <i class="ti ti-package me-1"></i> Shipping Details
                                    </span>
                                    @if ($order->shipped_at)
                                        <small class="text-muted"
                                            style="font-size:11px;">{{ $order->shipped_at->format('d M Y, h:i A') }}</small>
                                    @endif
                                </div>
                                @if ($order->courier_name)
                                    <div class="text-muted mb-1" style="font-size:12px;">
                                        <strong>Courier:</strong> {{ $order->courier_name }}
                                    </div>
                                @endif
                                @if ($order->tracking_id)
                                    <div class="text-muted mb-1" style="font-size:12px;">
                                        <strong>Tracking / AWB:</strong> <span
                                            class="badge bg-white text-dark border">{{ $order->tracking_id }}</span>
                                    </div>
                                @endif
                                @if ($order->tracking_url)
                                    <div class="mt-2">
                                        <a href="{{ $order->tracking_url }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:12px;">
                                            <i class="ti ti-external-link me-1"></i> Track Shipment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- SHIP BUTTON / UPDATE SHIPPING BUTTON --}}
                        @if (in_array($order->order_status, ['processing', 'confirmed', 'pending']))
                            <div class="mt-2">
                                <button type="button"
                                    class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#shipModal">
                                    <i class="ti ti-truck-delivery"></i> Mark as Shipped &amp; Notify
                                </button>
                            </div>
                        @elseif($order->order_status === 'shipped')
                            <div class="mt-2">
                                <button type="button"
                                    class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#shipModal">
                                    <i class="ti ti-edit"></i> Edit Tracking Details
                                </button>
                            </div>
                        @endif

                        {{-- SHIPROCKET --}}
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold" style="font-size:13px;">
                                    <i class="ti ti-rocket me-1 text-primary"></i> Shiprocket
                                </span>
                                @if ($order->shiprocket_status)
                                    <span class="badge bg-light text-dark border" style="font-size:11px;">{{ $order->shiprocket_status }}</span>
                                @endif
                            </div>

                            @if (! $shiprocketConfigured)
                                <p class="text-muted mb-0" style="font-size:12px;">
                                    Not configured. Add the <code>SHIPROCKET_*</code> values to <code>.env</code>.
                                </p>
                            @else
                                @if ($order->shiprocket_error)
                                    <div class="alert alert-danger py-2 px-3 mb-2" style="font-size:12px;">
                                        {{ $order->shiprocket_error }}
                                    </div>
                                @endif

                                @if ($order->shiprocket_order_id)
                                    <div class="text-muted mb-1" style="font-size:12px;">
                                        <strong>SR Order:</strong> {{ $order->shiprocket_order_id }}
                                        &middot; <strong>Shipment:</strong> {{ $order->shiprocket_shipment_id }}
                                    </div>
                                @endif
                                @if ($order->shiprocket_pickup_scheduled_at)
                                    <div class="text-muted mb-1" style="font-size:12px;">
                                        <strong>Pickup:</strong> {{ $order->shiprocket_pickup_scheduled_at->format('d M Y') }}
                                    </div>
                                @endif
                                @if ($order->shiprocket_synced_at)
                                    <div class="text-muted mb-2" style="font-size:11px;">
                                        Last update {{ $order->shiprocket_synced_at->diffForHumans() }}
                                    </div>
                                @endif

                                <div class="d-flex flex-column gap-2">
                                    @if (in_array($order->order_status, ['processing', 'confirmed', 'pending']))
                                        <form action="{{ route('order.shiprocket.ship', $order->id) }}" method="POST"
                                            onsubmit="return confirm('Book a courier via Shiprocket? Shipping charges will be deducted from the Shiprocket wallet.');">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                                <i class="ti ti-rocket"></i> Ship with Shiprocket
                                            </button>
                                        </form>
                                    @endif

                                    @if ($order->shiprocket_shipment_id && $order->tracking_id)
                                        <a href="{{ route('order.shiprocket.label', $order->id) }}" target="_blank"
                                            class="btn btn-outline-dark btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                            <i class="ti ti-printer"></i> Print Shipping Label
                                        </a>
                                        <form action="{{ route('order.shiprocket.sync', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                                <i class="ti ti-refresh"></i> Refresh Tracking
                                            </button>
                                        </form>
                                    @endif

                                    @if ($order->shiprocket_order_id && ! in_array($order->order_status, ['delivered', 'completed', 'cancelled', 'returns']))
                                        <form action="{{ route('order.shiprocket.cancel', $order->id) }}" method="POST"
                                            onsubmit="return confirm('Cancel this shipment in Shiprocket? This only works before the courier picks it up.');">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                                <i class="ti ti-x"></i> Cancel Shiprocket Shipment
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            {{-- Column 2 --}}
            <div class="col-lg-8 d-flex flex-column gap-3">

                {{-- Ordered Products --}}
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                            <i class="ti ti-shopping-bag text-muted"></i> Ordered Products
                        </h6>
                        @php
                            $orderItems = $order->valid_items;
                        @endphp
                        <span class="badge bg-light text-muted" style="font-size:12px">
                            {{ $orderItems->count() }} item(s)
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3"
                                        style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280"
                                        colspan="2">Product</th>
                                    <th class="text-center py-3"
                                        style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">
                                        Qty</th>
                                    <th class="text-end py-3"
                                        style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">
                                        Price</th>
                                    <th class="text-end px-4 py-3"
                                        style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderItems as $item)
                                    <tr>
                                        {{-- Product Image --}}
                                        <td class="ps-4 py-3" style="width:64px">
                                            @php $img = $item->product->thumbnail ?? null; @endphp
                                            @if ($img)
                                                <img src="{{ asset('backend/assets/images/product/' . $img) }}"
                                                    alt="{{ $item->product->name }}"
                                                    style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb">
                                            @else
                                                <div
                                                    style="width:48px;height:48px;border-radius:8px;background:#f3f4f6;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center">
                                                    <i class="ti ti-photo" style="font-size:18px;color:#9ca3af"></i>
                                                </div>
                                            @endif
                                        </td>
                                        {{-- Product Name --}}
                                        <td class="py-3 fw-semibold" style="font-size:14px">
                                            {{ $item->product->name ?? '-' }}
                                            @if ($item->product->sku ?? null)
                                                <br><span class="text-muted fw-normal" style="font-size:11px">SKU:
                                                    {{ $item->product->sku }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center py-3">{{ $item->quantity }}</td>
                                        <td class="text-end py-3 text-muted">₹{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end px-4 py-3 fw-semibold">₹{{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="ti ti-inbox d-block mb-2" style="font-size:28px;opacity:.4"></i>
                                            No products found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card border-0 shadow-sm h-100" style="background: #fafbfc;">
                    <div class="card-header bg-white border-bottom py-2 px-4">
                        <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                            <i class="ti ti-currency-rupee text-muted"></i>
                            Payment Summary
                        </h6>
                    </div>

                    <div class="card-body px-4 py-3">
                        <div class="table-responsive">
                            <table class="table mb-0"
                                style="max-width:400px; min-width:260px; margin:auto; background:transparent;">
                                <tbody>
                                    {{-- Subtotal --}}
                                    <tr>
                                        <td class="text-muted border-0" style="font-size:13px; padding:7px 12px 7px 7px;">
                                            Subtotal</td>
                                        <td class="text-end border-0" style="font-size:13px; padding:7px 7px 7px 12px;">
                                            ₹{{ number_format($order->subtotal ?? 0, 2) }}
                                        </td>
                                    </tr>

                                    {{-- Discount --}}
                                    @if (($order->discount ?? 0) > 0)
                                        <tr>
                                            <td class="text-muted border-0"
                                                style="font-size:13px; padding:7px 12px 7px 7px;">Discount @if (!empty($order->notes) && str_starts_with($order->notes, 'Coupon: '))
                                                    <span
                                                        class="text-danger">({{ trim(substr($order->notes, strlen('Coupon: '))) }})</span>
                                                @endif
                                            </td>
                                            <td class="text-end border-0 text-danger"
                                                style="font-size:13px; padding:7px 7px 7px 12px;">
                                                -₹{{ number_format($order->discount ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    @endif

                                    {{-- Shipping --}}
                                    <tr>
                                        <td class="text-muted border-0" style="font-size:13px; padding:7px 12px 7px 7px;">
                                            Shipping</td>
                                        <td class="text-end border-0" style="font-size:13px; padding:7px 7px 7px 12px;">
                                            @if (($order->shipping_charge ?? 0) <= 0)
                                                <span class="text-success">Free</span>
                                            @else
                                                ₹{{ number_format($order->shipping_charge ?? 0, 2) }}
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- GST - Total Only --}}
                                    @php
                                        $gstData = $order->gstBreakdown();
                                    @endphp

                                    <tr>
                                        <td class="text-muted border-0" style="font-size:13px; padding:7px 12px 7px 7px;">
                                            {{ $gstData['gst_rate_label'] ?? 'GST (18% inclusive)' }}
                                        </td>
                                        <td class="text-end border-0" style="font-size:13px; padding:7px 7px 7px 12px;">
                                            ₹{{ number_format($gstData['gst_total'] ?? 0, 2) }}
                                        </td>
                                    </tr>

                                    {{-- Grand Total --}}
                                    <tr style="background:#f3f4f6;">
                                        <td class="fw-bold border-0"
                                            style="font-size:15px; padding:11px 12px 11px 7px; background:#f3f4f6;">
                                            Grand Total
                                        </td>
                                        <td class="fw-bold text-end border-0"
                                            style="font-size:19px; padding:11px 7px 11px 12px; background:#f3f4f6;">
                                            ₹{{ number_format($order->grand_total ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted border-0"
                                            style="font-size:13px; padding:7px 12px 7px 7px;">
                                            Paid Amount
                                        </td>
                                        <td class="text-end border-0 text-success fw-bold"
                                            style="font-size:14px; padding:7px 7px 7px 12px;">
                                            ₹{{ number_format($paidAmount, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted border-0"
                                            style="font-size:13px; padding:7px 12px 7px 7px;">
                                            Due Amount
                                        </td>
                                        <td class="text-end border-0 {{ $dueAmount > 0 ? 'text-danger fw-bold' : 'text-muted' }}"
                                            style="font-size:14px; padding:7px 7px 7px 12px;">
                                            ₹{{ number_format($dueAmount, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- {{-- Transaction History --}}
        @if ($order->orderPayments->count())
    <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-history text-muted"></i> Transaction History
                </h6>
                <span class="badge bg-light text-muted" style="font-size:12px">
                    {{ $order->orderPayments->count() }} transaction(s)
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Amount</th>
                            <th class="py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Method</th>
                            <th class="py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Stage</th>
                            <th class="py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Status</th>
                            <th class="text-end px-4 py-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6b7280">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderPayments as $payment)
    <tr>
                            <td class="px-4 py-3 fw-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark border" style="font-size:11px;font-weight:600;letter-spacing:.3px">
                                    {{ strtoupper($payment->payment_method ?? '-') }}
                                </span>
                            </td>
                            <td class="py-3 text-muted">{{ ucfirst($payment->payment_stage ?? '-') }}</td>
                            <td class="py-3">
                                @if ($payment->status == 'success')
    <span class="badge bg-success" style="font-size:12px;padding:5px 12px">Success</span>
@elseif($payment->status == 'failed')
    <span class="badge bg-danger" style="font-size:12px;padding:5px 12px">Failed</span>
@else
    <span class="badge bg-warning text-dark" style="font-size:12px;padding:5px 12px">Pending</span>
    @endif
                            </td>
                            <td class="text-end px-4 py-3 text-muted" style="font-size:13px">
                                {{ $payment->created_at->format('d M Y') }}<br>
                                <span style="font-size:12px">{{ $payment->created_at->format('h:i A') }}</span>
                            </td>
                        </tr>
    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif -->



        {{-- Ship Modal --}}
        <div class="modal fade" id="shipModal" tabindex="-1" aria-labelledby="shipModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="shipModalLabel">
                            <i class="ti ti-truck-delivery me-1"></i>
                            {{ $order->order_status === 'shipped' ? 'Update Shipping Details' : 'Ship Order #' . ($order->order_id ?? $order->id) }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('order.ship', $order->id) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2"
                                style="font-size:13px">
                                <i class="ti ti-mail-forward fs-5"></i>
                                <span>Marking as shipped will automatically send email notifications to both the
                                    <strong>Customer</strong> and <strong>Admin</strong>.</span>
                            </div>

                            <div class="mb-3">
                                <label for="courier_name" class="form-label fw-semibold" style="font-size:13px">Courier /
                                    Shipping Partner</label>
                                <input type="text" class="form-control" id="courier_name" name="courier_name"
                                    value="{{ old('courier_name', $order->courier_name) }}"
                                    placeholder="e.g. Blue Dart, Delhivery, DTDC, FedEx">
                            </div>

                            <div class="mb-3">
                                <label for="tracking_id" class="form-label fw-semibold" style="font-size:13px">Tracking /
                                    AWB Number</label>
                                <input type="text" class="form-control" id="tracking_id" name="tracking_id"
                                    value="{{ old('tracking_id', $order->tracking_id) }}"
                                    placeholder="e.g. TRK123456789">
                            </div>

                            <div class="mb-3">
                                <label for="tracking_url" class="form-label fw-semibold" style="font-size:13px">Tracking
                                    URL <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="url" class="form-control" id="tracking_url" name="tracking_url"
                                    value="{{ old('tracking_url', $order->tracking_url) }}"
                                    placeholder="https://example.com/track/123456789">
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3">
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                                <i class="ti ti-check"></i>
                                {{ $order->order_status === 'shipped' ? 'Update & Resend Notification' : 'Confirm & Mark Shipped' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        .card {
            border-radius: 12px;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }

        .table-hover tbody tr:hover {
            background: #f8fafc;
        }
    </style>
@endpush
