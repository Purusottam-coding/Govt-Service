<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa सुरक्षित भुक्तानी - बाह्रदशी गाउँपालिका</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .redirect-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            max-width: 480px;
            width: 100%;
            padding: 2.5rem;
            text-align: center;
            border-top: 6px solid #60bb46;
        }
        .spinner {
            width: 3rem;
            height: 3rem;
            border: 4px solid #e2e8f0;
            border-top-color: #60bb46;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 1.5rem auto;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .esewa-badge {
            background-color: #60bb46;
            color: #ffffff;
            font-weight: 700;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="redirect-card">
        <div class="esewa-badge">eSewa ePay सुरक्षित गेटवे</div>
        <h4 class="fw-bold text-dark mb-1">बाह्रदशी गाउँपालिका</h4>
        <p class="text-muted small mb-3">गाउँ कार्यपालिकाको कार्यालय, झापा</p>

        <div class="spinner"></div>

        <h5 class="fw-semibold text-dark mb-2">eSewa भुक्तानी गेटवेमा जाँदैछ...</h5>
        <p class="text-muted small mb-3">
            तपाईंलाई सुरक्षित eSewa पोर्टलमा पठाइँदैछ। कृपया केही सेकेन्ड पर्खनुहोस् र ब्राउजर बन्द वा रिफ्रेस नगर्नुहोस्।
        </p>

        <div class="p-3 bg-light rounded text-start small mb-4 border">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">निवेदन नम्बर:</span>
                <span class="fw-bold">{{ $application->application_number }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">सेवा:</span>
                <span class="fw-semibold">{{ $application->service->name }}</span>
            </div>
            <div class="d-flex justify-content-between pt-1 border-top mt-1">
                <span class="fw-bold text-dark">कुल दस्तुर:</span>
                <span class="fw-bold text-success fs-6">रु. {{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <form id="esewaForm" action="{{ $actionUrl }}" method="POST">
            @foreach($payload as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm" style="background-color: #60bb46; border-color: #60bb46;">
                स्वत: रिडाइरेक्ट नभएमा यहाँ थिच्नुहोस् &rarr;
            </button>
        </form>

        <div class="mt-3">
            <a href="{{ route('citizen.payments.create', $application) }}" class="text-decoration-none text-muted small">
                &larr; फिर्ता जानुहोस्
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto submit form after 600ms
            setTimeout(function () {
                document.getElementById('esewaForm').submit();
            }, 600);
        });
    </script>
</body>
</html>
