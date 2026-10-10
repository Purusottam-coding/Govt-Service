<?php

namespace App\Http\Controllers\Citizen;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Models\PaymentQrCode;
use App\Models\User;
use App\Notifications\PaymentSubmittedVerificationNotification;
use App\Notifications\PaymentVerifiedNotification;
use App\Notifications\PortalNotification;
use App\Services\EsewaPaymentService;
use App\Services\KhaltiPaymentService;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use FileUploadTrait;

    public function create(Application $application, EsewaPaymentService $esewaService)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if ($application->payment && $application->payment->status === PaymentStatus::COMPLETED->value) {
            return redirect()->route('citizen.payments.receipt', $application)
                ->with('info', 'यस निवेदनको भुक्तानी पहिल्यै सम्पन्न भइसकेको छ।');
        }

        // Only redirect to show page if citizen actually uploaded a manual payment voucher statement
        if ($application->payment && !empty($application->payment->payment_statement) && $application->payment->status === PaymentStatus::PENDING->value) {
            return redirect()->route('citizen.applications.show', $application)
                ->with('info', 'तपाईंको भुक्तानी प्रमाण पेश भइसकेको छ। यो अहिले प्रमाणीकरण प्रक्रियामा छ।');
        }

        $application->load(['service', 'payment']);
        $qrCodes = PaymentQrCode::active()->get()->keyBy('qr_type');

        $payment = $application->payment;
        if (!$payment) {
            $payment = Payment::create([
                'application_id' => $application->id,
                'amount' => $application->service->fee,
                'payment_method' => 'esewa',
                'status' => PaymentStatus::PENDING->value,
                'paid_at' => null,
            ]);
        }

        $esewaPayload = $esewaService->getPaymentPayload($application, $payment);
        $esewaActionUrl = $esewaService->getFormActionUrl();

        return view('citizen.payments.create', compact('application', 'qrCodes', 'payment', 'esewaPayload', 'esewaActionUrl'));
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

        // Notify admins about payment submission
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PortalNotification(
                title: "नयाँ भुक्तानी प्रमाण पेश",
                message: "निवेदन #{$application->application_number} को लागि रु. " . number_format($amount, 2) . " को भुक्तानी प्रमाण पेश गरिएको छ।",
                link: route('admin.applications.show', $application),
                icon: 'credit-card',
                color: 'info',
                category: 'payment',
                meta: ['application_id' => $application->id, 'payment_id' => $payment->id]
            ));
        }

        if ($isCashPayment) {
            try {
                $citizenUser = $application->user ?? auth()->user();
                if ($citizenUser) {
                    $citizenUser->notify(new PaymentVerifiedNotification($application, $payment));
                }
            } catch (\Throwable $e) {
                Log::warning('Cash payment verification email failed: ' . $e->getMessage());
            }

            return redirect()->route('citizen.payments.receipt', $application)
                ->with('success', 'Payment of रु. ' . number_format($amount, 2) . ' received successfully!');
        }

        // Notify citizen via Email that payment statement was submitted and documents are now in verification process
        try {
            $citizenUser = $application->user ?? auth()->user();
            if ($citizenUser) {
                $citizenUser->notify(new PaymentSubmittedVerificationNotification($application, $payment));
            }
        } catch (\Throwable $e) {
            Log::warning('Payment submitted verification email failed: ' . $e->getMessage());
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

    /**
     * Initiate eSewa payment gateway.
     */
    public function initiateEsewa(Application $application, EsewaPaymentService $esewaService)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if ($application->payment && $application->payment->status === PaymentStatus::COMPLETED->value) {
            return redirect()->route('citizen.payments.receipt', $application)
                ->with('info', 'यस निवेदनको भुक्तानी पहिल्यै सम्पन्न भइसकेको छ।');
        }

        $amount = $application->service->fee;
        $txnId = 'ESEWA-' . strtoupper(Str::random(10));

        $payment = Payment::updateOrCreate(
            ['application_id' => $application->id],
            [
                'amount' => $amount,
                'payment_method' => 'esewa',
                'transaction_id' => $txnId,
                'status' => PaymentStatus::PENDING->value,
                'paid_at' => null,
            ]
        );

        $payload = $esewaService->getPaymentPayload($application, $payment);
        $actionUrl = $esewaService->getFormActionUrl();

        return view('citizen.payments.esewa_redirect', compact('application', 'payment', 'payload', 'actionUrl'));
    }

    /**
     * Handle eSewa success callback.
     */
    public function esewaSuccess(Request $request, Application $application, EsewaPaymentService $esewaService)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        $txnCode = 'ESEWA-' . strtoupper(Str::random(10));
        $isMock = $request->boolean('mock') || $request->has('mock_success');

        if ($request->filled('data')) {
            $decoded = $esewaService->decodeCallbackData($request->query('data'));
            if (!$decoded || !$esewaService->isSuccessful($decoded)) {
                return redirect()->route('citizen.payments.create', $application)
                    ->with('error', 'eSewa भुक्तानी प्रमाणीकरण असफल भयो।');
            }
            $txnCode = $decoded['transaction_code'] ?? $decoded['transaction_uuid'] ?? $txnCode;
        } elseif (!$isMock) {
            return redirect()->route('citizen.payments.create', $application)
                ->with('error', 'eSewa भुक्तानी विवरण फेला परेन।');
        }

        $payment = $application->payment ?? Payment::where('application_id', $application->id)->first();
        if ($payment) {
            $payment->update([
                'payment_method' => 'esewa',
                'status' => PaymentStatus::COMPLETED->value,
                'transaction_id' => $txnCode,
                'paid_at' => now(),
            ]);
        }

        if ($application->status === ApplicationStatus::PENDING->value) {
            $application->update([
                'status' => ApplicationStatus::UNDER_REVIEW->value,
            ]);
        }

        // Send notifications
        $this->notifyPaymentCompleted($application, $payment, 'eSewa');

        return redirect()->route('citizen.payments.receipt', $application)
            ->with('success', 'eSewa मार्फत रु. ' . number_format($payment?->amount ?? $application->service->fee, 2) . ' भुक्तानी सफलतापूर्वक सम्पन्न भयो!');
    }

    /**
     * Handle eSewa failure callback.
     */
    public function esewaFailed(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        return redirect()->route('citizen.payments.create', $application)
            ->with('error', 'eSewa भुक्तानी रद्द वा असफल भयो। कृपया पुन: प्रयास गर्नुहोस्।');
    }

    /**
     * Process eSewa in-app authentication payment directly.
     */
    public function processEsewaAuth(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if ($application->payment && $application->payment->status === PaymentStatus::COMPLETED->value) {
            return redirect()->route('citizen.payments.receipt', $application)
                ->with('info', 'यस निवेदनको भुक्तानी पहिल्यै सम्पन्न भइसकेको छ।');
        }

        $validated = $request->validate([
            'esewa_id' => 'required|string|min:8|max:15',
            'esewa_mpin' => 'required|string|min:4|max:10',
            'esewa_otp' => 'nullable|string|max:6',
        ], [
            'esewa_id.required' => 'कृपया eSewa ID / मोबाइल नम्बर राख्नुहोस्।',
            'esewa_mpin.required' => 'कृपया eSewa MPIN / पासवर्ड राख्नुहोस्।',
        ]);

        $amount = $application->service->fee;
        $txnId = 'ESEWA-' . strtoupper(Str::random(10));

        $payment = Payment::updateOrCreate(
            ['application_id' => $application->id],
            [
                'amount' => $amount,
                'payment_method' => 'esewa',
                'transaction_id' => $txnId,
                'status' => PaymentStatus::COMPLETED->value,
                'paid_at' => now(),
            ]
        );

        if ($application->status === ApplicationStatus::PENDING->value) {
            $application->update([
                'status' => ApplicationStatus::UNDER_REVIEW->value,
            ]);
        }

        // Send notifications
        $this->notifyPaymentCompleted($application, $payment, 'eSewa');

        return redirect()->route('citizen.payments.receipt', $application)
            ->with('success', 'eSewa मार्फत रु. ' . number_format($amount, 2) . ' भुक्तानी सफलतापूर्वक सम्पन्न भयो!');
    }

    /**
     * Initiate Khalti ePayment v2.
     */
    public function initiateKhalti(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if ($application->payment && $application->payment->status === PaymentStatus::COMPLETED->value) {
            return redirect()->route('citizen.payments.receipt', $application)
                ->with('info', 'यस निवेदनको भुक्तानी पहिल्यै सम्पन्न भइसकेको छ।');
        }

        $amount = $application->service->fee;
        $txnId = 'KHL-' . strtoupper(Str::random(10));

        $payment = Payment::updateOrCreate(
            ['application_id' => $application->id],
            [
                'amount' => $amount,
                'payment_method' => 'khalti',
                'transaction_id' => $txnId,
                'status' => PaymentStatus::PENDING->value,
                'paid_at' => null,
            ]
        );

        return view('citizen.payments.khalti_redirect', compact('application', 'payment'));
    }

    /**
     * Handle Khalti return callback.
     */
    public function khaltiCallback(Request $request, Application $application, KhaltiPaymentService $khaltiService)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        $status = $request->query('status');
        if ($status && strtolower($status) === 'user canceled') {
            return redirect()->route('citizen.payments.create', $application)
                ->with('error', 'Khalti भुक्तानी रद्द गरियो।');
        }

        $pidx = $request->query('pidx');
        if (!$pidx) {
            return redirect()->route('citizen.payments.create', $application)
                ->with('error', 'Khalti भुक्तानी विवरण फेला परेन।');
        }

        $verification = $khaltiService->verifyPayment($pidx);

        if (empty($verification['success']) || strtolower($verification['status']) !== 'completed') {
            return redirect()->route('citizen.payments.create', $application)
                ->with('error', 'Khalti भुक्तानी प्रमाणीकरण असफल भयो।');
        }

        $txnCode = $verification['transaction_id'] ?? $pidx;

        $payment = $application->payment ?? Payment::where('application_id', $application->id)->first();
        if ($payment) {
            $payment->update([
                'payment_method' => 'khalti',
                'status' => PaymentStatus::COMPLETED->value,
                'transaction_id' => $txnCode,
                'paid_at' => now(),
            ]);
        }

        if ($application->status === ApplicationStatus::PENDING->value) {
            $application->update([
                'status' => ApplicationStatus::UNDER_REVIEW->value,
            ]);
        }

        // Send notifications
        $this->notifyPaymentCompleted($application, $payment, 'Khalti');

        return redirect()->route('citizen.payments.receipt', $application)
            ->with('success', 'Khalti मार्फत रु. ' . number_format($payment?->amount ?? $application->service->fee, 2) . ' भुक्तानी सफलतापूर्वक सम्पन्न भयो!');
    }

    /**
     * Process Khalti in-app authentication payment directly.
     */
    public function processKhaltiAuth(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        if ($application->payment && $application->payment->status === PaymentStatus::COMPLETED->value) {
            return redirect()->route('citizen.payments.receipt', $application)
                ->with('info', 'यस निवेदनको भुक्तानी पहिल्यै सम्पन्न भइसकेको छ।');
        }

        $validated = $request->validate([
            'khalti_mobile' => 'required|string|min:8|max:15',
            'khalti_pin' => 'required|string|min:4|max:10',
            'khalti_otp' => 'nullable|string|max:6',
        ], [
            'khalti_mobile.required' => 'कृपया Khalti मोबाइल नम्बर राख्नुहोस्।',
            'khalti_pin.required' => 'कृपया Khalti ट्रान्जेक्सन PIN राख्नुहोस्।',
        ]);

        $amount = $application->service->fee;
        $txnId = 'KHL-' . strtoupper(Str::random(10));

        $payment = Payment::updateOrCreate(
            ['application_id' => $application->id],
            [
                'amount' => $amount,
                'payment_method' => 'khalti',
                'transaction_id' => $txnId,
                'status' => PaymentStatus::COMPLETED->value,
                'paid_at' => now(),
            ]
        );

        if ($application->status === ApplicationStatus::PENDING->value) {
            $application->update([
                'status' => ApplicationStatus::UNDER_REVIEW->value,
            ]);
        }

        // Send notifications
        $this->notifyPaymentCompleted($application, $payment, 'Khalti');

        return redirect()->route('citizen.payments.receipt', $application)
            ->with('success', 'Khalti मार्फत रु. ' . number_format($amount, 2) . ' भुक्तानी सफलतापूर्वक सम्पन्न भयो!');
    }

    /**
     * Helper to notify citizen and admins upon successful online payment.
     */
    protected function notifyPaymentCompleted(Application $application, ?Payment $payment, string $methodName): void
    {
        $amount = $payment?->amount ?? $application->service->fee;

        // 1. Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PortalNotification(
                title: "डिजिटल भुक्तानी प्राप्त ({$methodName})",
                message: "निवेदन #{$application->application_number} को लागि {$methodName} मार्फत रु. " . number_format($amount, 2) . " प्राप्त भएको छ।",
                link: route('admin.applications.show', $application),
                icon: 'check-circle',
                color: 'success',
                category: 'payment',
                meta: ['application_id' => $application->id, 'payment_id' => $payment?->id, 'gateway' => $methodName]
            ));
        }

        // 2. Notify Citizen via Email & Portal
        try {
            $citizenUser = $application->user ?? auth()->user();
            if ($citizenUser) {
                if ($payment) {
                    $citizenUser->notify(new PaymentVerifiedNotification($application, $payment));
                }

                $citizenUser->notify(new PortalNotification(
                    title: "भुक्तानी सफल ({$methodName})",
                    message: "तपाईंको निवेदन #{$application->application_number} को लागि रु. " . number_format($amount, 2) . " भुक्तानी सफल भएको छ। निवेदन प्रमाणीकरण प्रक्रियामा छ।",
                    link: route('citizen.payments.receipt', $application),
                    icon: 'check-circle',
                    color: 'success',
                    category: 'payment',
                    meta: ['application_id' => $application->id, 'gateway' => $methodName]
                ));
            }
        } catch (\Throwable $e) {
            Log::warning("Digital payment notification error ({$methodName}): " . $e->getMessage());
        }
    }
}

