@extends('layouts.app')

@section('content')

<main id="main" class="main">

    {{-- Page Title --}}
    <div class="pagetitle">
        <div class="pagetitle-left">
            <div class="pagetitle-icon">
                <i class="bi bi-speedometer2"></i>
            </div>
            <div>
                <h1>Dashboard</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="pagetitle-right">
            <span class="date-badge">
                <i class="bi bi-calendar3"></i>
                <span id="current-date"></span>
            </span>
        </div>
    </div>

    {{-- ══ FILTER TAHUN ══ --}}
    <div class="filter-bar mb-4">
        <form method="GET" action="{{ url('/') }}" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="filter-label">
                <i class="bi bi-funnel-fill me-1"></i> Filter Tahun
            </div>
            <select name="tahun" class="filter-select" onchange="this.form.submit()">
                <option value="">— Semua Tahun —</option>
                @foreach ($tahunList as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            @if ($tahun)
                <a href="{{ url('/') }}" class="filter-reset">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
            @endif
            <span class="filter-info">
                @if ($tahun)
                    Menampilkan data tahun <strong>{{ $tahun }}</strong>
                @else
                    Menampilkan <strong>semua tahun</strong>
                @endif
            </span>
        </form>
    </div>

    <section class="section dashboard">
        <div class="row g-4">

            {{-- Chart: Statistik Aset --}}
            <div class="col-lg-8 col-md-12">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div class="chart-card-title">
                            <div class="chart-icon chart-icon-blue">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div>
                                <h5>Statistik Aset</h5>
                                <p>Tren aset per jenis BMN — <strong>{{ $tahun ? $tahun : 'Semua Tahun' }}</strong></p>
                            </div>
                        </div>
                        <a href="{{ url('asetTetap') }}" class="chart-detail-btn">
                            <span>Lihat Detail</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="chart-card-body">
                        <div id="grafikAset" style="width:100%; height:370px; min-height:370px;"></div>
                    </div>
                </div>
            </div>

            {{-- Chart: Barang Habis Pakai --}}
            <div class="col-lg-4 col-md-12">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <div class="chart-card-title">
                            <div class="chart-icon chart-icon-amber">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h5>Barang Habis Pakai</h5>
                                <p>Stok terpakai per kategori — <strong>{{ $tahun ? $tahun : 'Semua Tahun' }}</strong></p>
                            </div>
                        </div>
                        <a href="{{ url('items') }}" class="chart-detail-btn">
                            <span>Lihat Detail</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="chart-card-body">
                        <div id="grafikBarang" style="width:100%; height:370px; min-height:370px;"></div>
                    </div>
                </div>
            </div>

        </div>
    </section>

</main>

<style>
.filter-bar {
    background: #fff; border: 1px solid rgba(30,58,95,0.08); border-radius: 14px;
    padding: 14px 20px; box-shadow: 0 2px 8px rgba(30,58,95,0.05);
    display: flex; align-items: center;
}
.filter-label { font-size: 0.82rem; font-weight: 700; color: #1e3a5f; white-space: nowrap; }
.filter-select {
    padding: 7px 36px 7px 14px; border-radius: 10px; border: 1.5px solid #dee2e6;
    font-size: 0.83rem; font-weight: 600; color: #1e3a5f; background: #f8fafc;
    cursor: pointer; outline: none; transition: border-color .15s, box-shadow .15s;
    appearance: auto; min-width: 160px;
}
.filter-select:focus { border-color: #2d5a8e; box-shadow: 0 0 0 3px rgba(30,58,95,0.1); }
.filter-reset {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px;
    background: #fff0f0; border: 1.5px solid #f5c6c6; border-radius: 8px;
    font-size: 0.8rem; font-weight: 600; color: #c0392b; text-decoration: none; transition: all .15s;
}
.filter-reset:hover { background: #fde8e8; color: #a93226; text-decoration: none; }
.filter-info {
    font-size: 0.8rem; color: #6c7a8d; padding: 5px 12px;
    background: rgba(30,58,95,0.04); border-radius: 8px; border: 1px solid rgba(30,58,95,0.08);
}
.filter-info strong { color: #1e3a5f; }

.pagetitle {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(30,58,95,0.08);
}
.pagetitle-left { display: flex; align-items: center; gap: 14px; }
.pagetitle-icon {
    width: 48px; height: 48px; background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
    border-radius: 12px; display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.25rem; box-shadow: 0 4px 12px rgba(30,58,95,0.25); flex-shrink: 0;
}
.pagetitle h1 { font-size: 1.45rem; font-weight: 700; color: #1e3a5f; margin: 0 0 4px; letter-spacing: -0.3px; }
.pagetitle .breadcrumb { margin: 0; padding: 0; background: transparent; font-size: 0.8rem; }
.pagetitle .breadcrumb-item a { color: #2d5a8e; text-decoration: none; display: flex; align-items: center; gap: 4px; }
.pagetitle .breadcrumb-item.active { color: #6c757d; }
.date-badge {
    display: flex; align-items: center; gap: 8px;
    background: rgba(30,58,95,0.06); border: 1px solid rgba(30,58,95,0.12);
    border-radius: 8px; padding: 8px 14px; font-size: 0.82rem; font-weight: 500; color: #1e3a5f;
}

.chart-card {
    background: #fff; border-radius: 16px; box-shadow: 0 2px 16px rgba(30,58,95,0.07);
    border: 1px solid rgba(30,58,95,0.07); overflow: hidden; height: 100%;
    transition: box-shadow .2s, transform .2s;
}
.chart-card:hover { box-shadow: 0 6px 24px rgba(30,58,95,0.12); transform: translateY(-2px); }
.chart-card-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 20px 24px 16px; border-bottom: 1px solid rgba(30,58,95,0.07);
}
.chart-card-title { display: flex; align-items: center; gap: 12px; }
.chart-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
}
.chart-icon-blue  { background: rgba(65,84,241,0.1);   color: #4154f1; }
.chart-icon-amber { background: rgba(232,184,75,0.15); color: #c49a2a; }
.chart-card-title h5 { font-size: 0.95rem; font-weight: 700; color: #1e3a5f; margin: 0 0 2px; }
.chart-card-title p  { font-size: 0.75rem; color: #8a96a3; margin: 0; }
.chart-detail-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
    background: rgba(30,58,95,0.06); border: 1px solid rgba(30,58,95,0.12);
    border-radius: 8px; font-size: 0.78rem; font-weight: 600; color: #1e3a5f;
    text-decoration: none; transition: all .2s; white-space: nowrap; flex-shrink: 0; margin-top: 2px;
}
.chart-detail-btn:hover { background: #1e3a5f; border-color: #1e3a5f; color: #fff; text-decoration: none; }
.chart-card-body { padding: 16px 12px 12px; }

@media (max-width: 768px) {
    .pagetitle { flex-direction: column; align-items: flex-start; gap: 12px; }
    .date-badge { display: none; }
    .filter-bar { flex-direction: column; align-items: flex-start; gap: 10px; }
}
</style>

{{-- Highcharts dari CDN dengan fallback lokal --}}
<script src="https://code.highcharts.com/highcharts.js"
        onerror="loadHighchartsFromAlternate()"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    // Fallback CDN jika code.highcharts.com gagal
    function loadHighchartsFromAlternate() {
        var s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/highcharts/11.2.0/highcharts.js';
        s.onload = function() { initCharts(); };
        document.head.appendChild(s);
    }

    // Set tanggal
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });

    // ── Data dari Laravel (PHP → JS) ──
    var dataAsetMesin = @json(array_values($quantityTetap));
    var dataAsetInfra = @json(array_values($quantityBergerak));
    var dataRT        = @json(array_values($quantityRT));
    var dataATK       = @json(array_values($quantityATK));
    var dataLab       = @json(array_values($quantityLab));
    var labelBulan    = @json(array_values($bulan));

    function initCharts() {
        if (typeof Highcharts === 'undefined') {
            document.getElementById('grafikAset').innerHTML =
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#8a96a3;font-size:.85rem;">' +
                '<i class="bi bi-exclamation-circle me-2"></i>Highcharts gagal dimuat. Periksa koneksi internet.</div>';
            document.getElementById('grafikBarang').innerHTML =
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#8a96a3;font-size:.85rem;">' +
                '<i class="bi bi-exclamation-circle me-2"></i>Highcharts gagal dimuat.</div>';
            return;
        }

        Highcharts.setOptions({
            chart: { style: { fontFamily: '"Plus Jakarta Sans","Nunito",sans-serif' } },
            lang:  { decimalPoint: ',', thousandsSep: '.', noData: 'Tidak ada data' }
        });

        // ── Grafik Statistik Aset ──
        Highcharts.chart('grafikAset', {
            chart: {
                type: 'areaspline', backgroundColor: 'transparent',
                spacingTop: 10, spacingBottom: 5,
                animation: { duration: 800 },
                reflow: true
            },
            title: { text: '' },
            xAxis: {
                categories: labelBulan,
                crosshair: { width: 1, color: 'rgba(30,58,95,0.15)', dashStyle: 'Dash' },
                gridLineWidth: 0, lineColor: '#e8ecf0', tickColor: 'transparent',
                labels: { style: { fontSize: '11px', color: '#8a96a3' } }
            },
            yAxis: {
                title: { text: '' },
                allowDecimals: false,
                gridLineDashStyle: 'LongDash', gridLineColor: '#f0f3f6',
                labels: { style: { fontSize: '11px', color: '#8a96a3' } }
            },
            tooltip: {
                shared: true, useHTML: true,
                backgroundColor: 'rgba(255,255,255,0.97)', borderWidth: 0, borderRadius: 12,
                shadow: { color: 'rgba(30,58,95,0.12)', offsetX: 0, offsetY: 4, opacity: 0.8, width: 16 },
                headerFormat: '<div style="font-size:11px;font-weight:600;color:#8a96a3;margin-bottom:6px">{point.key}</div>',
                pointFormat: '<div style="display:flex;align-items:center;justify-content:space-between;gap:24px;margin-bottom:3px">' +
                             '<span style="display:flex;align-items:center;gap:6px">' +
                             '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{series.color}"></span>' +
                             '<span style="color:#4a5568;font-size:12px">{series.name}</span></span>' +
                             '<b style="color:#1e3a5f;font-size:12px">{point.y} unit</b></div>',
                footerFormat: ''
            },
            legend: {
                align: 'center', verticalAlign: 'bottom',
                itemStyle: { fontSize: '12px', fontWeight: '600', color: '#4a5568' },
                symbolRadius: 4
            },
            plotOptions: {
                areaspline: {
                    fillOpacity: 0.08, lineWidth: 2.5,
                    marker: { radius: 4, lineColor: '#fff', lineWidth: 2, symbol: 'circle', enabled: true },
                    states: { hover: { lineWidth: 3 } }
                }
            },
            series: [{
                name: 'Mesin & Peralatan',
                data: dataAsetMesin,
                color: '#2eca6a',
                fillColor: { linearGradient: { x1:0,y1:0,x2:0,y2:1 },
                    stops: [[0,'rgba(46,202,106,0.35)'],[1,'rgba(46,202,106,0.00)']] }
            },{
                name: 'Kendaraan & Infrastruktur',
                data: dataAsetInfra,
                color: '#4154f1',
                fillColor: { linearGradient: { x1:0,y1:0,x2:0,y2:1 },
                    stops: [[0,'rgba(65,84,241,0.35)'],[1,'rgba(65,84,241,0.00)']] }
            }],
            credits: { enabled: false },
            noData: { style: { fontSize: '14px', color: '#8a96a3' } },
            exporting: {
                enabled: true,
                buttons: { contextButton: {
                    menuItems: ['viewFullscreen','printChart','separator',
                                'downloadPNG','downloadJPEG','downloadSVG','separator',
                                'downloadCSV','downloadXLS']
                }}
            }
        });

        // ── Grafik Barang Habis Pakai ──
        Highcharts.chart('grafikBarang', {
            chart: {
                type: 'column', backgroundColor: 'transparent',
                spacingTop: 10, spacingBottom: 5,
                animation: { duration: 800 },
                reflow: true
            },
            title: { text: '' },
            xAxis: {
                categories: labelBulan,
                crosshair: { width: 1, color: 'rgba(30,58,95,0.15)', dashStyle: 'Dash' },
                lineColor: '#e8ecf0', tickColor: 'transparent',
                labels: { style: { fontSize: '11px', color: '#8a96a3' } }
            },
            yAxis: {
                min: 0, title: { text: '' }, allowDecimals: false,
                gridLineDashStyle: 'LongDash', gridLineColor: '#f0f3f6',
                labels: { style: { fontSize: '11px', color: '#8a96a3' } }
            },
            tooltip: {
                shared: true, useHTML: true,
                backgroundColor: 'rgba(255,255,255,0.97)', borderWidth: 0, borderRadius: 12,
                shadow: { color: 'rgba(30,58,95,0.12)', offsetX: 0, offsetY: 4, opacity: 0.8, width: 16 },
                headerFormat: '<div style="font-size:11px;font-weight:600;color:#8a96a3;margin-bottom:6px">{point.key}</div>',
                pointFormat: '<div style="display:flex;align-items:center;justify-content:space-between;gap:24px;margin-bottom:3px">' +
                             '<span style="display:flex;align-items:center;gap:6px">' +
                             '<span style="display:inline-block;width:8px;height:8px;border-radius:2px;background:{series.color}"></span>' +
                             '<span style="color:#4a5568;font-size:12px">{series.name}</span></span>' +
                             '<b style="color:#1e3a5f;font-size:12px">{point.y}</b></div>',
                footerFormat: ''
            },
            legend: {
                align: 'center', verticalAlign: 'bottom',
                itemStyle: { fontSize: '11px', fontWeight: '600', color: '#4a5568' },
                symbolRadius: 3, symbolHeight: 10, symbolWidth: 10
            },
            plotOptions: {
                column: {
                    pointPadding: 0.15, groupPadding: 0.1,
                    borderWidth: 0, borderRadius: 5,
                    states: { hover: { brightness: -0.05 } }
                }
            },
            series: [
                { name: 'Rumah Tangga', data: dataRT,  color: '#ff771d' },
                { name: 'Laboratorium', data: dataLab, color: '#4154f1' },
                { name: 'ATK',          data: dataATK, color: '#2eca6a' }
            ],
            credits: { enabled: false },
            noData: { style: { fontSize: '14px', color: '#8a96a3' } },
            exporting: {
                enabled: true,
                buttons: { contextButton: {
                    menuItems: ['viewFullscreen','printChart','separator',
                                'downloadPNG','downloadJPEG','downloadSVG','separator',
                                'downloadCSV','downloadXLS']
                }}
            }
        });
    }

    // Tunggu DOM + Highcharts siap
    document.addEventListener('DOMContentLoaded', function () {
        // Retry sampai Highcharts tersedia (maks 5 detik)
        var attempts = 0;
        var interval = setInterval(function () {
            attempts++;
            if (typeof Highcharts !== 'undefined') {
                clearInterval(interval);
                initCharts();
            } else if (attempts > 50) {
                clearInterval(interval);
                loadHighchartsFromAlternate();
            }
        }, 100);
    });
</script>

@endsection