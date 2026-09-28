<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentQrCode extends Model
{
    protected $fillable = [
        'qr_type',
        'qr_code_path',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /* ---------- QR Code Helpers ---------- */

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
    }

    public function hasQrCode(): bool
    {
        return !empty($this->qr_code_path) && Storage::exists($this->qr_code_path);
    }

    public function getQrTypeLabel(): string
    {
        return match($this->qr_type) {
            'esewa' => 'eSewa',
            'khalti' => 'Khalti',
            'mobile_banking' => 'Mobile Banking',
            default => 'QR Code',
        };
    }

    public function getQrTypeColor(): string
    {
        return match($this->qr_type) {
            'esewa' => '#1D5C8F', // eSewa blue
            'khalti' => '#5C2D91', // Khalti purple
            'mobile_banking' => '#0066CC', // Banking blue
            default => '#64748B',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('qr_type', $type);
    }
}
