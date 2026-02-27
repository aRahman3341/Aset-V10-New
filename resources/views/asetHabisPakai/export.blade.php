{{-- resources/views/asetHabisPakai/export.blade.php --}}
<div class="modal fade" id="ModalExport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden;">

            {{-- Header --}}
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3a5f,#2d5a8e);border:none;padding:16px 22px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px;height:34px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-file-earmark-excel" style="color:#fff;font-size:1rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="color:#fff;font-size:0.95rem;font-weight:700;">Export Data Barang</h5>
                        <small style="color:rgba(255,255,255,0.65);font-size:0.73rem;">Pilih metode export</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">

                {{-- ── TAB: Opsi export ── --}}
                <div class="export-tabs" id="exportTabBtns">
                    <button class="export-tab-btn active" data-tab="bySelected">
                        <i class="bi bi-check2-square"></i>
                        Export Barang Terpilih
                    </button>
                    <button class="export-tab-btn" data-tab="byCategory">
                        <i class="bi bi-tags"></i>
                        Export per Kategori
                    </button>
                </div>

                {{-- ── PANEL 1: Export terpilih ── --}}
                <div class="export-panel" id="panelBySelected">
                    <div class="export-info-box" id="exportSelectedInfo">
                        <i class="bi bi-info-circle-fill" style="color:#2d5a8e;font-size:1.1rem;flex-shrink:0;"></i>
                        <div>
                            <div id="exportSelectedMsg" style="font-size:0.84rem;font-weight:600;color:#1e3a5f;">
                                Belum ada barang yang dipilih
                            </div>
                            <div style="font-size:0.74rem;color:#8a96a3;margin-top:2px;">
                                Centang barang di tabel terlebih dahulu, lalu klik Export
                            </div>
                        </div>
                    </div>

                    {{-- Preview list item terpilih --}}
                    <div id="exportSelectedList" style="display:none;margin-top:10px;">
                        <div style="font-size:0.73rem;font-weight:700;color:#5a6a7e;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
                            Barang yang akan diexport:
                        </div>
                        <div id="exportSelectedItems"
                             style="max-height:160px;overflow-y:auto;background:#f8fafd;border-radius:8px;border:1px solid rgba(30,58,95,0.10);padding:6px 10px;">
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm flex-fill" id="btnExportSelected">
                            <i class="bi bi-download"></i> Export Terpilih
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>

                {{-- ── PANEL 2: Export per kategori ── --}}
                <div class="export-panel" id="panelByCategory" style="display:none">
                    <form id="exportCategoryForm" method="GET" action="{{ route('items.export') }}" target="_blank">
                        <label class="export-label">Pilih Kategori</label>
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <div class="cat-option-group">
                                    <label class="cat-option">
                                        <input type="radio" name="categories" value="ATK" required>
                                        <span class="cat-option-box">
                                            <i class="bi bi-pen-fill" style="color:#4154f1"></i>
                                            <span class="cat-option-name">ATK</span>
                                            <span class="cat-option-desc">Alat Tulis Kantor</span>
                                        </span>
                                    </label>
                                    <label class="cat-option">
                                        <input type="radio" name="categories" value="Rumah Tangga" required>
                                        <span class="cat-option-box">
                                            <i class="bi bi-house-fill" style="color:#10b981"></i>
                                            <span class="cat-option-name">Rumah Tangga</span>
                                            <span class="cat-option-desc">Perlengkapan RT</span>
                                        </span>
                                    </label>
                                    <label class="cat-option">
                                        <input type="radio" name="categories" value="Laboratorium" required>
                                        <span class="cat-option-box">
                                            <i class="bi bi-eyedropper-fill" style="color:#ff771d"></i>
                                            <span class="cat-option-name">Laboratorium</span>
                                            <span class="cat-option-desc">Peralatan Lab</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-sm flex-fill">
                                <i class="bi bi-file-earmark-arrow-down"></i> Export Kategori
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Form POST tersembunyi untuk export terpilih --}}
<form id="exportSelectedForm" method="POST" action="{{ route('items.exportSelected') }}" style="display:none">
    @csrf
    <div id="exportSelectedInputs"></div>
</form>

<style>
.export-tabs {
    display: flex;
    border-bottom: 2px solid rgba(30,58,95,0.08);
    background: #fafbfd;
}
.export-tab-btn {
    flex: 1;
    border: none;
    background: transparent;
    padding: 11px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #8a96a3;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all .18s;
}
.export-tab-btn.active {
    color: #1e3a5f;
    border-bottom-color: #1e3a5f;
    background: #fff;
}
.export-tab-btn:hover:not(.active) { background: #f0f3f8; color: #3d5170; }

.export-panel { padding: 18px 22px 20px; }
.export-label { font-size: 0.73rem; font-weight: 700; color: #5a6a7e; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 10px; }

.export-info-box {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #eef2f8;
    border: 1px solid rgba(30,58,95,0.12);
    border-radius: 10px;
    padding: 12px 14px;
}

/* Kategori radio cards */
.cat-option-group { display: flex; flex-direction: column; gap: 8px; }
.cat-option { cursor: pointer; margin: 0; }
.cat-option input[type="radio"] { display: none; }
.cat-option-box {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1.5px solid rgba(30,58,95,0.12);
    background: #fff;
    transition: all .15s;
    user-select: none;
}
.cat-option input:checked + .cat-option-box {
    border-color: #1e3a5f;
    background: rgba(30,58,95,0.04);
    box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
}
.cat-option-box:hover { border-color: #2d5a8e; background: #f8fafd; }
.cat-option-box i { font-size: 1.1rem; flex-shrink: 0; }
.cat-option-name { font-size: 0.84rem; font-weight: 700; color: #1e3a5f; }
.cat-option-desc { font-size: 0.72rem; color: #8a96a3; margin-left: auto; }
</style>

<script>
// ── Tab switching ──
document.querySelectorAll('.export-tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.export-tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const tab = this.dataset.tab;
        document.getElementById('panelBySelected').style.display = tab === 'bySelected' ? 'block' : 'none';
        document.getElementById('panelByCategory').style.display = tab === 'byCategory' ? 'block' : 'none';
    });
});

// ── Saat modal dibuka, update info barang terpilih ──
document.getElementById('ModalExport').addEventListener('show.bs.modal', function () {
    const count = typeof checkedIds !== 'undefined' ? checkedIds.size : 0;
    const msg   = document.getElementById('exportSelectedMsg');
    const list  = document.getElementById('exportSelectedList');
    const items = document.getElementById('exportSelectedItems');

    if (count === 0) {
        msg.textContent = 'Belum ada barang yang dipilih';
        msg.style.color = '#8a96a3';
        list.style.display = 'none';
        document.getElementById('btnExportSelected').disabled = true;
        document.getElementById('btnExportSelected').classList.replace('btn-primary','btn-secondary');
    } else {
        msg.textContent = `${count} barang siap diexport`;
        msg.style.color = '#1e3a5f';
        document.getElementById('btnExportSelected').disabled = false;
        document.getElementById('btnExportSelected').classList.replace('btn-secondary','btn-primary');

        // Tampilkan nama barang dari baris tabel yang diceklis
        items.innerHTML = '';
        let shown = 0;
        document.querySelectorAll('input[name="id_items[]"]:checked').forEach(cb => {
            const row  = cb.closest('tr');
            const nama = row ? row.cells[3]?.textContent?.trim() : '';
            const kode = row ? row.querySelector('.code-badge')?.dataset?.raw || '' : '';
            if (nama && shown < 8) {
                items.innerHTML += `<div style="font-size:0.78rem;padding:3px 0;border-bottom:1px solid rgba(30,58,95,0.06);color:#3d5170;">
                    <span style="font-family:monospace;font-size:0.72rem;color:#2d5a8e;background:rgba(30,58,95,0.07);padding:1px 5px;border-radius:4px;margin-right:6px;">${kode}</span>${nama}
                </div>`;
                shown++;
            }
        });
        if (count > 8) {
            items.innerHTML += `<div style="font-size:0.74rem;color:#8a96a3;padding-top:4px;text-align:center;">... dan ${count - 8} barang lainnya</div>`;
        }
        list.style.display = 'block';
    }
});

// ── Tombol Export Terpilih ──
document.getElementById('btnExportSelected').addEventListener('click', function () {
    if (typeof checkedIds === 'undefined' || checkedIds.size === 0) return;
    const form   = document.getElementById('exportSelectedForm');
    const inputs = document.getElementById('exportSelectedInputs');
    inputs.innerHTML = '';
    checkedIds.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'id_items[]'; inp.value = id;
        inputs.appendChild(inp);
    });
    // Submit → download langsung (controller return Excel::download)
    form.submit();
    bootstrap.Modal.getInstance(document.getElementById('ModalExport')).hide();
});
</script>