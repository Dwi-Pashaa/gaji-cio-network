@extends('layouts.app')

@section('title')
    Riwayat Pembayaran Gaji
@endsection

@push('css')
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .badge-status-transferred {
        background-color: rgba(46, 164, 79, 0.12);
        color: #2ea44f;
        border: 1px solid rgba(46, 164, 79, 0.3);
    }
    .badge-status-pending {
        background-color: rgba(245, 158, 11, 0.12);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .badge-status-failed {
        background-color: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .avatar-initial {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')

    {{-- Summary Cards --}}
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card card-sm shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary-lt text-primary avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 14l2 2l4 -4" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted small">Total Transaksi</div>
                            <div class="text-dark fw-bold fs-3">{{ $payments->total() }} <span class="fs-6 fw-normal text-muted">Kali</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card card-sm shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success-lt text-success avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted small">Total Berhasil Transfer</div>
                            <div class="text-success fw-bold fs-4">Rp {{ number_format($totalTransferred, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card card-sm shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning-lt text-warning avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted small">Sedang Diproses</div>
                            <div class="text-warning fw-bold fs-3">{{ $totalPending }} <span class="fs-6 fw-normal text-muted">Pending</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card card-sm shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-danger-lt text-danger avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted small">Transfer Gagal (Refund)</div>
                            <div class="text-danger fw-bold fs-3">{{ $totalFailed }} <span class="fs-6 fw-normal text-muted">Gagal</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card shadow-sm border-0">
        {{-- Filter Header --}}
        <div class="card-body border-bottom py-3 bg-light-lt">
            <form method="GET" action="{{ route('salary.payment.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama karyawan...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <select name="month" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}" {{ request('month') == $i+1 ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 col-6">
                    <select name="year" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach(range(date('Y'), date('Y') - 3) as $yr)
                            <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Berhasil</option>
                        <option value="pending"     {{ request('status') == 'pending'     ? 'selected' : '' }}>Pending</option>
                        <option value="failed"      {{ request('status') == 'failed'      ? 'selected' : '' }}>Gagal / Refund</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-12 col-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.828 4.828a2 2 0 0 0 -.586 1.414v4.172l-4 2v-6.172a2 2 0 0 0 -.586 -1.414l-4.828 -4.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'month', 'year', 'status']))
                        <a href="{{ route('salary.payment.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Content --}}
        <div class="table-responsive">
            <table class="table card-table table-vcenter table-hover text-nowrap">
                <thead>
                    <tr class="bg-light text-muted">
                        <th class="w-1">No</th>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Potongan Kasbon</th>
                        <th>Gaji Bersih (Ditransfer)</th>
                        <th>Rekening Tujuan</th>
                        <th>Status Transfer</th>
                        <th>Waktu Transaksi</th>
                        <th class="text-center w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $index => $payment)
                        @php
                            $user = $payment->user;
                            $initials = strtoupper(substr($user->name ?? 'U', 0, 2));
                        @endphp
                        <tr>
                            <td class="text-muted text-center">{{ $payments->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-initial bg-blue-lt text-primary">
                                        {{ $initials }}
                                    </span>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name ?? '-' }}</div>
                                        <div class="text-muted small">{{ $user->phone ?? ($user->email ?? '-') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-azure-lt px-2 py-1 fs-7 fw-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                                    {{ $payment->period_label }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">Rp {{ number_format($payment->base_salary, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @if($payment->total_allowance > 0)
                                    <span class="text-success fw-medium">+Rp {{ number_format($payment->total_allowance, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->total_cash_advance > 0)
                                    <span class="text-danger fw-medium">-Rp {{ number_format($payment->total_cash_advance, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="badge bg-green-lt px-2 py-1 fs-6 fw-bold text-success">
                                    Rp {{ number_format($payment->net_salary, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-dark-lt text-dark fw-semibold">{{ $payment->bank_name ?? '-' }}</span>
                                    <span class="fw-medium text-dark">{{ $payment->account_number ?? '-' }}</span>
                                </div>
                                <div class="text-muted small">a/n {{ $payment->account_holder_name ?? '-' }}</div>
                            </td>
                            <td>
                                @if ($payment->status === 'transferred')
                                    <span class="badge badge-status-transferred px-2 py-1 d-inline-flex align-items-center gap-1 fw-bold">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                        Berhasil Ditransfer
                                    </span>
                                @elseif ($payment->status === 'pending')
                                    <span class="badge badge-status-pending px-2 py-1 d-inline-flex align-items-center gap-1 fw-bold">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Pending (Proses)
                                    </span>
                                @else
                                    <span class="badge badge-status-failed px-2 py-1 d-inline-flex align-items-center gap-1 fw-bold">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                                        Gagal (Saldo Direfund)
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                @if($payment->transfer_at)
                                    <div class="fw-medium text-dark">{{ $payment->transfer_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted small">{{ $payment->transfer_at->format('H:i') }} WIB</div>
                                @else
                                    <div class="fw-medium text-dark">{{ $payment->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted small">{{ $payment->created_at->format('H:i') }} WIB</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" onclick="showDetailModal({{ json_encode($payment) }}, {{ json_encode($user) }}, '{{ $payment->transferredBy->name ?? '-' }}')" title="Lihat Rincian">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon text-muted mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>
                                    </div>
                                    <p class="empty-title fw-bold text-dark fs-4">Belum Ada Riwayat Pembayaran Gaji</p>
                                    <p class="empty-subtitle text-muted">
                                        Setiap kali Anda menekan tombol "Transfer Gaji" pada menu Gaji Karyawan, riwayat dan status transfernya akan tercatat di sini.
                                    </p>
                                    <div class="empty-action mt-3">
                                        <a href="{{ route('salary.index') }}" class="btn btn-primary">
                                            Buka Halaman Gaji Karyawan
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($payments->hasPages())
            <div class="card-footer d-flex align-items-center justify-content-between py-2">
                <p class="m-0 text-muted small">
                    Menampilkan <strong>{{ $payments->firstItem() }}</strong> sampai <strong>{{ $payments->lastItem() }}</strong> dari total <strong>{{ $payments->total() }}</strong> transaksi
                </p>
                {{ $payments->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

{{-- Modal Detail Pembayaran Gaji --}}
<div class="modal modal-blur fade" id="modal-payment-detail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title d-flex align-items-center gap-2 fw-bold text-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 15l2 2l4 -4" /></svg>
                    Rincian Pembayaran Gaji
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light-lt border-0 p-3 rounded-3">
                            <div class="text-muted small mb-1">Nama Karyawan</div>
                            <div class="fw-bold fs-4 text-dark" id="modal-emp-name">-</div>
                            <div class="text-muted small" id="modal-emp-phone">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light-lt border-0 p-3 rounded-3">
                            <div class="text-muted small mb-1">Periode Pembayaran</div>
                            <div class="fw-bold fs-4 text-primary" id="modal-period">-</div>
                            <div class="text-muted small" id="modal-transferred-by">Diproses oleh: -</div>
                        </div>
                    </div>
                </div>

                <div class="card border rounded-3 p-3 mb-3">
                    <h4 class="card-title text-dark mb-3">Rincian Perhitungan Gaji:</h4>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Gaji Pokok:</span>
                        <span class="fw-bold text-dark" id="modal-base-salary">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Total Tunjangan:</span>
                        <span class="fw-bold text-success" id="modal-allowance">+Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Potongan Kasbon:</span>
                        <span class="fw-bold text-danger" id="modal-cash-advance">-Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2">
                        <span class="fw-bold fs-4 text-dark">Total Gaji Bersih:</span>
                        <span class="fw-bold fs-4 text-primary" id="modal-net-salary">Rp 0</span>
                    </div>
                </div>

                <div class="card border rounded-3 p-3 mb-3 bg-light-lt">
                    <h4 class="card-title text-dark mb-2">Informasi Rekening Tujuan & Xendit:</h4>
                    <div class="row g-2">
                        <div class="col-6">
                            <span class="text-muted small d-block">Bank & Nomor Rekening:</span>
                            <span class="fw-bold text-dark" id="modal-bank-info">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Nama Pemilik Rekening:</span>
                            <span class="fw-bold text-dark" id="modal-holder-name">-</span>
                        </div>
                        <div class="col-6 mt-2">
                            <span class="text-muted small d-block">Status Transfer:</span>
                            <span id="modal-status-badge">-</span>
                        </div>
                        <div class="col-6 mt-2">
                            <span class="text-muted small d-block">External ID (Xendit):</span>
                            <code class="small text-muted" id="modal-external-id">-</code>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }

    function showDetailModal(payment, user, transferredByName) {
        document.getElementById('modal-emp-name').innerText = user ? user.name : '-';
        document.getElementById('modal-emp-phone').innerText = (user && user.phone) ? user.phone : ((user && user.email) ? user.email : '-');
        document.getElementById('modal-period').innerText = payment.period_label || (payment.period_month + '/' + payment.period_year);
        document.getElementById('modal-transferred-by').innerText = 'Diproses oleh: ' + (transferredByName || '-');

        document.getElementById('modal-base-salary').innerText = formatRupiah(payment.base_salary);
        document.getElementById('modal-allowance').innerText = '+ ' + formatRupiah(payment.total_allowance);
        document.getElementById('modal-cash-advance').innerText = '- ' + formatRupiah(payment.total_cash_advance);
        document.getElementById('modal-net-salary').innerText = formatRupiah(payment.net_salary);

        document.getElementById('modal-bank-info').innerText = (payment.bank_name || '-') + ' - ' + (payment.account_number || '-');
        document.getElementById('modal-holder-name').innerText = payment.account_holder_name || '-';
        document.getElementById('modal-external-id').innerText = payment.xendit_external_id || '-';

        let badgeHtml = '';
        if (payment.status === 'transferred') {
            badgeHtml = '<span class="badge badge-status-transferred px-2 py-1 fw-bold">✓ Berhasil Ditransfer</span>';
        } else if (payment.status === 'pending') {
            badgeHtml = '<span class="badge badge-status-pending px-2 py-1 fw-bold">⏳ Pending (Diproses)</span>';
        } else {
            badgeHtml = '<span class="badge badge-status-failed px-2 py-1 fw-bold">✗ Gagal (Saldo Direfund)</span>';
        }
        document.getElementById('modal-status-badge').innerHTML = badgeHtml;

        let modal = new bootstrap.Modal(document.getElementById('modal-payment-detail'));
        modal.show();
    }
</script>
@endpush
