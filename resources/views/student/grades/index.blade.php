@extends('layouts.portal')

@section('title', __('messages.report_card'))
@section('breadcrumb', __('messages.report_card'))

@section('content')
<div class="space-y-6">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.report_card') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Official terminal examination evaluations, letter grades, and GPA statements.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-sm transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>{{ __('messages.print_report') }} / PDF</span>
            </button>
        </div>
    </div>

    <!-- Term Filter Bar (Screen Only) -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs no-print">
        <form method="GET" action="{{ route('student.grades.index') }}" class="flex items-center gap-3">
            <div class="w-full sm:w-72">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('messages.select_term') }}</label>
                <select name="term_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    @foreach($examTerms as $term)
                        <option value="{{ $term->id }}" {{ $selectedTerm->id == $term->id ? 'selected' : '' }}>
                            {{ $term->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Official Report Card Container (Ready for Print & Web) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 sm:p-10 space-y-8" id="reportCardSection">
        <!-- Institution Header -->
        <div class="border-b-2 border-blue-900/20 pb-6 text-center space-y-2">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-blue-900 items-center justify-center text-white shadow-md mb-2">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-blue-950 uppercase tracking-tight">{{ __('messages.school_name') }}</h2>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Official Academic Transcript & Progress Report</p>
            <p class="text-xs text-blue-900 font-bold bg-blue-50 inline-block px-3 py-1 rounded-full border border-blue-200 mt-2">
                {{ $selectedTerm->name }} • Academic Session 2026-2027
            </p>
        </div>

        <!-- Student Particulars Grid -->
        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block font-semibold">{{ __('messages.student_name') }}</span>
                <span class="font-bold text-slate-900 text-sm">{{ $student->user->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">{{ __('messages.admission_number') }}</span>
                <span class="font-bold text-blue-900 text-sm">{{ $student->admission_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">{{ __('messages.class_section') }}</span>
                <span class="font-bold text-slate-900 text-sm">{{ $enrollment?->schoolClass->name }} ({{ $enrollment?->section->name }})</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">{{ __('messages.roll_number') }}</span>
                <span class="font-bold text-slate-900 text-sm">#{{ $enrollment?->roll_number }}</span>
            </div>
        </div>

        <!-- Subject Grades Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-blue-950 text-white font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Subject Code</th>
                        <th class="px-6 py-3.5">Subject Name</th>
                        <th class="px-6 py-3.5 text-center">Full Marks</th>
                        <th class="px-6 py-3.5 text-center">Marks Obtained</th>
                        <th class="px-6 py-3.5 text-center">{{ __('messages.letter_grade') }}</th>
                        <th class="px-6 py-3.5 text-center">{{ __('messages.grade_point') }}</th>
                        <th class="px-6 py-3.5">Evaluator Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($grades as $g)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-blue-900 whitespace-nowrap">{{ $g->subject->code }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $g->subject->name }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ $g->total_marks }}</td>
                            <td class="px-6 py-4 text-center font-extrabold text-blue-950">{{ $g->marks_obtained }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded text-xs font-black bg-emerald-100 text-emerald-800">
                                    {{ $g->letter_grade }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-slate-900">{{ number_format($g->grade_point, 2) }}</td>
                            <td class="px-6 py-4 text-slate-500 italic">{{ $g->remarks ?? 'Satisfactory' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                Marks have not been finalized for this examination term.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Composite Performance Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-slate-200">
            <div class="p-5 rounded-2xl bg-blue-50 border border-blue-200 text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-800 block">{{ __('messages.total_score') }}</span>
                <span class="text-2xl sm:text-3xl font-black text-blue-950 mt-1 block">
                    {{ $totalMarksObtained }} / {{ $totalPossibleMarks }}
                </span>
                <span class="text-xs font-semibold text-blue-700 mt-1 block">Percentage: {{ $averagePercentage }}%</span>
            </div>

            <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">{{ __('messages.gpa') }}</span>
                <span class="text-2xl sm:text-3xl font-black text-emerald-900 mt-1 block">
                    {{ number_format($gpa, 2) }}
                </span>
                <span class="text-xs font-semibold text-emerald-700 mt-1 block">Scale: 5.00 Maximum</span>
            </div>

            <div class="p-5 rounded-2xl bg-purple-50 border border-purple-200 text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-800 block">Assessment Verdict</span>
                <span class="text-2xl sm:text-3xl font-black text-purple-950 mt-1 block">
                    {{ $gpa >= 3.5 ? 'Passed with Honors' : ($gpa >= 2.0 ? 'Passed' : 'Pending') }}
                </span>
                <span class="text-xs font-semibold text-purple-700 mt-1 block">Institutional Standing: Excellent</span>
            </div>
        </div>

        <!-- Official Signatures Block (For Printout / PDF) -->
        <div class="pt-12 grid grid-cols-3 gap-8 text-center text-xs text-slate-500 border-t border-slate-200">
            <div>
                <div class="w-32 border-b border-slate-400 mx-auto mb-2"></div>
                <p class="font-bold text-slate-800">Class Mentor Teacher</p>
                <p class="text-[10px] text-slate-400">Signature & Date</p>
            </div>
            <div>
                <div class="w-32 border-b border-slate-400 mx-auto mb-2"></div>
                <p class="font-bold text-slate-800">Controller of Examinations</p>
                <p class="text-[10px] text-slate-400">Institutional Seal</p>
            </div>
            <div>
                <div class="w-32 border-b border-slate-400 mx-auto mb-2"></div>
                <p class="font-bold text-slate-800">Principal / Headmaster</p>
                <p class="text-[10px] text-slate-400">Approved & Issued</p>
            </div>
        </div>
    </div>
</div>
@endsection
