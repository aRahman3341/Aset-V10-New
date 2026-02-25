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
				<form class="row g-3" method="POST" action="{{ route('asetout.store') }}">
					@csrf
					@if ($sess['jabatan'] !== 'Operator')
						<div class="col-12">
							<label for="inputNanme4" class="form-label fw-bold">No Faktur</label>
							<input type="text" class="form-control" name="no_faktur" id="no_faktur" value="{{ old('no_faktur') }}">
							@error('no_faktur')
								<div class="text-danger">{{ $message }}</div>
							@enderror
						</div>
					@else
						<div class="col-6">
							<label for="inputNanme4" class="form-label fw-bold">Nomor Nota Dinas</label>
							<input type="text" class="form-control" name="no_nd" id="no_nd" value="{{ old('no_nd') }}">
							@error('no_nd')
								<div class="text-danger">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-6">
							<label for="inputNanme4" class="form-label fw-bold">MAK</label>
							<input type="text" class="form-control" name="mak" id="mak" value="{{ old('mak') }}">
							@error('mak')
								<div class="text-danger">{{ $message }}</div>
							@enderror
						</div>						
					@endif

					<!-- Multiple inputs for name, qty, and satuan -->
					<!-- ... (previous code) ... -->

					<!-- Multiple inputs for name, qty, and satuan -->
					<div class="col-12">
						<label class="form-label fw-bold">Barang yang keluar</label><br>
						<div id="barang_keluar_container">
							<div class="barang_keluar_item">
								<select name="name[]" id="name" class="form-control namebox name">
									<option value="">Pilih Barang Persediaan</option>
									@foreach ($itemhabis as $item)
										@if ($item->saldo != 0)
											<option value="{{ $item->id }}" data-satuan="{{ $item->satuan }}">{{ $item->code }} - {{ $item->name }} - {{ $item->satuan }} - {{ $item->saldo }}</option>
										@endif
									@endforeach
								</select>
								@error('name.*')
									<div class="text-danger">{{ $message }}</div>
								@enderror
								<input type="text" class="form-control" name="qty[]" placeholder="Qty">
								@error('qty.*')
									<div class="text-danger">{{ $message }}</div>
								@enderror
								@if (session('error'))
									<div class="alert alert-danger">
										{{ session('error') }}
									</div>
								@endif
								<div class="col-12">
									<input type="hidden" name="status[]" value="Diproses">
								</div>
								<button type="button" class="btn btn-danger remove_item_btn">Remove</button>
							</div>
						</div>
						<button type="button" id="add_item_btn" class="btn btn-primary mt-2">Add Item</button>
					</div>
					<!-- ... (remaining code) ... -->
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
    let counter = 1; // Initialize a counter variable

    // Function to handle adding and removing item rows
    $("#add_item_btn").click(function() {
        const newItemRow = `
            <div class="barang_keluar_item">
                <select name="name[]" class="form-control namebox" id="nam_${counter}">
                    <option value="">Pilih Barang Persediaan</option>
                    @foreach ($itemhabis as $item)
                        @if ($item->saldo != 0)
                            <option value="{{ $item->id }}" data-satuan="{{ $item->satuan }}">{{ $item->code }} - {{ $item->name }} - {{ $item->satuan }} - {{ $item->saldo }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="text" class="form-control" name="qty[]" placeholder="Qty">
				<div class="col-12">
					<input type="hidden" name="status[]" value="Diproses">
				</div>
                <button type="button" class="btn btn-danger remove_item_btn">Remove</button>
            </div>
        `;

        $("#barang_keluar_container").append(newItemRow);

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



</script>
@endsection
