@extends('layouts.portal')

@section('title', __('messages.fee_invoices'))
@section('breadcrumb', __('messages.fee_invoices'))

@section('content')
<div class="space-y-6" 
     x-data="{
         payModalOpen: false,
         selectedInvoice: null,
         paymentMethod: 'bkash',
         payAmount: 0,
         openPay(inv) {
             this.selectedInvoice = inv;
             this.payAmount = inv.due_amount;
             this.payModalOpen = true;
         }
     }">

    <!-- Header with Child Indicator -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.fee_invoices') }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                Showing tuition & fee history for <strong>{{ $activeChild->user->name }}</strong> ({{ $activeChild->admission_number }})
            </p>
        </div>
    </div>

    <!-- Invoices Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Desktop Table (>= 768px) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">{{ __('messages.invoice_no') }}</th>
                        <th class="px-6 py-3.5">Fee Title</th>
                        <th class="px-6 py-3.5">Total Amount</th>
                        <th class="px-6 py-3.5">Paid Amount</th>
                        <th class="px-6 py-3.5">Remaining Due</th>
                        <th class="px-6 py-3.5">Due Date</th>
                        <th class="px-6 py-3.5">{{ __('messages.status') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-blue-900 whitespace-nowrap">{{ $inv->invoice_number }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $inv->title }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">৳{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-emerald-700 font-semibold">৳{{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-6 py-4 font-bold text-amber-700">৳{{ number_format($inv->due_amount, 2) }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $inv->due_date ? $inv->due_date->format('M d, Y') : '-' }}</td>
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
                                    <button type="button" 
                                            @click="openPay({{ json_encode($inv) }})"
                                            class="px-4 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition">
                                        {{ __('messages.pay_now') }}
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">✓ Settled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">No invoices issued for this child.</td>
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
                        @if($inv->status === 'paid')
                            <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full">{{ __('messages.paid') }}</span>
                        @elseif($inv->status === 'partial')
                            <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">{{ __('messages.partial') }}</span>
                        @else
                            <span class="text-[10px] font-bold bg-rose-100 text-rose-800 px-2 py-0.5 rounded-full">{{ __('messages.unpaid') }}</span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-slate-900">{{ $inv->title }}</p>
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
                            <button type="button" @click="openPay({{ json_encode($inv) }})"
                                    class="w-full py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white">
                                {{ __('messages.pay_now') }}
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

    <!-- Pay Now Gateway Modal -->
    <div x-show="payModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" 
         aria-modal="true" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="payModalOpen" 
                 @click="payModalOpen = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="payModalOpen" 
                 class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                
                <div class="bg-blue-900 px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <h3 class="text-base font-bold">{{ __('messages.payment_modal_title') }}</h3>
                    </div>
                    <button @click="payModalOpen = false" class="text-blue-200 hover:text-white">✕</button>
                </div>

                <template x-if="selectedInvoice">
                    <form :action="'{{ url('parent/fees') }}/' + selectedInvoice.id + '/pay'" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                            <span class="text-xs text-slate-400" x-text="selectedInvoice.invoice_number"></span>
                            <h4 class="text-sm font-bold text-slate-900" x-text="selectedInvoice.title"></h4>
                            <div class="flex justify-between items-center text-xs pt-1">
                                <span class="text-slate-500">Remaining Balance:</span>
                                <span class="font-extrabold text-amber-700" x-text="'৳' + selectedInvoice.due_amount"></span>
                            </div>
                        </div>

                        <!-- Payment Amount -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Payment Amount (৳) *</label>
                            <input type="number" step="0.01" min="1" :max="selectedInvoice.due_amount" 
                                   name="amount" x-model="payAmount" required
                                   class="w-full text-sm font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                        </div>

                        <!-- Gateway Options -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('messages.select_payment_gateway') }} *</label>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- bKash -->
                                <label class="border rounded-2xl p-3 flex items-center space-x-3 cursor-pointer hover:border-pink-500 transition"
                                       :class="paymentMethod === 'bkash' ? 'border-pink-500 bg-pink-50 ring-2 ring-pink-500/30' : 'border-slate-200'">
                                    <input type="radio" name="payment_method" value="bkash" x-model="paymentMethod" class="sr-only">
                                    <div class="w-8 h-8 rounded-lg bg-pink-600 text-white font-black text-xs flex items-center justify-center">
                                        bK
                                    </div>
                                    <div class="text-xs">
                                        <p class="font-bold text-slate-900">bKash</p>
                                        <p class="text-[10px] text-slate-400">Mobile Banking</p>
                                    </div>
                                </label>

                                <!-- SSLCommerz -->
                                <label class="border rounded-2xl p-3 flex items-center space-x-3 cursor-pointer hover:border-blue-500 transition"
                                       :class="paymentMethod === 'sslcommerz' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/30' : 'border-slate-200'">
                                    <input type="radio" name="payment_method" value="sslcommerz" x-model="paymentMethod" class="sr-only">
                                    <div class="w-8 h-8 rounded-lg bg-blue-700 text-white font-black text-xs flex items-center justify-center">
                                        SSL
                                    </div>
                                    <div class="text-xs">
                                        <p class="font-bold text-slate-900">SSLCommerz</p>
                                        <p class="text-[10px] text-slate-400">Cards & Internet</p>
                                    </div>
                                </label>

                                <!-- Stripe -->
                                <label class="border rounded-2xl p-3 flex items-center space-x-3 cursor-pointer hover:border-indigo-500 transition"
                                       :class="paymentMethod === 'stripe' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500/30' : 'border-slate-200'">
                                    <input type="radio" name="payment_method" value="stripe" x-model="paymentMethod" class="sr-only">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center">
                                        💳
                                    </div>
                                    <div class="text-xs">
                                        <p class="font-bold text-slate-900">Stripe</p>
                                        <p class="text-[10px] text-slate-400">Visa / Mastercard</p>
                                    </div>
                                </label>

                                <!-- Bank -->
                                <label class="border rounded-2xl p-3 flex items-center space-x-3 cursor-pointer hover:border-slate-500 transition"
                                       :class="paymentMethod === 'bank' ? 'border-slate-600 bg-slate-100 ring-2 ring-slate-500/30' : 'border-slate-200'">
                                    <input type="radio" name="payment_method" value="bank" x-model="paymentMethod" class="sr-only">
                                    <div class="w-8 h-8 rounded-lg bg-slate-700 text-white font-black text-xs flex items-center justify-center">
                                        🏛️
                                    </div>
                                    <div class="text-xs">
                                        <p class="font-bold text-slate-900">Bank Transfer</p>
                                        <p class="text-[10px] text-slate-400">Direct Deposit</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                            <button type="button" @click="payModalOpen = false" class="px-4 py-2 border rounded-xl text-xs font-bold text-slate-600">
                                {{ __('messages.cancel') }}
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">
                                Confirm & Pay ৳<span x-text="payAmount"></span>
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
