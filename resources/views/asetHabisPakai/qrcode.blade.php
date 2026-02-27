<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code — Barang Habis Pakai</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- QRCode.js — pure JS, tidak butuh package PHP --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4f8;
            padding: 24px;
            color: #1e3a5f;
        }

        /* ── Toolbar ── */
        .toolbar {
            display: flex; align-items: center; justify-content: space-between;
            background: #fff; border: 1px solid rgba(30,58,95,0.10);
            border-radius: 12px; padding: 12px 20px; margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(30,58,95,0.06);
        }
        .toolbar-title { font-size:1rem; font-weight:800; color:#1e3a5f; }
        .toolbar-meta  { font-size:0.76rem; color:#8a96a3; margin-top:2px; }
        .toolbar-right { display:flex; gap:8px; align-items:center; }

        .btn-print {
            display: inline-flex; align-items: center; gap:6px;
            padding: 9px 18px;
            background: linear-gradient(135deg,#1e3a5f,#2d5a8e);
            color:#fff; border:none; border-radius:9px;
            font-size:0.84rem; font-weight:700; cursor:pointer;
            font-family:'Plus Jakarta Sans',sans-serif;
            box-shadow: 0 3px 10px rgba(30,58,95,0.25);
            transition: all .18s;
        }
        .btn-print:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(30,58,95,0.35); }

        .btn-close-win {
            display: inline-flex; align-items:center; gap:6px;
            padding: 9px 16px; background:#fff; color:#5a6a7e;
            border: 1.5px solid rgba(30,58,95,0.12); border-radius:9px;
            font-size:0.84rem; font-weight:600; cursor:pointer;
            font-family:'Plus Jakarta Sans',sans-serif; transition:all .18s;
        }
        .btn-close-win:hover { background:#f4f6fb; }

        /* ── Grid ── */
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        /* ── Kartu QR ── */
        .qr-card {
            background: #fff; border-radius:14px;
            border: 1px solid rgba(30,58,95,0.10);
            box-shadow: 0 2px 12px rgba(30,58,95,0.07);
            overflow: hidden; display:flex; flex-direction:column;
            align-items:center; page-break-inside:avoid;
        }

        .qr-card-header {
            width:100%; padding:9px 12px;
            background: linear-gradient(135deg,#1e3a5f,#2d5a8e);
            text-align:center;
        }
        .qr-card-header .item-name {
            font-size:0.75rem; font-weight:700; color:#fff; line-height:1.3;
            display:-webkit-box; -webkit-line-clamp:2;
            -webkit-box-orient:vertical; overflow:hidden;
        }

        .qr-img-wrap {
            padding: 14px 14px 8px;
            display:flex; align-items:center; justify-content:center;
            min-height: 160px;
        }

        /* Override qrcode.js default canvas/img style */
        .qr-img-wrap canvas,
        .qr-img-wrap img {
            width: 150px !important;
            height: 150px !important;
            display: block;
        }

        .qr-card-footer {
            width:100%; padding:8px 12px 12px;
            text-align:center; background:#fafbfd;
            border-top:1px solid rgba(30,58,95,0.06);
        }
        .qr-code-text {
            font-family:'DM Mono',monospace; font-size:0.68rem; font-weight:500;
            color:#1e3a5f; background:rgba(30,58,95,0.07);
            border:1px solid rgba(30,58,95,0.10);
            padding:3px 8px; border-radius:5px; letter-spacing:0.8px;
            display:inline-block; word-break:break-all;
        }
        .qr-scan-hint { font-size:0.62rem; color:#a0aab4; margin-top:5px; }

        /* Loading state */
        .qr-loading {
            width:150px; height:150px; display:flex;
            align-items:center; justify-content:center;
            background:#f4f6fb; border-radius:8px; color:#8a96a3; font-size:0.72rem;
        }
        .qr-spinner {
            width:28px; height:28px; border:3px solid rgba(30,58,95,0.12);
            border-top-color:#1e3a5f; border-radius:50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        /* ── Print ── */
        @media print {
            body { background:#fff; padding:6px; }
            .toolbar { display:none !important; }
            .qr-grid { grid-template-columns:repeat(3,1fr); gap:8px; }
            .qr-card { border:1.5px solid #bbb; border-radius:6px; box-shadow:none; }
            .qr-card-header { padding:6px 8px; }
            .qr-card-header .item-name { font-size:0.68rem; }
            .qr-img-wrap { padding:8px 8px 4px; min-height:130px; }
            .qr-img-wrap canvas, .qr-img-wrap img { width:120px !important; height:120px !important; }
            .qr-card-footer { padding:5px 8px 8px; }
            .qr-code-text { font-size:0.6rem; }
        }
    </style>
</head>
<body>

{{-- Toolbar --}}
<div class="toolbar">
    <div>
        <div class="toolbar-title">&#9641; Cetak QR Code Barang Habis Pakai</div>
        <div class="toolbar-meta">
            {{ count($dataproduk) }} barang dipilih &middot; Scan QR → menampilkan kode barang
        </div>
    </div>
    <div class="toolbar-right">
        <button class="btn-close-win" onclick="window.close()">✕ Tutup</button>
        <button class="btn-print" onclick="window.print()">&#128424; Cetak / Simpan PDF</button>
    </div>
</div>

{{-- Grid QR --}}
<div class="qr-grid" id="qrGrid">
    @forelse ($dataproduk as $item)
        <div class="qr-card">

            {{-- Header --}}
            <div class="qr-card-header">
                <div class="item-name">{{ $item->name }}</div>
            </div>

            {{-- QR container — diisi oleh JS --}}
            <div class="qr-img-wrap">
                <div class="qr-loading">
                    <div class="qr-spinner"></div>
                </div>
                {{-- QRCode.js akan generate canvas di sini --}}
                <div class="qr-target" data-code="{{ $item->code }}" style="display:none"></div>
            </div>

            {{-- Footer --}}
            <div class="qr-card-footer">
                <span class="qr-code-text" data-raw="{{ $item->code }}">{{ $item->code }}</span>
                <div class="qr-scan-hint">Scan QR → menampilkan kode barang</div>
            </div>

        </div>
    @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px;color:#8a96a3;">
            <div style="font-size:3rem;margin-bottom:10px;">&#128274;</div>
            <p>Tidak ada barang yang dipilih.</p>
        </div>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Format kode: spasi setiap 4 karakter
    document.querySelectorAll('.qr-code-text').forEach(el => {
        const raw = el.dataset.raw || el.textContent.trim();
        el.dataset.raw = raw;
        if (raw.length > 4) {
            el.textContent = raw.replace(/(.{4})/g, '$1 ').trim();
        }
    });

    // Generate QR Code menggunakan QRCode.js
    document.querySelectorAll('.qr-target').forEach(function (target) {
        const code   = target.getAttribute('data-code');
        const wrap   = target.closest('.qr-img-wrap');
        const loader = wrap.querySelector('.qr-loading');

        // Sembunyikan loader, tampilkan container QR
        target.style.display = 'block';
        target.style.display = 'flex';
        target.style.alignItems = 'center';
        target.style.justifyContent = 'center';

        try {
            new QRCode(target, {
                text:           code,          // ← isi QR = kode barang
                width:          150,
                height:         150,
                colorDark:      '#1e3a5f',     // warna titik
                colorLight:     '#ffffff',
                correctLevel:   QRCode.CorrectLevel.H,
            });

            // Hapus loader setelah QR berhasil dibuat
            setTimeout(() => {
                if (loader) loader.remove();
            }, 100);

        } catch (err) {
            loader.textContent = 'Gagal generate QR';
            loader.style.fontSize = '0.7rem';
            console.error('QR error untuk kode', code, err);
        }
    });

});
</script>
</body>
</html>