@extends('layouts.app')

@section('title')
    Gaji Karyawan
@endsection

@push('css')
<style>
    .salary-table th {
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-weight: 700 !important;
        color: #475569 !important;
        background-color: #f8fafc !important;
        padding: 0.9rem 1rem !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .salary-table td {
        padding: 0.95rem 1rem !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .salary-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .emp-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        background: linear-gradient(135deg, rgba(26, 86, 219, 0.12) 0%, rgba(26, 86, 219, 0.22) 100%);
        color: #1a56db;
        border: 1px solid rgba(26, 86, 219, 0.2);
    }
    .badge-allowance {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin: 2px;
    }
    .badge-allowance-val {
        color: #16a34a;
        font-weight: 600;
    }
    .total-salary-box {
        display: inline-block;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.925rem;
    }
    .btn-action-custom {
        padding: 0.4rem 0.65rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }
    .btn-action-custom:hover {
        transform: translateY(-1px);
    }
    /* Wizard Stepper Styles */
    .wizard-stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 0 10px;
    }
    .wizard-stepper::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 50px;
        right: 50px;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
    }
    .wizard-step-item {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
        cursor: pointer;
    }
    .wizard-step-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #ffffff;
        color: #94a3b8;
        border: 2px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.25s ease;
    }
    .wizard-step-item.active .wizard-step-circle {
        background: #1a56db;
        color: #ffffff;
        border-color: #1a56db;
        box-shadow: 0 0 0 4px rgba(26, 86, 219, 0.18);
    }
    .wizard-step-item.completed .wizard-step-circle {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
    }
    .wizard-step-label {
        font-size: 0.775rem;
        font-weight: 600;
        color: #64748b;
        margin-top: 6px;
        transition: all 0.25s ease;
    }
    .wizard-step-item.active .wizard-step-label {
        color: #1a56db;
        font-weight: 700;
    }
    .wizard-step-item.completed .wizard-step-label {
        color: #16a34a;
    }
    .mode-tab-btn {
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .mode-tab-btn.active {
        background: #1a56db;
        color: #fff;
        box-shadow: 0 2px 4px rgba(26, 86, 219, 0.25);
    }
    .review-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
    }
    .tf-method-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.25s ease;
        background: #ffffff;
        position: relative;
    }
    .tf-method-card:hover {
        border-color: #3b82f6;
        background-color: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }
    .tf-method-card.selected {
        border-color: #2563eb;
        background: linear-gradient(145deg, #eff6ff 0%, #ffffff 100%);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.15);
    }
    .tf-method-card.is-insufficient {
        border-color: #fecaca;
        background: #fffafa;
    }
    .tf-method-card.selected.is-insufficient {
        border-color: #ef4444;
        background: linear-gradient(145deg, #fef2f2 0%, #ffffff 100%);
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.15);
    }
    .tf-method-card.disabled {
        opacity: 0.5;
        cursor: not-allowed !important;
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
        transform: none !important;
        pointer-events: none;
    }
    .tf-method-card.disabled * {
        cursor: not-allowed !important;
    }
</style>
@endpush

@section('content')
    <div class="card shadow-sm border-0">
        @can('tambah gaji karyawan')
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <div>
                    <h3 class="card-title fw-bold text-dark mb-0">Kelola Gaji Karyawan</h3>
                    <p class="text-muted small mb-0">Daftar konfigurasi gaji pokok, tunjangan, dan transfer gaji langsung</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('salary.payment.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12h6" /><path d="M9 16h6" /></svg>
                        Riwayat Pembayaran
                    </a>
                    <a href="javascript:void(0)" id="addBtn" data-bs-toggle="modal" data-bs-target="#modal-simple" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Buat Gaji Pegawai
                    </a>
                </div>
            </div>
        @endcan

        <div class="card-body border-bottom py-3 bg-light-lt">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Tampilkan:</span>
                    <select name="sort" id="sort" class="form-select form-select-sm" style="width: 80px;">
                        @php
                            $opts = [10, 25, 50, 100];
                        @endphp 
                        @foreach ($opts as $opt)
                            <option value="{{ $opt }}" {{ request('sort') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small">entri</span>
                </div>
                <div class="ms-auto" style="min-width: 260px;">
                    <form method="GET" action="{{ route('salary.index') }}">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </span>
                            <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan...">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table card-table table-vcenter salary-table text-nowrap">
                <thead>
                    <tr>
                        <th class="w-1 text-center">No</th>
                        <th>Karyawan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Total Gaji Tetap</th>
                        <th class="text-center">Aksi & Transfer</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = $salary->firstItem() ?? 1;
                        $grandTotal = 0;
                    @endphp
                    @forelse ($salary as $item)
                        @php
                            $empUser = $item->user;
                            $userName = $empUser ? $empUser->name : 'User Tidak Ditemukan';
                            $userPhone = $empUser ? ($empUser->phone ?? ($empUser->email ?? '-')) : '-';
                            $initials = strtoupper(substr($userName, 0, 2));
                            $totalAllw = ($empUser && isset($empUser->allowance)) ? $empUser->allowance->sum('amount') : 0;
                            $totalGaji = ($item->base_salary ?? 0) + $totalAllw;
                            $grandTotal += $totalGaji;
                            $transferState = $item->getTransferState();
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-medium">{{ $no++ }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="emp-avatar">
                                        {{ $initials }}
                                    </span>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $userName }}</div>
                                        <div class="text-muted small">{{ $userPhone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    Rp {{ number_format($item->base_salary ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="text-muted small">
                                    Status: <span class="badge {{ ($item->status ?? '') == 'active' ? 'bg-success-lt text-success' : 'bg-secondary-lt text-secondary' }}">{{ ucfirst($item->status ?? '-') }}</span>
                                </div>
                                @if($item->effective_date)
                                    <div class="text-muted" style="font-size: 11px;">
                                        Gajian Tgl: <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($item->effective_date)->format('d') }}</span> / bln
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($empUser && isset($empUser->allowance) && $empUser->allowance->count() > 0)
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 320px;">
                                        @foreach ($empUser->allowance as $alw)
                                            <span class="badge-allowance">
                                                <span>{{ $alw->name }}:</span>
                                                <span class="badge-allowance-val">+Rp {{ number_format($alw->amount, 0, ',', '.') }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Tidak memiliki tunjangan</span>
                                @endif
                            </td>
                            <td>
                                <div class="total-salary-box">
                                    Rp {{ number_format($totalGaji, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap justify-content-center align-items-center">
                                    {{-- Logika Tombol Transfer Gaji Berdasarkan Tanggal Aktif & Status --}}
                                    @if ($transferState['status'] === 'can_transfer')
                                        <a href="javascript:void(0)" onclick="return openTransferModal('{{ $item->id }}')" class="btn btn-action-custom btn-success shadow-sm" title="Kalkulasi & Transfer Gaji via Xendit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M18 12l.01 0" /><path d="M6 12l.01 0" /></svg>
                                            Transfer Gaji
                                        </a>
                                    @elseif ($transferState['status'] === 'failed')
                                        <a href="javascript:void(0)" onclick="return openTransferModal('{{ $item->id }}')" class="btn btn-action-custom btn-danger shadow-sm" title="Transfer Sebelumnya Gagal, Klik untuk Coba Lagi">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                                            Transfer Ulang
                                        </a>
                                    @elseif ($transferState['status'] === 'already_transferred')
                                        @if ($transferState['payment'])
                                            <a href="{{ route('salary.payment.invoice', $transferState['payment']->id) }}" target="_blank" class="btn btn-action-custom btn-outline-success" title="Lihat Bukti Transfer Bulan Ini">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                Sudah Ditransfer
                                            </a>
                                        @else
                                            <span class="badge bg-success-lt text-success py-2 px-2 d-inline-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                Sudah Ditransfer
                                            </span>
                                        @endif
                                    @elseif ($transferState['status'] === 'pending')
                                        <span class="badge bg-warning-lt text-warning py-2 px-2 d-inline-flex align-items-center gap-1" title="Sedang diproses oleh sistem / Xendit">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                            Diproses Xendit
                                        </span>
                                    @elseif ($transferState['status'] === 'not_due')
                                        <span class="badge bg-secondary-lt text-secondary py-2 px-2 d-inline-flex align-items-center gap-1" title="Tombol transfer akan aktif setiap tanggal {{ $transferState['pay_day'] ?? 1 }} bulannya">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1v4" /></svg>
                                            Jadwal: Tgl {{ $transferState['pay_day'] ?? 1 }}
                                        </span>
                                    @elseif ($transferState['status'] === 'inactive')
                                        <span class="badge bg-secondary-lt text-secondary py-2 px-2 d-inline-flex align-items-center gap-1" title="Status gaji karyawan nonaktif. Tidak dapat melakukan transfer gaji.">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M5.7 5.7l12.6 12.6" /></svg>
                                            Gaji Nonaktif
                                        </span>
                                    @endif

                                    @can('edit gaji karyawan')
                                        <a href="javascript:void(0)" onclick="return editModal('{{ $item->id }}')" class="btn btn-action-custom btn-outline-warning" title="Edit Gaji & Tunjangan">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                            Edit
                                        </a>
                                    @endcan
                                    @can('hapus gaji karyawan')
                                        <a href="javascript:void(0)" onclick="return deleteItem('{{ $item->id }}')" class="btn btn-action-custom btn-outline-danger" title="Hapus Data Gaji">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                            Hapus
                                        </a>
                                    @endcan
                                </div>
                            </td> 
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-5" colspan="6">
                                <div class="text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-muted"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>
                                    <div class="fw-semibold">Belum Ada Data Gaji Karyawan</div>
                                    <div class="small">Klik tombol "Buat Gaji" di atas untuk menambahkan data baru.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-light-lt fw-bold">
                        <td colspan="4" class="text-end text-uppercase small text-muted">Total Keseluruhan (Halaman Ini):</td>
                        <td colspan="2" class="text-success fs-4">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between py-2">
            <p class="m-0 text-muted small">
                Menampilkan <strong>{{ $salary->firstItem() ?? 0 }}</strong> sampai <strong>{{ $salary->lastItem() ?? 0 }}</strong> dari total <strong>{{ $salary->total() }}</strong> entri
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $salary->withQueryString()->links('pagination::bootstrap-5') }}
            </ul>
        </div>
    </div>
@endsection

@push('modal')
    <!-- Modal Buat / Edit Gaji & Karyawan (Wizard Step) -->
    <div class="modal modal-blur fade" id="modal-simple" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white border-bottom py-3">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Buat Karyawan & Gaji Pegawai</h5>
                        <p class="text-muted small mb-0" id="wizard-subtitle">Lengkapi formulir 3 langkah untuk mendaftarkan karyawan & konfigurasi gajinya</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="form">
                    <input type="hidden" name="type" id="type" value="create">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="employee_mode" id="employee_mode" value="new">

                    <div class="modal-body p-4">
                        <!-- Stepper Indicator Bar -->
                        <div class="wizard-stepper mb-4" id="wizard-stepper-bar">
                            <div class="wizard-step-item active" data-step="1" onclick="goToStep(1)">
                                <div class="wizard-step-circle">1</div>
                                <div class="wizard-step-label">Data Karyawan</div>
                            </div>
                            <div class="wizard-step-item" data-step="2" onclick="goToStep(2)">
                                <div class="wizard-step-circle">2</div>
                                <div class="wizard-step-label">Konfigurasi Gaji</div>
                            </div>
                            <div class="wizard-step-item" data-step="3" onclick="goToStep(3)">
                                <div class="wizard-step-circle">3</div>
                                <div class="wizard-step-label">Review & Simpan</div>
                            </div>
                        </div>

                        <!-- STEP 1: DATA KARYAWAN -->
                        <div class="wizard-step-content" id="step-1">
                            <!-- Toggle Mode Karyawan Baru / Pilih Terdaftar -->
                            <div class="d-flex align-items-center justify-content-between mb-3 p-2 bg-light rounded-3" id="employee-mode-wrapper">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm mode-tab-btn active" id="btn-mode-new" onclick="setEmployeeMode('new')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 11h6m-3 -3v6" /></svg>
                                        Buat Karyawan Baru
                                    </button>
                                    <button type="button" class="btn btn-sm mode-tab-btn btn-ghost-secondary" id="btn-mode-existing" onclick="setEmployeeMode('existing')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                                        Pilih Karyawan Terdaftar
                                    </button>
                                </div>
                                <span class="badge bg-blue-lt text-primary px-2 py-1 small">Langkah 1 dari 3</span>
                            </div>

                            <!-- Form Karyawan Baru -->
                            <div id="section-new-employee">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required">Nama Lengkap Karyawan</label>
                                        <input type="text" name="name" id="emp_name" class="form-control" placeholder="Contoh: Budi Santoso">
                                        <span class="text-danger error_name small"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required">Username</label>
                                        <input type="text" name="username" id="emp_username" class="form-control" placeholder="Contoh: budisantoso">
                                        <span class="text-danger error_username small"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required">Alamat Email</label>
                                        <input type="email" name="email" id="emp_email" class="form-control" placeholder="budi@example.com">
                                        <span class="text-danger error_email small"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required">Nomor WhatsApp / HP</label>
                                        <input type="text" name="phone" id="emp_phone" class="form-control" placeholder="081234567890">
                                        <span class="text-danger error_phone small"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required">Password Akun</label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="emp_password" class="form-control" placeholder="Minimal 6 karakter" value="password123">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('emp_password')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                            </button>
                                        </div>
                                        <small class="text-muted">Default: <code>password123</code></small>
                                        <span class="text-danger error_password small d-block"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Role / Hak Akses</label>
                                        <div class="form-control-plaintext bg-light px-3 py-2 rounded border d-flex align-items-center justify-content-between">
                                            <span class="badge bg-primary text-white fw-semibold">Karyawan (Otomatis Terpilih)</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                        </div>
                                        <input type="hidden" name="role" value="Karyawan">
                                        <span class="text-danger error_role small"></span>
                                    </div>
                                </div>

                                <hr class="my-3">
                                <h6 class="fw-bold text-dark mb-2">Informasi Rekening Bank (Untuk Transfer Gaji)</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small">Nama Bank</label>
                                        <select name="bank_name" id="emp_bank_name" class="form-select">
                                            <option value="">-- Pilih Bank --</option>
                                            @foreach ($banks as $code => $bankLabel)
                                                <option value="{{ $code }}">{{ $bankLabel }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error_bank_name small"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Nomor Rekening</label>
                                        <input type="text" name="account_number" id="emp_account_number" class="form-control" placeholder="Contoh: 1234567890">
                                        <span class="text-danger error_account_number small"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Nama Pemilik Rekening</label>
                                        <input type="text" name="account_holder_name" id="emp_account_holder_name" class="form-control" placeholder="Nama di buku tabungan">
                                        <span class="text-danger error_account_holder_name small"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Pilih Karyawan Terdaftar (Existing) -->
                            <div id="section-existing-employee" style="display: none;">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-semibold required">Pilih Karyawan Terdaftar</label>
                                    <select name="user_id" id="user_id" class="form-select" onchange="onExistingUserChange(this)">
                                        <option value="">-- Pilih Karyawan --</option>
                                        @foreach ($user as $usr)
                                            <option value="{{ $usr->id }}" data-name="{{ $usr->name }}" data-email="{{ $usr->email }}" data-phone="{{ $usr->phone ?? '-' }}" data-bank="{{ $usr->bank_name }}" data-acc="{{ $usr->account_number }}" data-holder="{{ $usr->account_holder_name }}">{{ $usr->name }} ({{ $usr->email }})</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error_user_id small"></span>
                                </div>
                                <div id="existing-user-preview" class="p-3 bg-light rounded border" style="display: none;">
                                    <div class="fw-bold text-primary" id="preview_user_name">-</div>
                                    <div class="small text-muted" id="preview_user_contact">-</div>
                                    <div class="small text-dark mt-1" id="preview_user_bank">Rekening: -</div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: KONFIGURASI GAJI & TUNJANGAN -->
                        <div class="wizard-step-content" id="step-2" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold text-dark mb-0">Nominal Gaji & Tunjangan Bulanan</h6>
                                <span class="badge bg-blue-lt text-primary px-2 py-1 small">Langkah 2 dari 3</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold required">Gaji Pokok (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold text-primary">Rp</span>
                                        <input type="text" name="base_salary" id="base_salary" class="form-control fw-bold fs-3 text-primary" placeholder="0">
                                    </div>
                                    <span class="text-danger error_base_salary small"></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold required">Tanggal Aktif Gaji</label>
                                    <input type="date" name="effective_date" id="effective_date" class="form-control" value="{{ date('Y-m-01') }}">
                                    <small class="text-muted">Tanggal gajian bulanan</small>
                                    <span class="text-danger error_effective_date small d-block"></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold required">Status Gaji</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <span class="text-danger error_status small"></span>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-semibold mb-0">Tunjangan Karyawan</label>
                                        <button type="button" id="add-tunjangan" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" onclick="addNewAllowanceItem()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                            Tambah Tunjangan
                                        </button>
                                    </div>
                                    <div id="tunjangan-wrapper">
                                        <div class="input-group mb-2 tunjangan-item">
                                            <select name="allowance_id[]" class="form-select allowance-select" onchange="if(currentStep === 3) populateReview();">
                                                <option value="">-- Tidak Mempunyai Tunjangan --</option>
                                                @foreach ($allowance as $alw)
                                                    <option value="{{ $alw->id }}" data-amount="{{ $alw->amount }}">{{ $alw->name }} - Rp {{ number_format($alw->amount, 0, ',', '.') }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-outline-danger remove-tunjangan" onclick="removeAllowanceItem(this)" style="display:none;">Hapus</button>
                                        </div>
                                    </div>
                                    <span class="text-danger error_allowance_id small"></span>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: REVIEW & SIMPAN -->
                        <div class="wizard-step-content" id="step-3" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold text-dark mb-0">Konfirmasi & Rangkuman Data</h6>
                                <span class="badge bg-green-lt text-success px-2 py-1 small">Langkah 3 dari 3</span>
                            </div>

                            <div class="row g-3">
                                <!-- Ringkasan Karyawan -->
                                <div class="col-md-6">
                                    <div class="review-box h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="avatar avatar-sm bg-primary-lt text-primary rounded-circle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                            </span>
                                            <h6 class="fw-bold text-dark mb-0">Data Profil Karyawan</h6>
                                        </div>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted" style="width: 120px;">Nama:</td>
                                                <td class="fw-bold text-dark" id="rev_emp_name">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Username:</td>
                                                <td id="rev_emp_username">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Email:</td>
                                                <td id="rev_emp_email">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">No. WhatsApp:</td>
                                                <td id="rev_emp_phone">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Role:</td>
                                                <td><span class="badge bg-primary-lt text-primary">Karyawan</span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Rekening Bank:</td>
                                                <td id="rev_emp_bank">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Ringkasan Gaji & Tunjangan -->
                                <div class="col-md-6">
                                    <div class="review-box h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="avatar avatar-sm bg-success-lt text-success rounded-circle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                            </span>
                                            <h6 class="fw-bold text-dark mb-0">Rincian Gaji & Tunjangan</h6>
                                        </div>
                                        <table class="table table-sm table-borderless mb-2">
                                            <tr>
                                                <td class="text-muted" style="width: 120px;">Gaji Pokok:</td>
                                                <td class="fw-bold text-dark" id="rev_salary_base">Rp 0</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tgl Aktif Gaji:</td>
                                                <td id="rev_salary_date">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Status:</td>
                                                <td id="rev_salary_status"><span class="badge bg-success-lt text-success">Active</span></td>
                                            </tr>
                                        </table>

                                        <div class="border-top pt-2 mt-2">
                                            <div class="small fw-semibold text-muted mb-1">Tunjangan:</div>
                                            <div id="rev_allowance_container" class="small mb-2">-</div>
                                        </div>

                                        <div class="p-2 rounded bg-success-lt text-success d-flex justify-content-between align-items-center mt-2">
                                            <span class="fw-bold small text-uppercase">Total Gaji Tetap:</span>
                                            <span class="h4 mb-0 fw-bold" id="rev_salary_total">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" id="btn-wizard-prev" style="display: none;" onclick="prevStep()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
                                Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-wizard-next" onclick="nextStep()">
                                Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ms-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                            </button>
                            <button type="submit" class="btn btn-success shadow-sm" id="btn-wizard-submit" style="display: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Simpan Data & Buat Gaji
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kalkulasi & Transfer Gaji (3-Step Wizard: Saldo Xendit vs Saldo Manual) -->
    <div class="modal modal-blur fade" id="modal-transfer-gaji" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white border-bottom py-3">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M18 12l.01 0" /><path d="M6 12l.01 0" /></svg>
                            Transfer & Pembayaran Gaji Karyawan
                        </h5>
                        <p class="text-muted small mb-0" id="tf-wizard-subtitle">Langkah 1: Pilih metode pembayaran & kantong saldo</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div id="transfer-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted mt-2">Menghitung gaji, tunjangan, potongan kasbon, & cek saldo realtime...</div>
                </div>

                <form id="form-transfer-gaji" style="display:none;">
                    <input type="hidden" id="tf_salary_id">
                    <input type="hidden" id="tf_selected_type" value="xendit">

                    <div class="modal-body p-4">
                        <!-- Stepper Indicator Bar -->
                        <div class="wizard-stepper mb-4" id="tf-wizard-stepper-bar">
                            <div class="wizard-step-item active" data-step="1" onclick="goToTransferStep(1)">
                                <div class="wizard-step-circle">1</div>
                                <div class="wizard-step-label">Pilih Saldo</div>
                            </div>
                            <div class="wizard-step-item" data-step="2" onclick="goToTransferStep(2)">
                                <div class="wizard-step-circle">2</div>
                                <div class="wizard-step-label">Kalkulasi & Rekening</div>
                            </div>
                            <div class="wizard-step-item" data-step="3" onclick="goToTransferStep(3)">
                                <div class="wizard-step-circle">3</div>
                                <div class="wizard-step-label">Review & Kirim</div>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 1: PILIH KANTONG SALDO & METODE TRANSFER -->
                        <!-- ============================================== -->
                        <div class="tf-step-content" id="tf-step-1">
                            <!-- Ringkasan Singkat Penerima Gaji -->
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <div class="text-muted small text-uppercase fw-semibold">Penerima Gaji</div>
                                            <div class="h3 mb-0 text-primary fw-bold" id="tf_s1_user_name">-</div>
                                            <div class="small text-muted" id="tf_s1_period">Periode: -</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-muted small text-uppercase fw-semibold">Estimasi Gaji Pokok</div>
                                            <div class="h4 mb-0 fw-bold text-dark" id="tf_s1_est_salary">Rp 0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold text-dark mb-0">Pilih Kantong Saldo & Jalur Transfer:</label>
                                <span class="badge bg-blue-lt text-primary px-2 py-1 small">Langkah 1 dari 3</span>
                            </div>
                            <p class="text-muted small mb-3">Tentukan sumber saldo yang akan dipotong dan apakah sistem akan mengirim dana via gateway Xendit atau transfer manual.</p>

                            <div class="row g-3">
                                <!-- Card Opsi 1: Saldo Xendit (Otomatis) -->
                                <div class="col-md-6">
                                    <div class="tf-method-card h-100 selected" id="tf_card_xendit" onclick="selectTransferType('xendit')">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-sm rounded-3 bg-azure-lt text-azure">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0z" /></svg>
                                                </span>
                                                <div>
                                                    <h4 class="mb-0 fw-bold text-dark">Saldo Xendit</h4>
                                                    <span class="badge bg-azure-lt text-azure" style="font-size: 0.7rem;">Disbursement Otomatis</span>
                                                </div>
                                            </div>
                                            <input type="radio" name="transfer_type_radio" id="radio_xendit" value="xendit" checked class="form-check-input">
                                        </div>

                                        <div class="my-2 p-2 bg-light rounded border">
                                            <div class="text-muted small">Sisa Saldo Xendit:</div>
                                            <div class="h3 mb-0 fw-bold text-azure" id="tf_val_xendit">Rp 0</div>
                                        </div>

                                        <p class="text-muted small mb-2" style="font-size: 0.775rem;">
                                            Kirim uang nyata langsung ke rekening karyawan secara realtime via <strong>Xendit Disbursement API</strong> dan potong Saldo Xendit di Finance API.
                                        </p>

                                        <div id="tf_badge_status_xendit">
                                            <span class="badge bg-success-lt text-success d-inline-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                Saldo Mencukupi
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Opsi 2: Saldo Manual (Kas / Transfer Bank Manual) -->
                                <div class="col-md-6">
                                    <div class="tf-method-card h-100" id="tf_card_manual" onclick="selectTransferType('manual')">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-sm rounded-3 bg-primary-lt text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                                </span>
                                                <div>
                                                    <h4 class="mb-0 fw-bold text-dark">Saldo Manual</h4>
                                                    <span class="badge bg-primary-lt text-primary" style="font-size: 0.7rem;">Kas / Bank Manual</span>
                                                </div>
                                            </div>
                                            <input type="radio" name="transfer_type_radio" id="radio_manual" value="manual" class="form-check-input">
                                        </div>

                                        <div class="my-2 p-2 bg-light rounded border">
                                            <div class="text-muted small">Sisa Saldo Manual:</div>
                                            <div class="h3 mb-0 fw-bold text-primary" id="tf_val_manual">Rp 0</div>
                                        </div>

                                        <p class="text-muted small mb-2" style="font-size: 0.775rem;">
                                            Pencatatan transfer bank manual / uang tunai kas operasional dengan potongan biaya admin dan memotong Saldo Manual di Finance API.
                                        </p>

                                        <div id="tf_badge_status_manual">
                                            <span class="badge bg-success-lt text-success d-inline-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                Saldo Mencukupi
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Peringatan Jika Saldo Terpilih Kurang -->
                            <div id="tf_insufficient_alert" class="alert alert-danger mt-3 mb-0" style="display: none;">
                                <div class="d-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                                    <div id="tf_insufficient_msg" class="fw-semibold small">Saldo tidak mencukupi untuk pembayaran gaji ini.</div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 2: RINCIAN KALKULASI & REKENING PENERIMA -->
                        <!-- ============================================== -->
                        <div class="tf-step-content" id="tf-step-2" style="display: none;">
                            <!-- Header Metode Terpilih -->
                            <div class="d-flex align-items-center justify-content-between mb-3 p-2 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Jalur Pembayaran:</span>
                                    <span id="tf_s2_method_badge" class="badge bg-azure text-white px-2 py-1">Saldo Xendit (Disbursement Otomatis)</span>
                                </div>
                                <span class="badge bg-blue-lt text-primary px-2 py-1 small">Langkah 2 dari 3</span>
                            </div>

                            <!-- Rincian Kalkulasi: Penambahan (+) & Pengurangan (-) -->
                            <div class="row g-3">
                                <!-- Kolom Kiri: Gaji Pokok & Tunjangan -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-success-subtle border">
                                        <div class="card-header bg-success-lt py-2">
                                            <strong class="text-success small text-uppercase">
                                                (+) Penghasilan & Tunjangan
                                            </strong>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between py-1 border-bottom">
                                                <span class="text-muted">Gaji Pokok:</span>
                                                <strong id="tf_base_salary">Rp 0</strong>
                                            </div>
                                            <div id="tf_allowance_list" class="mt-2">
                                                <!-- List tunjangan injected by JS -->
                                            </div>
                                            <div class="d-flex justify-content-between pt-2 mt-2 border-top fw-bold text-success">
                                                <span>Subtotal Pendapatan:</span>
                                                <span id="tf_subtotal_income">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Potongan Kasbon & Biaya Admin -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-danger-subtle border">
                                        <div class="card-header bg-danger-lt py-2">
                                            <strong class="text-danger small text-uppercase">
                                                (-) Potongan Kasbon & Biaya Admin
                                            </strong>
                                        </div>
                                        <div class="card-body p-3">
                                            <div id="tf_cash_advance_list">
                                                <!-- List kasbon injected by JS -->
                                            </div>
                                            <div class="d-flex justify-content-between py-1 small border-top pt-2 mt-1" id="tf_admin_fee_row">
                                                <span class="text-muted" id="tf_admin_fee_label">• Biaya Admin Transfer (Xendit):</span>
                                                <span class="text-danger fw-semibold" id="tf_admin_fee">-Rp 0</span>
                                            </div>
                                            <div class="d-flex justify-content-between pt-2 mt-2 border-top fw-bold text-danger">
                                                <span>Total Semua Potongan:</span>
                                                <span id="tf_total_deductions">-Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Bersih Banner -->
                            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%); border-radius: 12px; padding: 1.25rem 1.5rem; color: #ffffff; box-shadow: 0 4px 15px rgba(29, 78, 216, 0.25);" class="mt-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <div style="color: rgba(255,255,255,0.85); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Nominal Gaji Bersih Siap Ditransfer</div>
                                        <div style="color: rgba(255,255,255,0.7); font-size: 0.8rem; margin-top: 2px;" id="tf_net_salary_sub">(Gaji Pokok + Tunjangan - Total Kasbon - Biaya Admin)</div>
                                    </div>
                                    <div class="text-end">
                                        <div style="color: #ffffff; font-size: 1.85rem; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.2);" id="tf_net_salary">Rp 0</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Rekening Penerima (Hanya untuk Xendit) -->
                            <div class="mt-3" id="tf_bank_container">
                                <label class="form-label fw-bold text-dark mb-2">Informasi Rekening Bank Karyawan (Tujuan Transfer Xendit)</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Bank</label>
                                        <select name="bank_name" id="tf_bank_name" class="form-select">
                                            <option value="">-- Pilih Bank --</option>
                                            @foreach($banks as $bCode => $bLabel)
                                                <option value="{{ $bCode }}">{{ $bLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">No. Rekening</label>
                                        <input type="text" name="account_number" id="tf_account_number" class="form-control" placeholder="Nomor rekening">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Atas Nama Rekening</label>
                                        <input type="text" name="account_holder_name" id="tf_account_holder_name" class="form-control" placeholder="Nama pemilik rekening">
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan Kas Tunai / Manual (Pengganti Form Bank) -->
                            <div class="mt-3" id="tf_cash_container" style="display: none;">
                                <div class="alert alert-info py-3 px-3 mb-0" style="background-color: #f0f7ff; border: 1px solid #c8e1ff; border-radius: 8px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="avatar avatar-md bg-primary text-white rounded-circle fs-3">💵</span>
                                        <div>
                                            <div class="fw-bold text-primary fs-5">Pembayaran Gaji Tunai / Kas Operasional</div>
                                            <div class="text-muted small">
                                                Informasi rekening bank disembunyikan karena transaksi ini tidak ditransfer melalui gateway Xendit, melainkan dibayarkan tunai/manual dan saldo manual Web Finance akan dipotong otomatis.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 3: REVIEW AKHIR & EKSEKUSI TRANSFER -->
                        <!-- ============================================== -->
                        <div class="tf-step-content" id="tf-step-3" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold text-dark mb-0">Konfirmasi Akhir Transfer Gaji</h6>
                                <span class="badge bg-green-lt text-success px-2 py-1 small">Langkah 3 dari 3</span>
                            </div>

                            <div class="row g-3">
                                <!-- Ringkasan Profil & Rekening -->
                                <div class="col-md-6">
                                    <div class="review-box h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="avatar avatar-sm bg-primary-lt text-primary rounded-circle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                            </span>
                                            <h6 class="fw-bold text-dark mb-0">Informasi Penerima & Metode</h6>
                                        </div>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted" style="width: 120px;">Karyawan:</td>
                                                <td class="fw-bold text-dark" id="tf_rev_user_name">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Periode Gaji:</td>
                                                <td id="tf_rev_period">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Metode:</td>
                                                <td id="tf_rev_method_badge"><span class="badge bg-azure text-white">Saldo Xendit</span></td>
                                            </tr>
                                            <tr id="tf_rev_bank_row">
                                                <td class="text-muted">Rekening:</td>
                                                <td id="tf_rev_bank_info" class="fw-semibold text-dark">-</td>
                                            </tr>
                                            <tr id="tf_rev_cash_row" style="display: none;">
                                                <td class="text-muted">Jenis:</td>
                                                <td class="fw-semibold text-primary">💵 Pembayaran Tunai / Kas Langsung</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Ringkasan Saldo & Pemotongan -->
                                <div class="col-md-6">
                                    <div class="review-box h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="avatar avatar-sm bg-success-lt text-success rounded-circle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                            </span>
                                            <h6 class="fw-bold text-dark mb-0">Rincian Pemotongan Saldo</h6>
                                        </div>
                                        <table class="table table-sm table-borderless mb-2">
                                            <tr>
                                                <td class="text-muted" style="width: 140px;">Kantong Saldo:</td>
                                                <td class="fw-bold text-dark" id="tf_rev_balance_name">Saldo Xendit</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Saldo Saat Ini:</td>
                                                <td id="tf_rev_initial_balance">Rp 0</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Nominal Dipotong:</td>
                                                <td class="fw-bold text-danger" id="tf_rev_deducted_amount">-Rp 0</td>
                                            </tr>
                                            <tr class="border-top">
                                                <td class="text-muted pt-1">Estimasi Sisa Saldo:</td>
                                                <td class="fw-bold text-success pt-1" id="tf_rev_remaining_balance">Rp 0</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info py-2 px-3 mb-0 small" id="tf_rev_note_box">
                                        <div class="fw-bold mb-1">ℹ️ Catatan Proses:</div>
                                        <span id="tf_rev_note_text">Dana akan ditransfer otomatis melalui gateway Xendit dan saldo Xendit akan terpotong secara realtime.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" id="btn-tf-prev" style="display: none;" onclick="prevTransferStep()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
                                Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-tf-next" onclick="nextTransferStep()">
                                Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ms-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                            </button>
                            <button type="button" class="btn btn-success shadow-sm" id="btn-tf-submit" style="display: none;" onclick="submitTransferGaji()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Konfirmasi & Kirim Transfer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('js')
    <script>
        const BASE = "{{ route('salary.index') }}";

        let params = new URLSearchParams(window.location.search);
        $("#sort").change(function() {
            params.set('sort', $(this).val());
            window.location.href = BASE + '?' + params.toString();
        });

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        const allowanceOptions = `
            @foreach ($allowance as $alw)
                <option value="{{ $alw->id }}" data-amount="{{ $alw->amount }}">{{ $alw->name }} - Rp {{ number_format($alw->amount, 0, ',', '.') }}</option>
            @endforeach
        `;

        let currentStep = 1;

        // Auto format Rupiah pada input gaji
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("base_salary");
            if (input) {
                input.addEventListener("input", function(e) {
                    let value = this.value.replace(/\D/g, "");
                    if (value) {
                        this.value = new Intl.NumberFormat("id-ID").format(value);
                    } else {
                        this.value = "";
                    }
                    if (currentStep === 3) populateReview();
                });
            }

            // Auto-fill username & rekening dari nama lengkap
            const nameInput = document.getElementById("emp_name");
            if (nameInput) {
                nameInput.addEventListener("input", function() {
                    const nameVal = this.value.trim();
                    const usernameInput = document.getElementById("emp_username");
                    const holderInput = document.getElementById("emp_account_holder_name");

                    if (usernameInput && (!usernameInput.dataset.manual || usernameInput.dataset.manual === "false")) {
                        let slug = nameVal.toLowerCase()
                            .replace(/[^a-z0-9]/g, '')
                            .substring(0, 20);
                        usernameInput.value = slug;
                    }

                    if (holderInput && (!holderInput.dataset.manual || holderInput.dataset.manual === "false")) {
                        holderInput.value = nameVal;
                    }
                });
            }

            const usernameInput = document.getElementById("emp_username");
            if (usernameInput) {
                usernameInput.addEventListener("change", function() {
                    this.dataset.manual = "true";
                });
            }

            const holderInput = document.getElementById("emp_account_holder_name");
            if (holderInput) {
                holderInput.addEventListener("change", function() {
                    this.dataset.manual = "true";
                });
            }
        });

        // Toggle Password Visibility
        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        }

        // Switch mode: Buat Karyawan Baru vs Pilih Terdaftar
        function setEmployeeMode(mode) {
            $("#employee_mode").val(mode);
            if (mode === 'new') {
                $("#btn-mode-new").addClass("active").removeClass("btn-ghost-secondary");
                $("#btn-mode-existing").removeClass("active").addClass("btn-ghost-secondary");
                $("#section-new-employee").show();
                $("#section-existing-employee").hide();
            } else {
                $("#btn-mode-existing").addClass("active").removeClass("btn-ghost-secondary");
                $("#btn-mode-new").removeClass("active").addClass("btn-ghost-secondary");
                $("#section-new-employee").hide();
                $("#section-existing-employee").show();
            }
        }

        // Preview saat pilih existing user
        function onExistingUserChange(el) {
            const selected = $(el).find(":selected");
            const val = selected.val();
            if (val) {
                const name = selected.data("name") || "-";
                const email = selected.data("email") || "-";
                const phone = selected.data("phone") || "-";
                const bank = selected.data("bank") || "-";
                const acc = selected.data("acc") || "-";
                const holder = selected.data("holder") || "-";

                $("#preview_user_name").text(name);
                $("#preview_user_contact").text("Email: " + email + " | No. WA: " + phone);
                $("#preview_user_bank").text("Bank: " + bank.toUpperCase() + " | Rekening: " + acc + " (a.n " + holder + ")");
                $("#existing-user-preview").show();
            } else {
                $("#existing-user-preview").hide();
            }
        }

        // Update UI Wizard Step
        function updateWizardUI() {
            $(".wizard-step-content").hide();
            $("#step-" + currentStep).fadeIn(200);

            // Update Stepper Bar items
            $(".wizard-step-item").each(function() {
                const stepNum = parseInt($(this).data("step"));
                $(this).removeClass("active completed");
                if (stepNum === currentStep) {
                    $(this).addClass("active");
                } else if (stepNum < currentStep) {
                    $(this).addClass("completed");
                }
            });

            // Update Buttons
            if (currentStep === 1) {
                $("#btn-wizard-prev").hide();
                $("#btn-wizard-next").show();
                $("#btn-wizard-submit").hide();
            } else if (currentStep === 2) {
                $("#btn-wizard-prev").show();
                $("#btn-wizard-next").show();
                $("#btn-wizard-submit").hide();
            } else if (currentStep === 3) {
                $("#btn-wizard-prev").show();
                $("#btn-wizard-next").hide();
                $("#btn-wizard-submit").show();
                populateReview();
            }
        }

        // Validasi per step sebelum lanjut
        function validateStep(step) {
            let isValid = true;
            $(".is-invalid").removeClass("is-invalid");
            $(".text-danger[class*='error_']").html("");

            const type = $("#type").val();

            if (step === 1) {
                if (type === "update") {
                    return true;
                }

                const mode = $("#employee_mode").val();
                if (mode === "new") {
                    const name = $("#emp_name").val().trim();
                    const username = $("#emp_username").val().trim();
                    const email = $("#emp_email").val().trim();
                    const phone = $("#emp_phone").val().trim();
                    const password = $("#emp_password").val().trim();

                    if (!name) {
                        $("#emp_name").addClass("is-invalid");
                        $(".error_name").html("Nama lengkap wajib diisi.");
                        isValid = false;
                    }
                    if (!username) {
                        $("#emp_username").addClass("is-invalid");
                        $(".error_username").html("Username wajib diisi.");
                        isValid = false;
                    }
                    if (!email || !email.includes("@")) {
                        $("#emp_email").addClass("is-invalid");
                        $(".error_email").html("Email tidak valid.");
                        isValid = false;
                    }
                    if (!phone) {
                        $("#emp_phone").addClass("is-invalid");
                        $(".error_phone").html("Nomor WhatsApp wajib diisi.");
                        isValid = false;
                    }
                    if (!password || password.length < 6) {
                        $("#emp_password").addClass("is-invalid");
                        $(".error_password").html("Password minimal 6 karakter.");
                        isValid = false;
                    }
                } else {
                    const userId = $("#user_id").val();
                    if (!userId) {
                        $("#user_id").addClass("is-invalid");
                        $(".error_user_id").html("Silakan pilih karyawan terdaftar.");
                        isValid = false;
                    }
                }
            } else if (step === 2) {
                const baseSalary = $("#base_salary").val().trim();
                const effDate = $("#effective_date").val();
                const status = $("#status").val();

                if (!baseSalary || baseSalary === "0") {
                    $("#base_salary").addClass("is-invalid");
                    $(".error_base_salary").html("Nominal gaji pokok wajib diisi.");
                    isValid = false;
                }
                if (!effDate) {
                    $("#effective_date").addClass("is-invalid");
                    $(".error_effective_date").html("Tanggal aktif gaji wajib diisi.");
                    isValid = false;
                }
                if (!status) {
                    $("#status").addClass("is-invalid");
                    $(".error_status").html("Pilih status gaji.");
                    isValid = false;
                }
            }

            if (!isValid) {
                Toast.fire({
                    icon: "warning",
                    title: "Lengkapi data wajib pada langkah ini sebelum melanjutkan."
                });
            }

            return isValid;
        }

        function nextStep() {
            if (validateStep(currentStep)) {
                if (currentStep < 3) {
                    currentStep++;
                    updateWizardUI();
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateWizardUI();
            }
        }

        function goToStep(targetStep) {
            if (targetStep < currentStep) {
                currentStep = targetStep;
                updateWizardUI();
            } else if (targetStep > currentStep) {
                let canAdvance = true;
                for (let s = currentStep; s < targetStep; s++) {
                    if (!validateStep(s)) {
                        canAdvance = false;
                        break;
                    }
                }
                if (canAdvance) {
                    currentStep = targetStep;
                    updateWizardUI();
                }
            }
        }

        // Rangkuman live di Step 3
        function populateReview() {
            const mode = $("#employee_mode").val();
            const type = $("#type").val();

            if (type === "update") {
                // Mode edit
                $("#rev_emp_name").text($("#user_id option:selected").text() || "-");
                $("#rev_emp_username").text("-");
                $("#rev_emp_email").text("-");
                $("#rev_emp_phone").text("-");
                const bName = $("#emp_bank_name").val() || "-";
                const bAcc = $("#emp_account_number").val() || "-";
                $("#rev_emp_bank").text(bName.toUpperCase() + " - " + bAcc);
            } else if (mode === "new") {
                $("#rev_emp_name").text($("#emp_name").val() || "-");
                $("#rev_emp_username").text($("#emp_username").val() || "-");
                $("#rev_emp_email").text($("#emp_email").val() || "-");
                $("#rev_emp_phone").text($("#emp_phone").val() || "-");

                const bName = $("#emp_bank_name").val() || "";
                const bAcc = $("#emp_account_number").val() || "";
                const bHolder = $("#emp_account_holder_name").val() || "";
                if (bName && bAcc) {
                    $("#rev_emp_bank").text(bName.toUpperCase() + " : " + bAcc + " (a.n " + bHolder + ")");
                } else {
                    $("#rev_emp_bank").text("Belum diisi");
                }
            } else {
                const selected = $("#user_id option:selected");
                $("#rev_emp_name").text(selected.data("name") || selected.text());
                $("#rev_emp_username").text("-");
                $("#rev_emp_email").text(selected.data("email") || "-");
                $("#rev_emp_phone").text(selected.data("phone") || "-");
                const bName = selected.data("bank") || "-";
                const bAcc = selected.data("acc") || "-";
                $("#rev_emp_bank").text(bName.toUpperCase() + " : " + bAcc);
            }

            // Gaji Pokok & Tunjangan
            const baseSalaryRaw = $("#base_salary").val().replace(/\D/g, "") || "0";
            const baseSalaryNum = parseFloat(baseSalaryRaw) || 0;
            $("#rev_salary_base").text("Rp " + baseSalaryNum.toLocaleString("id-ID"));

            const effDate = $("#effective_date").val();
            if (effDate) {
                const dateObj = new Date(effDate);
                const day = dateObj.getDate();
                $("#rev_salary_date").html(effDate + " <span class='badge bg-blue-lt text-primary ms-1'>Gajian Tgl " + day + "</span>");
            } else {
                $("#rev_salary_date").text("-");
            }

            const status = $("#status").val();
            if (status === "active") {
                $("#rev_salary_status").html('<span class="badge bg-success-lt text-success">Active</span>');
            } else {
                $("#rev_salary_status").html('<span class="badge bg-secondary-lt text-secondary">Inactive</span>');
            }

            // Hitung Tunjangan
            let totalAllowance = 0;
            let allowanceItemsHtml = "";
            let countAlw = 0;

            $(".allowance-select").each(function() {
                const selectedOption = $(this).find(":selected");
                const val = selectedOption.val();
                if (val) {
                    const amount = parseFloat(selectedOption.data("amount")) || 0;
                    const text = selectedOption.text();
                    totalAllowance += amount;
                    countAlw++;
                    allowanceItemsHtml += `
                        <div class="d-flex justify-content-between py-1 border-bottom border-light">
                            <span>${text.split(" - ")[0]}</span>
                            <span class="fw-semibold text-success">+Rp ${amount.toLocaleString("id-ID")}</span>
                        </div>
                    `;
                }
            });

            if (countAlw > 0) {
                $("#rev_allowance_container").html(allowanceItemsHtml);
            } else {
                $("#rev_allowance_container").html('<span class="text-muted fst-italic">Tidak ada tunjangan</span>');
            }

            const grandTotal = baseSalaryNum + totalAllowance;
            $("#rev_salary_total").text("Rp " + grandTotal.toLocaleString("id-ID"));
        }

        // Tunjangan dynamic repeater
        function addNewAllowanceItem(selectedId = "") {
            const wrapper = document.getElementById("tunjangan-wrapper");
            if (!wrapper) return;

            let div = document.createElement("div");
            div.classList.add("input-group", "mb-2", "tunjangan-item");
            div.innerHTML = `
                <select name="allowance_id[]" class="form-select allowance-select" onchange="updateAllowanceRemoveButtons(); if(currentStep === 3) populateReview();">
                    <option value="">-- Pilih Tunjangan --</option>
                    ${allowanceOptions}
                </select>
                <button type="button" class="btn btn-outline-danger remove-tunjangan" onclick="removeAllowanceItem(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /></svg>
                    Hapus
                </button>
            `;

            if (selectedId) {
                const selectEl = div.querySelector("select");
                if (selectEl) selectEl.value = selectedId;
            }

            wrapper.appendChild(div);
            updateAllowanceRemoveButtons();

            if (currentStep === 3) populateReview();
        }

        function removeAllowanceItem(btn) {
            const item = btn.closest(".tunjangan-item");
            if (item) {
                item.remove();
                updateAllowanceRemoveButtons();
                if (currentStep === 3) populateReview();
            }
        }

        function updateAllowanceRemoveButtons() {
            const items = document.querySelectorAll("#tunjangan-wrapper .tunjangan-item");
            items.forEach((item) => {
                const removeBtn = item.querySelector(".remove-tunjangan");
                const selectVal = item.querySelector("select") ? item.querySelector("select").value : "";
                if (removeBtn) {
                    removeBtn.style.display = (items.length > 1 || selectVal !== "") ? "inline-flex" : "none";
                }
            });
        }

        // Tombol Tambah Gaji Karyawan (Reset & Buka Step 1)
        $("#addBtn").click(function() {
            $(".modal-title").html("Buat Karyawan & Gaji Pegawai");
            $("#wizard-subtitle").html("Lengkapi formulir 3 langkah untuk mendaftarkan karyawan & konfigurasi gajinya");
            $("#type").val("create");
            $("#id").val("");
            $("#employee-mode-wrapper").show();
            $("#wizard-stepper-bar").show();

            // Reset form fields
            $("#emp_name").val("").removeClass("is-invalid");
            $("#emp_username").val("").data("manual", "false").removeClass("is-invalid");
            $("#emp_email").val("").removeClass("is-invalid");
            $("#emp_phone").val("").removeClass("is-invalid");
            $("#emp_password").val("password123").removeClass("is-invalid");
            $("#emp_bank_name").val("");
            $("#emp_account_number").val("");
            $("#emp_account_holder_name").val("").data("manual", "false");
            $("#user_id").val("").removeClass("is-invalid");
            $("#existing-user-preview").hide();

            $("#base_salary").val("").removeClass("is-invalid");
            $("#effective_date").val("{{ date('Y-m-01') }}").removeClass("is-invalid");
            $("#status").val("active").removeClass("is-invalid");

            // Reset tunjangan
            const wrapper = document.getElementById("tunjangan-wrapper");
            if (wrapper) {
                wrapper.innerHTML = "";
                addNewAllowanceItem();
            }

            setEmployeeMode("new");
            currentStep = 1;
            updateWizardUI();
        });

        // Submit form via AJAX
        $("#form").on("submit", function(e) {
            e.preventDefault();

            let id   = $("#id").val();
            let type = $("#type").val();
            let url, method;

            if (type === "create") {
                url = BASE + "/store";
                method = "POST";
            } else {
                url = BASE + `/${id}/update`;
                method = "POST"; 
            }

            let formData = new FormData(this);
            if (type !== "create") {
                formData.append("_method", "PUT");
            }

            $("#btn-wizard-submit").prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');

            $.ajax({
                url: url,
                method: method,
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(response) {
                $("#btn-wizard-submit").prop("disabled", false).html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> Simpan Data & Buat Gaji');

                if (response.errors) {
                    let firstErrorStep = 1;
                    $.each(response.errors, function(index, value) {
                        let inputField = $("[name='" + index + "']");
                        inputField.addClass("is-invalid");
                        $(".error_" + index).html(value);

                        if (['name', 'username', 'email', 'phone', 'password', 'user_id'].includes(index)) {
                            firstErrorStep = 1;
                        } else if (['base_salary', 'effective_date', 'status', 'allowance_id'].includes(index)) {
                            if (firstErrorStep !== 1) firstErrorStep = 2;
                        }

                        setTimeout(() => {
                            inputField.removeClass("is-invalid");
                            $(".error_" + index).html("");
                        }, 5000);
                    });

                    Toast.fire({
                        icon: "error",
                        title: "Terdapat data yang belum valid. Silakan periksa kembali."
                    });

                    goToStep(firstErrorStep);
                } else {
                    $("#modal-simple").modal("hide");
                    Toast.fire({
                        icon: "success",
                        title: response.message
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $("#btn-wizard-submit").prop("disabled", false).html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg> Simpan Data & Buat Gaji');
                
                let msg = "Terjadi kesalahan pada server.";
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    msg = jqXHR.responseJSON.message;
                }
                Toast.fire({
                    icon: "error",
                    title: msg
                });
            });
        });

        // Edit Modal
        function editModal(id) {
            let url = BASE + `/${id}/show`;
            $.ajax({
                url: url,
                method: "GET",
                dataType: "json"
            }).done(function(response){
                $(".modal-title").html("Edit Gaji Pegawai");
                $("#wizard-subtitle").html("Perbarui data gaji pokok, tunjangan, dan rekening karyawan");
                $("#type").val("update");
                let data = response.data;
                $("#modal-simple").modal('show');

                $("#id").val(data.id);
                $("#user_id").val(data.user.id);
                $("#effective_date").val(data.effective_date);
                $("#status").val(data.status);

                if (data.user) {
                    $("#emp_name").val(data.user.name);
                    $("#emp_username").val(data.user.username);
                    $("#emp_email").val(data.user.email);
                    $("#emp_phone").val(data.user.phone);
                    $("#emp_bank_name").val(data.user.bank_name);
                    $("#emp_account_number").val(data.user.account_number);
                    $("#emp_account_holder_name").val(data.user.account_holder_name || data.user.name);
                }

                let formattedSalary = new Intl.NumberFormat('id-ID').format(data.base_salary);
                $("#base_salary").val(formattedSalary);

                let wrapper = document.getElementById("tunjangan-wrapper");
                if (wrapper) {
                    wrapper.innerHTML = "";

                    if (data.user && data.user.allowance && data.user.allowance.length > 0) {
                        data.user.allowance.forEach(alw => {
                            addNewAllowanceItem(alw.id);
                        });
                    } else {
                        addNewAllowanceItem();
                    }
                }

                setEmployeeMode("existing");
                $("#employee-mode-wrapper").hide();
                currentStep = 2;
                updateWizardUI();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("Error:", textStatus, errorThrown);
            });
        }


        function deleteItem(id) {
            Swal.fire({
                title: "Peringatan !",
                text: "Anda yakin ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Hapus",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: BASE + '/' + id + '/destroy',
                        method: "DELETE",
                        dataType: "json",
                        success: function(response) {
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });

                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        },
                        error: function(err) {
                            Toast.fire({
                                icon: "error",
                                title: "Server Error"
                            });
                        }
                    });
                }
            });
        }

        function formatRupiah(num) {
            if (num === null || num === undefined || isNaN(num)) {
                return 'Rp 0';
            }
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        let currentTransferStep = 1;
        let transferData = null;

        // Buka modal kalkulasi transfer gaji
        function openTransferModal(salaryId) {
            currentTransferStep = 1;
            transferData = null;
            $("#tf_salary_id").val(salaryId);
            $("#transfer-loading").show();
            $("#form-transfer-gaji").hide();
            $("#modal-transfer-gaji").modal("show");

            $.ajax({
                url: BASE + `/${salaryId}/calculate-transfer`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $("#transfer-loading").hide();
                    if (!res.status) {
                        Toast.fire({ icon: "error", title: res.message });
                        $("#modal-transfer-gaji").modal("hide");
                        return;
                    }

                    let d = res.data;
                    transferData = d;
                    $("#form-transfer-gaji").show();

                    // Step 1 Header Info
                    $("#tf_s1_user_name").text(d.user_name);
                    $("#tf_s1_period").text("Periode: " + d.month_name);
                    $("#tf_s1_est_salary").text(formatRupiah(d.base_salary));

                    // Saldo Displays
                    $("#tf_val_xendit").text(formatRupiah(d.balance_xendit || 0));
                    $("#tf_val_manual").text(formatRupiah(d.balance_manual || 0));

                    // Step 2 Pre-fill Bank info
                    if (d.bank_name) {
                        $("#tf_bank_name").val(d.bank_name);
                    }
                    $("#tf_account_number").val(d.account_number || '');
                    $("#tf_account_holder_name").val(d.account_holder_name || d.user_name || '');

                    // Step 2 Calculation Base & Allowances
                    $("#tf_base_salary").text(formatRupiah(d.base_salary));
                    let allwHtml = '';
                    if (d.allowances && d.allowances.length > 0) {
                        d.allowances.forEach(alw => {
                            allwHtml += `
                                <div class="d-flex justify-content-between py-1 small">
                                    <span class="text-muted">• ${alw.name}:</span>
                                    <span>+${formatRupiah(alw.amount)}</span>
                                </div>
                            `;
                        });
                    } else {
                        allwHtml = '<div class="text-muted small py-1 fst-italic">Tidak memiliki tunjangan</div>';
                    }
                    $("#tf_allowance_list").html(allwHtml);
                    $("#tf_subtotal_income").text(formatRupiah(d.subtotal_income));

                    // Step 2 Kasbon
                    let caHtml = '';
                    if (d.cash_advances && d.cash_advances.length > 0) {
                        d.cash_advances.forEach(ca => {
                            caHtml += `
                                <div class="d-flex justify-content-between py-1 small">
                                    <span class="text-muted">• ${ca.title} (${ca.request_date}):</span>
                                    <span class="text-danger">-${formatRupiah(ca.amount)}</span>
                                </div>
                            `;
                        });
                    } else {
                        caHtml = '<div class="text-muted small py-1 fst-italic">Tidak ada potongan kasbon bulan ini</div>';
                    }
                    $("#tf_cash_advance_list").html(caHtml);

                    // Saluran Aktif / Nonaktif (Channel Status)
                    let xenditActive = d.channel_xendit_enabled !== undefined ? d.channel_xendit_enabled : (d.channel_status ? d.channel_status.xendit : true);
                    let manualActive = d.channel_manual_enabled !== undefined ? d.channel_manual_enabled : (d.channel_status ? d.channel_status.manual : true);
                    transferData.xendit_active = xenditActive;
                    transferData.manual_active = manualActive;

                    if (!xenditActive) {
                        $("#tf_card_xendit").addClass("disabled");
                        $("#radio_xendit").prop("disabled", true);
                        $("#tf_badge_status_xendit").html(`
                            <span class="badge bg-secondary-lt text-secondary d-inline-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 16h.01" /></svg>
                                Sedang Dinonaktifkan Admin
                            </span>
                        `);
                    } else {
                        $("#tf_card_xendit").removeClass("disabled");
                        $("#radio_xendit").prop("disabled", false);
                    }

                    if (!manualActive) {
                        $("#tf_card_manual").addClass("disabled");
                        $("#radio_manual").prop("disabled", true);
                        $("#tf_badge_status_manual").html(`
                            <span class="badge bg-secondary-lt text-secondary d-inline-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 16h.01" /></svg>
                                Sedang Dinonaktifkan Admin
                            </span>
                        `);
                    } else {
                        $("#tf_card_manual").removeClass("disabled");
                        $("#radio_manual").prop("disabled", false);
                    }

                    // Tentukan pilihan awal: pilih channel yang AKTIF & SALDO CUKUP
                    let estXenditNet = (d.net_salary || 0);
                    let estManualNet = (d.net_salary || 0);

                    let initType = 'xendit';
                    if (xenditActive && (d.balance_xendit || 0) >= estXenditNet) {
                        initType = 'xendit';
                    } else if (manualActive && (d.balance_manual || 0) >= estManualNet) {
                        initType = 'manual';
                    } else if (xenditActive) {
                        initType = 'xendit';
                    } else if (manualActive) {
                        initType = 'manual';
                    }

                    selectTransferType(initType);
                    updateTransferWizardUI();
                },
                error: function(xhr) {
                    $("#transfer-loading").hide();
                    Toast.fire({ icon: "error", title: "Gagal memuat rincian kalkulasi gaji." });
                    $("#modal-transfer-gaji").modal("hide");
                }
            });
        }

        // Switch Tipe Transfer (Saldo Xendit vs Saldo Manual)
        function selectTransferType(type) {
            if (!transferData) return;
            
            // Cegah pemilihan saluran yang dinonaktifkan
            if (type === 'xendit' && transferData.xendit_active === false) {
                Toast.fire({ icon: "warning", title: "Saluran Saldo Xendit sedang dinonaktifkan admin." });
                return;
            }
            if (type === 'manual' && transferData.manual_active === false) {
                Toast.fire({ icon: "warning", title: "Saluran Saldo Manual sedang dinonaktifkan admin." });
                return;
            }

            $("#tf_selected_type").val(type);

            if (type === 'xendit') {
                $("#radio_xendit").prop("checked", true);
                $("#radio_manual").prop("checked", false);
                $("#tf_card_xendit").addClass("selected");
                $("#tf_card_manual").removeClass("selected");
            } else {
                $("#radio_manual").prop("checked", true);
                $("#radio_xendit").prop("checked", false);
                $("#tf_card_manual").addClass("selected");
                $("#tf_card_xendit").removeClass("selected");
            }

            // Hitung kalkulasi berdasarkan tipe transfer terpilih (keduanya dikenakan potongan biaya admin)
            let adminFee = transferData.admin_fee || 0;
            let totalDeductions = (transferData.total_cash_advance || 0) + adminFee;
            let netSalary = Math.max(0, transferData.subtotal_income - totalDeductions);

            transferData.current_net_salary = netSalary;
            transferData.current_admin_fee = adminFee;
            transferData.current_total_deductions = totalDeductions;

            // Update UI Step 2
            if (type === 'xendit') {
                $("#tf_s2_method_badge").attr("class", "badge bg-azure text-white px-2 py-1").text("Saldo Xendit (Disbursement Otomatis)");
                $("#tf_admin_fee_label").text("• Biaya Admin Transfer (Xendit):");
                $("#tf_admin_fee").text('-' + formatRupiah(adminFee));
                $("#tf_admin_fee_row").show();
                $("#tf_net_salary_sub").text("(Gaji Pokok + Tunjangan - Total Kasbon - Biaya Admin)");
                $("#tf_bank_container").show();
                $("#tf_cash_container").hide();
            } else {
                $("#tf_s2_method_badge").attr("class", "badge bg-primary text-white px-2 py-1").text("Saldo Manual (Kas / Pembayaran Tunai)");
                $("#tf_admin_fee_label").text("• Biaya Admin Transfer (Manual):");
                $("#tf_admin_fee").text('-' + formatRupiah(adminFee));
                $("#tf_admin_fee_row").show();
                $("#tf_net_salary_sub").text("(Gaji Pokok + Tunjangan - Total Kasbon - Biaya Admin)");
                $("#tf_bank_container").hide();
                $("#tf_cash_container").show();
            }

            $("#tf_total_deductions").text('-' + formatRupiah(totalDeductions));
            $("#tf_net_salary").text(formatRupiah(netSalary));

            // Evaluasi kecukupan saldo masing-masing kantong (keduanya memperhitungkan admin fee)
            let xenditNet = Math.max(0, transferData.subtotal_income - (transferData.total_cash_advance || 0) - (transferData.admin_fee || 0));
            let manualNet = Math.max(0, transferData.subtotal_income - (transferData.total_cash_advance || 0) - (transferData.admin_fee || 0));

            let xenditEnough = (transferData.balance_xendit || 0) >= xenditNet;
            let manualEnough = (transferData.balance_manual || 0) >= manualNet;

            // Update Badge Status Xendit
            if (!transferData.xendit_active) {
                $("#tf_card_xendit").removeClass("is-insufficient").addClass("disabled");
                $("#tf_badge_status_xendit").html(`
                    <span class="badge bg-secondary-lt text-secondary d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 16h.01" /></svg>
                        Sedang Dinonaktifkan Admin
                    </span>
                `);
            } else if (xenditEnough) {
                $("#tf_card_xendit").removeClass("is-insufficient disabled");
                $("#tf_badge_status_xendit").html(`
                    <span class="badge bg-success-lt text-success d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        Saldo Mencukupi
                    </span>
                `);
            } else {
                $("#tf_card_xendit").removeClass("disabled").addClass("is-insufficient");
                $("#tf_badge_status_xendit").html(`
                    <span class="badge bg-danger-lt text-danger d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 16h.01" /></svg>
                        Kurang ${formatRupiah(xenditNet - (transferData.balance_xendit || 0))}
                    </span>
                `);
            }

            // Update Badge Status Manual
            if (!transferData.manual_active) {
                $("#tf_card_manual").removeClass("is-insufficient").addClass("disabled");
                $("#tf_badge_status_manual").html(`
                    <span class="badge bg-secondary-lt text-secondary d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 16h.01" /></svg>
                        Sedang Dinonaktifkan Admin
                    </span>
                `);
            } else if (manualEnough) {
                $("#tf_card_manual").removeClass("is-insufficient disabled");
                $("#tf_badge_status_manual").html(`
                    <span class="badge bg-success-lt text-success d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        Saldo Mencukupi
                    </span>
                `);
            } else {
                $("#tf_card_manual").removeClass("disabled").addClass("is-insufficient");
                $("#tf_badge_status_manual").html(`
                    <span class="badge bg-danger-lt text-danger d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 16h.01" /></svg>
                        Kurang ${formatRupiah(manualNet - (transferData.balance_manual || 0))}
                    </span>
                `);
            }

            // Validasi kantong yang sedang aktif dipilih
            let isCurrentActive = (type === 'xendit') ? transferData.xendit_active : transferData.manual_active;
            let isCurrentEnough = (type === 'xendit') ? xenditEnough : manualEnough;
            let currentRequired = (type === 'xendit') ? xenditNet : manualNet;
            let kantongName     = (type === 'xendit') ? 'Saldo Xendit' : 'Saldo Manual';

            if (!isCurrentActive) {
                $("#tf_insufficient_msg").html(`<strong>${kantongName}</strong> sedang dinonaktifkan admin dan tidak dapat dipilih.`);
                $("#tf_insufficient_alert").show();
                $("#btn-tf-next").prop("disabled", true);
            } else if (!isCurrentEnough) {
                let curBal = type === 'xendit' ? (transferData.balance_xendit || 0) : (transferData.balance_manual || 0);
                $("#tf_insufficient_msg").html(`<strong>${kantongName}</strong> tidak mencukupi! Sisa saldo: <strong>${formatRupiah(curBal)}</strong>, dibutuhkan: <strong>${formatRupiah(currentRequired)}</strong>.`);
                $("#tf_insufficient_alert").show();
                $("#btn-tf-next").prop("disabled", true);
            } else {
                $("#tf_insufficient_alert").hide();
                $("#btn-tf-next").prop("disabled", false);
            }
        }

        // Update Tampilan Wizard Step Modal Transfer
        function updateTransferWizardUI() {
            $(".tf-step-content").hide();
            $("#tf-step-" + currentTransferStep).fadeIn(200);

            // Update Stepper Bar items
            $("#tf-wizard-stepper-bar .wizard-step-item").each(function() {
                const stepNum = parseInt($(this).data("step"));
                $(this).removeClass("active completed");
                if (stepNum === currentTransferStep) {
                    $(this).addClass("active");
                } else if (stepNum < currentTransferStep) {
                    $(this).addClass("completed");
                }
            });

            // Subtitle header
            if (currentTransferStep === 1) {
                $("#tf-wizard-subtitle").text("Langkah 1: Pilih metode pembayaran & kantong saldo");
                $("#btn-tf-prev").hide();
                $("#btn-tf-next").show();
                $("#btn-tf-submit").hide();
            } else if (currentTransferStep === 2) {
                let isManual = $("#tf_selected_type").val() === 'manual';
                $("#tf-wizard-subtitle").text(isManual ? "Langkah 2: Periksa rincian kalkulasi gaji (Tunai / Kas)" : "Langkah 2: Periksa rincian kalkulasi gaji & rekening tujuan");
                $("#btn-tf-prev").show();
                $("#btn-tf-next").show();
                $("#btn-tf-submit").hide();
            } else if (currentTransferStep === 3) {
                $("#tf-wizard-subtitle").text("Langkah 3: Konfirmasi akhir & eksekusi transfer");
                $("#btn-tf-prev").show();
                $("#btn-tf-next").hide();
                $("#btn-tf-submit").show();
                populateTransferReview();
            }
        }

        function validateTransferStep(step) {
            if (!transferData) return false;
            let type = $("#tf_selected_type").val();

            if (step === 1) {
                let isXendit = (type === 'xendit');
                let isActive = isXendit ? transferData.xendit_active : transferData.manual_active;
                if (!isActive) {
                    Toast.fire({
                        icon: "error",
                        title: "Saluran " + (isXendit ? "Saldo Xendit" : "Saldo Manual") + " sedang dinonaktifkan oleh administrator."
                    });
                    return false;
                }

                let requiredAmount = transferData.current_net_salary;
                let availableBalance = isXendit ? (transferData.balance_xendit || 0) : (transferData.balance_manual || 0);

                if (availableBalance < requiredAmount) {
                    Toast.fire({
                        icon: "error",
                        title: "Saldo yang Anda pilih tidak mencukupi untuk transfer gaji ini."
                    });
                    return false;
                }
                return true;
            } else if (step === 2) {
                let bankName = $("#tf_bank_name").val();
                let accNum = $("#tf_account_number").val().trim();

                if (type === 'xendit') {
                    if (!bankName) {
                        Toast.fire({ icon: "warning", title: "Silakan pilih Bank tujuan transfer." });
                        return false;
                    }
                    if (!accNum) {
                        Toast.fire({ icon: "warning", title: "Nomor rekening tujuan wajib diisi." });
                        return false;
                    }
                }
                return true;
            }
            return true;
        }

        function nextTransferStep() {
            if (!validateTransferStep(currentTransferStep)) return;
            if (currentTransferStep < 3) {
                currentTransferStep++;
                updateTransferWizardUI();
            }
        }

        function prevTransferStep() {
            if (currentTransferStep > 1) {
                currentTransferStep--;
                updateTransferWizardUI();
            }
        }

        function goToTransferStep(step) {
            if (step > currentTransferStep) {
                for (let s = currentTransferStep; s < step; s++) {
                    if (!validateTransferStep(s)) return;
                }
            }
            currentTransferStep = step;
            updateTransferWizardUI();
        }

        // Mengisi Data Step 3 Review Akhir
        function populateTransferReview() {
            if (!transferData) return;
            let type = $("#tf_selected_type").val();
            let isXendit = (type === 'xendit');

            $("#tf_rev_user_name").text(transferData.user_name);
            $("#tf_rev_period").text(transferData.month_name);

            if (isXendit) {
                $("#tf_rev_method_badge").html('<span class="badge bg-azure text-white px-2 py-1">⚡ Saldo Xendit (Otomatis)</span>');
                $("#tf_rev_balance_name").text("Saldo Xendit");
                let initBal = transferData.balance_xendit || 0;
                let netSal = transferData.current_net_salary || 0;
                let remBal = Math.max(0, initBal - netSal);

                $("#tf_rev_initial_balance").text(formatRupiah(initBal));
                $("#tf_rev_deducted_amount").text('-' + formatRupiah(netSal));
                $("#tf_rev_remaining_balance").text(formatRupiah(remBal));

                let bankCode = $("#tf_bank_name").val() || '-';
                let accNum = $("#tf_account_number").val() || '-';
                let holder = $("#tf_account_holder_name").val() || transferData.user_name;
                $("#tf_rev_bank_info").text(`${bankCode.toUpperCase()} - ${accNum} (a.n ${holder})`);
                $("#tf_rev_bank_row").show();
                $("#tf_rev_cash_row").hide();

                $("#tf_rev_note_text").html(`Dana gaji bersih setelah dipotong biaya admin sebesar <strong>${formatRupiah(netSal)}</strong> akan <strong>langsung ditransfer via Xendit API</strong> ke rekening karyawan dan <strong>Saldo Xendit</strong> Web Finance akan dipotong.`);
            } else {
                $("#tf_rev_method_badge").html('<span class="badge bg-primary text-white px-2 py-1">💵 Saldo Manual (Kas / Tunai)</span>');
                $("#tf_rev_balance_name").text("Saldo Manual");
                let initBal = transferData.balance_manual || 0;
                let netSal = transferData.current_net_salary || 0;
                let remBal = Math.max(0, initBal - netSal);

                $("#tf_rev_initial_balance").text(formatRupiah(initBal));
                $("#tf_rev_deducted_amount").text('-' + formatRupiah(netSal));
                $("#tf_rev_remaining_balance").text(formatRupiah(remBal));

                $("#tf_rev_bank_row").hide();
                $("#tf_rev_cash_row").show();

                $("#tf_rev_note_text").html(`Status pembayaran akan <strong>langsung ditandai BERHASIL (Transferred)</strong> sebagai pembayaran tunai/kas dengan potongan biaya admin, dan <strong>Saldo Manual</strong> Web Finance akan dipotong sebesar nominal gaji bersih (<strong>${formatRupiah(netSal)}</strong>).`);
            }
        }

        // Submit transfer gaji (Xendit / Manual)
        function submitTransferGaji() {
            if (!transferData) return;
            let salaryId = $("#tf_salary_id").val();
            let transferType = $("#tf_selected_type").val();
            let bankName = $("#tf_bank_name").val();
            let accNumber = $("#tf_account_number").val().trim();
            let accHolder = $("#tf_account_holder_name").val().trim();
            let netSalaryText = formatRupiah(transferData.current_net_salary);
            let userName = transferData.user_name;
            let typeLabel = (transferType === 'xendit') ? 'Saldo Xendit (Otomatis)' : 'Saldo Manual (Kas / Tunai)';

            let isXendit = (transferType === 'xendit');
            if (isXendit && (!bankName || !accNumber)) {
                Toast.fire({
                    icon: "warning",
                    title: "Harap lengkapi Nama Bank dan Nomor Rekening tujuan transfer."
                });
                goToTransferStep(2);
                return;
            }

            let detailContent = isXendit 
                ? `<div><strong>Rekening Tujuan:</strong> ${bankName.toUpperCase()} - ${accNumber} (a.n ${accHolder || userName})</div>`
                : `<div><strong>Bentuk Pembayaran:</strong> Kas Operasional / Tunai Langsung</div>`;

            Swal.fire({
                title: "Konfirmasi Pembayaran Gaji",
                html: `
                    Anda akan memproses gaji bersih sebesar <strong>${netSalaryText}</strong> untuk <strong>${userName}</strong>.<br><br>
                    <div class="p-2 rounded bg-light border text-start small">
                        <div><strong>Jalur Pembayaran:</strong> ${typeLabel}</div>
                        ${detailContent}
                    </div>
                    <br><small class="text-muted">${isXendit ? 'Proses ini akan mengirim uang via Xendit & memotong Saldo Xendit.' : 'Proses ini akan mencatat transaksi tunai & memotong Saldo Manual.'}</small>
                `,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#2fb344",
                cancelButtonColor: "#d33",
                confirmButtonText: isXendit ? "Ya, Kirim Transfer Xendit" : "Ya, Catat & Potong Saldo Manual",
                cancelButtonText: "Batal",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: BASE + `/${salaryId}/process-transfer`,
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            transfer_type: transferType,
                            bank_name: bankName,
                            account_number: accNumber,
                            account_holder_name: accHolder,
                        },
                        dataType: "json"
                    }).then(response => {
                        return response;
                    }).catch(error => {
                        let msg = "Terjadi kesalahan pada server.";
                        if (error.responseJSON && error.responseJSON.message) {
                            msg = error.responseJSON.message;
                        }
                        Swal.showValidationMessage(msg);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    let res = result.value;
                    if (res.status) {
                        $("#modal-transfer-gaji").modal("hide");
                        Swal.fire({
                            icon: "success",
                            title: "Transfer Berhasil Diproses!",
                            html: `
                                <p class="mb-2">${res.message || 'Permintaan transfer gaji telah berhasil diproses.'}</p>
                                <div class="alert alert-info py-2 px-3 text-start small mb-0 mt-3" style="background-color: #f0f8ff; border: 1px solid #b9ddff; border-radius: 8px;">
                                    <div class="fw-bold mb-1">ℹ️ Informasi:</div>
                                    <ul class="ps-3 mb-0">
                                        <li>Status dapat dicek pada <strong>Riwayat Pembayaran</strong>.</li>
                                        <li>Slip gaji otomatis tercatat sesuai metode <strong>${typeLabel}</strong>.</li>
                                    </ul>
                                </div>
                            `,
                            timer: 4000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: res.message
                        });
                    }
                }
            });
        }
    </script>
@endpush