<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Context7 Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth_context7.php';

/*
|--------------------------------------------------------------------------
| Context7 API v1 Routes  
|--------------------------------------------------------------------------
*/
require __DIR__.'/api_context7.php';

// Legacy API endpoints for security testing
Route::get('/jobs', function () {
    return response()->json([
        'message' => 'API endpoint requires authentication',
        'status' => 'unauthorized'
    ], 401);
})->name('api.jobs.index');
