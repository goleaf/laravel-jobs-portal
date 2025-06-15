<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SalaryCurrencyController extends Controller
{
    /**
     * Display a listing of salary currencies.
     */
    public function index(): View
    {
        return view('admin.salary_currencies.index');
    }
}
