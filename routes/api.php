<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\SectionImageController;
use App\Http\Controllers\Api\NavigationLinkController;
use App\Http\Controllers\Api\SocialLinkController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AdminAuthController::class, 'user'])->middleware('auth:sanctum');
    Route::get('/check-auth', [AdminAuthController::class, 'checkAuth']);
});

// Page routes
Route::get('/home/sections', [PageController::class, 'getHomeSections']);
Route::get('/pages/{slug}/sections', [PageController::class, 'getPageSections']);
Route::get('/header/sections', [PageController::class, 'getHeaderContent']);
Route::get('/contact/sections', [PageController::class, 'getContactContent']);
Route::get('/about/sections', [PageController::class, 'getAboutContent']);

// Service routes
Route::get('/services', [ServiceController::class, 'getAllServices']);
Route::get('/services/featured', [ServiceController::class, 'getFeaturedServices']);
Route::get('/services/{slug}', [ServiceController::class, 'getServiceBySlug']);
Route::get('/services/{slug}/sections', [ServiceController::class, 'getServiceWithSections']);

// Specific service pages
Route::get('/education-and-scholarship/sections', [PageController::class, 'getEducationSections']);
Route::get('/training-and-professional-development/sections', [PageController::class, 'getTrainingSections']);
Route::get('/ai-and-advanced-technologies/sections', [PageController::class, 'getAiSections']);
Route::get('/egaming-and-esport/sections', [PageController::class, 'getEgamingSections']);
Route::get('/arts-and-entertainment/sections', [PageController::class, 'getArtsSections']);