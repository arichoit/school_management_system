@extends('layouts.portal')

@section('title', __('messages.exam_grades'))
@section('breadcrumb', __('messages.exam_grades'))

@section('content')
<div class="space-y-6" 
     x-data="{
         totalMarks: {{ $selectedSubject->total_marks ?? 100 }},
         students: {{ json_encode($students) }},
         calcGrade(marks) {
             if (marks === '' || marks === null || isNaN(marks)) return { grade: '-', point: '-' };
             let pct = (parseFloat(marks) / this.totalMarks) * 100;
             if (pct >= 80) return { grade: 'A+', point: '5.00' };
             if (pct >= 70) return { grade: 'A', point: '4.00' };
             if (pct >= 60) return { grade: 'A-', point: '3.50' };
             if (pct >= 50) return { grade: 'B', point: '3.00' };
             if (pct >= 40) return { grade: 'C', point: '2.00' };
             if (pct >= 33) return { grade: 'D', point: '1.00' };
             return { grade: 'F', point: '0.00' };
         }
     }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.exam_grades') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Spreadsheet mark entry matrix with instant grade point evaluation and composite validation.</p>
        </div>
    </div>

    <!-- Filters Bar: Term, Subject, Class, Section -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('teacher.grades.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <!-- Exam Term -->
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('messages.select_term') }}</label>
                <select name="exam_term_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    @foreach($examTerms as $term)
                        <option value="{{ $term->id }}" {{ $selectedTermId == $term->id ? 'selected' : '' }}>
                            {{ $term->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Subject -->
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('messages.select_subject') }}</label>
                <select name="subject_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    @foreach($subjects as $sb)
                        <option value="{{ $sb->id }}" {{ $selectedSubjectId == $sb->id ? 'selected' : '' }}>
                            {{ $sb->name }} ({{ $sb->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Class -->
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Target Class</label>
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

            <!-- Section -->
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Section</label>
                <select name="section_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    @if($activeClass)
                        @foreach($activeClass->sections as $sec)
                            <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>
                                {{ $sec->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2 px-2 text-xs font-bold rounded-xl bg-slate-800 text-white hover:bg-slate-700 transition">
                    Load
                </button>
            </div>
        </form>
    </div>

    <!-- Active Subject Evaluation Metadata Banner -->
    @if($selectedSubject)
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-950 flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-2">
            <div>
                <span class="font-bold text-sm">{{ $selectedSubject->name }}</span>
                <span class="text-blue-700 ml-2">({{ $selectedSubject->code }})</span>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <span>Maximum Marks: <strong class="text-blue-950">{{ $selectedSubject->total_marks }}</strong></span>
                <span>•</span>
                <span>Pass Threshold: <strong class="text-emerald-700">{{ $selectedSubject->pass_marks }}</strong></span>
            </div>
        </div>
    @endif

    <!-- Mark Entry Form -->
    <form action="{{ route('teacher.grades.store') }}" method="POST">
        @csrf
        <input type="hidden" name="exam_term_id" value="{{ $selectedTermId }}">
        <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <!-- Desktop Spreadsheet Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">{{ __('messages.roll_number') }}</th>
                            <th class="px-6 py-3.5">{{ __('messages.student_name') }}</th>
                            <th class="px-6 py-3.5 w-48">{{ __('messages.marks_obtained') }} (Max: {{ $selectedSubject->total_marks ?? 100 }})</th>
                            <th class="px-6 py-3.5 text-center">{{ __('messages.letter_grade') }}</th>
                            <th class="px-6 py-3.5 text-center">{{ __('messages.grade_point') }}</th>
                            <th class="px-6 py-3.5">Evaluator Remarks</th>
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
                                <td class="px-6 py-4">
                                    <div class="relative">
                                        <input type="number" step="0.5" min="0" :max="totalMarks"
                                               :name="'marks[' + st.id + ']'" 
                                               x-model="st.marks_obtained"
                                               placeholder="0 - 100"
                                               class="w-full text-xs font-bold rounded-lg border border-slate-300 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900"
                                               :class="{
                                                   'border-rose-400 bg-rose-50 text-rose-900': parseFloat(st.marks_obtained) > totalMarks
                                               }">
                                        <span class="absolute right-3 top-2 text-[10px] text-slate-400">/ <span x-text="totalMarks"></span></span>
                                    </div>
                                    <template x-if="parseFloat(st.marks_obtained) > totalMarks">
                                        <span class="text-[10px] text-rose-600 font-bold block mt-0.5">Exceeds total!</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-extrabold"
                                          :class="{
                                              'bg-emerald-100 text-emerald-800': ['A+', 'A', 'A-'].includes(calcGrade(st.marks_obtained).grade),
                                              'bg-blue-100 text-blue-800': ['B', 'C'].includes(calcGrade(st.marks_obtained).grade),
                                              'bg-amber-100 text-amber-800': calcGrade(st.marks_obtained).grade === 'D',
                                              'bg-rose-100 text-rose-800': calcGrade(st.marks_obtained).grade === 'F',
                                              'bg-slate-100 text-slate-400': calcGrade(st.marks_obtained).grade === '-'
                                          }"
                                          x-text="calcGrade(st.marks_obtained).grade">
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800" x-text="calcGrade(st.marks_obtained).point"></td>
                                <td class="px-6 py-4">
                                    <input type="text" :name="'remarks[' + st.id + ']'" x-model="st.remarks" placeholder="Optional remark"
                                           class="w-full text-xs rounded-lg border border-slate-200 py-1.5 px-2.5 focus:outline-none focus:ring-1 focus:ring-blue-900">
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
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-xs font-bold"
                                      :class="{
                                          'bg-emerald-100 text-emerald-800': ['A+', 'A', 'A-'].includes(calcGrade(st.marks_obtained).grade),
                                          'bg-rose-100 text-rose-800': calcGrade(st.marks_obtained).grade === 'F',
                                          'bg-slate-100 text-slate-700': !['A+', 'A', 'A-', 'F'].includes(calcGrade(st.marks_obtained).grade)
                                      }"
                                      x-text="calcGrade(st.marks_obtained).grade">
                                </span>
                                <span class="text-xs font-bold text-slate-600" x-text="'GPA: ' + calcGrade(st.marks_obtained).point"></span>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-slate-900" x-text="st.name"></p>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1">Marks (Max <span x-text="totalMarks"></span>)</label>
                            <input type="number" step="0.5" min="0" :max="totalMarks"
                                   :name="'marks[' + st.id + ']'" 
                                   x-model="st.marks_obtained"
                                   class="w-full text-xs font-bold rounded-lg border border-slate-300 py-2 px-3">
                        </div>
                        <div>
                            <input type="text" :name="'remarks[' + st.id + ']'" x-model="st.remarks" placeholder="Optional remark"
                                   class="w-full text-xs rounded-lg border border-slate-200 py-1.5 px-2.5">
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Save -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-500">Marks are validated and calculated into GPA automatically.</span>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-md transition">
                    Save Examination Grades
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
