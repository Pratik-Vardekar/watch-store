@extends('layouts.front')

@section('content')
<main class="pageWrapper" style="font-family: inherit; font-weight: normal;">

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="py-5">
        <div class="container shopbagContainer">


            <div class="row justify-content-between">

                <div class="col-12">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-sm order-sm-1 mb-4">
                            <h1 class="sectTitle lh-1 mb-0 d-inline-flex align-items-center gap-3" style="font-weight: normal;">
                                Your Cart
                                <small class="bagCount bgOrange text-white rounded-pill py-1 px-2" style="font-weight: normal;">
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

                            <li class="bagItem rounded-3 p-sm-3 p-2 d-flex gap-xl-4 gap-3" style="font-weight: normal;">
                                <div class="col-auto flex-shrink-1">
                                    <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                        <img src="{{ $image }}" class="bgiImg" alt="{{ $product->name }}">
                                    </a>
                                </div>
                                <div class="col">
                                    <div class="row h-100 flex-column">
                                        <div class="col-12 mb-md-3 mb-2 d-flex align-items-center justify-content-between">
                                            <div class="col">
                                                <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="bgiProdname mb-0" style="font-weight: normal;">{{ $product->name }}</a>
                                            </div>
                                            <div class="col-auto">
                                                @if(!empty($cartItem->is_guest))
                                                    <form action="{{ route('cart.guest.remove', $cartItem->product_id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn p-0 bgiDeleteBtn" style="font-weight: normal;">&times;</button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn p-0 bgiDeleteBtn" style="font-weight: normal;">&times;</button>
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
                                                <small class="d-block" style="font-weight: normal;">Quantity</small>
                                                @if(!empty($cartItem->is_guest))
                                                    <form action="{{ route('cart.guest.update', $cartItem->product_id) }}" method="POST" class="cart-qty-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="quantitySpinner input-group border rounded d-inline-flex">
                                                            <button class="btn border-0 minus" type="button" style="font-weight: normal;" {{ $qtyValue <= 1 ? 'disabled' : '' }}>-</button>
                                                            <input type="text" name="quantity" value="{{ $qtyValue }}" min="1" max="{{ $maxQty }}" class="form-control border-0 text-center numData" style="font-weight: normal;" readonly>
                                                            <button class="btn border-0 plus" type="button" style="font-weight: normal;" {{ $qtyValue >= $maxQty ? 'disabled' : '' }}>+</button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <form action="{{ route('cart.update', $cartItem->id) }}" method="POST" class="cart-qty-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="quantitySpinner input-group border rounded d-inline-flex">
                                                            <button class="btn border-0 minus" type="button" style="font-weight: normal;" {{ $qtyValue <= 1 ? 'disabled' : '' }}>-</button>
                                                            <input type="text" name="quantity" value="{{ $qtyValue }}" min="1" max="{{ $maxQty }}" class="form-control border-0 text-center numData" style="font-weight: normal;" readonly>
                                                            <button class="btn border-0 plus" type="button" style="font-weight: normal;" {{ $qtyValue >= $maxQty ? 'disabled' : '' }}>+</button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                            <div class="col-sm-7 col-6 d-flex flex-wrap justify-content-between">
                                                <div class="col-sm-auto col-12 pt-3 d-flex align-items-center gap-sm-3 gap-2">
                                                    @if($originalPrice > $sellingPrice)
                                                        <span class="pibStikePrice" style="font-weight: normal;">₹{{ number_format($originalPrice, 2) }}</span>
                                                    @endif
                                                    @if($discount > 0)
                                                        <span class="txtgreen fw-semibold" style="font-weight: normal;">{{ $discount }}% Off</span>
                                                    @endif
                                                </div>
                                                <div class="col-sm-auto col-12 pt-sm-3 d-flex align-items-center gap-3">
                                                    <strong class="bgiProdPrice" style="font-weight: normal;">₹{{ number_format($lineTotal, 2) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-12 pt-4 bgiDelivery">
                                            @if($product->quantity > 0)
                                                In stock — {{ $product->quantity }} available
                                            @else
                                                Currently out of stock
                                            @endif
                                        </div> --}}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="bagItem rounded-3 p-sm-4 p-3 text-center" style="font-weight: normal;">
                                <h5 class="mb-2" style="font-weight: normal;">Your cart is empty</h5>
                                <p class="text-secondary mb-0" style="font-weight: normal;">Add products to your cart to see them here.</p>
                            </li>
                        @endforelse
                    </ul>

                    <div class="text-center" style="font-weight: normal;">
                        <small class="d-block mb-2 text-secondary" style="font-weight: normal;">Want to check some other toys, games and more?</small>
                        <a href="{{ route('home') }}" class="ctaBtn" style="font-weight: normal;">
                            <span class="btnTxt" style="font-weight: normal;">Continue Shopping</span>
                            <span class="arw"></span>
                        </a>
                    </div>

                </div>

                <div class="col-lg-4 mb-lg-5">

                    <div class="summaryWrap rounded-4 overflow-hidden" style="font-weight: normal;">
                        <h3 class="px-4 py-3 swTitle bgYellowSub txtwhite text-white" style="font-weight: normal;">Order Summary</h3>

                        <div class="px-4 py-3">

                            <div class="d-flex w-100 mb-2 swItem" style="font-weight: normal;">
                                <div class="fw-medium" style="font-weight: normal;">Subtotal</div>
                                <div class="ms-auto fw-semibold" style="font-weight: normal;"><span class="price" style="font-weight: normal;">₹{{ number_format($subtotal, 2) }}</span></div>
                            </div>

                            <div class="d-flex w-100 mb-2 swItem" style="font-weight: normal;">
                                <div class="fw-medium" style="font-weight: normal;">Shipping Charges</div>
                                <div class="ms-auto fw-semibold" style="font-weight: normal;">
                                    @if($shippingCharge <= 0)
                                        <span class="txtorange" style="font-weight: normal;">Free</span>
                                    @else
                                        <span class="price" style="font-weight: normal;">₹{{ number_format($shippingCharge, 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="swPromoBox mb-2 py-3" style="font-weight: normal;">
                                <div class="swPromoForm" style="font-weight: normal;">
                                    <div class="fw-medium d-flex flex-nowrap align-items-center swPromoHead" style="font-weight: normal;" role="button" data-bs-toggle="collapse" data-bs-target="#promoCart" aria-expanded="{{ !empty($couponCode) ? 'true' : 'false' }}">
                                        <svg class="me-2 promoIcon" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><circle cx="256.001" cy="346.888" r="17.133"></circle><circle cx="256.001" cy="165.11" r="17.133"></circle><path d="m498.198 13.815c-8.897-8.909-20.734-13.815-33.331-13.815h-179.488c-12.591 0-24.429 4.904-33.333 13.809l-238.235 238.235c-8.907 8.906-13.811 20.743-13.811 33.331 0 12.59 4.906 24.427 13.811 33.326l179.484 179.485c8.902 8.908 20.739 13.814 33.329 13.814 12.589 0 24.426-4.904 33.332-13.811l238.235-238.235c8.905-8.904 13.809-20.742 13.809-33.333v-179.488c0-12.597-4.906-24.435-13.802-33.318zm-242.197 104.162c25.989 0 47.133 21.144 47.133 47.133s-21.144 47.133-47.133 47.133-47.133-21.144-47.133-47.133 21.144-47.133 47.133-47.133zm0 276.044c-25.989 0-47.133-21.144-47.133-47.133s21.144-47.133 47.133-47.133 47.133 21.144 47.133 47.133-21.144 47.133-47.133 47.133zm96.399-123.022h-192.798c-8.284 0-15-6.716-15-15s6.716-15 15-15h192.798c8.284 0 15 6.716 15 15s-6.715 15-15 15zm64.267-160.666c-8.28 0-15-6.72-15-15s6.72-15 15-15 15 6.72 15 15-6.72 15-15 15z"></path></svg>
                                        <span style="font-weight: normal;">Have a Promo Code?</span>
                                    </div>
                                    <div class="collapse {{ !empty($couponCode) ? 'show' : '' }}" id="promoCart">
                                        <div id="promoAppliedBox" class="{{ !empty($couponCode) ? '' : 'd-none' }}">
                                            <div class="d-flex align-items-center justify-content-between mt-2 p-2 rounded-pill bg-white border" style="font-weight: normal;">
                                                <div class="ps-2">
                                                    <strong id="appliedCouponCode" class="txtorange" style="font-weight: normal;">{{ $couponCode }}</strong>
                                                    <small class="d-block" id="appliedCouponValue" style="font-weight: normal;">
                                                        @if($coupon && $coupon->type === 'percent')
                                                            <span class="txtorange" style="font-weight: normal;">{{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}% off</span>
                                                        @elseif($coupon)
                                                            <span class="txtorange" style="font-weight: normal;">₹{{ number_format($coupon->value, 2) }} off</span>
                                                        @endif
                                                    </small>
                                                </div>
                                                <button type="button" class="btn btn-sm rounded-pill" id="removePromoBtn" style="font-weight: normal;">Remove</button>
                                            </div>
                                        </div>
                                        <form id="promoApplyForm" class="input-group mt-2 p-1 rounded-pill bg-white border overflow-hidden {{ !empty($couponCode) ? 'd-none' : '' }}">
                                            @csrf
                                            <input type="text" name="coupon_code" class="form-control border-0" placeholder="Enter Promo Code" value="{{ old('coupon_code') }}" required style="font-weight: normal;" />
                                            <button type="submit" class="promoApplyBtn btn rounded-pill" style="font-weight: normal;">Apply</button>
                                        </form>
                                        <div class="small mt-2 text-danger d-none" id="promoError" style="font-weight: normal;"></div>
                                        <div class="small mt-2 text-success d-none" id="promoSuccess" style="font-weight: normal;"></div>
                                    </div>
                                    @if(($availableCoupons ?? collect())->isNotEmpty())
                                        <button type="button"
                                                class="btn btn-link p-0 mt-2 txtorange text-decoration-none fw-semibold"
                                                style="font-weight: normal;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#couponModal">
                                            View {{ $availableCoupons->count() }} available coupon{{ $availableCoupons->count() === 1 ? '' : 's' }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div id="discountRow" class="d-flex w-100 mb-2 swItem txtorange {{ ($discount ?? 0) > 0 ? '' : 'd-none' }}" style="font-weight: normal;">
                                <div class="fw-medium txtorange" style="font-weight: normal;">Discount <span id="discountCodeText" style="font-weight: normal;">{{ !empty($couponCode) ? '(' . $couponCode . ')' : '' }}</span></div>
                                <div class="ms-auto fw-semibold txtorange" id="discountAmountText" style="font-weight: normal;">-₹{{ number_format($discount ?? 0, 2) }}</div>
                            </div>

                            <div class="d-flex w-100 mb-2 swItem" style="font-weight: normal;">
                                <div class="fw-medium" id="gstLabel" style="font-weight: normal;">{{ $gstRateLabel ?? 'GST (inclusive)' }}</div>
                                <div class="ms-auto fw-semibold" style="font-weight: normal;"><span class="price" id="gstAmount" style="font-weight: normal;">₹{{ number_format($gstTotal, 2) }}</span></div>
                            </div>

                            <div class="d-flex w-100 mb-2 swItem" style="font-weight: normal;">
                                <div class="fw-bold" style="font-weight: normal;">Total</div>
                                <div class="ms-auto fw-bold" style="font-weight: normal;"><span class="price" id="grandTotalAmount" style="font-weight: normal;">₹{{ number_format($grandTotal, 2) }}</span></div>
                            </div>

                            <div class="w-100 mt-5 d-flex justify-content-center" style="font-weight: normal;">
                                @if($itemCount > 0)
                                    <a href="{{ route('checkout.shipping', ['from' => 'cart']) }}" class="ctaBtn cartBtn js-place-order" style="font-weight: normal;">
                                        <span class="btnTxt" style="font-weight: normal;">Proceed to Checkout</span>
                                        <span class="arw"></span>
                                    </a>
                                @else
                                    <button type="button" class="ctaBtn cartBtn" disabled style="font-weight: normal;">
                                        <span class="btnTxt" style="font-weight: normal;">Proceed to Checkout</span>
                                        <span class="arw"></span>
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

        <div class="d-lg-none animateThis" style="font-weight: normal;">
            <div class="mobSummaryItem py-2 w-100" style="font-weight: normal;">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col">
                            <div type="button" class="gotoTarget" data-bs-target=".summaryWrap" style="font-weight: normal;">
                                <div class="fw-semibold txtred" style="font-weight: normal;">Check Order Summary
                                    <svg xmlns="http://www.w3.org/2000/svg" width=".7rem" height=".7rem" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/></svg>
                                </div>
                                <div class="fw-bold fs-5" style="font-weight: normal;">Total : <span class="price" id="mobileGrandTotal" style="font-weight: normal;">₹{{ number_format($grandTotal, 2) }}</span></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            @if($itemCount > 0)
                                <a href="{{ route('checkout.shipping', ['from' => 'cart']) }}" class="ctaBtn cartBtn js-place-order" style="font-weight: normal;">
                                    <span class="btnTxt" style="font-weight: normal;">Place Order</span>
                                    <span class="arw"></span>
                                </a>
                            @else
                                <button type="button" class="ctaBtn cartBtn" disabled style="font-weight: normal;">
                                    <span class="btnTxt" style="font-weight: normal;">Place Order</span>
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
                <div class="modal-content" style="font-weight: normal;">
                    <div class="modal-header" style="font-weight: normal;">
                        <h5 class="modal-title" style="color: #d00d0d; font-weight: normal;" id="couponModalLabel">Available Coupons</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="couponModalBody" style="font-weight: normal;">
                        <p class="text-secondary small mb-3" style="color: #090909; font-weight: normal;">
                            Coupons are calculated on selling price. Minimum order and maximum discount rules are checked against your cart.
                        </p>
                        <div id="couponList" style="font-weight: normal;">
                            @include('frontend.partials.available-coupons-list', [
                                'availableCoupons' => $availableCoupons ?? collect(),
                                'couponCode' => $couponCode ?? null,
                                'subtotal' => $subtotal ?? 0,
                            ])
                        </div>
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
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    var promoApplyForm = document.getElementById('promoApplyForm');
    var promoAppliedBox = document.getElementById('promoAppliedBox');
    var promoError = document.getElementById('promoError');
    var promoSuccess = document.getElementById('promoSuccess');
    var removePromoBtn = document.getElementById('removePromoBtn');
    var discountRow = document.getElementById('discountRow');
    var discountAmountText = document.getElementById('discountAmountText');
    var discountCodeText = document.getElementById('discountCodeText');
    var gstAmountEl = document.getElementById('gstAmount');
    var gstLabelEl = document.getElementById('gstLabel');
    var grandTotalEl = document.getElementById('grandTotalAmount');
    var mobileGrandTotalEl = document.getElementById('mobileGrandTotal');
    var appliedCouponCode = document.getElementById('appliedCouponCode');
    var appliedCouponValue = document.getElementById('appliedCouponValue');

    function formatMoney(value) {
        return Number(value || 0).toFixed(2);
    }

    function couponValueText(data) {
        if (data.coupon_type === 'percent') {
            return String(data.coupon_value).replace(/\.00$/, '') + '% off';
        }
        if (data.coupon_value !== null && data.coupon_value !== undefined) {
            return '₹' + formatMoney(data.coupon_value) + ' off';
        }
        return '';
    }

    function updateSummary(data) {
        if ('discount' in data) {
            if (parseFloat(data.discount) > 0) {
                discountRow.classList.remove('d-none');
                discountAmountText.textContent = '-₹' + formatMoney(data.discount);
                discountCodeText.textContent = data.coupon_code ? '(' + data.coupon_code + ')' : '';
            } else {
                discountRow.classList.add('d-none');
            }
        }
        if ('gstTotal' in data) gstAmountEl.textContent = '₹' + formatMoney(data.gstTotal);
        if ('gstRateLabel' in data) gstLabelEl.textContent = data.gstRateLabel;
        if ('grandTotal' in data) {
            grandTotalEl.textContent = '₹' + formatMoney(data.grandTotal);
            if (mobileGrandTotalEl) mobileGrandTotalEl.textContent = '₹' + formatMoney(data.grandTotal);
        }
    }

    function bindCouponApplyButtons() {
        document.querySelectorAll('.coupon-apply-btn').forEach(function (btn) {
            if (btn.dataset.bound === '1') return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', function () {
                promoApplyForm.querySelector('[name="coupon_code"]').value = btn.getAttribute('data-code') || '';
                var promoCollapse = document.getElementById('promoCart');
                if (promoCollapse && !promoCollapse.classList.contains('show') && window.bootstrap) {
                    new bootstrap.Collapse(promoCollapse, { show: true });
                }
                promoApplyForm.dispatchEvent(new Event('submit', { cancelable: true }));
                document.querySelector('#couponModal .btn-close')?.click();
            });
        });
    }

    bindCouponApplyButtons();

    if (promoApplyForm) {
        promoApplyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            promoError.classList.add('d-none');
            promoSuccess.classList.add('d-none');
            var code = promoApplyForm.coupon_code.value.trim();
            if (!code) {
                promoError.textContent = 'Please enter a promo code.';
                promoError.classList.remove('d-none');
                return;
            }
            promoApplyForm.querySelector('.promoApplyBtn').disabled = true;
            fetch('{{ route("cart.coupon.apply") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new URLSearchParams({ coupon_code: code })
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (res) {
                promoApplyForm.querySelector('.promoApplyBtn').disabled = false;
                if (res.ok && res.data.status) {
                    promoApplyForm.classList.add('d-none');
                    promoAppliedBox.classList.remove('d-none');
                    appliedCouponCode.textContent = res.data.coupon_code;
                    appliedCouponValue.innerHTML = '<span class="txtorange" style="font-weight: normal;">' + couponValueText(res.data) + '</span>';
                    promoSuccess.textContent = res.data.message || 'Coupon applied!';
                    promoSuccess.classList.remove('d-none');
                    updateSummary(res.data);
                } else {
                    promoError.textContent = res.data.message || 'Invalid or expired code.';
                    promoError.classList.remove('d-none');
                }
            })
            .catch(function () {
                promoApplyForm.querySelector('.promoApplyBtn').disabled = false;
                promoError.textContent = 'Something went wrong. Try again.';
                promoError.classList.remove('d-none');
            });
        });
    }

    if (removePromoBtn) {
        removePromoBtn.addEventListener('click', function () {
            removePromoBtn.disabled = true;
            promoError.classList.add('d-none');
            promoSuccess.classList.add('d-none');
            fetch('{{ route("cart.coupon.remove") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                removePromoBtn.disabled = false;
                if (data.status) {
                    promoAppliedBox.classList.add('d-none');
                    promoApplyForm.classList.remove('d-none');
                    promoApplyForm.reset();
                    promoSuccess.textContent = data.message || 'Promo code removed.';
                    promoSuccess.classList.remove('d-none');
                    updateSummary(data);
                } else {
                    promoError.textContent = data.message || 'Could not remove promo.';
                    promoError.classList.remove('d-none');
                }
            })
            .catch(function () {
                removePromoBtn.disabled = false;
                promoError.textContent = 'Failed to remove coupon. Try again.';
                promoError.classList.remove('d-none');
            });
        });
    }

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
