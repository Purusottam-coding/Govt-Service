<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    public function index()
    {
        $qrCodes = PaymentQrCode::latest()->get();
        return view('admin.qr-codes.index', compact('qrCodes'));
    }

    public function create()
    {
        return view('admin.qr-codes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'qr_type' => 'required|in:esewa,khalti,mobile_banking|unique:payment_qr_codes,qr_type',
            'qr_code' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'description' => 'nullable|string|max:500',
            'status' => 'boolean',
        ]);

        // Upload QR code
        $path = $request->file('qr_code')->store('payment_qr_codes', 'public');

        PaymentQrCode::create([
            'qr_type' => $validated['qr_type'],
            'qr_code_path' => $path,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? true,
        ]);

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR code added successfully for ' . ucfirst(str_replace('_', ' ', $validated['qr_type'])));
    }

    public function edit(PaymentQrCode $qrCode)
    {
        return view('admin.qr-codes.edit', compact('qrCode'));
    }

    public function update(Request $request, PaymentQrCode $qrCode)
    {
        $validated = $request->validate([
            'qr_code' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'description' => 'nullable|string|max:500',
            'status' => 'boolean',
        ]);

        $data = [
            'description' => $validated['description'] ?? $qrCode->description,
            'status' => $validated['status'] ?? $qrCode->status,
        ];

        // Upload new QR code if provided
        if ($request->hasFile('qr_code')) {
            // Delete old QR code
            if ($qrCode->qr_code_path && Storage::exists($qrCode->qr_code_path)) {
                Storage::delete($qrCode->qr_code_path);
            }

            $data['qr_code_path'] = $request->file('qr_code')->store('payment_qr_codes', 'public');
        }

        $qrCode->update($data);

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR code updated successfully');
    }

    public function destroy(PaymentQrCode $qrCode)
    {
        // Delete QR code file
        if ($qrCode->qr_code_path && Storage::exists($qrCode->qr_code_path)) {
            Storage::delete($qrCode->qr_code_path);
        }

        $qrCode->delete();

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR code deleted successfully');
    }

    public function toggleStatus(PaymentQrCode $qrCode)
    {
        $qrCode->update(['status' => !$qrCode->status]);

        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR code status updated successfully');
    }
}
