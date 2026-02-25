<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-hidden="true">s
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="h4 fw-bold">Scanning</h4>
                <button type="button" class="btn-close fw-bold" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reader" width="600px"></div>
                <p id="result"></p>

            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <button type="reset" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    let isScanning = false;

    function onScanSuccess(decodedText, decodedResult) {
        if (isScanning) {
            // Split the decoded text into individual data fields
            const [code, nup, name, pengguna, lokasi, kalibrasi] = decodedText.split('*');
            html5QrcodeScanner.clear().then(_ => {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ url('generate_qrcodes/scanning') }}",
                    type: 'POST',
                    data: {
                        _method: "POST",
                        _token: CSRF_TOKEN,
                        code: code,
                        nup: nup,
                        name: name,
                        pengguna: pengguna,
                        lokasi: lokasi,
                        kalibrasi: kalibrasi
                    },
                    success: function(data) {
                        // Redirect to the page with the data
                        var items = JSON.stringify(data.items);
                        var locations = JSON.stringify(data.locations);
                        var employees = JSON.stringify(data.employees);
                        var categories = JSON.stringify(data.categories);
                        var tahun = JSON.stringify(data.tahun);

                        var url = "{{ route('generate_qrcodes.scanningResult') }}";
                        url += "?items=" + encodeURIComponent(items);
                        // url += "&locations=" + encodeURIComponent(locations);
                        // url += "&employees=" + encodeURIComponent(employees);
                        // url += "&categories=" + encodeURIComponent(categories);
                        // url += "&tahun=" + encodeURIComponent(tahun);

                        window.location.href = url;
                    },
                    error: function(xhr, status, error) {
                        console.error('Error performing search:', error);
                    }
                });
            }).catch(error => {
                console.error('Error clearing scanner:', error);
                alert('something wrong');
            });
        }
    }




    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // for example:
        // console.warn(`Code scan error = ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            }
        },
        /* verbose= */
        false
    );

    // Event listener for modal shown event
    document.getElementById('ModalAdd').addEventListener('shown.bs.modal', function() {
        isScanning = true; // Set scanning state to true
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });

    // Event listener for modal hidden event
    document.getElementById('ModalAdd').addEventListener('hidden.bs.modal', function() {
        isScanning = false; // Set scanning state to false
        document.getElementById("html5-qrcode-button-camera-stop").click();
        // You can optionally clear the result or perform any other necessary cleanup here
    });
</script>
