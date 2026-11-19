@extends('layouts.front')
@section('content')
<main class="pageWrapper">

   <section class="py-5">
      <div class="container">

          <div class="row justify-content-between mb-5">
              @include('frontend.partials.myaccount_nav', ['active' => 'profile', 'user' => $user])

              <div class="col-lg-9 mb-lg-4">

                  <h2 class="sectTitle lh-1 mb-4">My Profile</h2>

                  @if(session('success'))
                      <div class="alert alert-success">{{ session('success') }}</div>
                  @endif
                  @if(session('error'))
                      <div class="alert alert-danger">{{ session('error') }}</div>
                  @endif

                  <div style="max-width:400px;">
                     <form action="{{ route('profile.profile.update') }}" method="POST" class="row g-4 formFormat">
                        @csrf
                        <div class="col-12">
                           <small class="">Full Name <span class="txtRed">*</span></small>
                           <input type="text" class="form-control" name="name" placeholder="Full Name*" value="{{ old('name', $user->name) }}" required/>
                        </div>
                        <div class="col-12">
                           <small class="">Email Id <span class="txtRed">*</span></small>
                           <input type="email" class="form-control" name="email" placeholder="Email Id*" value="{{ old('email', $user->email) }}" required/>
                        </div>
                        <div class="col-12">
                           <small class="">Mobile No.</small>
                           <input type="tel" class="form-control" name="phone" placeholder="Mobile No." value="{{ old('phone', $user->phone) }}"/>
                        </div>
                        <div class="col-12 mt-4 d-flex align-items-center justify-content-center gap-3 flex-wrap">
                           <button type="submit" class="ctaBtn">Save Changes</button>
                           <form action="{{ route('logout') }}" method="POST" class="m-0">
                              @csrf
                              <button type="submit" class="ctaBtn dark border-0">Logout</button>
                           </form>
                        </div>
                     </form>
                  </div>

              </div>
          </div>

      </div>
   </section>

</main>
@endsection
