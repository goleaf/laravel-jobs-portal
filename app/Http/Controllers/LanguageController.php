<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:' . implode(',', Config::get('app.available_locales'))
        ]);

        $locale = $request->input('locale');

        // Update user's language preference if logged in
        if (auth()->check()) {
            $user = auth()->user();
            $user->language = $locale;
            $user->save();
        }

        // Set session and app locale
        Session::put('locale', $locale);
        App::setLocale($locale);

        // Redirect back with success message
        return redirect()->back()->with('status', __('language.switched', ['locale' => $locale]));
    }

    public function getSupportedLanguages()
    {
        return response()->json([
            'supported_languages' => Config::get('app.available_locales'),
            'current_locale' => App::getLocale()
        ]);
    }
} 