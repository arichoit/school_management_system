<?php

namespace App\Events;

use App\Models\FeePayment;

class PaymentReceived
{
    public function __construct(
        public readonly FeePayment $payment,
    ) {}
}