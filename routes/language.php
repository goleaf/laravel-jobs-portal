<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Language Routes
|--------------------------------------------------------------------------
|
| Routes for language switching functionality
|
*/

Route::get('/language/{locale}', function ($locale) {
    $supportedLanguages = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
    
    if (in_array($locale, $supportedLanguages)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
        
        // Set RTL direction for Arabic
        if ($locale === 'ar') {
            session(['direction' => 'rtl']);
        } else {
            session(['direction' => 'ltr']);
        }
    }
    
    return redirect()->back()->with('success', __('Language changed successfully'));
})->name('language.switch');