@extends('layouts.portal')

@section('title', __('messages.daily_attendance'))
@section('breadcrumb', __('messages.daily_attendance'))

@section('content')
<div class="space-y-6 pb-20 sm:pb-6" 
     x-data="{
         students: {{ json_encode($students) }},
         get counts() {
             let p = 0, a = 0, l = 0, e = 0;
             this.students.forEach(s => {
                 if (s.status === 'present') p++;
                 else if (s.status === 'absent') a++;
                 else if (s.status === 'late') l++;
                 else if (s.status === 'excused') e++;
             });
             return { present: p, absent: a, late: l, excused: e, total: this.students.length };
         },
         markAll(status) {
             this.students.forEach(s => s.status = status);
         }
     }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.daily_attendance') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Mobile-optimized classroom attendance roster with instantaneous batch synchronization.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" @click="markAll('present')" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition flex items-center gap-1.5 border border-emerald-200">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ __('messages.mark_all_present') }}</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar: Class, Section, Date -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('teacher.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
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

            <div class="sm:col-span-4">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Select Section</label>
                <select name="section_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    @if($activeClass)
                        @foreach($activeClass->sections as $sec)
                            <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>
                                {{ $sec->name }} ({{ $sec->room_number ?? 'Room 201' }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Attendance Date</label>
                <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                       class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2 px-2 text-xs font-bold rounded-xl bg-slate-800 text-white hover:bg-slate-700 transition">
                    Go
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Live Statistics Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
        <div class="p-3 bg-white border border-slate-200 rounded-xl shadow-xs">
            <span class="text-xs text-slate-400 font-bold block">Enrolled Total</span>
            <span class="text-xl font-extrabold text-slate-900" x-text="counts.total"></span>
        </div>
        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl shadow-xs">
            <span class="text-xs text-emerald-700 font-bold block">{{ __('messages.present') }}</span>
            <span class="text-xl font-extrabold text-emerald-800" x-text="counts.present"></span>
        </div>
        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl shadow-xs">
            <span class="text-xs text-rose-700 font-bold block">{{ __('messages.absent') }}</span>
            <span class="text-xl font-extrabold text-rose-800" x-text="counts.absent"></span>
        </div>
        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl shadow-xs">
            <span class="text-xs text-amber-700 font-bold block">{{ __('messages.late') }}</span>
            <span class="text-xl font-extrabold text-amber-800" x-text="counts.late"></span>
        </div>
    </div>

    <!-- Attendance Form for Batch Submit -->
    <form action="{{ route('teacher.attendance.store') }}" method="POST" id="attendanceForm">
        @csrf
        <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
        <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">
        <input type="hidden" name="date" value="{{ $selectedDate }}">

        <!-- Data Container -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <!-- Desktop Table (>= 768px) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">{{ __('messages.roll_number') }}</th>
                            <th class="px-6 py-3.5">{{ __('messages.student_name') }}</th>
                            <th class="px-6 py-3.5 text-center">Attendance Status Selection</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        <template x-for="(st, index) in students" :key="st.id">
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-bold text-blue-900 whitespace-nowrap">
                                    #<span x-text="st.roll_number"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900" x-text="st.name"></div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 space-x-1">
                                        <!-- Present -->
                                        <label class="cursor-pointer">
                                            <input type="radio" :name="'attendance[' + st.id + ']'" value="present" x-model="st.status" class="sr-only">
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1"
                                                  :class="st.status === 'present' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-emerald-700'">
                                                ✓ {{ __('messages.present') }}
                                            </span>
                                        </label>
                                        <!-- Absent -->
                                        <label class="cursor-pointer">
                                            <input type="radio" :name="'attendance[' + st.id + ']'" value="absent" x-model="st.status" class="sr-only">
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1"
                                                  :class="st.status === 'absent' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-rose-700'">
                                                ✗ {{ __('messages.absent') }}
                                            </span>
                                        </label>
                                        <!-- Late -->
                                        <label class="cursor-pointer">
                                            <input type="radio" :name="'attendance[' + st.id + ']'" value="late" x-model="st.status" class="sr-only">
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1"
                                                  :class="st.status === 'late' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-amber-700'">
                                                ⏱ {{ __('messages.late') }}
                                            </span>
                                        </label>
                                        <!-- Excused -->
                                        <label class="cursor-pointer">
                                            <input type="radio" :name="'attendance[' + st.id + ']'" value="excused" x-model="st.status" class="sr-only">
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1"
                                                  :class="st.status === 'excused' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-blue-700'">
                                                📝 {{ __('messages.excused') }}
                                            </span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Stacked Card View (< 768px) -->
            <div class="md:hidden divide-y divide-slate-200">
                <template x-for="(st, index) in students" :key="st.id">
                    <div class="p-4 space-y-3 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-900 bg-blue-50 px-2 py-0.5 rounded">
                                Roll #<span x-text="st.roll_number"></span>
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wider"
                                  :class="{
                                      'text-emerald-700': st.status === 'present',
                                      'text-rose-700': st.status === 'absent',
                                      'text-amber-700': st.status === 'late',
                                      'text-blue-700': st.status === 'excused'
                                  }"
                                  x-text="st.status">
                            </span>
                        </div>
                        <p class="text-sm font-bold text-slate-900" x-text="st.name"></p>
                        <!-- Mobile Touch Buttons -->
                        <div class="grid grid-cols-4 gap-1.5 pt-1">
                            <button type="button" @click="st.status = 'present'"
                                    class="py-2 px-1 text-center text-xs font-bold rounded-lg border transition"
                                    :class="st.status === 'present' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-50 text-slate-700 border-slate-200'">
                                Present
                            </button>
                            <button type="button" @click="st.status = 'absent'"
                                    class="py-2 px-1 text-center text-xs font-bold rounded-lg border transition"
                                    :class="st.status === 'absent' ? 'bg-rose-600 text-white border-rose-600' : 'bg-slate-50 text-slate-700 border-slate-200'">
                                Absent
                            </button>
                            <button type="button" @click="st.status = 'late'"
                                    class="py-2 px-1 text-center text-xs font-bold rounded-lg border transition"
                                    :class="st.status === 'late' ? 'bg-amber-500 text-white border-amber-500' : 'bg-slate-50 text-slate-700 border-slate-200'">
                                Late
                            </button>
                            <button type="button" @click="st.status = 'excused'"
                                    class="py-2 px-1 text-center text-xs font-bold rounded-lg border transition"
                                    :class="st.status === 'excused' ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200'">
                                Leave
                            </button>
                            <!-- Hidden inputs for form submit on mobile -->
                            <input type="hidden" :name="'attendance[' + st.id + ']'" :value="st.status">
                        </div>
                    </div>
                </template>

                <template x-if="students.length === 0">
                    <div class="p-8 text-center text-slate-400 text-xs">
                        No students enrolled in this section.
                    </div>
                </template>
            </div>

            <!-- Desktop Submit Footer -->
            <div class="hidden sm:flex px-6 py-4 bg-slate-50 border-t border-slate-200 items-center justify-between">
                <span class="text-xs text-slate-500">
                    Changes are committed in a single secure institutional database transaction.
                </span>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-md transition">
                    {{ __('messages.save_attendance') }}
                </button>
            </div>
        </div>

        <!-- Mobile Bottom Sticky Action Bar (<640px) -->
        <div class="sm:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur border-t border-slate-300 p-3 shadow-2xl z-40 flex items-center justify-between gap-3">
            <div class="text-xs leading-tight">
                <span class="text-emerald-700 font-extrabold" x-text="counts.present + 'P'"></span> • 
                <span class="text-rose-700 font-extrabold" x-text="counts.absent + 'A'"></span> • 
                <span class="text-amber-700 font-extrabold" x-text="counts.late + 'L'"></span>
                <span class="block text-[10px] text-slate-400">Total: <span x-text="counts.total"></span></span>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-md transition">
                {{ __('messages.save_attendance') }}
            </button>
        </div>
    </form>
</div>
@endsection
