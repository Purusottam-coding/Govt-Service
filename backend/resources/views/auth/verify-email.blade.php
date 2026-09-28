@extends('layouts.guest')

@section('title', 'Verify Email — GovServices')

@section('content')
<h3>Verify Email</h3>
<p class="auth-subtitle">Thanks for signing up! Please verify your email address by clicking on the link sent to your inbox.</p>

@if (session('status') == 'verification-link-sent')
    <div class="alert alert-success mb-3" role="alert">
        A new verification link has been sent to the email address you provided during registration.
    </div>
@endif

<div class="d-flex flex-column gap-2">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary w-100 py-2">
            <i data-lucide="envelope-paper" class="me-2"></i>Resend Verification Email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary w-100 py-2">
            Log Out
        </button>
    </form>
</div>
@endsection
