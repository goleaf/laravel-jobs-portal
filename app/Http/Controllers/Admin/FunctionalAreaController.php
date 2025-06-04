<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FunctionalAreaController extends Controller
{
    /**
     * Display a listing of functional areas.
     */
    public function index(): View
    {
        return view('admin.functional_areas.index');
    }
}
