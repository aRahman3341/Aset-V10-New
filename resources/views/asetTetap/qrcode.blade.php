<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak QR Code Aset</title>

    {{-- QRCode.js — pure JS, tidak butuh package PHP apapun --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: #fff;
        }

        /* ── Toolbar (tidak ikut print) ── */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }
        .toolbar-title { font-size: 13px; font-weight: bold; color: #003087; }
        .toolbar-meta  { font-size: 11px; color: #666; margin-top: 2px; }
        .toolbar-right { display: flex; gap: 8px; }
        .btn-print {
            padding: 7px 14px; background: #003087; color: #fff;
            border: none; border-radius: 5px; font-size: 12px;
            font-weight: bold; cursor: pointer; font-family: Arial, sans-serif;
        }
        .btn-print:hover { background: #012060; }
        .btn-close-win {
            padding: 7px 12px; background: #fff; color: #555;
            border: 1px solid #ccc; border-radius: 5px; font-size: 12px;
            cursor: pointer; font-family: Arial, sans-serif;
        }

        /* ── Grid utama ── */
        .page-wrapper { width: 100%; padding: 10px; }

        table.grid {
            width: 100%;
            border-collapse: collapse;
        }

        table.grid > tbody > tr > td {
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }

        /* ── Label Card (desain sama persis seperti aslinya) ── */
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
        .cell-info { line-height: 1.6; }

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
            padding: 4px !important;
        }

        /* QRCode.js menghasilkan canvas/img — samakan ukurannya */
        .cell-qr .qr-target canvas,
        .cell-qr .qr-target img {
            width: 90px !important;
            height: 90px !important;
            display: block;
            margin: auto;
        }

        /* Loading state saat QR belum selesai di-generate */
        .qr-loading {
            width: 90px; height: 90px;
            display: flex; align-items: center; justify-content: center;
            font-size: 8px; color: #888;
            background: #f9f9f9; margin: auto;
        }

        .text-center { text-align: center; }

        /* ── Print styles ── */
        @media print {
            .toolbar { display: none !important; }
            body { background: #fff; padding: 0; }
            .page-wrapper { padding: 4px; }
            table.grid > tbody > tr > td { padding: 3px; }
            .cell-qr .qr-target canvas,
            .cell-qr .qr-target img { width: 80px !important; height: 80px !important; }
        }
    </style>
</head>
<body>

{{-- ── Toolbar (tidak ikut print) ── --}}
<div class="toolbar">
    <div>
        <div class="toolbar-title">&#9641; Cetak QR Code Aset Tetap</div>
        <div class="toolbar-meta">
            {{ count($dataproduk) }} aset dipilih &middot;
            Scan QR → menampilkan kode barang, NUP, nama, lokasi
        </div>
    </div>
    <div class="toolbar-right">
        <button class="btn-close-win" onclick="window.close()">✕ Tutup</button>
        <button class="btn-print" onclick="window.print()">&#128424; Cetak / Simpan PDF</button>
    </div>
</div>

{{-- ── Grid Label ── --}}
<div class="page-wrapper">
    <table class="grid">
        <tbody>
            <tr>
            @foreach ($dataproduk as $index => $produk)
                @php
                    // Konten QR: kode*nup*nama*lokasi (sama seperti QrCodeController)
                    // Ini yang akan terbaca saat di-scan
                    $colIndex = $index % 2;
                @endphp

                <td>
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
                                     width="42" height="42" alt="PUPR"
                                     onerror="this.style.display='none'">
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

                            {{-- QR Code — di-generate oleh JS di browser --}}
                            <td class="cell-qr">
                                <div class="qr-loading" id="loading-{{ $index }}">...</div>
                                <div class="qr-target"
                                     id="qr-{{ $index }}"
                                     style="display:none"
                                     data-content="{{ implode('*', [
                                         $produk->code ?? '',
                                         $produk->nup  ?? '',
                                         mb_strimwidth($produk->name ?? '', 0, 40, ''),
                                     ]) }}">
                                </div>
                            </td>
                        </tr>

                    </table>
                </td>

                {{-- Tutup baris setiap 2 kolom --}}
                @if ($colIndex === 1 && ! $loop->last)
                    </tr><tr>
                @elseif ($loop->last && $colIndex === 0)
                    <td></td>{{-- Kolom kosong agar tidak patah --}}
                @endif

            @endforeach
            </tr>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Generate QR untuk setiap kartu
    document.querySelectorAll('.qr-target').forEach(function (target) {
        const content  = target.getAttribute('data-content');
        const idx      = target.id.replace('qr-', '');
        const loadEl   = document.getElementById('loading-' + idx);

        // Tampilkan container, sembunyikan loading
        target.style.display = 'block';
        if (loadEl) loadEl.style.display = 'none';

        try {
            new QRCode(target, {
                text:         content,      // ← isi QR = kode*nup*nama
                width:        90,
                height:       90,
                colorDark:    '#000000',
                colorLight:   '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
            });
        } catch (err) {
            target.innerHTML = '<div style="font-size:8px;color:red;padding:4px;">Gagal</div>';
            console.error('QR error:', err);
        }
    });
});
</script>
</body>
</html>