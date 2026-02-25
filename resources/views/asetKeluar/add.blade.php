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
		{{--card--}}
		<div class="card">
			<div class="card mt-4">
				<div class="card-header" >
					<div class="row" style="margin-top: -18px; margin-bottom: -18px">
						<div class="col-md-6">
							<h5 class="card-title" >Form Aset Keluar</h5>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="row g-3" method="POST" action="{{ route('asetkeluar.store') }}">
					@csrf
					<div class="col-12">
						<label for="nomor" class="form-label fw-bold">Nomor</label>
						{{-- <input type="text" class="form-control" name="nomor" id="nomor" value="01/BA/SATKER/CB36/{{ now()->format('Y')}}"> --}}
						<input type="text" class="form-control" name="nomor" id="nomor" value="{{ old('nomor')}}">
						@error('nomor')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>

                    <div class="col-12">
						<label for="kepada" class="form-label fw-bold">Diserahkan Kepada:</label>
						<input type="text" class="form-control" name="kepada" id="kepada" value="{{ old('kepada') }}">
						@error('kepada')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-md-6">
						<label for="pihakSatu" class="form-label fw-bold">Pihak Kesatu</label>
						<input type="text" class="form-control" name="pihakSatu" value="{{ old('pihakSatu') }}">
						@error('pihakSatu')
							<div class="text-danger">{{ $message }}</div>
						@enderror
						@if (session('error'))
							<div class="alert alert-danger">
								{{ session('error') }}
							</div>
						@endif
					</div>
					<div class="col-md-6">
						<label for="pihakDua" class="form-label fw-bold">Pihak Kedua</label>
						<input type="text" class="form-control" name="pihakDua" value="{{ old('pihakDua') }}">
						@error('pihakDua')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>

                    <div class="col-md-6">
						<label for="nipSatu" class="form-label fw-bold">NIP</label>
						<input type="text" class="form-control" name="nipSatu" value="{{ old('nipSatu') }}">
						@error('nipSatu')
							<div class="text-danger">{{ $message }}</div>
						@enderror
						@if (session('error'))
							<div class="alert alert-danger">
								{{ session('error') }}
							</div>
						@endif
					</div>
					<div class="col-md-6">
						<label for="nipDua" class="form-label fw-bold">NIP</label>
						<input type="text" class="form-control" name="nipDua" value="{{ old('nipDua') }}">
						@error('nipDua')
							<div class="text-danger">{{ $message }}</div>
						@enderror
                        @if (session('error'))
							<div class="alert alert-danger">
								{{ session('error') }}
							</div>
						@endif
					</div>
                    <div class="col-md-6">
						<label for="jabatanSatu" class="form-label fw-bold">Jabatan</label>
						<input type="text" class="form-control" name="jabatanSatu" value="{{ old('jabatanSatu') }}">
						@error('jabatanSatu')
							<div class="text-danger">{{ $message }}</div>
						@enderror
						@if (session('error'))
							<div class="alert alert-danger">
								{{ session('error') }}
							</div>
						@endif
					</div>
					<div class="col-md-6">
						<label for="jabatanDua" class="form-label fw-bold">Jabatan</label>
						<input type="text" class="form-control" name="jabatanDua" value="{{ old('jabatanDua') }}">
						@error('jabatanDua')
							<div class="text-danger">{{ $message }}</div>
						@enderror
                        @if (session('error'))
							<div class="alert alert-danger">
								{{ session('error') }}
							</div>
						@endif
					</div>

                    {{-- <div class="col-md-12">
                        <label for="technicalData" class="form-label fw-bold">Technical Data</label>
                        <textarea class="form-control" name="technicalData" rows="4">{{ old('technicalData') }}</textarea>
                        @error('technicalData')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div> --}}
                    <div id="name-fields-container" class="col-md-12">
                        <div class="row">
                            <div class="col-12">
                                <label for="name" class="form-label fw-bold">Aset</label><br>
                                <select name="name[]" id="name" class="form-control namebox" required>
                                    <option value="">Pilih Aset</option>
                                    @foreach ($items as $item)
                                        @if ($item->status != "Diserahkan")
                                            <option value="{{ $item->id }}">{{ $item->name . ' - ' . $item->code .' - ' . $item->nup }} </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- <div id="items-container" class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="itemBarang" class="form-label fw-bold">Item Barang:</label>
                                <input type="text" class="form-control" name="itemBarang[]" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label fw-bold">Quantity:</label>
                                <input type="number" class="form-control" name="quantity[]" required>
                            </div>
                            <div class="col-md-2 mt-4">
                                <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
                            </div>
                            @error('pihakDua')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> --}}

                    <div class="d-grid gap-2 mt-3">
						<button type="button" id="add-name" class="btn btn-primary">Tambah Aset</button>
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
    var customMatcher = function(params, data) {
        // Remove dots (.) from the search query and data text
        var term = params.term.replace(/\./g, '');
        var text = data.text.replace(/\./g, '');

        // Check if the search query is empty or the modified text contains the modified search query
        if (term === '' || text.indexOf(term) > -1) {
            return data;
        }

        return null;
    };

    $('.namebox').select2({
        dropdownAutoWidth: true,
    });

    // Add event listener to the search box to apply custom matcher when the user starts typing
    $('.namebox').on('select2:open', function() {
        // Check if the user has started typing (minimumInputLength > 0)
        if ($(this).data('select2').options.get('minimumInputLength') > 0) {
            // Apply the custom matcher function
            $(this).data('select2').options.set('matcher', customMatcher);
        }
    });

    $('#add-name').on('click', function() {
        var newNameField = `
        <div class="row">
            <div class="col-10">
                <label for="name" class="form-label fw-bold">Aset</label><br>
                <select name="name[]" class="form-control namebox">
                    <option value="">Pilih Aset</option>
                    @foreach ($items as $item)
                        @if ($item->status != "Diserahkan")
                            <option value="{{ $item->id }}">{{ $item->name . ' - ' . $item->code .' - ' . $item->nup }} </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mt-3">
                <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
            </div>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            </div>
        `;
        $('#name-fields-container').append(newNameField);

        // Reinitialize select2 for the new name field
        $('.namebox').select2({
            dropdownAutoWidth: true,
        });
        $('.namebox').off('select2:open'); // Remove previous event listener
        $('.namebox').on('select2:open', function() {
            if ($(this).data('select2').options.get('minimumInputLength') > 0) {
                $(this).data('select2').options.set('matcher', customMatcher);
            }
        });
        $(document).on('click', '.remove-item-btn', function() {
            $(this).closest('.row').remove();
        });
    });

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

    // ================ add itemBarang
    // $(document).ready(function() {
    //     // Add new item fields
    //     $('#add-item').on('click', function() {
    //         var newItem = `
    //             <div class="row">
    //                 <div class="col-md-6 mb-3">
    //                     <label for="itemBarang" class="form-label fw-bold">Item Barang:</label>
    //                     <input type="text" class="form-control" name="itemBarang[]" required>
    //                 </div>
    //                 <div class="col-md-4 mb-3">
    //                     <label for="quantity" class="form-label fw-bold">Quantity:</label>
    //                     <input type="number" class="form-control" name="quantity[]" required>
    //                 </div>
    //                 <div class="col-md-2 mt-4">
    //                     <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
    //                 </div>
    //             </div>
    //         `;
    //         $('#items-container').append(newItem);
    //     });

    //     // Remove item fields
    //     $(document).on('click', '.remove-item-btn', function() {
    //         $(this).closest('.row').remove();
    //     });
    // });
</script>
@endsection
