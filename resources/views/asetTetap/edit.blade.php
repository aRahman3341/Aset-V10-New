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

/* ── Photo ── */
.photo-upload-zone{border:2px dashed rgba(30,58,95,.25);border-radius:12px;padding:22px 20px;text-align:center;cursor:pointer;transition:all .2s;background:#fafbfd}
.photo-upload-zone:hover,.photo-upload-zone.dragover{border-color:#2d5a8e;background:#eef2f8}
.photo-upload-zone i{font-size:1.8rem;color:#2d5a8e;display:block;margin-bottom:6px}
.photo-upload-zone .upload-title{font-size:.82rem;font-weight:700;color:#1e3a5f}
.photo-upload-zone .upload-sub{font-size:.7rem;color:#8a96a3;margin-top:3px}

.photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px;margin-top:12px}
.photo-thumb{position:relative;border-radius:10px;overflow:hidden;aspect-ratio:1;background:#f4f6fb;border:2px solid #dee2e6;cursor:pointer;transition:border-color .15s}
.photo-thumb.selected{border-color:#dc3545;box-shadow:0 0 0 3px rgba(220,38,38,.2)}
.photo-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.photo-thumb .thumb-check{position:absolute;top:5px;left:5px;width:20px;height:20px;background:#dc3545;border-radius:50%;display:none;align-items:center;justify-content:center;color:#fff;font-size:.65rem}
.photo-thumb.selected .thumb-check{display:flex}
.photo-thumb .thumb-overlay{position:absolute;inset:0;background:rgba(220,38,38,.15);display:none;pointer-events:none}
.photo-thumb.selected .thumb-overlay{display:block}
.photo-thumb .thumb-badge{position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.5);color:#fff;font-size:.6rem;text-align:center;padding:2px 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

.photo-actions{display:flex;align-items:center;gap:8px;margin-top:10px;flex-wrap:wrap}
.btn-delete-selected{padding:6px 16px;background:#dc3545;color:#fff;border:none;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;display:none;align-items:center;gap:5px;transition:background .15s}
.btn-delete-selected:hover{background:#b91c1c}
.btn-delete-selected i{font-size:.8rem}
.photo-select-hint{font-size:.74rem;color:#8a96a3}
.photo-count-badge{display:inline-flex;align-items:center;gap:5px;background:rgba(30,58,95,.08);color:#1e3a5f;border-radius:20px;padding:3px 10px;font-size:.74rem;font-weight:700}
.new-photo-thumb{border-color:#4154f1 !important}
.new-photo-thumb .thumb-badge{background:rgba(65,84,241,.7) !important}
</style>

@php
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

    $existingPhotoCount = $photos->count();
    $slotsRemaining = max(0, 5 - $existingPhotoCount);
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
<form action="{{ route('asetTetap.update', $item->id) }}" method="POST" id="formEdit" enctype="multipart/form-data">
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

{{-- ══ 6. FOTO DOKUMENTASI ══ --}}
<div class="section-header mb-3">
    <div class="section-number">6</div>
    <i class="bi bi-images"></i> Foto Dokumentasi
</div>
<div class="mb-4">

    {{-- Info kuota --}}
    <div style="background:#eef2f8;border:1px solid rgba(30,58,95,.12);border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:.78rem;color:#1e3a5f;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-info-circle-fill"></i>
        Foto tersimpan: <strong>{{ $existingPhotoCount }}</strong> / 5 &nbsp;·&nbsp;
        Slot tersisa: <strong>{{ $slotsRemaining }}</strong>
        @if($existingPhotoCount > 0)
            &nbsp;·&nbsp; <span style="color:#dc3545;font-weight:700;">Klik foto untuk memilih → Hapus Foto Terpilih</span>
        @endif
    </div>

    {{-- Grid foto yang sudah ada --}}
    @if($photos->count() > 0)
    <div style="margin-bottom:14px;">
        <div class="form-label-custom mb-2">Foto Tersimpan</div>
        <div class="photo-grid" id="existingGrid">
            @foreach($photos as $photo)
            <div class="photo-thumb"
                 data-photo-id="{{ $photo->id }}"
                 onclick="toggleSelectPhoto(this)"
                 title="{{ $photo->original_name }}">
                <img src="{{ asset('assets/upload_asset_tetap/' . $photo->filename) }}"
                     alt="{{ $photo->original_name }}"
                     loading="lazy">
                <div class="thumb-check"><i class="bi bi-x-lg"></i></div>
                <div class="thumb-overlay"></div>
                <div class="thumb-badge">{{ \Illuminate\Support\Str::limit($photo->original_name, 18) }}</div>
            </div>
            @endforeach
        </div>

        <div class="photo-actions">
            <button type="button" id="btnDeleteSelected"
                    class="btn-delete-selected"
                    onclick="deleteSelectedPhotos()">
                <i class="bi bi-trash"></i>
                Hapus Foto Terpilih (<span id="selectedCount">0</span>)
            </button>
            <span class="photo-select-hint" id="selectHint">
                <i class="bi bi-hand-index-thumb me-1"></i>Klik foto untuk memilih yang akan dihapus
            </span>
        </div>
    </div>
    @endif

    {{-- Upload foto baru --}}
    @if($slotsRemaining > 0)
    <div class="form-label-custom mb-2">Tambah Foto Baru</div>
    <div class="photo-upload-zone" id="uploadZone" onclick="document.getElementById('photoInput').click()">
        <i class="bi bi-cloud-upload"></i>
        <div class="upload-title">Klik atau seret foto ke sini</div>
        <div class="upload-sub">
            Maks. <strong>{{ $slotsRemaining }} foto</strong> lagi ·
            Format: JPG, JPEG, PNG, WEBP · Maks. <strong>15 MB</strong> per foto
        </div>
    </div>
    <input type="file" id="photoInput" name="photos[]" multiple
           accept=".jpg,.jpeg,.png,.webp" style="display:none">
    <div id="photoError" class="text-danger mt-2" style="font-size:.8rem;display:none"></div>
    <div id="newPhotoGrid" class="photo-grid" style="margin-top:10px;"></div>
    <div id="photoCountWrap" style="display:none;margin-top:6px;">
        <span class="photo-count-badge">
            <i class="bi bi-plus-circle"></i>
            <span id="photoCount">0</span> foto baru akan ditambahkan
        </span>
    </div>
    @else
    <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:10px 14px;font-size:.78rem;color:#7b5800;">
        <i class="bi bi-exclamation-triangle me-1" style="color:#f59e0b;"></i>
        Kuota foto sudah penuh (5/5). Hapus foto yang ada untuk menambah yang baru.
    </div>
    @endif

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

{{-- SweetAlert untuk konfirmasi hapus foto --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
// ══ FOTO YANG SUDAH ADA: pilih & hapus ══
const selectedPhotoIds = new Set();
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function toggleSelectPhoto(el) {
    const id = el.dataset.photoId;
    if (selectedPhotoIds.has(id)) {
        selectedPhotoIds.delete(id);
        el.classList.remove('selected');
    } else {
        selectedPhotoIds.add(id);
        el.classList.add('selected');
    }
    updateDeleteBtn();
}

function updateDeleteBtn() {
    const btn   = document.getElementById('btnDeleteSelected');
    const count = document.getElementById('selectedCount');
    const hint  = document.getElementById('selectHint');
    if (!btn) return;
    const n = selectedPhotoIds.size;
    if (count) count.textContent = n;
    btn.style.display = n > 0 ? 'inline-flex' : 'none';
    if (hint) hint.style.display = n > 0 ? 'none' : 'inline';
}

function deleteSelectedPhotos() {
    if (selectedPhotoIds.size === 0) return;
    const ids = Array.from(selectedPhotoIds);
    Swal.fire({
        title: 'Hapus ' + ids.length + ' Foto?',
        text: 'Foto yang dihapus tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;

        // Hapus satu per satu via fetch
        const deletePromises = ids.map(photoId =>
            fetch(`/asetTetap/photo/${photoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            }).then(res => res.json())
        );

        Promise.all(deletePromises).then(results => {
            const allOk = results.every(r => r.success);
            ids.forEach(photoId => {
                const el = document.querySelector(`[data-photo-id="${photoId}"]`);
                if (el) el.remove();
                selectedPhotoIds.delete(photoId);
            });
            updateDeleteBtn();

            // Update kuota info
            const remaining = document.querySelectorAll('#existingGrid .photo-thumb').length;
            if (remaining === 0) {
                const existingSection = document.querySelector('#existingGrid')?.parentElement;
                if (existingSection) existingSection.style.display = 'none';
            }

            if (allOk) {
                Swal.fire({ icon:'success', title:'Berhasil', text:'Foto berhasil dihapus.', timer:1800, showConfirmButton:false });
            } else {
                Swal.fire({ icon:'warning', title:'Sebagian Gagal', text:'Beberapa foto gagal dihapus.' });
            }
        }).catch(() => {
            Swal.fire({ icon:'error', title:'Error', text:'Gagal menghapus foto. Coba lagi.' });
        });
    });
}

// ══ FOTO BARU: upload preview ══
@if($slotsRemaining > 0)
(function () {
    const MAX_SLOTS   = {{ $slotsRemaining }};
    const MAX_SIZE_B  = 15 * 1024 * 1024;
    const ALLOWED     = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    const input       = document.getElementById('photoInput');
    const zone        = document.getElementById('uploadZone');
    const grid        = document.getElementById('newPhotoGrid');
    const errBox      = document.getElementById('photoError');
    const countWrap   = document.getElementById('photoCountWrap');
    const countEl     = document.getElementById('photoCount');

    let dt = new DataTransfer();

    function showError(msg) {
        errBox.textContent = msg;
        errBox.style.display = 'block';
        setTimeout(() => errBox.style.display = 'none', 4000);
    }

    function renderPreviews() {
        grid.innerHTML = '';
        const files = dt.files;
        countEl.textContent = files.length;
        countWrap.style.display = files.length > 0 ? 'block' : 'none';

        Array.from(files).forEach((file, idx) => {
            const wrap = document.createElement('div');
            wrap.className = 'photo-thumb new-photo-thumb';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;

            const badge = document.createElement('div');
            badge.className = 'thumb-badge';
            badge.textContent = file.name.length > 18 ? file.name.substring(0, 15) + '...' : file.name;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'thumb-check';
            btn.style.cssText = 'position:absolute;top:5px;right:5px;width:22px;height:22px;background:rgba(220,38,38,.85);border:none;border-radius:50%;color:#fff;font-size:.65rem;display:flex;align-items:center;justify-content:center;cursor:pointer;';
            btn.innerHTML = '<i class="bi bi-x"></i>';
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const newDt = new DataTransfer();
                Array.from(dt.files).forEach((f, i) => { if (i !== idx) newDt.items.add(f); });
                dt = newDt;
                input.files = dt.files;
                renderPreviews();
            });

            wrap.appendChild(img);
            wrap.appendChild(btn);
            wrap.appendChild(badge);
            grid.appendChild(wrap);
        });

        input.files = dt.files;
    }

    function addFiles(newFiles) {
        Array.from(newFiles).forEach(file => {
            if (dt.files.length >= MAX_SLOTS) {
                showError('Slot foto tersisa hanya ' + MAX_SLOTS + '.');
                return;
            }
            if (!ALLOWED.includes(file.type)) {
                showError('Format tidak didukung: ' + file.name);
                return;
            }
            if (file.size > MAX_SIZE_B) {
                showError('Foto "' + file.name + '" melebihi 15 MB.');
                return;
            }
            dt.items.add(file);
        });
        renderPreviews();
    }

    input.addEventListener('change', function () {
        addFiles(this.files);
        this.value = '';
    });

    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        addFiles(e.dataTransfer.files);
    });
})();
@endif

// ══ Validasi form ══
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
</script>
@endsection