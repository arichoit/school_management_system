<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeePaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'fee_invoice_id' => $this->fee_invoice_id,
            'amount' => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toDateTimeString(),
            'recorded_by' => $this->recorded_by,
            'notes' => $this->notes,
            'recorded_by_user' => $this->whenLoaded('recordedBy', function () {
                return [
                    'id' => $this->recordedBy->id,
                    'name' => $this->recordedBy->name,
                ];
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}