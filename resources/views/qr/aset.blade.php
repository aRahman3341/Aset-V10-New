<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Aset — {{ $item->{'Nama Barang'} ?? $item->{'name'} ?? 'Aset' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/PUPR.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --navy:       #1e3a5f;
            --navy-mid:   #2d5a8e;
            --navy-light: #3d74b0;
            --gold:       #e8b84b;
            --gold-light: #f5d680;
            --bg:         #f0f4fa;
            --surface:    #ffffff;
            --border:     rgba(30,58,95,0.10);
            --text:       #1e2d3d;
            --text-sub:   #5a6a7e;
            --text-muted: #8a96a3;
            --font:       'Plus Jakarta Sans', sans-serif;
            --mono:       'DM Mono', monospace;
            --radius:     16px;
            --shadow:     0 4px 24px rgba(30,58,95,0.12);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Background decoration ── */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 260px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            top: 210px; left: 0; right: 0;
            height: 60px;
            background: var(--bg);
            border-radius: 24px 24px 0 0;
            z-index: 0;
        }

        .page-wrap {
            position: relative;
            z-index: 1;
            max-width: 480px;
            margin: 0 auto;
            padding: 0 16px 40px;
        }

        /* ── Header ── */
        .qr-header {
            padding: 28px 0 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .qr-header-logo {
            width: 48px; height: 48px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.2));
            flex-shrink: 0;
        }
        .qr-header-text { flex: 1; }
        .qr-header-ministry {
            font-size: 0.62rem;
            font-weight: 500;
            color: rgba(255,255,255,0.7);
            letter-spacing: 0.3px;
            line-height: 1.4;
        }
        .qr-header-unit {
            font-size: 0.7rem;
            font-weight: 800;
            color: var(--gold);
            letter-spacing: 0.5px;
            line-height: 1.4;
            text-transform: uppercase;
        }
        .qr-scan-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            backdrop-filter: blur(4px);
            flex-shrink: 0;
        }

        /* ── Main Card ── */
        .qr-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        /* ── Photo ── */
        .qr-photo-wrap {
            width: 100%;
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, #e8edf5, #f0f4fa);
            position: relative;
            overflow: hidden;
        }
        .qr-photo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .qr-photo-empty {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .qr-photo-empty i { font-size: 2.5rem; color: #c8d0dd; }
        .qr-photo-empty span { font-size: 0.72rem; color: var(--text-muted); font-weight: 600; }
        .qr-photo-badge {
            position: absolute;
            top: 10px; right: 10px;
            background: rgba(30,58,95,0.7);
            backdrop-filter: blur(6px);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
        }

        /* ── Body ── */
        .qr-body { padding: 20px; }

        /* Asset type badge */
        .asset-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, var(--navy), var(--navy-mid));
            color: #fff;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        /* Asset name */
        .asset-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--navy);
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .asset-merk {
            font-size: 0.8rem;
            color: var(--text-sub);
            font-weight: 500;
            margin-bottom: 16px;
        }

        /* Divider */
        .qr-divider {
            height: 1px;
            background: var(--border);
            margin: 16px 0;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .info-grid.full { grid-template-columns: 1fr; }

        .info-item {}
        .info-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.3;
        }
        .info-value.mono {
            font-family: var(--mono);
            font-size: 0.8rem;
            color: var(--navy-mid);
            background: rgba(30,58,95,0.06);
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        /* Badges */
        .badge-kondisi, .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .badge-baik    { background: #e1f7ef; color: #0f7a45; }
        .badge-ringan  { background: #fff4e5; color: #b45309; }
        .badge-berat   { background: #fee2e2; color: #b91c1c; }
        .badge-aktif   { background: rgba(30,58,95,0.08); color: var(--navy); }
        .badge-nonaktif{ background: #f3f4f6; color: #6b7280; }

        /* ── Action Section ── */
        .qr-action {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
            background: #fafbfd;
        }
        .btn-edit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, var(--navy), var(--navy-mid));
            color: #fff;
            font-family: var(--font);
            font-size: 0.88rem;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(30,58,95,0.25);
            transition: all 0.18s ease;
        }
        .btn-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(30,58,95,0.35);
            color: #fff;
        }
        .btn-edit i { font-size: 1rem; }

        /* ── Login prompt ── */
        .login-prompt {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            background: rgba(30,58,95,0.04);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.76rem;
            color: var(--text-sub);
        }
        .login-prompt i { color: var(--text-muted); font-size: 1rem; flex-shrink: 0; }
        .login-prompt a { color: var(--navy-mid); font-weight: 700; text-decoration: none; }
        .login-prompt a:hover { text-decoration: underline; }

        /* ── Footer ── */
        .qr-footer {
            margin-top: 20px;
            text-align: center;
        }
        .qr-footer-text {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.6;
        }
        .qr-footer-brand {
            font-size: 0.72rem;
            font-weight: 700;
            color: rgba(255,255,255,0.7);
            margin-top: 4px;
        }

        /* ── 404 State ── */
        .not-found {
            padding: 60px 20px;
            text-align: center;
        }
        .not-found i { font-size: 3rem; color: #c8d0dd; display: block; margin-bottom: 12px; }
        .not-found h2 { font-size: 1.1rem; font-weight: 700; color: var(--navy); margin-bottom: 6px; }
        .not-found p { font-size: 0.82rem; color: var(--text-sub); }

        @media (max-width: 480px) {
            .info-grid { grid-template-columns: 1fr 1fr; }
            .asset-name { font-size: 1rem; }
        }
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
        <div class="qr-scan-chip">
            <i class="bi bi-qr-code-scan"></i> ASET
        </div>
    </div>

    {{-- Main Card --}}
    <div class="qr-card">

        {{-- Foto --}}
        <div class="qr-photo-wrap">
            @if($photo)
                <img src="{{ asset('assets/upload_asset_tetap/' . $photo->filename) }}"
                     alt="{{ $item->{'Nama Barang'} ?? '' }}"
                     onerror="this.parentElement.innerHTML='<div class=\'qr-photo-empty\'><i class=\'bi bi-image\'></i><span>Foto tidak tersedia</span></div>'">
                <div class="qr-photo-badge"><i class="bi bi-camera-fill"></i> Foto Aset</div>
            @else
                <div class="qr-photo-empty">
                    <i class="bi bi-image"></i>
                    <span>Belum ada foto</span>
                </div>
            @endif
        </div>

        {{-- Info Body --}}
        <div class="qr-body">

            <div class="asset-type-badge">
                <i class="bi bi-building"></i>
                Aset Tetap · {{ $item->{'Jenis BMN'} ?? '-' }}
            </div>

            <div class="asset-name">{{ $item->{'Nama Barang'} ?? $item->name ?? '-' }}</div>
            @if(!empty($item->merk))
                <div class="asset-merk"><i class="bi bi-tag-fill" style="font-size:.7rem;color:var(--text-muted);"></i> {{ $item->merk }}</div>
            @endif

            <div class="qr-divider"></div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Kode Barang</div>
                    <div class="info-value mono">{{ $item->{'Kode Barang'} ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NUP</div>
                    <div class="info-value mono">{{ $item->nup ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kondisi</div>
                    @php
                        $kondisi = $item->kondisi ?? 'Baik';
                        $badgeClass = match($kondisi) {
                            'Rusak Ringan' => 'badge-ringan',
                            'Rusak Berat'  => 'badge-berat',
                            default        => 'badge-baik',
                        };
                    @endphp
                    <span class="badge-kondisi {{ $badgeClass }}">
                        <i class="bi bi-circle-fill" style="font-size:.45rem;"></i>
                        {{ $kondisi }}
                    </span>
                </div>
                <div class="info-item">
                    <div class="info-label">Status BMN</div>
                    @php
                        $statusBmn  = $item->{'Status BMN'} ?? 'Aktif';
                        $statusClass = $statusBmn === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif';
                    @endphp
                    <span class="badge-status {{ $statusClass }}">
                        <i class="bi bi-circle-fill" style="font-size:.45rem;"></i>
                        {{ $statusBmn }}
                    </span>
                </div>
                @if(!empty($item->{'Tanggal Perolehan'}))
                <div class="info-item">
                    <div class="info-label">Tgl Perolehan</div>
                    <div class="info-value" style="font-size:.8rem;">
                        {{ \Carbon\Carbon::parse($item->{'Tanggal Perolehan'})->format('d/m/Y') }}
                    </div>
                </div>
                @endif
                @if(!empty($item->{'No PSP'}))
                <div class="info-item">
                    <div class="info-label">No PSP</div>
                    <div class="info-value" style="font-size:.8rem;">{{ $item->{'No PSP'} }}</div>
                </div>
                @endif
            </div>

        </div>

        {{-- Action --}}
        <div class="qr-action">
            @if($isLoggedIn)
                <a href="{{ route('asetTetap.edit', $item->id) }}" class="btn-edit">
                    <i class="bi bi-pencil-square"></i>
                    Edit Data Aset Ini
                </a>
            @else
                <div class="login-prompt">
                    <i class="bi bi-lock-fill"></i>
                    <span>
                        <a href="{{ route('session.formLogin') }}">Login</a>
                        untuk mengakses fitur edit data aset.
                    </span>
                </div>
            @endif
        </div>

    </div>

    {{-- Footer --}}
    <div class="qr-footer">
        <div class="qr-footer-text">Sistem Monitoring Aset</div>
        <div class="qr-footer-brand">
            Direktorat Jenderal Cipta Karya · Balai Teknik Sains Bangunan
        </div>
    </div>

</div>

</body>
</html>
