<?php

use App\Http\Controllers\Teacher\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');

// Attendance
Route::get('/attendance', [TeacherController::class, 'attendanceIndex'])->name('attendance.index');
Route::post('/attendance', [TeacherController::class, 'attendanceStore'])->name('attendance.store');

// Grades
Route::get('/grades', [TeacherController::class, 'gradesIndex'])->name('grades.index');
Route::post('/grades', [TeacherController::class, 'gradesStore'])->name('grades.store');
