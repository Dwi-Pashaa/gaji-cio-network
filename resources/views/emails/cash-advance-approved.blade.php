<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasbon Disetujui</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 40px 15px 50px 15px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- BRAND HEADER -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); padding: 32px 30px 28px 30px; text-align: center;">
                            <table border="0" cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                    <td align="center" style="background-color: rgba(255, 255, 255, 0.18); padding: 8px 18px; border-radius: 10px;">
                                        <span style="font-size: 18px; font-weight: 800; color: #ffffff; letter-spacing: 1px; text-transform: uppercase;">
                                            CIO NETWORK SOLUTION
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top: 10px; font-size: 13px; color: #d1fae5; font-weight: 600;">
                                Sistem Informasi Penggajian &amp; Keuangan
                            </div>
                        </td>
                    </tr>

                    <!-- CONTENT BODY -->
                    <tr>
                        <td style="padding: 32px 28px 24px 28px;">
                            <div style="text-align: center; margin-bottom: 20px;">
                                <div style="display: inline-block; background-color: #dcfce7; color: #15803d; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $transferred ? '✓ Kasbon Disetujui & Ditransfer' : '✓ Kasbon Disetujui' }}
                                </div>
                                <h2 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 20px; font-weight: 700;">
                                    Halo, {{ $employee->name }}
                                </h2>
                                <p style="margin: 0; color: #64748b; font-size: 14px; line-height: 1.5;">
                                    Permintaan kasbon Anda untuk keperluan <strong>"{{ $cashAdvance->title }}"</strong> telah disetujui {{ $transferred ? 'dan dana telah berhasil ditransfer ke rekening Anda.' : 'oleh pihak manajemen.' }}
                                </p>
                            </div>

                            <!-- NOMINAL HIGHLIGHT -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0;">
                                <tr>
                                    <td align="center" style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 12px; padding: 20px;">
                                        <div style="font-size: 12px; color: #166534; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            NOMINAL DICAIRKAN
                                        </div>
                                        <div style="font-size: 32px; font-weight: 800; color: #15803d; margin-top: 4px;">
                                            Rp {{ number_format($cashAdvance->amount, 0, ',', '.') }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- DETAILS TABLE -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Keperluan / Judul</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">{{ $cashAdvance->title }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Rekening Bank Tujuan</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">
                                        {{ $cashAdvance->bank_name ?? '-' }} - {{ $cashAdvance->account_number ?? '-' }}<br>
                                        <span style="font-size: 12px; color: #64748b; font-weight: normal;">a/n {{ $cashAdvance->account_holder_name ?? $employee->name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Waktu Persetujuan</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">
                                        {{ now()->translatedFormat('l, d F Y - H:i') }} WIB
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">Status Transfer</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #15803d; font-weight: 700; text-align: right;">
                                        {{ $transferred ? 'Berhasil Ditransfer' : 'Disetujui' }}
                                    </td>
                                </tr>
                            </table>

                            @if($invoiceUrl)
                            <div style="text-align: center; margin-top: 24px;">
                                <a href="{{ $invoiceUrl }}" style="display: inline-block; background-color: #059669; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                                    Lihat Bukti Transfer / Invoice &rarr;
                                </a>
                            </div>
                            @endif
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
