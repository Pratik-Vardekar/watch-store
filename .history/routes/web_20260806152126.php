<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

// Frontend
use App\Http\Controllers\Frontend\FrontController;

// Authentication
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

// Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\WatchesController;
use App\Http\Controllers\Admin\ReturnRefundController;
use App\Http\Controllers\Admin\WarrantyGuaranteeController;
use App\Http\Controllers\Admin\HomeBannerController;
use App\Http\Controllers\Admin\ContactSettingController;
use App\Http\Controllers\Admin\FooterContactSettingController;
use App\Http\Controllers\Admin\HeaderTopBarSettingController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\GstSettingController;
use App\Http\Controllers\Admin\ShippingSettingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderPaymentController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\TermsAndConditionController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\BenefitController;
use App\Http\Controllers\Admin\PlayFunSectionController;
use App\Http\Controllers\Admin\LearnFunSectionController;
use App\Http\Controllers\Admin\HowToPlaySectionController;
use App\Http\Controllers\Admin\HomeFaqController;
use App\Http\Controllers\Admin\HelpCategoryController;
use App\Http\Controllers\Admin\HelpFaqController;
use App\Http\Controllers\Admin\HomeTestimonialController;
use App\Http\Controllers\Admin\HomeHighlightController;
use App\Http\Controllers\Admin\ContactEnquiryController;
use App\Http\Controllers\Admin\BrandController;

// API
use App\Http\Controllers\Api\ContactController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/category/{slug}', [FrontController::class, 'categoryShow'])->name('category.show');
Route::get('/search', [FrontController::class, 'searchResults'])->name('search');
Route::get('/search/suggest', [FrontController::class, 'searchSuggest'])->name('search.suggest');

Route::get('/blogs', [FrontController::class, 'blogs'])->name('blogs');
Route::get('/about', [FrontController::class, 'about'])->name('about');
Route::get('/return-policy', [FrontController::class, 'returnPolicy'])->name('return.policy');
Route::get('/warranties-guarantees', [FrontController::class, 'warrantiesGuarantees'])->name('warranties.guarantees');
// Route::get('/our-stores', [FrontController::class, 'ourStores'])->name('our.stores');
Route::get('/blogs/{slug}', [FrontController::class, 'blogsDetails'])->name('blogs.details');
Route::get('/gallery', [FrontController::class, 'gallery'])->name('gallery');
Route::get('/gallery/{slug}', [FrontController::class, 'galleryDetails'])->name('gallery.details');
// CART PAGE
Route::get('/cart', [FrontController::class, 'cart'])->name('cart');
Route::get('wishlist', [FrontController::class, 'wishlist'])->name('wishlist');

Route::get('/our-stores', [FrontController::class, 'ourStores'])->name('our.stores');

// ADD TO CART
Route::post('/cart/add/{product_id}', [FrontController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/guest/update/{product}', [FrontController::class, 'updateGuestCart'])->name('cart.guest.update');
Route::delete('/cart/guest/remove/{product}', [FrontController::class, 'removeGuestCart'])->name('cart.guest.remove');
Route::post('/cart/coupon/apply', [FrontController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [FrontController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/api/coupons/available', [FrontController::class, 'availableCoupons'])->name('cart.coupon.available');
Route::middleware('auth')->group(function () {
    Route::patch('/cart/update/{cart}', [FrontController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove/{cart}', [FrontController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/wishlist/add/{product}', [FrontController::class, 'addToWishlist'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product}', [FrontController::class, 'removeFromWishlist'])->name('wishlist.remove');
    Route::post('/wishlist/toggle/{product}', [FrontController::class, 'toggleWishlist'])->name('wishlist.toggle');
});

// BUY NOW (FIXED)
Route::get('/checkout/buy/{id}', [FrontController::class, 'buyNow'])->name('checkout.buy');

// CHECKOUT
Route::get('/checkout/shipping', [FrontController::class, 'checkoutShipping'])->name('checkout.shipping');
Route::middleware('auth')->group(function () {
    Route::post('/checkout/place-order', [FrontController::class, 'placeOrder'])->name('checkout.place');
    Route::post('/checkout/razorpay/verify', [FrontController::class, 'verifyRazorpayPayment'])->name('checkout.razorpay.verify');
});
Route::get('/checkout/success', [FrontController::class, 'checkoutSuccess'])->name('checkout.success');

Route::get('/product_details', [FrontController::class, 'productDetails'])->name('product.details');
Route::post('/product/check-delivery', [FrontController::class, 'checkDelivery'])->name('product.check_delivery');
Route::get('/ajax/product-listing/{slug}', [FrontController::class, 'productListingGrid'])->name('product_listing.grid');
Route::match(['GET', 'POST'], '/ajax/log-product-listing-error', [FrontController::class, 'logProductListingClientError'])->name('product_listing.log_error');
Route::get('/ajax/super-deals-products', [FrontController::class, 'superDealsGrid'])->name('super.deals.grid');
Route::get('/product-listing', [FrontController::class, 'productListing'])->name('product.listing');
Route::get('/super-deals', [FrontController::class, 'superDeals'])->name('super.deals');
Route::get('/privacy-policy', [FrontController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-and-conditions', [FrontController::class, 'termsAndConditions'])->name('terms.and.conditions');
Route::get('/contact-us', [FrontController::class, 'contactUs'])->name('contact.us');
Route::get('/faqs', [FrontController::class, 'helpCenter'])->name('faqs.center');
Route::get('/return-policy', [FrontController::class, 'returnPolicy'])->name('return.policy');
Route::get('/warranties-guarantees', [FrontController::class, 'warrantiesGuarantees'])->name('warranties.guarantees');

Route::get('/product/{product}/reviews', [FrontController::class, 'indexReview'])->name('review.index');
Route::get('/product/{product}/review/{review}', [FrontController::class, 'showReview'])->name('review.show');

// CONTACT ENQUIRY
Route::post('/contact-enquiry', [FrontController::class, 'storeContactEnquiry'])->name('contact.enquiry.store');

// LOGIN
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

/* LOGOUT */
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/* REGISTER */
Route::get('/signup', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/signup', [RegisteredUserController::class, 'store']);
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

Route::get('test-subcategories/{category_id}', [ProductController::class, 'getSubcategories']);


Route::middleware('auth')->group(function () {
    Route::get('/profile/profile', [FrontController::class, 'myAccountProfile'])->name('profile.profile');
    Route::get('/profile/orders', [FrontController::class, 'myAccountOrders'])->name('profile.orders');
    Route::get('/profile/address', [FrontController::class, 'myAccountAddresses'])->name('profile.address');
    Route::get('/profile/changepassword', [FrontController::class, 'myAccountChangePassword'])->name('profile.changepassword');

    Route::post('/profile/address', [FrontController::class, 'storeAddress'])->name('profile.address.store');
    Route::post('/profile/address/{address}/default', [FrontController::class, 'setDefaultAddress'])->name('profile.address.default');
    Route::delete('/profile/address/{address}', [FrontController::class, 'destroyAddress'])->name('profile.address.destroy');

    Route::post('/profile/profile', [FrontController::class, 'updateProfile'])->name('profile.profile.update');

    Route::get('/profile/orders/{order}', [FrontController::class, 'myAccountOrderDetails'])->name('frontend.order.details');
    Route::post('/profile/orders/{order}/cancel', [FrontController::class, 'cancelOrder'])
        ->name('frontend.orders.cancel');
    Route::post('/profile/orders/{order}/return', [FrontController::class, 'returnOrder'])->name('frontend.orders.return');
    Route::post('/profile/orders/{order}/cancel-return', [FrontController::class, 'cancelReturn'])
        ->name('frontend.orders.cancel_return');
    Route::post('/profile/changepassword', [FrontController::class, 'updatePassword'])->name('profile.changepassword.update');
    Route::get('/my-order/invoice/{order}', [ProfileController::class, 'userInvoice'])->name('user.invoice');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user && method_exists($user, 'canAccessAdmin') && $user->canAccessAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('profile.profile');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Keep profile.edit for Breeze nav links; send users to the storefront account page
    Route::get('/profile', function () {
        return redirect()->route('profile.profile');
    })->name('profile.edit');
});

/* REVIEW ROUTES */
Route::post('/product/{product}/review', [FrontController::class, 'addReview'])
    ->middleware('auth')
    ->name('review.store');
    
/*
|--------------------------------------------------------------------------
| STAFF DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/reviews', [ReviewController::class, 'index'])->name('admin.review.index');
});

/*
|--------------------------------------------------------------------------
| PRODUCTS & CATEGORIES - Admin + Web Designer (With Permission Checks)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staff'])->prefix('admin')->group(function () {

    // Category Management
    Route::middleware(['role:permission:categories'])->group(function () {
        Route::get('category', [CategoryController::class, 'index'])->name('category.index');
        Route::get('/category-export', [CategoryController::class, 'export'])->name('category.export');
        Route::get('/category-import-sample', [CategoryController::class, 'importSample'])->name('category.import.sample');
        Route::post('/category-import', [CategoryController::class, 'import'])->name('category.import');
        Route::get('category/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('category/store', [CategoryController::class, 'store'])->name('category.store');
        Route::get('category/edit/{category}', [CategoryController::class, 'edit'])->name('category.edit');
        Route::put('category/update/{category}', [CategoryController::class, 'update'])->name('category.update');
        Route::put('/category/status/{id}', [CategoryController::class, 'status'])->name('category.status');
        Route::post('/category/reorder', [CategoryController::class, 'reorder'])->name('category.reorder');
        Route::delete('category/delete/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
    });

    // Subcategory Management
    Route::middleware(['role:permission:subcategories'])->group(function () {
        Route::resource('subcategory', SubCategoryController::class);
        Route::put('subcategory/status/{id}', [SubCategoryController::class, 'status'])->name('subcategory.status');
        Route::get('subcategory-export', [SubCategoryController::class, 'export'])->name('subcategory.export');
        Route::get('subcategory-import-sample', [SubCategoryController::class, 'importSample'])->name('subcategory.import.sample');
        Route::post('subcategory-import', [SubCategoryController::class, 'import'])->name('subcategory.import');
    });

    // Product Management
    Route::middleware(['role:permission:products'])->group(function () {
        Route::resource('product', ProductController::class);
        Route::patch('/product/status/{product}', [ProductController::class, 'toggleStatus'])->name('product.status');
        Route::post('product/{product}/duplicate', [ProductController::class, 'duplicate'])->name('product.duplicate');
        Route::get('product-export', [ProductController::class, 'export'])->name('product.export');
        Route::get('product-import-sample', [ProductController::class, 'importSample'])->name('product.import.sample');
        Route::post('product-import', [ProductController::class, 'import'])->name('product.import');
        Route::get('get-subcategories/{category_id}', [ProductController::class, 'getSubcategories'])->name('get.subcategories');
    });

    // Reviews Management
    Route::middleware(['role:permission:reviews'])->group(function () {
        Route::resource('review', ReviewController::class)->names([
            'index' => 'admin.review.resource.index',
            'create' => 'admin.review.create',
            'store' => 'admin.review.store',
            'show' => 'admin.review.show',
            'edit' => 'admin.review.edit',
            'update' => 'admin.review.update',
            'destroy' => 'admin.review.destroy',
        ]);
        Route::post('review/status/{review}', [ReviewController::class, 'status'])->name('admin.review.status');
        Route::get('/reviews/export', [ReviewController::class, 'export'])->name('admin.review.export');
    });

        // Store Management
        Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
            Route::resource('stores', StoreController::class);
            Route::put('stores/{store}/toggle-status', [StoreController::class, 'toggleStatus'])->name('admin.stores.toggle-status');
            Route::delete('stores/{store}/image', [StoreController::class, 'deleteImage'])->name('admin.stores.delete-image');
            Route::post('stores/reorder', [StoreController::class, 'reorderStores'])->name('admin.stores.reorder');
    });

    // Return & Refund Management
    Route::middleware(['role:permission:return-refund'])->group(function () {
        Route::resource('return-refund', ReturnRefundController::class);
        Route::put('return-refund/{returnRefund}/toggle-status', [ReturnRefundController::class, 'toggleStatus'])->name('admin.return-refund.toggle-status');
    });
    
    // Warranty & Guarantee Management
    Route::middleware(['role:permission:warranty-guarantee'])->group(function () {
        Route::resource('warranty-guarantee', WarrantyGuaranteeController::class);
        Route::put('warranty-guarantee/{warrantyGuarantee}/toggle-status', [WarrantyGuaranteeController::class, 'toggleStatus'])->name('admin.warranty-guarantee.toggle-status');
    });
});

/*
|--------------------------------------------------------------------------
| CMS - Admin + Web Designer (With Permission Checks)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staff'])->prefix('admin')->group(function () {

    // Slider Management
    Route::middleware(['role:permission:sliders'])->group(function () {
        Route::resource('slider', SliderController::class);
        Route::put('/slider/status/{slider}', [SliderController::class, 'toggleStatus'])->name('slider.status');
        Route::get('slider-export', [SliderController::class, 'export'])->name('slider.export');
    });

    // Watches Banner Management (For Him / For Her homepage images)
    Route::middleware(['role:permission:watches'])->group(function () {
        Route::get('/watches', [WatchesController::class, 'index'])->name('watches.index');
        Route::post('/watches/update', [WatchesController::class, 'update'])->name('watches.update');
    });

    // Feature Management
    Route::middleware(['role:permission:features'])->group(function () {
        Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
    Route::get('/features/edit/{id}', [FeatureController::class, 'edit'])->name('features.edit');
    Route::put('/features/update/{id}', [FeatureController::class, 'update'])->name('features.update');
    });

    // Benefit Management
    Route::middleware(['role:permission:benefits'])->group(function () {
        Route::get('/benefits', [BenefitController::class, 'index'])->name('benefits.index');
        Route::get('/benefits/edit/{id}', [BenefitController::class, 'edit'])->name('benefits.edit');
        Route::put('/benefits/update/{id}', [BenefitController::class, 'update'])->name('benefits.update');
    });

    // Home Banners
    Route::middleware(['role:permission:banners'])->group(function () {
        Route::get('/home-banners', [HomeBannerController::class, 'index'])->name('home-banner.index');
        Route::post('/home-banners/update', [HomeBannerController::class, 'update'])->name('home-banner.update');
    });

    // About Page
    Route::middleware(['role:permission:about-us'])->group(function () {
        Route::get('/about-us', [AboutUsController::class, 'index'])->name('about-us.index');
        Route::post('/about-us', [AboutUsController::class, 'update'])->name('about-us.update');
    });

    // Privacy Policy & Terms and Conditions
    Route::middleware(['role:permission:cms'])->group(function () {
        Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy.index');
        Route::post('/privacy-policy', [PrivacyPolicyController::class, 'update'])->name('privacy-policy.update');
        Route::get('/terms-and-conditions', [TermsAndConditionController::class, 'index'])->name('terms-and-conditions.index');
        Route::post('/terms-and-conditions', [TermsAndConditionController::class, 'update'])->name('terms-and-conditions.update');
        Route::get('/play-fun-section', [PlayFunSectionController::class, 'edit'])->name('admin.play-fun-section.edit');
        Route::post('/play-fun-section', [PlayFunSectionController::class, 'update'])->name('admin.play-fun-section.update');
        Route::get('/learn-fun-section', [LearnFunSectionController::class, 'edit'])->name('admin.learn-fun-section.edit');
        Route::post('/learn-fun-section', [LearnFunSectionController::class, 'update'])->name('admin.learn-fun-section.update');
        Route::get('/how-to-play-section', [HowToPlaySectionController::class, 'edit'])->name('admin.how-to-play-section.edit');
        Route::post('/how-to-play-section', [HowToPlaySectionController::class, 'update'])->name('admin.how-to-play-section.update');
        Route::get('/home-faqs', [HomeFaqController::class, 'index'])->name('admin.home-faqs.index');
        Route::put('/home-faqs/section', [HomeFaqController::class, 'updateSection'])->name('admin.home-faqs.update-section');
        Route::get('/home-faqs/create', [HomeFaqController::class, 'create'])->name('admin.home-faqs.create');
        Route::post('/home-faqs', [HomeFaqController::class, 'store'])->name('admin.home-faqs.store');
        Route::get('/home-faqs/{homeFaq}/edit', [HomeFaqController::class, 'edit'])->name('admin.home-faqs.edit');
        Route::put('/home-faqs/{homeFaq}', [HomeFaqController::class, 'update'])->name('admin.home-faqs.update');
        Route::delete('/home-faqs/{homeFaq}', [HomeFaqController::class, 'destroy'])->name('admin.home-faqs.destroy');
        Route::put('/home-faqs/{homeFaq}/toggle-status', [HomeFaqController::class, 'toggleStatus'])->name('admin.home-faqs.toggle-status');

        Route::get('/help-categories', [HelpCategoryController::class, 'index'])->name('admin.help-categories.index');
        Route::get('/help-categories/create', [HelpCategoryController::class, 'create'])->name('admin.help-categories.create');
        Route::post('/help-categories', [HelpCategoryController::class, 'store'])->name('admin.help-categories.store');
        Route::get('/help-categories/{helpCategory}/edit', [HelpCategoryController::class, 'edit'])->name('admin.help-categories.edit');
        Route::put('/help-categories/{helpCategory}', [HelpCategoryController::class, 'update'])->name('admin.help-categories.update');
        Route::delete('/help-categories/{helpCategory}', [HelpCategoryController::class, 'destroy'])->name('admin.help-categories.destroy');

        Route::get('/help-faqs', [HelpFaqController::class, 'index'])->name('admin.help-faqs.index');
        Route::get('/help-faqs/create', [HelpFaqController::class, 'create'])->name('admin.help-faqs.create');
        Route::post('/help-faqs', [HelpFaqController::class, 'store'])->name('admin.help-faqs.store');
        Route::get('/help-faqs/{helpFaq}/edit', [HelpFaqController::class, 'edit'])->name('admin.help-faqs.edit');
        Route::put('/help-faqs/{helpFaq}', [HelpFaqController::class, 'update'])->name('admin.help-faqs.update');
        Route::delete('/help-faqs/{helpFaq}', [HelpFaqController::class, 'destroy'])->name('admin.help-faqs.destroy');
        Route::get('/home-testimonials', [HomeTestimonialController::class, 'index'])->name('admin.home-testimonials.index');
        Route::get('/home-testimonials/create', [HomeTestimonialController::class, 'create'])->name('admin.home-testimonials.create');
        Route::post('/home-testimonials', [HomeTestimonialController::class, 'store'])->name('admin.home-testimonials.store');
        Route::get('/home-testimonials/{homeTestimonial}/edit', [HomeTestimonialController::class, 'edit'])->name('admin.home-testimonials.edit');
        Route::put('/home-testimonials/{homeTestimonial}', [HomeTestimonialController::class, 'update'])->name('admin.home-testimonials.update');
        Route::delete('/home-testimonials/{homeTestimonial}', [HomeTestimonialController::class, 'destroy'])->name('admin.home-testimonials.destroy');
        Route::get('/home-highlights', [HomeHighlightController::class, 'index'])->name('admin.home-highlights.index');
        Route::get('/home-highlights/create', [HomeHighlightController::class, 'create'])->name('admin.home-highlights.create');
        Route::post('/home-highlights', [HomeHighlightController::class, 'store'])->name('admin.home-highlights.store');
        Route::get('/home-highlights/{homeHighlight}/edit', [HomeHighlightController::class, 'edit'])->name('admin.home-highlights.edit');
        Route::put('/home-highlights/{homeHighlight}', [HomeHighlightController::class, 'update'])->name('admin.home-highlights.update');
        Route::delete('/home-highlights/{homeHighlight}', [HomeHighlightController::class, 'destroy'])->name('admin.home-highlights.destroy');
    });

    // Contact Settings
    Route::middleware(['role:permission:settings'])->group(function () {
        Route::get('/contact-settings', [ContactSettingController::class, 'edit'])->name('admin.contact-settings.edit');
        Route::post('/contact-settings', [ContactSettingController::class, 'update'])->name('admin.contact-settings.update');
        Route::get('/footer-contact-settings', [FooterContactSettingController::class, 'edit'])->name('admin.footer-contact-settings.edit');
        Route::post('/footer-contact-settings', [FooterContactSettingController::class, 'update'])->name('admin.footer-contact-settings.update');
        Route::get('/header-top-bar-settings', [HeaderTopBarSettingController::class, 'edit'])->name('admin.header-top-bar-settings.edit');
        Route::post('/header-top-bar-settings', [HeaderTopBarSettingController::class, 'update'])->name('admin.header-top-bar-settings.update');

        // Contact Enquiry Management
        Route::get('/contact-enquiries',[ContactEnquiryController::class,'index'])->name('admin.contact-enquiries.index');
        Route::get('/contact-enquiries/edit/{contactEnquiry}',[ContactEnquiryController::class,'edit'])->name('admin.contact-enquiries.edit');
        Route::put('/contact-enquiries/update/{contactEnquiry}',[ContactEnquiryController::class,'update'])->name('admin.contact-enquiries.update');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Address Management
    Route::resource('address', AddressController::class);

    // Coupon Management
    Route::resource('coupon', CouponController::class);
    Route::put('/coupon/status/{coupon}', [CouponController::class, 'toggleStatus'])->name('coupon.status');
    Route::get('coupon-export', [CouponController::class, 'export'])->name('coupon.export');

    // GST Settings
    Route::get('/gst-settings', [GstSettingController::class, 'index'])->name('gst.settings.index');
    Route::get('/gst-settings/create', [GstSettingController::class, 'create'])->name('gst.settings.create');
    Route::post('/gst-settings/store', [GstSettingController::class, 'store'])->name('gst.settings.store');
    Route::get('/gst-settings/{gstSetting}/edit', [GstSettingController::class, 'edit'])->name('gst.settings.edit');
    Route::put('/gst-settings/{gstSetting}', [GstSettingController::class, 'update'])->name('gst.settings.update');
    Route::delete('/gst-settings/{gstSetting}', [GstSettingController::class, 'destroy'])->name('gst.settings.destroy');
    Route::patch('/gst-settings/{id}/status', [GstSettingController::class, 'status'])->name('gst.settings.status');

    // Shipping Settings
    Route::get('/shipping-settings', [ShippingSettingController::class, 'index'])->name('shipping.settings');
    Route::post('/shipping-settings', [ShippingSettingController::class, 'update'])->name('shipping.settings.update');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('admin.profile.password');

    // Contact Details API (for frontend)
    Route::get('/contact-details', [ContactController::class, 'contactDetails']);
    
    // Role Management Routes
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/assign-users', [RoleController::class, 'assignUsers'])->name('roles.assign-users');
    Route::post('roles/{role}/update-users', [RoleController::class, 'updateUsers'])->name('roles.update-users');
    Route::put('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    Route::post('roles/bulk-assign', [RoleController::class, 'bulkAssign'])->name('roles.bulk-assign');
    Route::get('roles/{role}/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');

    // User Management
    Route::resource('user', UserController::class);
    Route::get('customers', [UserController::class, 'customers'])->name('customer.index');
    Route::put('user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');
    Route::post('users/bulk-assign-role', [UserController::class, 'bulkAssignRole'])->name('users.bulk-assign-role');
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('users/get-by-role', [UserController::class, 'getUsersByRole'])->name('users.get-by-role');
    Route::get('user/{user}/details', [UserController::class, 'getUserDetails'])->name('user.details');
});

    Route::prefix('admin')->middleware(['auth'])->group(function () {

        // Gallery CRUD
        Route::get('/galleries', [GalleryController::class, 'index'])->name('gallery.index');
        Route::get('/galleries/create', [GalleryController::class, 'create'])->name('gallery.create');
        Route::post('/galleries/store', [GalleryController::class, 'store'])->name('gallery.store');

        Route::get('/galleries/edit/{gallery}', [GalleryController::class, 'edit'])->name('gallery.edit');
        Route::put('/galleries/update/{gallery}', [GalleryController::class, 'update'])->name('gallery.update');

        Route::delete('/galleries/delete/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

        // ✅ Gallery Images (FIXED)
        Route::get('/galleries/{gallery}/images', [GalleryController::class, 'images'])->name('gallery.images.index');
        Route::get('/galleries/{gallery}/images/create', [GalleryController::class, 'createImage'])->name('gallery.images.create');
        Route::post('/galleries/{gallery}/images/store', [GalleryController::class, 'storeImage'])->name('gallery.images.store');
        Route::delete('/galleries/{gallery}/images/{image}', [GalleryController::class, 'destroyImage'])->name('gallery.images.destroy');

        // Services CRUD
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
        
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services/store', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/edit/{service}', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/update/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/delete/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::delete('/services/{service}/images/{image}', [ServiceController::class, 'destroyImage'])->name('services.images.destroy');

    });

    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::post('/brands/reorder', [BrandController::class, 'reorder'])->name('brands.reorder');
        Route::resource('brands', BrandController::class);
    });

/*
|--------------------------------------------------------------------------
| ORDERS - Admin + Order Manager + Manager (With Permission Checks)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staff'])->prefix('admin')->group(function () {

    // Order Management
    Route::middleware(['role:permission:orders'])->group(function () {
        Route::resource('order', OrderController::class);
        Route::get('/transactions', [OrderController::class, 'transactions'])->name('transactions.index');
        Route::put('order/status/{order}', [OrderController::class, 'statusUpdate'])->name('order.status');
        Route::post('/admin/orders/{order}/remaining-payment', [OrderController::class, 'receiveRemainingPayment'])->name('order.remaining.payment');
        Route::put('order/payment-status/{order}', [OrderController::class, 'paymentStatusUpdate'])->name('order.payment.status');
        Route::get('order/invoice/{order}', [OrderController::class, 'invoice'])->name('order.invoice');
        Route::get('order/print/{order}', [OrderController::class, 'printInvoice'])->name('order.print');
        Route::get('order/pdf/{order}', [OrderController::class, 'downloadPdf'])->name('order.pdf');
        Route::post('order/{order}/payment', [OrderPaymentController::class, 'store'])->name('order.payment.store');
        Route::delete('order/payment/{payment}', [OrderController::class, 'destroy'])->name('order.payment.delete');
        Route::post('order/{order}/approve-cancel-request', [OrderController::class, 'approveCancelRequest'])->name('order.cancel_request.approve');
        Route::post('order/{order}/reject-cancel-request', [OrderController::class, 'rejectCancelRequest'])->name('order.cancel_request.reject');
        Route::post('order/{order}/approve-return-request', [OrderController::class, 'approveReturnRequest'])->name('order.return_request.approve');
        Route::post('order/{order}/reject-return-request', [OrderController::class, 'rejectReturnRequest'])->name('order.return_request.reject');
        Route::get('order-export', [OrderController::class, 'export'])->name('order.export');
        Route::get('order-import-sample', [OrderController::class, 'importSample'])->name('order.import.sample');
        Route::post('order-import', [OrderController::class, 'import'])->name('order.import');

        // Refund Management
        Route::post('/orders/{order}/refund', [RefundController::class, 'store'])->name('refund.store');
        Route::get('/refund/{refund}/approve', [RefundController::class, 'approve'])->name('refund.approve');
        Route::get('/refund/{refund}/reject', [RefundController::class, 'reject'])->name('refund.reject');
        Route::get('/refund/{refund}/complete', [RefundController::class, 'complete'])->name('refund.complete');
    }); 
    
    // Reports
    Route::middleware(['role:permission:reports'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/orders', [ReportController::class, 'orders'])->name('reports.orders');
        Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
    });
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';