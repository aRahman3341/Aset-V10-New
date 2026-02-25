@extends('layouts.app')

@section('content')
<main id="main" class="main">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h4 fw-bold">Barang Habis Pakai</h1>
    </div>
    <div class="row d-flex justify-content-center">
        {{--card--}}
		<div class="card">
			<div class="card mt-4">
				<div class="card-header" >
					<div class="row" style="margin-top: -18px; margin-bottom: -18px">
						<div class="col-md-6">
							<h5 class="card-title" >Form Edit Barang Habis Pakai</h5>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="row g-3" method="POST" action="/items/{{ $item->id }}">
					@csrf
          @method('PUT')
          <div class="col-md-6">
            <label for="inputNanme4" class="col-form-label fw-bold">Kode Barang  <span class="text-danger">*</span></label>
            <div class="">
                <input type="text" class="form-control" name="code" id="code" value="{{ old('code', $item->code)}}">
                @error('code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
          </div>
          <div class="col-md-6">
              <label for="categories" class="col-form-label fw-bold">Kategori <span
                      class="text-danger">*</span></label>
              <div class="">
                  <select id="categories" name="categories" class="form-control">
                      <option value="">Pilih Kategori</option>
                      <option value="ATK" {{ (old('categories', $item->categories ?? '') === 'ATK') ? 'selected' : '' }}>ATK</option>
                      <option value="Rumah Tangga" {{ (old('categories', $item->categories ?? '') === 'Rumah Tangga') ? 'selected' : '' }}>Rumah Tangga</option>
                      <option value="Laboratorium" {{ (old('categories', $item->categories ?? '') === 'Laboratorium') ? 'selected' : '' }}>Laboratorium</option>
                  </select>
                  @error('categories')
                      <div class="text-danger">{{ $message }}</div>
                  @enderror
              </div>
          </div>
          <div class="col-md-12">
            <label for="name" class="col-form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
            <div class="">
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $item->name) }}">
                <span id="name_error" class="text-danger"></span>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
          </div>
          <div class="col-4">
            <label for="saldo" class="col-form-label fw-bold">Saldo di Sistem <span class="text-danger">*</span></label>
            <div class="">
                <input type="text" id="saldo" name="saldo" class="form-control" value="{{ old('saldo', $item->saldo) }}">
                <span id="saldo_error" class="text-danger"></span>
                @error('saldo')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
          </div>
          {{--<div class="col-4">
            <label for="opsik" class="col-form-label fw-bold">Hasil Opsik <span class="text-danger">*</span></label>
            <div class="">
                <input type="text" id="opsik" name="opsik" class="form-control" value="{{ old('opsik', $item->opsik) }}">
                <span id="opsik_error" class="text-danger"></span>
                @error('opsik')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
          </div>--}}
          <div class="col-4">
            <label for="satuan" class="col-form-label fw-bold">Satuan <span class="text-danger">*</span></label>
            <div class="">
                <input type="text" id="satuan" name="satuan" class="form-control" value="{{ old('satuan', $item->satuan) }}">
                <span id="satuan_error" class="text-danger"></span>
                @error('satuan')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
          </div>
					<div class="col-12">
						<label for="status" class="col-form-label fw-bold">Status Pencatatan</label>
            <div class="">
                <div class="form-check form-check-inline">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" id="status" name="status" class="form-check-input" value="1">
                </div>
            </div>
					</div>
					<div class="d-grid gap-2 mt-3">
						<button type="submit" class="btn btn-primary">Update Item</button>
					</div>
				</form>
			<!-- End Default Table Example -->
			</div>
		</div>
		{{--endcard--}}
    </div>
</main>


<script>
    // ============ format rupiah =========
function formatRupiah(input) {
        // Format the input value as Indonesian Rupiah currency format
        var value = input.value.replace(/\D/g, "");
        input.value = formatNumber(value);
    }

    function formatNumber(value) {
        // Format the value as Indonesian Rupiah currency format
        return new Intl.NumberFormat({
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(value);
    }

    function validateQuantity(input) {
    var value = input.value.replace(/\D/g, ''); // Remove non-digit characters from the input value
    input.value = value; // Update the input value with the validated value
}

    // ============= end format rupiah =========

    // ============== sum of ==============

    function calculateTotal() {
        var quantity = document.getElementById("quantity").value;
        var hargaSatuanDisplay = document.getElementById("harga_satuan_display").value.replace(/\D/g, "");
    var hargaSatuan = parseInt(hargaSatuanDisplay);
        var total = quantity * hargaSatuan;

        document.getElementById("harga_satuan").value = hargaSatuan;
        document.getElementById("harga_total_display").value = formatNumber(total);
        document.getElementById("harga_total").value = total;
    }

    // ============== end sum of =================

    //============ CHECK KODE BARANG ===============
    $(document).ready(function() {
  var isKodeBarangValid = false; // Flag to track Kode Barang validity
  var initialKodeBarangValue = $('#kode_barang').val(); // Store the initial value of Kode Barang field

  // Event listener for "Kode Barang" input
  $('#kode_barang').on('input', function() {
    var kodeBarang = $(this).val();

    // Remove validation classes and messages
    $('#kode_barang').removeClass('is-valid is-invalid');
    $('#kode_barang_error').text('');
    $('#kode_barang_success').text('');

    if (kodeBarang.trim() !== '') {
      $.ajax({
        url: '/checkCodeExists',
        type: 'POST',
        data: {
          kode_barang: kodeBarang
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          // Kode Barang is valid, clear the error message and show success message
          $('#kode_barang').removeClass('is-invalid').addClass('is-valid');
          $('#kode_barang_error').text('');
          $('#kode_barang_success').text('Kode Barang is valid');
          isKodeBarangValid = true; // Set the Kode Barang validity flag to true
        },
        error: function(xhr, status, error) {
          if (xhr.status === 400) {
            // Kode Barang already exists, display the error message and remove success message
            $('#kode_barang').removeClass('is-valid').addClass('is-invalid');
            $('#kode_barang_error').text(xhr.responseJSON.message);
            $('#kode_barang_success').text('');
            isKodeBarangValid = false; // Set the Kode Barang validity flag to false
          } else {
            // Handle other errors
            console.log('Error:', error);
          }
        }
      });
    } else {
      isKodeBarangValid = false; // Set the Kode Barang validity flag to false if the field is empty
    }
  });

  // Event listener for form submission
  $('form').on('submit', function(e) {
    // Check if the Kode Barang is valid before allowing the form submission
    if (!isKodeBarangValid && $('#kode_barang').val() !== initialKodeBarangValue) {
      e.preventDefault(); // Prevent the form submission
    } else {
      // Clear feedback messages if the Kode Barang is valid
      $('#kode_barang_error').text('');
      $('#kode_barang_success').text('');
    }
  });
});


    // ========= END KODE BARANG ================
</script>
@endsection
