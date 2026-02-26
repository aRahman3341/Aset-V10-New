<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Monitoring Aset — BSB')</title>
    <meta content="" name="description">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Main CSS -->
    <link href="{{ asset('assets/css/default.css') }}" rel="stylesheet">

    <style>
        /* ============================================
           DESIGN SYSTEM — BSB Asset Monitoring
           Navy #1e3a5f · Gold #e8b84b
        ============================================ */
        :root {
            --navy:       #1e3a5f;
            --navy-mid:   #2d5a8e;
            --navy-light: #3d74b0;
            --gold:       #e8b84b;
            --gold-light: #f5d680;
            --bg:         #f4f6fb;
            --surface:    #ffffff;
            --border:     rgba(30, 58, 95, 0.09);
            --text-main:  #1e2d3d;
            --text-sub:   #5a6a7e;
            --text-muted: #8a96a3;
            --radius-sm:  8px;
            --radius-md:  12px;
            --radius-lg:  16px;
            --shadow-sm:  0 1px 6px rgba(30,58,95,0.07);
            --shadow-md:  0 4px 16px rgba(30,58,95,0.10);
            --shadow-lg:  0 8px 32px rgba(30,58,95,0.14);
            --font:       'Plus Jakarta Sans', 'Nunito', sans-serif;
            --font-mono:  'DM Mono', monospace;
            --header-h:   80px;
            --navbar-h:   52px;
            --total-h:    132px;
        }

        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            overflow-x: hidden;
            max-width: 100%;
            font-family: var(--font);
            background-color: var(--bg);
            color: var(--text-main);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Main Content ── */
        #main.main {
            padding: 32px 28px 48px;
            min-height: calc(100vh - var(--total-h));
        }

        /* ── Page Title ── */
        .pagetitle {
            margin-bottom: 26px;
        }
        .pagetitle h1 {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--navy);
            margin: 0 0 4px;
            letter-spacing: -0.4px;
        }
        .pagetitle .breadcrumb {
            margin: 0;
            padding: 0;
            background: transparent;
            font-size: 0.78rem;
        }
        .pagetitle .breadcrumb-item + .breadcrumb-item::before {
            color: var(--text-muted);
        }
        .pagetitle .breadcrumb-item a {
            color: var(--navy-mid);
            text-decoration: none;
        }
        .pagetitle .breadcrumb-item a:hover { text-decoration: underline; }
        .pagetitle .breadcrumb-item.active { color: var(--text-muted); }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .card .card-body { padding: 24px; }
        .card .card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── Tables ── */
        .table {
            font-size: 0.85rem;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table thead tr th {
            background: linear-gradient(135deg, var(--navy), var(--navy-mid));
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 12px 16px;
            border: none;
            white-space: nowrap;
        }
        .table thead tr th:first-child { border-radius: var(--radius-sm) 0 0 0; }
        .table thead tr th:last-child  { border-radius: 0 var(--radius-sm) 0 0; }
        .table tbody tr td {
            padding: 11px 16px;
            color: var(--text-main);
            border-bottom: 1px solid rgba(30,58,95,0.06);
            vertical-align: middle;
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover td { background: rgba(30,58,95,0.03); }
        .table tbody tr:nth-child(even) td { background: rgba(30,58,95,0.02); }
        .table tbody tr:nth-child(even):hover td { background: rgba(30,58,95,0.04); }

        /* ── Buttons ── */
        .btn {
            font-family: var(--font);
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: var(--radius-sm);
            padding: 8px 18px;
            border: none;
            transition: all 0.18s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn:active { transform: scale(0.97); }

        .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--navy-mid));
            color: #fff;
            box-shadow: 0 3px 10px rgba(30,58,95,0.25);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--navy-mid), var(--navy-light));
            color: #fff;
            box-shadow: 0 5px 16px rgba(30,58,95,0.32);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, #1e8a4a, #27a85c);
            color: #fff;
            box-shadow: 0 3px 10px rgba(30,138,74,0.25);
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #27a85c, #30c46b);
            color: #fff;
            box-shadow: 0 5px 16px rgba(30,138,74,0.32);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #b91c3a, #dc2650);
            color: #fff;
            box-shadow: 0 3px 10px rgba(185,28,58,0.25);
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2650, #f03062);
            color: #fff;
            box-shadow: 0 5px 16px rgba(185,28,58,0.32);
            transform: translateY(-1px);
        }

        .btn-warning {
            background: linear-gradient(135deg, #c49a2a, var(--gold));
            color: var(--navy);
            box-shadow: 0 3px 10px rgba(232,184,75,0.3);
        }
        .btn-warning:hover {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--navy);
            box-shadow: 0 5px 16px rgba(232,184,75,0.4);
            transform: translateY(-1px);
        }

        .btn-sm { padding: 5px 12px; font-size: 0.77rem; }
        .btn-lg { padding: 11px 24px; font-size: 0.9rem; }

        .btn-outline-primary {
            background: transparent;
            border: 1.5px solid var(--navy);
            color: var(--navy);
        }
        .btn-outline-primary:hover {
            background: var(--navy);
            color: #fff;
            transform: translateY(-1px);
        }

        /* ── Forms ── */
        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            font-family: var(--font);
            font-size: 0.85rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 9px 14px;
            background: #fff;
            color: var(--text-main);
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(30,58,95,0.1);
            outline: none;
        }
        .form-control::placeholder { color: var(--text-muted); }

        /* ── Badges ── */
        .badge {
            font-family: var(--font);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
        }

        /* ── Pagination ── */
        .pagination { gap: 4px; }
        .page-link {
            font-family: var(--font);
            font-size: 0.8rem;
            font-weight: 600;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm) !important;
            color: var(--navy);
            padding: 6px 12px;
            transition: all 0.18s ease;
        }
        .page-link:hover { background: var(--navy); color: #fff; border-color: var(--navy); }
        .page-item.active .page-link { background: var(--navy); border-color: var(--navy); color: #fff; }
        .page-item.disabled .page-link { opacity: 0.4; }

        /* ── Alerts ── */
        .alert {
            border-radius: var(--radius-md);
            border: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 14px 18px;
        }

        /* ── Modals ── */
        .modal-content {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(135deg, var(--navy), var(--navy-mid));
            color: #fff;
            border: none;
            padding: 18px 24px;
        }
        .modal-header .modal-title {
            font-size: 0.95rem;
            font-weight: 700;
        }
        .modal-header .btn-close { filter: invert(1); }
        .modal-body { padding: 24px; }
        .modal-footer {
            border-top: 1px solid var(--border);
            padding: 16px 24px;
        }

        /* ── Back to Top ── */
        .back-to-top {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--navy), var(--navy-mid));
            border-radius: 10px;
            box-shadow: var(--shadow-md);
            color: #fff !important;
            font-size: 1.1rem;
            transition: all 0.2s ease;
        }
        .back-to-top:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            #main.main { padding: 20px 16px 40px; }
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f4f8; }
        ::-webkit-scrollbar-thumb { background: rgba(30,58,95,0.25); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(30,58,95,0.45); }
    </style>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <!-- ======= Header ======= -->
    @if (Auth::check())
        @include('layouts.header')
    @endif
    <!-- End Header -->

    <!-- ======= Main Content ======= -->
    @yield('content')
    <!-- End #main -->

    <!-- Back to Top -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Vendor JS Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @yield('scripts')
</body>

</html>