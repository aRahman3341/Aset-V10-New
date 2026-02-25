@extends('layouts.app')

@section('content')
    <div id="location-data" data-locations="{{ json_encode($locations) }}"></div>

    <main id="main" class="main">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h4 fw-bold">Form Tambah Aset</h1>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="card shadow-sm">
                <div class=" mt-4">
                </div>
                <div class="card-body">
                    <form class="row g-3 needs-validation" action="{{ route('asetTetap.store') }}" method="POST"
                        enctype="multipart/form-data" id="your-form-id" novalidate>
                        @csrf
                        <div class="col-12">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-tag"></i> Identitas Barang</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="kode_barang" class="col-form-label fw-bold">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" id="kode_barang" name="kode_barang" class="form-control" placeholder="Contoh: 3050104..." required>
                            <div class="invalid-feedback">Kode Barang wajib diisi.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="nup" class="col-form-label fw-bold">NUP <span class="text-danger">*</span></label>
                            <input type="number" id="nup" name="nup" class="form-control" placeholder="No Urut Pendaftaran" required>
                            <div class="invalid-feedback">NUP wajib diisi.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="no_seri" class="col-form-label fw-bold">No Seri</label>
                            <input type="text" id="no_seri" name="no_seri" class="form-control" placeholder="Serial Number">
                        </div>

                        <div class="col-md-6">
                            <label for="nama_barang" class="col-form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" id="nama_barang" name="nama_barang" class="form-control" placeholder="Masukkan nama barang" required>
                            <div class="invalid-feedback">Nama Barang wajib diisi.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="merk" class="col-form-label fw-bold">Merk / Uraian Barang <span class="text-danger">*</span></label>
                            <input type="text" id="merk" name="merk" class="form-control" placeholder="Contoh: Asus, Honda, dll" required>
                            <div class="invalid-feedback">Merk/Uraian wajib diisi.</div>
                        </div>

                        {{-- ===== SECTION: KLASIFIKASI ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-grid"></i> Klasifikasi</h6>
                        </div>

                        <div class="col-md-3">
                            <label for="jenis_bmn" class="col-form-label fw-bold">Jenis BMN</label>
                            <input type="text" id="jenis_bmn" name="jenis_bmn" class="form-control" placeholder="Contoh: Alat Besar">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="col-form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select id="category" name="category" class="form-select" required>
                                <option value="" selected disabled>Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="kondisi" class="col-form-label fw-bold">Kondisi <span class="text-danger">*</span></label>
                            <select id="kondisi" name="kondisi" class="form-select" required>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="col-form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="Tidak Dipakai" selected>Tidak Dipakai</option>
                                <option value="Dipakai">Dipakai</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipe Aset <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_tetap" value="Tetap" checked>
                                    <label class="form-check-label" for="type_tetap">Tetap</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_bergerak" value="Bergerak">
                                    <label class="form-check-label" for="type_bergerak">Bergerak</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="intra_extra" class="col-form-label fw-bold">Intra / Extra</label>
                            <select id="intra_extra" name="intra_extra" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Intra">Intra</option>
                                <option value="Extra">Extra</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status_bmn" class="col-form-label fw-bold">Status BMN</label>
                            <select id="status_bmn" name="status_bmn" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>

                        {{-- ===== SECTION: NILAI & WAKTU ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-currency-dollar"></i> Nilai & Waktu</h6>
                        </div>

                        <div class="col-md-3">
                            <label for="nilai" class="col-form-label fw-bold">Nilai Perolehan (Rp)</label>
                            <input type="number" id="nilai" name="nilai" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label for="nilai_penyusutan" class="col-form-label fw-bold">Nilai Penyusutan (Rp)</label>
                            <input type="number" id="nilai_penyusutan" name="nilai_penyusutan" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label for="nilai_buku" class="col-form-label fw-bold">Nilai Buku (Rp)</label>
                            <input type="number" id="nilai_buku" name="nilai_buku" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label for="tahun" class="col-form-label fw-bold">Tahun Perolehan <span class="text-danger">*</span></label>
                            <input type="number" id="tahun" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label for="tanggal_perolehan" class="col-form-label fw-bold">Tanggal Perolehan</label>
                            <input type="date" id="tanggal_perolehan" name="tanggal_perolehan" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_buku_pertama" class="col-form-label fw-bold">Tgl Buku Pertama</label>
                            <input type="date" id="tanggal_buku_pertama" name="tanggal_buku_pertama" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_pengapusan" class="col-form-label fw-bold">Tgl Pengapusan</label>
                            <input type="date" id="tanggal_pengapusan" name="tanggal_pengapusan" class="form-control">
                        </div>

                        {{-- ===== SECTION: FISIK ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-box"></i> Data Fisik</h6>
                        </div>

                        <div class="col-md-3">
                            <label for="satuan" class="col-form-label fw-bold">Satuan</label>
                            <input type="text" id="satuan" name="satuan" class="form-control" placeholder="Unit/Pcs/Set">
                        </div>
                        <div class="col-md-3">
                            <label for="lifetime" class="col-form-label fw-bold">Umur Aset (Tahun)</label>
                            <input type="number" id="lifetime" name="lifetime" class="form-control" placeholder="Masa pakai">
                        </div>
                        <div class="col-md-6">
                            <label for="spek" class="col-form-label fw-bold">Spesifikasi</label>
                            <input type="text" id="spek" name="spek" class="form-control" placeholder="Detail spesifikasi barang">
                        </div>

                        {{-- ===== SECTION: LOKASI ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-geo-alt"></i> Lokasi Fisik</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="gedung" class="col-form-label fw-bold">Gedung <span class="text-danger">*</span></label>
                            <select id="gedung" name="gedung" class="form-select" required>
                                <option value="" disabled selected>Pilih Gedung</option>
                                @foreach ($gedungOptions as $gedung)
                                    <option value="{{ $gedung }}">{{ $gedung }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="lantai" class="col-form-label fw-bold">Lantai <span class="text-danger">*</span></label>
                            <select id="lantai" name="lantai" class="form-select" disabled required>
                                <option value="" disabled selected>Pilih Lantai</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ruangan" class="col-form-label fw-bold">Ruangan <span class="text-danger">*</span></label>
                            <select id="ruangan" name="ruangan" class="form-select" disabled required>
                                <option value="" disabled selected>Pilih Ruangan</option>
                            </select>
                        </div>

                        {{-- ===== SECTION: LOKASI BMN ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-building"></i> Data BMN / Satker</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="kode_satker" class="col-form-label fw-bold">Kode Satker</label>
                            <input type="text" id="kode_satker" name="kode_satker" class="form-control" placeholder="Kode Satuan Kerja">
                        </div>
                        <div class="col-md-4">
                            <label for="nama_satker" class="col-form-label fw-bold">Nama Satker</label>
                            <input type="text" id="nama_satker" name="nama_satker" class="form-control" placeholder="Nama Satuan Kerja">
                        </div>
                        <div class="col-md-4">
                            <label for="kode_register" class="col-form-label fw-bold">Kode Register</label>
                            <input type="text" id="kode_register" name="kode_register" class="form-control" placeholder="Kode Register BMN">
                        </div>

                        <div class="col-md-6">
                            <label for="nama_kl" class="col-form-label fw-bold">Nama K/L</label>
                            <input type="text" id="nama_kl" name="nama_kl" class="form-control" placeholder="Nama Kementerian/Lembaga">
                        </div>
                        <div class="col-md-6">
                            <label for="nama_e1" class="col-form-label fw-bold">Nama Unit (E1)</label>
                            <input type="text" id="nama_e1" name="nama_e1" class="form-control" placeholder="Nama Unit/Eselon 1">
                        </div>

                        <div class="col-md-4">
                            <label for="alamat" class="col-form-label fw-bold">Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="form-control" placeholder="Alamat aset">
                        </div>
                        <div class="col-md-4">
                            <label for="kab_kota" class="col-form-label fw-bold">Kab/Kota</label>
                            <input type="text" id="kab_kota" name="kab_kota" class="form-control" placeholder="Kabupaten/Kota">
                        </div>
                        <div class="col-md-4">
                            <label for="provinsi" class="col-form-label fw-bold">Provinsi</label>
                            <input type="text" id="provinsi" name="provinsi" class="form-control" placeholder="Provinsi">
                        </div>

                        {{-- ===== SECTION: DOKUMEN BMN ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-file-earmark-text"></i> Dokumen & Keterangan BMN</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="status_sertifikasi" class="col-form-label fw-bold">Status Sertifikasi</label>
                            <input type="text" id="status_sertifikasi" name="status_sertifikasi" class="form-control" placeholder="Contoh: Belum Bersertipikat">
                        </div>
                        <div class="col-md-4">
                            <label for="no_psp" class="col-form-label fw-bold">No PSP</label>
                            <input type="text" id="no_psp" name="no_psp" class="form-control" placeholder="Nomor PSP">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_psp" class="col-form-label fw-bold">Tanggal PSP</label>
                            <input type="date" id="tanggal_psp" name="tanggal_psp" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label for="status_penggunaan" class="col-form-label fw-bold">Status Penggunaan</label>
                            <input type="text" id="status_penggunaan" name="status_penggunaan" class="form-control" placeholder="Contoh: Digunakan sendiri untuk operasional">
                        </div>
                        <div class="col-md-3">
                            <label for="no_polisi" class="col-form-label fw-bold">No Polisi</label>
                            <input type="text" id="no_polisi" name="no_polisi" class="form-control" placeholder="Plat nomor kendaraan">
                        </div>
                        <div class="col-md-3">
                            <label for="no_stnk" class="col-form-label fw-bold">No STNK</label>
                            <input type="text" id="no_stnk" name="no_stnk" class="form-control" placeholder="Nomor STNK">
                        </div>

                        <div class="col-md-6">
                            <label for="nama_pengguna" class="col-form-label fw-bold">Nama Pengguna Aset</label>
                            <input type="text" id="nama_pengguna" name="nama_pengguna" class="form-control" placeholder="Nama pengguna / pemegang aset">
                        </div>

                        {{-- ===== SECTION: KALIBRASI ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-tools"></i> Kalibrasi</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="calibrate" class="col-form-label fw-bold">Perlu Kalibrasi?</label>
                            <select id="calibrate" name="calibrate" class="form-select">
                                <option value="0">Tidak Perlu</option>
                                <option value="1">Perlu Kalibrasi</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="last_kalibrasi" class="col-form-label fw-bold">Kalibrasi Terakhir</label>
                            <input type="date" id="last_kalibrasi" name="last_kalibrasi" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="schedule_kalibrasi" class="col-form-label fw-bold">Jadwal Kalibrasi</label>
                            <input type="date" id="schedule_kalibrasi" name="schedule_kalibrasi" class="form-control">
                        </div>

                        {{-- ===== SECTION: PENANGGUNG JAWAB & KETERANGAN ===== --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-1 mb-0"><i class="bi bi-person-check"></i> Penanggung Jawab & Keterangan</h6>
                        </div>

                        <div class="col-md-6">
                            <label for="supervisor" class="col-form-label fw-bold">Penanggung Jawab <span class="text-danger">*</span></label>
                            <select id="supervisor" name="supervisor" class="form-select" required>
                                <option value="" disabled selected>Pilih Penanggung Jawab</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="dokumentasi" class="col-form-label fw-bold">Dokumentasi (Foto)</label>
                            <input type="file" id="dokumentasi" name="dokumentasi" class="form-control" accept="image/*">
                        </div>

                        <div class="col-12">
                            <label for="keterangan" class="col-form-label fw-bold">Deskripsi / Keterangan <span class="text-danger">*</span></label>
                            <textarea id="keterangan" name="keterangan" class="form-control" rows="2" required placeholder="Catatan tambahan mengenai aset ini"></textarea>
                            <div class="invalid-feedback">Keterangan wajib diisi.</div>
                        </div>

                        <div class="col-12 mt-4 text-center">
                            <button type="submit" class="btn btn-primary px-5">Simpan Aset</button>
                            <a href="{{ route('asetTetap.index') }}" class="btn btn-secondary px-5">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Logika Dropdown Dinamis (Gedung -> Lantai -> Ruangan)
        var locations = {!! json_encode($locations) !!};
        var gedungSelect = document.getElementById("gedung");
        var lantaiSelect = document.getElementById("lantai");
        var ruanganSelect = document.getElementById("ruangan");

        gedungSelect.addEventListener("change", function() {
            var selectedGedung = this.value;
            lantaiSelect.innerHTML = '<option value="" disabled selected>Pilih Lantai</option>';
            ruanganSelect.innerHTML = '<option value="" disabled selected>Pilih Ruangan</option>';
            ruanganSelect.disabled = true;

            var filteredLantai = [...new Set(locations.filter(l => l.office === selectedGedung).map(l => l.floor))];
            filteredLantai.forEach(l => {
                lantaiSelect.add(new Option(l, l));
            });
            lantaiSelect.disabled = false;
        });

        lantaiSelect.addEventListener("change", function() {
            var selectedGedung = gedungSelect.value;
            var selectedLantai = this.value;
            ruanganSelect.innerHTML = '<option value="" disabled selected>Pilih Ruangan</option>';

            var filteredRuangan = locations.filter(l => l.office === selectedGedung && l.floor == selectedLantai).map(l => l.room);
            filteredRuangan.forEach(r => {
                ruanganSelect.add(new Option(r, r));
            });
            ruanganSelect.disabled = false;
        });

        // Bootstrap validation
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endsection