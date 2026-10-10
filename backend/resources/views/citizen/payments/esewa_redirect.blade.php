<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa UAT सर्भरमा जाँदैछ...</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
        }
        .redirect-box {
            background: #ffffff;
            padding: 2.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            max-width: 480px;
            width: 100%;
            text-align: center;
            border-top: 6px solid #60bb46;
        }
        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #e2e8f0;
            border-top-color: #60bb46;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 1.5rem auto;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .esewa-badge {
            background-color: #60bb46;
            color: #ffffff;
            font-weight: 800;
            padding: 0.35rem 1.1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 0.75rem;
        }
    </style>
</head>
<body onload="document.getElementById('esewaForm').submit();">
    <div class="redirect-box">
        <span class="esewa-badge">eSewa ePay UAT Server</span>
        <h4 style="margin: 0 0 0.5rem 0; color: #0f172a; font-weight: 700;">बाह्रदशी गाउँ कार्यपालिकाको कार्यालय</h4>
        <p style="margin: 0 0 1.25rem 0; color: #64748b; font-size: 0.85rem;">eSewa को आधिकारिक UAT सर्भरमा रिडाइरेक्ट गरिँदैछ...</p>

        <div class="spinner"></div>

        <p style="color: #334155; font-size: 0.9rem; margin-bottom: 1.5rem;">
            कृपया केही सेकेन्ड पर्खनुहोस्, eSewa को आधिकारिक UAT लगइन पोर्टल खुल्दैछ।
        </p>

        <form id="esewaForm" action="{{ $actionUrl }}" method="POST">
            @foreach($payload as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <button type="submit" style="background-color: #60bb46; border: none; color: white; padding: 0.75rem 1.5rem; font-weight: bold; border-radius: 8px; cursor: pointer; width: 100%; font-size: 0.95rem;">
                eSewa आधिकारिक UAT पोर्टलमा जानुहोस् &rarr;
            </button>
        </form>

        <div style="margin-top: 1.25rem;">
            <a href="{{ route('citizen.payments.create', $application) }}" style="color: #64748b; text-decoration: none; font-size: 0.85rem;">
                &larr; रद्द गरी सेवा पृष्ठमा फर्कनुहोस्
            </a>
        </div>
    </div>
</body>
</html>
