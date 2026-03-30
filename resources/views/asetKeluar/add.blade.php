@extends('layouts.app')
@section('title') Tambah Aset Keluar - Monitoring Aset @endsection
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<main id="main" class="main">
<style>
.ak-pagetitle { display:flex; align-items:center; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid rgba(30,58,95,0.08); }
.ak-pagetitle-left { display:flex; align-items:center; gap:14px; }
.ak-pagetitle-icon { width:46px; height:46px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; box-shadow:0 4px 12px rgba(30,58,95,0.25); flex-shrink:0; }
.ak-pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 3px; }
.ak-pagetitle .breadcrumb { font-size:0.75rem; margin:0; padding:0; background:transparent; }
.ak-pagetitle .breadcrumb a { color:#2d5a8e; text-decoration:none; }
.ak-pagetitle .breadcrumb-item.active { color:#8a96a3; }
.ak-card { background:#fff; border-radius:16px; border:1px solid rgba(30,58,95,0.08); box-shadow:0 2px 16px rgba(30,58,95,0.07); overflow:hidden; }
.ak-card-header { padding:16px 24px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); display:flex; align-items:center; gap:10px; color:#fff; }
.ak-card-header span { font-size:0.95rem; font-weight:700; }
.ak-card-body { padding:28px; }
.ak-label { display:block; font-size:0.78rem; font-weight:700; color:#4a5a6e; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px; }
.req { color:#dc3545; }
.ak-input { width:100%; padding:10px 14px; font-size:0.85rem; color:#1e3a5f; background:#f8fafd; border:1.5px solid #dee2e6; border-radius:10px; outline:none; transition:border-color .15s; }
.ak-input:focus { background:#fff; border-color:#2d5a8e; box-shadow:0 0 0 3px rgba(45,90,142,0.10); }
.ak-field { margin-bottom:18px; }
.ak-field-error { display:block; font-size:0.75rem; color:#dc3545; margin-top:4px; }
.ak-divider { display:flex; align-items:center; gap:10px; margin:4px 0 18px; font-size:0.75rem; font-weight:700; color:#8a96a3; text-transform:uppercase; letter-spacing:0.5px; }
.ak-divider::before, .ak-divider::after { content:''; flex:1; height:1px; background:rgba(30,58,95,0.08); }
.ak-btn-submit { width:100%; padding:12px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); color:#fff; border:none; border-radius:10px; font-size:0.9rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:all .18s; }
.ak-btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(30,58,95,0.35); }
.ak-btn-back { width:100%; padding:11px; background:#f4f6fb; color:#5a6a7e; border:1.5px solid #dee2e6; border-radius:10px; font-size:0.88rem; font-weight:600; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .15s; }
.ak-btn-back:hover { background:#e8ecf5; color:#1e3a5f; text-decoration:none; }
.ak-btn-add-aset { padding:9px 16px; background:#eff6ff; color:#1d4ed8; border:1.5px solid #bfdbfe; border-radius:8px; font-size:0.82rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .15s; }
.ak-btn-add-aset:hover { background:#1d4ed8; color:#fff; }
.aset-item { background:#f8fafd; border:1.5px solid #e0e8f5; border-radius:10px; padding:14px; margin-bottom:12px; position:relative; }
.aset-remove { position:absolute; top:10px; right:10px; width:26px; height:26px; border-radius:6px; background:#fff0f0; color:#dc2626; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:0.8rem; }
.aset-remove:hover { background:#dc2626; color:#fff; }
.select2-container--default .select2-selection--single { height:42px!important; border-radius:10px!important; border:1.5px solid #dee2e6!important; background:#f8fafd!important; display:flex; align-items:center; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:42px!important; padding-left:14px!important; font-size:0.85rem; color:#1e3a5f; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:40px!important; }
.select2-container--default.select2-container--focus .select2-selection--single { border-color:#2d5a8e!important; }
.select2-dropdown { border-radius:10px!important; }
.select2-results__option { font-size:0.83rem; padding:8px 14px; }
.select2-container--default .select2-results__option--highlighted { background:#1e3a5f!important; }
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

                <form method="POST" action="{{ route('asetkeluar.store') }}">
                    @csrf

                    <div class="ak-divider">Informasi Dokumen</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="ak-field">
                                <label class="ak-label">Nomor Dokumen <span class="req">*</span></label>
                                <input type="text" name="nomor" class="ak-input"
                                       placeholder="Contoh: 01/BA/SATKER/CB36/2025"
                                       value="{{ old('nomor') }}" required>
                                @error('nomor') <span class="ak-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ak-field">
                                <label class="ak-label">Diserahkan Kepada <span class="req">*</span></label>
                                <input type="text" name="kepada" class="ak-input"
                                       placeholder="Nama penerima"
                                       value="{{ old('kepada') }}" required>
                                @error('kepada') <span class="ak-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="ak-divider">Pihak yang Terlibat</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="ak-field">
                                <label class="ak-label">Nama Pihak Kesatu <span class="req">*</span></label>
                                <input type="text" name="pihakSatu" class="ak-input"
                                       placeholder="Nama pihak kesatu"
                                       value="{{ old('pihakSatu') }}" required>
                                @error('pihakSatu') <span class="ak-field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="ak-field">
                                <label class="ak-label">NIP Pihak Kesatu</label>
                                <input type="text" name="nipSatu" class="ak-input"
                                       placeholder="NIP pihak kesatu"
                                       value="{{ old('nipSatu') }}">
                                @error('nipSatu') <span class="ak-field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="ak-field">
                                <label class="ak-label">Jabatan Pihak Kesatu</label>
                                <input type="text" name="jabatanSatu" class="ak-input"
                                       placeholder="Jabatan pihak kesatu"
                                       value="{{ old('jabatanSatu') }}">
                                @error('jabatanSatu') <span class="ak-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ak-field">
                                <label class="ak-label">Nama Pihak Kedua <span class="req">*</span></label>
                                <input type="text" name="pihakDua" class="ak-input"
                                       placeholder="Nama pihak kedua"
                                       value="{{ old('pihakDua') }}" required>
                                @error('pihakDua') <span class="ak-field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="ak-field">
                                <label class="ak-label">NIP Pihak Kedua</label>
                                <input type="text" name="nipDua" class="ak-input"
                                       placeholder="NIP pihak kedua"
                                       value="{{ old('nipDua') }}">
                                @error('nipDua') <span class="ak-field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="ak-field">
                                <label class="ak-label">Jabatan Pihak Kedua</label>
                                <input type="text" name="jabatanDua" class="ak-input"
                                       placeholder="Jabatan pihak kedua"
                                       value="{{ old('jabatanDua') }}">
                                @error('jabatanDua') <span class="ak-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="ak-divider">Daftar Aset yang Keluar</div>

                    <div id="aset-container">
                        <div class="aset-item">
                            <label class="ak-label">Aset <span class="req">*</span></label>
                            <select name="name[]" class="namebox" style="width:100%" required>
                                <option value="">-- Pilih Aset --</option>
                                @foreach ($items as $item)
                                    @if ($item->status != "Diserahkan")
                                        <option value="{{ $item->id }}">{{ $item->name }} — {{ $item->code }} (NUP: {{ $item->nup }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="button" class="ak-btn-add-aset mb-4" id="btnAddAset">
                        <i class="bi bi-plus-circle"></i> Tambah Aset Lagi
                    </button>

                    <div class="row g-3 mt-2">
                        <div class="col-md-8">
                            <button type="submit" class="ak-btn-submit">
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

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function initSelect2() {
    $('.namebox').each(function () {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({ placeholder: '-- Pilih Aset --', allowClear: true });
        }
    });
}
initSelect2();

const itemTemplate = `
    <div class="aset-item">
        <button type="button" class="aset-remove btn-remove-aset" title="Hapus"><i class="bi bi-x"></i></button>
        <label class="ak-label">Aset</label>
        <select name="name[]" class="namebox" style="width:100%">
            <option value="">-- Pilih Aset --</option>
            @foreach ($items as $item)
                @if ($item->status != "Diserahkan")
                    <option value="{{ $item->id }}">{{ $item->name }} — {{ $item->code }} (NUP: {{ $item->nup }})</option>
                @endif
            @endforeach
        </select>
    </div>`;

document.getElementById('btnAddAset').addEventListener('click', function () {
    $('#aset-container').append(itemTemplate);
    initSelect2();
});

$(document).on('click', '.btn-remove-aset', function () {
    if ($('.aset-item').length > 1) {
        $(this).closest('.aset-item').remove();
    }
});
</script>
@endsection