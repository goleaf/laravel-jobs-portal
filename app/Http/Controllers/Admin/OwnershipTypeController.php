<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnershipTypeController extends Controller
{
    /**
     * Display a listing of ownership types.
     */
    public function index(): View
    {
        return view('admin.ownership_types.index');
    }
}
