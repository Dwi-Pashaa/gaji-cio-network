<div class="row row-cards">

    {{-- GAJI POKOK --}}
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                 <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2"></path>
                                 <path d="M12 3v3m0 12v3"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium"><b>Gaji Pokok</b></div>
                        <div class="text-secondary">
                            Rp. {{ number_format($baseSalary, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TUNJANGAN --}}
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-warning text-white avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                 <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                 <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                 <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                 <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                 <path d="M6 10h4m-2 -2v4" />
                            </svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium"><b>Total Tunjangan</b></div>
                        <div class="text-secondary">
                            Rp. {{ number_format($totalAllowance, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KASBON --}}
    <div class="col-sm-6 col-lg-4">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-danger text-white avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                 <path d="M12 18h-7a2 2 0 0 1 -2 -2v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7" />
                                 <path d="M16 19h6" />
                                 <circle cx="12" cy="12" r="3" />
                            </svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium"><b>Total Kasbon</b></div>
                        <div class="text-secondary">
                            Rp. {{ number_format($cashAdvance, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POTONGAN ABSENSI --}}
    <div class="col-sm-6 col-lg-6">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-orange text-white avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                 <path d="M17 3l4 4l-14 14h-4v-4z" />
                            </svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium"><b>Potongan Absensi ( Tidak Hadir, Terlambat, Cuti )</b></div>
                        <div class="text-secondary">
                            Rp. {{ number_format($currentAttendancePenalty ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GAJI BERSIH --}}
    <div class="col-sm-6 col-lg-6 mt-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-secondary text-white avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                 <path d="M7 9a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                                 <circle cx="14" cy="13" r="2" />
                            </svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium"><b>Gaji Bersih</b></div>
                        <div class="text-secondary">
                            Rp. {{ number_format($netSalary, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <div class="alert alert-primary">
        <b>
            Total Pendapatan Gaji Anda Di Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }} Adalah Rp. {{ number_format($netSalary) }}
        </b>
    </div>
</div>

<div class="mt-3">
    <div class="card">
        <div class="card-header">
            <b>History Pembayaran Gaji Setiap Bulan</b>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Kasbon</th>
                        <th>Tidak Hadir</th>
                        <th>Izin/Cuti</th>
                        <th>Terlambat</th>
                        <th>Total Gaji Bersih</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($salaryHistory as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ \Carbon\Carbon::create()->month($item['month'])->format('F') }}</td>
                            <td>{{ $item['year'] }}</td>
                            <td>Rp. {{ number_format($item['base_salary']) }}</td>
                            <td>Rp. {{ number_format($item['allowance']) }}</td>
                            <td>Rp. {{ number_format($item['cash_advance']) }}</td>
                            <td class="text-center">{{ $item['alpha'] }}</td>
                            <td class="text-center">{{ $item['cuti'] }}</td>
                            <td class="text-center">{{ $item['telat'] }}</td>
                            <td>Rp. {{ number_format($item['net_salary']) }}</td>
                            <td>
                                <a href="{{ route('dashboard.print.slip', ['month' => $item['month'], 'year' => $item['year']]) }}" class="btn btn-outline-primary">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                                    Cetak Slip Gaji
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
