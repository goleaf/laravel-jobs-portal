<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher
{
    public function handle(Request $request, Closure $next)
    {
        // Check if language is set in session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } 
        // Check if language is set in user preferences
        elseif (auth()->check() && auth()->user()->language) {
            $locale = auth()->user()->language;
        } 
        // Check browser's preferred language
        else {
            $locale = $request->getPreferredLanguage(Config::get('app.available_locales'));
        }

        // Validate locale
        $locale = in_array($locale, Config::get('app.available_locales')) 
            ? $locale 
            : Config::get('app.fallback_locale');

        // Set application locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
} 