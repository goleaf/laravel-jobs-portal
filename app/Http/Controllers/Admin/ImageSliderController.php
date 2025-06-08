<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
class ImageSliderController extends Controller
{
    /**
     * Display a listing of image sliders.
     */
    public function index(): View
    {
        return view('admin.image_sliders.index');
    }
}
