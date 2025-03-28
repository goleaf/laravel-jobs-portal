<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use App\Helpers\TranslationHelper;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale', config('app.locale'));
        App::setLocale($locale);
        
        // If running in debug mode and not an API or AJAX request, check for missing translations
        if (config('app.debug') && 
            !$request->expectsJson() && 
            !$request->ajax() &&
            !$this->isExcludedPath($request->path())) 
        {
            // Only check for missing translations if we're not using the default locale
            if ($locale !== config('app.fallback_locale')) {
                $missingCount = count(TranslationHelper::getMissingTranslations($locale));
                
                // Share the missing translation count with all views
                View::share('missingTranslationsCount', $missingCount);
                
                // Only show warning if there are missing translations
                if ($missingCount > 0) {
                    Session::flash('translation_warning', [
                        'count' => $missingCount,
                        'locale' => $locale
                    ]);
                }
            }
        }
        
        return $next($request);
    }
    
    /**
     * Check if the current path should be excluded from translation checks
     * 
     * @param string $path
     * @return bool
     */
    private function isExcludedPath(string $path): bool
    {
        $excludedPaths = [
            'livewire',
            '_debugbar',
            'translations',
            'api',
            'telescope',
            'horizon',
            'nova',
            'admin/translations',
        ];
        
        foreach ($excludedPaths as $excludedPath) {
            if (Str::startsWith($path, $excludedPath)) {
                return true;
            }
        }
        
        return false;
    }
} 