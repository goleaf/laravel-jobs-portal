<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryPeriodController extends Controller
{
    /**
     * Display a listing of salary periods.
     */
    public function index(): View
    {
        return view('admin.salary_periods.index');
    }
}
