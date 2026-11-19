<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:fee_invoices,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,bank,bkash,sslcommerz,stripe'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $invoice = \App\Models\FeeInvoice::find($this->input('invoice_id'));
            if (!$invoice) {
                return;
            }
            if ((float) $this->input('amount') > (float) $invoice->due_amount) {
                $validator->errors()->add('amount', 'Payment amount cannot exceed remaining due amount (' . $invoice->due_amount . ').');
            }
        });
    }
}