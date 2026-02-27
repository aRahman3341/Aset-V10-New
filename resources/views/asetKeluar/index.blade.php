@extends('layouts.app')
@section('title')
    Aset Keluar - Monitoring Aset
@endsection
@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/dist/air-datepicker/air-datepicker.css') }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

@php
/**
 * Helper: format Carbon/tanggal ke "5 Januari 2025"
 * Menggantikan formatLocalized('%e %B %Y') yang butuh ext-intl
 */
function tglIndo($date) {
    $bulan = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];
    $d = \Carbon\Carbon::parse($date);
    return $d->day . ' ' . $bulan[$d->month] . ' ' . $d->year;
}
@endphp

<main id="main" class="main">

{{-- ── Page Title ── --}}
<div class="ak-pagetitle">
    <div class="ak-pagetitle-left">
        <div class="ak-pagetitle-icon"><i class="bi bi-box-arrow-up-right"></i></div>
        <div>
            <h1>Aset Keluar</h1>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Aset Keluar</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('asetkeluar.add') }}" class="ak-btn-add">
        <i class="bi bi-plus-circle-fill"></i> Tambah Aset Keluar
    </a>
</div>

@if(session('success'))
    <div class="ak-alert ak-alert-success">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="ak-alert-close" onclick="this.closest('.ak-alert').remove()"><i class="bi bi-x-lg"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="ak-alert ak-alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ session('error') }}</span>
        <button type="button" class="ak-alert-close" onclick="this.closest('.ak-alert').remove()"><i class="bi bi-x-lg"></i></button>
    </div>
@endif

<div class="ak-layout">

    {{-- ════════════ TABEL UTAMA ════════════ --}}
    <div class="ak-main-panel">
        <div class="ak-card">

            {{-- Toolbar --}}
            <div class="ak-toolbar">
                <form action="{{ route('asetkeluar.search') }}" method="POST" class="ak-search-form">
                    @csrf
                    <div class="ak-search-wrap">
                        <i class="bi bi-search ak-search-icon"></i>
                        <input type="text" name="query" class="ak-search-input"
                               placeholder="Cari nomor, pihak, kepada..."
                               value="{{ request()->input('query') }}">
                        <button type="submit" class="ak-search-btn">Cari</button>
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive">
                <table class="ak-table">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nomor Surat</th>
                            <th>Nama Aset</th>
                            <th>Pihak Kesatu</th>
                            <th>Pihak Kedua</th>
                            <th>Diserahkan Kepada</th>
                            <th>Tanggal</th>
                            <th class="text-center" style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($asetkeluar as $item)
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>

                                <td>
                                    <span class="ak-nomor-badge">{{ $item->nomor }}</span>
                                </td>

                                <td>
                                    @foreach ($asets[$item->id] as $rel)
                                        <div class="ak-aset-item">
                                            <span class="ak-aset-dot"></span>
                                            <span>{{ $rel->name }}</span>
                                            <span class="ak-aset-code">{{ $rel->code }}</span>
                                            <span class="ak-aset-nup">NUP {{ $rel->nup }}</span>
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    <div class="ak-person">
                                        <span class="ak-person-name">{{ $item->pihakSatu }}</span>
                                        @if($item->pihakSatuJabatan)
                                            <span class="ak-person-jabatan">{{ $item->pihakSatuJabatan }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="ak-person">
                                        <span class="ak-person-name">{{ $item->pihakDua }}</span>
                                        @if($item->pihakDuaJabatan)
                                            <span class="ak-person-jabatan">{{ $item->pihakDuaJabatan }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td>{{ $item->kepada }}</td>

                                {{-- ✅ PERBAIKAN: ganti formatLocalized → helper tglIndo() --}}
                                <td>
                                    <span class="ak-date">{{ tglIndo($item->created_at) }}</span>
                                </td>

                                <td class="text-center">
                                    <div class="ak-action-group">
                                        <a href="{{ route('asetkeluar.edit', $item->id) }}"
                                           class="ak-abtn ak-abtn-edit" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="{{ route('asetkeluar.download', $item->id) }}"
                                           class="ak-abtn ak-abtn-download" title="Download BAST">
                                            <i class="bi bi-file-earmark-word"></i>
                                        </a>
                                        <form action="{{ route('asetkeluar.destroy', $item->id) }}" method="POST"
                                              id="delForm{{ $item->id }}" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                    class="ak-abtn ak-abtn-del delete-button"
                                                    data-form-id="delForm{{ $item->id }}"
                                                    title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="ak-empty">
                                    <i class="bi bi-inbox"></i>
                                    <p>Belum ada data aset keluar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="ak-card-footer">
                {{ $asetkeluar->links() }}
            </div>

        </div>
    </div>

    {{-- ════════════ PANEL DOWNLOAD ════════════ --}}
    <div class="ak-side-panel">
        <div class="ak-card">
            <div class="ak-side-header">
                <i class="bi bi-cloud-arrow-down-fill"></i>
                <span>Download Rekap</span>
            </div>
            <div class="ak-side-body">
                <p class="ak-side-desc">Export data aset keluar dalam rentang tanggal tertentu ke file Excel.</p>

                <form action="{{ route('asetkeluar.export') }}" method="GET">
                    <div class="ak-field">
                        <label class="ak-label">Dari Tanggal</label>
                        <div class="ak-input-wrap">
                            <span class="ak-input-icon"><i class="bi bi-calendar-event"></i></span>
                            <input type="text" class="ak-input" name="from_date"
                                   id="datepicker" placeholder="yyyy-mm-dd"
                                   value="{{ old('from_date') }}">
                        </div>
                        @error('from_date')<span class="ak-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="ak-field">
                        <label class="ak-label">Sampai Tanggal</label>
                        <div class="ak-input-wrap">
                            <span class="ak-input-icon"><i class="bi bi-calendar-check"></i></span>
                            <input type="text" class="ak-input" name="to_date"
                                   id="datepicker1" placeholder="yyyy-mm-dd"
                                   value="{{ old('to_date') }}">
                        </div>
                        @error('to_date')<span class="ak-field-error">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="ak-btn-export">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                        Download Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>{{-- end ak-layout --}}

</main>

<style>
:root {
    --navy:     #1e3a5f;
    --navy-mid: #2d5a8e;
    --navy-lt:  #eef2f8;
    --border:   rgba(30,58,95,0.10);
    --radius:   14px;
    --shadow:   0 2px 16px rgba(30,58,95,0.07);
}

/* ── Page title ── */
.ak-pagetitle {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 22px; flex-wrap: wrap; gap: 10px;
}
.ak-pagetitle-left { display: flex; align-items: center; gap: 14px; }
.ak-pagetitle-icon {
    width: 46px; height: 46px;
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(30,58,95,0.25); flex-shrink: 0;
}
.ak-pagetitle h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.3rem; font-weight: 800; color: var(--navy); margin: 0 0 3px;
}
.ak-pagetitle .breadcrumb { font-size: 0.75rem; }
.ak-pagetitle .breadcrumb a { color: var(--navy-mid); text-decoration: none; }
.ak-pagetitle .breadcrumb-item.active { color: #8a96a3; }

.ak-btn-add {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px;
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    color: #fff; border: none; border-radius: 10px;
    font-size: 0.84rem; font-weight: 700; text-decoration: none;
    box-shadow: 0 4px 12px rgba(30,58,95,0.25);
    transition: all .18s;
}
.ak-btn-add:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(30,58,95,0.35); }

/* ── Alert ── */
.ak-alert {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; border-radius: 10px; margin-bottom: 16px;
    font-size: 0.84rem; position: relative;
}
.ak-alert-success { background: #f0fdf4; border: 1px solid rgba(16,185,129,0.25); color: #065f46; }
.ak-alert-danger  { background: #fef2f2; border: 1px solid rgba(220,38,38,0.2);  color: #991b1b; }
.ak-alert-close {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: inherit; opacity: .6; font-size: 0.8rem;
}
.ak-alert-close:hover { opacity: 1; }

/* ── Layout ── */
.ak-layout {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 1024px) { .ak-layout { grid-template-columns: 1fr; } }

/* ── Card ── */
.ak-card {
    background: #fff; border-radius: var(--radius);
    border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden;
}

/* ── Toolbar ── */
.ak-toolbar {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    background: #fafbfd;
}
.ak-search-form { display: flex; }
.ak-search-wrap {
    display: flex; align-items: center;
    background: #fff; border: 1.5px solid var(--border);
    border-radius: 9px; overflow: hidden; height: 38px; flex: 1;
}
.ak-search-icon { padding: 0 10px; color: #8a96a3; font-size: 0.9rem; }
.ak-search-input {
    flex: 1; border: none; background: transparent;
    font-size: 0.83rem; padding: 0 6px; outline: none; color: var(--navy);
}
.ak-search-btn {
    background: var(--navy); color: #fff; border: none;
    padding: 0 16px; font-size: 0.8rem; font-weight: 600; height: 100%;
    cursor: pointer; transition: background .18s; white-space: nowrap;
}
.ak-search-btn:hover { background: var(--navy-mid); }

/* ── Table ── */
.ak-table { width: 100%; border-collapse: collapse; }
.ak-table thead th {
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    color: #fff; font-size: 0.71rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 11px 14px; border: none; white-space: nowrap;
}
.ak-table tbody td {
    padding: 11px 14px; vertical-align: middle;
    font-size: 0.83rem; color: #3d5170;
    border-bottom: 1px solid rgba(30,58,95,0.05);
}
.ak-table tbody tr:last-child td { border-bottom: none; }
.ak-table tbody tr:hover td { background: rgba(30,58,95,0.02); }

.ak-nomor-badge {
    font-family: 'DM Mono', monospace;
    font-size: 0.75rem; font-weight: 600;
    background: rgba(30,58,95,0.07);
    color: var(--navy); padding: 3px 9px; border-radius: 6px;
    border: 1px solid rgba(30,58,95,0.10);
    white-space: nowrap;
}

.ak-aset-item {
    display: flex; align-items: center; gap: 5px;
    font-size: 0.8rem; margin-bottom: 3px;
}
.ak-aset-item:last-child { margin-bottom: 0; }
.ak-aset-dot {
    width: 5px; height: 5px; border-radius: 50%;
    background: var(--navy-mid); flex-shrink: 0;
}
.ak-aset-code {
    font-family: 'DM Mono', monospace; font-size: 0.71rem;
    background: rgba(65,84,241,0.08); color: #4154f1;
    padding: 1px 6px; border-radius: 4px;
}
.ak-aset-nup {
    font-size: 0.7rem; color: #8a96a3;
}

.ak-person-name { display: block; font-weight: 600; color: var(--navy); font-size: 0.82rem; }
.ak-person-jabatan { display: block; font-size: 0.72rem; color: #8a96a3; margin-top: 1px; }

.ak-date {
    font-size: 0.78rem; color: #5a6a7e; white-space: nowrap;
}

.ak-action-group { display: flex; align-items: center; justify-content: center; gap: 4px; }
.ak-abtn {
    width: 30px; height: 30px; border-radius: 7px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.82rem; border: none; cursor: pointer; background: transparent; text-decoration: none;
    transition: all .15s;
}
.ak-abtn-edit     { color: #c49a2a; } .ak-abtn-edit:hover     { background: rgba(232,184,75,0.15); color: #c49a2a; }
.ak-abtn-download { color: #2d5a8e; } .ak-abtn-download:hover { background: rgba(45,90,142,0.12); color: #2d5a8e; }
.ak-abtn-del      { color: #dc2626; } .ak-abtn-del:hover      { background: rgba(220,38,38,0.10); }

.ak-empty { text-align: center; padding: 52px 0 !important; color: #8a96a3; }
.ak-empty i { font-size: 2.5rem; display: block; margin-bottom: 8px; }
.ak-empty p { margin: 0; font-size: 0.84rem; }

.ak-card-footer {
    padding: 12px 18px; border-top: 1px solid var(--border); background: #fafbfd;
}

/* ── Side panel ── */
.ak-side-header {
    display: flex; align-items: center; gap: 8px;
    padding: 14px 18px;
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    color: #fff; font-size: 0.88rem; font-weight: 700;
}
.ak-side-body { padding: 18px; }
.ak-side-desc { font-size: 0.78rem; color: #5a6a7e; margin-bottom: 16px; }

.ak-field { margin-bottom: 12px; }
.ak-label {
    display: block; font-size: 0.74rem; font-weight: 700;
    color: #4a5a6e; margin-bottom: 5px;
    text-transform: uppercase; letter-spacing: 0.3px;
}
.ak-input-wrap { position: relative; }
.ak-input-icon {
    position: absolute; left: 10px; top: 50%;
    transform: translateY(-50%); color: #8a96a3; font-size: 0.85rem; pointer-events: none;
}
.ak-input {
    width: 100%; padding: 8px 12px 8px 32px;
    font-size: 0.83rem; color: var(--navy);
    background: #f8fafd; border: 1.5px solid var(--border);
    border-radius: 8px; outline: none; transition: all .18s;
}
.ak-input:focus { background: #fff; border-color: var(--navy-mid); box-shadow: 0 0 0 3px rgba(45,90,142,0.10); }
.ak-field-error { display: block; font-size: 0.73rem; color: #dc2626; margin-top: 4px; }

.ak-btn-export {
    width: 100%; padding: 10px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff; border: none; border-radius: 9px;
    font-size: 0.84rem; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px;
    transition: all .18s; margin-top: 4px;
    box-shadow: 0 3px 10px rgba(16,185,129,0.25);
}
.ak-btn-export:hover { background: linear-gradient(135deg, #059669, #047857); transform: translateY(-1px); }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('assets/dist/air-datepicker/air-datepicker.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Delete confirmation
    document.querySelectorAll('.delete-button').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const formId = this.getAttribute('data-form-id');
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Data aset keluar ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e3a5f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) document.getElementById(formId).submit();
            });
        });
    });
});

$(function () {
    $("#datepicker").datepicker({ dateFormat: "yy-mm-dd" });
    $("#datepicker1").datepicker({ dateFormat: "yy-mm-dd" });
});
</script>
@endsection