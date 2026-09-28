@extends('layouts.guest')

@section('title', 'Reset Password — बाह्रदशी गाउँपालिका')

@section('content')
<h4 class="auth-form-title">Reset Password</h4>
@if(!($otpVerified ?? false))
    <p class="auth-form-subtitle">तपाईंको इमेलमा पठाइएको ६ अङ्कको OTP कोड प्रविष्ट गर्नुहोस्।</p>
@else
    <p class="auth-form-subtitle">OTP प्रमाणित भयो। नयाँ पासवर्ड प्रविष्ट गर्नुहोस्।</p>
@endif

@if (session('status'))
    <div class="alert alert-success py-2 px-3 mb-3 small" role="alert">
        {{ session('status') }}
    </div>
@endif

@if(!($otpVerified ?? false))
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold small text-secondary">Email Address</label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i data-lucide="mail"></i></span>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $resetEmail ?? '') }}" required readonly>
            </div>
        </div>

        <div class="mb-3">
            <label for="otp" class="form-label fw-semibold small text-secondary">OTP Code</label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i data-lucide="shield-check"></i></span>
                <input type="text" class="form-control @error('otp') is-invalid @enderror" id="otp" name="otp" value="{{ old('otp') }}" required autofocus placeholder="Enter 6-digit code" maxlength="6" inputmode="numeric">
            </div>
            @error('otp')
                <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-auth-primary w-100 py-2 mb-3">
            <i data-lucide="check" class="me-1"></i> Verify OTP
        </button>
    </form>
@else
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold small text-secondary">Email Address</label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i data-lucide="mail"></i></span>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $resetEmail ?? '') }}" required readonly>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold small text-secondary">New Password</label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i data-lucide="lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
            </div>
            @error('password')
                <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Confirm Password</label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i data-lucide="shield-check"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn btn-auth-primary w-100 py-2 mb-3">
            <i data-lucide="check" class="me-1"></i> Reset Password
        </button>
    </form>
@endif
@endsection

