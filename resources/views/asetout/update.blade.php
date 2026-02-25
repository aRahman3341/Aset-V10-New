@extends('layouts.app')
@section('title')
	Barang Keluar - Monitoring Aset
@endsection
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets\dist\air-datepicker\air-datepicker.css') }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Barang Keluar</h1>
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
							<h5 class="card-title" >Form Barang Keluar</h5>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="row g-3" method="POST" action="{{ route('asetout.update', $asetout->id) }}">
					@csrf
					@method('PUT')
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">No Faktur</label>
						{{--<input type="text" class="form-control" name="no_faktur" id="no_faktur" value="{{ old('no_faktur', $asetout->no_faktur) }}">--}}
						<input type="text" class="form-control" name="no_faktur" id="no_faktur" value="{{ str_replace('^^', '/', old('no_faktur', $asetout->no_faktur)) }}">
						@error('no_faktur')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>

					@php
						$num = 1
					@endphp
 					
					<div class="d-grid gap-2 mt-3">
						<button type="submit" class="btn btn-success" value="save">Submit</button>
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
@endsection


{{--@foreach ($asetout as $item)
	
	<form class="row g-3" method="POST" action="/asetout/edit/{{ $item->id }}">
		@method('put')
		@csrf
		<div class="modal fade" id="ModalEdit-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="h4 fw-bold">Update</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="col-12">
							<label for="inputNanme4" class="form-label fw-bold">No. Faktur</label>
							<input type="text" class="form-control" name="no_faktur" value="{{ $item->no_faktur }}" id="inputNanme4">
						</div>
					</div>
					<div class="modal-footer">
							<div class="text-center">
								<button type="submit" class="btn btn-success" value="save">Update</button>
								<button type="reset" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
							</div>
					</div>
				</div>
			</div>
		</div>
	</form>
@endforeach--}}
