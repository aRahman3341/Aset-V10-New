@extends('layouts.app')
@section('title') Tambah Pengguna - Monitoring Aset @endsection
@section('content')
<main id="main" class="main">
<style>
.pg-pagetitle { display:flex; align-items:center; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid rgba(30,58,95,0.08); }
.pg-pagetitle-left { display:flex; align-items:center; gap:14px; }
.pg-pagetitle-icon { width:46px; height:46px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; box-shadow:0 4px 12px rgba(30,58,95,0.25); flex-shrink:0; }
.pg-pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 3px; }
.pg-pagetitle .breadcrumb { font-size:0.75rem; margin:0; padding:0; background:transparent; }
.pg-pagetitle .breadcrumb a { color:#2d5a8e; text-decoration:none; }
.pg-pagetitle .breadcrumb-item.active { color:#8a96a3; }
.pg-card { background:#fff; border-radius:16px; border:1px solid rgba(30,58,95,0.08); box-shadow:0 2px 16px rgba(30,58,95,0.07); overflow:hidden; }
.pg-card-header { padding:16px 24px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); display:flex; align-items:center; gap:10px; color:#fff; }
.pg-card-header span { font-size:0.95rem; font-weight:700; }
.pg-card-body { padding:28px; }
.pg-label { display:block; font-size:0.78rem; font-weight:700; color:#4a5a6e; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px; }
.req { color:#dc3545; }
.pg-input, .pg-select { width:100%; padding:10px 14px; font-size:0.85rem; color:#1e3a5f; background:#f8fafd; border:1.5px solid #dee2e6; border-radius:10px; outline:none; transition:border-color .15s; }
.pg-input:focus, .pg-select:focus { background:#fff; border-color:#2d5a8e; box-shadow:0 0 0 3px rgba(45,90,142,0.10); }
.pg-field { margin-bottom:18px; }
.pg-field-error { display:block; font-size:0.75rem; color:#dc3545; margin-top:4px; }
.pg-divider { display:flex; align-items:center; gap:10px; margin:4px 0 18px; font-size:0.75rem; font-weight:700; color:#8a96a3; text-transform:uppercase; letter-spacing:0.5px; }
.pg-divider::before, .pg-divider::after { content:''; flex:1; height:1px; background:rgba(30,58,95,0.08); }
.pg-info { background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 16px; font-size:0.82rem; color:#1d4ed8; display:flex; align-items:flex-start; gap:8px; margin-bottom:20px; }
.role-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:700; }
.role-login { background:#dcfce7; color:#15803d; }
.role-nologin { background:#fee2e2; color:#b91c1c; }
.pg-btn-submit { width:100%; padding:12px; background:linear-gradient(135deg,#1e3a5f,#2d5a8e); color:#fff; border:none; border-radius:10px; font-size:0.9rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:all .18s; }
.pg-btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(30,58,95,0.35); }
.pg-btn-back { width:100%; padding:11px; background:#f4f6fb; color:#5a6a7e; border:1.5px solid #dee2e6; border-radius:10px; font-size:0.88rem; font-weight:600; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .15s; }
.pg-btn-back:hover { background:#e8ecf5; color:#1e3a5f; text-decoration:none; }
.role-info-box { display:none; border-radius:10px; padding:10px 14px; font-size:0.8rem; margin-top:8px; }
.role-info-box.login { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; display:flex; align-items:center; gap:7px; }
.role-info-box.nologin { background:#fff7ed; border:1px solid #fed7aa; color:#c2410c; display:flex; align-items:center; gap:7px; }
</style>

<div class="pg-pagetitle">
    <div class="pg-pagetitle-left">
        <div class="pg-pagetitle-icon"><i class="bi bi-person-plus"></i></div>
        <div>
            <h1>Tambah Pengguna</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('pengguna') }}">Pengguna</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol></nav>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-10">
        <div class="pg-card">
            <div class="pg-card-header">
                <i class="bi bi-person-fill-add"></i>
                <span>Form Tambah Pengguna</span>
            </div>
            <div class="pg-card-body">

                <div class="pg-info">
                    <i class="bi bi-info-circle-fill mt-1"></i>
                    <div>
                        <strong>Informasi Password:</strong> Untuk role <strong>Admin, Manager, Operator</strong> — password default akan diset otomatis menggunakan <strong>NIP</strong>. Password dapat diubah setelah akun dibuat melalui menu Edit.
                        <br><span class="role-badge role-nologin mt-1"><i class="bi bi-x-circle"></i> Karyawan tidak bisa login</span>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success rounded-3 mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('pengguna.store') }}">
                    @csrf

                    <div class="pg-divider">Data Identitas</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="pg-field">
                                <label class="pg-label">NIP <span class="req">*</span></label>
                                <input type="text" name="nip" class="pg-input" placeholder="Masukkan NIP" value="{{ old('nip') }}" required>
                                @error('nip') <span class="pg-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pg-field">
                                <label class="pg-label">Jabatan <span class="req">*</span></label>
                                <select name="jabatan" class="pg-select" id="jabatanSelect" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Admin"    {{ old('jabatan')=='Admin'    ? 'selected':'' }}>Admin</option>
                                    <option value="Manager"  {{ old('jabatan')=='Manager'  ? 'selected':'' }}>Manager</option>
                                    <option value="Operator" {{ old('jabatan')=='Operator' ? 'selected':'' }}>Operator</option>
                                    <option value="Karyawan" {{ old('jabatan')=='Karyawan' ? 'selected':'' }}>Karyawan</option>
                                </select>
                                @error('jabatan') <span class="pg-field-error">{{ $message }}</span> @enderror
                                <div class="role-info-box login" id="infoLogin">
                                    <i class="bi bi-check-circle-fill"></i> Role ini <strong>bisa login</strong>. Password default = NIP.
                                </div>
                                <div class="role-info-box nologin" id="infoNoLogin">
                                    <i class="bi bi-exclamation-circle-fill"></i> Role ini <strong>tidak bisa login</strong> ke sistem.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pg-field">
                        <label class="pg-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="name" class="pg-input" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                        @error('name') <span class="pg-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="pg-field">
                        <label class="pg-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="pg-input" placeholder="contoh@email.com" value="{{ old('email') }}" required>
                        @error('email') <span class="pg-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="pg-field">
                                <label class="pg-label">Jenis Kelamin <span class="req">*</span></label>
                                <select name="gender" class="pg-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('gender')=='L' ? 'selected':'' }}>Laki-laki (L)</option>
                                    <option value="P" {{ old('gender')=='P' ? 'selected':'' }}>Perempuan (P)</option>
                                </select>
                                @error('gender') <span class="pg-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pg-field">
                                <label class="pg-label">No Handphone <span class="req">*</span></label>
                                <input type="text" name="phone_number" class="pg-input" placeholder="Contoh: 08123456789" value="{{ old('phone_number') }}" required>
                                @error('phone_number') <span class="pg-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pg-field">
                        <label class="pg-label">Alamat <span class="req">*</span></label>
                        <input type="text" name="alamat" class="pg-input" placeholder="Masukkan alamat" value="{{ old('alamat') }}" required>
                        @error('alamat') <span class="pg-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-8">
                            <button type="submit" class="pg-btn-submit">
                                <i class="bi bi-check-circle-fill"></i> Simpan Pengguna
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ url('pengguna') }}" class="pg-btn-back">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</main>

<script>
const jabatanSelect  = document.getElementById('jabatanSelect');
const infoLogin      = document.getElementById('infoLogin');
const infoNoLogin    = document.getElementById('infoNoLogin');
const loginRoles     = ['Admin', 'Manager', 'Operator'];

function updateRoleInfo() {
    const val = jabatanSelect.value;
    if (!val) { infoLogin.style.display='none'; infoNoLogin.style.display='none'; return; }
    if (loginRoles.includes(val)) {
        infoLogin.style.display='flex'; infoNoLogin.style.display='none';
    } else {
        infoLogin.style.display='none'; infoNoLogin.style.display='flex';
    }
}

jabatanSelect.addEventListener('change', updateRoleInfo);
updateRoleInfo();
</script>
@endsection