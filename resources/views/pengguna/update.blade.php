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
							<h5 class="card-title" >Form Edit Pengguna</h5>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				{{--<form class="row g-3" method="POST" action="/pengguna/edit/{{ $employe->id }}">--}}
				<form class="row g-3" method="POST" action="{{ route('pengguna.update', $employe->id) }}">
					@csrf
					@method('PUT')
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">NIP</label>
										<input type="text" class="form-control" name="nip" id="inputNanme4" value="{{ ($employe->nip) }}">
										@error('nip')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">Nama</label>
										<input type="text" class="form-control" name="name" id="inputNanme4" value="{{ ($employe->name) }}">
										@error('name')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">Email</label>
										<input type="text" class="form-control" name="email" id="inputNanme4" value="{{ ($employe->email) }}">
										@error('email')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-6">
										<label for="inputNanme4" class="form-label fw-bold">Jabatan</label>
										<select id="inputNanme4" class="form-select" name="jabatan">
											<option value="{{ ($employe->jabatan) }}">{{ ($employe->jabatan) }}</option>
											@if ($employe->jabatan == 'Operator')
												<option value="Karyawan">Karyawan</option>
											@else
												<option value="Operator">Operator</option>
											@endif
										</select>
										@error('jabatan')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-6">
										<label for="inputNanme4" class="form-label fw-bold">Jenis Kelamin</label>
										<select id="inputNanme4" class="form-select" name="gender">
											<option value="{{ ($employe->gender) }}">{{ ($employe->gender) }}</option>
											@if ($employe->gender == 'L')
												<option value="P">P</option>
											@else
												<option value="L">L</option>
											@endif
										</select>
										@error('gender')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">Alamat</label>
										<input type="text" class="form-control" name="alamat" id="inputNanme4" value="{{ ($employe->alamat) }}">
										@error('alamat')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-12">
										<label for="inputNanme4" class="form-label fw-bold">No Handphone</label>
										<input type="text" class="form-control" name="phone_number" id="inputNanme4" value="{{ ($employe->phone_number) }}">
										@error('phone_number')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
                                    {{--@if ($employe->jabatan !== 'Karyawan')
                                        <div class="col-12">
                                            <label for="inputPassword" class="form-label fw-bold">Password</label>
                                            <input type="password" class="form-control" name="password" id="inputPassword">
                                            @error('password')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if ($sess['jabatan'] !== 'Operator')
                                            <div class="col-12 mt-3">
                                                <button class="btn btn-secondary" type="button" onclick="resetToDefaultPassword()">Reset Password</button>
                                            </div>
                                        @endif
                                    @endif--}}
									@if ($sess['jabatan'] === 'Admin' || $sess['jabatan'] === 'Operator')
										<div class="col-12">
											<label for="inputPassword" class="form-label fw-bold">Password</label>
											<input type="password" class="form-control" name="password" id="inputPassword">
											@error('password')
												<div class="text-danger">{{ $message }}</div>
											@enderror
										</div>
										@if ($sess['jabatan'] !== 'Operator')
											<div class="col-12 mt-3">
												<button class="btn btn-secondary" type="button" onclick="resetToDefaultPassword()">Reset Password</button>
											</div>
										@endif
									@endif
										<div class="d-grid gap-2 mt-3">
											<button type="submit" class="btn btn-success" value="save">Update</button>
										</div>
				</form>
			</div>
		</div>
		{{--endcard--}}
	</div>
</main>
@endsection


<script>
    function resetToDefaultPassword() {
        var defaultPassword = '{{ $employe->nip }}'; // Replace this with the actual default password
        document.getElementById('inputPassword').value = defaultPassword;
        document.querySelector('form').submit(); // Submit the form
    }

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
