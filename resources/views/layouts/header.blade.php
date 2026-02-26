<header id="site-header" class="fixed-top">

    {{-- ── Logo / Institutional Row ── --}}
    <div class="header-brand">

        {{-- Kiri: Logo + Teks (latar putih bersih) --}}
        <div class="header-left">
            <img src="{{ asset('assets/img/PUPR.png') }}" alt="Logo PUPR" class="header-pupr-logo">
            <div class="header-divider"></div>
            <div class="header-institution">
                <span class="inst-ministry">KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT</span>
                <span class="inst-unit">
                    <b>DIREKTORAT JENDERAL CIPTA KARYA</b><br>
                    <b>DIREKTORAT BINA TEKNIK PERMUKIMAN DAN PERUMAHAN</b><br>
                    <b>BALAI SAINS BANGUNAN</b>
                </span>
            </div>
        </div>

        {{-- Kanan: Foto sebagai background area kanan --}}
        <div class="header-right-photo" style="background-image: url('{{ asset('assets/img/KementrianRI.jpg') }}')">
            <div class="header-right-fade"></div>
        </div>

    </div>

    {{-- ── Navbar Row ── --}}
    @include('layouts.navbar')

</header>

<div style="height: 147px;"></div>

<style>
    :root {
        --navy:     #1e3a5f;
        --navy-mid: #2d5a8e;
        --gold:     #e8b84b;
    }

    #site-header {
        box-shadow: 0 2px 16px rgba(30, 58, 95, 0.12);
        z-index: 1040;
    }

    /* ── Brand row ── */
    .header-brand {
        display: flex;
        align-items: stretch;
        height: 95px;
        background: #ffffff;
        border-bottom: 1px solid rgba(30, 58, 95, 0.10);
        overflow: hidden;
        position: relative;
    }

    /* ── Kiri: putih bersih ── */
    .header-left {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 0 20px;
        flex: 1;
        background: #fff;
        position: relative;
        z-index: 1;
    }

    .header-pupr-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
        flex-shrink: 0;
    }

    .header-divider {
        width: 2px;
        height: 60px;
        background: linear-gradient(to bottom, var(--gold), rgba(232,184,75,0.15));
        border-radius: 2px;
        flex-shrink: 0;
    }

    .header-institution {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .inst-ministry {
        font-size: 0.72rem;
        font-weight: 400;
        color: #555;
        line-height: 1.5;
    }
    .inst-unit {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--navy);
        line-height: 1.6;
    }

    /* ── Kanan: foto sebagai background ── */
    .header-right-photo {
        width: 280px;
        flex-shrink: 0;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        position: relative;
    }

    /* Fade dari putih (kiri) ke transparan (kanan) agar menyambung halus */
    .header-right-fade {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, #ffffff 0%, rgba(255,255,255,0) 40%);
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .header-brand { height: auto; min-height: 70px; }
        .header-left { padding: 10px 14px; gap: 10px; }
        .header-pupr-logo { width: 50px; height: 50px; }
        .header-divider { height: 40px; }
        .header-right-photo { display: none; }
        .inst-ministry { font-size: 0.6rem; }
        .inst-unit { font-size: 0.65rem; }
    }
</style>