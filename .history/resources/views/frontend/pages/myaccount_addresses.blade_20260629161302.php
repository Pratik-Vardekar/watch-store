@extends('layouts.front')
@section('content')
<main class="pageWrapper">         

    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5">
       <div class="container">

           <div class="row justify-content-between mb-5">
               <div class="col-lg-auto mb-lg-4 d-none d-lg-block">

                   <div class="myAccLinkWrap">
                      <div class="d-flex mb-5 px-3 py-2 rounded-3 w-100 bgTeal text-white profileWrap">
                         <div class="col-auto"></div>
                         <div class="col">
                            <small class="d-block">Welcome,</small>
                            <strong>Mittal Solanki</strong>
                         </div>
                      </div>
                      <ul class="myAccLinks d-flex flex-column gap-4 mb-5">
                         <li><a href="{{ route('profile.profile') }}">Profile</a></li>
                         <li><a href="{{ route('profile.orders') }}">Orders</a></li>
                         <li><a href="{{ route('profile.address') }}" class="active">Addresses</a></li>
                         <li><a href="{{ route('profile.changepassword') }}">Change Password</a></li>
                      </ul>
                      <button type="button" class="ctaBtn">
                      <span class="btnTxt">Logout</span>
                      <span class="arw"></span>
                      </button>
                   </div>

               </div>
               <div class="col-lg-9 mb-lg-4">

                   <h2 class="sectTitle lh-1 mb-4">Addresses</h2>

                   <div style="max-width:400px;">
                      <form action="" class="row g-3 formFormat">
                         <div class="col-12">
                            <small class="text-secondary">Full Name <span class="txtred">*</span></small>
                            <input type="text" class="form-control" name="" id="" placeholder="Full Name*" value="Mittal Solanki"/>
                         </div>
                         <div class="col-12">
                            <small class="text-secondary">Email Id <span class="txtred">*</span></small>
                            <input type="email" class="form-control" name="" id="" placeholder="Email Id*" value="mittal@goyalinfotech.com"/>
                         </div>
                         <div class="col-12">
                            <small class="text-secondary">Mobile No. <span class="txtred">*</span></small>
                            <input type="tel" class="form-control" name="" id="country" placeholder="Mobile No.*" value="+91 98123 45678"/>
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