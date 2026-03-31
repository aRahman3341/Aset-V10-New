@extends('layouts.app')
@section('title') Pengguna - Monitoring Aset @endsection
@section('content')
<main id="main" class="main">

{{-- Page Title --}}
<div class="pagetitle">
    <div class="pagetitle-left">
        <div class="pagetitle-icon"><i class="bi bi-people-fill"></i></div>
        <div>
            <h1>Pengguna</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item active">Pengguna</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<section class="section">
<div class="main-card">

    {{-- ── Toolbar ── --}}
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form action="{{ route('pengguna.search') }}" method="POST">
                @csrf
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="query" class="form-control border-start-0 ps-0"
                           placeholder="Cari NIP, nama, atau email..." value="{{ request()->input('query') }}">
                    <button type="button" class="btn btn-outline-secondary" id="filterButton" title="Filter Lanjutan">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="toolbar-right">
            @php
                $jabatanSess = strtolower($sess['jabatan'] ?? '');
            @endphp
            @if(in_array($jabatanSess, ['admin', 'manager', 'operator', 'super-user', 'superuser']))
            <a href="{{ route('pengguna.add') }}" class="btn btn-success btn-sm me-1 shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah Pengguna
            </a>
            @endif
        </div>
    </div>

    {{-- ── Filter Panel ── --}}
    <div id="filterFields" style="display:none" class="filter-panel">
        <div class="filter-panel-header">
            <span><i class="bi bi-funnel-fill"></i> Filter Lanjutan</span>
        </div>
        @include('pengguna.filter')
    </div>

    {{-- TABEL 1 — Pengguna Login --}}
    <div class="section-label">
        <i class="bi bi-shield-lock-fill text-primary"></i>
        Pengguna Login
        <span class="section-badge">Admin · Manager · Operator</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width:46px">No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th class="text-center">Jabatan</th>
                    <th>Bagian</th>
                    <th class="text-center">Kelamin</th>
                    <th>No HP</th>
                    <th class="text-center">Password</th>
                    <th class="text-center" style="width:90px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $item)
                <tr>
                    <td class="text-center text-muted small">{{ $users->firstItem() + $i }}</td>
                    <td><span class="fw-bold text-primary">{{ $item->nip }}</span></td>
                    <td class="fw-semibold">{{ $item->name }}</td>
                    <td class="text-muted small">{{ $item->email }}</td>
                    <td class="text-center">
                        @php
                            $jab = strtolower($item->jabatan);
                            $jClass = match($jab) {
                                'admin'    => 'bjab-admin',
                                'manager'  => 'bjab-manager',
                                'operator' => 'bjab-operator',
                                default    => 'bjab-default',
                            };
                        @endphp
                        <span class="bjab {{ $jClass }}">{{ ucfirst($item->jabatan) }}</span>
                    </td>
                    <td class="small">{{ $item->bagian ?? '-' }}</td>
                    <td class="text-center">
                        <span class="bcat {{ $item->gender === 'L' ? 'bcat-l' : 'bcat-p' }}">
                            {{ $item->gender }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $item->phone_number }}</td>
                    <td class="text-center">
                        <span class="pwd-badge" title="Password aktif">
                            <i class="bi bi-key-fill"></i> Aktif
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="action-group">
                            @php $jSess = strtolower($sess['jabatan'] ?? ''); @endphp
                            @if($jSess === 'operator' && ($sess['id'] ?? null) == $item->id)
                                {{-- Operator hanya bisa edit diri sendiri --}}
                                <a href="{{ route('pengguna.edit', ['id' => $item->id, 'type' => 'user']) }}"
                                   class="abtn abtn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            @elseif(in_array($jSess, ['admin','manager','super-user','superuser']))
                                {{-- Admin & Manager bisa edit semua --}}
                                <a href="{{ route('pengguna.edit', ['id' => $item->id, 'type' => 'user']) }}"
                                   class="abtn abtn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('pengguna.resetPassword', $item->id) }}"
                                      method="POST" class="d-inline reset-pwd-form">
                                    @csrf
                                    <button type="button" class="abtn abtn-reset reset-pwd-btn"
                                            data-name="{{ $item->name }}" data-nip="{{ $item->nip }}"
                                            title="Reset Password ke NIP">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                @if(in_array($jSess, ['admin','manager','super-user','superuser']))
                                <form action="{{ route('pengguna.destroy', ['id' => $item->id, 'type' => 'user']) }}"
                                      method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="abtn abtn-del delete-btn"
                                            data-name="{{ $item->name }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="empty-row">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Belum ada pengguna login.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->lastPage() > 1)
    <div class="pag-nav pag-inner">
        @include('pengguna.pagenation', ['items' => $users])
    </div>
    @endif

    {{-- TABEL 2 — Karyawan --}}
    <div class="section-label section-label-2">
        <i class="bi bi-person-lines-fill text-success"></i>
        Karyawan
        <span class="section-badge-green">Tidak bisa login</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width:46px">No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th class="text-center">Jabatan</th>
                    <th>Bagian</th>
                    <th class="text-center">Kelamin</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th class="text-center" style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $i => $item)
                <tr>
                    <td class="text-center text-muted small">{{ $employees->firstItem() + $i }}</td>
                    <td><span class="fw-bold text-success">{{ $item->nip }}</span></td>
                    <td class="fw-semibold">{{ $item->name }}</td>
                    <td class="text-muted small">{{ $item->email }}</td>
                    <td class="text-center">
                        <span class="bjab bjab-default">{{ $item->jabatan }}</span>
                    </td>
                    <td class="small">{{ $item->bagian ?? '-' }}</td>
                    <td class="text-center">
                        <span class="bcat {{ $item->gender === 'L' ? 'bcat-l' : 'bcat-p' }}">
                            {{ $item->gender }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ Str::limit($item->alamat, 30) }}</td>
                    <td class="small text-muted">{{ $item->phone_number }}</td>
                    <td class="text-center">
                        <div class="action-group">
                            @php $jSessE = strtolower($sess['jabatan'] ?? ''); @endphp
                            @if(in_array($jSessE, ['admin','manager','operator','super-user','superuser']))
                                <a href="{{ route('pengguna.edit', ['id' => $item->id, 'type' => 'employee']) }}"
                                   class="abtn abtn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if(in_array($jSessE, ['admin','manager','super-user','superuser']))
                                <form action="{{ route('pengguna.destroy', ['id' => $item->id, 'type' => 'employee']) }}"
                                      method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="abtn abtn-del delete-btn"
                                            data-name="{{ $item->name }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="empty-row">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Belum ada data karyawan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($employees->lastPage() > 1)
    <div class="pag-nav pag-inner pag-bottom">
        @include('pengguna.pagenation', ['items' => $employees])
    </div>
    @endif

</div>
</section>
</main>

<style>
.pagetitle { display:flex; align-items:center; margin-bottom:20px; }
.pagetitle-left { display:flex; align-items:center; gap:12px; }
.pagetitle-icon {
    width:44px; height:44px;
    background:linear-gradient(135deg,#012970,#4154f1);
    border-radius:12px; display:flex; align-items:center;
    justify-content:center; color:#fff; font-size:1.15rem;
    box-shadow:0 4px 12px rgba(1,41,112,0.22);
}
.pagetitle h1 { font-size:1.25rem; font-weight:800; color:#012970; margin:0 0 2px; }
.pagetitle .breadcrumb { margin:0; padding:0; background:transparent; font-size:0.76rem; }
.pagetitle .breadcrumb-item a { color:#4154f1; text-decoration:none; }
.pagetitle .breadcrumb-item.active { color:#8a96a3; }
.main-card {
    background:#fff; border-radius:10px;
    border:1px solid rgba(1,41,112,0.07);
    box-shadow:0 2px 14px rgba(1,41,112,0.07); overflow:hidden;
}
.table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 14px; border-bottom:1px solid rgba(1,41,112,0.07);
    gap:8px; flex-wrap:wrap;
}
.toolbar-left  { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.toolbar-right { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.filter-panel { background:#f8fafd; border-bottom:1px solid rgba(1,41,112,0.07); padding:12px 18px; }
.filter-panel-header { margin-bottom:10px; font-size:0.8rem; font-weight:700; color:#5a6a7e; }
.section-label {
    padding:9px 16px; background:#f6f9ff;
    border-top:2px solid rgba(1,41,112,0.07); border-bottom:1px solid rgba(1,41,112,0.07);
    font-size:0.78rem; font-weight:800; color:#012970;
    display:flex; align-items:center; gap:8px;
    letter-spacing:0.3px; text-transform:uppercase;
}
.section-label-2 { margin-top:0; border-top:3px solid rgba(1,41,112,0.10); }
.section-badge { background:rgba(65,84,241,0.10); color:#4154f1; font-size:0.66rem; font-weight:700; padding:2px 9px; border-radius:20px; text-transform:none; letter-spacing:0; }
.section-badge-green { background:rgba(16,185,129,0.10); color:#10b981; font-size:0.66rem; font-weight:700; padding:2px 9px; border-radius:20px; text-transform:none; letter-spacing:0; }
.table thead th { background-color:#f6f9ff; color:#012970; font-weight:700; text-transform:uppercase; font-size:0.69rem; letter-spacing:0.5px; padding:10px 11px; border:none; white-space:nowrap; border-bottom:2px solid #e0e8f5; }
.table tbody td { padding:9px 11px; vertical-align:middle; font-size:0.83rem; border-bottom:1px solid rgba(1,41,112,0.05); }
.table tbody tr:last-child td { border-bottom:none; }
.table tbody tr:hover td { background:rgba(65,84,241,0.03); }
.bjab { display:inline-block; font-size:0.68rem; font-weight:700; padding:2px 9px; border-radius:20px; }
.bjab-admin    { background:rgba(220,38,38,0.10); color:#dc2626; }
.bjab-manager  { background:rgba(124,58,237,0.10); color:#7c3aed; }
.bjab-operator { background:rgba(65,84,241,0.10); color:#4154f1; }
.bjab-default  { background:#f0f2f5; color:#6c757d; }
.bcat { display:inline-block; font-size:0.68rem; font-weight:700; padding:2px 9px; border-radius:20px; }
.bcat-l { background:rgba(59,130,246,0.10); color:#3b82f6; }
.bcat-p { background:rgba(236,72,153,0.12); color:#ec4899; }
.pwd-badge { display:inline-flex; align-items:center; gap:4px; font-size:0.68rem; font-weight:700; padding:2px 9px; border-radius:20px; background:rgba(16,185,129,0.10); color:#10b981; }
.action-group { display:flex; align-items:center; justify-content:center; gap:4px; }
/* form inline di dalam action-group harus flex agar icon sejajar */
.action-group form { display:contents; margin:0; padding:0; }
.abtn { width:28px; height:28px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; border:none; cursor:pointer; transition:all .15s; background:transparent; flex-shrink:0; }
.abtn-edit  { color:#c49a2a; } .abtn-edit:hover  { background:rgba(232,184,75,0.15); }
.abtn-del   { color:#dc2626; } .abtn-del:hover   { background:rgba(220,38,38,0.10); }
.abtn-reset { color:#7c3aed; } .abtn-reset:hover { background:rgba(124,58,237,0.10); }
.empty-row { text-align:center; padding:44px 0 !important; color:#8a96a3; }
.empty-row i { font-size:2.2rem; display:block; margin-bottom:8px; }
.pag-nav { display:flex; align-items:center; padding:10px 14px; flex-wrap:wrap; gap:4px; border-top:1px solid rgba(1,41,112,0.06); background:#fafbfd; }
.pag-inner { border-top:1px solid rgba(1,41,112,0.05); }
.pag-bottom { border-top:2px solid rgba(1,41,112,0.07); border-radius:0 0 10px 10px; }
.pag-list { display:flex; align-items:center; gap:3px; list-style:none; margin:0; padding:0; }
.pag-btn { display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:34px; padding:0 9px; border-radius:6px; font-size:0.8rem; font-weight:700; color:#012970; background:#fff; border:1.5px solid rgba(1,41,112,0.13); text-decoration:none; transition:all .15s ease; cursor:pointer; }
.pag-btn:hover:not(.pag-btn-active) { background:#012970; color:#fff; border-color:#012970; text-decoration:none; transform:translateY(-1px); }
.pag-btn-active { background:linear-gradient(135deg,#012970,#4154f1) !important; color:#fff !important; border-color:transparent !important; box-shadow:0 3px 12px rgba(65,84,241,0.28) !important; }
.pag-disabled .pag-btn { opacity:.3; cursor:not-allowed; pointer-events:none; }
.pag-info { font-size:0.74rem; color:#8a96a3; margin-left:8px; white-space:nowrap; }
.pag-info strong { color:#012970; }
@media (max-width:768px) { .table-toolbar { flex-direction:column; align-items:stretch; } .toolbar-right { justify-content:flex-start; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('filterButton').addEventListener('click', function (e) {
        e.preventDefault();
        const f = document.getElementById('filterFields');
        f.style.display = f.style.display === 'none' ? 'block' : 'none';
    });
    document.querySelectorAll('.delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const name = this.dataset.name;
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Hapus Pengguna?',
                html: `Data <strong>${name}</strong> akan dihapus permanen.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc2626', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
    document.querySelectorAll('.reset-pwd-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const name = this.dataset.name;
            const nip  = this.dataset.nip;
            const form = this.closest('.reset-pwd-form');
            Swal.fire({
                title: 'Reset Password?',
                html: `Password <strong>${name}</strong> akan direset ke NIP <code>${nip}</code>.`,
                icon: 'info', showCancelButton: true,
                confirmButtonColor: '#7c3aed', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset!', cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
});
</script>
@endsection