<?php

namespace App\Http\Controllers\Citizen;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Models\PaymentQrCode;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use FileUploadTrait;

    public function create(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if ($application->payment && $application->payment->status === PaymentStatus::COMPLETED->value) {
            return redirect()->route('citizen.payments.receipt', $application)
                ->with('info', 'Payment has already been completed for this application.');
        }

        if ($application->payment && $application->payment->status === PaymentStatus::PENDING->value) {
            return redirect()->route('citizen.applications.show', $application)
                ->with('info', 'तपाईंको भुक्तानी प्रमाण पेश भइसकेको छ। यो अहिले प्रमाणीकरण प्रक्रियामा छ।');
        }

        $application->load(['service', 'payment']);
        $qrCodes = PaymentQrCode::active()->get()->keyBy('qr_type');

        return view('citizen.payments.create', compact('application', 'qrCodes'));
    }

    public function store(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,esewa,khalti,mobile_banking',
            'payment_statement' => 'required_unless:payment_method,cash|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $amount = $application->service->fee;

        // Generate simulated transaction ID
        $txnId = 'TXN-' . strtoupper(Str::random(10));

        $isCashPayment = $validated['payment_method'] === 'cash';
        $existingPayment = $application->payment;

        $statementPath = $existingPayment?->payment_statement;
        if (!$isCashPayment && $request->hasFile('payment_statement')) {
            if ($statementPath) {
                $this->deleteFile($statementPath);
            }

            $statementPath = $this->uploadFile($request->file('payment_statement'), 'payment-statements');
        }

        $payment = Payment::updateOrCreate(
            ['application_id' => $application->id],
            [
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $txnId,
                'payment_statement' => $statementPath,
                'status' => $isCashPayment ? PaymentStatus::COMPLETED->value : PaymentStatus::PENDING->value,
                'paid_at' => now(),
            ]
        );

        if (!$isCashPayment && $application->status === ApplicationStatus::PENDING->value) {
            $application->update([
                'status' => ApplicationStatus::UNDER_REVIEW->value,
            ]);
        }

        if ($isCashPayment) {
            return redirect()->route('citizen.payments.receipt', $application)
                ->with('success', 'Payment of रु. ' . number_format($amount, 2) . ' received successfully!');
        }

        return redirect()->route('citizen.applications.show', $application)
            ->with('success', 'भुक्तानी प्रमाण सफलतापूर्वक पेश गरियो।')
            ->with('payment_verification_popup', true)
            ->with('payment_verification_popup_message', 'तपाईंको भुक्तानी प्रमाण verification process मा पठाइएको छ।');
    }

    public function receipt(Application $application)
    {
        if ($application->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $application->load(['service.department', 'payment', 'user']);

        if (!$application->payment) {
            return redirect()->route('citizen.applications.show', $application)
                ->with('error', 'No payment record found for this application.');
        }

        return view('citizen.payments.receipt', compact('application'));
    }
}
