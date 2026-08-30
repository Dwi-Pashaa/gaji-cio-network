<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transfer Kasbon #KB-{{ $cashAdvance->id }}</title>
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
            max-width: 650px;
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
        .badge-approved {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid #3b82f6;
        }
        .badge-other {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid #f59e0b;
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
            border-bottom: 1px dashed #e5e7eb;
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
        .xendit-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .xendit-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .xendit-title svg {
            color: #2563eb;
        }
        .xendit-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .xendit-row:last-child {
            margin-bottom: 0;
        }
        .xendit-row .key {
            color: #64748b;
        }
        .xendit-row .val {
            font-weight: 600;
            color: #0f172a;
            font-family: monospace;
            font-size: 13px;
        }
        .invoice-footer {
            padding: 20px 30px;
            background: #fafafa;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-note {
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.4;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-print:hover {
            background: #1d4ed8;
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
                    <div class="company-title">{{ config('app.name', 'CIO Network') }}</div>
                    <div class="company-subtitle">Bukti Pembayaran Kasbon Karyawan</div>
                </div>
                <div>
                    @if($cashAdvance->status === 'transferred')
                        <span class="badge-status badge-transferred">✓ Berhasil Ditransfer</span>
                    @elseif($cashAdvance->status === 'approved')
                        <span class="badge-status badge-approved">✓ Disetujui</span>
                    @else
                        <span class="badge-status badge-other">{{ ucfirst($cashAdvance->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="invoice-amount-box">
                <div class="amount-label">Jumlah Kasbon</div>
                <div class="amount-value">Rp {{ number_format($cashAdvance->amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="invoice-body">
            <div class="section-title">Informasi Penerima & Pengajuan</div>
            <div class="info-grid">
                <div class="info-group">
                    <label>Nama Karyawan</label>
                    <span>{{ optional($cashAdvance->user)->name ?? '-' }}</span>
                </div>
                <div class="info-group">
                    <label>No. Pengajuan</label>
                    <span>#KB-{{ str_pad($cashAdvance->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-group">
                    <label>Tanggal Pengajuan</label>
                    <span>{{ \Carbon\Carbon::parse($cashAdvance->request_date)->translatedFormat('d F Y') }}</span>
                </div>
                <div class="info-group">
                    <label>Tanggal Pencairan / Transfer</label>
                    <span>
                        @if($cashAdvance->transfer_at)
                            {{ \Carbon\Carbon::parse($cashAdvance->transfer_at)->translatedFormat('d F Y - H:i') }} WIB
                        @elseif($cashAdvance->approved_date)
                            {{ \Carbon\Carbon::parse($cashAdvance->approved_date)->translatedFormat('d F Y') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="info-group" style="grid-column: span 2;">
                    <label>Keterangan / Keperluan</label>
                    <span>{{ $cashAdvance->title }}</span>
                </div>
            </div>

            <div class="section-title">Rekening Tujuan Transfer</div>
            <div class="info-grid">
                <div class="info-group">
                    <label>Bank Tujuan</label>
                    <span>{{ $cashAdvance->bank_name ?? '-' }}</span>
                </div>
                <div class="info-group">
                    <label>Nomor Rekening</label>
                    <span>{{ $cashAdvance->account_number ?? '-' }}</span>
                </div>
                <div class="info-group" style="grid-column: span 2;">
                    <label>Nama Pemilik Rekening</label>
                    <span>{{ $cashAdvance->account_holder_name ?? optional($cashAdvance->user)->name ?? '-' }}</span>
                </div>
            </div>

            @if($cashAdvance->xendit_disbursement_id)
                <div class="xendit-box">
                    <div class="xendit-title" style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9" /><path d="M12 7v5l3 3" /></svg>
                            Referensi Pembayaran Gateway (Xendit)
                        </span>
                        <button type="button" id="btnSync" onclick="syncXenditStatus()" style="background: none; border: 1px solid #cbd5e1; border-radius: 4px; padding: 3px 8px; font-size: 11px; cursor: pointer; color: #475569; display: inline-flex; align-items: center; gap: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" id="syncIcon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -5v5h5" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 5v-5h-5" /></svg>
                            <span id="syncText">Cek Status Terkini</span>
                        </button>
                    </div>
                    <div class="xendit-row">
                        <span class="key">Disbursement ID</span>
                        <span class="val">{{ $cashAdvance->xendit_disbursement_id }}</span>
                    </div>
                    <div class="xendit-row">
                        <span class="key">Status Xendit</span>
                        <span class="val" id="valXenditStatus" style="color: {{ ($cashAdvance->xendit_status === 'COMPLETED' ? '#10b981' : ($cashAdvance->xendit_status === 'FAILED' ? '#ef4444' : '#f59e0b')) }};">
                            {{ $cashAdvance->xendit_status ?? 'PENDING' }}
                        </span>
                    </div>
                    <div class="xendit-row">
                        <span class="key">Channel</span>
                        <span class="val">Bank Transfer Direct</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="invoice-footer">
            <div class="footer-note">
                Dokumen ini merupakan bukti sah transaksi kasbon elektronik.<br>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        function syncXenditStatus() {
            const btn = document.getElementById('btnSync');
            const syncText = document.getElementById('syncText');
            btn.disabled = true;
            syncText.innerText = 'Memeriksa...';

            fetch("{{ route('cash.advance.syncStatus', $cashAdvance->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                syncText.innerText = 'Cek Status Terkini';
                if (data.code === 200) {
                    const el = document.getElementById('valXenditStatus');
                    if (el) {
                        el.innerText = data.xendit_status;
                        el.style.color = data.xendit_status === 'COMPLETED' ? '#10b981' : (data.xendit_status === 'FAILED' ? '#ef4444' : '#f59e0b');
                    }
                    if (data.xendit_status === 'COMPLETED') {
                        setTimeout(() => window.location.reload(), 800);
                    }
                } else {
                    alert(data.message || 'Gagal memeriksa status');
                }
            })
            .catch(err => {
                btn.disabled = false;
                syncText.innerText = 'Cek Status Terkini';
                console.error(err);
            });
        }
    </script>
</body>
</html>
