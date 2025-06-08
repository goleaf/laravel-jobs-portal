<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
class ReportedJobController extends Controller
{
    /**
     * Display a listing of reported jobs.
     */
    public function index(): View
    {
        return view('admin.reported_jobs.index');
    }
}
