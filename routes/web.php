<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;

// API Routes
Route::post('/api/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Home page route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Other frontend pages - we can add more as needed
Route::get('/about', function () {
    return view('frontend.pages.about');
})->name('about');

Route::get('/services', function () {
    return view('frontend.pages.services');
})->name('services');

Route::get('/contact', function () {
    return view('frontend.pages.contact');
})->name('contact');

// Fallback route for SPA to handle frontend routing in Vue
Route::get('/services/{slug}', function () {
    return view('frontend.pages.service-detail');
})->where('slug', '.*')->name('service-detail');

// All other web routes should be handled by the SPA
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
