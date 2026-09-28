<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Initiate a payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'payment_method' => 'required|in:online,bank_transfer,cash',
        ]);

        $application = Application::with('service')
            ->findOrFail($request->application_id);

        // Get payment amount from the selected service
        $amount = $application->service->fee;

        // Prevent duplicate pending/completed payment
        $existingPayment = Payment::where('application_id', $application->id)
            ->whereIn('status', ['pending', 'completed'])
            ->first();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already exists for this application.',
                'data' => [
                    'transaction_id' => $existingPayment->transaction_id,
                    'amount' => $existingPayment->amount,
                    'status' => $existingPayment->status,
                ],
            ], 409);
        }

        // Generate unique transaction ID
        $transactionId = 'TXN-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));

        $payment = Payment::create([
            'application_id' => $application->id,
            'amount' => $amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $transactionId,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated successfully.',
            'data' => [
                'payment_id' => $payment->id,
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'service' => $application->service->name,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status,
            ],
        ], 201);
    }

    /**
     * Payment gateway callback
     */
    public function callback(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|in:completed,failed',
        ]);

        $payment = Payment::where(
            'transaction_id',
            $request->transaction_id
        )->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        $payment->update([
            'status' => $request->status,
            'paid_at' => $request->status === 'completed'
                ? now()
                : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully.',
            'data' => [
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at,
            ],
        ]);
    }

    /**
     * Check payment status
     */
    public function status(string $transactionId)
    {
        $payment = Payment::with('application.service')
            ->where('transaction_id', $transactionId)
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'payment_id' => $payment->id,
                'application_id' => $payment->application_id,
                'application_number' => $payment->application->application_number,
                'service' => $payment->application->service->name,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at,
            ],
        ]);
    }
}