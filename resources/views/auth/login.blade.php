@extends('layouts.auth')

@section('title', 'Login')

@push('css')
<style>
    .login-container {
        width: 100% !important;
        max-width: none !important;
        margin: 0 auto;
    }
    .auth-header-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(26, 86, 219, 0.1) 0%, rgba(26, 86, 219, 0.2) 100%);
        color: #1a56db;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(26, 86, 219, 0.12);
    }
    .form-control-modern {
        border-radius: 10px !important;
        border: 1.5px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #f8fafc;
    }
    .form-control-modern:focus {
        background-color: #ffffff;
        border-color: #1a56db;
        box-shadow: 0 0 0 4px rgba(26, 86, 219, 0.12) !important;
    }
    .input-group-modern {
        position: relative;
    }
    .input-group-modern .input-icon-left {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        z-index: 4;
        pointer-events: none;
        transition: color 0.2s ease;
    }
    .input-group-modern .form-control-modern {
        padding-left: 42px;
    }
    .input-group-modern .btn-toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        background: transparent;
        border: none;
        padding: 4px;
        cursor: pointer;
        z-index: 4;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .input-group-modern .btn-toggle-password:hover {
        color: #1a56db;
        background: #f1f5f9;
    }
    .input-group-modern:focus-within .input-icon-left {
        color: #1a56db;
    }
    .btn-login-modern {
        background: linear-gradient(135deg, #1a56db 0%, #103ba1 100%) !important;
        border: none !important;
        border-radius: 10px !important;
        padding: 0.8rem 1.5rem !important;
        font-size: 1rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.01em;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(26, 86, 219, 0.3) !important;
        transition: all 0.25s ease !important;
    }
    .btn-login-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(26, 86, 219, 0.4) !important;
        background: linear-gradient(135deg, #1e429f 0%, #0d2f80 100%) !important;
    }
    .btn-login-modern:active {
        transform: translateY(0);
    }
    .divider-text {
        display: flex;
        align-items: center;
        text-align: center;
        color: #94a3b8;
        font-size: 0.8rem;
        margin: 1.5rem 0;
    }
    .divider-text::before, .divider-text::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }
    .divider-text span {
        padding: 0 0.75rem;
    }
    .badge-secure {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
    <div class="login-container">
        <!-- Card Header Title -->
        <div class="text-center mb-4">
            <div class="auth-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /><path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 12l0 2.5" /></svg>
            </div>
            <h2 class="h3 fw-bold text-dark mb-1">Selamat Datang Kembali</h2>
            <p class="text-muted small mb-0">Masuk dengan kredensial akun keuangan Anda</p>
        </div>

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible shadow-sm border-0 mb-3" role="alert" style="border-radius: 10px; background-color: #fef2f2; border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
                    <div class="text-danger small fw-medium">{{ session('error') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning alert-dismissible shadow-sm border-0 mb-3" role="alert" style="border-radius: 10px; background-color: #fffbeb; border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-warning me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 17h.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                    <div class="text-warning small fw-medium">{{ session('warning') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <form action="{{ route('post.login') }}" method="POST" autocomplete="off" novalidate id="form-login">
            @csrf
            
            <!-- Username Input -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small mb-1" for="username">
                    Username <span class="text-danger">*</span>
                </label>
                <div class="input-group-modern">
                    <span class="input-icon-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </span>
                    <input type="text" name="username" id="username" class="form-control form-control-modern @error('username') is-invalid @enderror" placeholder="Masukkan username" value="{{ old('username') }}" autocomplete="off" autofocus required />
                </div>
                @error('username')
                    <div class="text-danger small mt-1 d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-semibold text-dark small mb-0" for="password">
                        Password <span class="text-danger">*</span>
                    </label>
                    <a href="javascript:void(0)" onclick="openForgotPasswordModal()" class="small text-primary text-decoration-none fw-semibold">
                        Lupa Password?
                    </a>
                </div>
                <div class="input-group-modern">
                    <span class="input-icon-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <input type="password" name="password" id="password" class="form-control form-control-modern pe-5 @error('password') is-invalid @enderror" placeholder="••••••••" autocomplete="off" required />
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password', this)" title="Lihat/Sembunyikan Password">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon-eye" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mt-1 d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-login-modern w-100 d-flex align-items-center justify-content-center gap-2" id="btn-login">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    <span>Masuk ke Akun</span>
                </button>
            </div>
        </form>

        <!-- Security Badge Footer -->
        <div class="text-center mt-4">
            <span class="badge-secure">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13l4 4l10 -10" /></svg>
                Enkripsi TLS 256-bit Terproteksi
            </span>
        </div>
    </div>

    {{-- MODAL MULTI-STEP RESET PASSWORD OTP WHATSAPP --}}
    <div class="modal modal-blur fade" id="modal-forgot-password" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 0 1 6.36 15.36l-1.36 1.36a9 9 0 0 1 -12.72 0l-1.36 -1.36a9 9 0 0 1 6.36 -15.36z" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
                        Reset Password via WhatsApp
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Alert Box Notifikasi Modal --}}
                    <div id="modal-alert" class="alert d-none mb-3" role="alert">
                        <span id="modal-alert-text"></span>
                    </div>

                    {{-- STEP 1: INPUT EMAIL UNTUK CARI AKUN --}}
                    <div id="step-1-container">
                        <div class="text-center mb-4">
                            <div class="avatar avatar-lg bg-primary-lt text-primary rounded-circle mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Cari Akun Anda</h4>
                            <p class="text-muted small mb-0">Masukkan alamat email terdaftar. Kode OTP verifikasi akan dikirimkan ke WhatsApp Anda.</p>
                        </div>

                        <form id="form-step-1" onsubmit="handleSendOtp(event)">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Alamat Email Terdaftar <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
                                    </span>
                                    <input type="email" id="reset-email" class="form-control form-control-lg" placeholder="contoh@cio.co.id" required autofocus />
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="btn-send-otp">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
                                Kirim Kode OTP WhatsApp
                            </button>
                        </form>
                    </div>

                    {{-- STEP 2: VERIFIKASI KODE OTP --}}
                    <div id="step-2-container" class="d-none">
                        <div class="text-center mb-4">
                            <div class="avatar avatar-lg bg-success-lt text-success rounded-circle mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Verifikasi Kode OTP</h4>
                            <p class="text-muted small mb-0">
                                Kode 6 digit telah dikirim ke WhatsApp <strong class="text-dark" id="display-masked-phone">-</strong>
                            </p>
                        </div>

                        <form id="form-step-2" onsubmit="handleVerifyOtp(event)">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-center d-block">Masukkan 6 Digit Kode OTP</label>
                                <input type="text" id="reset-otp" class="form-control form-control-lg otp-input-field" maxlength="6" pattern="[0-9]{6}" placeholder="••••••" required autocomplete="off" />
                                <div class="form-text text-center text-muted small mt-1">Kode OTP berlaku selama 10 menit</div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold mb-3 d-flex align-items-center justify-content-center gap-2" id="btn-verify-otp">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Verifikasi Kode OTP
                            </button>

                            <div class="text-center">
                                <button type="button" class="btn btn-link text-muted small text-decoration-none" id="btn-resend-otp" onclick="resendOtp()">
                                    Tidak menerima kode? <span class="text-primary fw-semibold">Kirim Ulang OTP</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- STEP 3: FORM INPUT PASSWORD BARU --}}
                    <div id="step-3-container" class="d-none">
                        <div class="text-center mb-4">
                            <div class="avatar avatar-lg bg-azure-lt text-azure rounded-circle mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></svg>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Buat Password Baru</h4>
                            <p class="text-muted small mb-0">Silakan tentukan password baru yang aman untuk akun Anda.</p>
                        </div>

                        <form id="form-step-3" onsubmit="handleResetPassword(event)">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group input-group-flat">
                                    <input type="password" id="new-password" class="form-control form-control-lg" minlength="8" placeholder="Minimal 8 karakter" required />
                                    <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('new-password', this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-eye" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group input-group-flat">
                                    <input type="password" id="new-password-confirmation" class="form-control form-control-lg" minlength="8" placeholder="Ketik ulang password baru" required />
                                    <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('new-password-confirmation', this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-eye" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="btn-save-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Simpan Password Baru
                            </button>
                        </form>
                    </div>

                    {{-- STEP 4: SUKSES --}}
                    <div id="step-4-container" class="d-none text-center py-3">
                        <div class="avatar avatar-xl bg-success text-white rounded-circle mb-3 shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Password Berhasil Diubah!</h3>
                        <p class="text-muted small mb-4">Password akun Anda telah berhasil diperbarui. Silakan login kembali dengan password baru Anda.</p>
                        <button type="button" class="btn btn-primary px-4 py-2 fw-semibold" data-bs-dismiss="modal">
                            Masuk ke Halaman Login
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    let resetEmailState = '';
    let resetTokenState = '';

    function togglePasswordVisibility(fieldId, element) {
        const input = document.getElementById(fieldId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            element.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-eye-off" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" /><path d="M3 3l18 18" /></svg>';
        } else {
            input.type = 'password';
            element.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-eye" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>';
        }
    }

    function showModalAlert(type, message) {
        const alertBox = document.getElementById('modal-alert');
        const alertText = document.getElementById('modal-alert-text');
        alertBox.className = `alert alert-${type} mb-3`;
        alertText.innerText = message;
        alertBox.classList.remove('d-none');
    }

    function hideModalAlert() {
        const alertBox = document.getElementById('modal-alert');
        alertBox.classList.add('d-none');
    }

    function openForgotPasswordModal() {
        hideModalAlert();
        resetEmailState = '';
        resetTokenState = '';

        document.getElementById('step-1-container').classList.remove('d-none');
        document.getElementById('step-2-container').classList.add('d-none');
        document.getElementById('step-3-container').classList.add('d-none');
        document.getElementById('step-4-container').classList.add('d-none');

        document.getElementById('reset-email').value = '';
        document.getElementById('reset-otp').value = '';
        document.getElementById('new-password').value = '';
        document.getElementById('new-password-confirmation').value = '';

        let modal = new bootstrap.Modal(document.getElementById('modal-forgot-password'));
        modal.show();
    }

    // Step 1: Kirim OTP
    async function handleSendOtp(e) {
        e.preventDefault();
        hideModalAlert();

        const email = document.getElementById('reset-email').value.trim();
        const btn = document.getElementById('btn-send-otp');

        if (!email) return;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mencari akun & mengirim OTP...';

        try {
            const response = await fetch('{{ route("password.sendOtp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            });

            const data = await response.json();

            if (response.ok && data.status) {
                resetEmailState = email;
                document.getElementById('display-masked-phone').innerText = data.masked_phone || 'WhatsApp';

                document.getElementById('step-1-container').classList.add('d-none');
                document.getElementById('step-2-container').classList.remove('d-none');
                document.getElementById('reset-otp').focus();

                showModalAlert('success', data.message);
            } else {
                showModalAlert('danger', data.message || 'Gagal mengirim OTP.');
            }
        } catch (err) {
            showModalAlert('danger', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg> Kirim Kode OTP WhatsApp';
        }
    }

    // Step 2: Verifikasi OTP
    async function handleVerifyOtp(e) {
        e.preventDefault();
        hideModalAlert();

        const otp = document.getElementById('reset-otp').value.trim();
        const btn = document.getElementById('btn-verify-otp');

        if (!otp || otp.length !== 6) {
            showModalAlert('warning', 'Masukkan 6 digit angka kode OTP.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi kode...';

        try {
            const response = await fetch('{{ route("password.verifyOtp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: resetEmailState,
                    otp: otp
                })
            });

            const data = await response.json();

            if (response.ok && data.status) {
                resetTokenState = data.reset_token;

                document.getElementById('step-2-container').classList.add('d-none');
                document.getElementById('step-3-container').classList.remove('d-none');
                document.getElementById('new-password').focus();

                showModalAlert('success', data.message);
            } else {
                showModalAlert('danger', data.message || 'Kode OTP tidak valid.');
            }
        } catch (err) {
            showModalAlert('danger', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> Verifikasi Kode OTP';
        }
    }

    // Resend OTP
    async function resendOtp() {
        if (!resetEmailState) return;
        document.getElementById('reset-email').value = resetEmailState;
        handleSendOtp(new Event('submit'));
    }

    // Step 3: Simpan Password Baru
    async function handleResetPassword(e) {
        e.preventDefault();
        hideModalAlert();

        const password = document.getElementById('new-password').value;
        const passwordConfirmation = document.getElementById('new-password-confirmation').value;
        const btn = document.getElementById('btn-save-password');

        if (password.length < 8) {
            showModalAlert('warning', 'Password minimal 8 karakter.');
            return;
        }

        if (password !== passwordConfirmation) {
            showModalAlert('warning', 'Konfirmasi password baru tidak cocok.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan password baru...';

        try {
            const response = await fetch('{{ route("password.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: resetEmailState,
                    reset_token: resetTokenState,
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            });

            const data = await response.json();

            if (response.ok && data.status) {
                document.getElementById('step-3-container').classList.add('d-none');
                document.getElementById('step-4-container').classList.remove('d-none');
            } else {
                showModalAlert('danger', data.message || 'Gagal mereset password.');
            }
        } catch (err) {
            showModalAlert('danger', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> Simpan Password Baru';
        }
    }
</script>
@endpush