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

<div class="pagetitle">
    <h1 style="font-size:1.2rem;font-weight:800;color:#1e3a5f;">
        <i class="bi bi-plus-circle me-2"></i>Tambah Aset Tetap
    </h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('asetTetap.index') }}">Aset Tetap</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol></nav>
</div>

<div class="create-card">
<form action="{{ route('asetTetap.store') }}" method="POST" id="formCreate">
@csrf

{{-- ══ 1. IDENTITAS BARANG ══ --}}
<div class="section-header mb-3"><div class="section-number">1</div><i class="bi bi-tag-fill"></i> Identitas Barang</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Kode Barang <span class="req">*</span></label>
        <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: 3010110005" required>
        <div class="invalid-feedback">Kode Barang wajib diisi.</div>
    </div>
    <div class="col-md-2">
        <label class="form-label-custom">NUP <span class="req">*</span></label>
        <input type="text" name="nup" class="form-control" placeholder="No Urut" required>
        <div class="invalid-feedback">NUP wajib diisi.</div>
    </div>
    <div class="col-md-7">
        <label class="form-label-custom">Nama Barang <span class="req">*</span></label>
        <input type="text" name="nama_barang" class="form-control" placeholder="Masukkan nama barang" required>
        <div class="invalid-feedback">Nama Barang wajib diisi.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Merk</label>
        <input type="text" name="merk" class="form-control" placeholder="Contoh: Honda, Asus">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Tipe</label>
        <input type="text" name="tipe" class="form-control" placeholder="Contoh: Livina 1.5 MT">
    </div>
</div>

{{-- ══ 2. KLASIFIKASI BMN ══ --}}
<div class="section-header mb-3"><div class="section-number">2</div><i class="bi bi-grid-fill"></i> Klasifikasi BMN</div>
<div class="row g-3 mb-4">
    <div class="col-md-5">
        <label class="form-label-custom">Jenis BMN <span class="req">*</span></label>
        <input type="text" name="jenis_bmn" class="form-control" placeholder="Contoh: ALAT BESAR" required>
        <div class="invalid-feedback">Jenis BMN wajib diisi.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Kondisi</label>
        <select name="kondisi" class="form-select">
            <option value="Baik" selected>Baik</option>
            <option value="Rusak Ringan">Rusak Ringan</option>
            <option value="Rusak Berat">Rusak Berat</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Status BMN</label>
        <select name="status_bmn" class="form-select">
            <option value="Aktif" selected>Aktif</option>
            <option value="Tidak Aktif">Tidak Aktif</option>
        </select>
    </div>
</div>

{{-- ══ 3. NILAI & WAKTU ══ --}}
<div class="section-header mb-3"><div class="section-number">3</div><i class="bi bi-cash-stack"></i> Nilai & Waktu</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Perolehan Pertama (Rp)</label>
        <input type="number" name="nilai_perolehan_pertama" class="form-control" placeholder="0" min="0">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Perolehan (Rp)</label>
        <input type="number" name="nilai_perolehan" class="form-control" placeholder="0" min="0">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Penyusutan (Rp)</label>
        <input type="number" name="nilai_penyusutan" class="form-control" placeholder="0" min="0">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Buku (Rp)</label>
        <input type="number" name="nilai_buku" class="form-control" placeholder="0" min="0">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tanggal Perolehan</label>
        <input type="date" name="tanggal_perolehan" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tanggal Buku Pertama</label>
        <input type="date" name="tanggal_buku_pertama" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Jumlah Foto</label>
        <input type="number" name="jumlah_foto" class="form-control" min="0" value="0">
    </div>
</div>

{{-- ══ 4. DOKUMEN PSP ══ --}}
<div class="section-header mb-3"><div class="section-number">4</div><i class="bi bi-file-earmark-text-fill"></i> Dokumen PSP</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label-custom">No PSP</label>
        <input type="text" name="no_psp" class="form-control" placeholder="Nomor PSP">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Tanggal PSP</label>
        <input type="date" name="tanggal_psp" class="form-control">
    </div>
</div>

{{-- ══ 5. DATA SATKER ══ --}}
<div class="section-header mb-3"><div class="section-number">5</div><i class="bi bi-building"></i> Data Satuan Kerja</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label-custom">Kode Satker</label>
        <input type="text" name="kode_satker" class="form-control" placeholder="Kode Satuan Kerja">
    </div>
    <div class="col-md-8">
        <label class="form-label-custom">Nama Satker</label>
        <input type="text" name="nama_satker" class="form-control" placeholder="Nama Satuan Kerja">
    </div>
    <div class="col-12">
        <label class="form-label-custom">Alamat</label>
        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap aset"></textarea>
    </div>
</div>

{{-- ══ TOMBOL ══ --}}
<div class="text-center pt-2 border-top mt-2">
    <button type="submit" class="btn-simpan me-2">
        <i class="bi bi-check-circle-fill me-1"></i> Simpan Aset
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
    document.getElementById('formCreate').addEventListener('submit', function (e) {
        var valid = true;
        this.querySelectorAll('[required]').forEach(function (el) {
            if (!el.value || !el.value.trim()) { el.classList.add('is-invalid'); valid = false; }
            else { el.classList.remove('is-invalid'); }
        });
        if (!valid) e.preventDefault();
    });
    document.getElementById('formCreate').querySelectorAll('[required]').forEach(function (el) {
        el.addEventListener('input',  function () { this.classList.remove('is-invalid'); });
        el.addEventListener('change', function () { this.classList.remove('is-invalid'); });
    });
});
</script>
@endsection