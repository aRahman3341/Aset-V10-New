@extends('layouts.app')
@section('title') Lokasi - Monitoring Aset @endsection
@section('content')
<main id="main" class="main">

<style>
.pagetitle { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon { width:44px; height:44px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.15rem; box-shadow:0 4px 12px rgba(30,58,95,0.22); }
.pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0; padding:0; background:transparent; font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#2d5a8e; text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }
.main-card { background:#fff; border-radius:12px; border:1px solid rgba(30,58,95,0.07); box-shadow:0 2px 14px rgba(30,58,95,0.07); overflow:hidden; }
.table-toolbar { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid rgba(30,58,95,0.07); gap:8px; flex-wrap:wrap; }
.toolbar-left { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.toolbar-right { display:flex; align-items:center; gap:6px; }
.filter-panel { background:#f8fafd; border-bottom:1px solid rgba(30,58,95,0.07); padding:12px 18px; }
.table thead th { background:#f6f9ff; color:#1e3a5f; font-weight:700; text-transform:uppercase; font-size:0.69rem; letter-spacing:0.5px; padding:10px 14px; border-bottom:2px solid #e0e8f5; white-space:nowrap; }
.table tbody td { padding:10px 14px; vertical-align:middle; font-size:0.83rem; border-bottom:1px solid rgba(30,58,95,0.05); }
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(30,58,95,0.025); }
.action-group { display:flex; align-items:center; gap:5px; }
.abtn { width:30px; height:30px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:0.82rem; border:none; cursor:pointer; transition:all .15s; background:transparent; text-decoration:none; }
.abtn-edit { color:#c49a2a; background:#fef9e7; } .abtn-edit:hover { background:#c49a2a; color:#fff; }
.abtn-del  { color:#dc2626; background:#fff0f0; } .abtn-del:hover  { background:#dc2626; color:#fff; }
.loc-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:700; background:#eff6ff; color:#1d4ed8; }

/* ── Pagination ── */
.table-footer {
    display:flex; align-items:center; justify-content:space-between;
    border-top:2px solid rgba(30,58,95,0.06);
    background:#fafbfd; border-radius:0 0 12px 12px;
    min-height:62px; flex-wrap:wrap;
}
.pag-nav { display:flex; align-items:center; padding:10px 16px; flex:1; flex-wrap:wrap; gap:4px; }
.pag-list { display:flex; align-items:center; gap:3px; list-style:none; margin:0; padding:0; }
.pag-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:34px; height:34px; padding:0 9px; border-radius:6px;
    font-size:0.8rem; font-weight:700; color:#1e3a5f;
    background:#fff; border:1.5px solid rgba(30,58,95,0.13);
    text-decoration:none; transition:all .15s ease; cursor:pointer;
}
.pag-btn:hover:not(.pag-btn-active) {
    background:#1e3a5f; color:#fff; border-color:#1e3a5f;
    text-decoration:none; transform:translateY(-1px);
    box-shadow:0 3px 10px rgba(30,58,95,0.20);
}
.pag-btn-icon { min-width:34px; padding:0; color:#5a6a7e; }
.pag-btn-active {
    background:linear-gradient(135deg,#1e3a5f,#2d5a8e) !important;
    color:#fff !important; border-color:transparent !important;
    box-shadow:0 3px 12px rgba(45,90,142,0.28) !important;
    transform:translateY(-1px) scale(1.06) !important;
    min-width:38px; height:38px; font-size:0.85rem;
}
.pag-disabled .pag-btn { opacity:.3; cursor:not-allowed; pointer-events:none; }
.pag-ellipsis span {
    display:inline-flex; align-items:center; justify-content:center;
    width:34px; height:34px; color:#a0aab4; font-size:0.9rem; letter-spacing:2px;
}
.pag-info { font-size:0.74rem; color:#8a96a3; margin-left:8px; white-space:nowrap; }
.pag-info strong { color:#1e3a5f; }
</style>

<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-geo-alt-fill"></i></div>
        <div>
            <h1>Lokasi</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item active">Lokasi</li>
            </ol></nav>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="main-card">
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form action="{{ route('location.search') }}" method="POST">
                @csrf
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="query" class="form-control border-start-0 ps-0"
                           placeholder="Cari gedung, lantai, atau ruangan..." value="{{ request()->input('query') }}" style="min-width:240px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="filterBtn" title="Filter">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="toolbar-right">
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ModalAdd">
                <i class="bi bi-plus-lg"></i> Tambah Lokasi
            </button>
        </div>
    </div>

    <div id="filterPanel" style="display:none;" class="filter-panel">
        @include('location.filter')
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">No</th>
                    <th>Gedung</th>
                    <th>Lantai</th>
                    <th>Ruangan</th>
                    <th class="text-center" style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($location as $item)
                <tr>
                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                    <td><span class="fw-semibold">{{ $item->office }}</span></td>
                    <td><span class="loc-badge"><i class="bi bi-layers"></i> Lt. {{ $item->floor }}</span></td>
                    <td>{{ $item->room }}</td>
                    <td class="text-center">
                        <div class="action-group justify-content-center">
                            <a href="#!" data-bs-toggle="modal" data-bs-target="#ModalEdit-{{ $item->id }}"
                               class="abtn abtn-edit" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('location.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="abtn abtn-del delete-btn"
                                        data-name="{{ $item->office }} Lt.{{ $item->floor }} - {{ $item->room }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Belum ada data lokasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Footer Pagination (sama seperti Aset Tetap) ── --}}
    <div class="table-footer">
        <div class="pag-nav">
            @include('location.pagenation')
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="ModalAdd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('location.store') }}">
                @csrf
                <div class="modal-header" style="background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;">
                    <h5 class="modal-title fw-700"><i class="bi bi-geo-alt me-2"></i>Tambah Lokasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Gedung</label>
                        <input type="text" class="form-control" name="office" placeholder="Contoh: Gedung A" required>
                        @error('office') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lantai</label>
                        <input type="text" class="form-control" name="floor" placeholder="Contoh: 1" required>
                        @error('floor') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ruangan</label>
                        <input type="text" class="form-control" name="room" placeholder="Contoh: Ruang Server" required>
                        @error('room') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
@foreach($location as $item)
<div class="modal fade" id="ModalEdit-{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('location.update', $item->id) }}">
                @csrf @method('PUT')
                <div class="modal-header" style="background:linear-gradient(135deg,#c49a2a,#e8b84b);color:#fff;">
                    <h5 class="modal-title fw-700"><i class="bi bi-pencil me-2"></i>Edit Lokasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Gedung</label>
                        <input type="text" class="form-control" name="office" value="{{ $item->office }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lantai</label>
                        <input type="text" class="form-control" name="floor" value="{{ $item->floor }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ruangan</label>
                        <input type="text" class="form-control" name="room" value="{{ $item->room }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm text-white"><i class="bi bi-check-circle me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('filterBtn').addEventListener('click', function() {
    const p = document.getElementById('filterPanel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
});
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const name = this.dataset.name;
        const form = this.closest('.delete-form');
        Swal.fire({
            title: 'Hapus Lokasi?',
            html: `Lokasi <strong>${name}</strong> akan dihapus permanen.`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#dc2626', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endsection