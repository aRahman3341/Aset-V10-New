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
                <h1 class="h5 fw-bold">Report Aset Keluar</h1>
            </div>
        </div>
    </div>
	<div class="table-responsive">
		<div class="row">			
			<div class="col-md-12">
				{{--card--}}
				<div class="card">
					<div class="card mt-4">
						<div class="card-header">
                                <a href="{{ route('asetout.reportAll') }}" style="float: right;">
                                    <button type="button" style="font-size: 14px" class="btn btn-success"><i class="bi bi-cloud-arrow-down-fill"></i> Download</button>
                                </a>
						</div>
					</div>
                        <div class="card-body">
                            <!-- Default Table -->
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">No Faktur</th>
                                    <th scope="col">Nama Aset</th>
                                    <th scope="col">Qty Diajukan</th>
                                    <th scope="col">Qty Disetujui</th>
                                    <th scope="col">Satuan</th>
                                    <th scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody style="background-color: #f4f8fb">
                                    {{--@dd($ajuan)--}}
                                    @foreach ($ajuan as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->asetOuts->no_faktur }}</td>
                                                <td>{{ $item->item->name }}</td>
                                                <td>{{ $item->qty }}</td>
                                                <td>{{ $item->total_qty }}</td>
                                                <td>{{ $item->item->satuan }}</td>
                                                <td>{{ $item->status }}</td>
                                            </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- End Default Table Example -->
                            <div class="d-grid gap-2 mt-3">
                            </div>
                            
                        <!-- End Default Table Example -->
                        </div>
					</div>
				</div>
				{{--endcard--}}
			</div>
		</div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('assets\dist\air-datepicker\air-datepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default form submission
            
            // Perform client-side validation
            var from_date = document.getElementById('datepicker').value;
            var to_date = document.getElementById('datepicker1').value;
            
            // Add your validation logic here
            var isValid = true;
            if (from_date === '') {
                isValid = false;
                // Display an error message for Rentang Awal
                document.getElementById('datepicker').classList.add('is-invalid');
                document.getElementById('datepicker').nextElementSibling.textContent = 'Rentang Awal harus diisi.';
            } else {
                document.getElementById('datepicker').classList.remove('is-invalid');
                document.getElementById('datepicker').nextElementSibling.textContent = '';
            }
            
            if (to_date === '') {
                isValid = false;
                // Display an error message for Rentang Akhir
                document.getElementById('datepicker1').classList.add('is-invalid');
                document.getElementById('datepicker1').nextElementSibling.textContent = 'Rentang Akhir harus diisi.';
            } else {
                document.getElementById('datepicker1').classList.remove('is-invalid');
                document.getElementById('datepicker1').nextElementSibling.textContent = '';
            }
            
            if (isValid) {
                // Display confirmation dialog using SweetAlert
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Anda yakin ingin mengirim data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form
                        document.getElementById('myForm').submit();
                    }
                });
            }
        });
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