<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak QR Code Barang Habis Pakai</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; background: #fff; }

        .toolbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 16px; background: #f5f5f5; border-bottom: 1px solid #ddd;
        }
        .toolbar-title { font-size: 13px; font-weight: bold; color: #003087; }
        .toolbar-meta  { font-size: 11px; color: #666; margin-top: 2px; }
        .toolbar-right { display: flex; gap: 8px; }
        .btn-print {
            padding: 7px 14px; background: #003087; color: #fff;
            border: none; border-radius: 5px; font-size: 12px; font-weight: bold; cursor: pointer;
        }
        .btn-print:hover { background: #012060; }
        .btn-close-win {
            padding: 7px 12px; background: #fff; color: #555;
            border: 1px solid #ccc; border-radius: 5px; font-size: 12px; cursor: pointer;
        }

        .page-wrapper { width: 100%; padding: 10px; }
        table.grid { width: 100%; border-collapse: collapse; }
        table.grid > tbody > tr > td { width: 50%; padding: 5px; vertical-align: top; }

        .label-card { border: 1.5px solid #333; width: 100%; border-collapse: collapse; }
        .label-card td { border: 1px solid #aaa; padding: 5px 7px; vertical-align: middle; }

        .label-card .row-header td {
            background-color: #003087; color: #fff; text-align: center;
            font-size: 9px; font-weight: bold; letter-spacing: 0.3px; padding: 4px 6px;
        }

        .cell-logo { width: 100px; text-align: center; background-color: #f0f4ff; padding: 4px !important; }
        .cell-logo img { width: 90px; height: 90px; object-fit: contain; display: block; margin: auto; }

        .cell-info { line-height: 1.6; padding: 6px 8px !important; }
        .cell-info .code-label {
            font-size: 10px; font-weight: bold; color: #003087;
            border-bottom: 1px solid #e0e8f5; padding-bottom: 3px; margin-bottom: 4px;
        }
        .cell-info .name-label { font-size: 10px; font-weight: bold; color: #111; }
        .cell-info .sub-label  { font-size: 9px; color: #555; margin-top: 2px; }
        .cell-info .scan-note  {
            margin-top: 6px; font-size: 8px; color: #888;
            border-top: 1px dashed #ddd; padding-top: 4px;
        }

        .cell-qr { width: 100px; text-align: center; background: #fff; padding: 4px !important; }
        .cell-qr .qr-target canvas,
        .cell-qr .qr-target img { width: 90px !important; height: 90px !important; display: block; margin: auto; }
        .qr-loading {
            width: 90px; height: 90px; display: flex; align-items: center;
            justify-content: center; font-size: 8px; color: #888; background: #f9f9f9; margin: auto;
        }

        @media print {
            .toolbar { display: none !important; }
            .page-wrapper { padding: 4px; }
            table.grid > tbody > tr > td { padding: 3px; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <div>
        <div class="toolbar-title">&#9641; Cetak QR Code Barang Habis Pakai</div>
        <div class="toolbar-meta">
            {{ count($dataproduk) }} barang dipilih &middot;
            Scan QR → halaman info publik barang
        </div>
    </div>
    <div class="toolbar-right">
        <button class="btn-close-win" onclick="window.close()">✕ Tutup</button>
        <button class="btn-print" onclick="window.print()">&#128424; Cetak / Simpan PDF</button>
    </div>
</div>

<div class="page-wrapper">
    <table class="grid">
        <tbody>
            <tr>
            @forelse ($dataproduk as $index => $item)
                @php
                    $colIndex = $index % 2;
                    // URL menuju halaman info publik (bukan halaman edit)
                    $qrUrl    = url('/qr/item/' . $item->id);
                @endphp

                <td>
                    <table class="label-card">
                        <tr class="row-header">
                            <td colspan="3">
                                KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT<br>
                                DIREKTORAT JENDERAL CIPTA KARYA — BALAI SAINS BANGUNAN
                            </td>
                        </tr>
                        <tr>
                            <td class="cell-logo">
                                <img src="{{ asset('assets/img/PUPR.png') }}"
                                     alt="Logo PUPR"
                                     onerror="this.style.display='none'">
                            </td>
                            <td class="cell-info">
                                <div class="code-label">Kode: {{ $item->code ?? '-' }}</div>
                                <div class="name-label">
                                    {{ mb_strimwidth($item->name ?? '-', 0, 45, '...') }}
                                </div>
                                @if($item->categories)
                                    <div class="sub-label">Kategori: {{ $item->categories }}</div>
                                @endif
                                <div class="sub-label">
                                    Satuan: {{ $item->satuan ?? '-' }}
                                    &nbsp;·&nbsp; Saldo: {{ $item->saldo ?? 0 }}
                                </div>
                                <div class="scan-note">
                                    &#x1F4F1; Scan QR untuk info &amp; edit barang
                                </div>
                            </td>
                            <td class="cell-qr">
                                <div class="qr-loading" id="loading-{{ $index }}">...</div>
                                <div class="qr-target"
                                     id="qr-{{ $index }}"
                                     style="display:none"
                                     data-url="{{ $qrUrl }}">
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>

                @if ($colIndex === 1 && ! $loop->last)
                    </tr><tr>
                @elseif ($loop->last && $colIndex === 0)
                    <td></td>
                @endif

            @empty
                <td colspan="2" style="text-align:center; padding:40px; color:#888;">
                    Tidak ada barang yang dipilih.
                </td>
            @endforelse
            </tr>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.qr-target').forEach(function (target) {
        var url    = target.getAttribute('data-url');
        var idx    = target.id.replace('qr-', '');
        var loadEl = document.getElementById('loading-' + idx);

        target.style.display = 'block';
        if (loadEl) loadEl.style.display = 'none';

        try {
            new QRCode(target, {
                text:         url,
                width:        90,
                height:       90,
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