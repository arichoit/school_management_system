<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => view('admin.dashboard'),
            'teacher' => view('teacher.dashboard'),
            'student' => view('student.dashboard'),
            default => abort(403),
        };
    }
}