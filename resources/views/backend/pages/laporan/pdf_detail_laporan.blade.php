<!DOCTYPE html>
<html>
<head>
    {{-- KRUSIAL: Tambahkan ini untuk memastikan UTF-8 dikenali oleh DomPDF --}}
    <meta charset="UTF-8">
    <title>{{ __('Laporan Detail Emisi Perusahaan') }}</title>
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
        .summary-box {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .summary-box p {
            margin: 5px 0;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <h1>{{ __('Laporan Detail Emisi Perusahaan') }}</h1>
    <h2>{{ $perusahaan->nama }}</h2> {{-- Nama perusahaan adalah data, bukan string terjemahan --}}
    <p class="text-center">{{ __('Periode') }}: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}</p>

    <div class="summary-box">
        <h3>{{ __('Ringkasan Periode Ini:') }}</h3>
        <p>{{ __('Total Emisi:') }} {{ number_format($totalEmisi, 2, ',', '.') }} kg CO₂</p>
        <p>{{ __('Rata-rata Harian:') }} {{ number_format($rataRataHarian, 2, ',', '.') }} kg CO₂</p>
        <p>{{ __('Total Perjalanan:') }} {{ $totalPerjalanan }}</p>
        <p>{{ __('Total Biaya Terkait:') }} Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
    </div>

    <h3>{{ __('Detail Perhitungan Emisi:') }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ __('No') }}</th>
                <th>{{ __('Tanggal') }}</th>
                <th>{{ __('Karyawan') }}</th>
                <th>{{ __('Metode') }}</th>
                <th>{{ __('Jenis') }}</th>
                <th>{{ __('Input') }}</th>
                <th>{{ __('Jml Orang') }}</th>
                <th>{{ __('Emisi (kg CO₂)') }}</th>
                <th>{{ __('Biaya (Rp)') }}</th>
                <th>{{ __('Rute') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($detailPerhitungans as $index => $perhitungan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $perhitungan->tanggal->translatedFormat('d M Y, H:i') }}</td>
                    <td>{{ $perhitungan->user->name ?? 'N/A' }}</td>
                    <td>{{ __(ucwords(str_replace('_', ' ', $perhitungan->metode))) }}</td> {{-- Terjemahkan metode --}}
                    <td>
                        @if ($perhitungan->metode == 'bahan_bakar' && $perhitungan->bahanBakar)
                            {{ $perhitungan->bahanBakar->Bahan_bakar }} ({{ $perhitungan->bahanBakar->kategori }})
                        @elseif ($perhitungan->metode == 'jarak_tempuh' && $perhitungan->transportasi)
                            {{ $perhitungan->transportasi->jenis }} ({{ $perhitungan->transportasi->kategori }})
                        @elseif ($perhitungan->metode == 'biaya' && $perhitungan->biaya)
                            {{ $perhitungan->biaya->jenisKendaraan }} ({{ $perhitungan->biaya->kategori }})
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        {{ number_format($perhitungan->nilai_input, 2, ',', '.') }}
                        @if ($perhitungan->metode == 'bahan_bakar')
                            {{ __('Liter') }}
                        @elseif ($perhitungan->metode == 'jarak_tempuh')
                            {{ __('km') }}
                        @elseif ($perhitungan->metode == 'biaya')
                            {{ __('Rp') }}
                        @endif
                    </td>
                    <td>{{ $perhitungan->jumlah_orang }}</td>
                    <td>{{ number_format($perhitungan->hasil_emisi, 2, ',', '.') }}</td>
                    <td>{{ number_format($perhitungan->biaya->factorEmisi ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $perhitungan->titik_awal }} - {{ $perhitungan->titik_tujuan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">{{ __('Tidak ada data perhitungan untuk periode ini.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        {{ __('Dicetak pada') }}: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </p>
</body>
</html>
