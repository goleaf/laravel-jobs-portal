<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display the contact form
     */
    public function index()
    {
        return view('forms.contact');
    }

    /**
     * Process the contact form submission
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|in:general,support,billing,other',
            'message' => 'required|string',
            'newsletter' => 'nullable',
        ]);

        // Process the form (this would typically send an email or store in database)
        // For demonstration, we'll just redirect with a success message
        
        return redirect()->route('contact')->with('success', 'Your message has been sent. We will get back to you soon!');
    }
} 