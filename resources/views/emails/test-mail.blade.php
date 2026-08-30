<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uji Coba Pengiriman Email - CIO Keuangan</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 40px 15px 50px 15px;">
        <tr>
            <td align="center">
                <!-- MAIN CONTAINER -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 560px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- BRAND HEADER -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); padding: 36px 30px 32px 30px; text-align: center;">
                            <table border="0" cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                    <td align="center" style="background-color: rgba(255, 255, 255, 0.18); padding: 10px 20px; border-radius: 12px; backdrop-filter: blur(8px);">
                                        <span style="font-size: 20px; font-weight: 800; color: #ffffff; letter-spacing: 1px; text-transform: uppercase;">
                                            CIO NETWORK SOLUTION
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top: 14px; font-size: 13px; color: #d1fae5; letter-spacing: 0.5px; text-transform: uppercase; font-weight: 600;">
                                Sistem Informasi Penggajian &amp; Keuangan
                            </div>
                        </td>
                    </tr>

                    <!-- CONTENT BODY -->
                    <tr>
                        <td style="padding: 36px 32px 30px 32px;">
                            
                            <!-- SUCCESS BADGE -->
                            <div style="text-align: center; margin-bottom: 18px;">
                                <div style="display: inline-block; width: 56px; height: 56px; line-height: 56px; background-color: #dcfce7; border-radius: 50%; color: #16a34a; font-size: 28px; font-weight: bold; text-align: center;">
                                    ✓
                                </div>
                            </div>

                            <h2 style="margin: 0 0 12px 0; color: #0f172a; font-size: 20px; font-weight: 700; text-align: center;">
                                Uji Coba Konfigurasi SMTP Berhasil!
                            </h2>
                            <p style="margin: 0 0 24px 0; color: #475569; font-size: 14px; line-height: 1.6; text-align: center;">
                                Halo, email ini mengonfirmasi bahwa server email SMTP sistem <strong>CIO Keuangan</strong> telah terkonfigurasi dengan benar dan siap mengirimkan notifikasi serta kode OTP.
                            </p>

                            <!-- DETAILS TABLE -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">SMTP Host</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;"><code>{{ config('mail.mailers.smtp.host') }}</code></td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Port &amp; Enkripsi</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">Port {{ config('mail.mailers.smtp.port') }} ({{ strtoupper(config('mail.mailers.smtp.encryption', 'TLS')) }})</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Pengirim (From)</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">{{ config('mail.from.address') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Waktu Pengujian</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
                                </tr>
                            </table>

                            <p style="margin: 20px 0 0 0; color: #64748b; font-size: 13px; line-height: 1.6; text-align: center;">
                                Anda dapat mengaktifkan saluran email untuk reset password melalui menu <strong>Pengaturan</strong> di admin panel.
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 22px 32px; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.6;">
                            <div style="font-weight: 600; color: #64748b; margin-bottom: 4px;">
                                &copy; {{ date('Y') }} PT CIO Network Solution
                            </div>
                            <div>
                                Email uji coba diagnostik server notifikasi.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
