@extends('layouts.app')
@section('title') Report Barang Keluar - Monitoring Aset @endsection
@section('content')

<main id="main" class="main">

<style>
.pagetitle { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon { width:44px; height:44px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.15rem; box-shadow:0 4px 12px rgba(30,58,95,0.22); }
.pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0; padding:0; background:transparent; font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#2d5a8e; text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

.main-card { background:#fff; border-radius:12px; border:1px solid rgba(30,58,95,0.07); box-shadow:0 2px 14px rgba(30,58,95,0.07); overflow:hidden; }

.card-header-bar {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 18px;
    background:linear-gradient(135deg,#1e3a5f,#2d5a8e);
    color:#fff; flex-wrap:wrap; gap:8px;
}
.card-header-bar span { font-size:0.9rem; font-weight:700; display:flex; align-items:center; gap:8px; }

.btn-download {
    display:inline-flex; align-items:center; gap:7px;
    padding:8px 16px; background:#10b981; color:#fff;
    border:none; border-radius:8px; font-size:0.82rem; font-weight:700;
    text-decoration:none; cursor:pointer; transition:all .18s;
    box-shadow:0 3px 10px rgba(16,185,129,0.3);
}
.btn-download:hover { background:#059669; color:#fff; transform:translateY(-1px); }

.table thead th {
    background:#f6f9ff; color:#1e3a5f; font-weight:700;
    text-transform:uppercase; font-size:0.69rem; letter-spacing:0.5px;
    padding:10px 14px; border:none; border-bottom:2px solid #e0e8f5; white-space:nowrap;
}
.table tbody td { padding:10px 14px; vertical-align:middle; font-size:0.83rem; border-bottom:1px solid rgba(30,58,95,0.05); }
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(30,58,95,0.02); }

.nomor-badge {
    font-family:'Courier New',monospace; font-size:0.73rem; font-weight:700;
    background:rgba(30,58,95,0.07); color:#1e3a5f;
    padding:3px 9px; border-radius:6px;
    border:1px solid rgba(30,58,95,0.12); white-space:nowrap;
}
.aset-item { display:flex; align-items:center; gap:5px; font-size:0.8rem; margin-bottom:3px; }
.aset-item:last-child { margin-bottom:0; }
.aset-dot { width:5px; height:5px; border-radius:50%; background:#2d5a8e; flex-shrink:0; }
.aset-code { font-family:'Courier New',monospace; font-size:0.71rem; background:rgba(65,84,241,0.08); color:#4154f1; padding:1px 6px; border-radius:4px; }
.aset-nup { font-size:0.7rem; color:#8a96a3; }

.person-name { display:block; font-weight:600; color:#1e3a5f; font-size:0.82rem; }
.person-jabatan { display:block; font-size:0.72rem; color:#8a96a3; margin-top:1px; }

.date-text { font-size:0.78rem; color:#5a6a7e; white-space:nowrap; }

.empty-row { text-align:center; padding:52px 0 !important; color:#8a96a3; }
.empty-row i { font-size:2.5rem; display:block; margin-bottom:8px; }

/* ── Pagination ── */
.table-footer { display:flex; align-items:center; border-top:2px solid rgba(30,58,95,0.06); background:#fafbfd; min-height:56px; flex-wrap:wrap; }
.pag-nav { display:flex; align-items:center; padding:10px 16px; flex:1; flex-wrap:wrap; gap:4px; }
.pag-list { display:flex; align-items:center; gap:3px; list-style:none; margin:0; padding:0; }
.pag-btn { display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:34px; padding:0 9px; border-radius:6px; font-size:0.8rem; font-weight:700; color:#1e3a5f; background:#fff; border:1.5px solid rgba(30,58,95,0.13); text-decoration:none; transition:all .15s ease; }
.pag-btn:hover:not(.pag-btn-active) { background:#1e3a5f; color:#fff; border-color:#1e3a5f; text-decoration:none; transform:translateY(-1px); }
.pag-btn-active { background:linear-gradient(135deg,#1e3a5f,#2d5a8e) !important; color:#fff !important; border-color:transparent !important; box-shadow:0 3px 12px rgba(45,90,142,0.28) !important; min-width:38px; height:38px; }
.pag-disabled .pag-btn { opacity:.3; cursor:not-allowed; pointer-events:none; }
.pag-ellipsis span { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; color:#a0aab4; }
.pag-info { font-size:0.74rem; color:#8a96a3; margin-left:8px; white-space:nowrap; }
.pag-info strong { color:#1e3a5f; }
</style>

@php
function tglIndoReport($date) {
    $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
              7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    $d = \Carbon\Carbon::parse($date);
    return $d->day . ' ' . $bulan[$d->month] . ' ' . $d->year;
}
@endphp

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
        <div>
            <h1>Report Barang Keluar</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item active">Report Barang Keluar</li>
            </ol></nav>
        </div>
    </div>
</div>

<div class="main-card">

    {{-- Header --}}
    <div class="card-header-bar">
        <span><i class="bi bi-table"></i> Data Aset Keluar (BAST)</span>
        <a href="{{ route('asetkeluar.export') }}?from_date={{ now()->startOfYear()->format('Y-m-d') }}&to_date={{ now()->format('Y-m-d') }}"
           class="btn-download">
            <i class="bi bi-cloud-arrow-down-fill"></i> Download Excel
        </a>
    </div>

    {{-- Tabel --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width:46px">No</th>
                    <th>Nomor Surat</th>
                    <th>Nama Aset</th>
                    <th>Pihak Kesatu</th>
                    <th>Pihak Kedua</th>
                    <th>Diserahkan Kepada</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($asetKeluarList as $item)
                    <tr>
                        <td class="text-center text-muted small">
                            {{ $asetKeluarList->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <span class="nomor-badge">{{ $item->nomor }}</span>
                        </td>

                        <td>
                            @if(isset($asets[$item->id]) && $asets[$item->id]->count() > 0)
                                @foreach ($asets[$item->id] as $rel)
                                    <div class="aset-item">
                                        <span class="aset-dot"></span>
                                        <span>{{ $rel->{'Nama Barang'} ?? '-' }}</span>
                                        <span class="aset-code">{{ $rel->{'Kode Barang'} ?? '' }}</span>
                                        <span class="aset-nup">NUP {{ $rel->nup }}</span>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>

                        <td>
                            <span class="person-name">{{ $item->pihakSatu }}</span>
                            @if($item->pihakSatuJabatan)
                                <span class="person-jabatan">{{ $item->pihakSatuJabatan }}</span>
                            @endif
                        </td>

                        <td>
                            <span class="person-name">{{ $item->pihakDua }}</span>
                            @if($item->pihakDuaJabatan)
                                <span class="person-jabatan">{{ $item->pihakDuaJabatan }}</span>
                            @endif
                        </td>

                        <td>{{ $item->kepada }}</td>

                        <td>
                            <span class="date-text">{{ tglIndoReport($item->created_at) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">
                            <i class="bi bi-inbox"></i>
                            <p class="mb-0">Belum ada data aset keluar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="table-footer">
        <div class="pag-nav">
            @if($asetKeluarList->lastPage() > 1)
            <ul class="pag-list">
                <li class="{{ $asetKeluarList->onFirstPage() ? 'pag-disabled' : '' }}">
                    <a href="{{ $asetKeluarList->url(1) }}" class="pag-btn" title="Pertama">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="{{ $asetKeluarList->onFirstPage() ? 'pag-disabled' : '' }}">
                    <a href="{{ $asetKeluarList->previousPageUrl() ?? '#' }}" class="pag-btn" title="Sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                @php
                    $cur   = $asetKeluarList->currentPage();
                    $last  = $asetKeluarList->lastPage();
                    $pages = [];
                    for ($i = 1; $i <= $last; $i++) {
                        if ($i == 1 || $i == $last || ($i >= $cur - 2 && $i <= $cur + 2)) $pages[] = $i;
                    }
                    $prev = null;
                @endphp
                @foreach($pages as $page)
                    @if($prev !== null && $page - $prev > 1)
                        <li class="pag-ellipsis"><span>···</span></li>
                    @endif
                    <li>
                        <a href="{{ $asetKeluarList->url($page) }}"
                           class="pag-btn {{ $page == $cur ? 'pag-btn-active' : '' }}">
                            {{ $page }}
                        </a>
                    </li>
                    @php $prev = $page; @endphp
                @endforeach
                <li class="{{ !$asetKeluarList->hasMorePages() ? 'pag-disabled' : '' }}">
                    <a href="{{ $asetKeluarList->nextPageUrl() ?? '#' }}" class="pag-btn" title="Berikutnya">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="{{ !$asetKeluarList->hasMorePages() ? 'pag-disabled' : '' }}">
                    <a href="{{ $asetKeluarList->url($asetKeluarList->lastPage()) }}" class="pag-btn" title="Terakhir">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            @endif
            <span class="pag-info">
                <strong>{{ $asetKeluarList->firstItem() ?? 0 }}–{{ $asetKeluarList->lastItem() ?? 0 }}</strong>
                dari <strong>{{ $asetKeluarList->total() }}</strong> data
                &nbsp;·&nbsp; Hal. <strong>{{ $asetKeluarList->currentPage() }}</strong>/{{ $asetKeluarList->lastPage() }}
            </span>
        </div>
    </div>

</div>

</main>
@endsection