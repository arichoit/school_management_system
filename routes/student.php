<?php

use App\Http\Controllers\Student\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
Route::get('/grades', [StudentController::class, 'gradesIndex'])->name('grades.index');
