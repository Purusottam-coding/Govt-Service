<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class EsewaPaymentService
{
    protected string $merchantCode;
    protected string $secretKey;
    protected string $baseUrl;
    protected string $statusUrl;

    public function __construct()
    {
        $this->merchantCode = config('services.esewa.merchant_code', 'EPAYTEST');
        $this->secretKey = config('services.esewa.secret_key', '8gBm/:&EnhH.1/q(');
        $this->baseUrl = config('services.esewa.base_url', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form');
        $this->statusUrl = config('services.esewa.status_url', 'https://rc-epay.esewa.com.np/api/epay/main/v2/verify');
    }

    /**
     * Generate HMAC-SHA256 signature for eSewa v2.
     */
    public function generateSignature(string $totalAmount, string $transactionUuid, string $productCode): string
    {
        $message = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
        $hash = hash_hmac('sha256', $message, $this->secretKey, true);

        return base64_encode($hash);
    }

    /**
     * Prepare eSewa payment form parameters.
     */
    public function getPaymentPayload(Application $application, Payment $payment, ?string $transactionUuid = null): array
    {
        $amountFormatted = number_format((float) $payment->amount, 2, '.', '');
        $taxAmount = '0';
        $productServiceCharge = '0';
        $productDeliveryCharge = '0';
        $totalAmount = $amountFormatted;

        $uuid = $transactionUuid ?? ('APP-' . $application->id . '-' . $payment->id . '-' . time());

        $signature = $this->generateSignature($totalAmount, $uuid, $this->merchantCode);

        return [
            'amount' => $amountFormatted,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'transaction_uuid' => $uuid,
            'product_code' => $this->merchantCode,
            'product_service_charge' => $productServiceCharge,
            'product_delivery_charge' => $productDeliveryCharge,
            'success_url' => route('citizen.payments.esewa.success', $application),
            'failure_url' => route('citizen.payments.esewa.failed', $application),
            'signed_field_names' => 'total_amount,transaction_uuid,product_code',
            'signature' => $signature,
        ];
    }

    /**
     * Get eSewa Gateway Form Action URL.
     */
    public function getFormActionUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Decode and verify response data returned in eSewa success callback.
     * eSewa returns a base64 encoded JSON string in the 'data' query parameter.
     */
    public function decodeCallbackData(string $encodedData): ?array
    {
        try {
            $json = base64_decode($encodedData);
            if (!$json) {
                return null;
            }

            $data = json_decode($json, true);
            if (!is_array($data) || !isset($data['status'])) {
                return null;
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('eSewa decode callback error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify whether the callback status is COMPLETE.
     */
    public function isSuccessful(array $decodedData): bool
    {
        return isset($decodedData['status']) && strtoupper($decodedData['status']) === 'COMPLETE';
    }
}
