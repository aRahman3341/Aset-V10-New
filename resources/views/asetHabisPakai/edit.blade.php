@extends('layouts.app')

@section('content')
<main id="main" class="main">

{{-- ── Page Title ── --}}
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

{{-- ══════════════════════════════════════
     SUMMARY CARDS — Jumlah Per Kategori
══════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Total Keseluruhan --}}
    <div class="col-12 col-md-3">
        <div class="scard scard-total">
            <div class="scard-left">
                <div class="scard-icon"><i class="bi bi-layers-fill"></i></div>
            </div>
            <div class="scard-right">
                <div class="scard-num">{{ $countATK + $countRT + $countLab }}</div>
                <div class="scard-title">Total Barang</div>
                <div class="scard-sub">semua kategori</div>
            </div>
            <div class="scard-stripe"></div>
        </div>
    </div>

    {{-- ATK --}}
    <div class="col-12 col-md-3">
        <div class="scard scard-atk">
            <div class="scard-left">
                <div class="scard-icon"><i class="bi bi-pen-fill"></i></div>
            </div>
            <div class="scard-right">
                <div class="scard-num">{{ $countATK }}</div>
                <div class="scard-title">ATK</div>
                <div class="scard-sub">Alat Tulis Kantor</div>
            </div>
            <div class="scard-stripe"></div>
        </div>
    </div>

    {{-- Rumah Tangga --}}
    <div class="col-12 col-md-3">
        <div class="scard scard-rt">
            <div class="scard-left">
                <div class="scard-icon"><i class="bi bi-house-fill"></i></div>
            </div>
            <div class="scard-right">
                <div class="scard-num">{{ $countRT }}</div>
                <div class="scard-title">Rumah Tangga</div>
                <div class="scard-sub">Perlengkapan RT</div>
            </div>
            <div class="scard-stripe"></div>
        </div>
    </div>

    {{-- Laboratorium --}}
    <div class="col-12 col-md-3">
        <div class="scard scard-lab">
            <div class="scard-left">
                <div class="scard-icon"><i class="bi bi-eyedropper-fill"></i></div>
            </div>
            <div class="scard-right">
                <div class="scard-num">{{ $countLab }}</div>
                <div class="scard-title">Laboratorium</div>
                <div class="scard-sub">Peralatan Lab</div>
            </div>
            <div class="scard-stripe"></div>
        </div>
    </div>

</div>

{{-- ── Main Card ── --}}
<section class="section">
    <div class="main-card">

        {{-- Toolbar --}}
        <div class="table-toolbar">
            <div class="toolbar-left">
                <form action="{{ route('items.index') }}" method="get" class="search-wrap">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="query" class="search-input" placeholder="Cari nama atau kode barang..." value="{{ request('query') }}">
                    <button type="submit" class="search-btn">Cari</button>
                </form>
                <button class="filter-toggle-btn" id="filterButton">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
            <div class="toolbar-right">
                <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Tambah Baru
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#ModalImport" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import
                </button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#ModalExport" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export
                </button>
                <button onclick="generateQRCodes('{{ route('items.qrcodes') }}')" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-qr-code"></i>
                </button>
                <button onclick="multiDelete()" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        {{-- Filter Panel --}}
        <div id="filterFields" style="display: {{ request()->routeIs('items.filter') ? 'block' : 'none' }};" class="filter-panel">
            <div class="filter-panel-header">
                <span><i class="bi bi-filter-left"></i> Filter Lanjutan</span>
                <a href="{{ route('items.index') }}" class="filter-reset">Reset</a>
            </div>
            @include('asetHabisPakai.filter')
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <form action="" method="post" class="form-items">
                @csrf
                <table class="table table-hover mb-0" id="itemsTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px">
                                <input type="checkbox" id="select_all" class="form-check-input">
                            </th>
                            <th class="text-center" style="width:50px">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th class="text-center">Saldo</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Satuan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Dibuat</th>
                            <th class="text-center">Diupdate</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = (($items->currentPage() - 1) * $items->perPage()) + 1; @endphp
                        @forelse ($items as $item)
                            <tr>
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" name="id_items[]" value="{{ $item->id }}">
                                </td>
                                <td class="text-center text-muted">{{ $no }}</td>
                                <td><span class="code-badge">{{ $item->code ?? '-' }}</span></td>
                                <td>{{ Str::limit($item->name ?? '-', 50) }}</td>
                                <td class="text-center fw-bold">{{ $item->saldo ?? 0 }}</td>
                                <td class="text-center">
                                    @php $catClass = match($item->categories) { 'ATK' => 'bcat-atk', 'Rumah Tangga' => 'bcat-rt', 'Laboratorium' => 'bcat-lab', default => 'bcat-default' }; @endphp
                                    <span class="bcat {{ $catClass }}">{{ $item->categories ?? '-' }}</span>
                                </td>
                                <td class="text-center text-muted">{{ $item->satuan ?? '-' }}</td>
                                <td class="text-center">
                                    @if($item->status)
                                        <span class="bstatus bstatus-ok">Teregister</span>
                                    @else
                                        <span class="bstatus bstatus-pending">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center small text-muted">{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '-' }}</td>
                                <td class="text-center small text-muted">{{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y') : '-' }}</td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <a href="{{ route('items.edit', $item->id) }}" class="abtn abtn-edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" id="form-{{ $item->id }}" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="abtn abtn-del delete-button" data-form-id="form-{{ $item->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @php $no++; @endphp
                        @empty
                            <tr>
                                <td colspan="11" class="empty-row">
                                    <i class="bi bi-inbox"></i>
                                    <p>Data barang tidak ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>

        {{-- ══════════════════════════════════════════
             FOOTER: Info Data + Pagination
        ══════════════════════════════════════════ --}}
        <div class="table-footer">

            {{-- Kiri: info record --}}
            <div class="footer-info">
                <i class="bi bi-table footer-info-icon"></i>
                <div>
                    <div class="footer-info-main">
                        Menampilkan <strong>{{ $items->firstItem() ?? 0 }}</strong> – <strong>{{ $items->lastItem() ?? 0 }}</strong>
                        dari <strong class="footer-total">{{ $items->total() }}</strong> data
                    </div>
                    <div class="footer-info-sub">
                        Halaman {{ $items->currentPage() }} dari {{ $items->lastPage() }}
                        &nbsp;·&nbsp; {{ $items->perPage() }} baris per halaman
                    </div>
                </div>
            </div>

            {{-- Tengah: Pagination --}}
            @if ($items->lastPage() > 1)
            <nav class="pag-nav">
                <ul class="pag-list">

                    {{-- First --}}
                    <li class="{{ $items->onFirstPage() ? 'pag-disabled' : '' }}">
                        @if(!$items->onFirstPage())
                            <a href="{{ $items->url(1) }}" class="pag-btn pag-btn-icon" title="Halaman pertama">
                                <i class="bi bi-chevron-double-left"></i>
                            </a>
                        @else
                            <span class="pag-btn pag-btn-icon"><i class="bi bi-chevron-double-left"></i></span>
                        @endif
                    </li>

                    {{-- Prev --}}
                    <li class="{{ $items->onFirstPage() ? 'pag-disabled' : '' }}">
                        @if(!$items->onFirstPage())
                            <a href="{{ $items->previousPageUrl() }}" class="pag-btn pag-btn-icon" title="Sebelumnya">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        @else
                            <span class="pag-btn pag-btn-icon"><i class="bi bi-chevron-left"></i></span>
                        @endif
                    </li>

                    {{-- Page numbers with ellipsis --}}
                    @php
                        $cur  = $items->currentPage();
                        $last = $items->lastPage();
                        $pages = [];
                        for ($i = 1; $i <= $last; $i++) {
                            if ($i == 1 || $i == $last || ($i >= $cur - 2 && $i <= $cur + 2)) {
                                $pages[] = $i;
                            }
                        }
                        $prev = null;
                    @endphp
                    @foreach($pages as $page)
                        @if($prev !== null && $page - $prev > 1)
                            <li class="pag-ellipsis"><span>···</span></li>
                        @endif
                        <li>
                            <a href="{{ $items->url($page) }}"
                               class="pag-btn {{ $page == $cur ? 'pag-btn-active' : '' }}">
                                {{ $page }}
                            </a>
                        </li>
                        @php $prev = $page; @endphp
                    @endforeach

                    {{-- Next --}}
                    <li class="{{ !$items->hasMorePages() ? 'pag-disabled' : '' }}">
                        @if($items->hasMorePages())
                            <a href="{{ $items->nextPageUrl() }}" class="pag-btn pag-btn-icon" title="Berikutnya">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <span class="pag-btn pag-btn-icon"><i class="bi bi-chevron-right"></i></span>
                        @endif
                    </li>

                    {{-- Last --}}
                    <li class="{{ !$items->hasMorePages() ? 'pag-disabled' : '' }}">
                        @if($items->hasMorePages())
                            <a href="{{ $items->url($last) }}" class="pag-btn pag-btn-icon" title="Halaman terakhir">
                                <i class="bi bi-chevron-double-right"></i>
                            </a>
                        @else
                            <span class="pag-btn pag-btn-icon"><i class="bi bi-chevron-double-right"></i></span>
                        @endif
                    </li>

                </ul>
            </nav>
            @endif

        </div>
        {{-- ═══ END FOOTER ═══ --}}

    </div>
</section>

@include('asetHabisPakai.import')
@include('asetHabisPakai.export')

</main>

<style>
/* ══ Page Title ══ */
.pagetitle { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; }
.pagetitle-left { display:flex; align-items:center; gap:14px; }
.pagetitle-icon { width:46px;height:46px;background:linear-gradient(135deg,#1e3a5f,#2d5a8e);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;box-shadow:0 4px 12px rgba(30,58,95,0.25);flex-shrink:0; }
.pagetitle h1 { font-size:1.3rem;font-weight:800;color:#1e3a5f;margin:0 0 4px; }
.pagetitle .breadcrumb { margin:0;padding:0;background:transparent;font-size:0.78rem; }
.pagetitle .breadcrumb-item a { color:#2d5a8e;text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

/* ══ Summary Cards ══ */
.scard {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 14px;
    border: 1px solid rgba(30,58,95,0.08);
    box-shadow: 0 2px 14px rgba(30,58,95,0.06);
    padding: 18px 20px;
    gap: 16px;
    position: relative;
    overflow: hidden;
    transition: transform .18s, box-shadow .18s;
}
.scard:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(30,58,95,0.12); }

/* Accent stripe kiri */
.scard-stripe {
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 5px; border-radius: 14px 0 0 14px;
}
.scard-total .scard-stripe { background: linear-gradient(to bottom, #1e3a5f, #2d5a8e); }
.scard-atk   .scard-stripe { background: linear-gradient(to bottom, #4154f1, #7b89f9); }
.scard-rt    .scard-stripe { background: linear-gradient(to bottom, #10b981, #2eca6a); }
.scard-lab   .scard-stripe { background: linear-gradient(to bottom, #ff771d, #ffab6e); }

.scard-left { flex-shrink: 0; padding-left: 4px; }
.scard-icon {
    width: 52px; height: 52px; border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
}
.scard-total .scard-icon { background: rgba(30,58,95,0.09);  color: #1e3a5f; }
.scard-atk   .scard-icon { background: rgba(65,84,241,0.10); color: #4154f1; }
.scard-rt    .scard-icon { background: rgba(16,185,129,0.10); color: #10b981; }
.scard-lab   .scard-icon { background: rgba(255,119,29,0.10); color: #ff771d; }

.scard-right { flex: 1; }
.scard-num   { font-size: 2rem; font-weight: 800; color: #1e3a5f; line-height: 1; letter-spacing: -1px; }
.scard-title { font-size: 0.82rem; font-weight: 700; color: #3d5170; margin-top: 3px; }
.scard-sub   { font-size: 0.7rem; color: #a0aab4; margin-top: 1px; }

/* ══ Main card ══ */
.main-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(30,58,95,0.07);
    box-shadow: 0 2px 16px rgba(30,58,95,0.07);
    overflow: hidden;
}

/* ══ Toolbar ══ */
.table-toolbar { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid rgba(30,58,95,0.07); gap:10px; flex-wrap:wrap; }
.toolbar-left  { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.toolbar-right { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.search-wrap { display:flex; align-items:center; background:#f4f6fb; border:1.5px solid rgba(30,58,95,0.10); border-radius:9px; overflow:hidden; height:36px; }
.search-icon  { padding:0 10px; color:#8a96a3; font-size:0.9rem; }
.search-input { border:none; background:transparent; font-size:0.83rem; padding:0 6px; width:200px; outline:none; color:#1e3a5f; }
.search-btn   { background:#1e3a5f; color:#fff; border:none; padding:0 14px; font-size:0.8rem; font-weight:600; height:100%; cursor:pointer; transition:background .18s; }
.search-btn:hover { background:#2d5a8e; }
.filter-toggle-btn { height:36px; padding:0 12px; background:#f4f6fb; border:1.5px solid rgba(30,58,95,0.10); border-radius:9px; font-size:0.8rem; font-weight:600; color:#1e3a5f; cursor:pointer; transition:all .18s; display:flex; align-items:center; gap:6px; }
.filter-toggle-btn:hover { background:#e8ecf5; }

/* Filter panel */
.filter-panel { background:#f8fafd; border-bottom:1px solid rgba(30,58,95,0.08); padding:14px 20px; }
.filter-panel-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; font-size:0.82rem; font-weight:700; color:#5a6a7e; }
.filter-reset { font-size:0.78rem; color:#dc2626; text-decoration:none; }

/* ══ Table ══ */
.table thead th { background:linear-gradient(135deg,#1e3a5f,#2d5a8e); color:#fff; font-size:0.71rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; padding:11px 12px; border:none; white-space:nowrap; }
.table tbody td { padding:10px 12px; vertical-align:middle; font-size:0.84rem; border-bottom:1px solid rgba(30,58,95,0.05); }
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(30,58,95,0.025); }

.code-badge { font-family:'DM Mono',monospace; font-size:0.78rem; font-weight:600; color:#2d5a8e; background:rgba(45,90,142,0.08); padding:3px 8px; border-radius:5px; }

.bcat { display:inline-block; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; }
.bcat-atk  { background:rgba(65,84,241,0.10); color:#4154f1; }
.bcat-rt   { background:rgba(16,185,129,0.12); color:#10b981; }
.bcat-lab  { background:rgba(255,119,29,0.10); color:#ff771d; }
.bcat-default { background:#f0f2f5; color:#6c757d; }

.bstatus { display:inline-block; font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:20px; }
.bstatus-ok      { background:#e1f7ef; color:#10b981; }
.bstatus-pending { background:#fff4e5; color:#f59e0b; }

.action-group { display:flex; align-items:center; justify-content:center; gap:5px; }
.abtn { width:30px; height:30px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:0.82rem; border:none; cursor:pointer; transition:all .15s; background:transparent; }
.abtn-edit  { color:#c49a2a; } .abtn-edit:hover  { background:rgba(232,184,75,0.15); }
.abtn-del   { color:#dc2626; } .abtn-del:hover   { background:rgba(220,38,38,0.10); }
.empty-row  { text-align:center; padding:48px 0 !important; color:#8a96a3; }
.empty-row i { font-size:2.5rem; display:block; margin-bottom:8px; }

/* ══════════════════════════
   TABLE FOOTER (KEY SECTION)
══════════════════════════ */
.table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0;
    border-top: 2px solid rgba(30,58,95,0.07);
    flex-wrap: wrap;
    gap: 0;
}

/* Kiri: info data */
.footer-info {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    background: linear-gradient(135deg, #f8fafd, #eef2f8);
    border-right: 1px solid rgba(30,58,95,0.07);
    min-width: 260px;
}
.footer-info-icon {
    font-size: 1.4rem;
    color: #2d5a8e;
    flex-shrink: 0;
}
.footer-info-main {
    font-size: 0.85rem;
    color: #3d5170;
    font-weight: 500;
    line-height: 1.4;
}
.footer-info-main strong { color: #1e3a5f; font-weight: 800; }
.footer-total {
    font-size: 1rem !important;
    color: #1e3a5f;
    background: rgba(30,58,95,0.08);
    padding: 1px 8px;
    border-radius: 6px;
}
.footer-info-sub {
    font-size: 0.72rem;
    color: #8a96a3;
    margin-top: 3px;
}

/* Tengah: pagination */
.pag-nav {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 14px 16px;
}
.pag-list {
    display: flex;
    align-items: center;
    gap: 5px;
    list-style: none;
    margin: 0; padding: 0;
}
.pag-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0 10px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #1e3a5f;
    background: #fff;
    border: 1.5px solid rgba(30,58,95,0.14);
    text-decoration: none;
    transition: all .15s ease;
    cursor: pointer;
    letter-spacing: 0;
}
.pag-btn:hover {
    background: #1e3a5f;
    color: #fff;
    border-color: #1e3a5f;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30,58,95,0.22);
}
.pag-btn-icon { min-width: 38px; padding: 0; color: #5a6a7e; }
.pag-btn-icon:hover { color: #fff; }

/* Active page — paling menonjol */
.pag-btn-active {
    background: linear-gradient(135deg, #1e3a5f, #2d5a8e) !important;
    color: #fff !important;
    border-color: transparent !important;
    box-shadow: 0 4px 14px rgba(30,58,95,0.32) !important;
    transform: translateY(-2px) scale(1.08) !important;
    min-width: 42px;
    height: 42px;
    font-size: 0.9rem;
}
.pag-btn-active:hover { transform: translateY(-2px) scale(1.08) !important; }

.pag-disabled .pag-btn { opacity: 0.3; cursor: not-allowed; pointer-events: none; }
.pag-ellipsis span { display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; color:#a0aab4; font-size:1rem; letter-spacing:2px; }

/* Responsive */
@media (max-width: 768px) {
    .table-toolbar { flex-direction:column; align-items:stretch; }
    .toolbar-right { justify-content:flex-start; }
    .search-input { width:130px; }
    .table-footer { flex-direction:column; align-items:stretch; }
    .footer-info { border-right:none; border-bottom:1px solid rgba(30,58,95,0.07); min-width:unset; }
    .pag-nav { padding:12px; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#filterButton').click(function () { $('#filterFields').slideToggle('fast'); });
    $('#select_all').change(function () { $('input[name="id_items[]"]').prop('checked', this.checked); });
    $('input[name="id_items[]"]').change(function () {
        $('#select_all').prop('checked', $('input[name="id_items[]"]:checked').length === $('input[name="id_items[]"]').length);
    });
    $('.delete-button').click(function () {
        const formId = $(this).data('form-id');
        Swal.fire({ title:'Hapus Barang?', text:"Data tidak dapat dikembalikan!", icon:'warning',
            showCancelButton:true, confirmButtonColor:'#1e3a5f', cancelButtonColor:'#6c757d',
            confirmButtonText:'Ya, Hapus!', cancelButtonText:'Batal'
        }).then(r => { if (r.isConfirmed) $('#' + formId).submit(); });
    });
});
function generateQRCodes(url) {
    if ($('input[name="id_items[]"]:checked').length < 1) { Swal.fire('Pilih Data','Centang minimal satu barang.','info'); return; }
    $('.form-items').attr('target','_blank').attr('action',url).attr('method','post').submit();
}
function multiDelete() {
    if ($('input[name="id_items[]"]:checked').length < 1) { Swal.fire('Pilih Data','Centang data yang ingin dihapus.','info'); return; }
    Swal.fire({ title:'Hapus Terpilih?', text:"Data akan dihapus permanen.", icon:'warning',
        showCancelButton:true, confirmButtonColor:'#1e3a5f', cancelButtonColor:'#6c757d', confirmButtonText:'Ya, Hapus!'
    }).then(r => { if (r.isConfirmed) $('.form-items').attr('action','{{ route("items.multiDelete") }}').attr('method','post').submit(); });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection