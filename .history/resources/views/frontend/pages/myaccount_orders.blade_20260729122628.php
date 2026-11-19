@extends('layouts.front')
@section('content')

<main class="pageWrapper">

   <section class="py-5">
      <div class="container">

          <div class="row justify-content-between mb-5">
              @include('frontend.partials.myaccount_nav', ['active' => 'orders', 'user' => $user])

              <div class="col-lg-9 mb-lg-4">

                <div class="row align-items-center">
                   <div class="col-sm mb-4">
                      <div class="d-flex align-items-center">
                         <h1 class="sectTitle mb-0 d-inline-flex align-items-center gap-3">My Orders</h1>
                         <span class="bagCount bgRed text-white rounded-pill p-sm-2 p-1 lh-1 ms-sm-3 ms-2">{{ $orders->total() }}</span>
                      </div>
                   </div>
                   <div class="col-sm-auto mb-4 d-flex">
                      <form method="GET" class="w-100">
                         <select class="form-select py-0 fw-semibold" name="status" onchange="this.form.submit()" style="min-height:2.5rem;">
                            <option value="" {{ request('status') === '' ? 'selected' : '' }}>All Orders</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="returns" {{ request('status') === 'returns' ? 'selected' : '' }}>Returns</option>
                         </select>
                      </form>
                   </div>
                </div>

                <ul class="ordersList d-flex flex-column gap-4">

                   @forelse($orders as $order)
                   @php
                       $firstProduct = $order->items->first()?->product;
                       $productImg = $firstProduct
                           ? $firstProduct->getProductImageUrl($firstProduct->thumbnail)
                           : asset('frontend/assets/images/product_1.jpg');
                       $isCancelPending = $order->cancel_requested_at && ! $order->cancel_approved_at && ! $order->cancel_rejected_at;
                       $isReturnPending = $order->return_requested_at && ! $order->return_approved_at && ! $order->return_rejected_at;
                       $returnDeadline = method_exists($order, 'returnDeadline') ? $order->returnDeadline() : null;
                       $expectedDeliveryDate = method_exists($order, 'expectedDeliveryDate') ? $order->expectedDeliveryDate() : null;
                       $canRequestReturn = method_exists($order, 'canRequestReturn') ? $order->canRequestReturn() : false;
                       $canRequestCancel = method_exists($order, 'canRequestCancel') ? $order->canRequestCancel() : false;
                       $detailsUrl = route('product.details', ['slug' => $firstProduct?->slug ?? '']);
                   @endphp
                   <li class="rounded-3 d-flex align-items-stretch gap-sm-4 gap-3 p-sm-3 p-2 olItem">
                      <div class="col-auto">
                         <img src="{{ $productImg }}" class="olImg" alt="{{ $firstProduct?->name ?? 'Product' }}">
                      </div>
                      <div class="col d-flex flex-wrap align-items-stretch olContent">
                         <div class="col-md-6 col-12 pe-md-3 mb-2 mb-md-0 d-flex flex-column justify-content-between gap-3">
                            <div class="fw-bolder">Order ID : #{{ $order->order_id ?? $order->id }}</div>
                            <div>
                               @if($firstProduct)
                               <a href="{{ $detailsUrl }}" class="bgiProdname d-block">
                                  <strong class="d-block text-uppercase">{{ optional($firstProduct->brand)->name ?? 'Watch' }}</strong>
                                  <div class="prodName">{{ $firstProduct->name }}</div>
                               </a>
                               @else
                               <div class="prodName">Product unavailable</div>
                               @endif
                            </div>
                            <div>₹{{ number_format($order->grand_total ?? 0, 2) }}</div>
                         </div>
                         <div class="col-md-6 col-12 d-flex flex-column justify-content-between gap-3">
                            <div>
                               @if($isCancelPending)
                                  <strong class="txtRed">Cancellation Requested</strong>
                                  <small class="d-block">On {{ $order->cancel_requested_at?->format('D, d M Y') }}</small>
                               @elseif($isReturnPending)
                                  <strong class="txtRed">{{ ucfirst($order->return_request_type ?? 'Return') }} Requested</strong>
                                  <small class="d-block">On {{ $order->return_requested_at?->format('D, d M Y') }}</small>
                               @elseif(strtolower($order->order_status ?? '') === 'delivered')
                                  <strong class="txtRed">Order Delivered</strong>
                                  <small class="d-block">On {{ $order->delivered_at?->format('D, d M Y') ?? $order->updated_at?->format('D, d M Y') }}</small>
                               @elseif($order->order_status === 'cancelled')
                                  <strong class="txtRed">Order Cancelled</strong>
                                  <small class="d-block">On {{ $order->cancelled_at?->format('D, d M Y') ?? $order->updated_at?->format('D, d M Y') }}</small>
                               @else
                                  <strong class="txtRed">Order Placed</strong>
                                  <small class="d-block">On {{ $order->created_at?->format('D, d M Y') }}</small>
                               @endif
                            </div>
                            <div>
                               @if(strtolower($order->order_status ?? '') === 'delivered' && $returnDeadline)
                                  <small class="d-block olInfo">Exchange/Return by : <span class="d-inline-block">{{ $returnDeadline->format('l, d M Y') }}</span></small>
                               @elseif($expectedDeliveryDate && ! in_array(strtolower($order->order_status ?? ''), ['cancelled', 'delivered'], true))
                                  <small class="d-block olInfo">Delivery by : <span class="d-inline-block">{{ $expectedDeliveryDate->format('l, d M Y') }}</span></small>
                               @endif
                            </div>
                            <div class="col-12 d-flex flex-wrap gap-sm-3 gap-2 olBtns">
                               <a href="{{ route('frontend.order.details', $order->id) }}" class="olBtn rounded">View Details</a>
                               @if($canRequestCancel)
                                  <button type="button" class="olBtn rounded dark" data-bs-toggle="modal" data-bs-target="#cancelOrderModal{{ $order->id }}">Cancel Order</button>
                               @endif
                               @if($canRequestReturn)
                                  <button type="button" class="olBtn rounded dark" data-bs-toggle="modal" data-bs-target="#returnOrderModal{{ $order->id }}">Exchange/Return</button>
                               @endif
                            </div>
                         </div>
                      </div>
                   </li>

                   <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                         <div class="modal-content">
                            <div class="modal-header">
                               <h5 class="modal-title">Cancel Order Request</h5>
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
                                        <option value="Other">Other</option>
                                     </select>
                                  </div>
                                  <div class="mb-0">
                                     <label class="form-label fw-semibold">Additional Information</label>
                                     <textarea name="note" class="form-control" rows="3"></textarea>
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

                   <div class="modal fade" id="returnOrderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                         <div class="modal-content">
                            <div class="modal-header">
                               <h5 class="modal-title">Return / Exchange Request</h5>
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
                                        <option value="Other">Other</option>
                                     </select>
                                  </div>
                                  <div class="mb-0">
                                     <label class="form-label fw-semibold">Additional Information</label>
                                     <textarea name="note" class="form-control" rows="3"></textarea>
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
                   @empty
                   <li class="rounded-3 p-4 text-center bg-light">
                      <div class="text-secondary py-4">No orders found.</div>
                   </li>
                   @endforelse

                </ul>

                @if($orders->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                   {{ $orders->links() }}
                </div>
                @endif

               </div>
          </div>

      </div>
   </section>

</main>

@endsection
