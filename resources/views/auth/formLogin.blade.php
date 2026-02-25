@extends('layouts.app')

@section('content')
<style>
    .bgImg {
        background-image: url('{!! asset("assets/img/bg.png") !!}');
    }
</style>
<div class="bgImg">
<div class="w-500 center border rounded px-3 py-3 mx-auto">
	<div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
		<div class="col-md-4 text-center">
			{{--card--}}
			<div class="card" style="background: transparent; border: none;">
				<div class="card mt-4" style="background: transparent; color: black">
					<div class="card-header" style="background: transparent; color: black">
						<h1 class="h4 fw-bold">Login</h1>
					</div>
				</div>
				<div class="card-body">
					<!-- Default Table -->
					@if (session('error'))
						<div class="alert alert-danger">
							{{ session('error') }}
						</div>
					@endif
					<form action="{{ url('session/login') }}" method="POST">
						@csrf
						<div class="row d-flex justify-content">
							<div class="input-group">
								<div class="col-12">
									<label for="email" class="form-label fw-bold" style="float: left; font-size: 14px">Email</label>
								</div>
								<div class="col-12">
									<input type="email" value="{{ Session::get('email') }}" name="email" class="form-control">
									@error('email')
									<div class="text-danger">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div><br>
						<div class="row d-flex justify-content">
							<div class="input-group">
								<div class="col-12">
									<label for="password" class="form-label fw-bold" style="float: left; font-size: 14px">Password</label>
									</div>
								<div class="col-12">
									<input type="password" name="password" class="form-control">
									@error('password')
									<div class="text-danger">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div><br>
						<div class="row">
							<div class="col-md-12">
								<div class="text-center">
									<button name="submit" type="submit" class="btn btn-primary">Login</button>
								</div>
							</div>
						</div>
					</form>	
				</div>
			</div>
			
			{{--<div class="card">
				<div class="card mt-4">
					<div class="card-header">
						<h1 class="h4 fw-bold">Login</h1>
					</div>
				</div>
				<div class="card-body">
					<!-- Default Table -->
					@if (session('error'))
						<div class="alert alert-danger">
							{{ session('error') }}
						</div>
					@endif
					<form action="/session/login" method="POST">
						@csrf
						<div class="row d-flex justify-content">
								<div class="input-group">
									<div class="col-12">
										<label for="email" class="form-label fw-bold" style="float: left; font-size: 14px">Email</label>
									</div>
									<div class="col-12">
										<input type="email" value="{{ Session::get('email') }}" name="email" class="form-control">
										@error('from_date')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
								</div>
						</div><br>
						<div class="row d-flex justify-content">
								<div class="input-group">
									<div class="col-12">
										<label for="password" class="form-label fw-bold" style="float: left; font-size: 14px">Password</label>
									</div>
									<div class="col-12">
										<input type="password" name="password" class="form-control">
										@error('from_date')
										<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
								</div>
						</div><br>
						<div class="row">
							<div class="col-md-12">
								<div class="text-center">
									<button name="submit" type="submit" class="btn btn-primary">Login</button>
								</div>
							</div>
						</div>
					</form>	
				</div>
			</div>--}}
			{{--endcard--}}
		</div>
	</div>
</div>
</div>

{{--<div class="w-500 center border rounded px-3 py-3 mx-auto">
	<div class="col-md-4 text-center">
		<div class="card">
			<div class="card mt-4">
				<div class="card-header">
					<h1 class="h4 fw-bold">Login</h1>
				</div>
			</div>
			<div class="card-body">

			<!-- Default Table -->
			@if (session('error'))
				<div class="alert alert-danger">
					{{ session('error') }}
				</div>
			@endif
			<form action="/session/login" method="POST">
				@csrf
				<div class="row d-flex justify-content-end">
					<div class="col-md-6">
						<div class="input-group">
							<label for="email" class="form-label">Email</label>
							<input type="email" value="{{ Session::get('email') }}" name="email" class="form-control">
							@error('from_date')
							<div class="text-danger">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div><br>
				<div class="row d-flex justify-content-end">
					<div class="col-md-6">
						<div class="input-group">
							<label for="password" class="form-label">Password</label>
							<input type="password" name="password" class="form-control">
							@error('from_date')
							<div class="text-danger">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div><br>
				<div class="row">
					<div class="col-md-12">
						<div class="text-center">
							<button name="submit" type="submit" class="btn btn-primary">Login</button>
						</div>
					</div>
				</div>
			</form>	
			</div>
		</div>
	</div>
</div>--}}
@endsection
