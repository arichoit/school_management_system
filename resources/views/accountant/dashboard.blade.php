@extends('layouts.portal')

@section('title', __('messages.accountant_dashboard'))
@section('breadcrumb', __('messages.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.accountant_dashboard') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Financial operations, collection summaries, and payment reconciliation.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('accountant.fees.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-sm transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>{{ __('messages.record_payment') }}</span>
            </a>
        </div>
    </div>

    <!-- 4 Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Invoiced -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.total_billed') }}</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-blue-950 mt-1">৳{{ number_format($totalBilled, 2) }}</p>
            <p class="text-xs text-slate-400 mt-2">All active student invoices</p>
        </div>

        <!-- Total Collected -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.total_collected') }}</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">৳{{ number_format($totalCollected, 2) }}</p>
            <p class="text-xs text-emerald-600 font-semibold mt-2">
                {{ round(($totalCollected / max(1, $totalBilled)) * 100, 1) }}% realization rate
            </p>
        </div>

        <!-- Outstanding Dues -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('messages.outstanding_dues') }}</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">৳{{ number_format($outstandingDues, 2) }}</p>
            <p class="text-xs text-slate-400 mt-2">Awaiting guardian settlement</p>
        </div>

        <!-- Today's Collection -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Today's Inflow</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-blue-900 mt-1">৳{{ number_format($todayCollected, 2) }}</p>
            <p class="text-xs text-blue-600 font-semibold mt-2">{{ count($todayPayments) }} transaction(s) today</p>
        </div>
    </div>

    <!-- Collection Breakdown by Payment Channel -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <h3 class="text-base font-bold text-slate-900 mb-4">Collection Volume by Payment Gateway & Mode</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div class="p-4 rounded-xl border border-pink-200 bg-pink-50/50">
                <span class="text-xs font-bold text-pink-700 block">bKash Mobile</span>
                <span class="text-xl font-extrabold text-slate-900 mt-1 block">৳{{ number_format($methodStats['bkash'] ?? 0, 2) }}</span>
            </div>
            <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50">
                <span class="text-xs font-bold text-blue-700 block">SSLCommerz</span>
                <span class="text-xl font-extrabold text-slate-900 mt-1 block">৳{{ number_format($methodStats['sslcommerz'] ?? 0, 2) }}</span>
            </div>
            <div class="p-4 rounded-xl border border-indigo-200 bg-indigo-50/50">
                <span class="text-xs font-bold text-indigo-700 block">Stripe Card</span>
                <span class="text-xl font-extrabold text-slate-900 mt-1 block">৳{{ number_format($methodStats['stripe'] ?? 0, 2) }}</span>
            </div>
            <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50">
                <span class="text-xs font-bold text-emerald-700 block">Cash Counter</span>
                <span class="text-xl font-extrabold text-slate-900 mt-1 block">৳{{ number_format($methodStats['cash'] ?? 0, 2) }}</span>
            </div>
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <span class="text-xs font-bold text-slate-700 block">Bank Transfer</span>
                <span class="text-xl font-extrabold text-slate-900 mt-1 block">৳{{ number_format($methodStats['bank'] ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Daily Collection Log / Recent Transactions -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">{{ __('messages.daily_collection') }}</h3>
                <p class="text-xs text-slate-400">Reconciled receipts across cash counter and online gateways</p>
            </div>
            <a href="{{ route('accountant.fees.index') }}" class="text-xs font-bold text-blue-900 hover:underline">
                View All →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">{{ __('messages.receipt_no') }}</th>
                        <th class="px-6 py-3.5">{{ __('messages.invoice_no') }}</th>
                        <th class="px-6 py-3.5">Student Account</th>
                        <th class="px-6 py-3.5">{{ __('messages.amount') }}</th>
                        <th class="px-6 py-3.5">{{ __('messages.method') }}</th>
                        <th class="px-6 py-3.5">Txn Reference</th>
                        <th class="px-6 py-3.5">{{ __('messages.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($recentPayments as $pay)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-blue-900 whitespace-nowrap">{{ $pay->receipt_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-600 whitespace-nowrap">{{ $pay->invoice?->invoice_number }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900">{{ $pay->invoice?->student->user->name ?? 'Student' }}</span>
                            </td>
                            <td class="px-6 py-4 font-extrabold text-emerald-700 whitespace-nowrap">৳{{ number_format($pay->amount, 2) }}</td>
                            <td class="px-6 py-4 capitalize whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                    {{ $pay->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                {{ $pay->transaction_id ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $pay->paid_at ? $pay->paid_at->format('M d, Y h:i A') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">No collection receipts logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
