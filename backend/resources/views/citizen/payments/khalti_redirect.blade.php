<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti ePayment - आधिकारिक अनलाइन भुक्तानी प्रमाणीकरण</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #faf5ff 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            padding: 1.5rem;
        }
        .gateway-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            border-top: 6px solid #5c2d91;
        }
        .gateway-header {
            background-color: #ffffff;
            padding: 1.5rem 2rem 1rem 2rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .khalti-brand-badge {
            background-color: #5c2d91;
            color: #ffffff;
            font-weight: 800;
            padding: 0.4rem 1.2rem;
            border-radius: 20px;
            font-size: 0.95rem;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        .gateway-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #5c2d91;
            box-shadow: 0 0 0 0.25rem rgba(92, 45, 145, 0.25);
        }
        .btn-khalti {
            background-color: #5c2d91;
            border-color: #5c2d91;
            color: #ffffff;
            font-weight: 700;
            padding: 0.75rem 1.2rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-khalti:hover {
            background-color: #481f75;
            border-color: #481f75;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="gateway-card">
        <!-- Gateway Header -->
        <div class="gateway-header text-center">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="khalti-brand-badge">Khalti ePayment</span>
                <span class="badge bg-light text-primary border border-primary-subtle small py-1 px-2" style="color:#5c2d91 !important;">
                    <i class="bi bi-shield-lock-fill me-1"></i> Secured by Khalti
                </span>
            </div>
            <h5 class="fw-bold text-dark mb-0">बाह्रदशी गाउँ कार्यपालिकाको कार्यालय</h5>
            <p class="text-muted small mb-0">डिजिटल भुक्तानी प्रमाणीकरण गेटवे (झापा)</p>
        </div>

        <div class="gateway-body">
            <!-- Invoice Details Summary -->
            <div class="p-3 bg-light rounded text-start small mb-4 border">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">निवेदन नम्बर:</span>
                    <span class="fw-bold text-primary">{{ $application->application_number }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">सेवाको नाम:</span>
                    <span class="fw-semibold text-dark">{{ $application->service->name }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top mt-2">
                    <span class="fw-bold text-dark">कुल बुझाउनुपर्ने रकम:</span>
                    <span class="fw-bold text-success fs-5">रु. {{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>

            <!-- Khalti Login Authentication Form -->
            <form action="{{ route('citizen.payments.khalti.process', $application) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="khalti_mobile" class="form-label fw-bold text-dark small">
                        Khalti मोबाइल नम्बर <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-phone"></i></span>
                        <input 
                            type="text" 
                            name="khalti_mobile" 
                            id="khalti_mobile" 
                            class="form-control" 
                            value="{{ auth()->user()->phone ?? '9800000000' }}" 
                            placeholder="98xxxxxxxx" 
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="khalti_pin" class="form-label fw-bold text-dark small">
                        Khalti ट्रान्जेक्सन PIN <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                        <input 
                            type="password" 
                            name="khalti_pin" 
                            id="khalti_pin" 
                            class="form-control" 
                            value="1122" 
                            placeholder="4-अङ्कको PIN" 
                            required
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label for="khalti_otp" class="form-label fw-bold text-dark small">
                        OTP टोकन प्रमाणीकरण कोड
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-shield-check"></i></span>
                        <input 
                            type="text" 
                            name="khalti_otp" 
                            id="khalti_otp" 
                            class="form-control" 
                            value="123456" 
                            placeholder="6-अङ्कको OTP"
                        >
                    </div>
                    <div class="form-text small text-muted">परीक्षणको लागि स्वतः 123456 राखिएको छ।</div>
                </div>

                <button type="submit" class="btn btn-khalti w-100 shadow-sm mb-3">
                    <i class="bi bi-check-circle-fill me-1"></i> रु. {{ number_format($payment->amount, 2) }} प्रमाणीकरण गरी भुक्तानी गर्नुहोस्
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('citizen.payments.create', $application) }}" class="text-decoration-none text-muted small">
                    &larr; भुक्तानी रद्द गरी सेवा पृष्ठमा फर्कनुहोस्
                </a>
            </div>
        </div>
    </div>
</body>
</html>
