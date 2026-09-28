@extends('layouts.guest')

@section('title', 'नयाँ खाता खोल्नुहोस् — बाह्रदशी गाउँपालिका')

@section('content')
<h4 class="auth-form-title">Create Citizen Account</h4>
<p class="auth-form-subtitle">बाह्रदशी गाउँपालिका अनलाइन सेवाका लागि नयाँ नागरिक दर्ता</p>

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm small py-2 px-3 mb-3" role="alert">
        <i data-lucide="info" class="me-1" style="width: 15px; height: 15px;"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm small py-2 px-3 mb-3" role="alert">
        <i data-lucide="alert-circle" class="me-1" style="width: 15px; height: 15px;"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    <div class="alert alert-light border small text-muted mb-3 py-2 px-3 rounded-3" style="background: #f8fafc;">
        <i data-lucide="shield-check" class="me-1 text-danger" style="width: 15px; height: 15px; display: inline-block; vertical-align: -2px;"></i>
        खाता सुरक्षित राख्न दर्तापछि तपाईंको <strong>Gmail</strong> मा ६ अंकको <strong>OTP कोड</strong> पठाइनेछ। एक नागरिकको एउटा मात्र Gmail खाता मान्य हुनेछ।
    </div>

    <div class="mb-3">
        <label for="name" class="form-label fw-semibold small text-secondary">पूरा नाम (Full Name) <span class="text-danger">*</span></label>
        <div class="input-group auth-input-group">
            <span class="input-group-text"><i data-lucide="user"></i></span>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="राम बहादुर श्रेष्ठ">
        </div>
        @error('name')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold small text-secondary d-flex justify-content-between align-items-center">
            <span>आधिकारिक Gmail ठेगाना (Official Gmail) <span class="text-danger">*</span></span>
            <span class="badge bg-danger-subtle text-danger fw-semibold" style="font-size: 0.7rem;">Only @gmail.com</span>
        </label>
        <div class="input-group auth-input-group">
            <span class="input-group-text"><i data-lucide="mail"></i></span>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   pattern="[a-zA-Z0-9._%+-]+@gmail\.com"
                   title="कृपया @gmail.com भएको मान्य इमेल प्रविष्ट गर्नुहोस्"
                   placeholder="yourname@gmail.com">
        </div>
        <div class="form-text extra-small text-muted mt-1" style="font-size: 0.75rem;">
            यसै Gmail मा खाता प्रमाणीकरणको ६ अंकको OTP कोड पठाइनेछ।
        </div>
        @error('email')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label fw-semibold small text-secondary">मोबाइल नम्बर (Mobile Number) <span class="text-danger">*</span></label>
        <div class="input-group auth-input-group">
            <span class="input-group-text"><i data-lucide="phone"></i></span>
            <input type="tel" 
                   class="form-control @error('phone') is-invalid @enderror" 
                   id="phone" 
                   name="phone" 
                   value="{{ old('phone') }}" 
                   required
                   maxlength="10"
                   pattern="(98|97|96)[0-9]{8}"
                   title="कृपया १० अंकको नेपाली मोबाइल नम्बर प्रविष्ट गर्नुहोस् (उदा. 98XXXXXXXX)"
                   placeholder="९८XXXXXXXX">
        </div>
        @error('phone')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="address" class="form-label fw-semibold small text-secondary">स्थायी ठेगाना (Permanent Address) <span class="text-danger">*</span></label>
        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" required placeholder="बाह्रदशी गाउँपालिका, वडा नं. ...">{{ old('address') }}</textarea>
        @error('address')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label fw-semibold small text-secondary d-flex justify-content-between align-items-center">
            <span>पासवर्ड (Password) <span class="text-danger">*</span></span>
            <span id="strengthBadge" class="badge bg-secondary-subtle text-secondary fw-semibold" style="font-size: 0.72rem;">प्रविष्ट गर्नुहोस्</span>
        </label>
        <div class="input-group auth-input-group">
            <span class="input-group-text"><i data-lucide="lock"></i></span>
            <input type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   required 
                   autocomplete="new-password" 
                   placeholder="••••••••">
            <button type="button" class="input-group-text password-toggle-btn" id="togglePasswordBtn" title="पासवर्ड देखाउनुहोस् / लुकाउनुहोस्">
                <i data-lucide="eye" id="togglePasswordIcon" style="width: 15px; height: 15px;"></i>
            </button>
        </div>

        <!-- Live Password Strength Meter & Criteria Checklist -->
        <div class="password-criteria-card" id="passwordCriteriaCard">
            <div class="password-strength-wrap">
                <div class="password-strength-header">
                    <span>पासवर्डको सुरक्षा स्तर (Strength):</span>
                    <span id="strengthPercentText" class="font-monospace">०%</span>
                </div>
                <div class="password-strength-bar">
                    <div class="password-strength-fill" id="strengthFill"></div>
                </div>
            </div>

            <ul class="criteria-list">
                <li class="criteria-item" id="critLength">
                    <span class="criteria-icon-box">✗</span>
                    <span>कम्तीमा ८ अक्षर (At least 8 chars)</span>
                </li>
                <li class="criteria-item" id="critUppercase">
                    <span class="criteria-icon-box">✗</span>
                    <span>१ ठूलो अक्षर (1 uppercase A-Z)</span>
                </li>
                <li class="criteria-item" id="critNumber">
                    <span class="criteria-icon-box">✗</span>
                    <span>१ अंक (1 number 0-9)</span>
                </li>
                <li class="criteria-item" id="critSpecial">
                    <span class="criteria-icon-box">✗</span>
                    <span>१ विशेष चिन्ह (@$!%*?&#)</span>
                </li>
            </ul>
        </div>

        @error('password')
            <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label fw-semibold small text-secondary">
            पासवर्ड पुनः पुष्टि गर्नुहोस् (Confirm Password) <span class="text-danger">*</span>
        </label>
        <div class="input-group auth-input-group">
            <span class="input-group-text"><i data-lucide="shield-check"></i></span>
            <input type="password" 
                   class="form-control" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   required 
                   placeholder="••••••••">
            <button type="button" class="input-group-text password-toggle-btn" id="toggleConfirmPasswordBtn" title="पासवर्ड देखाउनुहोस् / लुकाउनुहोस्">
                <i data-lucide="eye" id="toggleConfirmPasswordIcon" style="width: 15px; height: 15px;"></i>
            </button>
        </div>
        <div class="password-match-status d-none" id="passwordMatchStatus"></div>
    </div>

    <button type="submit" class="btn btn-auth-primary w-100 py-2.5 mb-3 fw-bold" id="submitBtn">
        <i data-lucide="user-plus" class="me-1"></i> खाता दर्ता गर्नुहोस्
    </button>

    <div class="text-center">
        <span class="text-muted small">पहिले नै खाता छ?</span>
        <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold ms-1" style="color: #053775;">लगइन गर्नुहोस्</a>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const strengthFill = document.getElementById('strengthFill');
    const strengthBadge = document.getElementById('strengthBadge');
    const strengthPercentText = document.getElementById('strengthPercentText');
    const matchStatus = document.getElementById('passwordMatchStatus');

    const critLength = document.getElementById('critLength');
    const critUppercase = document.getElementById('critUppercase');
    const critNumber = document.getElementById('critNumber');
    const critSpecial = document.getElementById('critSpecial');

    const nepaliDigits = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
    const toNepaliNumber = (num) => String(num).split('').map(d => nepaliDigits[d] || d).join('');

    function updateCriterion(el, isValid) {
        if (!el) return;
        const iconBox = el.querySelector('.criteria-icon-box');
        if (isValid) {
            if (!el.classList.contains('valid')) {
                el.classList.add('valid');
                if (iconBox) iconBox.textContent = '✓';
            }
        } else {
            if (el.classList.contains('valid')) {
                el.classList.remove('valid');
                if (iconBox) iconBox.textContent = '✗';
            }
        }
    }

    function checkPasswordCriteria() {
        const val = passwordInput.value || '';

        const hasLength = val.length >= 8;
        const hasUpper = /[A-Z]/.test(val);
        const hasNum = /[0-9]/.test(val);
        const hasSpecial = /[@$!%*?&#^()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(val);

        updateCriterion(critLength, hasLength);
        updateCriterion(critUppercase, hasUpper);
        updateCriterion(critNumber, hasNum);
        updateCriterion(critSpecial, hasSpecial);

        let score = 0;
        if (hasLength) score++;
        if (hasUpper) score++;
        if (hasNum) score++;
        if (hasSpecial) score++;

        const percentage = score * 25;
        strengthFill.style.width = percentage + '%';
        strengthPercentText.textContent = toNepaliNumber(percentage) + '%';

        if (val.length === 0) {
            strengthFill.style.backgroundColor = '#e2e8f0';
            strengthBadge.className = 'badge bg-secondary-subtle text-secondary fw-semibold';
            strengthBadge.textContent = 'प्रविष्ट गर्नुहोस्';
        } else if (score === 1) {
            strengthFill.style.backgroundColor = '#ef4444';
            strengthBadge.className = 'badge bg-danger-subtle text-danger fw-semibold';
            strengthBadge.textContent = 'धेरै कमजोर';
        } else if (score === 2) {
            strengthFill.style.backgroundColor = '#f97316';
            strengthBadge.className = 'badge bg-warning-subtle text-warning-emphasis fw-semibold';
            strengthBadge.textContent = 'कमजोर';
        } else if (score === 3) {
            strengthFill.style.backgroundColor = '#3b82f6';
            strengthBadge.className = 'badge bg-primary-subtle text-primary fw-semibold';
            strengthBadge.textContent = 'राम्रो (Good)';
        } else if (score === 4) {
            strengthFill.style.backgroundColor = '#16a34a';
            strengthBadge.className = 'badge bg-success-subtle text-success fw-bold';
            strengthBadge.textContent = 'अति बलियो (Strong ✓)';
        }

        checkPasswordMatch();
    }

    function checkPasswordMatch() {
        const passVal = passwordInput.value || '';
        const confirmVal = confirmInput.value || '';

        if (!confirmVal) {
            matchStatus.className = 'password-match-status d-none';
            matchStatus.innerHTML = '';
            confirmInput.classList.remove('is-valid', 'is-invalid');
            return;
        }

        matchStatus.classList.remove('d-none');
        if (passVal === confirmVal) {
            matchStatus.className = 'password-match-status text-success';
            matchStatus.innerHTML = '<span class="fw-bold fs-6">✓</span> दुबै पासवर्ड मेल खायो (Passwords match)';
            confirmInput.classList.add('is-valid');
            confirmInput.classList.remove('is-invalid');
        } else {
            matchStatus.className = 'password-match-status text-danger';
            matchStatus.innerHTML = '<span class="fw-bold fs-6">✗</span> पासवर्ड मेल खाएन (Passwords do not match)';
            confirmInput.classList.add('is-invalid');
            confirmInput.classList.remove('is-valid');
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', checkPasswordCriteria);
    }
    if (confirmInput) {
        confirmInput.addEventListener('input', checkPasswordMatch);
    }

    // Toggle Eye Buttons
    function setupPasswordToggle(btnId, inputId, iconId) {
        const btn = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (btn && input) {
            btn.addEventListener('click', function() {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                
                // Toggle icon
                if (icon) {
                    if (isPassword) {
                        icon.setAttribute('data-lucide', 'eye-off');
                    } else {
                        icon.setAttribute('data-lucide', 'eye');
                    }
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });
        }
    }

    setupPasswordToggle('togglePasswordBtn', 'password', 'togglePasswordIcon');
    setupPasswordToggle('toggleConfirmPasswordBtn', 'password_confirmation', 'toggleConfirmPasswordIcon');

    // Prevent double submissions and show loading state while sending OTP email
    const regForm = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    if (regForm && submitBtn) {
        regForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>खाता सिर्जना हुँदैछ... कृपया पर्खनुहोस्';
        });
    }
});
</script>
@endpush
@endsection

