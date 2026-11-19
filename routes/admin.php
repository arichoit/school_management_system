<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

// Students
Route::get('/students', [AdminController::class, 'studentsIndex'])->name('students.index');
Route::get('/students/create', [AdminController::class, 'studentsCreate'])->name('students.create');
Route::post('/students', [AdminController::class, 'studentsStore'])->name('students.store');

// Academics
Route::get('/academics/classes', [AdminController::class, 'academicsClasses'])->name('academics.classes');
Route::post('/academics/classes', [AdminController::class, 'academicsClassesStore'])->name('academics.classes.store');
Route::get('/academics/timetable', [AdminController::class, 'academicsTimetable'])->name('academics.timetable');

// Users & Permissions
Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
