@extends('layouts.guest')

@section('title', 'Confirm Password — GovServices')

@section('content')
<h3>Confirm Password</h3>
<p class="auth-subtitle">This is a secure area. Please confirm your password before continuing.</p>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i data-lucide="lock"></i></span>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
        <i data-lucide="shield-check" class="me-2"></i>Confirm Password
    </button>
</form>
@endsection
