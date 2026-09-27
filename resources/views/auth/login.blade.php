@extends('layouts.guest')

@section('title', 'Sign In — बाह्रदशी गाउँपालिका')

@section('content')
@php
    $defaultRole = old('role', ($rememberedRole ?: 'admin'));
    $initialEmail = old('email', '');
    if (!$initialEmail) {
        if ($defaultRole === 'admin' && !empty($rememberedAdminEmail)) {
            $initialEmail = $rememberedAdminEmail;
        } elseif ($defaultRole === 'citizen' && !empty($rememberedCitizenEmail)) {
            $initialEmail = $rememberedCitizenEmail;
        } else {
            $initialEmail = $rememberedEmail ?? '';
        }
    }
    $isRememberChecked = old('remember') ? true : (!empty($rememberMeChecked) && !empty($initialEmail));
@endphp

<h4 class="auth-form-title mb-3">खातामा प्रवेश गर्नुहोस् (Sign In)</h4>

@if (session('status'))
    <div class="alert alert-success py-2 px-3 mb-3 small" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf

    <!-- Role Selection Tabs -->
    <div class="mb-3">
        <label class="form-label fw-semibold small text-secondary">प्रवेश भूमिका चयन गर्नुहोस् (Login As) <span class="text-danger">*</span></label>
        <div class="auth-role-tabs d-flex p-1 bg-light border rounded-3 mb-2">
            <button type="button" 
                    class="auth-role-tab flex-fill btn py-2 fw-semibold {{ $defaultRole === 'admin' ? 'active' : '' }}" 
                    id="tabAdmin" 
                    onclick="setRole('admin', true)">
                <i data-lucide="shield-check" class="me-1"></i> प्रशासक (Admin)
            </button>
            <button type="button" 
                    class="auth-role-tab flex-fill btn py-2 fw-semibold {{ $defaultRole === 'citizen' ? 'active' : '' }}" 
                    id="tabCitizen" 
                    onclick="setRole('citizen', true)">
                <i data-lucide="user" class="me-1"></i> नागरिक (Citizen)
            </button>
        </div>
        <input type="hidden" name="role" id="selected_role" value="{{ $defaultRole }}">
        @error('role')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror

        <div class="mt-1">
            <span class="text-muted extra-small" id="roleInfoText">
                <i data-lucide="shield-check" style="width: 12px; height: 12px;" class="me-1"></i>प्रशासक खाताबाट मात्र लगइन हुनेछ
            </span>
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold small text-secondary">Email Address</label>
        <div class="input-group auth-input-group">
            <span class="input-group-text"><i data-lucide="mail"></i></span>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ $initialEmail }}" 
                   required 
                   autofocus 
                   placeholder="{{ $defaultRole === 'admin' ? 'admin@gov.np' : 'citizen@test.np वा तपाईंको इमेल' }}">
        </div>
        @error('email')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-semibold small text-secondary mb-0">Password</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none small fw-semibold" style="color: #053775;">पासवर्ड बिर्सनुभयो?</a>
            @endif
        </div>
        <div class="input-group auth-input-group">
            <span class="input-group-text"><i data-lucide="key-round"></i></span>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            <button type="button" class="input-group-text input-group-text-btn" id="togglePasswordBtn" onclick="togglePasswordVisibility()" title="पासवर्ड हेर्नुहोस्/लुकाउनुहोस्">
                <i data-lucide="eye" id="togglePasswordIcon"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember_me" name="remember" {{ $isRememberChecked ? 'checked' : '' }}>
        <label class="form-check-label small text-muted" for="remember_me">मलाई सम्झनुहोस् (Remember me)</label>
    </div>

    <button type="submit" class="btn btn-auth-primary w-100 py-2 mb-3" id="submitBtn">
        <i data-lucide="log-in" class="me-1"></i> Sign In
    </button>

    <div class="text-center" id="registerPrompt">
        <span class="text-muted small">खाता छैन?</span>
        <a href="{{ route('register') }}" class="text-decoration-none small fw-semibold ms-1" style="color: #053775;">नयाँ नागरिक खाता खोल्नुहोस्</a>
    </div>
</form>

@push('scripts')
<script>
    const rememberedEmails = {
        admin: @json($rememberedAdminEmail ?? ''),
        citizen: @json($rememberedCitizenEmail ?? ''),
        default: @json($rememberedEmail ?? '')
    };

    // Check localStorage fallback for instant client-side retrieval
    try {
        if (!rememberedEmails.admin && localStorage.getItem('remembered_admin_email')) {
            rememberedEmails.admin = localStorage.getItem('remembered_admin_email');
        }
        if (!rememberedEmails.citizen && localStorage.getItem('remembered_citizen_email')) {
            rememberedEmails.citizen = localStorage.getItem('remembered_citizen_email');
        }
    } catch(e) {}

    function setRole(role, isManualSwitch = false) {
        document.getElementById('selected_role').value = role;
        const tabCitizen = document.getElementById('tabCitizen');
        const tabAdmin = document.getElementById('tabAdmin');
        const emailInput = document.getElementById('email');
        const roleInfoText = document.getElementById('roleInfoText');
        const registerPrompt = document.getElementById('registerPrompt');
        const rememberCheckbox = document.getElementById('remember_me');

        if (role === 'admin') {
            tabAdmin.classList.add('active');
            tabCitizen.classList.remove('active');
            emailInput.placeholder = 'admin@gov.np';
            roleInfoText.innerHTML = '<i data-lucide="shield-check" style="width: 12px; height: 12px;" class="me-1"></i>प्रशासक खाताबाट मात्र लगइन हुनेछ';
            registerPrompt.classList.add('d-none');
        } else {
            tabCitizen.classList.add('active');
            tabAdmin.classList.remove('active');
            emailInput.placeholder = 'citizen@gov.np';
            roleInfoText.innerHTML = '<i data-lucide="user" style="width: 12px; height: 12px;" class="me-1"></i>नागरिक खाताबाट मात्र लगइन हुनेछ';
            registerPrompt.classList.remove('d-none');
        }

        if (isManualSwitch) {
            const otherRole = role === 'admin' ? 'citizen' : 'admin';
            if (emailInput.value === '' || emailInput.value === rememberedEmails[otherRole]) {
                if (rememberedEmails[role]) {
                    emailInput.value = rememberedEmails[role];
                    rememberCheckbox.checked = true;
                } else {
                    emailInput.value = '';
                    rememberCheckbox.checked = false;
                }
            }
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Save remembered email to localStorage on form submit for instant switching
    document.getElementById('loginForm').addEventListener('submit', function() {
        const role = document.getElementById('selected_role').value;
        const email = document.getElementById('email').value.trim();
        const isRemember = document.getElementById('remember_me').checked;

        try {
            if (isRemember && email) {
                if (role === 'admin') {
                    localStorage.setItem('remembered_admin_email', email);
                } else {
                    localStorage.setItem('remembered_citizen_email', email);
                }
                localStorage.setItem('remembered_role', role);
            } else {
                if (role === 'admin') {
                    localStorage.removeItem('remembered_admin_email');
                } else {
                    localStorage.removeItem('remembered_citizen_email');
                }
            }
        } catch(e) {}
    });

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        if (!passwordInput) return;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialRole = document.getElementById('selected_role').value || 'admin';
        setRole(initialRole, false);

        const emailInput = document.getElementById('email');
        if (!emailInput.value && rememberedEmails[initialRole]) {
            emailInput.value = rememberedEmails[initialRole];
            document.getElementById('remember_me').checked = true;
        }
    });
</script>
@endpush
@endsection

