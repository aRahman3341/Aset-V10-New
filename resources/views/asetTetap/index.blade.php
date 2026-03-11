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

<section class="section">
<div class="main-card">

    {{-- ── Toolbar ── --}}
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form action="{{ route('asetTetap.search') }}" method="POST" class="d-flex">
                @csrf
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="query" class="form-control border-start-0 ps-0"
                           placeholder="Cari Kode, NUP, atau Nama..." value="{{ request()->input('query') }}">
                    <button type="button" class="btn btn-outline-secondary" id="filterButton" title="Filter Lanjutan">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
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
            <button onclick="multiDelete('{{ route('asetTetap.multiDelete') }}')" class="btn btn-danger btn-sm">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
    </div>

    {{-- ── Filter Panel ── --}}
    <div id="filterFields" style="display:{{ request()->is('asetTetap/filter') ? 'block' : 'none' }};" class="filter-panel">
        <div class="filter-panel-header"><span><i class="bi bi-funnel-fill"></i> Filter Lanjutan</span></div>
        @include('asetTetap.filter')
    </div>

    {{-- ── Table ── --}}
    <div class="table-responsive" style="max-height:650px; overflow-y:auto;">
        <form action="" method="post" class="form-produk">
            @csrf
            <table class="table table-sm table-hover table-custom mb-0">
                <thead style="position:sticky; top:0; z-index:10;">
                    <tr>
                        <th class="text-center" style="width:40px;">
                            <input type="checkbox" id="select_all" class="form-check-input">
                        </th>
                        <th class="text-center" style="width:40px;">No</th>
                        <th>Kode Barang</th>
                        <th>NUP</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Tipe</th>
                        <th>Jenis BMN</th>
                        <th class="text-center">Kondisi</th>
                        <th class="text-center">Status BMN</th>
                        <th class="text-end">Nilai Perolehan Pertama (Rp)</th>
                        <th class="text-end">Nilai Perolehan (Rp)</th>
                        <th class="text-end">Nilai Penyusutan (Rp)</th>
                        <th class="text-end">Nilai Buku (Rp)</th>
                        <th>Tgl Perolehan</th>
                        <th>Tgl Buku Pertama</th>
                        <th>No PSP</th>
                        <th>Tgl PSP</th>
                        <th>Kode Satker</th>
                        <th>Nama Satker</th>
                        <th>Alamat</th>
                        <th class="text-center">Foto</th>
                        <th class="text-center" style="min-width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($items as $item)
                        @php
                            $kondisi   = $item->kondisi ?? 'Baik';
                            $condColor = match($kondisi) {
                                'Baik'         => 'success',
                                'Rusak Ringan' => 'warning',
                                default        => 'danger',
                            };
                        @endphp
                        <tr data-item-id="{{ $item->id }}">
                            <td class="text-center">
                                <input class="form-check-input" type="checkbox" name="id_aset[]"
                                       value="{{ $item->id }}" style="transform:scale(0.8);">
                            </td>
                            <td class="text-center">{{ $no }}</td>

                            <td><span class="fw-bold text-primary code-text">{{ $item->kode_barang ?? '-' }}</span></td>
                            <td>{{ $item->nup ?? '-' }}</td>
                            <td><span class="fw-semibold">{{ Str::limit($item->nama_barang ?? '-', 40) }}</span></td>
                            <td><span class="text-muted">{{ Str::limit($item->merk ?? '-', 25) }}</span></td>
                            <td><span class="text-muted">{{ Str::limit($item->tipe ?? '-', 25) }}</span></td>
                            <td><span class="badge-jenis">{{ Str::limit($item->jenis_bmn ?? '-', 30) }}</span></td>

                            <td class="text-center">
                                <span class="badge bg-{{ $condColor }}">{{ $kondisi }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ ($item->status_bmn ?? '') === 'Aktif' ? 'success' : 'secondary' }}">
                                    {{ $item->status_bmn ?? '-' }}
                                </span>
                            </td>

                            <td class="text-end">{{ $item->nilai_perolehan_pertama ? number_format($item->nilai_perolehan_pertama, 0, ',', '.') : '-' }}</td>
                            <td class="text-end">{{ $item->nilai_perolehan         ? number_format($item->nilai_perolehan, 0, ',', '.')         : '-' }}</td>
                            <td class="text-end">{{ $item->nilai_penyusutan        ? number_format($item->nilai_penyusutan, 0, ',', '.')        : '-' }}</td>
                            <td class="text-end fw-semibold" style="color:#1e3a5f;">
                                {{ $item->nilai_buku ? number_format($item->nilai_buku, 0, ',', '.') : '-' }}
                            </td>

                            <td>{{ $item->tanggal_perolehan    ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d/m/Y')    : '-' }}</td>
                            <td>{{ $item->tanggal_buku_pertama ? \Carbon\Carbon::parse($item->tanggal_buku_pertama)->format('d/m/Y') : '-' }}</td>

                            <td>{{ $item->no_psp ?? '-' }}</td>
                            <td>{{ $item->tanggal_psp ? \Carbon\Carbon::parse($item->tanggal_psp)->format('d/m/Y') : '-' }}</td>

                            <td>{{ $item->kode_satker ?? '-' }}</td>
                            <td>{{ Str::limit($item->nama_satker ?? '-', 25) }}</td>
                            <td>{{ Str::limit($item->alamat ?? '-', 30) }}</td>

                            <td class="text-center">
                                @if(($item->jumlah_foto ?? 0) > 0)
                                    <span class="badge bg-info">{{ $item->jumlah_foto }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="action-group">
                                    <a href="{{ route('asetTetap.edit', $item->id) }}" class="abtn abtn-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="abtn abtn-del delete-button" data-item-id="{{ $item->id }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @php $no++; @endphp
                    @empty
                        <tr>
                            <td colspan="23" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                Data tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>

</div>
</section>
</main>

@include('asetTetap.scane')

<style>
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
.action-btn-success  { background:linear-gradient(135deg,#1a7f4b,#22a86a); color:#fff; box-shadow:0 2px 8px rgba(26,127,75,0.25); }
.action-btn-success:hover  { transform:translateY(-1px); box-shadow:0 4px 14px rgba(26,127,75,0.35); color:#fff; }
.action-btn-outline  { background:#fff; color:#012970; border:1.5px solid rgba(1,41,112,0.20); }
.action-btn-outline:hover  { background:#f0f5ff; border-color:#4154f1; color:#4154f1; }
.action-btn-secondary { background:#f4f6fb; color:#5a6a7e; border:1.5px solid rgba(1,41,112,0.15); }
.action-btn-secondary:hover { background:#e8eef8; color:#012970; }
.action-btn-danger   { background:#fff0f0; color:#dc2626; border:1.5px solid rgba(220,38,38,0.20); }
.action-btn-danger:hover   { background:#ffe0e0; border-color:#dc2626; }

.filter-panel { background:#f8fafd; border-bottom:1px solid rgba(1,41,112,0.07); padding:12px 18px; }
.filter-panel-header { margin-bottom:10px; font-size:0.8rem; font-weight:700; color:#5a6a7e; }

.table-custom { font-size:0.8rem; }
.table-custom thead th {
    background-color:#f6f9ff; color:#012970; font-weight:700; text-transform:uppercase;
    font-size:0.69rem; letter-spacing:0.5px; padding:10px 11px; white-space:nowrap;
    border-bottom:2px solid #e0e8f5;
}
.table-custom tbody td {
    padding:9px 11px; vertical-align:middle; font-size:0.8rem;
    white-space:nowrap; border-bottom:1px solid rgba(1,41,112,0.05);
}
.table-custom tbody tr:last-child td { border-bottom:none; }
.table-custom tbody tr:hover td { background:rgba(65,84,241,0.03); }

.code-text { font-family:'DM Mono','Courier New',monospace; font-size:0.78rem; }
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
@media (max-width:768px) {
    .table-toolbar { flex-direction:column; align-items:stretch; }
    .toolbar-right { justify-content:flex-start; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="{{ asset('js/indexaset.js') }}"></script>
<script>
function generateQRCodes(url) {
    if ($('input[name="id_aset[]"]:checked').length < 1) {
        Swal.fire('Pilih Data', 'Centang minimal satu aset untuk mencetak QR.', 'info'); return;
    }
    $('.form-produk').attr('target', '_blank').attr('action', url).submit();
}
function multiDelete(url) {
    if ($('input[name="id_aset[]"]:checked').length < 1) {
        Swal.fire('Pilih Data', 'Centang data yang ingin dihapus.', 'info'); return;
    }
    Swal.fire({
        title: 'Hapus Terpilih?', text: "Data yang dicentang akan dihapus permanen.",
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#012970', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!'
    }).then(r => { if (r.isConfirmed) $('.form-produk').attr('action', url).submit(); });
}
function exportAset(url) { $('.form-produk').attr('action', url).submit(); }

$(document).ready(function () {
    var csrf = document.querySelector('input[name="_token"]').value;
    $(document).on('click', '.delete-button', function (e) {
        e.preventDefault();
        var id = $(this).data('item-id');
        Swal.fire({
            title: 'Hapus Item?', text: "Data ini tidak bisa dikembalikan.",
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#012970', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus'
        }).then(r => {
            if (r.isConfirmed) {
                $.ajax({
                    url: '/asetTetap/' + id, type: 'DELETE', data: { _token: csrf },
                    success: function () {
                        Swal.fire({ icon:'success', title:'Terhapus', showConfirmButton:false, timer:1000 })
                            .then(() => location.reload());
                    },
                    error: function () { Swal.fire('Error', 'Gagal menghapus data.', 'error'); }
                });
            }
        });
    });
    $('#select_all').change(function () { $('input[name="id_aset[]"]').prop('checked', this.checked); });
    $('input[name="id_aset[]"]').change(function () {
        $('#select_all').prop('checked',
            $('input[name="id_aset[]"]').length === $('input[name="id_aset[]"]:checked').length);
    });
    $('#filterButton').click(function (e) { e.preventDefault(); $('#filterFields').slideToggle(); });
});
</script>
@endsection