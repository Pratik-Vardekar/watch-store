@extends('layouts.front')

@push('styles')
<style>
    .orderSuccessMark {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .75rem;
        margin: 0 auto 1.5rem;
    }
    .orderSuccessCheck {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 50%;
        background: #e31c23;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(227, 28, 35, .28);
    }
    .orderSuccessLogo {
        width: 210px;
        height: auto;
        max-width: 80%;
        display: block;
    }
</style>
@endpush

@section('content')
<main class="pageWrapper loginMainWrap">         

    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 mb-5 overflow-hidden">
       <div class="container">

          <div class="mx-auto text-center">
             <div class="orderSuccessMark">
                <div class="orderSuccessCheck" aria-hidden="true">
                </div>
                <img
                   src="{{ asset('frontend/assets/images/tws_logo.png') }}"
                   onerror="this.onerror=null;this.src='{{ asset('backend/assets/images/tws_logo.png') }}';"
                   class="orderSuccessLogo"
                   alt="The Watch Store"
                   loading="eager"
                >
             </div>
             @if($order)
             <p><span class="d-inline-block bgRed text-white rounded-pill px-3">Order Id : <strong>{{ $order->order_id }}</strong></span></p>
             <h2 class="sectSubtitle fw-bold">Your order has been placed successfully!</h2>
             <p>
                Order total: <strong>₹{{ number_format($order->grand_total, 2) }}</strong>.
                @if(($order->payment_method ?? '') === 'razorpay')
                   Payment: <strong>{{ ucfirst($order->payment_status ?? 'pending') }}</strong> via Razorpay.
                @else
                   Payment: <strong>Cash on Delivery</strong>.
                @endif
                You'll get a confirmation email soon. We are already packing your items with care.
             </p>
             <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                <a href="{{ route('profile.orders') }}" class="ctaBtn">
                   <span class="btnTxt">View My Orders</span>
                   <span class="arw"></span>
                </a>
                <a href="{{ route('home') }}" class="ctaBtn">
                   <span class="btnTxt">Continue Shopping</span>
                   <span class="arw"></span>
                </a>
             </div>
             @else
             <h2 class="sectSubtitle fw-bold">Thank you for shopping with us!</h2>
             <p>Your order details are not available right now.</p>
             <a href="{{ route('home') }}" class="ctaBtn mt-4">
                <span class="btnTxt">Continue Shopping</span>
                <span class="arw"></span>
             </a>
             @endif
         </div>
          
       </div>
    </section>

 </main>
 @endsection
