<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AuthController;

// Admin Authentication Routes
Route::prefix('admin-auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/check-auth', [AuthController::class, 'checkAuth']);
});

// API Routes
Route::post('/api/contact/submit', [ContactController::class, 'apiSubmit'])->name('contact.submit');

// Home page route
Route::get('/', [HomeController::class, 'index'])->name('home');

// All other web routes should be handled by the SPA
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api|admin-auth).*$');
