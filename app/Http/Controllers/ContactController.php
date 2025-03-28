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
            'phone' => 'nullable|string|min:10',
            'password' => 'nullable|string|min:8',
            'password_confirmation' => 'nullable|string|same:password',
            'country' => 'nullable|string',
            'form_type' => 'nullable|string',
            'show_phone' => 'nullable',
            'interests' => 'nullable|array',
            'interests.*' => 'nullable|string',
            'priority' => 'nullable|string',
        ]);

        // Process the form (this would typically send an email or store in database)
        // For demonstration, we'll just redirect with a success message
        
        return redirect()->back()->with('success', 'Your message has been sent. We will get back to you soon!');
    }

    /**
     * Display the validation example form
     */
    public function validationExample()
    {
        return view('forms.validation');
    }

    /**
     * Display the Alpine.js integration example form
     */
    public function alpineExample()
    {
        return view('forms.alpine');
    }

    /**
     * Display the binding example form
     */
    public function bindingExample()
    {
        return view('forms.binding');
    }

    /**
     * Display the error handling example form
     */
    public function errorExample()
    {
        return view('forms.errors');
    }

    /**
     * Display the method spoofing example form
     */
    public function methodExample()
    {
        return view('forms.methods');
    }
} 