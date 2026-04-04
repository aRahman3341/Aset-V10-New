@extends('layouts.app')
@section('content')

<main id="main" class="main">

<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-building"></i></div>
        <div>
            <h1>Aset Tetap</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Aset</li>
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
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0"
                       placeholder="Cari Kode, NUP, atau Nama..." value="{{ request('query') }}">
                <button type="button" class="btn btn-outline-secondary" id="filterButton" title="Filter Lanjutan">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </div>
        <div class="toolbar-right">
            <span class="selected-badge d-none me-1" id="selectedBadge">
                <i class="bi bi-check2-square"></i>
                <span id="selectedCount">0</span> dipilih
            </span>
            <a href="{{ route('asetTetap.create') }}" class="btn btn-success btn-sm me-1 shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah
            </a>
            <a href="{{ route('asetTetap.import') }}" class="btn btn-success btn-sm me-1">
                <i class="bi bi-file-earmark-arrow-down"></i> Import
            </a>
            <button onclick="exportAset('{{ route('asetTetap.export') }}')" class="btn btn-primary btn-sm me-1">
                <i class="bi bi-file-earmark-arrow-up"></i> Export
            </button>
            <button onclick="generateQRCodes('{{ route('generate_qrcodes') }}')" class="btn btn-info btn-sm me-1">
                <i class="bi bi-qr-code"></i> QR
            </button>
            <button onclick="doMultiDelete()" class="btn btn-danger btn-sm">
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
                <label class="filter-label">Jenis BMN</label>
                <select class="form-select form-select-sm" id="filterJenis">
                    <option value="">Semua Jenis</option>
                    <option value="ALAT BESAR">Alat Besar</option>
                    <option value="ALAT ANGKUTAN BERMOTOR">Alat Angkutan Bermotor</option>
                    <option value="BANGUNAN DAN GEDUNG">Bangunan dan Gedung</option>
                    <option value="MESIN PERALATAN KHUSUS TIK">Mesin Peralatan TIK</option>
                    <option value="MESIN PERALATAN NON TIK">Mesin Peralatan Non TIK</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="filter-label">Kondisi</label>
                <select class="form-select form-select-sm" id="filterKondisi">
                    <option value="">Semua</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="filter-label">Status BMN</label>
                <select class="form-select form-select-sm" id="filterStatusBmn">
                    <option value="">Semua</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-sm w-100" id="applyFilter">
                    <i class="bi bi-funnel-fill"></i> Terapkan
                </button>
            </div>
        </div>
    </div>

    {{-- ── Table Container ── --}}
    <div id="tableContainer">
        @include('asetTetap.table', ['items' => $items])
    </div>

    {{-- ── Footer: Pagination ── --}}
    <div class="table-footer">
        <div id="paginationContainer" class="pag-nav">
            @include('asetTetap.pagenation', ['items' => $items])
        </div>
    </div>

</div>
</section>

{{-- Form tersembunyi untuk multi-action --}}
<form id="multiForm" method="POST" style="display:none">
    @csrf
    <div id="multiInputs"></div>
</form>

{{-- Form single delete --}}
<form id="deleteForm" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

@include('asetTetap.scane')

</main>

<style>
/* ── Page Title ── */
.pagetitle { display:flex; align-items:center; margin-bottom:20px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon {
    width:44px; height:44px; background:linear-gradient(135deg,#012970,#4154f1);
    border-radius:12px; display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:1.15rem; box-shadow:0 4px 12px rgba(1,41,112,0.22);
}
.pagetitle h1 { font-size:1.25rem; font-weight:800; color:#012970; margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0; padding:0; background:transparent; font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#4154f1; text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

/* ── Main card ── */
.main-card {
    background:#fff; border-radius:10px;
    border:1px solid rgba(1,41,112,0.07);
    box-shadow:0 2px 14px rgba(1,41,112,0.07); overflow:hidden;
}

/* ── Toolbar ── */
.table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px; border-bottom:1px solid rgba(1,41,112,0.07);
    gap:8px; flex-wrap:wrap;
}
.toolbar-left  { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.toolbar-right { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }

.selected-badge {
    display:inline-flex; align-items:center; gap:5px;
    background:linear-gradient(135deg,#012970,#4154f1);
    color:#fff; border-radius:20px; padding:3px 11px;
    font-size:0.76rem; font-weight:700;
}

/* ── Filter panel ── */
.filter-panel {
    background:#f8fafd; border-bottom:1px solid rgba(1,41,112,0.07);
    padding:12px 18px;
}
.filter-panel-header {
    display:flex; justify-content:space-between; align-items:center;
    margin-bottom:10px; font-size:0.8rem; font-weight:700; color:#5a6a7e;
}
.filter-label {
    font-size:0.71rem; font-weight:700; color:#7a8a9e;
    text-transform:uppercase; letter-spacing:0.4px;
    display:block; margin-bottom:3px;
}
.filter-reset-btn {
    background:none; border:none; font-size:0.75rem; color:#dc2626;
    cursor:pointer; display:flex; align-items:center;
    gap:4px; font-weight:600; padding:0;
}

/* ── Table ── */
.table thead th {
    background-color:#f6f9ff; color:#012970;
    font-weight:700; text-transform:uppercase;
    font-size:0.69rem; letter-spacing:0.5px;
    padding:10px 11px; border:none; white-space:nowrap;
    border-bottom:2px solid #e0e8f5;
}
.table tbody td {
    padding:9px 11px; vertical-align:middle;
    font-size:0.8rem; border-bottom:1px solid rgba(1,41,112,0.05);
    white-space:nowrap;
}
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(65,84,241,0.03); }
tr.row-checked td { background:rgba(65,84,241,0.05) !important; }

.code-text {
    font-family:'DM Mono','Courier New',monospace;
    font-size:0.75rem; font-weight:600; color:#012970;
}
.badge-jenis {
    display:inline-block; font-size:0.68rem; font-weight:700; padding:2px 8px;
    border-radius:20px; background:rgba(1,41,112,0.08); color:#012970;
}
.action-group { display:flex; align-items:center; justify-content:center; gap:4px; }
.abtn {
    width:28px; height:28px; border-radius:6px; display:inline-flex; align-items:center;
    justify-content:center; font-size:0.8rem; border:none; cursor:pointer;
    transition:all .15s; background:transparent;
}
.abtn-edit { color:#c49a2a; } .abtn-edit:hover { background:rgba(232,184,75,0.15); }
.abtn-del  { color:#dc2626; } .abtn-del:hover  { background:rgba(220,38,38,0.10); }

/* ── Table Footer ── */
.table-footer {
    display:flex; align-items:center; justify-content:space-between;
    border-top:2px solid rgba(1,41,112,0.06);
    background:#fafbfd; border-radius:0 0 10px 10px;
    min-height:62px; flex-wrap:wrap;
}

/* ── Pagination ── */
.pag-nav { display:flex; align-items:center; padding:10px 14px; flex:1; flex-wrap:wrap; gap:4px; }
.pag-list { display:flex; align-items:center; gap:3px; list-style:none; margin:0; padding:0; }
.pag-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:34px; height:34px; padding:0 9px; border-radius:6px;
    font-size:0.8rem; font-weight:700; color:#012970;
    background:#fff; border:1.5px solid rgba(1,41,112,0.13);
    text-decoration:none; transition:all .15s ease; cursor:pointer;
}
.pag-btn:hover:not(.pag-btn-active) {
    background:#012970; color:#fff; border-color:#012970;
    text-decoration:none; transform:translateY(-1px);
    box-shadow:0 3px 10px rgba(1,41,112,0.20);
}
.pag-btn-icon { min-width:34px; padding:0; color:#5a6a7e; }
.pag-btn-active {
    background:linear-gradient(135deg,#012970,#4154f1) !important;
    color:#fff !important; border-color:transparent !important;
    box-shadow:0 3px 12px rgba(65,84,241,0.28) !important;
    transform:translateY(-1px) scale(1.06) !important;
    min-width:38px; height:38px; font-size:0.85rem;
}
.pag-disabled .pag-btn { opacity:.3; cursor:not-allowed; pointer-events:none; }
.pag-ellipsis span {
    display:inline-flex; align-items:center; justify-content:center;
    width:34px; height:34px; color:#a0aab4; font-size:0.9rem; letter-spacing:2px;
}
.pag-info { font-size:0.74rem; color:#8a96a3; margin-left:8px; white-space:nowrap; }
.pag-info strong { color:#012970; }

/* Loading */
#tableContainer { position:relative; min-height:120px; }
.tbl-loading { position:absolute; inset:0; background:rgba(255,255,255,0.8); display:flex; align-items:center; justify-content:center; z-index:5; }
.tbl-spinner { width:30px; height:30px; border:3px solid rgba(1,41,112,0.12); border-top-color:#012970; border-radius:50%; animation:spin .7s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }

@media (max-width:768px) {
    .table-toolbar { flex-direction:column; align-items:stretch; }
    .toolbar-right { justify-content:flex-start; }
    .table-footer { flex-direction:column; align-items:stretch; }
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

function updateBadge() {
    const n = checkedIds.size;
    const badge = document.getElementById('selectedBadge');
    document.getElementById('selectedCount').textContent = n;
    badge.classList.toggle('d-none', n === 0);
}

function syncCheckboxes() {
    document.querySelectorAll('input[name="id_aset[]"]').forEach(cb => {
        const id = cb.value;
        cb.checked = checkedIds.has(id);
        cb.closest('tr').classList.toggle('row-checked', cb.checked);
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
            document.querySelectorAll('input[name="id_aset[]"]').forEach(cb => {
                cb.checked = this.checked;
                this.checked ? checkedIds.add(cb.value) : checkedIds.delete(cb.value);
                cb.closest('tr').classList.toggle('row-checked', this.checked);
            });
            updateBadge();
        });
    }
}

function syncSelectAll() {
    const all = document.querySelectorAll('input[name="id_aset[]"]');
    const chk = document.querySelectorAll('input[name="id_aset[]"]:checked');
    const sa  = document.getElementById('select_all');
    if (sa) sa.checked = all.length > 0 && all.length === chk.length;
}

// ═══════════════════════════════════
//  AJAX load tabel
// ═══════════════════════════════════
let curPage   = 1;
let curQuery  = '{{ request("query", "") }}';
let curJenis  = '{{ request("jenis_bmn", "") }}';
let curKondisi = '{{ request("kondisi", "") }}';
let curStatusBmn = '{{ request("status_bmn", "") }}';

function loadTable(page) {
    curPage = page || 1;
    const container = document.getElementById('tableContainer');
    const spin = document.createElement('div');
    spin.className = 'tbl-loading';
    spin.innerHTML = '<div class="tbl-spinner"></div>';
    container.appendChild(spin);

    const params = new URLSearchParams({
        page: curPage,
        query: curQuery,
        jenis_bmn: curJenis,
        kondisi: curKondisi,
        status_bmn: curStatusBmn,
        ajax: 1
    });

    fetch(`{{ route('asetTetap.index') }}?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        container.innerHTML = data.table;
        document.getElementById('paginationContainer').innerHTML = data.pagination;
        syncCheckboxes();
        updateBadge();
        bindDeleteButtons();
        bindPaginationLinks();
    })
    .catch(() => {
        window.location.href = `{{ route('asetTetap.index') }}?page=${curPage}&query=${curQuery}`;
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
                title: 'Hapus Aset?', text: 'Data tidak bisa dikembalikan!',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#012970', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
            }).then(r => {
                if (!r.isConfirmed) return;
                const form = document.getElementById('deleteForm');
                form.action = `/asetTetap/${id}`;
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
        inp.name  = 'id_aset[]';
        inp.value = id;
        inputs.appendChild(inp);
    });
    form.action = action;
    form.target = target || '_self';
    form.submit();
}

function generateQRCodes(url) {
    if (checkedIds.size < 1) { Swal.fire('Pilih Data', 'Centang minimal satu aset untuk mencetak QR.', 'info'); return; }
    buildMultiForm(url, '_blank');
}

function exportAset(url) {
    buildMultiForm(url, '_self');
}

function doMultiDelete() {
    if (checkedIds.size < 1) { Swal.fire('Pilih Data', 'Centang data yang ingin dihapus.', 'info'); return; }
    Swal.fire({
        title: `Hapus ${checkedIds.size} Aset?`, text: 'Data dihapus permanen.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#012970', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!'
    }).then(r => {
        if (r.isConfirmed) buildMultiForm('{{ route("asetTetap.multiDelete") }}', '_self');
    });
}

// ═══════════════════════════════════
//  Init
// ═══════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    syncCheckboxes();
    bindDeleteButtons();
    bindPaginationLinks();

    document.getElementById('searchInput').addEventListener('keydown', e => {
        if (e.key === 'Enter') { curQuery = e.target.value; loadTable(1); }
    });

    document.getElementById('filterButton').addEventListener('click', (e) => {
        e.preventDefault();
        $('#filterFields').slideToggle();
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        curJenis     = document.getElementById('filterJenis').value;
        curKondisi   = document.getElementById('filterKondisi').value;
        curStatusBmn = document.getElementById('filterStatusBmn').value;
        loadTable(1);
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('filterJenis').value     = '';
        document.getElementById('filterKondisi').value   = '';
        document.getElementById('filterStatusBmn').value = '';
        curJenis = ''; curKondisi = ''; curStatusBmn = '';
        loadTable(1);
    });
});
</script>
@endsection