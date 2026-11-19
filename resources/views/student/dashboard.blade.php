@extends('layouts.portal')

@section('title', __('messages.student_dashboard'))
@section('breadcrumb', __('messages.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-400 text-slate-950 uppercase tracking-wider">
                Student Profile
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-blue-200">
                Admission: <strong>{{ $student->admission_number }}</strong> • 
                Class: <strong>{{ $enrollment?->schoolClass->name }} ({{ $enrollment?->section->name }})</strong> • 
                Roll: <strong>#{{ $enrollment?->roll_number }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('student.grades.index') }}" 
               class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950 hover:bg-emerald-400 shadow-md transition">
                {{ __('messages.report_card') }} →
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Attendance Ring -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.my_attendance') }}</p>
                <p class="text-3xl font-extrabold text-blue-950 mt-1">{{ $attendancePercentage }}%</p>
                <p class="text-[11px] text-emerald-600 font-bold mt-1">Present {{ $presentCount }} / {{ $totalAttendance }} days</p>
            </div>
            <div class="w-16 h-16 rounded-full border-4 border-emerald-500 flex items-center justify-center text-xs font-extrabold text-emerald-700 bg-emerald-50">
                {{ $attendancePercentage }}%
            </div>
        </div>

        <!-- Class Mentor -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Class Mentor Teacher</p>
            <p class="text-lg font-extrabold text-slate-900 mt-2">
                {{ $enrollment?->section->classTeacher?->name ?? 'Faculty Office' }}
            </p>
            <p class="text-xs text-slate-500 mt-1">Room {{ $enrollment?->section->room_number ?? '201' }}</p>
        </div>

        <!-- Academic Calendar -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Academic Term</p>
            <p class="text-lg font-extrabold text-blue-900 mt-2">Term 1 Assessment</p>
            <p class="text-xs text-slate-500 mt-1">Regular classes in session</p>
        </div>
    </div>

    <!-- Today's Schedule & Weekly Schedule Matrix -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">{{ __('messages.my_schedule') }}</h3>
                <p class="text-xs text-slate-400">Classes for {{ date('l') }}</p>
            </div>
            <span class="text-xs font-bold text-blue-900 bg-blue-50 px-2.5 py-1 rounded-lg">
                {{ $todaySchedule->count() }} Periods Today
            </span>
        </div>

        <div class="p-6">
            @if($todaySchedule->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($todaySchedule as $period)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200">
                                {{ $period->start_time }} - {{ $period->end_time }}
                            </span>
                            <h4 class="text-sm font-extrabold text-slate-900">{{ $period->subject->name }}</h4>
                            <p class="text-xs text-slate-500 font-medium">{{ $period->teacher->name }}</p>
                            <span class="text-[11px] text-slate-400 block pt-1 border-t border-slate-200">Room {{ $period->room_number ?? '201' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-xs text-slate-400 italic">
                    No scheduled lectures recorded for today (weekend or holiday).
                </div>
            @endif
        </div>
    </div>

    <!-- 2 Column Widgets: Homework & Notices -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assigned Homework -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Current Homework</h3>
                <span class="text-xs text-slate-400">{{ $homeworks->count() }} assignments</span>
            </div>

            <div class="space-y-3">
                @forelse($homeworks as $hw)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-blue-900">{{ $hw->subject->name }}</span>
                            <span class="text-amber-700 font-semibold text-[11px]">Due {{ $hw->due_date ? $hw->due_date->format('M d') : 'Soon' }}</span>
                        </div>
                        <p class="text-xs font-bold text-slate-800">{{ $hw->title }}</p>
                        <p class="text-xs text-slate-500 line-clamp-1">{{ $hw->description }}</p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">No pending assignments.</p>
                @endforelse
            </div>
        </div>

        <!-- School Notices -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Official Notices</h3>
                <a href="{{ route('public.notices') }}" class="text-xs font-bold text-blue-900 hover:underline">View All →</a>
            </div>

            <div class="space-y-3">
                @foreach($notices as $nt)
                    <div class="p-3.5 rounded-xl border border-slate-200 space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">{{ $nt->category }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $nt->published_at ? $nt->published_at->format('M d') : '' }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900">{{ $nt->title }}</h4>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
