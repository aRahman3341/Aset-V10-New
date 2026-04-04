{{-- resources/views/asetTetap/table.blade.php --}}
<div class="table-responsive" style="max-height:600px; overflow-y:auto;">
    <table class="table table-hover mb-0" id="asetTable">
        <thead style="position:sticky; top:0; z-index:10;">
            <tr>
                <th class="text-center" style="width:38px">
                    <input type="checkbox" id="select_all" class="form-check-input">
                </th>
                <th class="text-center" style="width:46px">No</th>
                <th>Kode Barang</th>
                <th>NUP</th>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th>Tipe</th>
                <th>Jenis BMN</th>
                <th class="text-center">Kondisi</th>
                <th class="text-center">Status BMN</th>
                <th class="text-end">Nilai Perolehan (Rp)</th>
                <th class="text-end">Nilai Penyusutan (Rp)</th>
                <th class="text-end">Nilai Buku (Rp)</th>
                <th>Tgl Perolehan</th>
                <th>No PSP</th>
                <th>Kode Satker</th>
                <th>Nama Satker</th>
                <th>Alamat</th>
                <th class="text-center" style="width:80px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse ($items as $item)
                @php
                    {{-- Support nama kolom lama (kode_barang dll) maupun baru (code dll) --}}
                    $kode      = $item->kode_barang   ?? $item->code      ?? '-';
                    $nup       = $item->nup            ?? '-';
                    $nama      = $item->nama_barang    ?? $item->name      ?? '-';
                    $merk      = $item->merk           ?? $item->name_fix  ?? '-';
                    $tipe      = $item->tipe           ?? $item->type      ?? '-';
                    $jenisBmn  = $item->jenis_bmn      ?? '-';
                    $kondisi   = $item->kondisi        ?? $item->condition ?? 'Baik';
                    $statusBmn = $item->status_bmn     ?? '-';
                    $nilaiPerolehan   = $item->nilai_perolehan_pertama ?? $item->nilai_perolehan ?? $item->nilai ?? null;
                    $nilaiPenyusutan  = $item->nilai_penyusutan ?? null;
                    $nilaiBuku        = $item->nilai_buku ?? null;
                    $tglPerolehan     = $item->tanggal_perolehan ?? null;
                    $noPsp            = $item->no_psp      ?? '-';
                    $kodeSatker       = $item->kode_satker ?? '-';
                    $namaSatker       = $item->nama_satker ?? '-';
                    $alamat           = $item->alamat      ?? '-';

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
                    <td>{{ $nup }}</td>
                    <td><span class="fw-semibold">{{ Str::limit($nama, 40) }}</span></td>
                    <td><span class="text-muted">{{ Str::limit($merk, 25) }}</span></td>
                    <td><span class="text-muted">{{ $tipe }}</span></td>
                    <td><span class="badge-jenis">{{ Str::limit($jenisBmn, 30) }}</span></td>
                    <td class="text-center">
                        <span class="badge bg-{{ $condColor }}">{{ $kondisi }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $statusBmn === 'Aktif' ? 'success' : 'secondary' }}">
                            {{ $statusBmn }}
                        </span>
                    </td>
                    <td class="text-end">{{ $nilaiPerolehan ? number_format($nilaiPerolehan, 0, ',', '.') : '-' }}</td>
                    <td class="text-end">{{ $nilaiPenyusutan ? number_format($nilaiPenyusutan, 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-semibold" style="color:#1e3a5f;">
                        {{ $nilaiBuku ? number_format($nilaiBuku, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $noPsp }}</td>
                    <td>{{ $kodeSatker }}</td>
                    <td>{{ Str::limit($namaSatker, 25) }}</td>
                    <td>{{ Str::limit($alamat, 30) }}</td>
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
                    <td colspan="19" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Data tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>