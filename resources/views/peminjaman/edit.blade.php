@extends('layouts.app')
@section('title') Edit Peminjaman - Monitoring Aset @endsection
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
.pm-divider::before, .pm-divider::after { content:''; flex:1; height:1px; background:rgba(30,58,95,0.08); }
.pm-btn-submit { width:100%; padding:12px; background:linear-gradient(135deg,#c49a2a,#e8b84b); color:#fff; border:none; border-radius:10px; font-size:0.9rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 12px rgba(196,154,42,0.3); transition:all .18s; }
.pm-btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(196,154,42,0.4); }
.pm-btn-back { width:100%; padding:11px; background:#f4f6fb; color:#5a6a7e; border:1.5px solid #dee2e6; border-radius:10px; font-size:0.88rem; font-weight:600; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .15s; }
.pm-btn-back:hover { background:#e8ecf5; color:#1e3a5f; text-decoration:none; }
.select2-container--default .select2-selection--single { height:42px!important; border-radius:10px!important; border:1.5px solid #dee2e6!important; background:#f8fafd!important; display:flex; align-items:center; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:42px!important; padding-left:14px!important; font-size:0.85rem; color:#1e3a5f; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:40px!important; }
.select2-container--default.select2-container--focus .select2-selection--single { border-color:#c49a2a!important; box-shadow:0 0 0 3px rgba(196,154,42,0.12)!important; }
.select2-dropdown { border-radius:10px!important; border:1.5px solid rgba(30,58,95,0.15)!important; }
.select2-results__option { font-size:0.83rem; padding:8px 14px; }
.select2-container--default .select2-results__option--highlighted { background:#c49a2a!important; }
</style>

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
    <div class="col-lg-7 col-md-10">
        <div class="pm-card">
            <div class="pm-card-header">
                <i class="bi bi-pencil-fill"></i>
                <span>Form Edit Peminjaman</span>
            </div>
            <div class="pm-card-body">

                <form method="POST" action="{{ route('peminjaman.update', $loan->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Pilih Aset --}}
                    <div class="pm-field">
                        <label class="pm-label">Nama Barang / Aset <span class="req">*</span></label>
                        <select name="material_id" class="namebox" style="width:100%" required>
                            <option value="">-- Pilih Aset --</option>
                            @foreach ($material as $item)
                                <option value="{{ $item->id }}"
                                    {{ $loan->material_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }}
                                    @if($item->kode_barang) — {{ $item->kode_barang }} @endif
                                    (NUP: {{ $item->nup ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('material_id')
                            <span class="pm-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pm-divider">Periode Peminjaman</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="pm-field">
                                <label class="pm-label">Tanggal Pinjam <span class="req">*</span></label>
                                <div style="position:relative">
                                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#8a96a3;pointer-events:none"><i class="bi bi-calendar-event"></i></span>
                                    <input type="text" name="tgl_pinjam" id="datepicker"
                                           class="pm-input" style="padding-left:34px"
                                           placeholder="yyyy-mm-dd"
                                           value="{{ $loan->tgl_pinjam }}" required>
                                </div>
                                @error('tgl_pinjam')
                                    <span class="pm-field-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pm-field">
                                <label class="pm-label">Tanggal Kembali <span class="req">*</span></label>
                                <div style="position:relative">
                                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#8a96a3;pointer-events:none"><i class="bi bi-calendar-check"></i></span>
                                    <input type="text" name="tgl_kembali" id="datepicker1"
                                           class="pm-input" style="padding-left:34px"
                                           placeholder="yyyy-mm-dd"
                                           value="{{ $loan->tgl_kembali }}" required>
                                </div>
                                @error('tgl_kembali')
                                    <span class="pm-field-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pm-divider">Data Peminjam</div>

                    <div class="pm-field">
                        <label class="pm-label">Nama Peminjam <span class="req">*</span></label>
                        <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#8a96a3;pointer-events:none"><i class="bi bi-person"></i></span>
                            <input type="text" name="peminjam" class="pm-input" style="padding-left:34px"
                                   placeholder="Masukkan nama peminjam"
                                   value="{{ $loan->peminjam }}" required>
                        </div>
                        @error('peminjam')
                            <span class="pm-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row g-3 mt-2">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('.namebox').select2({ placeholder: '-- Pilih Aset --', allowClear: true });
    $("#datepicker").datepicker({ dateFormat: "yy-mm-dd" });
    $("#datepicker1").datepicker({ dateFormat: "yy-mm-dd" });
});
</script>
@endsection