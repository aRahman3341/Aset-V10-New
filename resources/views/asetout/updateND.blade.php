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
				<form class="row g-3" method="POST" action="{{ route('asetout.updateND', $asetout->id) }}">
					@csrf
					@method('PUT')
					<div class="col-6">
						<label for="inputNanme4" class="form-label fw-bold">Nomor Nota Dinas</label>
						<input type="text" class="form-control" name="no_nd" id="no_nd" value="{{ str_replace('^^', '/', old('no_nd', $asetout->no_nd)) }}">
						@error('no_nd')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-6">
						<label for="inputNanme4" class="form-label fw-bold">MAK</label>
						<input type="text" class="form-control" name="mak" id="mak" value="{{ str_replace('^^', '/', old('mak', $asetout->mak)) }}">
						@error('mak')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					@php
						$num = 1
					@endphp
 					@foreach ($ajuan as $item1)
						<div id="items-container" class="col-md-12">
							{{--@dd($ajuan)--}}
							<div class="row">
								<input type="hidden" name="id[]" value="{{ $item1->id}}">
								<div class="col-md-6">
									<select name="name[]" id="name_{{ $num }}" class="form-control namebox name">
										
										@foreach ($itemhabis as $item)
											@if ($item->saldo != 0 && $item1->name == $item->id)
												<option value="{{ $item->id }}" data-satuan="{{ $item->satuan }}">
													{{ $item->code }} - {{ $item->name }} - {{ $item->satuan }} - {{ $item->saldo }}
												</option>
											@endif
										@endforeach
										@foreach ($itemhabis as $item)
											@if ($item->saldo != 0 && $item1->name != $item->id)
												<option value="{{ $item->id }}" data-satuan="{{ $item->satuan }}">
													{{ $item->code }} - {{ $item->name }} - {{ $item->satuan }} - {{ $item->saldo }}
												</option>
											@endif
										@endforeach
									</select>
									@error('name.*')
										<div class="text-danger">{{ $message }}</div>
									@enderror
								</div>
								<div class="col-md-6">
									<input type="text" class="form-control" name="qty[]" placeholder="Qty"  value="{{ $item1->qty }}">
									@error('qty.*')
										<div class="text-danger">{{ $message }}</div>
									@enderror
									@if (session('error'))
										<div class="alert alert-danger">
											{{ session('error') }}
										</div>
									@endif
								</div>

								<div class="col-md-2 mt-4">
									<button type="button" class="btn btn-danger remove-item-btn">Remove</button>
								</div>
								@error('pihakDua')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						@php
							$num++;
						@endphp
					@endforeach

                    <div class="d-grid gap-2 mt-3">
						<button type="button" id="add_item_btn" class="btn btn-primary mt-2">Add Item</button>
					</div>

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
<script>
	$(document).ready(function() {
		let coun = 1;
		$('.namebox').select2();
		$(`#name_{{ $num }}`).select2();
		
	});
	// Initialize Select2 for the newly added select element

    // ================ add itemBarang
    $(document).ready(function() {
		let counter = 1; // Initialize a counter variable

		// Function to handle adding and removing item rows
		$("#add_item_btn").click(function() {
			const newItemRow = `
				<div class="barang_keluar_item">
					<div class="row">
						<div class="col-md-6">
					<select name="name1[]" class="form-control namebox" id="nam_${counter}">
						<option value="">Pilih Aset</option>
						@foreach ($itemhabis as $item)
							@if ($item->saldo != 0)
								<option value="{{ $item->id }}" data-satuan="{{ $item->satuan }}">{{ $item->code }} - {{ $item->name }} - {{ $item->satuan }} - {{ $item->saldo }}</option>
							@endif
						@endforeach
					</select>
					</div>
					<div class="col-md-6">
					<input type="text" class="form-control" name="qty1[]" placeholder="Qty">
					</div>
					<div class="col-12">
						<input type="hidden" name="status[]" value="Diproses">
					</div>
					</div>
					<div class="col-md-2 mt-4">
					<button type="button" class="btn btn-danger remove_item_btn">Remove</button>
					</div>
				</div>
			`;

			$("#items-container").append(newItemRow);

			// Initialize Select2 for the newly added select element
			$(`#nam_${counter}`).select2();

			// Increment the counter for the next element
			counter++;
		});

		// Remove item row
		$(document).on("click", ".remove_item_btn", function() {
			$(this).closest(".barang_keluar_item").remove();
		});

		// Initialize Select2 for the existing select elements
		$(".namebox").select2();
	});
	//$(document).ready(function() {
	//	let counter = 1; // Initialize a counter variable

    //    // Add new item fields
    //    $('#add-item').on('click', function() {
    //        var newItem = `
	//		<div class="row">
	//			<div class="col-md-6">
	//				<select name="name[]" id="name" class="form-control namebox name">
						
	//					@foreach ($itemhabis as $item)
	//						@if ($item->saldo != 0 && $item1->name == $item->id)
	//							<option value="{{ $item->id }}" data-satuan="{{ $item->satuan }}">
	//								{{ $item->code }} - {{ $item->name }} - {{ $item->satuan }}
	//							</option>
	//						@endif
	//					@endforeach
	//					@foreach ($itemhabis as $item)
	//						@if ($item->saldo != 0 && $item1->name != $item->id)
	//							<option value="{{ $item->id }}" data-satuan="{{ $item->satuan }}">
	//								{{ $item->code }} - {{ $item->name }} - {{ $item->satuan }}
	//							</option>
	//						@endif
	//					@endforeach
	//				</select>
	//				@error('name.*')
	//					<div class="text-danger">{{ $message }}</div>
	//				@enderror
	//			</div>
	//			<div class="col-md-6">
	//				<input type="text" class="form-control" name="qty[]" placeholder="Qty"  value="{{ $item1->qty }}">
	//				@error('qty.*')
	//					<div class="text-danger">{{ $message }}</div>
	//				@enderror
	//				@if (session('error'))
	//					<div class="alert alert-danger">
	//						{{ session('error') }}
	//					</div>
	//				@endif
	//			</div>

	//			<div class="col-md-2 mt-4">
	//				<button type="button" class="btn btn-danger remove-item-btn">Remove</button>
	//			</div>
	//		</div>
    //        `;
    //        $('#items-container').append(newItem);
    //    });

    //    // Remove item fields
    //    $(document).on('click', '.remove-item-btn', function() {
    //        $(this).closest('.row').remove();
    //    });
	//	// Initialize Select2 for the newly added select element
    //    $(`#nam_${counter}`).select2();
    //});

</script>
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
