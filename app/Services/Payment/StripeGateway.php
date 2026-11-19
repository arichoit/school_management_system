<?php

namespace App\Services\Payment;

use App\Models\FeeInvoice;
use App\Models\FeePayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'stripe';
    }

    public function processPayment(array $data): array
    {
        $invoice = FeeInvoice::find($data['invoice_id']);
        if (!$invoice) {
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'Invoice not found.'];
        }

        $secretKey = config('services.stripe.secret');
        if (!$secretKey) {
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'Stripe secret key not configured.'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => (int) round($data['amount'] * 100),
                'currency' => 'usd',
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                ],
            ])->json();

            if (isset($response['id'])) {
                return [
                    'success' => true,
                    'transaction_id' => $response['id'],
                    'gateway_response' => $response,
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'transaction_id' => null,
                'gateway_response' => $response,
                'error' => $response['error']['message'] ?? 'Payment failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe payment error', ['exception' => $e->getMessage()]);
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'Gateway communication error.'];
        }
    }

    public function verifyWebhook(array $payload): array
    {
        $webhookSecret = config('services.stripe.webhook_secret');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;

        if (!$webhookSecret || !$sigHeader) {
            return ['valid' => false, 'transaction_id' => null, 'amount' => null, 'status' => null, 'error' => 'Webhook signature missing.'];
        }

        try {
            \Stripe\TestHelper::validateWebhookSignature(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            return ['valid' => false, 'transaction_id' => null, 'amount' => null, 'status' => null, 'error' => 'Invalid signature.'];
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return ['valid' => false, 'transaction_id' => null, 'amount' => null, 'status' => null, 'error' => 'Signature verification failed.'];
        }

        $type = $payload['type'] ?? null;
        $data = $payload['data']['object'] ?? null;

        if (!$type || !$data) {
            return ['valid' => false, 'transaction_id' => null, 'amount' => null, 'status' => null, 'error' => 'Malformed payload.'];
        }

        $transactionId = $data['id'] ?? null;
        $amount = isset($data['amount']) ? $data['amount'] / 100 : null;
        $status = $data['status'] ?? null;

        if ($type === 'payment_intent.succeeded' && $status === 'succeeded') {
            return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => 'success', 'error' => null];
        }

        if ($type === 'charge.succeeded' && $status === 'succeeded') {
            return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => 'success', 'error' => null];
        }

        if ($type === 'payment_intent.payment_failed') {
            return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => 'failed', 'error' => null];
        }

        return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => $status, 'error' => null];
    }
}