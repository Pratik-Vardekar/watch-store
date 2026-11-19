@extends('layouts.front')
@section('content')

<main class="pageWrapper">

   <section class="py-5">
      <div class="container">

          <div class="row justify-content-between mb-5">
              @include('frontend.partials.myaccount_nav', ['active' => 'password', 'user' => $user])

              <div class="col-lg-9 mb-lg-4">

                  <h2 class="sectTitle lh-1 mb-4">Change Password</h2>

                  @if(session('success'))
                      <div class="alert alert-success">{{ session('success') }}</div>
                  @endif
                  @if(session('error'))
                      <div class="alert alert-danger">{{ session('error') }}</div>
                  @endif

                  <div style="max-width:400px;">
                     <form action="{{ route('profile.changepassword.update') }}" method="POST" class="row g-4 formFormat">
                        @csrf
                        <div class="col-12">
                           <small class="">Current Password <span class="txtRed">*</span></small>
                           <div class="position-relative">
                              <input type="password" class="form-control" name="current_password" required/>
                              <button type="button" class="passwordBtn" data-bs-toggle="button"></button>
                           </div>
                        </div>
                        <div class="col-12">
                           <small class="">New Password <span class="txtRed">*</span></small>
                           <div class="position-relative">
                              <input type="password" class="form-control" name="password" required/>
                              <button type="button" class="passwordBtn" data-bs-toggle="button"></button>
                           </div>
                        </div>
                        <div class="col-12">
                           <small class="">Re-enter New Password <span class="txtRed">*</span></small>
                           <div class="position-relative">
                              <input type="password" class="form-control" name="password_confirmation" required/>
                              <button type="button" class="passwordBtn" data-bs-toggle="button"></button>
                           </div>
                        </div>
                        <div class="col-12 mt-4 text-center">
                           <button type="submit" class="ctaBtn">
                              <span class="btnTxt">Save Changes</span>
                              <span class="arw"></span>
                           </button>
                        </div>
                     </form>
                  </div>

              </div>
          </div>

      </div>
   </section>

</main>
@endsection
