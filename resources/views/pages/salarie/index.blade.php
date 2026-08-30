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
                                <div class="btn-list flex-nowrap justify-content-center">
                                    <a href="javascript:void(0)" onclick="return openTransferModal('{{ $item->id }}')" class="btn btn-action-custom btn-success shadow-sm" title="Kalkulasi & Transfer Gaji via Xendit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M18 12l.01 0" /><path d="M6 12l.01 0" /></svg>
                                        Transfer Gaji
                                    </a>
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
    <div class="modal modal-blur fade" id="modal-simple" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-1 modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Gaji Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <form id="form">
                    <div class="modal-body">
                        <input type="hidden" name="type" id="type">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Pilih Karyawan/Pegawai</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">Pilih</option>
                                        @foreach ($user as $usr)
                                            <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error_user_id"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Jumlah Gaji Pokok</label>
                                    <input type="text" name="base_salary" id="base_salary" class="form-control">
                                    <span class="text-danger error_base_salary"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Tunjangan</label>
                                    <div id="tunjangan-wrapper">
                                        <div class="input-group mb-2 tunjangan-item">
                                            <select name="allowance_id[]" id="allowance_id" class="form-control">
                                                <option value="">Tidak Mempunyai Tunjangan</option>
                                                @foreach ($allowance as $alw)
                                                    <option value="{{ $alw->id }}">{{ $alw->name }} - Rp. {{ number_format($alw->amount) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="add-tunjangan" class="btn btn-primary">Tambah</button>
                                        </div>
                                    </div>
                                    <span class="text-danger error_allowance_id"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Tanggal Aktif Gaji</label>
                                    <input type="date" name="effective_date" id="effective_date" class="form-control">
                                    <span class="text-danger error_effective_date"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Status Gaji</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Pilih</option>
                                        @php
                                            $status = ["active", "inactive"];
                                        @endphp
                                        @foreach ($status as $st)
                                            <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error_status"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kalkulasi & Transfer Gaji via Xendit -->
    <div class="modal modal-blur fade" id="modal-transfer-gaji" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M18 12l.01 0" /><path d="M6 12l.01 0" /></svg>
                        Konfirmasi & Rincian Transfer Gaji Karyawan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div id="transfer-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted mt-2">Menghitung gaji, tunjangan, & potongan kasbon...</div>
                </div>

                <form id="form-transfer-gaji" style="display:none;">
                    <input type="hidden" id="tf_salary_id">
                    <div class="modal-body p-4">
                        <!-- Employee & Saldo Card -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Penerima Gaji</div>
                                        <div class="h3 mb-0 text-primary fw-bold" id="tf_user_name">-</div>
                                        <div class="small text-muted" id="tf_period_name">Periode: -</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small text-uppercase fw-semibold">Saldo Web Slip Saat Ini</div>
                                        <div class="h4 mb-0 fw-bold text-success" id="tf_current_balance">Rp 0</div>
                                    </div>
                                </div>
                            </div>
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

                            <!-- Kolom Kanan: Potongan Kasbon -->
                            <div class="col-md-6">
                                <div class="card h-100 border-danger-subtle border">
                                    <div class="card-header bg-danger-lt py-2">
                                        <strong class="text-danger small text-uppercase">
                                            (-) Potongan Kasbon Bulan Ini
                                        </strong>
                                    </div>
                                    <div class="card-body p-3">
                                        <div id="tf_cash_advance_list">
                                            <!-- List kasbon injected by JS -->
                                        </div>
                                        <div class="d-flex justify-content-between pt-2 mt-2 border-top fw-bold text-danger">
                                            <span>Total Potongan Kasbon:</span>
                                            <span id="tf_total_cash_advance">-Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Bersih Banner -->
                        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%); border-radius: 12px; padding: 1.25rem 1.5rem; color: #ffffff; box-shadow: 0 4px 15px rgba(29, 78, 216, 0.25);" class="mt-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div style="color: rgba(255,255,255,0.75); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Nominal Gaji Bersih Siap Ditransfer</div>
                                    <div style="color: #ffffff; font-size: 0.85rem; margin-top: 2px;">(Gaji Pokok + Tunjangan - Kasbon)</div>
                                </div>
                                <div class="text-end">
                                    <div style="color: #ffffff; font-size: 1.85rem; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.2);" id="tf_net_salary">Rp 0</div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Rekening Penerima -->
                        <div class="mt-3">
                            <label class="form-label fw-bold text-dark mb-2">Informasi Rekening Bank Tujuan Transfer (Xendit)</label>
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
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" id="btn-submit-transfer" onclick="submitTransferGaji()" class="btn btn-success d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /></svg>
                            Kirim Transfer Gaji Sekarang
                        </button>
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
            <option value="">Pilih</option>
            @foreach ($allowance as $alw)
                <option value="{{ $alw->id }}">
                    {{ $alw->name }} - Rp. {{ number_format($alw->amount) }}
                </option>
            @endforeach
        `;

        document.addEventListener("DOMContentLoaded", function() {
            const wrapper = document.getElementById("tunjangan-wrapper");

            wrapper.addEventListener("click", function(e) {
                if (e.target && e.target.id === "add-tunjangan") {
                    let div = document.createElement("div");
                    div.classList.add("input-group", "mb-2", "tunjangan-item");
                    div.innerHTML = `
                        <select name="allowance_id[]" class="form-control">
                            <option value="">Tidak Mempunyai Tunjangan</option>
                            ${allowanceOptions} 
                        </select>
                        <button type="button" class="btn btn-danger remove-tunjangan">Hapus</button>
                    `;

                    if (wrapper.contains(e.target)) {
                        wrapper.appendChild(div, e.target);
                    } else {
                        wrapper.appendChild(div);
                    }
                }

                if (e.target && e.target.classList.contains("remove-tunjangan")) {
                    e.target.closest(".tunjangan-item").remove();
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("base_salary");

            input.addEventListener("input", function(e) {
                let value = this.value.replace(/\D/g, "");
                
                if (value) {
                    this.value = new Intl.NumberFormat("id-ID").format(value);
                } else {
                    this.value = "";
                }
            });
        });

        $("#addBtn").click(function() {
            $(".modal-title").html("Buat Gaji Karyawan/Pegawai");
            $("#type").val("create");
            $("#id").val("");
            $("#user_id").val("");
            $("#base_salary").val("");
            $("#allowance_id").val("");
            $("#effective_date").val("");
            $("#status").val("");
        });

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

            $.ajax({
                url: url,
                method: method,
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(response) {
                if (response.errors) {
                    $.each(response.errors, function(index, value) {
                        let inputField = $("[name='" + index + "']");
                        inputField.addClass("is-invalid");
                        $(".error_" + index).html(value);

                        setTimeout(() => {
                            inputField.removeClass("is-invalid");
                            $(".error_" + index).html("");
                        }, 3000);
                    });
                } else {
                    $("#modal-simple").modal("hide");
                    Toast.fire({
                        icon: "success",
                        title: response.message
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("Error:", textStatus, errorThrown);
            });
        });

        function editModal(id) {
            let url = BASE + `/${id}/show`;
            $.ajax({
                url: url,
                method: "GET",
                dataType: "json"
            }).done(function(response){
                $(".modal-title").html("Edit Gaji Pegawai");
                $("#type").val("update");
                let data = response.data;
                $("#modal-simple").modal('show');

                $("#id").val(data.id);
                $("#user_id").val(data.user.id);
                $("#effective_date").val(data.effective_date);
                $("#status").val(data.status);

                let formattedSalary = new Intl.NumberFormat('id-ID').format(data.base_salary);
                $("#base_salary").val(formattedSalary);

                let wrapper = document.getElementById("tunjangan-wrapper");
                wrapper.innerHTML = "";

                if (data.user.allowance && data.user.allowance.length > 0) {
                    data.user.allowance.forEach(alw => {
                        let div = document.createElement("div");
                        div.classList.add("input-group", "mb-2", "tunjangan-item");
                        div.innerHTML = `
                            <select name="allowance_id[]" class="form-control">
                                <option value="">Tidak Mempunyai Tunjangan</option>
                                ${allowanceOptions}
                            </select>
                            <button type="button" class="btn btn-danger remove-tunjangan">Hapus</button>
                        `;
                        div.querySelector("select").value = alw.id;
                        wrapper.appendChild(div);
                    });
                    let addBtn = document.createElement("button");
                    addBtn.type = "button";
                    addBtn.id = "add-tunjangan";
                    addBtn.classList.add("btn", "btn-primary", "mt-2");
                    addBtn.textContent = "Tambah";
                    wrapper.appendChild(addBtn);

                } else {
                    let div = document.createElement("div");
                    div.classList.add("input-group", "mb-2", "tunjangan-item");
                    div.innerHTML = `
                        <select name="allowance_id[]" class="form-control">
                            <option value="">Tidak Mempunyai Tunjangan</option>
                            ${allowanceOptions}
                        </select>
                        <button type="button" id="add-tunjangan" class="btn btn-primary mb-2">Tambah</button>
                    `;
                    wrapper.appendChild(div);
                }
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

        // Buka modal kalkulasi transfer gaji
        function openTransferModal(salaryId) {
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
                    $("#form-transfer-gaji").show();

                    // Info Header
                    $("#tf_user_name").text(d.user_name);
                    $("#tf_period_name").text("Periode: " + d.month_name);
                    $("#tf_current_balance").text(d.current_balance !== null ? formatRupiah(d.current_balance) : 'Tidak terhubung API');

                    // Gaji Pokok & Tunjangan
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
                    let subtotalIncome = d.base_salary + d.total_allowance;
                    $("#tf_subtotal_income").text(formatRupiah(subtotalIncome));

                    // Kasbon
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
                    $("#tf_total_cash_advance").text('-' + formatRupiah(d.total_cash_advance));

                    // Total Bersih Siap Transfer
                    $("#tf_net_salary").text(formatRupiah(d.net_salary));

                    // Simpan data saldo dan nominal transfer di atribut modal
                    $("#modal-transfer-gaji").data("current_balance", d.current_balance);
                    $("#modal-transfer-gaji").data("net_salary", d.net_salary);

                    // Cek jika saldo website kurang
                    let btnTransfer = $("#btn-submit-transfer");
                    if (d.current_balance !== null && d.current_balance < d.net_salary) {
                        btnTransfer.prop("disabled", true);
                        btnTransfer.removeClass("btn-success").addClass("btn-danger");
                        btnTransfer.html(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                            Saldo Website Kurang (${formatRupiah(d.current_balance)})
                        `);
                        Toast.fire({
                            icon: "warning",
                            title: "Saldo Website Tidak Cukup untuk transfer gaji ini!"
                        });
                    } else {
                        btnTransfer.prop("disabled", false);
                        btnTransfer.removeClass("btn-danger").addClass("btn-success");
                        btnTransfer.html(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /></svg>
                            Kirim Transfer Gaji Sekarang
                        `);
                    }

                    // Pre-fill Rekening Bank
                    if (d.bank_name) {
                        $("#tf_bank_name").val(d.bank_name);
                    }
                    $("#tf_account_number").val(d.account_number || '');
                    $("#tf_account_holder_name").val(d.account_holder_name || d.user_name || '');
                },
                error: function(xhr) {
                    $("#transfer-loading").hide();
                    Toast.fire({ icon: "error", title: "Gagal memuat rincian kalkulasi gaji." });
                    $("#modal-transfer-gaji").modal("hide");
                }
            });
        }

        // Submit transfer gaji ke Xendit
        function submitTransferGaji() {
            let salaryId = $("#tf_salary_id").val();
            let bankName = $("#tf_bank_name").val();
            let accNumber = $("#tf_account_number").val().trim();
            let accHolder = $("#tf_account_holder_name").val().trim();
            let netSalaryText = $("#tf_net_salary").text();
            let userName = $("#tf_user_name").text();

            let currentBal = $("#modal-transfer-gaji").data("current_balance");
            let netSalVal  = $("#modal-transfer-gaji").data("net_salary");

            // VALIDASI SISI CLIENT: Tolak jika saldo website kurang
            if (currentBal !== null && currentBal !== undefined && currentBal < netSalVal) {
                Swal.fire({
                    icon: "error",
                    title: "Saldo Website Tidak Cukup",
                    html: `Saldo website saat ini: <strong>${formatRupiah(currentBal)}</strong><br>Nominal transfer yang dibutuhkan: <strong>${formatRupiah(netSalVal)}</strong><br><br><span class="text-danger">Transfer gaji dibatalkan dan tidak dikirim ke Xendit.</span>`,
                });
                return;
            }

            if (!bankName || !accNumber) {
                Toast.fire({
                    icon: "warning",
                    title: "Harap lengkapi Nama Bank dan Nomor Rekening tujuan transfer."
                });
                return;
            }

            Swal.fire({
                title: "Konfirmasi Transfer Gaji",
                html: `Anda akan mentransfer gaji bersih sebesar <strong>${netSalaryText}</strong> ke rekening <strong>${bankName} ${accNumber}</strong> a/n <strong>${accHolder}</strong> (${userName}).<br><br><small class="text-muted">Proses ini akan mengirim uang via Xendit & memotong saldo website.</small>`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#2fb344",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Kirim Transfer",
                cancelButtonText: "Batal",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: BASE + `/${salaryId}/process-transfer`,
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
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
                            title: "Transfer Berhasil Dikirim!",
                            html: `
                                <p class="mb-2">${res.message || 'Permintaan transfer gaji telah berhasil dikirim.'}</p>
                                <div class="alert alert-info py-2 px-3 text-start small mb-0 mt-3" style="background-color: #f0f8ff; border: 1px solid #b9ddff; border-radius: 8px;">
                                    <div class="fw-bold mb-1">ℹ️ Informasi Penting:</div>
                                    <ul class="ps-3 mb-0">
                                        <li>Silahkan cek status pada <strong>Riwayat Pembayaran</strong> secara berkala.</li>
                                        <li>Jika transfer bank <strong>gagal</strong>, sistem akan <strong>otomatis mengembalikan (refund) saldo website</strong> Anda.</li>
                                    </ul>
                                </div>
                            `,
                            timer: 5000,
                            showConfirmButton: false
                        });

                        if (res.wa_link_user) {
                            setTimeout(() => {
                                window.open(res.wa_link_user, '_blank');
                            }, 1000);
                        }

                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
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