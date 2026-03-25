@extends('layouts.app')
@section('title') Edit Pengguna - Monitoring Aset @endsection
@section('content')
<main id="main" class="main">

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-person-gear"></i></div>
        <div>
            <h1>Edit Pengguna</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pengguna.getData') }}">Pengguna</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="section">
<div class="form-card">

    <div class="form-card-header">
        <i class="bi bi-person-gear me-2"></i> Edit Data: <strong class="ms-1">{{ $employe->name }}</strong>
    </div>

    <div class="form-card-body">
        <form class="row g-3" method="POST"
              action="{{ route('pengguna.update', $employe->id) }}"
              novalidate id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="{{ $type }}">

            <div class="col-md-6">
                <label class="form-label fw-semibold">NIP <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nip') is-invalid @enderror"
                       name="nip" value="{{ old('nip', $employe->nip) }}" maxlength="12">
                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name', $employe->name) }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email', $employe->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                <select class="form-select @error('jabatan') is-invalid @enderror" name="jabatan">
                    @if($type === 'user')
                        <option value="admin"    {{ old('jabatan', $employe->jabatan) == 'admin'    ? 'selected' : '' }}>Admin</option>
                        <option value="manager"  {{ old('jabatan', $employe->jabatan) == 'manager'  ? 'selected' : '' }}>Manager</option>
                        <option value="operator" {{ old('jabatan', $employe->jabatan) == 'operator' ? 'selected' : '' }}>Operator</option>
                    @else
                        <option value="Karyawan" selected>Karyawan</option>
                    @endif
                </select>
                @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-select @error('gender') is-invalid @enderror" name="gender">
                    <option value="L" {{ old('gender', $employe->gender) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                    <option value="P" {{ old('gender', $employe->gender) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                </select>
                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">No Handphone <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                       name="phone_number" value="{{ old('phone_number', $employe->phone_number) }}">
                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('alamat') is-invalid @enderror"
                       name="alamat" value="{{ old('alamat', $employe->alamat) }}">
                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- ── Blok Password: hanya tampil untuk users (login roles) ── --}}
            @if($type === 'user')
            <div class="col-12">
                <hr class="pwd-divider">
                <div class="pwd-section-title">
                    <i class="bi bi-shield-lock-fill me-2"></i> Ubah Password
                </div>
                <p class="pwd-hint-text">
                    Kosongkan jika tidak ingin mengubah password.
                    Password wajib mengandung <strong>minimal 8 karakter</strong>,
                    <strong>minimal 1 angka</strong>, dan <strong>minimal 1 simbol</strong>
                    (contoh: <code>Pass@1234</code>).
                </p>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Password Baru</label>
                <div class="input-group">
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password" id="passwordInput"
                           placeholder="Kosongkan jika tidak diubah"
                           autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Strength meter --}}
                <div class="pwd-meter mt-2" id="pwdMeter" style="display:none">
                    <div class="pwd-bar-wrap">
                        <div class="pwd-bar" id="pwdBar"></div>
                    </div>
                    <div class="pwd-checklist mt-1" id="pwdChecklist"></div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Konfirmasi Password</label>
                <input type="password"
                       class="form-control" name="password_confirm"
                       id="passwordConfirm"
                       placeholder="Ulangi password baru"
                       autocomplete="new-password">
                <div class="form-hint" id="confirmHint"></div>
            </div>

            {{-- Tombol reset password ke NIP (khusus Admin & Manager) --}}
            @if(in_array($sess['jabatan'], ['admin', 'manager']))
            <div class="col-12">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="fillNipBtn">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Isi otomatis dengan NIP ({{ $employe->nip }})
                </button>
                <span class="form-hint ms-2">Mengisi field password dengan NIP — tetap harus klik Update untuk menyimpan.</span>
            </div>
            @endif
            @endif
            {{-- ── End Password Block ── --}}

            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-success px-4" id="submitBtn">
                    <i class="bi bi-check2-circle me-1"></i> Update
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
.form-label { font-size:0.82rem; color:#012970; margin-bottom:4px; }
.form-hint  { font-size:0.72rem; color:#8a96a3; margin-top:3px; }

/* ── Password section ── */
.pwd-divider { border-color:rgba(1,41,112,0.10); margin-bottom:12px; }
.pwd-section-title {
    font-size:0.85rem; font-weight:800; color:#012970;
    display:flex; align-items:center; margin-bottom:6px;
}
.pwd-hint-text { font-size:0.77rem; color:#6c757d; margin-bottom:0; }

/* Strength meter */
.pwd-bar-wrap {
    height:5px; background:#e9ecef; border-radius:99px; overflow:hidden;
}
.pwd-bar {
    height:100%; border-radius:99px;
    transition:width .3s ease, background .3s ease;
    width:0%;
}
.pwd-checklist { font-size:0.72rem; color:#6c757d; display:flex; flex-wrap:wrap; gap:4px 12px; }
.pwd-check-ok  { color:#10b981; }
.pwd-check-no  { color:#dc2626; }
</style>

<script>
@if($type === 'user')
const nipValue = '{{ $employe->nip }}';

// ── Show/hide password ──
document.getElementById('togglePwd').addEventListener('click', function () {
    const inp  = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// ── Isi dengan NIP ──
@if(in_array($sess['jabatan'], ['admin', 'manager']))
document.getElementById('fillNipBtn').addEventListener('click', function () {
    document.getElementById('passwordInput').value = nipValue;
    document.getElementById('passwordConfirm').value = nipValue;
    checkStrength(nipValue);
    checkConfirm();
});
@endif

// ── Strength meter ──
function checkStrength(val) {
    const meter    = document.getElementById('pwdMeter');
    const bar      = document.getElementById('pwdBar');
    const checklist= document.getElementById('pwdChecklist');

    meter.style.display = val.length > 0 ? 'block' : 'none';
    if (!val) return;

    const checks = [
        { ok: val.length >= 8,         label: 'Min. 8 karakter' },
        { ok: /[0-9]/.test(val),        label: 'Ada angka' },
        { ok: /[\W_]/.test(val),        label: 'Ada simbol' },
        { ok: /[A-Z]/.test(val),        label: 'Huruf besar (opsional)' },
    ];

    const passed = checks.filter(c => c.ok).length;
    const colors = ['#dc2626', '#f59e0b', '#3b82f6', '#10b981'];
    const widths = ['25%', '50%', '75%', '100%'];
    bar.style.width      = widths[passed - 1] || '5%';
    bar.style.background = colors[passed - 1] || '#dc2626';

    checklist.innerHTML = checks.map(c =>
        `<span class="${c.ok ? 'pwd-check-ok' : 'pwd-check-no'}">
            <i class="bi bi-${c.ok ? 'check' : 'x'}-circle-fill"></i> ${c.label}
         </span>`
    ).join('');
}

function checkConfirm() {
    const pwd  = document.getElementById('passwordInput').value;
    const conf = document.getElementById('passwordConfirm').value;
    const hint = document.getElementById('confirmHint');
    if (!conf) { hint.textContent = ''; return; }
    if (pwd === conf) {
        hint.innerHTML = '<span style="color:#10b981"><i class="bi bi-check-circle-fill"></i> Password cocok</span>';
    } else {
        hint.innerHTML = '<span style="color:#dc2626"><i class="bi bi-x-circle-fill"></i> Password tidak cocok</span>';
    }
}

document.getElementById('passwordInput').addEventListener('input', function () {
    checkStrength(this.value);
    checkConfirm();
});
document.getElementById('passwordConfirm').addEventListener('input', checkConfirm);

// ── Client-side validation sebelum submit ──
document.getElementById('editForm').addEventListener('submit', function (e) {
    const pwd  = document.getElementById('passwordInput').value;
    const conf = document.getElementById('passwordConfirm').value;

    if (pwd) {
        if (pwd.length < 8) {
            e.preventDefault();
            alert('Password minimal 8 karakter.');
            return;
        }
        if (!/[0-9]/.test(pwd)) {
            e.preventDefault();
            alert('Password harus mengandung minimal 1 angka.');
            return;
        }
        if (!/[\W_]/.test(pwd)) {
            e.preventDefault();
            alert('Password harus mengandung minimal 1 simbol (contoh: @, #, !).');
            return;
        }
        if (pwd !== conf) {
            e.preventDefault();
            alert('Konfirmasi password tidak cocok.');
            return;
        }
    }
});
@endif
</script>
@endsection