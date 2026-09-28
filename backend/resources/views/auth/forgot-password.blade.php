@extends('layouts.guest')

@section('title', 'पासवर्ड भुल्नुभयो — बाह्रदशी गाउँपालिका')

@section('content')
<h4 class="auth-form-title">Forgot Password?</h4>
<p class="auth-form-subtitle">आफ्नो दर्ता भएको इमेल प्रविष्ट गर्नुहोस् र हामी OTP कोड पठाउनेछौं।</p>

@if (session('status'))
    <div class="alert alert-success py-2 px-3 mb-3 small" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold small text-secondary">Email Address</label>
        <div class="input-group auth-input-group">
            <span class="input-group-text"><i data-lucide="mail"></i></span>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
        </div>
        @error('email')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-auth-primary w-100 py-2 mb-3">
        <i data-lucide="send" class="me-1"></i> Send OTP Code
    </button>

    <div class="text-center">
        <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold" style="color: #053775;"><i data-lucide="arrow-left" class="me-1"></i>Back to Sign In</a>
    </div>
</form>
@endsection

