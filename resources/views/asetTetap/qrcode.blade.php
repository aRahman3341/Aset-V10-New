<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak QR Code Aset Tetap</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; background: #fff; }

        /* ── Toolbar ── */
        .toolbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 16px; background: #f5f5f5; border-bottom: 1px solid #ddd;
        }
        .toolbar-title { font-size: 13px; font-weight: bold; color: #003087; }
        .toolbar-meta  { font-size: 11px; color: #666; margin-top: 2px; }
        .toolbar-right { display: flex; gap: 8px; }
        .btn-print {
            padding: 7px 14px; background: #003087; color: #fff;
            border: none; border-radius: 5px; font-size: 12px;
            font-weight: bold; cursor: pointer;
        }
        .btn-print:hover { background: #012060; }
        .btn-close-win {
            padding: 7px 12px; background: #fff; color: #555;
            border: 1px solid #ccc; border-radius: 5px; font-size: 12px; cursor: pointer;
        }

        /* ── Grid 2 kolom ── */
        .page-wrapper { width: 100%; padding: 10px; }
        table.grid { width: 100%; border-collapse: collapse; }
        table.grid > tbody > tr > td { width: 50%; padding: 5px; vertical-align: top; }

        /* ── Label Card ── */
        .label-card { border: 1.5px solid #333; width: 100%; border-collapse: collapse; }
        .label-card td { border: 1px solid #aaa; padding: 5px 7px; vertical-align: middle; }

        /* Header biru instansi */
        .label-card .row-header td {
            background-color: #003087; color: #fff; text-align: center;
            font-size: 9px; font-weight: bold; letter-spacing: 0.3px; padding: 4px 6px;
        }

        /* Logo */
        .cell-logo { width: 90px; text-align: center; background-color: #f0f4ff; padding: 4px !important; }
        .cell-logo img { width: 80px; height: 80px; object-fit: contain; display: block; margin: auto; }

        /* Info */
        .cell-info { line-height: 1.7; padding: 6px 8px !important; }
        .cell-info .code-label {
            font-size: 11px; font-weight: bold; color: #003087;
            border-bottom: 1px solid #e0e8f5; padding-bottom: 3px; margin-bottom: 4px;
        }
        .cell-info .name-label { font-size: 10px; font-weight: bold; color: #111; }
        .cell-info .sub-label  { font-size: 9px; color: #555; margin-top: 2px; }
        .cell-info .scan-note  {
            margin-top: 6px; font-size: 8px; color: #888;
            border-top: 1px dashed #ddd; padding-top: 4px;
        }

        /* QR */
        .cell-qr { width: 90px; text-align: center; background: #fff; padding: 4px !important; }
        .cell-qr .qr-target canvas,
        .cell-qr .qr-target img { width: 80px !important; height: 80px !important; display: block; margin: auto; }
        .qr-loading {
            width: 80px; height: 80px; display: flex; align-items: center;
            justify-content: center; font-size: 8px; color: #888;
            background: #f9f9f9; margin: auto;
        }

        /* Print */
        @media print {
            .toolbar { display: none !important; }
            .page-wrapper { padding: 4px; }
            table.grid > tbody > tr > td { padding: 3px; }
        }
    </style>
</head>
<body>

{{-- Toolbar --}}
<div class="toolbar">
    <div>
        <div class="toolbar-title">&#9641; Cetak QR Code Aset Tetap</div>
        <div class="toolbar-meta">
            {{ count($dataproduk) }} aset dipilih
            &middot; Scan QR → langsung buka halaman Edit aset
        </div>
    </div>
    <div class="toolbar-right">
        <button class="btn-close-win" onclick="window.close()">✕ Tutup</button>
        <button class="btn-print" onclick="window.print()">&#128424; Cetak / Simpan PDF</button>
    </div>
</div>

{{-- Grid Label --}}
<div class="page-wrapper">
    <table class="grid">
        <tbody>
            <tr>
            @foreach ($dataproduk as $index => $produk)
                @php
                    $colIndex = $index % 2;

                    // Baca kolom DB dengan nama spasi
                    $kode = $produk->{'Kode Barang'} ?? '-';
                    $nama = $produk->{'Nama Barang'} ?? '-';
                    $nup  = $produk->nup             ?? '-';
                    $merk = $produk->merk             ?? null;
                    $jenis= $produk->{'Jenis BMN'}   ?? null;

                    // URL edit — ini yang di-encode ke QR
                    // Saat di-scan, langsung buka halaman edit aset ini
                    $editUrl = url('/asetTetap/' . $produk->id . '/edit');
                @endphp

                <td>
                    <table class="label-card">

                        {{-- Header instansi --}}
                        <tr class="row-header">
                            <td colspan="3">
                                KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT<br>
                                DIREKTORAT JENDERAL CIPTA KARYA — BALAI SAINS BANGUNAN
                            </td>
                        </tr>

                        {{-- Logo | Info | QR --}}
                        <tr>
                            {{-- Logo --}}
                            <td class="cell-logo">
                                <img src="{{ asset('assets/img/PUPR.png') }}"
                                     alt="Logo PUPR"
                                     onerror="this.style.display='none'">
                            </td>

                            {{-- Info Aset --}}
                            <td class="cell-info">
                                <div class="code-label">
                                    Kode: {{ $kode }}
                                    &nbsp;|&nbsp; NUP: {{ $nup }}
                                </div>
                                <div class="name-label">
                                    {{ mb_strimwidth($nama, 0, 40, '...') }}
                                </div>
                                @if($merk)
                                    <div class="sub-label">Merk: {{ mb_strimwidth($merk, 0, 35, '...') }}</div>
                                @endif
                                @if($jenis)
                                    <div class="sub-label">{{ $jenis }}</div>
                                @endif
                                <div class="scan-note">
                                    &#x1F4F1; Scan QR untuk edit aset
                                </div>
                            </td>

                            {{-- QR Code — isi URL edit --}}
                            <td class="cell-qr">
                                <div class="qr-loading" id="loading-{{ $index }}">...</div>
                                <div class="qr-target"
                                     id="qr-{{ $index }}"
                                     style="display:none"
                                     data-url="{{ $editUrl }}">
                                </div>
                            </td>
                        </tr>

                    </table>
                </td>

                @if ($colIndex === 1 && !$loop->last)
                    </tr><tr>
                @elseif ($loop->last && $colIndex === 0)
                    <td></td>
                @endif

            @endforeach
            </tr>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.qr-target').forEach(function (target) {
        const url   = target.getAttribute('data-url');
        const idx   = target.id.replace('qr-', '');
        const loadEl = document.getElementById('loading-' + idx);

        target.style.display = 'block';
        if (loadEl) loadEl.style.display = 'none';

        try {
            new QRCode(target, {
                text:         url,           // URL edit aset — scan = langsung buka edit
                width:        80,
                height:       80,
                colorDark:    '#000000',
                colorLight:   '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
        } catch (err) {
            target.innerHTML = '<div style="font-size:8px;color:red;padding:4px;">Gagal</div>';
        }
    });
});
</script>
</body>
</html>