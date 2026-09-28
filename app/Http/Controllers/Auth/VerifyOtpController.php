<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\RegistrationOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VerifyOtpController extends Controller
{
    /**
     * Display the OTP verification screen.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // OTP is strictly accessible only when creating a citizen account
        if ($user->email_verified_at !== null || !$request->session()->get('requires_registration_otp')) {
            return redirect()->route('citizen.dashboard');
        }

        // Calculate cooldown remaining for resending OTP (60 seconds)
        $cooldown = 0;
        if ($user->otp_last_sent_at) {
            $secondsSinceLast = now()->diffInSeconds($user->otp_last_sent_at);
            if ($secondsSinceLast < 60) {
                $cooldown = 60 - $secondsSinceLast;
            }
        }

        return view('auth.verify-otp', [
            'user' => $user,
            'cooldown' => $cooldown,
        ]);
    }

    /**
     * Handle OTP verification submission.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'कृपया ६ अंकको OTP कोड प्रविष्ट गर्नुहोस्।',
            'otp.size' => 'OTP कोड ठ्याक्कै ६ अंकको हुनुपर्दछ।',
        ]);

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->email_verified_at !== null || !$request->session()->get('requires_registration_otp')) {
            return redirect()->route('citizen.dashboard');
        }

        // Check if OTP has expired
        if (!$user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'OTP कोडको समय सीमा समाप्त भइसकेको छ (Expired)। कृपया तलको "पुनः OTP पठाउनुहोस्" बटन थिच्नुहोस्।',
            ]);
        }

        // Compare OTP
        if (trim($user->verification_otp) !== trim($request->otp)) {
            return back()->withErrors([
                'otp' => 'तपाईंले प्रविष्ट गर्नुभएको OTP कोड मिलेन। कृपया आफ्नो Gmail मा आएको ६ अंकको कोड पुनः जाँच गर्नुहोस्।',
            ]);
        }

        // OTP is valid - mark email as verified and clear OTP session flag & attributes
        $user->forceFill([
            'email_verified_at' => now(),
            'verification_otp' => null,
            'otp_expires_at' => null,
        ])->save();

        $request->session()->forget('requires_registration_otp');

        return redirect()->route('citizen.dashboard')
            ->with('success', 'तपाईंको Gmail (' . $user->masked_email . ') सफलतापूर्वक प्रमाणीकरण भयो! बाह्रदशी गाउँपालिका नागरिक पोर्टलमा स्वागत छ।');
    }

    /**
     * Resend verification OTP code to citizen's Gmail.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->email_verified_at !== null || !$request->session()->get('requires_registration_otp')) {
            return redirect()->route('citizen.dashboard');
        }

        // Enforce 60-second cooldown
        if ($user->otp_last_sent_at && now()->diffInSeconds($user->otp_last_sent_at) < 60) {
            $remaining = 60 - now()->diffInSeconds($user->otp_last_sent_at);
            return back()->withErrors([
                'otp' => 'कृपया पुनः कोड पठाउन ' . $remaining . ' सेकेन्ड पर्खनुहोस्।',
            ]);
        }

        // Generate fresh 6-digit OTP
        $otp = sprintf('%06d', random_int(100000, 999999));

        $user->forceFill([
            'verification_otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_last_sent_at' => now(),
        ])->save();

        try {
            $user->notifyNow(new RegistrationOtpNotification($otp));
        } catch (\Throwable $e) {
            Log::error('Registration OTP Resend Failed: ' . $e->getMessage());
            return back()->with('error', 'इमेल पठाउँदा समस्या आयो। कृपया इन्टरनेट जडान वा इमेल ठेगाना जाँच गर्नुहोस्। (' . $e->getMessage() . ')');
        }

        return back()->with('success', 'नयाँ ६ अंकको OTP कोड तपाईंको Gmail (' . $user->masked_email . ') मा पठाइएको छ।');
    }

    /**
     * Cancel pending registration, purge unverified draft, and redirect back to register.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->isCitizen() && $user->email_verified_at === null) {
            $user->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register')
            ->with('info', 'अघिल्लो अपूरो दर्ता रद्द गरिएको छ। कृपया आफ्नो सही विवरण प्रविष्ट गरी नयाँ खाता दर्ता गर्नुहोस्।');
    }
}
