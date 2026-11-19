<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Gallery;
use App\Models\BuyNow;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Feature;
use App\Models\Benefit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Contact;
use App\Models\ContactSetting;
use App\Models\Review;
use App\Models\Brand;
use App\Models\Wishlist;
use App\Models\ReviewReaction;
use App\Models\ShippingSetting;
use App\Services\CartService;
use App\Models\AboutUsSection;
use App\Models\PlayFunSection;
use App\Models\LearnFunSection;
use App\Models\HowToPlaySection;
use App\Models\HomeFaq;
use App\Models\HomeFaqSection;
use App\Models\HelpCategory;
// REMOVED: use App\Models\HomeTestimonial;
use App\Models\HomeHighlight;
use App\Models\Watches;
use App\Models\ContactEnquiry;
use App\Models\PrivacyPolicy;
use App\Models\TermsAndCondition;
use App\Models\Coupon;
use App\Mail\OrderAdminMail;
use App\Mail\OrderSuccessMail;
use App\Mail\ContactEnquiryUserMail;
use App\Mail\ContactEnquiryAdminMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

class FrontController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    //Home Page
    public function index()
    {
        $sliders = Slider::where('status', 1)->latest()->get();
    
        $categories = Category::where('status', 1)
            ->withStartingFromPrice()
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->take(3)
            ->get();

        $brands = Brand::where('status',1)
            ->orderBy('position')
            ->get();

        $features = Feature::where('status', 1)
            ->latest()
            ->take(4)
            ->get();

        $benefits = Benefit::where('status', 1)
            ->latest()
            ->take(4)
            ->get();
        
        $bestSellingProducts = Product::where('status', 1)
            ->where('is_best_selling', 1)
            ->with(['brand', 'category'])
            ->withCount([
                'reviews as reviews_count' => fn ($query) => $query->where('status', 1),
            ])
            ->latest()
            ->take(8)
            ->get();

        $this->attachProductReviewMeta($bestSellingProducts);

        $newArrivalsProducts = Product::where('status', 1)
            ->where('is_new_arrival', 1)
            ->with(['brand', 'category'])
            ->withCount([
                'reviews as reviews_count' => fn ($query) => $query->where('status', 1),
            ])
            ->latest()
            ->take(8)
            ->get();

        $this->attachProductReviewMeta($newArrivalsProducts);

        $galleries = Gallery::where('status', 1)
        ->latest()
        ->take(2) // because your UI has 2 boxes
        ->get();

        $playFunSection = PlayFunSection::first();
        $learnFunSection = LearnFunSection::first();
        $howToPlaySection = HowToPlaySection::first();
        $homeFaqs = HomeFaq::active()
            ->ordered()
            ->get();
        $homeFaqSection = HomeFaqSection::first();

        $homeTestimonials = Review::with(['user', 'product'])
            ->where('status', 1)
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->latest()
            ->take(8)
            ->get();

        $homeHighlights = HomeHighlight::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $watchBanners = Watches::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('frontend.pages.index', compact(
            'sliders',
            'features',
            'benefits',
            'categories',
            'bestSellingProducts',
            'newArrivalsProducts',
            'galleries',
            'playFunSection',
            'learnFunSection',
            'howToPlaySection',
            'homeFaqs',
            'homeFaqSection',
            'homeTestimonials',
            'homeHighlights',
            'brands',
            'watchBanners'
        ));
    }

    //About Page
    public function about()
    {
        $aboutSection = AboutUsSection::query()->orderBy('id')->first();

        return view('frontend.pages.about', compact('aboutSection'));
    }

    //Gallery Page
    public function gallery(){
        $galleries = Gallery::where('status', 1)
        ->latest()
        ->paginate(6);
        return view('frontend.pages.gallery', compact('galleries'));
    }

    public function galleryDetails($slug)
    {
        $gallery = Gallery::where('slug', $slug)
                    ->where('status',1)
                    ->with('images')
                    ->firstOrFail();

        return view('frontend.pages.gallery-details', compact('gallery'));
    }

    //Product Details Page
    public function productDetails(Request $request)
    {
        $product = Product::where('status', 1)
            ->with(['category', 'brand', 'faqs'])
            ->withCount([
                'reviews as reviews_count' => fn ($query) => $query->where('status', 1),
            ])
            ->when($request->filled('slug'), fn ($q) => $q->where('slug', $request->slug))
            ->when($request->filled('id'), fn ($q) => $q->where('id', $request->id))
            ->when(! $request->filled('slug') && ! $request->filled('id'), fn ($q) => $q->whereRaw('1 = 0'))
            ->first();

        if (! $product) {
            abort(404);
        }

        $this->attachProductReviewMeta(collect([$product]));

        $related_products = Product::query()
            ->where('status', 1)
            ->where('id', '!=', $product->id)
            ->when($product->brand_id, fn ($q) => $q->where('brand_id', $product->brand_id))
            ->with(['brand', 'category'])
            ->withCount([
                'reviews as reviews_count' => fn ($query) => $query->where('status', 1),
            ])
            ->latest()
            ->take(8)
            ->get();

        if ($related_products->count() < 8) {
            $excludeIds = $related_products->pluck('id')->push($product->id)->all();

            $fallbackByCategory = Product::query()
                ->where('status', 1)
                ->whereNotIn('id', $excludeIds)
                ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
                ->with(['brand', 'category'])
                ->withCount([
                    'reviews as reviews_count' => fn ($query) => $query->where('status', 1),
                ])
                ->latest()
                ->take(8 - $related_products->count())
                ->get();

            $related_products = $related_products->concat($fallbackByCategory);
        }

        if ($related_products->count() < 8) {
            $excludeIds = $related_products->pluck('id')->push($product->id)->all();

            $fallbackLatest = Product::query()
                ->where('status', 1)
                ->whereNotIn('id', $excludeIds)
                ->with(['brand', 'category'])
                ->withCount([
                    'reviews as reviews_count' => fn ($query) => $query->where('status', 1),
                ])
                ->latest()
                ->take(8 - $related_products->count())
                ->get();

            $related_products = $related_products->concat($fallbackLatest);
        }

        $this->attachProductReviewMeta($related_products);

        $wishlistIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->map(fn ($id) => (int) $id)->all()
            : [];

        $features = Feature::where('status', 1)->latest()->take(6)->get();
        $sliderImages = $product->getSliderImages();

        $reviews = Review::where('product_id', $product->id)
            ->where('status', 1)
            ->with('user')
            ->latest()
            ->get();

        $reviewCount = $reviews->count();
        $averageRatingValue = $reviewCount > 0
            ? round((float) $reviews->avg('rating'), 1)
            : 0.0;

        $ratingBreakdown = [];
        foreach ([5, 4, 3, 2, 1] as $star) {
            $count = $reviews->where('rating', $star)->count();
            $ratingBreakdown[$star] = [
                'count' => $count,
                'percent' => $reviewCount > 0 ? (int) round(($count / $reviewCount) * 100) : 0,
            ];
        }

        $userHasReviewed = Auth::check()
            ? Review::where('product_id', $product->id)->where('user_id', Auth::id())->exists()
            : false;

        return view('frontend.pages.product_details', compact(
            'product',
            'related_products',
            'wishlistIds',
            'features',
            'sliderImages',
            'reviews',
            'reviewCount',
            'averageRatingValue',
            'ratingBreakdown',
            'userHasReviewed'
        ));
    }

    public function checkDelivery(Request $request)
    {
        $validated = $request->validate([
            'pincode' => ['required', 'digits:6'],
        ]);

        $pincode = $validated['pincode'];
        $shippingSetting = ShippingSetting::first();
        $shippingCharge = (float) ($shippingSetting->shipping_charge ?? 0);
        $freeShippingAmount = (float) ($shippingSetting->free_shipping_amount ?? 0);

        return response()->json([
            'success' => true,
            'pincode' => $pincode,
            'serviceable' => true,
            'estimated_days' => '2-5 working days',
            'shipping_charge' => $shippingCharge,
            'free_shipping_minimum' => $freeShippingAmount,
            'message' => 'Delivery is available for this pincode.',
        ]);
    }

    public function productListing(Request $request)
    {
        $gender = $request->input('gender');
        $filter = $request->input('filter');
        $categoryId = $request->filled('category') ? (int) $request->input('category') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : null;
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : null;
        $sort = $request->input('sort');
        $brandIds = array_values(array_filter(array_map('intval', (array) $request->input('brand', []))));

        $categories = Category::where('status', 1)
            ->withStartingFromPrice()
            ->withCount('products')
            ->when(
                in_array($gender, ['women', 'men'], true),
                fn ($query) => $query->forMenuGender($gender)
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Same fallback as header menu: if no gender-specific categories, use all active ones.
        if (in_array($gender, ['women', 'men'], true) && $categories->isEmpty()) {
            $categories = Category::where('status', 1)
                ->withStartingFromPrice()
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        $genderCategoryIds = in_array($gender, ['women', 'men'], true)
            ? $categories->pluck('id')->all()
            : [];

        $priceSql = Category::productPriceSql('products');

        $productsQuery = Product::query()
            ->where('products.status', 1)
            ->with(['brand', 'category'])
            ->when(
                $categoryId,
                fn ($query) => $query->where('products.category_id', $categoryId),
                fn ($query) => $query->when(
                    in_array($gender, ['women', 'men'], true),
                    function ($q) use ($gender, $genderCategoryIds) {
                        $q->where(function ($inner) use ($gender, $genderCategoryIds) {
                            $inner->forMenuGender($gender);
                            if (! empty($genderCategoryIds)) {
                                $inner->orWhereIn('products.category_id', $genderCategoryIds);
                            }
                        });
                    }
                )
            )
            ->when($filter === 'best_selling', fn ($query) => $query->where('products.is_best_selling', 1))
            ->when($filter === 'new_arrival', fn ($query) => $query->where('products.is_new_arrival', 1))
            ->when(! empty($brandIds), fn ($query) => $query->whereIn('products.brand_id', $brandIds))
            ->when(
                $minPrice !== null && $minPrice > 0,
                fn ($query) => $query->whereRaw("({$priceSql}) >= ?", [$minPrice])
            )
            ->when(
                $maxPrice !== null && $maxPrice > 0,
                fn ($query) => $query->whereRaw("({$priceSql}) > 0 AND ({$priceSql}) <= ?", [$maxPrice])
            )
            ->withCount([
                'reviews as reviews_count' => fn ($query) => $query->where('reviews.status', 1),
            ]);

        match ($sort) {
            'price_asc' => $productsQuery->orderByRaw("{$priceSql} asc"),
            'price_desc' => $productsQuery->orderByRaw("{$priceSql} desc"),
            'brand_asc' => $productsQuery->orderBy(
                Brand::select('name')->whereColumn('brands.id', 'products.brand_id')->limit(1)
            ),
            'brand_desc' => $productsQuery->orderByDesc(
                Brand::select('name')->whereColumn('brands.id', 'products.brand_id')->limit(1)
            ),
            'relevance' => $productsQuery
                ->orderByDesc('products.is_featured')
                ->orderByDesc('products.is_best_selling')
                ->latest('products.created_at'),
            default => $productsQuery->latest('products.id'),
        };

        $products = $productsQuery->paginate(15)->withQueryString();
        $this->attachProductReviewMeta($products);

        $brands = Brand::where('status', 1)->orderBy('name')->get();

        $listingTitle = $this->resolveListingTitle($gender, $filter, $maxPrice, $categoryId, $categories, $brandIds);
        $breadcrumbLabel = $this->resolveListingBreadcrumb($gender, $filter, $brandIds, $maxPrice);

        return view('frontend.pages.product_listing', compact(
            'categories',
            'products',
            'brands',
            'listingTitle',
            'breadcrumbLabel',
            'gender',
            'filter',
            'maxPrice',
            'minPrice',
            'sort',
            'brandIds',
            'categoryId'
        ));
    }

    private function resolveListingTitle(?string $gender, ?string $filter, ?float $maxPrice, ?int $categoryId, $categories, array $brandIds = []): string
    {
        if ($categoryId) {
            $category = $categories->firstWhere('id', $categoryId);
            if (! $category) {
                $category = Category::where('status', 1)->find($categoryId);
            }
            if ($category) {
                return $category->name;
            }
        }

        if (! empty($brandIds)) {
            $brandNames = Brand::where('status', 1)
                ->whereIn('id', $brandIds)
                ->orderBy('name')
                ->pluck('name')
                ->all();

            if (count($brandNames) === 1) {
                return $brandNames[0] . ' Watches';
            }

            if (! empty($brandNames)) {
                return implode(', ', $brandNames) . ' Watches';
            }
        }

        $genderLabel = match ($gender) {
            'women' => 'Women',
            'men' => 'Men',
            default => null,
        };

        if ($filter === 'best_selling') {
            return $genderLabel ? "{$genderLabel} Bestsellers" : 'Bestsellers';
        }

        if ($filter === 'new_arrival') {
            return $genderLabel ? "{$genderLabel} New Arrivals" : 'New Arrivals';
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $priceLabel = 'Under ₹' . number_format($maxPrice);
            $minPrice = request()->filled('min_price') ? (float) request('min_price') : null;
            if ($minPrice !== null && $minPrice > 0) {
                $priceLabel = '₹' . number_format($minPrice) . ' - ₹' . number_format($maxPrice);
            }
            return $genderLabel ? "{$genderLabel} Watches {$priceLabel}" : "Watches {$priceLabel}";
        }

        return $genderLabel ? "{$genderLabel} Watches" : 'All Watches';
    }

    private function resolveListingBreadcrumb(?string $gender, ?string $filter, array $brandIds = [], ?float $maxPrice = null): string
    {
        if (! empty($brandIds)) {
            $brandNames = Brand::where('status', 1)
                ->whereIn('id', $brandIds)
                ->orderBy('name')
                ->pluck('name')
                ->all();

            if (count($brandNames) === 1) {
                return $brandNames[0];
            }

            if (! empty($brandNames)) {
                return 'Brands';
            }
        }

        if ($filter === 'best_selling') {
            return 'Bestsellers';
        }

        if ($filter === 'new_arrival') {
            return 'New Arrivals';
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $priceLabel = 'Under ₹' . number_format($maxPrice);
            $minPrice = request()->filled('min_price') ? (float) request('min_price') : null;
            if ($minPrice !== null && $minPrice > 0) {
                $priceLabel = '₹' . number_format($minPrice) . ' - ₹' . number_format($maxPrice);
            }
            return match ($gender) {
                'women' => "Women · {$priceLabel}",
                'men' => "Men · {$priceLabel}",
                default => $priceLabel,
            };
        }

        return match ($gender) {
            'women' => 'Women',
            'men' => 'Men',
            default => 'Watches',
        };
    }

    public function productListingGrid($slug)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();
            [$products, , , $priceRanges] = $this->buildCategoryListingProducts($category);
            $products->withPath(rtrim(request()->getBaseUrl(), '/') . '/ajax/product-listing/' . $category->slug);

            $html = view('frontend.partials.product-listing-grid', compact('products'))->render();

            Log::info('Product listing grid loaded', [
                'slug' => $slug,
                'category_filter' => request()->input('category'),
                'price_filter' => request()->input('price'),
                'page' => request()->input('page'),
                'product_total' => $products->total(),
                'product_count' => $products->count(),
                'html_length' => strlen($html),
                'url' => request()->fullUrl(),
            ]);

            return response()->json([
                'success' => true,
                'html' => $html,
                'price_ranges' => $priceRanges,
                'meta' => [
                    'product_total' => $products->total(),
                    'product_count' => $products->count(),
                    'html_length' => strlen($html),
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logProductListingError('productListingGrid', $e, ['slug' => $slug]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logProductListingClientError(Request $request)
    {
        $payload = [
            'event' => $request->input('event', 'error'),
            'message' => $request->input('message'),
            'url' => $request->input('url'),
            'http_status' => $request->input('status'),
            'response_preview' => $request->input('response'),
            'html_length' => $request->input('html_length'),
            'product_boxes' => $request->input('product_boxes'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        Log::error('Product listing AJAX client event', $payload);

        return response()->json(['success' => true]);
    }

    public function superDeals()
    {
        $categories = $this->listingCategories(true);
        [$products, $selectedCategoryIds, $selectedPriceKeys, $priceRanges] = $this->buildSuperDealProducts();

        $category = (object) [
            'name' => 'Super Deals',
            'slug' => 'super-deals',
            'id' => null,
        ];

        $listingBreadcrumb = 'Super Deals';
        $listingTitle = 'Super Deals';
        $breadcrumbLabel = 'Super Deals';
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $gender = null;
        $filter = null;
        $maxPrice = null;
        $minPrice = null;
        $sort = null;
        $brandIds = [];
        $categoryId = null;
        $filterAction = route('super.deals');
        $gridUrl = rtrim(request()->getBaseUrl(), '/') . '/ajax/super-deals-products';
        $pageUrl = url()->current();
        $showDiscountTagOnly = true;

        return view('frontend.pages.product_listing', compact(
            'category',
            'categories',
            'products',
            'brands',
            'selectedCategoryIds',
            'selectedPriceKeys',
            'priceRanges',
            'listingBreadcrumb',
            'listingTitle',
            'breadcrumbLabel',
            'gender',
            'filter',
            'maxPrice',
            'minPrice',
            'sort',
            'brandIds',
            'categoryId',
            'filterAction',
            'gridUrl',
            'pageUrl',
            'showDiscountTagOnly'
        ));
    }

    public function superDealsGrid()
    {
        try {
            [$products, , , $priceRanges] = $this->buildSuperDealProducts();
            $products->withPath(rtrim(request()->getBaseUrl(), '/') . '/ajax/super-deals-products');

            $filterAction = route('super.deals');
            $showDiscountTagOnly = true;

            return response()->json([
                'success' => true,
                'html' => view('frontend.partials.product-listing-grid', compact(
                    'products',
                    'filterAction',
                    'showDiscountTagOnly'
                ))->render(),
                'price_ranges' => $priceRanges,
            ]);
        } catch (\Throwable $e) {
            $this->logProductListingError('superDealsGrid', $e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function logProductListingError(string $context, \Throwable $e, array $extra = []): void
    {
        Log::error('Product listing grid failed: ' . $context, array_merge([
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'category_filter' => request()->input('category'),
            'price_filter' => request()->input('price'),
            'page' => request()->input('page'),
            'url' => request()->fullUrl(),
        ], $extra));

        report($e);
    }

    public function buyNow(Request $request, $id)
    {
        if (!Auth::check()) {
            session(['url.intended' => route('checkout.buy', $id)]);
            return redirect()->route('login')->with('login_required', true);
        }

        $product = Product::findOrFail($id);
        $qty = min(10, max(1, (int) $request->input('qty', 1)));

        BuyNow::where('user_id', Auth::id())->delete();

        BuyNow::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $qty,
            'coupon_code' => session('applied_coupon_code'),
        ]);

        return redirect()->route('checkout.shipping');
    }

    public function addToCart(Request $request, $product_id)
    {
        $productId = (int) $product_id;

        if ($this->cartService->isInCart($productId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'already_in_cart' => true,
                    'message' => 'Product already exists in cart',
                    'cart_count' => $this->cartService->getCartCount(),
                    'product_id' => $productId,
                ]);
            }

            return back()->with('success', 'Product already exists in cart');
        }

        $qty = min(10, max(1, (int) $request->input('qty', 1)));
        $cartCount = $this->cartService->addItem($productId, $qty);
    
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'already_in_cart' => false,
                'message' => 'Product added to cart successfully',
                'cart_count' => $cartCount,
                'product_id' => $productId,
                'is_guest' => !Auth::check(),
            ]);
        }

        return back()->with('success', 'Added to cart');
    }

    public function updateGuestCart(Request $request, Product $product)
    {
        if (Auth::check()) {
            return redirect()->route('cart');
        }

        $qty = min(10, max(1, (int) $request->input('quantity', 1)));
        $this->cartService->updateGuestItem($product->id, $qty);

        return back()->with('success', 'Cart updated');
    }

    public function removeGuestCart(Product $product)
    {
        if (Auth::check()) {
            return redirect()->route('cart');
        }

        $this->cartService->removeGuestItem($product->id);

        return back()->with('success', 'Item removed from cart');
    }

    public function updateCart(Request $request, Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $qty = min(10, max(1, (int) $request->input('quantity', 1)));
        $cart->update(['quantity' => $qty]);

        return back()->with('success', 'Cart updated');
    }

    public function removeFromCart(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Item removed from cart');
    }

    //Blogs Page
    public function blogs(){
        $blogs = Blog::where('status', 1)
        ->latest()
        ->paginate(6);
        return view('frontend.pages.blogs', compact('blogs'));
    }

    public function blogsDetails($slug){
        $blog = Blog::where('slug', $slug)
        ->where('status', 1)
        ->firstOrFail();
        return view('frontend.pages.blogs_details', compact('blog'));
    }

    //Cart Page
    public function cart()
    {
        $summary = $this->buildCartSummary($this->cartService->getCartItems());
        $cartItems = collect($summary['cartItems']);
        $subtotal = (float) $summary['subtotal'];

        $availableCoupons = Coupon::where('status', 1)
            ->latest()
            ->get()
            ->map(fn (Coupon $coupon) => $coupon->buildAvailableOfferForCart($cartItems, $subtotal))
            ->filter()
            ->sortByDesc(fn (array $offer) => $offer['discount'])
            ->values();

        return view('frontend.pages.cart', array_merge($summary, compact('availableCoupons')));
    }

    public function availableCoupons()
    {
        $summary = $this->buildCartSummary($this->resolveCheckoutCartItems());
        $cartItems = collect($summary['cartItems']);
        $subtotal = (float) $summary['subtotal'];
        $appliedCode = $summary['couponCode'] ?? null;

        $coupons = Coupon::where('status', 1)
            ->latest()
            ->get()
            ->map(fn (Coupon $coupon) => $coupon->buildAvailableOfferForCart($cartItems, $subtotal))
            ->filter()
            ->sortByDesc(fn (array $offer) => $offer['discount'])
            ->values();

        return response()->json([
            'coupons' => $this->formatCouponOffersForJson($coupons, $appliedCode),
            'subtotal' => $subtotal,
        ]);
    }

    public function wishlist()
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('login_required', true);
        }

        $user = Auth::user();
        $wishlistItems = Wishlist::with(['product.brand', 'product.category'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $products = $wishlistItems->pluck('product')->filter();
        $this->attachProductReviewMeta($products);

        return view('frontend.pages.myaccount_wishlist', compact('user', 'wishlistItems'));
    }

    public function addToWishlist(Request $request, $productId)
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'login_required' => true,
                    'message' => 'Please login to manage wishlist',
                ], 401);
            }

            return redirect()->route('login')->with('login_required', true);
        }

        $product = Product::where('status', 1)->findOrFail($productId);

        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => true,
                'wishlist_count' => $this->wishlistCountForUser(),
                'message' => 'Added to wishlist',
            ]);
        }

        return back()->with('success', 'Added to wishlist');
    }

    public function removeFromWishlist(Request $request, Product $product)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('login_required', true);
        }

        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => false,
                'wishlist_count' => $this->wishlistCountForUser(),
                'message' => 'Removed from wishlist',
            ]);
        }

        return back()->with('success', 'Removed from wishlist');
    }

    public function toggleWishlist(Request $request, $productId)
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'login_required' => true,
                    'message' => 'Please login to manage wishlist',
                    'redirect' => route('login'),
                ], 401);
            }

            return redirect()->route('login')->with('login_required', true);
        }

        $product = Product::where('status', 1)->findOrFail($productId);
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message = 'Removed from wishlist';
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ]);
            $inWishlist = true;
            $message = 'Added to wishlist';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist,
                'wishlist_count' => $this->wishlistCountForUser(),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    private function wishlistCountForUser(): int
    {
        if (! Auth::check()) {
            return 0;
        }

        return (int) Wishlist::where('user_id', Auth::id())->count();
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->coupon_code));
        $cartItems = $this->resolveCheckoutCartItems();

        if ($cartItems->isEmpty()) {
            return $this->couponActionResponse(
                $request,
                false,
                'Your cart is empty.'
            );
        }

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [$code])
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            return $this->couponActionResponse(
                $request,
                false,
                'Invalid promo code.'
            );
        }

        $subtotal = 0;
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product) {
                continue;
            }
            $price = $cartItem->product->selling_price ?? $cartItem->product->price ?? 0;
            $subtotal += $price * $cartItem->quantity;
        }

        $result = $coupon->calculateDiscountForItems($cartItems, $subtotal);

        if ($result['discount'] <= 0) {
            return $this->couponActionResponse(
                $request,
                false,
                $result['message'] ?? 'Promo code cannot be applied.'
            );
        }

        session([
            'applied_coupon_code' => $coupon->code,
            'applied_coupon_discount' => $result['discount'],
        ]);

        if (Auth::check()) {
            try {
                Cart::where('user_id', Auth::id())->update(['coupon_code' => $coupon->code]);
            } catch (\Throwable $e) {
                // coupon_code column may be missing on older schemas
            }

            try {
                BuyNow::where('user_id', Auth::id())->update(['coupon_code' => $coupon->code]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $summary = $this->buildCartSummary($cartItems);

        return $this->couponActionResponse(
            $request,
            true,
            'Promo code applied. You saved ₹' . number_format($result['discount'], 2) . '.',
            $summary
        );
    }

    public function removeCoupon(Request $request)
    {
        session()->forget(['applied_coupon_code', 'applied_coupon_discount']);

        if (Auth::check()) {
            try {
                Cart::where('user_id', Auth::id())->update(['coupon_code' => null]);
            } catch (\Throwable $e) {
                // coupon_code column may be missing on older schemas
            }

            try {
                BuyNow::where('user_id', Auth::id())->update(['coupon_code' => null]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $summary = $this->buildCartSummary($this->resolveCheckoutCartItems());

        return $this->couponActionResponse(
            $request,
            true,
            'Promo code removed.',
            $summary
        );
    }

    public function addReview(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to write a review');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|max:2000',
        ]);

        $alreadyReviewed = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'You have already submitted a review for this product.');
        }

        $title = trim((string) ($validated['title'] ?? ''));
        $comment = trim($validated['comment']);
        $reviewText = $title !== '' ? $title . "\n\n" . $comment : $comment;

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => (int) $validated['rating'],
            'review' => $reviewText,
            'status' => 0,
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted and is pending approval.');
    }

    public function indexReview(Product $product)
    {
        $reviews = Review::where('product_id', $product->id)
            ->with('user')
            ->latest()
            ->get();
    
        return view('frontend.pages.review.index', compact('product', 'reviews'));
    }

    public function showReview(Product $product, Review $review)
    {
        $review = Review::where('id', $review->id)
        ->where('product_id', $product->id)
        ->where('status', 1)
        ->firstOrFail();

        return view('frontend.pages.review.show', compact('product', 'review'));
    }

    //Buy Now Page

    /* CHECKOUT SHIPPING */
    public function checkoutShipping()
    {
        if (!Auth::check()) {
            session(['url.intended' => route('checkout.shipping')]);
            return redirect()->route('login')->with('login_required', true);
        }

        $checkout = $this->buildCheckoutSummary();

        if ($checkout['itemCount'] === 0) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();
        $addresses = Address::where('user_id', $user->id)
            ->where('status', 1)
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $cartItems = collect($checkout['cartItems']);
        $availableCoupons = Coupon::where('status', 1)
            ->latest()
            ->get()
            ->map(fn (Coupon $coupon) => $coupon->buildAvailableOfferForCart($cartItems, (float) $checkout['subtotal']))
            ->filter()
            ->sortByDesc(fn (array $offer) => $offer['discount'])
            ->values();

        return view(
            'frontend.pages.checkout_shipping',
            array_merge($checkout, compact('user', 'addresses', 'defaultAddress', 'availableCoupons'))
        );
    }

    public function placeOrder(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('cart')->with('login_required', true);
        }

        $request->validate([
            'payment_method' => 'required|in:cod,razorpay',
        ]);

        $user = auth()->user();
        $checkout = $this->buildCheckoutSummary();
        $paymentMethod = $request->input('payment_method', 'cod');
        $wantsJson = $request->ajax() || $request->wantsJson() || $request->boolean('ajax');

        if ($checkout['itemCount'] === 0) {
            if ($wantsJson) {
                return response()->json(['status' => false, 'message' => 'Your cart is empty.'], 422);
            }

            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $billingPayload = $this->resolveCheckoutAddress($request, $user, 'billing');
        $sameShipping = $request->input('shipping_same_diff_address', 'same') === 'same';
        $shippingPayload = $sameShipping
            ? $billingPayload
            : $this->resolveCheckoutAddress($request, $user, 'shipping');

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'order_id' => 'ORD' . time() . rand(100, 999),
                'subtotal' => $checkout['subtotal'],
                'discount' => $checkout['discount'],
                'shipping_charge' => $checkout['shippingCharge'],
                'grand_total' => $checkout['grandTotal'],
                'paid_amount' => 0,
                'due_amount' => $checkout['grandTotal'],
                'payment_method' => $paymentMethod,
                'payment_type' => 'full',
                'payment_gateway' => $paymentMethod === 'razorpay' ? 'razorpay' : null,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'notes' => $checkout['couponCode']
                    ? 'Coupon: ' . $checkout['couponCode']
                    : null,
            ]);

            OrderAddress::create(array_merge($shippingPayload, [
                'order_id' => $order->id,
                'type' => 'shipping',
            ]));

            OrderAddress::create(array_merge($billingPayload, [
                'order_id' => $order->id,
                'type' => 'billing',
            ]));

            foreach ($checkout['cartItems'] as $cartItem) {
                if (!$cartItem->product) {
                    continue;
                }

                $price = $cartItem->product->selling_price ?? $cartItem->product->price ?? 0;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'variant_id' => $cartItem->variant_id ?? null,
                    'quantity' => $cartItem->quantity,
                    'price' => $price,
                    'total' => $price * $cartItem->quantity,
                ]);
            }

            // Coupon usage + cart clear only after payment is confirmed.
            // For Razorpay, wait until verify succeeds so cancel/close keeps the cart.
            if ($paymentMethod !== 'razorpay' && !empty($checkout['coupon']) && $checkout['discount'] > 0) {
                $checkout['coupon']->increment('used_count');
            }

            $razorpayPayload = null;

            if ($paymentMethod === 'razorpay') {
                $api = new Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );

                $amountPaise = (int) round(((float) $order->grand_total) * 100);

                if ($amountPaise < 100) {
                    throw new \RuntimeException('Order amount must be at least ₹1 for online payment.');
                }

                $razorpayOrder = $api->order->create([
                    'receipt' => $order->order_id,
                    'amount' => $amountPaise,
                    'currency' => 'INR',
                    'payment_capture' => 1,
                    'notes' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_id,
                    ],
                ]);

                $order->update([
                    'razorpay_order_id' => $razorpayOrder['id'],
                    'transaction_id' => $razorpayOrder['id'],
                ]);

                $razorpayPayload = [
                    'key' => config('services.razorpay.key'),
                    'amount' => $amountPaise,
                    'currency' => 'INR',
                    'name' => config('app.name', 'Cheriks'),
                    'description' => 'Order ' . $order->order_id,
                    'order_id' => $razorpayOrder['id'],
                    'prefill' => [
                        'name' => $billingPayload['name'] ?? $user->name,
                        'email' => $user->email,
                        'contact' => $billingPayload['phone'] ?? $user->phone,
                    ],
                    'notes' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_id,
                    ],
                    'theme' => [
                        'color' => '#E85D04',
                    ],
                ];
            } else {
                $this->clearCheckoutCart($user->id);
            }

            DB::commit();

            if ($paymentMethod === 'razorpay') {
                if ($wantsJson) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Order created. Complete payment to confirm.',
                        'order_id' => $order->id,
                        'order_number' => $order->order_id,
                        'razorpay' => $razorpayPayload,
                        'verify_url' => route('checkout.razorpay.verify'),
                    ]);
                }

                return redirect()
                    ->route('checkout.shipping')
                    ->with('error', 'Please enable JavaScript to complete Razorpay payment.');
            }

            $this->ensureOrderInvoice($order);
            $this->sendOrderSuccessEmail($order->fresh());

            return redirect()
                ->route('checkout.success')
                ->with('last_order_id', $order->order_id)
                ->with('success', 'Order placed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Checkout place order failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'payment_method' => $paymentMethod,
            ]);

            if ($wantsJson) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to place order. Please try again.',
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Unable to place order. Please try again.');
        }
    }

    public function verifyRazorpayPayment(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
        }

        if ($order->payment_method !== 'razorpay') {
            return response()->json(['status' => false, 'message' => 'Invalid payment method for this order.'], 422);
        }

        if ($order->payment_status === 'paid') {
            $this->clearCheckoutCart(Auth::id());

            return response()->json([
                'status' => true,
                'message' => 'Payment already verified.',
                'redirect_url' => route('checkout.success'),
            ]);
        }

        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);

            $storedRazorpayOrderId = $order->razorpay_order_id ?: $order->transaction_id;

            if ($storedRazorpayOrderId && $storedRazorpayOrderId !== $request->razorpay_order_id) {
                return response()->json(['status' => false, 'message' => 'Razorpay order mismatch.'], 422);
            }

            $order->update([
                'payment_status' => 'paid',
                'payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'transaction_id' => $request->razorpay_payment_id,
                'paid_amount' => $order->grand_total,
                'due_amount' => 0,
                'order_status' => 'processing',
            ]);

            if (!empty($order->notes) && str_starts_with($order->notes, 'Coupon: ')) {
                $couponCode = trim(substr($order->notes, strlen('Coupon: ')));
                if ($couponCode !== '') {
                    Coupon::whereRaw('UPPER(code) = ?', [strtoupper($couponCode)])->increment('used_count');
                }
            }

            $this->clearCheckoutCart(Auth::id());
            $this->ensureOrderInvoice($order);
            $this->sendOrderSuccessEmail($order->fresh());

            session()->flash('last_order_id', $order->order_id);
            session()->flash('success', 'Payment successful! Order placed.');

            return response()->json([
                'status' => true,
                'message' => 'Payment verified successfully.',
                'redirect_url' => route('checkout.success'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Razorpay verify failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed. Please contact support if amount was deducted.',
            ], 400);
        }
    }

    /* SUCCESS */
    public function checkoutSuccess()
    {
        $orderId = session('last_order_id');
        $order = null;

        if ($orderId && Auth::check()) {
            $order = Order::where('order_id', $orderId)
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('frontend.pages.checkout_success', compact('order', 'orderId'));
    }

    private function clearCheckoutCart(int $userId): void
    {
        Cart::where('user_id', $userId)->delete();
        BuyNow::where('user_id', $userId)->delete();
        session()->forget(['applied_coupon_code', 'applied_coupon_discount']);
    }

    private function ensureOrderInvoice(Order $order): void
    {
        if (!empty($order->invoice_no)) {
            return;
        }

        $order->update([
            'invoice_no' => 'INV' . $order->id . time(),
            'invoice_date' => now()->toDateString(),
        ]);
    }

    private function sendOrderSuccessEmail(Order $order): void
    {
        $order->loadMissing(['user', 'items.product', 'items.variant', 'shippingAddress', 'billingAddress', 'addresses', 'orderPayments', 'refunds']);

        $customerEmail = $order->user?->email;
        if ($customerEmail) {
            try {
                Mail::to($customerEmail)->send(new OrderSuccessMail($order));
            } catch (\Throwable $e) {
                Log::error('Order success email failed: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'order_number' => $order->order_id,
                    'email' => $customerEmail,
                ]);
            }
        } else {
            Log::warning('Order success email skipped: missing customer email.', [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
            ]);
        }

        $adminEmail = config('mail.admin_order_email');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new OrderAdminMail($order));
            } catch (\Throwable $e) {
                Log::error('Order admin email failed: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'order_number' => $order->order_id,
                    'email' => $adminEmail,
                ]);
            }
        }
    }

    private function listingCategories(bool $discountedOnly = false)
    {
        return Category::where('status', 1)
            ->withCount([
                'products as products_count' => function ($query) use ($discountedOnly) {
                    $query->where('status', 1);
                    if ($discountedOnly) {
                        $query->where('discount', '>', 0);
                    }
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function priceRangeDefinitions(): array
    {
        return [
            '500-1000' => [
                'key' => '500-1000',
                'label' => '₹ 500 - ₹ 1000',
                'min' => 500,
                'max' => 1000,
            ],
            '1001-1500' => [
                'key' => '1001-1500',
                'label' => '₹ 1001 - ₹ 1500',
                'min' => 1001,
                'max' => 1500,
            ],
            '1501-2000' => [
                'key' => '1501-2000',
                'label' => '₹ 1501 - ₹ 2000',
                'min' => 1501,
                'max' => 2000,
            ],
            '2001-2500' => [
                'key' => '2001-2500',
                'label' => '₹ 2001 - ₹ 2500',
                'min' => 2001,
                'max' => 2500,
            ],
            '2500+' => [
                'key' => '2500+',
                'label' => 'More than ₹ 2500',
                'min' => 2500,
                'max' => null,
                'exclusive_min' => true,
            ],
        ];
    }

    private function selectedPriceKeys(): array
    {
        $allowed = array_keys($this->priceRangeDefinitions());

        return collect(request()->input('price', []))
            ->filter(fn ($key) => in_array($key, $allowed, true))
            ->unique()
            ->values()
            ->all();
    }

    private function effectiveSellingPriceSql(): string
    {
        return 'COALESCE(NULLIF(selling_price, 0), price)';
    }

    private function applyPriceRangeFilter($query, array $selectedPriceKeys)
    {
        if (empty($selectedPriceKeys)) {
            return $query;
        }

        $ranges = $this->priceRangeDefinitions();
        $priceSql = $this->effectiveSellingPriceSql();

        return $query->where(function ($priceQuery) use ($selectedPriceKeys, $ranges, $priceSql) {
            foreach ($selectedPriceKeys as $key) {
                if (!isset($ranges[$key])) {
                    continue;
                }

                $range = $ranges[$key];

                $priceQuery->orWhere(function ($rangeQuery) use ($range, $priceSql) {
                    if (!empty($range['exclusive_min'])) {
                        $rangeQuery->whereRaw("{$priceSql} > ?", [$range['min']]);
                        return;
                    }

                    $rangeQuery->whereRaw("{$priceSql} >= ?", [$range['min']]);

                    if ($range['max'] !== null) {
                        $rangeQuery->whereRaw("{$priceSql} <= ?", [$range['max']]);
                    }
                });
            }
        });
    }

    private function buildPriceRangesWithCounts($baseQuery): array
    {
        $priceSql = $this->effectiveSellingPriceSql();
        $ranges = [];

        foreach ($this->priceRangeDefinitions() as $key => $range) {
            $countQuery = clone $baseQuery;

            if (!empty($range['exclusive_min'])) {
                $countQuery->whereRaw("{$priceSql} > ?", [$range['min']]);
            } else {
                $countQuery->whereRaw("{$priceSql} >= ?", [$range['min']]);
                if ($range['max'] !== null) {
                    $countQuery->whereRaw("{$priceSql} <= ?", [$range['max']]);
                }
            }

            $ranges[] = [
                'key' => $key,
                'label' => $range['label'],
                'count' => (int) $countQuery->count(),
            ];
        }

        return $ranges;
    }

    private function appendListingFilters($paginator): void
    {
        $appends = [];

        if ($categoryFilter = request()->input('category')) {
            $appends['category'] = $categoryFilter;
        }

        if ($priceFilter = request()->input('price')) {
            $appends['price'] = $priceFilter;
        }

        if ($appends) {
            $paginator->appends($appends);
        }
    }

    private function buildCategoryListingProducts(Category $category): array
    {
        $selectedCategoryIds = collect(request()->input('category', [$category->id]))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $selectedPriceKeys = $this->selectedPriceKeys();

        $baseQuery = Product::query()
            ->where('status', 1)
            ->when(
                !empty($selectedCategoryIds),
                fn ($query) => $query->inCategories($selectedCategoryIds),
                fn ($query) => $query->inCategory($category->id)
            );

        $priceRanges = $this->buildPriceRangesWithCounts(clone $baseQuery);

        $products = (clone $baseQuery)
            ->tap(fn ($query) => $this->applyPriceRangeFilter($query, $selectedPriceKeys))
            ->withCount([
                'reviews as reviews_count' => fn ($query) => $query->where('status', 1),
            ])
            ->latest()
            ->paginate(8);

        $this->appendListingFilters($products);
        $this->attachProductReviewMeta($products->getCollection());

        return [$products, $selectedCategoryIds, $selectedPriceKeys, $priceRanges];
    }

    private function buildSuperDealProducts(): array
    {
        $selectedCategoryIds = collect(request()->input('category', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $selectedPriceKeys = $this->selectedPriceKeys();

        $baseQuery = Product::query()
            ->where('status', 1)
            ->where('discount', '>', 0)
            ->when(
                !empty($selectedCategoryIds),
                fn ($query) => $query->whereIn('category_id', $selectedCategoryIds)
            );

        $priceRanges = $this->buildPriceRangesWithCounts(clone $baseQuery);

        $products = (clone $baseQuery)
            ->tap(fn ($query) => $this->applyPriceRangeFilter($query, $selectedPriceKeys))
            ->withCount([
                'reviews as reviews_count' => fn ($query) => $query->where('status', 1),
            ])
            ->orderByDesc('discount')
            ->latest()
            ->paginate(6);

        $this->appendListingFilters($products);
        $this->attachProductReviewMeta($products->getCollection());
        $products->getCollection()->each(function ($product) {
            $product->setAttribute('is_new', false);
        });

        return [$products, $selectedCategoryIds, $selectedPriceKeys, $priceRanges];
    }

    private function attachProductReviewMeta($products): void
    {
        $products->each(function ($product) {
            $product->setAttribute('rating', $product->averageRating() ?? 0);
            $product->setAttribute('is_new', (bool) ($product->is_new_arrival ?? false));

            $price = (float) ($product->price ?? 0);
            $selling = (float) ($product->selling_price ?? $price);
            $stored = (int) ($product->discount ?? 0);
            $discount = $stored > 0
                ? $stored
                : (($price > $selling && $price > 0)
                    ? (int) round((($price - $selling) / $price) * 100)
                    : 0);

            $product->setAttribute('discount_percent', $discount);
        });
    }

    private function resolveCheckoutCartItems()
    {
        $buyNow = BuyNow::where('user_id', Auth::id())->latest()->first();

        if ($buyNow && $buyNow->product) {
            $buyNow->loadMissing('product.gstSetting');

            return collect([(object) [
                'id' => 'buynow_' . $buyNow->id,
                'product_id' => $buyNow->product_id,
                'variant_id' => $buyNow->variant_id,
                'quantity' => $buyNow->quantity,
                'product' => $buyNow->product,
            ]]);
        }

        return $this->cartService->getCartItems();
    }

    private function buildCheckoutSummary(): array
    {
        return $this->buildCartSummary($this->resolveCheckoutCartItems());
    }

    private function buildCartSummary($cartItems): array
    {
        $subtotal = 0;
        $gstTotal = 0;
        $gstRateLabel = null;
        $itemCount = 0;
        $gstRates = [];

        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product) {
                continue;
            }

            $itemCount++;
            $sellingPrice = $cartItem->product->selling_price ?? $cartItem->product->price ?? 0;
            $taxInfo = $cartItem->product->getInclusiveTaxInfo($sellingPrice);

            $subtotal += $sellingPrice * $cartItem->quantity;
            $gstTotal += ($taxInfo['tax_amount'] ?? 0) * $cartItem->quantity;

            if (!empty($taxInfo['gst_percentage'])) {
                $gstRates[] = (float) $taxInfo['gst_percentage'];
            }
        }

        $subtotal = round($subtotal, 2);
        $gstTotal = round($gstTotal, 2);

        if (!empty($gstRates)) {
            $uniqueRates = array_values(array_unique($gstRates));
            $gstRateLabel = count($uniqueRates) === 1
                ? $uniqueRates[0] . '% GST (inclusive)'
                : 'GST (inclusive)';
        } else {
            $gstRateLabel = 'GST (inclusive)';
        }

        $shippingCharge = 0;
        $shippingSetting = ShippingSetting::first();

        if ($shippingSetting && $subtotal > 0) {
            $freeShippingAmount = (float) $shippingSetting->free_shipping_amount;
            $shippingCharge = $subtotal >= $freeShippingAmount
                ? 0
                : (float) $shippingSetting->shipping_charge;
        }

        $discount = 0.0;
        $coupon = null;
        $couponCode = session('applied_coupon_code');

        if (!$couponCode && Auth::check()) {
            $buyNowCoupon = BuyNow::where('user_id', Auth::id())->latest()->value('coupon_code');
            if ($buyNowCoupon) {
                $couponCode = $buyNowCoupon;
                session(['applied_coupon_code' => $couponCode]);
            }
        }

        if ($couponCode) {
            $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($couponCode)])
                ->where('status', 1)
                ->first();

            if ($coupon) {
                $result = $coupon->calculateDiscountForItems(collect($cartItems), $subtotal);
                if ($result['discount'] > 0) {
                    $discount = $result['discount'];
                    session(['applied_coupon_discount' => $discount]);
                } else {
                    session()->forget(['applied_coupon_code', 'applied_coupon_discount']);
                    $coupon = null;
                    $couponCode = null;
                }
            } else {
                session()->forget(['applied_coupon_code', 'applied_coupon_discount']);
                $couponCode = null;
            }
        }

        if ($subtotal > 0 && $discount > 0) {
            $gstTotal = round($gstTotal * max(0, ($subtotal - $discount) / $subtotal), 2);
        }

        $grandTotal = max(0, round($subtotal - $discount + $shippingCharge, 2));

        return compact(
            'cartItems',
            'subtotal',
            'gstTotal',
            'gstRateLabel',
            'shippingCharge',
            'discount',
            'coupon',
            'couponCode',
            'grandTotal',
            'itemCount'
        );
    }

    private function wantsCouponJson(Request $request): bool
    {
        return $request->ajax()
            || $request->wantsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function cartSummaryJsonPayload(array $summary): array
    {
        return [
            'subtotal' => (float) $summary['subtotal'],
            'discount' => (float) $summary['discount'],
            'gstTotal' => (float) $summary['gstTotal'],
            'gstRateLabel' => $summary['gstRateLabel'] ?? 'GST (inclusive)',
            'shippingCharge' => (float) $summary['shippingCharge'],
            'grandTotal' => (float) $summary['grandTotal'],
            'coupon_code' => $summary['couponCode'] ?? null,
            'coupon_type' => $summary['coupon']->type ?? null,
            'coupon_value' => isset($summary['coupon']) ? (float) $summary['coupon']->value : null,
        ];
    }

    private function couponActionResponse(Request $request, bool $success, string $message, ?array $summary = null)
    {
        if ($this->wantsCouponJson($request)) {
            if ($summary === null) {
                $summary = $this->buildCartSummary($this->resolveCheckoutCartItems());
            }

            return response()->json(array_merge([
                'status' => $success,
                'message' => $message,
            ], $this->cartSummaryJsonPayload($summary)), $success ? 200 : 422);
        }

        return $success
            ? back()->with('success', $message)
            : back()->with('error', $message);
    }

    private function formatCouponOffersForJson($offers, ?string $appliedCode = null): array
    {
        $appliedCode = strtoupper((string) $appliedCode);

        return $offers->map(function (array $offer) use ($appliedCode) {
            /** @var \App\Models\Coupon $coupon */
            $coupon = $offer['coupon'];

            return [
                'coupon' => [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => (float) $coupon->value,
                ],
                'discount' => (float) $offer['discount'],
                'can_apply' => (bool) $offer['can_apply'],
                'message' => $offer['message'],
                'minimum_amount' => $offer['minimum_amount'],
                'maximum_discount' => $offer['maximum_discount'],
                'eligible_subtotal' => (float) $offer['eligible_subtotal'],
                'meets_minimum' => (bool) $offer['meets_minimum'],
                'applicability' => $offer['applicability'],
                'eligible_items' => $offer['eligible_items'],
                'is_applied' => $appliedCode !== '' && strtoupper($coupon->code) === $appliedCode,
            ];
        })->values()->all();
    }

    private function resolveCheckoutAddress(Request $request, $user, string $prefix, bool $allowSaved = true): array
    {
        if ($allowSaved) {
            $addressId = $request->input($prefix . '_address_id');

            if ($addressId && $addressId !== 'new') {
                $address = Address::where('id', $addressId)
                    ->where('user_id', $user->id)
                    ->where('status', 1)
                    ->firstOrFail();

                return $this->addressToOrderPayload($address);
            }
        }

        $validated = $request->validate([
            $prefix . '_name' => 'required|string|max:255',
            $prefix . '_phone' => 'required|string|max:20',
            $prefix . '_address_line_1' => 'required|string|max:500',
            $prefix . '_address_line_2' => 'nullable|string|max:500',
            $prefix . '_city' => 'required|string|max:255',
            $prefix . '_state' => 'required|string|max:255',
            $prefix . '_pincode' => 'required|string|max:10',
        ], [], [
            $prefix . '_name' => 'name',
            $prefix . '_phone' => 'phone',
            $prefix . '_address_line_1' => 'address line 1',
            $prefix . '_address_line_2' => 'address line 2',
            $prefix . '_city' => 'city',
            $prefix . '_state' => 'state',
            $prefix . '_pincode' => 'pincode',
        ]);

        $shouldSave = $request->boolean('save_address')
            || ($prefix === 'billing' && $request->boolean('save_billing_address'));

        if ($shouldSave) {
            if ($request->boolean('is_default') || ($prefix === 'billing' && $request->boolean('billing_is_default'))) {
                Address::where('user_id', $user->id)->update(['is_default' => false]);
            }

            $isDefault = $prefix === 'billing'
                ? $request->boolean('billing_is_default')
                : $request->boolean('is_default');

            // First saved address becomes default automatically
            if (!$isDefault && !Address::where('user_id', $user->id)->where('status', 1)->exists()) {
                $isDefault = true;
            }

            Address::create([
                'user_id' => $user->id,
                'name' => $validated[$prefix . '_name'],
                'phone' => $validated[$prefix . '_phone'],
                'address' => $validated[$prefix . '_address_line_1'],
                'landmark' => $validated[$prefix . '_address_line_2'] ?? null,
                'city' => $validated[$prefix . '_city'],
                'state' => $validated[$prefix . '_state'],
                'country' => 'India',
                'pincode' => $validated[$prefix . '_pincode'],
                'type' => 'home',
                'is_default' => $isDefault,
                'status' => 1,
            ]);
        }

        return [
            'name' => $validated[$prefix . '_name'],
            'phone' => $validated[$prefix . '_phone'],
            'alternate_phone' => null,
            'company_name' => null,
            'gstin' => null,
            'address' => $validated[$prefix . '_address_line_1'],
            'landmark' => $validated[$prefix . '_address_line_2'] ?? null,
            'city' => $validated[$prefix . '_city'],
            'state' => $validated[$prefix . '_state'],
            'country' => 'India',
            'pincode' => $validated[$prefix . '_pincode'],
        ];
    }

    private function addressToOrderPayload(Address $address): array
    {
        $street = $address->address;

        if (!$street && ($address->flat || $address->area)) {
            $street = trim(($address->flat ?? '') . ', ' . ($address->area ?? ''), ', ');
        }

        return [
            'name' => $address->name,
            'phone' => $address->phone,
            'alternate_phone' => $address->alternate_phone,
            'company_name' => null,
            'gstin' => null,
            'address' => $street,
            'landmark' => $address->landmark,
            'city' => $address->city,
            'state' => $address->state,
            'country' => $address->country ?: 'India',
            'pincode' => $address->pincode,
        ];
    }

    //Privacy Policy Page
    public function privacyPolicy(){
        $privacy = PrivacyPolicy::first();
        return view('frontend.pages.privacy_policy', compact('privacy'));
    }

    //Terms and Conditions Page
    public function termsAndConditions(){
        $terms = TermsAndCondition::first();
        return view('frontend.pages.terms_and_conditions', compact('terms'));
    }

    //Contact Us Page
    public function contactUs()
    {
        $contactSetting = ContactSetting::firstOrCreate([]);

        return view('frontend.pages.contact_us', compact('contactSetting'));
    }

    // Help Center Page
    public function helpCenter()
    {
        $helpCategories = HelpCategory::where('status', 1)
            ->with(['activeFaqs'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view()->first(
            ['frontend.pages.help_center', 'frontend.pages.help'],
            compact('helpCategories')
        );
    }

    //Login Page
    public function login(){
        return view('frontend.auth.login');
    }

    //Signup Page
    public function signup(){
        return view('frontend.pages.signup');
    }

    //Forgot Password Page
    public function forgotPassword(){
        return view('frontend.pages.forgot_password');
    }

    //My Account Addresses Page
    public function myAccountAddresses()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)
            ->where('status', 1)
            ->latest()
            ->get();

        return view('frontend.pages.myaccount_addresses', compact('user', 'addresses'));
    }

    public function storeAddress(Request $request)
    {
         $user = auth()->user();

         $validated = $request->validate([
             'name' => 'required|string|max:255',
             'phone' => 'required|string|max:20',
             'address_line_1' => 'required|string|max:500',
             'address_line_2' => 'nullable|string|max:500',
             'city' => 'required|string|max:255',
             'state' => 'required|string|max:255',
             'pincode' => 'required|string|max:10',
             'is_default' => 'nullable|boolean',
         ]);

         $isDefault = $request->boolean('is_default');
         $hasAddresses = Address::where('user_id', $user->id)->where('status', 1)->exists();

         if (!$hasAddresses) {
             $isDefault = true;
         }

         if ($isDefault) {
             Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

         Address::create([
             'user_id' => $user->id,
             'name' => $validated['name'],
             'phone' => $validated['phone'],
             'address' => $validated['address_line_1'],
             'landmark' => $validated['address_line_2'] ?? null,
             'city' => $validated['city'],
             'state' => $validated['state'],
             'country' => 'India',
             'pincode' => $validated['pincode'],
            'type' => 'home',
             'is_default' => $isDefault,
             'status' => 1,
         ]);

         return back()->with('success', 'Address added successfully.');
    }

     public function destroyAddress(Address $address)
     {
         if ($address->user_id !== auth()->id()) {
             abort(403);
         }

         $wasDefault = $address->is_default;
         $userId = $address->user_id;
         $address->delete();

         if ($wasDefault) {
            $next = Address::where('user_id', $userId)
                 ->where('status', 1)
                ->latest()
                 ->first();

            if ($next) {
                 $next->update(['is_default' => true]);
             }
         }

         return back()->with('success', 'Address deleted successfully.');
    }

    public function setDefaultAddress(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated. It will be used as billing on checkout.');
    }

    //My Account Profile Page
    public function myAccountProfile()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        return view('frontend.pages.myaccount_profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        return back()->with('success', 'Profile updated successfully.');
    }

    //My Account Orders Page
    public function myAccountOrders(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $status = $request->input('status');

        $orders = Order::with(['items.product.brand'])
            ->where('user_id', $user->id)
            ->when($status === 'delivered', fn ($q) => $q->where('order_status', 'delivered'))
            ->when($status === 'cancelled', fn ($q) => $q->where('order_status', 'cancelled'))
            ->when($status === 'returns', function ($q) {
                $q->whereNotNull('return_requested_at');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('frontend.pages.myaccount_orders', compact('user', 'orders'));
    }

    public function myAccountOrderDetails(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'shippingAddress', 'addresses']);

        return view('frontend.pages.order_details', compact('order'));
    }

    public function cancelOrder(Request $request, Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        if (! in_array($order->order_status, ['pending', 'confirmed', 'processing'])) {
            return back()->with('error', 'This order cannot be cancelled now.');
        }

        if ($order->cancel_requested_at && ! $order->cancel_approved_at && ! $order->cancel_rejected_at) {
            return back()->with('error', 'Cancellation request already submitted.');
        }

        $order->update([
            'cancel_requested_at' => now(),
            'cancel_request_reason' => $request->reason,
            'cancel_request_note' => $request->note,
            'cancel_approved_at' => null,
            'cancel_rejected_at' => null,
            'cancel_decision_note' => null,
        ]);

        $message = 'Cancellation request sent to admin.';

        if ($order->payment_method === 'razorpay' && (float) $order->paid_amount > 0) {
            $message .= ' If approved, your refund will be credited to the original payment method within 2-3 working days.';
        }

        return back()->with('success', $message);
    }

    public function returnOrder(Request $request, Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'request_type' => 'required|in:return,exchange',
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        if (
            strtolower($order->order_status ?? '') !== 'delivered' ||
            ! $order->returnDeadline() ||
            now()->gt($order->returnDeadline())
        ) {
            return back()->with('error', 'Return or exchange is no longer available for this order.');
        }

        if ($order->return_requested_at && ! $order->return_approved_at && ! $order->return_rejected_at) {
            return back()->with('error', 'Return or exchange request already submitted.');
        }

        $order->update([
            'return_requested_at' => now(),
            'return_request_type' => $request->request_type,
            'return_reason' => $request->reason,
            'return_request_note' => $request->note,
            'return_approved_at' => null,
            'return_rejected_at' => null,
            'return_decision_note' => null,
            'pickup_date' => null,
            'returned_at' => null,
        ]);

        $message = ucfirst($request->request_type) . ' request submitted successfully.';

        if (
            $request->request_type === 'return' &&
            $order->payment_method === 'razorpay' &&
            (float) $order->paid_amount > 0
        ) {
            $message .= ' If approved, your refund will be credited to the original payment method within 2-3 working days.';
        }

        return back()->with('success', $message);
    }

    public function cancelReturn(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        if (
            ! $order->return_requested_at ||
            $order->return_approved_at ||
            $order->return_rejected_at
        ) {
            return back()->with('error', 'No pending return or exchange request found.');
        }

        $order->update([
            'return_requested_at' => null,
            'return_request_type' => null,
            'return_reason' => null,
            'return_request_note' => null,
            'return_approved_at' => null,
            'return_rejected_at' => null,
            'return_decision_note' => null,
            'pickup_date' => null,
        ]);

        return back()->with('success', 'Return or exchange request cancelled.');
    }
    //My Account Change Password Page
    public function myAccountChangePassword()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return view('frontend.pages.myaccount_changepassword', ['user' => Auth::user()]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => $request->password,
            'plain_password' => $request->password,
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function searchSuggest(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        // Empty query: default discovery panel
        if ($search === '') {
            $topCategories = Category::where('status', 1)
                ->withCount(['products' => fn ($q) => $q->where('status', 1)])
                ->orderByDesc('products_count')
                ->orderBy('name')
                ->limit(6)
                ->get(['id', 'name', 'slug', 'image'])
                ->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'url' => route('product_listing', $category->slug),
                    'image' => $category->image
                        ? asset('backend/assets/images/category/' . $category->image)
                        : asset('frontend/assets/images/no-image.png'),
                ])
                ->values();

            $trending = Category::where('status', 1)
                ->withCount(['products' => fn ($q) => $q->where('status', 1)])
                ->orderByDesc('products_count')
                ->orderBy('name')
                ->limit(6)
                ->get(['id', 'name', 'slug'])
                ->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => 'category',
                    'url' => route('product_listing', $category->slug),
                ])
                ->values();

            $categoryUrls = Category::where('status', 1)
                ->orderBy('name')
                ->get(['name', 'slug'])
                ->mapWithKeys(fn ($category) => [
                    mb_strtolower($category->name) => route('product_listing', $category->slug),
                ])
                ->all();

            return response()->json([
                'mode' => 'discover',
                'trending' => $trending,
                'top_categories' => $topCategories,
                'category_urls' => $categoryUrls,
                'results' => [],
            ]);
        }

        $like = "%{$search}%";
        $startsWith = "{$search}%";

        $categories = Category::where('status', 1)
            ->where(function ($query) use ($like) {
                $query->where('name', 'LIKE', $like)
                    ->orWhere('slug', 'LIKE', $like);
            })
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [$startsWith])
            ->orderBy('name')
            ->limit(5)
            ->get(['id', 'name', 'slug', 'image'])
            ->map(fn ($category) => [
                'type' => 'category',
                'id' => $category->id,
                'name' => $category->name,
                'url' => route('product_listing', $category->slug),
                'image' => $category->image
                    ? asset('backend/assets/images/category/' . $category->image)
                    : asset('frontend/assets/images/no-image.png'),
            ]);

        $products = Product::where('status', 1)
            ->where(function ($query) use ($like) {
                $query->where('name', 'LIKE', $like)
                    ->orWhere('sku', 'LIKE', $like)
                    ->orWhereHas('category', function ($q) use ($like) {
                        $q->where('status', 1)->where('name', 'LIKE', $like);
                    });
            })
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [$startsWith])
            ->orderBy('name')
            ->limit(12)
            ->get(['id', 'name', 'slug', 'thumbnail'])
            ->map(fn ($product) => [
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('product_details', $product->slug),
                'image' => $product->thumbnail
                    ? asset('backend/assets/images/product/' . $product->thumbnail)
                    : asset('frontend/assets/images/no-image.png'),
            ]);

        $results = $categories->concat($products)->values();

        return response()->json([
            'mode' => 'results',
            'results' => $results,
        ]);
    }

    public function searchResults(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        if ($search === '') {
            return redirect()->route('home');
        }

        $products = Product::with(['category', 'reviews'])
            ->withCount([
                'reviews as reviews_count' => function ($q) {
                    $q->where('status', 1);
                },
            ])
            ->where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('details', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('status', 1)
                            ->where(function ($cq) use ($search) {
                                $cq->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('slug', 'LIKE', "%{$search}%");
                            });
                    })
                    ->orWhereHas('subcategory', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('slug', 'LIKE', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $this->attachProductReviewMeta($products->getCollection());

        // Named separately so header nav `$categories` is not overwritten
        $matchedCategories = Category::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            })
            ->withCount(['products' => function ($q) {
                $q->where('status', 1);
            }])
            ->orderBy('name')
            ->get();

        return view('frontend.pages.search_results', compact(
            'search',
            'products',
            'matchedCategories'
        ));
    }

    public function storeContactEnquiry(Request $request)
    {
        $data = $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'contact_no' => 'required|string|max:20',
            'comment'    => 'nullable|string|max:2000',
        ]);

        $enquiry = ContactEnquiry::create([
            ...$data,
            'status' => false,
        ]);

        try {
            Mail::to($enquiry->email)->send(new ContactEnquiryUserMail($enquiry));
        } catch (\Throwable $e) {
            Log::error('Contact enquiry user email failed: ' . $e->getMessage(), [
                'enquiry_id' => $enquiry->id,
                'email' => $enquiry->email,
            ]);
        }

        $adminEmail = config('mail.admin_order_email') ?: config('mail.from.address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new ContactEnquiryAdminMail($enquiry));
            } catch (\Throwable $e) {
                Log::error('Contact enquiry admin email failed: ' . $e->getMessage(), [
                    'enquiry_id' => $enquiry->id,
                    'email' => $adminEmail,
                ]);
            }
        }

        return back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
