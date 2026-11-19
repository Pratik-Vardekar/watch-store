@extends('layouts.front')
@section('content')
<main class="pageWrapper">         

    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 overflow-hidden">
       <div class="container">

          <div class="text-center mb-0 animateThis slideTop">
             <nav class="breadcrumb justify-content-center mb-0 fw-semibold">
                <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                <span class="breadcrumb-item active" aria-current="page">Contact Us</span>
             </nav>
             <h1 class="sectTitle fw-bold txtred animateThis slideTop">Contact Us</h1>
          </div>

          <div class="row g-md-5 g-4">
             <div class="col-lg-3 formFormat animateThis slideRight">

                <address class="mb-3">
                   <small class="d-block txtteal text-uppercase" style="font-size:.75rem;">Address</small>
                   @if($contactSetting->contact_address)
                      <p>{!! nl2br(e($contactSetting->contact_address)) !!}</p>
                   @else
                      <strong>Cheriks Innovative Ventures Pvt. Ltd.</strong>
                      <p>D-201, AcharatBaug,<br> Phirozshah Mehta Road, Santacruz West,<br> Mumbai - 400 054. INDIA</p>
                   @endif
                </address>

                <div class="mb-3">
                   <small class="d-block txtteal text-uppercase" style="font-size:.75rem;">Email</small>
                   <a href="mailto:{{ $contactSetting->contact_email ?? 'cheriksinnovativeventures@gmail.com' }}">
                      {{ $contactSetting->contact_email ?? 'cheriksinnovativeventures@gmail.com' }}
                   </a>
                </div>

                <div class="mb-3">
                   <small class="d-block txtteal text-uppercase" style="font-size:.75rem;">Contact No.</small>
                   @php $phone = $contactSetting->contact_phone ?? '+91 98922 51015'; @endphp
                   <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
                </div>

                <div class="mb-3">
                   <small class="d-block txtteal text-uppercase" style="font-size:.75rem;">Working Hours</small>
                   <p>
                      {{ $contactSetting->business_days ?? 'Monday to Saturday' }}
                      <br>
                      {{ $contactSetting->business_hours ?? '10.00am - 07.00pm' }}
                   </p>
                </div>

             </div>
             <div class="col-lg animateThis curtainLeft">
                <div class="mapWrapper rounded-4 w-100 border border-3 border-warning" id="mapLocation"></div>
             </div>
          </div>

       </div>
    </section>

 </main>
@endsection
