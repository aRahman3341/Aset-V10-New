@extends('layouts.app')

@section('content')
<main id="main" class="main">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Pengajuan Barang Habis Pakai</h1>
                <div class="col-md-6">
                    {{--<a href="#!" data-bs-toggle="modal" data-bs-target="#ModalImport" style="float: right;">
                        <button type="button" class="btn btn-success"><i class="bi bi-plus-square-fill" style="font-size: 14px"></i> Import Data</button>
                    </a>--}}
                    <a href="{{ route('addPengajuan') }}" style="float: right">
                        <button type="button" class="btn btn-success"><i class="bi bi-plus-square-fill" style="font-size: 14px"></i> Add</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        {{--card--}}
        <div class="card mt-4">
			<div class="card mt-4">
				<div class="card-header">
					<div class="row">
						<div class="col-md-6">
						</div>
						<div class="col-md-6">
                            <form action="" method="get">
								<div class="input-group">
                                        <input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" style="font-size: 12px">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-primary search-btn" ><i class="bi bi-search"></i></button>
                                            <a href="#" class="btn btn-outline-primary filter-btn" id="filterButton"><i class="bi bi-filter"></i></a>
                                        </div>
                                </div>
                            </form>
						</div>
					</div>
				</div>
			</div>
            <div class="card-body">

                <!-- Default Table -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Kode Barang</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col">Satuan</th>
                            <th scope="col">Saldo</th>
                            <th scope="col">Hasil Opsik</th>
                            <th scope="col">Pengaju</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: #f4f8fb">
                        @foreach ($ajuan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td>{{ $item->saldo }}</td>
                                <td>{{ $item->opsik }}</td>
                                <td>{{ $item->pengaju }}</td>
                                <td>
                                    <div class="badge text-wrap" style="width: 6rem; background-color:
										@if ($item->status === null)
											#fd7e14
										@elseif ($item->status === '0')
											#df0133
										@elseif ($item->status === '1')
											#20c997
										@else
											#fd7e14
										@endif
										; color: #ffffff">
										@if ($item->status === null)
											Diproses
										@elseif ($item->status === '0')
											Tidak Disetujui
										@elseif ($item->status === '1')
											Disetujui
										@endif
									</div>
                                </td>
                                <td>
                                    <a href="/items/{{ $item->id }}/edit" class="badge bg-warning text-dark" style="text-decoration: none;"><i class="bi bi-pencil"></i></a>
                                    <a class="badge bg-danger ml-2" style="text-decoration: none;">
                                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" id="form-{{ $item->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="badge bg-danger border-0 text-white delete-button" style="text-decoration: none;" data-form-id="form-{{ $item->id }}"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
				{{--{{ $ajuan->links() }}--}}
			</div>
        </div>

        {{--endcard--}}
    </div>

    {{--@include('asetHabisPakai.import')--}}
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-button');

        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const formId = this.getAttribute('data-form-id');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Anda yakin ingin menghapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });
    });
</script>
@endsection
