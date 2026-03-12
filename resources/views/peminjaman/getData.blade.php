@extends('layouts.app')
@section('title') Peminjaman - Monitoring Aset @endsection
@section('content')

<main id="main" class="main">

<style>
.pagetitle { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
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

.main-card {
    background:#fff; border-radius:10px;
    border:1px solid rgba(1,41,112,0.07);
    box-shadow:0 2px 14px rgba(1,41,112,0.07); overflow:hidden;
}
.table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 18px; border-bottom:1px solid rgba(1,41,112,0.07); gap:8px; flex-wrap:wrap;
}
.toolbar-left  { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.toolbar-right { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }

.action-btn {
    display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 13px;
    border-radius:8px; font-size:0.78rem; font-weight:700; cursor:pointer;
    border:none; text-decoration:none; transition:all .18s; white-space:nowrap;
}
.action-btn-success { background:linear-gradient(135deg,#1a7f4b,#22a86a); color:#fff; box-shadow:0 2px 8px rgba(26,127,75,0.25); }
.action-btn-success:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(26,127,75,0.35); color:#fff; }

.filter-panel { background:#f8fafd; border-bottom:1px solid rgba(1,41,112,0.07); padding:12px 18px; }
.filter-panel-header { margin-bottom:10px; font-size:0.8rem; font-weight:700; color:#5a6a7e; }

.table-custom { font-size:0.8rem; }
.table-custom thead th {
    background:#f6f9ff; color:#012970; font-weight:700; text-transform:uppercase;
    font-size:0.69rem; letter-spacing:0.5px; padding:10px 11px; white-space:nowrap;
    border-bottom:2px solid #e0e8f5;
}
.table-custom tbody td {
    padding:9px 11px; vertical-align:middle; white-space:nowrap;
    border-bottom:1px solid rgba(1,41,112,0.05);
}
.table-custom tbody tr:last-child td { border-bottom:none; }
.table-custom tbody tr:hover td { background:rgba(65,84,241,0.03); }

.action-group { display:flex; align-items:center; justify-content:center; gap:4px; }
.abtn {
    height:28px; padding:0 8px; border-radius:6px; display:inline-flex; align-items:center;
    gap:4px; font-size:0.72rem; font-weight:700; border:none; cursor:pointer; transition:all .15s;
    text-decoration:none; white-space:nowrap;
}
.abtn-print   { background:#e8f4fd; color:#0369a1; }
.abtn-print:hover  { background:#0369a1; color:#fff; }
.abtn-edit    { background:#fef9e7; color:#c49a2a; }
.abtn-edit:hover   { background:rgba(232,184,75,0.25); }
.abtn-kembali { background:#f0fdf4; color:#15803d; }
.abtn-kembali:hover { background:#15803d; color:#fff; }
.abtn-del     { background:#fff0f0; color:#dc2626; }
.abtn-del:hover    { background:rgba(220,38,38,0.15); }

.badge-dipinjam   { background:#fff3cd; color:#856404; padding:3px 8px; border-radius:20px; font-size:0.7rem; font-weight:700; }
.badge-kembali    { background:#d1fae5; color:#065f46; padding:3px 8px; border-radius:20px; font-size:0.7rem; font-weight:700; }
</style>

<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-clipboard2-check"></i></div>
        <div>
            <h1>Peminjaman Alat</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item active">Peminjaman</li>
            </ol></nav>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="main-card">
    {{-- ── Toolbar ── --}}
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form action="{{ route('peminjaman.search') }}" method="POST" class="d-flex">
                @csrf
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="query" class="form-control border-start-0 ps-0"
                           placeholder="Cari kode, nama barang, peminjam..."
                           value="{{ request()->input('query') }}" style="min-width:260px;">
                    <button type="button" class="btn btn-outline-secondary" id="filterButton" title="Filter">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="toolbar-right">
            @if(isset($sess) && $sess['jabatan'] == 'admin')
                <a href="{{ route('peminjaman.create') }}" class="action-btn action-btn-success">
                    <i class="bi bi-plus-lg"></i> Tambah Peminjaman
                </a>
            @endif
        </div>
    </div>

    {{-- ── Filter Panel ── --}}
    <div id="filterFields" style="display:none;" class="filter-panel">
        <div class="filter-panel-header"><span><i class="bi bi-funnel-fill"></i> Filter</span></div>
        @include('peminjaman.filter')
    </div>

    {{-- ── Table ── --}}
    <div class="table-responsive" style="max-height:620px; overflow-y:auto;">
        <table class="table table-sm table-hover table-custom mb-0">
            <thead style="position:sticky; top:0; z-index:10;">
                <tr>
                    <th class="text-center" style="width:40px;">No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kode Barang</th>
                    <th>NUP</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Peminjam</th>
                    <th>Operator</th>
                    <th class="text-center">Status</th>
                    @if(isset($sess) && $sess['jabatan'] == 'admin')
                        <th class="text-center" style="min-width:180px;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse ($loan as $item)
                    @if($item->status == 'Dipinjam')
                    <tr>
                        <td class="text-center">{{ $no }}</td>
                        <td><span class="fw-bold text-primary" style="font-family:monospace;">{{ $item->code }}</span></td>
                        <td>
                            <span class="fw-semibold">{{ $item->material->nama_barang ?? '-' }}</span>
                        </td>
                        <td><small class="text-muted">{{ $item->material->kode_barang ?? '-' }}</small></td>
                        <td>{{ $item->material->nup ?? '-' }}</td>
                        <td>{{ $item->tgl_pinjam  ? \Carbon\Carbon::parse($item->tgl_pinjam)->format('d/m/Y')  : '-' }}</td>
                        <td>{{ $item->tgl_kembali ? \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->peminjam ?? '-' }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge-dipinjam">{{ $item->status }}</span>
                        </td>
                        @if(isset($sess) && $sess['jabatan'] == 'admin')
                        <td class="text-center">
                            <div class="action-group">
                                {{-- Print Surat --}}
                                <a href="{{ route('peminjaman.cetakSurat', $item->id) }}"
                                   class="abtn abtn-print" title="Cetak Surat">
                                    <i class="bi bi-printer-fill"></i> Surat
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('peminjaman.edit', $item->id) }}"
                                   class="abtn abtn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                {{-- Kembalikan --}}
                                <a href="{{ route('peminjaman.kembali', $item->id) }}"
                                   class="abtn abtn-kembali" title="Kembalikan">
                                    <i class="bi bi-arrow-return-left"></i>
                                </a>
                                {{-- Hapus --}}
                                <button class="abtn abtn-del delete-btn" data-id="{{ $item->id }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form id="delForm{{ $item->id }}"
                                      action="{{ route('peminjaman.destroy', $item->id) }}"
                                      method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @php $no++; @endphp
                    @endif
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Tidak ada data peminjaman aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-4 py-3 border-top">
        {{ $loan->links() }}
    </div>
</div>

</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script>
$(document).ready(function () {
    // Hapus dengan konfirmasi
    $(document).on('click', '.delete-btn', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?', text: 'Data peminjaman ini akan dihapus permanen.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#012970', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) $('#delForm' + id).submit(); });
    });

    // Toggle filter
    $('#filterButton').click(function () { $('#filterFields').slideToggle(); });
});
</script>
@endsection