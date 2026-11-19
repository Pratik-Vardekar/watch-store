@extends('layouts.front')

@section('content')
@php
    $shipping = $order->shippingAddress ?? $order->addresses->firstWhere('type', 'shipping');
    $billing = $order->billingAddress ?? $order->addresses->firstWhere('type', 'billing');
    $isCancelPending = $order->cancel_requested_at && !$order->cancel_approved_at && !$order->cancel_rejected_at;
    $isCancelApproved = !is_null($order->cancel_approved_at);
    $isReturnPending = $order->return_requested_at && !$order->return_approved_at && !$order->return_rejected_at;
    $isReturnApproved = !is_null($order->return_approved_at);
    $isReturnRejected = !is_null($order->return_rejected_at);
    $returnDeadline = $order->returnDeadline();
    $canRequestReturn = $order->canRequestReturn();
    $canRequestCancel = $order->canRequestCancel();
    $refundTimelineMessage = $order->payment_method === 'razorpay' && (float) ($order->paid_amount ?? 0) > 0
        ? 'Refund will be credited to your original payment method within 2-3 working days after admin approval.'
        : null;
    $gstBreakdown = $order->gstBreakdown();
@endphp
<main class="pageWrapper">
    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-between mb-4">
                <div class="col">
                    <h1 class="sectTitle lh-1 mb-2">Order Details</h1>
                    <p class="mb-0 text-secondary">Order ID: #{{ $order->order_id ?? $order->id }}</p>
                    <p class="text-secondary">Placed on {{ $order->created_at?->format('D, d M Y') }}</p>
                </div>
                <div class="col-auto d-flex flex-wrap gap-2 align-items-start">
                    @if($canRequestCancel)
                        <button type="button" class="olBtn" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">Cancel Order</button>
                    @endif
                    @if($canRequestReturn)
                        <button type="button" class="olBtn" data-bs-toggle="modal" data-bs-target="#returnOrderModal">Exchange / Return</button>
                    @endif
                    @if($isReturnPending)
                        <form action="{{ route('frontend.orders.cancel_return', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="olBtn" onclick="return confirm('Cancel return request?');">Cancel Request</button>
                        </form>
                    @endif
                    <a href="{{ route('profile.orders') }}" class="ctaBtn">
                        <span class="btnTxt">Back to Orders</span>
                        <span class="arw"></span>
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="rounded-4 border p-3 p-md-4 mb-4">
                        <h2 class="sectSubtitle fw-semibold mb-3">Items</h2>
                        @foreach($order->items as $item)
                            @php $product = $item->product; @endphp
                            <div class="d-flex gap-3 align-items-center mb-3 pb-3 border-bottom">
                                <img
                                    src="{{ $product ? $product->getProductImageUrl($product->thumbnail) : asset('frontend/assets/images/no-image.png') }}"
                                    alt="{{ $product->name ?? 'Product' }}"
                                    style="width:72px;height:72px;object-fit:cover;"
                                    class="rounded"
                                >
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $product->name ?? 'Product' }}</div>
                                    <small class="text-secondary">Qty: {{ $item->quantity }}</small>
                                </div>
                                <div class="fw-semibold">₹{{ number_format($item->total ?? ($item->price * $item->quantity), 2) }}</div>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>₹{{ number_format($order->subtotal, 2) }}</strong>
                        </div>
                        @if(($order->discount ?? 0) > 0)
                        <div class="d-flex justify-content-between mb-2 txtRed" style="color: #dc3545;">
                            <span>Discount @if(!empty($order->notes) && str_starts_with($order->notes, 'Coupon: ')) ({{ trim(substr($order->notes, strlen('Coupon: '))) }}) @endif</span>
                            <strong>-₹{{ number_format($order->discount, 2) }}</strong>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <strong>
                                @if(($order->shipping_charge ?? 0) <= 0)
                                    Free
                                @else
                                    ₹{{ number_format($order->shipping_charge, 2) }}
                                @endif
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $gstBreakdown['gst_rate_label'] }}</span>
                            <strong>₹{{ number_format($gstBreakdown['gst_total'], 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span>₹{{ number_format($order->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="rounded-4 border p-3 p-md-4 mb-4">
                        <h2 class="sectSubtitle fw-semibold mb-3">Shipping Address</h2>
                        @if($shipping)
                            <p class="fw-semibold mb-1">{{ $shipping->name }}</p>
                            <p class="text-secondary mb-1">{{ $shipping->address }}@if($shipping->landmark), {{ $shipping->landmark }}@endif</p>
                            <p class="text-secondary mb-1">{{ $shipping->city }}, {{ $shipping->state }} - {{ $shipping->pincode }}</p>
                            <p class="text-secondary mb-0">Phone: {{ $shipping->phone }}</p>
                        @else
                            <p class="text-secondary mb-0">No shipping address saved.</p>
                        @endif
                    </div>

                    <div class="rounded-4 border p-3 p-md-4 mb-4">
                        <h2 class="sectSubtitle fw-semibold mb-3">Billing Address</h2>
                        @if($billing)
                            <p class="fw-semibold mb-1">{{ $billing->name }}</p>
                            <p class="text-secondary mb-1">{{ $billing->address }}@if($billing->landmark), {{ $billing->landmark }}@endif</p>
                            <p class="text-secondary mb-1">{{ $billing->city }}, {{ $billing->state }} - {{ $billing->pincode }}</p>
                            <p class="text-secondary mb-0">Phone: {{ $billing->phone }}</p>
                        @elseif($shipping)
                            <p class="text-secondary small mb-2">Same as shipping address</p>
                            <p class="fw-semibold mb-1">{{ $shipping->name }}</p>
                            <p class="text-secondary mb-1">{{ $shipping->address }}@if($shipping->landmark), {{ $shipping->landmark }}@endif</p>
                            <p class="text-secondary mb-1">{{ $shipping->city }}, {{ $shipping->state }} - {{ $shipping->pincode }}</p>
                            <p class="text-secondary mb-0">Phone: {{ $shipping->phone }}</p>
                        @else
                            <p class="text-secondary mb-0">No billing address saved.</p>
                        @endif
                    </div>

                    <div class="rounded-4 border p-3 p-md-4">
                        <h2 class="sectSubtitle fw-semibold mb-2">Status</h2>
                        @if($isCancelPending)
                            <p class="mb-0 fw-semibold text-warning">Cancellation Requested</p>
                            <p class="text-secondary mb-0 mt-1">Reason: {{ $order->cancel_request_reason ?? '-' }}</p>
                        @elseif($isReturnPending)
                            <p class="mb-0 fw-semibold text-warning">{{ ucfirst($order->return_request_type ?? 'Return') }} Requested</p>
                            <p class="text-secondary mb-0 mt-1">Reason: {{ $order->return_reason ?? '-' }}</p>
                            @if($order->return_request_note)
                                <p class="text-secondary mb-0 mt-1">{{ $order->return_request_note }}</p>
                            @endif
                        @elseif($isReturnApproved || $order->order_status == 'returns')
                            <p class="mb-0 fw-semibold text-warning">{{ ucfirst($order->return_request_type ?? 'Return') }} {{ $isReturnApproved ? 'Approved' : 'In Progress' }}</p>
                            <p class="text-secondary mb-0 mt-1">
                                Pickup: {{ $order->pickup_date ? $order->pickup_date->format('D, d M Y') : 'Awaiting admin schedule' }}
                            </p>
                        @else
                            <p class="mb-0 text-capitalize fw-semibold">{{ str_replace('_', ' ', $order->order_status ?? 'pending') }}</p>
                        @endif

                        <p class="text-secondary mb-0 mt-2">Payment: {{ ucfirst($order->payment_status ?? 'pending') }} ({{ strtoupper($order->payment_method ?? 'cod') }})</p>

                        @if($returnDeadline)
                            <p class="text-secondary mb-0 mt-2">Exchange/Return by: {{ $returnDeadline->format('D, d M Y') }}</p>
                        @endif

                        @if($isCancelPending)
                            <p class="text-warning mb-0 mt-2">Cancellation request is pending admin review.</p>
                        @elseif($order->cancelled_at && $refundTimelineMessage)
                            <p class="text-secondary mb-0 mt-2">{{ $refundTimelineMessage }}</p>
                        @endif

                        @if($isReturnPending)
                            <p class="text-warning mb-0 mt-2">{{ ucfirst($order->return_request_type ?? 'Return') }} request is pending admin review.</p>
                        @elseif(($isReturnApproved || $order->order_status == 'returns') && $order->return_request_type === 'return' && $refundTimelineMessage)
                            <p class="text-secondary mb-0 mt-2">{{ $refundTimelineMessage }}</p>
                        @endif

                        @if($order->return_decision_note)
                            <p class="text-secondary mb-0 mt-2">Admin note: {{ $order->return_decision_note }}</p>
                        @elseif($order->cancel_decision_note)
                            <p class="text-secondary mb-0 mt-2">Admin note: {{ $order->cancel_decision_note }}</p>
                        @endif

                        @if($isReturnRejected)
                            <p class="text-danger mb-0 mt-2">
                                {{ ucfirst($order->return_request_type ?? 'Return') }} request was not approved{{ $order->return_decision_note ? ': '.$order->return_decision_note : '.' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('frontend.orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason</label>
                            <select name="reason" class="form-select" required>
                                <option value="">Select reason</option>
                                <option value="Ordered by mistake">Ordered by mistake</option>
                                <option value="Need to change address">Need to change address</option>
                                <option value="Found a better price">Found a better price</option>
                                <option value="Delay in delivery">Delay in delivery</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Additional Information</label>
                            <textarea name="note" class="form-control" rows="4" placeholder="Share any details for admin review"></textarea>
                        </div>
                        @if($refundTimelineMessage)
                            <small class="d-block mt-3 text-secondary">{{ $refundTimelineMessage }}</small>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="returnOrderModal" tabindex="-1" aria-labelledby="returnOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnOrderModalLabel">Return / Exchange Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('frontend.orders.return', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Request Type</label>
                            <select name="request_type" class="form-select" required>
                                <option value="">Select type</option>
                                <option value="return">Return</option>
                                <option value="exchange">Exchange</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason</label>
                            <select name="reason" class="form-select" required>
                                <option value="">Select reason</option>
                                <option value="Wrong item received">Wrong item received</option>
                                <option value="Size issue">Size issue</option>
                                <option value="Damaged product">Damaged product</option>
                                <option value="Product not as expected">Product not as expected</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Additional Information</label>
                            <textarea name="note" class="form-control" rows="4" placeholder="Share return or exchange details for admin review"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
