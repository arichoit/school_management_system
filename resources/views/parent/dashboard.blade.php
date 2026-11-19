@extends('layouts.portal')

@section('title', __('messages.parent_dashboard'))
@section('breadcrumb', __('messages.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Header with Multi-Child Switcher Banner -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950 uppercase tracking-wider">
                Guardian Portal
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                {{ $activeChild->user->name }}
            </h1>
            <p class="text-xs sm:text-sm text-blue-200">
                Admission: <strong>{{ $activeChild->admission_number }}</strong> • 
                Class: <strong>{{ $activeChild->currentEnrollment?->schoolClass->name }} ({{ $activeChild->currentEnrollment?->section->name }})</strong> • 
                Roll: <strong>#{{ $activeChild->currentEnrollment?->roll_number }}</strong>
            </p>
        </div>

        <!-- Child Switcher Card -->
        @if($children->count() > 1)
            <div class="bg-white/10 backdrop-blur border border-white/20 p-4 rounded-2xl flex-shrink-0">
                <span class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-2">
                    {{ __('messages.select_child') }}
                </span>
                <div class="flex items-center gap-2">
                    @foreach($children as $child)
                        <form action="{{ route('parent.switch-child', $child->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $activeChild->id === $child->id ? 'bg-amber-400 text-slate-950 shadow-md' : 'bg-blue-800 text-white hover:bg-blue-700' }}">
                                {{ $child->user->name }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Attendance Percentage -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.attendance_rate') }}</p>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-blue-950">{{ $attendancePercentage }}%</span>
                <span class="text-xs text-emerald-600 font-bold">Good Standing</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden">
                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $attendancePercentage }}%"></div>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Present {{ $presentCount }} of {{ $totalAttendance }} tracked days</p>
        </div>

        <!-- Pending Fee Invoices -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending Fee Dues</p>
            @php
                $totalDue = $unpaidInvoices->sum('due_amount');
            @endphp
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold {{ $totalDue > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                    ৳{{ number_format($totalDue) }}
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-3">
                {{ $unpaidInvoices->count() }} invoice(s) outstanding
            </p>
            @if($totalDue > 0)
                <a href="{{ route('parent.fees.index') }}" class="inline-block mt-2 text-xs font-bold text-blue-900 hover:underline">
                    {{ __('messages.pay_now') }} →
                </a>
            @endif
        </div>

        <!-- Recent Term GPA -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Term Performance</p>
            @php
                $gpa = $recentGrades->count() > 0 ? round($recentGrades->avg('grade_point'), 2) : 5.00;
            @endphp
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-blue-900">{{ number_format($gpa, 2) }}</span>
                <span class="text-xs font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded">GPA / 5.0</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-3">{{ $recentGrades->count() }} subjects evaluated</p>
        </div>

        <!-- Homework Assigned -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Assignments</p>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $recentHomework->count() }}</span>
                <span class="text-xs text-slate-500">Tasks</span>
            </div>
            <a href="{{ route('parent.homework.index') }}" class="inline-block mt-3 text-xs font-bold text-blue-900 hover:underline">
                View Homework →
            </a>
        </div>
    </div>

    <!-- 2 Column Widgets: Recent Grades & Fee Invoices -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Recent Grades Widget (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ __('messages.recent_grades') }}</h3>
                    <p class="text-xs text-slate-400">Continuous assessments and mid-term marks</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentGrades as $g)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                        <div>
                            <span class="font-extrabold text-sm text-slate-900 block">{{ $g->subject->name }}</span>
                            <span class="text-xs text-slate-400">{{ $g->examTerm->name }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-slate-600 font-semibold">{{ $g->marks_obtained }} / {{ $g->total_marks }}</span>
                            <span class="px-2.5 py-1 rounded-md text-xs font-extrabold bg-emerald-100 text-emerald-800">
                                {{ $g->letter_grade }} ({{ $g->grade_point }})
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        No examination records uploaded yet for this session.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Invoices & Gateways Widget (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Tuition Invoices</h3>
                <a href="{{ route('parent.fees.index') }}" class="text-xs font-bold text-blue-900 hover:underline">
                    All Invoices →
                </a>
            </div>

            <div class="space-y-3">
                @forelse($unpaidInvoices as $inv)
                    <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/50 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-900">{{ $inv->invoice_number }}</span>
                            <span class="text-xs font-extrabold text-amber-700">Due: ৳{{ number_format($inv->due_amount) }}</span>
                        </div>
                        <p class="text-xs text-slate-700 font-medium">{{ $inv->title }}</p>
                        <div class="pt-2 flex justify-end">
                            <a href="{{ route('parent.fees.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 transition">
                                {{ __('messages.pay_now') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-xl">
                        ✓ All tuition invoices have been cleared!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
