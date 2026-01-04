@extends('layouts.app')

@section('title')
    Rekap Absensi Bulanan
@endsection

@push('css')
    
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <b>Laporan Absensi</b>
        </div>
        <div class="card-body border-bottom py-3">
            <form>
                <div class="row">
                    <div class="col-lg-5">
                        <div class="form-group mb-3">
                            <select name="month" id="month" class="form-control">
                                <option value="">Pilih Bulan</option>
                                @php
                                    $months = [
                                        1 => 'Januari',
                                        2 => 'Februari',
                                        3 => 'Maret',
                                        4 => 'April',
                                        5 => 'Mei',
                                        6 => 'Juni',
                                        7 => 'Juli',
                                        8 => 'Agustus',
                                        9 => 'September',
                                        10 => 'Oktober',
                                        11 => 'November',
                                        12 => 'Desember'
                                    ];
                                @endphp
                                @foreach ($months as $key => $month)
                                    <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-group mb-3">
                            <select name="year" id="year" class="form-control">
                                <option value="">Pilih Tahun</option>
                                @php
                                    $currentYear = date('Y');
                                    $years = range($currentYear, $currentYear - 4);
                                @endphp
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive-lg">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th class="w-1">No</th>
                        <th>User</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Izin/Cuti</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Tidak Hadir</th>
                        <th class="text-center">Total Gaji Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($salaryHistoryAll) > 0)
                        @foreach ($salaryHistoryAll as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $item['user']['name'] }}
                                </td>
                                <td class="text-center">
                                    {{ $item['hadir'] }} Hari
                                </td>
                                <td class="text-center">
                                    {{ $item['cuti'] }} Hari
                                </td>
                                <td class="text-center">
                                    {{ $item['telat'] }} Hari
                                </td>
                                <td class="text-center">
                                    {{ $item['alpha'] }} Hari
                                </td>
                                <td class="text-center">
                                    {{ number_format($item['net_salary'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')

@endpush