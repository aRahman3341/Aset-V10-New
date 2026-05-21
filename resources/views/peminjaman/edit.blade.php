@extends('layouts.app')
@section('title') Edit Peminjaman - Monitoring Aset @endsection
@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<main id="main" class="main">
<style>
.pm-pagetitle { display:flex; align-items:center; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid rgba(30,58,95,0.08); }
.pm-pagetitle-left { display:flex; align-items:center; gap:14px; }
.pm-pagetitle-icon { width:46px; height:46px; background:linear-gradient(135deg,#c49a2a,#e8b84b); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; box-shadow:0 4px 12px rgba(196,154,42,0.3); flex-shrink:0; }
.pm-pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 3px; }
.pm-pagetitle .breadcrumb { font-size:0.75rem; margin:0; padding:0; background:transparent; }
.pm-pagetitle .breadcrumb a { color:#2d5a8e; text-decoration:none; }
.pm-pagetitle .breadcrumb-item.active { color:#8a96a3; }

.pm-card { background:#fff; border-radius:16px; border:1px solid rgba(30,58,95,0.08); box-shadow:0 2px 16px rgba(30,58,95,0.07); overflow:hidden; }
.pm-card-header { padding:16px 24px; background:linear-gradient(135deg,#c49a2a,#e8b84b); display:flex; align-items:center; gap:10px; color:#fff; }
.pm-card-header i { font-size:1.1rem; }
.pm-card-header span { font-size:0.95rem; font-weight:700; }
.pm-card-body { padding:28px 28px 20px; }

.pm-label { display:block; font-size:0.78rem; font-weight:700; color:#4a5a6e; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px; }
.req { color:#dc3545; }
.pm-input { width:100%; padding:10px 14px; font-size:0.85rem; color:#1e3a5f; background:#f8fafd; border:1.5px solid #dee2e6; border-radius:10px; outline:none; transition:border-color .15s; }
.pm-input:focus { background:#fff; border-color:#c49a2a; box-shadow:0 0 0 3px rgba(196,154,42,0.12); }
.pm-field { margin-bottom:18px; }
.pm-field-error { display:block; font-size:0.75rem; color:#dc3545; margin-top:4px; }
.pm-divider { display:flex; align-items:center; gap:10px; margin:4px 0 18px; font-size:0.75rem; font-weight:700; color:#8a96a3; text-transform:uppercase; letter-spacing:0.5px; }
.pm-divider::before,.pm-divider::after { content:''; flex:1; height:1px; background:rgba(30,58,95,0.08); }

/* ── Aset Picker ── */
.aset-picker-wrap { border:1.5px solid #dee2e6; border-radius:12px; overflow:hidden; }
.aset-picker-search { display:flex; align-items:center; background:#f8fafd; border-bottom:1.5px solid #dee2e6; padding:0 14px; gap:8px; }
.aset-picker-search i { color:#8a96a3; font-size:0.9rem; flex-shrink:0; }
.aset-picker-search input { flex:1; border:none; background:transparent; padding:10px 0; font-size:0.84rem; color:#1e3a5f; outline:none; }
.aset-picker-list { max-height:280px; overflow-y:auto; }
.aset-picker-list::-webkit-scrollbar { width:5px; }
.aset-picker-list::-webkit-scrollbar-thumb { background:#c5cfe0; border-radius:3px; }
.aset-row { display:flex; align-items:center; gap:12px; padding:11px 16px; border-bottom:1px solid rgba(30,58,95,0.05); cursor:pointer; transition:background .12s; user-select:none; }
.aset-row:last-child { border-bottom:none; }
.aset-row:hover { background:#f0f5ff; }
.aset-row.selected { background:#eef3ff; }
.aset-checkbox { width:18px; height:18px; border-radius:5px; border:2px solid #c5cfe0; background:#fff; flex-shrink:0; display:flex; align-items:center; justify-content:center; transition:all .15s; }
.aset-row.selected .aset-checkbox { background:#c49a2a; border-color:#c49a2a; }
.aset-checkbox i { font-size:0.65rem; color:#fff; display:none; }
.aset-row.selected .aset-checkbox i { display:block; }
.aset-info { flex:1; min-width:0; }
.aset-name { font-size:0.83rem; font-weight:600; color:#1e3a5f; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.aset-meta { display:flex; align-items:center; gap:6px; margin-top:2px; }
.aset-code { font-size:0.7rem; background:rgba(196,154,42,0.1); color:#c49a2a; padding:1px 6px; border-radius:4px; font-family:'Courier New',monospace; font-weight:600; }
.aset-nup { font-size:0.7rem; color:#8a96a3; }
.aset-empty { padding:28px; text-align:center; color:#8a96a3; font-size:0.84rem; }
.aset-empty i { font-size:1.8rem; display:block; margin-bottom:6px; }
.aset-footer { padding:10px 16px; background:#f8fafd; border-top:1.5px solid #dee2e6; display:flex; align-items:center; justify-content:space-between; }
.aset-footer-count { font-size:0.78rem; color:#5a6a7e; }
.aset-footer-count strong { color:#1e3a5f; font-weight:700; }
.aset-select-all { font-size:0.75rem; color:#c49a2a; font-weight:600; cursor:pointer; background:none; border:none; padding:0; }
.aset-select-all:hover { text-decoration:underline; }
.aset-warn { display:none; font-size:0.75rem; color:#dc3545; margin-top:6px; }
.aset-warn.show { display:block; }

/* ── Layout warning ── */
.layout-warn {
    display:none;
    margin-top:8px;
    padding:10px 14px;
    border-radius:10px;
    font-size:0.78rem;
    gap:8px;
    align-items:flex-start;
    line-height:1.5;
}
.layout-warn.show { display:flex; }
.layout-warn i { flex-shrink:0; margin-top:2px; }
.layout-warn.tier-compact { background:rgba(186,117,23,0.08); border:1.5px solid rgba(186,117,23,0.25); color:#854F0B; }
.layout-warn.tier-multipage { background:rgba(45,90,142,0.07); border:1.5px solid rgba(45,90,142,0.22); color:#1e3a5f; }

/* ── Aset tidak tersedia ── */
.aset-row.unavailable { opacity:0.45; cursor:not-allowed; pointer-events:none; background:#f8f8f8 !important; }
.aset-row.unavailable .aset-checkbox { background:#e9ecef !important; border-color:#ced4da !important; }
.aset-unavail-badge { font-size:0.62rem; background:#dc354520; color:#dc3545; padding:1px 6px; border-radius:4px; font-weight:700; margin-left:4px; white-space:nowrap; }

.pm-btn-submit { width:100%; padding:12px; background:linear-gradient(135deg,#c49a2a,#e8b84b); color:#fff; border:none; border-radius:10px; font-size:0.9rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 12px rgba(196,154,42,0.3); transition:all .18s; }
.pm-btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(196,154,42,0.4); }
.pm-btn-back { width:100%; padding:11px; background:#f4f6fb; color:#5a6a7e; border:1.5px solid #dee2e6; border-radius:10px; font-size:0.88rem; font-weight:600; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .15s; }
.pm-btn-back:hover { background:#e8ecf5; color:#1e3a5f; text-decoration:none; }
.icon-input { position:relative; }
.icon-input i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#8a96a3; pointer-events:none; }
.icon-input input { padding-left:34px; }
.pm-select { width:100%; padding:10px 14px; font-size:0.85rem; color:#1e3a5f; background:#f8fafd; border:1.5px solid #dee2e6; border-radius:10px; outline:none; appearance:none; cursor:pointer; }
.pm-select:focus { background:#fff; border-color:#c49a2a; box-shadow:0 0 0 3px rgba(196,154,42,0.12); }
.pm-select-wrap { position:relative; }
.pm-select-wrap::after { content:'\F282'; font-family:'bootstrap-icons'; position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#8a96a3; pointer-events:none; font-size:0.75rem; }
</style>

@php
    $selectedIds = [];
    if ($loan->material_id) {
        $decoded = json_decode($loan->material_id, true);
        $selectedIds = is_array($decoded) ? array_map('strval', $decoded) : [];
    }
@endphp

<div class="pm-pagetitle">
    <div class="pm-pagetitle-left">
        <div class="pm-pagetitle-icon"><i class="bi bi-pencil-square"></i></div>
        <div>
            <h1>Edit Peminjaman</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Peminjaman</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol></nav>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-11">
        <div class="pm-card">
            <div class="pm-card-header">
                <i class="bi bi-pencil-fill"></i>
                <span>Form Edit Peminjaman — Kode: {{ $loan->code }}</span>
            </div>
            <div class="pm-card-body">

                @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('peminjaman.update', $loan->id) }}" id="formEditPeminjaman">
                    @csrf
                    @method('PUT')

                    {{-- ── Periode ── --}}
                    <div class="pm-divider">Periode Peminjaman</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="pm-field">
                                <label class="pm-label">Tanggal Pinjam <span class="req">*</span></label>
                                <div class="icon-input">
                                    <i class="bi bi-calendar-event"></i>
                                    <input type="text" name="tgl_pinjam" id="datepicker"
                                           class="pm-input" placeholder="yyyy-mm-dd"
                                           value="{{ old('tgl_pinjam', $loan->tgl_pinjam) }}" required>
                                </div>
                                @error('tgl_pinjam')<span class="pm-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pm-field">
                                <label class="pm-label">Tanggal Kembali <span class="req">*</span></label>
                                <div class="icon-input">
                                    <i class="bi bi-calendar-check"></i>
                                    <input type="text" name="tgl_kembali" id="datepicker1"
                                           class="pm-input" placeholder="yyyy-mm-dd"
                                           value="{{ old('tgl_kembali', $loan->tgl_kembali) }}" required>
                                </div>
                                @error('tgl_kembali')<span class="pm-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- ── Peminjam ── --}}
                    <div class="pm-divider">Data Peminjam</div>
                    <div class="pm-field">
                        <label class="pm-label">Nama Peminjam <span class="req">*</span></label>
                        <div class="icon-input">
                            <i class="bi bi-person"></i>
                            <input type="text" name="peminjam" class="pm-input"
                                   placeholder="Masukkan nama peminjam"
                                   value="{{ old('peminjam', $loan->peminjam) }}" required>
                        </div>
                        @error('peminjam')<span class="pm-field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- ── Kop Surat ── --}}
                    <div class="pm-divider">Kop Surat</div>
                    <div class="pm-field">
                        <label class="pm-label">Kop Surat yang Digunakan <span class="req">*</span></label>
                        <div class="pm-select-wrap">
                            <select name="kop_surat" class="pm-select" required>
                                <option value="">-- Pilih Kop Surat --</option>
                                @foreach ($kopOptions as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('kop_surat', $loan->kop_surat ?? '') == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('kop_surat')<span class="pm-field-error">{{ $message }}</span>@enderror

                        <div id="kopPreview" style="margin-top:10px; padding:10px 14px; background:#f0f5ff;
                            border:1.5px solid #c5cfe0; border-radius:10px; font-size:0.8rem; color:#1e3a5f; display:none;">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            <span id="kopPreviewText"></span>
                        </div>
                    </div>

                    {{-- ── Daftar Barang ── --}}
                    <div class="pm-divider">Daftar Barang yang Dipinjam</div>
                    <div class="pm-field">
                        <label class="pm-label">
                            Pilih Aset <span class="req">*</span>
                            <span style="color:#8a96a3;font-weight:400;text-transform:none;font-size:0.72rem;">
                                (aset yang sebelumnya dipilih sudah tercentang)
                            </span>
                        </label>

                        <div class="aset-picker-wrap">
                            <div class="aset-picker-search">
                                <i class="bi bi-search"></i>
                                <input type="text" id="pmSearch" placeholder="Cari nama atau kode aset...">
                            </div>
                            <div class="aset-picker-list" id="pmPickerList">
                                @forelse ($material as $item)
                                    @php
                                        $namaBarang = $item->nama_barang ?? ($item->{'Nama Barang'} ?? '-');
                                        $kodeBarang = $item->kode_barang ?? ($item->{'Kode Barang'} ?? '-');
                                        $nup        = $item->nup ?? '-';
                                        $isSelected = in_array((string)$item->id, $selectedIds);
                                        $isUnavail  = in_array($item->id, $unavailableIds ?? []);
                                    @endphp
                                    <label class="aset-row {{ $isSelected ? 'selected' : '' }} {{ $isUnavail ? 'unavailable' : '' }}"
                                           data-search="{{ strtolower($namaBarang . ' ' . $kodeBarang . ' ' . $nup) }}">
                                        <input type="checkbox"
                                               name="material_id[]"
                                               value="{{ $item->id }}"
                                               class="pm-cb"
                                               style="display:none"
                                               {{ $isSelected ? 'checked' : '' }}
                                               {{ $isUnavail ? 'disabled' : '' }}>
                                        <div class="aset-checkbox"><i class="bi bi-check-lg"></i></div>
                                        <div class="aset-info">
                                            <div class="aset-name">
                                                {{ $namaBarang }}
                                                @if($isUnavail)
                                                    <span class="aset-unavail-badge">Sedang Dipinjam</span>
                                                @endif
                                            </div>
                                            <div class="aset-meta">
                                                <span class="aset-code">{{ $kodeBarang }}</span>
                                                <span class="aset-nup">NUP {{ $nup }}</span>
                                            </div>
                                        </div>
                                    </label>
                                @empty
                                    <div class="aset-empty">
                                        <i class="bi bi-inbox"></i>
                                        Tidak ada aset tersedia.
                                    </div>
                                @endforelse
                            </div>
                            <div class="aset-footer">
                                <span class="aset-footer-count">
                                    Dipilih: <strong id="pmSelectedCount">{{ count($selectedIds) }}</strong> barang
                                </span>
                                <button type="button" class="aset-select-all" id="pmSelectAll">
                                    Pilih Semua
                                </button>
                            </div>
                        </div>

                        {{-- Peringatan belum pilih --}}
                        <span class="aset-warn" id="pmWarn">
                            <i class="bi bi-exclamation-circle me-1"></i>Pilih minimal satu barang.
                        </span>

                        {{-- ── Peringatan layout surat (muncul otomatis) ── --}}
                        <div class="layout-warn" id="pmLayoutWarn">
                            <i class="bi bi-info-circle-fill"></i>
                            <span id="pmLayoutWarnText"></span>
                        </div>

                        @error('material_id')<span class="pm-field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- ── Tombol ── --}}
                    <div class="row g-3 mt-2 pm-divider">
                        <div class="col-md-8">
                            <button type="submit" class="pm-btn-submit">
                                <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('peminjaman.index') }}" class="pm-btn-back">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
/* ── Kop Surat Preview ── */
(function () {
    var kopSelect  = document.querySelector('select[name="kop_surat"]');
    var kopPreview = document.getElementById('kopPreview');
    var kopText    = document.getElementById('kopPreviewText');
    var descriptions = {
        'BTSB':   'Kop: KEMENTERIAN PEKERJAAN UMUM — DIREKTORAT JENDERAL CIPTA KARYA — BALAI TEKNIK SAINS BANGUNAN',
        'SATKER': 'Kop: KEMENTERIAN PEKERJAAN UMUM — DIREKTORAT JENDERAL CIPTA KARYA — SATUAN KERJA BALAI TEKNIK SAINS BANGUNAN',
    };
    function updatePreview() {
        var val = kopSelect.value;
        if (val && descriptions[val]) {
            kopText.textContent = descriptions[val];
            kopPreview.style.display = 'block';
        } else {
            kopPreview.style.display = 'none';
        }
    }
    kopSelect.addEventListener('change', updatePreview);
    updatePreview();
})();

/* ── Datepicker ── */
$(function () {
    $("#datepicker").datepicker({ dateFormat: "yy-mm-dd" });
    $("#datepicker1").datepicker({ dateFormat: "yy-mm-dd" });
});

/* ── Aset Picker ── */
(function () {
    'use strict';

    var LIMIT_NORMAL  = 8;
    var LIMIT_COMPACT = 15;

    /* Toggle baris */
    document.querySelectorAll('#pmPickerList .aset-row').forEach(function (row) {
        row.addEventListener('click', function () {
            var cb = this.querySelector('.pm-cb');
            cb.checked = !cb.checked;
            this.classList.toggle('selected', cb.checked);
            updateCount();
        });
    });

    /* Hitung & tampilkan warning layout */
    function updateCount() {
        var n      = document.querySelectorAll('.pm-cb:checked').length;
        var warnEl = document.getElementById('pmWarn');
        var lwDiv  = document.getElementById('pmLayoutWarn');
        var lwText = document.getElementById('pmLayoutWarnText');

        document.getElementById('pmSelectedCount').textContent = n;

        if (n > 0) warnEl.classList.remove('show');

        /* Reset class */
        lwDiv.classList.remove('show', 'tier-compact', 'tier-multipage');

        if (n > LIMIT_COMPACT) {
            lwDiv.classList.add('show', 'tier-multipage');
            lwText.innerHTML =
                '<strong>' + n + ' barang dipilih.</strong> ' +
                'Surat akan dicetak <strong>2 halaman</strong> — ' +
                'tanda tangan otomatis pindah ke halaman baru.';
        } else if (n > LIMIT_NORMAL) {
            lwDiv.classList.add('show', 'tier-compact');
            lwText.innerHTML =
                '<strong>' + n + ' barang dipilih.</strong> ' +
                'Surat akan dicetak <strong>mode kompak</strong> ' +
                '(font lebih kecil) agar tetap 1 halaman.';
        }
    }

    /* Jalankan saat halaman load (untuk edit — sudah ada item tercentang) */
    updateCount();

    /* Scroll ke item pertama yang tercentang */
    var firstSelected = document.querySelector('#pmPickerList .aset-row.selected');
    if (firstSelected) {
        setTimeout(function () {
            firstSelected.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }, 200);
    }

    /* Search */
    document.getElementById('pmSearch').addEventListener('input', function () {
        var q = this.value.toLowerCase().trim();
        document.querySelectorAll('#pmPickerList .aset-row').forEach(function (row) {
            row.style.display = !q || row.dataset.search.includes(q) ? '' : 'none';
        });
    });

    /* Pilih / Batal Semua */
    var allSelected = false;
    document.getElementById('pmSelectAll').addEventListener('click', function () {
        allSelected = !allSelected;
        document.querySelectorAll('#pmPickerList .aset-row').forEach(function (row) {
            if (row.style.display === 'none') return;
            var cb = row.querySelector('.pm-cb');
            cb.checked = allSelected;
            row.classList.toggle('selected', allSelected);
        });
        this.textContent = allSelected ? 'Batal Semua' : 'Pilih Semua';
        updateCount();
    });

    /* Validasi submit */
    document.getElementById('formEditPeminjaman').addEventListener('submit', function (e) {
        if (document.querySelectorAll('.pm-cb:checked').length === 0) {
            e.preventDefault();
            document.getElementById('pmWarn').classList.add('show');
            document.getElementById('pmPickerList').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
</script>
@endsection