<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display a listing of candidate applications.
     */
    public function index(): View
    {
        return view('candidate.applications.index');
    }
}
