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
    $supportedLanguages = ['en', 'lt'];
    
    if (in_array($locale, $supportedLanguages)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    
    return redirect()->back()->with('success', __('Language changed successfully'));
})->name('language.switch');