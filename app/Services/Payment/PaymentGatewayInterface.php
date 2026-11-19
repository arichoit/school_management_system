<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Process a payment through the gateway.
     *
     * @param array $data Payment data containing amount, invoice_id, etc.
     * @return array{success: bool, transaction_id: ?string, gateway_response: array, error: ?string}
     */
    public function processPayment(array $data): array;

    /**
     * Verify a webhook/callback payload from the gateway.
     *
     * @param array $payload The raw payload from the gateway.
     * @return array{valid: bool, transaction_id: ?string, amount: ?float, status: ?string, error: ?string}
     */
    public function verifyWebhook(array $payload): array;

    /**
     * Get the gateway name identifier.
     */
    public function getName(): string;
}