<?php

namespace App\Services\Payment;

use App\Models\FeeInvoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'sslcommerz';
    }

    public function processPayment(array $data): array
    {
        $invoice = FeeInvoice::find($data['invoice_id']);
        if (!$invoice) {
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'Invoice not found.'];
        }

        $store_id = config('services.sslcommerz.store_id');
        $store_password = config('services.sslcommerz.store_password');
        $sandbox = config('services.sslcommerz.sandbox', true);
        $baseUrl = $sandbox ? 'https://sandbox.sslcommerz.com' : 'https://sslcommerz.com';

        if (!$store_id || !$store_password) {
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'SSLCommerz credentials not configured.'];
        }

        try {
            $transactionId = 'SSL' . strtoupper(bin2hex(random_bytes(4)));

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/api.php', [
                'store_id' => $store_id,
                'store_password' => $store_password,
                'total_amount' => $data['amount'],
                'currency' => 'BDT',
                'product_category' => 'general',
                'product_name' => 'Fee Payment',
                'product_profile' => 'general',
                'cus_name' => $data['customer_name'] ?? 'Student Parent',
                'cus_email' => $data['customer_email'] ?? 'parent@example.com',
                'cus_phone' => $data['customer_phone'] ?? '01700000000',
                'shipping_address' => $data['shipping_address'] ?? 'Dhaka, Bangladesh',
                'shipping_city' => $data['shipping_city'] ?? 'Dhaka',
                'shipping_country' => 'Bangladesh',
                'success_url' => config('app.url') . '/payment/success/sslcommerz',
                'fail_url' => config('app.url') . '/payment/fail/sslcommerz',
                'cancel_url' => config('app.url') . '/payment/cancel/sslcommerz',
                'ipn_url' => config('app.url') . '/payment/ipn/sslcommerz',
                'valuation' => 1,
                'cus_add1' => $invoice->invoice_number,
            ])->json();

            if (isset($response['status']) && $response['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'transaction_id' => $response['transaction_id'] ?? $transactionId,
                    'gateway_response' => $response,
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'transaction_id' => null,
                'gateway_response' => $response,
                'error' => $response['error'] ?? 'Payment failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz payment error', ['exception' => $e->getMessage()]);
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => 'Gateway communication error.'];
        }
    }

    public function verifyWebhook(array $payload): array
    {
        $transactionId = $payload['transaction_id'] ?? $payload['tran_id'] ?? null;
        $status = $payload['status'] ?? $payload['validation_status'] ?? null;
        $amount = $payload['amount'] ?? $payload['amount'] ?? null;

        if (!$transactionId) {
            return ['valid' => false, 'transaction_id' => null, 'amount' => null, 'status' => null, 'error' => 'Missing transaction ID.'];
        }

        if ($status === 'VALIDATED' || $status === 'success') {
            return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => 'success', 'error' => null];
        }

        if ($status === 'FAILED' || $status === 'failed') {
            return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => 'failed', 'error' => null];
        }

        if ($status === 'CANCELLED' || $status === 'cancelled') {
            return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => 'cancelled', 'error' => null];
        }

        return ['valid' => true, 'transaction_id' => $transactionId, 'amount' => $amount, 'status' => $status, 'error' => null];
    }
}