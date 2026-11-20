@extends('layouts.front')

@push('styles')
    <style>
        .ordersPage .olItem {
            background: #fff;
            border: 1px solid #ececec;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .ordersPage .olHead {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: .75rem 1.25rem;
            padding-bottom: .85rem;
            margin-bottom: .85rem;
            border-bottom: 1px solid #eee;
        }

        .ordersPage .olHeadStatus {
            max-width: 100%;
            text-align: left;
        }

        .ordersPage .olProductRow {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }

        .ordersPage .olProductRow .olImg {
            flex: 0 0 auto;
            width: 4.5rem;
            height: 4.5rem;
            object-fit: contain;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: .35rem;
        }

        .ordersPage .olProductMeta {
            min-width: 0;
            flex: 1 1 auto;
        }

        .ordersPage .olFooter {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: .75rem 1rem;
            margin-top: 1rem;
            padding-top: .9rem;
            border-top: 1px solid #eee;
        }

        .ordersPage .olStatusBadge {
            display: inline-block;
            font-size: .8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        @media (min-width: 576px) {
            .ordersPage .olHeadStatus {
                text-align: right;
                max-width: 58%;
            }

            .ordersPage .olProductRow .olImg {
                width: 5.5rem;
                height: 5.5rem;
            }
        }

        @media (max-width: 575.98px) {
            .ordersPage .olBtns .olBtn {
                flex: 1 1 100%;
                min-width: 0;
            }

            .ordersPage .olItem {
                padding: .9rem !important;
            }
        }
    </style>
@endpush

@section('content')

    <main class="pageWrapper">

        <div class="cloudWrap position-relative d-none d-lg-block">
            <div class="container clouds position-relative"></div>
        </div>

        <section class="py-4 py-lg-5 ordersPage">
            <div class="container">
                <div class="row justify-content-between mb-4 mb-lg-5">
                    @include('frontend.partials.myaccount_nav', ['active' => 'orders', 'user' => $user])

                    <div class="col-lg-9 mb-lg-4 myAccContent">

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 mb-md-4">
                            <h2 class="sectTitle lh-1 mb-0 d-inline-flex align-items-center flex-wrap gap-2 gap-sm-3">
                                Orders
                                <small class="bagCount bgOrange text-white rounded-pill py-1 px-2">{{ $orders->total() }}</small>
                            </h2>
                            <form method="GET" class="myAccFilterForm">
                                <select class="form-select fw-semibold" name="status" onchange="this.form.submit()">
                                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>All Orders
                                    </option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                                        Delivered</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                    <option value="returns" {{ request('status') == 'returns' ? 'selected' : '' }}>Returns
                                    </option>
                                </select>
                            </form>
                        </div>

                        <ul class="ordersList list-unstyled d-flex flex-column gap-sm-4 gap-3 mb-0">

                            @forelse($orders as $order)
                                @php
                                    $orderItems = $order->valid_items;
                                    $productCount = $orderItems->count();
                                    $unitCount = (int) $orderItems->sum('quantity');
                                    $isCancelPending =
                                        $order->cancel_requested_at &&
                                        !$order->cancel_approved_at &&
                                        !$order->cancel_rejected_at;
                                    $isCancelApproved = !is_null($order->cancel_approved_at);
                                    $isCancelRejected = !is_null($order->cancel_rejected_at);
                                    $isReturnPending =
                                        $order->return_requested_at &&
                                        !$order->return_approved_at &&
                                        !$order->return_rejected_at;
                                    $isReturnApproved = !is_null($order->return_approved_at);
                                    $isReturnRejected = !is_null($order->return_rejected_at);
                                    $returnDeadline = $order->returnDeadline();
                                    $expectedDeliveryDate = $order->expectedDeliveryDate();
                                    $canRequestReturn = $order->canRequestReturn();
                                    $canRequestCancel = $order->canRequestCancel();
                                    $refundTimelineMessage =
                                        $order->payment_method === 'razorpay' && (float) ($order->paid_amount ?? 0) > 0
                                            ? 'Refund will be credited to your original payment method within 2-3 working days after admin approval.'
                                            : null;
                                @endphp

                                <li class="rounded-3 p-3 p-md-4 olItem">
                                    <div class="olHead">
                                        <div>
                                            <div class="fw-bold">Order ID : #{{ $order->order_id }}</div>
                                            <small class="text-secondary">Placed on
                                                {{ $order->created_at ? $order->created_at->format('D, d M Y') : '' }}</small>
                                        </div>
                                        <div class="olHeadStatus">

                                            @if ($isCancelPending)
                                                <strong class="olStatusBadge txtorange">Cancellation Requested</strong>
                                                <small class="d-block">Requested on
                                                    {{ $order->cancel_requested_at?->format('D, d M Y') }}</small>
                                                <small class="d-block mt-2 olInfo">
                                                    Reason: {{ $order->cancel_request_reason ?? '-' }}
                                                </small>
                                                @if ($refundTimelineMessage)
                                                    <small class="d-block mt-1 olInfo">{{ $refundTimelineMessage }}</small>
                                                @endif
                                            @elseif($isReturnPending)
                                                <strong
                                                    class="olStatusBadge txtorange">{{ ucfirst($order->return_request_type ?? 'Return') }}
                                                    Requested</strong>
                                                <small class="d-block">Requested on
                                                    {{ $order->return_requested_at?->format('D, d M Y') }}</small>
                                                <small class="d-block mt-2 olInfo">
                                                    Reason: {{ $order->return_reason ?? '-' }}
                                                </small>
                                                @if ($order->return_request_note)
                                                    <small
                                                        class="d-block mt-1 olInfo">{{ $order->return_request_note }}</small>
                                                @endif
                                            @elseif($isReturnApproved || $order->order_status == 'returns')
                                                <strong
                                                    class="olStatusBadge txtorange">{{ ucfirst($order->return_request_type ?? 'Return') }}
                                                    {{ $isReturnApproved ? 'Approved' : 'In Progress' }}</strong>
                                                <small class="d-block">On
                                                    {{ $order->returned_at ? $order->returned_at->format('D, d M Y') : ($order->return_approved_at ? $order->return_approved_at->format('D, d M Y') : ($order->created_at ? $order->created_at->format('D, d M Y') : '')) }}</small>
                                                <small class="d-block mt-2 olInfo">
                                                    Expected Pickup:
                                                    {{ $order->pickup_date ? $order->pickup_date->format('l, d M Y') : 'Awaiting admin schedule' }}
                                                </small>
                                                @if ($order->return_request_type === 'return' && $refundTimelineMessage)
                                                    <small class="d-block mt-1 olInfo">{{ $refundTimelineMessage }}</small>
                                                @endif
                                            @elseif($order->order_status == 'pending')
                                                <strong class="olStatusBadge txtyellow">Order Placed</strong>
                                                <small class="d-block">On
                                                    {{ $order->created_at ? $order->created_at->format('D, d M Y') : '' }}</small>
                                                <small class="d-block mt-2 olInfo">
                                                    Delivery by:
                                                    {{ $expectedDeliveryDate ? $expectedDeliveryDate->format('l, d M Y') : '-' }}
                                                </small>
                                            @elseif(strtolower($order->order_status ?? '') == 'delivered')
                                                <strong class="olStatusBadge txtgreen">Order Delivered</strong>
                                                <small class="d-block">On
                                                    {{ $order->delivered_at ? $order->delivered_at->format('D, d M Y') : ($order->created_at ? $order->created_at->format('D, d M Y') : '') }}</small>
                                                <small class="d-block mt-2 olInfo">
                                                    Exchange/Return by:
                                                    {{ $returnDeadline ? $returnDeadline->format('l, d M Y') : '-' }}
                                                </small>
                                            @elseif($order->order_status == 'cancelled')
                                                <strong class="olStatusBadge text-danger">Order Cancelled</strong>
                                                <small class="d-block">On
                                                    {{ $order->cancelled_at ? $order->cancelled_at->format('D, d M Y') : ($order->created_at ? $order->created_at->format('D, d M Y') : '') }}</small>
                                                @if ($refundTimelineMessage)
                                                    <small class="d-block mt-2 olInfo">{{ $refundTimelineMessage }}</small>
                                                @endif
                                            @else
                                                <strong
                                                    class="olStatusBadge text-muted text-capitalize">{{ ucfirst($order->order_status ?? 'unknown') }}</strong>
                                                <small class="d-block">On
                                                    {{ $order->created_at ? $order->created_at->format('D, d M Y') : '' }}</small>
                                                @if (!in_array(strtolower($order->order_status ?? ''), ['cancelled', 'delivered', 'returns'], true))
                                                    <small class="d-block mt-2 olInfo">
                                                        Delivery by:
                                                        {{ $expectedDeliveryDate ? $expectedDeliveryDate->format('l, d M Y') : '-' }}
                                                    </small>
                                                @endif
                                            @endif

                                            @if ($isCancelRejected)
                                                <small class="d-block mt-2 text-danger">
                                                    Cancellation request was not
                                                    approved{{ $order->cancel_decision_note ? ': ' . $order->cancel_decision_note : '.' }}
                                                </small>
                                            @endif

                                            @if ($isReturnRejected)
                                                <small class="d-block mt-2 text-danger">
                                                    {{ ucfirst($order->return_request_type ?? 'Return') }} request was not
                                                    approved{{ $order->return_decision_note ? ': ' . $order->return_decision_note : '.' }}
                                                </small>
                                            @endif

                                            @if ($isReturnApproved && $order->return_decision_note)
                                                <small class="d-block mt-2 olInfo">
                                                    Admin note: {{ $order->return_decision_note }}
                                                </small>
                                            @endif

                                            @if ($isCancelApproved && $order->cancel_decision_note)
                                                <small class="d-block mt-2 olInfo">
                                                    Admin note: {{ $order->cancel_decision_note }}
                                                </small>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="d-flex flex-column gap-3 olProductList olContent">
                                        @forelse($orderItems as $item)
                                            @php
                                                $product = $item->product;
                                                $productImg = $product
                                                    ? $product->getProductImageUrl($product->thumbnail)
                                                    : asset('frontend/assets/images/no-image.png');
                                                $itemPrice = (float) ($item->price ?? 0);
                                                if ($itemPrice <= 0 && $product) {
                                                    $itemPrice = (float) ($product->price ?? 0);
                                                }
                                                $lineTotal =
                                                    $item->total > 0
                                                        ? $item->total
                                                        : $itemPrice * ($item->quantity ?? 0);
                                            @endphp
                                            <div class="olProductRow">
                                                <img src="{{ $productImg }}" class="olImg"
                                                    alt="{{ $product->name ?? 'Product' }}">
                                                <div class="olProductMeta">
                                                    <div class="bgiProdname mb-1">{{ $product->name ?? 'Product' }}</div>
                                                    <div class="fw-semibold">
                                                        ₹{{ number_format((float) $lineTotal, 2) }}

                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-secondary">No products found.</div>
                                        @endforelse
                                    </div>

                                    <div class="olFooter">
                                        <div class="fw-semibold">
                                            Total: ₹{{ number_format($order->grand_total ?? $order->total, 2) }}
                                            <span class="fw-normal text-secondary">
                                                ({{ $productCount }} product{{ $productCount != 1 ? 's' : '' }})
                                            </span>
                                        </div>
                                        @if ((float) ($order->paid_amount ?? 0) > 0)
                                            <div class="fw-semibold text-success mt-1" style="font-size: 14px;">
                                                Online Paid: ₹{{ number_format($order->paid_amount, 2) }}
                                            </div>
                                        @endif
                                        @if ((float) ($order->due_amount ?? 0) > 0 && strtolower($order->payment_method) === 'cod')
                                            <div class="fw-semibold text-danger mt-1" style="font-size: 14px;">
                                                Remaining COD: ₹{{ number_format($order->due_amount, 2) }}
                                            </div>
                                        @endif
                                        <div class="olBtns d-flex flex-wrap gap-2 mt-2">
                                            <a href="{{ route('frontend.order.details', $order->id) }}"
                                                class="olBtn flex-grow-1 text-center">
                                                View Details
                                            </a>
                                            @if ($canRequestCancel)
                                                <button type="button" class="olBtn flex-grow-1 text-center"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cancelOrderModal{{ $order->id }}">
                                                    Cancel Order
                                                </button>
                                            @endif
                                            @if ($canRequestReturn)
                                                <button type="button" class="olBtn flex-grow-1 text-center"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#returnOrderModal{{ $order->id }}">
                                                    Exchange / Return
                                                </button>
                                            @endif
                                            @if ($isReturnPending)
                                                <form action="{{ route('frontend.orders.cancel_return', $order->id) }}"
                                                    method="POST" class="d-inline flex-grow-1">
                                                    @csrf
                                                    <button type="submit" class="olBtn w-100 text-center"
                                                        onclick="return confirm('Cancel return request?');">
                                                        Cancel Request
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('user.invoice', $order->id) }}"
                                                class="olBtn flex-grow-1 text-center"
                                                download="Invoice_{{ $order->invoice_no ?? $order->order_id }}.pdf">
                                                Invoice PDF
                                            </a>
                                        </div>

                                    </div>

                                    <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1"
                                        aria-labelledby="cancelOrderModalLabel{{ $order->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="cancelOrderModalLabel{{ $order->id }}">
                                                        Cancel Order Request
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('frontend.orders.cancel', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Reason</label>
                                                            <select name="reason" class="form-select" required>
                                                                <option value="">Select reason</option>
                                                                <option value="Ordered by mistake">Ordered by mistake
                                                                </option>
                                                                <option value="Need to change address">Need to change
                                                                    address</option>
                                                                <option value="Found a better price">Found a better price
                                                                </option>
                                                                <option value="Delay in delivery">Delay in delivery
                                                                </option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">Additional
                                                                Information</label>
                                                            <textarea name="note" class="form-control" rows="4" placeholder="Share any details for admin review"></textarea>
                                                        </div>
                                                        @if ($refundTimelineMessage)
                                                            <small
                                                                class="d-block mt-3 text-secondary">{{ $refundTimelineMessage }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light border"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-dark">Submit
                                                            Request</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="returnOrderModal{{ $order->id }}" tabindex="-1"
                                        aria-labelledby="returnOrderModalLabel{{ $order->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="returnOrderModalLabel{{ $order->id }}">Return / Exchange
                                                        Request</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('frontend.orders.return', $order->id) }}"
                                                    method="POST">
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
                                                                <option value="Wrong item received">Wrong item received
                                                                </option>
                                                                <option value="Size issue">Size issue</option>
                                                                <option value="Damaged product">Damaged product</option>
                                                                <option value="Product not as expected">Product not as
                                                                    expected</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold">Additional
                                                                Information</label>
                                                            <textarea name="note" class="form-control" rows="4"
                                                                placeholder="Share return or exchange details for admin review"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light border"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-dark">Submit
                                                            Request</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </li>

                            @empty

                                <li class="rounded-3 p-5 text-center bg-light">
                                    <div class="fs-5 text-secondary">No orders found.</div>
                                </li>
                            @endforelse
                        </ul>

                        @if ($orders->hasPages())
                            <div class="custom-pagination myAccPagination">
                                {{ $orders->onEachSide(1)->links('frontend.partials.pagination') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

    </main>

@endsection