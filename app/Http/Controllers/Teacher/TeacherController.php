<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\ExamGrade;
use App\Models\ExamTerm;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $sections = Section::with('schoolClass')
            ->where('class_teacher_id', $user->id)
            ->get();

        // Check if attendance is pending for today for assigned sections
        $pendingAttendance = [];
        foreach ($sections as $sec) {
            $markedCount = Attendance::where('section_id', $sec->id)->where('date', $today)->count();
            if ($markedCount === 0) {
                $pendingAttendance[] = $sec;
            }
        }

        $upcomingExams = ExamTerm::where('end_date', '>=', $today)->get();

        return view('teacher.dashboard', compact('sections', 'pendingAttendance', 'upcomingExams'));
    }

    public function attendanceIndex(Request $request): View
    {
        $classes = SchoolClass::with('sections')->get();
        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $selectedSectionId = $request->get('section_id', $classes->first()?->sections->first()?->id);
        $selectedDate = $request->get('date', Carbon::today()->toDateString());

        $students = [];
        if ($selectedSectionId) {
            $enrollments = Enrollment::with('student.user')
                ->where('section_id', $selectedSectionId)
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();

            // Fetch existing attendance for this date
            $existingAttendance = Attendance::where('section_id', $selectedSectionId)
                ->where('date', $selectedDate)
                ->pluck('status', 'student_id')
                ->toArray();

            foreach ($enrollments as $enr) {
                $students[] = [
                    'id' => $enr->student->id,
                    'roll_number' => $enr->roll_number,
                    'name' => $enr->student->user->name,
                    'status' => $existingAttendance[$enr->student->id] ?? 'present',
                ];
            }
        }

        return view('teacher.attendance.index', compact(
            'classes',
            'selectedClassId',
            'selectedSectionId',
            'selectedDate',
            'students'
        ));
    }

    public function attendanceStore(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:present,absent,late,excused'],
        ]);

        $userId = auth()->id();

        DB::transaction(function () use ($validated, $userId) {
            foreach ($validated['attendance'] as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date' => $validated['date'],
                    ],
                    [
                        'class_id' => $validated['class_id'],
                        'section_id' => $validated['section_id'],
                        'status' => $status,
                        'marked_by' => $userId,
                    ]
                );
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.attendance_saved'),
            ]);
        }

        return back()->with('success', __('messages.attendance_saved'));
    }

    public function gradesIndex(Request $request): View
    {
        $classes = SchoolClass::with('sections')->get();
        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $selectedSectionId = $request->get('section_id', $classes->first()?->sections->first()?->id);

        $examTerms = ExamTerm::latest()->get();
        $selectedTermId = $request->get('exam_term_id', $examTerms->first()?->id);

        $subjects = Subject::all();
        $selectedSubjectId = $request->get('subject_id', $subjects->first()?->id);
        $selectedSubject = Subject::find($selectedSubjectId) ?? $subjects->first();

        $students = [];
        if ($selectedSectionId && $selectedTermId && $selectedSubject) {
            $enrollments = Enrollment::with('student.user')
                ->where('section_id', $selectedSectionId)
                ->where('status', 'active')
                ->orderBy('roll_number')
                ->get();

            $existingGrades = ExamGrade::where('exam_term_id', $selectedTermId)
                ->where('subject_id', $selectedSubject->id)
                ->get()
                ->keyBy('student_id');

            foreach ($enrollments as $enr) {
                $grade = $existingGrades->get($enr->student->id);
                $students[] = [
                    'id' => $enr->student->id,
                    'roll_number' => $enr->roll_number,
                    'name' => $enr->student->user->name,
                    'marks_obtained' => $grade ? $grade->marks_obtained : '',
                    'letter_grade' => $grade ? $grade->letter_grade : '-',
                    'grade_point' => $grade ? $grade->grade_point : '-',
                    'remarks' => $grade ? $grade->remarks : '',
                ];
            }
        }

        return view('teacher.grades.index', compact(
            'classes',
            'selectedClassId',
            'selectedSectionId',
            'examTerms',
            'selectedTermId',
            'subjects',
            'selectedSubjectId',
            'selectedSubject',
            'students'
        ));
    }

    public function gradesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exam_term_id' => ['required', 'exists:exam_terms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'marks' => ['required', 'array'],
            'marks.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'remarks' => ['nullable', 'array'],
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        $userId = auth()->id();

        DB::transaction(function () use ($validated, $subject, $userId) {
            foreach ($validated['marks'] as $studentId => $marksObtained) {
                if ($marksObtained !== null && $marksObtained !== '') {
                    $marks = (float) $marksObtained;
                    $pct = ($marks / $subject->total_marks) * 100;
                    $calc = ExamGrade::calculateGradeAndPoint($pct);

                    ExamGrade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'subject_id' => $subject->id,
                            'exam_term_id' => $validated['exam_term_id'],
                        ],
                        [
                            'marks_obtained' => $marks,
                            'total_marks' => $subject->total_marks,
                            'letter_grade' => $calc['grade'],
                            'grade_point' => $calc['point'],
                            'remarks' => $validated['remarks'][$studentId] ?? null,
                            'entered_by' => $userId,
                        ]
                    );
                }
            }
        });

        return back()->with('success', __('messages.grades_saved'));
    }
}
