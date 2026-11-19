@extends('layouts.portal')

@section('title', __('messages.timetable_matrix'))
@section('breadcrumb', __('messages.timetable_matrix'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.timetable_matrix') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Institutional weekly routine, subject allotment, and lecture schedules.</p>
        </div>
    </div>

    <!-- Filters Bar (Class & Section) -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.academics.timetable') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-64">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Select Class</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $activeClass = $classes->firstWhere('id', $selectedClassId) ?? $classes->first();
            @endphp

            @if($activeClass && $activeClass->sections->isNotEmpty())
                <div class="w-full sm:w-64">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Select Section</label>
                    <select name="section_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                        @foreach($activeClass->sections as $sec)
                            <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>
                                {{ $sec->name }} ({{ $sec->room_number ?? 'Main Room' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </form>
    </div>

    <!-- Timetable Matrix by Days -->
    <div class="space-y-6">
        @foreach($days as $day)
            @php
                $daySlots = $timetables->where('day_of_week', $day);
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-900"></span>
                        <h3 class="text-sm font-extrabold text-blue-950 uppercase tracking-wider">{{ $day }}</h3>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ $daySlots->count() }} Periods scheduled</span>
                </div>

                @if($daySlots->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-5">
                        @foreach($daySlots as $slot)
                            <div class="p-4 rounded-xl border border-slate-200 bg-white hover:border-blue-900/40 hover:shadow-sm transition space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-900 border border-blue-200">
                                        {{ $slot->start_time }} - {{ $slot->end_time }}
                                    </span>
                                    <span class="text-slate-400 text-[11px]">Room {{ $slot->room_number ?? '201' }}</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-extrabold text-slate-900">{{ $slot->subject->name }}</h4>
                                    <span class="text-[11px] font-semibold text-slate-400">Code: {{ $slot->subject->code }}</span>
                                </div>
                                <div class="pt-2 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-600">
                                    <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-700">
                                        {{ substr($slot->teacher->name, 0, 1) }}
                                    </div>
                                    <span class="truncate font-medium">{{ $slot->teacher->name }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-xs text-slate-400 italic">
                        No scheduled periods configured for {{ $day }}.
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
