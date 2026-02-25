@extends('layouts.app')
@section('title')
	Pengguna - Monitoring Aset
@endsection
@section('content')
<main id="main" class="main">
	<div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Pengguna</h1>
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
							<h5 class="card-title" >Form Tambah Pengguna</h5>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="row g-3" method="POST" action="{{ route('pengguna.store') }}">
					@csrf
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">NIP</label>
										<input type="text" class="form-control" name="nip" id="inputNanme4" value="{{ old('nip') }}">
										@error('nip')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">Nama</label>
										<input type="text" class="form-control" name="name" id="inputNanme4" value="{{ old('name') }}">
										@error('name')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">Email</label>
										<input type="text" class="form-control" name="email" id="inputNanme4" value="{{ old('email') }}">
										@error('email')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-6">
										<label for="inputNanme4" class="form-label fw-bold">Jabatan</label>
										<select id="inputNanme4" class="form-select" name="jabatan">
											<option selected>Choose...</option>
											<option value="Karyawan" {{ old('jabatan') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
											<option value="Operator" {{ old('jabatan') == 'Operator' ? 'selected' : '' }}>Operator</option>
											<option value="Admin" {{ old('jabatan') == 'Admin' ? 'selected' : '' }}>Admin</option>
										</select>
										@error('jabatan')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-6">
										<label for="inputNanme4" class="form-label fw-bold">Jenis Kelamin</label>
										<select id="inputNanme4" class="form-select" name="gender">
											<option selected>Choose...</option>
											<option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>L</option>
											<option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>P</option>
										</select>
										@error('gender')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">Alamat</label>
										<input type="text" class="form-control" name="alamat" id="inputNanme4" value="{{ old('alamat') }}">
										@error('alamat')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">No Handphone</label>
										<input type="text" class="form-control" name="phone_number" id="inputNanme4" value="{{ old('phone_number') }}">
										@error('phone_number')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
										<div class="d-grid gap-2 mt-3">
											<button type="submit" class="btn btn-success" value="save">Tambah</button>
										</div>
				</form>
			</div>
		</div>
		{{--endcard--}}
	</div>
</main>
@endsection


<script>
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
