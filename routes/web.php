<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Public Website Routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::post('/admission-inquiry', [PublicController::class, 'storeAdmissionInquiry'])->name('admission.inquiry.store');
Route::get('/notices', [PublicController::class, 'notices'])->name('public.notices');

// Locale Switcher
Route::get('/locale/{locale}', [AuthController::class, 'switchLocale'])->name('locale.switch');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
