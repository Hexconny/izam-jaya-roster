<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;

class AboutController extends Controller
{
    public function index()
    {
        return view('public.about', ['profile' => BusinessProfile::current()]);
    }
}
