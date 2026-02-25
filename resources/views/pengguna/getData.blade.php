@extends('layouts.app')
@section('title')
	Pengguna - Monitoring Aset
@endsection
@section('content')
<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Pengguna</h1>
				<div class="col-md-6">
					<a href="{{ route('pengguna.add') }}" style="float: right">
						<button type="button" class="btn btn-success"><i class="bi bi-plus-square-fill"></i> Add</button>
					</a>
				</div>
            </div>
        </div>
    </div>

	<div class="table-responsive">
		{{--card--}}
		<div class="card">
			<div class="card mt-4">
				<div class="card-header">
					<div class="row">
						<div class="col-md-6">
						</div>
						<div class="col-md-6">
                            <form action="{{ route('pengguna.search') }}" method="POST" class="form-inline">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" value="{{ request()->input('query') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary search-btn" type="submit"><i class="bi bi-search"></i></button>
                                        <a href="#" class="btn btn-outline-primary filter-btn" id="filterButton"><i class="bi bi-filter"></i></a>
                                        <a href="#!" data-bs-toggle="modal" data-bs-target="#ModalAdd" style="float: right;"></a>
                                    </div>
                                </div>
                            </form>
						</div>
					</div>
                    <div class="col-md-12">
                        <div id="filterFields" style="display: {{ request()->is('pengguna/filter') ? ' block' : 'none' }};" class="form-inline mt-2">
                            <div class="card-body">
                                @include('pengguna.filter')
                            </div>    {{-- end of card body --}}
                        </div>
                    </div>
				</div>
			<div class="card-body">

			<!-- Default Table -->
			<div class="col-md-12">
				<table class="table table-bordered">
					<thead>
					<tr>
						<th scope="col">No</th>
						<th scope="col">NIP</th>
						<th scope="col">Name</th>
						<th scope="col">Email</th>
						<th scope="col">Jabatan</th>
						<th scope="col">Alamat</th>
						<th scope="col">Jenis Kelamin</th>
						<th scope="col">No Handphone</th>
						<th scope="col" class="">Action</th>
					</tr>
					</thead>
					<tbody style="background-color: #f4f8fb">
					{{--data karyawan--}}
						@foreach ($employee as $item)
						<tr>
						<td>{{ $loop->iteration }}</td>
						<td>{{ $item->nip }}</td>
						<td>{{ $item->name }}</td>
						<td>{{ $item->email }}</td>
						<td>{{ $item->jabatan }}</td>
						<td>{{ $item->alamat }}</td>
						<td>{{ $item->gender }}</td>
						<td>{{ $item->phone_number }}</td>
						<td>
							<div class="d-flex gap-1">
								{{-- Logika Tombol Edit --}}
								{{-- Jika jabatan adalah Operator, dia hanya bisa edit datanya sendiri --}}
								@if ($sess['jabatan'] === 'Operator')
									@if ($sess['id'] == $item->id && $sess['type'] == $item->type)
										<a href="{{ route('pengguna.edit', ['id' => $item->id, 'type' => $item->type]) }}" class="badge bg-warning text-dark">
											<i class="bi bi-pencil"></i>
										</a>
									@endif
								@else
									{{-- Jika Admin, bisa edit siapa saja --}}
									<a href="{{ route('pengguna.edit', ['id' => $item->id, 'type' => $item->type]) }}" class="badge bg-warning text-dark">
										<i class="bi bi-pencil"></i>
									</a>
									
									{{-- Tombol Hapus: Kirim ID dan Type agar tidak salah hapus --}}
									<form action="{{ route('pengguna.destroy', ['id' => $item->id, 'type' => $item->type]) }}" method="post" id="deleteForm{{ $item->id }}{{ $item->type }}">
										@csrf
										@method('DELETE')
										<button type="button" class="badge bg-danger border-0 delete-button" data-form-id="deleteForm{{ $item->id }}{{ $item->type }}">
											<i class="bi bi-trash"></i>
										</button>
									</form>
								@endif
							</div>
						</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
			<!-- End Default Table Example -->
			</div>
			<div class="card-footer">
				{{ $employee->links() }}
			</div>
		</div>
		{{--endcard--}}
</div>
</main>

<script src="{{ asset('js/indexaset.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
