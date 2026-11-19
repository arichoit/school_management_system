@extends('layouts.portal')

@section('title', __('messages.homework_tasks'))
@section('breadcrumb', __('messages.homework_tasks'))

@section('content')
<div class="space-y-6">
    <!-- Header with Child Indicator -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.homework_tasks') }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                Assignments and submission tracking for <strong>{{ $activeChild->user->name }}</strong>
            </p>
        </div>
    </div>

    <!-- Homework Tasks Feed -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($homeworks as $hw)
            @php
                $submission = $hw->submissions->first();
                $isDueSoon = $hw->due_date && $hw->due_date->isFuture() && $hw->due_date->diffInDays(now()) <= 3;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4 hover:border-blue-900/40 transition flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-blue-100 text-blue-900">
                            {{ $hw->subject->name }} ({{ $hw->subject->code }})
                        </span>
                        @if($submission)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                ✓ Submitted
                            </span>
                        @elseif($isDueSoon)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                Due Soon
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                Pending
                            </span>
                        @endif
                    </div>

                    <h3 class="text-base font-extrabold text-slate-900 leading-snug">{{ $hw->title }}</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $hw->description }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        <span class="text-slate-400">Teacher:</span>
                        <strong class="text-slate-700 ml-1">{{ $hw->teacher->name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400">Due:</span>
                        <strong class="text-slate-800 ml-1">{{ $hw->due_date ? $hw->due_date->format('M d, Y') : 'Ongoing' }}</strong>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-2xl p-12 border border-slate-200 text-center text-slate-400 text-xs">
                No homework assignments currently posted for this section.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $homeworks->links() }}
    </div>
</div>
@endsection
