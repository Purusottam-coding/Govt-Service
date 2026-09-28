<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>नागरिक खाता प्रमाणीकरण कोड (OTP)</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Noto Sans Devanagari', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">
                    <!-- Government Official Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #a60f1e 0%, #7b0a15 100%); padding: 26px 24px; text-align: center; color: #ffffff;">
                            <h2 style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">बाह्रदशी गाउँपालिका</h2>
                            <p style="margin: 4px 0 0; font-size: 13px; color: #fecdd3; font-weight: 500;">गाउँ कार्यपालिकाको कार्यालय, चकचकी, झापा • कोशी प्रदेश, नेपाल</p>
                            <div style="margin-top: 10px; display: inline-block; background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.25); padding: 4px 14px; border-radius: 20px; font-size: 12px; color: #fef08a; font-weight: 600;">
                                सुरक्षित विद्युतीय सरकारी सेवा प्रणाली
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 28px 24px;">
                            <h3 style="margin: 0 0 14px; font-size: 18px; color: #0f172a; font-weight: 700;">
                                नमस्ते {{ $user->name }},
                            </h3>
                            <p style="margin: 0 0 18px; font-size: 14.5px; color: #334155; line-height: 1.65;">
                                बाह्रदशी गाउँपालिकाको आधिकारिक अनलाइन नागरिक सेवा पोर्टलमा स्वागत छ। तपाईंको नयाँ खाता दर्ता तथा <strong>Gmail ठेगाना</strong> प्रमाणीकरण गर्नको लागि तल दिइएको ६ अंकको प्रमाणीकरण कोड (OTP) प्रयोग गर्नुहोस्:
                            </p>

                            <!-- OTP Box -->
                            <div style="text-align: center; margin: 28px 0;">
                                <div style="display: inline-block; background: #fff1f2; border: 2px dashed #e11d48; border-radius: 12px; padding: 18px 36px; text-align: center;">
                                    <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; color: #9f1239; font-weight: 700; margin-bottom: 6px;">
                                        तपाईंको प्रमाणीकरण कोड (OTP)
                                    </div>
                                    <div style="font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #a60f1e; font-family: monospace, Courier, sans-serif;">
                                        {{ $otp }}
                                    </div>
                                </div>
                            </div>

                            <!-- Expiration & Security Notice -->
                            <div style="background-color: #f8fafc; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 6px; margin-bottom: 22px;">
                                <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.5;">
                                    ⏳ <strong>समय सीमा:</strong> यो OTP कोड <strong>१० मिनेट</strong> सम्म मात्र मान्य रहनेछ।
                                    <br>
                                    🔒 <strong>सुरक्षा सूचना:</strong> यो कोड गोप्य राख्नुहोस् र कसैसँग सेयर नगर्नुहोस्।
                                </p>
                            </div>

                            <p style="margin: 0 0 8px; font-size: 13.5px; color: #64748b; line-height: 1.5;">
                                यदि तपाईंले यो खाता दर्ता गर्नुभएको होइन भने, कृपया यो इमेललाई बेवास्ता गर्न सक्नुहुन्छ। तपाईंको अनुमतिबिना कुनै खाता सक्रिय हुने छैन।
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 18px 24px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #64748b;">
                                © {{ date('Y') }} बाह्रदशी गाउँपालिका • सूचना प्रविधि शाखा
                            </p>
                            <p style="margin: 4px 0 0; font-size: 11px; color: #94a3b8;">
                                यो प्रणालीबाट स्वतः पठाइएको इमेल हो, यसमा सिधै जवाफ (Reply) नपठाउनुहोस्।
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
