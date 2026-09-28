<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $rememberedEmail = $request->cookie('remembered_email', '');
        $rememberedRole = $request->cookie('remembered_role', '');
        $rememberMeChecked = (bool) $request->cookie('remember_me_checked', false);
        $rememberedAdminEmail = $request->cookie('remembered_admin_email', '');
        $rememberedCitizenEmail = $request->cookie('remembered_citizen_email', '');

        return view('auth.login', compact(
            'rememberedEmail',
            'rememberedRole',
            'rememberMeChecked',
            'rememberedAdminEmail',
            'rememberedCitizenEmail'
        ));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        // Clear any pending registration OTP flag on login
        $request->session()->forget('requires_registration_otp');

        // Direct dashboard redirect on login auth without OTP
        $redirect = $user->isAdmin()
            ? redirect()->intended(route('admin.dashboard', absolute: false))
            : redirect()->intended(route('citizen.dashboard', absolute: false));

        $email = $request->string('email')->toString();
        $role = $request->string('role')->toString();

        if ($request->boolean('remember')) {
            // Save remember cookies for 1 year
            $redirect->withCookie(cookie()->forever('remembered_email', $email))
                ->withCookie(cookie()->forever('remembered_role', $role))
                ->withCookie(cookie()->forever('remember_me_checked', '1'));

            if ($role === 'admin') {
                $redirect->withCookie(cookie()->forever('remembered_admin_email', $email));
            } else {
                $redirect->withCookie(cookie()->forever('remembered_citizen_email', $email));
            }
        } else {
            // Clear remember cookies if remember me was unchecked
            $redirect->withCookie(cookie()->forget('remembered_email'))
                ->withCookie(cookie()->forget('remember_me_checked'));

            if ($role === 'admin') {
                $redirect->withCookie(cookie()->forget('remembered_admin_email'));
            } else {
                $redirect->withCookie(cookie()->forget('remembered_citizen_email'));
            }
        }

        return $redirect;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // If an unverified citizen logs out during registration, remove the incomplete draft
        if ($user && $user->isCitizen() && $user->email_verified_at === null) {
            $user->delete();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
