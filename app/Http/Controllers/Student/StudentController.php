<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamGrade;
use App\Models\ExamTerm;
use App\Models\Homework;
use App\Models\Notice;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(404, 'Student record not found for this user account.');
        }

        $enrollment = $student->currentEnrollment;
        $classId = $enrollment?->class_id;
        $sectionId = $enrollment?->section_id;

        // Timetable for current day & all week
        $todayName = Carbon::today()->format('l');
        $todaySchedule = Timetable::with(['subject', 'teacher'])
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('day_of_week', $todayName)
            ->orderBy('start_time')
            ->get();

        // Weekly schedule
        $weeklySchedule = Timetable::with(['subject', 'teacher'])
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        // Attendance stats
        $totalAttendance = Attendance::where('student_id', $student->id)->count();
        $presentCount = Attendance::where('student_id', $student->id)->where('status', 'present')->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 100;

        // Recent homework
        $homeworks = Homework::with('subject')
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->latest()
            ->take(4)
            ->get();

        $notices = Notice::where('is_published_public', true)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'enrollment',
            'todaySchedule',
            'weeklySchedule',
            'totalAttendance',
            'presentCount',
            'attendancePercentage',
            'homeworks',
            'notices'
        ));
    }

    public function gradesIndex(Request $request): View
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(404, 'Student record not found.');
        }

        $enrollment = $student->currentEnrollment;
        $examTerms = ExamTerm::latest()->get();
        $selectedTermId = $request->get('term_id', $examTerms->first()?->id);
        $selectedTerm = ExamTerm::find($selectedTermId) ?? $examTerms->first();

        $grades = ExamGrade::with('subject')
            ->where('student_id', $student->id)
            ->when($selectedTermId, fn($q) => $q->where('exam_term_id', $selectedTermId))
            ->get();

        $totalMarksObtained = $grades->sum('marks_obtained');
        $totalPossibleMarks = $grades->sum('total_marks');
        $averagePercentage = $totalPossibleMarks > 0 ? round(($totalMarksObtained / $totalPossibleMarks) * 100, 2) : 0;
        $gpa = $grades->count() > 0 ? round($grades->avg('grade_point'), 2) : 0.00;

        return view('student.grades.index', compact(
            'student',
            'enrollment',
            'examTerms',
            'selectedTerm',
            'grades',
            'totalMarksObtained',
            'totalPossibleMarks',
            'averagePercentage',
            'gpa'
        ));
    }
}
