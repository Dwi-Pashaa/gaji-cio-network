<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 2px solid #848484;
            margin-bottom: 15px;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .kop-surat img {
            width: 150px;
            height: auto;
            /* margin-left: 15px; */
        }
        .kop-surat .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-surat .kop-text h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .kop-surat .kop-text p {
            margin: 2px 0;
            font-size: 12px;
        }
        h2, h4 {
            margin: 0;
            text-align: center;
        }
        .slip-header {
            margin-bottom: 20px;
            text-align: center;
        }
        .employee-detail {
            margin-bottom: 20px;
        }
        .employee-detail table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .employee-detail td {
            padding: 4px;
        }
        table.salary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.salary th, table.salary td {
            border: 1px solid #444;
            padding: 6px 10px;
            text-align: left;
        }
        table.salary th {
            background-color: #f2f2f2;
        }
        table.salary tfoot th {
            text-align: right;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <img src="{{ public_path('img/logo.png') }}" alt="Logo">
    </div>

    <div class="slip-header">
        <h2>Slip Pembayaran Gaji</h2>
        <h4>Bulan {{ $month }} Tahun {{ $year }}</h4>
    </div>

    <div class="employee-detail">
        <table>
            <tr>
                <td><strong>Nama</strong></td>
                <td>: {{ $user->name }}</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>: {{ $user->email ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Periode Pembayaran</strong></td>
                <td>: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</td>
            </tr>
        </table>
    </div>

    <table class="salary">
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Gaji Pokok</strong></td>
                <td class="text-right"><strong>{{ number_format($baseSalary, 0, ',', '.') }}</strong></td>
            </tr>
            
            <tr>
                <td colspan="2"><strong>Tunjangan</strong></td>
            </tr>
            @forelse ($allowances as $alw)
                <tr>
                    <td>{{ $alw->name }}</td>
                    <td class="text-right">{{ number_format($alw->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td>Tunjangan</td>
                    <td class="text-right">-</td>
                </tr>
            @endforelse

            <tr>
                <td><strong>Total Tunjangan</strong></td>
                <td class="text-right"><strong>{{ number_format($totalAllowance, 0, ',', '.') }}</strong></td>
            </tr>

            <tr>
                <td colspan="2"><strong>Kasbon</strong></td>
            </tr>

            @forelse ($cashAdvance as $kasbon)
                <tr>
                    <td>Kasbon {{ $kasbon->title ?? 'Lainnya' }}</td>
                    <td class="text-right">{{ number_format($kasbon->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td>Kasbon</td>
                    <td class="text-right">-</td>
                </tr>
            @endforelse

            <tr>
                <td><strong>Total Kasbon</strong></td>
                <td class="text-right"><strong>{{ number_format($cashAdvanceTotal, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Total Diterima</th>
                <th class="text-right">{{ number_format($netSalary, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
   <div style="margin-top: 50px; width: 100%; text-align: right;">
        <p>Ds. Ciparay, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p>Admin CIO Network Solution</p>
        <br><br><br>
        <p>
            <strong>...............................................</strong>
        </p>
    </div>

</body>
</html>
