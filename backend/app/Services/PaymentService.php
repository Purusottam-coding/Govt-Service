<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Initiate or record a payment transaction for an application.
     */
    public function recordPayment(Application $application, float $amount, string $method = 'online', ?string $transactionId = null): Payment
    {
        $transactionId = $transactionId ?? 'TXN-' . strtoupper(Str::random(10));

        return Payment::create([
            'application_id' => $application->id,
            'user_id' => $application->user_id,
            'amount' => $amount,
            'payment_method' => $method,
            'transaction_id' => $transactionId,
            'status' => PaymentStatus::COMPLETED->value,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed or completed.
     */
    public function updatePaymentStatus(Payment $payment, PaymentStatus|string $status): Payment
    {
        $statusValue = $status instanceof PaymentStatus ? $status->value : $status;

        $payment->update([
            'status' => $statusValue,
            'paid_at' => $statusValue === PaymentStatus::COMPLETED->value ? now() : $payment->paid_at,
        ]);

        return $payment;
    }
}
