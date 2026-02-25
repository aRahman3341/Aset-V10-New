@extends('layouts.app')

@section('content')
    <div id="location-data" data-locations="{{ json_encode($locations) }}"></div>

    <main id="main" class="main">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h4 fw-bold">Edit Aset</h1>
        </div>

        <div class="row d-flex justify-content-center">
            <div class="card">
                <div class="card mt-4">
                    <div class="card-header">
                        <div class="row" style="margin-top: -18px; margin-bottom: -18px">
                            <div class="col-md-6">
                                <h5 class="card-title">Form Edit Aset</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form class="row g-3 needs-validation" action="{{ route('asetTetap.update', $item->id) }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- ===== SECTION: IDENTITAS BARANG ===== --}}
                        <div class="col-12">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-tag"></i> Identitas Barang</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="code" class="col-form-label fw-bold">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" id="code" name="code" class="form-control" value="{{ old('code', $item->code) }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nup" class="col-form-label fw-bold">NUP <span class="text-danger">*</span></label>
                            <input type="text" id="nup" name="nup" class="form-control" value="{{ old('nup', $item->nup) }}" required>
                            @error('nup') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="no_seri" class="col-form-label fw-bold">No Seri</label>
                            <input type="text" id="no_seri" name="no_seri" class="form-control" value="{{ old('no_seri', $item->no_seri) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="col-form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="name_fix" class="col-form-label fw-bold">Nama Fix / Merk / Uraian <span class="text-danger">*</span></label>
                            <input type="text" id="name_fix" name="name_fix" class="form-control" value="{{ old('name_fix', $item->name_fix) }}" required>
                        </div>

                        {{-- ===== SECTION: KLASIFIKASI ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-grid"></i> Klasifikasi</h6>
                        </div>

                        <div class="col-md-3">
                            <label for="jenis_bmn" class="col-form-label fw-bold">Jenis BMN</label>
                            <input type="text" id="jenis_bmn" name="jenis_bmn" class="form-control" value="{{ old('jenis_bmn', $item->jenis_bmn) }}" placeholder="Contoh: Alat Besar">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="col-form-label fw-bold">Kategori</label>
                            <select id="category" name="category" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category', $item->category) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="condition" class="col-form-label fw-bold">Kondisi <span class="text-danger">*</span></label>
                            <select id="condition" name="condition" class="form-select" required>
                                <option value="Baik"         {{ old('condition', $item->condition) == 'Baik'         ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak Ringan" {{ old('condition', $item->condition) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Rusak Berat"  {{ old('condition', $item->condition) == 'Rusak Berat'  ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="col-form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="Dipakai"      {{ old('status', $item->status) == 'Dipakai'      ? 'selected' : '' }}>Dipakai</option>
                                <option value="Tidak Dipakai"{{ old('status', $item->status) == 'Tidak Dipakai'? 'selected' : '' }}>Tidak Dipakai</option>
                                <option value="Maintenance"  {{ old('status', $item->status) == 'Maintenance'  ? 'selected' : '' }}>Maintenance</option>
                                <option value="Diserahkan"   {{ old('status', $item->status) == 'Diserahkan'   ? 'selected' : '' }}>Diserahkan</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="col-form-label fw-bold">Tipe Aset <span class="text-danger">*</span></label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="type_tetap" value="Tetap" {{ old('type', $item->type) == 'Tetap' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_tetap">Tetap</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="type_bergerak" value="Bergerak" {{ old('type', $item->type) == 'Bergerak' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_bergerak">Bergerak</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="intra_extra" class="col-form-label fw-bold">Intra / Extra</label>
                            <select id="intra_extra" name="intra_extra" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Intra" {{ old('intra_extra', $item->intra_extra) == 'Intra' ? 'selected' : '' }}>Intra</option>
                                <option value="Extra" {{ old('intra_extra', $item->intra_extra) == 'Extra' ? 'selected' : '' }}>Extra</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status_bmn" class="col-form-label fw-bold">Status BMN</label>
                            <select id="status_bmn" name="status_bmn" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Aktif"      {{ old('status_bmn', $item->status_bmn) == 'Aktif'      ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif"{{ old('status_bmn', $item->status_bmn) == 'Tidak Aktif'? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>

                        {{-- ===== SECTION: NILAI & WAKTU ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-currency-dollar"></i> Nilai & Waktu</h6>
                        </div>

                        <div class="col-md-3">
                            <label for="nilai" class="col-form-label fw-bold">Nilai Perolehan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" id="nilai" name="nilai" class="form-control" value="{{ old('nilai', $item->nilai_perolehan ?? $item->nilai) }}" step="1" required>
                        </div>
                        <div class="col-md-3">
                            <label for="nilai_penyusutan" class="col-form-label fw-bold">Nilai Penyusutan (Rp)</label>
                            <input type="number" id="nilai_penyusutan" name="nilai_penyusutan" class="form-control" value="{{ old('nilai_penyusutan', $item->nilai_penyusutan) }}" step="1">
                        </div>
                        <div class="col-md-3">
                            <label for="nilai_buku" class="col-form-label fw-bold">Nilai Buku (Rp)</label>
                            <input type="number" id="nilai_buku" name="nilai_buku" class="form-control" value="{{ old('nilai_buku', $item->nilai_buku) }}" step="1">
                        </div>
                        <div class="col-md-3">
                            <label for="years" class="col-form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                            <input type="number" id="years" name="years" class="form-control" value="{{ old('years', $item->years) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label for="tanggal_perolehan" class="col-form-label fw-bold">Tanggal Perolehan</label>
                            <input type="date" id="tanggal_perolehan" name="tanggal_perolehan" class="form-control"
                                value="{{ old('tanggal_perolehan', $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_buku_pertama" class="col-form-label fw-bold">Tgl Buku Pertama</label>
                            <input type="date" id="tanggal_buku_pertama" name="tanggal_buku_pertama" class="form-control"
                                value="{{ old('tanggal_buku_pertama', $item->tanggal_buku_pertama ? \Carbon\Carbon::parse($item->tanggal_buku_pertama)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_pengapusan" class="col-form-label fw-bold">Tgl Pengapusan</label>
                            <input type="date" id="tanggal_pengapusan" name="tanggal_pengapusan" class="form-control"
                                value="{{ old('tanggal_pengapusan', $item->tanggal_pengapusan ? \Carbon\Carbon::parse($item->tanggal_pengapusan)->format('Y-m-d') : '') }}">
                        </div>

                        {{-- ===== SECTION: FISIK ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-box"></i> Data Fisik</h6>
                        </div>

                        <div class="col-md-2">
                            <label for="quantity" class="col-form-label fw-bold">Qty</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity ?? 1) }}" min="1">
                        </div>
                        <div class="col-md-2">
                            <label for="satuan" class="col-form-label fw-bold">Satuan</label>
                            <input type="text" id="satuan" name="satuan" class="form-control" value="{{ old('satuan', $item->satuan) }}">
                        </div>
                        <div class="col-md-2">
                            <label for="umur_aset" class="col-form-label fw-bold">Umur (Thn)</label>
                            <input type="number" id="umur_aset" name="umur_aset" class="form-control" value="{{ old('umur_aset', $item->umur_aset ?? $item->life_time) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="specification" class="col-form-label fw-bold">Spesifikasi</label>
                            <input type="text" id="specification" name="specification" class="form-control" value="{{ old('specification', $item->specification) }}">
                        </div>

                        {{-- ===== SECTION: LOKASI FISIK ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-geo-alt"></i> Lokasi Fisik</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="gedung" class="col-form-label fw-bold">Gedung</label>
                            <select id="gedung" name="gedung" class="form-select">
                                <option value="">-- Pilih Gedung --</option>
                                @foreach ($gedungOptions as $gedung)
                                    <option value="{{ $gedung }}">{{ $gedung }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="lantai" class="col-form-label fw-bold">Lantai</label>
                            <select id="lantai" name="lantai" class="form-select"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="ruangan" class="col-form-label fw-bold">Ruangan</label>
                            <select id="ruangan" name="ruangan" class="form-select"></select>
                        </div>

                        {{-- ===== SECTION: LOKASI BMN ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-building"></i> Data BMN / Satker</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="kode_satker" class="col-form-label fw-bold">Kode Satker</label>
                            <input type="text" id="kode_satker" name="kode_satker" class="form-control" value="{{ old('kode_satker', $item->kode_satker) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="nama_satker" class="col-form-label fw-bold">Nama Satker</label>
                            <input type="text" id="nama_satker" name="nama_satker" class="form-control" value="{{ old('nama_satker', $item->nama_satker) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="kode_register" class="col-form-label fw-bold">Kode Register</label>
                            <input type="text" id="kode_register" name="kode_register" class="form-control" value="{{ old('kode_register', $item->kode_register) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="nama_kl" class="col-form-label fw-bold">Nama K/L</label>
                            <input type="text" id="nama_kl" name="nama_kl" class="form-control" value="{{ old('nama_kl', $item->nama_kl) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="nama_e1" class="col-form-label fw-bold">Nama Unit (E1)</label>
                            <input type="text" id="nama_e1" name="nama_e1" class="form-control" value="{{ old('nama_e1', $item->nama_e1) }}">
                        </div>

                        <div class="col-md-4">
                            <label for="alamat" class="col-form-label fw-bold">Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="form-control" value="{{ old('alamat', $item->alamat) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="kab_kota" class="col-form-label fw-bold">Kab/Kota</label>
                            <input type="text" id="kab_kota" name="kab_kota" class="form-control" value="{{ old('kab_kota', $item->kab_kota) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="provinsi" class="col-form-label fw-bold">Provinsi</label>
                            <input type="text" id="provinsi" name="provinsi" class="form-control" value="{{ old('provinsi', $item->provinsi) }}">
                        </div>

                        {{-- ===== SECTION: KALIBRASI ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-tools"></i> Kalibrasi</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="last_kalibrasi" class="col-form-label fw-bold">Kalibrasi Terakhir</label>
                            <input type="date" id="last_kalibrasi" name="last_kalibrasi" class="form-control"
                                value="{{ old('last_kalibrasi', $item->last_kalibrasi ? \Carbon\Carbon::parse($item->last_kalibrasi)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="schadule_kalibrasi" class="col-form-label fw-bold">Jadwal Kalibrasi</label>
                            <input type="date" id="schadule_kalibrasi" name="schadule_kalibrasi" class="form-control"
                                value="{{ old('schadule_kalibrasi', $item->schadule_kalibrasi ? \Carbon\Carbon::parse($item->schadule_kalibrasi)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="kalibrasi_by" class="col-form-label fw-bold">Dikalibrasi Oleh</label>
                            <input type="text" id="kalibrasi_by" name="kalibrasi_by" class="form-control" value="{{ old('kalibrasi_by', $item->kalibrasi_by) }}">
                        </div>

                        {{-- ===== SECTION: DOKUMEN BMN ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-file-earmark-text"></i> Dokumen & Keterangan BMN</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="status_sertifikasi" class="col-form-label fw-bold">Status Sertifikasi</label>
                            <input type="text" id="status_sertifikasi" name="status_sertifikasi" class="form-control" value="{{ old('status_sertifikasi', $item->status_sertifikasi) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="no_psp" class="col-form-label fw-bold">No PSP</label>
                            <input type="text" id="no_psp" name="no_psp" class="form-control" value="{{ old('no_psp', $item->no_psp) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_psp" class="col-form-label fw-bold">Tanggal PSP</label>
                            <input type="date" id="tanggal_psp" name="tanggal_psp" class="form-control"
                                value="{{ old('tanggal_psp', $item->tanggal_psp ? \Carbon\Carbon::parse($item->tanggal_psp)->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="status_penggunaan" class="col-form-label fw-bold">Status Penggunaan</label>
                            <input type="text" id="status_penggunaan" name="status_penggunaan" class="form-control" value="{{ old('status_penggunaan', $item->status_penggunaan) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="no_polisi" class="col-form-label fw-bold">No Polisi</label>
                            <input type="text" id="no_polisi" name="no_polisi" class="form-control" value="{{ old('no_polisi', $item->no_polisi) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="no_stnk" class="col-form-label fw-bold">No STNK</label>
                            <input type="text" id="no_stnk" name="no_stnk" class="form-control" value="{{ old('no_stnk', $item->no_stnk) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="nama_pengguna" class="col-form-label fw-bold">Nama Pengguna Aset</label>
                            <input type="text" id="nama_pengguna" name="nama_pengguna" class="form-control" value="{{ old('nama_pengguna', $item->nama_pengguna) }}">
                        </div>

                        {{-- ===== SECTION: PENANGGUNG JAWAB & KETERANGAN ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-person-check"></i> Penanggung Jawab & Keterangan</h6>
                        </div>

                        <div class="col-md-6">
                            <label for="supervisor" class="col-form-label fw-bold">Penanggung Jawab</label>
                            <select id="supervisor" name="supervisor" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('supervisor', $item->supervisor) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="documentation" class="col-form-label fw-bold">Dokumentasi / Foto</label>
                            <input type="file" id="documentation" name="documentation" class="form-control" accept="image/*,.pdf">
                            @if ($item->documentation)
                                <small class="text-muted">File saat ini: <a href="{{ asset('uploads/' . $item->documentation) }}" target="_blank">Lihat file</a></small>
                            @endif
                        </div>

                        <div class="col-12">
                            <label for="description" class="col-form-label fw-bold">Deskripsi / Catatan Tambahan <span class="text-danger">*</span></label>
                            <textarea id="description" name="description" class="form-control" rows="3" required>{{ old('description', $item->description) }}</textarea>
                        </div>

                        <div class="col-md-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2">Simpan Perubahan</button>
                            <a href="{{ route('asetTetap.index') }}" class="btn btn-secondary px-5 py-2 ms-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        var locations       = {!! json_encode($locations) !!};
        var previousGedung  = "{{ $prevLocation->office ?? '' }}";
        var previousLantai  = "{{ $prevLocation->floor  ?? '' }}";
        var previousRuangan = "{{ $prevLocation->room   ?? '' }}";

        var gedungSelect  = document.getElementById("gedung");
        var lantaiSelect  = document.getElementById("lantai");
        var ruanganSelect = document.getElementById("ruangan");

        function populateLantaiOptions() {
            var selectedGedung = gedungSelect.value;
            lantaiSelect.innerHTML = '<option value="">-- Pilih Lantai --</option>';
            var filteredLantai = [...new Set(locations.filter(l => l.office === selectedGedung).map(l => l.floor))];
            filteredLantai.forEach(l => {
                var opt = new Option(l || '-', l);
                if (l == previousLantai) opt.selected = true;
                lantaiSelect.add(opt);
            });
            populateRuanganOptions();
        }

        function populateRuanganOptions() {
            var selectedGedung = gedungSelect.value;
            var selectedLantai = lantaiSelect.value;
            ruanganSelect.innerHTML = '<option value="">-- Pilih Ruangan --</option>';
            var filteredRuangan = locations.filter(l => l.office === selectedGedung && l.floor == selectedLantai).map(l => l.room);
            filteredRuangan.forEach(r => {
                var opt = new Option(r || '-', r);
                if (r == previousRuangan) opt.selected = true;
                ruanganSelect.add(opt);
            });
        }

        gedungSelect.addEventListener("change", populateLantaiOptions);
        lantaiSelect.addEventListener("change", populateRuanganOptions);

        window.onload = function() {
            if (previousGedung) {
                gedungSelect.value = previousGedung;
                populateLantaiOptions();
            }
        };
    </script>
@endsection