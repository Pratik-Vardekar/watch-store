@extends('layouts.front')

@section('content')
<main class="pageWrapper">

   <section class="py-4 py-lg-5 myAccountPage bgGray">
      <div class="container">

          <div class="row g-4 align-items-start mb-4">
              @include('frontend.partials.myaccount_nav', ['active' => 'profile', 'user' => $user])

              <div class="col-lg-9 mb-lg-4">

                  {{-- Page Title Header --}}
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                      <div>
                          <h2 class="sectTitle lh-1 mb-1">My Profile</h2>
                          <p class="text-secondary small mb-0">View and update your personal details and contact information</p>
                      </div>
                  </div>

                  {{-- Flash Feedback Messages --}}
                  @if(session('success'))
                      <div class="alert alert-success d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm">
                          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                          <span>{{ session('success') }}</span>
                      </div>
                  @endif
                  @if(session('error'))
                      <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm">
                          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                          <span>{{ session('error') }}</span>
                      </div>
                  @endif

                  @if($errors->any())
                      <div class="alert alert-danger mb-4 rounded-3 border-0 shadow-sm">
                          <ul class="mb-0 ps-3">
                              @foreach($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                  @endif

                  {{-- Profile Card --}}
                  <div class="profileCard mb-4">
                      <div class="profileCardHeader">
                          <div class="d-flex align-items-center gap-3">
                              <div class="myAccAvatar" style="width: 52px; height: 52px; font-size: 1.25rem;">
                                  {{ strtoupper(substr(trim($user->name ?? 'User'), 0, 1) ?: 'U') }}
                              </div>
                              <div>
                                  <h4 class="mb-1 fw-bold text-dark">{{ $user->name }}</h4>
                                  <div class="d-flex align-items-center gap-2 flex-wrap">
                                      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.72rem;">Active Customer</span>
                                      <span class="text-muted small">Member since {{ $user->created_at ? $user->created_at->format('M Y') : 'Recent' }}</span>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="profileCardBody">
                         <form action="{{ route('profile.profile.update') }}" method="POST" class="row g-3 g-md-4 formFormat" id="profileUpdateForm">
                            @csrf
                            <div class="col-md-6">
                               <label class="form-label fw-semibold small text-secondary">Full Name <span class="txtRed">*</span></label>
                               <input type="text" class="form-control" name="name" placeholder="Full Name*" value="{{ old('name', $user->name) }}" required/>
                            </div>
                            <div class="col-md-6">
                               <label class="form-label fw-semibold small text-secondary">Email Address <span class="txtRed">*</span></label>
                               <input type="email" class="form-control" name="email" placeholder="Email Address*" value="{{ old('email', $user->email) }}" required/>
                            </div>
                            <div class="col-md-6">
                               <label class="form-label fw-semibold small text-secondary">Mobile Number</label>
                               <input type="tel" class="form-control" name="phone" placeholder="Enter mobile number" value="{{ old('phone', $user->phone) }}"/>
                            </div>
                            <div class="col-md-6">
                               <label class="form-label fw-semibold small text-secondary">Account ID</label>
                               <input type="text" class="form-control bg-light" value="TWS-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}" readonly disabled/>
                            </div>

                            <div class="col-12 mt-4 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                               <button type="submit" class="ctaBtn">
                                   <span class="btnTxt">Save Changes</span>
                                   <span class="arw"></span>
                               </button>

                               <button type="button" class="btn btn-outline-danger px-4 py-2 rounded-pill d-inline-flex align-items-center gap-2 fw-semibold" onclick="document.getElementById('profileLogoutForm').submit();">
                                   <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                   <span>Logout</span>
                               </button>
                            </div>
                         </form>

                         {{-- Separate logout form --}}
                         <form id="profileLogoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                         </form>
                      </div>
                  </div>

                  {{-- Quick Account Shortcuts --}}
                  <div class="row g-3">
                      <div class="col-sm-6 col-xl-3">
                          <a href="{{ route('profile.orders') }}" class="accountOverviewCard">
                              <div class="d-flex align-items-center gap-3">
                                  <div class="accountOverviewIcon">
                                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                  </div>
                                  <div>
                                      <div class="fw-bold text-dark small">My Orders</div>
                                      <div class="text-muted" style="font-size: 0.75rem;">Track &amp; view orders</div>
                                  </div>
                              </div>
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                          </a>
                      </div>
                      <div class="col-sm-6 col-xl-3">
                          <a href="{{ route('wishlist') }}" class="accountOverviewCard">
                              <div class="d-flex align-items-center gap-3">
                                  <div class="accountOverviewIcon">
                                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                  </div>
                                  <div>
                                      <div class="fw-bold text-dark small">My Wishlist</div>
                                      <div class="text-muted" style="font-size: 0.75rem;">Saved favorites</div>
                                  </div>
                              </div>
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                          </a>
                      </div>
                      <div class="col-sm-6 col-xl-3">
                          <a href="{{ route('profile.address') }}" class="accountOverviewCard">
                              <div class="d-flex align-items-center gap-3">
                                  <div class="accountOverviewIcon">
                                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                  </div>
                                  <div>
                                      <div class="fw-bold text-dark small">Addresses</div>
                                      <div class="text-muted" style="font-size: 0.75rem;">Manage delivery</div>
                                  </div>
                              </div>
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                          </a>
                      </div>
                      <div class="col-sm-6 col-xl-3">
                          <a href="{{ route('profile.changepassword') }}" class="accountOverviewCard">
                              <div class="d-flex align-items-center gap-3">
                                  <div class="accountOverviewIcon">
                                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                  </div>
                                  <div>
                                      <div class="fw-bold text-dark small">Password</div>
                                      <div class="text-muted" style="font-size: 0.75rem;">Security &amp; login</div>
                                  </div>
                              </div>
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                          </a>
                      </div>
                  </div>

              </div>
          </div>

      </div>
   </section>

</main>
@endsection
