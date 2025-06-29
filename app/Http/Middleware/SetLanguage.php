<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * use Illuminate\Support\Facades\Session;.
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $localeLanguage = Session::get('languageName');
        $default = Setting::where('key', '=', 'default_language')->first();
        
        // Set fallback default language if setting not found
        $defaultLanguage = $default?->value ?? 'en';

        if (!isset($localeLanguage)) {
            if (Auth::user()) {
                App::setLocale(Auth::user()->language ?? $defaultLanguage);
            } else {
                App::setLocale($defaultLanguage);
            }
        } else {
            if (Auth::user()) {
                if (isset($localeLanguage)) {
                    // dump(56456);
                    App::setLocale($localeLanguage);
                } else {
                    App::setLocale(Auth::user()->language ?? $defaultLanguage);
                }
            } else {
                App::setLocale($localeLanguage);
            }
        }

        return $next($request);
    }
}
