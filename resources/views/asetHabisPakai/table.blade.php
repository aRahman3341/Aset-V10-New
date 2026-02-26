{{-- resources/views/asetHabisPakai/_table.blade.php --}}
<div class="table-responsive">
    <form action="" method="post" class="form-items" id="mainForm">
        @csrf
        <table class="table table-hover mb-0" id="itemsTable">
            <thead>
                <tr>
                    <th class="text-center" style="width:40px">
                        <input type="checkbox" id="select_all" class="form-check-input">
                    </th>
                    <th class="text-center" style="width:50px">No</th>
                    <th style="min-width:140px">Kode</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Saldo</th>
                    <th class="text-center">Kategori</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Dibuat</th>
                    <th class="text-center">Diupdate</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = (($items->currentPage() - 1) * $items->perPage()) + 1; @endphp
                @forelse ($items as $item)
                    <tr>
                        <td class="text-center">
                            <input class="form-check-input" type="checkbox" name="id_items[]" value="{{ $item->id }}">
                        </td>
                        <td class="text-center text-muted small">{{ $no }}</td>
                        <td>
                            {{-- data-raw menyimpan kode asli, JS akan memformat --}}
                            <span class="code-badge" data-raw="{{ $item->code ?? '' }}">{{ $item->code ?? '-' }}</span>
                        </td>
                        <td>{{ Str::limit($item->name ?? '-', 50) }}</td>
                        <td class="text-center fw-bold">{{ $item->saldo ?? 0 }}</td>
                        <td class="text-center">
                            @php
                                $catClass = match($item->categories ?? '') {
                                    'ATK'          => 'bcat-atk',
                                    'Rumah Tangga' => 'bcat-rt',
                                    'Laboratorium' => 'bcat-lab',
                                    default        => 'bcat-default'
                                };
                            @endphp
                            <span class="bcat {{ $catClass }}">{{ $item->categories ?? '-' }}</span>
                        </td>
                        <td class="text-center text-muted">{{ $item->satuan ?? '-' }}</td>
                        <td class="text-center">
                            @if($item->status)
                                <span class="bstatus bstatus-ok">Teregister</span>
                            @else
                                <span class="bstatus bstatus-pending">Belum</span>
                            @endif
                        </td>
                        <td class="text-center small text-muted">{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '-' }}</td>
                        <td class="text-center small text-muted">{{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y') : '-' }}</td>
                        <td class="text-center">
                            <div class="action-group">
                                <a href="{{ route('items.edit', $item->id) }}" class="abtn abtn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('items.destroy', $item->id) }}" method="POST" id="form-{{ $item->id }}" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="abtn abtn-del delete-button"
                                            data-form-id="form-{{ $item->id }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @php $no++; @endphp
                @empty
                    <tr>
                        <td colspan="11" class="empty-row">
                            <i class="bi bi-inbox"></i>
                            <p>Data barang tidak ditemukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </form>
</div>