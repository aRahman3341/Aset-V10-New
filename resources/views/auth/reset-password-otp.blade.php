@extends('layouts.app')
@section('content')
<style>
    .login-bg{background-image:url('{!! asset("assets/img/bg.png") !!}');
        background-size:cover;background-position:center;min-height:100vh;
        display:flex;align-items:center;justify-content:center;}
    .login-overlay{position:fixed;inset:0;background:rgba(10,25,55,.45);z-index:0;}
    .login-wrap{position:relative;z-index:1;width:100%;max-width:440px;padding:0 16px;}
    .login-card{background:rgba(255,255,255,.97);border:none;border-radius:18px;
        box-shadow:0 20px 60px rgba(0,30,90,.30);overflow:hidden;}
    .card-head{background:linear-gradient(135deg,#003087,#1a56c4);
        padding:24px 32px;text-align:center;color:#fff;}
    .card-head h1{font-size:1.1rem;font-weight:800;margin:0 0 4px;}
    .card-head p{font-size:0.75rem;color:rgba(255,255,255,.75);margin:0;}
    .card-body{padding:28px 32px 32px;}
    .lbl{font-size:.75rem;font-weight:700;color:#3d5170;text-transform:uppercase;
         letter-spacing:.4px;display:block;margin-bottom:5px;}
    .inp-wrap{position:relative;margin-bottom:16px;}
    .inp-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);
              color:#8a96a3;font-size:.9rem;pointer-events:none;}
    .inp{width:100%;padding:10px 36px 10px 36px;font-size:.88rem;color:#1e3a5f;
         background:#f4f7fc;border:1.5px solid rgba(30,58,95,.12);
         border-radius:10px;outline:none;transition:all .18s;font-family:inherit;}
    .inp:focus{background:#fff;border-color:#1a56c4;
               box-shadow:0 0 0 3px rgba(26,86,196,.12);}
    .inp.is-invalid{border-color:#dc3545;}
    .err{font-size:.73rem;color:#dc3545;margin-top:3px;display:block;}
    .btn-submit{width:100%;padding:11px;background:linear-gradient(135deg,#003087,#1a56c4);
        color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;
        cursor:pointer;font-family:inherit;margin-top:4px;
        box-shadow:0 4px 14px rgba(0,48,135,.30);transition:all .18s;}
    .btn-submit:hover{transform:translateY(-1px);}
    .alert-err{background:#fef2f2;border:1px solid rgba(220,53,69,.2);border-radius:8px;
        padding:10px 14px;font-size:.8rem;color:#991b1b;margin-bottom:16px;}
    .strength-bar{height:4px;border-radius:4px;margin-top:6px;background:#e5e7eb;}
    .strength-bar .fill{height:100%;border-radius:4px;transition:all .3s;width:0;}
    .card-foot{text-align:center;padding:12px 32px 18px;font-size:.72rem;
        color:#8a96a3;border-top:1px solid rgba(30,58,95,.07);}
    #header,#sidebar,.sidebar,header.header{display:none!important}
    #main{margin:0!important;padding:0!important}
    body{padding:0!important;margin:0!important}
</style>

<div class="login-bg">
    <div class="login-overlay"></div>
    <div class="login-wrap">
        <div class="login-card">
            <div class="card-head">
                <h1>🔒 Password Baru</h1>
                <p>Buat password baru yang kuat untuk akun Anda</p>
            </div>
            <div class="card-body">

                @if($errors->any())
                    <div class="alert-err">
                        @foreach($errors->all() as $err)
                            <div><i class="bi bi-x-circle-fill"></i> {{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('password.resetPassword') }}" method="POST">
                    @csrf

                    <label class="lbl" for="password">
                        <i class="bi bi-lock"></i> Password Baru
                    </label>
                    <div class="inp-wrap">
                        <i class="bi bi-lock inp-icon"></i>
                        <input type="password" id="password" name="password"
                               class="inp {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="Min. 8 karakter, angka & simbol"
                               oninput="checkStrength(this.value)"
                               required>
                        <button type="button" onclick="togglePwd('password','eye1')"
                                style="position:absolute;right:12px;top:50%;
                                       transform:translateY(-50%);background:none;
                                       border:none;color:#8a96a3;cursor:pointer;padding:0;">
                            <i class="bi bi-eye" id="eye1"></i>
                        </button>
                    </div>
                    <div class="strength-bar">
                        <div class="fill" id="strengthFill"></div>
                    </div>
                    <small id="strengthText" style="font-size:.72rem;color:#6b7280;"></small>
                    @error('password')<span class="err">{{ $message }}</span>@enderror

                    <label class="lbl" for="password_confirmation"
                           style="margin-top:14px;display:block;">
                        <i class="bi bi-lock-fill"></i> Konfirmasi Password
                    </label>
                    <div class="inp-wrap">
                        <i class="bi bi-lock-fill inp-icon"></i>
                        <input type="password" id="password_confirmation"
                               name="password_confirmation"
                               class="inp"
                               placeholder="Ulangi password baru"
                               required>
                        <button type="button" onclick="togglePwd('password_confirmation','eye2')"
                                style="position:absolute;right:12px;top:50%;
                                       transform:translateY(-50%);background:none;
                                       border:none;color:#8a96a3;cursor:pointer;padding:0;">
                            <i class="bi bi-eye" id="eye2"></i>
                        </button>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-circle"></i>&nbsp; Simpan Password Baru
                    </button>
                </form>
            </div>
            <div class="card-foot">
                &copy; {{ date('Y') }} Balai Sains Bangunan — Sistem Monitoring Aset
            </div>
        </div>
    </div>
</div>

<script>
function togglePwd(id, iconId) {
    const inp  = document.getElementById(id);
    const icon = document.getElementById(iconId);
    inp.type   = inp.type === 'password' ? 'text' : 'password';
    icon.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function checkStrength(val) {
    const fill = document.getElementById('strengthFill');
    const text = document.getElementById('strengthText');
    let score = 0;
    if (val.length >= 8)   score++;
    if (/[0-9]/.test(val)) score++;
    if (/[\W_]/.test(val)) score++;
    if (val.length >= 12)  score++;

    const levels = [
        { pct: '0%',   color: '#e5e7eb', label: '' },
        { pct: '33%',  color: '#ef4444', label: '⚠️ Lemah' },
        { pct: '66%',  color: '#f59e0b', label: '👍 Sedang' },
        { pct: '85%',  color: '#3b82f6', label: '💪 Kuat' },
        { pct: '100%', color: '#22c55e', label: '🔒 Sangat Kuat' },
    ];

    fill.style.width      = levels[score].pct;
    fill.style.background = levels[score].color;
    text.textContent      = levels[score].label;
}
</script>
@endsection