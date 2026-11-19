<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Slider;
use App\Models\Category;
use App\Models\SubCategory;
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
use App\Models\SpecialOffer;
use App\Models\Review;
use App\Models\Brand;
use App\Models\ReturnRefund;
use App\Models\WarrantyGuarantee;
use App\Models\ReviewReaction;
use App\Models\ShippingSetting;
use App\Services\CartService;
use App\Services\ShiprocketService;
use App\Services\WishlistService;
use App\Models\Service;
use App\Models\AboutUsSection;
use App\Models\Store;
use App\Models\PlayFunSection;
use App\Models\LearnFunSection;
use App\Models\HowToPlaySection;
use App\Models\HomeFaq;
use App\Models\HomeFaqSection;
use App\Models\HelpCategory;
// REMOVED: use App\Models\HomeTestimonial;
use App\Models\HomeHighlight;
use App\Models\HomeServiceItem;
use App\Models\HomeServiceSectionSetting;
use App\Models\Watches;
use App\Models\HomeBanner;
use App\Models\ContactEnquiry;
use App\Models\PrivacyPolicy;
use App\Models\ProductVariant;
use App\Models\TermsAndCondition;
use App\Models\Coupon;
use App\Mail\OrderAdminMail;
use App\Mail\OrderSuccessMail;
use App\Mail\ContactEnquiryUserMail;
use App\Mail\ContactEnquiryAdminMail;
use App\Mail\OrderCancelled;
use App\Mail\OrderCancelledAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

class FrontController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected WishlistService $wishlistService
    ) {}

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

        $brands = Brand::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
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
                'reviews as reviews_count' => fn($query) => $query->where('status', 1),
            ])
            ->latest()
            ->take(8)
            ->get();

        $specialOffer = SpecialOffer::where('is_active', 1)
            ->orderBy('sort_order')
            ->first();

        $this->attachProductReviewMeta($bestSellingProducts);

        $newArrivalsProducts = Product::where('status', 1)
            ->where('is_new_arrival', 1)
            ->with(['brand', 'category'])
            ->withCount([
                'reviews as reviews_count' => fn($query) => $query->where('status', 1),
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

        $homeServiceItems = HomeServiceItem::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $homeServiceSectionSetting = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('home_service_section_settings')) {
                $homeServiceSectionSetting = HomeServiceSectionSetting::first();
            }
        } catch (\Throwable $e) {
            $homeServiceSectionSetting = null;
        }

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
            'homeServiceItems',
            'homeServiceSectionSetting',
            'brands',
            'watchBanners',
            'specialOffer'
        ));
    }

    //About Page
    public function about()
    {
        $aboutSection = AboutUsSection::first();

        if (!$aboutSection) {
            $aboutSection = AboutUsSection::create([
                'title' => 'About Us',
                'content' => 'Welcome to our watch store.',
                'image' => null,
            ]);
        }

        $homeTestimonials = Review::with(['user', 'product'])
            ->where('status', 1)
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->latest()
            ->take(8)
            ->get();

        $brands = Brand::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $homeServiceItems = HomeServiceItem::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $homeServiceSectionSetting = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('home_service_section_settings')) {
                $homeServiceSectionSetting = HomeServiceSectionSetting::first();
            }
        } catch (\Throwable $e) {
            $homeServiceSectionSetting = null;
        }

        return view('frontend.pages.about', compact('aboutSection', 'homeTestimonials', 'brands', 'homeServiceItems', 'homeServiceSectionSetting'));
    }

    //Our Stores Page
    public function ourStores()
    {
        $stores = Store::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('frontend.pages.our_store', compact('stores'));
    }

    //Gallery Page
    public function gallery()
    {
        $galleries = Gallery::where('status', 1)
            ->latest()
            ->paginate(6);
        return view('frontend.pages.gallery', compact('galleries'));
    }

    public function galleryDetails($slug)
    {
        $gallery = Gallery::where('slug', $slug)
            ->where('status', 1)
            ->with('images')
            ->firstOrFail();

        return view('frontend.pages.gallery-details', compact('gallery'));
    }

    //Product Details Page
    public function productDetails(Request $request, $slug = null)
    {
        $productSlug = $slug ?: $request->route('slug') ?: $request->query('slug') ?: $request->slug;

        $product = Product::where('status', 1)
            ->with(['category', 'brand', 'faqs'])
            ->withCount([
                'reviews as reviews_count' => fn($query) => $query->where('status', 1),
            ])
            ->when($productSlug, fn($q) => $q->where('slug', $productSlug))
            ->when($request->filled('id') && ! $productSlug, fn($q) => $q->where('id', $request->id))
            ->when(! $productSlug && ! $request->filled('id'), fn($q) => $q->whereRaw('1 = 0'))
            ->first();

        if (! $product) {
            abort(404);
        }

        $this->attachProductReviewMeta(collect([$product]));

        $related_products = collect();

        if ($product->brand_id) {
            $related_products = Product::query()
                ->where('status', 1)
                ->where('id', '!=', $product->id)
                ->where('brand_id', $product->brand_id)
                ->with(['brand', 'category'])
                ->withCount([
                    'reviews as reviews_count' => fn($query) => $query->where('status', 1),
                ])
                ->latest()
                ->take(12)
                ->get();
        }

        $this->attachProductReviewMeta($related_products);

        $wishlistIds = $this->wishlistService->getProductIds();

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

        $cartProductIds = $this->cartService->getCartProductIds();

        return view('frontend.pages.product_details', compact(
            'product',
            'related_products',
            'wishlistIds',
            'cartProductIds',
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

        $estimatedDays = '2-5 working days';
        $serviceability = app(ShiprocketService::class)->checkServiceability($pincode);

        if ($serviceability !== null && ! $serviceability['serviceable']) {
            return response()->json([
                'success' => false,
                'pincode' => $pincode,
                'serviceable' => false,
                'message' => 'Sorry, we do not deliver to this pincode yet.',
            ]);
        }

        if (! empty($serviceability['estimated_days'])) {
            $days = (int) $serviceability['estimated_days'];
            $estimatedDays = $days . '-' . ($days + 2) . ' working days';
        }

        return response()->json([
            'success' => true,
            'pincode' => $pincode,
            'serviceable' => true,
            'estimated_days' => $estimatedDays,
            'shipping_charge' => $shippingCharge,
            'free_shipping_minimum' => $freeShippingAmount,
            'message' => 'Delivery is available for this pincode.',
        ]);
    }

    public function brandProducts(Request $request, $slug)
    {
        $allBrands = Brand::all();
        $brand = null;

        if (ctype_digit((string) $slug)) {
            $brand = $allBrands->firstWhere('id', (int) $slug);
        } else {
            $brand = $allBrands->first(function ($b) use ($slug) {
                return strcasecmp((string) $b->slug, (string) $slug) === 0
                    || strcasecmp(\Illuminate\Support\Str::slug($b->name), (string) $slug) === 0;
            });
        }

        if (! $brand) {
            abort(404);
        }

        // Canonical 301 redirect if requested with ID or non-canonical slug
        if ((string) $slug !== (string) $brand->slug) {
            return redirect()->route('brand.products', array_merge(['slug' => $brand->slug], $request->query()), 301);
        }

        // If sidebar filter selected different or multiple brands, forward to product.listing
        $submittedBrands = array_values(array_filter(
            array_map(fn($v) => is_scalar($v) ? trim((string) $v) : '', (array) $request->input('brand', [])),
            fn($v) => $v !== ''
        ));

        if (! empty($submittedBrands)) {
            $isOnlyCurrentBrand = count($submittedBrands) === 1 && (
                strcasecmp($submittedBrands[0], $brand->slug) === 0 ||
                (ctype_digit($submittedBrands[0]) && (int) $submittedBrands[0] === (int) $brand->id)
            );

            if (! $isOnlyCurrentBrand) {
                return redirect()->route('product.listing', $request->query());
            }
        }

        return $this->buildListingPage(
            request: $request,
            brandIds: [$brand->id],
            selectedBrandSlugs: [$brand->slug],
            currentBrand: $brand,
            filterAction: route('brand.products', ['slug' => $brand->slug]),
            clearFilterUrl: route('brand.products', ['slug' => $brand->slug])
        );
    }

    public function productListing(Request $request)
    {
        $rawBrandInput = $request->input('brand', $request->input('brand_id', []));
        $rawBrandList = array_values(array_filter(
            array_map(fn($v) => is_scalar($v) ? trim((string) $v) : '', (array) $rawBrandInput),
            fn($v) => $v !== ''
        ));

        $allBrands = Brand::all();
        $selectedBrands = collect();
        $hasNumericBrandInput = false;

        foreach ($rawBrandList as $raw) {
            $matched = null;
            if (ctype_digit($raw)) {
                $hasNumericBrandInput = true;
                $matched = $allBrands->firstWhere('id', (int) $raw);
            } else {
                $matched = $allBrands->first(function ($b) use ($raw) {
                    return strcasecmp((string) $b->slug, $raw) === 0
                        || strcasecmp(\Illuminate\Support\Str::slug($b->name), $raw) === 0;
                });
            }

            if ($matched && ! $selectedBrands->contains('id', $matched->id)) {
                $selectedBrands->push($matched);
            }
        }

        // Single brand selected -> redirect 301 to dedicated brand URL: /brand/{slug}
        if ($selectedBrands->count() === 1) {
            $brandSlug = $selectedBrands->first()->slug;
            $redirectParams = $request->query();
            unset($redirectParams['brand'], $redirectParams['brand_id']);

            return redirect()->route('brand.products', array_merge(['slug' => $brandSlug], $redirectParams), 301);
        }

        // Redirect numeric brand query (for multiple brands) to canonical slug query
        if ($hasNumericBrandInput && $selectedBrands->isNotEmpty()) {
            $redirectParams = $request->query();
            $redirectParams['brand'] = $selectedBrands->pluck('slug')->values()->all();
            unset($redirectParams['brand_id']);

            return redirect()->route('product.listing', $redirectParams, 301);
        }

        $brandIds = $selectedBrands->pluck('id')->all();
        $selectedBrandSlugs = $selectedBrands->pluck('slug')->all();

        return $this->buildListingPage(
            request: $request,
            brandIds: $brandIds,
            selectedBrandSlugs: $selectedBrandSlugs
        );
    }

    private function buildListingPage(
        Request $request,
        array $brandIds = [],
        array $selectedBrandSlugs = [],
        ?Brand $currentBrand = null,
        ?string $filterAction = null,
        ?string $clearFilterUrl = null
    ) {
        $gender = $this->resolveListingGender($request->input('gender'));
        $filter = $request->input('filter');
        $onSale = $request->boolean('on_sale') || $request->input('filter') === 'sale' || $request->boolean('sale');
        $categoryId = $request->filled('category') ? (int) $request->input('category') : null;
        $subcategoryId = $request->filled('subcategory') ? (int) $request->input('subcategory') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : null;
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : null;
        $sort = $request->input('sort');
        $search = trim((string) $request->input('search', ''));

        $categories = Category::where('status', 1)
            ->withStartingFromPrice()
            ->withCount('products')
            ->when(in_array($gender, ['women', 'men'], true), fn($query) => $query->forMenuGender($gender))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($gender && $categories->isEmpty()) {
            $categories = Category::where('status', 1)
                ->withStartingFromPrice()
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        $priceSql = Category::productPriceSql('products');

        $productsQuery = Product::query()
            ->where('products.status', 1)
            ->with(['brand', 'category'])
            ->when($search !== '', function ($query) use ($search) {
                $cleanSearch = preg_replace('/[\s\-_]+/', '', $search);
                $query->where(function ($q) use ($search, $cleanSearch) {
                    $q->where('products.name', 'LIKE', "%{$search}%")
                        ->orWhere('products.model_no', 'LIKE', "%{$search}%")
                        ->orWhere('products.sku', 'LIKE', "%{$search}%")
                        ->orWhere('products.description', 'LIKE', "%{$search}%")
                        ->orWhere('products.details', 'LIKE', "%{$search}%");

                    if ($cleanSearch !== '' && $cleanSearch !== $search && strlen($cleanSearch) >= 2) {
                        $cleanLike = "%{$cleanSearch}%";
                        $q->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(products.model_no, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike])
                            ->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(products.sku, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike]);
                    }

                    $q->orWhereHas('variants', function ($vq) use ($search, $cleanSearch) {
                        $vq->where('sku', 'LIKE', "%{$search}%");
                        if ($cleanSearch !== '' && $cleanSearch !== $search && strlen($cleanSearch) >= 2) {
                            $cleanLike = "%{$cleanSearch}%";
                            $vq->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(sku, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike]);
                        }
                    })
                        ->orWhereHas('brand', function ($bq) use ($search) {
                            $bq->where('status', 1)->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('category', function ($cq) use ($search) {
                            $cq->where('status', 1)->where(function ($ccq) use ($search) {
                                $ccq->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('slug', 'LIKE', "%{$search}%");
                            });
                        })
                        ->orWhereHas('subcategory', function ($sq) use ($search) {
                            $sq->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('slug', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->tap(fn($query) => $this->applyListingGenderFilter($query, $gender, $categoryId, $subcategoryId, $onSale))
            ->when($subcategoryId, fn($query) => $query->inSubcategory($subcategoryId))
            ->when(! $subcategoryId && $categoryId, fn($query) => $query->inCategory($categoryId))
            ->when($filter === 'best_selling', fn($query) => $query->where('products.is_best_selling', 1))
            ->when($filter === 'new_arrival', fn($query) => $query->where('products.is_new_arrival', 1))
            ->when($onSale, function ($query) {
                $query->where(function ($q) {
                    $q->where(function ($sq) {
                        $sq->whereNotNull('products.selling_price')
                            ->where('products.selling_price', '>', 0)
                            ->whereColumn('products.price', '>', 'products.selling_price');
                    })
                        ->orWhere(function ($sq) {
                            $sq->whereNotNull('products.discount')
                                ->where('products.discount', '>', 0);
                        })
                        ->orWhere(function ($sq) {
                            $sq->whereNotNull('products.badge')
                                ->where(function ($bq) {
                                    $bq->where('products.badge', 'LIKE', '%sale%')
                                        ->orWhere('products.badge', 'LIKE', '%discount%')
                                        ->orWhere('products.badge', 'LIKE', '%off%');
                                });
                        })
                        ->orWhereHas('variants', function ($vq) {
                            $vq->whereNotNull('selling_price')
                                ->where('selling_price', '>', 0)
                                ->whereColumn('price', '>', 'selling_price');
                        });
                });
            })
            ->when(!empty($brandIds), fn($query) => $query->whereIn('products.brand_id', $brandIds))
            ->when(
                $minPrice !== null && $minPrice > 0,
                fn($query) => $query->whereRaw("({$priceSql}) >= ?", [$minPrice])
            )
            ->when(
                $maxPrice !== null && $maxPrice > 0,
                fn($query) => $query->whereRaw("({$priceSql}) > 0 AND ({$priceSql}) <= ?", [$maxPrice])
            )
            ->withCount([
                'reviews as reviews_count' => fn($query) => $query->where('reviews.status', 1),
            ]);

        // Apply sorting
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
            default => $productsQuery
                ->orderByDesc('products.is_featured')
                ->orderByDesc('products.is_best_selling')
                ->latest('products.created_at'),
        };

        $products = $productsQuery->paginate(12)->withQueryString();
        $this->attachProductReviewMeta($products);

        $brands = Brand::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('name')
            ->get();

        $listingTitle = $currentBrand ? ($currentBrand->name . ' Watches') : $this->resolveListingTitle($gender, $filter, $maxPrice, $categoryId, $categories, $brandIds, $subcategoryId, $onSale);
        $breadcrumbLabel = $currentBrand ? $currentBrand->name : $this->resolveListingBreadcrumb($gender, $filter, $brandIds, $maxPrice, $onSale);

        // Resolve listing header banner (Recommended intrinsic size: 2513 × 400 px)
        $listingBanner = null;
        if ($currentBrand && !empty($currentBrand->banner_url)) {
            $listingBanner = $currentBrand->banner_url;
        } elseif ($gender === 'men') {
            $listingBanner = HomeBanner::getBannerUrl('men');
        } elseif ($gender === 'women') {
            $listingBanner = HomeBanner::getBannerUrl('women');
        } else {
            $listingBanner = HomeBanner::getBannerUrl('default');
        }

        return view('frontend.pages.product_listing', compact(
            'categories',
            'products',
            'brands',
            'listingTitle',
            'breadcrumbLabel',
            'listingBanner',
            'gender',
            'filter',
            'maxPrice',
            'minPrice',
            'sort',
            'brandIds',
            'selectedBrandSlugs',
            'categoryId',
            'subcategoryId',
            'search',
            'onSale',
            'currentBrand',
            'filterAction',
            'clearFilterUrl'
        ));
    }

    private function resolveListingGender($gender): ?string
    {
        $gender = strtolower(trim((string) $gender));

        return in_array($gender, ['women', 'men', 'both'], true) ? $gender : null;
    }

    private function applyListingGenderFilter($query, ?string $gender, ?int $categoryId = null, ?int $subcategoryId = null, bool $onSale = false)
    {
        if ($onSale && empty($gender)) {
            return $query;
        }

        return match ($gender) {
            'women', 'men' => $query->forMenuGender($gender),
            'both' => $query->forBothGender(),
            default => ($categoryId || $subcategoryId || $onSale)
                ? $query
                : $query->withAssignedGender(),
        };
    }

    private function resolveListingTitle(?string $gender, ?string $filter, ?float $maxPrice, ?int $categoryId, $categories, array $brandIds = [], ?int $subcategoryId = null, bool $onSale = false): string
    {
        if ($subcategoryId) {
            $subcategory = SubCategory::where('status', 1)->find($subcategoryId);
            if ($subcategory) {
                return $subcategory->name;
            }
        }

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
            'both' => 'Women & Men',
            default => null,
        };

        if ($onSale) {
            return $genderLabel ? "{$genderLabel} Watches on Sale" : 'Watches on Sale';
        }

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

    private function resolveListingBreadcrumb(?string $gender, ?string $filter, array $brandIds = [], ?float $maxPrice = null, bool $onSale = false): string
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

        if ($onSale) {
            return match ($gender) {
                'women' => 'Women · Sale',
                'men' => 'Men · Sale',
                'both' => 'Women & Men · Sale',
                default => 'Sale',
            };
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
                'both' => "Women & Men · {$priceLabel}",
                default => $priceLabel,
            };
        }

        return match ($gender) {
            'women' => 'Women',
            'men' => 'Men',
            'both' => 'Women & Men',
            default => 'Watches',
        };
    }

    public function productListingGrid($slug)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();

            $categoryFilter = request()->input('category');
            $selectedCategoryIds = $categoryFilter
                ? array_map('intval', (array) $categoryFilter)
                : [$category->id];
            $gender = $this->resolveListingGender(request()->input('gender'));

            $baseQuery = Product::query()
                ->where('status', 1)
                ->tap(fn($query) => $this->applyListingGenderFilter($query, $gender, $selectedCategoryIds[0] ?? null))
                ->inCategories($selectedCategoryIds);

            if ($priceFilter = request()->input('price')) {
                $this->applyPriceRangeFilter($baseQuery, (array) $priceFilter);
            }

            $products = $baseQuery
                ->withCount(['reviews as reviews_count' => fn($q) => $q->where('status', 1)])
                ->latest()
                ->paginate(12)
                ->withQueryString();

            $this->attachProductReviewMeta($products->getCollection());

            $priceRanges = $this->buildPriceRangesWithCounts(
                Product::query()
                    ->where('status', 1)
                    ->tap(fn($query) => $this->applyListingGenderFilter($query, $gender, $selectedCategoryIds[0] ?? null))
                    ->inCategories($selectedCategoryIds)
            );

            $html = view('frontend.partials.product-listing-grid', compact('products'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'price_ranges' => $priceRanges,
                'meta' => [
                    'product_total' => $products->total(),
                    'product_count' => $products->count(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Product listing grid failed: ' . $e->getMessage(), [
                'slug' => $slug,
                'category' => request()->input('category'),
                'price' => request()->input('price'),
            ]);

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

    public function returnPolicy()
    {
        $returnRefund = ReturnRefund::where('is_active', 1)
            ->orderBy('sort_order')
            ->first();

        return view('frontend.pages.return_policy', compact('returnRefund'));
    }

    public function warrantiesGuarantees()
    {
        $warrantyGuarantee = WarrantyGuarantee::where('is_active', 1)
            ->orderBy('sort_order')
            ->first();

        return view('frontend.pages.warranties_guarantees', compact('warrantyGuarantee'));
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
        $brands = Brand::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('name')
            ->get();
        $gender = null;
        $filter = null;
        $maxPrice = null;
        $minPrice = null;
        $sort = null;
        $brandIds = [];
        $selectedBrandSlugs = [];
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
            'selectedBrandSlugs',
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
            [$products,,, $priceRanges] = $this->buildSuperDealProducts();
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

        if ((int) ($product->quantity ?? 0) <= 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Product is out of stock',
                ], 422);
            }

            return back()->with('error', 'Product is out of stock');
        }

        $qty = min(10, max(1, (int) $request->input('qty', 1)));

        if ($qty > (int) $product->quantity) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Only ' . $product->quantity . ' item(s) available in stock',
                ], 422);
            }

            return back()->with('error', 'Only ' . $product->quantity . ' item(s) available in stock');
        }

        BuyNow::where('user_id', Auth::id())->delete();

        BuyNow::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $qty,
            'coupon_code' => session('applied_coupon_code'),
        ]);

        session(['checkout_source' => 'buy_now']);

        return redirect()->route('checkout.shipping');
    }

    public function addToCart(Request $request, $product_id)
    {
        $productId = (int) $product_id;
        $product = Product::findOrFail($productId);

        if ((int) ($product->quantity ?? 0) <= 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Product is out of stock',
                ], 422);
            }

            return back()->with('error', 'Product is out of stock');
        }

        if ($this->cartService->isInCart($productId)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'already_in_cart' => true,
                    'message' => 'Product already exists in cart',
                    'cart_count' => $this->cartService->getCartCount(),
                    'product_id' => $productId,
                    'cart_url' => route('cart'),
                ]);
            }

            return back()->with('success', 'Product already exists in cart');
        }

        $qty = min(10, max(1, (int) $request->input('qty', 1)));
        $cartCount = $this->cartService->addItem($productId, $qty);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'success' => true,
                'already_in_cart' => false,
                'message' => 'Product Added to Cart Successfully',
                'cart_count' => $cartCount,
                'product_id' => $productId,
                'cart_url' => route('cart'),
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

        return back()->with('success', 'Item Removed from Cart');
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

        return back()->with('success', 'Item Removed from Cart');
    }

    //Blogs Page
    public function blogs()
    {
        $blogs = Blog::where('status', 1)
            ->latest()
            ->paginate(6);
        return view('frontend.pages.blogs', compact('blogs'));
    }

    public function blogsDetails($slug)
    {
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

        $availableCoupons = $this->buildAvailableCouponOffers(
            $this->cartService->getCartItems(),
            $subtotal,
            false,
            $summary['couponCode'] ?? null
        );

        $cartProductIds = $cartItems->map(fn($item) => $item->product_id ?? $item->product?->id)->filter()->unique()->toArray();
        $cartBrandIds = $cartItems->map(fn($item) => $item->product?->brand_id)->filter()->unique()->toArray();
        $cartCategoryIds = $cartItems->map(fn($item) => $item->product?->category_id)->filter()->unique()->toArray();

        $suggestedQuery = Product::where('status', 1)
            ->when(!empty($cartProductIds), fn($q) => $q->whereNotIn('id', $cartProductIds))
            ->with(['brand', 'category'])
            ->withCount([
                'reviews as reviews_count' => fn($query) => $query->where('status', 1),
            ]);

        $suggestedProducts = collect();

        // 1. Try to recommend products from same brands or categories as cart items
        if (!empty($cartCategoryIds) || !empty($cartBrandIds)) {
            $suggestedProducts = (clone $suggestedQuery)
                ->where(function ($q) use ($cartCategoryIds, $cartBrandIds) {
                    if (!empty($cartCategoryIds)) {
                        $q->whereIn('category_id', $cartCategoryIds);
                    }
                    if (!empty($cartBrandIds)) {
                        $q->orWhereIn('brand_id', $cartBrandIds);
                    }
                })
                ->inRandomOrder()
                ->take(12)
                ->get();
        }

        // 2. Augment with featured, best-selling, trending, or new arrival products
        if ($suggestedProducts->count() < 8) {
            $existingIds = array_merge($cartProductIds, $suggestedProducts->pluck('id')->toArray());
            $needed = 12 - $suggestedProducts->count();

            $popularProducts = (clone $suggestedQuery)
                ->whereNotIn('id', $existingIds)
                ->where(function ($q) {
                    $q->where('is_featured', 1)
                        ->orWhere('is_best_selling', 1)
                        ->orWhere('is_trending', 1)
                        ->orWhere('is_new_arrival', 1);
                })
                ->inRandomOrder()
                ->take($needed)
                ->get();

            $suggestedProducts = $suggestedProducts->merge($popularProducts);
        }

        // 3. Fallback to active products if still below 4
        if ($suggestedProducts->count() < 4) {
            $existingIds = array_merge($cartProductIds, $suggestedProducts->pluck('id')->toArray());
            $needed = 12 - $suggestedProducts->count();

            $fallbackProducts = (clone $suggestedQuery)
                ->whereNotIn('id', $existingIds)
                ->inRandomOrder()
                ->take($needed)
                ->get();

            $suggestedProducts = $suggestedProducts->merge($fallbackProducts);
        }

        $this->attachProductReviewMeta($suggestedProducts);
        $wishlistIds = $this->wishlistService->getProductIds();

        return view('frontend.pages.cart', array_merge($summary, compact('availableCoupons', 'suggestedProducts', 'wishlistIds')));
    }

    public function availableCoupons()
    {
        $cartItems = $this->resolveCheckoutCartItems();
        $summary = $this->buildCartSummary($cartItems);
        $subtotal = (float) $summary['subtotal'];
        $appliedCode = $summary['couponCode'] ?? null;

        $coupons = $this->buildAvailableCouponOffers($cartItems, $subtotal, false, $appliedCode);

        return response()->json([
            'coupons' => $this->formatCouponOffersForJson($coupons, $appliedCode),
            'subtotal' => $subtotal,
        ]);
    }

    public function wishlist()
    {
        $user = Auth::user();
        $wishlistItems = $this->wishlistService->getItems();
        $products = $wishlistItems->pluck('product')->filter();
        $this->attachProductReviewMeta($products);
        $cartProductIds = $this->cartService->getCartProductIds();

        return view('frontend.pages.myaccount_wishlist', compact('user', 'wishlistItems', 'cartProductIds'));
    }

    public function addToWishlist(Request $request, $productId)
    {
        $this->wishlistService->add((int) $productId);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => true,
                'wishlist_count' => $this->wishlistService->getCount(),
                'message' => 'Added to Wishlist',
            ]);
        }

        return back()->with('success', 'Added to Wishlist');
    }

    public function removeFromWishlist(Request $request, Product $product)
    {
        $this->wishlistService->remove((int) $product->id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'success' => true,
                'in_wishlist' => false,
                'wishlist_count' => $this->wishlistService->getCount(),
                'message' => 'Removed From Wishlist',
            ]);
        }

        return back()->with('success', 'Removed from wishlist');
    }

    public function toggleWishlist(Request $request, $productId)
    {
        $inWishlist = $this->wishlistService->toggle((int) $productId);
        $message = $inWishlist ? 'Added to Wishlist' : 'Removed from Wishlist';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist,
                'wishlist_count' => $this->wishlistService->getCount(),
                'message' => $message,
                'redirect' => $inWishlist ? route('wishlist') : null,
            ]);
        }

        return back()->with('success', $message);
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

        if (Auth::check() && $coupon->isUsedByUser(Auth::id())) {
            return $this->couponActionResponse(
                $request,
                false,
                'You have already used this coupon code. Each coupon can only be used once per customer.'
            );
        }

        $subtotal = 0;
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product) {
                continue;
            }
            $price = (float) ($cartItem->product->selling_price ?? 0);
            if ($price <= 0) {
                $price = (float) ($cartItem->product->price ?? 0);
            }
            $subtotal += $price * $cartItem->quantity;
        }

        $result = $coupon->calculateDiscountForItems($cartItems, $subtotal, Auth::id());

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
            return redirect()->route('login')->with('error', 'Please Login to write a Review');
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
    public function checkoutShipping(Request $request)
    {
        if (!Auth::check()) {
            $intended = $request->query('from') === 'cart'
                ? route('checkout.shipping', ['from' => 'cart'])
                : route('checkout.shipping');
            session(['url.intended' => $intended]);
            return redirect()->route('login')->with('login_required', true);
        }

        if ($request->query('from') === 'cart') {
            session(['checkout_source' => 'cart']);
            BuyNow::where('user_id', Auth::id())->delete();
        }

        $checkout = $this->buildCheckoutSummary();

        if ($checkout['itemCount'] === 0) {
            return redirect()->route('cart')->with('error', 'Your Cart is Empty.');
        }

        $user = auth()->user();
        $addresses = Address::where('user_id', $user->id)
            ->where('status', 1)
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $cartItems = collect($checkout['cartItems']);
        $availableCoupons = $this->buildAvailableCouponOffers(
            $cartItems,
            (float) $checkout['subtotal'],
            false,
            $checkout['couponCode'] ?? null
        );

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
                return response()->json(['status' => false, 'message' => 'Your Cart is Empty.'], 422);
            }

            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $billingPayload = $this->resolveCheckoutAddress($request, $user, 'billing');
        $sameShipping = $request->input('shipping_same_diff_address', 'same') === 'same';
        $shippingPayload = $sameShipping
            ? $billingPayload
            : $this->resolveCheckoutAddress($request, $user, 'shipping');

        $shippingPincode = preg_replace('/\D+/', '', (string) ($shippingPayload['pincode'] ?? ''));
        if (strlen($shippingPincode) === 6) {
            $serviceability = app(ShiprocketService::class)->checkServiceability($shippingPincode, $paymentMethod === 'cod');

            if ($serviceability !== null && ! $serviceability['serviceable']) {
                $message = $paymentMethod === 'cod'
                    ? 'Cash on delivery is not available for pincode ' . $shippingPincode . '. Please Choose Online Payment or Another Address.'
                    : 'Sorry, we do not deliver to pincode ' . $shippingPincode . ' yet.';

                if ($wantsJson) {
                    return response()->json(['status' => false, 'message' => $message], 422);
                }

                return back()->withInput()->with('error', $message);
            }
        }

        // Sync missing profile details from checkout input if not already saved
        if (!empty($billingPayload['phone']) && empty($user->phone)) {
            $userDigits = preg_replace('/\D+/', '', $billingPayload['phone']);
            if (strlen($userDigits) >= 10) {
                $user->phone = '+91' . substr($userDigits, -10);
                $user->save();
            }
        }
        if (!empty($billingPayload['name']) && empty($user->name)) {
            $user->name = $billingPayload['name'];
            $user->save();
        }

        DB::beginTransaction();

        try {
            $grandTotal = $checkout['grandTotal'];
            $payNow = 0;
            $paymentType = 'full';

            if ($paymentMethod === 'razorpay') {
                $payNow = $grandTotal;
                $paymentType = 'full';
            } else {
                // Orders up to Rs. 2,000 can choose Cash on Delivery without any advance payment.
                // Orders above Rs. 2,000 require a mandatory 10% advance payment online.
                if ($grandTotal <= 2000) {
                    $payNow = 0;
                    $paymentType = 'full'; // Full COD
                } else {
                    $payNow = round($grandTotal * 0.10, 2);
                    $paymentType = 'partial'; // 10% online advance, 90% COD
                }
            }

            if (!empty($checkout['couponCode']) && Order::isCouponUsedByUser($user->id, $checkout['couponCode'])) {
                session()->forget(['applied_coupon_code', 'applied_coupon_discount']);
                try {
                    Cart::where('user_id', $user->id)->update(['coupon_code' => null]);
                    BuyNow::where('user_id', $user->id)->update(['coupon_code' => null]);
                } catch (\Throwable $e) {
                }
                return back()->with('error', 'You have already used coupon code "' . $checkout['couponCode'] . '". Each coupon can only be used once per customer.');
            }

            $orderPayload = [
                'user_id' => $user->id,
                'order_id' => 'ORD' . time() . rand(100, 999),
                'invoice_no' => Order::generateNextInvoiceNumber(),
                'invoice_date' => now()->toDateString(),
                'subtotal' => $checkout['subtotal'],
                'discount' => $checkout['discount'],
                'shipping_charge' => $checkout['shippingCharge'],
                'grand_total' => $checkout['grandTotal'],
                'paid_amount' => 0,
                'due_amount' => $checkout['grandTotal'],
                'payment_method' => $paymentMethod,
                'payment_type' => $paymentType,
                'payment_gateway' => $payNow > 0 ? 'razorpay' : null,
                'payment_status' => 'pending',
                'order_status' => $payNow == 0 ? 'processing' : 'pending',
                'notes' => $checkout['couponCode']
                    ? 'Coupon: ' . strtoupper($checkout['couponCode'])
                    : null,
                'coupon_code' => $checkout['couponCode']
                    ? strtoupper($checkout['couponCode'])
                    : null,
                'coupon_discount' => $checkout['discount'] ?? 0,
                'gst_rate' => 18,
                'gst_amount' => $checkout['gstTotal'] ?? 0,
            ];

            $order = Order::create($orderPayload);

            OrderAddress::create(array_merge($shippingPayload, [
                'order_id' => $order->id,
                'type' => 'shipping',
            ]));

            OrderAddress::create(array_merge($billingPayload, [
                'order_id' => $order->id,
                'type' => 'billing',
            ]));

            // Group cart items to prevent duplicate OrderItem records for the same product & variant
            $itemsToProcess = $checkout['cartItems'];
            if (session('checkout_source') === 'buy_now') {
                $buyItems = $this->resolveBuyNowItems();
                if ($buyItems->isNotEmpty()) {
                    $itemsToProcess = $buyItems;
                }
            }

            // Remove any stale/orphaned order items for this order_id before creating new ones
            OrderItem::where('order_id', $order->id)->delete();

            $groupedCartItems = collect($itemsToProcess)->groupBy(function ($item) {
                return $item->product_id . '_' . ($item->variant_id ?? '0');
            });

            foreach ($groupedCartItems as $group) {
                $cartItem = $group->first();
                if (!$cartItem || !$cartItem->product) {
                    continue;
                }

                $quantity = (int) $group->sum('quantity');
                $price = ($cartItem->product->selling_price > 0 ? $cartItem->product->selling_price : $cartItem->product->price) ?? $cartItem->product->price ?? 0;

                OrderItem::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'variant_id' => $cartItem->variant_id ?? null,
                    ],
                    [
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $price * $quantity,
                    ]
                );
            }

            // Decrease stock for COD
            if ($payNow == 0) {
                $this->decreaseOrderStock($order);
            }
            // Coupon usage + cart clear only after payment is confirmed.
            // For Razorpay, wait until verify succeeds so cancel/close keeps the cart.
            if ($payNow == 0 && !empty($checkout['coupon']) && $checkout['discount'] > 0) {
                $checkout['coupon']->increment('used_count');
            }

            $razorpayPayload = null;

            if ($payNow > 0) {
                $razorpayKey = config('services.razorpay.key');
                $razorpaySecret = config('services.razorpay.secret');

                if (empty($razorpayKey) || empty($razorpaySecret)) {
                    throw new \RuntimeException('Online payment gateway is temporarily unavailable. Please contact support or try again later.');
                }

                $api = new Api($razorpayKey, $razorpaySecret);

                $amountPaise = (int) round(((float) $payNow) * 100);

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
                    'key' => $razorpayKey,
                    'amount' => $amountPaise,
                    'currency' => 'INR',
                    'name' => config('app.name', 'The Watch Store'),
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
                        'color' => '#e31c23',
                    ],
                ];
            } else {
                $this->clearCheckoutCart($user->id);
            }

            DB::commit();

            if ($payNow > 0) {
                if ($wantsJson) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Order created. Complete payment to confirm.',
                        'order_id' => $order->id,
                        'order_number' => $order->order_id,
                        'payment_type' => $paymentType,
                        'pay_now' => $payNow,
                        'due_amount' => $order->due_amount,
                        'razorpay' => $razorpayPayload,
                        'verify_url' => route('checkout.razorpay.verify'),
                    ]);
                }

                return redirect()
                    ->route('checkout.shipping')
                    ->with('error', 'Please enable JavaScript to complete Razorpay payment.');
            }

            $this->ensureOrderInvoice($order);

            // Execute email sending in background after response is sent
            // so order completes in ~1-2 seconds without blocking user
            $orderId = $order->id;
            dispatch(function () use ($orderId) {
                try {
                    $freshOrder = Order::find($orderId);
                    if ($freshOrder) {
                        $this->sendOrderSuccessEmail($freshOrder);
                    }
                } catch (\Throwable $e) {
                    Log::error('Background order email failed: ' . $e->getMessage());
                }

                $shiprocket = app(ShiprocketService::class);
                $freshOrder = Order::find($orderId);
                if ($freshOrder && $shiprocket->autoCreateEnabled()) {
                    try {
                        $shiprocket->createOrder($freshOrder);
                    } catch (\Throwable $e) {
                        $shiprocket->recordError($freshOrder, $e);
                    }
                }
            })->afterResponse();

            session()->flash('last_order_id', $order->order_id);
            session()->flash('success', 'Order placed successfully!');

            if ($wantsJson) {
                return response()->json([
                    'status' => true,
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id,
                    'order_number' => $order->order_id,
                    'redirect_url' => route('checkout.success'),
                ]);
            }

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

            $errorMessage = $e instanceof \RuntimeException
                ? $e->getMessage()
                : 'Unable to place order. Please try again.';

            if ($wantsJson) {
                return response()->json([
                    'status' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
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

        if ($order->payment_method !== 'razorpay' && $order->payment_gateway !== 'razorpay') {
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

            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $paidAmount = $payment['amount'] / 100;

            $remainingAmount = $order->grand_total - $paidAmount;
            $paymentStage = ($order->payment_type === 'partial') ? 'advance' : 'full';

            DB::table('order_payments')->insert([
                'order_id' => $order->id,
                'amount' => $paidAmount,
                'payment_method' => 'razorpay',
                'transaction_id' => $request->razorpay_payment_id,
                'status' => 'success',
                'paid_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $order->update([
                'payment_status' => ($remainingAmount <= 0) ? 'paid' : 'partial',
                'payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'transaction_id' => $request->razorpay_payment_id,
                'paid_amount' => $paidAmount,
                'due_amount' => max(0, $remainingAmount),
                'order_status' => 'processing',
            ]);

            if (!empty($order->notes) && str_starts_with($order->notes, 'Coupon: ')) {
                $couponCode = trim(substr($order->notes, strlen('Coupon: ')));
                if ($couponCode !== '') {
                    Coupon::whereRaw('UPPER(code) = ?', [strtoupper($couponCode)])->increment('used_count');
                }
            }

            // Decrease product stock
            $this->decreaseOrderStock($order);

            $this->ensureOrderInvoice($order);

            $this->clearCheckoutCart(Auth::id());

            $orderId = $order->id;
            dispatch(function () use ($orderId) {
                try {
                    $freshOrder = Order::find($orderId);
                    if ($freshOrder) {
                        $this->sendOrderSuccessEmail($freshOrder);
                    }
                } catch (\Throwable $e) {
                    Log::error('Background order email failed: ' . $e->getMessage());
                }

                $shiprocket = app(ShiprocketService::class);
                $freshOrder = Order::find($orderId);
                if ($freshOrder && $shiprocket->autoCreateEnabled()) {
                    try {
                        $shiprocket->createOrder($freshOrder);
                    } catch (\Throwable $e) {
                        $shiprocket->recordError($freshOrder, $e);
                    }
                }
            })->afterResponse();

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

    public function dismissRazorpayPayment(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response()->json(['status' => false, 'message' => 'Order ID required.'], 400);
        }

        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->where('order_status', 'pending')
            ->where('payment_status', 'pending')
            ->first();

        if ($order && (float) ($order->paid_amount ?? 0) <= 0) {
            OrderItem::where('order_id', $order->id)->delete();
            OrderAddress::where('order_id', $order->id)->delete();
            $order->delete();
        }

        return response()->json(['status' => true]);
    }

    public function specialOffers()
    {
        $specialOffer = SpecialOffer::where('is_active', 1)
            ->orderBy('sort_order')
            ->first();

        return view('frontend.pages.index', compact('specialOffer'));
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
        session()->forget(['applied_coupon_code', 'applied_coupon_discount', 'checkout_source']);
    }

    private function ensureOrderInvoice(Order $order): void
    {
        $rawInvoice = $order->getRawOriginal('invoice_no');
        if (!empty($rawInvoice) && preg_match('/^INV\d{3,8}$/', (string) $rawInvoice)) {
            return;
        }

        $order->update([
            'invoice_no' => Order::generateNextInvoiceNumber($order->id),
            'invoice_date' => $order->invoice_date ?? now()->toDateString(),
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
            ->filter(fn($key) => in_array($key, $allowed, true))
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
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $selectedPriceKeys = $this->selectedPriceKeys();

        // FIX: Use whereHas for category relationship
        $baseQuery = Product::query()
            ->where('status', 1)
            ->when(
                !empty($selectedCategoryIds),
                function ($query) use ($selectedCategoryIds) {
                    return $query->whereHas('categories', function ($q) use ($selectedCategoryIds) {
                        $q->whereIn('categories.id', $selectedCategoryIds);
                    });
                },
                function ($query) use ($category) {
                    return $query->whereHas('categories', function ($q) use ($category) {
                        $q->where('categories.id', $category->id);
                    });
                }
            );

        $priceRanges = $this->buildPriceRangesWithCounts(clone $baseQuery);

        $products = (clone $baseQuery)
            ->tap(fn($query) => $this->applyPriceRangeFilter($query, $selectedPriceKeys))
            ->withCount([
                'reviews as reviews_count' => fn($query) => $query->where('status', 1),
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
            ->map(fn($id) => (int) $id)
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
                fn($query) => $query->inCategories($selectedCategoryIds)
            );

        $priceRanges = $this->buildPriceRangesWithCounts(clone $baseQuery);

        $products = (clone $baseQuery)
            ->tap(fn($query) => $this->applyPriceRangeFilter($query, $selectedPriceKeys))
            ->withCount([
                'reviews as reviews_count' => fn($query) => $query->where('status', 1),
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
            $selling = (float) ($product->selling_price ?? 0);
            $stored = (int) ($product->discount ?? 0);

            if (($selling <= 0 || $selling >= $price) && $stored > 0 && $price > 0) {
                $selling = max(0, round($price - ($price * ($stored / 100)), 2));
                $product->selling_price = $selling;
            }

            if ($selling <= 0) {
                $selling = $price;
            }

            $discount = 0;
            if ($selling < $price) {
                $discount = $stored > 0 ? $stored : (int) round((($price - $selling) / $price) * 100);
            } elseif ($stored > 0) {
                $discount = $stored;
            }

            $product->setAttribute('discount_percent', $discount);
        });
    }

    private function resolveCheckoutCartItems()
    {
        $source = session('checkout_source', 'cart');
        $cartItems = $this->cartService->getCartItems();

        if ($source === 'buy_now' || ($source !== 'cart' && $cartItems->isEmpty())) {
            $buyNowItems = $this->resolveBuyNowItems();
            if ($buyNowItems->isNotEmpty()) {
                return $buyNowItems;
            }
        }

        return $cartItems;
    }

    private function resolveBuyNowItems()
    {
        if (!Auth::check()) {
            return collect();
        }

        $buyNow = BuyNow::where('user_id', Auth::id())->latest()->first();

        if (!$buyNow || !$buyNow->product_id) {
            return collect();
        }

        $product = $buyNow->product ?: Product::find($buyNow->product_id);

        if (!$product) {
            return collect();
        }

        $product->loadMissing('gstSetting');

        return collect([(object) [
            'id' => 'buynow_' . $buyNow->id,
            'product_id' => $buyNow->product_id,
            'variant_id' => $buyNow->variant_id,
            'quantity' => max(1, (int) $buyNow->quantity),
            'product' => $product,
        ]]);
    }

    private function buildAvailableCouponOffers($cartItems, float $subtotal, bool $includeIneligible = false, ?string $appliedCode = null)
    {
        $appliedCode = strtoupper((string) $appliedCode);
        $userId = Auth::id();

        return Coupon::where('status', 1)
            ->latest()
            ->get()
            ->map(fn(Coupon $coupon) => $coupon->buildAvailableOfferForCart(collect($cartItems), $subtotal, $includeIneligible, $userId))
            ->filter(function ($offer) {
                return $offer && !empty($offer['can_apply']);
            })
            ->sortByDesc(fn(array $offer) => ((int) $offer['can_apply'] * 1000000) + (float) $offer['discount'])
            ->values();
    }

    private function buildCheckoutSummary(): array
    {
        return $this->buildCartSummary($this->resolveCheckoutCartItems());
    }

    private function buildCartSummary($cartItems): array
    {
        $subtotal = 0;
        $gstTotal = 0;
        $gstRateLabel = 'GST (18% inclusive)';
        $itemCount = 0;
        $gstRates = [];

        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product) {
                continue;
            }

            $itemCount++;
            $originalPrice = (float) ($cartItem->product->price ?? 0);
            $sellingPrice = (float) ($cartItem->product->selling_price ?? 0);
            if ($sellingPrice <= 0) {
                $sellingPrice = $originalPrice;
            }
            $quantity = (int) $cartItem->quantity;
            $itemSubtotal = $sellingPrice * $quantity;

            // GET GST RATE FROM PRODUCT
            $gstRate = $cartItem->product->getGstRate();
            $gstRates[] = $gstRate;

            // Calculate GST inclusive - Formula: (Price × Rate) / (100 + Rate)
            $itemGst = ($itemSubtotal * $gstRate) / (100 + $gstRate);

            $subtotal += $itemSubtotal;
            $gstTotal += $itemGst;
        }

        $subtotal = round($subtotal, 2);
        $gstTotal = round($gstTotal, 2);

        // Determine GST label
        if (!empty($gstRates)) {
            $uniqueRates = array_values(array_unique($gstRates));
            if (count($uniqueRates) === 1) {
                $gstRateLabel = 'GST (' . rtrim(rtrim(number_format($uniqueRates[0], 2), '0'), '.') . '% inclusive)';
            } else {
                $gstRateLabel = 'GST (inclusive)';
            }
        }

        // Shipping calculation
        $shippingCharge = 0;
        $shippingSetting = ShippingSetting::first();
        if ($shippingSetting && $subtotal > 0) {
            $freeShippingAmount = (float) $shippingSetting->free_shipping_amount;
            $shippingCharge = $subtotal >= $freeShippingAmount ? 0 : (float) $shippingSetting->shipping_charge;
        }

        // Coupon logic
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
                if (Auth::check() && $coupon->isUsedByUser(Auth::id())) {
                    // Coupon already used by this user in a previous order
                    session()->forget(['applied_coupon_code', 'applied_coupon_discount']);
                    try {
                        Cart::where('user_id', Auth::id())->update(['coupon_code' => null]);
                        BuyNow::where('user_id', Auth::id())->update(['coupon_code' => null]);
                    } catch (\Throwable $e) {
                    }
                    $coupon = null;
                    $couponCode = null;
                } else {
                    $result = $coupon->calculateDiscountForItems(collect($cartItems), $subtotal, Auth::id());
                    if ($result['discount'] > 0) {
                        $discount = $result['discount'];
                        session(['applied_coupon_discount' => $discount]);
                    } else {
                        session()->forget(['applied_coupon_code', 'applied_coupon_discount']);
                        $coupon = null;
                        $couponCode = null;
                    }
                }
            } else {
                session()->forget(['applied_coupon_code', 'applied_coupon_discount']);
                $couponCode = null;
            }
        }

        // Recalculate included GST proportionally after discount
        if ($subtotal > 0 && $discount > 0) {
            $ratio = max(0, ($subtotal - $discount) / $subtotal);
            $gstTotal = round($gstTotal * $ratio, 2);
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
            'gstRateLabel' => $summary['gstRateLabel'] ?? 'GST (18% inclusive)',
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
            $prefix . '_email' => 'nullable|email|max:255',
            $prefix . '_phone' => 'required|string|max:20',
            $prefix . '_address_line_1' => 'required|string|max:500',
            $prefix . '_address_line_2' => 'nullable|string|max:500',
            $prefix . '_city' => 'required|string|max:255',
            $prefix . '_state' => 'required|string|max:255',
            $prefix . '_pincode' => 'required|string|max:10',
        ], [], [
            $prefix . '_name' => 'name',
            $prefix . '_email' => 'email',
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
    public function privacyPolicy()
    {
        $privacy = PrivacyPolicy::first();
        return view('frontend.pages.privacy_policy', compact('privacy'));
    }

    //Terms and Conditions Page
    public function termsAndConditions()
    {
        $terms = TermsAndCondition::first();
        return view('frontend.pages.terms_and_conditions_page', compact('terms'));
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
            ['frontend.pages.faqs', 'frontend.pages.faqs'],
            compact('helpCategories')
        );
    }

    //Login Page
    public function login()
    {
        return view('frontend.auth.login');
    }

    //Signup Page
    public function signup()
    {
        return view('frontend.pages.signup');
    }

    //Forgot Password Page
    public function forgotPassword()
    {
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

    public function saveAddress(Request $request)
    {
        $user = auth()->user();
        $address = Address::where('user_id', $user->id)->where('is_default', 1)->first();
        $address->update($request->all());
        return back()->with('success', 'Address saved successfully.');
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
            ->where(function ($q) {
                // Do not show abandoned/unpaid pending orders if user exited without payment
                $q->where('order_status', '!=', 'pending')
                    ->orWhere('payment_status', 'paid')
                    ->orWhere('payment_status', 'partial')
                    ->orWhere(function ($sub) {
                        $sub->where('order_status', 'pending')
                            ->whereNull('payment_gateway')
                            ->where('payment_status', '!=', 'failed');
                    });
            })
            ->when($status === 'delivered', fn($q) => $q->where('order_status', 'delivered'))
            ->when($status === 'cancelled', fn($q) => $q->where('order_status', 'cancelled'))
            ->when($status === 'returns', function ($q) {
                $q->whereNotNull('return_requested_at');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Cleanup any accidental duplicate or ghost/mismatched order items for these orders
        $this->cleanupOrderItemsForOrders($orders);

        return view('frontend.pages.myaccount_orders', compact('user', 'orders'));
    }

    public function myAccountOrderDetails(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        // Prevent viewing uncompleted / abandoned pending orders
        if ($order->order_status === 'pending' && $order->payment_status === 'pending' && $order->payment_gateway === 'razorpay' && (float) ($order->paid_amount ?? 0) <= 0) {
            abort(404);
        }

        // Cleanup duplicate or ghost items if any
        $this->cleanupOrderItemsForOrders($order);

        $order->load(['items.product', 'shippingAddress', 'addresses']);

        return view('frontend.pages.order_details', compact('order'));
    }

    private function cleanupOrderItemsForOrders($orders): void
    {
        $orderList = $orders instanceof \Illuminate\Support\Collection || $orders instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $orders
            : collect([$orders]);

        $orderIds = $orderList->pluck('id')->filter()->all();
        if (empty($orderIds)) {
            return;
        }

        $deletedAny = false;

        // 1. Remove duplicate items with exact same (order_id, product_id, variant_id)
        $duplicateItemIds = OrderItem::whereIn('order_id', $orderIds)
            ->select('id', 'order_id', 'product_id', 'variant_id')
            ->get()
            ->groupBy(fn($item) => $item->order_id . '_' . $item->product_id . '_' . ($item->variant_id ?? '0'))
            ->filter(fn($group) => $group->count() > 1)
            ->flatMap(fn($group) => $group->slice(1)->pluck('id'))
            ->all();

        if (!empty($duplicateItemIds)) {
            OrderItem::whereIn('id', $duplicateItemIds)->delete();
            $deletedAny = true;
        }

        // 2. Remove ghost / unpurchased items where item totals exceed order subtotal/grand_total
        foreach ($orderList as $ord) {
            $ordItems = OrderItem::where('order_id', $ord->id)->with('product')->get();
            if ($ordItems->count() <= 1) {
                continue;
            }

            $orderSubtotal = (float) ($ord->subtotal > 0 ? $ord->subtotal : $ord->grand_total);
            $itemsSum = (float) $ordItems->sum(function ($it) {
                $p = (float) ($it->price > 0 ? $it->price : ($it->product->selling_price ?? $it->product->price ?? 0));
                $q = max(1, (int) $it->quantity);
                return (float) ($it->total > 0 ? $it->total : ($p * $q));
            });

            if ($orderSubtotal > 0 && ($itemsSum - $orderSubtotal) > 1.0) {
                // Check if one item matches the order total
                $matchingItem = $ordItems->first(function ($it) use ($orderSubtotal) {
                    $p = (float) ($it->price > 0 ? $it->price : ($it->product->selling_price ?? $it->product->price ?? 0));
                    $q = max(1, (int) $it->quantity);
                    $lineTotal = (float) ($it->total > 0 ? $it->total : ($p * $q));
                    return abs($lineTotal - $orderSubtotal) < 1.0;
                });

                $invalidIds = [];
                if ($matchingItem) {
                    $invalidIds = $ordItems->where('id', '!=', $matchingItem->id)->pluck('id')->all();
                } else {
                    $invalidIds = $ordItems->filter(function ($it) use ($orderSubtotal) {
                        $p = (float) ($it->price > 0 ? $it->price : ($it->product->selling_price ?? $it->product->price ?? 0));
                        $q = max(1, (int) $it->quantity);
                        $lineTotal = (float) ($it->total > 0 ? $it->total : ($p * $q));
                        return $lineTotal > ($orderSubtotal + 1.0);
                    })->pluck('id')->all();
                }

                if (!empty($invalidIds)) {
                    OrderItem::whereIn('id', $invalidIds)->delete();
                    $deletedAny = true;
                }
            }
        }

        if ($deletedAny) {
            foreach ($orderList as $ord) {
                $ord->load(['items.product.brand']);
            }
        }
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
        $fallback = asset('frontend/assets/images/no-image.png');

        if ($search === '') {
            $trendingProducts = Product::where('status', 1)
                ->where(function ($query) {
                    $query->where('is_trending', 1)
                        ->orWhere('is_best_selling', 1)
                        ->orWhere('is_new_arrival', 1);
                })
                ->with(['brand', 'category'])
                ->orderByDesc('is_trending')
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->map(fn($product) => [
                    'type' => 'product',
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('product.details', ['slug' => $product->slug]),
                    'image' => $product->thumbnailUrl(),
                    'price' => ($product->selling_price > 0 ? $product->selling_price : $product->price) ?? $product->price,
                    'brand' => $product->brand->name ?? '',
                ])
                ->values();

            if ($trendingProducts->isEmpty()) {
                $trendingProducts = Product::where('status', 1)
                    ->orderByDesc('id')
                    ->limit(6)
                    ->get()
                    ->map(fn($product) => [
                        'type' => 'product',
                        'id' => $product->id,
                        'name' => $product->name,
                        'url' => route('product.details', ['slug' => $product->slug]),
                        'image' => $product->thumbnailUrl(),
                        'price' => ($product->selling_price > 0 ? $product->selling_price : $product->price) ?? $product->price,
                    ])
                    ->values();
            }

            $categories = Category::where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(8)
                ->get()
                ->map(fn($category) => [
                    'type' => 'category',
                    'id' => $category->id,
                    'name' => $category->name,
                    'url' => route('product.listing', ['category' => $category->id]),
                    'image' => $category->image_url,
                ])
                ->values();

            $trendingSearches = $categories->take(6)->map(fn($category) => [
                'name' => $category['name'],
                'url' => $category['url'],
            ])->values();

            if ($trendingSearches->isEmpty()) {
                $trendingSearches = Brand::where('status', 1)
                    ->orderBy('position')
                    ->limit(6)
                    ->get()
                    ->map(fn($brand) => [
                        'name' => $brand->name,
                        'url' => route('product.listing', ['brand' => [$brand->id]]),
                    ])
                    ->values();
            }

            return response()->json([
                'mode' => 'discover',
                'trending' => $trendingProducts,
                'trending_searches' => $trendingSearches,
                'categories' => $categories,
                'results' => [],
            ]);
        }

        $like = "%{$search}%";
        $startsWith = "{$search}%";
        $cleanSearch = preg_replace('/[\s\-_]+/', '', $search);

        // Search Products
        $products = Product::where('status', 1)
            ->where(function ($query) use ($like, $search, $cleanSearch) {
                $query->where('name', 'LIKE', $like)
                    ->orWhere('model_no', 'LIKE', $like)
                    ->orWhere('sku', 'LIKE', $like)
                    ->orWhere('description', 'LIKE', $like);

                if ($cleanSearch !== '' && $cleanSearch !== $search && strlen($cleanSearch) >= 2) {
                    $cleanLike = "%{$cleanSearch}%";
                    $query->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(model_no, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike])
                        ->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(sku, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike]);
                }

                $query->orWhereHas('variants', function ($vq) use ($like, $cleanSearch, $search) {
                    $vq->where('sku', 'LIKE', $like);
                    if ($cleanSearch !== '' && $cleanSearch !== $search && strlen($cleanSearch) >= 2) {
                        $cleanLike = "%{$cleanSearch}%";
                        $vq->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(sku, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike]);
                    }
                })
                    ->orWhereHas('brand', function ($q) use ($like) {
                        $q->where('status', 1)->where('name', 'LIKE', $like);
                    })
                    ->orWhereHas('category', function ($q) use ($like) {
                        $q->where('status', 1)->where('name', 'LIKE', $like);
                    });
            })
            ->orderByRaw('CASE WHEN model_no = ? THEN 0 WHEN model_no LIKE ? THEN 1 WHEN sku LIKE ? THEN 2 WHEN name LIKE ? THEN 3 ELSE 4 END', [$search, $startsWith, $startsWith, $startsWith])
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'slug', 'thumbnail', 'selling_price', 'price', 'model_no', 'sku'])
            ->map(fn($product) => [
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'model_no' => $product->model_no,
                'sku' => $product->sku,
                'url' => route('product.details', ['slug' => $product->slug]),
                'image' => $product->thumbnailUrl(),
                'price' => ($product->selling_price > 0 ? $product->selling_price : $product->price) ?? $product->price,
            ])
            ->values();

        return response()->json([
            'mode' => 'results',
            'results' => $products,
            'search_url' => route('search', ['search' => $search]),
        ]);
    }
    public function searchResults(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        if ($search === '') {
            return redirect()->route('home');
        }

        $gender = $this->resolveListingGender($request->input('gender'));
        $filter = $request->input('filter');
        $categoryId = $request->filled('category') ? (int) $request->input('category') : null;
        $subcategoryId = $request->filled('subcategory') ? (int) $request->input('subcategory') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : null;
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : null;
        $sort = $request->input('sort');

        $rawBrandInput = $request->input('brand', $request->input('brand_id', []));
        $rawBrandList = array_values(array_filter(
            array_map(fn($v) => is_scalar($v) ? trim((string) $v) : '', (array) $rawBrandInput),
            fn($v) => $v !== ''
        ));

        $allBrands = Brand::all();
        $selectedBrands = collect();
        $hasNumericBrandInput = false;

        foreach ($rawBrandList as $raw) {
            $matched = null;
            if (ctype_digit($raw)) {
                $hasNumericBrandInput = true;
                $matched = $allBrands->firstWhere('id', (int) $raw);
            } else {
                $matched = $allBrands->first(function ($b) use ($raw) {
                    return strcasecmp((string) $b->slug, $raw) === 0
                        || strcasecmp(\Illuminate\Support\Str::slug($b->name), $raw) === 0;
                });
            }

            if ($matched && ! $selectedBrands->contains('id', $matched->id)) {
                $selectedBrands->push($matched);
            }
        }

        if ($hasNumericBrandInput && $selectedBrands->isNotEmpty()) {
            $redirectParams = $request->query();
            $redirectParams['brand'] = $selectedBrands->pluck('slug')->values()->all();
            unset($redirectParams['brand_id']);

            return redirect()->route('search', $redirectParams, 301);
        }

        $brandIds = $selectedBrands->pluck('id')->all();
        $selectedBrandSlugs = $selectedBrands->pluck('slug')->all();

        $categories = Category::where('status', 1)
            ->withStartingFromPrice()
            ->withCount('products')
            ->when(in_array($gender, ['women', 'men'], true), fn($query) => $query->forMenuGender($gender))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($gender && $categories->isEmpty()) {
            $categories = Category::where('status', 1)
                ->withStartingFromPrice()
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        $priceSql = Category::productPriceSql('products');

        $productsQuery = Product::query()
            ->where('products.status', 1)
            ->with(['brand', 'category'])
            ->where(function ($query) use ($search) {
                $cleanSearch = preg_replace('/[\s\-_]+/', '', $search);
                $query->where('products.name', 'LIKE', "%{$search}%")
                    ->orWhere('products.model_no', 'LIKE', "%{$search}%")
                    ->orWhere('products.sku', 'LIKE', "%{$search}%")
                    ->orWhere('products.description', 'LIKE', "%{$search}%")
                    ->orWhere('products.details', 'LIKE', "%{$search}%");

                if ($cleanSearch !== '' && $cleanSearch !== $search && strlen($cleanSearch) >= 2) {
                    $cleanLike = "%{$cleanSearch}%";
                    $query->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(products.model_no, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike])
                        ->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(products.sku, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike]);
                }

                $query->orWhereHas('variants', function ($vq) use ($search, $cleanSearch) {
                    $vq->where('sku', 'LIKE', "%{$search}%");
                    if ($cleanSearch !== '' && $cleanSearch !== $search && strlen($cleanSearch) >= 2) {
                        $cleanLike = "%{$cleanSearch}%";
                        $vq->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(sku, ''), '-', ''), ' ', ''), '_', '') LIKE ?", [$cleanLike]);
                    }
                })
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('status', 1)
                            ->where('name', 'LIKE', "%{$search}%");
                    })
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
            ->tap(fn($query) => $this->applyListingGenderFilter($query, $gender, $categoryId, $subcategoryId))
            ->when($subcategoryId, fn($query) => $query->inSubcategory($subcategoryId))
            ->when(! $subcategoryId && $categoryId, fn($query) => $query->inCategory($categoryId))
            ->when($filter === 'best_selling', fn($query) => $query->where('products.is_best_selling', 1))
            ->when($filter === 'new_arrival', fn($query) => $query->where('products.is_new_arrival', 1))
            ->when(!empty($brandIds), fn($query) => $query->whereIn('products.brand_id', $brandIds))
            ->when(
                $minPrice !== null && $minPrice > 0,
                fn($query) => $query->whereRaw("({$priceSql}) >= ?", [$minPrice])
            )
            ->when(
                $maxPrice !== null && $maxPrice > 0,
                fn($query) => $query->whereRaw("({$priceSql}) > 0 AND ({$priceSql}) <= ?", [$maxPrice])
            )
            ->withCount([
                'reviews as reviews_count' => fn($query) => $query->where('reviews.status', 1),
            ]);

        // Apply sorting
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
                ->orderByRaw('CASE WHEN products.model_no = ? THEN 0 WHEN products.model_no LIKE ? THEN 1 WHEN products.name LIKE ? THEN 2 ELSE 3 END', [$search, "{$search}%", "{$search}%"])
                ->orderByDesc('products.is_featured')
                ->orderByDesc('products.is_best_selling')
                ->latest('products.created_at'),
            default => $productsQuery
                ->orderByRaw('CASE WHEN products.model_no = ? THEN 0 WHEN products.model_no LIKE ? THEN 1 WHEN products.name LIKE ? THEN 2 ELSE 3 END', [$search, "{$search}%", "{$search}%"])
                ->latest('products.id'),
        };

        $products = $productsQuery->paginate(12)->withQueryString();

        $this->attachProductReviewMeta($products->getCollection());

        $brands = Brand::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('name')
            ->get();

        return view('frontend.pages.search_results', compact(
            'search',
            'products',
            'categories',
            'brands',
            'gender',
            'filter',
            'maxPrice',
            'minPrice',
            'sort',
            'brandIds',
            'selectedBrandSlugs',
            'categoryId',
            'subcategoryId'
        ));
    }

    public function ourServices()
    {
        $services = Service::where('status', 1)
            ->orderBy('sort_order')
            ->get();

        return view('frontend.pages.services', compact('services'));
    }

    public function brands(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $letter = strtoupper(trim((string) $request->input('letter', '')));
        $sort = $request->input('sort');

        $brandsQuery = Brand::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->withCount(['products' => function ($q) {
                $q->where('status', 1);
            }]);

        if ($search !== '') {
            $brandsQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($letter !== '' && ctype_alpha($letter)) {
            $brandsQuery->where('name', 'LIKE', "{$letter}%");
        }

        match ($sort) {
            'name_asc' => $brandsQuery->orderBy('name', 'asc'),
            'name_desc' => $brandsQuery->orderBy('name', 'desc'),
            'popular' => $brandsQuery->orderByDesc('products_count')->orderBy('name', 'asc'),
            'latest' => $brandsQuery->latest('id'),
            default => $brandsQuery->orderBy('position')->orderBy('name', 'asc'),
        };

        $brands = $brandsQuery->get();
        $totalCount = $brands->count();
        $shownCount = $totalCount;

        $allActiveBrands = Brand::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $availableLetters = $allActiveBrands
            ->map(fn($b) => strtoupper(substr(trim($b->name), 0, 1)))
            ->filter(fn($c) => ctype_alpha($c))
            ->unique()
            ->sort()
            ->values()
            ->all();

        $brandsBanner = HomeBanner::getBannerUrl('brands');

        return view('frontend.pages.brands', compact(
            'brands',
            'search',
            'letter',
            'sort',
            'availableLetters',
            'totalCount',
            'shownCount',
            'brandsBanner'
        ));
    }

    public function explore()
    {
        $exploreSubcategories = SubCategory::where('status', 1)
            ->where('show_in_explore', 1)
            ->with('category')
            ->orderBy('id')
            ->get();

        if ($exploreSubcategories->isEmpty()) {
            $exploreSubcategories = SubCategory::where('status', 1)
                ->with('category')
                ->latest()
                ->get();
        }

        $categories = Category::where('status', 1)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $featuredProducts = Product::where('status', 1)
            ->where(function ($q) {
                $q->where('is_featured', 1)
                    ->orWhere('is_trending', 1)
                    ->orWhere('is_best_selling', 1);
            })
            ->with(['brand', 'category'])
            ->withCount([
                'reviews as reviews_count' => fn($query) => $query->where('status', 1),
            ])
            ->latest()
            ->take(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('status', 1)
                ->with(['brand', 'category'])
                ->withCount([
                    'reviews as reviews_count' => fn($query) => $query->where('status', 1),
                ])
                ->latest()
                ->take(8)
                ->get();
        }

        $this->attachProductReviewMeta($featuredProducts);

        $services = Service::where('status', 1)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $brands = Brand::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('position')
            ->get();

        $wishlistIds = [];
        try {
            $wishlistIds = $this->wishlistService->getProductIds();
        } catch (\Throwable $e) {
            $wishlistIds = [];
        }

        $listingTitle = 'Explore Our Products & Services';

        return view('frontend.pages.explore', compact(
            'exploreSubcategories',
            'categories',
            'featuredProducts',
            'services',
            'brands',
            'wishlistIds',
            'listingTitle'
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

    public function cancelOrderAdmin(Order $order)
    {
        try {
            Mail::to(config('mail.admin_order_email'))->send(new OrderCancelledAdmin($order));
        } catch (\Throwable $e) {
            Log::error('Order cancelled admin email failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'email' => $order->user->email,
            ]);
        }
    }

    private function decreaseOrderStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {

            $qty = (int) $item->quantity;

            if ($qty <= 0) {
                continue;
            }

            // Variable product
            if ($item->variant_id) {

                $variant = ProductVariant::where('id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    throw new \RuntimeException(
                        "Product variant not found for Order Item ID: {$item->id}"
                    );
                }

                if ($variant->quantity < $qty) {
                    throw new \RuntimeException(
                        "Insufficient stock for variant ID: {$variant->id}"
                    );
                }

                $variant->decrement('quantity', $qty);
            } else {

                // Simple product
                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new \RuntimeException(
                        "Product not found for Order Item ID: {$item->id}"
                    );
                }

                if ($product->quantity < $qty) {
                    throw new \RuntimeException(
                        "Insufficient stock for product ID: {$product->id}"
                    );
                }

                $product->decrement('quantity', $qty);
            }
        }
    }

    private function increaseOrderStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {

            $qty = (int) $item->quantity;

            if ($qty <= 0) {
                continue;
            }

            // Variable product
            if ($item->variant_id) {

                $variant = ProductVariant::where('id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();

                if ($variant) {
                    $variant->increment('quantity', $qty);
                }
            } else {

                // Simple product
                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($product) {
                    $product->increment('quantity', $qty);
                }
            }
        }
    }
}
