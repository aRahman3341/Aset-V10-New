@extends('layouts.app')

@section('content')
<main id="main" class="main">

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-box-seam"></i></div>
        <div>
            <h1>Barang Habis Pakai</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Habis Pakai</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<section class="section">
<div class="main-card">

    {{-- ── Toolbar ── --}}
    <div class="table-toolbar">
        <div class="toolbar-left">
            <div class="search-wrap">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input"
                       placeholder="Cari nama atau kode..." value="{{ request('query') }}">
                <button type="button" class="search-btn" id="searchBtn">Cari</button>
            </div>
            <button class="filter-toggle-btn" id="filterButton">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
        <div class="toolbar-right">
            <span class="selected-badge d-none" id="selectedBadge">
                <i class="bi bi-check2-square"></i>
                <span id="selectedCount">0</span> dipilih
            </span>
            <a href="{{ route('items.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            <button type="button" data-bs-toggle="modal" data-bs-target="#ModalImport" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-upload"></i> Import
            </button>
            <button type="button" data-bs-toggle="modal" data-bs-target="#ModalExport" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export
            </button>
            <button onclick="doQRCodes()" class="btn btn-outline-secondary btn-sm" title="Cetak QR">
                <i class="bi bi-qr-code"></i> QR
            </button>
            <button onclick="doMultiDelete()" class="btn btn-outline-danger btn-sm" title="Hapus terpilih">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
    </div>

    {{-- ── Filter Panel ── --}}
    <div id="filterFields" style="display:none" class="filter-panel">
        <div class="filter-panel-header">
            <span><i class="bi bi-funnel-fill"></i> Filter Lanjutan</span>
            <button type="button" class="filter-reset-btn" id="resetFilter">
                <i class="bi bi-x-circle"></i> Reset
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-3">
                <label class="filter-label">Kategori</label>
                <select class="form-select form-select-sm" id="filterKategori">
                    <option value="">Semua Kategori</option>
                    <option value="ATK" {{ request('categories') == 'ATK' ? 'selected' : '' }}>ATK</option>
                    <option value="Rumah Tangga" {{ request('categories') == 'Rumah Tangga' ? 'selected' : '' }}>Rumah Tangga</option>
                    <option value="Laboratorium" {{ request('categories') == 'Laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Status</label>
                <select class="form-select form-select-sm" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="1">Teregister</option>
                    <option value="0">Belum</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-sm w-100" id="applyFilter">
                    <i class="bi bi-funnel-fill"></i> Terapkan
                </button>
            </div>
        </div>
    </div>

    {{-- ── Table ── --}}
    <div id="tableContainer">
        @include('asetHabisPakai.table', ['items' => $items])
    </div>

    {{-- ══ FOOTER: Pagination + Jumlah ══ --}}
    <div class="table-footer">

        {{-- Pagination --}}
        <div id="paginationContainer" class="pag-nav">
            @include('asetHabisPakai.pagenation', ['items' => $items])
        </div>

        {{-- Jumlah per kategori --}}
        <div class="footer-summary">
            <div class="fs-item">
                <div class="fs-num fs-total">{{ $countATK + $countRT + $countLab }}</div>
                <div class="fs-lbl">Total</div>
            </div>
            <div class="fs-sep"></div>
            <div class="fs-item">
                <div class="fs-num fs-atk">{{ $countATK }}</div>
                <div class="fs-lbl"><i class="bi bi-pen-fill text-primary"></i> ATK</div>
            </div>
            <div class="fs-item">
                <div class="fs-num fs-rt">{{ $countRT }}</div>
                <div class="fs-lbl"><i class="bi bi-house-fill text-success"></i> RT</div>
            </div>
            <div class="fs-item">
                <div class="fs-num fs-lab">{{ $countLab }}</div>
                <div class="fs-lbl"><i class="bi bi-eyedropper-fill text-warning"></i> Lab</div>
            </div>
        </div>

    </div>

</div>
</section>

{{-- Import Modal --}}
@include('asetHabisPakai.import')

{{-- Export Modal --}}
@include('asetHabisPakai.export')

{{-- Form tersembunyi untuk multi-action (QR & multiDelete) --}}
<form id="multiForm" method="POST" style="display:none">
    @csrf
    <div id="multiInputs"></div>
</form>

{{-- Form single delete --}}
<form id="deleteForm" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

</main>

<style>
/* ── Page Title ── */
.pagetitle { display:flex; align-items:center; margin-bottom:20px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon { width:44px;height:44px;background:linear-gradient(135deg,#1e3a5f,#2d5a8e);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.15rem;box-shadow:0 4px 12px rgba(30,58,95,0.22); }
.pagetitle h1 { font-size:1.25rem;font-weight:800;color:#1e3a5f;margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0;padding:0;background:transparent;font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#2d5a8e;text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

/* ── Main card ── */
.main-card { background:#fff;border-radius:14px;border:1px solid rgba(30,58,95,0.07);box-shadow:0 2px 14px rgba(30,58,95,0.07);overflow:hidden; }

/* ── Toolbar ── */
.table-toolbar { display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-bottom:1px solid rgba(30,58,95,0.07);gap:8px;flex-wrap:wrap; }
.toolbar-left  { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
.toolbar-right { display:flex;align-items:center;gap:5px;flex-wrap:wrap; }

.search-wrap { display:flex;align-items:center;background:#f4f6fb;border:1.5px solid rgba(30,58,95,0.10);border-radius:8px;overflow:hidden;height:34px; }
.search-icon { padding:0 9px;color:#8a96a3;font-size:0.85rem; }
.search-input { border:none;background:transparent;font-size:0.82rem;padding:0 6px;width:190px;outline:none;color:#1e3a5f; }
.search-btn { background:#1e3a5f;color:#fff;border:none;padding:0 12px;font-size:0.78rem;font-weight:600;height:100%;cursor:pointer;transition:background .18s; }
.search-btn:hover { background:#2d5a8e; }

.filter-toggle-btn { height:34px;padding:0 11px;background:#f4f6fb;border:1.5px solid rgba(30,58,95,0.10);border-radius:8px;font-size:0.78rem;font-weight:600;color:#1e3a5f;cursor:pointer;display:flex;align-items:center;gap:5px;transition:all .18s; }
.filter-toggle-btn:hover { background:#e8ecf5; }

.selected-badge { display:inline-flex;align-items:center;gap:5px;background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;border-radius:20px;padding:3px 11px;font-size:0.76rem;font-weight:700; }

/* ── Filter panel ── */
.filter-panel { background:#f8fafd;border-bottom:1px solid rgba(30,58,95,0.07);padding:12px 18px; }
.filter-panel-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;font-size:0.8rem;font-weight:700;color:#5a6a7e; }
.filter-label { font-size:0.71rem;font-weight:700;color:#7a8a9e;text-transform:uppercase;letter-spacing:0.4px;display:block;margin-bottom:3px; }
.filter-reset-btn { background:none;border:none;font-size:0.75rem;color:#dc2626;cursor:pointer;display:flex;align-items:center;gap:4px;font-weight:600;padding:0; }

/* ── Table ── */
.table thead th { background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;font-size:0.69rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:10px 11px;border:none;white-space:nowrap; }
.table tbody td { padding:9px 11px;vertical-align:middle;font-size:0.83rem;border-bottom:1px solid rgba(30,58,95,0.05); }
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(30,58,95,0.025); }
tr.row-checked td { background:rgba(30,58,95,0.04) !important; }

/* Kode — spasi setiap 4 karakter agar mudah dibaca */
.code-badge {
    font-family: 'DM Mono','Courier New',monospace;
    font-size: 0.75rem;
    font-weight: 600;
    color: #1e3a5f;
    background: rgba(30,58,95,0.07);
    border: 1px solid rgba(30,58,95,0.12);
    padding: 3px 8px;
    border-radius: 6px;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.bcat { display:inline-block;font-size:0.68rem;font-weight:700;padding:2px 9px;border-radius:20px; }
.bcat-atk  { background:rgba(65,84,241,0.10);color:#4154f1; }
.bcat-rt   { background:rgba(16,185,129,0.12);color:#10b981; }
.bcat-lab  { background:rgba(255,119,29,0.10);color:#ff771d; }
.bcat-default { background:#f0f2f5;color:#6c757d; }

.bstatus { display:inline-block;font-size:0.68rem;font-weight:700;padding:2px 9px;border-radius:20px; }
.bstatus-ok      { background:#e1f7ef;color:#10b981; }
.bstatus-pending { background:#fff4e5;color:#f59e0b; }

.action-group { display:flex;align-items:center;justify-content:center;gap:4px; }
.abtn { width:28px;height:28px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;border:none;cursor:pointer;transition:all .15s;background:transparent; }
.abtn-edit { color:#c49a2a; } .abtn-edit:hover { background:rgba(232,184,75,0.15); }
.abtn-del  { color:#dc2626; } .abtn-del:hover  { background:rgba(220,38,38,0.10); }

.empty-row { text-align:center;padding:44px 0 !important;color:#8a96a3; }
.empty-row i { font-size:2.2rem;display:block;margin-bottom:8px; }

/* ── Table Footer ── */
.table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 2px solid rgba(30,58,95,0.06);
    background: #fafbfd;
    border-radius: 0 0 14px 14px;
    min-height: 62px;
    flex-wrap: wrap;
}

/* Pagination */
.pag-nav { display:flex;align-items:center;padding:10px 14px;flex:1;flex-wrap:wrap;gap:4px; }
.pag-list { display:flex;align-items:center;gap:3px;list-style:none;margin:0;padding:0; }
.pag-btn {
    display:inline-flex;align-items:center;justify-content:center;
    min-width:34px;height:34px;padding:0 9px;border-radius:8px;
    font-size:0.8rem;font-weight:700;color:#1e3a5f;
    background:#fff;border:1.5px solid rgba(30,58,95,0.13);
    text-decoration:none;transition:all .15s ease;cursor:pointer;
}
.pag-btn:hover:not(.pag-btn-active) {
    background:#1e3a5f;color:#fff;border-color:#1e3a5f;
    text-decoration:none;transform:translateY(-1px);
    box-shadow:0 3px 10px rgba(30,58,95,0.20);
}
.pag-btn-icon { min-width:34px;padding:0;color:#5a6a7e; }
.pag-btn-active {
    background:linear-gradient(135deg,#1e3a5f,#2d5a8e) !important;
    color:#fff !important;border-color:transparent !important;
    box-shadow:0 3px 12px rgba(30,58,95,0.28) !important;
    transform:translateY(-1px) scale(1.06) !important;
    min-width:38px;height:38px;font-size:0.85rem;
}
.pag-disabled .pag-btn { opacity:.3;cursor:not-allowed;pointer-events:none; }
.pag-ellipsis span { display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;color:#a0aab4;font-size:0.9rem;letter-spacing:2px; }
.pag-info { font-size:0.74rem;color:#8a96a3;margin-left:8px;white-space:nowrap; }
.pag-info strong { color:#1e3a5f; }

/* Jumlah per kategori */
.footer-summary {
    display:flex;align-items:center;
    padding:10px 18px;
    border-left:1px solid rgba(30,58,95,0.08);
    gap:0;flex-shrink:0;
}
.fs-item { display:flex;flex-direction:column;align-items:center;padding:4px 14px;min-width:60px; }
.fs-num  { font-size:1.4rem;font-weight:800;line-height:1;letter-spacing:-0.5px; }
.fs-lbl  { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-top:2px;color:#8a96a3;display:flex;align-items:center;gap:3px; }
.fs-sep  { width:1px;height:38px;background:rgba(30,58,95,0.09);margin:0 2px; }
.fs-total { color:#1e3a5f; }
.fs-atk   { color:#4154f1; }
.fs-rt    { color:#10b981; }
.fs-lab   { color:#ff771d; }

/* Loading */
#tableContainer { position:relative;min-height:120px; }
.tbl-loading { position:absolute;inset:0;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;z-index:5; }
.tbl-spinner { width:30px;height:30px;border:3px solid rgba(30,58,95,0.12);border-top-color:#1e3a5f;border-radius:50%;animation:spin .7s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }

@media (max-width:768px) {
    .table-toolbar { flex-direction:column;align-items:stretch; }
    .toolbar-right { justify-content:flex-start; }
    .search-input { width:130px; }
    .table-footer { flex-direction:column;align-items:stretch; }
    .footer-summary { border-left:none;border-top:1px solid rgba(30,58,95,0.07);justify-content:center; }
    .pag-nav { justify-content:center; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
// ═══════════════════════════════════
//  State centang lintas halaman
// ═══════════════════════════════════
const checkedIds = new Set();

// Format kode: setiap 4 karakter diberi spasi
function formatCode(raw) {
    if (!raw || raw.length <= 4) return raw;
    return raw.replace(/(.{4})/g, '$1 ').trim();
}

function applyCodeFormat() {
    document.querySelectorAll('.code-badge').forEach(el => {
        const raw = el.dataset.raw || el.textContent.trim();
        el.dataset.raw = raw;
        el.textContent = formatCode(raw);
    });
}

function updateBadge() {
    const n = checkedIds.size;
    const badge = document.getElementById('selectedBadge');
    document.getElementById('selectedCount').textContent = n;
    badge.classList.toggle('d-none', n === 0);
}

function syncCheckboxes() {
    document.querySelectorAll('input[name="id_items[]"]').forEach(cb => {
        const id = cb.value;
        cb.checked = checkedIds.has(id);
        cb.closest('tr').classList.toggle('row-checked', cb.checked);
        // re-bind (clone trick untuk menghindari duplikat listener)
        const fresh = cb.cloneNode(true);
        cb.parentNode.replaceChild(fresh, cb);
        fresh.checked = checkedIds.has(id);
        fresh.addEventListener('change', function() {
            this.checked ? checkedIds.add(id) : checkedIds.delete(id);
            this.closest('tr').classList.toggle('row-checked', this.checked);
            updateBadge();
            syncSelectAll();
        });
    });
    const sa = document.getElementById('select_all');
    if (sa) {
        const fresh = sa.cloneNode(true);
        sa.parentNode.replaceChild(fresh, sa);
        fresh.checked = false;
        fresh.addEventListener('change', function() {
            document.querySelectorAll('input[name="id_items[]"]').forEach(cb => {
                cb.checked = this.checked;
                this.checked ? checkedIds.add(cb.value) : checkedIds.delete(cb.value);
                cb.closest('tr').classList.toggle('row-checked', this.checked);
            });
            updateBadge();
        });
    }
}

function syncSelectAll() {
    const all = document.querySelectorAll('input[name="id_items[]"]');
    const chk = document.querySelectorAll('input[name="id_items[]"]:checked');
    const sa  = document.getElementById('select_all');
    if (sa) sa.checked = all.length > 0 && all.length === chk.length;
}

// ═══════════════════════════════════
//  AJAX load tabel
// ═══════════════════════════════════
let curPage     = 1;
let curQuery    = '{{ request("query", "") }}';
let curKategori = '{{ request("categories", "") }}';
let curStatus   = '{{ request("status", "") }}';

function loadTable(page) {
    curPage = page || 1;
    const container = document.getElementById('tableContainer');
    const spin = document.createElement('div');
    spin.className = 'tbl-loading';
    spin.innerHTML = '<div class="tbl-spinner"></div>';
    container.appendChild(spin);

    const params = new URLSearchParams({
        page: curPage, query: curQuery,
        categories: curKategori, status: curStatus, ajax: 1
    });

    fetch(`{{ route('items.index') }}?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        container.innerHTML = data.table;
        document.getElementById('paginationContainer').innerHTML = data.pagination;
        applyCodeFormat();
        syncCheckboxes();
        updateBadge();
        bindDeleteButtons();
        bindPaginationLinks();
    })
    .catch(() => {
        window.location.href = `{{ route('items.index') }}?page=${curPage}&query=${curQuery}`;
    })
    .finally(() => {
        container.querySelector('.tbl-loading')?.remove();
    });
}

function bindPaginationLinks() {
    document.querySelectorAll('.pag-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const pg = parseInt(link.dataset.page);
            if (pg) loadTable(pg);
        });
    });
}

// ═══════════════════════════════════
//  Single delete
// ═══════════════════════════════════
function bindDeleteButtons() {
    document.querySelectorAll('.delete-button').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                title: 'Hapus Barang?', text: 'Data tidak bisa dikembalikan!',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#1e3a5f', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
            }).then(r => {
                if (!r.isConfirmed) return;
                const form = document.getElementById('deleteForm');
                form.action = `/items/${id}`;
                form.submit();
            });
        });
    });
}

// ═══════════════════════════════════
//  Multi actions
// ═══════════════════════════════════
function buildMultiForm(action, target) {
    const form   = document.getElementById('multiForm');
    const inputs = document.getElementById('multiInputs');
    inputs.innerHTML = '';
    checkedIds.forEach(id => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'id_items[]';
        inp.value = id;
        inputs.appendChild(inp);
    });
    form.action = action;
    form.target = target || '_self';
    form.submit();
}

function doQRCodes() {
    if (checkedIds.size < 1) { Swal.fire('Pilih Data', 'Centang minimal satu barang.', 'info'); return; }
    buildMultiForm('{{ route("items.qrcodes") }}', '_blank');
}

function doMultiDelete() {
    if (checkedIds.size < 1) { Swal.fire('Pilih Data', 'Centang data yang ingin dihapus.', 'info'); return; }
    Swal.fire({
        title: `Hapus ${checkedIds.size} Barang?`, text: 'Data dihapus permanen.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#1e3a5f', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!'
    }).then(r => {
        if (r.isConfirmed) buildMultiForm('{{ route("items.multiDelete") }}', '_self');
    });
}

// ═══════════════════════════════════
//  Init
// ═══════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    applyCodeFormat();
    syncCheckboxes();
    bindDeleteButtons();
    bindPaginationLinks();

    document.getElementById('searchBtn').addEventListener('click', () => {
        curQuery = document.getElementById('searchInput').value;
        loadTable(1);
    });
    document.getElementById('searchInput').addEventListener('keydown', e => {
        if (e.key === 'Enter') { curQuery = e.target.value; loadTable(1); }
    });

    document.getElementById('filterButton').addEventListener('click', () => {
        const f = document.getElementById('filterFields');
        f.style.display = f.style.display === 'none' ? 'block' : 'none';
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        curKategori = document.getElementById('filterKategori').value;
        curStatus   = document.getElementById('filterStatus').value;
        loadTable(1);
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('filterKategori').value = '';
        document.getElementById('filterStatus').value   = '';
        curKategori = ''; curStatus = '';
        loadTable(1);
    });
});
</script>
@endsection