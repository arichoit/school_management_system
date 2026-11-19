<?php

namespace App\Events;

use App\Models\FeeInvoice;

class InvoiceGenerated
{
    public function __construct(
        public readonly FeeInvoice $invoice,
        public readonly int $generatedBy,
    ) {}
}