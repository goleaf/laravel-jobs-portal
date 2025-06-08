<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates.
     */
    public function index(): View
    {
        return view('admin.email_templates.index');
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit($template): View
    {
        return view('admin.email_templates.edit', compact('template'));
    }
}
