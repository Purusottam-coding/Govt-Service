@extends('layouts.citizen', ['pageTitle' => 'सरकारी सेवा दस्तुर भुक्तानी'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="credit-card" class="me-2 text-primary"></i>सरकारी सेवा आवेदन भुक्तानी</h6>
            </div>
            <div class="card-body p-4">
                <!-- Payment Summary Box -->
                <div class="bg-light p-3 rounded mb-4 border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">निवेदन नम्बर:</span>
                        <span class="fw-bold text-primary">{{ $application->application_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">सेवाको नाम:</span>
                        <span class="fw-semibold text-dark">{{ $application->service->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-2">
                        <span class="fw-bold text-dark">कुल बुझाउनुपर्ने दस्तुर:</span>
                        <span class="fw-bold text-success fs-5">रु. {{ number_format($application->service->fee, 2) }}</span>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div id="qrCodeSection" class="alert alert-success mb-4 text-center d-none">
                    <i data-lucide="qr-code" class="fs-2 mb-2 d-block mx-auto"></i>
                    <h6 class="fw-bold mb-2" id="qrCodeTitle">QR कोड स्क्यान गर्नुहोस्</h6>
                    <img id="qrCodeImage" src="" alt="Payment QR Code" class="img-fluid mx-auto d-block" style="max-width: 200px; border: 3px solid #10b981; border-radius: 12px;">
                    <p class="small mb-0 mt-2" id="qrCodeInstruction">माथिको QR कोड स्क्यान गरी भुक्तानी गर्नुहोस्</p>
                </div>

                <!-- Hidden QR code data -->
                @foreach($qrCodes as $type => $qrCode)
                    <div id="qrCodeData_{{ $type }}" data-url="{{ $qrCode->qr_code_url }}" data-label="{{ $qrCode->getQrTypeLabel() }}" class="d-none"></div>
                @endforeach

                <form action="{{ route('citizen.payments.store', $application) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="payment_method" class="form-label fw-bold">भुक्तानीको माध्यम छान्नुहोस् <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select form-select-lg @error('payment_method') is-invalid @enderror" required>
                            <option value="esewa">eSewa</option>
                            <option value="khalti">Khalti</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="cash">सरकारी काउन्टर (नगद)</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Secure Payment Info Notice -->
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-4 py-2 small">
                        <i data-lucide="shield-check" class="text-info flex-shrink-0" style="width:20px;height:20px;"></i>
                        <div>अनलाइन भुक्तानी छनोट गर्दा तपाईंलाई सुरक्षित भुक्तानी गेटवे (eSewa / Khalti) मा पठाइनेछ।</div>
                    </div>

                    <div class="mb-4" id="statementUploadGroup">
                        <label for="payment_statement" class="form-label fw-bold">भुक्तानी स्टेटमेन्ट / प्रमाण फोटो <span class="text-danger" id="statementRequiredMark">*</span></label>
                        <input
                            type="file"
                            name="payment_statement"
                            id="payment_statement"
                            accept="image/png,image/jpeg,image/webp"
                            class="form-control @error('payment_statement') is-invalid @enderror"
                        >
                        <div class="form-text">QR स्क्यान गरेर भुक्तानी गरेपछि screenshot वा payment statement image अपलोड गर्नुहोस् (JPG, PNG, WEBP, max 4MB)।</div>
                        @error('payment_statement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                        <i data-lucide="lock" class="me-1"></i> रु. {{ number_format($application->service->fee, 2) }} भुक्तानी प्रमाण पेश गर्नुहोस्
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const qrCodeSection = document.getElementById('qrCodeSection');
        const qrCodeImage = document.getElementById('qrCodeImage');
        const qrCodeTitle = document.getElementById('qrCodeTitle');
        const qrCodeInstruction = document.getElementById('qrCodeInstruction');
        const paymentStatementInput = document.getElementById('payment_statement');
        const statementUploadGroup = document.getElementById('statementUploadGroup');
        const statementRequiredMark = document.getElementById('statementRequiredMark');

        // Function to show QR code based on selected payment method
        function showQrCode() {
            const selectedMethod = paymentMethodSelect.value;
            const qrCodeData = document.getElementById('qrCodeData_' + selectedMethod);

            if (qrCodeData) {
                const qrUrl = qrCodeData.getAttribute('data-url');
                const qrLabel = qrCodeData.getAttribute('data-label');

                qrCodeImage.src = qrUrl;
                qrCodeTitle.textContent = 'QR कोड स्क्यान गर्नुहोस् - ' + qrLabel;
                qrCodeInstruction.textContent = 'माथिको QR कोड स्क्यान गरी ' + qrLabel + ' मा रु. {{ number_format($application->service->fee, 2) }} भुक्तानी गर्नुहोस्';
                qrCodeSection.classList.remove('d-none');
            } else {
                qrCodeSection.classList.add('d-none');
            }

            const isCashPayment = selectedMethod === 'cash';
            if (isCashPayment) {
                statementUploadGroup.classList.add('d-none');
                statementRequiredMark.classList.add('d-none');
                paymentStatementInput.required = false;
            } else {
                statementUploadGroup.classList.remove('d-none');
                statementRequiredMark.classList.remove('d-none');
                paymentStatementInput.required = true;
            }
        }

        // Add event listener to payment method select
        paymentMethodSelect.addEventListener('change', showQrCode);

        // Check on page load if there's a QR code for the default selection
        showQrCode();
    });
</script>
@endpush
@endsection
