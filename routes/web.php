<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;

// API Routes
Route::post('/api/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Home page route
Route::get('/', [HomeController::class, 'index'])->name('home');

// All other web routes should be handled by the SPA
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
