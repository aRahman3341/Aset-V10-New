// ================= FILTER BUTTON =============
document.getElementById('filterButton').addEventListener('click', function(event) {
    var filterFields = document.getElementById('filterFields');
    filterFields.style.display = (filterFields.style.display === 'none') ? 'block' : 'none';
});


// ================= END FILTER BUTTON =============

// // ================= LOCATIONS ==================// Get the select elements
var gedungSelect = document.getElementById('gedung');
var lantaiSelect = document.getElementById('lantai');
var ruanganSelect = document.getElementById('ruangan');

// Add event listeners to the select elements
gedungSelect.addEventListener('change', enableLantaiSelect);
lantaiSelect.addEventListener('change', enableRuanganSelect);

// Function to enable the lantai select element
function enableLantaiSelect() {
  // Enable the lantai select element
  lantaiSelect.disabled = false;
}

// Function to enable the ruangan select element
function enableRuanganSelect() {
  // Enable the ruangan select element
  ruanganSelect.disabled = false;
}


