<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\FeeInvoice;
use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $today = Carbon::today()->toDateString();

        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();

        $totalInvoiced = FeeInvoice::sum('total_amount') ?: 1;
        $totalCollected = FeeInvoice::sum('paid_amount') ?: 0;
        $feeCollectionRate = round(($totalCollected / $totalInvoiced) * 100, 1);

        $totalAttendanceToday = Attendance::where('date', $today)->count();
        $presentToday = Attendance::where('date', $today)->where('status', 'present')->count();
        $attendanceRate = $totalAttendanceToday > 0 ? round(($presentToday / $totalAttendanceToday) * 100, 1) : 96.5;

        $recentStudents = Student::with(['user', 'currentEnrollment.schoolClass', 'currentEnrollment.section'])
            ->latest()
            ->take(6)
            ->get();

        $classes = SchoolClass::withCount('enrollments')->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'feeCollectionRate',
            'attendanceRate',
            'totalInvoiced',
            'totalCollected',
            'recentStudents',
            'classes'
        ));
    }

    public function studentsIndex(Request $request): View
    {
        $query = Student::with(['user', 'currentEnrollment.schoolClass', 'currentEnrollment.section', 'parents.user']);

        if ($request->filled('class_id')) {
            $query->whereHas('currentEnrollment', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        $classes = SchoolClass::with('sections')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function studentsCreate(): View
    {
        $classes = SchoolClass::with('sections')->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $parents = ParentProfile::with('user')->get();

        return view('admin.students.create', compact('classes', 'academicYears', 'parents'));
    }

    public function studentsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'roll_number' => ['required', 'integer', 'min:1'],
            'parent_id' => ['nullable', 'exists:parents,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role' => 'student',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            $seq = Student::count() + 1;
            do {
                $admissionNo = 'ADM-' . date('Y') . '-' . str_pad($seq++, 4, '0', STR_PAD_LEFT);
            } while (Student::where('admission_number', $admissionNo)->exists());

            $student = Student::create([
                'user_id' => $user->id,
                'admission_number' => $admissionNo,
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'blood_group' => $validated['blood_group'] ?? null,
                'address' => $validated['address'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
            ]);

            Enrollment::create([
                'student_id' => $student->id,
                'academic_year_id' => $validated['academic_year_id'],
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'roll_number' => $validated['roll_number'],
                'status' => 'active',
            ]);

            if (!empty($validated['parent_id'])) {
                $student->parents()->attach($validated['parent_id']);
            }
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Student admitted and enrolled successfully! Default password is "password".');
    }

    public function academicsClasses(): View
    {
        $classes = SchoolClass::with(['sections.classTeacher', 'subjects'])->get();
        $teachers = User::where('role', 'teacher')->get();
        $subjects = Subject::all();

        return view('admin.academics.classes', compact('classes', 'teachers', 'subjects'));
    }

    public function academicsClassesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:10', 'unique:classes,code'],
            'numeric_level' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        SchoolClass::create($validated);

        return back()->with('success', 'Class configuration created successfully.');
    }

    public function academicsTimetable(Request $request): View
    {
        $classes = SchoolClass::with('sections')->get();
        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $selectedSectionId = $request->get('section_id', $classes->first()?->sections->first()?->id);

        $timetables = Timetable::with(['subject', 'teacher'])
            ->when($selectedClassId, fn($q) => $q->where('class_id', $selectedClassId))
            ->when($selectedSectionId, fn($q) => $q->where('section_id', $selectedSectionId))
            ->orderBy('start_time')
            ->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        return view('admin.academics.timetable', compact('classes', 'selectedClassId', 'selectedSectionId', 'timetables', 'days'));
    }

    public function usersIndex(Request $request): View
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(12)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleUserStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'User account status updated.');
    }
}
