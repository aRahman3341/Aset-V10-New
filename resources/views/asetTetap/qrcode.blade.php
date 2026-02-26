<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak QR Code Aset</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .page-wrapper {
            width: 100%;
        }

        table.grid {
            width: 100%;
            border-collapse: collapse;
        }

        table.grid > tbody > tr > td {
            width: 50%;
            padding: 6px;
            vertical-align: top;
        }

        /* ── Card Label ─────────────────────── */
        .label-card {
            border: 1.5px solid #333;
            width: 100%;
            border-collapse: collapse;
        }

        .label-card td {
            border: 1px solid #aaa;
            padding: 5px 7px;
            vertical-align: middle;
        }

        /* Header row */
        .label-card .row-header td {
            background-color: #003087;
            color: #fff;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.3px;
            padding: 4px 6px;
        }

        /* Logo cell */
        .cell-logo {
            width: 48px;
            text-align: center;
            background-color: #f5f5f5;
        }

        /* Info cell */
        .cell-info {
            line-height: 1.6;
        }

        .cell-info .code-label {
            font-size: 10px;
            font-weight: bold;
            color: #003087;
        }

        .cell-info .name-label {
            font-size: 10px;
            font-weight: bold;
            color: #111;
        }

        .cell-info .sub-label {
            font-size: 9px;
            color: #555;
        }

        /* QR cell */
        .cell-qr {
            width: 100px;
            text-align: center;
            background-color: #fff;
        }

        .cell-qr img {
            width: 90px;
            height: 90px;
        }

        .text-center { text-align: center; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <table class="grid">
        <tbody>
            <tr>
            @foreach ($qrcode as $index => $qr)
                @php
                    $produk    = $dataproduk[$index];
                    $colIndex  = $index % 2;
                @endphp

                <td>
                    {{-- ── Label Card ── --}}
                    <table class="label-card">
                        {{-- Baris 1: Header instansi --}}
                        <tr class="row-header">
                            <td colspan="3">
                                KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT<br>
                                DIREKTORAT JENDERAL CIPTA KARYA — BALAI SAINS BANGUNAN
                            </td>
                        </tr>

                        {{-- Baris 2: Logo | Info | QR --}}
                        <tr>
                            {{-- Logo --}}
                            <td class="cell-logo">
                                <img src="{{ public_path('assets/img/PUPR.png') }}"
                                     width="42" height="42" alt="PUPR">
                            </td>

                            {{-- Info Aset --}}
                            <td class="cell-info">
                                <div class="code-label">
                                    {{ $produk->code ?? '-' }}
                                    @if ($produk->years)
                                        &nbsp;/&nbsp;{{ $produk->years }}
                                    @endif
                                    &nbsp;&nbsp;NUP: {{ $produk->nup ?? '-' }}
                                </div>
                                <div class="name-label">
                                    {{ mb_strimwidth($produk->name ?? '-', 0, 45, '...') }}
                                </div>
                                @if ($produk->name_fix)
                                    <div class="sub-label">
                                        Merk&nbsp;: {{ mb_strimwidth($produk->name_fix, 0, 35, '...') }}
                                    </div>
                                @endif
                                @if ($produk->no_seri)
                                    <div class="sub-label">
                                        No. Seri&nbsp;: {{ $produk->no_seri }}
                                    </div>
                                @endif
                            </td>

                            {{-- QR Code --}}
                            <td class="cell-qr">
                                <img src="data:image/png;base64,{{ $qr }}" alt="QR">
                            </td>
                        </tr>
                    </table>
                </td>

                {{-- Tutup baris setiap 2 kolom --}}
                @if ($colIndex === 1 && ! $loop->last)
                    </tr>
                    <tr>
                @elseif ($loop->last && $colIndex === 0)
                    {{-- Kolom kosong agar tabel tidak patah --}}
                    <td></td>
                @endif

            @endforeach
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>