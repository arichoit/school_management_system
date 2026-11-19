@extends('layouts.portal')

@section('title', __('messages.admin_dashboard'))
@section('breadcrumb', __('messages.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.admin_dashboard') }}</h1>
            <p class="text-xs text-slate-500 mt-1">High-level institutional KPIs, academic operations, and revenue reconciliation.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.students.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-sm transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>{{ __('messages.admit_student') }}</span>
            </a>
            <a href="{{ route('admin.academics.classes') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Config</span>
            </a>
        </div>
    </div>

    <!-- 4 Primary KPI Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- KPI 1: Students -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-900/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.total_students') }}</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-blue-950 mt-1">{{ $totalStudents }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-900 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-emerald-600 font-semibold gap-1">
                <span>↑ 100% active</span>
                <span class="text-slate-400 font-normal">• Session 2026-2027</span>
            </div>
        </div>

        <!-- KPI 2: Teachers -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-900/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.total_teachers') }}</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-blue-950 mt-1">{{ $totalTeachers }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-900 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-purple-600 font-semibold gap-1">
                <span>Assigned Sections</span>
                <span class="text-slate-400 font-normal">• All certified</span>
            </div>
        </div>

        <!-- KPI 3: Fee Collection Rate -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-900/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.fee_collection_rate') }}</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $feeCollectionRate }}%</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-500 font-medium">
                ৳{{ number_format($totalCollected) }} of ৳{{ number_format($totalInvoiced) }} collected
            </div>
        </div>

        <!-- KPI 4: Active Attendance -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs hover:border-blue-900/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.active_attendance') }}</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-blue-900 mt-1">{{ $attendanceRate }}%</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-500 font-medium">
                Daily classroom tracking active
            </div>
        </div>
    </div>

    <!-- 2 Column Analytics & Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Class-wise Student Distribution (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Class Enrollment Matrix</h3>
                <span class="text-xs text-slate-400">Total 5 active standards</span>
            </div>
            <div class="space-y-4">
                @foreach($classes as $c)
                    @php
                        $pct = min(100, max(15, ($c->enrollments_count / max(1, $totalStudents)) * 100));
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-800">{{ $c->name }} ({{ $c->code }})</span>
                            <span class="text-slate-500">{{ $c->enrollments_count }} enrolled students</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-blue-900 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Institutional Actions (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900">Administrative Quick Actions</h3>
            <div class="grid grid-cols-1 gap-2.5">
                <a href="{{ route('admin.students.create') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-900 flex items-center justify-center font-bold">
                            +
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ __('messages.admit_student') }}</p>
                            <p class="text-[11px] text-slate-400">Enroll new student & link parent</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-900">→</span>
                </a>

                <a href="{{ route('admin.academics.timetable') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold">
                            📅
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ __('messages.timetable_matrix') }}</p>
                            <p class="text-[11px] text-slate-400">Review weekly schedules</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-900">→</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-900 flex items-center justify-center font-bold">
                            👥
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ __('messages.staff_management') }}</p>
                            <p class="text-[11px] text-slate-400">Manage user roles & permissions</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-900">→</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Admissions Directory Preview -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Recent Student Admissions</h3>
                <p class="text-xs text-slate-400">Newly registered students in the active academic session</p>
            </div>
            <a href="{{ route('admin.students.index') }}" class="text-xs font-bold text-blue-900 hover:underline">
                {{ __('messages.view_details') }} →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">{{ __('messages.admission_number') }}</th>
                        <th class="px-6 py-3">{{ __('messages.student_name') }}</th>
                        <th class="px-6 py-3">{{ __('messages.class_section') }}</th>
                        <th class="px-6 py-3">{{ __('messages.roll_number') }}</th>
                        <th class="px-6 py-3">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($recentStudents as $st)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3.5 font-bold text-blue-900">{{ $st->admission_number }}</td>
                            <td class="px-6 py-3.5">
                                <div class="font-bold text-slate-900">{{ $st->user->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $st->user->email }}</div>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-medium">{{ $st->currentEnrollment?->schoolClass->name }}</span>
                                <span class="text-slate-400 text-[11px]">• {{ $st->currentEnrollment?->section->name }}</span>
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">
                                #{{ $st->currentEnrollment?->roll_number }}
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    {{ __('messages.active') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
