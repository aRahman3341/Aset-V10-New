@extends('layouts.app')
@section('title') Barang Keluar - Monitoring Aset @endsection
@section('content')
<main id="main" class="main">

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-box-arrow-up-right"></i></div>
        <div>
            <h1>Barang Keluar</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item active">Barang Keluar</li>
            </ol></nav>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<section class="section">
<div class="row g-4">

    {{-- ════════ PANEL KIRI: Tabel ════════ --}}
    <div class="col-lg-8">
    <div class="main-card">

        {{-- Toolbar --}}
        <div class="table-toolbar">
            <div class="toolbar-left">
                <form action="{{ url('/asetout') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="query" class="form-control border-start-0 ps-0"
                               placeholder="Cari No Faktur / MAK / ND..."
                               value="{{ request('query') }}">
                        <button type="submit" class="btn btn-outline-secondary">Cari</button>
                    </div>
                </form>
            </div>
            @if($sess['jabatan'] === 'Operator')
            <div class="toolbar-right">
                <a href="{{ route('asetout.add') }}" class="btn btn-success btn-sm shadow-sm">
                    <i class="bi bi-plus-lg"></i> Tambah
                </a>
            </div>
            @endif
        </div>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width:46px">No</th>
                        @if($sess['jabatan'] !== 'Operator')
                        <th>No Faktur</th>
                        @endif
                        <th>MAK</th>
                        <th>Nota Dinas</th>
                        <th class="text-center">Tgl Pengajuan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asetout as $item)
                        @php
                            $tgl = \Carbon\Carbon::parse($item->created_at)->format('d M Y');

                            // Hitung status dari ajuan terkait
                            $ajuanList = $item->ajuan;
                            if ($ajuanList instanceof \Illuminate\Database\Eloquent\Model) {
                                $ajuanList = collect([$ajuanList]);
                            }
                            $allDiproses  = $ajuanList->every(fn($a) => $a->status === 'Diproses');
                            $anyDisetujui = $ajuanList->contains(fn($a) => $a->status === 'Disetujui');
                            $anyDitolak   = $ajuanList->contains(fn($a) => $a->status === 'Ditolak');

                            if ($anyDitolak)       { $statusLabel = 'Ditolak';   $statusClass = 'bdanger'; }
                            elseif ($anyDisetujui) { $statusLabel = 'Disetujui'; $statusClass = 'bsuccess'; }
                            else                   { $statusLabel = 'Diproses';  $statusClass = 'bwarning'; }
                        @endphp
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            @if($sess['jabatan'] !== 'Operator')
                            <td class="fw-semibold small">{{ str_replace('^^', '/', $item->no_faktur) ?: '-' }}</td>
                            @endif
                            <td class="small">{{ str_replace('^^', '/', $item->mak) ?: '-' }}</td>
                            <td class="small">{{ str_replace('^^', '/', $item->no_nd) ?: '-' }}</td>
                            <td class="text-center small text-muted">{{ $tgl }}</td>
                            <td class="text-center">
                                <span class="bstat {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-center">
                                <div class="action-group">
                                    {{-- Detail / Approval --}}
                                    <a href="{{ route('asetout.ajuan', $item->id) }}"
                                       class="abtn abtn-view" title="Detail">
                                        <i class="bi bi-card-list"></i>
                                    </a>
                                    {{-- Edit --}}
                                    @if($sess['jabatan'] !== 'Operator')
                                        <a href="{{ route('asetout.edit', $item->id) }}"
                                           class="abtn abtn-edit" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('asetout.editND', $item->id) }}"
                                           class="abtn abtn-edit" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endif
                                    {{-- Hapus --}}
                                    <form action="{{ route('asetout.destroy', $item->id) }}"
                                          method="POST" class="d-contents delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="abtn abtn-del delete-btn"
                                                data-name="{{ str_replace('^^','/',$item->no_faktur ?: $item->no_nd) }}"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $sess['jabatan'] !== 'Operator' ? 7 : 6 }}" class="empty-row">
                                <i class="bi bi-inbox"></i>
                                <p class="mb-0">Belum ada data barang keluar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

    {{-- ════════ PANEL KANAN: Download ════════ --}}
    <div class="col-lg-4">
    <div class="main-card">
        <div class="side-header">
            @if($sess['jabatan'] !== 'Operator')
                <i class="bi bi-file-earmark-arrow-down-fill me-2"></i> Download Barang Keluar
            @else
                <i class="bi bi-file-earmark-arrow-down-fill me-2"></i> Download Nota Dinas
            @endif
        </div>
        <div class="p-3">
            @if($sess['jabatan'] !== 'Operator')
                <label class="filter-label mb-1">Pilih No Faktur</label>
                <select id="noFaktur" class="form-select form-select-sm mb-3">
                    <option value="">-- Pilih No Faktur --</option>
                    @foreach($asetout->unique('no_faktur') as $item)
                        @if($item->no_faktur)
                        <option value="{{ $item->no_faktur }}">
                            {{ str_replace('^^', '/', $item->no_faktur) }}
                        </option>
                        @endif
                    @endforeach
                </select>
                <button onclick="downloadFaktur()" class="btn btn-success btn-sm w-100">
                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Download Faktur
                </button>
            @else
                <label class="filter-label mb-1">Pilih Nota Dinas</label>
                <select id="notaDinas" class="form-select form-select-sm mb-3">
                    <option value="">-- Pilih Nota Dinas --</option>
                    @foreach($asetout->unique('no_nd') as $item)
                        @if($item->no_nd)
                        <option value="{{ $item->no_nd }}">
                            {{ str_replace('^^', '/', $item->no_nd) }}
                        </option>
                        @endif
                    @endforeach
                </select>
                <button onclick="downloadNota()" class="btn btn-success btn-sm w-100">
                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Download Nota Dinas
                </button>
            @endif
        </div>
    </div>
    </div>

</div>
</section>
</main>

<style>
.pagetitle { display:flex; align-items:center; margin-bottom:20px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon { width:44px; height:44px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.15rem; box-shadow:0 4px 12px rgba(30,58,95,0.22); }
.pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0; padding:0; background:transparent; font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#2d5a8e; text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

.main-card { background:#fff; border-radius:10px; border:1px solid rgba(30,58,95,0.08); box-shadow:0 2px 14px rgba(30,58,95,0.07); overflow:hidden; }

.table-toolbar { display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-bottom:1px solid rgba(30,58,95,0.07); gap:8px; flex-wrap:wrap; }
.toolbar-left { display:flex; align-items:center; gap:8px; }
.toolbar-right { display:flex; align-items:center; gap:4px; }

.table thead th { background:linear-gradient(135deg,#1e3a5f,#2d5a8e); color:#fff; font-weight:700; text-transform:uppercase; font-size:0.69rem; letter-spacing:0.5px; padding:10px 11px; border:none; white-space:nowrap; }
.table tbody td { padding:9px 11px; vertical-align:middle; font-size:0.82rem; border-bottom:1px solid rgba(30,58,95,0.05); }
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(30,58,95,0.02); }

.bstat { display:inline-block; font-size:0.68rem; font-weight:700; padding:2px 9px; border-radius:20px; }
.bsuccess { background:rgba(16,185,129,0.12); color:#10b981; }
.bwarning { background:rgba(245,158,11,0.12); color:#f59e0b; }
.bdanger  { background:rgba(220,38,38,0.10);  color:#dc2626; }

.action-group { display:flex; align-items:center; justify-content:center; gap:4px; }
.action-group form.d-contents { display:contents; }
.abtn { width:28px; height:28px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; border:none; cursor:pointer; transition:all .15s; background:transparent; text-decoration:none; }
.abtn-view { color:#2d5a8e; } .abtn-view:hover { background:rgba(45,90,142,0.12); }
.abtn-edit { color:#c49a2a; } .abtn-edit:hover { background:rgba(232,184,75,0.15); }
.abtn-del  { color:#dc2626; } .abtn-del:hover  { background:rgba(220,38,38,0.10); }

.empty-row { text-align:center; padding:44px 0 !important; color:#8a96a3; }
.empty-row i { font-size:2.2rem; display:block; margin-bottom:8px; }

.side-header { padding:12px 16px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); color:#fff; font-size:0.85rem; font-weight:700; display:flex; align-items:center; }
.filter-label { font-size:0.72rem; font-weight:700; color:#4a5a6e; text-transform:uppercase; letter-spacing:0.3px; display:block; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const name = this.dataset.name;
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Hapus Data?',
                html: `Data <strong>${name || 'ini'}</strong> akan dihapus permanen.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#1e3a5f', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
});

function downloadFaktur() {
    var val = document.getElementById('noFaktur').value;
    if (!val) {
        Swal.fire('Pilih No Faktur', 'Pilih No Faktur terlebih dahulu.', 'info');
        return;
    }
    window.open('/asetout/cetak-faktur/' + val, '_blank');
}

function downloadNota() {
    var val = document.getElementById('notaDinas').value;
    if (!val) {
        Swal.fire('Pilih Nota Dinas', 'Pilih Nota Dinas terlebih dahulu.', 'info');
        return;
    }
    window.open('/asetout/download/' + val, '_blank');
}
</script>
@endsection