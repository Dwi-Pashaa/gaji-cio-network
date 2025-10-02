@extends('layouts.app')

@section('title')
    Detail Gaji {{ $data['user']['name'] }} Bulan {{ $data['month'] }} Tahun {{ $data['year'] }}
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detail Slip Gaji</h4>
            <div class="d-flex gap-2">
                {{-- <a href="" target="_blank" class="btn btn-danger">
                    <i class="bi bi-printer"></i> Cetak Slip
                </a> --}}
                <a href="{{ route('salary.recap') }}" class="btn btn-primary">
                    <i class="bi bi-chevron-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Informasi Karyawan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Nama</strong></td>
                            <td> {{ $data['user']['name'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td> {{ $data['user']['email'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Periode</strong></td>
                            <td> {{ \Carbon\Carbon::create()->month($data['month'])->translatedFormat('F') }} {{ $data['year'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th colspan="2" class="text-center">Gaji Pokok</th>
                    </tr>
                    <tr>
                        <th>Komponen</th>
                        <th class="text-end">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="text-end">{{ number_format($data['base_salary'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th colspan="2" class="text-center">Tunjangan</th>
                    </tr>
                    <tr>
                        <th>Komponen</th>
                        <th class="text-end">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['allowances'] as $alw)
                        <tr>
                            <td>Tunjangan {{ $alw->name }}</td>
                            <td class="text-end">{{ number_format($alw->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="2">Tidak Ada Tunjangan</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Tunjangan</th>
                        <th class="text-end">{{ number_format($data['total_allowance'], 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th colspan="2" class="text-center">Kasbon</th>
                    </tr>
                    <tr>
                        <th>Komponen</th>
                        <th class="text-end">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['cash_advances'] as $kasbon)
                        <tr>
                            <td>{{ $kasbon->title ?? 'Kasbon' }}</td>
                            <td class="text-end">{{ number_format($kasbon->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Tidak ada kasbon</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Kasbon</th>
                        <th class="text-end">{{ number_format($data['total_cash_advance'], 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="alert alert-info mt-2">
                <h6 class="mb-1">Total Gaji Diterima</h6>
                <h4 class="fw-bold">
                    Rp {{ number_format($data['net_salary'], 0, ',', '.') }}
                </h4>
            </div>
        </div>
    </div>
@endsection
