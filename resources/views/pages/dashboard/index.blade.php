@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@push('css')
<style>
    .welcome-hero {
        background: linear-gradient(135deg, #0b132b 0%, #1e3a8a 50%, #2563eb 100%);
        border-radius: 16px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.3);
    }
    .welcome-hero::before {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    .welcome-hero::after {
        content: '';
        position: absolute;
        right: 80px;
        bottom: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 50%;
    }
    .finance-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0284c7 100%);
        border-radius: 16px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.35);
    }
    .stat-card-modern {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .stat-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.08);
    }
    .quick-action-box {
        transition: all 0.25s ease;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        text-decoration: none !important;
        color: #1e293b;
        border-radius: 12px;
    }
    .quick-action-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.12);
        border-color: #3b82f6;
        color: #2563eb;
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(46, 164, 79, 0.6); }
        70% { box-shadow: 0 0 0 8px rgba(46, 164, 79, 0); }
        100% { box-shadow: 0 0 0 0 rgba(46, 164, 79, 0); }
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #2ea44f;
        border-radius: 50%;
        display: inline-block;
        animation: pulse-green 2s infinite;
    }
    .balance-card-item {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 12px;
        transition: all 0.25s ease;
        backdrop-filter: blur(6px);
    }
    .balance-card-item:hover {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.32);
        transform: translateY(-2px);
    }
    .balance-card-highlight {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.06) 100%);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        transition: all 0.25s ease;
    }
    .balance-card-highlight:hover {
        border-color: rgba(255, 255, 255, 0.45);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
    <!-- Welcome Greeting Header Banner -->
    <div class="welcome-hero p-4 p-md-5 mb-4 position-relative">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white-lt text-white px-3 py-1 fw-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="badge bg-white-lt text-white px-3 py-1">
                        Sistem Keuangan CIO
                    </span>
                </div>
                <h1 class="display-6 fw-bold mb-2 text-white">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-white-50 mb-0 fs-3">
                    Dashboard Manajemen Keuangan, Penggajian, dan Operasional Karyawan.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-inline-flex align-items-center bg-white-lt p-2 px-3 rounded-pill">
                    <span class="avatar avatar-sm rounded-circle bg-primary text-white me-2 fw-bold">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </span>
                    <span class="text-white small fw-medium">
                        Role: <strong>{{ Auth::user()->roles->pluck('name')->first() ?? 'Staff' }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- SISI KARYAWAN (SLIP GAJI & RIWAYAT PRIBADI) --}}
    @can('slip gaji karyawan')
        @if (isset($isAbsenOn) && $isAbsenOn == true)
            @include('pages.dashboard.partials.dashboard-on-absen')
        @else
            @include('pages.dashboard.partials.dashboard-off-absen')
        @endif
    @endcan

    {{-- SISI ADMIN (RINGKASAN EKSEKUTIF, SALDO FINANCE, KASBON & TRANSFER GAJI) --}}
    @role("Admin")
        <!-- Admin Dashboard Section: Realtime 2 Saldo Website Finance (Saldo Manual & Saldo Xendit) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="finance-hero-card p-4">
                    <div class="position-relative" style="z-index: 2;">
                        <!-- Header Status Bar -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-3 border-bottom" style="border-color: rgba(255,255,255,0.15) !important;">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-white-lt text-white px-3 py-1 d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 10l18 0" /><path d="M5 6l7 -3l7 3" /><path d="M4 10l0 11" /><path d="M20 10l0 11" /><path d="M8 14l0 3" /><path d="M12 14l0 3" /><path d="M16 14l0 3" /></svg>
                                    Integrasi Saldo Web (Finance API)
                                </span>
                                @if(isset($financeBalance) && $financeBalance['success'])
                                    <span class="badge bg-success-lt text-success px-3 py-1 d-inline-flex align-items-center gap-2">
                                        <span class="pulse-dot"></span>
                                        Terhubung Realtime
                                    </span>
                                @else
                                    <span class="badge bg-warning-lt text-warning px-3 py-1">
                                        ⚠ Konfigurasi Diperlukan
                                    </span>
                                @endif
                            </div>
                            <div class="text-white-50 small">
                                @if(isset($financeBalance) && $financeBalance['success'])
                                    Client: <strong class="text-white">{{ $financeBalance['data']['client_name'] ?? 'Web Slip' }}</strong>
                                    &nbsp;·&nbsp; Diperbarui: {{ \Carbon\Carbon::now()->translatedFormat('H:i:s') }} WIB
                                @else
                                    {{ $financeBalance['message'] ?? 'Konfigurasi API Finance belum aktif di .env' }}
                                @endif
                            </div>
                        </div>

                        <!-- 2 Saldo (Manual & Xendit) + Total Saldo Cards -->
                        <div class="row g-3 align-items-stretch">
                            <!-- 1. Saldo Manual -->
                            <div class="col-md-4">
                                <div class="balance-card-item p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded bg-primary-lt text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                                </span>
                                                <span class="text-white fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">
                                                    Saldo Manual
                                                </span>
                                            </div>
                                            @if(isset($financeBalance) && $financeBalance['success'] && !($financeBalance['channel_manual_enabled'] ?? true))
                                                <span class="badge bg-danger-lt text-danger" style="font-size: 0.7rem;">⚠️ Nonaktif</span>
                                            @else
                                                <span class="badge bg-primary-lt text-white" style="font-size: 0.7rem;">Kas / Bank</span>
                                            @endif
                                        </div>
                                        <div class="text-white fw-bold mb-1" style="font-size: 1.65rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                                            @if(isset($financeBalance) && $financeBalance['success'])
                                                Rp {{ number_format($financeBalance['balance_manual'] ?? ($financeBalance['data']['balance_manual'] ?? 0), 0, ',', '.') }}
                                            @else
                                                Rp —
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Saldo Xendit -->
                            <div class="col-md-4">
                                <div class="balance-card-item p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded bg-azure-lt text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0z" /></svg>
                                                </span>
                                                <span class="text-white fw-semibold small text-uppercase" style="letter-spacing: 0.05em;">
                                                    Saldo Xendit
                                                </span>
                                            </div>
                                            @if(isset($financeBalance) && $financeBalance['success'] && !($financeBalance['channel_xendit_enabled'] ?? true))
                                                <span class="badge bg-danger-lt text-danger" style="font-size: 0.7rem;">⚠️ Nonaktif</span>
                                            @else
                                                <span class="badge bg-azure-lt text-white" style="font-size: 0.7rem;">Disbursement</span>
                                            @endif
                                        </div>
                                        <div class="text-white fw-bold mb-1" style="font-size: 1.65rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                                            @if(isset($financeBalance) && $financeBalance['success'])
                                                Rp {{ number_format($financeBalance['balance_xendit'] ?? ($financeBalance['data']['balance_xendit'] ?? 0), 0, ',', '.') }}
                                            @else
                                                Rp —
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Total Saldo Gabungan -->
                            <div class="col-md-4">
                                <div class="balance-card-highlight p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs rounded bg-success-lt text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3v12" /><path d="M16 7l-4 -4l-4 4" /><path d="M3 13v4a4 4 0 0 0 4 4h10a4 4 0 0 0 4 -4v-4" /></svg>
                                                </span>
                                                <span class="text-white fw-bold small text-uppercase" style="letter-spacing: 0.05em;">
                                                    Total Saldo
                                                </span>
                                            </div>
                                            <span class="badge bg-success text-white" style="font-size: 0.7rem;">Akumulasi</span>
                                        </div>
                                        <div class="text-white fw-bold mb-1" style="font-size: 1.65rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                                            @if(isset($financeBalance) && $financeBalance['success'])
                                                Rp {{ number_format($financeBalance['total_balance'] ?? ($financeBalance['data']['total_balance'] ?? ($financeBalance['balance'] ?? 0)), 0, ',', '.') }}
                                            @else
                                                Rp —
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Ringkasan Kartu -->
        <div class="row row-cards g-3 mb-4">
            <!-- Jumlah Karyawan -->
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card-modern shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="avatar avatar-md rounded-3 bg-primary-lt text-primary shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                            </span>
                            <span class="badge bg-primary-lt text-primary fw-semibold">
                                Total Staff
                            </span>
                        </div>
                        <div class="text-muted small fw-medium text-uppercase">
                            Jumlah Karyawan
                        </div>
                        <div class="h2 mb-0 fw-bold text-dark mt-1">
                            {{ number_format($totalEmployees ?? 0, 0, ',', '.') }}
                            <span class="fs-4 text-muted fw-normal">Orang</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gaji Terbayar Bulan Ini -->
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card-modern shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="avatar avatar-md rounded-3 bg-azure-lt text-azure shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 14l2 2l4 -4" /></svg>
                            </span>
                            <span class="badge bg-azure-lt text-azure fw-semibold">
                                {{ \Carbon\Carbon::now()->translatedFormat('M Y') }}
                            </span>
                        </div>
                        <div class="text-muted small fw-medium text-uppercase">
                            Gaji Terbayar (Bulan Ini)
                        </div>
                        <div class="h3 mb-0 fw-bold text-azure mt-1">
                            Rp {{ number_format($totalSalaryPaidMonth ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">
                            {{ $salaryPaidCount ?? 0 }} Transaksi Berhasil
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kasbon Disetujui -->
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card-modern shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="avatar avatar-md rounded-3 bg-success-lt text-success shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                            </span>
                            <span class="badge bg-success-lt text-success fw-semibold">
                                Disetujui
                            </span>
                        </div>
                        <div class="text-muted small fw-medium text-uppercase">
                            Kasbon Disetujui
                        </div>
                        <div class="h3 mb-0 fw-bold text-success mt-1">
                            Rp {{ number_format($kasbonApprovedAmount ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">
                            {{ number_format($kasbonApprovedCount ?? 0, 0, ',', '.') }} Pengajuan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kasbon Menunggu (Pending) -->
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card-modern shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="avatar avatar-md rounded-3 bg-warning-lt text-warning shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                            </span>
                            <span class="badge bg-warning-lt text-warning fw-semibold">
                                Perlu Review
                            </span>
                        </div>
                        <div class="text-muted small fw-medium text-uppercase">
                            Kasbon Pending
                        </div>
                        <div class="h2 mb-0 fw-bold text-warning mt-1">
                            {{ number_format($kasbonPendingCount ?? 0, 0, ',', '.') }}
                            <span class="fs-5 text-muted fw-normal">Pengajuan</span>
                        </div>
                        <div class="small text-muted mt-1">
                            Menunggu verifikasi admin
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Transaksi Terbaru (Gaji & Kasbon) -->
        <div class="row row-cards g-3 mb-4">
            <!-- 5 Transaksi Pembayaran Gaji Terbaru -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h4 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12h6" /><path d="M9 16h6" /></svg>
                            Transfer Gaji Terbaru
                        </h4>
                        <a href="{{ route('salary.payment.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Karyawan</th>
                                    <th>Periode</th>
                                    <th>Gaji Bersih</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSalaryPayments ?? [] as $pay)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $pay->user->name ?? '-' }}</div>
                                            <div class="text-muted small">{{ $pay->bank_name }} - {{ $pay->account_number }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-azure-lt">{{ $pay->period_label }}</span>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            Rp {{ number_format($pay->net_salary, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if($pay->status === 'transferred')
                                                <span class="badge bg-success-lt">✓ Berhasil</span>
                                            @elseif($pay->status === 'pending')
                                                <span class="badge bg-warning-lt">⏳ Pending</span>
                                            @else
                                                <span class="badge bg-danger-lt">✗ Gagal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Belum ada transaksi pembayaran gaji.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5 Kasbon Terbaru -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h4 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-warning"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                            Pengajuan Kasbon Terbaru
                        </h4>
                        <a href="{{ route('cash.advance.approval') }}" class="btn btn-sm btn-outline-warning">
                            Kelola Approval
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Karyawan</th>
                                    <th>Keperluan</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCashAdvances ?? [] as $ca)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $ca->user->name ?? '-' }}</div>
                                            <div class="text-muted small">{{ \Carbon\Carbon::parse($ca->request_date)->translatedFormat('d M Y') }}</div>
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 140px;" title="{{ $ca->title }}">{{ $ca->title }}</span>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            Rp {{ number_format($ca->amount, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if($ca->status === 'approved' || $ca->status === 'transferred')
                                                <span class="badge bg-success-lt">Disetujui</span>
                                            @elseif($ca->status === 'pending')
                                                <span class="badge bg-warning-lt">Pending</span>
                                            @else
                                                <span class="badge bg-danger-lt">Ditolak</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Belum ada data pengajuan kasbon.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Navigation Grid -->
        <div class="mt-4 mb-3">
            <h3 class="h3 fw-bold text-dark mb-0">Akses Menu Cepat</h3>
            <p class="text-muted small mb-0">Pintasan navigasi untuk operasional sehari-hari</p>
        </div>

        <div class="row g-3">
            @can('lihat gaji karyawan')
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('salary.index') }}" class="card quick-action-box p-3 h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-primary-lt text-primary rounded-3 me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" /><path d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" /><path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /></svg>
                            </span>
                            <div>
                                <h4 class="mb-0 fw-semibold">Kelola Gaji</h4>
                                <span class="small text-muted">Data Gaji Pokok</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            @can('lihat gaji karyawan')
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('salary.payment.index') }}" class="card quick-action-box p-3 h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-azure-lt text-azure rounded-3 me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12h6" /><path d="M9 16h6" /></svg>
                            </span>
                            <div>
                                <h4 class="mb-0 fw-semibold">Riwayat Transfer</h4>
                                <span class="small text-muted">Tracking Xendit</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            @can('approve kasbon')
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('cash.advance.approval') }}" class="card quick-action-box p-3 h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-success-lt text-success rounded-3 me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                            </span>
                            <div>
                                <h4 class="mb-0 fw-semibold">Approve Kasbon</h4>
                                <span class="small text-muted">Persetujuan Pengajuan</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            @can('rekap absensi')
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('absen.list.rekap') }}" class="card quick-action-box p-3 h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-warning-lt text-warning rounded-3 me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 14l2 2l4 -4" /></svg>
                            </span>
                            <div>
                                <h4 class="mb-0 fw-semibold">Rekap Absensi</h4>
                                <span class="small text-muted">Laporan Kehadiran</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            @can('lihat pengeluaran')
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('expenditure.index') }}" class="card quick-action-box p-3 h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-danger-lt text-danger rounded-3 me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" /><path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" /><path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" /></svg>
                            </span>
                            <div>
                                <h4 class="mb-0 fw-semibold">Pengeluaran</h4>
                                <span class="small text-muted">Buku Kas Keluar</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan
        </div>
    @endrole
@endsection

@push('js')
@endpush