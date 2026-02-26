{{-- ============================================================
     resources/views/asetTetap/scane.blade.php
     Modal kamera untuk scan QR Code aset
     ============================================================ --}}

<!-- ── Modal Scan QR ──────────────────────────────────────── -->
<div class="modal fade" id="modalScanQR" tabindex="-1" aria-labelledby="modalScanQRLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">

            <!-- Header -->
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #012970, #4154f1);">
                <h5 class="modal-title fw-bold" id="modalScanQRLabel">
                    <i class="bi bi-qr-code-scan me-2"></i> Scan QR Code Aset
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <div class="row g-3">

                    <!-- Kiri: Kamera -->
                    <div class="col-md-5">
                        <div class="text-center mb-2">
                            <small class="text-muted">Arahkan kamera ke QR Code</small>
                        </div>
                        <div id="qr-reader"
                             style="width:100%; border-radius:8px; overflow:hidden; border:2px solid #dee2e6;">
                        </div>
                        <div id="qr-reader-status" class="text-center mt-2 text-muted" style="font-size:0.8rem;">
                            Memuat kamera...
                        </div>

                        <!-- Input manual sebagai fallback -->
                        <hr class="my-3">
                        <div class="text-center mb-1">
                            <small class="text-muted">Atau masukkan kode manual</small>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="text" id="manualQRInput" class="form-control"
                                   placeholder="Kode Barang atau CODE*NUP">
                            <button class="btn btn-primary" id="btnManualSearch">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Kanan: Hasil Scan -->
                    <div class="col-md-7">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-info-circle me-1"></i> Informasi Aset
                        </h6>

                        <!-- Skeleton / Empty state -->
                        <div id="scan-empty" class="text-center py-4 text-muted">
                            <i class="bi bi-qr-code" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-2">Belum ada data. Scan QR untuk memulai.</p>
                        </div>

                        <!-- Loading -->
                        <div id="scan-loading" style="display:none;" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Mencari data aset...</p>
                        </div>

                        <!-- Error -->
                        <div id="scan-error" style="display:none;" class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <span id="scan-error-msg">Aset tidak ditemukan.</span>
                        </div>

                        <!-- Hasil -->
                        <div id="scan-result" style="display:none;">
                            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;">
                                <div class="card-body p-3">

                                    <!-- Nama & Kode -->
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-0" id="res-name">-</h6>
                                            <small class="text-muted" id="res-name-fix">-</small>
                                        </div>
                                        <span class="badge bg-primary" id="res-code">-</span>
                                    </div>

                                    <hr class="my-2">

                                    <!-- Detail Grid -->
                                    <div class="row g-2" style="font-size:0.82rem;">
                                        <div class="col-6">
                                            <span class="text-muted">NUP</span><br>
                                            <strong id="res-nup">-</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">No. Seri</span><br>
                                            <strong id="res-seri">-</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Kategori</span><br>
                                            <strong id="res-category">-</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Kondisi</span><br>
                                            <span id="res-condition" class="badge">-</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Status</span><br>
                                            <span id="res-status" class="badge">-</span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Tahun</span><br>
                                            <strong id="res-years">-</strong>
                                        </div>
                                        <div class="col-12">
                                            <span class="text-muted">Lokasi</span><br>
                                            <strong id="res-lokasi">-</strong>
                                        </div>
                                        <div class="col-12">
                                            <span class="text-muted">Penanggung Jawab</span><br>
                                            <strong id="res-supervisor">-</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Nilai Perolehan</span><br>
                                            <strong id="res-nilai">-</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted">Perlu Kalibrasi</span><br>
                                            <span id="res-kalibrasi" class="badge">-</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Tombol aksi -->
                            <div class="d-flex gap-2">
                                <a id="btn-edit-aset" href="#" class="btn btn-warning btn-sm flex-fill">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Aset
                                </a>
                                <button class="btn btn-outline-secondary btn-sm" id="btnScanAgain">
                                    <i class="bi bi-arrow-repeat me-1"></i> Scan Lagi
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ── html5-qrcode library ──────────────────────────────── -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
(function () {
    'use strict';

    const SCAN_URL   = "{{ route('scanning') }}";
    const EDIT_BASE  = "/asetTetap/";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let html5QrCode  = null;
    let scannerRunning = false;

    // ── Helpers ──────────────────────────────────────────────
    function showPanel(id) {
        ['scan-empty', 'scan-loading', 'scan-error', 'scan-result'].forEach(function (p) {
            document.getElementById(p).style.display = (p === id) ? 'block' : 'none';
        });
    }

    function formatRupiah(value) {
        if (! value) return '-';
        return 'Rp ' + parseFloat(value).toLocaleString('id-ID', { minimumFractionDigits: 0 });
    }

    function conditionBadgeClass(cond) {
        if (cond === 'Baik') return 'bg-success';
        if (cond === 'Rusak Ringan') return 'bg-warning text-dark';
        return 'bg-danger';
    }

    function statusBadgeClass(status) {
        if (status === 'Dipakai') return 'bg-success';
        if (status === 'Maintenance') return 'bg-warning text-dark';
        if (status === 'Tidak Dipakai') return 'bg-secondary';
        return 'bg-light text-dark border';
    }

    // ── Render hasil ─────────────────────────────────────────
    function renderItem(item) {
        document.getElementById('res-name').textContent        = item.name       ?? '-';
        document.getElementById('res-name-fix').textContent    = item.name_fix   ?? '-';
        document.getElementById('res-code').textContent        = (item.code ?? '-') + ' / NUP ' + (item.nup ?? '-');
        document.getElementById('res-nup').textContent         = item.nup        ?? '-';
        document.getElementById('res-seri').textContent        = item.no_seri    ?? '-';
        document.getElementById('res-category').textContent    = item.category_name ?? '-';
        document.getElementById('res-years').textContent       = item.years      ?? '-';
        document.getElementById('res-lokasi').textContent      = item.lokasi_label  ?? '-';
        document.getElementById('res-supervisor').textContent  = item.supervisor_name ?? '-';
        document.getElementById('res-nilai').textContent       = formatRupiah(item.nilai_perolehan ?? item.nilai);

        // Kondisi badge
        const condEl = document.getElementById('res-condition');
        condEl.textContent  = item.condition ?? 'Baik';
        condEl.className    = 'badge ' + conditionBadgeClass(item.condition ?? 'Baik');

        // Status badge
        const statEl = document.getElementById('res-status');
        statEl.textContent  = item.status ?? '-';
        statEl.className    = 'badge ' + statusBadgeClass(item.status ?? '');

        // Kalibrasi badge
        const kalEl = document.getElementById('res-kalibrasi');
        if (item.dikalibrasi == 1) {
            kalEl.textContent = 'Perlu';
            kalEl.className   = 'badge bg-info';
        } else {
            kalEl.textContent = 'Tidak';
            kalEl.className   = 'badge bg-light text-dark border';
        }

        // Tombol edit
        document.getElementById('btn-edit-aset').href = EDIT_BASE + item.id + '/edit';

        showPanel('scan-result');
    }

    // ── Kirim ke API ─────────────────────────────────────────
    function doSearch(code, nup) {
        showPanel('scan-loading');

        fetch(SCAN_URL, {
            method: 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : CSRF_TOKEN,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify({ code: code, nup: nup }),
        })
        .then(function (r) {
            if (! r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (data) {
            if (! data.items || data.items.length === 0) {
                document.getElementById('scan-error-msg').textContent =
                    'Aset dengan kode "' + code + '" tidak ditemukan.';
                showPanel('scan-error');
                return;
            }
            renderItem(data.items[0]);
        })
        .catch(function (err) {
            document.getElementById('scan-error-msg').textContent =
                'Gagal terhubung ke server. ' + err.message;
            showPanel('scan-error');
        });
    }

    // ── Handle hasil scan ─────────────────────────────────────
    function onScanSuccess(decodedText) {
        // Hentikan scanner agar tidak scan berulang
        if (html5QrCode && scannerRunning) {
            html5QrCode.stop().catch(function () {});
            scannerRunning = false;
            document.getElementById('qr-reader-status').textContent = '✅ QR berhasil dibaca.';
        }

        let code = decodedText.trim();
        let nup  = '';

        if (code.includes('*')) {
            const parts = code.split('*');
            code = parts[0].trim();
            nup  = parts[1] ? parts[1].trim() : '';
        }

        doSearch(code, nup);
    }

    // ── Inisialisasi Scanner ──────────────────────────────────
    function startScanner() {
        if (html5QrCode && scannerRunning) return;

        html5QrCode = new Html5Qrcode('qr-reader');

        Html5Qrcode.getCameras()
            .then(function (devices) {
                if (! devices || devices.length === 0) {
                    document.getElementById('qr-reader-status').textContent =
                        '⚠️ Kamera tidak ditemukan.';
                    return;
                }

                // Pilih kamera belakang jika ada
                let cameraId = devices[0].id;
                devices.forEach(function (d) {
                    if (/(back|rear|environment)/i.test(d.label)) cameraId = d.id;
                });

                html5QrCode.start(
                    cameraId,
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    onScanSuccess,
                    function () {} // abaikan error parsial
                )
                .then(function () {
                    scannerRunning = true;
                    document.getElementById('qr-reader-status').textContent =
                        '📷 Kamera aktif — arahkan ke QR Code';
                })
                .catch(function (err) {
                    document.getElementById('qr-reader-status').textContent =
                        '⚠️ Gagal akses kamera: ' + err;
                });
            })
            .catch(function (err) {
                document.getElementById('qr-reader-status').textContent =
                    '⚠️ Tidak bisa mengakses kamera: ' + err;
            });
    }

    function stopScanner() {
        if (html5QrCode && scannerRunning) {
            html5QrCode.stop().catch(function () {});
            scannerRunning = false;
        }
    }

    // ── Event Listeners ───────────────────────────────────────
    const modalEl = document.getElementById('modalScanQR');

    modalEl.addEventListener('shown.bs.modal', function () {
        showPanel('scan-empty');
        startScanner();
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        stopScanner();
        // Reset UI
        showPanel('scan-empty');
        document.getElementById('manualQRInput').value          = '';
        document.getElementById('qr-reader-status').textContent = 'Memuat kamera...';
    });

    // Tombol scan lagi
    document.getElementById('btnScanAgain').addEventListener('click', function () {
        showPanel('scan-empty');
        document.getElementById('qr-reader-status').textContent = 'Memuat kamera...';
        startScanner();
    });

    // Pencarian manual
    document.getElementById('btnManualSearch').addEventListener('click', function () {
        const val = document.getElementById('manualQRInput').value.trim();
        if (! val) { alert('Masukkan kode terlebih dahulu.'); return; }
        onScanSuccess(val);
    });

    document.getElementById('manualQRInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') document.getElementById('btnManualSearch').click();
    });

})();
</script>