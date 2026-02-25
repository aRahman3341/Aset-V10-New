@extends('layouts.app')
@section('title')
	Peminjaman - Monitoring Aset
@endsection
@section('content')
<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Peminjaman</h1>
				<div class="col-md-6">
					@if ($sess['jabatan'] == 'admin')
						<a href="{{ route('peminjaman.create') }}" style="float: right">
							<button type="button" class="btn btn-success"><i class="bi bi-plus-square-fill"></i> Add</button>
						</a>	
					@endif
				</div>
            </div>
        </div>
    </div>
	<div class="table-responsive">

		{{--card--}}
		<div class="card mt-4">
			<div class="card mt-4">
				<div class="card-header">
					<div class="row">
						<div class="col-md-6">
						</div>
						<div class="col-md-6">
							{{--<form action="{{ route('peminjaman.search') }}" method="POST" class="form-inline">
								@csrf
								<div class="input-group">
									<input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" value="{{ request()->input('query') }}" style="font-size: 12px">
									<div class="input-group-append">
										<button class="btn btn-outline-primary search-btn" ><i class="bi bi-search"></i></button>
									</div>
								</div>
							</form>--}}
							<form action="" method="get">
                                <div class="input-group">
                                    <input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" style="font-size: 12px">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary search-btn" ><i class="bi bi-search"></i></button>
                                        <a href="#" class="btn btn-outline-primary filter-btn" id="filterButton"><i class="bi bi-filter"></i></a>
                                    </div>
                                </div>
                            </form>
							{{--<form action="" method="POST" class="form-inline">
								@csrf
								<div class="input-group">
									<input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" value="{{ request()->input('query') }}">
									<div class="input-group-append">
										<button class="btn btn-outline-primary search-btn" type="submit"><i class="bi bi-search"></i></button>
									</div>
								</div>
							</form>--}}
						</div>
                        <div class="col-md-4"></div>
                            <div class="col-md-8">
                                <div id="filterFields" style="display: {{ request()->is('asetTetap/filter') ? ' block' : 'none' }};" class="form-inline mt-2">
                                    <div class="card-body">
                                        @include('peminjaman.filter')
                                    </div>    {{-- end of card body --}}
                                </div>
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
						<th scope="col">Kode</th>
						<th scope="col">Name</th>
						<th scope="col">Tanggal Pinjam</th>
						<th scope="col">Tanggal Kembali</th>
						<th scope="col">Operator</th>
						<th scope="col">Peminjam</th>
						<th scope="col">Status</th>
						@if ($sess['jabatan'] == 'admin')
							<th scope="col" class="">Action</th>
						@endif
					</tr>
					</thead>
					@php
						$count = 1;
					@endphp
					<tbody style="background-color: #f4f8fb">
						@foreach ($loan as $item)
							@if ($item->status == 'Dipinjam')
								<tr>
									{{--<td>{{ $loop->iteration }}</td>--}}
									<td>{{ $count }}</td>
									<td>{{ $item->code }}</td>
									<td>{{ $item->material->name }}</td>
									<td>{{ $item->tgl_pinjam }}</td>
									<td>{{ $item->tgl_kembali }}</td>
									<td>{{ $item->user->name }}</td>
									<td>{{ $item->peminjam }}</td>
									<td>
										<div class="badge text-wrap" style="width: 6rem; background-color: #FFA726; color: #fffff">
											{{ $item->status }}
										</div>
									</td>
									@if ($sess['jabatan'] == 'admin')
										<td>
											<a href="{{ route('peminjaman.kembali', $item->id) }}" class="badge" style="text-decoration: none; background-color: #6610f2; color: #fffff">
												<i data-feather="edit"></i>Pengembalian
											</a>
											<a href="{{ route('peminjaman.edit', $item->id) }}" class="badge text-light" style="text-decoration: none;background-color: #FFA726; color: #fffff">
												<i class="bi bi-pencil"></i>
											</a>
											<a class="badge bg-danger" style="text-decoration: none;"><i data-feather="edit"></i>
												<form action="{{ route('peminjaman.destroy', $item->id ) }}" method="post" id="deleteForm{{ $item->id }}">
													@csrf
													@method('DELETE')
													<button type="button" class="bg-danger border-0 text-white delete-button" data-form-id="deleteForm{{ $item->id }}"><i class="bi bi-trash"></i></button>
												</form>
											</a>
										</td>
									@endif
								</tr>
								@php
									$count++;
								@endphp
							@elseif ($item->status == 'Dikembalikan')
								<!-- Hidden data -->
							@endif
						@endforeach
					</tbody>
				</table>
			<div class="card-footer">
				{{ $loan->links() }}
			</div>
			<!-- End Default Table Example -->
			</div>
		</div>
		{{--endcard--}}
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/indexaset.js') }}"></script>
<script src="{{ asset('assets\dist\air-datepicker\air-datepicker.js') }}"></script>
<script>
    $(function() {
        $("#datepicker").datepicker({
			dateFormat: "yy-mm-dd"
		});
    });

    // Tambahkan event listener untuk tombol hapus
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
