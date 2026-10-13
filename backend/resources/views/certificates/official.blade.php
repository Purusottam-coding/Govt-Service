<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>आधिकारिक प्रमाणपत्र — {{ $certificateNumber }}</title>

    @if(empty($isPdf))
        <!-- Google Fonts Noto Sans Devanagari for crisp Web Browser rendering -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @else
        <!-- Local TTF font for Puppeteer / DomPDF engines -->
        <style>
            @font-face {
                font-family: 'Noto Sans Devanagari';
                src: url('{{ $fontRegular ?? str_replace('\\', '/', public_path('fonts/NotoSansDevanagari-Regular.ttf')) }}') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            @font-face {
                font-family: 'Noto Sans Devanagari';
                src: url('{{ $fontBold ?? str_replace('\\', '/', public_path('fonts/NotoSansDevanagari-Bold.ttf')) }}') format('truetype');
                font-weight: bold;
                font-style: normal;
            }
        </style>
    @endif

    <style>
        /* Base typography */
        body, table, th, td, p, div, span, h1, h2, h3, h4, h5, h6, strong, b {
            font-family: 'Noto Sans Devanagari', 'Mukta', 'Kalimati', sans-serif;
            letter-spacing: normal !important;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Action Bar for Browser View */
        .no-print-bar {
            background: #05264E;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .no-print-bar a, .no-print-bar button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .btn-print {
            background: #ffffff;
            color: #05264E;
        }
        .btn-print:hover {
            background: #f1f5f9;
        }

        .btn-download-pdf {
            background: #0284c7;
            color: #ffffff;
        }
        .btn-download-pdf:hover {
            background: #0369a1;
        }

        @if(empty($isPdf))
        /* ==========================================================================
           BROWSER VIEW SPECIFIC STYLES (Generous, crisp, legible on desktop screens)
           ========================================================================== */
        body {
            background-color: #cbd5e1;
            color: #0f172a;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .cert-wrapper {
            max-width: 880px;
            margin: 32px auto 48px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18), 0 1px 3px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            position: relative;
        }

        .cert-outer-border {
            border: 4px double #05264E;
            padding: 6px;
            background: #ffffff;
        }

        .cert-inner-border {
            border: 1.5px solid #d97706;
            padding: 28px 36px;
            background: #ffffff;
            position: relative;
        }

        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 330px;
            opacity: 0.045;
            pointer-events: none;
            z-index: 1;
        }

        .cert-content {
            position: relative;
            z-index: 2;
        }

        .cert-header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #05264E;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header-col-left {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: left;
        }

        .header-col-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .header-col-right {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: right;
        }

        .emblem-img {
            width: 82px;
            height: auto;
        }

        .header-qr-box {
            display: inline-block;
            background: #ffffff;
            padding: 4px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-align: center;
        }

        .header-qr-img {
            width: 72px;
            height: 72px;
            display: block;
            margin: 0 auto;
        }

        .header-qr-caption {
            font-size: 7.5px;
            color: #475569;
            margin-top: 2px;
            font-weight: 700;
        }

        .gov-sub-title {
            font-size: 13px;
            font-weight: 700;
            color: #dc2626;
            margin: 0;
            line-height: 1.25;
        }

        .gov-main-title {
            font-size: 25px;
            font-weight: 800;
            color: #05264E;
            margin: 2px 0;
            line-height: 1.2;
        }

        .gov-office-title {
            font-size: 16.5px;
            font-weight: 700;
            color: #dc2626;
            margin: 2px 0;
            line-height: 1.2;
        }

        .gov-address-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
            margin: 0;
            line-height: 1.2;
        }

        .gov-dept-title {
            font-size: 14px;
            font-weight: 700;
            color: #053775;
            margin-top: 4px;
            display: inline-block;
            background: #eff6ff;
            padding: 2px 14px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
        }

        .ref-strip {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            font-size: 13.5px;
            line-height: 1.5;
        }

        .ref-left {
            display: table-cell;
            text-align: left;
            vertical-align: top;
            width: 50%;
        }

        .ref-right {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            width: 50%;
        }

        .ref-item {
            margin-bottom: 2px;
            color: #334155;
        }

        .ref-item strong {
            color: #0f172a;
        }

        .cert-id-badge {
            display: inline-block;
            background: #05264E;
            color: #ffffff;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12.5px;
        }

        .cert-title-container {
            text-align: center;
            margin: 14px 0 16px 0;
        }

        .cert-title-pill {
            display: inline-block;
            background-color: #05264E;
            color: #ffffff;
            padding: 6px 28px;
            border-radius: 20px;
            font-size: 16.5px;
            font-weight: 700;
            border: 1.5px solid #d97706;
            box-shadow: 0 2px 6px rgba(5, 38, 78, 0.18);
        }

        .cert-subject {
            text-align: center;
            margin-bottom: 14px;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .cert-subject span {
            text-decoration: underline;
        }

        .cert-body {
            font-size: 14px;
            line-height: 1.75;
            color: #1e293b;
            text-align: justify;
            margin-bottom: 16px;
        }

        .cert-body p {
            margin: 0 0 8px 0;
            text-indent: 26px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 16px 0;
            font-size: 13.5px;
        }

        .details-table td {
            padding: 7px 12px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            vertical-align: middle;
        }

        .details-table td.label-cell {
            width: 26%;
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
        }

        .details-table td.value-cell {
            width: 24%;
            color: #0f172a;
            font-weight: 600;
        }

        .bottom-section {
            display: table;
            width: 100%;
            margin-top: 18px;
            padding-top: 8px;
        }

        .bottom-note-col {
            display: table-cell;
            width: 55%;
            vertical-align: bottom;
            text-align: left;
            padding-left: 6px;
        }

        .doc-verification-note {
            background: #f8fafc;
            border-left: 3px solid #003893;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            max-width: 320px;
        }

        .note-badge {
            display: inline-block;
            background: #05264E;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 3px;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .note-text {
            font-size: 10px;
            color: #475569;
            margin: 0;
            line-height: 1.4;
        }

        .sign-col {
            display: table-cell;
            width: 45%;
            vertical-align: bottom;
            text-align: right;
            padding-right: 15px;
        }

        .sign-box {
            display: inline-block;
            text-align: center;
            min-width: 170px;
        }

        .sign-space {
            height: 38px;
        }

        .sign-line {
            width: 160px;
            border-bottom: 1.5px dashed #05264E;
            margin: 0 auto 4px auto;
        }

        .sign-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #05264E;
            margin: 0;
        }

        .sign-office {
            font-size: 11px;
            color: #475569;
            margin: 2px 0 0 0;
        }

        .security-footer {
            border-top: 1px solid #cbd5e1;
            margin-top: 16px;
            padding-top: 8px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
            line-height: 1.4;
        }

        /* Print Media Styles (Full attached A4 sheet) */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }
            html, body {
                width: 100% !important;
                height: 100% !important;
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .cert-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                height: 100% !important;
                min-height: 278mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
                display: flex !important;
                flex-direction: column !important;
            }
            .cert-outer-border {
                flex: 1 !important;
                height: 100% !important;
                border: 4px double #05264E !important;
                padding: 4px !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .cert-inner-border {
                flex: 1 !important;
                height: 100% !important;
                border: 1.5px solid #d97706 !important;
                padding: 22px 26px 14px 26px !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }
            .cert-content {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                height: 100% !important;
            }
        }

        @else
        /* ==========================================================================
           FULL ATTACHED A4 PDF SPECIFIC STYLES (Chromium Puppeteer Engine)
           ========================================================================== */
        @page {
            size: A4 portrait;
            margin: 7mm 9mm;
        }

        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #0f172a;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .cert-wrapper {
            width: 100%;
            height: 100%;
            min-height: 280mm;
            max-height: 280mm;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background: #ffffff;
            page-break-inside: avoid;
            page-break-after: avoid;
            display: flex;
            flex-direction: column;
        }

        .cert-outer-border {
            border: 4px double #05264E;
            padding: 4px;
            background: #ffffff;
            box-sizing: border-box;
            flex: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .cert-inner-border {
            border: 1.5px solid #d97706;
            padding: 22px 28px 16px 28px;
            background: #ffffff;
            position: relative;
            box-sizing: border-box;
            flex: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 320px;
            height: auto;
            opacity: 0.045;
            pointer-events: none;
            z-index: 1;
        }

        .cert-content {
            position: relative;
            z-index: 2;
            flex: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cert-header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #05264E;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header-col-left {
            display: table-cell;
            width: 95px;
            vertical-align: middle;
            text-align: left;
        }

        .header-col-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .header-col-right {
            display: table-cell;
            width: 95px;
            vertical-align: middle;
            text-align: right;
        }

        .emblem-img {
            width: 76px;
            height: auto;
        }

        .header-qr-box {
            display: inline-block;
            background: #ffffff;
            padding: 3px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            text-align: center;
        }

        .header-qr-img {
            width: 66px;
            height: 66px;
            display: block;
            margin: 0 auto;
        }

        .header-qr-caption {
            font-size: 7px;
            color: #475569;
            margin-top: 1.5px;
            font-weight: 700;
        }

        .gov-sub-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #dc2626;
            margin: 0;
            line-height: 1.25;
        }

        .gov-main-title {
            font-size: 24px;
            font-weight: 800;
            color: #05264E;
            margin: 2px 0;
            line-height: 1.2;
        }

        .gov-office-title {
            font-size: 16px;
            font-weight: 700;
            color: #dc2626;
            margin: 2px 0;
            line-height: 1.2;
        }

        .gov-address-title {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin: 0;
            line-height: 1.2;
        }

        .gov-dept-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #053775;
            margin-top: 3px;
            display: inline-block;
            background: #eff6ff;
            padding: 2px 14px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
        }

        .ref-strip {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            font-size: 12.5px;
            line-height: 1.45;
        }

        .ref-left {
            display: table-cell;
            text-align: left;
            vertical-align: top;
            width: 50%;
        }

        .ref-right {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            width: 50%;
        }

        .ref-item {
            margin-bottom: 2px;
            color: #334155;
        }

        .ref-item strong {
            color: #0f172a;
        }

        .cert-id-badge {
            display: inline-block;
            background: #05264E;
            color: #ffffff;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 12px;
        }

        .cert-title-container {
            text-align: center;
            margin: 10px 0 12px 0;
        }

        .cert-title-pill {
            display: inline-block;
            background-color: #05264E;
            color: #ffffff;
            padding: 5px 26px;
            border-radius: 18px;
            font-size: 15.5px;
            font-weight: 700;
            border: 1.5px solid #d97706;
        }

        .cert-subject {
            text-align: center;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .cert-subject span {
            text-decoration: underline;
        }

        .cert-body {
            font-size: 13.5px;
            line-height: 1.7;
            color: #1e293b;
            text-align: justify;
            margin-bottom: 12px;
        }

        .cert-body p {
            margin: 0 0 6px 0;
            text-indent: 24px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px 0;
            font-size: 12.5px;
        }

        .details-table td {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            vertical-align: middle;
        }

        .details-table td.label-cell {
            width: 26%;
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
        }

        .details-table td.value-cell {
            width: 24%;
            color: #0f172a;
            font-weight: 600;
        }

        .bottom-section {
            display: table;
            width: 100%;
            margin-top: 14px;
            padding-top: 8px;
        }

        .bottom-note-col {
            display: table-cell;
            width: 55%;
            vertical-align: bottom;
            text-align: left;
            padding-left: 6px;
        }

        .doc-verification-note {
            background: #f8fafc;
            border-left: 2.5px solid #003893;
            padding: 6px 10px;
            border-radius: 4px;
            display: inline-block;
            max-width: 300px;
        }

        .note-badge {
            display: inline-block;
            background: #05264E;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            padding: 1.5px 6px;
            border-radius: 3px;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .note-text {
            font-size: 9px;
            color: #475569;
            margin: 0;
            line-height: 1.35;
        }

        .sign-col {
            display: table-cell;
            width: 45%;
            vertical-align: bottom;
            text-align: right;
            padding-right: 12px;
        }

        .sign-box {
            display: inline-block;
            text-align: center;
            min-width: 165px;
        }

        .sign-space {
            height: 34px;
        }

        .sign-line {
            width: 155px;
            border-bottom: 1.5px dashed #05264E;
            margin: 0 auto 4px auto;
        }

        .sign-title {
            font-size: 12px;
            font-weight: 700;
            color: #05264E;
            margin: 0;
        }

        .sign-office {
            font-size: 10.5px;
            color: #475569;
            margin: 1px 0 0 0;
        }

        .security-footer {
            border-top: 1px solid #cbd5e1;
            margin-top: 12px;
            padding-top: 6px;
            font-size: 9px;
            color: #64748b;
            text-align: center;
            line-height: 1.35;
        }
        @endif
    </style>
</head>
<body>

    @if(empty($isPdf))
        <!-- Browser Top Action Bar (Hidden when downloading as PDF) -->
        <div class="no-print-bar">
            <div>
                <a href="{{ url()->previous() ?: route('citizen.approved-documents.index') }}" class="btn-back">
                    &larr; पछाडि फर्कनुहोस्
                </a>
            </div>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn-print">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    प्रिन्ट / Save as PDF
                </button>
                <a href="{{ auth()->user()->isAdmin() ? route('admin.applications.certificate.pdf', $application) : route('citizen.applications.certificate.pdf', $application) }}" class="btn-download-pdf">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    PDF फाइल डाउनलोड
                </a>
            </div>
        </div>
    @endif

    <div class="cert-wrapper">
        <div class="cert-outer-border">
            <div class="cert-inner-border">
                
                <!-- Background Watermark -->
                @if(!empty($emblemBase64))
                    <img src="{{ $emblemBase64 }}" class="cert-watermark" alt="Nepal Emblem Watermark">
                @endif

                <div class="cert-content">

                    <!-- Official Header -->
                    <div class="cert-header">
                        <div class="header-col-left">
                            @if(!empty($emblemBase64))
                                <img src="{{ $emblemBase64 }}" class="emblem-img" alt="Emblem of Nepal">
                            @endif
                        </div>
                        <div class="header-col-center">
                            <p class="gov-sub-title">नेपाल सरकार • कोशी प्रदेश सरकार</p>
                            <h1 class="gov-main-title">बाह्रदशी गाउँपालिका</h1>
                            <p class="gov-office-title">गाउँ कार्यपालिकाको कार्यालय</p>
                            <p class="gov-address-title">चकचकी, झापा, कोशी प्रदेश, नेपाल</p>
                            <div class="gov-dept-title">{{ $departmentName }}</div>
                        </div>
                        <div class="header-col-right">
                            <div class="header-qr-box">
                                <img src="{{ $qrCodeDataUri }}" class="header-qr-img" alt="Verification QR Code">
                                <div class="header-qr-caption">QR स्क्यान</div>
                            </div>
                        </div>
                    </div>

                    <!-- Reference Details Strip -->
                    <div class="ref-strip">
                        <div class="ref-left">
                            <div class="ref-item">पत्र संख्या: <strong>{{ $fiscalYear }}</strong></div>
                            <div class="ref-item">चलानी नं.: <strong>{{ $dispatchNumber }}</strong></div>
                            <div class="ref-item">निवेदन नं.: <strong>#{{ $application->application_number }}</strong></div>
                        </div>
                        <div class="ref-right">
                            <div class="ref-item">मिति: <strong>{{ $issuedAt->format('M d, Y') }}</strong></div>
                            <div class="ref-item">प्रमाणपत्र ID: <span class="cert-id-badge">{{ $certificateNumber }}</span></div>
                            <div class="ref-item" style="color: #059669; font-weight: 700;">स्थिति: आधिकारिक प्रमाणित</div>
                        </div>
                    </div>

                    <!-- Certificate Title Banner -->
                    <div class="cert-title-container">
                        <div class="cert-title-pill">
                            आधिकारिक प्रमाणपत्र / सिफारिस पत्र
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="cert-subject">
                        विषय: <span>{{ $serviceName }} सम्बन्धी आधिकारिक प्रमाणपत्र</span>
                    </div>

                    <!-- Certificate Body Narrative -->
                    <div class="cert-body">
                        <p>
                            यस बाह्रदशी गाउँपालिका अन्तर्गत <strong>{{ $departmentName }}</strong> मा दर्ता भएको निवेदन नं. <strong>#{{ $application->application_number }}</strong> अनुसार आवेदक <strong>श्री / श्रीमती {{ $applicantName }}</strong> (ठेगाना: <strong>{{ $applicantAddress }}</strong>) ले माग गर्नुभएको सेवा <strong>"{{ $serviceName }}"</strong> को लागि पेश गरिएका सम्पूर्ण विवरण तथा प्रमाण कागजातहरू गाउँ कार्यपालिकाको नियमानुसार विधिवत् रुजु तथा छानबिन गरिएको छ।
                        </p>
                        <p>
                            तदनुसार उल्लिखित सेवा सम्बन्धी सम्पूर्ण कानुनी प्रक्रिया तथा दस्तुर भुक्तानी कार्य सम्पन्न भएको पाइएकाले यो आधिकारिक प्रमाणपत्र / सिफारिस पत्र जारी गरिएको प्रमाणित गरिन्छ।
                        </p>
                    </div>

                    <!-- Details Box Table -->
                    <table class="details-table">
                        <tr>
                            <td class="label-cell">आवेदकको नाम</td>
                            <td class="value-cell">{{ $applicantName }}</td>
                            <td class="label-cell">प्रमाणपत्र नम्बर</td>
                            <td class="value-cell"><span style="color: #05264E; font-weight: bold;">{{ $certificateNumber }}</span></td>
                        </tr>
                        <tr>
                            <td class="label-cell">स्थायी / हालको ठेगाना</td>
                            <td class="value-cell">{{ $applicantAddress }}</td>
                            <td class="label-cell">सेवा / योजनाको नाम</td>
                            <td class="value-cell">{{ $serviceName }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">सम्पर्क नम्बर</td>
                            <td class="value-cell">{{ $applicantPhone ?: 'उपलब्ध नभएको' }}</td>
                            <td class="label-cell">जारी गर्ने शाखा</td>
                            <td class="value-cell">{{ $departmentName }}</td>
                        </tr>
                        @if($adminRemarks)
                            <tr>
                                <td class="label-cell">प्रशासकीय कैफियत / निर्णय</td>
                                <td class="value-cell" colspan="3">{{ $adminRemarks }}</td>
                            </tr>
                        @endif
                    </table>

                    <!-- Bottom Details & Signature -->
                    <div class="bottom-section">
                        <!-- Left: Document Verification Note -->
                        <div class="bottom-note-col">
                            <div class="doc-verification-note">
                                <span class="note-badge">प्रमाणपत्र ID: {{ $certificateNumber }}</span>
                                <p class="note-text">
                                    यस प्रमाणपत्रको आधिकारिकता माथि शीर्ष भागमा रहेको <strong>QR कोड स्क्यान गरी</strong> वा गाउँपालिकाको पोर्टलमा सिधै प्रमाणीकरण गर्न सकिन्छ।
                                </p>
                            </div>
                        </div>

                        <!-- Right: Authorized Signature -->
                        <div class="sign-col">
                            <div class="sign-box">
                                <div class="sign-space"></div>
                                <div class="sign-line"></div>
                                <p class="sign-title">शाखा प्रमुख / प्रशासकीय अधिकृत</p>
                                <p class="sign-office">बाह्रदशी गाउँपालिका, झापा</p>
                            </div>
                        </div>
                    </div>

                    <!-- Security Verification Footer -->
                    <div class="security-footer">
                        यो डिजिटल सेवा प्रणालीबाट प्रमाणित भई जारी गरिएको आधिकारिक विद्युतीय प्रमाणपत्र हो। यसको आधिकारिकता पुष्टि गर्न माथिको QR कोड स्क्यान गर्नुहोस् वा गाउँपालिकाको पोर्टल ({{ url('/verify') }}) मा प्रमाणपत्र ID <strong>{{ $certificateNumber }}</strong> प्रविष्ट गरी जाँच्न सकिनेछ।
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
</html>
