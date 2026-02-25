<nav class="navbar navbar-expand-lg bg-body-tertiary" style="background-color: #354571;">
    <div class="container-fluid">
      <a class="navbar-brand text-white" href="{{ url('/') }}">APLIKASI MONITORING ASET</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="true" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('/') ? ' active' : '' }}" href="{{ url('/') }}">
                  <i class="bi bi-grid"></i> Dashboard
                </a>
              </li>
          <!-- Add "hoverable-dropdown" class to the parent li element of the dropdown -->
          <li class="nav-item hoverable-dropdown">
            <a class="nav-link text-white {{ request()->is('asetTetap*') || request()->is('items*') ? ' active' : 'collapsed' }}" href="#components-nav">
              <i class="bi bi-menu-button-wide"></i>
              <span>Data Aset</span>
              <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="dropdown-menu collapse data-bs-parent="#sidebar-nav">
              <li class="dropdown-item">
                <a class="{{ request()->is('asetTetap*') ? ' active' : '' }}" href="{{ url('asetTetap') }}">
                  <span>Aset</span>
                </a>
              </li>
              <li class="dropdown-item">
                <a class="{{ request()->is('items*') ? ' active' : '' }}" href="{{ url('items') }}">
                  <span>Barang Habis Pakai</span>
                </a>
              </li>
            </ul>
          </li>
          <!-- Add "hoverable-dropdown" class to the parent li element of the dropdown -->
          <li class="nav-item hoverable-dropdown">
            <a class="nav-link text-white {{ request()->is('peminjaman') || request()->is('peminjaman/add') || request()->is('peminjaman/edit*') ||
              request()->is('asetout/add') || request()->is('asetout/edit*') || request()->is('asetout') ? ' active' : 'collapsed' }}" href="#transaksi-nav">
              <i class="bi bi-menu-button-wide"></i>
              <span>Transaksi</span>
              <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="transaksi-nav" class="dropdown-menu collapse data-bs-parent="#sidebar-nav">
              <li class="dropdown-item">
                <a class="{{ request()->is('asetkeluar') || request()->is('asetkeluar/add') || request()->is('asetkeluar/edit*') ? ' active' : '' }}" href="{{ url('asetkeluar') }}">
                  <span>Aset Keluar</span>
                </a>
              </li>
              <li class="dropdown-item">
                  <a class="{{ request()->is('peminjaman/add') || request()->is('peminjaman') || request()->is('peminjaman/edit*') ? ' active' : '' }}" href="{{ url('peminjaman') }}">
                      <span>Peminjaman</span>
                  </a>
              </li>
            </ul>
          </li>
          <!-- Add "hoverable-dropdown" class to the parent li element of the dropdown -->
          <li class="nav-item hoverable-dropdown">
            <a class="nav-link text-white {{ request()->is('location*') || request()->is('category*') || request()->is('pengguna*') ? ' active' : 'collapsed' }}" href="#master-nav">
              <i class="bi bi-database"></i>
              <span>Master</span>
              <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="master-nav" class="dropdown-menu collapse data-bs-parent="#sidebar-nav">
              <li class="dropdown-item">
                <a class="{{ request()->is('location*') ? ' active' : '' }}" href="{{ url('location') }}">
                  <span>Lokasi</span>
                </a>
              </li>
              <li class="dropdown-item">
                <a class="{{ request()->is('category*') ? ' active' : '' }}" href="{{ url('category') }}">
                  <span>Kategori</span>
                </a>
              </li>
              <li class="dropdown-item">
                <a class="{{ request()->is('pengguna*') ? ' active' : '' }}" href="{{ url('pengguna') }}">
                  <span>Pengguna</span>
                </a>
              </li>
            </ul>
          </li>
          <!-- Add "hoverable-dropdown" class to the parent li element of the dropdown -->
          <li class="nav-item hoverable-dropdown">
            <a class="nav-link text-white {{ request()->is('peminjaman/report*') || request()->is('asetout/report*') ? ' active' : 'collapsed' }}" href="#report-nav">
              <i class="bi bi-menu-button-wide"></i>
              <span>Report</span>
              <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="report-nav" class="dropdown-menu collapse data-bs-parent="#sidebar-nav">
              <li class="dropdown-item">
                <a class="{{ request()->is('peminjaman/report*') ? ' active' : '' }}" href="{{ route('peminjaman.report-peminjaman') }}">
                  <span>Peminjaman</span>
                </a>
              </li>
              <li class="dropdown-item">
                <a class="{{ request()->is('asetkeluar/report*') ? ' active' : '' }}" href="{{ route('asetkeluar.report-asetkeluar') }}">
                  <span>Aset Keluar</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="col-md-3 text-end">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item hoverable-dropdown">
            <a class="nav-link text-white {{ request()->is('session/login*') ? ' active' : 'collapsed' }}" href="#report-nav">
              {{--<i class="bi bi-menu-button-wide"></i>--}}
              <img src="{{ asset('assets/img/logo2.png') }}" style="width: 30px;" alt="Profile" class="rounded-circle">
              {{--<i class="bi bi-chevron-down ms-auto"></i>--}}
            </a>
            <ul id="report-nav" class="dropdown-menu collapse data-bs-parent="#sidebar-nav">
              <li class="dropdown-item">
                <a class="{{ request()->is('session/login*') ? ' active' : '' }}" href="{{ route('session.logout') }}">
                  <span>Logout</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <style>
    .navbar-nav .hoverable-dropdown > a {
      border-bottom: 2px solid transparent;
    }

    .navbar-nav .hoverable-dropdown:hover > a {
      border-bottom: 2px solid yellow;
    }
  </style>

  <!-- Add the following script after the HTML code -->
  <script>
    // Enable dropdown toggling on hover
    const hoverableDropdowns = document.querySelectorAll('.hoverable-dropdown');
    hoverableDropdowns.forEach((dropdown) => {
        dropdown.addEventListener('mouseenter', () => {
            dropdown.classList.add('show');
            dropdown.querySelector('.dropdown-menu').classList.add('show');
        });

        dropdown.addEventListener('mouseleave', () => {
            dropdown.classList.remove('show');
            dropdown.querySelector('.dropdown-menu').classList.remove('show');
        });
    });

    // Enable the Bootstrap navbar-toggler button to work on mobile screens
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    navbarToggler.addEventListener('click', () => {
        navbarCollapse.classList.toggle('show');
    });
  </script>
