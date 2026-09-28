<section>
    <header class="mb-4">
        <h5 class="fw-bold text-dark">
            पासवर्ड अद्यावधिक (Update Password)
        </h5>
        <p class="text-muted small">
            खाता सुरक्षित राख्नको लागि बलियो र गोप्य पासवर्ड प्रयोग गर्नुहोस्।
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-3">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">हालको पासवर्ड <span class="text-danger">*</span></label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" required />
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">नयाँ पासवर्ड <span class="text-danger">*</span></label>
            <input id="update_password_password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required />
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">नयाँ पासवर्डको पुष्टि गर्नुहोस् <span class="text-danger">*</span></label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" required />
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary"><i data-lucide="save" class="me-1"></i> पासवर्ड सुरक्षित गर्नुहोस्</button>

            @if (session('status') === 'password-updated')
                <span class="text-success small fw-semibold"><i data-lucide="check-circle-2" class="me-1"></i>पासवर्ड अद्यावधिक गरियो।</span>
            @endif
        </div>
    </form>
</section>
