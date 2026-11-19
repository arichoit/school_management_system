@extends('layouts.public')

@section('content')
<div class="bg-blue-950 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight">{{ __('messages.notices') }}</h1>
        <p class="text-sm text-blue-200 mt-1">Official circulars, academic schedules, and public notices</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="space-y-4">
        @forelse($notices as $notice)
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-md transition">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        @php
                            $catBadge = match($notice->category) {
                                'academic' => 'bg-blue-100 text-blue-800',
                                'exam' => 'bg-amber-100 text-amber-800',
                                'holiday' => 'bg-emerald-100 text-emerald-800',
                                default => 'bg-slate-100 text-slate-700'
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider {{ $catBadge }}">
                            {{ $notice->category }}
                        </span>
                        <span class="text-xs text-slate-400">
                            {{ $notice->published_at ? $notice->published_at->format('M d, Y') : 'Recent' }}
                        </span>
                    </div>
                    <span class="text-xs text-slate-400">Target: {{ ucfirst($notice->target_role) }}</span>
                </div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">{{ $notice->title }}</h2>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $notice->content }}</p>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 border border-slate-200 text-center text-slate-400">
                No published notices found.
            </div>
        @endforelse

        <div class="pt-6">
            {{ $notices->links() }}
        </div>
    </div>
</div>
@endsection
