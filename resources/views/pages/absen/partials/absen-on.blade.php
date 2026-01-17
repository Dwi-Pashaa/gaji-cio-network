<style>
    .calendar-container {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .calendar-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .calendar-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .calendar-table {
        width: 100%;
        margin: 0;
    }
    
    .calendar-table thead th {
        background: #f8f9fa;
        padding: 12px 8px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #495057;
        border: 1px solid #dee2e6;
    }
    
    .calendar-table thead th.weekend {
        background: #fff3cd;
        color: #856404;
    }
    
    .calendar-table tbody td {
        height: 100px;
        vertical-align: top;
        padding: 8px;
        border: 1px solid #dee2e6;
        position: relative;
        background: #fff;
    }
    
    .calendar-table tbody td:first-child {
        background: #f8f9fa;
        font-weight: 600;
        vertical-align: middle;
        text-align: center;
    }
    
    .calendar-date {
        font-weight: 600;
        font-size: 1.1rem;
        color: #495057;
        margin-bottom: 8px;
        display: block;
    }
    
    .calendar-day-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    
    /* Badge Styles */
    .badge-calendar {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }
    
    .badge-time {
        display: block;
        font-size: 0.65rem;
        font-weight: 500;
        margin-top: 2px;
        opacity: 0.9;
    }
    
    .badge-libur {
        background: #fff3cd;
        color: #856404;
        border: 1px dashed #ffc107;
    }
    
    .badge-belum-berlaku {
        background: #e9ecef;
        color: #6c757d;
    }
    
    .badge-belum-datang {
        background: #cfe2ff;
        color: #084298;
    }
    
    .badge-cuti {
        background: #fff3cd;
        color: #997404;
    }
    
    .badge-hadir {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .badge-terlambat {
        background: #fff3cd;
        color: #997404;
    }
    
    .badge-tidak-hadir {
        background: #f8d7da;
        color: #842029;
    }
    
    .badge-silahkan-absen {
        background: #0d6efd;
        color: white;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
    
    /* Weekend Cell */
    .weekend-cell {
        background: #fffbf0 !important;
    }
    
    /* Today Highlight */
    .today-cell {
        background: #fff9e6 !important;
        border: 2px solid #ffc107 !important;
    }
    
    .today-cell .calendar-date {
        color: #f59f00;
    }
    
    /* Rekap Statistics */
    .stats-container {
        background: #f8f9fa;
        padding: 20px;
        border-top: 1px solid #dee2e6;
    }
    
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #495057;
    }
    
    .stat-card.total .stat-value { color: #6c757d; }
    .stat-card.hadir .stat-value { color: #198754; }
    .stat-card.terlambat .stat-value { color: #ffc107; }
    .stat-card.cuti .stat-value { color: #fd7e14; }
    .stat-card.tidak-hadir .stat-value { color: #dc3545; }
    
    .persentase-kehadiran {
        margin-top: 20px;
        padding: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        color: white;
        text-align: center;
    }
    
    .persentase-kehadiran .label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 4px;
    }
    
    .persentase-kehadiran .value {
        font-size: 2.5rem;
        font-weight: 700;
    }
    
    /* Empty State */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }
    
    .empty-state svg {
        width: 80px;
        height: 80px;
        color: #adb5bd;
        margin-bottom: 16px;
    }
    
    .empty-state h4 {
        color: #6c757d;
        margin-bottom: 8px;
    }
    
    .empty-state p {
        color: #adb5bd;
        font-size: 0.95rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .calendar-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .calendar-header .btn {
            width: 100%;
        }
        
        .calendar-table tbody td {
            height: 80px;
            padding: 4px;
            font-size: 0.8rem;
        }
        
        .calendar-date {
            font-size: 0.9rem;
            margin-bottom: 4px;
        }
        
        .badge-calendar {
            font-size: 0.6rem;
            padding: 4px 6px;
            min-width: 60px;
        }
        
        .badge-time {
            font-size: 0.55rem;
            margin-top: 1px;
        }
        
        .stat-value {
            font-size: 1.5rem;
        }
        
        .persentase-kehadiran .value {
            font-size: 2rem;
        }
    }
</style>

<div class="calendar-container">
    <div class="calendar-header">
        <h3>
            📅 Kalender Absensi - {{ date('F Y', strtotime("$tahun-$bulan-01")) }}
        </h3>
        @if (count($workdays) > 0)
            <a href="javascript:void(0)" class="btn btn-light btn-lg" onclick="absenToday()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Absen Hari Ini
            </a>
        @endif
    </div>

    @if (count($workdays) > 0)

    @php
        $tanggalSekarang = 1;
        $minggu = 0;

        $jumlahHari = date('t', strtotime("$tahun-$bulan-01"));
        $hariPertama = date('N', strtotime("$tahun-$bulan-01"));

        $today = date('Y-m-d');
        $attendanceStart = \Carbon\Carbon::parse($attendanceStartDate)->toDateString();

        $attendancesByMonth = collect($attendancesByMonth ?? $attandance ?? [])->keyBy('date');
    @endphp

    <div class="table-responsive">
        <table class="calendar-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 80px;">Minggu</th>
                    <th class="text-center">Senin</th>
                    <th class="text-center">Selasa</th>
                    <th class="text-center">Rabu</th>
                    <th class="text-center">Kamis</th>
                    <th class="text-center">Jumat</th>
                    <th class="text-center">Sabtu</th>
                    <th class="text-center">Minggu</th>
                </tr>
            </thead>

            <tbody>
            @while($tanggalSekarang <= $jumlahHari)
                <tr>
                    <td>
                        <span class="badge bg-primary text-white">Minggu Ke - {{ ++$minggu }}</span>
                    </td>

                    @for($hari = 1; $hari <= 7; $hari++)
                        @php
                            $weekday = $hari % 7;
                        @endphp

                        <td @if($minggu == 1 && $hari < $hariPertama) class="bg-light" @elseif($tanggalSekarang <= $jumlahHari)
                            @php
                                $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $tanggalSekarang);
                                $isToday = $tanggal == $today;
                                $weekday = $hari % 7;
                                $isWorkday = in_array($weekday, $workdays);
                            @endphp
                            @if($isToday) 
                                class="today-cell" 
                            @elseif(!$isWorkday)
                                class="weekend-cell"
                            @endif
                        @endif>
                        
                        @if($minggu == 1 && $hari < $hariPertama)
                            <span class="text-muted">-</span>
                        @elseif($tanggalSekarang <= $jumlahHari)
                            @php
                                $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $tanggalSekarang);
                                $attendance = $attendancesByMonth[$tanggal] ?? null;
                                $isWorkday = in_array($weekday, $workdays);
                                $isToday = $tanggal == $today;
                                $isFuture = $tanggal > $today;
                            @endphp

                            <div class="calendar-day-content">
                                <span class="calendar-date">{{ $tanggalSekarang }}</span>

                                {{-- ================= LOGIKA UTAMA ================= --}}

                                @if(!$isWorkday)
                                    <span class="badge-calendar badge-libur">
                                        📅 Hari Libur
                                    </span>

                                {{-- sebelum masa absensi --}}
                                @elseif($tanggal < $attendanceStart)
                                    <span class="badge-calendar badge-belum-berlaku">Belum Berlaku</span>

                                {{-- ADA ABSENSI (cek dulu sebelum masa depan) --}}
                                @elseif($attendance)

                                    @if(in_array($attendance->status, ['izin','cuti']))
                                        <span class="badge-calendar badge-cuti">
                                            ✉️ Cuti/Izin
                                        </span>

                                    @elseif($attendance->status === 'hadir')
                                        <span class="badge-calendar badge-hadir">
                                            ✓ Hadir
                                            @if($attendance->check_in)
                                                <span class="badge-time">{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</span>
                                            @endif
                                        </span>

                                    @elseif($attendance->status === 'terlambat')
                                        <span class="badge-calendar badge-terlambat">
                                            ⏰ Terlambat
                                            @if($attendance->check_in)
                                                <span class="badge-time">{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</span>
                                            @endif
                                        </span>

                                    @else
                                        <span class="badge-calendar badge-tidak-hadir">
                                            ✗ Tidak Hadir
                                        </span>
                                    @endif

                                {{-- masa depan (setelah cek absensi) --}}
                                @elseif($isFuture)
                                    <span class="badge-calendar badge-belum-datang">Belum Datang</span>

                                {{-- hari ini belum absen --}}
                                @elseif($isToday)
                                    <span class="badge-calendar badge-silahkan-absen">
                                        👆 Silahkan Absen
                                    </span>

                                {{-- hari kerja lewat tanpa absen --}}
                                @else
                                    <span class="badge-calendar badge-tidak-hadir">
                                        ✗ Tidak Hadir
                                    </span>
                                @endif
                            </div>

                            @php $tanggalSekarang++; @endphp
                        @endif
                        </td>
                    @endfor
                </tr>
            @endwhile
            </tbody>
        </table>
    </div>

    {{-- ================= REKAP ================= --}}
    @php
        $hariKerja = $hadir = $terlambat = $cuti = $tidakHadir = 0;

        for ($d = 1; $d <= $jumlahHari; $d++) {

            $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
            $weekday = date('w', strtotime($tanggal));

            // Skip jika bukan hari kerja atau belum berlaku
            if (!in_array($weekday, $workdays)) continue;
            if ($tanggal < $attendanceStart) continue;

            $hariKerja++;

            $attendance = $attendancesByMonth[$tanggal] ?? null;

            // Hitung cuti/izin meskipun di masa depan
            if ($attendance && in_array($attendance->status, ['izin','cuti'])) {
                $cuti++;
                continue; // Skip ke tanggal berikutnya
            }

            // Skip perhitungan lain jika masa depan
            if ($tanggal > $today) continue;

            // Skip jika hari ini tapi belum ada absensi
            if ($tanggal == $today && !$attendance) continue;

            // Hitung status absensi
            if (!$attendance) {
                $tidakHadir++;
            } elseif ($attendance->status === 'terlambat') {
                $terlambat++;
            } elseif ($attendance->status === 'hadir') {
                $hadir++;
            }
        }

        $persenKehadiran = $hariKerja > 0
            ? round(($hadir / $hariKerja) * 100, 1)
            : 0;
    @endphp

    <div class="stats-container">
        <div class="row g-3">
            <div class="col-6 col-md">
                <div class="stat-card total">
                    <div class="stat-label">Hari Kerja</div>
                    <div class="stat-value">{{ $hariKerja }}</div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card hadir">
                    <div class="stat-label">Hadir</div>
                    <div class="stat-value">{{ $hadir }}</div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card terlambat">
                    <div class="stat-label">Terlambat</div>
                    <div class="stat-value">{{ $terlambat }}</div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card cuti">
                    <div class="stat-label">Cuti</div>
                    <div class="stat-value">{{ $cuti }}</div>
                </div>
            </div>
            <div class="col-12 col-md">
                <div class="stat-card tidak-hadir">
                    <div class="stat-label">Tidak Hadir</div>
                    <div class="stat-value">{{ $tidakHadir }}</div>
                </div>
            </div>
        </div>

        <div class="persentase-kehadiran">
            <div class="label">Persentase Kehadiran</div>
            <div class="value">{{ $persenKehadiran }}%</div>
        </div>
    </div>

    @else
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <h4>Hari Kerja Belum Diatur</h4>
        <p>Silakan hubungi administrator untuk mengatur hari kerja</p>
    </div>
    @endif
</div>