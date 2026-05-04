<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Barang — {{ $item->name ?? 'Barang' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/PUPR.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --navy:     #1e3a5f;
            --navy-mid: #2d5a8e;
            --gold:     #e8b84b;
            --bg:       #f0f4fa;
            --surface:  #ffffff;
            --border:   rgba(30,58,95,0.10);
            --text:     #1e2d3d;
            --text-sub: #5a6a7e;
            --muted:    #8a96a3;
            --font:     'Plus Jakarta Sans', sans-serif;
            --mono:     'DM Mono', monospace;
            --radius:   16px;
            --shadow:   0 4px 24px rgba(30,58,95,0.12);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed; top: 0; left: 0; right: 0; height: 260px;
            background: linear-gradient(135deg, #012970 0%, #4154f1 100%);
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed; top: 210px; left: 0; right: 0; height: 60px;
            background: var(--bg);
            border-radius: 24px 24px 0 0;
            z-index: 0;
        }
        .page-wrap {
            position: relative; z-index: 1;
            max-width: 480px; margin: 0 auto; padding: 0 16px 40px;
        }

        /* Header */
        .qr-header { padding: 28px 0 20px; display: flex; align-items: center; gap: 12px; }
        .qr-header-logo { width: 48px; height: 48px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.2)); flex-shrink: 0; }
        .qr-header-text { flex: 1; }
        .qr-header-ministry { font-size: 0.62rem; font-weight: 500; color: rgba(255,255,255,0.7); line-height: 1.4; }
        .qr-header-unit { font-size: 0.7rem; font-weight: 800; color: var(--gold); letter-spacing: 0.5px; text-transform: uppercase; }
        .qr-scan-chip {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2);
            color: #fff; font-size: 0.65rem; font-weight: 700;
            padding: 4px 10px; border-radius: 20px; backdrop-filter: blur(4px); flex-shrink: 0;
        }

        /* Card */
        .qr-card { background: var(--surface); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; border: 1px solid var(--border); }

        /* Icon Banner */
        .qr-icon-banner {
            width: 100%; padding: 32px 20px;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
            background: linear-gradient(135deg, #eef2fb, #f4f6fd);
            border-bottom: 1px solid var(--border);
        }
        .qr-icon-circle {
            width: 76px; height: 76px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 2.2rem;
        }
        .icon-atk { background: rgba(65,84,241,0.10);  color: #4154f1; }
        .icon-rt  { background: rgba(16,185,129,0.12); color: #10b981; }
        .icon-lab { background: rgba(255,119,29,0.10); color: #ff771d; }
        .icon-def { background: rgba(30,58,95,0.08);   color: var(--navy); }
        .qr-icon-cat { font-size: 0.7rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }

        /* Body */
        .qr-body { padding: 20px; }

        .cat-badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.65rem; font-weight: 800; padding: 4px 12px; border-radius: 20px;
            letter-spacing: 0.6px; text-transform: uppercase; margin-bottom: 12px;
        }
        .cat-atk { background: rgba(65,84,241,0.10);  color: #4154f1; }
        .cat-rt  { background: rgba(16,185,129,0.12); color: #10b981; }
        .cat-lab { background: rgba(255,119,29,0.10); color: #ff771d; }
        .cat-def { background: rgba(30,58,95,0.08);   color: var(--navy); }

        .item-name { font-size: 1.15rem; font-weight: 800; color: var(--navy); line-height: 1.3; margin-bottom: 4px; }
        .item-code { font-family: var(--mono); font-size: 0.76rem; color: var(--text-sub); margin-bottom: 16px; }

        .qr-divider { height: 1px; background: var(--border); margin: 16px 0; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .info-label { font-size: 0.65rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px; }
        .info-value { font-size: 0.88rem; font-weight: 700; color: var(--text); }
        .saldo-big { font-size: 1.8rem; font-weight: 800; color: var(--navy); line-height: 1; }
        .saldo-unit { font-size: 0.72rem; color: var(--text-sub); font-weight: 600; margin-top: 3px; }

        .badge-reg, .badge-unreg {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 20px;
        }
        .badge-reg   { background: #e1f7ef; color: #0f7a45; }
        .badge-unreg { background: #fff4e5; color: #b45309; }

        /* Action */
        .qr-action { padding: 16px 20px; border-top: 1px solid var(--border); background: #fafbfd; }
        .btn-edit {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 13px 20px;
            background: linear-gradient(135deg, #012970, #4154f1);
            color: #fff; font-family: var(--font); font-size: 0.88rem; font-weight: 700;
            border: none; border-radius: 12px; text-decoration: none;
            box-shadow: 0 4px 16px rgba(65,84,241,0.3); transition: all 0.18s;
        }
        .btn-edit:hover { transform: translateY(-1px); box-shadow: 0 6px 22px rgba(65,84,241,0.4); color: #fff; }

        .login-prompt {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 14px; background: rgba(1,41,112,0.04);
            border: 1px solid var(--border); border-radius: 10px;
            font-size: 0.76rem; color: var(--text-sub);
        }
        .login-prompt i { color: var(--muted); font-size: 1rem; flex-shrink: 0; }
        .login-prompt a { color: #4154f1; font-weight: 700; text-decoration: none; }
        .login-prompt a:hover { text-decoration: underline; }

        /* Footer */
        .qr-footer { margin-top: 20px; text-align: center; }
        .qr-footer-text  { font-size: 0.68rem; color: rgba(255,255,255,0.5); line-height: 1.6; }
        .qr-footer-brand { font-size: 0.72rem; font-weight: 700; color: rgba(255,255,255,0.7); margin-top: 4px; }
    </style>
</head>
<body>
<div class="page-wrap">

    {{-- Header --}}
    <div class="qr-header">
        <img src="{{ asset('assets/img/PUPR.png') }}" alt="Logo PUPR" class="qr-header-logo"
             onerror="this.style.display='none'">
        <div class="qr-header-text">
            <div class="qr-header-ministry">KEMENTERIAN PEKERJAAN UMUM</div>
            <div class="qr-header-unit">Balai Teknik Sains Bangunan</div>
        </div>
        <div class="qr-scan-chip"><i class="bi bi-qr-code-scan"></i> BHP</div>
    </div>

    @php
        $cat       = $item->categories ?? '';
        $catClass  = match($cat) { 'ATK' => 'cat-atk', 'Rumah Tangga' => 'cat-rt', 'Laboratorium' => 'cat-lab', default => 'cat-def' };
        $iconClass = match($cat) { 'ATK' => 'icon-atk', 'Rumah Tangga' => 'icon-rt', 'Laboratorium' => 'icon-lab', default => 'icon-def' };
        $catIcon   = match($cat) { 'ATK' => 'bi-pen-fill', 'Rumah Tangga' => 'bi-house-fill', 'Laboratorium' => 'bi-eyedropper-fill', default => 'bi-box-seam' };
    @endphp

    {{-- Main Card --}}
    <div class="qr-card">

        {{-- Icon Banner --}}
        <div class="qr-icon-banner">
            <div class="qr-icon-circle {{ $iconClass }}">
                <i class="bi {{ $catIcon }}"></i>
            </div>
            <div class="qr-icon-cat">Barang Habis Pakai</div>
        </div>

        {{-- Body --}}
        <div class="qr-body">
            <div class="cat-badge {{ $catClass }}">
                <i class="bi {{ $catIcon }}"></i> {{ $cat ?: 'Umum' }}
            </div>
            <div class="item-name">{{ $item->name ?? '-' }}</div>
            <div class="item-code">
                <i class="bi bi-upc-scan" style="font-size:.7rem;color:var(--muted);margin-right:4px;"></i>
                Kode: {{ $item->code ?? '-' }}
            </div>

            <div class="qr-divider"></div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Saldo Tersedia</div>
                    <div class="saldo-big">{{ number_format($item->saldo ?? 0) }}</div>
                    <div class="saldo-unit">{{ $item->satuan ?? 'unit' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Satuan</div>
                    <div class="info-value" style="font-size:1rem;">{{ $item->satuan ?? '-' }}</div>
                </div>
                <div class="info-item" style="grid-column:span 2;">
                    <div class="info-label">Status Pencatatan</div>
                    @if($item->status)
                        <span class="badge-reg"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i> Teregister</span>
                    @else
                        <span class="badge-unreg"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i> Belum Teregister</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action --}}
        <div class="qr-action">
            @if($isLoggedIn)
                <a href="{{ route('items.edit', $item->id) }}" class="btn-edit">
                    <i class="bi bi-pencil-square"></i> Edit Data Barang Ini
                </a>
            @else
                <div class="login-prompt">
                    <i class="bi bi-lock-fill"></i>
                    <span><a href="{{ route('session.formLogin') }}">Login</a> untuk mengakses fitur edit data barang.</span>
                </div>
            @endif
        </div>

    </div>

    {{-- Footer --}}
    <div class="qr-footer">
        <div class="qr-footer-text">Sistem Monitoring Aset</div>
        <div class="qr-footer-brand">Direktorat Jenderal Cipta Karya · Balai Teknik Sains Bangunan</div>
    </div>

</div>
</body>
</html>
