@extends('layouts.front')

@push('styles')
<style>
    .checkoutPageLoader {
        position: fixed;
        inset: 0;
        z-index: 1080;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.72);
        backdrop-filter: blur(2px);
    }
    .checkoutSummaryName {
    font-size: .85rem;
    line-height: 1.35;
    color: #333;
    white-space: normal;
    overflow: visible;
    text-overflow: unset;
    word-break: break-word;
}
    .checkoutPageLoader.is-visible {
        display: flex;
    }
    .checkoutPageLoaderInner {
        display: flex; 
        flex-direction: column;
        align-items: center;
        gap: .85rem;
        padding: 1.5rem 1.75rem;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    }
    .checkoutPageLoaderSpinner {
        width: 2.25rem;
        height: 2.25rem;
        border: 3px solid #eee;
        border-top-color: var(--redColor, #d32f2f);
        border-radius: 50%;
        animation: checkoutSpin .7s linear infinite;
    }
    /* =========================
   CHECKOUT PROMO CODE
========================= */

.swPromoBox {
    margin: 0 !important;
    padding: 0 0 18px !important;
    border-bottom: 1px dashed #d5d5d5;
}

.swPromoForm {
    width: 100%;
}

/* Promo header */
.swPromoHead {
    min-height: 44px;
    padding: 0;
    color: #333;
    font-size: 16px;
    font-weight: 500 !important;
    cursor: pointer;
    user-select: none;
}

.swPromoHead .promoIcon {
    width: 19px;
    height: 19px;
    flex: 0 0 19px;
    color: #191515;
    margin-right: 9px !important;
}

/* Chevron */
.swPromoHead::after {
    content: "";
    width: 9px;
    height: 9px;
    margin-left: auto;
    margin-right: 3px;
    border-top: 2px solid #222;
    border-left: 2px solid #222;
    transform: rotate(45deg);
    transition: transform .2s ease;
}

/* When promo section is open */
.swPromoHead[aria-expanded="true"]::after {
    transform: rotate(225deg);
}

/* Promo input area */
#promoCheckout {
    margin-top: 3px;
}

#promoApplyForm {
    display: flex;
    align-items: center;
    width: 100%;
    height: 58px;
    padding: 5px;
    background: #f0f0f0;
    border-radius: 6px !important;
    overflow: hidden;
}

/* Input */
#promoApplyForm .nlInput {
    height: 48px;
    min-width: 0;
    flex: 1;
    padding: 0 13px;
    background: transparent !important;
    border: 0 !important;
    outline: none !important;
    box-shadow: none !important;
    color: #333;
    font-size: 14px;
    font-weight: 500;
}

#promoApplyForm .nlInput::placeholder {
    color: #777;
}

/* Apply button */
#promoApplyForm .promoApplyBtn {
    width: 87px;
    height: 50px;
    flex: 0 0 87px;
    margin: 0;
    padding: 0;
    border: 0 !important;
    border-radius: 5px !important;
    background: #444 !important;
    color: #fff !important;
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    box-shadow: none !important;
}

#promoApplyForm .promoApplyBtn:hover {
    background: #333 !important;
}

#promoApplyForm .promoApplyBtn:disabled {
    opacity: .7;
}

/* Applied coupon */
#promoAppliedBox .d-flex {
    min-height: 58px;
    padding: 5px 7px !important;
    background: #f0f0f0 !important;
    border: 0 !important;
    border-radius: 6px !important;
}

#promoAppliedBox #appliedCouponCode {
    color: #333 !important;
}

#promoAppliedBox #appliedCouponValue {
    color: #777 !important;
}

#promoAppliedBox #removePromoBtn {
    background: #444 !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 5px !important;
    padding: 8px 14px;
}

#promoAppliedBox #removePromoBtn:hover {
    background: #333 !important;
}

/* Coupon messages */
#promoError,
#promoSuccess {
    font-size: 12px;
}

/* Available coupons */
#showCouponModalBtn {
    font-size: 13px;
}
    .checkoutPageLoaderText {
        margin: 0;
        font-size: .95rem;
        font-weight: 600;
        color: #333;
    }
    @keyframes checkoutSpin {
        to { transform: rotate(360deg); }
    }
    /* Apply Coupon button */
    .promoApplyBtn,
    .coupon-apply-btn.apply-blue {
        background: #007bff !important;
        color: #fff !important;
        border: none !important;
    }
    .promoApplyBtn:hover,
    .coupon-apply-btn.apply-blue:hover {
        background-color: #0056b3 !important;
        border-color: #0056b3 !important;
        color: #fff !important;
    }
    /* Applied Coupon button in modal */
    .coupon-apply-btn.applied-red {
        background: #f4ebeb !important;
        color: #fff !important;
        border: none !important;
        cursor: default !important;
        pointer-events: none;
    }

    /* Applied Coupon box */
    #promoAppliedBox .d-flex {
        background: var(--redColor, #ee0000) !important;
        color: #fff !important;
    }

    #promoAppliedBox #appliedCouponCode {
        color: #fff !important;
    }

    #promoAppliedBox #appliedCouponValue {
        color: #fff !important;
    }

    /* Remove button inside applied coupon */
    #promoAppliedBox #removePromoBtn {
        background-color: #fff !important;
        color: #ee0000 !important;
        border: 1px solid #fff !important;
    }

    #promoAppliedBox #removePromoBtn:hover {
        background-color: #f5f5f5 !important;
    }
</style>
@endpush

@section('content')
@php
$states = ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana',
           'Himachal Pradesh','Jammu and Kashmir','Jharkhand','Karnataka','Kerala','Madhya Pradesh',
           'Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan',
           'Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
           'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli','Daman and Diu',
           'Delhi','Lakshadweep','Puducherry','Ladakh'];
$defaultAddress = $defaultAddress ?? ($addresses->firstWhere('is_default', true) ?? $addresses->first());
$shippingMode   = old('shipping_same_diff_address', 'same');
@endphp

<main class="pageWrapper">

    <section class="py-3">
        <div class="container">
            <nav class="breadcrumb mb-0">
                <a class="breadcrumb-item" href="{{ url('/') }}">Home</a>
                <a class="breadcrumb-item active" href="">Checkout</a>
            </nav>
        </div>
    </section>

    <section class="mb-5">
        <div class="container">

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="row justify-content-between">

                <div class="col-12">
                    <div class="mb-4 d-flex align-items-center">
                        <h1 class="sectTitle mb-0 d-inline-flex align-items-center gap-3">Checkout</h1>
                    </div>
                </div>

                {{-- ======== LEFT COLUMN: Forms ======== --}}
                <div class="col-xl-7 col-lg-8 mb-5">

                    <form action="{{ route('checkout.place') }}" method="POST" class="formFormat" id="checkoutForm" autocomplete="off">
                        @csrf

                        {{-- ========== BILLING ADDRESS ========== --}}
                        <div class="shippingFormWrap mb-4 pb-4">

                            <h2 class="fs-6 fw-bold mb-3 border-bottom border-dark pb-2">Billing Address</h2>

                            {{-- Saved address picker --}}
                            @if($addresses->isNotEmpty())
                            <div class="mb-3 filterList">
                                @foreach($addresses as $addr)
                                <div class="form-check addressCheck mb-2 border rounded-3 p-3">
                                    <input class="form-check-input billing-address-toggle"
                                           type="radio"
                                           name="billing_address_id"
                                           id="billing_addr_{{ $addr->id }}"
                                           value="{{ $addr->id }}"
                                           {{ old('billing_address_id', $defaultAddress?->id) == $addr->id ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="billing_addr_{{ $addr->id }}">
                                        <strong>{{ $addr->name }}</strong>
                                        <small class="d-block text-secondary">
                                            {{ $addr->address }}{{ $addr->landmark ? ', ' . $addr->landmark : '' }},
                                            {{ $addr->city }}, {{ $addr->state }} – {{ $addr->pincode }}
                                        </small>
                                        <small class="text-secondary">{{ $addr->phone }}</small>
                                    </label>
                                </div>
                                @endforeach
                                <div class="form-check addressCheck mb-2 border rounded-3 p-3">
                                    <input class="form-check-input billing-address-toggle"
                                           type="radio"
                                           name="billing_address_id"
                                           id="billing_addr_new"
                                           value="new"
                                           {{ old('billing_address_id', $defaultAddress?->id) === 'new' || $addresses->isEmpty() ? 'checked' : '' }}>
                                    <label class="form-check-label" for="billing_addr_new">
                                        <strong>Enter a new address</strong>
                                    </label>
                                </div>
                            </div>
                            @endif

                            {{-- New billing address fields --}}
                            <div id="billingFields" class="{{ ($addresses->isNotEmpty() && old('billing_address_id', $defaultAddress?->id) !== 'new') ? 'd-none' : '' }}">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <small>Full Name <span class="txtRed">*</span></small>
                                        <input type="text" class="form-control" name="billing_name"
                                               value="{{ old('billing_name', $defaultAddress?->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <small>Phone <span class="txtRed">*</span></small>
                                        <input 
                                            type="text"
                                            class="form-control" 
                                            name="billing_phone"
                                            value="{{ old('billing_phone', $defaultAddress?->phone) }}" 
                                            required
                                            pattern="^[0-9]{10}$"
                                            maxlength="10"
                                            inputmode="numeric"
                                            title="Please enter a valid 10 digit phone number"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                        >
                                    </div>
                               
                               
                                    
                                    <div class="col-12 m-0"></div>
                                    <div class="col-md-6">
                                        <small>Address Line 1 <span class="txtRed">*</span></small>
                                        <input type="text" class="form-control" name="billing_address_line_1"
                                               value="{{ old('billing_address_line_1', $defaultAddress?->address) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <small>Address Line 2</small>
                                        <input type="text" class="form-control" name="billing_address_line_2"
                                               value="{{ old('billing_address_line_2', $defaultAddress?->landmark) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <small>State <span class="txtRed">*</span></small>
                                        <select class="form-select" name="billing_state" required>
                                            <option value="">Select State</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state }}"
                                                    {{ old('billing_state', $defaultAddress?->state) === $state ? 'selected' : '' }}>
                                                    {{ $state }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <small>City <span class="txtRed">*</span></small>
                                        <input type="text" class="form-control" name="billing_city"
                                               value="{{ old('billing_city', $defaultAddress?->city) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <small>Pincode <span class="txtRed">*</span></small>
                                        <input type="number" class="form-control" name="billing_pincode"
                                               value="{{ old('billing_pincode', $defaultAddress?->pincode) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox" name="save_billing_address" id="save_billing_address" value="1"
                                                {{ old('save_billing_address') ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2" for="save_billing_address">Save this address</label>
                                            {{-- <button type="button" class="btn btn-primary btn-sm ms-3" id="billingSaveAddressBtn">Save Address</button> --}}
                                        </div>
                                        {{-- <div class="small mt-1 text-success d-none" id="billingSaveAddressSuccess"></div>
                                        <div class="small mt-1 text-danger d-none" id="billingSaveAddressError"></div> --}}
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- ========== SHIPPING ADDRESS ========== --}}
                        <div class="billingFormWrap mb-4 pb-4">

                            <h2 class="fs-6 fw-bold mb-3 border-bottom border-dark pb-2">Shipping Address</h2>

                            <div class="mb-4 filterList">
                                <div class="form-check form-check-inline addressCheck">
                                    <input class="form-check-input shipping-toggle"
                                           type="radio"
                                           id="sameAddress"
                                           name="shipping_same_diff_address"
                                           value="same"
                                           {{ $shippingMode === 'same' ? 'checked' : '' }}>
                                    <label class="form-check-label mt-1" for="sameAddress">Same as billing address</label>
                                </div>
                                <div class="form-check form-check-inline addressCheck">
                                    <input class="form-check-input shipping-toggle"
                                           type="radio"
                                           id="diffAddress"
                                           name="shipping_same_diff_address"
                                           value="different"
                                           {{ $shippingMode === 'different' ? 'checked' : '' }}>
                                    <label class="form-check-label mt-1" for="diffAddress">Use a different shipping address</label>
                                </div>
                            </div>

                            {{-- Different shipping address form --}}
                            <div id="shippingAddressForm" style="{{ $shippingMode === 'different' ? '' : 'display:none;' }}">

                                {{-- Saved address picker for shipping --}}
                                @if($addresses->isNotEmpty())
                                <div class="mb-3 filterList">
                                    @foreach($addresses as $addr)
                                    <div class="form-check addressCheck mb-2 border rounded-3 p-3">
                                        <input class="form-check-input shipping-address-toggle"
                                               type="radio"
                                               name="shipping_address_id"
                                               id="shipping_addr_{{ $addr->id }}"
                                               value="{{ $addr->id }}"
                                               {{ old('shipping_address_id') == $addr->id ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="shipping_addr_{{ $addr->id }}">
                                            <strong>{{ $addr->name }}</strong>
                                            <small class="d-block text-secondary">
                                                {{ $addr->address }}{{ $addr->landmark ? ', ' . $addr->landmark : '' }},
                                                {{ $addr->city }}, {{ $addr->state }} – {{ $addr->pincode }}
                                            </small>
                                        </label>
                                    </div>
                                    @endforeach
                                    <div class="form-check addressCheck mb-2 border rounded-3 p-3">
                                        <input class="form-check-input shipping-address-toggle"
                                               type="radio"
                                               name="shipping_address_id"
                                               id="shipping_addr_new"
                                               value="new"
                                               {{ old('shipping_address_id', 'new') === 'new' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shipping_addr_new">
                                            <strong>Enter a new address</strong>
                                        </label>
                                    </div>
                                </div>
                                @endif

                                <div id="shippingFields" class="{{ ($addresses->isNotEmpty() && old('shipping_address_id', 'new') !== 'new') ? 'd-none' : '' }}">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <small>Full Name <span class="txtRed">*</span></small>
                                            <input type="text" class="form-control" name="shipping_name"
                                                   value="{{ old('shipping_name') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <small>Phone <span class="txtRed">*</span></small>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="shipping_phone"
                                                   value="{{ old('shipping_phone') }}"
                                                   required
                                                   pattern="^[0-9]{10}$"
                                                   maxlength="10"
                                                   inputmode="numeric"
                                                   title="Please enter a valid 10 digit phone number"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                        >
                                        </div>
                                   
                                        <div class="col-12 m-0"></div>
                                        <div class="col-md-6">
                                            <small>Address Line 1 <span class="txtRed">*</span></small>
                                            <input type="text" class="form-control" name="shipping_address_line_1"
                                                   value="{{ old('shipping_address_line_1') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <small>Address Line 2</small>
                                            <input type="text" class="form-control" name="shipping_address_line_2"
                                                   value="{{ old('shipping_address_line_2') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <small>State <span class="txtRed">*</span></small>
                                            <select class="form-select" name="shipping_state">
                                                <option value="">Select State</option>
                                                @foreach($states as $state)
                                                    <option value="{{ $state }}"
                                                        {{ old('shipping_state') === $state ? 'selected' : '' }}>
                                                        {{ $state }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <small>City <span class="txtRed">*</span></small>
                                            <input type="text" class="form-control" name="shipping_city"
                                                   value="{{ old('shipping_city') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <small>Pincode <span class="txtRed">*</span></small>
                                            <input type="number" class="form-control" name="shipping_pincode"
                                                   value="{{ old('shipping_pincode') }}">
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input" type="checkbox" name="save_shipping_address" id="save_shipping_address" value="1"
                                                    {{ old('save_shipping_address') ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="save_shipping_address">Save this address</label>
                                                {{-- <button type="button" class="btn btn-primary btn-sm ms-3" id="shippingSaveAddressBtn">Save Address</button> --}}
                                            </div>
                                            {{-- <div class="small mt-1 text-success d-none" id="shippingSaveAddressSuccess"></div>
                                            <div class="small mt-1 text-danger d-none" id="shippingSaveAddressError"></div> --}}
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- ========== PAYMENT METHOD ========== --}}
                        <div class="paymentFormWrap mt-4 pt-4 border-top">
                            <h2 class="fs-6 fw-bold mb-3 border-bottom border-dark pb-2">Payment Method</h2>
                            <div class="filterList d-flex flex-column gap-2">
                                <div class="form-check addressCheck border rounded-3 p-3">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="payment_cod" value="cod"
                                           {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="payment_cod">
                                        <strong>Cash on Delivery (COD)</strong>
                                        <small class="d-block text-secondary">Pay when your order is delivered.</small>
                                    </label>
                                </div>
                                <div class="form-check addressCheck border rounded-3 p-3">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="payment_razorpay" value="razorpay"
                                           {{ old('payment_method') === 'razorpay' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="payment_razorpay">
                                        <strong>Pay Online (Razorpay)</strong>
                                        <small class="d-block text-secondary">UPI, cards, net banking and wallets.</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>

                {{-- ======== RIGHT COLUMN: Order Summary ======== --}}
                <div class="col-lg-4 mb-lg-5">

                    <div class="summaryWrap overflow-hidden rounded-2 shadow-lg">
                        <h3 class="px-4 py-3 swTitle text-white">Order Summary</h3>

                        <div class="px-4 py-3">

                            {{-- Cart items --}}
                            @foreach($cartItems as $cartItem)
                                @if($cartItem->product)
                                @php
                                    $product       = $cartItem->product;
                                    $sellingPrice  = $product->selling_price ?? $product->price ?? 0;
                                    $origPrice     = $product->price ?? 0;
                                    $lineTotal     = $sellingPrice * $cartItem->quantity;
                                    $itemDiscount  = $product->discount ?? 0;
                                    if (!$itemDiscount && $origPrice > $sellingPrice && $origPrice > 0) {
                                        $itemDiscount = (int) round((($origPrice - $sellingPrice) / $origPrice) * 100);
                                    }
                                    $productImage = method_exists($product, 'getProductImageUrl')
                                        ? $product->getProductImageUrl($product->thumbnail)
                                        : asset('frontend/assets/images/no-image.png');
                                @endphp
                                <div class="d-flex w-100 mb-3 gap-3 align-items-start checkoutSummaryItem">
                                    <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="flex-shrink-0">
                                        <img src="{{ $productImage }}" alt="{{ $product->name }}" class="checkoutSummaryImg bgiImg" style="width:60px;height:60px;object-fit:contain;">
                                    </a>
                                    <div class="flex-grow-1 min-w-0">
                                        <a href="{{ route('product.details', ['slug' => $product->slug]) }}"
                                            class="checkoutSummaryName d-block fw-semibold text-decoration-none mb-1"
                                            title="{{ $product->name }}">
                                             {{ $product->name }}
                                         </a>
                                        <div class="d-flex flex-wrap align-items-center gap-2 small text-secondary mb-1">
                                            <span>Qty: {{ $cartItem->quantity }}</span>
                                            @if($itemDiscount > 0)
                                                <span class="txtRed fw-semibold">{{ $itemDiscount }}% Off</span>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            @if($origPrice > $sellingPrice)
                                                <span class="pip_ProdStrike small">₹{{ number_format($origPrice, 2) }}</span>
                                            @endif
                                            <strong class="prod_Price" style="font-size:1rem;">₹{{ number_format($lineTotal, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach

                            <hr class="border-secondary-subtle my-3">

                            <div class="d-flex w-100 mb-3 swItem">
                                <div class="fw-medium">Subtotal</div>
                                <div class="ms-auto fw-semibold"><span class="price">₹{{ number_format($subtotal, 2) }}</span></div>
                            </div>

                            <div class="d-flex w-100 mb-3 swItem">
                                <div class="fw-medium">Shipping Charges</div>
                                <div class="ms-auto fw-semibold">
                                    @if($shippingCharge <= 0)
                                        <span class="txtRed">Free</span>
                                    @else
                                        <span class="pip_ProdStrike price">₹{{ number_format($shippingCharge, 2) }}</span>
                                    @endif
                                </div>
                            </div>

                        {{-- Promo Code --}}
                        <div class="swPromoBox mb-3">
                            <div class="swPromoForm">

                                {{-- Promo Header --}}
                                <div class="fw-medium d-flex flex-nowrap align-items-center swPromoHead"
                                    role="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#promoCheckout"
                                    aria-expanded="{{ !empty($couponCode) ? 'true' : 'false' }}">

                                    <svg class="promoIcon"
                                        viewBox="0 0 512 512"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor">
                                        <circle cx="256.001" cy="346.888" r="17.133"></circle>
                                        <circle cx="256.001" cy="165.11" r="17.133"></circle>
                                        <path d="m498.198 13.815c-8.897-8.909-20.734-13.815-33.331-13.815h-179.488c-12.591 0-24.429 4.904-33.333 13.809l-238.235 238.235c-8.907 8.906-13.811 20.743-13.811 33.331 0 12.59 4.906 24.427 13.811 33.326l179.484 179.485c8.902 8.908 20.739 13.814 33.329 13.814 12.589 0 24.426-4.904 33.332-13.811l238.235-238.235c8.905-8.904 13.809-20.742 13.809-33.333v-179.488c0-12.597-4.906-24.435-13.802-33.318zm-242.197 104.162c25.989 0 47.133 21.144 47.133 47.133s-21.144 47.133-47.133 47.133-47.133-21.144-47.133-47.133 21.144-47.133 47.133-47.133zm0 276.044c-25.989 0-47.133-21.144-47.133-47.133s21.144-47.133 47.133-47.133 47.133 21.144 47.133 47.133-21.144 47.133-47.133 47.133zm96.399-123.022h-192.798c-8.284 0-15-6.716-15-15s6.716-15 15-15h192.798c8.284 0 15 6.716 15 15s-6.715 15-15 15zm64.267-160.666c-8.28 0-15-6.72-15-15s6.72-15 15-15 15 6.72 15 15-6.72 15-15 15z"></path>
                                    </svg>

                                    <span>Do you have a Promo Code?</span>
                                </div>

                                {{-- Promo Content --}}
                                <div class="collapse {{ !empty($couponCode) ? 'show' : '' }}"
                                    id="promoCheckout">

                                    {{-- Applied Coupon --}}
                                    <div id="promoAppliedBox"
                                        class="{{ !empty($couponCode) ? '' : 'd-none' }}">

                                        <div class="d-flex align-items-center justify-content-between mt-2">

                                            <div class="ps-2">
                                                <strong id="appliedCouponCode">
                                                    {{ $couponCode }}
                                                </strong>

                                                <small class="d-block" id="appliedCouponValue">
                                                    @if($coupon && $coupon->type === 'percent')
                                                        {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}% off
                                                    @elseif($coupon)
                                                        ₹{{ number_format($coupon->value, 2) }} off
                                                    @endif
                                                </small>
                                            </div>

                                            <button type="button"
                                                    class="btn btn-sm"
                                                    id="removePromoBtn">
                                                Remove
                                            </button>

                                        </div>
                                    </div>

                                    {{-- Enter Promo Code --}}
                                    <form id="promoApplyForm"
                                        class="input-group mt-2 {{ !empty($couponCode) ? 'd-none' : '' }}">

                                        <input type="text"
                                            name="coupon_code"
                                            class="form-control nlInput"
                                            placeholder="Enter Promo Code"
                                            value="{{ old('coupon_code') }}"
                                            required>

                                        <button type="submit"
                                                class="promoApplyBtn">
                                            APPLY
                                        </button>

                                    </form>

                                    <div class="small mt-2 text-danger d-none"
                                        id="promoError"></div>

                                    <div class="small mt-2 text-success d-none"
                                        id="promoSuccess"></div>

                                    <button type="button"
                                            class="btn btn-link p-0 mt-2 txtRed text-decoration-none fw-semibold"
                                            data-bs-toggle="modal"
                                            id="showCouponModalBtn"
                                            data-bs-target="#couponModal">
                                        View available coupons
                                    </button>

                                </div>
                            </div>
                        </div>

                            {{-- Discount row --}}
                            <div id="discountRow" class="d-flex w-100 mb-3 swItem {{ ($discount ?? 0) > 0 ? '' : 'd-none' }}">
                                <div class="fw-medium txtRed">Discount <span id="discountCodeText">{{ !empty($couponCode) ? '(' . $couponCode . ')' : '' }}</span></div>
                                <div class="ms-auto fw-semibold txtRed" id="discountAmountText">-₹{{ number_format($discount ?? 0, 2) }}</div>
                            </div>

                            <div class="d-flex w-100 mb-3 swItem">
                                <div class="fw-medium" id="gstLabel">{{ $gstRateLabel ?? 'GST (inclusive)' }}</div>
                                <div class="ms-auto fw-semibold"><span class="price" id="gstAmount">₹{{ number_format($gstTotal, 2) }}</span></div>
                            </div>

                            <div class="d-flex w-100 mb-3 swItem total">
                                <div class="">Total</div>
                                <div class="ms-auto"><span class="price" id="grandTotalAmount">₹{{ number_format($grandTotal, 2) }}</span></div>
                            </div>

                            <div class="w-100 mt-5 d-flex justify-content-center">
                                <button type="submit" form="checkoutForm" class="ctaBtn" id="checkoutSubmitBtn">
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

    {{-- Coupon Modal --}}
    <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
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
                            // Passing button colorize variables to partial
                            'btnClasses' => [
                                'apply' => 'coupon-apply-btn apply-blue btn btn-sm rounded-pill me-2',
                                'applied' => 'coupon-apply-btn applied-red btn btn-sm rounded-pill me-2'
                            ]
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="checkoutPageLoader" id="checkoutPageLoader" aria-live="polite" aria-busy="true">
        <div class="checkoutPageLoaderInner">
            <div class="checkoutPageLoaderSpinner"></div>
            <p class="checkoutPageLoaderText">Placing your order...</p>
        </div>
    </div>

</main>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    var billingFields      = document.getElementById('billingFields');
    var shippingAddressForm = document.getElementById('shippingAddressForm');
    var shippingFields     = document.getElementById('shippingFields');
    var billingToggles     = document.querySelectorAll('.billing-address-toggle');
    var shippingRadios     = document.querySelectorAll('.shipping-toggle');
    var shippingToggles    = document.querySelectorAll('.shipping-address-toggle');
    var checkoutForm       = document.getElementById('checkoutForm');
    var checkoutSubmitBtn  = document.getElementById('checkoutSubmitBtn');
    var checkoutPageLoader = document.getElementById('checkoutPageLoader');
    var csrfToken          = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function showCheckoutLoader() {
        if (checkoutPageLoader) checkoutPageLoader.classList.add('is-visible');
        if (checkoutSubmitBtn) checkoutSubmitBtn.disabled = true;
    }

    function hideCheckoutLoader() {
        if (checkoutPageLoader) checkoutPageLoader.classList.remove('is-visible');
        if (checkoutSubmitBtn) checkoutSubmitBtn.disabled = false;
    }

    // -------- billing same/diff address toggle --------
    function toggleBillingFields() {
        var selected = document.querySelector('.billing-address-toggle:checked');
        var isNew    = !selected || selected.value === 'new';
        if (billingFields) {
            billingFields.classList.toggle('d-none', !isNew);
            setRequired(billingFields, isNew);
        }
    }

    // -------- shipping same/diff address toggle --------
    function toggleShippingFields() {
        if (!shippingAddressForm) return;
        var modeEl      = document.querySelector('.shipping-toggle:checked');
        var isDifferent = modeEl && modeEl.value === 'different';

        shippingAddressForm.style.display = isDifferent ? '' : 'none';

        if (!isDifferent) {
            setRequired(shippingFields, false);
            return;
        }

        var selShip = document.querySelector('.shipping-address-toggle:checked');
        var isNew   = !selShip || selShip.value === 'new';
        if (shippingFields) {
            shippingFields.classList.toggle('d-none', !isNew);
            setRequired(shippingFields, isNew);
        }
    }

    function setRequired(container, enabled) {
        if (!container) return;
        container.querySelectorAll('input, select').forEach(function (f) {
            if (f.type === 'checkbox' || f.type === 'radio' || f.type === 'hidden') return;
            if (f.name && f.name.indexOf('address_line_2') !== -1) { f.required = false; return; }
            f.required = enabled;
        });
    }

    billingToggles.forEach(function (r) { r.addEventListener('change', toggleBillingFields); });
    shippingRadios.forEach(function (r)  { r.addEventListener('change', toggleShippingFields); });
    shippingToggles.forEach(function (r) { r.addEventListener('change', toggleShippingFields); });

    toggleBillingFields();
    toggleShippingFields();

    // -------- Promo code --------
    var promoApplyForm    = document.getElementById('promoApplyForm');
    var promoAppliedBox   = document.getElementById('promoAppliedBox');
    var promoError        = document.getElementById('promoError');
    var promoSuccess      = document.getElementById('promoSuccess');
    var removePromoBtn    = document.getElementById('removePromoBtn');
    var discountRow       = document.getElementById('discountRow');
    var discountAmountText = document.getElementById('discountAmountText');
    var discountCodeText  = document.getElementById('discountCodeText');
    var gstAmountEl       = document.getElementById('gstAmount');
    var gstLabelEl        = document.getElementById('gstLabel');
    var grandTotalEl      = document.getElementById('grandTotalAmount');
    var appliedCouponCode = document.getElementById('appliedCouponCode');
    var appliedCouponValue = document.getElementById('appliedCouponValue');

    function updateSummary(data) {
        if ('discount' in data) {
            if (parseFloat(data.discount) > 0) {
                discountRow.classList.remove('d-none');
                discountAmountText.textContent = '-₹' + Number(data.discount).toFixed(2);
                discountCodeText.textContent   = data.coupon_code ? '(' + data.coupon_code + ')' : '';
            } else {
                discountRow.classList.add('d-none');
            }
        }
        if ('gstTotal'    in data) gstAmountEl.textContent  = '₹' + Number(data.gstTotal).toFixed(2);
        if ('gstRateLabel' in data) gstLabelEl.textContent  = data.gstRateLabel;
        if ('grandTotal'  in data) grandTotalEl.textContent = '₹' + Number(data.grandTotal).toFixed(2);
    }

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

            // Ensure that the Blade directive is not causing unclosed parentheses or PHP syntax issues
            fetch("{{ route('cart.coupon.apply') }}", {
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
                promoApplyForm.querySelector('.promoApplyBtn').disabled = false;
                if (data.status) {
                    promoApplyForm.classList.add('d-none');
                    promoAppliedBox.classList.remove('d-none');
                    appliedCouponCode.textContent  = data.coupon_code;
                    appliedCouponValue.textContent = data.coupon_type === 'percent'
                        ? String(data.coupon_value).replace(/\.00$/, '') + '% off'
                        : '₹' + Number(data.coupon_value).toFixed(2) + ' off';
                    promoSuccess.textContent = data.message ? data.message : 'Coupon applied!';
                    promoSuccess.classList.remove('d-none');
                    updateSummary(data);

                    // Update coupon list buttons in the modal for their color/status
                    document.querySelectorAll('.coupon-apply-btn').forEach(function(btn){
                        var btnCode = btn.getAttribute('data-code');
                        if (data.coupon_code && btnCode && btnCode === data.coupon_code) {
                            btn.classList.remove('apply-blue');
                            btn.classList.add('applied-red');
                            btn.textContent = 'Applied';
                            btn.disabled = true;
                        } else {
                            btn.classList.remove('applied-red');
                            btn.classList.add('apply-blue');
                            btn.textContent = 'Apply';
                            btn.disabled = false;
                        }
                    });

                } else {
                    promoError.textContent = data.message || 'Invalid or expired code.';
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
                    promoSuccess.textContent = data.message ?? 'Promo code removed.';
                    promoSuccess.classList.remove('d-none');
                    updateSummary(data);

                    // update coupon modal buttons
                    document.querySelectorAll('.coupon-apply-btn').forEach(function(btn){
                        btn.classList.remove('applied-red');
                        btn.classList.add('apply-blue');
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
                promoError.textContent = 'Failed to remove coupon. Try again.';
                promoError.classList.remove('d-none');
            });
        });
    }

    function bindCouponApplyButtons() {
        document.querySelectorAll('.coupon-apply-btn').forEach(function (btn) {
            if (btn.dataset.bound === '1') return;
            btn.dataset.bound = '1';

            // Set correct classes for initial state
            var code = btn.getAttribute('data-code');
            var applied = false;
            @if (!empty($couponCode))
                applied = (btn.getAttribute('data-code') === "{{ $couponCode }}");
            @endif
            if (applied) {
                btn.classList.remove('apply-blue');
                btn.classList.add('applied-red');
                btn.textContent = 'Applied';
                btn.disabled = true;
            } else {
                btn.classList.remove('applied-red');
                btn.classList.add('apply-blue');
                btn.textContent = 'Apply';
                btn.disabled = false;
            }

            btn.addEventListener('click', function () {
                var codeInput = document.querySelector('#promoApplyForm input[name="coupon_code"]');
                if (codeInput) {
                    codeInput.value = btn.getAttribute('data-code');
                }
                var promoCollapse = document.getElementById('promoCheckout');
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

    // -------- Razorpay submit intercept --------
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (event) {
            var paymentMethod = checkoutForm.querySelector('input[name="payment_method"]:checked');
            if (!checkoutForm.reportValidity()) {
                event.preventDefault();
                return;
            }

            if (!paymentMethod || paymentMethod.value !== 'razorpay') {
                showCheckoutLoader();
                return;
            }

            event.preventDefault();
            showCheckoutLoader();

            fetch(checkoutForm.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(checkoutForm),
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (res) {
                if (!res.ok || !res.data.status || !res.data.razorpay) {
                    throw new Error(res.data.message || 'Unable to start payment.');
                }
                var rp = res.data.razorpay;
                hideCheckoutLoader();
                var options = {
                    key: rp.key,
                    amount: rp.amount,
                    currency: rp.currency,
                    name: rp.name,
                    description: rp.description,
                    order_id: rp.order_id,
                    prefill: rp.prefill || {},
                    theme: rp.theme || { color: '#bf9a7c' },
                    handler: function (response) {
                        showCheckoutLoader();
                        fetch('{{ route("checkout.razorpay.verify") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({
                                order_id: res.data.order_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature,
                            }),
                        })
                        .then(function (vr) { return vr.json().then(function (vd) { return { ok: vr.ok, data: vd }; }); })
                        .then(function (v) {
                            if (v.ok && v.data.status && v.data.redirect_url) {
                                window.location.href = v.data.redirect_url;
                            } else {
                                throw new Error(v.data.message || 'Payment verification failed.');
                            }
                        })
                        .catch(function (err) {
                            alert(err.message || 'Payment verification failed.');
                            hideCheckoutLoader();
                        });
                    },
                    modal: {
                        ondismiss: function () {
                            hideCheckoutLoader();
                        }
                    }
                };
                new Razorpay(options).open();
            })
            .catch(function (err) {
                alert(err.message || 'Unable to place order. Please try again.');
                hideCheckoutLoader();
            });
        });
    }

});
</script>
@endpush
