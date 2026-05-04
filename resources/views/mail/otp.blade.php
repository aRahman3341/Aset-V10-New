<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7fc; margin: 0; padding: 20px; }
        .container { max-width: 480px; margin: 0 auto; background: #fff;
                     border-radius: 12px; overflow: hidden;
                     box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #003087, #1a56c4);
                  padding: 28px 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0 0 6px; font-size: 1.1rem; }
        .header p  { color: rgba(255,255,255,0.75); margin: 0; font-size: 0.8rem; }
        .body      { padding: 32px; }
        .otp-box   { background: #f0f5ff; border: 2px dashed #1a56c4;
                     border-radius: 10px; text-align: center; padding: 20px; margin: 24px 0; }
        .otp-box .code { font-size: 2.4rem; font-weight: 800;
                         color: #003087; letter-spacing: 8px; }
        .otp-box .exp  { font-size: 0.78rem; color: #6b7280; margin-top: 8px; }
        .warning { background: #fff7ed; border-left: 4px solid #f59e0b;
                   padding: 12px 16px; border-radius: 6px;
                   font-size: 0.82rem; color: #92400e; }
        .footer  { text-align: center; padding: 16px; font-size: 0.72rem;
                   color: #9ca3af; border-top: 1px solid #f3f4f6; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔐 Reset Password</h1>
        <p>Sistem Monitoring Aset — Balai Teknik Sains Bangunan PUPR</p>
    </div>
    <div class="body">
        <p>Halo, <strong>{{ $name }}</strong>!</p>
        <p>Kami menerima permintaan reset password untuk akun Anda.
           Gunakan kode OTP berikut:</p>

        <div class="otp-box">
            <div class="code">{{ $otp }}</div>
            <div class="exp">⏱ Berlaku selama <strong>10 menit</strong></div>
        </div>

        <div class="warning">
            ⚠️ <strong>Jangan bagikan kode ini</strong> kepada siapapun.
            Tim kami tidak pernah meminta kode OTP Anda.
            Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.
        </div>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Balai Sains Bangunan — Direktorat Jenderal Cipta Karya PUPR
    </div>
</div>
</body>
</html>