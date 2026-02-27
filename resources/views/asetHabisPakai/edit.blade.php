@extends('layouts.app')

@section('content')
<main id="main" class="main">

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-pencil-square"></i></div>
        <div>
            <h1>Edit Barang Habis Pakai</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Habis Pakai</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="form-card">

            {{-- Header form --}}
            <div class="form-card-header">
                <i class="bi bi-box-seam"></i>
                <div>
                    <span>Edit Barang</span>
                    <span class="item-code-badge">{{ $item->code ?? '-' }}</span>
                </div>
            </div>

            <div class="form-card-body">

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form class="row g-3" method="POST" action="{{ route('items.update', $item->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="col-md-6">
                        <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" name="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $item->code) }}"
                               placeholder="Contoh: 1010301001000001">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="categories" class="form-select @error('categories') is-invalid @enderror">
                            <option value="">— Pilih Kategori —</option>
                            <option value="ATK"          {{ old('categories', $item->categories) == 'ATK'          ? 'selected' : '' }}>ATK</option>
                            <option value="Rumah Tangga" {{ old('categories', $item->categories) == 'Rumah Tangga' ? 'selected' : '' }}>Rumah Tangga</option>
                            <option value="Laboratorium" {{ old('categories', $item->categories) == 'Laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                        </select>
                        @error('categories')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $item->name) }}"
                               placeholder="Nama lengkap barang">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Saldo <span class="text-danger">*</span></label>
                        <input type="number" name="saldo"
                               class="form-control @error('saldo') is-invalid @enderror"
                               value="{{ old('saldo', $item->saldo) }}"
                               placeholder="0" min="0">
                        @error('saldo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="satuan"
                               class="form-control @error('satuan') is-invalid @enderror"
                               value="{{ old('satuan', $item->satuan) }}"
                               placeholder="pcs / rim / botol...">
                        @error('satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Status Pencatatan</label>
                        <div class="status-toggle">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" id="status" name="status" value="1"
                                   class="status-checkbox"
                                   {{ old('status', $item->status) ? 'checked' : '' }}>
                            <label for="status" class="status-label">
                                <span class="status-knob"></span>
                                <span class="status-text" id="statusText">
                                    {{ old('status', $item->status) ? 'Teregister' : 'Belum Teregister' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-lg"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

</main>

<style>
.pagetitle { display:flex; align-items:center; margin-bottom:24px; }
.pagetitle-left { display:flex; align-items:center; gap:14px; }
.pagetitle-icon { width:46px;height:46px;background:linear-gradient(135deg,#1e3a5f,#2d5a8e);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;box-shadow:0 4px 12px rgba(30,58,95,0.25);flex-shrink:0; }
.pagetitle h1 { font-size:1.3rem;font-weight:800;color:#1e3a5f;margin:0 0 4px; }
.pagetitle .breadcrumb { margin:0;padding:0;background:transparent;font-size:0.78rem; }
.pagetitle .breadcrumb-item a { color:#2d5a8e;text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

/* Form Card */
.form-card { background:#fff;border-radius:16px;border:1px solid rgba(30,58,95,0.08);box-shadow:0 2px 16px rgba(30,58,95,0.07);overflow:hidden; }

.form-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 24px;
    background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
    color: #fff;
    font-size: 0.92rem;
    font-weight: 700;
}
.item-code-badge {
    font-family: 'DM Mono', monospace;
    font-size: 0.78rem;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.25);
    padding: 2px 10px;
    border-radius: 20px;
    letter-spacing: 0.5px;
}

.form-card-body { padding: 28px 24px; }

.form-label { font-size:0.8rem; font-weight:700; color:#3d5170; margin-bottom:5px; }
.form-control, .form-select {
    font-size: 0.85rem;
    border-color: rgba(30,58,95,0.15);
    border-radius: 8px;
    padding: 8px 12px;
}
.form-control:focus, .form-select:focus {
    border-color: #2d5a8e;
    box-shadow: 0 0 0 3px rgba(30,58,95,0.10);
}

/* Status toggle switch */
.status-toggle { display:flex; align-items:center; gap:10px; }
.status-checkbox { display:none; }
.status-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}
.status-knob {
    width: 42px; height: 24px;
    background: #d0d8e4;
    border-radius: 12px;
    position: relative;
    transition: background .2s;
    flex-shrink: 0;
}
.status-knob::after {
    content: '';
    position: absolute;
    width: 18px; height: 18px;
    background: #fff;
    border-radius: 50%;
    top: 3px; left: 3px;
    transition: left .2s;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
}
.status-checkbox:checked + .status-label .status-knob {
    background: linear-gradient(135deg, #10b981, #2eca6a);
}
.status-checkbox:checked + .status-label .status-knob::after {
    left: 21px;
}
.status-text { font-size:0.83rem; font-weight:600; color:#3d5170; }
</style>

<script>
// Update teks status saat toggle berubah
document.getElementById('status').addEventListener('change', function() {
    document.getElementById('statusText').textContent = this.checked ? 'Teregister' : 'Belum Teregister';
});
</script>
@endsection