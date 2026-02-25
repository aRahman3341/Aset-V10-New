@extends('layouts.app')
@section('title')
	Peminjaman - Monitoring Aset
@endsection
@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets\dist\air-datepicker\air-datepicker.css') }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Report Peminjaman</h1>
            </div>
        </div>
    </div>
	<div class="table-responsive">
		<div class="row">
			<div class="col-md-8">
				{{--card--}}
				<div class="card">
					<div class="card mt-4">
						<div class="card-header">
							<div class="row">
								<div class="col-md-6"><h1 class="h4 fw-bold">Data</h1>
								</div>
								<div class="col-md-6">
								<form action="{{ route('peminjaman.search') }}" method="POST" class="form-inline">
								@csrf
								<div class="input-group">
									<input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" value="{{ request()->input('query') }}" style="font-size: 12px">
									<div class="input-group-append">
										<button class="btn btn-outline-primary search-btn" ><i class="bi bi-search"></i></button>
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
									<th scope="col">Name</th>
									<th scope="col">Tanggal Pinjam</th>
									<th scope="col">Tanggal Kembali</th>
									<th scope="col">Operator</th>
									<th scope="col">Pengguna</th>
									<th scope="col">Status</th>
								</tr>
								</thead>
								<tbody style="background-color: #f4f8fb">
									@foreach ($loan as $item)
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $item->code }}</td>
												<td>{{ $item->material->name }}</td>
												<td>{{ $item->tgl_pinjam }}</td>
												<td>{{ $item->tgl_kembali }}</td>
												<td>{{ $item->user->name }}</td>
												<td>{{ $item->peminjam }}</td>
												<td>
													<div class="badge text-wrap" style="width: 6rem; background-color: {{ $item->status === 'Dikembalikan' ? '#59beda' : '#FFA726' }}; color: #ffffff">
														{{ $item->status }}
													  </div>
												</td>
											</tr>
									@endforeach
								</tbody>
							</table>
							<div class="my-5">
								{{ $loan->links() }}
							</div>
							<!-- End Default Table Example -->
					</div>
				</div>
						{{--endcard--}}
					<!-- End Default Table Example -->
			</div>
			<div class="col-md-4">
				{{--card--}}
				<div class="card">
					<div class="card mt-4">
						<div class="card-header">
							<h1 class="h4 fw-bold">Download Data</h1>
						</div>
					</div>
					<div class="card-body">

					<!-- Default Table -->
					@if (session('error'))
						<div class="alert alert-danger">
							{{ session('error') }}
						</div>
					@endif
					<form action="{{ route('peminjaman.report') }}" method="get">
						<div class="row d-flex justify-content-end">
							<div class="col-md-6">
								<div class="input-group">
									<input type="text" class="form-control" name="from_date" id="datepicker" placeholder="Range awal" value="{{ old('from_date') }}"  style="font-size: 14px">
									@error('from_date')
									<div class="text-danger">{{ $message }}</div>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="input-group">
									<input type="text" class="form-control" placeholder="Range akhir" name="to_date" id="datepicker1" value="{{ old('to_date') }}" style="font-size: 14px">
									@error('to_date')
									<div class="text-danger">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div><br>
						<div class="row">
							<div class="col-md-12">
								<div class="text-center">
									<button type="submit" class="btn btn-success" value="save"  style="font-size: 14px"><i class="bi bi-cloud-arrow-down-fill"></i> Download Range</button>
								</div>
							</div>
						</div><br>
					</form>

					<div class="row">
						<div class="col-md-12">
							<div class="text-center">
								<a href="{{ route('peminjaman.reportAll') }}" class="ml-2">
									<button type="button" class="btn btn-success" style="font-size: 14px"><i class="bi bi-cloud-arrow-down-fill"></i> Download All</button>
								</a>
							</div>
						</div>
					</div>
					</div>
				</div>
				{{--endcard--}}
			</div>
		</div>
	</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('assets\dist\air-datepicker\air-datepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Perform client-side validation
            var from_date = document.getElementById('datepicker').value;
            var to_date = document.getElementById('datepicker1').value;

            // Add your validation logic here
            var isValid = true;
            if (from_date === '') {
                isValid = false;
                // Display an error message for Rentang Awal
                document.getElementById('datepicker').classList.add('is-invalid');
                document.getElementById('datepicker').nextElementSibling.textContent = 'Rentang Awal harus diisi.';
            } else {
                document.getElementById('datepicker').classList.remove('is-invalid');
                document.getElementById('datepicker').nextElementSibling.textContent = '';
            }

            if (to_date === '') {
                isValid = false;
                // Display an error message for Rentang Akhir
                document.getElementById('datepicker1').classList.add('is-invalid');
                document.getElementById('datepicker1').nextElementSibling.textContent = 'Rentang Akhir harus diisi.';
            } else {
                document.getElementById('datepicker1').classList.remove('is-invalid');
                document.getElementById('datepicker1').nextElementSibling.textContent = '';
            }

            if (isValid) {
                // Display confirmation dialog using SweetAlert
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Anda yakin ingin mengirim data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form
                        document.getElementById('myForm').submit();
                    }
                });
            }
        });
    });

	$(function() {
        $("#datepicker").datepicker({
			dateFormat: "yy-mm-dd"
		});
    });

	$(function() {
        $("#datepicker1").datepicker({
			dateFormat: "yy-mm-dd"
		});
    });
</script>
@endsection
