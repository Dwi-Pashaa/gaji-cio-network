@extends('layouts.app')

@section('title', 'Pengaturan Notifikasi Sistem')

@push('css')
<style>
    .channel-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.25s ease;
        cursor: pointer;
        background: #ffffff;
    }
    .channel-card:hover {
        border-color: #1a56db;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(26, 86, 219, 0.08);
    }
    .channel-card.active {
        border-color: #1a56db;
        background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 100%);
        box-shadow: 0 4px 14px rgba(26, 86, 219, 0.12);
    }
    .channel-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .channel-icon-email {
        background: #eff6ff;
        color: #2563eb;
    }
    .channel-icon-whatsapp {
        background: #ecfdf5;
        color: #059669;
    }
    .switch-toggle-custom {
        transform: scale(1.2);
        cursor: pointer;
    }
    .feature-toggle-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        transition: background-color 0.2s ease;
    }
    .feature-toggle-card:hover {
        background: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="row row-cards">
    {{-- BAGIAN KIRI: PENGATURAN NOTIFIKASI --}}
    <div class="col-lg-7">
        
        {{-- KARTU 1: SALURAN OTP RESET PASSWORD --}}
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
            <div class="card-header bg-transparent py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">1. Saluran OTP Reset Password</h3>
                        <p class="text-muted small mb-0 mt-1">Saluran yang digunakan saat user melakukan permintaan lupa / reset password</p>
                    </div>
                    <span class="badge {{ $otpChannel === 'email' ? 'bg-blue-lt text-primary' : 'bg-green-lt text-success' }} px-3 py-2 fs-6 fw-semibold" id="active-badge">
                        {{ $otpChannel === 'email' ? '📧 Email Aktif' : '💬 WhatsApp Aktif' }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <form id="form-otp-channel" action="{{ route('settings.otpChannel.update') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        {{-- OPSI 1: EMAIL --}}
                        <div class="col-md-6">
                            <label class="channel-card p-3 d-block position-relative {{ $otpChannel === 'email' ? 'active' : '' }}" id="card-email" for="channel_email">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="channel-icon-wrapper channel-icon-email">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                            <path d="M3 7l9 6l9 -6" />
                                        </svg>
                                    </div>
                                    <input class="form-check-input switch-toggle-custom" type="radio" name="otp_channel" id="channel_email" value="email" {{ $otpChannel === 'email' ? 'checked' : '' }} onchange="handleChannelChange('email')">
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Email (SMTP)</h4>
                                <p class="text-muted small mb-0">Kode OTP 6-digit dikirimkan ke alamat email terdaftar user.</p>
                            </label>
                        </div>

                        {{-- OPSI 2: WHATSAPP --}}
                        <div class="col-md-6">
                            <label class="channel-card p-3 d-block position-relative {{ $otpChannel === 'whatsapp' ? 'active' : '' }}" id="card-whatsapp" for="channel_whatsapp">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="channel-icon-wrapper channel-icon-whatsapp">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                                            <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                                        </svg>
                                    </div>
                                    <input class="form-check-input switch-toggle-custom" type="radio" name="otp_channel" id="channel_whatsapp" value="whatsapp" {{ $otpChannel === 'whatsapp' ? 'checked' : '' }} onchange="handleChannelChange('whatsapp')">
                                </div>
                                <h4 class="fw-bold text-dark mb-1">WhatsApp (Mekari)</h4>
                                <p class="text-muted small mb-0">Kode OTP dikirimkan via WhatsApp Mekari Qontak ke nomor HP user.</p>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold" id="btn-save-channel">
                            Simpan Pilihan Saluran OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KARTU 2: NOTIFIKASI KASBON & GAJI KARYAWAN --}}
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
            <div class="card-header bg-transparent py-3">
                <h3 class="card-title fw-bold text-dark mb-0">2. Notifikasi Email &amp; WhatsApp (Kasbon &amp; Gaji)</h3>
                <p class="text-muted small mb-0 mt-1">Atur pengiriman notifikasi otomatis saat terjadi pengajuan/persetujuan kasbon dan pembayaran gaji</p>
            </div>

            <div class="card-body p-4">
                <form id="form-notification-settings" action="{{ route('settings.notifications.update') }}" method="POST">
                    @csrf

                    <!-- FITUR KASBON -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="avatar avatar-xs bg-azure-lt text-azure rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M12 18h-7a2 2 0 0 1 -2 -2v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7" /><path d="M18 12h.01" /><path d="M6 12h.01" /><path d="M16 19h6" /></svg>
                            </span>
                            <h4 class="fw-bold text-dark mb-0">Fitur Kasbon (Pengajuan &amp; Persetujuan)</h4>
                        </div>
                        <p class="text-muted small mb-3">Notifikasi dikirim saat ada pengajuan baru (ke Admin) dan saat kasbon disetujui/ditolak (ke Karyawan).</p>
                        
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="feature-toggle-card d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold text-dark small">📧 Notifikasi Email</div>
                                        <div class="text-muted" style="font-size: 11.5px;">Kirim email resmi ke Admin &amp; Karyawan</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="notify_cash_advance_email" value="1" id="notify_cash_advance_email" {{ $notifyCashAdvanceEmail == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="feature-toggle-card d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold text-dark small">💬 Notifikasi WhatsApp</div>
                                        <div class="text-muted" style="font-size: 11.5px;">Kirim pesan via Mekari Qontak</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="notify_cash_advance_wa" value="1" id="notify_cash_advance_wa" {{ $notifyCashAdvanceWa == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- FITUR GAJI / PAYROLL -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="avatar avatar-xs bg-green-lt text-success rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                            </span>
                            <h4 class="fw-bold text-dark mb-0">Fitur Pembayaran Gaji / Payroll</h4>
                        </div>
                        <p class="text-muted small mb-3">Notifikasi rincian slip gaji dikirimkan ke karyawan saat transfer selesai diproses.</p>
                        
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="feature-toggle-card d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold text-dark small">📧 Kirim Slip via Email</div>
                                        <div class="text-muted" style="font-size: 11.5px;">Kirim rincian slip gaji lengkap via Email</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="notify_salary_email" value="1" id="notify_salary_email" {{ $notifySalaryEmail == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="feature-toggle-card d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold text-dark small">💬 Notifikasi WhatsApp</div>
                                        <div class="text-muted" style="font-size: 11.5px;">Kirim notifikasi transfer via Mekari WA</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="notify_salary_wa" value="1" id="notify_salary_wa" {{ $notifySalaryWa == '1' ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- EMAIL ADMIN PENERIMA NOTIFIKASI -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark small mb-1">
                            Alamat Email Admin Penerima Notifikasi Pengajuan Kasbon
                        </label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
                            </span>
                            <input type="email" name="admin_notification_email" class="form-control" placeholder="support@cionetwork.id" value="{{ $adminNotificationEmail }}">
                        </div>
                        <div class="form-text text-muted small mt-1">
                            Email ini akan menerima pemberitahuan setiap kali ada karyawan yang mengajukan kasbon baru.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold d-flex align-items-center gap-2" id="btn-save-notif">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Simpan Pengaturan Notifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KARTU 3: BATAS PENGAJUAN KASBON & BIAYA ADMIN TRANSFER --}}
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
            <div class="card-header bg-transparent py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">3. Batas Pengajuan Kasbon &amp; Biaya Admin Transfer (Finance)</h3>
                        <p class="text-muted small mb-0 mt-1">Atur limit maksimal nominal kasbon karyawan dan biaya administrasi transfer bank / Xendit</p>
                    </div>
                    <span class="badge bg-purple-lt text-purple px-3 py-2 fs-6 fw-semibold">
                        💳 Finance &amp; Payroll
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <form id="form-finance-settings" action="{{ route('settings.finance.update') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        {{-- MAKSIMAL PENGAJUAN KASBON --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Batas Maksimal Nominal Pengajuan Kasbon (Rp)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-primary bg-light">Rp</span>
                                <input type="text" name="max_cash_advance_amount" id="max_cash_advance_amount" class="form-control fw-bold text-primary rupiah-input" placeholder="0" value="{{ $maxCashAdvance ? number_format((float)$maxCashAdvance, 0, ',', '.') : '0' }}">
                            </div>
                            <div class="form-text text-muted small mt-1">
                                Batas tertinggi kasbon yang boleh diajukan karyawan dalam sekali pengajuan. Isi <code>0</code> jika tidak dibatasi.
                            </div>
                        </div>

                        {{-- BIAYA ADMIN TRANSFER DISBURSEMENT --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Biaya Admin Transfer Bank / Xendit (Rp)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-danger bg-light">Rp</span>
                                <input type="text" name="admin_fee_disbursement" id="admin_fee_disbursement" class="form-control fw-bold text-danger rupiah-input" placeholder="0" value="{{ $adminFee ? number_format((float)$adminFee, 0, ',', '.') : '0' }}">
                            </div>
                            <div class="form-text text-muted small mt-1">
                                Biaya administrasi transfer per transaksi yang otomatis diperhitungkan saat <strong>Transfer Gaji</strong> dan <strong>Transfer Kasbon</strong> via Xendit.
                            </div>
                        </div>
                    </div>

                    {{-- INFORMASI PENERAPAN BIAYA ADMIN --}}
                    <div class="p-3 bg-light rounded border border-light mb-4">
                        <div class="fw-semibold text-dark small mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                            Penerapan Biaya Admin &amp; Batas Kasbon:
                        </div>
                        <ul class="text-muted small mb-0 ps-3">
                            <li><strong>Pengajuan Kasbon</strong>: Validasi otomatis akan menolak nominal pengajuan kasbon jika melebihi batas maksimal yang ditentukan.</li>
                            <li><strong>Transfer Kasbon Xendit</strong>: Biaya admin otomatis diikutsertakan dalam pemeriksaan kecukupan saldo website dan pemotongan mutasi saldo Finance.</li>
                            <li><strong>Transfer Gaji Xendit</strong>: Biaya admin otomatis masuk dalam rincian kalkulasi total biaya transfer gaji dan mutasi saldo Finance.</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold d-flex align-items-center gap-2" id="btn-save-finance">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Simpan Batas Kasbon &amp; Biaya Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- BAGIAN KANAN: DIAGNOSTIK & STATUS MAIL / MEKARI --}}
    <div class="col-lg-5">
        {{-- TEST MAIL SERVER --}}
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
            <div class="card-header bg-transparent py-3">
                <h3 class="card-title fw-bold text-dark mb-0">Uji Coba Pengiriman Email (SMTP)</h3>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Kirimkan email uji coba untuk memverifikasi apakah server SMTP aplikasi dapat mengirim email HTML dengan sukses.</p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Alamat Email Penerima Tes</label>
                    <div class="input-group">
                        <input type="email" id="test-email-input" class="form-control" placeholder="contoh@gmail.com" value="{{ Auth::user()->email ?? 'support@cionetwork.id' }}" />
                        <button type="button" class="btn btn-outline-primary fw-semibold" id="btn-send-test-mail" onclick="handleSendTestMail()">
                            Kirim Uji Coba
                        </button>
                    </div>
                    <div class="form-text text-muted small mt-2">
                        Host: <code>{{ $mailHost ?: 'Local/Env' }}</code> (Port {{ config('mail.mailers.smtp.port', 587) }}) | Pengirim: <code>{{ $mailFrom ?: 'support@cionetwork.id' }}</code>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATUS MEKARI QONTAK --}}
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-transparent py-3">
                <h3 class="card-title fw-bold text-dark mb-0">Status WhatsApp Mekari Qontak</h3>
            </div>
            <div class="card-body p-4">
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">API Token Status</span>
                        <span class="badge {{ $mekariToken ? 'bg-green-lt text-success' : 'bg-red-lt text-danger' }}">
                            {{ $mekariToken ? 'Terkonfigurasi' : 'Belum Diisi' }}
                        </span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">Channel Integration ID</span>
                        <span class="badge {{ $mekariChannelId ? 'bg-green-lt text-success' : 'bg-yellow-lt text-warning' }}">
                            {{ $mekariChannelId ? substr($mekariChannelId, 0, 8) . '...' : 'Belum Diisi' }}
                        </span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">Template OTP Lupa Password</span>
                        <span class="badge bg-blue-lt text-primary">kode_otp_apps</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted">Integrasi Broadcast WA</span>
                        <span class="badge bg-green-lt text-success">Siap Digunakan</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function handleChannelChange(channel) {
        document.getElementById('card-email').classList.remove('active');
        document.getElementById('card-whatsapp').classList.remove('active');

        if (channel === 'email') {
            document.getElementById('card-email').classList.add('active');
            document.getElementById('active-badge').className = 'badge bg-blue-lt text-primary px-3 py-2 fs-6 fw-semibold';
            document.getElementById('active-badge').innerText = '📧 Email Aktif';
        } else {
            document.getElementById('card-whatsapp').classList.add('active');
            document.getElementById('active-badge').className = 'badge bg-green-lt text-success px-3 py-2 fs-6 fw-semibold';
            document.getElementById('active-badge').innerText = '💬 WhatsApp Aktif';
        }
    }

    // Submit form simpan pengaturan saluran OTP
    document.getElementById('form-otp-channel').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save-channel');
        const selectedChannel = document.querySelector('input[name="otp_channel"]:checked').value;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

        $.ajax({
            url: "{{ route('settings.otpChannel.update') }}",
            type: "POST",
            data: {
                otp_channel: selectedChannel
            },
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: res.message,
                    timer: 2500,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                });
            },
            complete: function () {
                btn.disabled = false;
                btn.innerHTML = 'Simpan Pilihan Saluran OTP';
            }
        });
    });

    // Submit form simpan pengaturan notifikasi Kasbon & Gaji
    document.getElementById('form-notification-settings').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save-notif');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

        const formData = {
            notify_cash_advance_email: document.getElementById('notify_cash_advance_email').checked ? '1' : '0',
            notify_cash_advance_wa: document.getElementById('notify_cash_advance_wa').checked ? '1' : '0',
            notify_salary_email: document.getElementById('notify_salary_email').checked ? '1' : '0',
            notify_salary_wa: document.getElementById('notify_salary_wa').checked ? '1' : '0',
            admin_notification_email: document.querySelector('input[name="admin_notification_email"]').value
        };

        $.ajax({
            url: "{{ route('settings.notifications.update') }}",
            type: "POST",
            data: formData,
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: res.message,
                    timer: 2500,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                });
            },
            complete: function () {
                btn.disabled = false;
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> Simpan Pengaturan Notifikasi';
            }
        });
    });

    // Auto format Rupiah pada input finance
    document.querySelectorAll('.rupiah-input').forEach(function(input) {
        input.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            this.value = val ? new Intl.NumberFormat('id-ID').format(val) : '0';
        });
    });

    // Submit form simpan batas kasbon & biaya admin
    document.getElementById('form-finance-settings').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save-finance');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

        const formData = {
            max_cash_advance_amount: document.getElementById('max_cash_advance_amount').value,
            admin_fee_disbursement: document.getElementById('admin_fee_disbursement').value,
        };

        $.ajax({
            url: "{{ route('settings.finance.update') }}",
            type: "POST",
            data: formData,
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: res.message,
                    timer: 2500,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                });
            },
            complete: function () {
                btn.disabled = false;
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> Simpan Batas Kasbon & Biaya Admin';
            }
        });
    });

    // Test send email
    function handleSendTestMail() {
        const email = document.getElementById('test-email-input').value.trim();
        const btn = document.getElementById('btn-send-test-mail');

        if (!email) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Masukkan alamat email penerima tes terlebih dahulu.'
            });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';

        $.ajax({
            url: "{{ route('settings.testEmail') }}",
            type: "POST",
            data: {
                email: email
            },
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Terkirim!',
                    text: res.message
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim Email',
                    text: xhr.responseJSON?.message || 'Gagal mengirim email tes. Periksa koneksi atau konfigurasi SMTP .env Anda.'
                });
            },
            complete: function () {
                btn.disabled = false;
                btn.innerHTML = 'Kirim Uji Coba';
            }
        });
    }
</script>
@endpush
