<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HeaderSliderController extends Controller
{
    /**
     * Display a listing of header sliders.
     */
    public function index(): View
    {
        return view('admin.header_sliders.index');
    }
}
