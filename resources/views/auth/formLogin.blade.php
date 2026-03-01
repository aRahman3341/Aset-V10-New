@extends('layouts.app')

@section('content')
<style>
    /* ── Background tetap sama ── */
    .login-bg {
        background-image: url('{!! asset("assets/img/bg.png") !!}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ── Overlay gelap tipis agar card lebih terbaca ── */
    .login-overlay {
        position: fixed;
        inset: 0;
        background: rgba(10, 25, 55, 0.45);
        z-index: 0;
    }

    /* ── Card Login ── */
    .login-wrap {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 420px;
        padding: 0 16px;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.97);
        border: none;
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(0, 30, 90, 0.30);
        overflow: hidden;
    }

    /* Header kartu */
    .login-card-header {
        background: linear-gradient(135deg, #003087, #1a56c4);
        padding: 28px 32px 22px;
        text-align: center;
    }

    .login-card-header .logo-wrap {
        width: 70px;
        height: 70px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        border: 3px solid rgba(255,255,255,0.6);
        box-shadow: 0 4px 16px rgba(0,0,0,0.18);
        overflow: hidden;
    }

    .login-card-header .logo-wrap img {
        width: 54px;
        height: 54px;
        object-fit: contain;
    }

    .login-card-header h1 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #fff;
        margin: 0 0 3px;
        letter-spacing: 0.2px;
    }

    .login-card-header p {
        font-size: 0.72rem;
        color: rgba(255,255,255,0.75);
        margin: 0;
        line-height: 1.4;
    }

    /* Body kartu */
    .login-card-body {
        padding: 28px 32px 32px;
    }

    /* Label */
    .login-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #3d5170;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: block;
        margin-bottom: 5px;
    }

    /* Input group */
    .login-input-wrap {
        position: relative;
        margin-bottom: 18px;
    }

    .login-input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a96a3;
        font-size: 0.95rem;
        pointer-events: none;
    }

    .login-input {
        width: 100%;
        padding: 10px 12px 10px 36px;
        font-size: 0.88rem;
        color: #1e3a5f;
        background: #f4f7fc;
        border: 1.5px solid rgba(30,58,95,0.12);
        border-radius: 10px;
        outline: none;
        transition: all .18s;
        font-family: inherit;
    }

    .login-input:focus {
        background: #fff;
        border-color: #1a56c4;
        box-shadow: 0 0 0 3px rgba(26,86,196,0.12);
    }

    .login-input.is-invalid {
        border-color: #dc3545;
        background: #fff8f8;
    }

    .login-error {
        font-size: 0.73rem;
        color: #dc3545;
        margin-top: 4px;
        display: block;
    }

    /* Alert error session */
    .login-alert {
        background: #fef2f2;
        border: 1px solid rgba(220,53,69,0.2);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.8rem;
        color: #991b1b;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Submit button */
    .login-btn {
        width: 100%;
        padding: 11px;
        background: linear-gradient(135deg, #003087, #1a56c4);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 14px rgba(0,48,135,0.30);
        transition: all .18s;
        margin-top: 4px;
    }

    .login-btn:hover {
        background: linear-gradient(135deg, #002070, #1246a8);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0,48,135,0.38);
    }

    .login-btn:active { transform: translateY(0); }

    /* Footer kartu */
    .login-card-footer {
        text-align: center;
        padding: 12px 32px 18px;
        font-size: 0.72rem;
        color: #8a96a3;
        border-top: 1px solid rgba(30,58,95,0.07);
    }

    /* Sembunyikan navbar/sidebar default jika ada */
    #header, #sidebar, .sidebar, header.header { display: none !important; }
    #main { margin: 0 !important; padding: 0 !important; }
    body { padding: 0 !important; margin: 0 !important; }
</style>

<div class="login-bg">
    <div class="login-overlay"></div>

    <div class="login-wrap">
        <div class="login-card">

            {{-- ── Header ── --}}
            <div class="login-card-header">
                <div class="logo-wrap">
                    {{--
                        Coba load PUPR.png via asset().
                        Jika gagal (file tidak ada / case-sensitive), tampilkan
                        inisial "BSB" sebagai fallback agar header tetap rapi.
                    --}}
                    <img src="{{ asset('assets/img/PUPR.png') }}"
                         alt="Logo PUPR"
                         id="logoImg"
                         style="width:54px;height:54px;object-fit:contain;"
                         onerror="
                            this.style.display='none';
                            document.getElementById('logoFallback').style.display='flex';
                         ">
                    <div id="logoFallback"
                         style="display:none;width:42px;height:42px;align-items:center;
                                justify-content:center;font-size:0.75rem;font-weight:800;
                                color:#fff;letter-spacing:0.5px;line-height:1.1;text-align:center;">
                        BSB
                    </div>
                </div>
                <h1>Monitoring Aset</h1>
                <p>BALAI SAINS BANGUNAN<br>Direktorat Jenderal Cipta Karya — PUPR</p>
            </div>

            {{-- ── Body ── --}}
            <div class="login-card-body">

                @if (session('error'))
                    <div class="login-alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ url('session/login') }}" method="POST" autocomplete="off">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="login-label" for="email">
                            <i class="bi bi-envelope"></i>&nbsp; Email
                        </label>
                        <div class="login-input-wrap">
                            <i class="bi bi-envelope login-input-icon"></i>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="login-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                   value="{{ Session::get('email') ?? old('email') }}"
                                   placeholder="email@example.com"
                                   required
                                   autofocus>
                        </div>
                        @error('email')
                            <span class="login-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="login-label" for="password">
                            <i class="bi bi-lock"></i>&nbsp; Password
                        </label>
                        <div class="login-input-wrap" style="margin-bottom:8px">
                            <i class="bi bi-lock login-input-icon"></i>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="login-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="••••••••"
                                   required>
                            {{-- Toggle show/hide password --}}
                            <button type="button"
                                    onclick="togglePassword()"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#8a96a3;cursor:pointer;padding:0;font-size:0.95rem;"
                                    tabindex="-1">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="login-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="bi bi-box-arrow-in-right"></i>&nbsp; Masuk
                    </button>
                </form>
            </div>

            {{-- ── Footer ── --}}
            <div class="login-card-footer">
                &copy; {{ date('Y') }} Balai Sains Bangunan &mdash; Sistem Monitoring Aset
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input   = document.getElementById('password');
    const icon    = document.getElementById('eyeIcon');
    const isHide  = input.type === 'password';
    input.type    = isHide ? 'text' : 'password';
    icon.className = isHide ? 'bi bi-eye-slash' : 'bi bi-eye';
}
</script>
@endsection