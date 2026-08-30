{{-- SISI KARYAWAN: ON ABSENSI AKTIF --}}
<div class="row row-cards g-3 mb-4">
    {{-- GAJI POKOK --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card-modern shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md rounded-3 bg-primary-lt text-primary me-3 flex-shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                    </span>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase">Gaji Pokok</div>
                        <div class="h3 mb-0 fw-bold text-dark">
                            Rp {{ number_format($baseSalary, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TUNJANGAN --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card-modern shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md rounded-3 bg-success-lt text-success me-3 flex-shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3v12" /><path d="M16 11l-4 4l-4 -4" /><path d="M3 12a9 9 0 0 0 9 9a9 9 0 0 0 9 -9" /></svg>
                    </span>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase">Total Tunjangan</div>
                        <div class="h3 mb-0 fw-bold text-success">
                            +Rp {{ number_format($totalAllowance, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KASBON --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card-modern shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md rounded-3 bg-danger-lt text-danger me-3 flex-shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M12 18h-7a2 2 0 0 1 -2 -2v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7" /><path d="M18 12h.01" /><path d="M6 12h.01" /><path d="M16 19h6" /></svg>
                    </span>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase">Potongan Kasbon</div>
                        <div class="h3 mb-0 fw-bold text-danger">
                            -Rp {{ number_format($cashAdvance, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POTONGAN ABSENSI --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card-modern shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md rounded-3 bg-warning-lt text-warning me-3 flex-shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8v4" /><path d="M12 16h.01" /><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /></svg>
                    </span>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase">Potongan Absensi</div>
                        <div class="h3 mb-0 fw-bold text-warning">
                            -Rp {{ number_format($currentAttendancePenalty ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Highlight Card Gaji Bersih Bulan Berjalan -->
<div class="card mb-4 border shadow-sm rounded-3 bg-light-lt">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-primary text-white fw-bold px-3 py-1">
                        Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                    </span>
                    @if(isset($currentPayment))
                        @if($currentPayment->status === 'transferred')
                            <span class="badge bg-success-lt text-success fw-bold px-3 py-1 d-inline-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Gaji Sudah Ditransfer
                            </span>
                        @elseif($currentPayment->status === 'pending')
                            <span class="badge bg-warning-lt text-warning fw-bold px-3 py-1 d-inline-flex align-items-center gap-1">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Transfer Sedang Diproses
                            </span>
                        @elseif($currentPayment->status === 'failed')
                            <span class="badge bg-danger-lt text-danger fw-bold px-3 py-1">
                                Gagal Transfer
                            </span>
                        @endif
                    @else
                        <span class="badge bg-secondary-lt text-secondary px-3 py-1">
                            Belum Ditransfer
                        </span>
                    @endif
                </div>
                <div class="text-secondary text-uppercase small fw-semibold" style="letter-spacing: 0.05em;">Estimasi Total Gaji Bersih Diterima</div>
                <div class="display-5 fw-bold text-primary mb-0 mt-1">
                    Rp {{ number_format($netSalary, 0, ',', '.') }}
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
                @if(isset($currentPayment) && in_array($currentPayment->status, ['transferred', 'pending']))
                    <a href="{{ route('salary.payment.invoice', $currentPayment->id) }}" target="_blank" class="btn btn-outline-success fw-bold px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 17h6" /><path d="M9 13h6" /></svg>
                        Bukti Transfer
                    </a>
                @endif
                <a href="{{ route('dashboard.print.slip', ['month' => now()->format('m'), 'year' => now()->format('Y')]) }}" target="_blank" class="btn btn-primary fw-bold px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Cetak Slip Bulan Ini
                </a>
            </div>
        </div>
    </div>
</div>

<!-- History Table Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12h6" /><path d="M9 16h6" /></svg>
                Riwayat & Arsip Slip Gaji Bulanan
            </h3>
            <p class="text-muted small mb-0">Rincian komponen gaji pokok, potongan absensi, kasbon, status transfer, dan unduh slip gaji</p>
        </div>
        <span class="badge bg-blue-lt px-3 py-1">Histori Gaji</span>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter table-hover text-nowrap">
            <thead>
                <tr class="bg-light text-muted small">
                    <th class="w-1">No</th>
                    <th>Periode Bulan</th>
                    <th>Tahun</th>
                    <th>Gaji Pokok</th>
                    <th>Tunjangan</th>
                    <th>Kasbon</th>
                    <th class="text-center">Alpa</th>
                    <th class="text-center">Izin/Cuti</th>
                    <th class="text-center">Telat</th>
                    <th>Total Gaji Bersih</th>
                    <th class="text-center">Status Transfer</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse ($salaryHistory as $item)
                    @php
                        $payment = $item['payment'] ?? null;
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $no++ }}</td>
                        <td class="fw-bold text-dark">
                            <span class="badge bg-azure-lt px-2 py-1">
                                {{ \Carbon\Carbon::create()->month($item['month'])->translatedFormat('F') }}
                            </span>
                        </td>
                        <td><span class="badge bg-secondary-lt">{{ $item['year'] }}</span></td>
                        <td>Rp {{ number_format($item['base_salary'], 0, ',', '.') }}</td>
                        <td class="text-success fw-medium">+Rp {{ number_format($item['allowance'], 0, ',', '.') }}</td>
                        <td class="text-danger fw-medium">
                            @if($item['cash_advance'] > 0)
                                -Rp {{ number_format($item['cash_advance'], 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item['alpha'] > 0)
                                <span class="badge bg-danger-lt">{{ $item['alpha'] }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item['cuti'] > 0)
                                <span class="badge bg-warning-lt">{{ $item['cuti'] }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item['telat'] > 0)
                                <span class="badge bg-secondary-lt">{{ $item['telat'] }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="fw-bold text-primary fs-6">
                            Rp {{ number_format($item['net_salary'], 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($payment)
                                @if($payment->status === 'transferred')
                                    <span class="badge bg-success-lt text-success fw-bold px-2 py-1 d-inline-flex align-items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                        Berhasil
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning-lt text-warning fw-bold px-2 py-1 d-inline-flex align-items-center gap-1">
                                        <span class="spinner-border spinner-border-sm me-1" style="width: 10px; height: 10px;" role="status"></span>
                                        Proses
                                    </span>
                                @elseif($payment->status === 'failed')
                                    <span class="badge bg-danger-lt text-danger fw-bold px-2 py-1">
                                        Gagal
                                    </span>
                                @else
                                    <span class="badge bg-secondary-lt text-secondary px-2 py-1">{{ ucfirst($payment->status) }}</span>
                                @endif
                            @else
                                <span class="badge bg-secondary-lt text-secondary px-2 py-1">
                                    Belum Ditransfer
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-list flex-nowrap justify-content-center gap-1">
                                @if($payment && in_array($payment->status, ['transferred', 'pending']))
                                    <a href="{{ route('salary.payment.invoice', $payment->id) }}" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1 d-inline-flex align-items-center gap-1" title="Lihat Bukti Transfer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 17h6" /><path d="M9 13h6" /></svg>
                                        Bukti Transfer
                                    </a>
                                @endif
                                <a href="{{ route('dashboard.print.slip', ['month' => $item['month'], 'year' => $item['year']]) }}" class="btn btn-sm btn-outline-primary px-2 py-1 d-inline-flex align-items-center gap-1" target="_blank" title="Cetak Slip Gaji">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                                    Cetak Slip
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-4 text-muted">
                            Belum ada riwayat penggajian yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
