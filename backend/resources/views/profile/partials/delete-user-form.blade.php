<section>
    <header class="mb-4">
        <h5 class="fw-bold text-dark">
            खाता मेटाउनुहोस् (Delete Account)
        </h5>
        <p class="text-muted small">
            एक पटक तपाईंको खाता मेटाइएपछि, यसका सबै डेटा र इतिहास स्थायी रूपमा मेटिनेछन्। खाता मेटाउनु अघि, तपाईंले राख्न चाहनुभएको कुनै पनि डेटा वा जानकारी डाउनलोड गर्नुहोस्।
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        <i data-lucide="trash-2" class="me-1"></i> खाता मेटाउनुहोस्
    </button>

    <!-- Deletion Confirmation Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger" id="confirmUserDeletionModalLabel">के तपाईं साँच्चै खाता मेटाउन चाहनुहुन्छ?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-start">
                    <p class="text-muted small">
                        खाता मेटाइएपछि सबै विवरणहरू स्थायी रूपमा नष्ट हुनेछन्। कृपया खाता मेटाउन पुष्टि गर्न तपाईंको पासवर्ड प्रविष्ट गर्नुहोस्।
                    </p>

                    <div class="mb-3">
                        <label for="password" class="form-label">तपाईंको पासवर्ड</label>
                        <input id="password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="पासवर्ड प्रविष्ट गर्नुहोस्" required />
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द गर्नुहोस्</button>
                    <button type="submit" class="btn btn-danger">स्थायी रूपमा खाता मेटाउनुहोस्</button>
                </div>
            </form>
        </div>
    </div>
</section>
