@extends('layouts.app')
@section('title')
	Category - Monitoring Aset
@endsection
@section('content')
<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Kategori</h1>
				<div class="col-md-6">
					<a href="#!" data-bs-toggle="modal" data-bs-target="#ModalAdd" style="float: right;">
						<button type="button" class="btn btn-success"><i class="bi bi-plus-square-fill"></i> Add</button>
					</a>
				</div>
            </div>
        </div>
    </div>

	<div class="table-responsive">
		{{--card--}}
		<div class="card" >
			<div class="card mt-4">
				<div class="card-header">
					<div class="row">
						<div class="col-md-6">
						</div>
						<div class="col-md-6">
                            <form action="{{ route('category.search') }}" method="POST" class="form-inline">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" value="{{ request()->input('query') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary search-btn" type="submit"><i class="bi bi-search"></i></button>
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
						<th scope="col">Kode</th>
						<th scope="col">Nama</th>
						<th scope="col" class="">Action</th>
					</tr>
					</thead>
					<tbody style="background-color: #f4f8fb">
						@foreach ($category as $item)
						<tr>
						<td>{{ $loop->iteration }}</td>
						<td>{{ $item->code }}</td>
						<td>{{ $item->name }}</td>
						<td>
							<a href="#!" data-bs-toggle="modal" data-bs-target="#ModalEdit-{{ $item->id }}" class="badge bg-warning text-dark" style="text-decoration: none;"><i class="bi bi-pencil"></i></a>
							<a class="badge bg-danger" style="text-decoration: none;"><i data-feather="edit"></i>
								<form action="{{ route('category.destroy', $item->id ) }}" method="post" id="deleteForm{{ $item->id }}">
									@csrf
									@method('DELETE')
									<button type="button" class="bg-danger border-0 text-white delete-button" data-form-id="deleteForm{{ $item->id }}"><i class="bi bi-trash"></i></button>
								</form>
							</a>
						</td>
						</tr>
					@endforeach
					</tbody>
				</table>
				<div class="card-footer">
					{{ $category->links() }}
				</div>
			<!-- End Default Table Example -->
			</div>
		</div>

		@include('category.add')
		@include('category.update')
		{{--endcard--}}
	</div>
</main>

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
