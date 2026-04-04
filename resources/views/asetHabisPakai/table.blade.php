{{-- resources/views/asetTetap/table.blade.php --}}
<div class="table-responsive" style="max-height:620px; overflow-y:auto;">
    <table class="table table-hover mb-0" id="asetTable">
        <thead style="position:sticky; top:0; z-index:10;">
            <tr>
                <th class="text-center" style="width:38px">
                    <input type="checkbox" id="select_all" class="form-check-input">
                </th>
                <th class="text-center" style="width:46px">No</th>
                <th style="min-width:130px">Kode Barang</th>
                <th style="min-width:50px">NUP</th>
                <th style="min-width:200px">Nama Barang</th>
                <th style="min-width:100px">Merk</th>
                <th style="min-width:80px">Tipe</th>
                <th style="min-width:180px">Jenis BMN</th>
                <th class="text-center" style="min-width:90px">Kondisi</th>
                <th class="text-center" style="min-width:90px">Status BMN</th>
                <th class="text-end" style="min-width:160px">Nilai Perolehan Pertama (Rp)</th>
                <th class="text-end" style="min-width:150px">Nilai Perolehan (Rp)</th>
                <th class="text-end" style="min-width:150px">Nilai Penyusutan (Rp)</th>
                <th class="text-end" style="min-width:130px">Nilai Buku (Rp)</th>
                <th style="min-width:110px">Tgl Perolehan</th>
                <th style="min-width:130px">Tgl Buku Pertama</th>
                <th style="min-width:80px">No PSP</th>
                <th style="min-width:110px">Tgl PSP</th>
                <th class="text-center" style="min-width:70px">Foto</th>
                <th class="text-center" style="width:80px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = ($items->currentPage() - 1) * $items->perPage() + 1; @endphp
            @forelse ($items as $item)
                @php
                    $kode      = $item->kode_barang   ?? $item->code      ?? '-';
                    $nup       = $item->nup            ?? '-';
                    $nama      = $item->nama_barang    ?? $item->name      ?? '-';
                    $merk      = $item->merk           ?? $item->name_fix  ?? '-';
                    $tipe      = $item->tipe           ?? $item->type      ?? '-';
                    $jenisBmn  = $item->jenis_bmn      ?? '-';
                    $kondisi   = $item->kondisi        ?? $item->condition ?? 'Baik';
                    $statusBmn = $item->status_bmn     ?? '-';

                    $nilaiPertama    = $item->nilai_perolehan_pertama ?? $item->nilai ?? null;
                    $nilaiPerolehan  = $item->nilai_perolehan ?? $item->nilai ?? null;
                    $nilaiPenyusutan = $item->nilai_penyusutan ?? null;
                    $nilaiBuku       = $item->nilai_buku ?? null;

                    $tglPerolehan   = $item->tanggal_perolehan    ?? null;
                    $tglBukuPertama = $item->tanggal_buku_pertama ?? null;
                    $noPsp          = $item->no_psp               ?? '-';
                    $tglPsp         = $item->tanggal_psp          ?? null;
                    $jumlahFoto     = $item->jumlah_foto          ?? 0;

                    $condColor = match($kondisi) {
                        'Baik'         => 'success',
                        'Rusak Ringan' => 'warning',
                        default        => 'danger',
                    };
                @endphp
                <tr>
                    <td class="text-center">
                        <input class="form-check-input" type="checkbox" name="id_aset[]" value="{{ $item->id }}">
                    </td>
                    <td class="text-center text-muted small">{{ $no }}</td>
                    <td><span class="fw-bold text-primary code-text">{{ $kode }}</span></td>
                    <td class="text-center">{{ $nup }}</td>
                    <td>
                        <span class="fw-semibold" title="{{ $nama }}">
                            {{ Str::limit($nama, 45) }}
                        </span>
                    </td>
                    <td><span class="text-muted">{{ $merk ?: '-' }}</span></td>
                    <td><span class="text-muted">{{ $tipe ?: '-' }}</span></td>
                    <td><span class="badge-jenis">{{ $jenisBmn }}</span></td>
                    <td class="text-center">
                        <span class="badge bg-{{ $condColor }}">{{ $kondisi }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $statusBmn === 'Aktif' ? 'success' : 'secondary' }}">
                            {{ $statusBmn }}
                        </span>
                    </td>
                    <td class="text-end">
                        {{ $nilaiPertama !== null ? number_format($nilaiPertama, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end">
                        {{ $nilaiPerolehan !== null ? number_format($nilaiPerolehan, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end">
                        {{ $nilaiPenyusutan !== null ? number_format($nilaiPenyusutan, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end fw-semibold" style="color:#1e3a5f;">
                        {{ $nilaiBuku !== null ? number_format($nilaiBuku, 0, ',', '.') : '-' }}
                    </td>
                    <td class="small text-muted">
                        {{ $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="small text-muted">
                        {{ $tglBukuPertama ? \Carbon\Carbon::parse($tglBukuPertama)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="small">{{ $noPsp }}</td>
                    <td class="small text-muted">
                        {{ $tglPsp ? \Carbon\Carbon::parse($tglPsp)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="text-center">
                        @if($jumlahFoto > 0)
                            <span class="badge bg-info">{{ $jumlahFoto }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="action-group">
                            <a href="{{ route('asetTetap.edit', $item->id) }}" class="abtn abtn-edit" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button type="button" class="abtn abtn-del delete-button"
                                    data-id="{{ $item->id }}" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @php $no++; @endphp
            @empty
                <tr>
                    <td colspan="20" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Data tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>