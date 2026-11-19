<?php

namespace App\Services\Payment;

use App\Models\FeeInvoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BKashGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'bkash';
    }

    public function processPayment(array $data): array
    {
        $invoice = FeeInvoice::find($data['invoice_id']);
        if (!$invoice) {
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'Invoice not found.'];
        }

        $username = config('services.bkash.username');
        $password = config('services.bkash.password');
        $baseUrl = config('services.bkash.sandbox') ? 'https://sandbox.bkash.com' : 'https://checkout.bkash.com';

        if (!$username || !$password) {
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'bKash credentials not configured.'];
        }

        try {
            $transactionId = 'BKS' . strtoupper(bin2hex(random_bytes(4)));

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode("{$username}:{$password}"),
            ])->post($baseUrl . '/v2/checkouts', [
                'merchantInvoiceNumber' => $invoice->invoice_number,
                'amount' => $data['amount'],
                'currency' => 'BDT',
                'message' => 'Payment for invoice ' . $invoice->invoice_number,
                'clientReference' => $transactionId,
                'callbackURL' => config('app.url') . '/payment/callback/bkash',
                'failureCallbackURL' => config('app.url') . '/payment/failure/bkash',
            ])->json();

            if (isset($response['paymentID']) || isset($response['transactionId'])) {
                return [
                    'success' => true,
                    'transaction_id' => $response['transactionId'] ?? $response['paymentID'],
                    'gateway_response' => $response,
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'transaction_id' => null,
                'gateway_response' => $response,
                'error' => $response['errorMessage'] ?? 'Payment failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('bKash payment error', ['exception' => $e->getMessage()]);
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'Gateway communication error.'];
        }
    }

    public function verifyWebhook(array $payload): array
    {
        $transactionId = $payload['transactionID'] ?? $payload['merchantInvoiceNumber'] ?? null;
        $status = $payload['status'] ?? $payload['paymentStatus'] ?? null;
        $amount = $payload['amount'] ?? null;

        if (!$transactionId) {
            return ['valid' => false, 'transaction_id' => null, 'amount' => null, 'status' => null, 'error' => 'Missing transaction ID.'];
        }

        if ($status === 'Completed' || $status === 'success') {
            return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => 'success', 'error' => null];
        }

        if ($status === 'Failed' || $status === 'failed') {
            return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => 'failed', 'error' => null];
        }

        return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => $status, 'error' => null];
    }
}