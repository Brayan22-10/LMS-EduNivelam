<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function reports()
    {
        return view('page.reports');
    }

    public function directory()
    {
        return view('page.directory');
    }
}