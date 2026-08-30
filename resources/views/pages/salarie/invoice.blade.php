<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transfer Gaji - {{ $payment->period_label }} - {{ $payment->user->name ?? 'Karyawan' }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        body {
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 30px 15px;
            font-size: 14px;
        }
        .invoice-card {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .invoice-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 30px;
            position: relative;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .company-title {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 4px;
        }
        .badge-status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-transferred {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid #10b981;
        }
        .badge-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid #f59e0b;
        }
        .badge-failed {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid #ef4444;
        }
        .invoice-amount-box {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        .amount-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .amount-value {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            margin-top: 4px;
        }
        .invoice-body {
            padding: 30px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }
        .info-group label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 3px;
        }
        .info-group span {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }
        .salary-breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .salary-breakdown-table th {
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            padding: 8px 12px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .salary-breakdown-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }
        .salary-breakdown-table .row-total {
            font-weight: 700;
            background-color: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: #16a34a;
        }
        .text-danger {
            color: #dc2626;
        }
        .xendit-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 24px;
            font-size: 12px;
        }
        .xendit-title {
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .xendit-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            color: #64748b;
        }
        .xendit-row .val {
            font-weight: 600;
            color: #1e293b;
        }
        .invoice-footer {
            padding: 20px 30px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #6b7280;
        }
        .btn-print {
            background-color: #1e3a8a;
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-print:hover {
            background-color: #1e40af;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
            }
            .btn-print {
                display: none;
            }
            .invoice-footer {
                justify-content: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <div class="invoice-card">
        <div class="invoice-header">
            <div class="header-top">
                <div>
                    <div class="company-title">{{ config('app.name', 'CIO Network Solution') }}</div>
                    <div class="company-subtitle">Bukti Resmi Transfer Pembayaran Gaji Karyawan</div>
                </div>
                <div>
                    @if($payment->status === 'transferred')
                        <span class="badge-status badge-transferred">✓ Berhasil Ditransfer</span>
                    @elseif($payment->status === 'pending')
                        <span class="badge-status badge-pending">⏳ Sedang Diproses</span>
                    @else
                        <span class="badge-status badge-failed">✕ {{ ucfirst($payment->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="invoice-amount-box">
                <div class="amount-label">Total Gaji Bersih Ditransfer (Periode {{ $payment->period_label }})</div>
                <div class="amount-value">Rp {{ number_format($payment->net_salary, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="invoice-body">
            <div class="section-title">Informasi Karyawan & Pembayaran</div>
            <div class="info-grid">
                <div class="info-group">
                    <label>Nama Karyawan</label>
                    <span>{{ $payment->user->name ?? '-' }}</span>
                </div>
                <div class="info-group">
                    <label>No. Transaksi / Slip</label>
                    <span>#TRX-GJ-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-group">
                    <label>Periode Penggajian</label>
                    <span>{{ $payment->period_label }}</span>
                </div>
                <div class="info-group">
                    <label>Waktu Transfer</label>
                    <span>
                        @if($payment->transfer_at)
                            {{ $payment->transfer_at->translatedFormat('d F Y - H:i') }} WIB
                        @else
                            {{ $payment->created_at->translatedFormat('d F Y - H:i') }} WIB
                        @endif
                    </span>
                </div>
                <div class="info-group" style="grid-column: span 2;">
                    <label>Diproses Oleh</label>
                    <span>{{ $payment->transferredBy->name ?? 'Admin / Sistem' }}</span>
                </div>
            </div>

            <div class="section-title">Rekening Tujuan Transfer</div>
            <div class="info-grid">
                <div class="info-group">
                    <label>Bank Tujuan</label>
                    <span>{{ $payment->bank_name ?? '-' }}</span>
                </div>
                <div class="info-group">
                    <label>Nomor Rekening</label>
                    <span>{{ $payment->account_number ?? '-' }}</span>
                </div>
                <div class="info-group" style="grid-column: span 2;">
                    <label>Nama Pemilik Rekening</label>
                    <span>{{ $payment->account_holder_name ?? $payment->user->name ?? '-' }}</span>
                </div>
            </div>

            <div class="section-title">Rincian Komponen Gaji</div>
            <table class="salary-breakdown-table">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="text-right">Rp {{ number_format($payment->base_salary, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Tunjangan</td>
                        <td class="text-right text-success">+Rp {{ number_format($payment->total_allowance, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Potongan Kasbon</td>
                        <td class="text-right text-danger">
                            @if($payment->total_cash_advance > 0)
                                -Rp {{ number_format($payment->total_cash_advance, 0, ',', '.') }}
                            @else
                                Rp 0
                            @endif
                        </td>
                    </tr>
                    <tr class="row-total">
                        <td>Total Gaji Bersih (Take Home Pay)</td>
                        <td class="text-right text-success" style="font-size: 15px;">Rp {{ number_format($payment->net_salary, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            @if($payment->xendit_disbursement_id || $payment->xendit_external_id)
                <div class="xendit-box">
                    <div class="xendit-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9" /><path d="M12 7v5l3 3" /></svg>
                        Referensi Pembayaran Gateway (Xendit Disbursement)
                    </div>
                    @if($payment->xendit_disbursement_id)
                        <div class="xendit-row">
                            <span class="key">Disbursement ID</span>
                            <span class="val font-monospace">{{ $payment->xendit_disbursement_id }}</span>
                        </div>
                    @endif
                    @if($payment->xendit_external_id)
                        <div class="xendit-row">
                            <span class="key">External ID</span>
                            <span class="val font-monospace">{{ $payment->xendit_external_id }}</span>
                        </div>
                    @endif
                    <div class="xendit-row">
                        <span class="key">Status Xendit</span>
                        <span class="val" style="color: {{ ($payment->xendit_status === 'COMPLETED' ? '#10b981' : ($payment->xendit_status === 'FAILED' ? '#ef4444' : '#f59e0b')) }};">
                            {{ $payment->xendit_status ?? 'COMPLETED' }}
                        </span>
                    </div>
                    <div class="xendit-row">
                        <span class="key">Metode Pencairan</span>
                        <span class="val">Bank Direct Transfer Realtime</span>
                    </div>
                </div>
            @endif

            @if($payment->notes)
                <div class="info-group mb-3">
                    <label>Catatan Tambahan</label>
                    <span style="font-weight: normal; color: #4b5563;">{{ $payment->notes }}</span>
                </div>
            @endif
        </div>

        <div class="invoice-footer">
            <div class="footer-note">
                Dokumen ini merupakan bukti sah transfer pembayaran gaji elektronik.<br>
                Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }} WIB
            </div>
            <div>
                <button type="button" onclick="window.print()" class="btn-print">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </div>

</body>
</html>
