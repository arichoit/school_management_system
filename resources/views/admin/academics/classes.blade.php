@extends('layouts.portal')

@section('title', __('messages.class_management'))
@section('breadcrumb', __('messages.class_management'))

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.class_management') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Configure academic classes, section allocations, and institutional subjects.</p>
        </div>
        <div>
            <button @click="createModalOpen = true" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-sm transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add New Class</span>
            </button>
        </div>
    </div>

    <!-- Classes & Sections Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($classes as $c)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-900 bg-blue-100 px-2 py-0.5 rounded">
                                Code: {{ $c->code }}
                            </span>
                            <h3 class="text-lg font-extrabold text-slate-900 mt-1">{{ $c->name }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-900 text-white flex items-center justify-center font-bold text-sm">
                            {{ $c->numeric_level }}
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Sections & Class Mentors</p>
                            <div class="space-y-2">
                                @forelse($c->sections as $sec)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-bold text-slate-900">{{ $sec->name }}</span>
                                            <span class="text-slate-400 text-[11px] block">{{ $sec->room_number ?? 'Room unassigned' }} • Cap: {{ $sec->capacity }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[11px] font-semibold text-blue-950 block">
                                                {{ $sec->classTeacher?->name ?? 'No Teacher' }}
                                            </span>
                                            <span class="text-[10px] text-slate-400">Class Mentor</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic">No sections created for this class yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Curriculum Subjects</p>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($c->subjects as $subj)
                                    <span class="px-2 py-1 bg-slate-100 border border-slate-200 text-slate-700 text-[11px] font-medium rounded-lg">
                                        {{ $subj->name }} ({{ $subj->code }})
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">Core subjects inherited</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-medium">{{ $c->sections->count() }} active section(s)</span>
                    <a href="{{ route('admin.academics.timetable', ['class_id' => $c->id]) }}" class="font-bold text-blue-900 hover:underline">
                        View Schedule →
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Institutional Subjects Directory -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Institutional Subjects Catalog</h3>
            <p class="text-xs text-slate-400">Master subject definitions with evaluation criteria and grading limits</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Subject Code</th>
                        <th class="px-6 py-3.5">Subject Name</th>
                        <th class="px-6 py-3.5">Evaluation Type</th>
                        <th class="px-6 py-3.5">Total Marks</th>
                        <th class="px-6 py-3.5">Pass Threshold</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @foreach($subjects as $sb)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-blue-900 whitespace-nowrap">{{ $sb->code }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $sb->name }}</td>
                            <td class="px-6 py-4 capitalize">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $sb->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $sb->total_marks }} Marks</td>
                            <td class="px-6 py-4 text-emerald-700 font-semibold">{{ $sb->pass_marks }} Marks</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Class Modal -->
    <div x-show="createModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" 
         aria-modal="true" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="createModalOpen" 
                 @click="createModalOpen = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="createModalOpen" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                
                <div class="bg-blue-900 px-6 py-4 flex items-center justify-between text-white">
                    <h3 class="text-base font-bold">Configure New Academic Class</h3>
                    <button @click="createModalOpen = false" class="text-blue-200 hover:text-white">✕</button>
                </div>

                <form action="{{ route('admin.academics.classes.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Class Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Class 11"
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Class Code *</label>
                        <input type="text" name="code" required placeholder="e.g. C11"
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Numeric Level *</label>
                        <input type="number" name="numeric_level" min="1" max="12" value="11" required
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 border rounded-xl text-xs font-bold text-slate-600">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-blue-900 text-white">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
