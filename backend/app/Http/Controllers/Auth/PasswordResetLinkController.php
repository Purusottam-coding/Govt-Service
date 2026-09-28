<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $otp = (string) random_int(100000, 999999);
        $cacheKey = 'password-reset-otp:' . Str::lower($user->email);

        Cache::put($cacheKey, [
            'email' => $user->email,
            'otp_hash' => Hash::make($otp),
        ], now()->addMinutes(10));

        $user->notify(new PasswordResetOtpNotification($otp));

        $request->session()->put([
            'password_reset_email' => $user->email,
            'password_reset_verified' => false,
            'password_reset_otp_hash' => Hash::make($otp),
        ]);

        return redirect()->route('password.reset')
            ->with('status', 'OTP तपाईंको email मा पठाइएको छ। 10 मिनेटभित्र code प्रयोग गर्नुहोस्।');
    }
}
