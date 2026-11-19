<header class="app-topbar">
   <div class="container-fluid topbar-menu">
   <div class="d-flex align-items-center gap-2">
      <!-- Topbar Brand Logo -->
      <div class="logo-topbar">
         <!-- Logo light -->
         <a href="{{ route('admin.dashboard') }}" class="logo-light">
         <span class="logo-lg">
         <img src="{{asset('backend/assets/images/cheriksLogo.png')}}" alt="logo" />
         </span>
         <span class="logo-sm">
         <img src="{{asset('backend/assets/images/cheriksLogo.png')}}" alt="small logo" />
         </span>
         </a>
         <!-- Logo Dark -->
         <a href="{{ route('admin.dashboard') }}" class="logo-dark">
         <span class="logo-lg">
         <img src="{{asset('backend/assets/images/cheriksLogo.png')}}" alt="dark logo" />
         </span>
         <span class="logo-sm">
         <img src="{{asset('backend/assets/images/cheriksLogo.png')}}" alt="small logo" />
         </span>
         </a>
      </div>
      <!-- Sidebar Menu Toggle Button -->
      <button class="sidenav-toggle-button btn btn-primary btn-icon">
      <i class="ti ti-menu-4"></i>
      </button>
      <!-- Horizontal Menu Toggle Button -->
      <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu">
      <i class="ti ti-menu-4"></i>
      </button>
      <!-- <div id="search-box-rounded" class="app-search d-none d-xl-flex">
         <input type="search" class="form-control rounded-pill topbar-search" name="search" placeholder="Quick Search..." />
         <i class="ti ti-search app-search-icon text-muted"></i>
         </div> -->
   </div>
   <div class="d-flex align-items-center gap-2">
      
      <div id="fullscreen-toggler" class="topbar-item d-none d-md-flex">
         <button class="topbar-link" type="button" data-toggle="fullscreen">
         <i class="ti ti-maximize topbar-link-icon"></i>
         <i class="ti ti-minimize topbar-link-icon d-none"></i>
         </button>
      </div>
    
      <div id="user-dropdown-detailed" class="topbar-item nav-user">

  <div class="dropdown">

    <a class="topbar-link dropdown-toggle drop-arrow-none px-2"
       data-bs-toggle="dropdown"
       href="#!"
       aria-haspopup="false"
       aria-expanded="false">

        <img src="{{ asset('backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg') }}"
             width="36"
             height="36"
             class="rounded-circle me-lg-2 object-fit-cover"
             alt="user-image" />

        <div class="d-lg-flex align-items-center gap-1 d-none">

            <span>

                <h5 class="my-0 lh-1 pro-username fw-semibold">
                    {{ auth()->user()->name }}
                </h5>

                <span class="fs-xs text-muted">
                    {{ auth()->user()->role }}
                </span>

            </span>

            <i class="ti ti-chevron-down align-middle"></i>

        </div>

    </a>

    <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg p-0 overflow-hidden">

        {{-- Header --}}
        <div class="bg-primary text-white p-3">

            <div class="d-flex align-items-center gap-2">

                <img src="{{ asset('backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg') }}"
                     width="50" 
                     height="50"
                     class="rounded-circle border border-2 border-white"
                     alt="user-image">

                <div>

                    <h5 class="mb-0 text-white">
                        {{ auth()->user()->name }}
                    </h5>

                    <small>
                        {{ auth()->user()->email }}
                    </small>

                </div>

            </div>

        </div>

        {{-- Menu --}}
        <div class="p-2">

            {{-- Profile --}}
            <a href="{{ route('admin.profile') }}"
               class="dropdown-item rounded">

                <i class="ti ti-user-circle me-2 fs-lg align-middle"></i>

                <span>
                    My Profile
                </span>

            </a>

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="dropdown-item rounded">

                <i class="ti ti-layout-dashboard me-2 fs-lg align-middle"></i>

                <span>
                    Dashboard
                </span>

            </a>

            {{-- Change Password --}}
            <a href="{{ route('admin.profile') }}"
               class="dropdown-item rounded">

                <i class="ti ti-lock-password me-2 fs-lg align-middle"></i>

                <span>
                    Change Password
                </span>

            </a>

            <div class="dropdown-divider"></div>

            {{-- Logout --}}
            <form method="POST"
                  action="{{ route('logout') }}">

                @csrf

                <button type="submit"
                        class="dropdown-item text-danger rounded fw-semibold">

                    <i class="ti ti-logout me-2 fs-lg align-middle"></i>

                    <span>
                        Log Out
                    </span>

                </button>

            </form>

        </div>

    </div>

</div>
</div>
   </div>
</header>