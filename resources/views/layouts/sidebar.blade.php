<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link {{ request()->is('/') ? ' active' : 'collapsed' }}" href="{{ url('/') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link {{ request()->is('asetTetap*') || request()->is('items*') ? ' active' : 'collapsed' }}" href="#components-nav">
          <i class="bi bi-menu-button-wide"></i><span>Aset</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse{{ request()->is('asetTetap*') || request()->is('items*') ? ' show' : '' }}" data-bs-parent="#sidebar-nav">
          <li >
            <a class="{{ request()->is('asetTetap*') ? ' active' : '' }}" href="{{ url('asetTetap') }}">
              <i class="bi bi-circle"></i><span>Data Aset</span>
            </a>
          </li>
          {{--<li >
            <a class="{{ request()->is('items*') ? ' active' : '' }}" href="{{ url('items') }}">
              <i class="bi bi-circle"></i><span>Barang Habis Pakai</span>
            </a>
          </li>--}}
          @if ($sess['jabatan'] !== 'Admin')
							<li >
                <a class="{{ request()->is('peminjaman/add') || request()->is('peminjaman') || request()->is('peminjaman/edit*') ? ' active' : '' }}" href="{{ url('peminjaman') }}">
                  <i class="bi bi-circle"></i><span>Peminjaman</span>
                </a>
              </li>
					@endif
        </ul>
      </li><!-- End Components Nav -->
      {{--<li class="nav-item">--}}
        <a class="nav-link {{ request()->is('peminjaman') || request()->is('peminjaman/add') || request()->is('peminjaman/edit*') ||
            request()->is('asetout/add') || request()->is('asetout/edit*') || request()->is('asetout') ? ' active' : 'collapsed' }}" href="#transaksi-nav">
          <i class="bi bi-menu-button-wide"></i><span>Transaksi</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="transaksi-nav" class="nav-content collapse{{ request()->is('peminjaman') || request()->is('peminjaman/add') || request()->is('peminjaman/edit*') ||
            request()->is('asetout') || request()->is('asetout/add') || request()->is('asetout/edit*') ? ' show' : '' }}" data-bs-parent="#sidebar-nav">
          <li >
            <a class="{{ request()->is('asetout') || request()->is('asetout/add') || request()->is('asetout/edit*') ? ' active' : '' }}" href="{{ url('asetout') }}">
              <i class="bi bi-circle"></i><span>Aset Keluar</span>
            </a>
          </li>
        </ul>
      </li><!-- End transaksi Nav -->
      <li class="nav-item">
        <a class="nav-link {{ request()->is('location*') || request()->is('category*') || request()->is('pengguna*') ? ' active' : 'collapsed' }}" href="#master-nav">
          <i class="bi bi-database"></i><span>Master</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="master-nav" class="nav-content collapse{{ request()->is('location*') || request()->is('category*') || request()->is('pengguna*') ? ' show' : '' }}" data-bs-parent="#sidebar-nav">
          <li >
            <a class="{{ request()->is('location*') ? ' active' : '' }}" href="{{ url('location') }}">
              <i class="bi bi-circle"></i><span>Lokasi</span>
            </a>
          </li>
          <li >
            <a class="{{ request()->is('category*') ? ' active' : '' }}" href="{{ url('category') }}">
              <i class="bi bi-circle"></i><span>Kategori</span>
            </a>
          </li>
          <li >
            <a class="{{ request()->is('pengguna*') ? ' active' : '' }}" href="{{ url('pengguna') }}">
              <i class="bi bi-circle"></i><span>Pengguna</span>
            </a>
          </li>
        </ul>
      </li><!-- End master Nav -->
      <li class="nav-item">
        <a class="nav-link {{ request()->is('peminjaman/report*') || request()->is('asetout/report*') ? ' active' : 'collapsed' }}" href="#report-nav">
          <i class="bi bi-menu-button-wide"></i><span>Report</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="report-nav" class="nav-content collapse{{ request()->is('peminjaman/report*') || request()->is('asetout/report') ? ' show' : '' }}" data-bs-parent="#sidebar-nav">
          <li >
            <a class="{{ request()->is('peminjaman/report*') ? ' active' : '' }}" href="{{ route('peminjaman.report-peminjaman') }}">
              <i class="bi bi-circle"></i><span>Peminjaman</span>
            </a>
          </li>
          {{--<li >
            <a class="{{ request()->is('asetout/report*') ? ' active' : '' }}" href="{{ route('asetout.report-asetout') }}">
              <i class="bi bi-circle"></i><span>Barang Keluar</span>
            </a>
          </li>--}}
        </ul>
      </li><!-- End Profile Page Nav -->
    </ul>
  </aside>
