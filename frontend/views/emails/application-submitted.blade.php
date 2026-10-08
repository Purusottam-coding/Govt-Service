<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>निवेदन दर्ता पुष्टि — बाह्रदशी गाउँपालिका</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Noto Sans Devanagari', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">
                    <!-- Government Official Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #05264E 0%, #003893 100%); padding: 26px 24px; text-align: center; color: #ffffff;">
                            <h2 style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">बाह्रदशी गाउँपालिका</h2>
                            <p style="margin: 4px 0 0; font-size: 13px; color: #bfdbfe; font-weight: 500;">गाउँ कार्यपालिकाको कार्यालय, चकचकी, झापा • कोशी प्रदेश, नेपाल</p>
                            <div style="margin-top: 10px; display: inline-block; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 4px 14px; border-radius: 20px; font-size: 12px; color: #ffffff; font-weight: 600;">
                                सुरक्षित विद्युतीय सरकारी सेवा प्रणाली
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 28px 24px;">
                            <div style="display: inline-block; background-color: #eff6ff; color: #1d4ed8; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 6px; margin-bottom: 12px; text-transform: uppercase;">
                                नयाँ निवेदन दर्ता पुष्टि (Application Submitted)
                            </div>
                            <h3 style="margin: 0 0 14px; font-size: 18px; color: #0f172a; font-weight: 700;">
                                नमस्ते {{ $application->applicant_name }},
                            </h3>
                            <p style="margin: 0 0 18px; font-size: 14.5px; color: #334155; line-height: 1.65;">
                                तपाईंले बाह्रदशी गाउँपालिकाको अनलाइन नागरिक पोर्टल मार्फत <strong>{{ $application->service->name }}</strong> सेवाका लागि पेश गर्नुभएको निवेदन तथा आवश्यक कागजातहरू सफलतापूर्वक दर्ता भएका छन्।
                            </p>

                            <!-- Application Details Table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 24px; font-size: 14px;">
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
                                <tr>
                                    <td style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">पेश गरिएको मिति:</td>
                                    <td style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #0f172a;">{{ $application->submitted_at ? $application->submitted_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 14px; color: #64748b; font-weight: 600;">सरकारी दस्तुर:</td>
                                    <td style="padding: 10px 14px; color: #0f172a; font-weight: 700;">रु. {{ number_format($application->service->fee, 2) }}</td>
                                </tr>
                            </table>

                            <!-- Next Steps Box -->
                            <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 14px 16px; border-radius: 6px; margin-bottom: 24px;">
                                <h4 style="margin: 0 0 6px; font-size: 14px; color: #166534; font-weight: 700;">अब के हुन्छ? (Next Steps)</h4>
                                <p style="margin: 0; font-size: 13.5px; color: #14532d; line-height: 1.5;">
                                    गाउँपालिकाका अधिकृतहरूले तपाईंले पेश गर्नुभएका कागजातहरूको प्रारम्भिक छानबिन गर्नेछन्। कागजातहरू प्रमाणीकरण भएपछि निवेदन स्वीकृत वा थप निर्देशन सम्बन्धी जानकारी तपाईंको यसै इमेलमा पठाइनेछ।
                                </p>
                            </div>

                            <!-- Action Button -->
                            <div style="text-align: center; margin: 26px 0 16px;">
                                <a href="{{ route('citizen.applications.show', $application) }}" style="display: inline-block; background-color: #05264E; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; box-shadow: 0 2px 6px rgba(5, 38, 78, 0.3);">
                                    निवेदनको स्थिति हेर्नुहोस् (Track Application) &rarr;
                                </a>
                            </div>

                            <p style="margin: 20px 0 0; font-size: 13px; color: #64748b; line-height: 1.5; text-align: center;">
                                यो सन्देश प्रणालीद्वारा स्वतः पठाइएको हो। यदि तपाईंले यो निवेदन दिनुभएको होइन भने, कृपया हाम्रो सहायता कक्षमा सम्पर्क गर्नुहोस्।
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
