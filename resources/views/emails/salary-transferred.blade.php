<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Pembayaran Gaji</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 40px 15px 50px 15px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- BRAND HEADER -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); padding: 34px 30px 30px 30px; text-align: center;">
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
                                @if(($paymentType ?? 'xendit') === 'manual')
                                    <div style="display: inline-block; background-color: #eff6ff; color: #1d4ed8; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                        ✓ Gaji Berhasil Dibayarkan (Kas/Tunai)
                                    </div>
                                @else
                                    <div style="display: inline-block; background-color: #dcfce7; color: #15803d; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                        ✓ Gaji Berhasil Ditransfer
                                    </div>
                                @endif

                                <h2 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 20px; font-weight: 700;">
                                    Slip Pembayaran Gaji — {{ $monthYearStr }}
                                </h2>
                                <p style="margin: 0; color: #64748b; font-size: 14px; line-height: 1.5;">
                                    Halo <strong style="color: #0f172a;">{{ $employee->name }}</strong>, gaji Anda untuk periode <strong>{{ $monthYearStr }}</strong> telah berhasil diproses @if(($paymentType ?? 'xendit') === 'manual') dan dicatat sebagai pembayaran tunai / kas. @else dan ditransfer ke rekening bank Anda. @endif
                                </p>
                            </div>

                            <!-- NET SALARY CARD -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0;">
                                <tr>
                                    <td align="center" style="background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%); border: 2px solid #bbf7d0; border-radius: 14px; padding: 22px;">
                                        <div style="font-size: 12px; color: #166534; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                            TOTAL GAJI BERSIH (TAKE HOME PAY)
                                        </div>
                                        <div style="font-size: 34px; font-weight: 800; color: #15803d; margin-top: 6px;">
                                            Rp {{ number_format($netSalary, 0, ',', '.') }}
                                        </div>
                                        <div style="font-size: 12.5px; color: #64748b; margin-top: 4px;">
                                            Diproses pada {{ $dateStr }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- RINCIAN GAJI TABLE -->
                            <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                📋 Rincian Penghitungan Gaji:
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">(+) Gaji Pokok</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right;">Rp {{ number_format($baseSalary, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">(+) Total Tunjangan</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #059669; font-weight: 600; text-align: right;">+ Rp {{ number_format($totalAllowance, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 13px; color: #64748b;">(-) Potongan Kasbon</td>
                                    <td style="padding: 6px 0; font-size: 13px; color: #dc2626; font-weight: 600; text-align: right;">- Rp {{ number_format($totalCashAdvance, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 4px;"></td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 14px; color: #0f172a; font-weight: 700;">Gaji Bersih Diterima</td>
                                    <td style="padding: 6px 0; font-size: 14px; color: #15803d; font-weight: 800; text-align: right;">Rp {{ number_format($netSalary, 0, ',', '.') }}</td>
                                </tr>
                            </table>

                            <!-- INFORMASI PEMBAYARAN / REKENING -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 18px; margin-bottom: 20px;">
                                <tr>
                                    <td style="font-size: 12.5px; color: #64748b;">
                                        @if(($paymentType ?? 'xendit') === 'manual')
                                            <strong>Metode Pembayaran:</strong><br>
                                            <span style="color: #1e40af; font-weight: 600;">💵 Pembayaran Tunai / Kas Operasional (Manual)</span>
                                        @else
                                            <strong>Rekening Tujuan Transfer:</strong><br>
                                            <span style="color: #0f172a; font-weight: 600;">{{ $bankName }} - {{ $accountNumber }} a/n {{ $accountHolderName }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- TOMBOL LINK INVOICE / BUKTI PEMBAYARAN -->
                            @if(!empty($invoiceUrl))
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 24px 0 18px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $invoiceUrl }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%); color: #ffffff; text-decoration: none; padding: 13px 28px; border-radius: 10px; font-size: 13.5px; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);">
                                            📄 Lihat &amp; Cetak Bukti Pembayaran (Invoice)
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6; text-align: center;">
                                Anda juga dapat mengakses dan mengunduh slip gaji resmi kapan saja melalui menu Dashboard CIO Keuangan.
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 28px; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.5;">
                            &copy; {{ date('Y') }} PT CIO Network Solution &bull; Notifikasi Otomatis Penggajian
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
