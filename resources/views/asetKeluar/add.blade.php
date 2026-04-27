@extends('layouts.app')
@section('title') Tambah Aset Keluar - Monitoring Aset @endsection
@section('content')

<main id="main" class="main">
<style>
/* ── Page Title ── */
.ak-pagetitle { display:flex; align-items:center; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid rgba(30,58,95,0.08); }
.ak-pagetitle-left { display:flex; align-items:center; gap:14px; }
.ak-pagetitle-icon { width:46px; height:46px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; box-shadow:0 4px 12px rgba(30,58,95,0.25); flex-shrink:0; }
.ak-pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 3px; }
.ak-pagetitle .breadcrumb { font-size:0.75rem; margin:0; padding:0; background:transparent; }
.ak-pagetitle .breadcrumb a { color:#2d5a8e; text-decoration:none; }
.ak-pagetitle .breadcrumb-item.active { color:#8a96a3; }

/* ── Card ── */
.ak-card { background:#fff; border-radius:16px; border:1px solid rgba(30,58,95,0.08); box-shadow:0 2px 16px rgba(30,58,95,0.07); overflow:hidden; }
.ak-card-header { padding:16px 24px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); display:flex; align-items:center; gap:10px; color:#fff; }
.ak-card-header span { font-size:0.95rem; font-weight:700; }
.ak-card-body { padding:28px; }

/* ── Form elements ── */
.ak-label { display:block; font-size:0.78rem; font-weight:700; color:#4a5a6e; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px; }
.req { color:#dc3545; }
.ak-input { width:100%; padding:10px 14px; font-size:0.85rem; color:#1e3a5f; background:#f8fafd; border:1.5px solid #dee2e6; border-radius:10px; outline:none; transition:border-color .15s; }
.ak-input:focus { background:#fff; border-color:#2d5a8e; box-shadow:0 0 0 3px rgba(45,90,142,0.10); }
.ak-field { margin-bottom:18px; }
.ak-field-error { display:block; font-size:0.75rem; color:#dc3545; margin-top:4px; }
.ak-divider { display:flex; align-items:center; gap:10px; margin:4px 0 18px; font-size:0.75rem; font-weight:700; color:#8a96a3; text-transform:uppercase; letter-spacing:0.5px; }
.ak-divider::before,.ak-divider::after { content:''; flex:1; height:1px; background:rgba(30,58,95,0.08); }

/* ── Aset Picker ── */
.aset-picker-wrap { border:1.5px solid #dee2e6; border-radius:12px; overflow:hidden; }
.aset-picker-search { display:flex; align-items:center; background:#f8fafd; border-bottom:1.5px solid #dee2e6; padding:0 14px; gap:8px; }
.aset-picker-search i { color:#8a96a3; font-size:0.9rem; flex-shrink:0; }
.aset-picker-search input { flex:1; border:none; background:transparent; padding:10px 0; font-size:0.84rem; color:#1e3a5f; outline:none; }
.aset-picker-list { max-height:320px; overflow-y:auto; }
.aset-picker-list::-webkit-scrollbar { width:5px; }
.aset-picker-list::-webkit-scrollbar-track { background:#f1f1f1; }
.aset-picker-list::-webkit-scrollbar-thumb { background:#c5cfe0; border-radius:3px; }

/* ── Aset Row ── */
.aset-row { display:flex; align-items:center; gap:12px; padding:11px 16px; border-bottom:1px solid rgba(30,58,95,0.05); cursor:pointer; transition:background .12s; user-select:none; }
.aset-row:last-child { border-bottom:none; }
.aset-row:hover { background:#f0f5ff; }
.aset-row.selected { background:#eef3ff; }

/* Custom checkbox */
.aset-checkbox { width:18px; height:18px; border-radius:5px; border:2px solid #c5cfe0; background:#fff; flex-shrink:0; display:flex; align-items:center; justify-content:center; transition:all .15s; }
.aset-row.selected .aset-checkbox { background:#2d5a8e; border-color:#2d5a8e; }
.aset-checkbox i { font-size:0.65rem; color:#fff; display:none; }
.aset-row.selected .aset-checkbox i { display:block; }

.aset-info { flex:1; min-width:0; }
.aset-name { font-size:0.83rem; font-weight:600; color:#1e3a5f; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.aset-meta { display:flex; align-items:center; gap:6px; margin-top:2px; }
.aset-code { font-size:0.7rem; background:rgba(65,84,241,0.08); color:#4154f1; padding:1px 6px; border-radius:4px; font-family:'Courier New',monospace; font-weight:600; }
.aset-nup  { font-size:0.7rem; color:#8a96a3; }

.aset-empty { padding:28px; text-align:center; color:#8a96a3; font-size:0.84rem; }
.aset-empty i { font-size:1.8rem; display:block; margin-bottom:6px; }

/* ── Terpilih counter ── */
.aset-footer { padding:10px 16px; background:#f8fafd; border-top:1.5px solid #dee2e6; display:flex; align-items:center; justify-content:space-between; }
.aset-footer-count { font-size:0.78rem; color:#5a6a7e; }
.aset-footer-count strong { color:#1e3a5f; font-weight:700; }
.aset-select-all { font-size:0.75rem; color:#2d5a8e; font-weight:600; cursor:pointer; background:none; border:none; padding:0; }
.aset-select-all:hover { text-decoration:underline; }

/* ── Buttons ── */
.ak-btn-submit { width:100%; padding:12px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); color:#fff; border:none; border-radius:10px; font-size:0.9rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:all .18s; }
.ak-btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(30,58,95,0.35); }
.ak-btn-back { width:100%; padding:11px; background:#f4f6fb; color:#5a6a7e; border:1.5px solid #dee2e6; border-radius:10px; font-size:0.88rem; font-weight:600; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .15s; }
.ak-btn-back:hover { background:#e8ecf5; color:#1e3a5f; text-decoration:none; }

/* ── Validation warning ── */
.aset-warn { display:none; font-size:0.75rem; color:#dc3545; margin-top:6px; }
.aset-warn.show { display:block; }
</style>

<div class="ak-pagetitle">
    <div class="ak-pagetitle-left">
        <div class="ak-pagetitle-icon"><i class="bi bi-box-arrow-right"></i></div>
        <div>
            <h1>Tambah Aset Keluar</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('asetkeluar.index') }}">Aset Keluar</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol></nav>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9 col-md-11">
        <div class="ak-card">
            <div class="ak-card-header">
                <i class="bi bi-file-earmark-plus"></i>
                <span>Form Aset Keluar</span>
            </div>
            <div class="ak-card-body">

                @if(session('error'))
                    <div class="alert alert-danger rounded-3 mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('asetkeluar.store') }}" id="formAsetKeluar">
                    @csrf

                    {{-- ── Informasi Dokumen ── --}}
                    <div class="ak-divider">Informasi Dokumen</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="ak-field">
                                <label class="ak-label">Nomor Dokumen <span class="req">*</span></label>
                                <input type="text" name="nomor" class="ak-input"
                                       placeholder="Contoh: 01/BA/SATKER/CB36/2025"
                                       value="{{ old('nomor') }}" required>
                                @error('nomor')<span class="ak-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ak-field">
                                <label class="ak-label">Diserahkan Kepada <span class="req">*</span></label>
                                <input type="text" name="kepada" class="ak-input"
                                       placeholder="Nama penerima"
                                       value="{{ old('kepada') }}" required>
                                @error('kepada')<span class="ak-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- ── Pihak Terlibat ── --}}
                    <div class="ak-divider">Pihak yang Terlibat</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="ak-field">
                                <label class="ak-label">Nama Pihak Kesatu <span class="req">*</span></label>
                                <input type="text" name="pihakSatu" class="ak-input"
                                       placeholder="Nama pihak kesatu" value="{{ old('pihakSatu') }}" required>
                                @error('pihakSatu')<span class="ak-field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="ak-field">
                                <label class="ak-label">NIP Pihak Kesatu</label>
                                <input type="text" name="nipSatu" class="ak-input"
                                       placeholder="NIP pihak kesatu" value="{{ old('nipSatu') }}">
                            </div>
                            <div class="ak-field">
                                <label class="ak-label">Jabatan Pihak Kesatu</label>
                                <input type="text" name="jabatanSatu" class="ak-input"
                                       placeholder="Jabatan pihak kesatu" value="{{ old('jabatanSatu') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ak-field">
                                <label class="ak-label">Nama Pihak Kedua <span class="req">*</span></label>
                                <input type="text" name="pihakDua" class="ak-input"
                                       placeholder="Nama pihak kedua" value="{{ old('pihakDua') }}" required>
                                @error('pihakDua')<span class="ak-field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="ak-field">
                                <label class="ak-label">NIP Pihak Kedua</label>
                                <input type="text" name="nipDua" class="ak-input"
                                       placeholder="NIP pihak kedua" value="{{ old('nipDua') }}">
                            </div>
                            <div class="ak-field">
                                <label class="ak-label">Jabatan Pihak Kedua</label>
                                <input type="text" name="jabatanDua" class="ak-input"
                                       placeholder="Jabatan pihak kedua" value="{{ old('jabatanDua') }}">
                            </div>
                        </div>
                    </div>

                    {{-- ── Daftar Aset (Checkbox Picker) ── --}}
                    <div class="ak-divider">Daftar Aset yang Keluar</div>
                    <div class="ak-field">
                        <label class="ak-label">Pilih Aset <span class="req">*</span> <span style="color:#8a96a3;font-weight:400;text-transform:none;font-size:0.72rem;">(klik untuk memilih)</span></label>

                        <div class="aset-picker-wrap">
                            {{-- Search atas --}}
                            <div class="aset-picker-search">
                                <i class="bi bi-search"></i>
                                <input type="text" id="asetSearch" placeholder="Cari nama atau kode aset...">
                            </div>

                            {{-- List aset ── hidden checkboxes + visual row ── --}}
                            <div class="aset-picker-list" id="asetPickerList">
                                @forelse ($items as $item)
                                    @php
                                        $namaBarang = $item->{'Nama Barang'} ?? '-';
                                        $kodeBarang = $item->{'Kode Barang'} ?? '-';
                                        $nup        = $item->nup ?? '-';
                                    @endphp
                                    <label class="aset-row" data-search="{{ strtolower($namaBarang . ' ' . $kodeBarang . ' ' . $nup) }}">
                                        <input type="checkbox" name="name[]" value="{{ $item->id }}"
                                               class="aset-cb" style="display:none">
                                        <div class="aset-checkbox"><i class="bi bi-check-lg"></i></div>
                                        <div class="aset-info">
                                            <div class="aset-name">{{ $namaBarang }}</div>
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

                            {{-- Footer counter ── --}}
                            <div class="aset-footer">
                                <span class="aset-footer-count">
                                    Dipilih: <strong id="selectedCount">0</strong> aset
                                </span>
                                <button type="button" class="aset-select-all" id="btnSelectAll">
                                    Pilih Semua
                                </button>
                            </div>
                        </div>
                        <span class="aset-warn" id="asetWarn">
                            <i class="bi bi-exclamation-circle me-1"></i>Pilih minimal satu aset.
                        </span>
                        @error('name')<span class="ak-field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- ── Submit ── --}}
                    <div class="row g-3 mt-2">
                        <div class="col-md-8">
                            <button type="submit" class="ak-btn-submit" id="btnSubmit">
                                <i class="bi bi-check-circle-fill"></i> Simpan Aset Keluar
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('asetkeluar.index') }}" class="ak-btn-back">
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

<script>
(function () {
    'use strict';

    /* ── Toggle row selection ── */
    document.querySelectorAll('.aset-row').forEach(function (row) {
        row.addEventListener('click', function () {
            const cb = this.querySelector('.aset-cb');
            cb.checked = !cb.checked;
            this.classList.toggle('selected', cb.checked);
            updateCount();
        });
    });

    /* ── Count terpilih ── */
    function updateCount() {
        const n = document.querySelectorAll('.aset-cb:checked').length;
        document.getElementById('selectedCount').textContent = n;
        if (n > 0) document.getElementById('asetWarn').classList.remove('show');
    }

    /* ── Search / filter ── */
    document.getElementById('asetSearch').addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('.aset-row').forEach(function (row) {
            const match = !q || row.dataset.search.includes(q);
            row.style.display = match ? '' : 'none';
        });
    });

    /* ── Pilih Semua ── */
    var allSelected = false;
    document.getElementById('btnSelectAll').addEventListener('click', function () {
        allSelected = !allSelected;
        document.querySelectorAll('.aset-row').forEach(function (row) {
            if (row.style.display === 'none') return;
            const cb = row.querySelector('.aset-cb');
            cb.checked = allSelected;
            row.classList.toggle('selected', allSelected);
        });
        this.textContent = allSelected ? 'Batal Semua' : 'Pilih Semua';
        updateCount();
    });

    /* ── Validasi sebelum submit ── */
    document.getElementById('formAsetKeluar').addEventListener('submit', function (e) {
        const checked = document.querySelectorAll('.aset-cb:checked').length;
        if (checked === 0) {
            e.preventDefault();
            document.getElementById('asetWarn').classList.add('show');
            document.getElementById('asetPickerList').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
</script>
@endsection