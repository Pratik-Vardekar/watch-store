@extends('layouts.front')
@section('content')
<main class="pageWrapper">         

   <div class="cloudWrap position-relative d-none d-lg-block">
      <div class="container clouds position-relative"></div>
   </div>
   
   <section class="py-5">
      <div class="container">

          <div class="row justify-content-between mb-5">
              <div class="col-lg-3 mb-lg-4 d-none d-lg-block">

                  <div class="myAccLinkWrap">
                     <div class="d-flex mb-5 px-3 py-2 rounded-3 w-100 bgTeal text-white profileWrap">
                        <div class="col-auto"></div>
                        <div class="col">
                           <small class="d-block">Welcome,</small>
                           <strong>Mittal Solanki</strong>
                        </div>
                     </div>
                     <ul class="myAccLinks d-flex flex-column gap-4 mb-5">
                        <li><a href="myaccount_profile.html">Profile</a></li>
                        <li><a href="myaccount_orders.html">Orders</a></li>
                        <li><a href="myaccount_addresses.html" class="active">Addresses</a></li>
                        <li><a href="myaccount_changepassword.html">Change Password</a></li>
                     </ul>
                     <button type="button" class="ctaBtn">
                     <span class="btnTxt">Logout</span>
                     <span class="arw"></span>
                     </button>
                  </div>

              </div>
              <div class="col-lg-9 mb-lg-4">

                  <h2 class="sectTitle lh-1 mb-4">Addresses</h2>

                  <ul class="addressList mb-3 d-flex flex-column gap-3" style="max-width:800px;">
                     <li class="border rounded-3 p-3 position-relative">
                        <button type="button" class="btn-close position-absolute" style="width:.25rem;height:.25rem;top:.5rem;right:.5rem;" title="Delete"></button>
                        <strong>Mittal Solanki</strong>
                        <span class="badge bgOrange ms-2 lh-1">Default</span>
                        <address class="mb-0">
                           410, 9 Business Bay, Mindspace, Malad (W), Mumbai - 400064. Maharashtra.
                        </address>
                     </li>
                     <li class="border rounded-3 p-3 position-relative">
                        <button type="button" class="btn-close position-absolute" style="width:.25rem;height:.25rem;top:.5rem;right:.5rem;" title="Delete"></button>
                        <strong>Mittal Solanki</strong>
                        <address class="mb-0">
                           410, 9 Business Bay, Mindspace, Malad (W), Mumbai - 400064. Maharashtra.
                        </address>
                     </li>
                  </ul>

                  <div class="mb-3">Currently we don't have your address with us.</div>

                  <button type="button" class="ctaBtn mb-4" data-bs-toggle="collapse" data-bs-target="#addressForm" aria-expanded="false">
                     <span class="btnTxt">Add Address</span>
                     <span class="arw"></span>
                  </button>

                  <div class="collapse p-3 rounded-3 bg-light" id="addressForm" style="max-width:800px;">
                     <form action="" class="row g-3 formFormat">
                        <div class="col-md-6">
                           <small class="text-secondary">Full Name <span class="txtred">*</span></small>
                           <input type="text" class="form-control" name="" id="" value=""/>
                        </div>
                        <div class="col-12 m-0"></div>
                        <div class="col-md-6">
                           <small class="text-secondary">Address Line 1 <span class="txtred">*</span></small>
                           <input type="text" class="form-control" name="" id="" value=""/>
                        </div>
                        <div class="col-md-6">
                           <small class="text-secondary">Address Line 2 </small>
                           <input type="text" class="form-control" name="" id="" value=""/>
                        </div>
                        <div class="col-md-6">
                           <small class="text-secondary">State <span class="txtred">*</span></small>
                           <select class="form-select" name="" id="">
                              <option value="">Option 1</option>
                              <option value="">Option 2</option>
                              <option value="">Option 3</option>
                              <option value="">Option 4</option>
                           </select>
                        </div>
                        <div class="col-md-6">
                           <small class="text-secondary">City <span class="txtred">*</span></small>
                           <select class="form-select" name="" id="">
                              <option value="">Option 1</option>
                              <option value="">Option 2</option>
                              <option value="">Option 3</option>
                              <option value="">Option 4</option>
                           </select>
                        </div>
                        <div class="col-md-6">
                           <small class="text-secondary">Pincode <span class="txtred">*</span></small>
                           <input type="number" class="form-control" name="" id="" value=""/>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                           <div class="filterList d-flex gap-2 mt-3 align-items-center">
                              <input type="checkbox" name="" id="markDefault">
                              <label for="markDefault">Mark as Default</label>
                           </div>
                        </div>
                        <div class="col-12 mt-4 text-center">
                           <button type="submit" class="ctaBtn">
                              <span class="btnTxt">Save</span>
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