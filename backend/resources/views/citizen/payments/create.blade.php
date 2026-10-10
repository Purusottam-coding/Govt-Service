@extends('layouts.citizen', ['pageTitle' => 'सरकारी सेवा दस्तुर भुक्तानी'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark">
                    <i data-lucide="credit-card" class="me-2 text-primary"></i>सरकारी सेवा आवेदन भुक्तानी
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    #{{ $application->application_number }}
                </span>
            </div>
            <div class="card-body p-4">
                <!-- Payment Summary Box -->
                <div class="bg-light p-3 rounded mb-4 border">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-sm-7">
                            <span class="text-muted small d-block">सेवाको नाम:</span>
                            <span class="fw-bold text-dark fs-6">{{ $application->service->name }}</span>
                        </div>
                        <div class="col-12 col-sm-5 text-sm-end">
                            <span class="text-muted small d-block">कुल बुझाउनुपर्ने दस्तुर:</span>
                            <span class="fw-bold text-success fs-5">रु. {{ number_format($application->service->fee, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection Grid -->
                <label class="form-label fw-bold text-dark mb-2">भुक्तानीको माध्यम छान्नुहोस् <span class="text-danger">*</span></label>
                <div class="row g-3 mb-4">
                    <!-- 1. eSewa Direct Payment Card -->
                    <div class="col-6 col-md-3">
                        <form id="esewaDirectForm" action="{{ $esewaActionUrl ?? 'https://rc-epay.esewa.com.np/api/epay/main/v2/form' }}" method="POST" class="h-100">
                            @if(isset($esewaPayload))
                                @foreach($esewaPayload as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            @endif
                            <button type="submit" class="payment-action-card card w-100 h-100 p-3 text-center border text-decoration-none bg-white cursor-pointer d-flex flex-column justify-content-center align-items-center" style="border: 1.5px solid #60bb46 !important; min-height: 110px;">
                                <div class="d-flex justify-content-center align-items-center mb-1" style="height: 48px;">
                                    <img src="{{ asset('images/esewa-logo.png') }}" alt="eSewa" style="max-height: 44px; max-width: 140px; object-fit: contain;">
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 0.95rem;">eSewa</span>
                            </button>
                        </form>
                    </div>

                    <!-- 2. Khalti Direct Payment Card -->
                    <div class="col-6 col-md-3">
                        <a href="{{ route('citizen.payments.khalti.initiate', $application) }}" class="payment-action-card card h-100 p-3 text-center border text-decoration-none bg-white cursor-pointer d-flex flex-column justify-content-center align-items-center" style="border: 1.5px solid #e11d48 !important; min-height: 110px;">
                            <div class="d-flex justify-content-center align-items-center mb-1" style="height: 48px;">
                                <img src="{{ asset('images/khalti-logo.png') }}" alt="Khalti" style="max-height: 44px; max-width: 140px; object-fit: contain;">
                            </div>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">Khalti</span>
                        </a>
                    </div>

                    <!-- 3. QR Scan & Voucher Option Card -->
                    <div class="col-6 col-md-3">
                        <div class="payment-action-card card h-100 p-3 text-center border cursor-pointer bg-white d-flex flex-column justify-content-center align-items-center" id="card_qr" onclick="toggleOption('qr')" style="border: 1.5px solid #0ea5e9 !important; min-height: 110px;">
                            <div class="d-flex justify-content-center align-items-center mb-1" style="height: 48px;">
                                <span class="badge bg-info-subtle text-info p-2 rounded-circle">
                                    <i data-lucide="qr-code" style="width: 28px; height: 28px;"></i>
                                </span>
                            </div>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">QR स्क्यान</span>
                        </div>
                    </div>

                    <!-- 4. Cash Counter Option Card -->
                    <div class="col-6 col-md-3">
                        <div class="payment-action-card card h-100 p-3 text-center border cursor-pointer bg-white d-flex flex-column justify-content-center align-items-center" id="card_cash" onclick="toggleOption('cash')" style="border: 1.5px solid #64748b !important; min-height: 110px;">
                            <div class="d-flex justify-content-center align-items-center mb-1" style="height: 48px;">
                                <span class="badge bg-secondary-subtle text-secondary p-2 rounded-circle">
                                    <i data-lucide="building-2" style="width: 28px; height: 28px;"></i>
                                </span>
                            </div>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">नगद भुक्तानी</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden QR code sources -->
                @foreach($qrCodes as $type => $qrCode)
                    <div id="qrCodeData_{{ $type }}" data-url="{{ $qrCode->qr_code_url }}" data-label="{{ $qrCode->getQrTypeLabel() }}" class="d-none"></div>
                @endforeach

                <!-- ========================================== -->
                <!-- QR SCAN & VOUCHER UPLOAD PANEL             -->
                <!-- (Shown ONLY when QR card is clicked!)      -->
                <!-- ========================================== -->
                <div id="panel_qr" class="payment-panel d-none mt-4 pt-3 border-top">
                    <form action="{{ route('citizen.payments.store', $application) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- QR Code Type Selector -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">कुन वालेट / बैंकको QR स्क्यान गर्ने छान्नुहोस्:</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="payment_method" id="qr_opt_mobile_banking" value="mobile_banking" checked onchange="updateQrDisplay('mobile_banking')">
                                <label class="btn btn-outline-primary" for="qr_opt_mobile_banking">Mobile Banking QR</label>

                                <input type="radio" class="btn-check" name="payment_method" id="qr_opt_esewa" value="esewa" onchange="updateQrDisplay('esewa')">
                                <label class="btn btn-outline-success" for="qr_opt_esewa">eSewa QR</label>

                                <input type="radio" class="btn-check" name="payment_method" id="qr_opt_khalti" value="khalti" onchange="updateQrDisplay('khalti')">
                                <label class="btn btn-outline-secondary" for="qr_opt_khalti">Khalti QR</label>
                            </div>
                        </div>

                        <!-- QR Code Image Box -->
                        <div id="qrCodeSection" class="alert alert-success mb-3 text-center p-3">
                            <i data-lucide="qr-code" class="fs-2 mb-2 d-block mx-auto text-success"></i>
                            <h6 class="fw-bold mb-2 text-dark" id="qrCodeTitle">QR कोड स्क्यान गर्नुहोस्</h6>
                            <img id="qrCodeImage" src="" alt="Payment QR Code" class="img-fluid mx-auto d-block bg-white p-2 rounded shadow-sm" style="max-width: 190px; border: 2px solid #10b981;">
                            <p class="small mb-0 mt-2 text-muted" id="qrCodeInstruction">माथिको QR कोड स्क्यान गरी भुक्तानी गर्नुहोस्</p>
                        </div>

                        <!-- Statement / Voucher Upload Input -->
                        <div class="mb-3">
                            <label for="payment_statement" class="form-label fw-bold text-dark small">
                                भुक्तानी भौचर / Screenshot फोटो <span class="text-danger">*</span>
                            </label>
                            <input
                                type="file"
                                name="payment_statement"
                                id="payment_statement"
                                accept="image/png,image/jpeg,image/webp"
                                class="form-control @error('payment_statement') is-invalid @enderror"
                            >
                            <div class="form-text small">QR स्क्यान गरेर भुक्तानी गरेपछि प्राप्त screenshot वा भौचर फोटो अपलोड गर्नुहोस् (JPG, PNG, WEBP, max 4MB)।</div>
                            @error('payment_statement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i data-lucide="upload" class="me-1"></i> रु. {{ number_format($application->service->fee, 2) }} भौचर प्रमाण पेश गर्नुहोस्
                        </button>
                    </form>
                </div>

                <!-- ========================================== -->
                <!-- CASH COUNTER PANEL                         -->
                <!-- (Shown ONLY when Cash card is clicked!)    -->
                <!-- ========================================== -->
                <div id="panel_cash" class="payment-panel d-none mt-4 pt-3 border-top">
                    <form action="{{ route('citizen.payments.store', $application) }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="cash">

                        <div class="card border rounded-3 p-4 mb-3 bg-light">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="p-3 bg-white rounded-circle border shadow-sm">
                                    <i data-lucide="building-2" class="text-primary" style="width: 28px; height: 28px;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">गाउँपालिका राजस्व काउन्टर (नगद भुक्तानी)</h6>
                                    <p class="text-muted small mb-0">बाह्रदशी गाउँ कार्यपालिकाको कार्यालय, झापा</p>
                                </div>
                            </div>
                            <div class="alert alert-info py-2 px-3 small mb-3">
                                <i data-lucide="info" class="me-1" style="width:14px;height:14px;display:inline;"></i>
                                यो विकल्प छान्नुभएमा तपाईंले निवेदन दर्ता गरी कार्यालयको राजस्व काउन्टरमा रु. {{ number_format($application->service->fee, 2) }} नगद बुझाउनुपर्नेछ।
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-2 fw-bold">
                                <i data-lucide="check-circle" class="me-1"></i> काउन्टरमा नगद भुक्तानीको लागि दर्ता गर्नुहोस्
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleOption(type) {
        const qrCard = document.getElementById('card_qr');
        const cashCard = document.getElementById('card_cash');
        const qrPanel = document.getElementById('panel_qr');
        const cashPanel = document.getElementById('panel_cash');

        if (type === 'qr') {
            const isCurrentlyOpen = !qrPanel.classList.contains('d-none');
            
            cashPanel.classList.add('d-none');
            cashCard.style.borderColor = '#e2e8f0';
            cashCard.style.backgroundColor = '#ffffff';

            if (isCurrentlyOpen) {
                qrPanel.classList.add('d-none');
                qrCard.style.borderColor = '#e2e8f0';
                qrCard.style.backgroundColor = '#ffffff';
            } else {
                qrPanel.classList.remove('d-none');
                qrCard.style.borderColor = '#0ea5e9';
                qrCard.style.backgroundColor = '#f0f9ff';
                const checkedQr = document.querySelector('input[name="payment_method"]:checked');
                updateQrDisplay(checkedQr ? checkedQr.value : 'mobile_banking');
            }
        } else if (type === 'cash') {
            const isCurrentlyOpen = !cashPanel.classList.contains('d-none');

            qrPanel.classList.add('d-none');
            qrCard.style.borderColor = '#e2e8f0';
            qrCard.style.backgroundColor = '#ffffff';

            if (isCurrentlyOpen) {
                cashPanel.classList.add('d-none');
                cashCard.style.borderColor = '#e2e8f0';
                cashCard.style.backgroundColor = '#ffffff';
            } else {
                cashPanel.classList.remove('d-none');
                cashCard.style.borderColor = '#64748b';
                cashCard.style.backgroundColor = '#f8fafc';
            }
        }
    }

    function updateQrDisplay(type) {
        const qrCodeData = document.getElementById('qrCodeData_' + type);
        const qrCodeSection = document.getElementById('qrCodeSection');
        const qrCodeImage = document.getElementById('qrCodeImage');
        const qrCodeTitle = document.getElementById('qrCodeTitle');
        const qrCodeInstruction = document.getElementById('qrCodeInstruction');

        if (qrCodeData && qrCodeImage) {
            const qrUrl = qrCodeData.getAttribute('data-url');
            const qrLabel = qrCodeData.getAttribute('data-label') || type;

            if (qrUrl) {
                qrCodeImage.src = qrUrl;
                qrCodeImage.style.display = 'block';
            } else {
                qrCodeImage.style.display = 'none';
            }

            qrCodeTitle.textContent = 'QR कोड स्क्यान गर्नुहोस् (' + qrLabel + ')';
            qrCodeInstruction.textContent = 'माथिको QR स्क्यान गरी ' + qrLabel + ' मा रु. {{ number_format($application->service->fee, 2) }} भुक्तानी गर्नुहोस्';
        }
    }
</script>
<style>
    .cursor-pointer {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .payment-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush
@endsection
