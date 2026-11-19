@extends('layouts.portal')

@section('title', __('messages.teacher_dashboard'))
@section('breadcrumb', __('messages.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.teacher_dashboard') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Classroom supervision, attendance marking, and academic grade evaluations.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('teacher.attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-sm transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Take Attendance</span>
            </a>
            <a href="{{ route('teacher.grades.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                <span>Enter Marks</span>
            </a>
        </div>
    </div>

    <!-- Pending Tasks Alerts -->
    @if(count($pendingAttendance) > 0)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 font-bold flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-800">Pending Action Required</h4>
                    <p class="text-xs text-amber-700 font-medium">
                        Daily classroom attendance for today has not been submitted for {{ count($pendingAttendance) }} assigned section(s).
                    </p>
                </div>
            </div>
            <a href="{{ route('teacher.attendance.index') }}" class="px-4 py-2 bg-amber-600 text-white text-xs font-bold rounded-xl hover:bg-amber-700 transition">
                Record Attendance Now
            </a>
        </div>
    @else
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold flex-shrink-0">
                ✓
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800">All Daily Tasks Up to Date</h4>
                <p class="text-xs text-emerald-700 font-medium">All attendance registers for assigned classes have been marked for today.</p>
            </div>
        </div>
    @endif

    <!-- Assigned Classes & Sections Grid -->
    <div>
        <h3 class="text-base font-bold text-slate-900 mb-4">{{ __('messages.assigned_classes') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($sections as $sec)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4 hover:border-blue-900/40 transition">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">Class Mentor</span>
                            <h4 class="text-lg font-extrabold text-slate-900">{{ $sec->schoolClass->name }} - {{ $sec->name }}</h4>
                        </div>
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-900 font-extrabold flex items-center justify-center text-xs">
                            {{ $sec->schoolClass->numeric_level }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-500">
                        <div>
                            <span class="text-slate-400 block">Classroom:</span>
                            <span class="font-bold text-slate-800">{{ $sec->room_number ?? 'Room 201' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Section Capacity:</span>
                            <span class="font-bold text-slate-800">{{ $sec->capacity }} Students</span>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <a href="{{ route('teacher.attendance.index', ['class_id' => $sec->class_id, 'section_id' => $sec->id]) }}" 
                           class="flex-1 text-center py-2 px-3 rounded-xl bg-blue-50 text-blue-900 text-xs font-bold hover:bg-blue-100 transition">
                            Attendance
                        </a>
                        <a href="{{ route('teacher.grades.index', ['class_id' => $sec->class_id, 'section_id' => $sec->id]) }}" 
                           class="flex-1 text-center py-2 px-3 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition">
                            Grades
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400 text-xs">
                    No classroom sections directly assigned to your mentor profile. You can still mark attendance by selecting classes above.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Upcoming Exam Schedules -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Active & Upcoming Examination Terms</h3>
            <p class="text-xs text-slate-400">Published examination periods requiring grade ledger submissions</p>
        </div>

        <div class="p-6">
            <div class="space-y-3">
                @foreach($upcomingExams as $exam)
                    <div class="p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition">
                        <div>
                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-100 text-purple-800 mb-1">Evaluation Term</span>
                            <h4 class="text-sm font-extrabold text-slate-900">{{ $exam->name }}</h4>
                            <p class="text-xs text-slate-400">
                                Duration: {{ $exam->start_date->format('M d') }} - {{ $exam->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <a href="{{ route('teacher.grades.index', ['exam_term_id' => $exam->id]) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 transition">
                            <span>Open Mark Entry</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
