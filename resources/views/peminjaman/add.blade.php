@extends('layouts.app')
@section('title') Tambah Pengguna - Monitoring Aset @endsection
@section('content')
<main id="main" class="main">

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-person-plus-fill"></i></div>
        <div>
            <h1>Tambah Pengguna</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pengguna.getData') }}">Pengguna</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="section">
<div class="form-card">

    <div class="form-card-header">
        <i class="bi bi-person-plus-fill me-2"></i> Form Tambah Pengguna
    </div>

    {{-- Info box --}}
    <div class="info-box">
        <i class="bi bi-info-circle-fill me-2"></i>
        <div>
            <strong>Catatan:</strong>
            Password tidak diisi saat penambahan akun.
            Untuk role <strong>Admin, Manager, dan Operator</strong> — password default akan otomatis diset sama dengan <strong>NIP</strong> yang dimasukkan.
            Password dapat diubah setelah login pertama melalui menu <em>Edit Pengguna</em>.
        </div>
    </div>

    <div class="form-card-body">
        <form class="row g-3" method="POST" action="{{ route('pengguna.store') }}" novalidate id="addForm">
            @csrf

            <div class="col-md-6">
                <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nip') is-invalid @enderror"
                       name="nip" value="{{ old('nip') }}" maxlength="12"
                       placeholder="Masukkan NIP (maks. 12 digit)">
                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-hint">Akan digunakan sebagai password awal untuk role yang bisa login.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name') }}" placeholder="Nama lengkap">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" placeholder="email@domain.com">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Jabatan / Role <span class="text-danger">*</span></label>
                <select class="form-select @error('jabatan') is-invalid @enderror"
                        name="jabatan" id="jabatanSelect">
                    <option value="">Pilih jabatan...</option>
                    <optgroup label="Bisa Login">
                        <option value="admin"    {{ old('jabatan') == 'admin'    ? 'selected' : '' }}>Admin</option>
                        <option value="manager"  {{ old('jabatan') == 'manager'  ? 'selected' : '' }}>Manager</option>
                        <option value="operator" {{ old('jabatan') == 'operator' ? 'selected' : '' }}>Operator</option>
                    </optgroup>
                    <optgroup label="Tidak Bisa Login">
                        <option value="Karyawan" {{ old('jabatan') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </optgroup>
                </select>
                @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror

                {{-- Indikator real-time --}}
                <div id="roleIndicator" class="role-indicator d-none mt-2"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-select @error('gender') is-invalid @enderror" name="gender">
                    <option value="">Pilih...</option>
                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                </select>
                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">No Handphone <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                       name="phone_number" value="{{ old('phone_number') }}"
                       placeholder="08xxxxxxxxxx">
                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('alamat') is-invalid @enderror"
                       name="alamat" value="{{ old('alamat') }}" placeholder="Alamat lengkap">
                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
                </button>
                <a href="{{ route('pengguna.getData') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

        </form>
    </div>
</div>
</section>
</main>

<style>
.pagetitle { display:flex; align-items:center; margin-bottom:20px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon {
    width:44px; height:44px;
    background:linear-gradient(135deg,#012970,#4154f1);
    border-radius:12px; display:flex; align-items:center;
    justify-content:center; color:#fff; font-size:1.15rem;
    box-shadow:0 4px 12px rgba(1,41,112,0.22);
}
.pagetitle h1 { font-size:1.25rem; font-weight:800; color:#012970; margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0; padding:0; background:transparent; font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#4154f1; text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }

.form-card {
    background:#fff; border-radius:10px;
    border:1px solid rgba(1,41,112,0.07);
    box-shadow:0 2px 14px rgba(1,41,112,0.07);
    overflow:hidden; max-width:780px;
}
.form-card-header {
    background:#f6f9ff; border-bottom:2px solid #e0e8f5;
    padding:12px 20px; font-size:0.9rem; font-weight:800;
    color:#012970; display:flex; align-items:center;
}
.form-card-body { padding:24px 20px; }

.info-box {
    background:rgba(65,84,241,0.05); border-left:4px solid #4154f1;
    padding:12px 16px; font-size:0.8rem; color:#444;
    display:flex; align-items:flex-start; gap:8px;
    border-bottom:1px solid rgba(1,41,112,0.07);
}
.info-box i { color:#4154f1; font-size:1rem; flex-shrink:0; margin-top:1px; }

.form-label { font-size:0.82rem; color:#012970; margin-bottom:4px; }
.form-hint  { font-size:0.72rem; color:#8a96a3; margin-top:3px; }

.role-indicator {
    font-size:0.76rem; font-weight:700; padding:5px 11px;
    border-radius:6px; display:flex; align-items:center; gap:6px;
}
.role-login    { background:rgba(65,84,241,0.08); color:#4154f1; border:1px solid rgba(65,84,241,0.2); }
.role-nologin  { background:rgba(16,185,129,0.08); color:#10b981; border:1px solid rgba(16,185,129,0.2); }
</style>

<script>
const loginRoles = ['admin', 'manager', 'operator'];

document.getElementById('jabatanSelect').addEventListener('change', function () {
    const ind  = document.getElementById('roleIndicator');
    const val  = this.value.toLowerCase();
    ind.classList.remove('d-none', 'role-login', 'role-nologin');

    if (!val) { ind.classList.add('d-none'); return; }

    if (loginRoles.includes(val)) {
        ind.className = 'role-indicator mt-2 role-login';
        ind.innerHTML = '<i class="bi bi-shield-check-fill"></i> Bisa login — password default = NIP';
    } else {
        ind.className = 'role-indicator mt-2 role-nologin';
        ind.innerHTML = '<i class="bi bi-person-fill"></i> Karyawan — tidak bisa login';
    }
});
</script>
@endsection