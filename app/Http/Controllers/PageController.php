<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;

class PageController extends Controller
{
    public function home()
    {
        $banner = Banner::where('page_identifier', 'home')->where('is_active', true)->first();

        return view('page.home', compact('banner'));
    }

    public function about()
    {
        return view('page.about');
    }

    public function contact()
    {
        return view('page.contact');
    }
}