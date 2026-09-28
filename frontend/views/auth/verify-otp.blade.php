@extends('layouts.guest')

@section('title', 'Gmail प्रमाणीकरण (OTP) — बाह्रदशी गाउँपालिका')

@section('content')
<div class="text-center mb-4">
    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 58px; height: 58px; background: rgba(166, 15, 30, 0.1); border: 2px solid rgba(166, 15, 30, 0.25);">
        <i data-lucide="mail-check" style="width: 28px; height: 28px; color: #a60f1e;"></i>
    </div>
    <h4 class="auth-form-title mb-1">Gmail प्रमाणीकरण (OTP)</h4>
    <p class="auth-form-subtitle mb-0">नागरिक खाता सक्रिय गर्न ६ अंकको प्रमाणीकरण कोड प्रविष्ट गर्नुहोस्</p>
</div>

{{-- Status / Flash Alerts --}}
@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm small py-2 px-3 mb-3" role="alert">
        <i data-lucide="info" class="me-1" style="width: 15px; height: 15px;"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm small py-2 px-3 mb-3" role="alert">
        <i data-lucide="check-circle" class="me-1 text-success" style="width: 15px; height: 15px;"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm small py-2 px-3 mb-3" role="alert">
        <i data-lucide="alert-triangle" class="me-1" style="width: 15px; height: 15px;"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm small py-2 px-3 mb-3" role="alert">
        <i data-lucide="alert-circle" class="me-1 text-danger" style="width: 15px; height: 15px;"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="p-3 mb-4 rounded-3 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0;">
    <div class="small text-muted mb-1">कोड पठाइएको Gmail ठेगाना:</div>
    <div class="fw-bold text-dark font-monospace fs-6">
        <i data-lucide="mail" class="me-1 text-primary" style="width: 15px; height: 15px; vertical-align: -2px;"></i>
        {{ $user->masked_email }}
    </div>
</div>

<form method="POST" action="{{ route('otp.submit') }}" id="otpForm">
    @csrf

    <div class="mb-3">
        <label for="otp" class="form-label fw-semibold small text-secondary d-flex justify-content-between align-items-center">
            <span>६ अंकको OTP कोड (Enter 6-digit OTP)</span>
            <span class="text-danger small">* अनिवार्य</span>
        </label>
        
        <!-- Unified 6-Digit OTP Field with large styled font -->
        <div class="input-group auth-input-group mb-2">
            <span class="input-group-text"><i data-lucide="shield-check" style="color: #a60f1e;"></i></span>
            <input type="text" 
                   class="form-control text-center font-monospace fw-bold fs-4 letter-spacing-lg @error('otp') is-invalid @enderror" 
                   id="otp" 
                   name="otp" 
                   maxlength="6" 
                   inputmode="numeric" 
                   pattern="[0-9]{6}" 
                   autocomplete="one-time-code"
                   placeholder="• • • • • •" 
                   required 
                   autofocus
                   style="letter-spacing: 10px; font-size: 1.6rem !important;">
        </div>

        @error('otp')
            <div class="invalid-feedback d-block text-danger small mt-1">
                <i data-lucide="alert-circle" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;"></i>
                {{ $message }}
            </div>
        @enderror

        <div class="form-text small text-muted text-center mt-2">
            <i data-lucide="clock" style="width: 13px; height: 13px; display: inline-block; vertical-align: -2px;"></i>
            यो कोड १० मिनेटसम्म मात्र मान्य रहनेछ। कोड प्राप्त नभए Spam/Junk फोल्डर पनि जाँच्नुहोस्।
        </div>
    </div>

    <button type="submit" class="btn btn-auth-primary w-100 py-2.5 mb-3 fw-bold" id="verifyBtn">
        <i data-lucide="check-circle" class="me-1"></i> OTP प्रमाणीकरण गर्नुहोस्
    </button>
</form>

<!-- Resend OTP Form -->
<div class="text-center pt-2 pb-1 border-top">
    <div class="small text-muted mb-2">कोड प्राप्त भएन वा समय समाप्त भयो?</div>
    <form method="POST" action="{{ route('otp.resend') }}" id="resendForm">
        @csrf
        <button type="submit" 
                class="btn btn-sm btn-outline-secondary px-3 py-1.5 fw-semibold" 
                id="resendBtn" 
                @if($cooldown > 0) disabled @endif>
            <i data-lucide="refresh-cw" class="me-1" style="width: 13px; height: 13px;"></i>
            <span id="resendBtnText">
                @if($cooldown > 0)
                    पुनः पठाउन {{ $cooldown }} सेकेन्ड पर्खनुहोस्
                @else
                    नयाँ OTP पुनः पठाउनुहोस्
                @endif
            </span>
        </button>
    </form>
</div>

<!-- Cancel Registration / Switch Email -->
<div class="text-center mt-3 pt-2">
    <form method="POST" action="{{ route('otp.cancel') }}">
        @csrf
        <span class="text-muted small">इमेल वा विवरण सच्याउनु पर्नेछ?</span>
        <button type="submit" class="btn btn-link text-decoration-none small fw-semibold text-danger p-0 ms-1" style="vertical-align: baseline;">
            <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;" class="me-1"></i>रद्द गरी पुनः दर्ता गर्नुहोस्
        </button>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp');
        const resendBtn = document.getElementById('resendBtn');
        const resendBtnText = document.getElementById('resendBtnText');
        let remainingSeconds = {{ (int) $cooldown }};

        // Only allow numbers in OTP input
        if (otpInput) {
            otpInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                if (this.value.length === 6) {
                    document.getElementById('otpForm').requestSubmit();
                }
            });
        }

        // Live Resend countdown timer
        if (remainingSeconds > 0) {
            const timer = setInterval(function() {
                remainingSeconds--;
                if (remainingSeconds > 0) {
                    resendBtnText.textContent = `पुनः पठाउन ${remainingSeconds} सेकेन्ड पर्खनुहोस्`;
                    resendBtn.disabled = true;
                } else {
                    clearInterval(timer);
                    resendBtnText.textContent = 'नयाँ OTP पुनः पठाउनुहोस्';
                    resendBtn.disabled = false;
                }
            }, 1000);
        }
    });
</script>
@endpush
@endsection
