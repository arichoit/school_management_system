<?php

namespace App\Services\Payment;

use App\Models\FeeInvoice;
use App\Models\FeePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected array $gateways = [];

    public function __construct()
    {
        $this->gateways = [
            'stripe' => app(StripeGateway::class),
            'bkash' => app(BKashGateway::class),
            'sslcommerz' => app(SSLCommerzGateway::class),
        ];
    }

    public function getGateway(string $name): ?PaymentGatewayInterface
    {
        return $this->gateways[$name] ?? null;
    }

    public function processPayment(string $gateway, array $data): array
    {
        $gatewayService = $this->getGateway($gateway);
        if (!$gatewayService) {
            return ['success' => false, 'transaction_id' => null, 'gateway_response' => [], 'error' => "Gateway [{$gateway}] not configured."];
        }

        $result = $gatewayService->processPayment($data);

        if ($result['success']) {
            Log::info('Payment gateway processed', [
                'gateway' => $gateway,
                'transaction_id' => $result['transaction_id'],
                'invoice_id' => $data['invoice_id'] ?? null,
            ]);
        } else {
            Log::error('Payment gateway failed', [
                'gateway' => $gateway,
                'error' => $result['error'],
                'invoice_id' => $data['invoice_id'] ?? null,
            ]);
        }

        return $result;
    }

    public function verifyWebhook(string $gateway, array $payload): array
    {
        $gatewayService = $this->getGateway($gateway);
        if (!$gatewayService) {
            return ['valid' => false, 'transaction_id' => null, 'amount' => null, 'status' => null, 'error' => "Gateway [{$gateway}] not configured."];
        }

        $result = $gatewayService->verifyWebhook($payload);

        Log::info('Payment webhook verification', [
            'gateway' => $gateway,
            'valid' => $result['valid'],
            'transaction_id' => $result['transaction_id'],
            'status' => $result['status'],
        ]);

        return $result;
    }

    public function reconcilePayment(string $gateway, array $payload): bool
    {
        $verification = $this->verifyWebhook($gateway, $payload);
        if (!$verification['valid'] || !$verification['transaction_id']) {
            return false;
        }

        $transactionId = $verification['transaction_id'];
        $amount = $verification['amount'];

        // Check if payment already recorded (idempotency)
        $existing = FeePayment::where('transaction_id', $transactionId)->first();
        if ($existing) {
            Log::info('Payment already recorded, skipping', ['transaction_id' => $transactionId]);
            return true;
        }

        // Find invoice by metadata or transaction ID
        $invoiceId = $payload['data']['object']['metadata']['invoice_id'] ?? $payload['merchantInvoiceNumber'] ?? $payload['cus_add1'] ?? null;
        if (!$invoiceId) {
            Log::error('No invoice ID found in webhook payload', ['payload' => $payload]);
            return false;
        }

        $invoice = FeeInvoice::find($invoiceId);
        if (!$invoice) {
            Log::error('Invoice not found for webhook', ['invoice_id' => $invoiceId]);
            return false;
        }

        DB::transaction(function () use ($invoice, $transactionId, $amount, $gateway, $payload) {
            $payment = FeePayment::create([
                'receipt_number' => 'REC-' . date('Y') . '-' . rand(10000, 99999),
                'fee_invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_method' => $gateway,
                'transaction_id' => $transactionId,
                'status' => $verification['status'] === 'success' ? 'success' : 'failed',
                'paid_at' => now(),
                'gateway_response' => $payload,
                'notes' => 'Auto-reconciled from ' . strtoupper($gateway) . ' webhook.',
            ]);

            if ($payment->status === 'success') {
                $newPaid = $invoice->paid_amount + $amount;
                $newDue = max(0, $invoice->total_amount - $newPaid);
                $newStatus = $newDue <= 0 ? 'paid' : 'partial';

                $invoice->update([
                    'paid_amount' => $newPaid,
                    'due_amount' => $newDue,
                    'status' => $newStatus,
                ]);
            }
        });

        return true;
    }
}