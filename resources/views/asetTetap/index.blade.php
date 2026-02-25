@extends('layouts.app')
@section('content')

<style>
    .table-custom { font-size: 0.8rem; }
    .table-custom th {
        background-color: #f6f9ff;
        color: #012970;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.72rem;
        padding: 9px 7px;
        white-space: nowrap;
    }
    .table-custom td { vertical-align: middle; padding: 7px 7px; white-space: nowrap; }
    .page-title-custom {
        font-family: "Nunito", sans-serif;
        font-weight: 800;
        color: #012970;
        font-size: 1.35rem;
        letter-spacing: -0.3px;
        margin-bottom: 0;
    }
    .search-input { font-size: 0.9rem; border-right: none; }
    .search-btn { border-left: none; background-color: white; color: #4154f1; border-color: #ced4da; }
    .search-btn:hover { background-color: #f6f9ff; }
    .btn-action-group .btn { padding: 0.25rem 0.6rem; font-size: 0.8rem; }
    .badge-status { font-size: 0.7rem; }
</style>

<div id="location-data" data-locations="{{ json_encode($locations) }}"></div>

<main id="main" class="main" style="padding-top: 50px;">

    <div class="pagetitle mt-2 mb-4">
    <h1 class="page-title-custom text-uppercase">Persediaan Barang Habis Pakai</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Habis Pakai</li>
        </ol>
    </nav>
</div>

    <section class="section">
        <div class="card border-0 shadow-sm" style="border-radius: 8px;">
            <div class="card-body p-3">

                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-lg-4 col-md-6">
                        <form action="{{ route('asetTetap.search') }}" method="POST">
                            @csrf
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="query" class="form-control border-start-0 ps-0" placeholder="Cari Kode, NUP, atau Nama..." value="{{ request()->input('query') }}">
                                <a href="#" class="btn btn-outline-secondary" id="filterButton" title="Filter Lanjutan"><i class="bi bi-funnel"></i></a>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-8 col-md-6 text-md-end text-start">
                        <div class="btn-action-group">
                            <a href="{{ route('asetTetap.create') }}" class="btn btn-success btn-sm me-1 shadow-sm">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </a>
                            <a href="{{ route('asetTetap.import') }}" class="btn btn-success btn-sm me-1">
                                <i class="bi bi-file-earmark-arrow-down"></i> Import
                            </a>
                            <div class="d-inline-block border-start"></div>
                            <button onclick="exportAset('{{ route('asetTetap.export') }}')" class="btn btn-primary btn-sm me-1" title="Export Excel">
                                <i class="bi bi-file-earmark-arrow-up"></i> Export
                            </button>
                            <button onclick="generateQRCodes('{{ route('generate_qrcodes') }}')" class="btn btn-info btn-sm me-1" title="Cetak QR">
                                <i class="bi bi-qr-code"></i> QR
                            </button>
                            <button onclick="multiDelete('{{ route('asetTetap.multiDelete') }}')" class="btn btn-danger btn-sm" title="Hapus Masal">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <div id="filterFields" style="display: {{ request()->is('asetTetap/filter') ? 'block' : 'none' }};" class="mb-3 bg-light p-3 rounded">
                    @include('asetTetap.filter')
                </div>

                <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                    <form action="" method="post" class="form-produk">
                        @csrf
                        <table class="table table-sm table-hover table-bordered table-custom mb-0">
                            <thead style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th class="text-center" style="width: 40px;">
                                        <input type="checkbox" id="select_all">
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
                                    <th class="text-center" style="min-width: 80px;">Dokumentasi</th>
                                    <th class="text-center" style="min-width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($items as $item)
                                    @if ($item->status !== "Diserahkan")
                                        @php
                                            $location = $locations->firstWhere('id', $item->store_location);
                                            $cat = $categories->firstWhere('id', $item->category);
                                            $emp = $employees->firstWhere('id', $item->supervisor);
                                            $cond = $item->condition ?? 'Baik';
                                            $condColor = match($cond) {
                                                'Baik' => 'success',
                                                'Rusak Ringan' => 'warning',
                                                default => 'danger',
                                            };
                                            $statusColor = match($item->status ?? '') {
                                                'Dipakai' => 'success',
                                                'Maintenance' => 'warning',
                                                'Tidak Dipakai' => 'secondary',
                                                default => 'light',
                                            };
                                        @endphp
                                        <tr data-item-id="{{ $item->id }}">
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" name="id_aset[]" value="{{ $item->id }}" style="transform: scale(0.8);">
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
                                            <td>{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $item->tanggal_buku_pertama ? \Carbon\Carbon::parse($item->tanggal_buku_pertama)->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $item->tanggal_pengapusan ? \Carbon\Carbon::parse($item->tanggal_pengapusan)->format('d/m/Y') : '-' }}</td>

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
                                                    <a href="{{ asset('uploads/' . $item->documentation) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-0 px-1" title="Lihat Foto">
                                                        <i class="bi bi-image"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('asetTetap.edit', $item->id) }}" class="btn btn-warning btn-sm py-0" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-sm py-0 delete-button" data-item-id="{{ $item->id }}" title="Hapus">
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
        </div>
    </section>
</main>

@include('asetTetap.scane')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/indexaset.js') }}"></script>

<script>
    var gedungSelect = document.getElementById('gedung');
    var lantaiSelect = document.getElementById('lantai');
    var ruanganSelect = document.getElementById('ruangan');

    if (gedungSelect && lantaiSelect && ruanganSelect) {
        gedungSelect.addEventListener('change', populateLantaiSelect);
        lantaiSelect.addEventListener('change', populateRuanganSelect);
    }

    function populateLantaiSelect() {
        var selectedGedung = gedungSelect.value;
        lantaiSelect.innerHTML = '<option value="">Lantai</option>';
        var lantaiOptions = getUniqueOptionsByOffice(selectedGedung, 'floor');
        lantaiOptions.forEach(function(option) {
            var opt = document.createElement('option');
            opt.value = option; opt.textContent = option;
            lantaiSelect.appendChild(opt);
        });
        ruanganSelect.innerHTML = '<option value="">Ruangan</option>';
        ruanganSelect.disabled = true;
    }

    function populateRuanganSelect() {
        var selectedLantai = lantaiSelect.value;
        var selectedGedung = gedungSelect.value;
        ruanganSelect.innerHTML = '<option value="">Ruangan</option>';
        var ruanganOptions = getUniqueOptionsByFloor(selectedGedung, selectedLantai, 'room');
        ruanganOptions.forEach(function(option) {
            var opt = document.createElement('option');
            opt.value = option; opt.textContent = option;
            ruanganSelect.appendChild(opt);
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
            Swal.fire('Pilih Data', 'Centang minimal satu aset untuk mencetak QR.', 'info');
            return;
        }
        $('.form-produk').attr('target', '_blank').attr('action', url).submit();
    }

    function multiDelete(url) {
        if ($('input[name="id_aset[]"]:checked').length < 1) {
            Swal.fire('Pilih Data', 'Centang data yang ingin dihapus.', 'info');
            return;
        }
        Swal.fire({
            title: 'Hapus Terpilih?',
            text: "Data yang dicentang akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('.form-produk').attr('action', url).submit();
            }
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
                title: 'Hapus Item?',
                text: "Data ini tidak bisa dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/asetTetap/' + itemId,
                        type: 'DELETE',
                        data: { _token: csrfToken },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => { location.reload(); });
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            Swal.fire('Error', 'Gagal menghapus data. Cek Console.', 'error');
                        }
                    });
                }
            });
        });

        $('#select_all').change(function() {
            $('input[name="id_aset[]"]').prop('checked', this.checked);
        });

        $('input[name="id_aset[]"]').change(function() {
            if ($('input[name="id_aset[]"]:checked').length == $('input[name="id_aset[]"]').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });

        $('#filterButton').click(function(e) {
            e.preventDefault();
            $('#filterFields').slideToggle();
        });
    });
</script>

@endsection