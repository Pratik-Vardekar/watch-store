@extends('layouts.front')

@section('content')

<main class="pageWrapper">         

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-4 py-lg-5">
        <div class="container">
            <div class="row justify-content-between mb-4 mb-lg-5">
                @include('frontend.partials.myaccount_nav', ['active' => 'orders', 'user' => $user])

                <div class="col-lg-9 mb-lg-4 myAccContent">

                    <div class="row align-items-center g-2 mb-3 mb-md-4">
                        <div class="col-12 col-sm">
                            <h2 class="sectTitle lh-1 mb-0 d-inline-flex align-items-center flex-wrap gap-2 gap-sm-3">
                                Orders
                                <small class="bagCount bgOrange text-white rounded-pill py-1 px-2">{{ $orders->total() }} order{{ $orders->total()!=1 ? 's':'' }}</small>
                            </h2>
                        </div>
                        <div class="col-12 col-sm-auto formFormat">
                            <form method="GET" class="myAccFilterForm">
                                <select class="form-select py-0 fw-semibold" name="status" onchange="this.form.submit()">
                                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>All Orders</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="returns" {{ request('status') == 'returns' ? 'selected' : '' }}>Returns</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <ul class="ordersList d-flex flex-column gap-sm-4 gap-2">

                        @forelse($orders as $order)

                        @php
                            $orderItems = $order->items;
                            $productCount = $orderItems->count();
                            $unitCount = (int) $orderItems->sum('quantity');
                            $isCancelPending = $order->cancel_requested_at && !$order->cancel_approved_at && !$order->cancel_rejected_at;
                            $isCancelApproved = !is_null($order->cancel_approved_at);
                            $isCancelRejected = !is_null($order->cancel_rejected_at);
                            $isReturnPending = $order->return_requested_at && !$order->return_approved_at && !$order->return_rejected_at;
                            $isReturnApproved = !is_null($order->return_approved_at);
                            $isReturnRejected = !is_null($order->return_rejected_at);
                            $returnDeadline = $order->returnDeadline();
                            $expectedDeliveryDate = $order->expectedDeliveryDate();
                            $canRequestReturn = $order->canRequestReturn();
                            $canRequestCancel = $order->canRequestCancel();
                            $refundTimelineMessage = $order->payment_method === 'razorpay' && (float) ($order->paid_amount ?? 0) > 0
                                ? 'Refund will be credited to your original payment method within 2-3 working days after admin approval.'
                                : null;
                        @endphp

                        <li class="rounded-3 p-3 olItem">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">

                                <div class="fw-bold">Order ID : #{{ $order->display_order_id }}</div>
                                <div class="text-md-end">

                                    @if($isCancelPending)
                                        <strong class="txtorange">Cancellation Requested</strong>
                                        <small class="d-block">Requested on {{ $order->cancel_requested_at?->format('D, d M Y') }}</small>
                                        <small class="d-block mt-2 olInfo">
                                            Reason: {{ $order->cancel_request_reason ?? '-' }}
                                        </small>
                                        @if($refundTimelineMessage)
                                            <small class="d-block mt-1 olInfo">{{ $refundTimelineMessage }}</small>
                                        @endif
                                    @elseif($isReturnPending)
                                        <strong class="txtorange">{{ ucfirst($order->return_request_type ?? 'Return') }} Requested</strong>
                                        <small class="d-block">Requested on {{ $order->return_requested_at?->format('D, d M Y') }}</small>
                                        <small class="d-block mt-2 olInfo">
                                            Reason: {{ $order->return_reason ?? '-' }}
                                        </small>
                                        @if($order->return_request_note)
                                            <small class="d-block mt-1 olInfo">{{ $order->return_request_note }}</small>
                                        @endif
                                    @elseif($isReturnApproved || $order->order_status == 'returns')
                                        <strong class="txtorange">{{ ucfirst($order->return_request_type ?? 'Return') }} {{ $isReturnApproved ? 'Approved' : 'In Progress' }}</strong>
                                        <small class="d-block">On {{ $order->returned_at ? $order->returned_at->format('D, d M Y') : ($order->return_approved_at ? $order->return_approved_at->format('D, d M Y') : ($order->created_at ? $order->created_at->format('D, d M Y') : '')) }}</small>
                                        <small class="d-block mt-2 olInfo">
                                            Expected Pickup: {{ $order->pickup_date ? $order->pickup_date->format('l, d M Y') : 'Awaiting admin schedule' }}
                                        </small>
                                        @if($order->return_request_type === 'return' && $refundTimelineMessage)
                                            <small class="d-block mt-1 olInfo">{{ $refundTimelineMessage }}</small>
                                        @endif
                                    @elseif($order->order_status == 'pending')
                                        <strong class="txtyellow">Order Placed</strong>
                                        <small class="d-block">On {{ $order->created_at ? $order->created_at->format('D, d M Y') : '' }}</small>
                                        <small class="d-block mt-2 olInfo">
                                            Delivery by: {{ $expectedDeliveryDate ? $expectedDeliveryDate->format('l, d M Y') : '-' }}
                                        </small>
                                    @elseif(strtolower($order->order_status ?? '') == 'delivered')
                                        <strong class="txtgreen">Order Delivered</strong>
                                        <small class="d-block">On {{ $order->delivered_at ? $order->delivered_at->format('D, d M Y') : ($order->created_at ? $order->created_at->format('D, d M Y') : '') }}</small>
                                        <small class="d-block mt-2 olInfo">
                                            Exchange/Return by: {{ $returnDeadline ? $returnDeadline->format('l, d M Y') : '-' }}
                                        </small>
                                    @elseif($order->order_status == 'cancelled')
                                        <strong class="text-danger">Order Cancelled</strong>
                                        <small class="d-block">On {{ $order->cancelled_at ? $order->cancelled_at->format('D, d M Y') : ($order->created_at ? $order->created_at->format('D, d M Y') : '') }}</small>
                                        @if($refundTimelineMessage)
                                            <small class="d-block mt-2 olInfo">{{ $refundTimelineMessage }}</small>
                                        @endif
                                    @else
                                        <strong class="text-muted text-capitalize">{{ ucfirst($order->order_status ?? 'unknown') }}</strong>
                                        <small class="d-block">On {{ $order->created_at ? $order->created_at->format('D, d M Y') : '' }}</small>
                                        @if(!in_array(strtolower($order->order_status ?? ''), ['cancelled', 'delivered', 'returns'], true))
                                            <small class="d-block mt-2 olInfo">
                                                Delivery by: {{ $expectedDeliveryDate ? $expectedDeliveryDate->format('l, d M Y') : '-' }}
                                            </small>
                                        @endif
                                    @endif

                                    @if($isCancelRejected)
                                        <small class="d-block mt-2 text-danger">
                                            Cancellation request was not approved{{ $order->cancel_decision_note ? ': '.$order->cancel_decision_note : '.' }}
                                        </small>
                                    @endif

                                    @if($isReturnRejected)
                                        <small class="d-block mt-2 text-danger">
                                            {{ ucfirst($order->return_request_type ?? 'Return') }} request was not approved{{ $order->return_decision_note ? ': '.$order->return_decision_note : '.' }}
                                        </small>
                                    @endif

                                    @if($isReturnApproved && $order->return_decision_note)
                                        <small class="d-block mt-2 olInfo">
                                            Admin note: {{ $order->return_decision_note }}
                                        </small>
                                    @endif

                                    @if($isCancelApproved && $order->cancel_decision_note)
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
                                        $lineTotal = $item->total ?? (($item->price ?? 0) * ($item->quantity ?? 0));
                                    @endphp
                                    <div class="d-flex flex-wrap align-items-center gap-sm-4 gap-2 olProductRow">
                                        <div class="col-auto">
                                            <img src="{{ $productImg }}" class="olImg" alt="{{ $product->name ?? 'Product' }}">
                                        </div>
                                        <div class="col min-w-0">
                                            <div class="bgiProdname mb-1">{{ $product->name ?? 'Product' }}</div>
                                            <div>
                                                ₹{{ number_format((float) $lineTotal, 2) }}
                                                <span>({{ (int) $item->quantity }} Unit{{ (int) $item->quantity != 1 ? 's' : '' }})</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-secondary">No products found.</div>
                                @endforelse
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                                <div class="fw-semibold">
                                    Total: ₹{{ number_format($order->grand_total ?? $order->total, 2) }}
                                    <span class="fw-normal text-secondary">
                                        ({{ $productCount }} product{{ $productCount != 1 ? 's' : '' }},
                                        {{ $unitCount }} unit{{ $unitCount != 1 ? 's' : '' }})
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap gap-2 olBtns">
                                    <a href="{{ route('frontend.order.details', $order->id) }}" class="olBtn">
                                        View Details
                                    </a>
                                    @if($canRequestCancel)
                                        <button type="button" class="olBtn" data-bs-toggle="modal" data-bs-target="#cancelOrderModal{{ $order->id }}">
                                            Cancel Order
                                        </button>
                                    @endif
                                    @if($canRequestReturn)
                                        <button type="button" class="olBtn" data-bs-toggle="modal" data-bs-target="#returnOrderModal{{ $order->id }}">
                                            Exchange / Return
                                        </button>
                                    @endif
                                    @if($isReturnPending)
                                        <form action="{{ route('frontend.orders.cancel_return', $order->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="olBtn" onclick="return confirm('Cancel return request?');">
                                                Cancel Request
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('user.invoice', $order->id) }}" class="olBtn" download="Invoice_Order_{{ $order->display_order_id }}.pdf">
                                        Invoice PDF
                                    </a>
                                </div>
                            </div>

                            <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="cancelOrderModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cancelOrderModalLabel{{ $order->id }}">Cancel Order Request</h5>
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

                            <div class="modal fade" id="returnOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="returnOrderModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="returnOrderModalLabel{{ $order->id }}">Return / Exchange Request</h5>
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

                        </li>

                        @empty

                        <li class="rounded-3 d-flex flex-wrap align-items-center p-4 text-center flex-column bg-light">
                            <div class="w-100 fs-5 text-secondary py-5">
                                <div>No orders found.</div>
                            </div>
                        </li>

                        @endforelse
                    </ul>

                    <!-- Pagination start -->
                    @if($orders->hasPages())
                    <div class="mt-4 d-flex justify-content-center myAccPagination">
                        <nav>
                            <ul class="pagination justify-content-center align-items-center flex-wrap mb-0">
                                {{-- Previous Page Link --}}
                                @if ($orders->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                    @if ($page == $orders->currentPage())
                                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($orders->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                                    </li>
                                @else
                                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    @endif
                    <!-- Pagination end -->
                </div>
            </div>
        </div>
    </section>

</main>

@endsection
