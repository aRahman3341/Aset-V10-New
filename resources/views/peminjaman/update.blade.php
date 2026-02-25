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
                <h1 class="h4 fw-bold">Peminjaman</h1>
            </div>
        </div>
    </div>

	<div class="table-responsive">
		{{--card--}}
		<div class="card">
			<div class="card mt-4">
				<div class="card-header" >
					<div class="row" style="margin-top: -18px; margin-bottom: -18px">
						<div class="col-md-6">
							<h5 class="card-title" >Form Pengembalian</h5>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				{{--<form class="row g-3" method="POST" action="/peminjaman/kembali/{{ $loan->id }}">--}}
				<form class="row g-3" method="POST" action="{{ route('peminjaman.pengembalian',$loan->id) }}">
					@csrf
					@method('PUT')
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Nama Barang</label>
						<input type="text" class="form-control" name="material_display" id="inputNanme4" value="{{ ($loan->material->name) }}" disabled>
						<input type="hidden" class="form-control" name="material_id" id="inputNanme4" value="{{ ($loan->material_id) }}">
						{{--@error('material_id')
						<div class="text-danger">{{ $message }}</div>
						@enderror--}}
					</div>
					<div class="col-md-6">
						<label for="inputNanme4" class="form-label fw-bold">Tanggal Pinjam</label>
						<input type="text" class="form-control" name="tgl_pinjam" id="datepicker" value="{{ ($loan->tgl_pinjam) }}" disabled>
						<input type="hidden" class="form-control" name="tgl_pinjam" id="datepicker" value="{{ ($loan->tgl_pinjam) }}">
						{{--@error('tgl_pinjam')
						<div class="text-danger">{{ $message }}</div>
						@enderror--}}
					</div>
					<div class="col-md-6">
						<label for="inputNanme4" class="form-label fw-bold" fw-bold>Tanggal Kembali</label>
						<input type="text" class="form-control" name="tgl_kembali" id="datepicker1" value="{{ ($loan->tgl_kembali) }}">
						@error('tgl_kembali')
						<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					{{--<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Operator</label>
						<select name="employee_id" id="operator" class="form-control opratorbox">
							<option value="">Pilih Operator</option>
							@foreach ($employe as $item)
                                @if ($item->jabatan == 'Operator')
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endif
							@endforeach
						</select>
						@error('employee_id')
						<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>--}}
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Peminjam</label>
						<input type="text" class="form-control" name="peminjam" id="inputNanme4" value="{{ ($loan->peminjam) }}" disabled>
					</div>
					<div class="col-12">
						<input type="hidden" name="status" value="Dikembalikan">
					</div>
					<div class="col-12">
						<input type="hidden" name="code" value="{{ ($loan->code) }}">
					</div>
					<div class="d-grid gap-2 mt-3">
						<button type="submit" class="btn btn-success" value="save">Submit</button>
					</div>
				</form>


			<!-- End Default Table Example -->
			</div>
		</div>
		{{--endcard--}}
</div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('assets\dist\air-datepicker\air-datepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$('.opratorbox').select2();
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
