@extends('layouts.app')
@section('content')

<main id="main" class="main">
<style>
.form-control,.form-select{background-image:none!important;padding-right:12px!important}
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

/* ── Photo Upload ── */
.photo-upload-zone{border:2px dashed rgba(30,58,95,.25);border-radius:12px;padding:28px 20px;text-align:center;cursor:pointer;transition:all .2s;background:#fafbfd;position:relative}
.photo-upload-zone:hover,.photo-upload-zone.dragover{border-color:#2d5a8e;background:#eef2f8}
.photo-upload-zone i{font-size:2.2rem;color:#2d5a8e;display:block;margin-bottom:8px}
.photo-upload-zone .upload-title{font-size:.85rem;font-weight:700;color:#1e3a5f}
.photo-upload-zone .upload-sub{font-size:.72rem;color:#8a96a3;margin-top:4px}
.photo-preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;margin-top:12px}
.photo-thumb-wrap{position:relative;border-radius:10px;overflow:hidden;aspect-ratio:1;background:#f4f6fb;border:1.5px solid #dee2e6}
.photo-thumb-wrap img{width:100%;height:100%;object-fit:cover;display:block}
.photo-thumb-remove{position:absolute;top:4px;right:4px;width:22px;height:22px;background:rgba(220,38,38,.85);border:none;border-radius:50%;color:#fff;font-size:.7rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s}
.photo-thumb-remove:hover{background:#b91c1c}
.photo-count-badge{display:inline-flex;align-items:center;gap:5px;background:rgba(30,58,95,.08);color:#1e3a5f;border-radius:20px;padding:3px 10px;font-size:.74rem;font-weight:700;margin-top:8px}
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
<form action="{{ route('asetTetap.store') }}" method="POST" id="formCreate" enctype="multipart/form-data">
@csrf

{{-- ══ 1. IDENTITAS BARANG ══ --}}
<div class="section-header mb-3">
    <div class="section-number">1</div>
    <i class="bi bi-tag-fill"></i> Identitas Barang
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Kode Barang <span class="req">*</span></label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
               placeholder="Contoh: 3010110005" value="{{ old('code') }}" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label-custom">NUP <span class="req">*</span></label>
        <input type="text" name="nup" class="form-control @error('nup') is-invalid @enderror"
               placeholder="No Urut" value="{{ old('nup') }}" required>
        @error('nup')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-7">
        <label class="form-label-custom">Nama Barang <span class="req">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               placeholder="Masukkan nama barang" value="{{ old('name') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Merk / Uraian</label>
        <input type="text" name="name_fix" class="form-control"
               placeholder="Contoh: Honda, Asus" value="{{ old('name_fix') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">No. Seri</label>
        <input type="text" name="no_seri" class="form-control"
               placeholder="Nomor seri barang" value="{{ old('no_seri') }}">
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
        <select name="jenis_bmn" class="form-select @error('jenis_bmn') is-invalid @enderror" required>
            <option value="">-- Pilih Jenis BMN --</option>
            <option value="ALAT BESAR"                 {{ old('jenis_bmn')=='ALAT BESAR'                 ?'selected':'' }}>Alat Besar</option>
            <option value="ALAT ANGKUTAN BERMOTOR"     {{ old('jenis_bmn')=='ALAT ANGKUTAN BERMOTOR'     ?'selected':'' }}>Alat Angkutan Bermotor</option>
            <option value="BANGUNAN DAN GEDUNG"        {{ old('jenis_bmn')=='BANGUNAN DAN GEDUNG'        ?'selected':'' }}>Bangunan dan Gedung</option>
            <option value="JALAN DAN JEMBATAN"         {{ old('jenis_bmn')=='JALAN DAN JEMBATAN'         ?'selected':'' }}>Jalan dan Jembatan</option>
            <option value="MESIN PERALATAN KHUSUS TIK" {{ old('jenis_bmn')=='MESIN PERALATAN KHUSUS TIK' ?'selected':'' }}>Mesin Peralatan TIK</option>
            <option value="MESIN PERALATAN NON TIK"    {{ old('jenis_bmn')=='MESIN PERALATAN NON TIK'    ?'selected':'' }}>Mesin Peralatan Non TIK</option>
        </select>
        @error('jenis_bmn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tipe Aset</label>
        <select name="type" class="form-select">
            <option value="Tetap"      {{ old('type','Tetap')=='Tetap'      ?'selected':'' }}>Tetap</option>
            <option value="Alat besar" {{ old('type')=='Alat besar'         ?'selected':'' }}>Alat Besar</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Kondisi</label>
        <select name="condition" class="form-select">
            <option value="Baik"         {{ old('condition','Baik')=='Baik'         ?'selected':'' }}>Baik</option>
            <option value="Rusak Ringan" {{ old('condition')=='Rusak Ringan'         ?'selected':'' }}>Rusak Ringan</option>
            <option value="Rusak Berat"  {{ old('condition')=='Rusak Berat'          ?'selected':'' }}>Rusak Berat</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Status Penggunaan</label>
        <select name="status" class="form-select">
            <option value="Tidak Dipakai" {{ old('status','Tidak Dipakai')=='Tidak Dipakai' ?'selected':'' }}>Tidak Dipakai</option>
            <option value="Dipakai"       {{ old('status')=='Dipakai'                        ?'selected':'' }}>Dipakai</option>
            <option value="Maintenance"   {{ old('status')=='Maintenance'                    ?'selected':'' }}>Maintenance</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Status BMN</label>
        <select name="status_bmn" class="form-select">
            <option value="Aktif"       {{ old('status_bmn','Aktif')=='Aktif'       ?'selected':'' }}>Aktif</option>
            <option value="Tidak Aktif" {{ old('status_bmn')=='Tidak Aktif'          ?'selected':'' }}>Tidak Aktif</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tahun Perolehan</label>
        <input type="number" name="years" class="form-control"
               placeholder="{{ date('Y') }}" min="1900" max="{{ date('Y') }}"
               value="{{ old('years') }}">
    </div>
</div>

{{-- ══ 3. NILAI ══ --}}
<div class="section-header mb-3">
    <div class="section-number">3</div>
    <i class="bi bi-cash-stack"></i> Nilai & Waktu
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Perolehan Pertama (Rp)</label>
        <input type="number" name="nilai" class="form-control" placeholder="0" min="0"
               value="{{ old('nilai') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Perolehan (Rp)</label>
        <input type="number" name="nilai_perolehan" class="form-control" placeholder="0" min="0"
               value="{{ old('nilai_perolehan') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Penyusutan (Rp)</label>
        <input type="number" name="nilai_penyusutan" class="form-control" placeholder="0" min="0"
               value="{{ old('nilai_penyusutan') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Buku (Rp)</label>
        <input type="number" name="nilai_buku" class="form-control" placeholder="0" min="0"
               value="{{ old('nilai_buku') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Tanggal Perolehan</label>
        <input type="date" name="tanggal_perolehan" class="form-control"
               value="{{ old('tanggal_perolehan') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Tanggal Buku Pertama</label>
        <input type="date" name="tanggal_buku_pertama" class="form-control"
               value="{{ old('tanggal_buku_pertama') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Umur Aset (Tahun)</label>
        <input type="number" name="life_time" class="form-control" placeholder="0" min="0"
               value="{{ old('life_time') }}">
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
        <input type="text" name="no_psp" class="form-control" placeholder="Nomor PSP"
               value="{{ old('no_psp') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Tanggal PSP</label>
        <input type="date" name="tanggal_psp" class="form-control"
               value="{{ old('tanggal_psp') }}">
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
               placeholder="Kode Satuan Kerja" value="{{ old('kode_satker') }}">
    </div>
    <div class="col-md-9">
        <label class="form-label-custom">Nama Satker</label>
        <input type="text" name="nama_satker" class="form-control"
               placeholder="Nama Satuan Kerja" value="{{ old('nama_satker') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Alamat</label>
        <textarea name="alamat" class="form-control" rows="2"
                  placeholder="Alamat lengkap aset">{{ old('alamat') }}</textarea>
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Kab / Kota</label>
        <input type="text" name="kab_kota" class="form-control"
               placeholder="Contoh: Kota Bandung" value="{{ old('kab_kota') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Provinsi</label>
        <input type="text" name="provinsi" class="form-control"
               placeholder="Contoh: Jawa Barat" value="{{ old('provinsi') }}">
    </div>
</div>

{{-- ══ 6. FOTO DOKUMENTASI ══ --}}
<div class="section-header mb-3">
    <div class="section-number">6</div>
    <i class="bi bi-images"></i> Foto Dokumentasi
</div>
<div class="mb-4">
    <div class="photo-upload-zone" id="uploadZone" onclick="document.getElementById('photoInput').click()">
        <i class="bi bi-cloud-upload"></i>
        <div class="upload-title">Klik atau seret foto ke sini</div>
        <div class="upload-sub">Maks. <strong>5 foto</strong> · Format: JPG, JPEG, PNG, WEBP · Maks. <strong>15 MB</strong> per foto</div>
    </div>
    <input type="file" id="photoInput" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp" style="display:none">
    <div id="photoError" class="text-danger mt-2" style="font-size:.8rem;display:none"></div>
    <div id="photoPreviewGrid" class="photo-preview-grid"></div>
    <div id="photoCountWrap" style="display:none">
        <span class="photo-count-badge">
            <i class="bi bi-images"></i>
            <span id="photoCount">0</span> foto dipilih
        </span>
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

<script>
(function () {
    const MAX_FILES    = 5;
    const MAX_SIZE_MB  = 15;
    const MAX_SIZE_B   = MAX_SIZE_MB * 1024 * 1024;
    const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    const input       = document.getElementById('photoInput');
    const zone        = document.getElementById('uploadZone');
    const grid        = document.getElementById('photoPreviewGrid');
    const errBox      = document.getElementById('photoError');
    const countWrap   = document.getElementById('photoCountWrap');
    const countEl     = document.getElementById('photoCount');

    // DataTransfer untuk manajemen file list
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
            wrap.className = 'photo-thumb-wrap';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'photo-thumb-remove';
            btn.title = 'Hapus foto ini';
            btn.innerHTML = '<i class="bi bi-x"></i>';
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                removeFile(idx);
            });

            wrap.appendChild(img);
            wrap.appendChild(btn);
            grid.appendChild(wrap);
        });

        // Sync ke input
        input.files = dt.files;
    }

    function addFiles(newFiles) {
        let added = 0;
        Array.from(newFiles).forEach(file => {
            if (dt.files.length >= MAX_FILES) {
                showError('Maksimal ' + MAX_FILES + ' foto per aset.');
                return;
            }
            if (!ALLOWED_TYPES.includes(file.type)) {
                showError('Format tidak didukung: ' + file.name + '. Gunakan JPG, PNG, atau WEBP.');
                return;
            }
            if (file.size > MAX_SIZE_B) {
                showError('Ukuran foto "' + file.name + '" melebihi ' + MAX_SIZE_MB + ' MB.');
                return;
            }
            dt.items.add(file);
            added++;
        });
        if (added > 0) renderPreviews();
    }

    function removeFile(idx) {
        const newDt = new DataTransfer();
        Array.from(dt.files).forEach((f, i) => { if (i !== idx) newDt.items.add(f); });
        dt = newDt;
        renderPreviews();
    }

    input.addEventListener('change', function () {
        addFiles(this.files);
        this.value = '';
    });

    // Drag & drop
    zone.addEventListener('dragover', function (e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    zone.addEventListener('dragleave', function () {
        this.classList.remove('dragover');
    });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        this.classList.remove('dragover');
        addFiles(e.dataTransfer.files);
    });
})();
</script>
@endsection