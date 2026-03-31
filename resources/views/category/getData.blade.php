@extends('layouts.app')
@section('title') Kategori - Monitoring Aset @endsection
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
.toolbar-left { display:flex; align-items:center; gap:8px; }
.toolbar-right { display:flex; align-items:center; gap:6px; }
.table thead th { background:#f6f9ff; color:#1e3a5f; font-weight:700; text-transform:uppercase; font-size:0.69rem; letter-spacing:0.5px; padding:10px 14px; border-bottom:2px solid #e0e8f5; white-space:nowrap; }
.table tbody td { padding:10px 14px; vertical-align:middle; font-size:0.83rem; border-bottom:1px solid rgba(30,58,95,0.05); }
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(30,58,95,0.025); }
.action-group { display:flex; align-items:center; gap:5px; }
.abtn { width:30px; height:30px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:0.82rem; border:none; cursor:pointer; transition:all .15s; background:transparent; text-decoration:none; }
.abtn-edit { color:#c49a2a; background:#fef9e7; } .abtn-edit:hover { background:#c49a2a; color:#fff; }
.abtn-del  { color:#dc2626; background:#fff0f0; } .abtn-del:hover  { background:#dc2626; color:#fff; }
</style>

<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-tag-fill"></i></div>
        <div>
            <h1>Kategori</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item active">Kategori</li>
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
            <form action="{{ route('category.search') }}" method="POST">
                @csrf
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="query" class="form-control border-start-0 ps-0"
                           placeholder="Cari kode atau nama kategori..." value="{{ request()->input('query') }}" style="min-width:240px;">
                </div>
            </form>
        </div>
        <div class="toolbar-right">
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ModalAdd">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">No</th>
                    <th>Kode Kategori</th>
                    <th>Nama Kategori</th>
                    <th class="text-center" style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($category as $item)
                <tr>
                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                    <td><span class="fw-bold" style="font-family:monospace;color:#2d5a8e;">{{ $item->code }}</span></td>
                    <td class="fw-semibold">{{ $item->name }}</td>
                    <td class="text-center">
                        <div class="action-group justify-content-center">
                            <a href="#!" data-bs-toggle="modal" data-bs-target="#ModalEdit-{{ $item->id }}"
                               class="abtn abtn-edit" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('category.destroy', $item->id) }}" method="POST"
                                  class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="abtn abtn-del delete-btn"
                                        data-name="{{ $item->name }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Belum ada data kategori.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-top">{{ $category->links() }}</div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="ModalAdd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('category.store') }}">
                @csrf
                <div class="modal-header" style="background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;">
                    <h5 class="modal-title fw-700"><i class="bi bi-tag me-2"></i>Tambah Kategori</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Kategori</label>
                        <input type="text" class="form-control" name="code" placeholder="Contoh: KAT-001" required>
                        @error('code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" class="form-control" name="name" placeholder="Masukkan nama kategori" required>
                        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
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
@foreach($category as $item)
<div class="modal fade" id="ModalEdit-{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('category.update', $item->id) }}">
                @csrf @method('PUT')
                <div class="modal-header" style="background:linear-gradient(135deg,#c49a2a,#e8b84b);color:#fff;">
                    <h5 class="modal-title fw-700"><i class="bi bi-pencil me-2"></i>Edit Kategori</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Kategori</label>
                        <input type="text" class="form-control" name="code" value="{{ $item->code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori</label>
                        <input type="text" class="form-control" name="name" value="{{ $item->name }}" required>
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
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const name = this.dataset.name;
        const form = this.closest('.delete-form');
        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Kategori <strong>${name}</strong> akan dihapus permanen.`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#dc2626', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endsection