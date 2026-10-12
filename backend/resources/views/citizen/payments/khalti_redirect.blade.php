<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment Gateway - बाह्रदशी गाउँपालिका</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --khalti-purple: #5c2d91;
            --khalti-dark: #471f73;
            --khalti-light: #f5f0fb;
            --khalti-border: #e6ddf2;
        }
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding: 1rem;
            margin: 0;
        }
        .khalti-checkout-window {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(92, 45, 145, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            border: 1px solid var(--khalti-border);
        }
        .khalti-top-bar {
            background-color: var(--khalti-purple);
            color: #ffffff;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .khalti-amount-card {
            background: var(--khalti-light);
            border-bottom: 1px solid var(--khalti-border);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .checkout-body {
            padding: 1.5rem;
        }
        .khalti-input {
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .khalti-input:focus {
            border-color: var(--khalti-purple);
            box-shadow: 0 0 0 3px rgba(92, 45, 145, 0.15);
            outline: none;
        }
        .btn-pay-khalti {
            background-color: var(--khalti-purple);
            border: none;
            color: #ffffff;
            font-weight: 700;
            padding: 0.8rem;
            border-radius: 8px;
            font-size: 1rem;
            width: 100%;
            transition: background 0.2s ease;
        }
        .btn-pay-khalti:hover {
            background-color: var(--khalti-dark);
            color: #ffffff;
        }
        .test-badge {
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            border-radius: 8px;
            padding: 0.6rem 0.8rem;
            font-size: 0.8rem;
        }
        .payment-tab {
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.85rem;
            border-bottom: 2px solid var(--khalti-purple);
            color: var(--khalti-purple);
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="khalti-checkout-window">
        <!-- Khalti Brand Header -->
        <div class="khalti-top-bar">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/khalti-logo.png') }}" alt="Khalti" style="max-height: 34px; background: white; padding: 4px 8px; border-radius: 6px;" onerror="this.src='https://khalti.com/static/images/logo1.png'">
                <span class="ms-2 fw-bold small text-white">Khalti ePayment</span>
            </div>
            <span class="badge bg-white text-dark small py-1 px-2 fw-semibold" style="font-size: 0.75rem;">
                <i class="bi bi-shield-check text-success me-1"></i> UAT Server
            </span>
        </div>

        <!-- Order Fee Banner -->
        <div class="khalti-amount-card">
            <div>
                <span class="text-muted d-block" style="font-size: 0.8rem;">भुक्तानी गर्नुपर्ने रकम:</span>
                <span class="fw-bold fs-5 text-dark">रु. {{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="text-end">
                <span class="text-muted d-block" style="font-size: 0.75rem;">निवेदन नम्बर</span>
                <span class="badge bg-white text-dark border small fw-bold">#{{ $application->application_number }}</span>
            </div>
        </div>

        <div class="checkout-body">
            <!-- Tabs -->
            <div class="border-bottom mb-3">
                <span class="payment-tab">
                    <i class="bi bi-wallet2 me-1"></i> Khalti Wallet
                </span>
            </div>

            <!-- Test Credentials Hint Box -->
            <div class="test-badge mb-3">
                <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Khalti आधिकारिक Test Credentials:</div>
                <div class="d-flex justify-content-between">
                    <span>Mobile: <strong>9800000000</strong></span>
                    <span>MPIN: <strong>1111</strong></span>
                    <span>OTP: <strong>987654</strong></span>
                </div>
            </div>

            <!-- Khalti Wallet Auth Form -->
            <form id="khaltiAuthForm" action="{{ route('citizen.payments.khalti.process', $application) }}" method="POST">
                @csrf

                <!-- Mobile Number Input -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">
                        Khalti ID (मोबाइल नम्बर) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted small px-2 fw-semibold">🇳🇵 +977</span>
                        <input 
                            type="tel" 
                            name="khalti_mobile" 
                            id="khalti_mobile" 
                            class="form-control khalti-input" 
                            value="9800000000" 
                            placeholder="९८xxxxxxxx" 
                            required
                        >
                    </div>
                </div>

                <!-- MPIN Input -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">
                        Khalti MPIN (४-अङ्कको पिन) <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="khalti_pin" 
                        id="khalti_pin" 
                        class="form-control khalti-input" 
                        value="1111" 
                        maxlength="6" 
                        placeholder="••••" 
                        required
                    >
                </div>

                <!-- OTP Input -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">OTP टोकन कोड</label>
                        <span class="text-muted" style="font-size: 0.75rem;">(डेमो कोड: 987654)</span>
                    </div>
                    <input 
                        type="text" 
                        name="khalti_otp" 
                        id="khalti_otp" 
                        class="form-control khalti-input" 
                        value="987654" 
                        placeholder="६-अङ्कको OTP"
                    >
                </div>

                <!-- Pay Button -->
                <button type="submit" class="btn btn-pay-khalti mb-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-lock-fill me-2"></i> रु. {{ number_format($payment->amount, 2) }} भुक्तानी गर्नुहोस्
                </button>
            </form>

            <!-- Cancel Button -->
            <div class="text-center pt-2">
                <a href="{{ route('citizen.payments.khalti.callback', ['application' => $application, 'status' => 'User Canceled']) }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> भुक्तानी रद्द गरी फिर्ता जानुहोस्
                </a>
            </div>
        </div>

        <div class="bg-light p-2 text-center text-muted border-top" style="font-size: 0.75rem;">
            Secured 256-bit SSL Transaction • Khalti Digital Wallet
        </div>
    </div>
</body>
</html>
