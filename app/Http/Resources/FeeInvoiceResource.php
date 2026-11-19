<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeeInvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'student_id' => $this->student_id,
            'fee_type_id' => $this->fee_type_id,
            'title' => $this->title,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'due_amount' => (float) $this->due_amount,
            'due_date' => $this->due_date?->toDateString(),
            'status' => $this->status,
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'admission_number' => $this->student->admission_number,
                    'name' => $this->student->user->name ?? null,
                ];
            }),
            'fee_type' => $this->whenLoaded('feeType', function () {
                return [
                    'id' => $this->feeType->id,
                    'title' => $this->feeType->title,
                    'amount' => (float) $this->feeType->amount,
                ];
            }),
            'payments' => FeePaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}