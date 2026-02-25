@extends('layouts.app')

@section('content')
{{-- DataTables CSS --}}
<style>
    .table-responsive { overscroll-behavior-x: none; }
    html, body { overscroll-behavior-x: none; overscroll-behavior-y: auto; }
    .table-responsive { -webkit-overflow-scrolling: touch; overflow-x: auto; }

    .table-custom td:first-child, .table-custom th:first-child { font-weight: 600; color: #6c757d; }
    .table-custom { font-size: 0.85rem; }
    .table-custom th { background-color: #f6f9ff; color: #012970; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 12px 10px; border-bottom: 2px solid #ebeef4; }
    .table-custom td { vertical-align: middle; padding: 10px 10px; }
    
    .page-title-custom { font-family: "Nunito", sans-serif; font-weight: 800; color: #012970; font-size: 1.5rem; margin-bottom: 0; }

    .btn-action-group .btn { border-radius: 4px; font-weight: 600; }

    .badge-status-registered { background-color: #e1f7ef; color: #10b981; border: 1px solid #10b981; }
    .badge-status-pending { background-color: #fff4e5; color: #f59e0b; border: 1px solid #f59e0b; }
    
    .pagination {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        border-radius: 0.5rem !important;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4f46e5;
        background-color: white;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .page-item .page-link:hover {
        background-color: #f3f4f6;
        color: #4338ca;
        border-color: #c7d2fe;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .page-item.active .page-link {
        background-color: #4f46e5;
        color: white;
        border-color: #4f46e5;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
    }

    .page-item.disabled .page-link {
        color: #9ca3af;
        cursor: not-allowed;
        background-color: #f9fafb;
    }
</style>

<div class="pagetitle mt-2 mb-4">
    <h1 class="page-title-custom text-uppercase">Persediaan Barang Habis Pakai</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Habis Pakai</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            
            {{-- Toolbar Atas --}}
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-lg-5">
                    <form action="{{ route('items.index') }}" method="get" class="search-form d-flex align-items-center">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="query" class="form-control bg-light border-0" placeholder="Cari nama atau kode barang..." value="{{ request('query') }}">
                            <button type="button" class="btn btn-primary" id="filterButton">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-7 text-end">
                    <div class="btn-action-group">
                        <a href="{{ route('items.create') }}" class="btn btn-success shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Baru
                        </a>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#ModalImport" class="btn btn-outline-success ms-1">
                            <i class="bi bi-file-earmark-arrow-up"></i> Import
                        </button>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#ModalExport" class="btn btn-outline-primary ms-1">
                            <i class="bi bi-file-earmark-excel"></i> Export
                        </button>
                        <button onclick="generateQRCodes('{{ route('items.qrcodes') }}')" class="btn btn-outline-info ms-1" title="Cetak QR">
                            <i class="bi bi-qr-code"></i> QR
                        </button>
                        <button onclick="multiDelete()" class="btn btn-outline-danger ms-1" title="Hapus Masal">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>

            {{-- Area Filter Lanjutan --}}
            <div id="filterFields" style="display: {{ request()->routeIs('items.filter') ? 'block' : 'none' }};" class="mb-4 bg-light p-3 rounded-3 border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-secondary"><i class="bi bi-filter-left"></i> Filter Lanjutan</h6>
                    <a href="{{ route('items.index') }}" class="text-decoration-none small text-danger">Reset Filter</a>
                </div>
                @include('asetHabisPakai.filter')
            </div>

            <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                <form action="" method="post" class="form-items">
                    @csrf
                    <table class="table table-sm table-hover table-bordered table-custom mb-0" id="itemsTable">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th class="text-center" style="width: 40px;">
                                    <input type="checkbox" id="select_all">
                                </th>
                                <th class="text-center" width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Saldo</th>
                                <!-- Uncomment jika opsik ada di DB
                                <th class="text-center">Opsik</th> -->
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Dibuat Pada</th>
                                <th class="text-center">Diupdate Pada</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse ($items as $item)
                                <tr data-item-id="{{ $item->id }}">
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" name="id_items[]" value="{{ $item->id }}" style="transform: scale(0.8);">
                                    </td>
                                    <td class="text-center">{{ $no }}</td>
                                    <td><span class="fw-bold text-primary">{{ $item->code ?? '-' }}</span></td>
                                    <td>{{ Str::limit($item->name ?? '-', 50) }}</td>
                                    <td class="text-center">{{ $item->saldo ?? 0 }}</td>
                                    <!-- Uncomment jika opsik ada
                                    <td class="text-center">{{ $item->opsik ?? 0 }}</td> -->
                                    <td class="text-center">
                                        <span class="badge bg-secondary text-white" style="font-size: 0.7rem;">{{ $item->categories ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">{{ $item->satuan ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill {{ $item->status ? 'badge-status-registered' : 'badge-status-pending' }}">
                                            {{ $item->status ? 'Teregister' : 'Belum' }}
                                        </span>
                                    </td>
                                    <td class="text-center small">{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') : '-' }}</td>
                                    <td class="text-center small">{{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" id="form-{{ $item->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0 delete-button" data-form-id="form-{{ $item->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @php $no++; @endphp
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">Data barang tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>

            {{-- Pagination --}}
            <div class="mt-6 mb-8 flex justify-center">
                <nav aria-label="Page navigation">
                    <ul class="inline-flex -space-x-px rounded-md shadow-sm">
                        <!-- Previous -->
                        @if ($items->onFirstPage())
                            <li>
                                <span class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-md opacity-50 cursor-not-allowed">«</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $items->previousPageUrl() }}" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-100 hover:text-gray-700">«</a>
                            </li>
                        @endif

                        <!-- Nomor halaman -->
                        @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                            @if ($page == $items->currentPage())
                                <li>
                                    <span class="px-3 py-2 leading-tight text-white bg-indigo-600 border border-indigo-600">{{ $page }}</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        <!-- Next -->
                        @if ($items->hasMorePages())
                            <li>
                                <a href="{{ $items->nextPageUrl() }}" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-100 hover:text-gray-700">»</a>
                            </li>
                        @else
                            <li>
                                <span class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-md opacity-50 cursor-not-allowed">»</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>

            {{-- Footer Info: Ringkasan Saldo --}}
            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="p-3 border rounded bg-light">
                        <div class="small text-muted text-uppercase fw-bold">Total ATK</div>
                        <div class="h5 mb-0 fw-bold text-primary">{{ $countATK }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded bg-light">
                        <div class="small text-muted text-uppercase fw-bold">Total Rumah Tangga</div>
                        <div class="h5 mb-0 fw-bold text-success">{{ $countRT }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded bg-light">
                        <div class="small text-muted text-uppercase fw-bold">Total Laboratorium</div>
                        <div class="h5 mb-0 fw-bold text-info">{{ $countLab }}</div>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <p class="mb-0 small text-muted">Menampilkan {{ $items->count() }} dari {{ $items->total() }} data aset.</p>
            </div>
        </div>
    </div>
</section>

@include('asetHabisPakai.import')
@include('asetHabisPakai.export')

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
    $(document).ready(function() {

        $('#filterButton').click(function() {
            $('#filterFields').slideToggle('fast');
        });

        $('#select_all').change(function() {
            $('input[name="id_items[]"]').prop('checked', this.checked);
        });

        $('input[name="id_items[]"]').change(function() {
            if ($('input[name="id_items[]"]:checked').length == $('input[name="id_items[]"]').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });

        $('.delete-button').click(function() {
            const formId = $(this).data('form-id');
            Swal.fire({
                title: 'Hapus Barang?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#' + formId).submit();
                }
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
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $('.form-items').attr('action', '{{ route("items.multiDelete") }}')
                            .attr('method', 'post')
                            .submit();
        }
    });

}
    
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection