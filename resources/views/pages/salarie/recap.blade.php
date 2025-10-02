@extends('layouts.app')

@section('title')
    Rekap Gaji Karyawan
@endsection

@push('css')
    
@endpush

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <b>Silahkan Pilih Bulan & Tahun Untuk Melakukan Rekap Gaji Bulanan</b>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    @php
                        $months = [
                            1  => 'Januari',
                            2  => 'Februari',
                            3  => 'Maret',
                            4  => 'April',
                            5  => 'Mei',
                            6  => 'Juni',
                            7  => 'Juli',
                            8  => 'Agustus',
                            9  => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ];
                    @endphp
                    <div class="col-lg-5">
                        <div class="form-group mb-3">
                            <label for="month" class="mb-2">Bulan</label>
                            <select name="month" id="month" class="form-control">
                                <option value="">Pilih</option>
                                @foreach ($months as $num => $name)
                                    <option value="{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}" {{ request('month') == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @php
                        $currentYear = request('year') ?? now()->year;
                        $startYear   = now()->year - 5; 
                        $endYear     = now()->year;    
                    @endphp
                    <div class="col-lg-5">
                        <div class="form-group mb-3">
                            <label for="year" class="mb-2">Tahun</label>
                            <select name="year" id="year" class="form-control">
                                <option value="">Pilih</option>
                                @for ($y = $startYear; $y <= $endYear; $y++)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-outline-primary w-100 mt-4">Cetak</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (request('month') && request('year'))  
        <div class="card">
            <div class="card-header">
                <b>Rekap Gaji Karyawan Bulan {{ request('month') }} Pada Tahun {{ request('year') }}</b>
            </div>
            {{-- <div class="card-body">
                <a href="" class="btn btn-outline-success">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-excel"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" /><path d="M10 12l4 5" /><path d="M10 17l4 -5" /></svg>
                    Unduh Excel
                </a>
            </div> --}}
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Gaji Pokok</th>
                            <th>Total Tunjangan</th>
                            <th>Total Kasbon</th>
                            <th>Total Diterima</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaryHistories as $index => $history)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $history['name'] }}</td>
                                <td>{{ number_format($history['base_salary'], 0, ',', '.') }}</td>
                                <td>{{ number_format($history['allowance'], 0, ',', '.') }}</td>
                                <td>{{ number_format($history['cash_advance'], 0, ',', '.') }}</td>
                                <td><strong>Rp. {{ number_format($history['net_salary'], 0, ',', '.') }}</strong></td>
                                <td>
                                    <a href="{{ route('salary.detail', ['month' => request('month'), 'year' => request('year'), 'id' => $history['user_id']]) }}" class="btn btn-outline-warning">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    Tidak ada data rekap gaji untuk bulan {{ $month }} tahun {{ $year }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if($salaryHistories->isNotEmpty())
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-center">Total</th>
                                <th colspan="2">Rp. {{ number_format($salaryHistories->sum('net_salary'), 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif
@endsection