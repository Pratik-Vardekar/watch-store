<div class="sidenav-menu">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="logo d-flex flex-column align-items-center justify-content-center" style="width:100%; text-align:center; padding: 1rem 0;">
        <span class="logo logo-light d-flex flex-column align-items-center">
            <span class="logo-lg">
                <img src="{{asset('backend/assets/images/tws_logo.png')}}" alt="logo"
                     style="height:60px; width:auto; max-width:320px; object-fit:contain; display:block; margin: 0 auto;" />
            </span>
            <span class="logo-sm">
                <img src="{{asset('backend/assets/images/tws_logo.png')}}" alt="small logo"
                     style="height:60px; width:auto; max-width:90px; object-fit:contain; display:block; margin: 0 auto;" />
            </span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-on-hover">
        <span class="btn-on-hover-icon"></span>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-offcanvas">
        <i class="ti ti-menu-4 align-middle"></i>
    </button>

    <div class="scrollbar" data-simplebar="">
        <div id="user-profile-settings" class="sidenav-user" style="background: url(assets/images/user-bg-pattern.svg)">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="#!" class="link-reset">
                        <img src="{{ auth()->user()->avatar_url }}" alt="user-image" class="rounded-circle mb-2 avatar-md object-fit-cover" />
                        <span class="sidenav-user-name fw-bold">{{ auth()->user()->name }}</span>
                        <span class="fs-12 fw-semibold" data-lang="user-role">
                            @php
                                // Get role display name properly
                                $user = auth()->user();
                                $roleDisplayName = 'User';
                                
                                // Try to get role from relationship
                                if ($user->role_id) {
                                    $role = $user->role()->first();
                                    if ($role && isset($role->display_name)) {
                                        $roleDisplayName = $role->display_name;
                                    } elseif ($role && isset($role->name)) {
                                        $roleDisplayName = ucwords(str_replace('_', ' ', $role->name));
                                    }
                                } elseif (isset($user->role) && is_string($user->role)) {
                                    $roleDisplayName = ucwords(str_replace('_', ' ', $user->role));
                                }
                                echo $roleDisplayName;
                            @endphp
                        </span>
                    </a>
                </div>
                <div>
                    <a class="dropdown-toggle drop-arrow-none link-reset sidenav-user-set-icon" data-bs-toggle="dropdown" data-bs-offset="0,12" href="#!" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-settings fs-24 align-middle ms-1"></i>
                    </a>

                    <div class="dropdown-menu">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome back!</h6>
                        </div>

                        <a href="{{ route('admin.profile') }}" class="dropdown-item">
                            <i class="ti ti-user-circle me-1 fs-lg align-middle"></i>
                            <span class="align-middle">Profile</span>
                        </a>

                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="ti ti-settings-2 me-1 fs-lg align-middle"></i>
                            <span class="align-middle">Account Settings</span>
                        </a>

                        <a href="{{ route('logout') }}" class="dropdown-item text-danger fw-semibold" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti ti-logout me-1 fs-lg align-middle"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--- Sidenav Menu -->
        <div id="sidenav-menu">
            <ul class="side-nav">

                @php
                    $user = auth()->user();
                    $roleName = 'user';
                    $permissions = [];
                    
                    // Get role and permissions from relationship
                    if ($user->role_id) {
                        $role = $user->role()->first();
                        if ($role) {
                            $roleName = $role->name;
                            // Get permissions
                            $perms = $role->permissions;
                            if (is_string($perms)) {
                                $permissions = json_decode($perms, true) ?? [];
                            } elseif (is_array($perms)) {
                                $permissions = $perms;
                            }
                        }
                    }
                    
                    // If no role found via relationship, fallback to old column
                    if (empty($roleName) || $roleName === 'user') {
                        if (isset($user->role) && is_string($user->role)) {
                            $roleName = $user->role;
                        }
                    }
                    
                    // Make sure permissions is an array
                    if (!is_array($permissions)) {
                        $permissions = [];
                    }
                    
                    // Determine if user has permission (Admin always has all)
                    $hasPermission = function($perm) use ($roleName, $permissions) {
                        if ($roleName === 'admin') {
                            return true;
                        }
                        return in_array($perm, $permissions) || in_array('all', $permissions);
                    };
                @endphp

                <!-- Dashboard -->
                @if($hasPermission('dashboard') || in_array($roleName, ['admin', 'web_designer', 'order_manager', 'manager']))
                <li class="side-nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                @endif

                <!-- Orders - Show if has orders permission -->
                @if($hasPermission('orders'))
                <li class="side-nav-item">
                    <a href="{{ route('order.index') }}" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-shopping-cart"></i></span>
                        <span class="menu-text">Orders</span>
                    </a>
                </li>
                @endif

                <!-- Transactions - Show if has transactions permission -->
                {{-- @if($hasPermission('transactions'))
                <li class="side-nav-item">
                    <a href="{{ route('transactions.index') }}" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-credit-card"></i></span>
                        <span class="menu-text">Transactions</span>
                    </a>
                </li>
                @endif --}}

                <!-- Products - Show if has any product-related permission -->
                @if($hasPermission('products') || $hasPermission('categories') || $hasPermission('subcategories') || $hasPermission('reviews'))
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#productMenu" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-package"></i></span>
                        <span class="menu-text">Products</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse" id="productMenu">
                        <ul class="sub-menu">
                            @if($hasPermission('brands'))
                            <li>
                                <a href="{{ route('brands.index') }}" class="side-nav-link">
                                    <i class="ti ti-star me-2"></i>Brand
                                </a>
                            </li>
                            @endif
                            @if($hasPermission('categories'))
                            <li>
                                <a href="{{ route('category.index') }}" class="side-nav-link">
                                    <i class="ti ti-package me-2"></i>Categories
                                </a>
                            </li>
                            @endif
                            @if($hasPermission('products'))
                            <li>
                                <a href="{{ route('product.index') }}" class="side-nav-link">
                                    <i class="ti ti-package me-2"></i>Products
                                </a>
                            </li>
                            @endif
                            {{-- @if($hasPermission('subcategories'))
                            <li>
                                <a href="{{ route('subcategory.index') }}" class="side-nav-link">
                                    <i class="ti ti-folders me-2"></i>Sub Categories
                                </a>
                            </li>
                            @endif --}}
                            @if($hasPermission('reviews'))
                            <li>
                                <a href="{{ route('admin.review.index') }}" class="side-nav-link">
                                    <i class="ti ti-star me-2"></i>Reviews
                                </a>
                            </li>
                            @endif

                        </ul>
                    </div>
                </li>
                @endif

                <!-- CMS - Show if has any CMS permission -->
                @if($hasPermission('cms') || $hasPermission('sliders') || $hasPermission('blogs') || $hasPermission('banners') || $hasPermission('watches'))
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#cmsMenu" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-file-text"></i></span>
                        <span class="menu-text">CMS</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse" id="cmsMenu">
                        <ul class="sub-menu">
                            @if($hasPermission('sliders') || $hasPermission('cms'))
                            @if($hasPermission('sliders'))
                            <li>
                                <a href="{{ route('slider.index') }}" class="side-nav-link">
                                    <i class="ti ti-slideshow me-2"></i>Sliders
                                </a>
                            </li>
                            @endif
                            {{-- <li>
                                <a href="{{ route('brands.index') }}" class="side-nav-link">
                                    <i class="ti ti-star me-2"></i>Brand
                                </a>
                            </li> --}}
                            {{-- <li>
                                <a href="{{ route('features.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>Features
                                </a>
                            </li> --}}
                            @if($hasPermission('watches'))
                            <li>
                                <a href="{{ route('watches.index') }}" class="side-nav-link">
                                <i class="ti ti-article me-2"></i>Watches Banner
                            </a>
                        </li>
                        @endif
                            <li>
                                <a href="{{ route('admin.home-service-items.index') }}" class="side-nav-link">
                                    <i class="ti ti-tool me-2"></i>Service Center
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.home-highlights.index') }}" class="side-nav-link">
                                    <i class="ti ti-layout-grid me-2"></i>Homepage Cards
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('benefits.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>Benefits
                                </a>
                            </li> --}}
                            @endif
                            <li>
                                <a href="{{ route('stores.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>
                                    Stores
                                </a>
                            </li>
                       
                            <li>
                                <a data-bs-toggle="collapse" href="#helpCenterSettingsMenu" class="side-nav-link">
                                    <i class="ti ti-help me-2"></i>Help Center Settings
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="helpCenterSettingsMenu">
                                    <ul class="sub-menu">
                                        <li>
                                            <a href="{{ route('admin.help-categories.index') }}" class="side-nav-link">
                                                <i class="ti ti-layout-list me-2"></i>
                                                Help Categories
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.help-faqs.index') }}" class="side-nav-link">
                                                <i class="ti ti-question-mark me-2"></i>
                                                Help FAQs
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                       
                            @if(isset($hasPermission) && $hasPermission('about-us'))
                            <li>
                                <a href="{{ route('about-us.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>About Page
                                </a>
                            </li>
                            @endif
                            
                            @endif
                            @if($hasPermission('galleries'))
                            <li>
                                <a data-bs-toggle="collapse"
                                   href="#galleryMenu"
                                   class="side-nav-link">
                                    <i class="ti ti-photo me-2"></i>
                                    <span>Galleries</span>
                                    <span class="menu-arrow"></span>
                                </a>
                            
                                <div class="collapse" id="galleryMenu">
                                    <ul class="sub-menu">
                                        <li>
                                            <a href="{{ route('gallery.index') }}" class="side-nav-link">
                                                <i class="ti ti-photo me-2"></i>
                                                Gallery List
                                            </a>
                                        </li>
                                        {{-- If you have Gallery Category --}}
                                        {{-- <li>
                                            <a href="{{ route('gallery-category.index') }}">
                                                Gallery Categories
                                            </a>
                                        </li> --}}
                                    </ul>
                                </div>
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('services.index') }}" class="side-nav-link">
                                    <i class="ti ti-tool me-2"></i>Services
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('return-refund.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>Return Policy
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('warranty-guarantee.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>Warranties & Guarantees
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.special-offers.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>Special Offers
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('privacy-policy.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>Privacy Policy
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('terms-and-conditions.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>Terms and Conditions
                                </a>
                            </li>
                            {{--
                            <li>
                                <a href="{{ route('return-policy.index') }}" class="side-nav-link">
                                    <i class="ti ti-article me-2"></i>Return Policy
                                </a>
                            </li> --}}
                        </ul>
                    </div>
                </li>
                {{-- The following @endif was likely in error and is now removed --}}
                <!-- Coupons - Permission based (admin fallback) -->
                @if($hasPermission('coupons') || $roleName == 'admin')
                <li class="side-nav-item">
                    <a href="{{ route('coupon.index') }}" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-discount-2"></i></span>
                        <span class="menu-text">Coupons</span>
                    </a>
                </li>
                @endif

                <!-- Contact Enquiries - Permission based (admin fallback) -->
                @if($hasPermission('contact_enquiries') || $roleName == 'admin')
                    <li class="side-nav-item">
                        <a href="{{ route('admin.contact-enquiries.index') }}" class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-message-circle"></i></span>
                            <span class="menu-text">Contact Enquiries</span>
                        </a>
                    </li>
                @endif
                <!-- Customers - Permission based (admin fallback) -->
                @if($hasPermission('customers') || $roleName == 'admin')
                <li class="side-nav-item">
                    <a href="{{ route('customer.index') }}" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-shopping-bag"></i></span>
                        <span class="menu-text">Customers</span>
                    </a>
                </li>
                @endif

                <!-- Users - Permission based (admin fallback) -->
                @if($hasPermission('users') || $roleName == 'admin')
                <li class="side-nav-item">
                    <a href="{{ route('user.index') }}" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-users"></i></span>
                        <span class="menu-text">Users</span>
                    </a>
                </li>
                @endif

                <!-- Roles & Permissions - Permission based (admin fallback) -->
                @if($hasPermission('roles_permissions') || $hasPermission('roles') || $roleName == 'admin')
                <li class="side-nav-item">
                    <a href="{{ route('roles.index') }}" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-shield"></i></span>
                        <span class="menu-text">Roles & Permissions</span>
                    </a>
                </li>
                @endif

                <!-- Reports - Show if has reports permission -->
                @if($hasPermission('reports'))
                <li class="side-nav-item">
                    <a href="{{ route('reports.index') }}" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-chart-bar"></i></span>
                        <span class="menu-text">Reports</span>
                    </a>
                </li>
                @endif

                <!-- Settings - Permission based (admin fallback) -->
                @if($hasPermission('settings') || $roleName == 'admin')
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#settingsMenu" class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-settings"></i></span>
                        <span class="menu-text">Settings</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse" id="settingsMenu">
                        <ul class="sub-menu">
                            <li>
                                <a href="{{ route('gst.settings.index') }}" class="side-nav-link">
                                    <i class="ti ti-truck-delivery me-2"></i>GST Settings
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.contact-settings.edit') }}" class="side-nav-link">
                                    <i class="ti ti-phone-call me-2"></i>Contact Details
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.footer-contact-settings.edit') }}" class="side-nav-link">
                                    <i class="ti ti-layout-bottombar me-2"></i>Footer Contact
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.header-top-bar-settings.edit') }}" class="side-nav-link">
                                    <i class="ti ti-layout-navbar me-2"></i>Header Top Bar
                                </a>
                            </li>
                            {{-- <li>
                                <a data-bs-toggle="collapse" href="#helpCenterSettingsMenu" class="side-nav-link">
                                    <i class="ti ti-help me-2"></i>Help Center Settings
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="helpCenterSettingsMenu">
                                    <ul class="sub-menu">
                                        <li>
                                            <a href="{{ route('admin.help-categories.index') }}" class="side-nav-link">
                                                <i class="ti ti-layout-list me-2"></i>
                                                Help Categories
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.help-faqs.index') }}" class="side-nav-link">
                                                <i class="ti ti-question-mark me-2"></i>
                                                Help FAQs
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li> --}}
                            <li>
                                <a href="{{ route('shipping.settings') }}" class="side-nav-link">
                                    <i class="ti ti-truck-delivery me-2"></i>Shipping Settings
                                </a>
                            </li>
                        </ul>
                    </div>
               
                </li>
                @endif

            </ul>
        </div>
    </div>
</div>