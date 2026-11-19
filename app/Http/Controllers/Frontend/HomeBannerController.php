<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;

class HomeBannerController extends Controller
{
    public function index()
    {
        $banners = HomeBanner::orderBy('sort_order', 'ASC')->get();

        // convert image to full URL
        $banners->transform(function ($banner) {
            $banner->image = asset($banner->image);
            return $banner;
        });

        return response()->json([
            'status' => true,
            'data' => $banners
        ]);
    }
}