<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/google-login', [AuthController::class, 'googleLogin']);

Route::controller(AuthController::class)->group(function () {
    Route::post('/auth', 'auth');
    Route::post('/verify-otp', 'verifyOtp');
    Route::post('/resend-otp', 'resendOtp');
    Route::post('/google-login', 'googleLogin');
    Route::middleware('auth:sanctum')->post('/logout', 'logout');
});

Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user-profile', 'profile');
        Route::post('/user-profile/update', 'updateProfile');
        Route::post('/logout', 'logout');
    });


    Route::controller(ProductController::class)->group(function () {
        Route::post('/merge-cart', 'mergeCart');
        Route::post('/add-to-wishlist', 'addToWishlist');
        Route::get('/get-wishlist', 'getWishlist');
        Route::post('/remove-wishlist-item', 'removeWishlistItem');
        Route::post('/clear-wishlist', 'clearWishlist');
        Route::get('/wishlist-count', 'wishlistCount');
        Route::post('/add-review', 'addReview');
        Route::get('/get-review/{id}', 'getReview');
        Route::post('/edit-review', 'editReview');
        Route::post('/update-review', 'updateReview');
        Route::post('/delete-review', 'deleteReview');
        Route::get('/my-reviews', 'myReviews');
        Route::post('/review-like', 'reviewLike');
        Route::post('/review-dislike', 'reviewDislike');
        Route::post('/apply-coupon', 'applyCoupon');    
        Route::post('/remove-coupon-from-cart', 'removeCoupon');
    });

    Route::controller(PaymentController::class)->group(function () {
        Route::post('/payment/create-order', 'createOrder');
        Route::post('/payment/verify', 'verifyPayment');
    });

    Route::controller(AddressController::class)->group(function () {
        Route::get('/all-addresses', 'index');
Route::post('/add-address', [AddressController::class, 'store']);
        Route::get('/address/{id}', 'show');
        Route::post('/update-address', 'update');
        Route::post('/delete-address', 'destroy');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::post('/order/create', 'createOrder');
        Route::get('/order/payment-status/{id}', 'paymentStatus');
        Route::get('/my-orders', 'myOrders');
        Route::get('/order/view/{id}', 'orderView');
        Route::post('/order/cancel/{id}', 'cancelOrder');
        Route::get('/order/track/{id}', 'trackOrder');
    });
});

/*
|--------------------------------------------------------------------------
| Product API Routes (NO AUTH)
|--------------------------------------------------------------------------
*/
Route::controller(ProductController::class)->group(function () {

// ========== BUY NOW ROUTES (MUST BE FIRST - BEFORE CATEGORY ROUTES) ==========
Route::post('/buy-now/add', 'addToBuyNow');
Route::get('/buy-now/get', 'getBuyNow');
Route::post('/buy-now/update', 'updateBuyNow');
Route::post('/buy-now/update-address', 'updateBuyNowAddress');
Route::post('/buy-now/apply-coupon', 'applyCouponToBuyNow');  // Apply coupon to buy now
Route::post('/buy-now/remove-coupon', 'removeCouponFromBuyNow');  // Remove coupon from buy now
Route::post('/buy-now/checkout', 'buyNowCheckout');  // Checkout from buy now
Route::post('/buy-now/remove', 'removeBuyNow');  // 👈 ADD THIS LINE - Remove/clear buy now item
Route::get('/contact-details', [ContactController::class, 'contactDetails']);
    // Home page
    Route::get('/home', 'home');
    Route::get('/product-listings', 'productListings');
    
    // Category
    Route::get('/categories', 'categories');
    Route::get('/subcategories', 'subcategories');
    Route::get('/all-category-subcategories', 'allCategorySubcategories');

    // Slider
    Route::get('/sliders', 'sliders');

    // Blog List
    Route::get('/related-blogs/{slug}', 'relatedBlogs');
    Route::get('/blogs-categories', 'blogCategories');
    Route::post('/blogs-list', 'blogs');
    Route::get('/blogs/{slug}', 'blogDetails');

    // Search Products
    Route::get('/search', 'searchProducts');

    // Product List
    Route::get('/products', 'index');
    Route::get('/product/{slug}', 'productDetails');
    Route::get('/products/best-selling', 'bestSelling');
    Route::get('/products/top-rated', 'topRated');

    // Cart Routes
    Route::post('/add-to-cart', 'addToCart');
    Route::post('/apply-coupon-to-cart', 'applyCouponToCart');
    Route::get('/get-cart', 'getCart');
    Route::post('/update-cart', 'updateCart');
    Route::post('/remove-cart-item', 'removeCartItem');
    Route::post('/clear-cart', 'clearCart');
    Route::post('/cart-count', 'cartCount');

    Route::get('/available-coupons', 'availableCoupons');
    // ========== CATEGORY ROUTES (MUST BE LAST - THEY CATCH ALL SINGLE SEGMENTS) ==========

    Route::get('/price-range/{category_slug}/{subcategory_slug?}', 'productPriceRange');
    
    Route::get('/{category_slug}', 'categorywiseProduct');
    Route::get('/{category_slug}/{subcategory_slug}', 'subcategorywiseProduct');

    //footer
    Route::post('/subscribe-email','emails');
    Route::post('/product-question', 'addProductQuestion');
});

