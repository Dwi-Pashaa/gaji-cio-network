<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Kasbon</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 40px 15px 50px 15px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- BRAND HEADER -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #475569 0%, #334155 100%); padding: 32px 30px 28px 30px; text-align: center;">
                            <table border="0" cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                    <td align="center" style="background-color: rgba(255, 255, 255, 0.15); padding: 8px 18px; border-radius: 10px;">
                                        <span style="font-size: 18px; font-weight: 800; color: #ffffff; letter-spacing: 1px; text-transform: uppercase;">
                                            CIO NETWORK SOLUTION
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top: 10px; font-size: 13px; color: #cbd5e1; font-weight: 600;">
                                Sistem Informasi Penggajian &amp; Keuangan
                            </div>
                        </td>
                    </tr>

                    <!-- CONTENT BODY -->
                    <tr>
                        <td style="padding: 32px 28px 24px 28px;">
                            <div style="text-align: center; margin-bottom: 20px;">
                                <div style="display: inline-block; background-color: #fee2e2; color: #b91c1c; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    ✕ Pengajuan Tidak Disetujui
                                </div>
                                <h2 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 20px; font-weight: 700;">
                                    Halo, {{ $employee->name }}
                                </h2>
                                <p style="margin: 0; color: #64748b; font-size: 14px; line-height: 1.5;">
                                    Mohon maaf, pengajuan kasbon Anda untuk keperluan <strong>"{{ $cashAdvance->title }}"</strong> belum dapat disetujui pada saat ini.
                                </p>
                            </div>

                            <!-- DETAILS TABLE -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Keperluan / Judul</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">{{ $cashAdvance->title }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Nominal Pengajuan</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">Rp {{ number_format($cashAdvance->amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Tanggal Keputusan</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">
                                        {{ now()->translatedFormat('l, d F Y - H:i') }} WIB
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Status</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #dc2626; font-weight: 700; text-align: right;">Ditolak</td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6; text-align: center;">
                                Silakan hubungi bagian HRD / Keuangan untuk informasi lebih lanjut mengenai pengajuan ini.
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 28px; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.5;">
                            &copy; {{ date('Y') }} PT CIO Network Solution &bull; Notifikasi Otomatis Sistem Keuangan
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
