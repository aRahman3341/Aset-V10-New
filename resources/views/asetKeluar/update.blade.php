@extends('layouts.app')
@section('title') Aset Keluar - Monitoring Aset @endsection
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<main id="main" class="main">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Edit Aset Keluar</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Edit Aset Keluar</h5>
        </div>
        <div class="card-body">
            <form class="row g-3" method="POST" action="{{ route('asetkeluar.update', $asetKeluar->id) }}">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label class="form-label fw-bold">Nomor</label>
                    <input type="text" class="form-control" name="nomor" value="{{ $asetKeluar->nomor }}">
                    @error('nomor') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Diserahkan Kepada</label>
                    <input type="text" class="form-control" name="kepada" value="{{ $asetKeluar->kepada }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Pihak Kesatu</label>
                    <input type="text" class="form-control" name="pihakSatu" value="{{ $asetKeluar->pihakSatu }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Pihak Kedua</label>
                    <input type="text" class="form-control" name="pihakDua" value="{{ $asetKeluar->pihakDua }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">NIP Pihak Kesatu</label>
                    <input type="text" class="form-control" name="nipSatu" value="{{ $asetKeluar->pihakSatuNip }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">NIP Pihak Kedua</label>
                    <input type="text" class="form-control" name="nipDua" value="{{ $asetKeluar->pihakDuaNIP }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Jabatan Pihak Kesatu</label>
                    <input type="text" class="form-control" name="jabatanSatu" value="{{ $asetKeluar->pihakSatuJabatan }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Jabatan Pihak Kedua</label>
                    <input type="text" class="form-control" name="jabatanDua" value="{{ $asetKeluar->pihakDuaJabatan }}">
                </div>

                {{-- Daftar Aset --}}
                <div id="name-fields-container" class="col-md-12">
                    @foreach (json_decode($asetKeluar->aset) as $asetItem)
                        <div class="row mb-2">
                            <div class="col-10">
                                <label class="form-label fw-bold">Aset</label>
                                <select name="name[]" class="form-control namebox" required>
                                    <option value="">Pilih Aset</option>
                                    @foreach ($items as $item)
                                        @if ($item->status != 'Diserahkan' || $item->id == $asetItem->name)
                                            <option value="{{ $item->id }}" {{ $item->id == $asetItem->name ? 'selected' : '' }}>
                                                {{ $item->{'Nama Barang'} ?? '-' }} — {{ $item->{'Kode Barang'} ?? '-' }} (NUP: {{ $item->nup }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item-btn w-100">Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-12">
                    <button type="button" id="add-name" class="btn btn-primary">+ Tambah Aset</button>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="{{ route('asetkeluar.index') }}" class="btn btn-secondary ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('.namebox').select2({ dropdownAutoWidth: true });

    // Tambah baris aset baru
    $('#add-name').on('click', function () {
        var newRow = `
            <div class="row mb-2">
                <div class="col-10">
                    <label class="form-label fw-bold">Aset</label>
                    <select name="name[]" class="form-control namebox new-select">
                        <option value="">Pilih Aset</option>
                        @foreach ($items as $item)
                            @if ($item->status != 'Diserahkan')
                                <option value="{{ $item->id }}">
                                    {{ $item->{'Nama Barang'} ?? '-' }} — {{ $item->{'Kode Barang'} ?? '-' }} (NUP: {{ $item->nup }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-item-btn w-100">Hapus</button>
                </div>
            </div>`;
        $('#name-fields-container').append(newRow);
        $('#name-fields-container .new-select').last().select2({ dropdownAutoWidth: true }).removeClass('new-select');
    });

    $(document).on('click', '.remove-item-btn', function () {
        $(this).closest('.row').remove();
    });
});
</script>
@endsection