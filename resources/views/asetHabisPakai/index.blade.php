@extends('layouts.app')

@section('content')
<main id="main" class="main">

{{-- ── Page Title ── --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            <h1>Barang Habis Pakai</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Habis Pakai</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- ── Summary Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="summary-card summary-card-atk">
            <div class="summary-icon"><i class="bi bi-pen"></i></div>
            <div class="summary-body">
                <div class="summary-label">ATK</div>
                <div class="summary-value">{{ $countATK }}</div>
                <div class="summary-sub">item tercatat</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card summary-card-rt">
            <div class="summary-icon"><i class="bi bi-house-heart"></i></div>
            <div class="summary-body">
                <div class="summary-label">Rumah Tangga</div>
                <div class="summary-value">{{ $countRT }}</div>
                <div class="summary-sub">item tercatat</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card summary-card-lab">
            <div class="summary-icon"><i class="bi bi-eyedropper"></i></div>
            <div class="summary-body">
                <div class="summary-label">Laboratorium</div>
                <div class="summary-value">{{ $countLab }}</div>
                <div class="summary-sub">item tercatat</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Main Card ── --}}
<section class="section">
    <div class="card">
        <div class="card-body p-0">

            {{-- Toolbar --}}
            <div class="table-toolbar">
                <div class="toolbar-left">
                    <form action="{{ route('items.index') }}" method="get" class="search-wrap">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="query" class="search-input" placeholder="Cari nama atau kode barang..." value="{{ request('query') }}">
                        <button type="submit" class="search-btn">Cari</button>
                    </form>
                    <button class="filter-toggle-btn" id="filterButton">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
                <div class="toolbar-right">
                    <a href="{{ route('items.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Baru
                    </a>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#ModalImport" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-arrow-up"></i> Import
                    </button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#ModalExport" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-excel"></i> Export
                    </button>
                    <button onclick="generateQRCodes('{{ route('items.qrcodes') }}')" class="btn btn-outline-primary" title="Cetak QR">
                        <i class="bi bi-qr-code"></i>
                    </button>
                    <button onclick="multiDelete()" class="btn btn-outline-danger" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            {{-- Filter Lanjutan --}}
            <div id="filterFields" style="display: {{ request()->routeIs('items.filter') ? 'block' : 'none' }};" class="filter-panel">
                <div class="filter-panel-header">
                    <span><i class="bi bi-filter-left"></i> Filter Lanjutan</span>
                    <a href="{{ route('items.index') }}" class="filter-reset">Reset</a>
                </div>
                @include('asetHabisPakai.filter')
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <form action="" method="post" class="form-items">
                    @csrf
                    <table class="table table-hover mb-0" id="itemsTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:40px">
                                    <input type="checkbox" id="select_all" class="form-check-input">
                                </th>
                                <th class="text-center" style="width:50px">No</th>
                                <th>Kode</th>
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
                                    <td class="text-center text-muted">{{ $no }}</td>
                                    <td><span class="code-badge">{{ $item->code ?? '-' }}</span></td>
                                    <td>{{ Str::limit($item->name ?? '-', 50) }}</td>
                                    <td class="text-center fw-bold">{{ $item->saldo ?? 0 }}</td>
                                    <td class="text-center">
                                        @php
                                            $catClass = match($item->categories) {
                                                'ATK' => 'badge-cat-atk',
                                                'Rumah Tangga' => 'badge-cat-rt',
                                                'Laboratorium' => 'badge-cat-lab',
                                                default => 'badge-cat-default'
                                            };
                                        @endphp
                                        <span class="badge-cat {{ $catClass }}">{{ $item->categories ?? '-' }}</span>
                                    </td>
                                    <td class="text-center text-muted">{{ $item->satuan ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($item->status)
                                            <span class="status-badge status-registered">Teregister</span>
                                        @else
                                            <span class="status-badge status-pending">Belum</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted small">{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '-' }}</td>
                                    <td class="text-center text-muted small">{{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y') : '-' }}</td>
                                    <td class="text-center">
                                        <div class="action-group">
                                            <a href="{{ route('items.edit', $item->id) }}" class="action-btn action-edit" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" id="form-{{ $item->id }}" style="display:inline">
                                                @csrf @method('DELETE')
                                                <button type="button" class="action-btn action-delete delete-button" data-form-id="form-{{ $item->id }}" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @php $no++; @endphp
                            @empty
                                <tr>
                                    <td colspan="11" class="empty-state">
                                        <i class="bi bi-inbox" style="font-size:2rem;color:#c0c8d4"></i>
                                        <p class="mt-2 mb-0 text-muted">Data barang tidak ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>

            {{-- ── Footer: Pagination + Info ── --}}
            <div class="table-footer">

                {{-- Info kiri --}}
                <div class="table-info">
                    Menampilkan
                    <strong>{{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }}</strong>
                    dari <strong>{{ $items->total() }}</strong> data
                </div>

                {{-- Pagination tengah --}}
                <div class="pagination-wrap">
                    @if ($items->lastPage() > 1)
                    <nav>
                        <ul class="custom-pagination">

                            {{-- Prev --}}
                            <li class="{{ $items->onFirstPage() ? 'disabled' : '' }}">
                                @if ($items->onFirstPage())
                                    <span class="page-btn"><i class="bi bi-chevron-left"></i></span>
                                @else
                                    <a href="{{ $items->previousPageUrl() }}" class="page-btn"><i class="bi bi-chevron-left"></i></a>
                                @endif
                            </li>

                            {{-- Nomor halaman dengan ellipsis --}}
                            @php
                                $current = $items->currentPage();
                                $last    = $items->lastPage();
                                $range   = 2; // halaman di kiri-kanan current
                                $pages   = [];
                                for ($i = 1; $i <= $last; $i++) {
                                    if ($i == 1 || $i == $last || ($i >= $current - $range && $i <= $current + $range)) {
                                        $pages[] = $i;
                                    }
                                }
                            @endphp

                            @php $prev = null; @endphp
                            @foreach ($pages as $page)
                                @if ($prev !== null && $page - $prev > 1)
                                    <li class="ellipsis"><span>…</span></li>
                                @endif
                                <li class="{{ $page == $current ? 'active' : '' }}">
                                    <a href="{{ $items->url($page) }}" class="page-btn {{ $page == $current ? 'page-btn-active' : '' }}">
                                        {{ $page }}
                                    </a>
                                </li>
                                @php $prev = $page; @endphp
                            @endforeach

                            {{-- Next --}}
                            <li class="{{ !$items->hasMorePages() ? 'disabled' : '' }}">
                                @if ($items->hasMorePages())
                                    <a href="{{ $items->nextPageUrl() }}" class="page-btn"><i class="bi bi-chevron-right"></i></a>
                                @else
                                    <span class="page-btn"><i class="bi bi-chevron-right"></i></span>
                                @endif
                            </li>

                        </ul>
                    </nav>
                    @endif
                </div>

                {{-- Per-page kanan --}}
                <div class="page-size-info">
                    Halaman <strong>{{ $items->currentPage() }}</strong> / <strong>{{ $items->lastPage() }}</strong>
                </div>

            </div>
        </div>
    </div>
</section>

@include('asetHabisPakai.import')
@include('asetHabisPakai.export')

</main>

{{-- ── Styles ── --}}
<style>
    /* ── Page Title ── */
    .pagetitle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    .pagetitle-left { display: flex; align-items: center; gap: 14px; }
    .pagetitle-icon {
        width: 46px; height: 46px;
        background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.2rem;
        box-shadow: 0 4px 12px rgba(30,58,95,0.25);
        flex-shrink: 0;
    }
    .pagetitle h1 { font-size: 1.3rem; font-weight: 800; color: #1e3a5f; margin: 0 0 4px; }
    .pagetitle .breadcrumb { margin: 0; padding: 0; background: transparent; font-size: 0.78rem; }
    .pagetitle .breadcrumb-item a { color: #2d5a8e; text-decoration: none; }
    .pagetitle .breadcrumb-item.active { color: #8a96a3; }

    /* ── Summary Cards ── */
    .summary-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 14px;
        border: 1px solid rgba(30,58,95,0.07);
        box-shadow: 0 2px 12px rgba(30,58,95,0.06);
        background: #fff;
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .summary-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(30,58,95,0.11); }

    .summary-icon {
        width: 50px; height: 50px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .summary-card-atk .summary-icon  { background: rgba(65,84,241,0.10); color: #4154f1; }
    .summary-card-rt .summary-icon   { background: rgba(46,202,106,0.12); color: #1a9b54; }
    .summary-card-lab .summary-icon  { background: rgba(255,119,29,0.10); color: #e06010; }

    .summary-label { font-size: 0.73rem; font-weight: 700; color: #8a96a3; text-transform: uppercase; letter-spacing: 0.5px; }
    .summary-value { font-size: 1.8rem; font-weight: 800; color: #1e3a5f; line-height: 1.1; }
    .summary-sub   { font-size: 0.72rem; color: #a0aab4; }

    /* ── Toolbar ── */
    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(30,58,95,0.07);
        gap: 12px;
        flex-wrap: wrap;
    }
    .toolbar-left  { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .toolbar-right { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    .search-wrap {
        display: flex;
        align-items: center;
        background: #f4f6fb;
        border: 1.5px solid rgba(30,58,95,0.10);
        border-radius: 9px;
        overflow: hidden;
        height: 38px;
    }
    .search-icon { padding: 0 10px; color: #8a96a3; font-size: 0.9rem; }
    .search-input {
        border: none; background: transparent;
        font-size: 0.83rem; padding: 0 8px;
        width: 220px; outline: none; color: #1e3a5f;
    }
    .search-btn {
        background: #1e3a5f; color: #fff;
        border: none; padding: 0 14px;
        font-size: 0.8rem; font-weight: 600;
        height: 100%; cursor: pointer;
        transition: background 0.18s;
    }
    .search-btn:hover { background: #2d5a8e; }

    .filter-toggle-btn {
        height: 38px; padding: 0 14px;
        background: #f4f6fb;
        border: 1.5px solid rgba(30,58,95,0.10);
        border-radius: 9px;
        font-size: 0.82rem; font-weight: 600;
        color: #1e3a5f; cursor: pointer;
        transition: all 0.18s;
        display: flex; align-items: center; gap: 6px;
    }
    .filter-toggle-btn:hover { background: #e8ecf5; }

    /* Filter Panel */
    .filter-panel {
        background: #f8fafd;
        border-bottom: 1px solid rgba(30,58,95,0.08);
        padding: 16px 20px;
    }
    .filter-panel-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 12px;
        font-size: 0.82rem; font-weight: 700; color: #5a6a7e;
    }
    .filter-reset { font-size: 0.78rem; color: #dc2626; text-decoration: none; }
    .filter-reset:hover { text-decoration: underline; }

    /* ── Table ── */
    .table thead th {
        background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 12px;
        border: none;
        white-space: nowrap;
    }
    .table tbody td {
        padding: 10px 12px;
        vertical-align: middle;
        font-size: 0.84rem;
        border-bottom: 1px solid rgba(30,58,95,0.05);
    }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover td { background: rgba(30,58,95,0.025); }

    .code-badge {
        font-family: 'DM Mono', monospace;
        font-size: 0.78rem;
        font-weight: 600;
        color: #2d5a8e;
        background: rgba(45,90,142,0.08);
        padding: 3px 8px;
        border-radius: 5px;
    }

    /* Category badges */
    .badge-cat {
        display: inline-block;
        font-size: 0.7rem; font-weight: 700;
        padding: 3px 10px; border-radius: 20px;
    }
    .badge-cat-atk     { background: rgba(65,84,241,0.10); color: #4154f1; }
    .badge-cat-rt      { background: rgba(46,202,106,0.12); color: #1a9b54; }
    .badge-cat-lab     { background: rgba(255,119,29,0.10); color: #e06010; }
    .badge-cat-default { background: #f0f2f5; color: #6c757d; }

    /* Status badges */
    .status-badge {
        display: inline-block;
        font-size: 0.7rem; font-weight: 700;
        padding: 3px 10px; border-radius: 20px;
    }
    .status-registered { background: #e1f7ef; color: #10b981; }
    .status-pending    { background: #fff4e5; color: #f59e0b; }

    /* Action buttons */
    .action-group { display: flex; align-items: center; justify-content: center; gap: 6px; }
    .action-btn {
        width: 30px; height: 30px;
        border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.82rem;
        border: none; cursor: pointer;
        transition: all 0.15s;
        background: transparent;
    }
    .action-edit  { color: #c49a2a; } .action-edit:hover  { background: rgba(232,184,75,0.15); color: #a07820; }
    .action-delete{ color: #dc2626; } .action-delete:hover { background: rgba(220,38,38,0.10); }

    .empty-state { text-align: center; padding: 48px 0 !important; }

    /* ── Table Footer ── */
    .table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-top: 1px solid rgba(30,58,95,0.07);
        background: #fafbfd;
        border-radius: 0 0 16px 16px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .table-info {
        font-size: 0.8rem;
        color: #8a96a3;
        min-width: 200px;
    }
    .table-info strong { color: #1e3a5f; }

    .page-size-info {
        font-size: 0.8rem;
        color: #8a96a3;
        text-align: right;
        min-width: 120px;
    }
    .page-size-info strong { color: #1e3a5f; }

    /* ── Custom Pagination ── */
    .pagination-wrap { display: flex; justify-content: center; flex: 1; }

    .custom-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        list-style: none;
        margin: 0; padding: 0;
    }

    .custom-pagination li { display: flex; }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: 9px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1e3a5f;
        background: #fff;
        border: 1.5px solid rgba(30,58,95,0.12);
        text-decoration: none;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .page-btn:hover {
        background: #1e3a5f;
        color: #fff;
        border-color: #1e3a5f;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(30,58,95,0.2);
    }

    .page-btn-active {
        background: linear-gradient(135deg, #1e3a5f, #2d5a8e) !important;
        color: #fff !important;
        border-color: transparent !important;
        box-shadow: 0 3px 12px rgba(30,58,95,0.30);
        transform: none;
    }
    .page-btn-active:hover { transform: none; }

    .custom-pagination .disabled .page-btn {
        opacity: 0.35;
        cursor: not-allowed;
        pointer-events: none;
    }

    .custom-pagination .ellipsis span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        font-size: 0.85rem;
        color: #8a96a3;
        letter-spacing: 2px;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .table-toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-right { justify-content: flex-start; }
        .search-input  { width: 140px; }
        .table-footer  { flex-direction: column; align-items: center; gap: 12px; }
        .page-size-info, .table-info { text-align: center; min-width: unset; }
    }
</style>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#filterButton').click(function () {
            $('#filterFields').slideToggle('fast');
        });
        $('#select_all').change(function () {
            $('input[name="id_items[]"]').prop('checked', this.checked);
        });
        $('input[name="id_items[]"]').change(function () {
            $('#select_all').prop('checked',
                $('input[name="id_items[]"]:checked').length === $('input[name="id_items[]"]').length
            );
        });
        $('.delete-button').click(function () {
            const formId = $(this).data('form-id');
            Swal.fire({
                title: 'Hapus Barang?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e3a5f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) $('#' + formId).submit();
            });
        });
    });

    function generateQRCodes(url) {
        if ($('input[name="id_items[]"]:checked').length < 1) {
            Swal.fire('Pilih Data', 'Centang minimal satu barang untuk mencetak QR.', 'info');
            return;
        }
        $('.form-items').attr('target', '_blank').attr('action', url).attr('method', 'post').submit();
    }

    function multiDelete() {
        if ($('input[name="id_items[]"]:checked').length < 1) {
            Swal.fire('Pilih Data', 'Centang data yang ingin dihapus.', 'info');
            return;
        }
        Swal.fire({
            title: 'Hapus Terpilih?',
            text: "Data yang dicentang akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e3a5f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('.form-items').attr('action', '{{ route("items.multiDelete") }}')
                                .attr('method', 'post').submit();
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection