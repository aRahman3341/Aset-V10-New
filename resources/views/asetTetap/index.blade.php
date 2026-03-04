@extends('layouts.app')
@section('content')

<main id="main" class="main">

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-building"></i></div>
        <div>
            <h1>Aset Tetap</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Aset</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div id="location-data" data-locations="{{ json_encode($locations) }}"></div>

<section class="section">
<div class="main-card">

    {{-- ── Toolbar ── --}}
    <div class="table-toolbar">
        {{-- Kiri: Search + Filter --}}
        <div class="toolbar-left">
            <form action="{{ route('asetTetap.search') }}" method="POST" class="d-flex">
                @csrf
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="query" class="form-control border-start-0 ps-0"
                           placeholder="Cari Kode, NUP, atau Nama..." value="{{ request()->input('query') }}">
                    <button type="button" class="btn btn-outline-secondary" id="filterButton" title="Filter Lanjutan">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Kanan: Action buttons --}}
        <div class="toolbar-right">
            <a href="{{ route('asetTetap.create') }}" class="btn btn-success btn-sm me-1 shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah
            </a>
            <a href="{{ route('asetTetap.import') }}" class="btn btn-success btn-sm me-1">
                <i class="bi bi-file-earmark-arrow-down"></i> Import
            </a>
            <div class="d-inline-block border-start" style="height:24px; margin:0 4px;"></div>
            <button onclick="exportAset('{{ route('asetTetap.export') }}')" class="btn btn-primary btn-sm me-1">
                <i class="bi bi-file-earmark-arrow-up"></i> Export
            </button>
            <button onclick="generateQRCodes('{{ route('generate_qrcodes') }}')" class="btn btn-info btn-sm me-1">
                <i class="bi bi-qr-code"></i> QR
            </button>
            <button onclick="multiDelete('{{ route('asetTetap.multiDelete') }}')" class="btn btn-danger btn-sm">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
    </div>

    {{-- ── Filter Panel ── --}}
    <div id="filterFields" style="display: {{ request()->is('asetTetap/filter') ? 'block' : 'none' }};" class="filter-panel">
        <div class="filter-panel-header">
            <span><i class="bi bi-funnel-fill"></i> Filter Lanjutan</span>
        </div>
        @include('asetTetap.filter')
    </div>

    {{-- ── Table ── --}}
    <div class="table-responsive" style="max-height:650px; overflow-y:auto;">
        <form action="" method="post" class="form-produk">
            @csrf
            <table class="table table-sm table-hover table-custom mb-0">
                <thead style="position:sticky; top:0; z-index:10;">
                    <tr>
                        <th class="text-center" style="width:40px;">
                            <input type="checkbox" id="select_all" class="form-check-input">
                        </th>
                        <th class="text-center">No</th>
                        {{-- Identitas Utama --}}
                        <th>Kode Barang</th>
                        <th>NUP</th>
                        <th>Nama Barang</th>
                        <th>Nama Fix / Merk</th>
                        <th>No Seri</th>
                        {{-- Klasifikasi --}}
                        <th>Jenis BMN</th>
                        <th>Kategori</th>
                        <th>Tipe Aset</th>
                        <th class="text-center">Kondisi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Status BMN</th>
                        {{-- Nilai & Waktu --}}
                        <th class="text-center">Tahun</th>
                        <th>Nilai Perolehan (Rp)</th>
                        <th>Nilai Penyusutan (Rp)</th>
                        <th>Nilai Buku (Rp)</th>
                        <th>Tgl Perolehan</th>
                        <th>Tgl Buku Pertama</th>
                        <th>Tgl Pengapusan</th>
                        {{-- Fisik --}}
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>Umur (Thn)</th>
                        <th>Spesifikasi</th>
                        {{-- Lokasi Fisik --}}
                        <th>Gedung / Lantai / Ruang</th>
                        {{-- Lokasi BMN --}}
                        <th>Intra/Extra</th>
                        <th>Kode Satker</th>
                        <th>Nama Satker</th>
                        <th>Alamat</th>
                        <th>Kab/Kota</th>
                        <th>Provinsi</th>
                        <th>Nama K/L</th>
                        <th>Nama Unit</th>
                        <th>Kode Register</th>
                        {{-- Kalibrasi --}}
                        <th>Perlu Kalibrasi</th>
                        <th>Kalibrasi Terakhir</th>
                        <th>Jadwal Kalibrasi</th>
                        <th>Dikalibrasi Oleh</th>
                        {{-- Dokumen BMN --}}
                        <th>Status Sertifikasi</th>
                        <th>No PSP</th>
                        <th>Tgl PSP</th>
                        <th>Status Penggunaan</th>
                        <th>No Polisi</th>
                        <th>No STNK</th>
                        <th>Nama Pengguna</th>
                        {{-- Lain-lain --}}
                        <th>Penanggung Jawab</th>
                        <th>Deskripsi / Keterangan</th>
                        <th class="text-center" style="min-width:80px;">Dokumentasi</th>
                        <th class="text-center" style="min-width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($items as $item)
                        @if ($item->status !== "Diserahkan")
                            @php
                                $location  = $locations->firstWhere('id', $item->store_location);
                                $cat       = $categories->firstWhere('id', $item->category);
                                $emp       = $employees->firstWhere('id', $item->supervisor);
                                $cond      = $item->condition ?? 'Baik';
                                $condColor = match($cond) {
                                    'Baik'         => 'success',
                                    'Rusak Ringan' => 'warning',
                                    default        => 'danger',
                                };
                                $statusColor = match($item->status ?? '') {
                                    'Dipakai'      => 'success',
                                    'Maintenance'  => 'warning',
                                    'Tidak Dipakai'=> 'secondary',
                                    default        => 'light',
                                };
                            @endphp
                            <tr data-item-id="{{ $item->id }}">
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" name="id_aset[]" value="{{ $item->id }}" style="transform:scale(0.8);">
                                </td>
                                <td class="text-center">{{ $no }}</td>

                                {{-- Identitas Utama --}}
                                <td><span class="fw-bold text-primary">{{ $item->code ?? '-' }}</span></td>
                                <td>{{ $item->nup ?? '-' }}</td>
                                <td><div class="fw-semibold">{{ Str::limit($item->name ?? '-', 35) }}</div></td>
                                <td><span class="text-muted">{{ Str::limit($item->name_fix ?? '-', 25) }}</span></td>
                                <td>{{ $item->no_seri ?? '-' }}</td>

                                {{-- Klasifikasi --}}
                                <td>{{ $item->jenis_bmn ?? '-' }}</td>
                                <td>{{ $cat->name ?? '-' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->type ?? '-' }}</span></td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $condColor }}">{{ $cond }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $statusColor }}">{{ $item->status ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ ($item->status_bmn ?? '') === 'Aktif' ? 'success' : 'secondary' }}">
                                        {{ $item->status_bmn ?? '-' }}
                                    </span>
                                </td>

                                {{-- Nilai & Waktu --}}
                                <td class="text-center">{{ $item->years ?? '-' }}</td>
                                <td class="text-end">{{ $item->nilai_perolehan ? number_format($item->nilai_perolehan, 0, ',', '.') : number_format($item->nilai ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">{{ $item->nilai_penyusutan ? number_format($item->nilai_penyusutan, 0, ',', '.') : '-' }}</td>
                                <td class="text-end">{{ $item->nilai_buku ? number_format($item->nilai_buku, 0, ',', '.') : '-' }}</td>
                                <td>{{ $item->tanggal_perolehan       ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d/m/Y')       : '-' }}</td>
                                <td>{{ $item->tanggal_buku_pertama    ? \Carbon\Carbon::parse($item->tanggal_buku_pertama)->format('d/m/Y')    : '-' }}</td>
                                <td>{{ $item->tanggal_pengapusan      ? \Carbon\Carbon::parse($item->tanggal_pengapusan)->format('d/m/Y')      : '-' }}</td>

                                {{-- Fisik --}}
                                <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                                <td>{{ $item->satuan ?? '-' }}</td>
                                <td class="text-center">{{ $item->umur_aset ?? $item->life_time ?? '-' }}</td>
                                <td>{{ Str::limit($item->specification ?? '-', 30) }}</td>

                                {{-- Lokasi Fisik --}}
                                <td>
                                    @if ($location)
                                        <small>{{ $location->office ?? '' }} / {{ $location->floor ?? '' }} / {{ $location->room ?? '' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Lokasi BMN --}}
                                <td>{{ $item->intra_extra ?? '-' }}</td>
                                <td>{{ $item->kode_satker ?? '-' }}</td>
                                <td>{{ Str::limit($item->nama_satker ?? '-', 25) }}</td>
                                <td>{{ Str::limit($item->alamat ?? '-', 30) }}</td>
                                <td>{{ $item->kab_kota ?? '-' }}</td>
                                <td>{{ $item->provinsi ?? '-' }}</td>
                                <td>{{ Str::limit($item->nama_kl ?? '-', 25) }}</td>
                                <td>{{ Str::limit($item->nama_e1 ?? '-', 25) }}</td>
                                <td>{{ $item->kode_register ?? '-' }}</td>

                                {{-- Kalibrasi --}}
                                <td class="text-center">
                                    @if ($item->dikalibrasi == 1)
                                        <span class="badge bg-info">Perlu</span>
                                    @else
                                        <span class="badge bg-light text-dark border">Tidak</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->last_kalibrasi)
                                        {{ \Carbon\Carbon::parse($item->last_kalibrasi)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->schadule_kalibrasi)
                                        @php
                                            $jadwal = \Carbon\Carbon::parse($item->schadule_kalibrasi);
                                            $isLate = $jadwal->isPast();
                                        @endphp
                                        <span class="{{ $isLate ? 'text-danger fw-bold' : '' }}">
                                            {{ $jadwal->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $item->kalibrasi_by ?? '-' }}</td>

                                {{-- Dokumen BMN --}}
                                <td>{{ $item->status_sertifikasi ?? '-' }}</td>
                                <td>{{ $item->no_psp ?? '-' }}</td>
                                <td>{{ $item->tanggal_psp ? \Carbon\Carbon::parse($item->tanggal_psp)->format('d/m/Y') : '-' }}</td>
                                <td>{{ Str::limit($item->status_penggunaan ?? '-', 30) }}</td>
                                <td>{{ $item->no_polisi ?? '-' }}</td>
                                <td>{{ $item->no_stnk ?? '-' }}</td>
                                <td>{{ $item->nama_pengguna ?? '-' }}</td>

                                {{-- Lain-lain --}}
                                <td>{{ $emp->name ?? $item->supervisor ?? '-' }}</td>
                                <td>{{ Str::limit($item->description ?? '-', 30) }}</td>

                                {{-- Dokumentasi --}}
                                <td class="text-center">
                                    @if ($item->documentation)
                                        <a href="{{ asset('uploads/' . $item->documentation) }}" target="_blank"
                                           class="btn btn-outline-secondary btn-sm py-0 px-1" title="Lihat Foto">
                                            <i class="bi bi-image"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center">
                                    <div class="action-group">
                                        <a href="{{ route('asetTetap.edit', $item->id) }}" class="abtn abtn-edit" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="abtn abtn-del delete-button" data-item-id="{{ $item->id }}" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @php $no++; @endphp
                        @endif
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>

</div>
</section>
</main>

@include('asetTetap.scane')

<style>
/* ── Page Title ── */
.pagetitle { display:flex; align-items:center; margin-bottom:20px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon {
    width:44px; height:44px;
    background:linear-gradient(135deg,#012970,#4154f1);
    border-radius:12px; display:flex; align-items:center;
    justify-content:center; color:#fff; font-size:1.15rem;
    box-shadow:0 4px 12px rgba(1,41,112,0.22);
}
.pagetitle h1 { font-size:1.25rem; font-weight:800; color:#012970; margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0; padding:0; background:transparent; font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#4154f1; text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

/* ── Main card ── */
.main-card {
    background:#fff; border-radius:10px;
    border:1px solid rgba(1,41,112,0.07);
    box-shadow:0 2px 14px rgba(1,41,112,0.07); overflow:hidden;
}

/* ── Toolbar ── */
.table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px; border-bottom:1px solid rgba(1,41,112,0.07);
    gap:8px; flex-wrap:wrap;
}
.toolbar-left  { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.toolbar-right { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }

/* ── Filter panel ── */
.filter-panel {
    background:#f8fafd; border-bottom:1px solid rgba(1,41,112,0.07);
    padding:12px 18px;
}
.filter-panel-header {
    margin-bottom:10px; font-size:0.8rem; font-weight:700; color:#5a6a7e;
}

/* ── Table ── */
.table-custom { font-size:0.8rem; }
.table-custom thead th {
    background-color:#f6f9ff; color:#012970;
    font-weight:700; text-transform:uppercase;
    font-size:0.69rem; letter-spacing:0.5px;
    padding:10px 11px; white-space:nowrap;
    border-bottom:2px solid #e0e8f5;
}
.table-custom tbody td {
    padding:9px 11px; vertical-align:middle;
    font-size:0.8rem; white-space:nowrap;
    border-bottom:1px solid rgba(1,41,112,0.05);
}
.table-custom tbody tr:last-child td { border-bottom:none; }
.table-custom tbody tr:hover td { background:rgba(65,84,241,0.03); }

/* ── Action buttons ── */
.action-group { display:flex; align-items:center; justify-content:center; gap:4px; }
.abtn {
    width:28px; height:28px; border-radius:6px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:0.8rem; border:none; cursor:pointer;
    transition:all .15s; background:transparent;
}
.abtn-edit { color:#c49a2a; } .abtn-edit:hover { background:rgba(232,184,75,0.15); }
.abtn-del  { color:#dc2626; } .abtn-del:hover  { background:rgba(220,38,38,0.10); }

@media (max-width:768px) {
    .table-toolbar { flex-direction:column; align-items:stretch; }
    .toolbar-right { justify-content:flex-start; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/indexaset.js') }}"></script>

<script>
    var gedungSelect  = document.getElementById('gedung');
    var lantaiSelect  = document.getElementById('lantai');
    var ruanganSelect = document.getElementById('ruangan');

    if (gedungSelect && lantaiSelect && ruanganSelect) {
        gedungSelect.addEventListener('change', populateLantaiSelect);
        lantaiSelect.addEventListener('change', populateRuanganSelect);
    }

    function populateLantaiSelect() {
        var selectedGedung = gedungSelect.value;
        lantaiSelect.innerHTML = '<option value="">Lantai</option>';
        getUniqueOptionsByOffice(selectedGedung, 'floor').forEach(function(opt) {
            var o = document.createElement('option'); o.value = opt; o.textContent = opt;
            lantaiSelect.appendChild(o);
        });
        ruanganSelect.innerHTML = '<option value="">Ruangan</option>';
        ruanganSelect.disabled = true;
    }

    function populateRuanganSelect() {
        var selectedLantai = lantaiSelect.value;
        var selectedGedung = gedungSelect.value;
        ruanganSelect.innerHTML = '<option value="">Ruangan</option>';
        getUniqueOptionsByFloor(selectedGedung, selectedLantai, 'room').forEach(function(opt) {
            var o = document.createElement('option'); o.value = opt; o.textContent = opt;
            ruanganSelect.appendChild(o);
        });
    }

    function getUniqueOptionsByOffice(gedung, property) {
        var options = [];
        <?php foreach($locations as $location) { ?>
            if ("<?php echo $location->office; ?>" === gedung && options.indexOf("<?php echo $location->floor; ?>") === -1) {
                options.push("<?php echo $location->floor; ?>");
            }
        <?php } ?>
        return options;
    }

    function getUniqueOptionsByFloor(gedung, lantai, property) {
        var options = [];
        <?php foreach($locations as $location) { ?>
            if ("<?php echo $location->floor; ?>" === lantai && "<?php echo $location->office; ?>" === gedung && options.indexOf("<?php echo $location->room; ?>") === -1) {
                options.push("<?php echo $location->room; ?>");
            }
        <?php } ?>
        return options;
    }

    function generateQRCodes(url) {
        if ($('input[name="id_aset[]"]:checked').length < 1) {
            Swal.fire('Pilih Data', 'Centang minimal satu aset untuk mencetak QR.', 'info'); return;
        }
        $('.form-produk').attr('target', '_blank').attr('action', url).submit();
    }

    function multiDelete(url) {
        if ($('input[name="id_aset[]"]:checked').length < 1) {
            Swal.fire('Pilih Data', 'Centang data yang ingin dihapus.', 'info'); return;
        }
        Swal.fire({
            title: 'Hapus Terpilih?',
            text: "Data yang dicentang akan dihapus permanen.",
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#012970', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) $('.form-produk').attr('action', url).submit();
        });
    }

    function exportAset(url) {
        $('.form-produk').attr('action', url).submit();
    }

    $(document).ready(function() {
        var csrfToken = document.querySelector('input[name="_token"]').value;

        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();
            var itemId = $(this).data('item-id');
            Swal.fire({
                title: 'Hapus Item?', text: "Data ini tidak bisa dikembalikan.",
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#012970', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/asetTetap/' + itemId,
                        type: 'DELETE',
                        data: { _token: csrfToken },
                        success: function() {
                            Swal.fire({ icon: 'success', title: 'Terhapus', showConfirmButton: false, timer: 1000 })
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            Swal.fire('Error', 'Gagal menghapus data.', 'error');
                        }
                    });
                }
            });
        });

        $('#select_all').change(function() {
            $('input[name="id_aset[]"]').prop('checked', this.checked);
        });

        $('input[name="id_aset[]"]').change(function() {
            var all   = $('input[name="id_aset[]"]').length;
            var checked = $('input[name="id_aset[]"]:checked').length;
            $('#select_all').prop('checked', all === checked);
        });

        $('#filterButton').click(function(e) {
            e.preventDefault();
            $('#filterFields').slideToggle();
        });
    });
</script>

@endsection