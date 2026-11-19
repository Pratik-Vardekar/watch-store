@extends('layouts.front')

@section('content')
@php
$states = ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu and Kashmir','Ladakh'];
$defaultAddress = $defaultAddress ?? ($addresses->firstWhere('is_default', true) ?? $addresses->first());
$selectedBillingId = old('billing_address_id', $defaultAddress?->id ?? 'new');
$selectedShippingId = old('shipping_address_id', 'new');
$shippingMode = old('shipping_same_diff_address', 'same');
@endphp
<main class="pageWrapper">
    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>

    <section class="py-5">
       <div class="container shopbagContainer">
           <div class="row justify-content-between">
             <div class="col-12">
                <h1 class="sectTitle lh-1 mb-4 d-inline-flex align-items-center gap-3">Checkout</h1>
             </div>

             <div class="col-xl-7 col-lg-8 mb-5">

                @if(session('error'))
                   <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                   <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('checkout.place') }}" method="POST" class="formFormat" id="checkoutForm" autocomplete="off">
                   @csrf

                   <!-- ... [Billing and Shipping Address Forms (unchanged)] ... -->

                   <div class="paymentFormWrap mt-4 pt-4 border-top">
                      <h2 class="sectSubtitle fw-semibold mb-3">Payment Method</h2>
                      <div class="filterList d-flex flex-column gap-2">
                         <div class="form-check addressCheck border rounded-3 p-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }} required>
                            <label class="form-check-label w-100" for="payment_cod">
                               <strong>Cash on Delivery (COD)</strong>
                               <small class="d-block text-secondary">Pay when your order is delivered.</small>
                            </label>
                         </div>
                         <div class="form-check addressCheck border rounded-3 p-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_razorpay" value="razorpay" {{ old('payment_method') === 'razorpay' ? 'checked' : '' }} required>
                            <label class="form-check-label w-100" for="payment_razorpay">
                               <strong>Pay Online (Razorpay)</strong>
                               <small class="d-block text-secondary">UPI, cards, net banking and wallets.</small>
                            </label>
                         </div>
                      </div>
                   </div>

                </form>
             </div>

             <div class="col-lg-4 mb-lg-5">
                <div class="summaryWrap rounded-4 overflow-hidden">
                   <h3 class="px-4 py-3 swTitle bgYellowSub txtblack">Order Summary</h3>
                   <div class="px-4 py-3">
                      @foreach($cartItems as $cartItem)
                         @if($cartItem->product)
                         @php
                            $product = $cartItem->product;
                            $sellingPrice = $product->selling_price ?? $product->price ?? 0;
                            $originalPrice = $product->price ?? 0;
                            $lineTotal = $sellingPrice * $cartItem->quantity;
                            $itemDiscount = $product->discount ?? 0;
                            if (!$itemDiscount && $originalPrice > $sellingPrice && $originalPrice > 0) {
                               $itemDiscount = (int) round((($originalPrice - $sellingPrice) / $originalPrice) * 100);
                            }
                            $productImage = method_exists($product, 'getProductImageUrl')
                               ? $product->getProductImageUrl($product->thumbnail)
                               : asset('frontend/assets/images/no-image.png');
                         @endphp
                         <div class="d-flex w-100 mb-3 gap-3 align-items-start checkoutSummaryItem">
                            <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="flex-shrink-0">
                               <img src="{{ $productImage }}" alt="{{ $product->name }}" class="checkoutSummaryImg">
                            </a>
                            <div class="flex-grow-1 min-w-0">
                               <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="checkoutSummaryName d-block fw-semibold text-decoration-none mb-1">
                                  {{ $product->name }}
                               </a>
                               <div class="d-flex flex-wrap align-items-center gap-2 small text-secondary mb-1">
                                  <span>Qty: {{ $cartItem->quantity }}</span>
                                  @if($itemDiscount > 0)
                                     <span class="txtorange fw-semibold">{{ $itemDiscount }}% Off</span>
                                  @endif
                               </div>
                               <div class="d-flex flex-wrap align-items-center gap-2">
                                  @if($originalPrice > $sellingPrice)
                                     <span class="pibStikePrice small">₹{{ number_format($originalPrice, 2) }}</span>
                                  @endif
                                  <strong class="checkoutSummaryPrice">₹{{ number_format($lineTotal, 2) }}</strong>
                               </div>
                            </div>
                         </div>
                         @endif
                      @endforeach

                      <hr class="border-secondary-subtle my-3">

                      <div class="d-flex w-100 mb-2 swItem">
                         <div class="fw-medium">Subtotal</div>
                         <div class="ms-auto fw-semibold"> <span class="price">₹{{ number_format($subtotal, 2) }}</span> </div>
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
                            <div class="fw-medium d-flex flex-nowrap align-items-center swPromoHead" role="button" data-bs-toggle="collapse" data-bs-target="#promoCheckout" aria-expanded="{{ !empty($couponCode) ? 'true' : 'false' }}">
                               <svg class="me-2 promoIcon" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><circle cx="256.001" cy="346.888" r="17.133"></circle><circle cx="256.001" cy="165.11" r="17.133"></circle><path d="m498.198 13.815c-8.897-8.909-20.734-13.815-33.331-13.815h-179.488c-12.591 0-24.429 4.904-33.333 13.809l-238.235 238.235c-8.907 8.906-13.811 20.743-13.811 33.331 0 12.59 4.906 24.427 13.811 33.326l179.484 179.485c8.902 8.908 20.739 13.814 33.329 13.814 12.589 0 24.426-4.904 33.332-13.811l238.235-238.235c8.905-8.904 13.809-20.742 13.809-33.333v-179.488c0-12.597-4.906-24.435-13.802-33.318zm-242.197 104.162c25.989 0 47.133 21.144 47.133 47.133s-21.144 47.133-47.133 47.133-47.133-21.144-47.133-47.133 21.144-47.133 47.133-47.133zm0 276.044c-25.989 0-47.133-21.144-47.133-47.133s21.144-47.133 47.133-47.133 47.133 21.144 47.133 47.133-21.144 47.133-47.133 47.133zm96.399-123.022h-192.798c-8.284 0-15-6.716-15-15s6.716-15 15-15h192.798c8.284 0 15 6.716 15 15s-6.715 15-15 15zm64.267-160.666c-8.28 0-15-6.72-15-15s6.72-15 15-15 15 6.72 15 15-6.72 15-15 15z"></path></svg>
                               <span>Have a Promo Code?</span>
                            </div>
                            <div class="collapse {{ !empty($couponCode) ? 'show' : '' }}" id="promoCheckout">
                               <div id="promoAppliedBox" class="{{ !empty($couponCode) ? '' : 'd-none' }}">
                                   <div class="d-flex align-items-center justify-content-between mt-2 p-2 rounded-pill bg-white border">
                                       <div class="ps-2">
                                           <strong id="appliedCouponCode" class="txtorange">{{ $couponCode }}</strong>
                                           <small class="d-block text-secondary" id="appliedCouponValue">
                                               @if($coupon && $coupon->type === 'percent')
                                                  {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}% off
                                               @elseif($coupon)
                                                  ₹{{ number_format($coupon->value, 2) }} off
                                               @endif
                                           </small>
                                       </div>
                                       <button type="button" class="btn btn-sm rounded-pill" id="removePromoBtn">Remove</button>
                                   </div>
                               </div>
                               <form id="promoApplyForm" class="input-group mt-2 p-1 rounded-pill bg-white border overflow-hidden {{ !empty($couponCode) ? 'd-none' : '' }}">
                                   <input type="text" name="coupon_code" class="form-control border-0" placeholder="Enter Promo Code" value="{{ old('coupon_code') }}" required />
                                   <button type="submit" class="promoApplyBtn btn rounded-pill">Apply</button>
                               </form>
                               <div class="small mt-2 text-danger d-none" id="promoError"></div>
                               <div class="small mt-2 text-success d-none" id="promoSuccess"></div>
                            </div>
                            <button type="button"
                                    class="btn btn-link p-0 mt-2 txtorange text-decoration-none fw-semibold"
                                    data-bs-toggle="modal"
                                    id="showCouponModalBtn"
                                    data-bs-target="#couponModal">
                               View available coupons
                            </button>
                         </div>
                      </div>

                      <div id="discountRow" class="d-flex w-100 mb-2 swItem txtorange {{ ($discount ?? 0) > 0 ? '' : 'd-none' }}">
                         <div class="fw-medium txtorange">Discount <span id="discountCodeText">{{ !empty($couponCode) ? '(' . $couponCode . ')' : '' }}</span></div>
                         <div class="ms-auto fw-semibold txtorange" id="discountAmountText">-₹{{ number_format($discount ?? 0, 2) }}</div>
                      </div>

                      <div class="d-flex w-100 mb-2 swItem">
                         <div class="fw-medium" id="gstLabel">{{ $gstRateLabel ?? 'GST (inclusive)' }}</div>
                         <div class="ms-auto fw-semibold"> <span class="price" id="gstAmount">₹{{ number_format($gstTotal, 2) }}</span> </div>
                      </div>

                      <div class="d-flex w-100 mb-2 swItem">
                         <div class=" fw-bold">Total</div>
                         <div class="ms-auto fw-bold"> <span class="price" id="grandTotalAmount">₹{{ number_format($grandTotal, 2) }}</span> </div>
                      </div>

                      <div class="w-100 mt-5 d-flex justify-content-center">
                         <button type="submit" form="checkoutForm" class="ctaBtn cartBtn" id="checkoutSubmitBtn">
                            <span class="btnTxt">Place Order</span>
                            <span class="arw"></span>
                         </button>
                      </div>

                   </div>
                </div>
             </div>
           </div>
       </div>
    </section>

    <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-scrollable modal-lg">
          <div class="modal-content">
             <div class="modal-header">
                <h5 class="modal-title" id="couponModalLabel">Available Coupons</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body" id="couponModalBody">
                <p class="text-secondary small mb-3">
                   Coupons are calculated on selling price. Minimum order and maximum discount rules are checked against your order.
                </p>
                <div id="couponList">
                   <div class="text-center py-4 text-secondary">
                      Loading coupons...
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </main>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const billingFields = document.getElementById('billingFields');
    const shippingAddressForm = document.getElementById('shippingAddressForm');
    const shippingFields = document.getElementById('shippingFields');
    const shippingRadios = document.querySelectorAll('.shipping-toggle');
    const billingToggles = document.querySelectorAll('.billing-address-toggle');
    const shippingToggles = document.querySelectorAll('.shipping-address-toggle');
    const checkoutForm = document.getElementById('checkoutForm');
    const checkoutSubmitBtn = document.getElementById('checkoutSubmitBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || checkoutForm?.querySelector('input[name="_token"]')?.value
        || '';

    // --- Promo code dynamic ---
    const promoApplyForm = document.getElementById('promoApplyForm');
    const promoAppliedBox = document.getElementById('promoAppliedBox');
    const promoError = document.getElementById('promoError');
    const promoSuccess = document.getElementById('promoSuccess');
    const removePromoBtn = document.getElementById('removePromoBtn');
    const discountRow = document.getElementById('discountRow');
    const discountAmountText = document.getElementById('discountAmountText');
    const discountCodeText = document.getElementById('discountCodeText');
    const gstAmount = document.getElementById('gstAmount');
    const gstLabel = document.getElementById('gstLabel');
    const grandTotalAmount = document.getElementById('grandTotalAmount');
    const appliedCouponCode = document.getElementById('appliedCouponCode');
    const appliedCouponValue = document.getElementById('appliedCouponValue');
    const showCouponModalBtn = document.getElementById('showCouponModalBtn');

    function updateSummary(data) {
        if('discount' in data) {
            if(parseFloat(data.discount) > 0) {
                discountRow.classList.remove('d-none');
                discountAmountText.textContent = "-₹" + Number(data.discount).toFixed(2);
                discountCodeText.textContent = data.coupon_code ? "(" + data.coupon_code + ")" : "";
            } else {
                discountRow.classList.add('d-none');
                discountAmountText.textContent = "";
                discountCodeText.textContent = "";
            }
        }
        if('gstTotal' in data) {
            gstAmount.textContent = "₹" + Number(data.gstTotal).toFixed(2);
        }
        if('gstRateLabel' in data) {
            gstLabel.textContent = data.gstRateLabel;
        }
        if('grandTotal' in data) {
            grandTotalAmount.textContent = "₹" + Number(data.grandTotal).toFixed(2);
        }
    }

    if (promoApplyForm) {
        promoApplyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // hide all feedbacks
            promoError.classList.add('d-none');
            promoSuccess.classList.add('d-none');
            const code = promoApplyForm.coupon_code.value.trim();
            if (!code) {
                promoError.textContent = "Please enter a promo code.";
                promoError.classList.remove('d-none');
                return;
            }
            promoApplyForm.querySelector('.promoApplyBtn').disabled = true;

            fetch("{{ route('cart.coupon.apply') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({
                    coupon_code: code
                })
            })
            .then(response => response.json())
            .then(data => {
                promoApplyForm.querySelector('.promoApplyBtn').disabled = false;
                if (data.status) {
                    // Success: show applied coupon visually, update summary
                    promoApplyForm.classList.add('d-none');
                    promoAppliedBox.classList.remove('d-none');
                    appliedCouponCode.textContent = data.coupon_code;
                    appliedCouponValue.textContent = data.coupon_type === 'percent'
                        ? data.coupon_value.replace(/\.00$/, '') + '% off'
                        : '₹' + Number(data.coupon_value).toFixed(2) + ' off';
                    promoSuccess.textContent = data.message ?? 'Coupon applied!';
                    promoSuccess.classList.remove('d-none');
                    updateSummary(data);
                } else {
                    promoError.textContent = data.message || 'Invalid or expired code.';
                    promoError.classList.remove('d-none');
                }
            })
            .catch(() => {
                promoApplyForm.querySelector('.promoApplyBtn').disabled = false;
                promoError.textContent = "Something went wrong. Try again.";
                promoError.classList.remove('d-none');
            });
        });
    }

    if (removePromoBtn) {
        removePromoBtn.addEventListener('click', function() {
            removePromoBtn.disabled = true;
            promoError.classList.add('d-none');
            promoSuccess.classList.add('d-none');
            fetch("{{ route('cart.coupon.remove') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                removePromoBtn.disabled = false;
                if (data.status) {
                    promoAppliedBox.classList.add('d-none');
                    promoApplyForm.classList.remove('d-none');
                    promoApplyForm.reset();
                    promoSuccess.textContent = data.message ?? "Promo code removed.";
                    promoSuccess.classList.remove('d-none');
                    updateSummary(data);
                } else {
                    promoError.textContent = data.message || 'Could not remove promo.';
                    promoError.classList.remove('d-none');
                }
            })
            .catch(() => {
                removePromoBtn.disabled = false;
                promoError.textContent = "Failed to remove coupon. Try again.";
                promoError.classList.remove('d-none');
            });
        });
    }

    // Dynamically load coupons in modal
    if (showCouponModalBtn) {
        showCouponModalBtn.addEventListener('click', function() {
            const couponList = document.getElementById('couponList');
            couponList.innerHTML = '<div class="text-center py-4 text-secondary">Loading coupons...</div>';
            fetch("{{ route('cart.coupon.available') }}", {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.coupons || data.coupons.length === 0) {
                    couponList.innerHTML = `<div class="text-center py-4 text-secondary">No coupons available for the products in your order.</div>`;
                    return;
                }
                let html = "";
                data.coupons.forEach(offer => {
                    const code = offer.coupon.code;
                    html += `
                    <div class="border rounded-3 p-3 mb-3 ${offer.can_apply ? '' : 'opacity-75'}">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <strong class="fs-5">${code}</strong>
                                    ${offer.coupon.type === "percent" ? `
                                      <span class="badge text-bg-orange">${Number(offer.coupon.value).toFixed(2).replace(/\.00$/, '')}% OFF</span>
                                    ` : `
                                      <span class="badge text-bg-orange">₹${Number(offer.coupon.value).toFixed(2)} OFF</span>
                                    `}
                                    ${offer.is_applied ? `<span class="badge text-bg-orange">Applied</span>` : ""}
                                </div>
                                <small class="text-secondary d-block">
                                    ${offer.applicability && offer.applicability.label ? offer.applicability.label : 'Valid on selected items'}
                                </small>
                            </div>
                            <div class="text-end">
                                ${offer.can_apply ? `
                                  <div class="txtorange fw-semibold mb-1">You save ₹${Number(offer.discount).toFixed(2)}</div>
                                ` : (offer.message ? `
                                  <div class="text-danger small mb-1">${offer.message}</div>
                                ` : "")}
                                ${offer.is_applied ? `
                                  <button type="button" class="btn btn-sm btn-purple rounded-pill px-3" disabled>Applied</button>
                                ` : offer.can_apply ? `
                                  <button type="button" class="btn btn-sm btn-purple rounded-pill px-3 coupon-apply-btn" data-code="${code}">Apply</button>
                                ` : `
                                  <button type="button" class="btn btn-sm btn-outline-purple rounded-pill px-3" disabled>Not applicable</button>
                                `}
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-3 small mb-3">
                            <div>
                                <span class="text-secondary">Min. order:</span>
                                ${offer.minimum_amount ?
                                    `<strong>₹${Number(offer.minimum_amount).toFixed(2)}</strong>
                                     ${offer.meets_minimum ? `<span class="txtgreen">(met)</span>` : `<span class="text-danger">(need ₹${Number(Math.max(0, offer.minimum_amount - (data.subtotal || 0))).toFixed(2)} more)</span>`}`
                                    : `<strong>None</strong>`
                                }
                            </div>
                            <div>
                                <span class="text-secondary">Max. discount:</span>
                                ${offer.maximum_discount ?
                                    `<strong>₹${Number(offer.maximum_discount).toFixed(2)}</strong>` :
                                    `<strong>No cap</strong>`
                                }
                            </div>
                            <div>
                                <span class="text-secondary">Eligible on selling price:</span>
                                <strong>₹${Number(offer.eligible_subtotal || 0).toFixed(2)}</strong>
                            </div>
                        </div>
                    </div>
                    `;
                });
                couponList.innerHTML = html;
                // Bind apply buttons
                document.querySelectorAll('.coupon-apply-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const code = btn.getAttribute('data-code');
                        document.querySelector('#promoApplyForm input[name="coupon_code"]').value = code;
                        // Simulate submit
                        promoApplyForm.dispatchEvent(new Event('submit', {cancelable:true}));
                        // Close modal
                        document.querySelector('#couponModal .btn-close').click();
                    });
                });
            })
            .catch(() => {
                couponList.innerHTML = `<div class="text-center py-4 text-danger">Could not fetch coupons. Please try again.</div>`;
            });
        });
    }
    // --- End Promo code dynamic ---

    function setRequired(container, enabled) {
        if (!container) return;
        container.querySelectorAll('input, select').forEach(function (field) {
            if (field.type === 'checkbox' || field.type === 'radio' || field.type === 'hidden') return;
            if (field.id && field.id.indexOf('address_line_2') !== -1) {
                field.required = false;
                return;
            }
            field.required = enabled;
        });
    }

    function toggleBillingFields() {
        const selected = document.querySelector('.billing-address-toggle:checked');
        const isNew = !selected || selected.value === 'new';
        if (billingFields) {
            billingFields.classList.toggle('d-none', !isNew);
        }
        setRequired(billingFields, isNew);
    }

    function toggleShippingFields() {
        if (!shippingAddressForm) return;
        const mode = document.querySelector('.shipping-toggle:checked');
        const isDifferent = mode && mode.value === 'different';

        shippingAddressForm.classList.toggle('d-none', !isDifferent);

        if (!isDifferent) {
            setRequired(shippingFields, false);
            return;
        }

        const selected = document.querySelector('.shipping-address-toggle:checked');
        const isNew = !selected || selected.value === 'new';
        if (shippingFields) {
            shippingFields.classList.toggle('d-none', !isNew);
        }
        setRequired(shippingFields, isNew);
    }

    billingToggles.forEach(function (radio) {
        radio.addEventListener('change', toggleBillingFields);
    });

    shippingRadios.forEach(function (radio) {
        radio.addEventListener('change', toggleShippingFields);
    });

    shippingToggles.forEach(function (radio) {
        radio.addEventListener('change', toggleShippingFields);
    });

    toggleBillingFields();
    toggleShippingFields();

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (event) {
            const paymentMethod = checkoutForm.querySelector('input[name="payment_method"]:checked');
            if (!paymentMethod || paymentMethod.value !== 'razorpay') {
                return;
            }

            event.preventDefault();

            if (!checkoutForm.reportValidity()) {
                return;
            }

            if (checkoutSubmitBtn) {
                checkoutSubmitBtn.disabled = true;
            }

            const formData = new FormData(checkoutForm);

            fetch(checkoutForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function ({ ok, data }) {
                if (!ok || !data.status || !data.razorpay) {
                    throw new Error(data.message || 'Unable to start Razorpay payment.');
                }

                const options = {
                    key: data.razorpay.key,
                    amount: data.razorpay.amount,
                    currency: data.razorpay.currency,
                    name: data.razorpay.name,
                    description: data.razorpay.description,
                    order_id: data.razorpay.order_id,
                    prefill: data.razorpay.prefill || {},
                    theme: data.razorpay.theme || { color: '#E85D04' },
                    handler: function (response) {
                        fetch(data.verify_url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                order_id: data.order_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature,
                            }),
                        })
                        .then(function (verifyResponse) {
                            return verifyResponse.json().then(function (verifyData) {
                                return { ok: verifyResponse.ok, data: verifyData };
                            });
                        })
                        .then(function ({ ok, data: verifyData }) {
                            if (ok && verifyData.status && verifyData.redirect_url) {
                                window.location.href = verifyData.redirect_url;
                                return;
                            }
                            throw new Error(verifyData.message || 'Payment verification failed.');
                        })
                        .catch(function (error) {
                            alert(error.message || 'Payment verification failed.');
                            if (checkoutSubmitBtn) {
                                checkoutSubmitBtn.disabled = false;
                            }
                        });
                    },
                    modal: {
                        ondismiss: function () {
                            if (checkoutSubmitBtn) {
                                checkoutSubmitBtn.disabled = false;
                            }
                        },
                    },
                };

                const razorpay = new Razorpay(options);
                razorpay.open();
            })
            .catch(function (error) {
                alert(error.message || 'Unable to place order. Please try again.');
                if (checkoutSubmitBtn) {
                    checkoutSubmitBtn.disabled = false;
                }
            });
        });
    }
});
</script>
@endpush
