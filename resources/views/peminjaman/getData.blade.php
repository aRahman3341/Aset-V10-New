@extends('layouts.app')
@section('title') Peminjaman - Monitoring Aset @endsection
@section('content')
<main id="main" class="main">

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon" style="background:linear-gradient(135deg,#1e6f3e,#28a745)">
            <i class="bi bi-box-arrow-in-right"></i>
        </div>
        <div>
            <h1>Peminjaman Aset</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Peminjaman</li>
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
            <form action="{{ route('peminjaman.search') }}" method="POST">
                @csrf
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="query" class="form-control border-start-0 ps-0"
                           placeholder="Cari kode, peminjam, atau nama barang..." value="{{ request()->input('query') }}">
                    <button type="button" class="btn btn-outline-secondary" id="filterButton" title="Filter">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="toolbar-right">
            <a href="{{ route('peminjaman.create') }}" class="btn btn-success btn-sm shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah Peminjaman
            </a>
            <a href="{{ route('peminjaman.report') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-file-earmark-text"></i> Report
            </a>
        </div>
    </div>

    {{-- ── Filter Panel ── --}}
    <div id="filterPanel" style="display:none;" class="filter-panel">
        <div class="filter-panel-header">
            <span><i class="bi bi-funnel-fill"></i> Filter Data</span>
        </div>
        <form action="{{ route('peminjaman.filter') }}" method="POST">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Petugas</label>
                    <select name="code" class="form-select form-select-sm">
                        <option value="all">Semua Petugas</option>
                        @foreach($codes as $c)
                            <option value="{{ $c->employee_id }}" {{ request()->input('code') == $c->employee_id ? 'selected' : '' }}>
                                {{ $c->employee_id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request()->input('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request()->input('end_date') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-funnel"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Tabel ── --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width:46px">No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Peminjam</th>
                    <th class="text-center">Tgl Pinjam</th>
                    <th class="text-center">Tgl Kembali</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loan as $i => $item)
                <tr>
                    <td class="text-center text-muted small">{{ $loan->firstItem() + $i }}</td>
                    <td><span class="fw-bold text-success">{{ $item->code }}</span></td>
                    <td class="fw-semibold">{{ $item->material->nama_barang ?? '-' }}</td>
                    <td>{{ $item->peminjam }}</td>
                    <td class="text-center small">{{ $item->tgl_pinjam }}</td>
                    <td class="text-center small">{{ $item->tgl_kembali }}</td>
                    <td class="text-center">
                        @php
                            $status = $item->status ?? 'Dipinjam';
                            $sClass = $status === 'Dikembalikan' ? 'bstatus-kembali' : 'bstatus-pinjam';
                        @endphp
                        <span class="bstatus {{ $sClass }}">{{ $status }}</span>
                    </td>
                    <td class="text-center">
                        <div class="action-group">
                            {{-- Cetak Surat --}}
                            <a href="{{ route('peminjaman.cetakSurat', $item->id) }}"
                               class="abtn abtn-print" title="Cetak Surat">
                                <i class="bi bi-printer"></i>
                            </a>

                            @if($item->status !== 'Dikembalikan')
                                {{-- Pengembalian --}}
                                <a href="{{ route('peminjaman.kembali', $item->id) }}"
                                   class="abtn abtn-kembali" title="Kembalikan">
                                    <i class="bi bi-box-arrow-in-left"></i>
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('peminjaman.edit', $item->id) }}"
                                   class="abtn abtn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            @endif

                            {{-- Hapus --}}
                            <form action="{{ route('peminjaman.destroy', $item->id) }}"
                                  method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="abtn abtn-del delete-btn"
                                        data-name="{{ $item->code }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Belum ada data peminjaman.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($loan->lastPage() > 1)
    <div class="pag-nav">
        @include('pengguna.pagenation', ['items' => $loan])
    </div>
    @endif

</div>
</section>
</main>

<style>
.pagetitle { display:flex; align-items:center; margin-bottom:20px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon {
    width:44px; height:44px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:1.15rem; box-shadow:0 4px 12px rgba(40,167,69,0.3);
}
.pagetitle h1 { font-size:1.25rem; font-weight:800; color:#012970; margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0; padding:0; background:transparent; font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#4154f1; text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

.main-card {
    background:#fff; border-radius:10px;
    border:1px solid rgba(1,41,112,0.07);
    box-shadow:0 2px 14px rgba(1,41,112,0.07); overflow:hidden;
}
.table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px; border-bottom:1px solid rgba(1,41,112,0.07);
    gap:8px; flex-wrap:wrap;
}
.toolbar-left  { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.toolbar-right { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.filter-panel {
    background:#f8fafd; border-bottom:1px solid rgba(1,41,112,0.07); padding:12px 18px;
}
.filter-panel-header { margin-bottom:10px; font-size:0.8rem; font-weight:700; color:#5a6a7e; }
.table thead th {
    background-color:#f6f9ff; color:#012970; font-weight:700;
    text-transform:uppercase; font-size:0.69rem; letter-spacing:0.5px;
    padding:10px 11px; border:none; white-space:nowrap; border-bottom:2px solid #e0e8f5;
}
.table tbody td {
    padding:9px 11px; vertical-align:middle;
    font-size:0.83rem; border-bottom:1px solid rgba(1,41,112,0.05);
}
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(65,84,241,0.03); }

.bstatus { display:inline-block; font-size:0.68rem; font-weight:700; padding:2px 9px; border-radius:20px; }
.bstatus-pinjam  { background:rgba(255,193,7,0.15); color:#b45309; }
.bstatus-kembali { background:rgba(16,185,129,0.12); color:#047857; }

.action-group { display:flex; align-items:center; justify-content:center; gap:4px; }
.abtn {
    width:28px; height:28px; border-radius:6px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:0.8rem; border:none; cursor:pointer; transition:all .15s; background:transparent;
}
.abtn-edit    { color:#c49a2a; } .abtn-edit:hover    { background:rgba(232,184,75,0.15); }
.abtn-del     { color:#dc2626; } .abtn-del:hover     { background:rgba(220,38,38,0.10); }
.abtn-print   { color:#4154f1; } .abtn-print:hover   { background:rgba(65,84,241,0.10); }
.abtn-kembali { color:#10b981; } .abtn-kembali:hover { background:rgba(16,185,129,0.12); }

.empty-row { text-align:center; padding:44px 0 !important; color:#8a96a3; }
.empty-row i { font-size:2.2rem; display:block; margin-bottom:8px; }

.pag-nav { display:flex; align-items:center; padding:10px 14px; flex-wrap:wrap; gap:4px;
           border-top:1px solid rgba(1,41,112,0.06); background:#fafbfd; }
.pag-list { display:flex; align-items:center; gap:3px; list-style:none; margin:0; padding:0; }
.pag-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:34px; height:34px; padding:0 9px; border-radius:6px;
    font-size:0.8rem; font-weight:700; color:#012970;
    background:#fff; border:1.5px solid rgba(1,41,112,0.13);
    text-decoration:none; transition:all .15s ease; cursor:pointer;
}
.pag-btn:hover:not(.pag-btn-active) { background:#012970; color:#fff; border-color:#012970; text-decoration:none; }
.pag-btn-active {
    background:linear-gradient(135deg,#012970,#4154f1) !important;
    color:#fff !important; border-color:transparent !important;
    box-shadow:0 3px 12px rgba(65,84,241,0.28) !important;
}
.pag-disabled .pag-btn { opacity:.3; cursor:not-allowed; pointer-events:none; }
.pag-info { font-size:0.74rem; color:#8a96a3; margin-left:8px; }
.pag-info strong { color:#012970; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('filterButton').addEventListener('click', function (e) {
        e.preventDefault();
        const f = document.getElementById('filterPanel');
        f.style.display = f.style.display === 'none' ? 'block' : 'none';
    });

    document.querySelectorAll('.delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const name = this.dataset.name;
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Hapus Data?',
                html: `Data peminjaman <strong>${name}</strong> akan dihapus permanen.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc2626', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

});
</script>
@endsection