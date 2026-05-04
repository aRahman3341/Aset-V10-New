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
    .otp-inputs{display:flex;gap:10px;justify-content:center;margin:20px 0;}
    .otp-inputs input{width:50px;height:56px;text-align:center;font-size:1.5rem;
        font-weight:800;border:2px solid rgba(30,58,95,.15);border-radius:10px;
        background:#f4f7fc;color:#003087;outline:none;transition:all .18s;font-family:inherit;}
    .otp-inputs input:focus{border-color:#1a56c4;background:#fff;
        box-shadow:0 0 0 3px rgba(26,86,196,.12);}
    .btn-submit{width:100%;padding:11px;background:linear-gradient(135deg,#003087,#1a56c4);
        color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;
        cursor:pointer;font-family:inherit;box-shadow:0 4px 14px rgba(0,48,135,.30);
        transition:all .18s;}
    .btn-submit:hover{transform:translateY(-1px);}
    .alert-ok{background:#f0fdf4;border:1px solid rgba(34,197,94,.3);border-radius:8px;
        padding:10px 14px;font-size:.8rem;color:#166534;margin-bottom:16px;text-align:center;}
    .alert-err{background:#fef2f2;border:1px solid rgba(220,53,69,.2);border-radius:8px;
        padding:10px 14px;font-size:.8rem;color:#991b1b;margin-bottom:16px;text-align:center;}
    .resend-area{text-align:center;margin-top:16px;font-size:.8rem;color:#6b7280;}
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
                <h1>✉️ Verifikasi OTP</h1>
                <p>Masukkan 6 digit kode yang dikirim ke email Anda</p>
            </div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert-ok">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-err">
                        <i class="bi bi-x-circle-fill"></i> {{ $errors->first() }}
                    </div>
                @endif

                <p style="text-align:center;font-size:.85rem;color:#6b7280;margin:0 0 4px;">
                    Kode dikirim ke:
                    <strong style="color:#003087;">{{ session('otp_email') }}</strong>
                </p>
                <p style="text-align:center;font-size:.78rem;color:#9ca3af;margin:0;">
                    ⏱ Berlaku 10 menit
                </p>

                <form action="{{ route('password.verifyOtp') }}" method="POST" id="otpForm">
                    @csrf
                    <input type="hidden" name="otp" id="otpHidden">

                    <div class="otp-inputs">
                        @for($i = 1; $i <= 6; $i++)
                            <input type="text" class="otp-digit"
                                   maxlength="1" inputmode="numeric"
                                   pattern="[0-9]" autocomplete="off">
                        @endfor
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="bi bi-shield-check"></i>&nbsp; Verifikasi
                    </button>
                </form>

                <div class="resend-area">
                    Tidak menerima kode?
                    <form action="{{ route('password.resendOtp') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit"
                                style="background:none;border:none;color:#1a56c4;cursor:pointer;
                                       font-size:.8rem;font-family:inherit;
                                       text-decoration:underline;padding:0;">
                            Kirim ulang OTP
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-foot">
                <a href="{{ route('password.forgot') }}"
                   style="color:#1a56c4;text-decoration:none;">
                    <i class="bi bi-arrow-left"></i> Ulangi dari awal
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const digits = document.querySelectorAll('.otp-digit');

digits.forEach((input, idx) => {
    input.addEventListener('input', () => {
        input.value = input.value.replace(/[^0-9]/g, '');
        if (input.value && idx < digits.length - 1) {
            digits[idx + 1].focus();
        }
        updateHidden();
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !input.value && idx > 0) {
            digits[idx - 1].focus();
        }
    });

    input.addEventListener('paste', e => {
        const pasted = e.clipboardData.getData('text')
                        .replace(/[^0-9]/g, '').slice(0, 6);
        if (pasted.length === 6) {
            digits.forEach((d, i) => { d.value = pasted[i] || ''; });
            updateHidden();
            digits[5].focus();
            e.preventDefault();
        }
    });
});

function updateHidden() {
    document.getElementById('otpHidden').value =
        Array.from(digits).map(d => d.value).join('');
}

document.getElementById('otpForm').addEventListener('submit', function(e) {
    updateHidden();
    const otp = document.getElementById('otpHidden').value;
    if (otp.length !== 6) {
        e.preventDefault();
        alert('Masukkan 6 digit kode OTP terlebih dahulu.');
    }
});
</script>
@endsection