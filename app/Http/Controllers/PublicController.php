<?php

namespace App\Http\Controllers;

use App\Models\AdmissionInquiry;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $notices = Notice::where('is_published_public', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();

        $stats = [
            'students' => Student::count() > 0 ? Student::count() + 1195 : 1200,
            'teachers' => Teacher::count() > 0 ? Teacher::count() + 83 : 85,
            'classes' => SchoolClass::count(),
            'passRate' => '100%',
        ];

        return view('public.index', compact('notices', 'classes', 'stats'));
    }

    public function storeAdmissionInquiry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'class_applied_for' => ['required', 'string', 'max:100'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        AdmissionInquiry::create($validated);

        return back()->with('success', __('messages.inquiry_success'));
    }

    public function notices(): View
    {
        $notices = Notice::where('is_published_public', true)
            ->latest('published_at')
            ->paginate(10);

        return view('public.notices', compact('notices'));
    }
}
