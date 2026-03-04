@extends('layouts.app')

@section('content')
<div id="location-data" data-locations="{{ json_encode($locations) }}"></div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

<main id="main" class="main">

<style>
.form-control, .form-select {
    background-image: none !important; padding-right: 12px !important;
}
.form-control:valid, .form-select:valid,
.form-control.is-valid, .form-select.is-valid {
    border-color: #dee2e6 !important; background-image: none !important; padding-right: 12px !important;
}
.form-control:focus, .form-select:focus {
    border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
}
.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545 !important; background-image: none !important;
}
.section-header {
    display: flex; align-items: center; gap: 8px; padding: 8px 14px;
    background: linear-gradient(135deg, #1e3a5f, #2d5a8e); color: #fff;
    border-radius: 8px; font-size: 0.82rem; font-weight: 700; letter-spacing: 0.3px; margin-bottom: 4px;
}
.section-number {
    width: 22px; height: 22px; background: rgba(255,255,255,0.2); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.72rem; font-weight: 800; flex-shrink: 0;
}
.form-label-custom { font-size: 0.78rem; font-weight: 700; color: #4a5a6e; margin-bottom: 4px; display: block; }
.req { color: #dc3545; }
.form-control, .form-select {
    font-size: 0.85rem; border-radius: 8px; border: 1.5px solid #dee2e6; transition: border-color .15s, box-shadow .15s;
}
.create-card {
    background: #fff; border-radius: 14px; border: 1px solid rgba(30,58,95,0.08);
    box-shadow: 0 2px 14px rgba(30,58,95,0.06); padding: 24px;
}
.btn-simpan {
    padding: 10px 36px; background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
    color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: 0.9rem;
    box-shadow: 0 4px 12px rgba(30,58,95,0.25); transition: all .18s; cursor: pointer;
}
.btn-simpan:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(30,58,95,0.35); color: #fff; }
.btn-batal {
    padding: 10px 36px; background: #f4f6fb; color: #5a6a7e;
    border: 1.5px solid #dee2e6; border-radius: 10px; font-weight: 600; font-size: 0.9rem;
    text-decoration: none; transition: all .18s; display: inline-block;
}
.btn-batal:hover { background: #e8ecf5; color: #3d5170; }

/* ── Select2 ── */
.select2-container--default .select2-selection--single {
    height: 38px !important; border: 1.5px solid #dee2e6 !important;
    border-radius: 8px !important; padding: 4px 8px !important; font-size: 0.85rem;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important; color: #212529; padding-left: 4px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; right: 6px; }
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #86b7fe !important; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
}
.select2-dropdown { border: 1.5px solid #dee2e6; border-radius: 8px; font-size: 0.85rem; box-shadow: 0 4px 16px rgba(30,58,95,0.12); }
.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1.5px solid #dee2e6; border-radius: 6px; padding: 5px 8px; font-size: 0.83rem; outline: none;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #1e3a5f; }
.select2-container--default .select2-results__option { padding: 7px 10px; font-size: 0.84rem; }
.select2-container { width: 100% !important; }
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
<form action="{{ route('asetTetap.store') }}" method="POST" enctype="multipart/form-data" id="formCreate">
@csrf

{{-- ══ 1. IDENTITAS BARANG ══ --}}
<div class="section-header mb-3"><div class="section-number">1</div><i class="bi bi-tag-fill"></i> Identitas Barang</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label-custom">Kode Barang <span class="req">*</span></label>
        <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: 3050104..." required>
        <div class="invalid-feedback">Kode Barang wajib diisi.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">NUP <span class="req">*</span></label>
        <input type="number" name="nup" class="form-control" placeholder="No Urut Pendaftaran" required>
        <div class="invalid-feedback">NUP wajib diisi.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">No Seri</label>
        <input type="text" name="no_seri" class="form-control" placeholder="Serial Number">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Nama Barang <span class="req">*</span></label>
        <input type="text" name="nama_barang" class="form-control" placeholder="Masukkan nama barang" required>
        <div class="invalid-feedback">Nama Barang wajib diisi.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Merk / Uraian Barang <span class="req">*</span></label>
        <input type="text" name="merk" class="form-control" placeholder="Contoh: Asus, Honda, dll" required>
        <div class="invalid-feedback">Merk/Uraian wajib diisi.</div>
    </div>
</div>

{{-- ══ 2. KLASIFIKASI ══ --}}
<div class="section-header mb-3"><div class="section-number">2</div><i class="bi bi-grid-fill"></i> Klasifikasi</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Jenis BMN</label>
        <input type="text" name="jenis_bmn" class="form-control" placeholder="Contoh: Alat Besar">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Kategori <span class="req">*</span></label>
        <select name="category" class="form-select" required>
            <option value="" disabled selected>Pilih Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback">Kategori wajib dipilih.</div>
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Kondisi <span class="req">*</span></label>
        <select name="kondisi" class="form-select" required>
            <option value="Baik">Baik</option>
            <option value="Rusak Ringan">Rusak Ringan</option>
            <option value="Rusak Berat">Rusak Berat</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Status <span class="req">*</span></label>
        <select name="status" class="form-select" required>
            <option value="Tidak Dipakai" selected>Tidak Dipakai</option>
            <option value="Dipakai">Dipakai</option>
            <option value="Maintenance">Maintenance</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tipe Aset <span class="req">*</span></label>
        <div class="d-flex gap-4 mt-1">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type" value="Tetap" checked>
                <label class="form-check-label" style="font-size:0.85rem;">Tetap</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type" value="Bergerak">
                <label class="form-check-label" style="font-size:0.85rem;">Bergerak</label>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Intra / Extra</label>
        <select name="intra_extra" class="form-select">
            <option value="">-- Pilih --</option>
            <option value="Intra">Intra</option>
            <option value="Extra">Extra</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Status BMN</label>
        <select name="status_bmn" class="form-select">
            <option value="">-- Pilih --</option>
            <option value="Aktif">Aktif</option>
            <option value="Tidak Aktif">Tidak Aktif</option>
        </select>
    </div>
</div>

{{-- ══ 3. NILAI & WAKTU ══ --}}
<div class="section-header mb-3"><div class="section-number">3</div><i class="bi bi-cash-stack"></i> Nilai & Waktu</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Perolehan (Rp)</label>
        <input type="number" name="nilai" class="form-control" placeholder="0">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Penyusutan (Rp)</label>
        <input type="number" name="nilai_penyusutan" class="form-control" placeholder="0">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Nilai Buku (Rp)</label>
        <input type="number" name="nilai_buku" class="form-control" placeholder="0">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Tahun Perolehan <span class="req">*</span></label>
        <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tanggal Perolehan</label>
        <input type="date" name="tanggal_perolehan" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tgl Buku Pertama</label>
        <input type="date" name="tanggal_buku_pertama" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tgl Pengapusan</label>
        <input type="date" name="tanggal_pengapusan" class="form-control">
    </div>
</div>

{{-- ══ 4. DATA FISIK ══ --}}
<div class="section-header mb-3"><div class="section-number">4</div><i class="bi bi-box-fill"></i> Data Fisik</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label-custom">Satuan</label>
        <input type="text" name="satuan" class="form-control" placeholder="Unit/Pcs/Set">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">Umur Aset (Tahun)</label>
        <input type="number" name="lifetime" class="form-control" placeholder="Masa pakai">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Spesifikasi</label>
        <input type="text" name="spek" class="form-control" placeholder="Detail spesifikasi barang">
    </div>
</div>

{{-- ══ 5. LOKASI FISIK ══ --}}
<div class="section-header mb-3"><div class="section-number">5</div><i class="bi bi-geo-alt-fill"></i> Lokasi Fisik</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label-custom">Gedung <span class="req">*</span></label>
        <select id="gedung" name="gedung" class="form-select" required>
            <option value="" disabled selected>Pilih Gedung</option>
            @foreach ($gedungOptions as $gedung)
                <option value="{{ $gedung }}">{{ $gedung }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Lantai <span class="req">*</span></label>
        <select id="lantai" name="lantai" class="form-select" disabled required>
            <option value="" disabled selected>Pilih Lantai</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Ruangan <span class="req">*</span></label>
        <select id="ruangan" name="ruangan" class="form-select" disabled required>
            <option value="" disabled selected>Pilih Ruangan</option>
        </select>
    </div>
</div>

{{-- ══ 6. DATA BMN / SATKER ══ --}}
<div class="section-header mb-3"><div class="section-number">6</div><i class="bi bi-building"></i> Data BMN / Satker</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label-custom">Kode Satker</label>
        <input type="text" name="kode_satker" class="form-control" placeholder="Kode Satuan Kerja">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Nama Satker</label>
        <input type="text" name="nama_satker" class="form-control" placeholder="Nama Satuan Kerja">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Kode Register</label>
        <input type="text" name="kode_register" class="form-control" placeholder="Kode Register BMN">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Nama K/L</label>
        <input type="text" name="nama_kl" class="form-control" placeholder="Nama Kementerian/Lembaga">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Nama Unit (E1)</label>
        <input type="text" name="nama_e1" class="form-control" placeholder="Nama Unit/Eselon 1">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Alamat</label>
        <input type="text" name="alamat" class="form-control" placeholder="Alamat aset">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Kab/Kota</label>
        <input type="text" name="kab_kota" class="form-control" placeholder="Kabupaten/Kota">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Provinsi</label>
        <input type="text" name="provinsi" class="form-control" placeholder="Provinsi">
    </div>
</div>

{{-- ══ 7. DOKUMEN BMN ══ --}}
<div class="section-header mb-3"><div class="section-number">7</div><i class="bi bi-file-earmark-text-fill"></i> Dokumen & Keterangan BMN</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label-custom">Status Sertifikasi</label>
        <input type="text" name="status_sertifikasi" class="form-control" placeholder="Contoh: Belum Bersertipikat">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">No PSP</label>
        <input type="text" name="no_psp" class="form-control" placeholder="Nomor PSP">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Tanggal PSP</label>
        <input type="date" name="tanggal_psp" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Status Penggunaan</label>
        <input type="text" name="status_penggunaan" class="form-control" placeholder="Contoh: Digunakan sendiri untuk operasional">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">No Polisi</label>
        <input type="text" name="no_polisi" class="form-control" placeholder="Plat nomor kendaraan">
    </div>
    <div class="col-md-3">
        <label class="form-label-custom">No STNK</label>
        <input type="text" name="no_stnk" class="form-control" placeholder="Nomor STNK">
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Nama Pengguna Aset</label>
        <input type="text" name="nama_pengguna" class="form-control" placeholder="Nama pengguna / pemegang aset">
    </div>
</div>

{{-- ══ 8. KALIBRASI ══ --}}
<div class="section-header mb-3"><div class="section-number">8</div><i class="bi bi-tools"></i> Kalibrasi</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label-custom">Perlu Kalibrasi?</label>
        <select name="calibrate" class="form-select">
            <option value="0">Tidak Perlu</option>
            <option value="1">Perlu Kalibrasi</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Kalibrasi Terakhir</label>
        <input type="date" name="last_kalibrasi" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label-custom">Jadwal Kalibrasi</label>
        <input type="date" name="schedule_kalibrasi" class="form-control">
    </div>
</div>

{{-- ══ 9. PENANGGUNG JAWAB & KETERANGAN ══ --}}
<div class="section-header mb-3"><div class="section-number">9</div><i class="bi bi-person-check-fill"></i> Penanggung Jawab & Keterangan</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label-custom">Penanggung Jawab <span class="req">*</span></label>
        <select name="supervisor" id="supervisorSelect" class="form-select" required>
            <option value="">-- Pilih Penanggung Jawab --</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback">Penanggung jawab wajib dipilih.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label-custom">Dokumentasi (Foto)</label>
        <input type="file" name="dokumentasi" class="form-control" accept="image/*">
    </div>
    <div class="col-12">
        <label class="form-label-custom">Deskripsi / Keterangan <span class="req">*</span></label>
        <textarea name="keterangan" class="form-control" rows="3"
                  placeholder="Catatan tambahan mengenai aset ini" required></textarea>
        <div class="invalid-feedback">Keterangan wajib diisi.</div>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {

    // ── 1. Select2 Penanggung Jawab ──
    $('#supervisorSelect').select2({
        placeholder: 'Ketik nama untuk mencari...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() { return 'Nama tidak ditemukan'; },
            searching:  function() { return 'Mencari...'; }
        }
    });

    // ── 2. Dropdown lokasi ──
    var locations    = {!! json_encode($locations) !!};
    var gedungSelect = document.getElementById('gedung');
    var lantaiSelect = document.getElementById('lantai');
    var ruanganSelect= document.getElementById('ruangan');

    gedungSelect.addEventListener('change', function() {
        var g = this.value;
        lantaiSelect.innerHTML  = '<option value="" disabled selected>Pilih Lantai</option>';
        ruanganSelect.innerHTML = '<option value="" disabled selected>Pilih Ruangan</option>';
        ruanganSelect.disabled  = true;
        [...new Set(locations.filter(function(l){ return l.office === g; }).map(function(l){ return l.floor; }))]
            .forEach(function(f){ lantaiSelect.add(new Option(f, f)); });
        lantaiSelect.disabled = false;
    });

    lantaiSelect.addEventListener('change', function() {
        var g = gedungSelect.value, f = this.value;
        ruanganSelect.innerHTML = '<option value="" disabled selected>Pilih Ruangan</option>';
        locations.filter(function(l){ return l.office === g && l.floor == f; })
            .map(function(l){ return l.room; })
            .forEach(function(r){ ruanganSelect.add(new Option(r, r)); });
        ruanganSelect.disabled = false;
    });

    // ── 3. Validasi ──
    document.getElementById('formCreate').addEventListener('submit', function(e) {
        var valid = true;
        this.querySelectorAll('[required]').forEach(function(el) {
            if (!el.value || !el.value.trim()) {
                el.classList.add('is-invalid'); valid = false;
            } else {
                el.classList.remove('is-invalid');
            }
        });
        if (!$('#supervisorSelect').val()) {
            $('#supervisorSelect').next('.select2-container').css('border','1.5px solid #dc3545');
            valid = false;
        } else {
            $('#supervisorSelect').next('.select2-container').css('border','');
        }
        if (!valid) e.preventDefault();
    });

    document.getElementById('formCreate').querySelectorAll('[required]').forEach(function(el) {
        el.addEventListener('input',  function(){ this.classList.remove('is-invalid'); });
        el.addEventListener('change', function(){ this.classList.remove('is-invalid'); });
    });

    $('#supervisorSelect').on('select2:select select2:clear', function() {
        $(this).next('.select2-container').css('border','');
    });

});
</script>
@endsection