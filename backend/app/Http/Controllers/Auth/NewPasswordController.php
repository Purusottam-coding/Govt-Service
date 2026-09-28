<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'request' => $request,
            'resetEmail' => $request->session()->get('password_reset_email'),
            'otpVerified' => $request->session()->get('password_reset_verified', false),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (!$request->session()->get('password_reset_verified', false)) {
            $request->validate([
                'otp' => ['required', 'digits:6'],
            ]);

            if (!$email) {
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'Reset request expired. Please request a new OTP.']);
            }

            $cacheKey = 'password-reset-otp:' . Str::lower($email);
            $payload = Cache::get($cacheKey);
            $sessionOtpHash = $request->session()->get('password_reset_otp_hash');

            $isValidOtp = false;

            if ($sessionOtpHash && Hash::check($request->otp, $sessionOtpHash)) {
                $isValidOtp = true;
            }

            if (!$isValidOtp && $payload && isset($payload['otp_hash']) && Hash::check($request->otp, $payload['otp_hash'])) {
                $isValidOtp = true;
            }

            if (!$isValidOtp) {
                return back()->withInput($request->only('otp'))
                    ->withErrors(['otp' => 'Invalid or expired OTP code.']);
            }

            $request->session()->put('password_reset_verified', true);

            return back()->with('status', 'OTP verified. Now create your new password.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Reset request expired. Please request a new OTP.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        Cache::forget('password-reset-otp:' . Str::lower($email));
        $request->session()->forget(['password_reset_email', 'password_reset_verified', 'password_reset_otp_hash']);

        return redirect()->route('login')->with('status', 'Password successfully reset. Please sign in with your new password.');
    }
}
