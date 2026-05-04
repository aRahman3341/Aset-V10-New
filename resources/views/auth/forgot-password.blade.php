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
    .inp{width:100%;padding:10px 12px 10px 36px;font-size:.88rem;color:#1e3a5f;
         background:#f4f7fc;border:1.5px solid rgba(30,58,95,.12);
         border-radius:10px;outline:none;transition:all .18s;font-family:inherit;}
    .inp:focus{background:#fff;border-color:#1a56c4;box-shadow:0 0 0 3px rgba(26,86,196,.12);}
    .inp.is-invalid{border-color:#dc3545;}
    .err{font-size:.73rem;color:#dc3545;margin-top:3px;display:block;}
    .btn-submit{width:100%;padding:11px;background:linear-gradient(135deg,#003087,#1a56c4);
        color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;
        cursor:pointer;font-family:inherit;margin-top:4px;
        box-shadow:0 4px 14px rgba(0,48,135,.30);transition:all .18s;}
    .btn-submit:hover{transform:translateY(-1px);}
    .alert-ok{background:#f0fdf4;border:1px solid rgba(34,197,94,.3);border-radius:8px;
        padding:10px 14px;font-size:.8rem;color:#166534;margin-bottom:16px;
        display:flex;align-items:center;gap:8px;}
    .alert-err{background:#fef2f2;border:1px solid rgba(220,53,69,.2);border-radius:8px;
        padding:10px 14px;font-size:.8rem;color:#991b1b;margin-bottom:16px;}
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
                <h1>🔑 Lupa Kata Sandi</h1>
                <p>Masukkan Email dan NIP terdaftar Anda</p>
            </div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert-ok">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-err">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('password.sendOtp') }}" method="POST">
                    @csrf

                    <label class="lbl" for="email">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <div class="inp-wrap">
                        <i class="bi bi-envelope inp-icon"></i>
                        <input type="email" id="email" name="email"
                               class="inp {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email') }}"
                               placeholder="email@perusahaan.com"
                               required autofocus>
                    </div>
                    @error('email')<span class="err">{{ $message }}</span>@enderror

                    <label class="lbl" for="nip">
                        <i class="bi bi-person-badge"></i> NIP
                    </label>
                    <div class="inp-wrap">
                        <i class="bi bi-person-badge inp-icon"></i>
                        <input type="text" id="nip" name="nip"
                               class="inp {{ $errors->has('nip') ? 'is-invalid' : '' }}"
                               value="{{ old('nip') }}"
                               placeholder="Nomor Induk Pegawai"
                               maxlength="12" required>
                    </div>
                    @error('nip')<span class="err">{{ $message }}</span>@enderror

                    <button type="submit" class="btn-submit">
                        <i class="bi bi-send"></i>&nbsp; Kirim Kode OTP
                    </button>
                </form>
            </div>
            <div class="card-foot">
                <a href="{{ route('session.formLogin') }}"
                   style="color:#1a56c4;text-decoration:none;">
                    <i class="bi bi-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection