<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class PublicVerificationController extends Controller
{
    /**
     * Public Document & Certificate Verification by QR Scan or Direct Link.
     * Accessible WITHOUT ANY LOGIN by anyone who scans the QR code on an official certificate.
     */
    public function verify(Request $request, ?string $code = null)
    {
        // Support code passed via route URL /verify/{code} or query string (?code=... or legacy ?query=...)
        $lookupCode = trim($code ?: ($request->input('code') ?: $request->input('query', '')));

        $application = null;
        $searched = false;

        if (!empty($lookupCode)) {
            $searched = true;

            $application = Application::with(['service.department', 'user', 'payment'])
                ->where(function ($q) {
                    $q->where('status', 'approved')
                        ->orWhereNotNull('approved_document_path');
                })
                ->where(function ($q) use ($lookupCode) {
                    $q->where('certificate_number', strtoupper($lookupCode))
                        ->orWhere('certificate_number', $lookupCode)
                        ->orWhere('application_number', strtoupper($lookupCode))
                        ->orWhere('application_number', $lookupCode);
                })
                ->first();
        }

        return view('public.verify', compact('application', 'lookupCode', 'searched'));
    }
}
