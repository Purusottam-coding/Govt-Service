<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>कागजात पुनः अपलोड अनुरोध — बाह्रदशी गाउँपालिका</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Noto Sans Devanagari', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">
                    <!-- Government Official Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #b45309 0%, #d97706 100%); padding: 26px 24px; text-align: center; color: #ffffff;">
                            <h2 style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">बाह्रदशी गाउँपालिका</h2>
                            <p style="margin: 4px 0 0; font-size: 13px; color: #fef3c7; font-weight: 500;">गाउँ कार्यपालिकाको कार्यालय, चकचकी, झापा • कोशी प्रदेश, नेपाल</p>
                            <div style="margin-top: 10px; display: inline-block; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); padding: 4px 14px; border-radius: 20px; font-size: 12px; color: #ffffff; font-weight: 600;">
                                कागजात सच्याउने अनुरोध (Action Required)
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 28px 24px;">
                            <div style="display: inline-block; background-color: #fef3c7; color: #92400e; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 6px; margin-bottom: 14px; border: 1px solid #fde68a;">
                                ⚠️ कागजात पुनः पेश गर्न अनुरोध
                            </div>

                            <h3 style="margin: 0 0 14px; font-size: 18px; color: #0f172a; font-weight: 700;">
                                नमस्ते {{ $application->applicant_name }},
                            </h3>
                            <p style="margin: 0 0 18px; font-size: 14.5px; color: #334155; line-height: 1.65;">
                                तपाईंले <strong>{{ $application->service->name }}</strong> सेवाका लागि पेश गर्नुभएको निवेदन (नं. #{{ $application->application_number }}) को छानबिन गर्दा निम्न कागजात अस्पष्ट वा अपुग देखिएकाले पुनः अपलोड गर्न अनुरोध गरिएको छ:
                            </p>

                            <!-- Document Details Box -->
                            <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 8px; margin-bottom: 22px;">
                                <div style="font-size: 13px; color: #92400e; font-weight: 600; margin-bottom: 4px;">पुनः पेश गर्नुपर्ने कागजात:</div>
                                <div style="font-size: 16px; color: #78350f; font-weight: 700;">{{ $document->document_name }}</div>
                                @if(!empty($reason))
                                    <div style="margin-top: 8px; font-size: 13.5px; color: #92400e; font-style: italic;">
                                        <strong>अधिकृतको कैफियत:</strong> "{{ $reason }}"
                                    </div>
                                @endif
                            </div>

                            <p style="margin: 0 0 18px; font-size: 14px; color: #475569; line-height: 1.6;">
                                कृपया आफ्नो नागरिक प्रोफाइल लगइन गरी नयाँ, स्पष्ट र प्रमाणित कागजात तुरुन्तै अपलोड गर्नुहोस् ताकि तपाईंको निवेदनको काम बिना कुनै ढिलाइ अगाडि बढाउन सकियोस्।
                            </p>

                            <!-- Action Button -->
                            <div style="text-align: center; margin: 26px 0 16px;">
                                <a href="{{ route('citizen.applications.show', $application) }}" style="display: inline-block; background-color: #05264E; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px; box-shadow: 0 2px 6px rgba(5, 38, 78, 0.3);">
                                    कागजात पुनः अपलोड गर्नुहोस् (Upload Document) &rarr;
                                </a>
                            </div>
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
