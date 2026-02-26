@extends('layouts.app')

@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <div class="pagetitle-left">
            <div class="pagetitle-icon"><i class="bi bi-plus-circle"></i></div>
            <div>
                <h1>Tambah Barang Habis Pakai</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Habis Pakai</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-card-header">
                    <i class="bi bi-box-seam"></i>
                    <span>Form Barang Habis Pakai</span>
                </div>
                <div class="form-card-body">
                    <form class="row g-3" method="POST" action="{{ route('items.store') }}">
                        @csrf

                        <div class="col-md-6">
                            <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" placeholder="Contoh: BHP-001">
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="categories" class="form-select @error('categories') is-invalid @enderror">
                                <option value="">— Pilih Kategori —</option>
                                <option value="ATK"          {{ old('categories') == 'ATK' ? 'selected' : '' }}>ATK</option>
                                <option value="Rumah Tangga" {{ old('categories') == 'Rumah Tangga' ? 'selected' : '' }}>Rumah Tangga</option>
                                <option value="Laboratorium" {{ old('categories') == 'Laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                            </select>
                            @error('categories')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Nama lengkap barang">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Saldo di Sistem <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('saldo') is-invalid @enderror" name="saldo" value="{{ old('saldo') }}" placeholder="0">
                            @error('saldo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('satuan') is-invalid @enderror" name="satuan" value="{{ old('satuan') }}" placeholder="pcs / rim / botol...">
                            @error('satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Status Pencatatan</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox" id="status" name="status" class="form-check-input" value="1" style="width:18px;height:18px;">
                                <label for="status" class="mb-0 text-muted" style="font-size:0.85rem">Tandai sebagai Teregister</label>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="bi bi-check-lg"></i> Simpan
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

    .form-card { background:#fff;border-radius:16px;border:1px solid rgba(30,58,95,0.08);box-shadow:0 2px 16px rgba(30,58,95,0.07);overflow:hidden; }
    .form-card-header { display:flex;align-items:center;gap:10px;padding:16px 24px;background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;font-size:0.92rem;font-weight:700; }
    .form-card-body { padding:28px 24px; }
</style>
@endsection