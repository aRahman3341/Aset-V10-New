@extends('layouts.app')
@section('title') Edit Pengguna - Monitoring Aset @endsection
@section('content')
<main id="main" class="main">
<style>
.pg-pagetitle { display:flex; align-items:center; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid rgba(30,58,95,0.08); }
.pg-pagetitle-left { display:flex; align-items:center; gap:14px; }
.pg-pagetitle-icon { width:46px; height:46px; background:linear-gradient(135deg,#c49a2a,#e8b84b); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; box-shadow:0 4px 12px rgba(196,154,42,0.3); flex-shrink:0; }
.pg-pagetitle h1 { font-size:1.25rem; font-weight:800; color:#1e3a5f; margin:0 0 3px; }
.pg-pagetitle .breadcrumb { font-size:0.75rem; margin:0; padding:0; background:transparent; }
.pg-pagetitle .breadcrumb a { color:#2d5a8e; text-decoration:none; }
.pg-pagetitle .breadcrumb-item.active { color:#8a96a3; }
.pg-card { background:#fff; border-radius:16px; border:1px solid rgba(30,58,95,0.08); box-shadow:0 2px 16px rgba(30,58,95,0.07); overflow:hidden; }
.pg-card-header { padding:16px 24px; background:linear-gradient(135deg,#c49a2a,#e8b84b); display:flex; align-items:center; gap:10px; color:#fff; }
.pg-card-header span { font-size:0.95rem; font-weight:700; }
.pg-card-body { padding:28px; }
.pg-label { display:block; font-size:0.78rem; font-weight:700; color:#4a5a6e; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px; }
.req { color:#dc3545; }
.pg-input, .pg-select { width:100%; padding:10px 14px; font-size:0.85rem; color:#1e3a5f; background:#f8fafd; border:1.5px solid #dee2e6; border-radius:10px; outline:none; transition:border-color .15s; }
.pg-input:focus, .pg-select:focus { background:#fff; border-color:#c49a2a; box-shadow:0 0 0 3px rgba(196,154,42,0.12); }
.pg-field { margin-bottom:18px; }
.pg-field-error { display:block; font-size:0.75rem; color:#dc3545; margin-top:4px; }
.pg-divider { display:flex; align-items:center; gap:10px; margin:4px 0 18px; font-size:0.75rem; font-weight:700; color:#8a96a3; text-transform:uppercase; letter-spacing:0.5px; }
.pg-divider::before, .pg-divider::after { content:''; flex:1; height:1px; background:rgba(30,58,95,0.08); }
.pg-btn-submit { width:100%; padding:12px; background:linear-gradient(135deg,#c49a2a,#e8b84b); color:#fff; border:none; border-radius:10px; font-size:0.9rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:all .18s; }
.pg-btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(196,154,42,0.4); }
.pg-btn-back { width:100%; padding:11px; background:#f4f6fb; color:#5a6a7e; border:1.5px solid #dee2e6; border-radius:10px; font-size:0.88rem; font-weight:600; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .15s; }
.pg-btn-back:hover { background:#e8ecf5; color:#1e3a5f; text-decoration:none; }
.pg-btn-reset { padding:9px 16px; background:#f4f6fb; color:#1e3a5f; border:1.5px solid #dee2e6; border-radius:10px; font-size:0.82rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .15s; }
.pg-btn-reset:hover { background:#1e3a5f; color:#fff; }
.pw-strength { height:4px; border-radius:4px; margin-top:6px; transition:all .3s; }
.pw-strength-text { font-size:0.72rem; margin-top:4px; font-weight:600; }
.pw-req { font-size:0.75rem; color:#8a96a3; margin-top:6px; display:flex; flex-direction:column; gap:2px; }
.pw-req span { display:flex; align-items:center; gap:5px; }
.pw-req .ok { color:#15803d; }
.pw-req .fail { color:#dc3545; }
.pass-section { background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:18px; }
.pass-section-title { font-size:0.82rem; font-weight:700; color:#92400e; margin-bottom:14px; display:flex; align-items:center; gap:7px; }
</style>

<div class="pg-pagetitle">
    <div class="pg-pagetitle-left">
        <div class="pg-pagetitle-icon"><i class="bi bi-pencil-square"></i></div>
        <div>
            <h1>Edit Pengguna</h1>
            <nav><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('pengguna') }}">Pengguna</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol></nav>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-10">
        <div class="pg-card">
            <div class="pg-card-header">
                <i class="bi bi-person-fill-gear"></i>
                <span>Form Edit Pengguna</span>
            </div>
            <div class="pg-card-body">

                @if(session('success'))
                    <div class="alert alert-success rounded-3 mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('pengguna.update', $employe->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="pg-divider">Data Identitas</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="pg-field">
                                <label class="pg-label">NIP <span class="req">*</span></label>
                                <input type="text" name="nip" class="pg-input" value="{{ $employe->nip }}" required>
                                @error('nip') <span class="pg-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pg-field">
                                <label class="pg-label">Jabatan <span class="req">*</span></label>
                                <select name="jabatan" class="pg-select" required>
                                    <option value="Admin"    {{ $employe->jabatan=='Admin'    ? 'selected':'' }}>Admin</option>
                                    <option value="Manager"  {{ $employe->jabatan=='Manager'  ? 'selected':'' }}>Manager</option>
                                    <option value="Operator" {{ $employe->jabatan=='Operator' ? 'selected':'' }}>Operator</option>
                                    <option value="Karyawan" {{ $employe->jabatan=='Karyawan' ? 'selected':'' }}>Karyawan</option>
                                </select>
                                @error('jabatan') <span class="pg-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pg-field">
                        <label class="pg-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="name" class="pg-input" value="{{ $employe->name }}" required>
                        @error('name') <span class="pg-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="pg-field">
                        <label class="pg-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="pg-input" value="{{ $employe->email }}" required>
                        @error('email') <span class="pg-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="pg-field">
                                <label class="pg-label">Jenis Kelamin <span class="req">*</span></label>
                                <select name="gender" class="pg-select" required>
                                    <option value="L" {{ $employe->gender=='L' ? 'selected':'' }}>Laki-laki (L)</option>
                                    <option value="P" {{ $employe->gender=='P' ? 'selected':'' }}>Perempuan (P)</option>
                                </select>
                                @error('gender') <span class="pg-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pg-field">
                                <label class="pg-label">No Handphone <span class="req">*</span></label>
                                <input type="text" name="phone_number" class="pg-input" value="{{ $employe->phone_number }}" required>
                                @error('phone_number') <span class="pg-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pg-field">
                        <label class="pg-label">Alamat <span class="req">*</span></label>
                        <input type="text" name="alamat" class="pg-input" value="{{ $employe->alamat }}" required>
                        @error('alamat') <span class="pg-field-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password hanya untuk role yang bisa login --}}
                    @if(in_array($employe->jabatan, ['Admin','Manager','Operator']))
                    <div class="pass-section mt-2">
                        <div class="pass-section-title">
                            <i class="bi bi-shield-lock-fill"></i> Ubah Password (Opsional)
                        </div>
                        <p style="font-size:0.78rem;color:#8a96a3;margin-bottom:14px;">
                            Kosongkan jika tidak ingin mengubah password. Password harus minimal 8 karakter dan mengandung <strong>angka</strong> serta <strong>simbol</strong>.
                        </p>

                        <div class="pg-field">
                            <label class="pg-label">Password Baru</label>
                            <div style="position:relative">
                                <input type="password" name="password" id="passwordInput" class="pg-input" style="padding-right:42px" placeholder="Kosongkan jika tidak diubah">
                                <button type="button" onclick="togglePw('passwordInput','eyeIcon1')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#8a96a3;">
                                    <i class="bi bi-eye" id="eyeIcon1"></i>
                                </button>
                            </div>
                            <div class="pw-strength" id="pwStrengthBar" style="background:#e5e7eb;width:0%"></div>
                            <div class="pw-strength-text" id="pwStrengthText"></div>
                            <div class="pw-req" id="pwReqs">
                                <span id="req-len"  class="fail"><i class="bi bi-x-circle-fill"></i> Minimal 8 karakter</span>
                                <span id="req-num"  class="fail"><i class="bi bi-x-circle-fill"></i> Mengandung angka</span>
                                <span id="req-sym"  class="fail"><i class="bi bi-x-circle-fill"></i> Mengandung simbol (!@#$% dll)</span>
                            </div>
                            @error('password') <span class="pg-field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="pg-field">
                            <label class="pg-label">Konfirmasi Password</label>
                            <div style="position:relative">
                                <input type="password" name="password_confirmation" id="passwordConfirm" class="pg-input" style="padding-right:42px" placeholder="Ulangi password baru">
                                <button type="button" onclick="togglePw('passwordConfirm','eyeIcon2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#8a96a3;">
                                    <i class="bi bi-eye" id="eyeIcon2"></i>
                                </button>
                            </div>
                            <div id="confirmMsg" style="font-size:0.75rem;margin-top:4px;font-weight:600;"></div>
                        </div>

                        <button type="button" class="pg-btn-reset" onclick="resetPassword()">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset ke Password Default (NIP)
                        </button>
                    </div>
                    @endif

                    <div class="row g-3 mt-3">
                        <div class="col-md-8">
                            <button type="submit" class="pg-btn-submit">
                                <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
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
function togglePw(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (inp.type === 'password') { inp.type = 'text'; icon.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; icon.className = 'bi bi-eye'; }
}

function resetPassword() {
    const nip = document.querySelector('input[name="nip"]').value;
    document.getElementById('passwordInput').value    = nip;
    document.getElementById('passwordConfirm').value  = nip;
    checkPassword();
}

const pwInput   = document.getElementById('passwordInput');
const pwConfirm = document.getElementById('passwordConfirm');

function checkPassword() {
    if (!pwInput) return;
    const val  = pwInput.value;
    const hasLen = val.length >= 8;
    const hasNum = /[0-9]/.test(val);
    const hasSym = /[\W_]/.test(val);

    setReq('req-len', hasLen);
    setReq('req-num', hasNum);
    setReq('req-sym', hasSym);

    const score = [hasLen, hasNum, hasSym].filter(Boolean).length;
    const bar   = document.getElementById('pwStrengthBar');
    const txt   = document.getElementById('pwStrengthText');
    const colors = ['#ef4444','#f97316','#22c55e'];
    const labels = ['Lemah','Sedang','Kuat'];
    if (val.length === 0) { bar.style.width='0%'; txt.textContent=''; return; }
    bar.style.width      = (score * 33.3) + '%';
    bar.style.background = colors[score-1] || '#ef4444';
    txt.textContent      = labels[score-1] || 'Lemah';
    txt.style.color      = colors[score-1] || '#ef4444';

    checkConfirm();
}

function setReq(id, ok) {
    const el = document.getElementById(id);
    if (!el) return;
    el.className = ok ? 'ok' : 'fail';
    el.innerHTML = (ok ? '<i class="bi bi-check-circle-fill"></i> ' : '<i class="bi bi-x-circle-fill"></i> ') + el.innerHTML.replace(/<[^>]+>\s*/,'');
}

function checkConfirm() {
    if (!pwConfirm) return;
    const msg = document.getElementById('confirmMsg');
    if (!pwConfirm.value) { msg.textContent=''; return; }
    if (pwInput.value === pwConfirm.value) {
        msg.textContent = '✓ Password cocok'; msg.style.color='#15803d';
    } else {
        msg.textContent = '✗ Password tidak cocok'; msg.style.color='#dc3545';
    }
}

if (pwInput)   pwInput.addEventListener('input', checkPassword);
if (pwConfirm) pwConfirm.addEventListener('input', checkConfirm);
</script>
@endsection