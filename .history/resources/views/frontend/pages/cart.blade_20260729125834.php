@extends('layouts.front')

@section('content')
<main class="pageWrapper">

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="py-5">
        <div class="container shopbagContainer">


            <div class="row justify-content-between">

                <div class="col-12">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-sm order-sm-1 mb-4">
                            <h1 class="sectTitle lh-1 mb-0 d-inline-flex align-items-center gap-3">
                                Your Cart
                                <small class="bagCount bgOrange text-white rounded-pill py-1 px-2">
                                    {{ $itemCount }} {{ $itemCount === 1 ? 'item' : 'items' }}
                                </small>
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7 col-lg-8 mb-5">

                    <ul class="bagListWrap mb-4 d-flex flex-column gap-3">
                        @forelse($cartItems as $cartItem)
                            @php
                                $product = $cartItem->product;
                                if (!$product) {
                                    continue;
                                }

                                $sellingPrice = $product->selling_price ?? $product->price ?? 0;
                                $originalPrice = $product->price ?? 0;
                                $discount = $product->discount ?? 0;

                                if (!$discount && $originalPrice > $sellingPrice && $originalPrice > 0) {
                                    $discount = round((($originalPrice - $sellingPrice) / $originalPrice) * 100);
                                }

                                $lineTotal = $sellingPrice * $cartItem->quantity;
                                $image = $product->getProductImageUrl($product->thumbnail);
                            @endphp

                            <li class="bagItem rounded-3 p-sm-3 p-2 d-flex gap-xl-4 gap-3">
                                <div class="col-auto flex-shrink-1">
                                    <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                        <img src="{{ $image }}" class="bgiImg" alt="{{ $product->name }}">
                                    </a>
                                </div>
                                <div class="col">
                                    <div class="row h-100 flex-column">
                                        <div class="col-12 mb-md-3 mb-2 d-flex align-items-center justify-content-between">
                                            <div class="col">
                                                <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="bgiProdname mb-0">{{ $product->name }}</a>
                                            </div>
                                            <div class="col-auto">
                                                @if(!empty($cartItem->is_guest))
                                                    <form action="{{ route('cart.guest.remove', $cartItem->product_id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn p-0 bgiDeleteBtn">&times;</button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn p-0 bgiDeleteBtn">&times;</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 mb-auto d-flex flex-wrap align-items-center justify-content-between">
                                            @php
                                                $maxQty = min(10, max(1, (int) $product->quantity));
                                                $qtyValue = min(max(1, (int) $cartItem->quantity), $maxQty);
                                            @endphp
                                            <div class="col-sm-5 col-6">
                                                <small class="d-block">Quantity</small>
                                                @if(!empty($cartItem->is_guest))
                                                    <form action="{{ route('cart.guest.update', $cartItem->product_id) }}" method="POST" class="cart-qty-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="quantitySpinner input-group border rounded d-inline-flex">
                                                            <button class="btn border-0 minus" type="button" {{ $qtyValue <= 1 ? 'disabled' : '' }}>-</button>
                                                            <input type="text" name="quantity" value="{{ $qtyValue }}" min="1" max="{{ $maxQty }}" class="form-control border-0 text-center numData" readonly>
                                                            <button class="btn border-0 plus" type="button" {{ $qtyValue >= $maxQty ? 'disabled' : '' }}>+</button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <form action="{{ route('cart.update', $cartItem->id) }}" method="POST" class="cart-qty-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="quantitySpinner input-group border rounded d-inline-flex">
                                                            <button class="btn border-0 minus" type="button" {{ $qtyValue <= 1 ? 'disabled' : '' }}>-</button>
                                                            <input type="text" name="quantity" value="{{ $qtyValue }}" min="1" max="{{ $maxQty }}" class="form-control border-0 text-center numData" readonly>
                                                            <button class="btn border-0 plus" type="button" {{ $qtyValue >= $maxQty ? 'disabled' : '' }}>+</button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                            <div class="col-sm-7 col-6 d-flex flex-wrap justify-content-between">
                                                <div class="col-sm-auto col-12 pt-3 d-flex align-items-center gap-sm-3 gap-2">
                                                    @if($originalPrice > $sellingPrice)
                                                        <span class="pibStikePrice">₹{{ number_format($originalPrice, 2) }}</span>
                                                    @endif
                                                    @if($discount > 0)
                                                        <span class="txtgreen fw-semibold">{{ $discount }}% Off</span>
                                                    @endif
                                                </div>
                                                <div class="col-sm-auto col-12 pt-sm-3 d-flex align-items-center gap-3">
                                                    <strong class="bgiProdPrice">₹{{ number_format($lineTotal, 2) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 pt-4 bgiDelivery">
                                            @if($product->quantity > 0)
                                                In stock — {{ $product->quantity }} available
                                            @else
                                                Currently out of stock
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="bagItem rounded-3 p-sm-4 p-3 text-center">
                                <h5 class="mb-2">Your cart is empty</h5>
                                <p class="text-secondary mb-0">Add products to your cart to see them here.</p>
                            </li>
                        @endforelse
                    </ul>

                    <div class="text-center">
                        <small class="d-block mb-2 text-secondary">Want to check some other toys, games and more?</small>
                        <a href="{{ route('home') }}" class="ctaBtn">
                            <span class="btnTxt">Continue Shopping</span>
                            <span class="arw"></span>
                        </a>
                    </div>

                </div>

                <div class="col-lg-4 mb-lg-5">

                    <div class="summaryWrap rounded-4 overflow-hidden">
                        <h3 class="px-4 py-3 swTitle bgYellowSub txtwhite">Order Summary</h3>

                        <div class="px-4 py-3">

                            <div class="d-flex w-100 mb-2 swItem">
                                <div class="fw-medium">Subtotal</div>
                                <div class="ms-auto fw-semibold"><span class="price">₹{{ number_format($subtotal, 2) }}</span></div>
                            </div>

                            <div class="d-flex w-100 mb-2 swItem">
                                <div class="fw-medium">Shipping Charges</div>
                                <div class="ms-auto fw-semibold">
                                    @if($shippingCharge <= 0)
                                        <span class="txtorange">Free</span>
                                    @else
                                        <span class="price">₹{{ number_format($shippingCharge, 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="swPromoBox mb-2 py-3">
                                <div class="swPromoForm">
                                    <div class="fw-medium d-flex flex-nowrap align-items-center swPromoHead" role="button" data-bs-toggle="collapse" data-bs-target="#promoCart" aria-expanded="{{ !empty($couponCode) ? 'true' : 'false' }}">
                                        <svg class="me-2 promoIcon" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><circle cx="256.001" cy="346.888" r="17.133"></circle><circle cx="256.001" cy="165.11" r="17.133"></circle><path d="m498.198 13.815c-8.897-8.909-20.734-13.815-33.331-13.815h-179.488c-12.591 0-24.429 4.904-33.333 13.809l-238.235 238.235c-8.907 8.906-13.811 20.743-13.811 33.331 0 12.59 4.906 24.427 13.811 33.326l179.484 179.485c8.902 8.908 20.739 13.814 33.329 13.814 12.589 0 24.426-4.904 33.332-13.811l238.235-238.235c8.905-8.904 13.809-20.742 13.809-33.333v-179.488c0-12.597-4.906-24.435-13.802-33.318zm-242.197 104.162c25.989 0 47.133 21.144 47.133 47.133s-21.144 47.133-47.133 47.133-47.133-21.144-47.133-47.133 21.144-47.133 47.133-47.133zm0 276.044c-25.989 0-47.133-21.144-47.133-47.133s21.144-47.133 47.133-47.133 47.133 21.144 47.133 47.133-21.144 47.133-47.133 47.133zm96.399-123.022h-192.798c-8.284 0-15-6.716-15-15s6.716-15 15-15h192.798c8.284 0 15 6.716 15 15s-6.715 15-15 15zm64.267-160.666c-8.28 0-15-6.72-15-15s6.72-15 15-15 15 6.72 15 15-6.72 15-15 15z"></path></svg>
                                        <span>Have a Promo Code?</span>
                                    </div>
                                    <div class="collapse {{ !empty($couponCode) ? 'show' : '' }}" id="promoCart">
                                        @if(!empty($couponCode))
                                            <div class="d-flex align-items-center justify-content-between mt-2 p-2 rounded-pill bg-white border">
                                                <div class="ps-2">
                                                    <strong class="txtorange">{{ $couponCode }}</strong>
                                                    <small class="d-block">
                                                        @if($coupon && $coupon->type === 'percent')
                                                            <span class="txtorange">{{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}% off</span>
                                                        @elseif($coupon)
                                                            <span class="txtorange">₹{{ number_format($coupon->value, 2) }} off</span>
                                                        @endif
                                                    </small>
                                                </div>
                                                <form action="{{ route('cart.coupon.remove') }}" method="POST" class="me-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm rounded-pill">Remove</button>
                                                </form>
                                            </div>
                                       
                                        @else
                                            <form action="{{ route('cart.coupon.apply') }}" method="POST" class="input-group mt-2 p-1 rounded-pill bg-white border overflow-hidden">
                                                @csrf
                                                <input type="text" name="coupon_code" class="form-control border-0" placeholder="Enter Promo Code" value="{{ old('coupon_code') }}" required />
                                                <button type="submit" class="promoApplyBtn btn rounded-pill">Apply</button>
                                            </form>
                                        @endif
                                    </div>
                                    @if(($availableCoupons ?? collect())->isNotEmpty())
                                        <button type="button"
                                                class="btn btn-link p-0 mt-2 txtorange text-decoration-none fw-semibold"
                                                data-bs-toggle="modal"
                                                data-bs-target="#couponModal">
                                            View {{ $availableCoupons->count() }} available coupon{{ $availableCoupons->count() === 1 ? '' : 's' }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if(($discount ?? 0) > 0)
                            <div class="d-flex w-100 mb-2 swItem txtorange">
                                <div class="fw-medium txtorange">Discount {{ !empty($couponCode) ? '(' . $couponCode . ')' : '' }}</div>
                                <div class="ms-auto fw-semibold txtorange">-₹{{ number_format($discount, 2) }}</div>
                            </div>
                            @endif

                            <div class="d-flex w-100 mb-2 swItem">
                                <div class="fw-medium">{{ $gstRateLabel ?? 'GST (inclusive)' }}</div>
                                <div class="ms-auto fw-semibold"><span class="price">₹{{ number_format($gstTotal, 2) }}</span></div>
                            </div>

                            <div class="d-flex w-100 mb-2 swItem">
                                <div class="fw-bold">Total</div>
                                <div class="ms-auto fw-bold"><span class="price">₹{{ number_format($grandTotal, 2) }}</span></div>
                            </div>

                            <div class="w-100 mt-5 d-flex justify-content-center">
                                @if($itemCount > 0)
                                    <a href="{{ route('checkout.shipping') }}" class="ctaBtn cartBtn js-place-order">
                                        <span class="btnTxt">Proceed to Checkout</span>
                                        <span class="arw"></span>
                                    </a>
                                @else
                                    <button type="button" class="ctaBtn cartBtn" disabled>
                                        <span class="btnTxt">Proceed to Checkout</span>
                                        <span class="arw"></span>
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

        <div class="d-lg-none animateThis">
            <div class="mobSummaryItem py-2 w-100">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col">
                            <div type="button" class="gotoTarget" data-bs-target=".summaryWrap">
                                <div class="fw-semibold txtred">Check Order Summary
                                    <svg xmlns="http://www.w3.org/2000/svg" width=".7rem" height=".7rem" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/></svg>
                                </div>
                                <div class="fw-bold fs-5">Total : <span class="price">₹{{ number_format($grandTotal, 2) }}</span></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            @if($itemCount > 0)
                                <a href="{{ route('checkout.shipping') }}" class="ctaBtn cartBtn js-place-order">
                                    <span class="btnTxt">Place Order</span>
                                    <span class="arw"></span>
                                </a>
                            @else
                                <button type="button" class="ctaBtn cartBtn" disabled>
                                    <span class="btnTxt">Place Order</span>
                                    <span class="arw"></span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="couponModalLabel">Available Coupons</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-secondary small mb-3">
                            Coupons are calculated on selling price. Minimum order and maximum discount rules are checked against your cart.
                        </p>

                        @forelse(($availableCoupons ?? collect()) as $offer)
                            @php
                                $offerCoupon = $offer['coupon'];
                                $isApplied = !empty($couponCode) && strtoupper($couponCode) === strtoupper($offerCoupon->code);
                            @endphp

                            <div class="border rounded-3 p-3 mb-3 {{ $offer['can_apply'] ? '' : 'opacity-75' }}">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                                    <div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <strong class="fs-5">{{ $offerCoupon->code }}</strong>
                                            @if($offerCoupon->type === 'percent')
                                                <span class="badge text-bg-orange">
                                                    {{ rtrim(rtrim(number_format($offerCoupon->value, 2), '0'), '.') }}% OFF
                                                </span>
                                            @else
                                                <span class="badge text-bg-orange">
                                                    ₹{{ number_format($offerCoupon->value, 2) }} OFF
                                                </span>
                                            @endif
                                            @if($isApplied)
                                                <span class="badge text-bg-orange">Applied</span>
                                            @endif
                                        </div>
                                        <small class="text-secondary d-block">
                                            {{ $offer['applicability']['label'] ?? 'Valid on selected items' }}
                                        </small>
                                    </div>

                                    <div class="text-end">
                                        @if($offer['can_apply'])
                                            <div class="txtorange fw-semibold mb-1">
                                                You save ₹{{ number_format($offer['discount'], 2) }}
                                            </div>
                                        @elseif(!empty($offer['message']))
                                            <div class="text-danger small mb-1">{{ $offer['message'] }}</div>
                                        @endif

                                        @if($isApplied)
                                            <button type="button" class="btn btn-sm btn-purple rounded-pill px-3" disabled>Applied</button>
                                        @elseif($offer['can_apply'])
                                            <form action="{{ route('cart.coupon.apply') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="coupon_code" value="{{ $offerCoupon->code }}">
                                                <button type="submit" class="btn btn-sm btn-purple rounded-pill px-3">Apply</button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-purple rounded-pill px-3" disabled>Not applicable</button>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-3 small mb-3">
                                    <div>
                                        <span class="text-secondary">Min. order:</span>
                                        @if($offer['minimum_amount'])
                                            <strong>₹{{ number_format($offer['minimum_amount'], 2) }}</strong>
                                            @if($offer['meets_minimum'])
                                                <span class="txtgreen">(met)</span>
                                            @else
                                                <span class="text-danger">(need ₹{{ number_format(max(0, $offer['minimum_amount'] - $subtotal), 2) }} more)</span>
                                            @endif
                                        @else
                                            <strong>None</strong>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-secondary">Max. discount:</span>
                                        @if($offer['maximum_discount'])
                                            <strong>₹{{ number_format($offer['maximum_discount'], 2) }}</strong>
                                        @else
                                            <strong>No cap</strong>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-secondary">Eligible on selling price:</span>
                                        <strong>₹{{ number_format($offer['eligible_subtotal'], 2) }}</strong>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr class="text-secondary small">
                                                <th>Product</th>
                                                <th class="text-end">MRP</th>
                                                <th class="text-end">Selling</th>
                                                <th class="text-end">Product save</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($offer['eligible_items'] as $item)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $item['name'] }}</strong>
                                                        <small class="d-block text-secondary">Qty {{ $item['quantity'] }}</small>
                                                    </td>
                                                    <td class="text-end">
                                                        @if($item['price'] > $item['selling_price'])
                                                            <del class="text-secondary">₹{{ number_format($item['line_mrp'], 2) }}</del>
                                                        @else
                                                            ₹{{ number_format($item['line_mrp'], 2) }}
                                                        @endif
                                                    </td>
                                                    <td class="text-end fw-semibold">₹{{ number_format($item['line_selling'], 2) }}</td>
                                                    <td class="text-end txtgreen">
                                                        @if($item['product_save'] > 0)
                                                            ₹{{ number_format($item['product_save'], 2) }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-secondary">
                                No coupons available for the products in your cart.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.quantitySpinner').forEach(function (spinner) {
        const form = spinner.closest('.cart-qty-form');
        const minusButton = spinner.querySelector('.minus');
        const plusButton = spinner.querySelector('.plus');
        const input = spinner.querySelector('.numData');

        if (!minusButton || !plusButton || !input) {
            return;
        }

        const minQty = 1;
        const maxQty = Math.min(10, parseInt(input.getAttribute('max') || '10', 10) || 10);

        const syncButtons = function (value) {
            minusButton.disabled = value <= minQty;
            plusButton.disabled = value >= maxQty;
        };

        const submitForm = function () {
            if (form) {
                form.submit();
            }
        };

        syncButtons(parseInt(input.value || '1', 10));

        minusButton.addEventListener('click', function () {
            let currentValue = parseInt(input.value || '1', 10);
            if (currentValue > minQty) {
                currentValue -= 1;
                input.value = currentValue;
                syncButtons(currentValue);
                submitForm();
            }
        });

        plusButton.addEventListener('click', function () {
            let currentValue = parseInt(input.value || '1', 10);
            if (currentValue < maxQty) {
                currentValue += 1;
                input.value = currentValue;
                syncButtons(currentValue);
                submitForm();
            }
        });
    });
});
</script>
@endpush
