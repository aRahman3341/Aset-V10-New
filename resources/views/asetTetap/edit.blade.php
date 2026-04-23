@extends('layouts.app')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<main id="main" class="main">
<style>
.form-control,.form-select{background-image:none!important;padding-right:12px!important}
.form-control:valid,.form-select:valid,.form-control.is-valid,.form-select.is-valid{border-color:#dee2e6!important;background-image:none!important;padding-right:12px!important}
.form-control:focus,.form-select:focus{border-color:#86b7fe;box-shadow:0 0 0 .2rem rgba(13,110,253,.15)}
.form-control.is-invalid,.form-select.is-invalid{border-color:#dc3545!important;background-image:none!important}
.section-header{display:flex;align-items:center;gap:8px;padding:8px 14px;background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;border-radius:8px;font-size:.82rem;font-weight:700;letter-spacing:.3px;margin-bottom:4px}
.section-number{width:22px;height:22px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800;flex-shrink:0}
.form-label-custom{font-size:.78rem;font-weight:700;color:#4a5a6e;margin-bottom:4px;display:block}
.req{color:#dc3545}
.form-control,.form-select{font-size:.85rem;border-radius:8px;border:1.5px solid #dee2e6;transition:border-color .15s,box-shadow .15s}
.create-card{background:#fff;border-radius:14px;border:1px solid rgba(30,58,95,.08);box-shadow:0 2px 14px rgba(30,58,95,.06);padding:24px}
.btn-simpan{padding:10px 36px;background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;border:none;border-radius:10px;font-weight:700;font-size:.9rem;box-shadow:0 4px 12px rgba(30,58,95,.25);transition:all .18s;cursor:pointer}
.btn-simpan:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(30,58,95,.35);color:#fff}
.btn-batal{padding:10px 36px;background:#f4f6fb;color:#5a6a7e;border:1.5px solid #dee2e6;border-radius:10px;font-weight:600;font-size:.9rem;text-decoration:none;transition:all .18s;display:inline-block}
.btn-batal:hover{background:#e8ecf5;color:#3d5170}
</style>

@php
    /*
     * Helper: baca properti yang mungkin berasal dari Eloquent (accessor)
     * ATAU dari DB::table (stdClass dengan kolom nama spasi).
     * Urutan: cek accessor-name dulu, lalu fallback ke kolom DB spasi.
     */
    $kodeBarang       = old('code',                $item->code                ?? $item->{'Kode Barang'}             ?? '');
    $nup              = old('nup',                 $item->nup                 ?? '');
    $namaBarang       = old('name',                $item->name                ?? $item->{'Nama Barang'}             ?? '');
    $merk             = old('name_fix',            $item->merk                ?? $item->name_fix                    ?? '');
    $noSeri           = old('no_seri',             $item->no_seri             ?? '');
    $jenisBmn         = old('jenis_bmn',           $item->jenis_bmn           ?? $item->{'Jenis BMN'}               ?? '');
    $tipeAset         = old('type',                $item->tipe                ?? $item->type                        ?? 'Tetap');
    $kondisi          = old('condition',           $item->kondisi             ?? $item->condition                   ?? 'Baik');
    $statusPenggunaan = old('status',              $item->status              ?? 'Tidak Dipakai');
    $statusBmn        = old('status_bmn',          $item->status_bmn          ?? $item->{'Status BMN'}              ?? 'Aktif');
    $satuan           = old('satuan',              $item->satuan              ?? '');
    $nilaiAwal        = old('nilai',               $item->nilai               ?? $item->{'Nilai Perolehan Pertama'} ?? '');
    $nilaiPerolehan   = old('nilai_perolehan',     $item->nilai_perolehan     ?? $item->{'Nilai Perolehan'}         ?? '');
    $nilaiPenyusutan  = old('nilai_penyusutan',    $item->nilai_penyusutan    ?? $item->{'Nilai Penyusutan'}        ?? '');
    $nilaiBuku        = old('nilai_buku',          $item->nilai_buku          ?? $item->{'Nilai Buku'}              ?? '');
    $years            = old('years',               $item->years               ?? '');

    // Tanggal — parse aman
    $tglPerolehan = '';
    $rawTglP = $item->tanggal_perolehan ?? $item->{'Tanggal Perolehan'} ?? null;
    if ($rawTglP) { try { $tglPerolehan = \Carbon\Carbon::parse($rawTglP)->format('Y-m-d'); } catch(\Exception $e){} }
    $tglPerolehan = old('tanggal_perolehan', $tglPerolehan);

    $tglBukuPertama = '';
    $rawTglB = $item->tanggal_buku_pertama ?? $item->{'Tanggal Buku Pertama'} ?? null;
    if ($rawTglB) { try { $tglBukuPertama = \Carbon\Carbon::parse($rawTglB)->format('Y-m-d'); } catch(\Exception $e){} }
    $tglBukuPertama = old('tanggal_buku_pertama', $tglBukuPertama);

    $lifeTime   = old('life_time',       $item->life_time   ?? $item->umur_aset ?? '');
    $noPsp      = old('no_psp',          $item->no_psp      ?? $item->{'No PSP'} ?? '');

    $tglPsp = '';
    $rawTglPsp = $item->tanggal_psp ?? $item->{'Tanggal PSP'} ?? null;
    if ($rawTglPsp) { try { $tglPsp = \Carbon\Carbon::parse($rawTglPsp)->format('Y-m-d'); } catch(\Exception $e){} }
    $tglPsp = old('tanggal_psp', $tglPsp);

    $kodeSatker = old('kode_satker', $item->kode_satker ?? '');
    $namaSatker = old('nama_satker', $item->nama_satker ?? '');
    $alamat     = old('alamat',      $item->alamat      ?? '');
    $kabKota    = old('kab_kota',    $item->kab_kota    ?? '');
    $provinsi   = old('provinsi',    $item->provinsi    ?? '');
@endphp

<div class="pagetitle">
    <h1 style="font-size:1.2rem;font-weight:800;color:#1e3a5f;">
        <i class="bi bi-pencil-square me-2"></i>Edit Aset Tetap
    </h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('asetTetap.index') }}">Aset Tetap</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol></nav>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>Terdapat kesalahan:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="create-card">
<form action="{{ route('asetTetap.update', $item->id) }}" method="POST" id="formEdit">
@csrf
@method('PUT')

{{-- ══ 1. IDENTITAS BARANG ══ --}}
<div class="section-header mb-3">
    <div class="section-number">1</div>
    <i class="bi bi-tag-fill"></i> Identitas Barang
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Kode Barang <span class="req">*</span></label>
        <input type="text" name="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ $kodeBarang }}" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label-custom">NUP <span class="req">*</span></label>
        <input type="text" name="nup"
               class="form-control @error('nup') is-invalid @enderror"
               value="{{ $nup }}" required>
        @error('nup')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-7">
        <label class="form-label-custom">Nama Barang <span class="req">*</span></label>
        <input type="text" name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ $namaBarang }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Merk / Uraian</label>
        <input type="text" name="name_fix" class="form-control"
               value="{{ $merk }}" placeholder="Contoh: Honda, Asus">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">No. Seri</label>
        <input type="text" name="no_seri" class="form-control"
               value="{{ $noSeri }}" placeholder="Nomor seri barang">
    </div>
</div>

{{-- ══ 2. KLASIFIKASI BMN ══ --}}
<div class="section-header mb-3">
    <div class="section-number">2</div>
    <i class="bi bi-grid-fill"></i> Klasifikasi BMN
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label-custom">Jenis BMN <span class="req">*</span></label>
        <input type="text" name="jenis_bmn"
               class="form-control @error('jenis_bmn') is-invalid @enderror"
               value="{{ $jenisBmn }}"
               placeholder="Contoh: ALAT BESAR" required>
        @error('jenis_bmn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tipe Aset</label>
        <select name="type" class="form-select">
            <option value="Tetap"      {{ $tipeAset == 'Tetap'      ? 'selected':'' }}>Tetap</option>
            <option value="Alat besar" {{ $tipeAset == 'Alat besar' ? 'selected':'' }}>Alat Besar</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Kondisi</label>
        <select name="condition" class="form-select">
            <option value="Baik"         {{ $kondisi == 'Baik'         ? 'selected':'' }}>Baik</option>
            <option value="Rusak Ringan" {{ $kondisi == 'Rusak Ringan' ? 'selected':'' }}>Rusak Ringan</option>
            <option value="Rusak Berat"  {{ $kondisi == 'Rusak Berat'  ? 'selected':'' }}>Rusak Berat</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Status Penggunaan</label>
        <select name="status" class="form-select">
            <option value="Tidak Dipakai" {{ $statusPenggunaan == 'Tidak Dipakai' ? 'selected':'' }}>Tidak Dipakai</option>
            <option value="Dipakai"       {{ $statusPenggunaan == 'Dipakai'       ? 'selected':'' }}>Dipakai</option>
            <option value="Maintenance"   {{ $statusPenggunaan == 'Maintenance'   ? 'selected':'' }}>Maintenance</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Status BMN</label>
        <select name="status_bmn" class="form-select">
            <option value="Aktif"       {{ $statusBmn == 'Aktif'       ? 'selected':'' }}>Aktif</option>
            <option value="Tidak Aktif" {{ $statusBmn == 'Tidak Aktif' ? 'selected':'' }}>Tidak Aktif</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Satuan</label>
        <input type="text" name="satuan" class="form-control"
               value="{{ $satuan }}" placeholder="Contoh: Unit, Buah">
    </div>
</div>

{{-- ══ 3. NILAI & WAKTU ══ --}}
<div class="section-header mb-3">
    <div class="section-number">3</div>
    <i class="bi bi-cash-stack"></i> Nilai & Waktu
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Perolehan Pertama (Rp)</label>
        <input type="number" name="nilai" class="form-control" min="0"
               value="{{ $nilaiAwal }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Perolehan (Rp)</label>
        <input type="number" name="nilai_perolehan" class="form-control" min="0"
               value="{{ $nilaiPerolehan }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Penyusutan (Rp)</label>
        <input type="number" name="nilai_penyusutan" class="form-control" min="0"
               value="{{ $nilaiPenyusutan }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Buku (Rp)</label>
        <input type="number" name="nilai_buku" class="form-control" min="0"
               value="{{ $nilaiBuku }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Tahun Perolehan</label>
        <input type="number" name="years" class="form-control"
               min="1900" max="{{ date('Y') }}" value="{{ $years }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Tanggal Perolehan</label>
        <input type="date" name="tanggal_perolehan" class="form-control"
               value="{{ $tglPerolehan }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Tanggal Buku Pertama</label>
        <input type="date" name="tanggal_buku_pertama" class="form-control"
               value="{{ $tglBukuPertama }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Umur Aset (Tahun)</label>
        <input type="number" name="life_time" class="form-control" min="0"
               value="{{ $lifeTime }}">
    </div>
</div>

{{-- ══ 4. DOKUMEN PSP ══ --}}
<div class="section-header mb-3">
    <div class="section-number">4</div>
    <i class="bi bi-file-earmark-text-fill"></i> Dokumen PSP
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label-custom">No PSP</label>
        <input type="text" name="no_psp" class="form-control"
               value="{{ $noPsp }}" placeholder="Nomor PSP">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Tanggal PSP</label>
        <input type="date" name="tanggal_psp" class="form-control"
               value="{{ $tglPsp }}">
    </div>
</div>

{{-- ══ 5. DATA SATKER ══ --}}
<div class="section-header mb-3">
    <div class="section-number">5</div>
    <i class="bi bi-building"></i> Data Satuan Kerja
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Kode Satker</label>
        <input type="text" name="kode_satker" class="form-control"
               value="{{ $kodeSatker }}" placeholder="Kode Satuan Kerja">
    </div>
    <div class="col-md-9">
        <label class="form-label-custom">Nama Satker</label>
        <input type="text" name="nama_satker" class="form-control"
               value="{{ $namaSatker }}" placeholder="Nama Satuan Kerja">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Alamat</label>
        <textarea name="alamat" class="form-control" rows="2"
                  placeholder="Alamat lengkap aset">{{ $alamat }}</textarea>
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Kab / Kota</label>
        <input type="text" name="kab_kota" class="form-control"
               value="{{ $kabKota }}" placeholder="Contoh: Kota Bandung">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Provinsi</label>
        <input type="text" name="provinsi" class="form-control"
               value="{{ $provinsi }}" placeholder="Contoh: Jawa Barat">
    </div>
</div>

{{-- ══ TOMBOL ══ --}}
<div class="text-center pt-2 border-top mt-2">
    <button type="submit" class="btn-simpan me-2">
        <i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan
    </button>
    <a href="{{ route('asetTetap.index') }}" class="btn-batal">
        <i class="bi bi-x-circle me-1"></i> Batal
    </a>
</div>
</form>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script>
$(function () {
    document.getElementById('formEdit').addEventListener('submit', function (e) {
        var valid = true;
        this.querySelectorAll('[required]').forEach(function (el) {
            if (!el.value || !el.value.trim()) { el.classList.add('is-invalid'); valid = false; }
            else { el.classList.remove('is-invalid'); }
        });
        if (!valid) e.preventDefault();
    });
    document.getElementById('formEdit').querySelectorAll('[required]').forEach(function (el) {
        el.addEventListener('input',  function () { this.classList.remove('is-invalid'); });
        el.addEventListener('change', function () { this.classList.remove('is-invalid'); });
    });
});
</script>
@endsection