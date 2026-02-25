@extends('layouts.app')
@section('title')
	Aset Keluar - Monitoring Aset
@endsection
@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets\dist\air-datepicker\air-datepicker.css') }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Aset Keluar</h1>
            </div>
        </div>
    </div>
	<div class="table-responsive">
		<div class="col-md-12">


		</div><br>
		<div class="row">
			<div class="col-md-8">
				{{--card--}}
				<div class="card">
					<div class="card mt-4">
						<div class="card-header">
							<div class="row">
								<div class="col-md-5"><h1 class="h4 fw-bold">Data</h1>
								</div>
								<div class="col-md-7">
                                    <form action="{{ route('asetkeluar.search') }}" method="POST" class="form-inline">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" value="{{ request()->input('query') }}" style="font-size: 12px">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary search-btn" ><i class="bi bi-search"></i></button>
                                                {{-- <a href="#" class="btn btn-outline-primary filter-btn" id="filterButton"><i class="bi bi-filter"></i></a> --}}
                                                <a href="{{ route('asetkeluar.add') }}" style="float: right; padding-left: 5px">
                                                    <button type="button" class="btn btn-success" style="font-size: 14px"><i class="bi bi-plus-square-fill"></i> Add</button>
                                                </a>
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
							<th scope="col">Nomor</th>
							<th scope="col">Nama Aset</th>
							<th scope="col">Pihak Kesatu</th>
							<th scope="col">Pihak Kedua</th>
							<th scope="col">Kepada</th>
							<th scope="col">Tanggal</th>
							<th scope="col" class="">Action</th>
						</tr>
						</thead>
						<tbody style="background-color: #f4f8fb">
							@foreach ($asetkeluar as $item)
									<tr>
										<td>{{ $loop->iteration }}</td>
										<td>{{ $item->nomor }}</td>
                                        <td>
                                            @foreach ($asets[$item->id] as $relatedMaterial)
                                                - {{ $relatedMaterial->name . ' (' . $relatedMaterial->code . '), (' . $relatedMaterial->nup . ')'}}<br>
                                            @endforeach
                                        </td>
										{{-- <td>{{ $item->asetskeluar->name }}</td>
										<td>{{ $item->asetskeluar->code }}</td>
										<td>{{ $item->asetskeluar->nup }}</td> --}}
										<td>{{ $item->pihakSatu }}</td>
										<td>{{ $item->pihakDua }}</td>
										<td>{{ $item->kepada }}</td>
                                        @php
                                            setlocale(LC_TIME, 'id_ID.utf8');
                                        @endphp
										<td>{{ $item->created_at->formatLocalized('%e %B %Y') }}</td>
										<td>
											<a href="{{ route('asetkeluar.edit', $item->id) }}" class="badge bg-warning text-dark" style="text-decoration: none;">
												<i class="bi bi-pencil"></i>
											</a>
											<a class="badge bg-danger" style="text-decoration: none;"><i data-feather="edit"></i>
												<form action="{{ route('asetkeluar.destroy', $item->id ) }}" method="post" id="deleteForm{{ $item->id }}">
													@csrf
													@method('DELETE')
													<button type="button" class="bg-danger border-0 text-white delete-button" data-form-id="deleteForm{{ $item->id }}"><i class="bi bi-trash"></i></button>
												</form>
											</a>
                                            <a href="{{ route('asetkeluar.download', $item->id) }}" class="badge bg-primary" style="text-decoration: none;">
												<i class="bi bi-download"></i>
											</a>
										</td>
									</tr>
							@endforeach
						</tbody>
					</table>
					<!-- End Default Table Example -->
					</div>
					<div class="card-footer">
						{{ $asetkeluar->links() }}
					</div>
				</div>
				{{--endcard--}}
			</div>

			{{--Download Aset Keluar--}}
			<div class="col-sm-4">
				{{--card--}}
				<div class="card">
					<div class="card mt-4">
						<div class="card-header">
							<h1 class="h4 fw-bold">Download Aset Keluar</h1>
						</div>
					</div>
					<div class="card-body">

					<!-- Default Table -->
					@if (session('error'))
						<div class="alert alert-danger">
							{{ session('error') }}
						</div>
					@endif
					<form action="{{ route('asetkeluar.export') }}" method="get">
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

					</div>
				</div>
				{{--endcard--}}
			</div>
		</div>

</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('assets\dist\air-datepicker\air-datepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
