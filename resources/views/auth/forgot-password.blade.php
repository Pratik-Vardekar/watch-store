@extends('layouts.front')

@section('title', 'Forgot Password')

@section('content')
<main class="pageWrapper d-flex">

   <section class="w-100 d-flex position-relative">
      
      <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

      <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

         <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

            <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center" style="background: #fff;">
               <h1 class="resetTitle mb-3 d-inline-flex align-items-center gap-3">Reset Password</h1>
               <small class="mb-4 d-block lh-sm custom-desc">
                   Enter your email id below to receive a password reset link.
               </small>

               @if (session('status'))
                   <div class="alert alert-success mb-3" role="alert">
                       {{ session('status') }}
                   </div>
               @endif

               <form method="POST" action="{{ route('password.email') }}" class="row g-3 formFormat">
                   @csrf

                  <div class="col-12">
                     <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror emailInputBox" 
                        name="email" 
                        id="email" 
                        placeholder="Email id"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        style="background:#e8f0fe;"
                     />
                     @error('email')
                         <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                     @enderror
                  </div>

                  <div class="col-12 text-center">
                    <button type="submit" class="ctaBtn">Submit</button>
                  </div>

                  <div class="col-12 mt-3 text-center">
                     <a href="{{ route('login') }}" class="fw-medium">Back to Sign In</a>
                  </div>
               </form>
            </div>

            <div class="col-md-6 col-12 d-flex justify-content-center align-items-center bgGray loginImg">
               <iframe src="{{ asset('frontend/assets/watch/index.html') }}" class="watchFrame" title="Watch Display"></iframe>
            </div>

         </div>
            
      </div>

   </section>         

</main>
@endsection
