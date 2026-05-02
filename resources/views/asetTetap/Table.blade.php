{{-- resources/views/asetTetap/table.blade.php --}}
<div class="table-responsive" style="max-height:620px; overflow-y:auto;">
    <table class="table table-hover mb-0" id="asetTable">
        <thead style="position:sticky; top:0; z-index:10;">
            <tr>
                <th class="text-center" style="width:38px">
                    <input type="checkbox" id="select_all" class="form-check-input">
                </th>
                <th class="text-center" style="width:46px">No</th>
                <th style="min-width:140px">Kode Barang</th>
                <th style="min-width:50px">NUP</th>
                <th style="min-width:200px">Nama Barang</th>
                <th style="min-width:130px">Merk</th>
                <th style="min-width:130px">Tipe</th>
                <th style="min-width:200px">Jenis BMN</th>
                <th class="text-center" style="min-width:90px">Kondisi</th>
                <th class="text-center" style="min-width:90px">Status BMN</th>
                <th class="text-end" style="min-width:175px">Nilai Perolehan Pertama (Rp)</th>
                <th class="text-end" style="min-width:155px">Nilai Perolehan (Rp)</th>
                <th class="text-end" style="min-width:155px">Nilai Penyusutan (Rp)</th>
                <th class="text-end" style="min-width:135px">Nilai Buku (Rp)</th>
                <th style="min-width:115px">Tgl Perolehan</th>
                <th style="min-width:130px">Tgl Buku Pertama</th>
                <th style="min-width:170px">No PSP</th>
                <th style="min-width:110px">Tgl PSP</th>
                <th class="text-center" style="min-width:60px">Foto</th>
                <th class="text-center" style="width:80px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = ($items->currentPage() - 1) * $items->perPage() + 1; @endphp
            @forelse ($items as $item)
                @php
                    $kode            = $item->{'Kode Barang'}             ?? '-';
                    $nama            = $item->{'Nama Barang'}             ?? '-';
                    $jenisBmn        = $item->{'Jenis BMN'}               ?? '-';
                    $statusBmn       = $item->{'Status BMN'}              ?? '-';
                    $nilaiPertama    = $item->{'Nilai Perolehan Pertama'} ?? null;
                    $nilaiPerolehan  = $item->{'Nilai Perolehan'}         ?? null;
                    $nilaiPenyusutan = $item->{'Nilai Penyusutan'}        ?? null;
                    $nilaiBuku       = $item->{'Nilai Buku'}              ?? null;
                    $tglPerolehan    = $item->{'Tanggal Perolehan'}       ?? null;
                    $tglBukuPertama  = $item->{'Tanggal Buku Pertama'}    ?? null;
                    $noPsp           = $item->{'No PSP'}                  ?? '-';
                    $tglPsp          = $item->{'Tanggal PSP'}             ?? null;
                    $jumlahFoto      = $item->{'Jumlah Foto'}             ?? 0;
                    $nup             = $item->nup                         ?? '-';
                    $merk            = $item->merk                        ?? '-';
                    $tipe            = $item->tipe                        ?? '-';
                    $kondisi         = $item->kondisi                     ?? 'Baik';

                    $condColor = match($kondisi) {
                        'Baik'         => 'success',
                        'Rusak Ringan' => 'warning',
                        default        => 'danger',
                    };
                    $bmnColor = $statusBmn === 'Aktif' ? 'success' : 'secondary';
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
                        <span class="badge bg-{{ $bmnColor }}">{{ $statusBmn }}</span>
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
                    {{-- FOTO: badge yang bisa diklik --}}
                    <td class="text-center">
                        @if($jumlahFoto > 0)
                            <button type="button"
                                    class="foto-badge-btn"
                                    onclick="openPhotoModal({{ $item->id }}, '{{ addslashes(Str::limit($nama, 30)) }}')"
                                    title="Lihat {{ $jumlahFoto }} foto">
                                <i class="bi bi-images"></i>
                                {{ $jumlahFoto }}
                            </button>
                        @else
                            <span class="text-muted" style="font-size:.78rem;">-</span>
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

<style>
/* ── Foto Badge Button ── */
.foto-badge-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: linear-gradient(135deg, #0d6efd, #4154f1);
    color: #fff;
    border: none;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 0.72rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .18s;
    box-shadow: 0 2px 6px rgba(13,110,253,.3);
}
.foto-badge-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13,110,253,.4);
    background: linear-gradient(135deg, #4154f1, #0d6efd);
}
</style>