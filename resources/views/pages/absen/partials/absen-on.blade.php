<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <b>
                Kalender Absensi Bulan - {{ date('F Y', strtotime("$tahun-$bulan-01")) }}
            </b>
        </h3>
        @if (count($workdays) > 0)
            <a href="javascript:void(0)" class="btn btn-primary" onclick="absenToday()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 11l3 3l8 -8"></path>
                    <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"></path>
                </svg>
                Absen Hari Ini
            </a>
        @endif
    </div>

    @if (count($workdays) > 0)
        @php
            $tanggalSekarang = 1;
            $minggu = 0;

            $bulan ??= date('m');
            $tahun ??= date('Y');
            $jumlahHari ??= date('t', strtotime("$tahun-$bulan-01"));
            $hariPertama ??= date('N', strtotime("$tahun-$bulan-01"));
            
            // Tanggal hari ini untuk perbandingan
            $today = date('Y-m-d');

            // pastikan absensi keyed by date
            $attendancesByMonth = $attendancesByMonth ?? collect($attandance ?? [])->keyBy('date');
        @endphp

        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr class="bg-light">
                        <th class="text-center fw-bold" style="width: 100px;">Minggu Ke</th>
                        <th class="text-center fw-bold">Senin</th>
                        <th class="text-center fw-bold">Selasa</th>
                        <th class="text-center fw-bold">Rabu</th>
                        <th class="text-center fw-bold">Kamis</th>
                        <th class="text-center fw-bold">Jumat</th>
                        <th class="text-center fw-bold bg-azure-lt">Sabtu</th>
                        <th class="text-center fw-bold bg-azure-lt">Minggu</th>
                    </tr>
                </thead>

                <tbody>
                @while($tanggalSekarang <= $jumlahHari)
                    <tr>
                        <td class="text-center align-middle bg-light">
                            <strong class="text-primary">Minggu {{ ++$minggu }}</strong>
                        </td>

                        @for($hari = 1; $hari <= 7; $hari++)
                            @php
                                // 0 = Minggu ... 6 = Sabtu
                                $weekday = $hari % 7;
                            @endphp

                            <td class="text-center align-middle {{ $hari >= 6 ? 'bg-azure-lt' : '' }}" style="min-width: 120px; height: 100px;">
                                {{-- sebelum tanggal 1 --}}
                                @if($minggu == 1 && $hari < $hariPertama)
                                    <div class="text-muted">-</div>

                                {{-- tanggal berjalan --}}
                                @elseif($tanggalSekarang <= $jumlahHari && ($minggu > 1 || $hari >= $hariPertama))
                                    @php
                                        $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $tanggalSekarang);
                                        $attendance = $attendancesByMonth[$tanggal] ?? null;
                                        $isWorkday = in_array($weekday, $workdays);
                                        $isToday = $tanggal == $today;
                                        $isFuture = $tanggal > $today; // Cek apakah tanggal di masa depan
                                        $isPast = $tanggal < $today; // Cek apakah tanggal sudah lewat
                                    @endphp

                                    <div class="p-2">
                                        <!-- Tanggal -->
                                        <div class="mb-2">
                                            <span class="badge {{ $isToday ? 'bg-primary' : ($isFuture ? 'bg-secondary' : 'bg-dark') }} text-white fs-6">
                                                {{ $tanggalSekarang }}
                                            </span>
                                        </div>

                                        {{-- HARI KERJA --}}
                                        @if($isWorkday)
                                            
                                            {{-- TANGGAL BELUM DATANG --}}
                                            @if($isFuture)
                                                <div class="text-muted">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <circle cx="12" cy="12" r="9"></circle>
                                                        <polyline points="12 7 12 12 15 15"></polyline>
                                                    </svg>
                                                </div>
                                                <span class="badge bg-info text-white w-100">Belum Datang</span>
                                            
                                            {{-- ADA ABSEN --}}
                                            @elseif($attendance)
                                                <div class="small text-start">
                                                    <div class="p-2 text-center border rounded mb-2">
                                                        <b>
                                                            Jam Masuk : 
                                                            {{ $attendance->check_in ?? '-' }}
                                                        </b>
                                                    </div>

                                                    @if($attendance->status == 'hadir')
                                                        <span class="badge bg-success text-white w-100">Hadir</span>
                                                    @elseif($attendance->status == 'terlambat')
                                                        <span class="badge bg-warning text-white w-100">Terlambat</span>
                                                    @else
                                                        <span class="badge bg-danger text-white w-100">Tidak Hadir</span>
                                                    @endif
                                                </div>

                                            {{-- HARI INI BELUM ABSEN --}}
                                            @elseif($isToday)
                                                <div>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary mb-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M12 9v4"></path>
                                                        <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                                        <path d="M12 16h.01"></path>
                                                    </svg>
                                                    <div>
                                                        <span class="badge bg-primary text-white w-100">Silahkan Absen</span>
                                                    </div>
                                                </div>

                                            {{-- HARI KERJA SUDAH LEWAT TAPI TIDAK ABSEN --}}
                                            @elseif($isPast)
                                                <div>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger mb-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                    <div>
                                                        <span class="badge bg-danger text-white w-100">Tidak Hadir</span>
                                                    </div>
                                                </div>
                                            @endif

                                        {{-- BUKAN HARI KERJA --}}
                                        @else
                                            <div class="text-muted">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                                </svg>
                                            </div>
                                            <span class="badge bg-secondary text-white w-100">Libur</span>
                                        @endif
                                    </div>

                                    @php $tanggalSekarang++; @endphp

                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endwhile
                </tbody>
            </table>
        </div>

        @php
            $hariKerja   = 0;
            $hadir       = 0;
            $terlambat   = 0;
            $tidakHadir  = 0;

            for ($d = 1; $d <= $jumlahHari; $d++) {

                $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                $weekday = date('w', strtotime($tanggal));   // 0–6

                // 👉 lewati jika bukan hari kerja user
                if (!in_array($weekday, $workdays)) {
                    continue;
                }

                // **SETIAP hari kerja dihitung sebagai hari kerja**
                $hariKerja++;

                // 👉 Kalau tanggal di masa depan — tidak dinilai (belum saatnya)
                if ($tanggal > $today) {
                    continue;
                }

                // Ambil absensi (kalau ada)
                $attendance = $attendancesByMonth[$tanggal] ?? null;

                // 👉 Jika hari ini & belum absen — JANGAN dihitung tidak hadir
                if ($tanggal == $today && !$attendance) {
                    continue;
                }

                // 👉 Tidak ada absen (hari kerja lewat) → TIDAK HADIR
                if (!$attendance) {
                    $tidakHadir++;
                    continue;
                }

                // 👉 Ada absen → cek status
                if ($attendance->status == 'terlambat') {
                    $terlambat++;
                } elseif ($attendance->status == 'hadir') {
                    $hadir++;
                }
            }

            // persentase
            $persenKehadiran = $hariKerja > 0
                ? round(($hadir / $hariKerja) * 100, 1)
                : 0;
        @endphp

        <div class="card-footer">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-sm bg-light">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold">{{ $hariKerja }}</div>
                            <div class="text-muted small mt-1">Hari Kerja</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-sm bg-success-lt">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold text-success">{{ $hadir }}</div>
                            <div class="text-muted small mt-1">Hadir ({{ $persenKehadiran }}%)</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-sm bg-warning-lt">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold text-warning">{{ $terlambat }}</div>
                            <div class="text-muted small mt-1">Terlambat</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-sm bg-danger-lt">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold text-danger">{{ $tidakHadir }}</div>
                            <div class="text-muted small mt-1">Tidak Hadir</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card-body">
            <div class="empty">
                <div class="empty-img">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-off" width="120" height="120" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M19.823 19.824a2 2 0 0 1 -1.823 1.176h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 1.175 -1.823m3.825 -.177h9a2 2 0 0 1 2 2v9" />
                        <path d="M16 3v4" />
                        <path d="M8 3v1" />
                        <path d="M4 11h7m4 0h5" />
                        <path d="M3 3l18 18" />
                    </svg>
                </div>
                <p class="empty-title">Oops! Hari Kerja Belum Diatur</p>
                <p class="empty-subtitle text-muted">
                    Hari kerja Anda belum ditambahkan oleh admin.<br>
                    Silakan hubungi administrator untuk mengatur jadwal hari kerja Anda.
                </p>
            </div>
        </div>
    @endif
</div>