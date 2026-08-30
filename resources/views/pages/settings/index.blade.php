@extends('layouts.app')

@section('title', 'Pengaturan Notifikasi & Saluran OTP')

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
        width: 52px;
        height: 52px;
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
        transform: scale(1.3);
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="row row-cards">
    {{-- ALERT PEMBERITAHUAN CAKUPAN PENGATURAN --}}
    <div class="col-12">
        <div class="alert alert-info shadow-sm border-0 d-flex align-items-center mb-3" style="border-radius: 10px; background-color: #eff6ff; border-left: 4px solid #2563eb !important;">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary me-3 flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9h.01" />
                <path d="M11 12h1v4h1" />
                <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
            </svg>
            <div>
                <strong class="text-primary d-block mb-1">Informasi Cakupan Pengaturan:</strong>
                <span class="text-secondary small">
                    Pengaturan saluran di bawah ini <strong>hanya berlaku khusus untuk pengiriman Kode OTP Reset Password</strong> akun.
                    Semua notifikasi transaksi lain (seperti <em>Kasbon, Approval Kasbon, Slip Gaji/Payroll, dan Xendit</em>) <strong>tetap 100% menggunakan WhatsApp Mekari Qontak</strong>.
                </span>
            </div>
        </div>
    </div>

    {{-- KARTU UTAMA: SALURAN OTP RESET PASSWORD --}}
    <div class="col-lg-7">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-transparent py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">Saluran Notifikasi OTP Reset Password</h3>
                        <p class="text-muted small mb-0 mt-1">Pilih saluran yang digunakan saat user melakukan permintaan lupa / reset password</p>
                    </div>
                    <span class="badge {{ $otpChannel === 'email' ? 'bg-blue-lt text-primary' : 'bg-green-lt text-success' }} px-3 py-2 fs-6 fw-semibold" id="active-badge">
                        {{ $otpChannel === 'email' ? '📧 Email Aktif' : '💬 WhatsApp Aktif' }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <form id="form-otp-channel" action="{{ route('settings.otpChannel.update') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-4">
                        {{-- OPSI 1: EMAIL --}}
                        <div class="col-md-6">
                            <label class="channel-card p-3 d-block position-relative {{ $otpChannel === 'email' ? 'active' : '' }}" id="card-email" for="channel_email">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="channel-icon-wrapper channel-icon-email">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                            <path d="M3 7l9 6l9 -6" />
                                        </svg>
                                    </div>
                                    <input class="form-check-input switch-toggle-custom" type="radio" name="otp_channel" id="channel_email" value="email" {{ $otpChannel === 'email' ? 'checked' : '' }} onchange="handleChannelChange('email')">
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Email (SMTP)</h4>
                                <p class="text-muted small mb-0">Kode OTP 6-digit dikirimkan langsung ke alamat email terdaftar user.</p>
                                <div class="mt-3 pt-2 border-top">
                                    <span class="badge bg-blue-subtle text-primary small">Rekomendasi Utama</span>
                                </div>
                            </label>
                        </div>

                        {{-- OPSI 2: WHATSAPP --}}
                        <div class="col-md-6">
                            <label class="channel-card p-3 d-block position-relative {{ $otpChannel === 'whatsapp' ? 'active' : '' }}" id="card-whatsapp" for="channel_whatsapp">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="channel-icon-wrapper channel-icon-whatsapp">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                                            <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                                        </svg>
                                    </div>
                                    <input class="form-check-input switch-toggle-custom" type="radio" name="otp_channel" id="channel_whatsapp" value="whatsapp" {{ $otpChannel === 'whatsapp' ? 'checked' : '' }} onchange="handleChannelChange('whatsapp')">
                                </div>
                                <h4 class="fw-bold text-dark mb-1">WhatsApp (Mekari)</h4>
                                <p class="text-muted small mb-0">Kode OTP dikirimkan via Broadcast WhatsApp Mekari Qontak ke nomor HP user.</p>
                                <div class="mt-3 pt-2 border-top">
                                    <span class="badge bg-green-subtle text-success small">Broadcast WA</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <div class="text-muted small">
                            Status tersimpan otomatis saat Anda menekan tombol simpan atau memilih saluran.
                        </div>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold d-flex align-items-center gap-2" id="btn-save-channel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- KARTU KANAN: DIAGNOSTIK & STATUS MAIL / MEKARI --}}
    <div class="col-lg-5">
        {{-- TEST MAIL SERVER --}}
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
            <div class="card-header bg-transparent py-3">
                <h3 class="card-title fw-bold text-dark mb-0">Uji Coba Pengiriman Email (SMTP)</h3>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Kirimkan email uji coba untuk memverifikasi apakah server SMTP aplikasi dapat mengirim email dengan sukses.</p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Alamat Email Penerima Tes</label>
                    <div class="input-group">
                        <input type="email" id="test-email-input" class="form-control" placeholder="contoh@gmail.com" value="{{ Auth::user()->email ?? '' }}" />
                        <button type="button" class="btn btn-outline-primary fw-semibold" id="btn-send-test-mail" onclick="handleSendTestMail()">
                            Kirim Uji Coba
                        </button>
                    </div>
                    <div class="form-text text-muted small mt-2">
                        Host SMTP: <code>{{ $mailHost ?: 'Local/Env' }}</code> | Dari: <code>{{ $mailFrom ?: 'hello@example.com' }}</code>
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
                        <span class="text-muted">Notifikasi Kasbon & Payroll</span>
                        <span class="badge bg-green-lt text-success">Selalu Aktif via WA</span>
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
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> Simpan Pengaturan';
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
