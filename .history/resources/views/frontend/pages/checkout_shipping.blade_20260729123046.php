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

                   <div class="billingFormWrap mb-4 pb-4 border-bottom">
                      <h2 class="sectSubtitle fw-semibold mb-2">Billing Address</h2>
                      <p class="text-secondary small mb-3">Choose a saved address or enter a new billing address.</p>

                      @if($addresses->isNotEmpty())
                      <div class="mb-4 filterList d-flex flex-column gap-2" id="billingAddressList">
                         @foreach($addresses as $address)
                         <div class="form-check addressCheck border rounded-3 p-3">
                            <input class="form-check-input billing-address-toggle" type="radio"
                               name="billing_address_id" id="billing_addr_{{ $address->id }}"
                               value="{{ $address->id }}"
                               data-name="{{ $address->name }}"
                               data-phone="{{ $address->phone }}"
                               data-line1="{{ $address->address }}"
                               data-line2="{{ $address->landmark }}"
                               data-state="{{ $address->state }}"
                               data-city="{{ $address->city }}"
                               data-pincode="{{ $address->pincode }}"
                               {{ (string) $selectedBillingId === (string) $address->id ? 'checked' : '' }}>
                            <label class="form-check-label w-100" for="billing_addr_{{ $address->id }}">
                               <strong>{{ $address->name }}</strong>
                               @if($address->is_default)
                                  <span class="badge bgOrange ms-1 lh-1">Default</span>
                               @endif
                               <address class="mb-0 small text-secondary">{{ $address->formatted_address }}</address>
                               @if($address->phone)
                                  <small class="text-secondary">Phone: {{ $address->phone }}</small>
                               @endif
                            </label>
                         </div>
                         @endforeach
                         <div class="form-check addressCheck border rounded-3 p-3">
                            <input class="form-check-input billing-address-toggle" type="radio"
                               name="billing_address_id" id="billing_addr_new" value="new"
                               {{ (string) $selectedBillingId === 'new' ? 'checked' : '' }}>
                            <label class="form-check-label" for="billing_addr_new">Enter a new billing address</label>
                         </div>
                      </div>
                      @else
                         <input type="hidden" name="billing_address_id" value="new">
                      @endif

                      <div id="billingFields" class="{{ $addresses->isNotEmpty() && (string) $selectedBillingId !== 'new' ? 'd-none' : '' }}">
                         <div class="row g-3">
                            <div class="col-md-6">
                               <small class="text-secondary">Full Name <span class="txtred">*</span></small>
                               <input type="text" class="form-control billing-field" name="billing_name" id="billing_name"
                                  value="{{ old('billing_name', $defaultAddress->name ?? $user->name) }}" />
                            </div>
                            <div class="col-md-6">
                               <small class="text-secondary">Mobile No. <span class="txtred">*</span></small>
                               <input type="tel" class="form-control billing-field" name="billing_phone" id="billing_phone"
                                  value="{{ old('billing_phone', $defaultAddress->phone ?? $user->phone) }}" />
                            </div>
                            <div class="col-12 m-0"></div>
                            <div class="col-md-6">
                               <small class="text-secondary">Address Line 1 <span class="txtred">*</span></small>
                               <input type="text" class="form-control billing-field" name="billing_address_line_1" id="billing_address_line_1"
                                  value="{{ old('billing_address_line_1', $defaultAddress->address ?? '') }}" />
                            </div>
                            <div class="col-md-6">
                               <small class="text-secondary">Address Line 2</small>
                               <input type="text" class="form-control billing-field" name="billing_address_line_2" id="billing_address_line_2"
                                  value="{{ old('billing_address_line_2', $defaultAddress->landmark ?? '') }}" />
                            </div>
                            <div class="col-md-6">
                               <small class="text-secondary">State <span class="txtred">*</span></small>
                               <select class="form-select billing-field" name="billing_state" id="billing_state">
                                  <option value="">Select State</option>
                                  @foreach($states as $state)
                                     <option value="{{ $state }}" {{ old('billing_state', $defaultAddress->state ?? '') === $state ? 'selected' : '' }}>{{ $state }}</option>
                                  @endforeach
                               </select>
                            </div>
                            <div class="col-md-6">
                               <small class="text-secondary">City <span class="txtred">*</span></small>
                               <input type="text" class="form-control billing-field" name="billing_city" id="billing_city"
                                  value="{{ old('billing_city', $defaultAddress->city ?? '') }}" />
                            </div>
                            <div class="col-md-6">
                               <small class="text-secondary">Pincode <span class="txtred">*</span></small>
                               <input type="text" class="form-control billing-field" name="billing_pincode" id="billing_pincode"
                                  value="{{ old('billing_pincode', $defaultAddress->pincode ?? '') }}" maxlength="6" />
                            </div>
                            @if($addresses->isEmpty())
                            <div class="col-md-6 d-flex align-items-center">
                               <div class="filterList d-flex flex-column gap-2 mt-3">
                                  <div class="d-flex gap-2 align-items-center">
                                     <input type="checkbox" name="save_billing_address" id="saveBillingAddress" value="1" {{ old('save_billing_address', '1') ? 'checked' : '' }}>
                                     <label for="saveBillingAddress">Save to my addresses</label>
                                  </div>
                                  <div class="d-flex gap-2 align-items-center">
                                     <input type="checkbox" name="billing_is_default" id="billingMarkDefault" value="1" {{ old('billing_is_default', '1') ? 'checked' : '' }}>
                                     <label for="billingMarkDefault">Mark as Default</label>
                                  </div>
                               </div>
                            </div>
                            @else
                            <div class="col-md-6 d-flex align-items-center">
                               <div class="filterList d-flex flex-column gap-2 mt-3">
                                  <div class="d-flex gap-2 align-items-center">
                                     <input type="checkbox" name="save_billing_address" id="saveBillingAddress" value="1" {{ old('save_billing_address', '1') ? 'checked' : '' }}>
                                     <label for="saveBillingAddress">Save to my addresses</label>
                                  </div>
                                  <div class="d-flex gap-2 align-items-center">
                                     <input type="checkbox" name="billing_is_default" id="billingMarkDefault" value="1" {{ old('billing_is_default') ? 'checked' : '' }}>
                                     <label for="billingMarkDefault">Mark as Default</label>
                                  </div>
                               </div>
                            </div>
                            @endif
                         </div>
                      </div>
                   </div>

                   <div class="shippingFormWrap">
                      <h2 class="sectSubtitle fw-semibold mb-2">Shipping Address</h2>
                      <div class="mb-4 filterList">
                         <div class="form-check form-check-inline addressCheck">
                            <input class="form-check-input shipping-toggle" type="radio" id="shipping_sameAddress" name="shipping_same_diff_address" value="same" {{ $shippingMode === 'same' ? 'checked' : '' }}>
                            <label class="form-check-label" for="shipping_sameAddress">Same as billing address</label>
                         </div>
                         <div class="form-check form-check-inline addressCheck">
                            <input class="form-check-input shipping-toggle" type="radio" id="shipping_diffAddress" name="shipping_same_diff_address" value="different" {{ $shippingMode === 'different' ? 'checked' : '' }}>
                            <label class="form-check-label" for="shipping_diffAddress">Use a different shipping address</label>
                         </div>
                      </div>

                      <div id="shippingAddressForm" class="{{ $shippingMode === 'different' ? '' : 'd-none' }}">
                         @if($addresses->isNotEmpty())
                         <div class="mb-4 filterList d-flex flex-column gap-2" id="shippingAddressList">
                            @foreach($addresses as $address)
                            <div class="form-check addressCheck border rounded-3 p-3">
                               <input class="form-check-input shipping-address-toggle" type="radio"
                                  name="shipping_address_id" id="shipping_addr_{{ $address->id }}"
                                  value="{{ $address->id }}"
                                  data-name="{{ $address->name }}"
                                  data-phone="{{ $address->phone }}"
                                  data-line1="{{ $address->address }}"
                                  data-line2="{{ $address->landmark }}"
                                  data-state="{{ $address->state }}"
                                  data-city="{{ $address->city }}"
                                  data-pincode="{{ $address->pincode }}"
                                  {{ (string) $selectedShippingId === (string) $address->id ? 'checked' : '' }}>
                               <label class="form-check-label w-100" for="shipping_addr_{{ $address->id }}">
                                  <strong>{{ $address->name }}</strong>
                                  @if($address->is_default)
                                     <span class="badge bgOrange ms-1 lh-1">Default</span>
                                  @endif
                                  <address class="mb-0 small text-secondary">{{ $address->formatted_address }}</address>
                                  @if($address->phone)
                                     <small class="text-secondary">Phone: {{ $address->phone }}</small>
                                  @endif
                               </label>
                            </div>
                            @endforeach
                            <div class="form-check addressCheck border rounded-3 p-3">
                               <input class="form-check-input shipping-address-toggle" type="radio"
                                  name="shipping_address_id" id="shipping_addr_new" value="new"
                                  {{ (string) $selectedShippingId === 'new' || !$selectedShippingId ? 'checked' : '' }}>
                               <label class="form-check-label" for="shipping_addr_new">Enter a new shipping address</label>
                            </div>
                         </div>
                         @else
                            <input type="hidden" name="shipping_address_id" value="new">
                         @endif

                         <div id="shippingFields" class="{{ $addresses->isNotEmpty() && (string) $selectedShippingId !== 'new' && $selectedShippingId ? 'd-none' : '' }}">
                            <div class="row g-3">
                               <div class="col-md-6">
                                  <small class="text-secondary">Full Name <span class="txtred">*</span></small>
                                  <input type="text" class="form-control shipping-field" name="shipping_name" id="shipping_name" value="{{ old('shipping_name') }}" />
                               </div>
                               <div class="col-md-6">
                                  <small class="text-secondary">Mobile No. <span class="txtred">*</span></small>
                                  <input type="tel" class="form-control shipping-field" name="shipping_phone" id="shipping_phone" value="{{ old('shipping_phone') }}" />
                               </div>
                               <div class="col-12 m-0"></div>
                               <div class="col-md-6">
                                  <small class="text-secondary">Address Line 1 <span class="txtred">*</span></small>
                                  <input type="text" class="form-control shipping-field" name="shipping_address_line_1" id="shipping_address_line_1" value="{{ old('shipping_address_line_1') }}" />
                               </div>
                               <div class="col-md-6">
                                  <small class="text-secondary">Address Line 2</small>
                                  <input type="text" class="form-control shipping-field" name="shipping_address_line_2" id="shipping_address_line_2" value="{{ old('shipping_address_line_2') }}" />
                               </div>
                               <div class="col-md-6">
                                  <small class="text-secondary">State <span class="txtred">*</span></small>
                                  <select class="form-select shipping-field" name="shipping_state" id="shipping_state">
                                     <option value="">Select State</option>
                                     @foreach($states as $state)
                                        <option value="{{ $state }}" {{ old('shipping_state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                                     @endforeach
                                  </select>
                               </div>
                               <div class="col-md-6">
                                  <small class="text-secondary">City <span class="txtred">*</span></small>
                                  <input type="text" class="form-control shipping-field" name="shipping_city" id="shipping_city" value="{{ old('shipping_city') }}" />
                               </div>
                               <div class="col-md-6">
                                  <small class="text-secondary">Pincode <span class="txtred">*</span></small>
                                  <input type="text" class="form-control shipping-field" name="shipping_pincode" id="shipping_pincode" value="{{ old('shipping_pincode') }}" maxlength="6" />
                               </div>
                               <div class="col-md-6 d-flex align-items-center">
                                  <div class="filterList d-flex flex-column gap-2 mt-3">
                                     <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" name="save_address" id="saveAddress" value="1" {{ old('save_address', '1') ? 'checked' : '' }}>
                                        <label for="saveAddress">Save to my addresses</label>
                                     </div>
                                     <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" name="is_default" id="markDefault" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                        <label for="markDefault">Mark as Default</label>
                                     </div>
                                  </div>
                               </div>
                            </div>
                         </div>
                      </div>
                   </div>

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
                               @if(!empty($couponCode))
                                  <div class="d-flex align-items-center justify-content-between mt-2 p-2 rounded-pill bg-white border">
                                     <div class="ps-2">
                                        <strong class="txtorange">{{ $couponCode }}</strong>
                                        <small class="d-block text-secondary">
                                           @if($coupon && $coupon->type === 'percent')
                                              {{ rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') }}% off
                                           @elseif($coupon)
                                              ₹{{ number_format($coupon->value, 2) }} off
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
                         <div class="ms-auto fw-semibold"> <span class="price">₹{{ number_format($gstTotal, 2) }}</span> </div>
                      </div>

                      <div class="d-flex w-100 mb-2 swItem">
                         <div class=" fw-bold">Total</div>
                         <div class="ms-auto fw-bold"> <span class="price">₹{{ number_format($grandTotal, 2) }}</span> </div>
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
             <div class="modal-body">
                <p class="text-secondary small mb-3">
                   Coupons are calculated on selling price. Minimum order and maximum discount rules are checked against your order.
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
                   </div>
                @empty
                   <div class="text-center py-4 text-secondary">
                      No coupons available for the products in your order.
                   </div>
                @endforelse
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
