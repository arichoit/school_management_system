<?php

namespace App\Services;

use App\Models\FeeInvoice;
use App\Models\FeePayment;
use BarryVD\DomPDF\Facade as PDF;

class PdfReceiptService
{
    public function generate(FeePayment $payment): string
    {
        $payment->load(['invoice.student.user', 'invoice.student.currentEnrollment.schoolClass', 'recordedBy']);

        $invoice = $payment->invoice;
        $student = $invoice->student;
        $user = $student->user;
        $enrollment = $student->currentEnrollment;

        $data = [
            'payment' => $payment,
            'invoice' => $invoice,
            'student' => $student,
            'user' => $user,
            'enrollment' => $enrollment,
            'school' => [
                'name' => config('app.name', 'School Management System'),
                'address' => config('school.address', 'Dhaka, Bangladesh'),
                'phone' => config('school.phone', '+8801700000000'),
                'email' => config('school.email', 'info@school.edu'),
            ],
            'generated_at' => now(),
        ];

        $pdf = PDF::loadView('receipts.fee-payment', $data);

        $filename = 'receipts/' . $payment->receipt_number . '.pdf';
        $path = storage_path('app/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $filename;
    }

    public function stream(FeePayment $payment)
    {
        $payment->load(['invoice.student.user', 'invoice.student.currentEnrollment.schoolClass', 'recordedBy']);

        $invoice = $payment->invoice;
        $student = $invoice->student;
        $user = $student->user;
        $enrollment = $student->currentEnrollment;

        $data = [
            'payment' => $payment,
            'invoice' => $invoice,
            'student' => $student,
            'user' => $user,
            'enrollment' => $enrollment,
            'school' => [
                'name' => config('app.name', 'School Management System'),
                'address' => config('school.address', 'Dhaka, Bangladesh'),
                'phone' => config('school.phone', '+8801700000000'),
                'email' => config('school.email', 'info@school.edu'),
            ],
            'generated_at' => now(),
        ];

        return PDF::loadView('receipts.fee-payment', $data)->download($payment->receipt_number . '.pdf');
    }
}