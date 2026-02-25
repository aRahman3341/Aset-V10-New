{{--@extends('layouts.app')
@section('title')
	Barang Keluar - Monitoring Aset
@endsection
@section('content')
<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Barang Keluar</h1>
            </div>
        </div>
    </div>
	<div class="table-responsive">--}}
		{{--card--}}
		{{--<div class="card">
			<div class="card-body">
				<div class="col-md-12">	
					<a href="{{ route('lampiran.pdf') }}">
						<button type="button" class="btn btn-success">Download data</button>
					</a>
				</div>--}}
			<!-- Default Table -->
				{{--<table class="table table-bordered">
					<thead>
					<tr>
						<th scope="col">No</th>
						<th scope="col">No Faktur</th>
						<th scope="col">Nama Aset</th>
						<th scope="col">Spesifikasi</th>
						<th scope="col">Qty</th>
						<th scope="col">Satuan</th>
					</tr>
					</thead>
					<tbody>
						@foreach ($asetout as $item)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>{{ $item->no_faktur }}</td>
									<td>{{ $item->name }}</td>
									<td>{{ $item->spek }}</td>
									<td>{{ $item->qty }}</td>
									<td>{{ $item->satuan }}</td>
								</tr>
						@endforeach
					</tbody>
				</table>--}}
			<!-- End Default Table Example -->
			{{--</div>
		</div>--}}
		{{--endcard--}}
{{--</div>
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
@endsection--}}

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Download Data</title>
	<style>
		table.static {
			position: relative;

			border: 1px solid #543535
		}
		.signatures {
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            border-bottom: 1px solid #000;
            margin-bottom: 20px;
        }

        .signature-label {
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }

		.center-image {
			display: block;
			margin: 0 auto;
		}

	</style>
</head>
<body>
		<div class="form-group">
			<link href="{{ asset('assets/img/head.png') }}" rel="icon">
			<img src="{{ asset('assets/img/head.JPG') }}" width="700" alt="" class="center-image">
			<p align="center"><b>PENGELUARAN BARANG</b></p>
			<p align="center"><b>
				{{--{!! $cetak->no_faktur !!}--}}
				{!! str_replace('^^', '/',$cetak->no_faktur) !!}
				@foreach ($cetak as $item)
				@endforeach
			</b></p>
			
			<table class="static" align="center" rules="all" border="1px" style="width: 95%">
				<thead>
				<tr>
					<th scope="col">No</th>
					<th scope="col">Nama Aset</th>
					<th scope="col">Qty</th>
					<th scope="col">Satuan</th>
				</tr>
				</thead>
				<tbody>
					{{--@dd($data)--}}
					@foreach ($data as $dat)
					@if ($dat->status == 'Disetujui')
					<tr>
						<td>{{ $loop->iteration }}</td>
						<td>{{ $dat->item->name }}</td>
						<td>{{ $dat->total_qty }}</td>
						<td>{{ $dat->item->satuan }}</td>
					</tr>
					@endif
					@endforeach
				</tbody>
			</table>
			<div class="signatures">
				<div class="signature-box">
					<div class="signature-label">
						<br><p>Yang Menyerahkan</p><br><br><br>
					</div>
				</div>
				<div class="signature-box">
					<div class="signature-label">
						<p>Bandung, ......................</p>
						<p>Yang Menerima</p><br><br><br>
					</div>
				</div>
			</div>
		</div>
	<script type="text/javascript">
		window.print();
	</script>
</body>
</html>