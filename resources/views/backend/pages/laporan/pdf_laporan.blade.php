<!DOCTYPE html>
<html>
<head>
    {{-- KRUSIAL: Tambahkan ini untuk memastikan UTF-8 dikenali oleh DomPDF --}}
    <meta charset="UTF-8">
    <title>{{ __('Laporan Emisi Perusahaan') }}</title>
    <style>
        /* CSS sederhana untuk PDF */
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 20mm; /* Menambahkan margin untuk tampilan yang lebih baik */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1, h2, h3 {
            text-align: center;
            margin-bottom: 10px;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <h1>{{ __('Laporan Emisi Perusahaan') }}</h1>
    <h2>{{ __('Ringkasan Bulanan') }}</h2>
    <p class="text-center">{{ __('Periode') }}: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}</p>

    <h3>{{ __('Laporan Perusahaan') }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ __('No') }}</th>
                <th>{{ __('Nama Perusahaan') }}</th>
                <th>{{ __('Total Emisi (kg CO₂)') }}</th>
                <th>{{ __('Rata-rata Harian (kg CO₂)') }}</th>
                <th>{{ __('Banyak Perjalanan') }}</th>
                <th>{{ __('Periode') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($perusahaans as $index => $perusahaan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $perusahaan->nama }}</td>
                    <td>{{ number_format($perusahaan->total_emisi_filtered, 2, ',', '.') }} kg</td>
                    <td>{{ number_format($perusahaan->rata_rata_harian, 2, ',', '.') }} kg</td>
                    <td>{{ $perusahaan->total_perjalanan }} {{ __('kali') }}</td>
                    <td>{{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('Tidak ada data perusahaan untuk periode ini.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        {{ __('Dicetak pada') }}: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </p>
</body>
</html>
