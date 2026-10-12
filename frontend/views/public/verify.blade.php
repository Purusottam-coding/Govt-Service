<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>आधिकारिक कागजात प्रमाणीकरण - बाह्रदशी गाउँ कार्यपालिकाको कार्यालय</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gov-blue: #003893;
            --gov-dark-blue: #05264E;
            --gov-red: #DC2626;
            --verified-green: #059669;
        }
        body {
            font-family: 'Mukta', 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            color: #1e293b;
            padding-bottom: 3rem;
        }
        .header-stripe {
            height: 6px;
            background: linear-gradient(90deg, #DC2626 0%, #003893 100%);
        }
        .official-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.5rem 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .emblem-img {
            max-height: 75px;
            object-fit: contain;
        }
        .verification-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .verified-banner {
            background: linear-gradient(135deg, #065f46 0%, #059669 100%);
            color: #ffffff;
            padding: 1.5rem 2rem;
        }
        .failed-banner {
            background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
            color: #ffffff;
            padding: 1.5rem 2rem;
        }
        .info-pill {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .official-seal-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #ecfdf5;
            color: #065f46;
            border: 1.5px solid #a7f3d0;
            border-radius: 30px;
            padding: 0.4rem 1.25rem;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .qr-placeholder-box {
            background: linear-gradient(135deg, #f8fafc 0%, #ede9fe 100%);
            border: 2px dashed #a855f7;
            border-radius: 20px;
            padding: 3rem 2rem;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .verification-card {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }
        }
    </style>
</head>
<body>
    <div class="header-stripe"></div>

    <!-- Official Top Government Branding -->
    <header class="official-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/Emblem_of_Nepal.png') }}" alt="नेपाल सरकार निशान छाप" class="emblem-img" onerror="this.style.display='none'">
                    <div>
                        <span class="text-danger fw-bold small d-block">नेपाल सरकार | कोशी प्रदेश</span>
                        <h4 class="fw-bold mb-0 text-dark" style="color: var(--gov-blue) !important; letter-spacing: -0.3px;">बाह्रदशी गाउँ कार्यपालिकाको कार्यालय</h4>
                        <p class="text-muted small mb-0">चकचकी, झापा • डिजिटल अभिलेख तथा सार्वजनिक प्रमाणीकरण</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 no-print">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-7">
                        <i class="bi bi-shield-check me-1"></i> आधिकारिक प्रमाणीकरण पोर्टल
                    </span>
                    <a href="{{ route('welcome') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-house me-1"></i> गृहपृष्ठ
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container my-4 my-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">

                @if($searched && $application)
                    <!-- ========================================== -->
                    <!-- 1. VERIFIED DOCUMENT DETAILS (ON QR SCAN)   -->
                    <!-- ========================================== -->
                    <div class="verification-card">
                        <!-- Success Banner -->
                        <div class="verified-banner">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-patch-check-fill fs-2"></i>
                                    <div>
                                        <h4 class="fw-bold mb-0 text-white">कागजात सत्यता प्रमाणित (VERIFIED)</h4>
                                        <p class="mb-0 text-white-50 small">गाउँ कार्यपालिकाको विद्युतीय अभिलेख प्रणालीसँग १००% पुष्टि भएको छ</p>
                                    </div>
                                </div>
                                <span class="badge bg-white text-success px-3 py-2 fw-bold" style="font-size: 0.85rem;">
                                    <i class="bi bi-check2-circle me-1"></i> VALID & AUTHENTIC
                                </span>
                            </div>
                        </div>

                        <div class="p-4 p-md-5">
                            <div class="text-center mb-4">
                                <span class="official-seal-badge mb-2">
                                    <i class="bi bi-shield-fill-check"></i> बाह्रदशी गाउँपालिका आधिकारिक डिजिटल छाप
                                </span>
                                <h5 class="fw-bold text-dark mt-2 mb-1">{{ $application->service->name }}</h5>
                                <span class="text-muted small">शाखा: <strong>{{ $application->service->department->name ?? 'सामान्य प्रशासन शाखा' }}</strong></span>
                            </div>

                            <!-- Document Details Grid -->
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <div class="info-pill h-100">
                                        <span class="text-muted small d-block">प्रमाणपत्र / सिफारिस नम्बर:</span>
                                        <span class="fw-bold text-primary fs-6 font-monospace">
                                            #{{ $application->certificate_number ?: $application->application_number }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="info-pill h-100">
                                        <span class="text-muted small d-block">निवेदन ट्र्याकिङ कोड:</span>
                                        <span class="fw-bold text-dark fs-6 font-monospace">
                                            #{{ $application->application_number }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="info-pill h-100">
                                        <span class="text-muted small d-block">निवेदकको नाम:</span>
                                        <span class="fw-bold text-dark fs-6">
                                            {{ $application->applicant_name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="info-pill h-100">
                                        <span class="text-muted small d-block">ठेगाना / वडा:</span>
                                        <span class="fw-bold text-dark fs-6">
                                            {{ $application->applicant_address ?: 'बाह्रदशी गाउँपालिका, झापा' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="info-pill h-100">
                                        <span class="text-muted small d-block">जारी भएको मिति:</span>
                                        <span class="fw-bold text-dark fs-6">
                                            {{ $application->issued_at ? $application->issued_at->format('Y-m-d') : ($application->processed_at ? $application->processed_at->format('Y-m-d') : now()->format('Y-m-d')) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="info-pill h-100">
                                        <span class="text-muted small d-block">चलानी नम्बर:</span>
                                        <span class="fw-bold text-dark fs-6 font-monospace">
                                            CH-{{ str_pad((string) $application->id, 5, '0', STR_PAD_LEFT) }} (आ.व. २०८३/०८४)
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Verification Statement Notice -->
                            <div class="alert alert-success border-0 bg-success-subtle p-3 rounded-3 mb-4 text-start">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-info-circle-fill text-success fs-5"></i>
                                    <div class="small">
                                        <strong>सत्यता पुष्टि विवरण:</strong> यो प्रमाणपत्र बाह्रदशी गाउँ कार्यपालिकाको कार्यालयबाट नियमानुसार आवश्यक प्रक्रिया, कागजात तथा दस्तुर रुजु गरी जारी गरिएको हो। यसमा रहेको QR कोडमार्फत डिजिटल प्रणालीबाट प्रमाणीकरण गरिएको पुष्टि गरिन्छ।
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 no-print border-top pt-3">
                                <button onclick="window.print()" class="btn btn-outline-dark btn-sm fw-bold">
                                    <i class="bi bi-printer me-1"></i> यो प्रमाणीकरण पत्र प्रिन्ट गर्नुहोस्
                                </button>
                                <span class="text-muted small font-monospace">
                                    प्रमाणीकरण मिति: {{ now()->format('Y-m-d H:i:s') }}
                                </span>
                            </div>
                        </div>
                    </div>

                @elseif($searched && !$application)
                    <!-- ========================================== -->
                    <!-- 2. VERIFICATION FAILED (INVALID QR CODE)    -->
                    <!-- ========================================== -->
                    <div class="verification-card">
                        <div class="failed-banner text-center py-4">
                            <i class="bi bi-x-circle-fill fs-1 text-white mb-2 d-block"></i>
                            <h4 class="fw-bold text-white mb-1">प्रमाणीकरण असफल (Verification Failed)</h4>
                            <p class="text-white-50 small mb-0">खोजी गरिएको कोड प्रणालीको अभिलेखमा फेला परेन</p>
                        </div>
                        <div class="p-4 p-md-5 text-center">
                            <div class="alert alert-danger bg-danger-subtle border-0 mb-4 p-3 text-start small">
                                <p class="mb-1"><strong>संकेत:</strong> तपाईंले स्क्यान वा प्रविष्ट गर्नुभएको कोड <code>{{ $lookupCode }}</code> सँग मेल खाने कुनै पनि स्वीकृत आधिकारिक प्रमाणपत्र फेला परेन।</p>
                                <p class="mb-0 text-muted">यो निवेदन अझै प्रक्रियामा रहेको, अस्वीकृत भएको वा अवैध भएको हुन सक्छ।</p>
                            </div>
                            <p class="text-muted small mb-4">थप जानकारीको लागि कृपया बाह्रदशी गाउँ कार्यपालिकाको कार्यालय वा सम्बन्धित वडा कार्यालयमा सम्पर्क गर्नुहोस्।</p>
                            <a href="{{ route('welcome') }}" class="btn btn-primary px-4">
                                <i class="bi bi-arrow-left me-1"></i> गृहपृष्ठमा फर्कनुहोस्
                            </a>
                        </div>
                    </div>

                @else
                    <!-- ========================================== -->
                    <!-- 3. ROOT LANDING (INSTRUCTIONS TO SCAN QR)   -->
                    <!-- (No manual search box for security)        -->
                    <!-- ========================================== -->
                    <div class="verification-card text-center p-4 p-md-5">
                        <div class="qr-placeholder-box mb-4">
                            <div class="mb-3">
                                <i class="bi bi-qr-code-scan" style="font-size: 4rem; color: var(--gov-blue);"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">कागजात प्रमाणीकरणका लागि QR कोड स्क्यान गर्नुहोस्</h4>
                            <p class="text-muted leading-relaxed mx-auto mb-0" style="max-width: 520px;">
                                नागरिकको व्यक्तिगत विवरण सुरक्षा तथा कागजातको आधिकारिकता सुनिश्चित गर्नका लागि म्यानुअल खोजी बन्द गरिएको छ। कृपया सिफारिस वा प्रमाणपत्रमा मुद्रित <strong>आधिकारिक QR कोड</strong> आफ्नो मोबाइल क्यामेराबाट सिधै स्क्यान गर्नुहोस्।
                            </p>
                        </div>

                        <div class="row g-3 justify-content-center text-start small text-muted mb-4">
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <strong class="d-block text-dark mb-1"><i class="bi bi-1-circle text-primary me-1"></i> QR स्क्यान</strong>
                                    कागजात वा सिफारिसमा रहेको क्युआर कोड मोबाइलले स्क्यान गर्नुहोस्।
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <strong class="d-block text-dark mb-1"><i class="bi bi-2-circle text-primary me-1"></i> स्वतः पुष्टि</strong>
                                    कुनै पनि लगइन बिना तत्काल सरकारी अभिलेख र सत्यता खुल्छ।
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <strong class="d-block text-dark mb-1"><i class="bi bi-3-circle text-primary me-1"></i> १००% सुरक्षित</strong>
                                    नक्कली कागजातको जोखिम पूर्ण रूपमा नियन्त्रण।
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-center gap-3">
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3">
                                <i class="bi bi-box-arrow-in-right me-1"></i> नागरिक लगइन पोर्टल
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </main>

    <footer class="text-center text-muted small mt-4">
        &copy; {{ date('Y') }} बाह्रदशी गाउँ कार्यपालिकाको कार्यालय, झापा • सर्वाधिकार सुरक्षित
    </footer>
</body>
</html>
