<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>आधिकारिक प्रमाणपत्र — {{ $certificateNumber }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Mukta', 'Kalimati', 'Noto Sans Devanagari', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        /* Top Action Bar (hidden on print) */
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

        /* Certificate Container */
        .cert-wrapper {
            max-width: 820px;
            margin: 24px auto;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .cert-outer-border {
            border: 4px double #05264E;
            padding: 6px;
            margin: 0;
            position: relative;
            background: #ffffff;
        }

        .cert-inner-border {
            border: 1.5px solid #d97706;
            padding: 24px 28px;
            position: relative;
            background: #ffffff;
        }

        /* Watermark */
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
        }

        /* Header */
        .cert-header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #05264E;
            padding-bottom: 12px;
            margin-bottom: 14px;
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
            width: 88px;
            height: auto;
        }

        .flag-img {
            width: 58px;
            height: auto;
        }

        .gov-sub-title {
            font-size: 13px;
            font-weight: 600;
            color: #dc2626;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .gov-main-title {
            font-size: 24px;
            font-weight: 800;
            color: #05264E;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }

        .gov-office-title {
            font-size: 16px;
            font-weight: 700;
            color: #dc2626;
            margin: 2px 0;
        }

        .gov-address-title {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin: 0;
        }

        .gov-dept-title {
            font-size: 15px;
            font-weight: 700;
            color: #053775;
            margin-top: 4px;
            display: inline-block;
            background: #eff6ff;
            padding: 2px 14px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
        }

        /* Reference Strip */
        .ref-strip {
            display: table;
            width: 100%;
            margin-bottom: 18px;
            font-size: 13px;
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
            font-family: monospace;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        /* Certificate Title */
        .cert-title-container {
            text-align: center;
            margin: 18px 0 20px 0;
        }

        .cert-title-pill {
            display: inline-block;
            background: linear-gradient(135deg, #05264E 0%, #003893 100%);
            color: #ffffff;
            padding: 8px 32px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
            border: 2px solid #d97706;
            box-shadow: 0 2px 6px rgba(5, 38, 78, 0.2);
        }

        /* Subject */
        .cert-subject {
            text-align: center;
            margin-bottom: 18px;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .cert-subject span {
            text-decoration: underline;
            text-decoration-thickness: 1.5px;
            text-underline-offset: 4px;
        }

        /* Certificate Body */
        .cert-body {
            font-size: 14.5px;
            line-height: 1.8;
            color: #1e293b;
            text-align: justify;
            margin-bottom: 20px;
        }

        .cert-body p {
            margin: 0 0 12px 0;
            text-indent: 32px;
        }

        .highlight-text {
            font-weight: 700;
            color: #05264E;
        }

        /* Details Grid / Key-Value table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 20px 0;
            font-size: 13.5px;
        }

        .details-table td {
            padding: 6px 12px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
        }

        .details-table td.label-cell {
            width: 28%;
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
        }

        .details-table td.value-cell {
            color: #0f172a;
            font-weight: 600;
        }

        /* Bottom Seal & Signatures */
        .bottom-section {
            display: table;
            width: 100%;
            margin-top: 30px;
            padding-top: 10px;
        }

        .seal-col {
            display: table-cell;
            width: 33%;
            vertical-align: bottom;
            text-align: center;
        }

        .qr-col {
            display: table-cell;
            width: 34%;
            vertical-align: bottom;
            text-align: center;
        }

        .sign-col {
            display: table-cell;
            width: 33%;
            vertical-align: bottom;
            text-align: center;
        }

        /* Circular Municipal Seal */
        .municipal-stamp {
            width: 108px;
            height: 108px;
            border: 3px dashed #b91c1c;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #b91c1c;
            transform: rotate(-5deg);
            padding: 6px;
            background: rgba(254, 242, 242, 0.4);
        }

        .stamp-outer-text {
            font-size: 9.5px;
            font-weight: 800;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .stamp-inner-text {
            font-size: 8px;
            font-weight: 700;
            border-top: 1px solid #b91c1c;
            border-bottom: 1px solid #b91c1c;
            padding: 2px 4px;
            margin: 2px 0;
        }

        .stamp-label {
            font-size: 8.5px;
            font-weight: 600;
            color: #7f1d1d;
        }

        /* QR Code Container */
        .qr-box {
            display: inline-block;
            background: #ffffff;
            padding: 6px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-align: center;
        }

        .qr-img {
            width: 90px;
            height: 90px;
            display: block;
            margin: 0 auto;
        }

        .qr-caption {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 600;
        }

        /* Signature Box */
        .sign-box {
            display: inline-block;
            width: 100%;
            text-align: center;
        }

        .sign-line {
            width: 160px;
            border-bottom: 1.5px dashed #05264E;
            margin: 0 auto 6px auto;
        }

        .sign-title {
            font-size: 13px;
            font-weight: 700;
            color: #05264E;
            margin: 0;
        }

        .sign-office {
            font-size: 11.5px;
            color: #475569;
            margin: 2px 0 0 0;
        }

        /* Security Footer Note */
        .security-footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 24px;
            padding-top: 8px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
            line-height: 1.4;
        }

        /* Print Specific Styling */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .cert-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                box-shadow: none !important;
            }
            .cert-outer-border {
                border: 4px double #05264E !important;
            }
            .cert-inner-border {
                border: 1.5px solid #d97706 !important;
                padding: 20px 24px !important;
            }
        }
    </style>
</head>
<body>

    @if(empty($isPdf))
        <!-- Browser Top Action Bar -->
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
                            @if(!empty($flagBase64))
                                <img src="{{ $flagBase64 }}" class="flag-img" alt="Nepal Flag">
                            @else
                                <div style="width: 50px;"></div>
                            @endif
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
                            <td class="value-cell"><span style="font-family: monospace; color: #05264E;">{{ $certificateNumber }}</span></td>
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

                    <!-- Bottom Seals, QR & Signatures -->
                    <div class="bottom-section">
                        <!-- Left: Circular Municipal Seal -->
                        <div class="seal-col">
                            <div class="municipal-stamp">
                                <div class="stamp-outer-text">बाह्रदशी गाउँपालिका</div>
                                <div class="stamp-inner-text">गाउँ कार्यपालिकाको कार्यालय</div>
                                <div class="stamp-label">डिजिटल छाप • २०७३</div>
                                <div style="font-size: 7.5px; font-weight: 700; margin-top: 1px;">झापा, नेपाल</div>
                            </div>
                        </div>

                        <!-- Center: Dynamic Verification QR -->
                        <div class="qr-col">
                            <div class="qr-box">
                                <img src="{{ $qrCodeDataUri }}" class="qr-img" alt="Verification QR Code">
                                <div class="qr-caption">प्रमाणीकरणको लागि QR स्क्यान गर्नुहोस्</div>
                                <div style="font-size: 8.5px; font-family: monospace; font-weight: bold; color: #05264E;">{{ $certificateNumber }}</div>
                            </div>
                        </div>

                        <!-- Right: Authorized Signature -->
                        <div class="sign-col">
                            <div class="sign-box">
                                <div style="height: 48px; display: flex; align-items: flex-end; justify-content: center;">
                                    <span style="font-family: 'Brush Script MT', cursive, sans-serif; font-size: 22px; color: #05264E; font-weight: bold;">
                                        Authorized Seal
                                    </span>
                                </div>
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
