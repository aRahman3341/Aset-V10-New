@extends('layouts.app')
@section('title') Report Peminjaman - Monitoring Aset @endsection
@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<main id="main" class="main">
<style>
.rp-pagetitle { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid rgba(30,58,95,0.08); }
.rp-pagetitle-left { display:flex; align-items:center; gap:14px; }
.rp-pagetitle-icon { width:46px; height:46px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; box-shadow:0 4px 12px rgba(30,58,95,0.25); }
.rp-pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 3px; }
.rp-pagetitle .breadcrumb { font-size:0.75rem; margin:0; padding:0; background:transparent; }
.rp-pagetitle .breadcrumb a { color:#2d5a8e; text-decoration:none; }
.rp-pagetitle .breadcrumb-item.active { color:#8a96a3; }

/* Stats */
.rp-stats { display:flex; gap:14px; margin-bottom:22px; flex-wrap:wrap; }
.rp-stat { flex:1; min-width:140px; background:#fff; border-radius:12px; border:1px solid rgba(30,58,95,0.08); padding:14px 18px; display:flex; align-items:center; gap:12px; box-shadow:0 2px 8px rgba(30,58,95,0.06); }
.rp-stat-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
.rp-stat-icon.blue  { background:rgba(30,58,95,0.1);  color:#1e3a5f; }
.rp-stat-icon.orange{ background:rgba(255,167,38,0.15); color:#e07b00; }
.rp-stat-icon.green { background:rgba(26,127,75,0.12); color:#1a7f4b; }
.rp-stat-val  { font-size:1.4rem; font-weight:800; color:#1e3a5f; line-height:1; }
.rp-stat-label{ font-size:0.72rem; color:#8a96a3; font-weight:600; margin-top:2px; }

/* Layout */
.rp-layout { display:grid; grid-template-columns:1fr 300px; gap:18px; align-items:start; }
@media(max-width:900px){ .rp-layout { grid-template-columns:1fr; } }

/* Main card */
.rp-card { background:#fff; border-radius:14px; border:1px solid rgba(30,58,95,0.08); box-shadow:0 2px 14px rgba(30,58,95,0.07); overflow:hidden; }
.rp-card-header { padding:14px 20px; border-bottom:1px solid rgba(30,58,95,0.07); display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
.rp-card-title { font-size:0.88rem; font-weight:700; color:#1e3a5f; display:flex; align-items:center; gap:7px; }
.rp-card-body { padding:0; }

/* Search */
.rp-search { display:flex; align-items:center; background:#f8fafd; border:1.5px solid #dee2e6; border-radius:8px; overflow:hidden; height:34px; min-width:220px; }
.rp-search input { border:none; background:transparent; padding:0 12px; font-size:0.82rem; color:#1e3a5f; outline:none; flex:1; }
.rp-search button { border:none; background:none; padding:0 12px; color:#2d5a8e; cursor:pointer; height:100%; }
.rp-search button:hover { background:#e8ecf5; }

/* Table */
.rp-table { width:100%; font-size:0.8rem; border-collapse:collapse; }
.rp-table thead th { background:#f6f9ff; color:#1e3a5f; font-weight:700; font-size:0.69rem; text-transform:uppercase; letter-spacing:0.5px; padding:10px 14px; border-bottom:2px solid #e0e8f5; white-space:nowrap; }
.rp-table tbody td { padding:10px 14px; border-bottom:1px solid rgba(30,58,95,0.05); vertical-align:middle; color:#2d3748; }
.rp-table tbody tr:last-child td { border-bottom:none; }
.rp-table tbody tr:hover td { background:rgba(30,58,95,0.025); }
.rp-table-wrap { overflow-x:auto; }

.badge-dipinjam  { display:inline-block; background:#fff3cd; color:#856404; padding:3px 10px; border-radius:20px; font-size:0.69rem; font-weight:700; white-space:nowrap; }
.badge-kembali   { display:inline-block; background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:20px; font-size:0.69rem; font-weight:700; white-space:nowrap; }

.code-chip { font-family:monospace; font-size:0.8rem; font-weight:700; color:#2d5a8e; background:#eef2ff; padding:2px 8px; border-radius:6px; }

/* Sidebar card */
.rp-side-card { background:#fff; border-radius:14px; border:1px solid rgba(30,58,95,0.08); box-shadow:0 2px 14px rgba(30,58,95,0.07); overflow:hidden; }
.rp-side-header { padding:14px 18px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); color:#fff; display:flex; align-items:center; gap:8px; }
.rp-side-header i { font-size:1rem; }
.rp-side-header span { font-size:0.88rem; font-weight:700; }
.rp-side-body { padding:18px; }

.rp-dl-label { font-size:0.72rem; font-weight:700; color:#4a5a6e; text-transform:uppercase; letter-spacing:0.3px; display:block; margin-bottom:5px; }
.rp-dl-input { width:100%; padding:9px 12px; font-size:0.82rem; color:#1e3a5f; background:#f8fafd; border:1.5px solid #dee2e6; border-radius:8px; outline:none; margin-bottom:12px; transition:border-color .15s; }
.rp-dl-input:focus { border-color:#2d5a8e; background:#fff; box-shadow:0 0 0 3px rgba(45,90,142,0.1); }
.rp-btn-dl { width:100%; padding:10px; border:none; border-radius:9px; font-size:0.82rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .18s; margin-bottom:10px; }
.rp-btn-range { background:linear-gradient(135deg,#1e3a5f,#2d5a8e); color:#fff; box-shadow:0 3px 10px rgba(30,58,95,0.22); }
.rp-btn-range:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(30,58,95,0.32); }
.rp-btn-all { background:linear-gradient(135deg,#1a7f4b,#22a86a); color:#fff; box-shadow:0 3px 10px rgba(26,127,75,0.22); }
.rp-btn-all:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(26,127,75,0.32); }
.rp-divider { height:1px; background:rgba(30,58,95,0.07); margin:14px 0; }
</style>

{{-- Page Title --}}
<div class="rp-pagetitle">
    <div class="rp-pagetitle-left">
        <div class="rp-pagetitle-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
        <div>
            <h1>Report Peminjaman</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item active">Report Peminjaman</li>
            </ol></nav>
        </div>
    </div>
</div>

{{-- Stats --}}
@php
    $total     = $loan->total();
    $dipinjam  = $loan->getCollection()->where('status','Dipinjam')->count();
    $kembali   = $loan->getCollection()->where('status','Dikembalikan')->count();
@endphp
<div class="rp-stats">
    <div class="rp-stat">
        <div class="rp-stat-icon blue"><i class="bi bi-clipboard-data"></i></div>
        <div>
            <div class="rp-stat-val">{{ $total }}</div>
            <div class="rp-stat-label">Total Data</div>
        </div>
    </div>
    <div class="rp-stat">
        <div class="rp-stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="rp-stat-val">{{ $dipinjam }}</div>
            <div class="rp-stat-label">Sedang Dipinjam</div>
        </div>
    </div>
    <div class="rp-stat">
        <div class="rp-stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="rp-stat-val">{{ $kembali }}</div>
            <div class="rp-stat-label">Dikembalikan</div>
        </div>
    </div>
</div>

<div class="rp-layout">

    {{-- ── Tabel Utama ── --}}
    <div class="rp-card">
        <div class="rp-card-header">
            <div class="rp-card-title">
                <i class="bi bi-table"></i> Data Peminjaman
            </div>
            <form action="{{ route('peminjaman.search') }}" method="POST">
                @csrf
                <div class="rp-search">
                    <input type="text" name="query" placeholder="Cari kode, nama barang, peminjam..."
                           value="{{ request()->input('query') }}">
                    <button type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="rp-card-body">
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead>
                        <tr>
                            <th style="width:40px">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Petugas</th>
                            <th>Peminjam</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loan as $item)
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                <td><span class="code-chip">{{ $item->code }}</span></td>
                                <td>
                                    <span style="font-weight:600;">{{ $item->material->nama_barang ?? '-' }}</span><br>
                                    <small style="color:#8a96a3; font-size:0.69rem;">{{ $item->material->kode_barang ?? '' }}</small>
                                </td>
                                <td>{{ $item->tgl_pinjam  ? \Carbon\Carbon::parse($item->tgl_pinjam)->format('d/m/Y')  : '-' }}</td>
                                <td>{{ $item->tgl_kembali ? \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->peminjam ?? '-' }}</td>
                                <td class="text-center">
                                    @if($item->status === 'Dikembalikan')
                                        <span class="badge-kembali"><i class="bi bi-check-circle-fill"></i> Dikembalikan</span>
                                    @else
                                        <span class="badge-dipinjam"><i class="bi bi-hourglass-split"></i> Dipinjam</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                    Tidak ada data peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top">
                {{ $loan->links() }}
            </div>
        </div>
    </div>

    {{-- ── Sidebar Download ── --}}
    <div class="rp-side-card">
        <div class="rp-side-header">
            <i class="bi bi-cloud-arrow-down"></i>
            <span>Download Data</span>
        </div>
        <div class="rp-side-body">

            @if(session('error'))
                <div class="alert alert-danger rounded-3 mb-3" style="font-size:0.8rem;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            <form action="{{ route('peminjaman.export') }}" method="get" id="downloadForm">
                <label class="rp-dl-label">Rentang Awal</label>
                <input type="text" class="rp-dl-input" name="from_date" id="datepicker"
                       placeholder="yyyy-mm-dd" value="{{ old('from_date') }}">

                <label class="rp-dl-label">Rentang Akhir</label>
                <input type="text" class="rp-dl-input" name="to_date" id="datepicker1"
                       placeholder="yyyy-mm-dd" value="{{ old('to_date') }}">

                <button type="button" class="rp-btn-dl rp-btn-range" id="btnDownloadRange">
                    <i class="bi bi-cloud-arrow-down-fill"></i> Download Range
                </button>
            </form>

            <div class="rp-divider"></div>

            <a href="{{ route('peminjaman.reportAll') }}" style="text-decoration:none;">
                <button type="button" class="rp-btn-dl rp-btn-all">
                    <i class="bi bi-cloud-arrow-down-fill"></i> Download Semua
                </button>
            </a>
        </div>
    </div>

</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
    $("#datepicker").datepicker({ dateFormat: "yy-mm-dd" });
    $("#datepicker1").datepicker({ dateFormat: "yy-mm-dd" });
});

document.getElementById('btnDownloadRange').addEventListener('click', function () {
    var from = document.getElementById('datepicker').value;
    var to   = document.getElementById('datepicker1').value;
    if (!from || !to) {
        Swal.fire({ icon:'warning', title:'Lengkapi Tanggal', text:'Isi rentang awal dan akhir terlebih dahulu.', confirmButtonColor:'#1e3a5f' });
        return;
    }
    Swal.fire({
        title: 'Download Data?',
        text: 'Mengunduh data dari ' + from + ' hingga ' + to,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1e3a5f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-cloud-arrow-down-fill"></i> Ya, Download',
        cancelButtonText: 'Batal'
    }).then(r => { if (r.isConfirmed) document.getElementById('downloadForm').submit(); });
});
</script>
@endsection