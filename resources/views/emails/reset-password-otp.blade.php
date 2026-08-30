<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password - CIO Keuangan</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 40px 15px 50px 15px;">
        <tr>
            <td align="center">
                <!-- MAIN CONTAINER -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 560px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- BRAND HEADER -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); padding: 36px 30px 32px 30px; text-align: center;">
                            <table border="0" cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                    <td align="center" style="background-color: rgba(255, 255, 255, 0.15); padding: 10px 20px; border-radius: 12px; backdrop-filter: blur(8px);">
                                        <span style="font-size: 20px; font-weight: 800; color: #ffffff; letter-spacing: 1px; text-transform: uppercase;">
                                            CIO NETWORK SOLUTION
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top: 14px; font-size: 13px; color: #cbd5e1; letter-spacing: 0.5px; text-transform: uppercase; font-weight: 600;">
                                Sistem Informasi Penggajian &amp; Keuangan
                            </div>
                        </td>
                    </tr>

                    <!-- CONTENT BODY -->
                    <tr>
                        <td style="padding: 36px 32px 30px 32px;">
                            <!-- TITLE -->
                            <h2 style="margin: 0 0 12px 0; color: #0f172a; font-size: 20px; font-weight: 700; text-align: center;">
                                Verifikasi Keamanan Reset Password
                            </h2>
                            <p style="margin: 0 0 24px 0; color: #64748b; font-size: 14px; line-height: 1.6; text-align: center;">
                                Halo <strong style="color: #1e293b;">{{ $userName }}</strong>, kami menerima permintaan untuk mereset password akun Anda. Masukkan kode OTP berikut untuk melanjutkan:
                            </p>

                            <!-- OTP CODE BOX -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 28px 0;">
                                <tr>
                                    <td align="center" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); border: 2px dashed #93c5fd; border-radius: 14px; padding: 24px 20px;">
                                        <div style="font-size: 11px; font-weight: 700; color: #64748b; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 8px;">
                                            KODE VERIFIKASI RESMI
                                        </div>
                                        <div style="font-size: 38px; font-weight: 800; letter-spacing: 10px; color: #1e40af; font-family: 'Courier New', Courier, monospace; margin: 4px 0 8px 10px;">
                                            {{ $otpCode }}
                                        </div>
                                        <div style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 20px; margin-top: 4px;">
                                            ⏱️ Berlaku selama {{ $validMinutes }} menit
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- SECURITY INSTRUCTION -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 14px 16px; margin: 24px 0 10px 0;">
                                <tr>
                                    <td style="font-size: 12.5px; line-height: 1.6; color: #92400e;">
                                        <strong>⚠️ Jaga Kerahasiaan Akun:</strong><br>
                                        Jangan berikan kode ini kepada siapa pun, termasuk staf atau pihak yang mengatasnamakan CIO Network Solution. Jika Anda tidak melakukan permintaan ini, segera abaikan pesan ini atau hubungi tim IT Administrator.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- DIVIDER -->
                    <tr>
                        <td style="padding: 0 32px;">
                            <div style="border-top: 1px solid #e2e8f0;"></div>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 32px; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.6;">
                            <div style="font-weight: 600; color: #64748b; margin-bottom: 4px;">
                                &copy; {{ date('Y') }} PT CIO Network Solution
                            </div>
                            <div>
                                Email otomatis dari server notifikasi sistem keuangan.<br>
                                Mohon untuk tidak membalas email ini secara langsung.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
