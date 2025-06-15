<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display a listing of employer applications.
     */
    public function index(): View
    {
        return view('employer.applications.index');
    }
}
