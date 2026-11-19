@extends('layouts.front')

@section('content')
<main class="pageWrapper">

    {{-- Breadcrumb --}}
    <section class="py-3">
        <div class="container">
            <nav class="breadcrumb mb-0">
                <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                <a class="breadcrumb-item active" href="">My Bag</a>
            </nav>
        </div>
    </section>

    {{-- Cart Section --}}
    <section class="mb-5">
        <div class="container">

            <div class="row justify-content-between">

                <div class="col-12">
                    <div class="mb-4 d-flex align-items-center">
                        <h1 class="sectTitle mb-0 d-inline-flex align-items-center gap-3">My Bag</h1>
                        <span class="bagCount bgRed text-white rounded-pill p-sm-2 p-1 lh-1 ms-sm-3 ms-2">{{ $itemCount }}</span>
                    </div>
                </div>

                {{-- Cart Items --}}
                <div class="col-xl-7 col-lg-8 mb-5">

                    <ul class="bagListWrap mb-5 d-flex flex-column gap-4">

                        @forelse($cartItems as $cartItem)
                            @php
                                $product = $cartItem->product;
                                if (!$product) continue;

                                $sellingPrice = $product->selling_price ?? $product->price ?? 0;
                                $originalPrice = $product->price ?? 0;
                                
                                // Product discount percentage (for display only)
                                $productDiscountPercent = (int) ($product->discount ?? 0);
                                
                                // If no stored discount, calculate from price difference
                                if ($productDiscountPercent == 0 && $originalPrice > $sellingPrice && $originalPrice > 0) {
                                    $productDiscountPercent = round((($originalPrice - $sellingPrice) / $originalPrice) * 100);
                                }

                                $lineTotal = $sellingPrice * $cartItem->quantity;
                                $image = $product->getProductImageUrl($product->thumbnail);
                                $maxQty = min(10, max(1, (int) $product->quantity));
                                $qtyValue = min(max(1, (int) $cartItem->quantity), $maxQty);
                            @endphp

                            <li class="bagItem rounded-3 p-sm-3 p-2 w-100 d-flex flex-nowrap gap-3 gap-lg-4">
                                <div class="col-auto">
                                    <a href="{{ route('product.details', ['slug' => $product->slug]) }}">
                                        <img src="{{ $image }}" class="bgiImg" alt="{{ $product->name }}">
                                    </a>
                                </div>
                                <div class="col d-flex flex-column gap-4" style="min-width:0;">
                                    
                                    <div class="d-flex flex-nowrap justify-content-between">
                                        <div class="col" style="min-width:0;">
                                            <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="bgiProdname d-block">
                                                <strong class="d-block text-uppercase">{{ $product->brand->name ?? '' }}</strong>
                                                <div class="prodName">{{ $product->name }}</div>
                                            </a>
                                        </div>
                                        <div class="col-auto">
                                            @if(!empty($cartItem->is_guest))
                                                <form action="{{ route('cart.guest.remove', $cartItem->product_id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-close" title="Remove"></button>
                                                </form>
                                            @else
                                                <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-close" title="Remove"></button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        <div class="col-sm-5 col-6">
                                            <small class="d-block mb-1 text-secondary">Quantity</small>
                                            @if(!empty($cartItem->is_guest))
                                                <form action="{{ route('cart.guest.update', $cartItem->product_id) }}" method="POST" class="cart-qty-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="quantitySpinner input-group border rounded d-inline-flex">
                                                        <button class="btn bg-white border-0 minus" type="button" {{ $qtyValue <= 1 ? 'disabled' : '' }}>-</button>
                                                        <input type="text" name="quantity" value="{{ $qtyValue }}" min="1" max="{{ $maxQty }}" class="form-control border-0 text-center px-0 numData" readonly>
                                                        <button class="btn bg-white border-0 plus" type="button" {{ $qtyValue >= $maxQty ? 'disabled' : '' }}>+</button>
                                                    </div>
                                                </form>
                                            @else
                                                <form action="{{ route('cart.update', $cartItem->id) }}" method="POST" class="cart-qty-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="quantitySpinner input-group border rounded d-inline-flex">
                                                        <button class="btn bg-white border-0 minus" type="button" {{ $qtyValue <= 1 ? 'disabled' : '' }}>-</button>
                                                        <input type="text" name="quantity" value="{{ $qtyValue }}" min="1" max="{{ $maxQty }}" class="form-control border-0 text-center px-0 numData" readonly>
                                                        <button class="btn bg-white border-0 plus" type="button" {{ $qtyValue >= $maxQty ? 'disabled' : '' }}>+</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="col-sm-7 col-6 d-flex flex-wrap justify-content-between">
                                            <div class="col-sm-auto col-12 pt-3 d-flex align-items-center gap-sm-3 gap-2">
                                                @if($originalPrice > $sellingPrice)
                                                    <span class="pip_ProdStrike">₹{{ number_format($originalPrice, 2) }}</span>
                                                    <small class="txtRed fw-semibold">{{ $productDiscountPercent }}% Off</small>
                                                @endif
                                            </div>
                                            <div class="col-sm-auto col-12 pt-sm-3 d-flex align-items-center gap-3">
                                                <strong class="bgiProdPrice">₹{{ number_format($lineTotal, 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bgiDelivery d-flex align-items-start gap-1">
                                        <span>
                                            Delivery in: 
                                            <span class="fw-medium d-inline-block">
                                                5-6 days
                                            </span>
                                        </span>
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
                        <small class="d-block mb-3">Want to check some more Watches and other products?</small>
                        <a href="{{ route('product.listing') }}" class="ctaBtn dark">Continue Shopping</a>
                    </div>

                </div>

                {{-- Order Summary --}}
                <div class="col-lg-4 mb-lg-5">
                    
                    <div class="summaryWrap overflow-hidden rounded-2 shadow-lg">
                        <h3 class="px-4 py-3 swTitle text-white">Order Summary</h3>

                        <div class="px-4 py-3">

                            {{-- Subtotal --}}
                            <div class="d-flex w-100 mb-3 swItem">
                                <div class="fw-medium">Subtotal</div>
                                <div class="ms-auto fw-semibold"><span class="price">₹ {{ number_format($subtotal, 2) }}</span></div>
                            </div>

                            {{-- Shipping Charges --}}
                            <div class="d-flex w-100 mb-3 swItem">
                                <div class="fw-medium">Shipping Charges</div>
                                <div class="ms-auto fw-semibold">
                                    @if($shippingCharge <= 0)
                                        <span class="txtgreen">Free</span>
                                    @else
                                        <span class="price">₹ {{ number_format($shippingCharge, 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Promo Code Section --}}
                            <div class="swPromoBox mb-3 py-3">
                                <div class="swPromoForm">
                                    <div class="fw-medium d-flex flex-nowrap align-items-center swPromoHead"
                                         role="button"
                                         data-bs-toggle="collapse"
                                         data-bs-target="#promoCart"
                                         aria-expanded="{{ !empty($couponCode) ? 'true' : 'false' }}">
                                        <svg class="me-2 promoIcon" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><circle cx="256.001" cy="346.888" r="17.133"></circle><circle cx="256.001" cy="165.11" r="17.133"></circle><path d="m498.198 13.815c-8.897-8.909-20.734-13.815-33.331-13.815h-179.488c-12.591 0-24.429 4.904-33.333 13.809l-238.235 238.235c-8.907 8.906-13.811 20.743-13.811 33.331 0 12.59 4.906 24.427 13.811 33.326l179.484 179.485c8.902 8.908 20.739 13.814 33.329 13.814 12.589 0 24.426-4.904 33.332-13.811l238.235-238.235c8.905-8.904 13.809-20.742 13.809-33.333v-179.488c0-12.597-4.906-24.435-13.802-33.318zm-242.197 104.162c25.989 0 47.133 21.144 47.133 47.133s-21.144 47.133-47.133 47.133-47.133-21.144-47.133-47.133 21.144-47.133 47.133-47.133zm0 276.044c-25.989 0-47.133-21.144-47.133-47.133s21.144-47.133 47.133-47.133 47.133 21.144 47.133 47.133-21.144 47.133-47.133 47.133zm96.399-123.022h-192.798c-8.284 0-15-6.716-15-15s6.716-15 15-15h192.798c8.284 0 15 6.716 15 15s-6.715 15-15 15zm64.267-160.666c-8.28 0-15-6.72-15-15s6.72-15 15-15 15 6.72 15 15-6.72 15-15 15z"></path></svg>
                                        <span>Have a Promo Code?</span>
                                    </div>
                                    <div class="collapse {{ !empty($couponCode) ? 'show' : '' }}" id="promoCart">
                                        
                                        {{-- Applied Coupon Display --}}
                                        <div id="promoAppliedBox" class="{{ !empty($couponCode) ? '' : 'd-none' }}">
                                            <div class="d-flex align-items-center justify-content-between mt-2 p-2 rounded-pill bg-white border">
                                                <div class="ps-2">
                                                    <strong id="appliedCouponCode" class="txtRed">{{ $couponCode }}</strong>
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

                                        {{-- Promo Code Input --}}
                                        <form id="promoApplyForm" class="input-group mt-2 nlField rounded overflow-hidden {{ !empty($couponCode) ? 'd-none' : '' }}">
                                            <input type="text" name="coupon_code" style="size:14px; line-height: 17px;background: transparent;border: none;" class="form-control bg-gray fw-medium" placeholder="Enter Promo Code" value="{{ old('coupon_code') }}" required>
                                            <button type="submit" class="ctaBtn nlBtn promoApplyBtn">Apply</button>
                                        </form>

                                        {{-- Messages --}}
                                        <div class="small mt-2 text-danger d-none" id="promoError"></div>
                                        <div class="small mt-2 text-success d-none" id="promoSuccess"></div>
                                    </div>

                                    {{-- View Available Coupons Button --}}
                                    @if(($availableCoupons ?? collect())->isNotEmpty())
                                        <button type="button"
                                                class="btn btn-link p-0 mt-2 text-decoration-none fw-semibold"
                                                data-bs-toggle="modal"
                                                id="showCouponModalBtn"
                                                data-bs-target="#couponModal">
                                            View {{ $availableCoupons->count() }} available coupon{{ $availableCoupons->count() === 1 ? '' : 's' }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- DISCOUNT ROW - Uses $discount from controller (coupon discount only) --}}
                          {{-- DISCOUNT ROW - Shows only when coupon is applied --}}
{{-- DISCOUNT ROW - Uses inline style for initial visibility --}}
@php
$showDiscountRow = ($discount ?? 0) > 0;
@endphp

<div id="discountRow" 
     class="d-flex w-100 mb-3 swItem {{ $showDiscountRow ? '' : 'd-none' }}" 
     style="{{ $showDiscountRow ? 'display: flex !important;' : 'display: none !important;' }}">
    <div class="fw-medium txtRed" style="color: #dc3545 !important;">
        Discount 
        @if(!empty($couponCode))
            <span id="discountCodeText" style="color: #dc3545 !important;">({{ $couponCode }})</span>
        @endif
    </div>
    <div class="ms-auto fw-semibold txtRed" id="discountAmountText" style="color: #dc3545 !important;">
        -₪{{ number_format($discount ?? 0, 2) }}
    </div>
</div>
@else
<div id="discountRow" class="d-none"></div>
@endif

                            {{-- GST Row - Shows inclusive GST --}}
                            <div class="d-flex w-100 mb-3 swItem">
                                <div class="fw-medium" id="gstLabel">{{ $gstRateLabel ?? 'GST (18% inclusive)' }}</div>
                                <div class="ms-auto fw-semibold">
                                    <span class="price" id="gstAmount">₹{{ number_format($gstTotal, 2) }}</span>
                                </div>
                            </div>

                            {{-- Grand Total --}}
                            <div class="d-flex w-100 mb-3 swItem total">
                                <div class="">Total</div>
                                <div class="ms-auto"><span class="price" id="grandTotalAmount">₹{{ number_format($grandTotal, 2) }}</span></div>
                            </div>

                            {{-- Checkout Button --}}
                            <div class="w-100 mt-5 d-flex justify-content-center">
                                @if($itemCount > 0)
                                    <a href="{{ route('checkout.shipping', ['from' => 'cart']) }}" class="ctaBtn cartBtn">
                                        <span class="btnTxt">Checkout</span>
                                        <span class="arw"></span>
                                    </a>
                                @else
                                    <button type="button" class="ctaBtn cartBtn" disabled>
                                        <span class="btnTxt">Checkout</span>
                                        <span class="arw"></span>
                                    </button>
                                @endif
                            </div>

                        </div>

                    </div>

                </div>

            </div>
                
        </div>

        {{-- Mobile Summary --}}
        <div class="d-lg-none animateThis">
            <div class="mobSummaryItem py-2 w-100">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col text-white">
                            <div type="button" class="gotoTarget" data-bs-target=".summaryWrap">
                                <div class="mb-1" style="font-size:.7rem;">
                                    <small>Check Order Summary</small>
                                    <svg xmlns="http://www.w3.org/2000/svg" width=".7rem" height=".7rem" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/></svg>
                                </div>
                                <div class="">Total : <span class="price fw-bold" id="mobileGrandTotal">₹ {{ number_format($grandTotal, 2) }}</span></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            @if($itemCount > 0)
                                <a href="{{ route('checkout.shipping', ['from' => 'cart']) }}" class="ctaBtn">Checkout</a>
                            @else
                                <button type="button" class="ctaBtn" disabled>Checkout</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    {{-- Coupon Modal --}}
    <div class="coupon-modal modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">Available Coupons</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="couponModalBody">
                    <p class="text-secondary small mb-3">Coupons are calculated on selling price.</p>
                    <div id="couponList">
                        @include('frontend.partials.available-coupons-list', [
                            'availableCoupons' => $availableCoupons ?? collect(),
                            'couponCode' => $couponCode ?? null,
                            'subtotal' => $subtotal ?? 0,
                            'emptyMessage' => 'No coupons available for this order.',
                            'btnClasses' => [
                                'apply' => 'coupon-apply-btn btn btn-sm rounded-pill me-2 bg-primary text-white',
                                'applied' => 'coupon-apply-btn btn btn-sm rounded-pill me-2 bg-danger text-white'
                            ]
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    var grandTotalEl = document.getElementById('grandTotalAmount');
    var mobileGrandTotalEl = document.getElementById('mobileGrandTotal');
    var gstAmountEl = document.getElementById('gstAmount');
    var gstLabelEl = document.getElementById('gstLabel');
    var appliedCouponCode = document.getElementById('appliedCouponCode');
    var appliedCouponValue = document.getElementById('appliedCouponValue');
    var subtotalEl = document.querySelector('.swItem .price'); // Subtotal element

    // Function to update summary after coupon apply/remove
    function updateSummary(data) {
        console.log('Updating summary with data:', data);
        
        // ========== UPDATE DISCOUNT ROW ==========
        if ('discount' in data) {
            var discountAmount = parseFloat(data.discount) || 0;
            var discountRow = document.getElementById('discountRow');
            var discountAmountText = document.getElementById('discountAmountText');
            var discountCodeText = document.getElementById('discountCodeText');
            
            if (discountRow) {
                if (discountAmount > 0) {
                    // Show discount row
                    discountRow.classList.remove('d-none');
                    discountRow.style.display = 'flex';
                    
                    // Update the discount amount
                    if (discountAmountText) {
                        discountAmountText.textContent = '-₪' + discountAmount.toFixed(2);
                        discountAmountText.style.color = '#dc3545';
                    }
                    
                    // Update coupon code if present
                    if (discountCodeText) {
                        discountCodeText.textContent = data.coupon_code ? '(' + data.coupon_code + ')' : '';
                        discountCodeText.style.color = '#dc3545';
                    }
                } else {
                    // Hide discount row
                    discountRow.classList.add('d-none');
                    discountRow.style.display = 'none';
                }
            }
        }
        
        // ========== UPDATE SUBTOTAL ==========
        if ('subtotal' in data && subtotalEl) {
            subtotalEl.textContent = '₪ ' + Number(data.subtotal).toFixed(2);
        }
        
        // ========== UPDATE GST ==========
        if ('gstTotal' in data) {
            var gstAmountEl = document.getElementById('gstAmount');
            if (gstAmountEl) {
                gstAmountEl.textContent = '₪' + Number(data.gstTotal).toFixed(2);
            }
        }
        if ('gstRateLabel' in data) {
            var gstLabelEl = document.getElementById('gstLabel');
            if (gstLabelEl) {
                gstLabelEl.textContent = data.gstRateLabel;
            }
        }
        
        // ========== UPDATE GRAND TOTAL ==========
        if ('grandTotal' in data) {
            var grandTotalEl = document.getElementById('grandTotalAmount');
            var mobileGrandTotalEl = document.getElementById('mobileGrandTotal');
            
            if (grandTotalEl) {
                grandTotalEl.textContent = '₪' + Number(data.grandTotal).toFixed(2);
            }
            if (mobileGrandTotalEl) {
                mobileGrandTotalEl.textContent = '₪ ' + Number(data.grandTotal).toFixed(2);
            }
        }
        
        // ========== UPDATE SHIPPING ==========
        if ('shippingCharge' in data) {
            var shippingEl = document.querySelector('.swItem:has(.fw-medium:contains("Shipping")) .price');
            if (shippingEl) {
                if (data.shippingCharge <= 0) {
                    shippingEl.textContent = 'Free';
                } else {
                    shippingEl.textContent = '₪ ' + Number(data.shippingCharge).toFixed(2);
                }
            }
        }
    }

    // Bind coupon apply buttons from modal
    function bindCouponApplyButtons() {
        document.querySelectorAll('.coupon-apply-btn').forEach(function (btn) {
            if (btn.dataset.bound === '1') return;
            btn.dataset.bound = '1';

            btn.addEventListener('click', function () {
                var codeInput = document.querySelector('#promoApplyForm input[name="coupon_code"]');
                if (codeInput) {
                    codeInput.value = btn.getAttribute('data-code');
                }
                var promoCollapse = document.getElementById('promoCart');
                if (promoCollapse && !promoCollapse.classList.contains('show') && window.bootstrap) {
                    new bootstrap.Collapse(promoCollapse, { show: true });
                }
                if (promoApplyForm) {
                    promoApplyForm.dispatchEvent(new Event('submit', { cancelable: true }));
                }
                var closeBtn = document.querySelector('#couponModal .btn-close');
                if (closeBtn) closeBtn.click();
            });
        });
    }

    bindCouponApplyButtons();

    // ========== APPLY COUPON ==========
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
            
            var applyBtn = promoApplyForm.querySelector('.promoApplyBtn');
            applyBtn.disabled = true;
            applyBtn.textContent = 'Applying...';
            
            fetch('{{ route("cart.coupon.apply") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({ coupon_code: code })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                applyBtn.disabled = false;
                applyBtn.textContent = 'Apply';
                
                if (data.status) {
                    // Show applied coupon box
                    promoApplyForm.classList.add('d-none');
                    promoAppliedBox.classList.remove('d-none');
                    
                    if (appliedCouponCode) {
                        appliedCouponCode.textContent = data.coupon_code;
                    }
                    if (appliedCouponValue) {
                        appliedCouponValue.textContent = data.coupon_type === 'percent'
                            ? String(data.coupon_value).replace(/\.00$/, '') + '% off'
                            : '₪' + Number(data.coupon_value).toFixed(2) + ' off';
                    }
                    
                    promoSuccess.textContent = data.message || 'Coupon applied!';
                    promoSuccess.classList.remove('d-none');
                    
                    // CRITICAL: Update the summary with all data
                    updateSummary(data);

                    // Update coupon buttons
                    document.querySelectorAll('.coupon-apply-btn').forEach(function(btn){
                        var btnCode = btn.getAttribute('data-code');
                        if (data.coupon_code && btnCode && btnCode === data.coupon_code) {
                            btn.classList.remove('bg-primary');
                            btn.classList.add('bg-danger');
                            btn.textContent = 'Applied';
                            btn.disabled = true;
                        } else {
                            btn.classList.remove('bg-danger');
                            btn.classList.add('bg-primary');
                            btn.textContent = 'Apply';
                            btn.disabled = false;
                        }
                    });
                } else {
                    promoError.textContent = data.message || 'Invalid or expired code.';
                    promoError.classList.remove('d-none');
                }
            })
            .catch(function (error) {
                console.error('Error:', error);
                applyBtn.disabled = false;
                applyBtn.textContent = 'Apply';
                promoError.textContent = 'Something went wrong. Please try again.';
                promoError.classList.remove('d-none');
            });
        });
    }

    // ========== REMOVE COUPON ==========
    if (removePromoBtn) {
        removePromoBtn.addEventListener('click', function () {
            removePromoBtn.disabled = true;
            removePromoBtn.textContent = 'Removing...';
            promoError.classList.add('d-none');
            promoSuccess.classList.add('d-none');
            
            fetch('{{ route("cart.coupon.remove") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                removePromoBtn.disabled = false;
                removePromoBtn.textContent = 'Remove';
                
                if (data.status) {
                    promoAppliedBox.classList.add('d-none');
                    promoApplyForm.classList.remove('d-none');
                    promoApplyForm.reset();
                    promoSuccess.textContent = data.message || 'Promo code removed.';
                    promoSuccess.classList.remove('d-none');
                    
                    // CRITICAL: Update the summary with all data
                    updateSummary(data);

                    // Reset coupon buttons
                    document.querySelectorAll('.coupon-apply-btn').forEach(function(btn){
                        btn.classList.remove('bg-danger');
                        btn.classList.add('bg-primary');
                        btn.textContent = 'Apply';
                        btn.disabled = false;
                    });
                } else {
                    promoError.textContent = data.message || 'Could not remove promo.';
                    promoError.classList.remove('d-none');
                }
            })
            .catch(function () {
                removePromoBtn.disabled = false;
                removePromoBtn.textContent = 'Remove';
                promoError.textContent = 'Failed to remove coupon. Try again.';
                promoError.classList.remove('d-none');
            });
        });
    }

    // Quantity spinner logic
    document.querySelectorAll('.quantitySpinner').forEach(function (spinner) {
        const form = spinner.closest('.cart-qty-form');
        const minusButton = spinner.querySelector('.minus');
        const plusButton = spinner.querySelector('.plus');
        const input = spinner.querySelector('.numData');

        if (!minusButton || !plusButton || !input) return;

        const minQty = 1;
        const maxQty = Math.min(10, parseInt(input.getAttribute('max') || '10', 10) || 10);

        const syncButtons = function (value) {
            minusButton.disabled = value <= minQty;
            plusButton.disabled = value >= maxQty;
        };

        const submitForm = function () {
            if (form) form.submit();
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