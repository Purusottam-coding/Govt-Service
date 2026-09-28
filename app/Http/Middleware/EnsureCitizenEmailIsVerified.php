<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCitizenEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only intercept and redirect if currently in the citizen registration flow
        if ($user && $user->isCitizen() && $request->session()->get('requires_registration_otp')) {
            if (!$request->routeIs('otp.*') && !$request->routeIs('logout')) {
                return redirect()->route('otp.verify')
                    ->with('warning', 'कृपया खाता सक्रिय गर्न पहिले आफ्नो Gmail मा पठाइएको ६ अंकको OTP कोड प्रमाणीकरण गर्नुहोस्।');
            }
        }

        return $next($request);
    }
}
