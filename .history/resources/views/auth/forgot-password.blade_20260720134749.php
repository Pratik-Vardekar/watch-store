@extends('layouts.front')

@section('title', 'Reset Password')

@section('content')
<main class="pageWrapper loginMainWrap">         

    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 mb-5 overflow-hidden">
       <div class="container py-5">

          <div class="rounded-4 animateThis slideTop loginWrap shadow-lg p-5 mx-auto">
             
             <h1 class="sectTitle mb-3 lh-1 fw-bold animateThis slideTop">Reset Password</h1>
             <p>Please enter your email address below to receive a password reset link.</p>

             <form method="POST" action="{{ route('password.email') }}" class="row g-3 formFormat animateThis fadeGrow">
                @csrf
                <div class="col-12">
                   <input
                      type="email"
                      class="form-control @error('email') is-invalid @enderror"
                      name="email"
                      id="email"
                      placeholder="Email Id"
                      value="{{ old('email') }}"
                      required
                      autofocus
                   />
                   @error('email')
                      <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                   @enderror
                </div>
                <div class="col-12 text-center">
                   <button type="submit" class="ctaBtn">
                      <span class="btnTxt">Submit</span>
                      <span class="arw"></span>
                   </button>
                </div>
                <div class="col-12 mt-4 text-center">
                   <a href="{{ route('login') }}" class="fw-medium">Back to Sign In</a>
                </div>
             </form>

          </div>

       </div>
    </section>

</main>
@endsection
