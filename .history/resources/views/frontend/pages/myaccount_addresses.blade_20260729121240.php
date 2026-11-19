@extends('layouts.front')
@section('content')

@php
$states = ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu and Kashmir','Ladakh'];
@endphp

<main class="pageWrapper">

   <section class="py-5">
      <div class="container">

          <div class="row justify-content-between mb-5">
              @include('frontend.partials.myaccount_nav', ['active' => 'address', 'user' => $user])

              <div class="col-lg-9 mb-lg-4">

               <h2 class="sectTitle lh-1 mb-4">Addresses</h2>

               @if(session('success'))
                   <div class="alert alert-success">{{ session('success') }}</div>
               @endif
               @if(session('error'))
                   <div class="alert alert-danger">{{ session('error') }}</div>
               @endif

               @if($addresses->isNotEmpty())
               <ul class="addressList mb-3 d-flex flex-column gap-3" style="max-width:800px;">
                  @foreach($addresses as $address)
                  <li class="border rounded-3 p-3 position-relative">
                     <form action="{{ route('profile.address.destroy', $address) }}" method="POST" class="position-absolute" style="top:.5rem;right:.5rem;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-close" style="width:.25rem;height:.25rem;" title="Delete" onclick="return confirm('Delete this address?');"></button>
                     </form>
                     <strong>{{ $address->name }}</strong>
                     @if($address->is_default)
                        <span class="badge bgOrange ms-2 lh-1">Default</span>
                     @else
                        <form action="{{ route('profile.address.default', $address) }}" method="POST" class="d-inline ms-2">
                           @csrf
                           <button type="submit" class="btn btn-link btn-sm p-0 txtorange">Mark as default</button>
                        </form>
                     @endif
                     <address class="mb-0 mt-2">{{ $address->formatted_address ?? trim($address->address . ', ' . $address->city . ', ' . $address->state . ' - ' . $address->pincode, ', ') }}</address>
                     @if($address->phone)
                        <small class="text-secondary d-block mt-1">Phone: {{ $address->phone }}</small>
                     @endif
                  </li>
                  @endforeach
               </ul>
               @else
               <div class="mb-3">Currently we don't have your address with us.</div>
               @endif

               <button type="button" class="ctaBtn mb-4" data-bs-toggle="collapse" data-bs-target="#addressForm" aria-expanded="false">
                  <span class="btnTxt">Add Address</span>
                  <span class="arw"></span>
               </button>

               <div class="collapse p-3 p-4 rounded-3 bg-light" id="addressForm" style="max-width:800px;">
                  <form action="{{ route('profile.address.store') }}" method="POST" class="row g-3 formFormat">
                     @csrf
                     <div class="col-md-6">
                        <small class="">Full Name <span class="txtRed">*</span></small>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required/>
                     </div>
                     <div class="col-md-6">
                        <small class="">Mobile No. <span class="txtRed">*</span></small>
                        <input type="tel" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" required/>
                     </div>
                     <div class="col-12 m-0"></div>
                     <div class="col-md-6">
                        <small class="">Address Line 1 <span class="txtRed">*</span></small>
                        <input type="text" class="form-control" name="address_line_1" value="{{ old('address_line_1') }}" required/>
                     </div>
                     <div class="col-md-6">
                        <small class="">Address Line 2</small>
                        <input type="text" class="form-control" name="address_line_2" value="{{ old('address_line_2') }}"/>
                     </div>
                     <div class="col-md-6">
                        <small class="">State <span class="txtRed">*</span></small>
                        <select class="form-select" name="state" required>
                           <option value="">Select State</option>
                           @foreach($states as $state)
                              <option value="{{ $state }}" {{ old('state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                           @endforeach
                        </select>
                     </div>
                     <div class="col-md-6">
                        <small class="">City <span class="txtRed">*</span></small>
                        <input type="text" class="form-control" name="city" value="{{ old('city') }}" required/>
                     </div>
                     <div class="col-md-6">
                        <small class="text-secondary">Pincode <span class="txtRed">*</span></small>
                        <input type="text" class="form-control" name="pincode" value="{{ old('pincode') }}" maxlength="6" required/>
                     </div>
                     <div class="col-md-6 d-flex align-items-center">
                        <div class="afiContent d-flex gap-2 mt-3 align-items-center">
                           <input class="form-check-input" type="checkbox" name="is_default" id="markDefault" value="1" {{ old('is_default') ? 'checked' : '' }}>
                           <label for="markDefault">Mark as Default</label>
                        </div>
                     </div>
                     <div class="col-12 mt-4 text-center">
                        <button type="submit" class="ctaBtn">Save</button>
                     </div>
                  </form>
               </div>

              </div>
          </div>

      </div>
   </section>

</main>
@endsection
