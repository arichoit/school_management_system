@extends('layouts.portal')

@section('title', __('messages.fee_structure_setup'))
@section('breadcrumb', __('messages.fee_structure_setup'))

@section('content')
<div class="space-y-6" 
     x-data="{
         feeModalOpen: false,
         recordModalOpen: false,
         selectedInvoiceId: '',
         selectedInvoiceDue: 0,
         setInvoice(inv) {
             this.selectedInvoiceId = inv.id;
             this.selectedInvoiceDue = inv.due_amount;
             this.recordModalOpen = true;
         }
     }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.fee_structure_setup') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Manage institutional fee rates, log manual payments, and reconcile gateway settlement.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="feeModalOpen = true" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white text-slate-800 border border-slate-300 hover:bg-slate-50 shadow-xs transition">
                <svg class="w-4 h-4 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>New Fee Structure</span>
            </button>
            <button @click="recordModalOpen = true" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-sm transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>{{ __('messages.record_payment') }}</span>
            </button>
        </div>
    </div>

    <!-- Active Fee Structures List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900">Configured Institutional Fee Structures</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($feeTypes as $ft)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-900">
                        {{ $ft->schoolClass ? $ft->schoolClass->name : 'All Classes' }}
                    </span>
                    <h4 class="text-sm font-extrabold text-slate-900 leading-tight">{{ $ft->title }}</h4>
                    <p class="text-lg font-black text-emerald-700">৳{{ number_format($ft->amount, 2) }}</p>
                    <p class="text-[11px] text-slate-400">Due: {{ $ft->due_date ? $ft->due_date->format('M d, Y') : 'Session-wide' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Invoices Directory Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('accountant.fees.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('messages.search') }} Invoice Number or Student Name"
                       class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
            </div>
            <div class="sm:col-span-4">
                <select name="status" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    <option value="">All Payment Statuses</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid Dues</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial Settlement</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Fully Settled (Paid)</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full py-2 px-3 text-xs font-bold rounded-xl bg-slate-800 text-white hover:bg-slate-700 transition">
                    {{ __('messages.filter') }}
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('accountant.fees.index') }}" class="py-2 px-3 text-xs font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Desktop Table (>= 768px) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">{{ __('messages.invoice_no') }}</th>
                        <th class="px-6 py-3.5">Student Account</th>
                        <th class="px-6 py-3.5">Fee Particulars</th>
                        <th class="px-6 py-3.5">Total Billed</th>
                        <th class="px-6 py-3.5">Collected</th>
                        <th class="px-6 py-3.5">Remaining Due</th>
                        <th class="px-6 py-3.5">{{ __('messages.status') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-blue-900 whitespace-nowrap">{{ $inv->invoice_number }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $inv->student->user->name }}</div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $inv->student->admission_number }} • {{ $inv->student->currentEnrollment?->schoolClass->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $inv->title }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">৳{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-emerald-700 font-semibold whitespace-nowrap">৳{{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-6 py-4 font-bold text-amber-700 whitespace-nowrap">৳{{ number_format($inv->due_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($inv->status === 'paid')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ __('messages.paid') }}
                                    </span>
                                @elseif($inv->status === 'partial')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        {{ __('messages.partial') }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        {{ __('messages.unpaid') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($inv->status !== 'paid')
                                    <button type="button" @click="setInvoice({{ json_encode($inv) }})"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 transition">
                                        Record Cash/Bank
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">✓ Settled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">No invoices found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Card View (< 768px) -->
        <div class="md:hidden divide-y divide-slate-200">
            @forelse($invoices as $inv)
                <div class="p-4 space-y-2 hover:bg-slate-50 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-blue-900">{{ $inv->invoice_number }}</span>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $inv->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $inv->student->user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $inv->title }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-500 pt-1">
                        <div>
                            <span>Total:</span> <strong class="text-slate-800">৳{{ number_format($inv->total_amount, 2) }}</strong>
                        </div>
                        <div>
                            <span>Due:</span> <strong class="text-amber-700">৳{{ number_format($inv->due_amount, 2) }}</strong>
                        </div>
                    </div>
                    @if($inv->status !== 'paid')
                        <div class="pt-2 flex justify-end">
                            <button type="button" @click="setInvoice({{ json_encode($inv) }})"
                                    class="w-full py-2 rounded-xl text-xs font-bold bg-blue-900 text-white">
                                Record Payment Receipt
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">No invoices found.</div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Modal 1: Create Fee Structure -->
    <div x-show="feeModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" 
         aria-modal="true" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="feeModalOpen" 
                 @click="feeModalOpen = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="feeModalOpen" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                
                <div class="bg-blue-900 px-6 py-4 flex items-center justify-between text-white">
                    <h3 class="text-base font-bold">New Fee Structure Definition</h3>
                    <button @click="feeModalOpen = false" class="text-blue-200 hover:text-white">✕</button>
                </div>

                <form action="{{ route('accountant.fees.structure.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Academic Session *</label>
                        <select name="academic_year_id" required class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Class (Optional)</label>
                        <select name="class_id" class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                            <option value="">All Academic Classes</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Fee Structure Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Science Lab & Robotics Fee 2026"
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Amount (৳) *</label>
                        <input type="number" step="0.01" min="0" name="amount" required placeholder="e.g. 3500.00"
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Due Date</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="feeModalOpen = false" class="px-4 py-2 border rounded-xl text-xs font-bold text-slate-600">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: Record Cash / Bank Payment -->
    <div x-show="recordModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" 
         aria-modal="true" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="recordModalOpen" 
                 @click="recordModalOpen = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="recordModalOpen" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                
                <div class="bg-blue-900 px-6 py-4 flex items-center justify-between text-white">
                    <h3 class="text-base font-bold">{{ __('messages.record_payment') }}</h3>
                    <button @click="recordModalOpen = false" class="text-blue-200 hover:text-white">✕</button>
                </div>

                <form action="{{ route('accountant.fees.record-payment') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Target Invoice *</label>
                        <select name="invoice_id" x-model="selectedInvoiceId" required 
                                class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                            <option value="">-- Choose student invoice --</option>
                            @foreach($invoices->where('status', '!=', 'paid') as $inv)
                                <option value="{{ $inv->id }}">
                                    {{ $inv->invoice_number }} - {{ $inv->student->user->name }} (Due: ৳{{ $inv->due_amount }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Collection Mode *</label>
                        <select name="payment_method" required class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                            <option value="cash">{{ __('messages.cash') }} (Institutional Cash Desk)</option>
                            <option value="bank">{{ __('messages.bank') }} (Direct Bank Deposit)</option>
                            <option value="bkash">{{ __('messages.bkash') }} (Manual bKash Ref)</option>
                            <option value="sslcommerz">{{ __('messages.sslcommerz') }} (Offline POS Card)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Amount Collected (৳) *</label>
                        <input type="number" step="0.01" min="1" name="amount" required placeholder="Enter received amount"
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bank Deposit / Cheque / Txn Slip Ref</label>
                        <input type="text" name="transaction_id" placeholder="e.g. SLIP-90812 or CHQ-4412"
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Accountant Notes</label>
                        <textarea name="notes" rows="2" placeholder="Manual verification notes"
                                  class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="recordModalOpen = false" class="px-4 py-2 border rounded-xl text-xs font-bold text-slate-600">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white">
                            Generate Official Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
