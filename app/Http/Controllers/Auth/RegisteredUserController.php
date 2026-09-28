<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\RegistrationOtpNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rawEmail = strtolower(trim((string) $request->email));
        $rawPhone = trim((string) $request->phone);

        // Pre-validation cleanup:
        // If an unverified citizen account exists with this email or phone (e.g. from an incomplete/interrupted attempt, mistype, or back button),
        // remove that pending unverified citizen draft so they can re-register or fix mistakes without getting "already registered" errors.
        if ($rawEmail || $rawPhone) {
            User::where('role', 'citizen')
                ->whereNull('email_verified_at')
                ->where(function ($query) use ($rawEmail, $rawPhone) {
                    if ($rawEmail) {
                        $query->where('email', $rawEmail);
                    }
                    if ($rawPhone) {
                        $query->orWhere('phone', $rawPhone);
                    }
                })
                ->delete();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
                Rule::unique(User::class, 'email')->where(function ($query) {
                    return $query->whereNotNull('email_verified_at')->orWhere('role', 'admin');
                }),
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^(98|97|96)[0-9]{8}$/',
                Rule::unique(User::class, 'phone')->where(function ($query) {
                    return $query->whereNotNull('email_verified_at')->orWhere('role', 'admin');
                }),
            ],
            'address' => ['required', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'कृपया नागरिकको पूरा नाम प्रविष्ट गर्नुहोस्।',
            'email.required' => 'कृपया आफ्नो Gmail ठेगाना प्रविष्ट गर्नुहोस्।',
            'email.regex' => 'सुरक्षा तथा प्रमाणीकरणका लागि केवल आधिकारिक @gmail.com ठेगाना मात्र मान्य हुनेछ।',
            'email.unique' => 'यो Gmail ठेगाना पहिले नै दर्ता भइसकेको छ। एक नागरिकका लागि एउटा मात्र Gmail खाता मान्य छ।',
            'phone.required' => 'कृपया आफ्नो मोबाइल नम्बर प्रविष्ट गर्नुहोस्।',
            'phone.regex' => 'कृपया नेपालको मान्य १० अंकको मोबाइल नम्बर प्रविष्ट गर्नुहोस् (उदा. ९८XXXXXXXX)।',
            'phone.unique' => 'यो मोबाइल नम्बर पहिले नै अर्को नागरिकको नाममा दर्ता भइसकेको छ।',
            'address.required' => 'कृपया आफ्नो स्थायी ठेगाना (वडा नं. सहित) प्रविष्ट गर्नुहोस्।',
            'password.required' => 'कृपया बलियो पासवर्ड प्रविष्ट गर्नुहोस्।',
            'password.confirmed' => 'दुबै पासवर्डहरू एकआपसमा मेल खाएनन्।',
            'password.min' => 'पासवर्ड कम्तीमा ८ अक्षरको हुनुपर्दछ।',
        ]);

        // Generate secure 6-digit numeric OTP code
        $otp = sprintf('%06d', random_int(100000, 999999));

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'citizen',
            'password' => Hash::make($request->password),
            'verification_otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_last_sent_at' => now(),
            'email_verified_at' => null,
        ]);

        event(new Registered($user));

        // Send OTP email synchronously using configured Gmail SMTP
        try {
            $user->notifyNow(new RegistrationOtpNotification($otp));
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch registration OTP email: ' . $e->getMessage());
        }

        // Log the newly registered citizen in and set account creation OTP flag
        Auth::login($user);
        $request->session()->put('requires_registration_otp', true);

        // Redirect directly to the OTP verification screen
        return redirect()->route('otp.verify')
            ->with('info', 'तपाईंको Gmail (' . $user->masked_email . ') मा ६ अंकको प्रमाणीकरण कोड (OTP) पठाइएको छ। कृपया खाता सक्रिय गर्न कोड प्रविष्ट गर्नुहोस्।');
    }
}
