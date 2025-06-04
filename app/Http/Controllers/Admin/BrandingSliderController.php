<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandingSliderController extends Controller
{
    /**
     * Display a listing of branding sliders.
     */
    public function index(): View
    {
        return view('admin.branding_sliders.index');
    }
}
