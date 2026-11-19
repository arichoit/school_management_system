<?php

use App\Http\Controllers\Accountant\AccountantController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [AccountantController::class, 'dashboard'])->name('dashboard');

// Fees
Route::get('/fees', [AccountantController::class, 'feesIndex'])->name('fees.index');
Route::post('/fees/structure', [AccountantController::class, 'storeFeeType'])->name('fees.structure.store');
Route::post('/fees/record-payment', [AccountantController::class, 'recordPayment'])->name('fees.record-payment');
