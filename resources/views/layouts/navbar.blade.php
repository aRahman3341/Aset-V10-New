<nav id="main-navbar" class="navbar navbar-expand-lg">
    <div class="container-fluid nav-container">

        {{-- Brand / App Name --}}
        <a class="nav-brand" href="{{ url('/') }}">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <span>MONITORING ASET</span>
        </a>

        {{-- Mobile Toggle --}}
        <button class="navbar-toggler-custom" id="navToggler" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>

        {{-- Nav Items --}}
        <div class="nav-collapse" id="navbarMain">
            <ul class="nav-list">

                {{-- Dashboard --}}
                <li class="nav-item-custom">
                    <a class="nav-link-custom {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- Data Aset --}}
                <li class="nav-item-custom nav-dropdown">
                    <a class="nav-link-custom {{ request()->is('asetTetap*') || request()->is('items*') ? 'active' : '' }}" href="#">
                        <i class="bi bi-layers"></i>
                        <span>Data Aset</span>
                        <i class="bi bi-chevron-down nav-chevron"></i>
                    </a>
                    <ul class="nav-dropdown-menu">
                        <li>
                            <a href="{{ url('asetTetap') }}" class="{{ request()->is('asetTetap*') ? 'active' : '' }}">
                                <i class="bi bi-archive"></i> Aset
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('items') }}" class="{{ request()->is('items*') ? 'active' : '' }}">
                                <i class="bi bi-box-seam"></i> Barang Habis Pakai
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Transaksi --}}
                <li class="nav-item-custom nav-dropdown">
                    <a class="nav-link-custom {{ request()->is('peminjaman*') || request()->is('asetkeluar*') ? 'active' : '' }}" href="#">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Transaksi</span>
                        <i class="bi bi-chevron-down nav-chevron"></i>
                    </a>
                    <ul class="nav-dropdown-menu">
                        <li>
                            <a href="{{ url('asetkeluar') }}" class="{{ request()->is('asetkeluar*') ? 'active' : '' }}">
                                <i class="bi bi-box-arrow-right"></i> Aset Keluar
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('peminjaman') }}" class="{{ request()->is('peminjaman') || request()->is('peminjaman/add') || request()->is('peminjaman/edit*') ? 'active' : '' }}">
                                <i class="bi bi-clipboard-check"></i> Peminjaman
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Master --}}
                <li class="nav-item-custom nav-dropdown">
                    <a class="nav-link-custom {{ request()->is('location*') || request()->is('category*') || request()->is('pengguna*') ? 'active' : '' }}" href="#">
                        <i class="bi bi-database"></i>
                        <span>Master</span>
                        <i class="bi bi-chevron-down nav-chevron"></i>
                    </a>
                    <ul class="nav-dropdown-menu">
                        <li>
                            <a href="{{ url('location') }}" class="{{ request()->is('location*') ? 'active' : '' }}">
                                <i class="bi bi-geo-alt"></i> Lokasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('category') }}" class="{{ request()->is('category*') ? 'active' : '' }}">
                                <i class="bi bi-tag"></i> Kategori
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('pengguna') }}" class="{{ request()->is('pengguna*') ? 'active' : '' }}">
                                <i class="bi bi-people"></i> Pengguna
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Report --}}
                <li class="nav-item-custom nav-dropdown">
                    <a class="nav-link-custom {{ request()->is('peminjaman/report*') || request()->is('asetkeluar/report*') ? 'active' : '' }}" href="#">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Report</span>
                        <i class="bi bi-chevron-down nav-chevron"></i>
                    </a>
                    <ul class="nav-dropdown-menu">
                        <li>
                            <a href="{{ route('peminjaman.report-peminjaman') }}" class="{{ request()->is('peminjaman/report*') ? 'active' : '' }}">
                                <i class="bi bi-clipboard-data"></i> Peminjaman
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('asetkeluar.report-asetkeluar') }}" class="{{ request()->is('asetkeluar/report*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-arrow-up"></i> Aset Keluar
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>

            {{-- Right: User Profile --}}
            <div class="nav-right">
                <div class="nav-item-custom nav-dropdown">
                    <a class="nav-user" href="#">
                        <div class="nav-user-avatar">
                            <img src="{{ asset('assets/img/logo2.png') }}" alt="Profile">
                        </div>
                        <div class="nav-user-info">
                            <span class="nav-user-name">{{ Auth::user()->name ?? 'Pengguna' }}</span>
                            <span class="nav-user-role">{{ Auth::user()->jabatan ?? '' }}</span>
                        </div>
                        <i class="bi bi-chevron-down nav-chevron" style="font-size:0.65rem; margin-left:4px;"></i>
                    </a>
                    <ul class="nav-dropdown-menu nav-dropdown-right">
                        <li>
                            <a href="{{ route('session.logout') }}" class="text-danger">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    /* ── Variables (fallback) ── */
    :root {
        --navy:      #1e3a5f;
        --navy-mid:  #2d5a8e;
        --gold:      #e8b84b;
        --navbar-h:  52px;
    }

    /* ── Navbar Base ── */
    #main-navbar {
        background: linear-gradient(90deg, var(--navy) 0%, var(--navy-mid) 100%);
        height: var(--navbar-h);
        padding: 0;
        border-top: 2.5px solid var(--gold);
    }

    .nav-container {
        padding: 0 20px;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 0;
    }

    /* ── Brand ── */
    .nav-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #fff !important;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 0 16px 0 0;
        white-space: nowrap;
        border-right: 1px solid rgba(255,255,255,0.15);
        margin-right: 8px;
    }
    .nav-brand i {
        color: var(--gold);
        font-size: 1rem;
    }
    .nav-brand:hover { color: var(--gold) !important; }

    /* ── Collapse wrapper ── */
    .nav-collapse {
        display: flex;
        align-items: center;
        flex: 1;
        height: 100%;
    }

    /* ── Nav list ── */
    .nav-list {
        display: flex;
        align-items: center;
        list-style: none;
        margin: 0;
        padding: 0;
        height: 100%;
        gap: 2px;
    }

    /* ── Nav Item ── */
    .nav-item-custom {
        position: relative;
        height: 100%;
        display: flex;
        align-items: center;
    }

    /* ── Nav Link ── */
    .nav-link-custom {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 0 14px;
        height: 100%;
        color: rgba(255,255,255,0.82) !important;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        transition: all 0.18s ease;
        white-space: nowrap;
        border-bottom: 2px solid transparent;
        position: relative;
    }
    .nav-link-custom i:not(.nav-chevron) { font-size: 0.9rem; }
    .nav-chevron {
        font-size: 0.6rem;
        opacity: 0.7;
        transition: transform 0.2s ease;
    }

    .nav-link-custom:hover {
        color: #fff !important;
        border-bottom-color: var(--gold);
        background: rgba(255,255,255,0.06);
    }
    .nav-link-custom.active {
        color: var(--gold) !important;
        border-bottom-color: var(--gold);
        background: rgba(232,184,75,0.08);
    }

    /* ── Dropdown ── */
    .nav-dropdown-menu {
        position: absolute;
        top: calc(100% + 2px);
        left: 0;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 28px rgba(30,58,95,0.15);
        list-style: none;
        margin: 0;
        padding: 6px;
        min-width: 190px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(6px);
        transition: all 0.18s ease;
        z-index: 2000;
        border-top: 2px solid var(--gold);
    }
    .nav-dropdown-menu li a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--navy);
        text-decoration: none;
        border-radius: 7px;
        transition: all 0.15s ease;
    }
    .nav-dropdown-menu li a i { font-size: 0.85rem; color: var(--navy-mid); }
    .nav-dropdown-menu li a:hover {
        background: rgba(30,58,95,0.07);
        color: var(--navy);
        padding-left: 18px;
    }
    .nav-dropdown-menu li a.active {
        background: rgba(30,58,95,0.1);
        color: var(--navy);
        font-weight: 700;
    }
    .nav-dropdown-menu li a.text-danger { color: #dc2626 !important; }
    .nav-dropdown-menu li a.text-danger i { color: #dc2626 !important; }
    .nav-dropdown-menu li a.text-danger:hover { background: rgba(220,38,38,0.07); }

    /* Show dropdown on hover */
    .nav-dropdown:hover > .nav-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .nav-dropdown:hover > .nav-link-custom .nav-chevron {
        transform: rotate(180deg);
    }

    /* Dropdown aligned to right */
    .nav-dropdown-right {
        left: auto;
        right: 0;
    }

    /* ── Right section ── */
    .nav-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        height: 100%;
    }

    /* ── User profile ── */
    .nav-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 12px;
        height: 100%;
        cursor: pointer;
        text-decoration: none;
        border-left: 1px solid rgba(255,255,255,0.12);
        transition: background 0.18s;
    }
    .nav-user:hover { background: rgba(255,255,255,0.07); }

    .nav-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid rgba(255,255,255,0.25);
        flex-shrink: 0;
    }
    .nav-user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .nav-user-info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }
    .nav-user-name {
        font-size: 0.78rem;
        font-weight: 700;
        color: #fff;
        white-space: nowrap;
    }
    .nav-user-role {
        font-size: 0.67rem;
        color: rgba(255,255,255,0.6);
        white-space: nowrap;
    }

    /* ── Mobile Toggle ── */
    .navbar-toggler-custom {
        display: none;
        flex-direction: column;
        gap: 5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        margin-left: auto;
    }
    .navbar-toggler-custom span {
        display: block;
        width: 22px;
        height: 2px;
        background: #fff;
        border-radius: 2px;
        transition: all 0.2s ease;
    }

    /* ── Responsive ── */
    @media (max-width: 991px) {
        .navbar-toggler-custom { display: flex; }
        .nav-collapse {
            display: none;
            position: fixed;
            top: var(--total-h, 132px);
            left: 0;
            right: 0;
            background: var(--navy);
            flex-direction: column;
            align-items: stretch;
            height: auto;
            max-height: calc(100vh - 132px);
            overflow-y: auto;
            z-index: 1035;
            padding: 12px;
            gap: 4px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        .nav-collapse.open { display: flex; }
        .nav-list { flex-direction: column; height: auto; gap: 0; width: 100%; }
        .nav-item-custom { height: auto; flex-direction: column; align-items: stretch; }
        .nav-link-custom { height: auto; padding: 10px 14px; border-bottom: none; border-radius: 8px; }
        .nav-link-custom:hover { background: rgba(255,255,255,0.1); }
        .nav-dropdown-menu {
            position: static;
            opacity: 1;
            visibility: visible;
            transform: none;
            box-shadow: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.06);
            border-top: none;
            margin: 0 0 6px 12px;
            display: none;
        }
        .nav-dropdown-menu li a { color: rgba(255,255,255,0.82); }
        .nav-dropdown-menu li a:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .nav-dropdown-menu li a i { color: rgba(255,255,255,0.5); }
        .nav-dropdown.open > .nav-dropdown-menu { display: block; }
        .nav-right { height: auto; margin-left: 0; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; margin-top: 4px; }
        .nav-user { border-left: none; padding: 10px 14px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggler = document.getElementById('navToggler');
        const collapse = document.getElementById('navbarMain');
        const dropdowns = document.querySelectorAll('.nav-dropdown');

        // Mobile toggle
        toggler.addEventListener('click', () => {
            collapse.classList.toggle('open');
        });

        // Mobile dropdown toggle
        dropdowns.forEach(dd => {
            const link = dd.querySelector('.nav-link-custom, .nav-user');
            if (!link) return;
            link.addEventListener('click', (e) => {
                if (window.innerWidth <= 991) {
                    e.preventDefault();
                    dd.classList.toggle('open');
                }
            });
        });
    });
</script>