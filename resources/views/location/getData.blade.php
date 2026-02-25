@extends('layouts.app')
@section('title')
	Location - Monitoring Aset
@endsection
@section('content')
<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Lokasi</h1>
				<div class="col-md-6">
					<a href="#!" data-bs-toggle="modal" data-bs-target="#ModalAdd" style="float: right;">
						<button type="button" class="btn btn-success"><i class="bi bi-plus-square-fill"></i> Add</button>
					</a>
				</div>
            </div>
        </div>
    </div>

	<div class="table-responsive">

		{{--card--}}
		<div class="card">
			<div class="card mt-4">
				<div class="card-header">
					<div class="row">
						<div class="col-md-6">
						</div>
						<div class="col-md-6">
                            <form action="{{ route('location.search') }}" method="POST" class="form-inline">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="query" class="form-control" placeholder="Search" aria-label="Search" value="{{ request()->input('query') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary search-btn" type="submit"><i class="bi bi-search"></i></button>
                                        <a href="#" class="btn btn-outline-primary filter-btn" id="filterButton"><i class="bi bi-filter"></i></a>
                                        <a href="#!" data-bs-toggle="modal" data-bs-target="#ModalAdd" style="float: right;"></a>
                                    </div>
                                </div>
                            </form>
						</div>
					</div>
                    <div class="col-md-12">
                        <div id="filterFields" style="display: {{ request()->is('location/filter') ? ' block' : 'none' }};" class="form-inline mt-2">
                            <div class="card-body">
                                @include('location.filter')
                            </div>    {{-- end of card body --}}
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
					<th scope="col">Gedung</th>
					<th scope="col">Lantai</th>
					<th scope="col">Ruangan</th>
					<th scope="col" class="">Action</th>
				</tr>
				</thead>
				<tbody style="background-color: #f4f8fb">
					@foreach ($location as $item)
					<tr>
					<td>{{ $loop->iteration }}</td>
					<td>{{ $item->office }}</td>
					<td>Lt - {{ $item->floor }}</td>
					<td>{{ $item->room }}</td>
					<td>
						<a href="#!" data-bs-toggle="modal" data-bs-target="#ModalEdit-{{ $item->id }}" class="badge bg-warning text-dark" style="text-decoration: none;"><i class="bi bi-pencil"></i></a>
						<a class="badge bg-danger" style="text-decoration: none;"><i data-feather="edit"></i>
							<form action="{{ route('location.destroy', $item->id ) }}" method="post" id="deleteForm{{ $item->id }}">
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
			<div class="card-footer">
				{{ $location->links() }}
			</div>
			<!-- End Default Table Example -->
			</div>
		</div>

		@include('location.add')
		@include('location.update')
		{{--endcard--}}
</div>
</main>

<script src="{{ asset('js/indexaset.js') }}"></script>
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


    // =============== lokasi
    // Get the select elements
    var gedungSelect = document.getElementById('gedung');
    var lantaiSelect = document.getElementById('lantai');
    var ruanganSelect = document.getElementById('ruangan');

    // Add event listeners to the select elements
    gedungSelect.addEventListener('change', populateLantaiSelect);
    lantaiSelect.addEventListener('change', populateRuanganSelect);

    // Function to populate the lantai select element based on the selected gedung
    function populateLantaiSelect() {
    var selectedGedung = gedungSelect.value;

    // Clear previous options
    lantaiSelect.innerHTML = '<option value="">Lantai</option>';


    // Populate lantai options based on the selected gedung
    var lantaiOptions = getUniqueOptionsByOffice(selectedGedung, 'floor');
    lantaiOptions.forEach(function(option) {
        var optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        lantaiSelect.appendChild(optionElement);
    });


    // Reset and disable the ruangan select element
    ruanganSelect.innerHTML = '<option value="">Ruangan</option>';
    ruanganSelect.disabled = true;
    }

    // Function to populate the ruangan select element based on the selected lantai
    function populateRuanganSelect() {
    var selectedLantai = lantaiSelect.value;
    var selectedGedung = gedungSelect.value;

    // Clear previous options
    ruanganSelect.innerHTML = '<option value="">Ruangan</option>';

    // Populate ruangan options based on the selected lantai
    var ruanganOptions = getUniqueOptionsByFloor(selectedGedung, selectedLantai, 'room');
    ruanganOptions.forEach(function(option) {
        var optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        ruanganSelect.appendChild(optionElement);
    });

    }

    // Helper function to get unique options from the $locations array based on the given property
    function getUniqueOptionsByOffice(gedung, property) {
    var options = [];
        <?php foreach($locations as $location) { ?>
            if ("<?php echo $location->office; ?>" === gedung && options.indexOf("<?php echo $location->floor; ?>") === -1) {
            options.push("<?php echo $location->floor; ?>");
            }
        <?php } ?>
        return options;
    }

    // Helper function to get unique options from the $locations array based on the given property
    function getUniqueOptionsByFloor(gedung, lantai, property) {
        var options = [];
        <?php foreach($locations as $location) { ?>
            if ("<?php echo $location->floor; ?>" === lantai && "<?php echo $location->office; ?>" === gedung && options.indexOf("<?php echo $location->room; ?>") === -1) {
            options.push("<?php echo $location->room; ?>");
            }
        <?php } ?>
        return options;
    }
</script>
@endsection
