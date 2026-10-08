<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>निवेदन स्थिति अद्यावधिक — बाह्रदशी गाउँपालिका</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Noto Sans Devanagari', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">
                    <!-- Government Official Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, {{ $application->status === 'approved' ? '#065f46 0%, #047857 100%' : ($application->status === 'rejected' ? '#991b1b 0%, #b91c1c 100%' : '#05264E 0%, #003893 100%') }}); padding: 26px 24px; text-align: center; color: #ffffff;">
                            <h2 style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">बाह्रदशी गाउँपालिका</h2>
                            <p style="margin: 4px 0 0; font-size: 13px; color: {{ $application->status === 'approved' ? '#a7f3d0' : ($application->status === 'rejected' ? '#fecaca' : '#bfdbfe') }}; font-weight: 500;">
                                गाउँ कार्यपालिकाको कार्यालय, चकचकी, झापा • कोशी प्रदेश, नेपाल
                            </p>
                            <div style="margin-top: 10px; display: inline-block; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.28); padding: 4px 14px; border-radius: 20px; font-size: 12px; color: #ffffff; font-weight: 600;">
                                आधिकारिक प्रशासकीय निर्णय सूचना
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 28px 24px;">
                            <!-- Status Pill Badge -->
                            @if($application->status === 'approved')
                                <div style="display: inline-block; background-color: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 6px; margin-bottom: 14px; border: 1px solid #bbf7d0;">
                                    ✓ निवेदन स्वीकृत (APPLICATION APPROVED)
                                </div>
                            @elseif($application->status === 'rejected')
                                <div style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 6px; margin-bottom: 14px; border: 1px solid #fecaca;">
                                    ✕ निवेदन अस्वीकृत (APPLICATION REJECTED)
                                </div>
                            @else
                                <div style="display: inline-block; background-color: #e0f2fe; color: #0369a1; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 6px; margin-bottom: 14px; border: 1px solid #bae6fd;">
                                    ℹ स्थिति अद्यावधिक: {{ $application->getStatusLabel() }}
                                </div>
                            @endif

                            <h3 style="margin: 0 0 14px; font-size: 18px; color: #0f172a; font-weight: 700;">
                                नमस्ते {{ $application->applicant_name }},
                            </h3>

                            @if($application->status === 'approved')
                                <p style="margin: 0 0 18px; font-size: 14.5px; color: #334155; line-height: 1.65;">
                                    बधाई छ! तपाईंले <strong>{{ $application->service->name }}</strong> का लागि पेश गर्नुभएको कागजातहरू प्रमाणीकरण भई गाउँपालिकाको कार्यालयबाट <strong>स्वीकृत (Approved)</strong> भएको छ।
                                </p>

                                <!-- Unique Certificate ID Highlight Box -->
                                @if(!empty($application->certificate_number))
                                    <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px dashed #16a34a; border-radius: 10px; padding: 18px 20px; text-align: center; margin-bottom: 24px;">
                                        <div style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 1.5px; color: #166534; font-weight: 700; margin-bottom: 6px;">
                                            आधिकारिक प्रमाणपत्र नम्बर (Unique Certificate ID)
                                        </div>
                                        <div style="font-size: 26px; font-weight: 800; letter-spacing: 3px; color: #15803d; font-family: monospace, Courier, sans-serif;">
                                            {{ $application->certificate_number }}
                                        </div>
                                        <div style="font-size: 12px; color: #166534; margin-top: 6px;">
                                            सार्वजनिक प्रमाणीकरण लिङ्क: 
                                            <a href="{{ url('/verify?query=' . $application->certificate_number) }}" style="color: #15803d; font-weight: 700; text-decoration: underline;">
                                                {{ url('/verify?query=' . $application->certificate_number) }}
                                            </a>
                                        </div>
                                    </div>
                                @endif

                            @elseif($application->status === 'rejected')
                                <p style="margin: 0 0 18px; font-size: 14.5px; color: #334155; line-height: 1.65;">
                                    तपाईंले <strong>{{ $application->service->name }}</strong> सेवाका लागि पेश गर्नुभएको निवेदन बाह्रदशी गाउँपालिकाको प्रशासकीय छानबिन पश्चात् <strong>अस्वीकृत (Rejected)</strong> गरिएको जानकारी गराइन्छ।
                                </p>
                            @else
                                <p style="margin: 0 0 18px; font-size: 14.5px; color: #334155; line-height: 1.65;">
                                    तपाईंको निवेदन (नं. #{{ $application->application_number }}) को स्थिति परिवर्तन भई <strong>{{ $application->getStatusLabel() }}</strong> भएको छ।
                                </p>
                            @endif

                            <!-- Details Table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 22px; font-size: 14px;">
                                <tr>
                                    <td style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600; width: 40%;">निवेदन नम्बर:</td>
                                    <td style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 700; font-family: monospace;">#{{ $application->application_number }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">सेवाको नाम:</td>
                                    <td style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 600;">{{ $application->service->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">सम्बन्धित शाखा:</td>
                                    <td style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #0f172a;">{{ $application->service->department?->name ?? 'सामान्य प्रशासन शाखा' }}</td>
                                </tr>
                                @if(!empty($application->admin_remarks))
                                    <tr>
                                        <td style="padding: 10px 14px; color: #64748b; font-weight: 600; vertical-align: top;">प्रशासकीय कैफियत (Remarks):</td>
                                        <td style="padding: 10px 14px; color: #0f172a; font-style: italic; font-weight: 500;">
                                            "{{ $application->admin_remarks }}"
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            <!-- Action Buttons -->
                            <div style="text-align: center; margin: 26px 0 16px;">
                                @if($application->status === 'approved')
                                    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                                        <a href="{{ route('citizen.applications.certificate', $application) }}" style="display: inline-block; background-color: #05264E; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; margin: 4px; box-shadow: 0 2px 6px rgba(5, 38, 78, 0.3);">
                                            आधिकारिक प्रमाणपत्र हेर्नुहोस् &rarr;
                                        </a>
                                        <a href="{{ route('citizen.applications.certificate.pdf', $application) }}" style="display: inline-block; background-color: #16a34a; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; margin: 4px; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);">
                                            PDF डाउनलोड गर्नुहोस् ⬇
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('citizen.applications.show', $application) }}" style="display: inline-block; background-color: #05264E; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; box-shadow: 0 2px 6px rgba(5, 38, 78, 0.3);">
                                        निवेदन विवरण हेर्नुहोस् &rarr;
                                    </a>
                                @endif
                            </div>

                            <p style="margin: 20px 0 0; font-size: 13px; color: #64748b; line-height: 1.5; text-align: center;">
                                कुनै जिज्ञासा वा थप सहयोग चाहिएमा बाह्रदशी गाउँपालिकाको नागरिक सहायता कक्षमा सम्पर्क गर्न सक्नुहुन्छ।
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 18px 24px; text-align: center; font-size: 12px; color: #64748b;">
                            <p style="margin: 0 0 4px; font-weight: 600; color: #334155;">बाह्रदशी गाउँपालिका, गाउँ कार्यपालिकाको कार्यालय</p>
                            <p style="margin: 0;">चकचकी, झापा, कोशी प्रदेश, नेपाल | इमेल: info@barhadashimun.gov.np</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
