@extends('layouts.app')
@section('title')
	Barang keluar - Monitoring Aset
@endsection
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets\dist\air-datepicker\air-datepicker.css') }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Barang Keluar</h1>
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
							<h5 class="card-title" >Form Approval Barang Keluar</h5>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="row g-3" method="POST" action="/asetout/ajuan/{{ $asetout->id }}">
					@csrf
					@method('PUT')
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">No Faktur</label>
						<input type="text" class="form-control" name="no_faktur" id="inputNanme4" value="{{ ($asetout->no_faktur) }}" disabled>
						@error('no_faktur')
						<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Nama Aset</label><br>
						{{--<input type="text" class="form-control" name="name" id="inputNanme4" value="{{ $itemshabis->first()->name }}" disabled>
						@foreach($itemshabis as $item)
						@endforeach--}}
						{{--<select class="form-control" name="name" id="inputName">
							@foreach($itemshabis as $item)
								@if($item->id === $asetout->name)
									<option value="{{ $item->id }}" selected>{{ $item->name }}</option>
								@else
									<option value="{{ $item->id }}">{{ $item->name }}</option>
								@endif
							@endforeach
						</select>--}}
						
						<input type="text" class="form-control" name="name" id="inputNanme4" value="{{ ($itemshabis->name) }}" disabled>
						{{--<select name="name" id="name" class="form-control namebox">
							<option value="">Pilih Aset</option>
							{{--@if ($asetout->name = $)
								
							@endif-}}
							@foreach ($itemhabis as $item)
								@if ($item->saldo != 0)
									<option value="{{ $item->id }}" {{ old('name') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
								@endif
							@endforeach
						</select>--}}
						@error('name')
						<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-md-6">
						<label for="inputNanme4" class="form-label fw-bold">Qty</label>
						<input type="text" class="form-control" name="qty" id="inputNanme4" value="{{ ($asetout->qty) }}">
						@error('qty')
						<div class="text-danger">{{ $message }}</div>
						@enderror
						@if (session('error'))
							<div class="alert alert-danger">
								{{ session('error') }}
							</div>
						@endif
					</div>
					<div class="col-md-6">
						<label for="inputNanme4" class="form-label fw-bold">Satuan</label>
						<input type="text" class="form-control" name="satuan" id="inputNanme4" value="{{ ($asetout->satuan) }}" disabled>
						@error('satuan')
						<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-12">
						{{--<input type="hidden" name="status" value="Disetujui">--}}
					</div>
					<div class="d-grid gap-2 mt-3">
						<button type="submit" class="btn btn-success" value="save">Update</button>
					</div>
				</form>
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

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('form');
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

</script>
@endsection
