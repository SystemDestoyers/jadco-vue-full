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
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Api\AuthCheckController;

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

// Contact form submission route
Route::post('/contact/submit', [App\Http\Controllers\ContactController::class, 'apiSubmit']);
Route::post('/contact/email', [App\Http\Controllers\ContactController::class, 'sendEmail']);

// Admin Authentication Routes
// These routes are now handled by web.php, so they're commented out here

Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AdminAuthController::class, 'user'])->middleware('auth:sanctum');
    Route::get('/check-auth', [AdminAuthController::class, 'checkAuth']);
    Route::get('/debug-auth', [AuthCheckController::class, 'checkAuth']);
});


// Page routes
Route::get('/home/sections', [PageController::class, 'getHomeSections']);
Route::get('/pages/{slug}/sections', [PageController::class, 'getPageSections']);
Route::get('/header/sections', [PageController::class, 'getHeaderContent']);
Route::get('/contact/sections', [PageController::class, 'getContactContent']);
Route::get('/about/sections', [PageController::class, 'getAboutContent']);
Route::get('/navbar/section', [PageController::class, 'getNavbarContent']);

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

// Admin API Routes
Route::prefix('admin')->group(function () {
    // Pages management
    Route::get('/pages', [AdminPageController::class, 'index']);
    Route::post('/pages', [AdminPageController::class, 'store']);
    Route::get('/pages/{id}', [AdminPageController::class, 'show']);
    Route::put('/pages/{id}', [AdminPageController::class, 'update']);
    Route::delete('/pages/{id}', [AdminPageController::class, 'destroy']);

    // User profile routes
    Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth:sanctum');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('auth:sanctum');
    Route::post('/profile/image', [ProfileController::class, 'updateProfileImage'])->middleware('auth:sanctum');
    
    // Sections management
    Route::get('/pages/{pageId}/sections', [AdminSectionController::class, 'index']);
    Route::post('/pages/{pageId}/sections', [AdminSectionController::class, 'store']);
    Route::get('/sections/{id}', [AdminSectionController::class, 'show']);
    Route::put('/sections/{id}', [AdminSectionController::class, 'update']);
    Route::put('/sections/{id}/content', [AdminSectionController::class, 'updateContent']);
    Route::put('/sections/{id}/order', [AdminSectionController::class, 'updateOrder']);
    Route::delete('/sections/{id}', [AdminSectionController::class, 'destroy']);
    
    // Media Library management
    Route::get('/media', [MediaController::class, 'index']);
    Route::post('/media', [MediaController::class, 'store']);
    Route::get('/media/count', [MediaController::class, 'count']);
    Route::get('/media/{id}', [MediaController::class, 'show']);
    Route::put('/media/{id}', [MediaController::class, 'update']);
    Route::delete('/media/{id}', [MediaController::class, 'destroy']);
    Route::post('/media/folders', [MediaController::class, 'createFolder']);
    Route::get('/media/folders/list', [MediaController::class, 'listFolders']);
    Route::delete('/media/folders/delete', [MediaController::class, 'deleteFolder']);

    // Message management
    Route::get('messages/archived', [\App\Http\Controllers\Admin\MessageController::class, 'archived']);
    Route::get('messages/sent', [\App\Http\Controllers\Admin\MessageController::class, 'sent']);
    Route::apiResource('messages', \App\Http\Controllers\Admin\MessageController::class);
    Route::put('messages/{id}/mark-as-read', [\App\Http\Controllers\Admin\MessageController::class, 'markAsRead']);
    Route::put('messages/{id}/mark-as-unread', [\App\Http\Controllers\Admin\MessageController::class, 'markAsUnread']);
    Route::put('messages/{id}/archive', [\App\Http\Controllers\Admin\MessageController::class, 'archive']);
    Route::put('messages/{id}/unarchive', [\App\Http\Controllers\Admin\MessageController::class, 'unarchive']);
    Route::post('messages/send', [\App\Http\Controllers\Admin\MessageController::class, 'sendMessage']);

    // Settings management
    Route::post('/settings/email', [App\Http\Controllers\Api\SettingsController::class, 'updateEmailSettings']);
    Route::post('/settings/email/test', [App\Http\Controllers\Api\SettingsController::class, 'testEmailConnection']);
    Route::get('/settings', [App\Http\Controllers\Api\SettingsController::class, 'getSettings']);
    
    // Database settings management (new)
    Route::get('/database-settings', [App\Http\Controllers\Api\SettingsController::class, 'getDatabaseSettings']);
    Route::post('/database-settings', [App\Http\Controllers\Api\SettingsController::class, 'updateDatabaseSettings']);
});