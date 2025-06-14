<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes V1
|--------------------------------------------------------------------------
| Enhanced Level 4 Complex System Transformation
| Generated RESTful API routes for Vue3 SPA
*/

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Authentication routes
    Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->withoutMiddleware(['auth:sanctum']);
    Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register'])->withoutMiddleware(['auth:sanctum']);
    Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/auth/user', [App\Http\Controllers\Api\AuthController::class, 'user']);
    
    // Generated API routes

    // Admin Routes
    Route::apiResource('admin', App\Http\Controllers\Api\V1\AdminApiController::class);

    // Candidate Routes
    Route::apiResource('candidate', App\Http\Controllers\Api\V1\CandidateApiController::class);

    // Job Routes
    Route::apiResource('job', App\Http\Controllers\Api\V1\JobApiController::class);
    Route::apiResource('reportedjob', App\Http\Controllers\Api\V1\ReportedJobApiController::class);

    // Company Routes
    Route::apiResource('companysize', App\Http\Controllers\Api\V1\CompanySizeApiController::class);
    Route::apiResource('company', App\Http\Controllers\Api\V1\CompanyApiController::class);

    // General Routes
    Route::apiResource('home', App\Http\Controllers\Api\V1\HomeApiController::class);
    Route::apiResource('brandingslider', App\Http\Controllers\Api\V1\BrandingSliderApiController::class);
    Route::apiResource('masterdata', App\Http\Controllers\Api\V1\MasterDataApiController::class);
    Route::apiResource('cms', App\Http\Controllers\Api\V1\CmsApiController::class);
    Route::apiResource('emailtemplate', App\Http\Controllers\Api\V1\EmailTemplateApiController::class);
    Route::apiResource('functionalarea', App\Http\Controllers\Api\V1\FunctionalAreaApiController::class);
    Route::apiResource('headerslider', App\Http\Controllers\Api\V1\HeaderSliderApiController::class);
    Route::apiResource('imageslider', App\Http\Controllers\Api\V1\ImageSliderApiController::class);
    Route::apiResource('salarycurrency', App\Http\Controllers\Api\V1\SalaryCurrencyApiController::class);
    Route::apiResource('salaryperiod', App\Http\Controllers\Api\V1\SalaryPeriodApiController::class);
    Route::apiResource('subscriber', App\Http\Controllers\Api\V1\SubscriberApiController::class);
    Route::apiResource('transaction', App\Http\Controllers\Api\V1\TransactionApiController::class);
    Route::apiResource('blogcomment', App\Http\Controllers\Api\V1\BlogCommentApiController::class);
    Route::apiResource('application', App\Http\Controllers\Api\V1\ApplicationApiController::class);
    Route::apiResource('location', App\Http\Controllers\Api\V1\LocationApiController::class);
    Route::apiResource('dashboard', App\Http\Controllers\Api\V1\DashboardApiController::class);
    Route::apiResource('swagger', App\Http\Controllers\Api\V1\SwaggerApiController::class);
    Route::apiResource('swaggerasset', App\Http\Controllers\Api\V1\SwaggerAssetApiController::class);
    Route::apiResource('frontendassets', App\Http\Controllers\Api\V1\FrontendAssetsApiController::class);
    Route::apiResource('filepreview', App\Http\Controllers\Api\V1\FilePreviewApiController::class);
    Route::apiResource('handlerequests', App\Http\Controllers\Api\V1\HandleRequestsApiController::class);
    Route::apiResource('fileupload', App\Http\Controllers\Api\V1\FileUploadApiController::class);
    Route::apiResource('realtime', App\Http\Controllers\Api\V1\RealTimeApiController::class);
    Route::apiResource('csrfcookie', App\Http\Controllers\Api\V1\CsrfCookieApiController::class);
    Route::apiResource('sitemap', App\Http\Controllers\Api\V1\SitemapApiController::class);
    Route::apiResource('payment', App\Http\Controllers\Api\V1\PaymentApiController::class);
    Route::apiResource('webhook', App\Http\Controllers\Api\V1\WebhookApiController::class);
    Route::apiResource('wireuiassets', App\Http\Controllers\Api\V1\WireUiAssetsApiController::class);
    Route::apiResource('unknown', App\Http\Controllers\Api\V1\UnknownApiController::class);
});