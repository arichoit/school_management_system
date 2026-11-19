@extends('layouts.portal')

@section('title', __('messages.student_directory'))
@section('breadcrumb', __('messages.student_directory'))

@section('content')
<div class="space-y-6">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.student_directory') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Manage active enrollments, admission profiles, and guardian associations.</p>
        </div>
        <div>
            <a href="{{ route('admin.students.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-sm transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>{{ __('messages.admit_student') }}</span>
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="{{ __('messages.search') }} Name, Email or Admission No."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="sm:col-span-4">
                <select name="class_id" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900">
                    <option value="">All Academic Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full py-2 px-3 text-xs font-bold rounded-xl bg-slate-800 text-white hover:bg-slate-700 transition">
                    {{ __('messages.filter') }}
                </button>
                @if(request()->hasAny(['search', 'class_id']))
                    <a href="{{ route('admin.students.index') }}" class="py-2 px-3 text-xs font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Desktop Table (screens >= 768px) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">{{ __('messages.admission_number') }}</th>
                        <th class="px-6 py-3.5">{{ __('messages.student_name') }}</th>
                        <th class="px-6 py-3.5">{{ __('messages.class_section') }}</th>
                        <th class="px-6 py-3.5">{{ __('messages.roll_number') }}</th>
                        <th class="px-6 py-3.5">{{ __('messages.parent') }}</th>
                        <th class="px-6 py-3.5">{{ __('messages.gender') }}</th>
                        <th class="px-6 py-3.5">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($students as $st)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-blue-900 whitespace-nowrap">
                                {{ $st->admission_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $st->user->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $st->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-slate-900">{{ $st->currentEnrollment?->schoolClass->name }}</span>
                                <span class="text-slate-400">• {{ $st->currentEnrollment?->section->name }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                                #{{ $st->currentEnrollment?->roll_number }}
                            </td>
                            <td class="px-6 py-4">
                                @if($st->parents->isNotEmpty())
                                    <span class="font-medium text-slate-800">{{ $st->parents->first()->user->name }}</span>
                                    <span class="block text-[10px] text-slate-400">{{ $st->parents->first()->relationship }}</span>
                                @else
                                    <span class="text-slate-400 italic">Not linked</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 capitalize text-slate-600">
                                {{ $st->gender }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    {{ __('messages.active') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">No students found</p>
                                    <p class="text-xs text-slate-400">Try adjusting your search criteria or register a new student.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Card View (screens < 768px) -->
        <div class="md:hidden divide-y divide-slate-200">
            @forelse($students as $st)
                <div class="p-4 space-y-2 hover:bg-slate-50 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-blue-900 bg-blue-50 px-2 py-0.5 rounded">
                            {{ $st->admission_number }}
                        </span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            {{ __('messages.active') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $st->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $st->user->email }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-slate-100">
                        <div>
                            <span class="text-slate-400">{{ __('messages.class_section') }}:</span>
                            <span class="font-bold text-slate-800">{{ $st->currentEnrollment?->schoolClass->name }} ({{ $st->currentEnrollment?->section->name }})</span>
                        </div>
                        <div>
                            <span class="text-slate-400">{{ __('messages.roll_number') }}:</span>
                            <span class="font-bold text-slate-800">#{{ $st->currentEnrollment?->roll_number }}</span>
                        </div>
                    </div>
                    @if($st->parents->isNotEmpty())
                        <div class="text-xs text-slate-500 pt-1">
                            <span class="text-slate-400">Guardian:</span> {{ $st->parents->first()->user->name }} ({{ $st->parents->first()->relationship }})
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    No students match the criteria.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
