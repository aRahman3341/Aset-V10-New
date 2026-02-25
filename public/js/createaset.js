   var csrfToken = $('meta[name="csrf-token"]').attr('content');

//     (function() {
//     'use strict';

//     // Fetch the form element
//     var form = document.querySelector('.needs-validation');

//     // Submit event listener
//     form.addEventListener('submit', function(event) {
//         // Check if the form is valid
//         if (!form.checkValidity()) {
//             event.preventDefault(); // Prevent form submission if invalid
//         }

//         form.classList.add('was-validated'); // Add validation styles to the form
//     });
// })();

    //==================== SELECT LOCATIONS ================================
    //====================================================

    // Function to validate the form fields
    function validateForm(event) {
        event.preventDefault(); // Prevent the default form submission

        var gedungSelect = document.getElementById('gedung');
        var lantaiSelect = document.getElementById('lantai');
        var ruanganSelect = document.getElementById('ruangan');

        if (!gedungSelect.value || !lantaiSelect.value || !ruanganSelect.value) {
            gedungSelect.classList.add('is-invalid');
            lantaiSelect.classList.add('is-invalid');
            ruanganSelect.classList.add('is-invalid');

            document.getElementById('lokasi_error').style.display = 'block';
            return false;
        }

        gedungSelect.classList.remove('is-invalid');
        lantaiSelect.classList.remove('is-invalid');
        ruanganSelect.classList.remove('is-invalid');

        // If the validation passes, you can submit the form programmatically here if needed
        // form.submit();

        var typeInputs = document.querySelectorAll('#type-aset input[name="type"]');
        var isTypeSelected = false;

        for (var i = 0; i < typeInputs.length; i++) {
            if (typeInputs[i].checked) {
                isTypeSelected = true;
                break;
            }
        }

        if (!isTypeSelected) {
            // Show an error message for the Type Aset radio inputs
            document.getElementById('type-aset-error').style.display = 'block';
            return false;
        }

        return true;
    }
    //========================= END SELECT LOCATIONS ===========================
    //====================================================


    //========================== IMG FILTER ==========================
    //====================================================
    $(document).ready(function() {
        var fileInput = $('#dokumentasi');

        fileInput.on('change', function(e) {
          var file = fileInput.get(0).files[0];

          // Remove validation classes and messages
          fileInput.removeClass('is-valid is-invalid');
          $('#file_error').text('');

          if (file) {
            var fileSize = file.size;
            var maxSize = 20 * 1024 * 1024; // 20MB in bytes
            var validExtensions = ['jpg', 'jpeg', 'png']; // Allowed extensions

            // Check file size
            if (fileSize > maxSize) {
              fileInput.addClass('is-invalid');
              $('#file_error').text('File size exceeds the limit of 20MB');
              return;
            }

            // Check file extension
            var fileExtension = file.name.split('.').pop().toLowerCase();
            if (!validExtensions.includes(fileExtension)) {
              fileInput.addClass('is-invalid');
              $('#file_error').text('Only image files (jpg, jpeg, png) are allowed');
              return;
            }

            // File is valid
            fileInput.addClass('is-valid');
          }
        });

        $('form').on('submit', function(e) {
          var file = fileInput.get(0).files[0];

          // Check if a file is selected
          if (file) {
            var fileSize = file.size;
            var maxSize = 20 * 1024 * 1024; // 20MB in bytes
            var validExtensions = ['jpg', 'jpeg', 'png']; // Allowed extensions

            // Check file size
            if (fileSize > maxSize) {
              e.preventDefault(); // Prevent form submission
              fileInput.addClass('is-invalid');
              $('#file_error').text('File size exceeds the limit of 20MB');
              return;
            }

            // Check file extension
            var fileExtension = file.name.split('.').pop().toLowerCase();
            if (!validExtensions.includes(fileExtension)) {
              e.preventDefault(); // Prevent form submission
              fileInput.addClass('is-invalid');
              $('#file_error').text('Only image files (jpg, jpeg, png) are allowed');
              return;
            }
          }
        });
      });



    //======================== END OF IMG FILTER ============================
    //====================================================



    //====================== CHECK NUP BARANG ==============================
    //====================================================
    $(document).ready(function() {
    var isNupValid = false; // Flag to track NUP validity

    // Event listener for "NUP" input
    $('#nup').on('input', function() {
        var nup = $(this).val();
        var code = $('#kode_barang').val();;

        // Remove validation classes and messages
        $('#nup').removeClass('is-valid is-invalid');
        $('#nup_error').text('');
        $('#nup_success').text('');

        if (nup.trim() !== '') {
        $.ajax({
            url: '/checkNupExists',
            type: 'POST',
            data: {
            nup: nup,
            code: code
            },
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
            // NUP is valid, clear the error message and show success message
            $('#nup').removeClass('is-invalid').addClass('is-valid');
            $('#nup_error').text('');
            $('#nup_success').text('NUP is valid');
            isNupValid = true; // Set the NUP validity flag to true
            },
            error: function(xhr, status, error) {
            if (xhr.status === 400) {
                // NUP already exists, display the error message and remove success message
                $('#nup').removeClass('is-valid').addClass('is-invalid');
                $('#nup_error').text(xhr.responseJSON.message);
                $('#nup_success').text('');
                isNupValid = false; // Set the NUP validity flag to false
            } else {
                // Handle other errors
                console.log('Error:', error);
            }
            }
        });
        } else {
        isNupValid = false; // Set the NUP validity flag to false if the field is empty
        }
    });

    // Event listener for form submission
    $('form').on('submit', function(e) {
        // Check if the NUP is valid before allowing the form submission
        if (!isNupValid) {
        e.preventDefault(); // Prevent the form submission
        } else {
        // Clear feedback messages if the NUP is valid
        $('#nup_error').text('');
        $('#nup_success').text('');
        }
    });
    });




    //==================== END CHECK KODE BARANG ================================
    //====================================================



    //======================= CHECK NO SERI =============================
    //====================================================

    $(document).ready(function() {
        var isNoSeriValid = false; // Flag to track No Seri validity

        // Event listener for "No Seri" input
        $('#no_seri').on('input', function() {
          var noSeri = $(this).val();

          // Remove validation classes and messages
          $('#no_seri').removeClass('is-valid is-invalid');
          $('#no_seri_error').text('');
          $('#no_seri_success').text('');

          if (noSeri.trim() !== '') {
            $.ajax({
              url: '/checkNoSeriExists',
              type: 'POST',
              data: {
                no_seri: noSeri
              },
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                // No Seri is valid, clear the error message and show success message
                $('#no_seri').removeClass('is-invalid').addClass('is-valid');
                $('#no_seri_error').text('');
                $('#no_seri_success').text('No Seri is valid');
                isNoSeriValid = true; // Set the No Seri validity flag to true
              },
              error: function(xhr, status, error) {
                if (xhr.status === 400) {
                  // No Seri already exists, display the error message and remove success message
                  $('#no_seri').removeClass('is-valid').addClass('is-invalid');
                  $('#no_seri_error').text(xhr.responseJSON.message);
                  $('#no_seri_success').text('');
                  isNoSeriValid = false; // Set the No Seri validity flag to false
                } else {
                  // Handle other errors
                  console.log('Error:', error);
                }
              }
            });
          } else {
            isNoSeriValid = false; // Set the No Seri validity flag to false if the field is empty
          }
        });

        // Event listener for form submission
        $('form').on('submit', function(e) {
          // Check if the No Seri is valid before allowing the form submission
          if (!isNoSeriValid) {
            e.preventDefault(); // Prevent the form submission
          } else {
            // Clear feedback messages if the No Seri is valid
            $('#no_seri_error').text('');
            $('#no_seri_success').text('');
          }
        });
      });

    //======================= END CHECK NO SERI =============================
    //====================================================



    //================ KALIBRASI =========================
    //====================================================

    function toggleKalibrasiFields(checkbox) {
        var kalibrasiFields = document.getElementById('kalibrasiFields');
        kalibrasiFields.style.display = checkbox.checked ? 'block' : 'none';

        var kalibrasiByField = document.querySelector("input[name=kalibrasi_by]");
        var lastKalibrasiField = document.querySelector("input[name=last_kalibrasi]");
        var scheduleKalibrasiField = document.querySelector("input[name=schedule_kalibrasi]");

        if (checkbox.checked) {
          kalibrasiByField.setAttribute("required", "required");
          lastKalibrasiField.setAttribute("required", "required");
          scheduleKalibrasiField.setAttribute("required", "required");
        } else {
          kalibrasiByField.removeAttribute("required");
          lastKalibrasiField.removeAttribute("required");
          scheduleKalibrasiField.removeAttribute("required");
        }
      }

      function updateScheduleKalibrasi(input) {
        var lastKalibrasiField = document.querySelector("input[name=last_kalibrasi]");
        var scheduleKalibrasiField = document.querySelector("input[name=schedule_kalibrasi]");
        var kalibrasiWarning = document.getElementById('kalibrasiWarning');

        var lastKalibrasiValue = parseInt(input.value);
        if (isNaN(lastKalibrasiValue) || lastKalibrasiValue < 1900 || lastKalibrasiValue > 2100) {
          scheduleKalibrasiField.value = '';
          kalibrasiWarning.style.display = 'block';
        } else {
          var scheduleKalibrasiValue = lastKalibrasiValue + 2;
          scheduleKalibrasiField.value = scheduleKalibrasiValue;
          kalibrasiWarning.style.display = 'none';
        }
      }

      var form = document.getElementById('your-form-id'); // Replace with your form ID
      var dikalibrasiCheckbox = document.querySelector("input[name=dikalibrasi]");
      var kalibrasiFields = document.getElementById('kalibrasiFields');
      var kalibrasiByField = document.querySelector("input[name=kalibrasi_by]");
      var lastKalibrasiField = document.querySelector("input[name=last_kalibrasi]");
      var scheduleKalibrasiField = document.querySelector("input[name=schedule_kalibrasi]");
      var kalibrasiWarning = document.getElementById('kalibrasiWarning');

      form.addEventListener('submit', function(e) {
        if (dikalibrasiCheckbox.checked && (!kalibrasiByField.value || !lastKalibrasiField.value || !scheduleKalibrasiField.value)) {
          e.preventDefault();
          kalibrasiFields.classList.add('has-warning');
          kalibrasiWarning.style.display = 'block';
        }
      });

    // ==============================================================================
    //====================== END KALIBRASI ========================================

    // input date requiered if the checkbox checked
    function toggleDateInput(checkbox) {
        var dateInputContainer = document.getElementById('dateInputContainer');
        var expiryDateInput = document.getElementById('expiry_date');

        if (checkbox.checked) {
            dateInputContainer.style.display = 'block';
            expiryDateInput.required = true;
        } else {
            dateInputContainer.style.display = 'none';
            expiryDateInput.required = false;
        }
    }

    //====================== FORMAT RUPIAH ==============================
    //====================================================

    $(document).ready(function() {
        // Format the input field on keyup
        $('#amount_display').keyup(function() {
          let formattedAmount = formatAmount($(this).val());
          $(this).val(formattedAmount);

          // Store the unformatted value in the hidden input field
          let unformattedAmount = unformatAmount(formattedAmount);
          $('#nilai').val(unformattedAmount);
        });

        // Function to format the amount
        function formatAmount(amount) {
          // Remove any non-digit characters
          let cleanAmount = amount.replace(/[^0-9]/g, '');

          // Format the amount with commas
          let formattedAmount = cleanAmount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

          // Add currency symbol or any additional formatting if desired
          // For example, to format as Indonesian Rupiah:
          formattedAmount = formattedAmount;

          return formattedAmount;
        }

        // Function to remove formatting from the amount
        function unformatAmount(formattedAmount) {
          // Remove currency symbol and commas
          let unformattedAmount = formattedAmount.replace(/[^0-9]/g, '');

          return unformattedAmount;
        }
      });

    //======================== END FORMAT RUPIAH ============================
    //====================================================

    //================== SELECT LOCATIONS ==================================
    //====================================================
   // Retrieve the locations data from the data attribute
var locationsDataElement = document.getElementById('location-data');
var locations = JSON.parse(locationsDataElement.getAttribute('data-locations'));

var gedungSelect = document.getElementById("gedung");
var lantaiSelect = document.getElementById("lantai");
var ruanganSelect = document.getElementById("ruangan");

// Event listener for "Gedung" select field change
gedungSelect.addEventListener("change", function() {
  // Enable "Lantai" select field
  lantaiSelect.disabled = false;

  // Clear previous options
  lantaiSelect.innerHTML = '<option value="#" disabled selected>Pilih Lantai</option>';
  ruanganSelect.innerHTML = '<option value="#" disabled selected>Pilih Ruangan</option>';

  // Filter and populate "Lantai" options
  var selectedGedung = gedungSelect.value;
  var selectedLantaiOptions = locations.filter(function(location) {
    return location.office === selectedGedung;
  }).map(function(location) {
    return location.floor;
  }).filter(function(value, index, self) {
    return self.indexOf(value) === index;
  });

  selectedLantaiOptions.forEach(function(option) {
    var optionElement = document.createElement("option");
    optionElement.value = option;
    optionElement.textContent = option;
    lantaiSelect.appendChild(optionElement);
  });
});

// Event listener for "Lantai" select field change
lantaiSelect.addEventListener("change", function() {
  // Enable "Ruangan" select field
  ruanganSelect.disabled = false;

  // Clear previous options
  ruanganSelect.innerHTML = '<option value="#" disabled selected>Pilih Ruangan</option>';

  // Filter and populate "Ruangan" options
  var selectedGedung = gedungSelect.value;
  var selectedLantai = lantaiSelect.value;
  var selectedRuanganOptions = locations.filter(function(location) {
    return location.office === selectedGedung && location.floor === selectedLantai;
  }).map(function(location) {
    return location.room;
  }).filter(function(value, index, self) {
    return self.indexOf(value) === index;
  });

  selectedRuanganOptions.forEach(function(option) {
    var optionElement = document.createElement("option");
    optionElement.value = option;
    optionElement.textContent = option;
    ruanganSelect.appendChild(optionElement);
  });
});

// Form submission event listener
var form = document.getElementById('your-form-id'); // Replace with your form ID
form.addEventListener('submit', function(e) {
  if (gedungSelect.value === '' || lantaiSelect.value === '' || ruanganSelect.value === '') {
    e.preventDefault(); // Prevent form submission
    $('#lokasi_error').text('Silakan pilih lokasi yang valid.');
    return;
  }
  if (lantaiSelect.value === '#' || ruanganSelect.value === '#') {
    e.preventDefault(); // Prevent form submission
    $('#lokasi_error').text('Silakan pilih lokasi yang valid.');
    return;
  }
});


    //======================= END LOCATION =============================
    //====================================================


    // ================= Number input ============
    // script.js

// script.js

document.addEventListener('DOMContentLoaded', function() {
  const tahunInput = document.getElementById('tahun');
  const tahunError = document.getElementById('tahun_error');
  const tahunSuccess = document.getElementById('tahun_success');

  tahunInput.addEventListener('input', function() {
      const value = parseInt(tahunInput.value);
      if (value < 1900 || value > 2100 || isNaN(value)) {
          tahunInput.classList.add('is-invalid');
          tahunInput.classList.remove('is-valid');
          tahunError.style.display = 'block';
          tahunSuccess.style.display = 'none';
      } else {
          tahunInput.classList.remove('is-invalid');
          tahunInput.classList.add('is-valid');
          tahunError.style.display = 'none';
          tahunSuccess.style.display = 'block';
      }
  });
});


      // ============= End number input ===============
