<?php

use App\Http\Controllers\Parent\ParentController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
Route::post('/switch-child/{childId}', [ParentController::class, 'switchChild'])->name('switch-child');

// Fees
Route::get('/fees', [ParentController::class, 'feesIndex'])->name('fees.index');
Route::post('/fees/{invoice}/pay', [ParentController::class, 'processPayment'])->name('fees.pay');

// Homework
Route::get('/homework', [ParentController::class, 'homeworkIndex'])->name('homework.index');
