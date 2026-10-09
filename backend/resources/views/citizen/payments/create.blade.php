@extends('layouts.citizen', ['pageTitle' => 'सरकारी सेवा दस्तुर भुक्तानी'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark"><i data-lucide="credit-card" class="me-2 text-primary"></i>सरकारी सेवा आवेदन भुक्तानी</h6>
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

                <!-- Instant Online Payment Gateways Section -->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-bold text-dark"><i data-lucide="zap" class="me-1 text-warning"></i>द्रुत अनलाइन भुक्तानी गेटवे (तत्काल प्रमाणीकरण)</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">सिफारिस गरिएको</span>
                    </div>
                    <div class="row g-3">
                        <!-- eSewa Gateway Card -->
                        <div class="col-12 col-sm-6">
                            <a href="{{ route('citizen.payments.esewa.initiate', $application) }}" class="btn w-100 text-start p-3 border rounded shadow-sm d-flex flex-column justify-content-between text-white text-decoration-none" style="background: linear-gradient(135deg, #60bb46 0%, #43962d 100%); min-height: 110px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-6">eSewa ePay</span>
                                    <span class="badge bg-white text-success fw-bold px-2">१-क्लिक</span>
                                </div>
                                <div class="small opacity-90 mb-2">
                                    eSewa वालेटबाट सिधै भुक्तानी
                                </div>
                                <div class="fw-semibold small d-flex align-items-center justify-content-between">
                                    <span>रु. {{ number_format($application->service->fee, 2) }} तिर्नुहोस्</span>
                                    <span>&rarr;</span>
                                </div>
                            </a>
                        </div>

                        <!-- Khalti Gateway Card -->
                        <div class="col-12 col-sm-6">
                            <a href="{{ route('citizen.payments.khalti.initiate', $application) }}" class="btn w-100 text-start p-3 border rounded shadow-sm d-flex flex-column justify-content-between text-white text-decoration-none" style="background: linear-gradient(135deg, #5c2d91 0%, #3e1669 100%); min-height: 110px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-6">Khalti ePayment</span>
                                    <span class="badge bg-white text-primary fw-bold px-2" style="color: #5c2d91 !important;">१-क्लिक</span>
                                </div>
                                <div class="small opacity-90 mb-2">
                                    वालेट वा बैंकिङबाट सिधै भुक्तानी
                                </div>
                                <div class="fw-semibold small d-flex align-items-center justify-content-between">
                                    <span>रु. {{ number_format($application->service->fee, 2) }} तिर्नुहोस्</span>
                                    <span>&rarr;</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="form-text mt-2 text-muted small">
                        <i data-lucide="check-circle" class="me-1 text-success" style="width: 14px; height: 14px;"></i>
                        अनलाइन गेटवेबाट भुक्तानी गर्दा स्वतः प्रमाणिकरण भई तत्काल रसिद डाउनलोड गर्न सकिन्छ।
                    </div>
                </div>

                <!-- Separator -->
                <div class="position-relative my-4 text-center">
                    <hr class="text-muted">
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-semibold">
                        वा QR भौचर स्क्यान / सरकारी काउन्टर
                    </span>
                </div>

                <!-- QR Code Section -->
                <div id="qrCodeSection" class="alert alert-success mb-4 text-center d-none">
                    <i data-lucide="qr-code" class="fs-2 mb-2 d-block mx-auto text-success"></i>
                    <h6 class="fw-bold mb-2 text-dark" id="qrCodeTitle">QR कोड स्क्यान गर्नुहोस्</h6>
                    <img id="qrCodeImage" src="" alt="Payment QR Code" class="img-fluid mx-auto d-block" style="max-width: 200px; border: 3px solid #10b981; border-radius: 12px;">
                    <p class="small mb-0 mt-2 text-muted" id="qrCodeInstruction">माथिको QR कोड स्क्यान गरी भुक्तानी गर्नुहोस्</p>
                </div>

                <!-- Hidden QR code data -->
                @foreach($qrCodes as $type => $qrCode)
                    <div id="qrCodeData_{{ $type }}" data-url="{{ $qrCode->qr_code_url }}" data-label="{{ $qrCode->getQrTypeLabel() }}" class="d-none"></div>
                @endforeach

                <form action="{{ route('citizen.payments.store', $application) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="payment_method" class="form-label fw-bold text-dark">म्यानुअल भुक्तानी माध्यम छान्नुहोस् <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select form-select-lg @error('payment_method') is-invalid @enderror" required>
                            <option value="mobile_banking">Mobile Banking QR</option>
                            <option value="esewa">eSewa QR</option>
                            <option value="khalti">Khalti QR</option>
                            <option value="cash">सरकारी काउन्टर (नगद)</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4" id="statementUploadGroup">
                        <label for="payment_statement" class="form-label fw-bold text-dark">भुक्तानी स्टेटमेन्ट / भौचर फोटो <span class="text-danger" id="statementRequiredMark">*</span></label>
                        <input
                            type="file"
                            name="payment_statement"
                            id="payment_statement"
                            accept="image/png,image/jpeg,image/webp"
                            class="form-control @error('payment_statement') is-invalid @enderror"
                        >
                        <div class="form-text">QR स्क्यान गरेर भुक्तानी गरेपछि screenshot वा भौचर फोटो अपलोड गर्नुहोस् (JPG, PNG, WEBP, max 4MB)।</div>
                        @error('payment_statement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-outline-primary btn-lg w-100 fw-bold">
                        <i data-lucide="upload" class="me-1"></i> रु. {{ number_format($application->service->fee, 2) }} भौचर प्रमाण पेश गर्नुहोस्
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

        paymentMethodSelect.addEventListener('change', showQrCode);
        showQrCode();
    });
</script>
@endpush
@endsection
