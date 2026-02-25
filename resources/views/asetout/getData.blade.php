@extends('layouts.app')
@section('title')
	Barang Keluar - Monitoring Aset
@endsection
@section('content')
<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Barang Keluar</h1>
            </div>
        </div>
    </div>
	<div class="table-responsive">
		<div class="col-md-12">
		</div><br>
		<div class="row">
			<div class="col-md-7">
				{{--card--}}
				<div class="card">
					<div class="card mt-4">
						<div class="card-header">
							<div class="row">
								<div class="col-md-5"><h1 class="h4 fw-bold">Data</h1>
								</div>
								<div class="col-md-7">
									<form action="" method="get">
										<div class="input-group">
											<input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" style="font-size: 12px">
											@if ($sess['jabatan'] !== 'Operator')
											<div class="input-group-append">
												<button class="btn btn-outline-primary search-btn" ><i class="bi bi-search"></i></button>
											</div>
											@else
											<div class="input-group-append">
												<button class="btn btn-outline-primary search-btn" ><i class="bi bi-search"></i></button>
												<a href="{{ route('asetout.add') }}" style="float: right; padding-left: 5px">
													<button type="button" class="btn btn-success" style="font-size: 14px"><i class="bi bi-plus-square-fill"></i> Add</button>
												</a>
											</div>
											@endif
										</div>
									</form>
								</div>
								<div class="col-md-4"></div>
								<div class="col-md-8">
									<div id="filterFields" style="display: {{ request()->is('asetTetap/filter') ? ' block' : 'none' }};" class="form-inline mt-2">
										<div class="card-body">
											{{--@include('asetout.filter')--}}
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
									@if ($sess['jabatan'] !== 'Operator')
									<th scope="col">No Faktur</th>	
									@endif
									<th scope="col">Tanggal Pengajuan</th>
									<th scope="col">MAK</th>
									<th scope="col">Nomor Nota Dinas</th>
									<th scope="col">Status</th>
									<th scope="col" class="">Action</th>
								</tr>
							</thead>
							<tbody style="background-color: #f4f8fb">
								<!-- View (getData.blade.php) -->
								@foreach ($asetout as $item)
									@php
										$tgl = date('j F Y', strtotime($item->created_at));
									@endphp
										<tr>
											<td>{{ $loop->iteration }}</td>
											{{--<td>{{ $item->no_faktur }}</td>--}}
											@if ($sess['jabatan'] !== 'Operator')
											<td>{{ str_replace('^^', '/', $item->no_faktur) }}</td>
											@endif
											<td>{{ $tgl }}</td>
											<td>{{ str_replace('^^', '/', $item->mak) }}</td>
											<td>{{ str_replace('^^', '/', $item->no_nd) }}</td>
											<td>{{ $status }}</td>
											<td>
												@if ($item->no_faktur != null)
													<a href="{{ route('asetout.ajuan', $item->id) }}" class="badge bg-warning text-dark" style="text-decoration: none; font-size: 12px">
														<i class="bi bi-card-list"></i>
													</a>
												@endif
												@if ($sess['jabatan'] !== 'Operator')
													<a href="{{ route('asetout.edit', $item->id) }}" class="badge bg-warning text-dark" style="text-decoration: none; font-size: 12px"><i class="bi bi-pencil"></i></a>
												@else
													<a href="{{ route('asetout.editND', $item->id) }}" class="badge bg-warning text-dark" style="text-decoration: none; font-size: 12px"><i class="bi bi-pencil"></i></a>
												@endif
												<a class="badge bg-danger" style="text-decoration: none;  font-size: 10px"><i data-feather="edit"></i>
													<form action="{{ route('asetout.destroy', $item->id ) }}" method="post" id="deleteForm{{ $item->id }}">
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
						<!-- End Default Table Example -->
					</div>
					<div class="card-footer">
						{{--{{ $asetout->links() }}--}}
					</div>
				</div>
				{{--endcard--}}
			</div>

			<div class="col-md-5">
				@if ($sess['jabatan'] !== 'Operator')
					{{--Download Barang Keluar--}}
					<div class="col-sm-">
						{{--card--}}
						<div class="card">
							<div class="card mt-4">
								<div class="card-header">
									<div class="row">
										<div class="col-md-12"><h3 class="h5 fw-bold">Download Barang Keluar</h3>
										</div>
									</div>
								</div>
							</div>
							{{--<div class="card-header">
								<div class="col-md-12">
									<h3 class="h5 fw-bold">Download Barang Keluar</h3>
								</div>
							</div>--}}
							<div class="card-body">
							<div class="col-md-12">
								<select name="noFaktur" id="noFaktur" class="form-control namebox" style="font-size: 14px">
									<option value="">Pilih No Faktur</option>
									@foreach ($asetout->unique('no_faktur') as $item)
									{{--@foreach ($cetak->itemskeluar->unique('no_faktur') as $item)--}}
											{{--<option value="{{ $item->no_faktur }}">{{ $item->no_faktur }}</option>--}}
											@php
												$modifiedValue = str_replace('^^', '/', $item->no_faktur);
											@endphp
											<option value="{{ $item->no_faktur }}">{{ $modifiedValue }}</option>
									@endforeach
								</select>
							</div>
							<div class="card-footer">
								<div class="col-md-12">
									<div class="text-center">
										<a href="#" onclick="validateAndRedirect()" class="btn btn-success" style="font-size: 14px">
											<i class="bi bi-cloud-arrow-down-fill"></i> Download
										</a>
									</div>
								</div>
							</div>
	
							</div>
						</div>
						{{--endcard--}}
					</div>
				@else
					{{--Download Nota Dinas--}}
					<div class="col-sm-">
						{{--card--}}
						<div class="card">
							<div class="card mt-4">
								<div class="card-header">
									<div class="row">
										<div class="col-md-12"><h3 class="h5 fw-bold">Download Nota Dinas</h3>
										</div>
									</div>
								</div>
							</div>
							{{--<div class="card-header">
								<div class="col-md-12">
									<h3 class="h5 fw-bold">Download Barang Keluar</h3>
								</div>
							</div>--}}
							<div class="card-body">
							<div class="col-md-12">
								<select name="notaDinas" id="notaDinas" class="form-control namebox" style="font-size: 14px">
									<option value="">Pilih Nota Dinas</option>
									@foreach ($asetout->unique('no_nd') as $item)
									{{--@foreach ($cetak->itemskeluar->unique('no_faktur') as $item)--}}
											{{--<option value="{{ $item->no_faktur }}">{{ $item->no_faktur }}</option>--}}
											@php
												$modifiedValue = str_replace('^^', '/', $item->no_nd);
											@endphp
											<option value="{{ $item->no_nd }}">{{ $modifiedValue }}</option>
									@endforeach
								</select>
							</div>
							<div class="card-footer">
								<div class="col-md-12">
									<div class="text-center">
										<a href="#" onclick="validateAndRedirect1()" class="btn btn-success" style="font-size: 14px">
											<i class="bi bi-cloud-arrow-down-fill"></i> Download
										</a>
									</div>
								</div>
							</div>
	
							</div>
						</div>
						{{--endcard--}}
					</div>
				@endif
			</div>
		</div>
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

	$(document).ready(function() {
		$('.namebox').select2();
	});

	function validateAndRedirect() {
        var noFaktur = document.getElementById('noFaktur').value;

        if (noFaktur === '') {
            Swal.fire({
                title: 'Error',
                text: 'No Faktur harus dipilih.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        } else {
			// di local
            var href = '/asetout/cetak-faktur/' + noFaktur;
			// di Server
            //var href = '/ck_devs/monitor/asetout/cetak-faktur/' + noFaktur;
            window.open(href, '_blank');
        }
    }
	function validateAndRedirect1() {
        var noFaktur = document.getElementById('notaDinas').value;

        if (noFaktur === '') {
            Swal.fire({
                title: 'Error',
                text: 'No Faktur harus dipilih.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        } else {
			// di local
            var href = '/asetout/download/' + noFaktur;
			// di server
            //var href = '/ck_devs/monitor/asetout/download/' + noFaktur;
            window.open(href, '_blank');
        }
    }
</script>
@endsection
