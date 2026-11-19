<?php

namespace App\Providers;
use App\Models\Category;
use App\Models\ContactSetting;
use App\Models\FooterContactSetting;
use App\Services\CartService;
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
        View::composer('*', function ($view) {
            $data = $view->getData();

            if (!array_key_exists('navCategories', $data)) {
                $navCategories = Category::where('status', 1)
                    ->with(['products' => function ($q) {
                        $q->where('status', 1)->orderBy('name');
                    }])
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
                $view->with('contactSetting', ContactSetting::first());
            }
            if (!array_key_exists('footerContactSetting', $data)) {
                $view->with('footerContactSetting', FooterContactSetting::first());
            }
            $view->with('cartCount', app(CartService::class)->getCartCount());
        });
    }
}
