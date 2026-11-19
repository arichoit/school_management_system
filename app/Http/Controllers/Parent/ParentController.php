<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamGrade;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Homework;
use App\Models\Notice;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ParentController extends Controller
{
    private function getParentAndActiveChild(Request $request): array
    {
        $user = auth()->user();
        $parentProfile = $user->parentProfile;

        if (!$parentProfile) {
            abort(403, 'Parent profile not found.');
        }

        $children = $parentProfile->students()->with(['user', 'currentEnrollment.schoolClass', 'currentEnrollment.section'])->get();

        if ($children->isEmpty()) {
            abort(404, 'No enrolled children found linked to your parent account.');
        }

        $activeChildId = $request->get('child_id') ?? session('active_child_id', $children->first()->id);

        // IDOR prevention: Ensure the requested child is in the parent's children collection
        $activeChild = $children->firstWhere('id', (int) $activeChildId);
        if (!$activeChild) {
            abort(403, 'Unauthorized access: this student is not associated with your guardian account.');
        }

        session(['active_child_id' => $activeChild->id]);

        return [$parentProfile, $children, $activeChild];
    }

    public function dashboard(Request $request): View
    {
        [$parentProfile, $children, $activeChild] = $this->getParentAndActiveChild($request);

        // Attendance stats
        $totalAttendance = Attendance::where('student_id', $activeChild->id)->count();
        $presentCount = Attendance::where('student_id', $activeChild->id)->where('status', 'present')->count();
        $lateCount = Attendance::where('student_id', $activeChild->id)->where('status', 'late')->count();
        $absentCount = Attendance::where('student_id', $activeChild->id)->where('status', 'absent')->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 100;

        // Recent Grades
        $recentGrades = ExamGrade::with(['subject', 'examTerm'])
            ->where('student_id', $activeChild->id)
            ->latest()
            ->take(6)
            ->get();

        // Pending Fees
        $unpaidInvoices = FeeInvoice::where('student_id', $activeChild->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();

        // Recent Homework
        $classId = $activeChild->currentEnrollment?->class_id;
        $sectionId = $activeChild->currentEnrollment?->section_id;

        $recentHomework = Homework::with('subject')
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->latest()
            ->take(4)
            ->get();

        $notices = Notice::where('is_published_public', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('parent.dashboard', compact(
            'children',
            'activeChild',
            'totalAttendance',
            'presentCount',
            'lateCount',
            'absentCount',
            'attendancePercentage',
            'recentGrades',
            'unpaidInvoices',
            'recentHomework',
            'notices'
        ));
    }

    public function switchChild(Request $request, int $childId): RedirectResponse
    {
        $parentProfile = auth()->user()->parentProfile;
        // Verify ownership (IDOR check)
        $parentProfile->students()->findOrFail($childId);

        session(['active_child_id' => $childId]);

        return back()->with('success', 'Child profile switched successfully.');
    }

    public function feesIndex(Request $request): View
    {
        [$parentProfile, $children, $activeChild] = $this->getParentAndActiveChild($request);

        $invoices = FeeInvoice::with('payments')
            ->where('student_id', $activeChild->id)
            ->latest()
            ->paginate(10);

        return view('parent.fees.index', compact('children', 'activeChild', 'invoices'));
    }

    public function processPayment(Request $request, FeeInvoice $invoice): RedirectResponse
    {
        $parentProfile = auth()->user()->parentProfile;
        // Ensure invoice belongs to one of parent's children
        $childIds = $parentProfile->students()->pluck('students.id')->toArray();
        if (!in_array($invoice->student_id, $childIds)) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:bkash,sslcommerz,stripe,bank'],
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $invoice->due_amount],
        ]);

        $amount = (float) $validated['amount'];
        $receiptNo = 'REC-' . date('Y') . '-' . rand(10000, 99999);
        $txnId = strtoupper($validated['payment_method']) . '-' . strtoupper(bin2hex(random_bytes(4)));

        DB::transaction(function () use ($invoice, $amount, $validated, $receiptNo, $txnId) {
            FeePayment::create([
                'receipt_number' => $receiptNo,
                'fee_invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $txnId,
                'status' => 'success',
                'paid_at' => Carbon::now(),
                'notes' => 'Online self-payment processed via ' . strtoupper($validated['payment_method']),
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

        return back()->with('success', __('messages.payment_success') . " Receipt #{$receiptNo} (Txn: {$txnId})");
    }

    public function homeworkIndex(Request $request): View
    {
        [$parentProfile, $children, $activeChild] = $this->getParentAndActiveChild($request);

        $classId = $activeChild->currentEnrollment?->class_id;
        $sectionId = $activeChild->currentEnrollment?->section_id;

        $homeworks = Homework::with(['subject', 'teacher', 'submissions' => function ($q) use ($activeChild) {
            $q->where('student_id', $activeChild->id);
        }])
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->latest()
            ->paginate(10);

        return view('parent.homework.index', compact('children', 'activeChild', 'homeworks'));
    }
}
