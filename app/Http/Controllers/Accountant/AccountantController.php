<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccountantController extends Controller
{
    public function dashboard(): View
    {
        $today = Carbon::today()->toDateString();

        $totalBilled = FeeInvoice::sum('total_amount');
        $totalCollected = FeeInvoice::sum('paid_amount');
        $outstandingDues = FeeInvoice::sum('due_amount');

        // Today's collection
        $todayCollected = FeePayment::whereDate('paid_at', $today)->sum('amount');
        $todayPayments = FeePayment::with(['invoice.student.user'])
            ->whereDate('paid_at', $today)
            ->latest('paid_at')
            ->get();

        // Recent 10 payments overall
        $recentPayments = FeePayment::with(['invoice.student.user'])
            ->latest('paid_at')
            ->take(8)
            ->get();

        // Breakdown by method
        $methodStats = FeePayment::select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        return view('accountant.dashboard', compact(
            'totalBilled',
            'totalCollected',
            'outstandingDues',
            'todayCollected',
            'todayPayments',
            'recentPayments',
            'methodStats'
        ));
    }

    public function feesIndex(Request $request): View
    {
        $query = FeeInvoice::with(['student.user', 'student.currentEnrollment.schoolClass', 'feeType', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        $feeTypes = FeeType::with('academicYear', 'schoolClass')->latest()->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $classes = SchoolClass::all();
        $recentPayments = FeePayment::with(['invoice.student.user'])->latest()->take(10)->get();

        return view('accountant.fees.index', compact(
            'invoices',
            'feeTypes',
            'academicYears',
            'classes',
            'recentPayments'
        ));
    }

    public function storeFeeType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ]);

        FeeType::create($validated);

        return back()->with('success', 'Fee structure created successfully.');
    }

    public function recordPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:fee_invoices,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,bank,bkash,sslcommerz,stripe'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $invoice = FeeInvoice::findOrFail($validated['invoice_id']);

        if ($validated['amount'] > $invoice->due_amount) {
            return back()->with('error', 'Payment amount cannot exceed remaining due amount (' . $invoice->due_amount . ').');
        }

        $receiptNo = 'REC-' . date('Y') . '-' . rand(10000, 99999);
        $amount = (float) $validated['amount'];

        DB::transaction(function () use ($invoice, $amount, $validated, $receiptNo) {
            FeePayment::create([
                'receipt_number' => $receiptNo,
                'fee_invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? strtoupper($validated['payment_method']) . '-' . rand(100000, 999999),
                'status' => 'success',
                'paid_at' => Carbon::now(),
                'recorded_by' => auth()->id(),
                'notes' => $validated['notes'] ?? 'Manual receipt recorded by accountant.',
            ]);

            $newPaid = $invoice->paid_amount + $amount;
            $newDue = max(0, $invoice->total_amount - $newPaid);
            $newStatus = $newDue <= 0 ? 'paid' : 'partial';

            $invoice->update([
                'paid_amount' => $newPaid,
                'due_amount' => $newDue,
                'status' => $newStatus,
            ]);
        });

        return back()->with('success', "Payment of ৳{$amount} recorded successfully. Receipt #{$receiptNo} generated.");
    }
}
