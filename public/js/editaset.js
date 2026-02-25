var csrfToken = $('meta[name="csrf-token"]').attr('content');


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



    //==================== KALIBRASI ================================
    //====================================================

    // Add an event listener to the checkbox on page load
    document.addEventListener("DOMContentLoaded", function() {
        var checkbox = document.querySelector("input[name=dikalibrasi]");
        toggleKalibrasiFields(checkbox);
    });

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

        var lastKalibrasiValue = parseInt(input.value);
        if (!isNaN(lastKalibrasiValue)) {
            var scheduleKalibrasiValue = lastKalibrasiValue + 2;
            scheduleKalibrasiField.value = scheduleKalibrasiValue;
        } else {
            scheduleKalibrasiField.value = '';
        }
    }

    //====================== END KALIBRASI ==============================
    //====================================================



    //====================== FORMAT RUPIAH ==============================
    //====================================================

    // format input nilai
    $(document).ready(function() {
        // Format the input field on keyup
        $('#amount_display').keyup(function() {
            let formattedAmount = formatAmount($(this).val());
            $(this).val(formattedAmount);
        });

        // Store the unformatted value in the hidden input field
        $('#amount_display').change(function() {
            let unformattedAmount = unformatAmount($(this).val());
            $('#nilai').val(unformattedAmount);
        });

        // Function to format the amount
        function formatAmount(amount) {
            // Remove any non-digit characters
            let cleanAmount = amount.replace(/[^0-9]/g, '');

            // Format the amount with commas and currency symbol
            let formattedAmount = cleanAmount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            return formattedAmount;
        }

        // Function to remove formatting from the amount
        function unformatAmount(formattedAmount) {
            // Remove currency symbol and commas
            let unformattedAmount = formattedAmount.replace(/[^0-9]/g, '');

            return unformattedAmount;
        }
    });
    //===================== END FORMAT RUPIAH ===============================
    //====================================================



    //====================== CHECK KODE BARANG ==============================
    //====================================================
    $(document).ready(function() {
        var isNupValid = true; // Flag to track NUP validity
        var isNupModified = false; // Flag to track if NUP field has been modified
        var oldNup = "{{ $item->nup ?? '' }}"; // Get the old NUP value

        // Event listener for "NUP" input
        $('#nup').on('input', function() {
            var nup = $(this).val();
            var code = $('#kode_barang').val();;


            // Remove validation classes and messages
            $('#nup').removeClass('is-valid is-invalid');
            $('#nup_error').text('');
            $('#nup_success').text('');

            if (nup.trim() !== '') {
                if (nup === oldNup) {
                    // The value is the same as the old value, no need for further checks
                    $('#nup').removeClass('is-invalid').addClass('is-valid');
                    $('#nup_error').text('');
                    $('#nup_success').text('NUP is valid');
                    isNupValid = true;
                    isNupModified = false;
                } else {
                    // The value is different from the old value, perform AJAX request to check existence
                    $.ajax({
                        url: '/checkNupExists',
                        type: 'POST',
                        data: {
                            nup: nup,
                            code: code,
                            old_nup: oldNup // Send the old NUP value to the server
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
                            isNupModified = true; // Set the NUP modified flag to true
                        },
                        error: function(xhr, status, error) {
                            if (xhr.status === 400) {
                                // NUP already exists or has different values, display the error message and remove success message
                                $('#nup').removeClass('is-valid').addClass('is-invalid');
                                $('#nup_error').text(xhr.responseJSON.message);
                                $('#nup_success').text('');
                                isNupValid = false; // Set the NUP validity flag to false
                                isNupModified = true; // Set the NUP modified flag to true
                            } else {
                                // Handle other errors
                                console.log('Error:', error);
                            }
                        }
                    });
                }
            } else {
                isNupValid = false; // Set the NUP validity flag to false if the field is empty
                isNupModified = true; // Set the NUP modified flag to true
            }
        });

        // Event listener for form submission
        $('form').on('submit', function(e) {
            if (isNupValid || (!isNupModified && $('#nup').val() === oldNup)) {
                // Allow form submission if NUP is valid or the value is the same as the old value
                // Clear feedback messages if the NUP is valid
                $('#nup_error').text('');
                $('#nup_success').text('');
            } else {
                e.preventDefault(); // Prevent the form submission if NUP is not valid and has been modified
            }
        });
      });



    //==================== END CHECK KODE BARANG ================================
    //====================================================



    //======================= CHECK NO SERI =============================
    //====================================================

    $(document).ready(function() {
        var isNoSeriValid = true; // Flag to track No Seri validity
        var isNoSeriModified = false; // Flag to track if No Seri field has been modified
        var oldNoSeri = "{{ $item->no_seri ?? '' }}"; // Get the old No Seri value

        // Event listener for "No Seri" input
        $('#no_seri').on('input', function() {
          var noSeri = $(this).val();

          // Remove validation classes and messages
          $('#no_seri').removeClass('is-valid is-invalid');
          $('#no_seri_error').text('');
          $('#no_seri_success').text('');

          if (noSeri.trim() !== '') {
            if (noSeri === oldNoSeri) {
              // The value is the same as the old value, no need for further checks
              $('#no_seri').removeClass('is-invalid').addClass('is-valid');
              $('#no_seri_error').text('');
              $('#no_seri_success').text('No Seri is valid');
              isNoSeriValid = true;
              isNoSeriModified = false;
            } else {
              // The value is different from the old value, perform AJAX request to check existence
              $.ajax({
                url: '/checkNoSeriExists',
                type: 'POST',
                data: {
                  no_seri: noSeri,
                  old_no_seri: oldNoSeri // Send the old No Seri value to the server
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
                  isNoSeriModified = true; // Set the No Seri modified flag to true
                },
                error: function(xhr, status, error) {
                  if (xhr.status === 400) {
                    // No Seri already exists or has different values, display the error message and remove success message
                    $('#no_seri').removeClass('is-valid').addClass('is-invalid');
                    $('#no_seri_error').text(xhr.responseJSON.message);
                    $('#no_seri_success').text('');
                    isNoSeriValid = false; // Set the No Seri validity flag to false
                    isNoSeriModified = true; // Set the No Seri modified flag to true
                  } else {
                    // Handle other errors
                    console.log('Error:', error);
                  }
                }
              });
            }
          } else {
            isNoSeriValid = false; // Set the No Seri validity flag to false if the field is empty
            isNoSeriModified = true; // Set the No Seri modified flag to true
          }
        });

        // Event listener for form submission
        $('form').on('submit', function(e) {
          if (isNoSeriValid || (!isNoSeriModified && $('#no_seri').val() === oldNoSeri)) {
            // Allow form submission if No Seri is valid or the value is the same as the old value
            // Clear feedback messages if the No Seri is valid
            $('#no_seri_error').text('');
            $('#no_seri_success').text('');
          } else {
            e.preventDefault(); // Prevent the form submission if No Seri is not valid and has been modified
          }
        });
      });

    //======================= END CHECK NO SERI =============================
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

