<form action="{{ route('asetTetap.filter') }}" method="post">
    @csrf
    <div class="row g-2 align-items-end">

        <div class="col-md-3">
            <label class="form-label-custom mb-1" style="font-size:.78rem;font-weight:700;color:#4a5a6e;">Jenis BMN:</label>
            <select class="form-control form-control-sm" name="jenis_bmn">
                <option value="all">Semua Jenis</option>
                <option value="ALAT BESAR"                 {{ request()->input('jenis_bmn') == 'ALAT BESAR'                 ? 'selected' : '' }}>Alat Besar</option>
                <option value="ALAT ANGKUTAN BERMOTOR"     {{ request()->input('jenis_bmn') == 'ALAT ANGKUTAN BERMOTOR'     ? 'selected' : '' }}>Alat Angkutan Bermotor</option>
                <option value="BANGUNAN DAN GEDUNG"        {{ request()->input('jenis_bmn') == 'BANGUNAN DAN GEDUNG'        ? 'selected' : '' }}>Bangunan dan Gedung</option>
                <option value="JALAN DAN JEMBATAN"         {{ request()->input('jenis_bmn') == 'JALAN DAN JEMBATAN'         ? 'selected' : '' }}>Jalan dan Jembatan</option>
                <option value="MESIN PERALATAN KHUSUS TIK" {{ request()->input('jenis_bmn') == 'MESIN PERALATAN KHUSUS TIK' ? 'selected' : '' }}>Mesin Peralatan TIK</option>
                <option value="MESIN PERALATAN NON TIK"    {{ request()->input('jenis_bmn') == 'MESIN PERALATAN NON TIK'    ? 'selected' : '' }}>Mesin Peralatan Non TIK</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label-custom mb-1" style="font-size:.78rem;font-weight:700;color:#4a5a6e;">Kondisi:</label>
            <select class="form-control form-control-sm" name="kondisi">
                <option value="all">Semua</option>
                <option value="Baik"         {{ request()->input('kondisi') == 'Baik'         ? 'selected' : '' }}>Baik</option>
                <option value="Rusak Ringan" {{ request()->input('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="Rusak Berat"  {{ request()->input('kondisi') == 'Rusak Berat'  ? 'selected' : '' }}>Rusak Berat</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label-custom mb-1" style="font-size:.78rem;font-weight:700;color:#4a5a6e;">Status BMN:</label>
            <select class="form-control form-control-sm" name="status_bmn">
                <option value="all">Semua</option>
                <option value="Aktif"       {{ request()->input('status_bmn') == 'Aktif'       ? 'selected' : '' }}>Aktif</option>
                <option value="Tidak Aktif" {{ request()->input('status_bmn') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label-custom mb-1" style="font-size:.78rem;font-weight:700;color:#4a5a6e;">Tahun Perolehan:</label>
            <div class="d-flex gap-1">
                <select class="form-control form-control-sm" name="tahun_dari">
                    <option value="">Dari</option>
                    @foreach(range(date('Y'), 1980, -1) as $thn)
                        <option value="{{ $thn }}" {{ request()->input('tahun_dari') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
                <select class="form-control form-control-sm" name="tahun_sampai">
                    <option value="">Sampai</option>
                    @foreach(range(date('Y'), 1980, -1) as $thn)
                        <option value="{{ $thn }}" {{ request()->input('tahun_sampai') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-search me-1"></i> Terapkan
            </button>
        </div>

    </div>
</form>