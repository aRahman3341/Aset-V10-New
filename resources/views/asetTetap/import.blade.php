{{-- resources/views/asetTetap/import.blade.php --}}
{{-- Modal Import Aset Tetap — dipanggil dari asetTetap/index.blade.php --}}

<div class="modal fade" id="ModalImportAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden;">

            {{-- Header --}}
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3a5f,#2d5a8e);border:none;padding:16px 22px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px;height:34px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-file-earmark-arrow-down" style="color:#fff;font-size:1rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="color:#fff;font-size:0.95rem;font-weight:700;">Import Data Aset Tetap</h5>
                        <small style="color:rgba(255,255,255,0.65);font-size:0.73rem;">Upload file Excel (.xls / .xlsx)</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body" style="padding:22px;">

                {{-- Info format --}}
                <div style="background:#eef2f8;border:1px solid rgba(30,58,95,0.12);border-radius:10px;padding:12px 14px;margin-bottom:16px;">
                    <div style="font-size:0.78rem;font-weight:700;color:#1e3a5f;margin-bottom:6px;">
                        <i class="bi bi-info-circle-fill me-1"></i> Format Kolom yang Dikenali:
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:4px;">
                        @foreach(['Kode Barang *','NUP *','Nama Barang *','Jenis BMN *','Merk','Tipe','Kondisi','Status BMN','Nilai Perolehan Pertama (Rp)','Nilai Perolehan (Rp)','Nilai Penyusutan (Rp)','Nilai Buku (Rp)','Tgl Perolehan','Tgl Buku Pertama','No PSP','Tgl PSP','Jumlah Foto'] as $col)
                            <span style="font-size:0.68rem;background:rgba(30,58,95,0.08);color:#1e3a5f;padding:2px 7px;border-radius:4px;font-family:monospace;font-weight:600;">
                                {{ $col }}
                            </span>
                        @endforeach
                    </div>
                    <div style="font-size:0.72rem;color:#8a96a3;margin-top:8px;">
                        <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                        Kolom bertanda <strong>*</strong> wajib diisi. Baris yang sudah ada (Kode+NUP sama) akan diperbarui, baris baru akan ditambahkan.
                    </div>
                </div>

                {{-- Tip: gunakan hasil Export sebagai template --}}
                <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:10px 12px;margin-bottom:16px;font-size:0.76rem;color:#7b5800;">
                    <i class="bi bi-lightbulb-fill me-1" style="color:#f59e0b;"></i>
                    <strong>Tips:</strong> Gunakan hasil <em>Export</em> sebagai template. Format baris header export sudah sesuai dengan format import ini.
                </div>

                {{-- Form upload --}}
                <form id="formImportAset"
                      action="{{ route('asetTetap.import') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <label style="font-size:0.75rem;font-weight:700;color:#4a5a6e;display:block;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.4px;">
                        Pilih File Excel
                    </label>

                    {{-- Drop zone --}}
                    <div id="dropZoneAset"
                         style="border:2px dashed rgba(30,58,95,0.25);border-radius:10px;padding:28px 16px;text-align:center;cursor:pointer;transition:all .2s;background:#fafbfd;"
                         onclick="document.getElementById('fileInputAset').click()">
                        <i class="bi bi-cloud-upload" style="font-size:2rem;color:#2d5a8e;"></i>
                        <div style="font-size:0.82rem;font-weight:600;color:#1e3a5f;margin-top:6px;">Klik atau seret file ke sini</div>
                        <div style="font-size:0.72rem;color:#8a96a3;margin-top:3px;">Format: .xls / .xlsx · Maks. 10MB</div>
                    </div>

                    <input type="file"
                           id="fileInputAset"
                           name="file"
                           accept=".xls,.xlsx"
                           style="display:none">

                    {{-- Nama file terpilih --}}
                    <div id="fileNameAset"
                         style="display:none;margin-top:10px;padding:8px 12px;background:#f0f4ff;border-radius:8px;font-size:0.8rem;color:#1e3a5f;font-weight:600;">
                        <i class="bi bi-file-earmark-excel text-success me-1"></i>
                        <span id="fileNameTextAset"></span>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-fill" id="btnImportAset" disabled>
                            <i class="bi bi-upload me-1"></i> Import Sekarang
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const input    = document.getElementById('fileInputAset');
    const dropZone = document.getElementById('dropZoneAset');
    const nameBox  = document.getElementById('fileNameAset');
    const nameText = document.getElementById('fileNameTextAset');
    const btnImport= document.getElementById('btnImportAset');

    function applyFile(file) {
        if (!file) return;
        nameText.textContent = file.name;
        nameBox.style.display = 'block';
        btnImport.disabled = false;
        dropZone.style.borderColor   = '#2d5a8e';
        dropZone.style.background    = '#eef2f8';
    }

    input.addEventListener('change', function () {
        applyFile(this.files[0]);
    });

    // Drag & drop
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        this.style.borderColor = '#1e3a5f';
        this.style.background  = '#e8edf5';
    });
    dropZone.addEventListener('dragleave', function () {
        this.style.borderColor = 'rgba(30,58,95,0.25)';
        this.style.background  = '#fafbfd';
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        this.style.borderColor = 'rgba(30,58,95,0.25)';
        this.style.background  = '#fafbfd';
        const file = e.dataTransfer.files[0];
        if (file) {
            // Validasi ekstensi
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['xls','xlsx'].includes(ext)) {
                alert('Format file tidak didukung. Gunakan .xls atau .xlsx');
                return;
            }
            // Inject ke input supaya ikut form submit
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            applyFile(file);
        }
    });

    // Reset saat modal ditutup
    document.getElementById('ModalImportAset').addEventListener('hidden.bs.modal', function () {
        input.value            = '';
        nameBox.style.display  = 'none';
        nameText.textContent   = '';
        btnImport.disabled     = true;
        dropZone.style.borderColor = 'rgba(30,58,95,0.25)';
        dropZone.style.background  = '#fafbfd';
    });
})();
</script>