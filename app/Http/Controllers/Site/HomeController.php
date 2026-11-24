<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home', [
            'profile'    => BusinessProfile::current(),
            'categories' => Category::withCount('products')->get(),
            'featured'   => Product::with(['category', 'images', 'variants'])->latest()->take(4)->get(),
        ]);
    }
}
