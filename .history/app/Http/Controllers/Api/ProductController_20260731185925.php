<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Slider;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Review;
use App\Models\ReviewReaction;
use App\Models\ProductFaq;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\OrderNote;
use App\Models\OrderRefund;
use App\Models\OrderAddress;
use App\Models\OrderShippingAddress;
use App\Models\OrderBillingAddress;
use App\Models\Address;
use App\Models\BuyNow;
use App\Models\ShippingSetting;
use App\Models\Newsletter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\HomeBanner;
use Laravel\Sanctum\PersonalAccessToken;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Home API
    |--------------------------------------------------------------------------
    */
    /**
 * Check product stock availability (handles both simple and variant products)
 */
private function checkProductStock($productId, $quantity = 1, $variantId = null)
{
    $product = Product::find($productId);
    
    if (!$product) {
        return [
            'available' => false,
            'message' => 'Product not found',
            'available_stock' => 0,
            'product_type' => null
        ];
    }
    
    // If variant is specified, check variant stock
    if ($variantId) {
        $variant = ProductVariant::find($variantId);
        
        if (!$variant) {
            return [
                'available' => false,
                'message' => 'Variant not found',
                'available_stock' => 0,
                'product_type' => 'variable'
            ];
        }
        
        if ($variant->quantity <= 0) {
            return [
                'available' => false,
                'message' => 'This variant is currently out of stock',
                'available_stock' => 0,
                'product_type' => 'variable'
            ];
        }
        
        if ($quantity > $variant->quantity) {
            return [
                'available' => false,
                'message' => 'Not enough stock for this variant. Available: ' . $variant->quantity,
                'available_stock' => $variant->quantity,
                'product_type' => 'variable'
            ];
        }
        
        return [
            'available' => true,
            'message' => 'In stock',
            'available_stock' => $variant->quantity,
            'product_type' => 'variable'
        ];
    }
    
    // Simple product stock check
    if ($product->quantity <= 0) {
        return [
            'available' => false,
            'message' => 'This product is currently out of stock',
            'available_stock' => 0,
            'product_type' => 'simple'
        ];
    }
    
    if ($quantity > $product->quantity) {
        return [
            'available' => false,
            'message' => 'Not enough stock available. Available: ' . $product->quantity,
            'available_stock' => $product->quantity,
            'product_type' => 'simple'
        ];
    }
    
    return [
        'available' => true,
        'message' => 'In stock',
        'available_stock' => $product->quantity,
        'product_type' => 'simple'
    ];
}

/**
 * Get available stock for a product (handles both simple and variant)
 */
private function getProductAvailableStock($productId, $variantId = null)
{
    if ($variantId) {
        $variant = ProductVariant::find($variantId);
        return $variant ? (int) $variant->quantity : 0;
    }
    
    $product = Product::find($productId);
    return $product ? (int) $product->quantity : 0;
}
    public function home(Request $request)
    {
          try {
        // Get authenticated user from token first
        $user = auth('sanctum')->user();
        
        // If no token auth, check for user_id parameter (for debugging/mobile apps)
        if (!$user && $request->has('user_id')) {
            $user = \App\Models\User::find($request->user_id);
        }
        
        // Fallback to default guard
        if (!$user) {
            $user = auth()->user();
        }
        
        $wishlistProductIds = [];
        $cartProductIds = [];

        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)
                ->pluck('product_id')
                ->toArray();
            $cartProductIds = Cart::where('user_id', $user->id)
                ->pluck('product_id')
                ->toArray();
                
            // Debug logging - check your laravel.log file
            \Log::info('Home API - User ID: ' . $user->id);
            \Log::info('Home API - Wishlist IDs: ' . json_encode($wishlistProductIds));
            \Log::info('Home API - Is product 252 in wishlist? ' . (in_array(252, $wishlistProductIds) ? 'YES' : 'NO'));
        } else {
            // Guest user logic
            $sessionId = $request->header('session-id');
            if ($sessionId) {
                $cartProductIds = Cart::where('session_id', $sessionId)
                    ->pluck('product_id')
                    ->toArray();
            }
        }

            $categories = Category::where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(function ($category) {
                    $category->image = url('backend/assets/images/category/' . $category->image);
                    return $category;
                });

            $sliders = Slider::where('status', 1)
                ->latest()
                ->get()
                ->map(function ($slider) {
                    $slider->image = url('backend/assets/images/slider/' . $slider->image);
                    return $slider;
                });
               $banners = HomeBanner::where('status', 1)
    ->orderBy('sort_order', 'asc')
    ->get();

$bannerData = $banners->mapWithKeys(function ($banner) {
    return [
        str_replace('-', '_', $banner->slug) => [
            'id' => $banner->id,
            'name' => $banner->name,
            'slug' => $banner->slug,
            'image' => $banner->image
                ? url($banner->image)
                : null,
            'sort_order' => $banner->sort_order,
            'recommended_size' => $banner->recommended_size,
        ]
    ];
});
            $blogs = Blog::where('status', 1)
                ->latest()
                ->get()
                ->map(function ($blog) {
                    $blog->image = url('backend/assets/images/blog/' . $blog->image);
                    return $blog;
                });

            $products = [];
            $bestSelling = [];
            $topRated = [];

            $allCategories = Category::where('status', 1)
                ->with(['products' => function ($query) {
                    $query->where('status', 1)
                        ->with('subcategory')
                        ->with(['reviews' => function ($q) {
                            $q->where('status', 1)->with('user');
                        }])
                        ->latest();
                }])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            foreach ($allCategories as $category) {
                $categoryKey = \Illuminate\Support\Str::slug($category->name, '_');
                $products[$categoryKey] = $category->products->map(function ($product) use (&$bestSelling, &$topRated, $wishlistProductIds, $cartProductIds, $user) {
                    $approvedReviews = $product->reviews->where('status', 1);
                    $discountValue = 0;

                    if (
                        $product->price !== null &&
                        $product->selling_price !== null &&
                        $product->price > 0 &&
                        $product->selling_price < $product->price
                    ) {
                        $discountAmount = $product->price - $product->selling_price;
                        $discountValue = round(($discountAmount / $product->price) * 100);
                    }

                    // Wishlist flag
                   $isWishlist = in_array($product->id, $wishlistProductIds ?? []);
$isCart = in_array($product->id, $cartProductIds ?? []);
                    $formattedProduct = [
                        'id' => $product->id,
                        'category_id' => $product->category_id,
                        'subcategory_id' => $product->subcategory_id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'thumbnail' => $product->thumbnail
                            ? url('backend/assets/images/product/' . $product->thumbnail)
                            : null,
                        'price' => $product->price,
                        'selling_price' => $product->selling_price,
                        'details' => $product->details,
                        'description' => $product->description,
                        'specification' => $product->specification,
                        'quantity' => $product->quantity,
                        'gallery' => collect($product->gallery)->map(fn($img) =>
                            url('backend/assets/images/product/' . $img)
                        ),
                        'gallery_order' => collect($product->gallery_order)->map(fn($img) =>
                            url('backend/assets/images/product/' . $img)
                        ),
                        'status' => $product->status,
                        'is_trending' => $product->is_trending,
                        'is_featured' => $product->is_featured,
                        'is_best_selling' => $product->is_best_selling,
                        'is_top_rated' => $product->is_top_rated,
                        'is_new_arrival' => $product->is_new_arrival,
                        'badge' => $product->badge,
                        'discount' => $discountValue,
                        'subcategory' => $product->subcategory,
                        'average_rating' => round($approvedReviews->avg('rating') ?? 0, 1),
                        'total_reviews' => $approvedReviews->count(),
                        'is_wishlist' => $isWishlist,
                        'is_cart' => $isCart,
                    ];

                    if ($product->is_best_selling == 1) {
                        $bestSelling[] = $formattedProduct;
                    }

                    if ($product->is_top_rated == 1) {
                        $topRated[] = $formattedProduct;
                    }

                    return $formattedProduct;
                });
            }

            // Add directly in products object
            $products['best_selling'] = $bestSelling;
            $products['top_rated'] = $topRated;

            return response()->json([
                'status' => true,
                'message' => 'Home data fetched successfully',
                'data' => [
                    'categories' => $categories,
                    'sliders' => $sliders,
                    'banners' => $bannerData, // <-- This is now an object
                    'products' => $products,
                    'blogs' => $blogs
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function productListings()
    {
        $user = auth('sanctum')->user();
        $wishlistProductIds = [];

        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray();
            $cartProductIds = Cart::where('user_id', $user->id)->pluck('product_id')->toArray();
        } else {
            $sessionId = request()->header('session-id');
            if ($sessionId) {
                $cartProductIds = Cart::where('session_id', $sessionId)->pluck('product_id')->toArray();
            }
        }

        $products = Product::where('status', 1)
            ->latest()
            ->get()
            ->map(function ($product) use ($wishlistProductIds, $cartProductIds, $user) {
                // Thumbnail Full Path
                $product->thumbnail = $product->thumbnail
                    ? url('backend/assets/images/product/' . $product->thumbnail)
                    : null;

                // Gallery Full Path
                $product->gallery = collect($product->gallery)->map(function ($img) {
                    return url('backend/assets/images/product/' . $img);
                });

                // Gallery Order Full Path
                $product->gallery_order = collect($product->gallery_order)->map(function ($img) {
                    return url('backend/assets/images/product/' . $img);
                });

                // Discount in percent
                $discountValue = null;
                if (
                    $product->price !== null &&
                    $product->selling_price !== null &&
                    $product->price > 0 &&
                    $product->selling_price < $product->price
                ) {
                    $discountValue = round((($product->price - $product->selling_price) / $product->price) * 100);
                }

                $product->discount = $discountValue;
                $product->is_wishlist = ($user && in_array($product->id, $wishlistProductIds)) ? true : false;
                $product->is_cart = in_array($product->id, $cartProductIds);

                return $product;
            });

        return response()->json([
            'status' => true,
            'message' => 'Product listings fetched successfully',
            'data' => $products
        ]);
    }


    public function categories()
    {
        $categories = Category::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($category) {
                $category->image = url('backend/assets/images/category/' . $category->image);
                return $category;
            });

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories
        ]);
    }

    public function subcategories()
    {
        $subcategories = SubCategory::with('category')
            ->where('status', 1)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Subcategories with status 1 shown successfully',
            'data' => $subcategories
        ]);
    }

    public function sliders()
    {
        $sliders = Slider::where('status', 1)
            ->latest()
            ->get()
            ->map(function ($slider) {
                $slider->image = url('backend/assets/images/slider/' . $slider->image);
                return $slider;
            });

        return response()->json([
            'status' => true,
            'message' => 'Sliders fetched successfully',
            'data' => $sliders
        ]);
    }

    public function searchProducts(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Search Keyword
        |--------------------------------------------------------------------------
        */
        $user = auth()->user();
        $wishlistProductIds = [];

        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray();
        }

        $query = trim($request->input('query'));

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */
        if (empty($query)) {
            return response()->json([
                'status' => false,
                'message' => 'Search query is required',
                'data' => []
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Search Products Only
        |--------------------------------------------------------------------------
        */
        $products = Product::with(['category', 'subcategory', 'reviews'])
            ->where('status', 1)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhereHas('category', function ($category) use ($query) {
                        $category->where('name', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('subcategory', function ($subcategory) use ($query) {
                        $subcategory->where('name', 'like', '%' . $query . '%');
                    });
            })
            ->latest()
            ->get()
            ->map(function ($product) use ($wishlistProductIds, $user) {
                $discountValue = null;
                if (
                    $product->price !== null &&
                    $product->selling_price !== null &&
                    $product->price > 0 &&
                    $product->selling_price < $product->price
                ) {
                    $discountValue = round((($product->price - $product->selling_price) / $product->price) * 100);
                }

                return [
                    'id' => $product->id,
                    'category_id' => $product->category_id,
                    'subcategory_id' => $product->subcategory_id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'thumbnail' => $product->thumbnail
                        ? url('backend/assets/images/product/' . $product->thumbnail)
                        : null,
                    'price' => $product->price,
                    'selling_price' => $product->selling_price,
                    'details' => $product->details,
                    'description' => $product->description,
                    'specification' => $product->specification,
                    'quantity' => $product->quantity,
                    'gallery' => collect($product->gallery ?? [])
                        ->map(function ($img) {
                            return url('backend/assets/images/product/' . $img);
                        }),
                    'gallery_order' => collect($product->gallery_order ?? [])
                        ->map(function ($img) {
                            return url('backend/assets/images/product/' . $img);
                        }),
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'subcategory' => $product->subcategory ? [
                        'id' => $product->subcategory->id,
                        'name' => $product->subcategory->name,
                        'slug' => $product->subcategory->slug,
                    ] : null,
                    'average_rating' => $product->averageRating(),
                    'total_reviews' => $product->totalReviews(),
                    'discount' => $discountValue,
                    'is_wishlist' => ($user && in_array($product->id, $wishlistProductIds)) ? true : false,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Product Not Found
        |--------------------------------------------------------------------------
        */
        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'data' => []
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'data' => $products
        ]);
    }

    public function index()
    {
        $user = auth('sanctum')->user();

        $wishlistProductIds = [];
        
        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)
                ->pluck('product_id')
                ->toArray();
        }

        $products = Product::with(['category', 'subcategory', 'reviews'])
            ->where('status', 1)
            ->get()
            ->map(function ($product) use ($wishlistProductIds, $user) {
                $product->thumbnail = $product->thumbnail
                    ? url('backend/assets/images/product/' . $product->thumbnail)
                    : null;

                $product->gallery = collect($product->gallery ?? [])->map(function ($image) {
                    return url('backend/assets/images/product/' . $image);
                });

                $product->gallery_order = collect($product->gallery_order ?? [])->map(function ($image) {
                    return url('backend/assets/images/product/' . $image);
                });

                $discountValue = null;
                if (
                    $product->price !== null &&
                    $product->selling_price !== null &&
                    $product->price > 0 &&
                    $product->selling_price < $product->price
                ) {
                    $discountValue = round((($product->price - $product->selling_price) / $product->price) * 100);
                }

                return [
                    'id' => $product->id,
                    'category_id' => $product->category_id,
                    'subcategory_id' => $product->subcategory_id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'sku' => $product->sku,
                    'thumbnail' => $product->thumbnail,
                    'price' => $product->price,
                    'selling_price' => $product->selling_price,
                    'details' => $product->details,
                    'description' => $product->description,
                    'specification' => $product->specification,
                    'quantity' => $product->quantity,
                    'gallery' => $product->gallery,
                    'gallery_order' => $product->gallery_order,
                    'status' => $product->status,
                    'is_trending' => $product->is_trending,
                    'is_featured' => $product->is_featured,
                    'is_best_selling' => $product->is_best_selling,
                    'is_top_rated' => $product->is_top_rated,
                    'is_new_arrival' => $product->is_new_arrival,
                    'badge' => $product->badge,
                    'discount' => $discountValue,
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'reviews' => $product->reviews->map(
                        fn ($review) => $this->formatReviewForApi($review)
                    ),
                    'average_rating' => $product->averageRating(),
                    'total_reviews' => $product->totalReviews(),
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                    'is_wishlist' => ($user && in_array($product->id, $wishlistProductIds)) ? true : false
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Products with status 1 fetched successfully',
            'data' => $products
        ]);
    }
public function productDetails(Request $request, $slug)
{
    $user = $this->resolveOptionalApiUser($request);

    $wishlistProductIds = [];
    $cartProductIds = [];

    // ------------------------------------
    // GET PRODUCT FIRST
    // ------------------------------------
    $product = Product::with([
        'category',
        'subcategory',
        'faqs' => function ($q) {
            $q->where('status', 1)
              ->whereNotNull('answer')
              ->orderBy('order', 'asc');
        },
        'reviews.user' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }
    ])
    ->where('slug', $slug)
    ->where('status', 1)
    ->first();

    // Product Not Found
    if (!$product) {
        return response()->json([
            'status' => false,
            'message' => 'Product not found'
        ], 404);
    }

    // ------------------------------------
    // WISHLIST / CART
    // ------------------------------------
    if ($user) {
        $wishlistProductIds = Wishlist::where('user_id', $user->id)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
        $cartProductIds = Cart::where('user_id', $user->id)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    } else {
        $sessionId = $request->header('session-id');
        if ($sessionId) {
            $cartProductIds = Cart::where('session_id', $sessionId)
                ->pluck('product_id')
                ->toArray();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Thumbnail
    |--------------------------------------------------------------------------
    */
    $product->thumbnail = $product->thumbnail
        ? url('backend/assets/images/product/' . $product->thumbnail)
        : null;

    $product->video_url = $product->video
        ? asset('backend/assets/videos/product/' . $product->video)
        : null;

    /*
    |--------------------------------------------------------------------------
    | Gallery Images
    |--------------------------------------------------------------------------
    */
    $product->gallery = collect($product->gallery ?? [])
        ->map(function ($img) {
            return url('backend/assets/images/product/' . $img);
        })
        ->values()
        ->toArray();

    unset($product->gallery_order);

    /*
    |--------------------------------------------------------------------------
    | Recently Bought Count
    |--------------------------------------------------------------------------
    */
    $boughtCount = OrderItem::where('product_id', $product->id)->sum('quantity');
    $product->recently_bought = [
        'count' => $boughtCount,
        'text' => $boughtCount > 0
            ? $boughtCount . '+ people bought this recently'
            : 'Recently added product'
    ];

    /*
    |--------------------------------------------------------------------------
    | Clean Details & Description
    |--------------------------------------------------------------------------
    */
    $rawDetails = html_entity_decode($product->details ?? '');
    $product->details = trim(strip_tags($rawDetails));
    $product->description = trim(html_entity_decode(strip_tags($product->description ?? '')));

    /*
    |--------------------------------------------------------------------------
    | Key Features
    |--------------------------------------------------------------------------
    */
    $keyFeatures = [];
    preg_match_all('/<li[^>]*>(.*?)<\/li>/s', $rawDetails, $matches);

    if (!empty($matches[1])) {
        foreach ($matches[1] as $feature) {
            $feature = trim(html_entity_decode(strip_tags($feature)));
            if (!empty($feature)) {
                $keyFeatures[] = $feature;
            }
        }
    }

    if (empty($keyFeatures)) {
        $lines = preg_split('/\r\n|\r|\n/', strip_tags($rawDetails));
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $keyFeatures[] = $line;
            }
        }
    }

    $product->key_features = $keyFeatures;

    /*
    |--------------------------------------------------------------------------
    | Specification
    |--------------------------------------------------------------------------
    */
    $specifications = [];
    if (!empty($product->specification)) {
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $product->specification, $rows);

        foreach ($rows[1] as $row) {
            preg_match_all('/<t[dh][^>]*>(.*?)<\/t[dh]>/is', $row, $cols);
            $cells = array_map(fn ($cell) => trim(strip_tags($cell)), $cols[1] ?? []);

            if (count($cells) >= 3) {
                $first = $cells[0];
                if (preg_match('/^\d{1,3}$/', $first) || in_array(mb_strtolower(rtrim($first, '.')), ['row no', 'row number', 's.no', 'sr no', 'no', '#'], true)) {
                    $key = $cells[1];
                    $value = $cells[2];
                } else {
                    $key = $cells[1];
                    $value = $cells[2];
                }
            } elseif (isset($cells[0], $cells[1])) {
                $key = $cells[0];
                $value = $cells[1];
            } else {
                continue;
            }

            $normalizedKey = mb_strtolower(rtrim(trim($key), '.'));
            if ($key === '' || in_array($normalizedKey, ['specification', 'specifications', 'details', 'detail', 'row no', 'row number'], true)) {
                continue;
            }

            $specifications[] = [
                'key' => $key,
                'value' => $value,
            ];
        }
    }

    $product->specification = $specifications;

    /*
    |--------------------------------------------------------------------------
    | Stock Status
    |--------------------------------------------------------------------------
    */
    $product->in_stock = $product->quantity > 0;
    $product->stock_status = $product->quantity > 0 ? 'In Stock' : 'Out Of Stock';

    /*
    |--------------------------------------------------------------------------
    | Reviews & Ratings
    |--------------------------------------------------------------------------
    */
    $reviews = collect($product->reviews)->where('status', 1);
    $totalReviews = $reviews->count();
    $averageRating = round($reviews->avg('rating') ?? 0, 1);

    $fullStars = floor($averageRating);
    $emptyStars = 5 - $fullStars;
    $stars = str_repeat('⭐', $fullStars) . str_repeat('☆', $emptyStars);

    $ratingBreakdown = [];
    for ($i = 5; $i >= 1; $i--) {
        $count = $reviews->where('rating', $i)->count();
        $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;

        $ratingBreakdown[] = [
            'star' => $i,
            'count' => $count,
            'percentage' => $percentage
        ];
    }

    $product->rating_summary = [
        'average_rating' => $averageRating,
        'total_reviews' => $totalReviews,
        'rating_breakdown' => $ratingBreakdown,
        'stars' => $stars,
        'display_text' => $stars . ' ' . $averageRating . '(' . $totalReviews . ' reviews)'
    ];

    /*
    |--------------------------------------------------------------------------
    | Review List
    |--------------------------------------------------------------------------
    */
/*
|--------------------------------------------------------------------------
| Purchase & Review Status
|--------------------------------------------------------------------------
*/
$hasPurchased = false;
$hasReviewed = false;

if ($user) {

    $hasPurchased = OrderItem::where('product_id', $product->id)
        ->whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->exists();

    $hasReviewed = Review::where('product_id', $product->id)
        ->where('user_id', $user->id)
        ->exists();
}

$product->has_purchased = $hasPurchased;
$product->has_reviewed = $hasReviewed;
$product->can_review = $hasPurchased && !$hasReviewed;

/*
|--------------------------------------------------------------------------
| Product Review Media (All Users Images & Videos)
|--------------------------------------------------------------------------
*/
$product->product_review_media = Review::with('user')
    ->where('product_id', $product->id)
    ->where('status', 1)
    ->get()
    ->flatMap(function ($review) {

        return collect($review->attachment_urls ?? [])
            ->map(function ($attachment) use ($review) {

                return [
                    'review_id'      => $review->id,
                    'user_id'        => $review->user_id,
                    'user_name'      => $review->user->name ?? null,
                    'rating'         => $review->rating,
                    'review'         => $review->review,
                    'created_at'     => $review->created_at?->format('d M Y'),
                    'media_type'     => $attachment['type'] ?? 'image',
                    'media_url'      => $attachment['url'] ?? null,
                ];
            });

    })
    ->filter(fn ($item) => !empty($item['media_url']))
    ->values()
    ->toArray();
    /*
    |--------------------------------------------------------------------------
    | FAQ - Filter only approved FAQs with answers
    |--------------------------------------------------------------------------
    */
    $product->faqs = collect($product->faqs ?? [])
        ->filter(function ($faq) {
            return !empty($faq->answer);
        })
        ->map(function ($faq) {
            return [
                'question' => $faq->question,
                'answer' => strip_tags($faq->answer),
            ];
        })
        ->values()
        ->toArray();

    /*
    |--------------------------------------------------------------------------
    | Similar Products
    |--------------------------------------------------------------------------
    */
    $similarProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('status', 1)
        ->latest()
        ->take(10)
        ->get();

    $product->similar_products = $similarProducts->map(function ($item) use ($wishlistProductIds, $cartProductIds, $user) {

        $avgRating = Review::where('product_id', $item->id)
            ->where('status', 1)
            ->avg('rating');

        $reviewCount = Review::where('product_id', $item->id)
            ->where('status', 1)
            ->count();

        $galleryImages = [];
        if (!empty($item->gallery)) {
            $gallery = is_string($item->gallery)
                ? json_decode($item->gallery, true)
                : $item->gallery;

            if (is_array($gallery)) {
                $galleryImages = collect($gallery)
                    ->map(fn($image) => url('backend/assets/images/product/' . $image))
                    ->values()
                    ->toArray();
            }
        }

        // ✅ Define isWishlist before using it
        $isWishlist = ($user && in_array($item->id, $wishlistProductIds));
        $isCart = in_array($item->id, $cartProductIds);

        return [
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'thumbnail' => $item->thumbnail
                ? url('backend/assets/images/product/' . $item->thumbnail)
                : null,
            'gallery' => $galleryImages,
            'price' => (float) $item->price,
            'selling_price' => (float) $item->selling_price,
            'rating' => round($avgRating ?? 0, 1),
            'review_count' => $reviewCount,
            'quantity' => $item->quantity,
            'in_stock' => $item->quantity > 0,
            'stock_status' => $item->quantity > 0 ? 'In Stock' : 'Out Of Stock',
            'is_wishlist' => $isWishlist,
            'is_cart' => $isCart,
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | MAIN PRODUCT WISHLIST / CART STATUS
    |--------------------------------------------------------------------------
    */
    $product->is_wishlist = ($user && in_array((int) $product->id, $wishlistProductIds, true));
    $product->is_cart = in_array((int) $product->id, $cartProductIds, true);

    /*
    |--------------------------------------------------------------------------
    | Format Reviews
    |--------------------------------------------------------------------------
    */
    $approvedReviews = collect($product->reviews)
        ->where('status', 1)
        ->sortByDesc('created_at')
        ->values();
    
    $userReactions = $this->getUserReviewReactions($user, $approvedReviews->pluck('id')->all());

    $product->reviews = $approvedReviews
        ->map(fn ($review) => $this->formatReviewForApi($review, $userReactions[$review->id] ?? null))
        ->values();

    return response()->json([
        'status' => true,
        'message' => 'Product fetched successfully',
        'data' => $product
    ]);
}

    public function productPriceRange($category_slug, $subcategory_slug = null)
    {
        $category = Category::where('slug', $category_slug)->where('status', 1)->first();

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
                'data' => null,
            ], 404);
        }

        $subcategory = null;

        if ($subcategory_slug) {
            $subcategory = SubCategory::where('slug', $subcategory_slug)
                ->where('category_id', $category->id)
                ->where('status', 1)
                ->first();

            if (!$subcategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subcategory not found for this category',
                    'data' => null,
                ], 404);
            }
        }

        $productsQuery = Product::where('status', 1)
            ->inCategory($category->id);

        if ($subcategory) {
            $productsQuery->where('subcategory_id', $subcategory->id);
        }

        $prices = $this->collectProductPrices($productsQuery);

        return response()->json([
            'status' => true,
            'message' => 'Price range fetched successfully',
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'subcategory' => $subcategory ? [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'slug' => $subcategory->slug,
                ] : null,
                'min_price' => !empty($prices) ? (float) min($prices) : 0,
                'max_price' => !empty($prices) ? (float) max($prices) : 0,
                'product_count' => $productsQuery->count(),
            ],
        ]);
    }

    private function collectProductPrices($query): array
    {
        $prices = [];
     
        $query->with(['variants' => function ($q) {
            $q->where('status', 1);
        }])->get()->each(function ($product) use (&$prices) {
            if ($product->product_type === 'variable' && $product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    $price = $this->getEffectivePrice($variant->selling_price, $variant->price);
                    if ($price !== null) {
                        $prices[] = $price;
                    }
                }
            } else {
                $price = $this->getEffectivePrice($product->selling_price, $product->price);
                if ($price !== null) {
                    $prices[] = $price;
                }
            }
        });
     
        return $prices;
    }

    private function getEffectivePrice($sellingPrice, $price): ?float
    {
        if ($sellingPrice !== null && $sellingPrice > 0) {
            return (float) $sellingPrice;
        }
     
        if ($price !== null && $price > 0) {
            return (float) $price;
        }
     
        return null;
    }
     
    private function getPriceRange(array $prices): array
    {
        return [
            'min_price' => !empty($prices) ? (float) min($prices) : 0,
            'max_price' => !empty($prices) ? (float) max($prices) : 0,
        ];
    }

    public function categorywiseProduct(Request $request, $category_slug)
    {
        $category = Category::where('slug', $category_slug)
            ->where('status', 1)
            ->firstOrFail();
    
        // Price Range Query
        $priceRangeQuery = Product::where('status', 1)
            ->inCategory($category->id);
    
        $allPrices = $this->collectProductPrices(clone $priceRangeQuery);
    
        $originalPriceRange = [
            'min_price' => !empty($allPrices) ? min($allPrices) : 0,
            'max_price' => !empty($allPrices) ? max($allPrices) : 0,
        ];
    
        // Product Query
        $query = Product::with(['category', 'subcategory', 'reviews', 'categories'])
            ->where('status', 1)
            ->inCategory($category->id);
    
        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }
    
        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
        }
    
        $products = $query->get();
    
        // Image URLs
        $products = $products->map(function ($product) {
    
            // Thumbnail
            $product->thumbnail = $product->thumbnail
                ? url('backend/assets/images/product/' . $product->thumbnail)
                : null;
    
            // Gallery
            $gallery = is_array($product->gallery)
                ? $product->gallery
                : json_decode($product->gallery, true);
    
            $product->gallery = collect($gallery ?? [])
                ->map(function ($img) {
                    return url('backend/assets/images/product/' . $img);
                })
                ->values();
    
            // Gallery Order
            $galleryOrder = is_array($product->gallery_order)
                ? $product->gallery_order
                : json_decode($product->gallery_order, true);
    
            $product->gallery_order = collect($galleryOrder ?? [])
                ->map(function ($img) {
                    return url('backend/assets/images/product/' . $img);
                })
                ->values();
    
            return $product;
        });
    
        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'filters_applied' => [
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
            ],
            'price_range' => $originalPriceRange,
            'data' => $products,
        ]);
    }

    public function subcategorywiseProduct(Request $request, $category_slug, $subcategory_slug)
    {
        $user = auth()->user();
        $wishlistProductIds = [];
     
        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)
                ->pluck('product_id')
                ->toArray();
        }
     
        // Find category
        $category = Category::where('slug', $category_slug)->first();
     
        if (!$category) {
            return response()->json([
                'status' => true,
                'message' => 'Category not found',
                'min_price' => 0,
                'max_price' => 0,
                'data' => []
            ]);
        }
     
        // Find subcategory
        $subcategory = SubCategory::where('slug', $subcategory_slug)
            ->where('category_id', $category->id)
            ->first();
     
        if (!$subcategory) {
            return response()->json([
                'status' => true,
                'message' => 'Subcategory not found for this category',
                'min_price' => 0,
                'max_price' => 0,
                'data' => []
            ]);
        }
     
        // 🔥 Build product query with price filters
        $productQuery = Product::with(['category', 'subcategory', 'reviews', 'categories'])
            ->where('status', 1)
            ->inCategory($category->id)
            ->where('subcategory_id', $subcategory->id);
     
        // ✅ Apply price range filters from query parameters
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $productQuery->where(function($q) use ($request) {
                $q->where('selling_price', '>=', $request->min_price)
                  ->orWhere('price', '>=', $request->min_price);
            });
        }
     
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $productQuery->where(function($q) use ($request) {
                $q->where('selling_price', '<=', $request->max_price)
                  ->orWhere('price', '<=', $request->max_price);
            });
        }
     
        // Get filtered products
        $products = $productQuery->latest()->get();
     
        // Get price range for the filtered products
        $priceRange = $this->getPriceRange(
            $this->collectProductPrices(
                clone $productQuery
            )
        );
     
        if ($products->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No products found in this price range',
                'min_price' => $priceRange['min_price'],
                'max_price' => $priceRange['max_price'],
                'data' => []
            ]);
        }
     
        $products = $products->map(function ($product) use ($wishlistProductIds, $user) {
            // Thumbnail
            $product->thumbnail = $product->thumbnail
                ? url('backend/assets/images/product/' . $product->thumbnail)
                : null;
     
            // Gallery
            $product->gallery = collect($product->gallery ?? [])
                ->map(function ($img) {
                    return url('backend/assets/images/product/' . $img);
                })
                ->values();
     
            // Gallery Order
            $product->gallery_order = collect($product->gallery_order ?? [])
                ->map(function ($img) {
                    return url('backend/assets/images/product/' . $img);
                })
                ->values();
     
            // Discount %
            $discountValue = null;
            if (
                $product->price !== null &&
                $product->selling_price !== null &&
                $product->price > 0 &&
                $product->selling_price < $product->price
            ) {
                $discountValue = round(
                    (($product->price - $product->selling_price) / $product->price) * 100
                );
            }
     
            $product->discount = $discountValue;
            $product->is_wishlist = $user
                ? in_array($product->id, $wishlistProductIds)
                : false;
     
            return $product;
        });
     
        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'filters_applied' => [
                'min_price' => $request->min_price ?? null,
                'max_price' => $request->max_price ?? null,
            ],
            'price_range' => $priceRange,
            'data' => $products
        ]);
    }

    public function allCategorySubcategories()
    {
        $user = auth()->user();
        $wishlistProductIds = [];

        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | All Categories With Subcategories
        |--------------------------------------------------------------------------
        */
        $categories = Category::where('status', 1)
            ->with([
                'subcategories' => function ($query) {
                    $query->where('status', 1)->latest();
                }
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Only Latest 2 Products With Reviews
        |--------------------------------------------------------------------------
        */
        $products = Product::with(['category', 'subcategory', 'reviews'])
            ->where('status', 1)
            ->latest()
            ->take(2)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Category Data
        |--------------------------------------------------------------------------
        */
        $allCategory = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => $category->image
                    ? url('backend/assets/images/category/' . $category->image)
                    : null,
                'subcategories' => $category->subcategories->map(function ($subcategory) use ($category) {
                    return [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'slug' => $subcategory->slug,
                        'url' => url($category->slug . '/' . $subcategory->slug),
                    ];
                }),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Product Data
        |--------------------------------------------------------------------------
        */
        $productData = $products->map(function ($product) use ($wishlistProductIds, $user) {
            $approvedReviews = $product->reviews->where('status', 1);
            $discountValue = null;

            if (
                $product->price !== null &&
                $product->selling_price !== null &&
                $product->price > 0 &&
                $product->selling_price < $product->price
            ) {
                $discountValue = round((($product->price - $product->selling_price) / $product->price) * 100);
            }

            return [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'subcategory_id' => $product->subcategory_id,
                'category_name' => optional($product->category)->name,
                'subcategory_name' => optional($product->subcategory)->name,
                'name' => $product->name,
                'slug' => $product->slug,
                'thumbnail' => $product->thumbnail
                    ? url('backend/assets/images/product/' . $product->thumbnail)
                    : null,
                'price' => $product->price,
                'selling_price' => $product->selling_price,
                'discount' => $discountValue,
                'badge' => $product->badge,
                'average_rating' => round($approvedReviews->avg('rating') ?? 0, 1),
                'total_reviews' => $approvedReviews->count(),
                'product_url' => url(
                    optional($product->category)->slug . '/' .
                        optional($product->subcategory)->slug . '/' .
                        $product->slug
                ),
                'is_wishlist' => ($user && in_array($product->id, $wishlistProductIds)) ? true : false
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Final Response
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'status' => true,
            'message' => 'All categories and latest 2 products fetched successfully',
            'data' => [
                'AllCategory' => $allCategory,
                'Product' => $productData,
            ]
        ]);
    }

    private function productResponse($product)
    {
        $user = auth()->user();
        $wishlistProductIds = [];

        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray();
        }

        // Calculate discount as percent
        $discountValue = null;
        if (
            $product->price !== null &&
            $product->selling_price !== null &&
            $product->price > 0 &&
            $product->selling_price < $product->price
        ) {
            $discountValue = round((($product->price - $product->selling_price) / $product->price) * 100);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product fetched successfully',
            'data' => [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'subcategory_id' => $product->subcategory_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'thumbnail' => $product->thumbnail,
                'price' => $product->price,
                'selling_price' => $product->selling_price,
                'details' => $product->details,
                'description' => $product->description,
                'specification' => $product->specification,
                'quantity' => $product->quantity,
                'gallery' => $product->gallery,
                'gallery_order' => $product->gallery_order,
                'status' => $product->status,
                'is_trending' => $product->is_trending,
                'is_featured' => $product->is_featured,
                'is_best_selling' => $product->is_best_selling,
                'is_top_rated' => $product->is_top_rated,
                'is_new_arrival' => $product->is_new_arrival,
                'badge' => $product->badge,
                'discount' => $discountValue,
                'category' => $product->category,
                'subcategory' => $product->subcategory,
                'reviews' => $product->reviews->map(
                    fn ($review) => $this->formatReviewForApi($review)
                ),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                'is_wishlist' => ($user && in_array($product->id, $wishlistProductIds)) ? true : false,
            ],
            'average_rating' => $product->averageRating(),
            'total_reviews' => $product->totalReviews()
        ]);
    }

    public function bestSelling()
    {
        $user = auth()->user();
        $wishlistProductIds = [];

        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray();
        }

        $products = Product::where('status', 1)
            ->where('is_best_selling', 1)
            ->latest()
            ->get()
            ->map(function ($product) use ($wishlistProductIds, $user) {
                $discountValue = null;
                if (
                    $product->price !== null &&
                    $product->selling_price !== null &&
                    $product->price > 0 &&
                    $product->selling_price < $product->price
                ) {
                    $discountValue = round((($product->price - $product->selling_price) / $product->price) * 100);
                }

                $product->discount = $discountValue;
                $product->is_wishlist = ($user && in_array($product->id, $wishlistProductIds)) ? true : false;

                return $product;
            });

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }

    public function topRated()
    {
        $user = auth()->user();
        $wishlistProductIds = [];

        if ($user) {
            $wishlistProductIds = Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray();
        }

        $products = Product::where('status', '=', 1)
            ->where('is_top_rated', '=', 1)
            ->latest()
            ->get()
            ->map(function ($product) use ($wishlistProductIds, $user) {
                $discountValue = null;
                if (
                    $product->price !== null &&
                    $product->selling_price !== null &&
                    $product->price > 0 &&
                    $product->selling_price < $product->price
                ) {
                    $discountValue = round((($product->price - $product->selling_price) / $product->price) * 100);
                }

                $product->discount = $discountValue;
                $product->is_wishlist = ($user && in_array($product->id, $wishlistProductIds)) ? true : false;

                return $product;
            });

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }

    public function blogCategories()
    {
        $categories = BlogCategory::where('status', 1)
            ->latest()
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Blog categories fetched successfully',
            'data' => $categories
        ]);
    }

    public function blogs(Request $request)
    {
        $query = Blog::where('status', 1);
    
        if ($request->filled('slug') && $request->slug !== 'all') {
            $category = BlogCategory::where('slug', $request->slug)->first();
    
            if ($category) {
                $query->where('category_id', $category->id);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Category Not Found',
                    'data' => [
                        'blogs' => [],
                        'trending' => []
                    ]
                ]);
            }
        }
    
        $blogs = $query->with('category')
            ->latest()
            ->get()
            ->map(function ($blog) {
                $blog->image = url('backend/assets/images/blog/' . $blog->image);
                $blog->description = strip_tags($blog->description);
                $blog->short_description = strip_tags($blog->short_description);
                return $blog;
            });
    
        $trending = Blog::where('status', 1)
            ->where('trending', 1)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($blog) {
                return [
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'image' => url('backend/assets/images/blog/' . $blog->image),
                    'short_description' => strip_tags($blog->short_description),
                    'created_at' => $blog->created_at->format('d M Y'),
                ];
            });
    
        return response()->json([
            'success' => true,
            'message' => ($request->filled('slug') && $request->slug !== 'all')
                ? 'Category wise blogs fetched successfully'
                : 'All blogs fetched successfully',
            'data' => [
                'blogs' => $blogs,
                'trending' => $trending
            ]
        ]);
    }

    public function blogDetails($slug)
    {
        $slug = trim($slug);

        $blog = Blog::with('category')
            ->where('slug', $slug)
            ->where('status', 1)
            ->first();

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $blogImage = $blog->image
            ? url('backend/assets/images/blog/' . $blog->image)
            : null;

        // Trending Blogs
        $trending = Blog::where('status', 1)
            ->where('trending', 1)
            ->where('id', '!=', $blog->id) // Exclude current blog
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'image' => $item->image
                        ? url('backend/assets/images/blog/' . $item->image)
                        : null,
                    'short_description' => strip_tags($item->short_description),
                    'created_at' => $item->created_at->format('d M Y'),
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Blog fetched successfully',
            'data' => [
                'blog' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'category' => $blog->category,
                    'image' => $blogImage,
                    'author' => $blog->author,
                    'blog_date' => $blog->blog_date,
                    'short_description' => strip_tags($blog->short_description),
                    'description' => $blog->description,
                    'tags' => !empty($blog->tags)
                        ? array_map('trim', explode(',', $blog->tags))
                        : [],
                    'status' => $blog->status,
                    'created_at' => $blog->created_at,
                ],
                'trending' => $trending
            ]
        ]);
    }   

    public function relatedBlogs($slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        $related = Blog::where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->where('status', 1)
            ->latest()
            ->limit(10)
            ->with('category') // Make sure to eager load category relation
            ->get()
            ->map(function ($relatedBlog) {
                return [
                    'id' => $relatedBlog->id,
                    'title' => $relatedBlog->title,
                    'slug' => $relatedBlog->slug,
                    'name' => $relatedBlog->title, // Added 'name' field as duplicate of title
                    'category_id' => $relatedBlog->category_id,
                    'category_name' => optional($relatedBlog->category)->name, // Show category name
                    'image' => $relatedBlog->image ? url('backend/assets/images/blog/' . $relatedBlog->image) : null,
                    'author' => $relatedBlog->author,
                    'blog_date' => $relatedBlog->blog_date,
                    'short_description' => strip_tags($relatedBlog->short_description),
                    'description' => strip_tags($relatedBlog->description),
                    'status' => $relatedBlog->status,
                    'created_at' => $relatedBlog->created_at,
                    'updated_at' => $relatedBlog->updated_at
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $related
        ]);
    }

    public function mergeCart(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $user = auth()->user();
        $guestCartItems = Cart::where('session_id', $request->session_id)->get();

        foreach ($guestCartItems as $item) {
            $existingCart = Cart::where('user_id', $user->id)
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existingCart) {
                $existingCart->quantity += $item->quantity;
                $existingCart->save();
                $item->delete();
            } else {
                $item->update([
                    'user_id' => $user->id,
                    'session_id' => null
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart merged successfully'
        ]);
    }

    public function addToCart(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'variant_id' => 'nullable|integer|exists:product_variants,id',
        'quantity'   => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    // 🔥 Check stock for simple or variant product
    $stockCheck = $this->checkProductStock(
        $request->product_id,
        $request->quantity,
        $request->variant_id
    );

    if (!$stockCheck['available']) {
        return response()->json([
            'status' => false,
            'message' => $stockCheck['message'],
            'available_stock' => $stockCheck['available_stock'],
            'product_type' => $stockCheck['product_type']
        ], 400);
    }

    // Get product
    $product = Product::find($request->product_id);
    
    // 🔐 Check login user
    $user = auth('sanctum')->user();

    // 🟢 Guest session support
    $sessionId = $request->header('session-id');

    if (!$user && !$sessionId) {
        $sessionId = uniqid('guest_');
    }

    // 🔍 Find existing cart item (user OR guest)
    $cartQuery = Cart::where('product_id', $request->product_id)
        ->where('variant_id', $request->variant_id);

    if ($user) {
        $cartQuery->where('user_id', $user->id);
    } else {
        $cartQuery->where('session_id', $sessionId);
    }

    $cart = $cartQuery->first();

    // ✅ Check if total quantity (existing + new) exceeds available stock
    $currentQuantity = $cart ? (int) $cart->quantity : 0;
    $newTotalQuantity = $currentQuantity + $request->quantity;
    
    $availableStock = $this->getProductAvailableStock(
        $request->product_id,
        $request->variant_id
    );
    
    if ($newTotalQuantity > $availableStock) {
        return response()->json([
            'status' => false,
            'message' => 'Cannot add more items. Available stock: ' . $availableStock . ', Your cart has: ' . $currentQuantity,
            'available_stock' => $availableStock,
            'current_cart_quantity' => $currentQuantity
        ], 400);
    }

    if ($cart) {
        // ➕ increase quantity
        $cart->quantity += $request->quantity;
        $cart->save();
    } else {
        // 🆕 create new cart item
        $cart = Cart::create([
            'user_id'    => $user->id ?? null,
            'session_id' => $user ? null : $sessionId,
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
            'quantity'   => $request->quantity,
        ]);
    }

    // Load product details for response
    $cart->load('product', 'variant');

    return response()->json([
        'status' => true,
        'message' => 'Product added to cart successfully',
        'data' => [
            'cart_id' => $cart->id,
            'product_id' => $cart->product_id,
            'variant_id' => $cart->variant_id,
            'quantity' => $cart->quantity,
            'product_name' => $cart->product->name ?? null,
            'variant_name' => $cart->variant ? $cart->variant->sku : null,
            'available_stock' => $availableStock,
            'in_stock' => $availableStock > 0
        ],
        'session_id' => $sessionId
    ]);
}

    public function getCart(Request $request)
    {
        try {
    
            $user = auth('sanctum')->user();
            $sessionId = $request->header('session-id');
    
            $query = Cart::with('product');
    
            if ($user) {
                $query->where('user_id', $user->id);
            } elseif ($sessionId) {
                $query->where('session_id', $sessionId);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Session ID is required for guest users'
                ], 422);
            }
    
            /*
            |--------------------------------------------------------------------------
            | 🔥 FORCE FRESH DATA FROM DB (IMPORTANT FIX)
            |--------------------------------------------------------------------------
            */
            $cartItems = $query->get()->fresh(); // ✅ FIX FOR UPDATED QUANTITY
    
            /*
            |--------------------------------------------------------------------------
            | ADDRESS
            |--------------------------------------------------------------------------
            */
            $selectedAddress = null;
    
            $addressId = $cartItems->first()?->address_id;
    
            if ($addressId) {
    
                $selectedAddress = Address::where('id', $addressId)
                    ->when($user, function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->first();
    
            } elseif ($user) {
    
                $selectedAddress = Address::where('user_id', $user->id)
                ->latest()
                ->first();
            }
    
            $allSimilarProducts = collect();
            
            $cartItems = $cartItems->map(function ($item) use (&$allSimilarProducts) {
            
                $item->quantity = (int) $item->quantity;
            
                $price = 0;
            
                if ($item->product) {
            
                    $price = $item->product->selling_price
                        ?? $item->product->price
                        ?? 0;
            
                        if ($item->product->thumbnail) {
                            $item->product->thumbnail = url(
                                'backend/assets/images/product/' . $item->product->thumbnail
                            );
                        }
                        
                        /*
                        |--------------------------------------------------------------------------
                        | Product Gallery
                        |--------------------------------------------------------------------------
                        */
                        $galleryImages = [];
                        
                        if (!empty($item->product->gallery)) {
                        
                            $gallery = is_string($item->product->gallery)
                                ? json_decode($item->product->gallery, true)
                                : $item->product->gallery;
                        
                            if (is_array($gallery)) {
                                $galleryImages = collect($gallery)
                                    ->map(function ($image) {
                                        return url('backend/assets/images/product/' . $image);
                                    })
                                    ->values()
                                    ->toArray();
                            }
                        }
                        
                        $item->product->gallery = $galleryImages;
            
                    $similarProducts = Product::where('category_id', $item->product->category_id)
                        ->where('id', '!=', $item->product->id)
                        ->where('status', 1)
                        ->latest()
                        ->take(5)
                        ->get()
                        ->map(function ($p) {

                            $galleryImages = [];
                        
                            if (!empty($p->gallery)) {
                        
                                $gallery = is_string($p->gallery)
                                    ? json_decode($p->gallery, true)
                                    : $p->gallery;
                        
                                if (is_array($gallery)) {
                                    $galleryImages = collect($gallery)
                                        ->map(function ($image) {
                                            return url('backend/assets/images/product/' . $image);
                                        })
                                        ->values()
                                        ->toArray();
                                }
                            }
                        
                            return [
                                'id' => $p->id,
                                'category_id' => $p->category_id,
                                'subcategory_id' => $p->subcategory_id,
                                'name' => $p->name,
                                'slug' => $p->slug,
                            
                                'thumbnail' => $p->thumbnail
                                    ? url('backend/assets/images/product/' . $p->thumbnail)
                                    : null,
                            
                                'gallery' => $galleryImages,
                            
                                'price' => (float) $p->price,
                                'selling_price' => (float) $p->selling_price,
                            
                                'is_trending' => (bool) $p->is_trending,
                                'is_featured' => (bool) $p->is_featured,
                                'is_best_selling' => (bool) $p->is_best_selling,
                                'is_top_rated' => (bool) $p->is_top_rated,
                                'is_new_arrival' => (bool) $p->is_new_arrival,
                            
                                'quantity' => $p->quantity,
                                'in_stock' => $p->quantity > 0,
                                'stock_status' => $p->quantity > 0
                                    ? 'In Stock'
                                    : 'Out Of Stock',
                            ];
                        });
            
                    $allSimilarProducts = $allSimilarProducts->merge($similarProducts);
                }
            
                $item->line_total = $price * $item->quantity;
            
                return $item;
            });
            
            /*
            |--------------------------------------------------------------------------
            | SHOW TRENDING PRODUCTS IF CART IS EMPTY
            |--------------------------------------------------------------------------
            */
            if ($cartItems->isEmpty()) {

                $allSimilarProducts = Product::where('status', 1)
                    ->latest()
                    ->take(10)
                    ->get()
                    ->map(function ($p) {

                        $galleryImages = [];

                        if (!empty($p->gallery)) {

                            $gallery = is_string($p->gallery)
                                ? json_decode($p->gallery, true)
                                : $p->gallery;

                            if (is_array($gallery)) {
                                $galleryImages = collect($gallery)
                                    ->map(function ($image) {
                                        return url('backend/assets/images/product/' . $image);
                                    })
                                    ->values()
                                    ->toArray();
                            }
                        }

                        return [
                            'id' => $p->id,
                            'category_id' => $p->category_id,
                            'subcategory_id' => $p->subcategory_id,
                            'name' => $p->name,
                            'slug' => $p->slug,
                        
                            'thumbnail' => $p->thumbnail
                                ? url('backend/assets/images/product/' . $p->thumbnail)
                                : null,
                        
                            'gallery' => $galleryImages,
                        
                            'price' => (float) $p->price,
                            'selling_price' => (float) $p->selling_price,
                        
                            'is_trending' => (bool) $p->is_trending,
                            'is_featured' => (bool) $p->is_featured,
                            'is_best_selling' => (bool) $p->is_best_selling,
                            'is_top_rated' => (bool) $p->is_top_rated,
                            'is_new_arrival' => (bool) $p->is_new_arrival,
                        
                            'quantity' => $p->quantity,
                            'in_stock' => $p->quantity > 0,
                            'stock_status' => $p->quantity > 0
                                ? 'In Stock'
                                : 'Out Of Stock',
                        ];
                    });
            }
            /*
            |--------------------------------------------------------------------------
            | SUBTOTAL
            |--------------------------------------------------------------------------
            */
            $subtotal = $cartItems->sum('line_total');
    
            /*
            |--------------------------------------------------------------------------
            | SHIPPING
            |--------------------------------------------------------------------------
            */
            $shippingCharge = $this->calculateShippingCharge($subtotal);
    
            /*
            |--------------------------------------------------------------------------
            | COUPON
            |--------------------------------------------------------------------------
            */
            $couponCode = $cartItems->first()?->coupon_code;
    
            $discountAmount = 0;
            $couponData = null;
    
            if ($couponCode) {
    
                $coupon = Coupon::where('code', $couponCode)
                    ->where('status', 1)
                    ->first();
    
                if ($coupon) {
    
                    $today = now()->toDateString();
    
                    $isValid =
                        (!$coupon->start_date || $today >= $coupon->start_date) &&
                        (!$coupon->end_date || $today <= $coupon->end_date) &&
                        (empty($coupon->minimum_amount) || $subtotal >= $coupon->minimum_amount);
    
                    if ($isValid) {
    
                        if ($coupon->type === 'percent') {
                            $discountAmount = ($subtotal * $coupon->value) / 100;
    
                            if (!empty($coupon->maximum_discount)) {
                                $discountAmount = min($discountAmount, $coupon->maximum_discount);
                            }
    
                        } elseif ($coupon->type === 'fixed') {
                            $discountAmount = $coupon->value;
                        }
    
                        $couponData = [
                            'id' => $coupon->id,
                            'code' => $coupon->code,
                            'type' => $coupon->type,
                            'value' => $coupon->value,
                            'discount_amount' => round($discountAmount, 2),
                        ];
                    }
                }
            }
    
            /*
            |--------------------------------------------------------------------------
            | GRAND TOTAL
            |--------------------------------------------------------------------------
            */
        $taxable = $subtotal - $discountAmount;

        $gstPercentage = 0;
        $gstAmount = round(($subtotal * $gstPercentage) / 100, 2);

        $grandTotal = round(
            $taxable + $gstAmount + $shippingCharge,
            2
        );    
                    return response()->json([
                        'status' => true,
                        'message' => 'Cart fetched successfully',
            
                        'selected_address' => $selectedAddress,
                        'coupon' => $couponData,
                        
                        'data' => $cartItems,
                        'trending_products' => $allSimilarProducts
                        ->unique('id')
                        ->values(),
            
                    'cart_summary' => [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'amount_after_discount' => round($taxable, 2),
            'gst_percentage' => $gstPercentage,
            'gst_amount' => round($gstAmount, 2),
            'shipping_charge' => $shippingCharge,
            'grand_total' => round($grandTotal, 2),
        ]
                    ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

public function updateCart(Request $request)
{
    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    $cartQuery = Cart::query();

    if ($user) {
        $cartQuery->where('user_id', $user->id);
    } elseif ($sessionId) {
        $cartQuery->where('session_id', $sessionId);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'User or Session not found'
        ], 422);
    }

    // 1. Apply Coupon
    if ($request->filled('coupon_code')) {
        $cartQuery->update([
            'coupon_code' => $request->coupon_code
        ]);
    }

    // 2. Update Address
    if ($request->filled('address_id')) {
        $cartQuery->update([
            'address_id' => $request->address_id
        ]);
    }

    // 3. Update Quantity with stock validation
    if ($request->filled('cart_id') && $request->filled('quantity')) {

        $cart = Cart::where('id', $request->cart_id)
            ->where(function ($q) use ($user, $sessionId) {
                if ($user) {
                    $q->where('user_id', $user->id);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        // 🔥 Check stock before updating quantity
        $availableStock = $this->getProductAvailableStock(
            $cart->product_id,
            $cart->variant_id
        );

        if ($request->quantity > $availableStock) {
            return response()->json([
                'status' => false,
                'message' => 'Not enough stock available. Available: ' . $availableStock,
                'available_stock' => $availableStock,
                'requested_quantity' => $request->quantity
            ], 400);
        }

        $cart->update([
            'quantity' => $request->quantity
        ]);

        // Return updated cart item with stock info
        $cart->load('product', 'variant');
        
        return response()->json([
            'status' => true,
            'message' => 'Cart item updated successfully',
            'data' => [
                'cart_id' => $cart->id,
                'product_id' => $cart->product_id,
                'variant_id' => $cart->variant_id,
                'quantity' => $cart->quantity,
                'available_stock' => $availableStock,
                'in_stock' => $availableStock > 0,
                'product_name' => $cart->product->name ?? null,
                'variant_name' => $cart->variant ? $cart->variant->sku : null
            ]
        ]);
    }

    return response()->json([
        'status' => true,
        'message' => 'Cart updated successfully'
    ]);
}

    public function applyCouponToCart(Request $request)
    {
        // Early return if coupon code is empty
        if (empty($request->coupon_code)) {
            return response()->json([
                'status' => false,
                'message' => 'No coupon code provided',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        // Get cart items
        $cartItems = Cart::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty',
            ], 400);
        }

        // Find coupon
        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid coupon code. Available: SALE200, PROMO20, AGRIWOW10, etc.',
            ], 404);
        }

        // Calculate subtotal
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $product = Product::find($item->product_id);
            $price = $product->selling_price ?? $product->price ?? 0;
            $subtotal += $price * $item->quantity;
        }

        // Check minimum amount requirement
        if (!empty($coupon->minimum_amount) && $subtotal < $coupon->minimum_amount) {
            // Remove coupon if minimum amount not met
            Cart::where('user_id', $user->id)->update(['coupon_code' => null]);

            return response()->json([
                'status' => false,
                'message' => 'Minimum order amount of ₹' . $coupon->minimum_amount . ' required. Current subtotal: ₹' . $subtotal,
            ], 400);
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->type === 'percent') {
            $discount = ($subtotal * $coupon->value) / 100;
            if (!empty($coupon->maximum_discount) && $discount > $coupon->maximum_discount) {
                $discount = $coupon->maximum_discount;
            }
        } elseif ($coupon->type === 'fixed') {
            $discount = $coupon->value;
        }

        // Update coupon in database for ALL cart items of this user
        $updated = Cart::where('user_id', $user->id)->update(['coupon_code' => $request->coupon_code]);

        if ($updated) {
            \Log::info('Coupon applied for user: ' . $user->id . ', coupon: ' . $request->coupon_code . ', rows updated: ' . $updated);
        } else {
            \Log::error('Failed to update coupon for user: ' . $user->id);
        }

        $shippingCharge = $this->calculateShippingCharge($subtotal);
        $grandTotal = ($subtotal + $shippingCharge) - $discount;

        // Verify the update was successful
        $verifyCart = Cart::where('user_id', $user->id)->first();
        \Log::info('Coupon code in cart after update: ' . ($verifyCart ? $verifyCart->coupon_code : 'no cart found'));

        return response()->json([
            'status' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'coupon' => $this->formatCouponForApi($coupon, [
                    'discount_amount' => round($discount, 2),
                ]),
                'cart_summary' => [
                    'subtotal' => round($subtotal, 2),
                    'shipping_charge' => $shippingCharge,
                    'discount_amount' => round($discount, 2),
                    'grand_total' => round($grandTotal, 2)
                ]
            ]
        ]);
    }

    public function clearCart(Request $request)
    {
        $user = auth()->user();
        $sessionId = $request->header('session-id');

        if (!$user && !$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'Session required'
            ], 422);
        }

        Cart::when($user, function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->when(!$user, function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
            })
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function cartCount(Request $request)
    {
        $user = auth()->user();
        $sessionId = $request->header('session-id');

        if (!$user && !$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'Session required',
                'count' => 0
            ], 422);
        }

        $count = Cart::when($user, function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->when(!$user, function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
            })
            ->sum('quantity');

        return response()->json([
            'status' => true,
            'message' => 'Cart count fetched successfully',
            'count' => $count
        ]);
    }

    public function removeCartItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
    
        $user = auth('sanctum')->user();
        $sessionId = $request->header('session-id');
    
        if (!$user && !$sessionId) {
            return response()->json([
                'status' => false,
                'message' => 'Session ID or Login required',
            ], 422);
        }
    
        $query = Cart::where('id', $request->cart_id);
    
        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }
    
        $cart = $query->first();
    
        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found',
            ], 404);
        }
    
        $cart->delete();
    
        return response()->json([
            'status' => true,
            'message' => 'Cart item removed successfully',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Add to Wishlist
    |--------------------------------------------------------------------------
    */
    public function addToWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
    
        // ✅ Sanctum user
        $authUser = auth('sanctum')->user();
    
        if (!$authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user',
            ], 401);
        }
    
        // ❗ SECURITY CHECK (VERY IMPORTANT)
        if ($authUser->id != $request->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'User mismatch with token',
            ], 403);
        }
    
        // variant validation
        if ($request->variant_id) {
            $variantValid = ProductVariant::where('id', $request->variant_id)
                ->where('product_id', $request->product_id)
                ->exists();
    
            if (!$variantValid) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid variant for this product',
                ], 422);
            }
        }
    
        // prevent duplicate
        $exists = Wishlist::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->where(function ($q) use ($request) {
                if ($request->variant_id) {
                    $q->where('variant_id', $request->variant_id);
                } else {
                    $q->whereNull('variant_id');
                }
            })
            ->exists();
    
        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Already in wishlist',
            ]);
        }
    
        $wishlist = Wishlist::create([
            'user_id'    => $request->user_id,
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id ?? null,
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Added to wishlist successfully',
            'data' => $wishlist
        ]);
    }

    public function getWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $wishlist = Wishlist::with(['product', 'user', 'variant'])
            ->where('user_id', $request->user_id)
            ->latest()
            ->get();

        $data = [];

        foreach ($wishlist as $item) {
            if (!$item->product) {
                continue;
            }

            $price = $item->variant
                ? ($item->variant->price ?? 0)
                : ($item->product->selling_price ?? $item->product->price ?? 0);

            $product = $item->product->toArray();

            // Thumbnail full URL
            $product['thumbnail'] = $item->product->thumbnail
                ? url('backend/assets/images/product/' . $item->product->thumbnail)
                : null;

            // Gallery full URL
            $product['gallery'] = collect($item->product->gallery)->map(function ($img) {
                return url('backend/assets/images/product/' . $img);
            })->toArray();

            // Gallery order full URL
            $product['gallery_order'] = collect($item->product->gallery_order)->map(function ($img) {
                return url('backend/assets/images/product/' . $img);
            })->toArray();

            $data[] = [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'price' => $price,
                'variant' => $item->variant ? [
                    'id' => $item->variant->id,
                    'name' => $item->variant->name ?? null,
                    'price' => $item->variant->price ?? 0,
                ] : null,
                'product' => $product,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Wishlist fetched successfully',
            'count' => count($data),
            'data' => $data
        ]);
    }

    public function wishlistCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $count = Wishlist::where('user_id', $request->user_id)
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Wishlist count fetched successfully',
            'count' => $count
        ]);
    }

    public function clearWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        Wishlist::where('user_id', $request->user_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Wishlist cleared successfully'
        ]);
    }

    public function removeWishlistItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found in wishlist',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'status' => true,
            'message' => 'Wishlist item removed successfully',
        ]);
    }

    public function checkout(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string'
        ]);

        $user = auth()->user();
        $sessionId = $request->header('session-id');
        
        // ===========================================
        // BUY NOW CHECKOUT (if buy_now_id is present)
        // ===========================================
        if ($request->has('buy_now_id')) {
            
            // Get buy now item
            $query = BuyNow::with(['product', 'variant']);
            
            if ($user) {
                $query->where('user_id', $user->id);
            } elseif ($sessionId) {
                $query->where('session_id', $sessionId);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No buy now item found'
                ], 404);
            }

            $buyNow = $query->where('id', $request->buy_now_id)->first();

            if (!$buyNow) {
                return response()->json([
                    'status' => false,
                    'message' => 'Buy now item not found'
                ], 404);
            }

            // Get address
            $address = Address::find($request->address_id);
            if (!$address || ($user && $address->user_id != $user->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Address not found'
                ]);
            }

            // Check stock
            $product = $buyNow->product;
            if ($product->quantity < $buyNow->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock for ' . $product->name
                ]);
            }

            // Calculate amounts
            $price = $this->getBuyNowPrice($buyNow);
            $subtotal = $price * $buyNow->quantity;

            // Coupon discount calculation
            $discountAmount = 0;
            
            if ($buyNow->coupon_code) {
                $coupon = Coupon::where('code', $buyNow->coupon_code)
                    ->where('status', 1)
                    ->first();

                if ($coupon) {
                    $today = now()->toDateString();
                    $isValid = (!$coupon->start_date || $today >= $coupon->start_date) &&
                              (!$coupon->end_date || $today <= $coupon->end_date) &&
                              (empty($coupon->minimum_amount) || $subtotal >= $coupon->minimum_amount);

                    if ($isValid) {
                        if ($coupon->type === 'percent') {
                            $discountAmount = ($subtotal * $coupon->value) / 100;
                            if (!empty($coupon->maximum_discount)) {
                                $discountAmount = min($discountAmount, $coupon->maximum_discount);
                            }
                        } elseif ($coupon->type === 'fixed') {
                            $discountAmount = $coupon->value;
                        }
                        
                        $discountAmount = round($discountAmount, 2);
                        
                        // Increment coupon usage count
                        $coupon->increment('used_count');
                    }
                }
            }

            // GST calculation (Applied AFTER Discount)
            $gstPercentage = 0;
            $amountAfterDiscount = $subtotal - $discountAmount;
            $gstAmount = ($amountAfterDiscount * $gstPercentage) / 100;
            $totalWithGst = $amountAfterDiscount + $gstAmount;
            
            $shippingCharge = $this->calculateShippingCharge($subtotal);
            $grandTotal = $totalWithGst + $shippingCharge;

            // Create order with discount
            $order = Order::create([
                'user_id' => $user->id ?? null,
                'order_id' => 'ORD' . time(),
                'invoice_no' => 'INV' . time(),
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'gst_amount' => $gstAmount,
                'shipping_charge' => $shippingCharge,
                'grand_total' => $grandTotal,
                'paid_amount' => 0,
                'due_amount' => $grandTotal,
                'payment_method' => $request->payment_method,
                'payment_type' => 'full',
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'invoice_date' => now(),
                'coupon_code' => $buyNow->coupon_code,
            ]);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $buyNow->product_id,
                'variant_id' => $buyNow->variant_id,
                'quantity' => $buyNow->quantity,
                'price' => $price,
                'total' => $subtotal,
            ]);

            // Reduce product stock
            $product->decrement('quantity', $buyNow->quantity);

            // Delete buy now item (auto-clear after checkout)
            $buyNow->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_id,
                    'subtotal' => round($subtotal, 2),
                    'discount_amount' => $discountAmount,
                    'gst_amount' => round($gstAmount, 2),
                    'shipping_charge' => $shippingCharge,
                    'grand_total' => round($grandTotal, 2),
                    'payment_method' => $request->payment_method,
                    'coupon_applied' => $buyNow->coupon_code
                ]
            ]);
        }
        
        // ===========================================
        // REGULAR CART CHECKOUT
        // ===========================================
        
        // Get cart items
        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ]);
        }

        // Get address
        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ]);
        }

        // Calculate subtotal
        $subtotal = 0;
        foreach ($cartItems as $cart) {
            $price = $cart->price ?? $cart->product->selling_price ?? $cart->product->price ?? 0;
            $subtotal += $price * $cart->quantity;
        }

        // Check for coupon on cart
        $discountAmount = 0;
        $couponCode = null;
        
        $firstCartItem = $cartItems->first();
        if ($firstCartItem && $firstCartItem->coupon_code) {
            $coupon = Coupon::where('code', $firstCartItem->coupon_code)
                ->where('status', 1)
                ->first();
            
            if ($coupon) {
                $today = now()->toDateString();
                $isValid = (!$coupon->start_date || $today >= $coupon->start_date) &&
                          (!$coupon->end_date || $today <= $coupon->end_date) &&
                          (empty($coupon->minimum_amount) || $subtotal >= $coupon->minimum_amount);
                
                if ($isValid) {
                    if ($coupon->type === 'percent') {
                        $discountAmount = ($subtotal * $coupon->value) / 100;
                        if (!empty($coupon->maximum_discount)) {
                            $discountAmount = min($discountAmount, $coupon->maximum_discount);
                        }
                    } elseif ($coupon->type === 'fixed') {
                        $discountAmount = $coupon->value;
                    }
                    
                    $discountAmount = round($discountAmount, 2);
                    $couponCode = $coupon->code;
                    $coupon->increment('used_count');
                }
            }
        }

        // GST calculation for cart
        $gstPercentage = 0;
        $amountAfterDiscount = $subtotal - $discountAmount;
        $gstAmount = ($amountAfterDiscount * $gstPercentage) / 100;
        $totalWithGst = $amountAfterDiscount + $gstAmount;
        
        $shippingCharge = $this->calculateShippingCharge($subtotal);
        $grandTotal = $totalWithGst + $shippingCharge;

        // Create Order
        $order = Order::create([
            'user_id' => $user->id,
            'order_id' => 'ORD' . time(),
            'invoice_no' => 'INV' . time(),
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'gst_amount' => $gstAmount,
            'shipping_charge' => $shippingCharge,
            'grand_total' => $grandTotal,
            'paid_amount' => 0,
            'due_amount' => $grandTotal,
            'payment_method' => $request->payment_method,
            'payment_type' => 'full',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'invoice_date' => now(),
            'coupon_code' => $couponCode,
        ]);

        // Save order items
        foreach ($cartItems as $cart) {
            $price = $cart->price ?? $cart->product->selling_price ?? $cart->product->price ?? 0;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
                'variant_id' => $cart->variant_id,
                'quantity' => $cart->quantity,
                'price' => $price,
                'total' => $price * $cart->quantity,
            ]);
            
            // Reduce product stock for cart items
            $product = Product::find($cart->product_id);
            if ($product) {
                $product->decrement('quantity', $cart->quantity);
            }
        }

        // Clear cart
        Cart::where('user_id', $user->id)->delete();

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'subtotal' => round($subtotal, 2),
                'discount_amount' => $discountAmount,
                'gst_amount' => round($gstAmount, 2),
                'shipping_charge' => $shippingCharge,
                'grand_total' => round($grandTotal, 2),
                'payment_method' => $request->payment_method,
                'coupon_applied' => $couponCode
            ]
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
/**
 * Remove/clear buy now item
 */
public function removeBuyNow(Request $request)
{
    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    $query = BuyNow::query();
    
    if ($user) {
        $query->where('user_id', $user->id);
    } elseif ($sessionId) {
        $query->where('session_id', $sessionId);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'User or Session not found'
        ], 422);
    }

    $buyNow = $query->first();

    if (!$buyNow) {
        return response()->json([
            'status' => false,
            'message' => 'No buy now item found to remove'
        ], 404);
    }

    $buyNow->delete();

    return response()->json([
        'status' => true,
        'message' => 'Buy now item removed successfully'
    ]);
}
public function applyCoupon(Request $request)
{
    $validator = Validator::make($request->all(), [
        'coupon_code' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    $coupon = Coupon::where('code', $request->coupon_code)
        ->where('status', 1)
        ->first();

    if (!$coupon) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid coupon code',
        ], 404);
    }

    $subtotal = 0;
    $buyNow = null;
    $cartItems = collect();

    // ==========================================
    // CHECK BUY NOW FIRST
    // ==========================================

    $buyNowQuery = BuyNow::query();

    if ($user) {
        $buyNowQuery->where('user_id', $user->id);
    } elseif ($sessionId) {
        $buyNowQuery->where('session_id', $sessionId);
    }

    $buyNow = $buyNowQuery->first();

    if ($buyNow) {

        $price = $this->getBuyNowPrice($buyNow);
        $subtotal = $price * $buyNow->quantity;

        $source = 'buy_now';

    } else {

        // ==========================================
        // FALLBACK TO CART
        // ==========================================

        $cartQuery = Cart::query();

        if ($user) {
            $cartQuery->where('user_id', $user->id);
        } elseif ($sessionId) {
            $cartQuery->where('session_id', $sessionId);
        }

        $cartItems = $cartQuery->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No item found in cart or buy now'
            ], 404);
        }

        foreach ($cartItems as $cart) {

            $price = $cart->variant
                ? $cart->variant->selling_price
                : $cart->product->selling_price;

            $subtotal += ($price * $cart->quantity);
        }

        $source = 'cart';
    }

    // ==========================================
    // COUPON VALIDATIONS
    // ==========================================

    $today = now()->toDateString();

    if ($coupon->start_date && $today < $coupon->start_date->toDateString()) {
        return response()->json([
            'status' => false,
            'message' => 'Coupon not started yet',
        ]);
    }

    if ($coupon->end_date && $today > $coupon->end_date->toDateString()) {
        return response()->json([
            'status' => false,
            'message' => 'Coupon expired',
        ]);
    }

    if (!empty($coupon->usage_limit) && $coupon->used_count >= $coupon->usage_limit) {
        return response()->json([
            'status' => false,
            'message' => 'Coupon usage limit exceeded',
        ]);
    }

    if (!empty($coupon->minimum_amount) && $subtotal < $coupon->minimum_amount) {
        return response()->json([
            'status' => false,
            'message' => 'Minimum order amount of ₹' . $coupon->minimum_amount . ' required.',
        ]);
    }

    // ==========================================
    // DISCOUNT CALCULATION
    // ==========================================

    $discount = 0;

    if ($coupon->type === 'percent') {

        $discount = ($subtotal * $coupon->value) / 100;

        if (!empty($coupon->maximum_discount) &&
            $discount > $coupon->maximum_discount) {
            $discount = $coupon->maximum_discount;
        }

    } else {

        $discount = $coupon->value;
    }

    $discount = round($discount, 2);

    // ==========================================
    // SAVE COUPON
    // ==========================================

    if ($source === 'buy_now') {

        $buyNow->update([
            'coupon_code' => $request->coupon_code
        ]);

    } else {

        $cartQuery->update([
            'coupon_code' => $request->coupon_code
        ]);
    }

    // ==========================================
    // TOTALS
    // ==========================================

    $amountAfterDiscount = $subtotal - $discount;

    $gstPercentage = 0;
    $gstAmount = ($amountAfterDiscount * $gstPercentage) / 100;

    $shippingCharge = $this->calculateShippingCharge($subtotal);

    $grandTotal =
        $amountAfterDiscount +
        $gstAmount +
        $shippingCharge;

    return response()->json([
        'status' => true,
        'message' => 'Coupon applied successfully',
        'applied_on' => $source,
        'coupon' => $this->formatCouponForApi($coupon, [
            'discount_amount' => $discount,
        ]),
        'summary' => [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => $discount,
            'amount_after_discount' => round($amountAfterDiscount, 2),
            'gst_amount' => round($gstAmount, 2),
            'shipping_charge' => $shippingCharge,
            'grand_total' => round($grandTotal, 2),
        ]
    ]);
}

    public function availableCoupons()
    {
        $today = now()->toDateString();

        $coupons = Coupon::where('status', 1)
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->get()
            ->map(fn ($coupon) => $this->formatCouponForApi($coupon));

        return response()->json([
            'status' => true,
            'message' => 'Available coupons fetched successfully',
            'data' => $coupons
        ]);
    }

 public function removeCoupon(Request $request)
{
    $request->validate([
        'source' => 'required|in:cart,buy_now'
    ]);

    
    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    // ======================================
    // BUY NOW ONLY
    // ======================================
    if ($request->source === 'buy_now') {

        $buyNowQuery = BuyNow::query();

        if ($user) {
            $buyNowQuery->where('user_id', $user->id);
        } elseif ($sessionId) {
            $buyNowQuery->where('session_id', $sessionId);
        }

        $buyNowQuery->update([
            'coupon_code' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Coupon removed from Buy Now'
        ]);
    }

    // ======================================
    // CART ONLY
    // ======================================
    $cartQuery = Cart::query();

    if ($user) {
        $cartQuery->where('user_id', $user->id);
    } elseif ($sessionId) {
        $cartQuery->where('session_id', $sessionId);
    }

    $cartQuery->update([
        'coupon_code' => null
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Coupon removed from Cart'
    ]);
}
    public function submitFaqQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'user_id'    => 'required|exists:users,id',
            'question'   => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $faq = ProductFaq::create([
            'product_id' => $request->product_id,
            'user_id'    => $request->user_id,
            'question'   => $request->question,
            'answer'     => null,
            'order'      => 0,
            'status'     => 0
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Question submitted successfully.',
            'data' => $faq
        ]);
    }

    public function addReview(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */
        $validator = Validator::make($request->except(array_keys($request->allFiles())), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK: ONE REVIEW PER USER PER PRODUCT
        |--------------------------------------------------------------------------
        */
        $alreadyReviewed = Review::where('product_id', $request->product_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'status' => false,
                'message' => 'You have already submitted a review for this product'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL: PURCHASE CHECK (KEEP IF REQUIRED)
        |--------------------------------------------------------------------------
        */
        $hasPurchased = OrderItem::where('product_id', $request->product_id)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                ->where('payment_status', 'paid');
            })
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'status' => false,
                'message' => 'You can only review purchased products'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | MEDIA FILES
        |--------------------------------------------------------------------------
        */
        $mediaFiles = $this->collectReviewMediaFiles($request);
        $imageFiles = $mediaFiles['images'];
        $videoFiles = $mediaFiles['videos'];

        $fileValidator = Validator::make(
            ['image' => $imageFiles, 'video' => $videoFiles],
            [
                'image' => 'nullable|array',
                'image.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:5120',
                'video' => 'nullable|array',
                'video.*' => 'mimes:mp4,mov,avi,mkv,3gp,webm|max:51200',
            ]
        );

        if ($fileValidator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $fileValidator->errors()->first(),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | UPLOAD FILES
        |--------------------------------------------------------------------------
        */
        $images = $this->uploadFiles($imageFiles, 'backend/assets/images/review');
        $videos = $this->uploadFiles($videoFiles, 'backend/assets/videos/review');

        /*
        |--------------------------------------------------------------------------
        | CREATE REVIEW
        |--------------------------------------------------------------------------
        */
        $review = new Review([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'review' => $request->review,
            'status' => 1,
            'like_count' => 0,
            'dislike_count' => 0,
        ]);

        $review->setAttachmentsFromUploads($images, $videos);
        $review->save();

        $review->load('user');

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'status' => true,
            'message' => 'Review added successfully',
            'data' => $this->formatReviewForApi($review),
            'meta' => [
                'images_uploaded' => count($images),
                'videos_uploaded' => count($videos),
            ],
        ]);
    }

    private function collectReviewMediaFiles(Request $request): array
    {
        $imageFiles = [];
        $videoFiles = [];

        foreach ($request->allFiles() as $field => $files) {
            if (preg_match('/^(image|images|photo|photos|video|videos|attachments?|media|files)([._\[].*)?$/i', (string) $field) !== 1) {
                continue;
            }

            $isVideoField = preg_match('/^(video|videos)([._\[].*)?$/i', (string) $field) === 1;

            foreach ($this->flattenUploadedFiles($files) as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                if ($isVideoField || $this->isVideoUpload($file)) {
                    $videoFiles[] = $file;
                } else {
                    $imageFiles[] = $file;
                }
            }
        }

        return [
            'images' => $this->uniqueUploadedFiles($imageFiles),
            'videos' => $this->uniqueUploadedFiles($videoFiles),
        ];
    }

    private function uniqueUploadedFiles(array $files): array
    {
        $unique = [];
        $seen = [];

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            $key = spl_object_hash($file);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[] = $file;
        }

        return $unique;
    }

    private function isVideoUpload($file): bool
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $mimeType = strtolower((string) $file->getMimeType());

        if (in_array($extension, ['mp4', 'mov', 'avi', 'mkv', '3gp', 'webm'], true)) {
            return true;
        }

        return str_starts_with($mimeType, 'video/');
    }

    private function getUploadedFilesAsArray(Request $request, string $field): array
    {
        $files = $request->file($field);

        if (!$files) {
            return [];
        }

        return $this->flattenUploadedFiles($files);
    }

    private function flattenUploadedFiles($files): array
    {
        if (!$files) {
            return [];
        }

        if (!is_array($files)) {
            return $files->isValid() ? [$files] : [];
        }

        $result = [];
        foreach ($files as $file) {
            $result = array_merge($result, $this->flattenUploadedFiles($file));
        }

        return $result;
    }

    public function uploadFiles($files, $path)
    {
        $uploadedFiles = [];

        if (!is_array($files)) {
            $files = [$files];
        }

        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0777, true);
        }

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'bin';
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $destination = public_path($path . '/' . $fileName);

            if ($file->move(public_path($path), $fileName)) {
                $uploadedFiles[] = $fileName;
                continue;
            }

            if (@copy($file->getRealPath(), $destination)) {
                $uploadedFiles[] = $fileName;
            }
        }

        return $uploadedFiles;
    }

    public function getReview($id)
    {
        $review = Review::with(['product', 'user'])->find($id);
    
        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found'
            ], 404);
        }
    
        // Optional authentication
        $user = auth('sanctum')->user();
    
        $userReaction = null;
    
        if ($user) {
            $userReactions = $this->getUserReviewReactions(
                $user,
                [$review->id]
            );
    
            $userReaction = $userReactions[$review->id] ?? null;
        }
    
        // Get all images/videos from all reviews of this product
        $productReviewMedia = Review::where('product_id', $review->product_id)
            ->get()
            ->flatMap(function ($reviewItem) {
    
                return collect($reviewItem->attachment_urls)
                    ->map(function ($attachment) {
    
                        return [
                            'type' => $attachment['type'] ?? null,
                            'url'  => $attachment['url'] ?? null,
                        ];
                    });
    
            })
            ->filter(function ($item) {
                return !empty($item['url']);
            })
            ->values()
            ->toArray();
    
        $data = $this->formatReviewForApi(
            $review,
            $userReaction
        );
    
        $data['product_review_media'] = $productReviewMedia;
    
        return response()->json([
            'status' => true,
            'message' => 'Review fetched successfully',
            'data' => $data
        ]);
    }

    public function updateReview(Request $request)
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
    
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'id' => 'required|exists:reviews,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'remove_attachments' => 'nullable',
            'remove_images' => 'nullable',
            'remove_videos' => 'nullable',
            'keep_attachments' => 'nullable',
            'keep_images' => 'nullable',
            'keep_videos' => 'nullable',
            'replace_attachments' => 'nullable|boolean',
        ]);
    
        /*
        |--------------------------------------------------------------------------
        | FIND REVIEW (SECURE)
        |--------------------------------------------------------------------------
        */
        $review = Review::where('id', $request->id)
            ->where('user_id', $user->id)
            ->first();
    
        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found or unauthorized'
            ], 404);
        }
    
        /*
        |--------------------------------------------------------------------------
        | ATTACHMENTS HANDLING
        |--------------------------------------------------------------------------
        */
        $currentAttachments = $review->getStoredAttachments();
        $mediaFiles = $this->collectReviewMediaFiles($request);
    
        $hasNewUploads = !empty($mediaFiles['images']) || !empty($mediaFiles['videos']);
        $hasAttachmentFieldUploads = $this->hasReviewAttachmentFieldUploads($request);
    
        $shouldReplaceAttachments =
            $hasAttachmentFieldUploads ||
            ($request->boolean('replace_attachments') && $hasNewUploads) ||
            $this->shouldClearAttachmentsFromInput($request);
    
        $keepList = $this->resolveReviewAttachmentKeepList($request, $currentAttachments);
    
        /*
        |--------------------------------------------------------------------------
        | APPLY ATTACHMENT LOGIC
        |--------------------------------------------------------------------------
        */
        if ($shouldReplaceAttachments) {
    
            $review->clearAttachments();
    
        } elseif ($keepList !== null) {
    
            $review->keepOnlyAttachments($keepList);
    
        } else {
    
            $filesToRemove = array_values(array_unique(array_merge(
                Review::normalizeAttachmentRemovalReferences($request->input('remove_attachments', []), $currentAttachments),
                Review::normalizeAttachmentRemovalReferences($request->input('remove_images', []), $currentAttachments),
                Review::normalizeAttachmentRemovalReferences($request->input('remove_videos', []), $currentAttachments)
            )));
    
            if (!empty($filesToRemove)) {
                $review->removeAttachmentsByFilenames($filesToRemove);
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | NEW FILE UPLOADS
        |--------------------------------------------------------------------------
        */
        if ($hasNewUploads) {
    
            $newImages = $this->uploadFiles($mediaFiles['images'], 'backend/assets/images/review');
            $newVideos = $this->uploadFiles($mediaFiles['videos'], 'backend/assets/videos/review');
    
            if ($shouldReplaceAttachments) {
                $review->setAttachmentsFromUploads($newImages, $newVideos);
            } else {
                $review->appendAttachmentsFromUploads($newImages, $newVideos);
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | UPDATE CORE DATA
        |--------------------------------------------------------------------------
        */
        $review->update([
            'rating' => $request->rating,
            'review' => $request->review,
            'attachments' => Review::encodeFromAttachmentItems(
                $review->getStoredAttachments()
            ),
        ]);
    
        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'status' => true,
            'message' => 'Review updated successfully',
            'data' => $this->formatReviewForApi(
                $review->fresh()->load('user')
            ),
        ]);
    }

    private function resolveReviewAttachmentKeepList(Request $request, array $currentAttachments): ?array
    {
        if ($request->has('keep_attachments')) {
            return Review::normalizeAttachmentReferences($request->input('keep_attachments', []));
        }

        if ($request->has('keep_images') || $request->has('keep_videos')) {
            return array_values(array_unique(array_merge(
                Review::normalizeAttachmentReferences($request->input('keep_images', [])),
                Review::normalizeAttachmentReferences($request->input('keep_videos', []))
            )));
        }

        $attachmentsInput = $request->input('attachments');

        if ($attachmentsInput !== null && !$this->hasReviewAttachmentFieldUploads($request)) {
            return Review::normalizeAttachmentReferences($attachmentsInput);
        }

        return null;
    }

    private function hasReviewAttachmentFieldUploads(Request $request): bool
    {
        foreach (array_keys($request->allFiles()) as $field) {
            if (preg_match('/^attachments?([._\[]|$)/i', (string) $field) !== 1) {
                continue;
            }

            foreach ($this->flattenUploadedFiles($request->file($field)) as $file) {
                if ($file && $file->isValid()) {
                    return true;
                }
            }
        }

        return false;
    }

    private function shouldClearAttachmentsFromInput(Request $request): bool
    {
        if ($this->hasReviewAttachmentFieldUploads($request) || !$request->has('attachments')) {
            return false;
        }

        $attachmentsInput = $request->input('attachments');

        if ($attachmentsInput === null || $attachmentsInput === '' || $attachmentsInput === '[]') {
            return true;
        }

        return is_array($attachmentsInput) && empty($attachmentsInput);
    }

    public function deleteReview(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:reviews,id'
        ]);
    
        $review = Review::where('id', $request->id)
            ->where('user_id', auth()->id())
            ->first();
    
        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found or you are not authorized to delete it'
            ], 404);
        }
    
        $review->deleteMediaFiles();
        $review->delete();
    
        return response()->json([
            'status' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    public function myReviews()
    {
        $reviews = Review::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $reviews->map(fn ($review) => $this->formatReviewForApi($review))->values(),
        ]);
    }

    public function reviewLike(Request $request)
    {
        return $this->handleReviewReaction($request, 'like');
    }

    public function reviewDislike(Request $request)
    {
        return $this->handleReviewReaction($request, 'dislike');
    }

    private function handleReviewReaction(Request $request, string $type)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to ' . $type . ' this review',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'review_id' => 'required|exists:reviews,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $review = Review::where('id', $request->review_id)
            ->where('status', 1)
            ->first();

        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $oppositeType = $type === 'like' ? 'dislike' : 'like';
        $countColumn = $type . '_count';
        $oppositeCountColumn = $oppositeType . '_count';
        $userReaction = null;

        DB::transaction(function () use ($review, $user, $type, $oppositeType, $countColumn, $oppositeCountColumn, &$userReaction) {
            $reaction = ReviewReaction::where('review_id', $review->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($reaction && $reaction->type === $type) {
                $reaction->delete();
                $review->decrement($countColumn);
                $userReaction = null;
                return;
            }

            if ($reaction && $reaction->type === $oppositeType) {
                $reaction->update(['type' => $type]);
                $review->decrement($oppositeCountColumn);
                $review->increment($countColumn);
                $userReaction = $type;
                return;
            }

            ReviewReaction::create([
                'review_id' => $review->id,
                'user_id' => $user->id,
                'type' => $type,
            ]);
            $review->increment($countColumn);
            $userReaction = $type;
        });

        $review->refresh()->load('user');

        return response()->json([
            'status' => true,
            'message' => ucfirst($type) . ' updated successfully',
            'data' => $this->formatReviewForApi($review, $userReaction),
        ]);
    }

    private function resolveOptionalApiUser(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user && $request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }

        if (!$user && $request->has('user_id')) {
            $user = \App\Models\User::find($request->user_id);
        }

        if (!$user) {
            $user = auth()->user();
        }

        return $user;
    }

    private function getUserReviewReactions($user, array $reviewIds): array
    {
        if (!$user || empty($reviewIds)) {
            return [];
        }

        return ReviewReaction::where('user_id', $user->id)
            ->whereIn('review_id', $reviewIds)
            ->pluck('type', 'review_id')
            ->toArray();
    }

    private function formatReviewForApi(Review $review, ?string $userReaction = null): array
    {
        if (!$review->relationLoaded('user')) {
            $review->load('user');
        }

        return [
            'id' => $review->id,
            'user_id' => $review->user_id,
            'user_name' => $review->user->name ?? 'User',
            'user' => $review->user ? [
                'id' => $review->user->id,
                'name' => $review->user->name,
                'avatar' => $review->user->avatar_url,
            ] : null,
            'product_id' => $review->product_id,
            'rating' => $review->rating,
            'review' => $review->review,
            'attachments' => $review->attachment_urls,
            'like_count' => (int) ($review->like_count ?? 0),
            'dislike_count' => (int) ($review->dislike_count ?? 0),
            'user_reaction' => $userReaction,
            'status' => $review->status,
            'created_at' => $review->created_at?->format('d M Y'),
            'updated_at' => $review->updated_at?->format('d M Y'),
        ];
    }



    public function addToBuyNow(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'variant_id' => 'nullable|integer|exists:product_variants,id',
        'quantity'   => 'required|integer|min:1',
        'address_id' => 'nullable|exists:addresses,id',
        'coupon_code' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    // 🔥 Check stock for simple or variant product
    $stockCheck = $this->checkProductStock(
        $request->product_id,
        $request->quantity,
        $request->variant_id
    );

    if (!$stockCheck['available']) {
        return response()->json([
            'status' => false,
            'message' => $stockCheck['message'],
            'available_stock' => $stockCheck['available_stock'],
            'product_type' => $stockCheck['product_type']
        ], 400);
    }

    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    if (!$user && !$sessionId) {
        $sessionId = uniqid('guest_');
    }

    // Get existing buy now to check for coupon
    $existingBuyNow = BuyNow::when($user, function($q) use ($user) {
        $q->where('user_id', $user->id);
    })->when(!$user && $sessionId, function($q) use ($sessionId) {
        $q->where('session_id', $sessionId);
    })->first();

    // Preserve existing coupon if not provided in request
    $couponCode = $request->coupon_code ?? ($existingBuyNow ? $existingBuyNow->coupon_code : null);

    // Delete existing buy now for this user/session (only one allowed)
    BuyNow::when($user, function($q) use ($user) {
        $q->where('user_id', $user->id);
    })->when(!$user && $sessionId, function($q) use ($sessionId) {
        $q->where('session_id', $sessionId);
    })->delete();

    // Create new buy now with address_id and coupon_code
    $buyNow = BuyNow::create([
        'user_id' => $user->id ?? null,
        'session_id' => $user ? null : $sessionId,
        'product_id' => $request->product_id,
        'variant_id' => $request->variant_id,
        'quantity' => $request->quantity,
        'address_id' => $request->address_id,
        'coupon_code' => $couponCode,
    ]);

    // Load product details
    $buyNow->load(['product', 'variant', 'address']);

    $price = $this->getBuyNowPrice($buyNow);
    $subtotal = $price * $buyNow->quantity;

    $availableStock = $this->getProductAvailableStock(
        $request->product_id,
        $request->variant_id
    );

    // Calculate discount if coupon exists
    $discountAmount = 0;
    if ($buyNow->coupon_code) {
        $coupon = Coupon::where('code', $buyNow->coupon_code)->where('status', 1)->first();
        if ($coupon) {
            if ($coupon->type === 'percent') {
                $discountAmount = ($subtotal * $coupon->value) / 100;
                if (!empty($coupon->maximum_discount)) {
                    $discountAmount = min($discountAmount, $coupon->maximum_discount);
                }
            } elseif ($coupon->type === 'fixed') {
                $discountAmount = $coupon->value;
            }
        }
    }

    return response()->json([
        'status' => true,
        'message' => 'Product added to buy now',
        'data' => [
            'buy_now_id' => $buyNow->id,
            'address_id' => $buyNow->address_id,
            'coupon_code' => $buyNow->coupon_code,
            'discount_amount' => round($discountAmount, 2),
            'product' => [
                'id' => $buyNow->product->id,
                'name' => $buyNow->product->name,
                'slug' => $buyNow->product->slug,
                'thumbnail' => $buyNow->product->thumbnail 
                    ? url('backend/assets/images/product/' . $buyNow->product->thumbnail)
                    : null,
                'price' => $price,
                'selling_price' => $buyNow->product->selling_price,
                'product_type' => $buyNow->product->product_type,
                'in_stock' => $availableStock > 0,
                'available_stock' => $availableStock
            ],
            'variant' => $buyNow->variant ? [
                'id' => $buyNow->variant->id,
                'sku' => $buyNow->variant->sku,
                'price' => $buyNow->variant->price,
                'selling_price' => $buyNow->variant->selling_price,
                'in_stock' => $buyNow->variant->quantity > 0,
                'available_stock' => $buyNow->variant->quantity
            ] : null,
            'quantity' => $buyNow->quantity,
            'subtotal' => $subtotal,
            'available_stock' => $availableStock
        ]
    ]);
}

/**
 * Get current buy now item
 */
/**
 * Get current buy now item
 */
public function getBuyNow(Request $request)
{
    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    $query = BuyNow::with(['product', 'variant', 'address']);

    if ($user) {
        $query->where('user_id', $user->id);
    } elseif ($sessionId) {
        $query->where('session_id', $sessionId);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'No buy now item found'
        ], 404);
    }

    $buyNow = $query->first();

    if (!$buyNow) {
        return response()->json([
            'status' => false,
            'message' => 'No buy now item found'
        ], 404);
    }

    // Refresh to ensure we have latest data
    $buyNow->refresh();
    
    \Log::info('Get Buy Now - ID: ' . $buyNow->id . ', Coupon from DB: ' . $buyNow->coupon_code);

    // Address handling
    $selectedAddress = null;
    if ($buyNow->address_id) {
        $selectedAddress = Address::where('id', $buyNow->address_id)
            ->when($user, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();
    } elseif ($user) {
        $selectedAddress = Address::where('user_id', $user->id)
            ->latest()
            ->first();
        if ($selectedAddress) {
            $buyNow->address_id = $selectedAddress->id;
            $buyNow->save();
        }
    }

    $price = $this->getBuyNowPrice($buyNow);
    $subtotal = $price * $buyNow->quantity;

    // Coupon discount calculation
    $discountAmount = 0;
    $couponData = null;
    $appliedDiscount = 0;
    $appliedCouponCode = null;

    if ($buyNow->coupon_code) {
        $coupon = Coupon::where('code', $buyNow->coupon_code)
            ->where('status', 1)
            ->first();

        if ($coupon) {
            $today = now()->toDateString();
            $isValid = (!$coupon->start_date || $today >= $coupon->start_date) &&
                      (!$coupon->end_date || $today <= $coupon->end_date) &&
                      (empty($coupon->minimum_amount) || $subtotal >= $coupon->minimum_amount);

            if ($isValid) {
                if ($coupon->type === 'percent') {
                    $discountAmount = ($subtotal * $coupon->value) / 100;
                    if (!empty($coupon->maximum_discount)) {
                        $discountAmount = min($discountAmount, $coupon->maximum_discount);
                    }
                } elseif ($coupon->type === 'fixed') {
                    $discountAmount = $coupon->value;
                }
                
                $appliedDiscount = round($discountAmount, 2);
                $appliedCouponCode = $coupon->code;
                
                $couponData = $this->formatCouponForApi($coupon, [
                    'discount_amount' => $appliedDiscount,
                ]);
            } else {
                // Coupon is invalid, clear it from database
                $buyNow->coupon_code = null;
                $buyNow->save();
                \Log::info('Removed invalid coupon from buy_now: ' . $buyNow->coupon_code);
            }
        } else {
            // Coupon doesn't exist, clear it
            $buyNow->coupon_code = null;
            $buyNow->save();
            \Log::info('Removed non-existent coupon from buy_now');
        }
    }

    // GST calculation
    $gstPercentage = 0;
    $amountAfterDiscount = $subtotal - $appliedDiscount;
    $gstAmount = ($amountAfterDiscount * $gstPercentage) / 100;
    $totalWithGst = $amountAfterDiscount + $gstAmount;
    $shippingCharge = $this->calculateShippingCharge($subtotal);
    $grandTotal = $totalWithGst + $shippingCharge;

    // Product images
    $product = $buyNow->product;
    if ($product) {
        $product->thumbnail = $product->thumbnail
            ? url('backend/assets/images/product/' . $product->thumbnail)
            : null;

        if (!empty($product->gallery)) {
            $gallery = is_array($product->gallery)
                ? $product->gallery
                : json_decode($product->gallery, true);

            $product->gallery = collect($gallery ?? [])
                ->map(function ($image) {
                    return url('backend/assets/images/product/' . $image);
                })
                ->values();
        }
    }

    return response()->json([
        'status' => true,
        'message' => 'Buy now item fetched',
        'selected_address' => $selectedAddress,
        'coupon_code' => $appliedCouponCode,
        'coupon' => $couponData,
        'data' => [
            'id' => $buyNow->id,
            'address_id' => $buyNow->address_id,
            'coupon_code' => $buyNow->coupon_code,
            'product' => $product,
            'variant' => $buyNow->variant,
            'quantity' => (int) $buyNow->quantity,
            'price' => $price,
            'subtotal' => round($subtotal, 2),
          
        ],
          'summary' => [
                'subtotal' => round($subtotal, 2),
                'discount_amount' => $appliedDiscount,
                'amount_after_discount' => round($amountAfterDiscount, 2),
                'gst_percentage' => $gstPercentage,
                'gst_amount' => round($gstAmount, 2),
                'amount_after_gst' => round($totalWithGst, 2),
                'shipping_charge' => $shippingCharge,
                'grand_total' => round($grandTotal, 2),
            ]
    ]);
}

/**
 * Update buy now quantity
 */
public function updateBuyNow(Request $request)
{
    \Log::info('=== UPDATE BUY NOW REQUEST START ===');
    \Log::info('Request data:', $request->all());
    
    $validator = Validator::make($request->all(), [
        'buy_now_id' => 'required|exists:buy_nows,id',
        'quantity' => 'sometimes|required|integer|min:1',
        'address_id' => 'nullable|exists:addresses,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    $query = BuyNow::where('id', $request->buy_now_id);
    
    if ($user) {
        $query->where('user_id', $user->id);
    } elseif ($sessionId) {
        $query->where('session_id', $sessionId);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'User or Session not found'
        ], 422);
    }

    $buyNow = $query->first();

    if (!$buyNow) {
        return response()->json([
            'status' => false,
            'message' => 'Buy now item not found'
        ], 404);
    }

    // 🔥 Check stock before updating quantity
    if ($request->has('quantity')) {
        $availableStock = $this->getProductAvailableStock(
            $buyNow->product_id,
            $buyNow->variant_id
        );

        if ($request->quantity > $availableStock) {
            return response()->json([
                'status' => false,
                'message' => 'Not enough stock available. Available: ' . $availableStock,
                'available_stock' => $availableStock,
                'requested_quantity' => $request->quantity
            ], 400);
        }
    }

    // Update fields
    $changes = [];
    
    if ($request->has('quantity')) {
        $oldQuantity = $buyNow->quantity;
        $buyNow->quantity = $request->quantity;
        $changes['quantity'] = ['old' => $oldQuantity, 'new' => $request->quantity];
    }
    
    if ($request->has('address_id')) {
        $oldAddressId = $buyNow->address_id;
        $buyNow->address_id = $request->address_id;
        $changes['address_id'] = ['old' => $oldAddressId, 'new' => $request->address_id];
    }
    
    if (empty($changes)) {
        return response()->json([
            'status' => true,
            'message' => 'No changes to update'
        ]);
    }
    
    $buyNow->save();
    $buyNow->refresh();
    $buyNow->load(['product', 'variant', 'address']);
    
    $price = $this->getBuyNowPrice($buyNow);
    $subtotal = $price * $buyNow->quantity;
    $availableStock = $this->getProductAvailableStock(
        $buyNow->product_id,
        $buyNow->variant_id
    );
    
    return response()->json([
        'status' => true,
        'message' => 'Buy now updated successfully',
        'changes' => $changes,
        'data' => [
            'id' => $buyNow->id,
            'quantity' => (int) $buyNow->quantity,
            'address_id' => $buyNow->address_id,
            'price' => $price,
            'subtotal' => round($subtotal, 2),
            'available_stock' => $availableStock,
            'in_stock' => $availableStock > 0,
            'product' => [
                'id' => $buyNow->product->id,
                'name' => $buyNow->product->name,
                'slug' => $buyNow->product->slug,
                'thumbnail' => $buyNow->product->thumbnail 
                    ? url('backend/assets/images/product/' . $buyNow->product->thumbnail)
                    : null,
            ]
        ]
    ]);
}

public function updateBuyNowAddress(Request $request)
{
    $validator = Validator::make($request->all(), [
        'address_id' => 'required|exists:addresses,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    $query = BuyNow::query();

    if ($user) {
        $query->where('user_id', $user->id);
    } elseif ($sessionId) {
        $query->where('session_id', $sessionId);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'User or Session not found'
        ], 422);
    }

    $updated = $query->update([
        'address_id' => $request->address_id
    ]);

    if ($updated) {
        \Log::info('BuyNow address updated - User: ' . ($user->id ?? $sessionId) . ', Address ID: ' . $request->address_id);
    }

    return response()->json([
        'status' => true,
        'message' => 'BuyNow address updated successfully'
    ]);
}
/**
 * Process buy now checkout - creates order and automatically clears buy now
 */
/**
 * Process buy now checkout - creates order and automatically clears buy now
 */
public function buyNowCheckout(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string'
        ]);

        $user = auth()->user();
        $sessionId = $request->header('session-id');

        // Get buy now item
        $query = BuyNow::with(['product', 'variant']);
        
        if ($user) {
            $query->where('user_id', $user->id);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No buy now item found'
            ], 404);
        }

        $buyNow = $query->first();

        if (!$buyNow) {
            return response()->json([
                'status' => false,
                'message' => 'No buy now item found'
            ], 404);
        }

        // Get address
        $address = Address::find($request->address_id);
        if (!$address || ($user && $address->user_id != $user->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ]);
        }

        // Check stock
        $product = $buyNow->product;
        if ($product->quantity < $buyNow->quantity) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient stock for ' . $product->name
            ]);
        }

        // Calculate amounts
        $price = $this->getBuyNowPrice($buyNow);
        $subtotal = $price * $buyNow->quantity;

        // 🔥 COUPON DISCOUNT CALCULATION
        $discountAmount = 0;
        $couponData = null;
        
        if ($buyNow->coupon_code) {
            $coupon = Coupon::where('code', $buyNow->coupon_code)
                ->where('status', 1)
                ->first();

            if ($coupon) {
                $today = now()->toDateString();
                $isValid = (!$coupon->start_date || $today >= $coupon->start_date) &&
                          (!$coupon->end_date || $today <= $coupon->end_date) &&
                          (empty($coupon->minimum_amount) || $subtotal >= $coupon->minimum_amount);

                if ($isValid) {
                    if ($coupon->type === 'percent') {
                        $discountAmount = ($subtotal * $coupon->value) / 100;
                        if (!empty($coupon->maximum_discount)) {
                            $discountAmount = min($discountAmount, $coupon->maximum_discount);
                        }
                    } elseif ($coupon->type === 'fixed') {
                        $discountAmount = $coupon->value;
                    }
                    
                    $discountAmount = round($discountAmount, 2);
                    
                    // Increment coupon usage count
                    $coupon->increment('used_count');
                }
            }
        }

        // 🔥 GST CALCULATION (Applied AFTER Discount)
        $gstPercentage = 0;
        $amountAfterDiscount = $subtotal - $discountAmount;
        $gstAmount = ($amountAfterDiscount * $gstPercentage) / 100;
        $totalWithGst = $amountAfterDiscount + $gstAmount;
        
        $shippingCharge = $this->calculateShippingCharge($subtotal);
        $grandTotal = $totalWithGst + $shippingCharge;

        // Create order with discount
        $order = Order::create([
            'user_id' => $user->id ?? null,
            'order_id' => 'ORD' . time(),
            'invoice_no' => 'INV' . time(),
            'subtotal' => $subtotal,
            'discount' => $discountAmount,  // Include discount
            'gst_amount' => $gstAmount,
            'shipping_charge' => $shippingCharge,
            'grand_total' => $grandTotal,
            'paid_amount' => 0,
            'due_amount' => $grandTotal,
            'payment_method' => $request->payment_method,
            'payment_type' => 'full',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'invoice_date' => now(),
            'coupon_code' => $buyNow->coupon_code, // Save coupon code in order
        ]);

        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $buyNow->product_id,
            'variant_id' => $buyNow->variant_id,
            'quantity' => $buyNow->quantity,
            'price' => $price,
            'total' => $subtotal,
        ]);

        // Reduce product stock
        $product->decrement('quantity', $buyNow->quantity);

        // Delete buy now item (auto-clear after checkout)
        $buyNow->delete();

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'subtotal' => round($subtotal, 2),
                'discount_amount' => $discountAmount,
                'gst_amount' => round($gstAmount, 2),
                'shipping_charge' => $shippingCharge,
                'grand_total' => round($grandTotal, 2),
                'payment_method' => $request->payment_method,
                'coupon_applied' => $buyNow->coupon_code
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

private function calculateShippingCharge(float $subtotal): float
{
    $shippingSetting = ShippingSetting::where('status', 1)->first();

    if (!$shippingSetting) {
        return 0;
    }

    return $subtotal >= $shippingSetting->free_shipping_amount
        ? 0
        : (float) $shippingSetting->shipping_charge;
}

/**
 * Helper method to get price (product selling price or variant price)
 */
private function getBuyNowPrice($buyNow)
{
    if ($buyNow->variant && $buyNow->variant->price) {
        return $buyNow->variant->price;
    }
    return $buyNow->product->selling_price ?? $buyNow->product->price ?? 0;
}
public function removeCouponFromBuyNow(Request $request)
{
    $user = auth('sanctum')->user();
    $sessionId = $request->header('session-id');

    $query = BuyNow::query();
    
    if ($user) {
        $query->where('user_id', $user->id);
    } elseif ($sessionId) {
        $query->where('session_id', $sessionId);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'No buy now item found'
        ], 404);
    }

    $buyNow = $query->first();

    if (!$buyNow) {
        return response()->json([
            'status' => false,
            'message' => 'No buy now item found'
        ], 404);
    }

    $buyNow->coupon_code = null;
    $buyNow->save();

    return response()->json([
        'status' => true,
        'message' => 'Coupon removed successfully',
        'data' => [
            'buy_now_id' => $buyNow->id,
            'coupon_code' => null
        ]
    ]);
}

    public function emails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletters,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $newsletter = Newsletter::create([
            'email' => $request->email,
            'status' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Newsletter subscribed successfully',
            'data' => $newsletter
        ]);
    }
    public function addProductQuestion(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'question' => 'required|string|max:1000',
        ]);
    
        $faq = ProductFaq::create([
            'product_id' => $request->product_id,
            'user_id' => $request->user_id,
            'question' => $request->question,
            'answer' => null,
            'order' => 0,
            'status' => 0,
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Question submitted successfully.',
            'data' => $faq
        ]);
    }

    private function formatCouponForApi(Coupon $coupon, array $extra = []): array
    {
        $categoryIds = $this->resolveCouponCategoryIds($coupon);
        $productIds = $this->resolveCouponProductIds($coupon);

        $categories = $this->mapCouponItemsByIds(Category::class, $categoryIds);
        $products = $this->mapCouponItemsByIds(Product::class, $productIds);

        $categoryNames = array_column($categories, 'name');
        $productNames = array_column($products, 'name');

        $hasInclude = !empty($categoryIds) || !empty($productIds);

        if (!$hasInclude) {
            $type = 'all';
            $label = 'All products';
            $applyOnText = 'All products';
        } elseif (!empty($productIds) && empty($categoryIds)) {
            $type = 'products';
            $applyOnText = 'Products: ' . implode(', ', $productNames);
            $label = $applyOnText;
        } elseif (!empty($categoryIds) && empty($productIds)) {
            $type = 'categories';
            $applyOnText = 'Categories: ' . implode(', ', $categoryNames);
            $label = $applyOnText;
        } else {
            $type = 'specific';
            $parts = [];
            if (!empty($categoryNames)) {
                $parts[] = 'Categories: ' . implode(', ', $categoryNames);
            }
            if (!empty($productNames)) {
                $parts[] = 'Products: ' . implode(', ', $productNames);
            }
            $applyOnText = implode(' | ', $parts);
            $label = $applyOnText;
        }

        return array_merge([
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'minimum_amount' => $coupon->minimum_amount,
            'maximum_discount' => $coupon->maximum_discount,
            'start_date' => $coupon->start_date ? $coupon->start_date->format('Y-m-d') : null,
            'end_date' => $coupon->end_date ? $coupon->end_date->format('Y-m-d') : null,
            'applicability' => [
                'type' => $type,
                'label' => $label,
                'apply_on_text' => $applyOnText,
                'categories' => $categories,
                'products' => $products,
                'category_names' => $categoryNames,
                'product_names' => $productNames,
            ],
        ], $extra);
    }

    private function resolveCouponCategoryIds(Coupon $coupon): array
    {
        $ids = $this->normalizeCouponIds($coupon->category_ids);

        if (!empty($ids)) {
            return $ids;
        }

        if (Schema::hasTable('coupon_category')) {
            return DB::table('coupon_category')
                ->where('coupon_id', $coupon->id)
                ->pluck('category_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        return [];
    }

    private function resolveCouponProductIds(Coupon $coupon): array
    {
        return $this->normalizeCouponIds($coupon->product_ids);
    }

    private function mapCouponItemsByIds(string $modelClass, array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $modelClass::whereIn('id', $ids)
            ->get(['id', 'name'])
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->values()
            ->all();
    }

    private function normalizeCouponIds($ids): array
    {
        if ($ids === null || $ids === '' || $ids === '[]') {
            return [];
        }

        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $ids = $decoded;
            } elseif (str_contains($ids, ',')) {
                $ids = explode(',', $ids);
            } elseif (is_numeric($ids)) {
                $ids = [$ids];
            } else {
                return [];
            }
        }

        if (is_object($ids)) {
            $ids = (array) $ids;
        }

        return array_values(array_unique(array_filter(array_map('intval', (array) $ids))));
    }
}