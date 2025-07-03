<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Authentication middleware removed - all methods now public
    }

    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        return view('front_web.home.home');
    }
}
