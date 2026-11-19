<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\ContactSetting;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($appUrl = config('app.url')) {
            URL::forceRootUrl(rtrim($appUrl, '/'));
        }

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            try {
                $this->shareGlobalViewData($view);
            } catch (\Throwable $e) {
                report($e);

                $view->with([
                    'navCategories' => collect(),
                    'categories' => collect(),
                    'contactSetting' => null,
                    'footerContactSetting' => null,
                    'headerTopBarSetting' => null,
                    'cartCount' => 0,
                    'cartProductIds' => [],
                    'wishlistCount' => 0,
                    'wishlistIds' => [],
                ]);
            }
        });
    }

    private function shareGlobalViewData($view): void
    {
        $data = $view->getData();

        if (!array_key_exists('navCategories', $data)) {
            $navCategories = Category::where('status', 1)
                ->with(['products' => function ($q) {
                    $q->where('status', 1)->orderBy('name');
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            $view->with('navCategories', $navCategories);

            if (!array_key_exists('categories', $data)) {
                $view->with('categories', $navCategories);
            }
        } elseif (!array_key_exists('categories', $data)) {
            $view->with('categories', $data['navCategories']);
        }

        if (!array_key_exists('contactSetting', $data)) {
            $contactSetting = null;

            if (class_exists(ContactSetting::class)) {
                try {
                    $contactSetting = ContactSetting::first();
                } catch (\Throwable $e) {
                    $contactSetting = null;
                }
            }

            $view->with('contactSetting', $contactSetting);
        }

        if (!array_key_exists('footerContactSetting', $data)) {
            $footerContactSetting = null;

            if (class_exists(\App\Models\FooterContactSetting::class)) {
                try {
                    $footerContactSetting = \App\Models\FooterContactSetting::first();
                } catch (\Throwable $e) {
                    $footerContactSetting = null;
                }
            }

            $view->with('footerContactSetting', $footerContactSetting);
        }

        if (!array_key_exists('headerTopBarSetting', $data)) {
            $headerTopBarSetting = null;

            if (class_exists(\App\Models\HeaderTopBarSetting::class)) {
                try {
                    $headerTopBarSetting = \App\Models\HeaderTopBarSetting::first();
                } catch (\Throwable $e) {
                    $headerTopBarSetting = null;
                }
            }

            $view->with('headerTopBarSetting', $headerTopBarSetting);
        }

        $cartService = app(CartService::class);

        if (!array_key_exists('cartCount', $data)) {
            $view->with('cartCount', $cartService->getCartCount());
        }

        if (!array_key_exists('cartProductIds', $data)) {
            $view->with('cartProductIds', $cartService->getCartProductIds());
        }

        if (!array_key_exists('wishlistCount', $data) || !array_key_exists('wishlistIds', $data)) {
            if (Auth::check()) {
                $wishlistIds = Wishlist::where('user_id', Auth::id())
                    ->pluck('product_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                if (!array_key_exists('wishlistIds', $data)) {
                    $view->with('wishlistIds', $wishlistIds);
                }
                if (!array_key_exists('wishlistCount', $data)) {
                    $view->with('wishlistCount', count($wishlistIds));
                }
            } else {
                if (!array_key_exists('wishlistIds', $data)) {
                    $view->with('wishlistIds', []);
                }
                if (!array_key_exists('wishlistCount', $data)) {
                    $view->with('wishlistCount', 0);
                }
            }
        }
    }
}
