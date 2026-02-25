@extends('layouts.app')
@section('title')
	Pengajuan - Monitoring Aset
@endsection
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets\dist\air-datepicker\air-datepicker.css') }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Pengajuan Barang Habis Pakai</h1>
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
							<h5 class="card-title" >Form Pengajuan</h5>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="row g-3" method="POST" action="{{ route('pengajuan') }}">
					@csrf
					<div class="col-6">
						<label for="inputNanme4" class="form-label fw-bold">Kode Barang</label>
						<input type="text" class="form-control" name="code" value="{{ old('code') }}">
						@error('code')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-6">
						<label for="inputNanme4" class="form-label fw-bold">Nama Barang</label>
						<input type="text" class="form-control" name="name" value="{{ old('name') }}">
						@error('name')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-md-4">
						<label for="inputNanme4" class="form-label fw-bold">Satuan</label>
						<input type="text" class="form-control" name="satuan" value="{{ old('satuan') }}">
						@error('satuan')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-md-4">
						<label for="inputNanme4" class="form-label fw-bold">Saldo</label>
						<input type="text" class="form-control" name="saldo" value="{{ old('saldo') }}">
						@error('saldo')
						<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-md-4">
						<label for="inputNanme4" class="form-label fw-bold">Hasil Opsik</label>
						<input type="text" class="form-control" name="opsik" value="{{ old('opsik') }}">
						@error('opsik')
						<div class="text-danger">{{ $message }}</div>
						@enderror
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
		$('.namebox').select2();
	});

	$(document).ready(function() {
		$('.opratorbox').select2();
	});
	
	$(document).ready(function() {
		$('.opratorbox2').select2();
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