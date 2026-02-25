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
			{{--card--}}
			<div class="card">
				<div class="card mt-4">
					<div class="card-header">
						<div class="row">
							{{--@dd($data->name)--}}
							@foreach ($barangOut as $item)
								{{--<div class="col-md-5"><h1 class="h4 fw-bold">{{ $item->no_faktur }}</h1>--}}
								<div class="col-md-5"><h1 class="h4 fw-bold">{{ str_replace('^^', '/', $item->no_faktur) }}</h1>
							@endforeach
							{{--<div class="col-md-5"><h1 class="h4 fw-bold">{{ $asetout->no_faktur }}</h1>--}}
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
							<th scope="col">Nama Barang</th>
							<th scope="col">Qty Pengajuan</th>
							<th scope="col">Satuan</th>
							<th scope="col">Qty Disetujui</th>
							<th scope="col">Pengaju</th>
							<th scope="col">Status</th>
							@if ($sess['jabatan'] !== 'Operator')
							<th scope="col" class="">Action</th>
							@endif
						</tr>
						</thead>
						<tbody style="background-color: #f4f8fb">
							@foreach ($data as $item)
									<tr>
										<td>{{ $loop->iteration }}</td>
										<td>{{ $item->name }}</td>
										<td>{{ $item->qty }}</td>
										<td>{{ $item->satuan }}</td>
										<td>{{ $item->total_qty }}</td>
										<td>{{ $item->user->name }}</td>
										<td>
											<div class="badge text-wrap" style="width: 6rem; background-color: {{ $item->status === 'Diproses' ? '#fd7e14' : ($item->status === 'Ditolak' ? '#eb2159' : '#20c997') }}; color: #ffffff">
												{{ $item->status }}
											</div>
										</td>
										@if ($sess['jabatan'] !== 'Operator')
										<td>
											<a href="#" data-bs-toggle="modal" data-bs-target="#ModalApprove-{{ $item->id }}" class="badge bg-warning border-0 text-dark" style="text-decoration: none;">
												<i class="bi bi-clipboard-check-fill" style="width: 100px"></i>
											</a>
											<a class="badge bg-danger" style="text-decoration: none;"><i data-feather="edit"></i>
												<form method="POST" action="{{ route('ajuan.reject', $item->id ) }}">
													@method('put')
													@csrf
													<input type="hidden" name="status" value="Ditolak">
													<button type="submit" value="save" class="bg-danger border-0 text-white"><i class="bi bi-x-lg"></i></button>
												</form>
											</a>
											{{--<a class="badge bg-danger" style="text-decoration: none;"><i data-feather="edit"></i>
												<form action="{{ route('asetout.destroy', $item->id ) }}" method="post" id="deleteForm{{ $item->id }}">
													@csrf
													@method('DELETE')
													<button type="button" class="bg-danger border-0 text-white delete-button" data-form-id="deleteForm{{ $item->id }}"><i class="bi bi-trash"></i></button>
												</form>							
											</a>--}}
										</td>
										@endif
									</tr>
							@endforeach
						</tbody>
					</table>
					<!-- End Default Table Example -->
				</div>
				<div class="card-footer">
					{{--{{ $asetout->links() }}--}}
				</div>
				{{--endcard--}}
			</div>
		</div>
		@include('asetout.updateAjuan')
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
            var href = '/asetout/cetak-faktur/' + noFaktur;
            window.open(href, '_blank');
        }
    }
</script>
@endsection