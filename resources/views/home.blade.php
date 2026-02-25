@extends('layouts.app')

@section('content')

<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center" style="margin-bottom: 30px;">
      <div>
        <h1>Dashboard</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </nav>
      </div>
    </div><section class="section dashboard">
      <div class="row">

        <div class="col-lg-8 col-md-12">
          <div class="card dashboard-card">
            <div class="card-body">
              <a href="{{ url('asetTetap') }}" class="text-decoration-none">
                <div class="card-header-custom">
                  <h5 class="card-title-custom">
                    <i class="bi bi-graph-up-arrow"></i> Statistik Aset
                  </h5>
                  <span class="badge bg-primary-light text-primary"><i class="bi bi-arrow-right"></i> Detail</span>
                </div>
              </a>
              
              <div id="grafikAset" style="width:100%; height:400px;"></div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-12">
          <div class="card dashboard-card">
            <div class="card-body">
              <a href="{{ url('items') }}" class="text-decoration-none">
                <div class="card-header-custom">
                  <h5 class="card-title-custom">
                    <i class="bi bi-box-seam"></i> Barang Habis Pakai
                  </h5>
                  <span class="badge bg-warning-light text-warning"><i class="bi bi-arrow-right"></i> Detail</span>
                </div>
              </a>
              
              <div id="grafikBarang" style="width:100%; height:400px;"></div>
            </div>
          </div>
        </div>
        </div>
    </section>

</main>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function () {
    
    // --- Data dari PHP Controller ---
    var dataAsetBergerak = <?php echo json_encode($quantityBergerak) ?>;
    var dataAsetTetap = <?php echo json_encode($quantityTetap) ?>;
    var labelBulanAset = <?php echo json_encode($bulan) ?>;

    var dataRT = <?php echo json_encode($quantityRT) ?>;
    var dataATK = <?php echo json_encode($quantityATK) ?>;
    var dataLab = <?php echo json_encode($quantityLab) ?>;
    var labelBulanBarang = <?php echo json_encode($bulan1) ?>;

    // --- Konfigurasi Global Highcharts (Agar tampilan konsisten) ---
    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: '"Nunito", sans-serif'
            }
        },
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#ffca2c']
    });

    // --- 1. Render Grafik Aset (Area Spline agar terlihat modern) ---
    Highcharts.chart('grafikAset', {
      chart: {
        type: 'areaspline', // Menggunakan kurva halus dengan fill area
        backgroundColor: 'transparent'
      },
      title: {
        text: '',
        style: { display: 'none' } // Judul sudah ada di card header
      },
      xAxis: {
        categories: labelBulanAset,
        crosshair: true,
        gridLineWidth: 0,
        lineWidth: 1,
        lineColor: '#e0e0e0'
      },
      yAxis: {
        title: {
          text: 'Jumlah Unit'
        },
        gridLineDashStyle: 'LongDash',
        gridLineColor: '#f0f0f0'
      },
      tooltip: {
        shared: true,
        useHTML: true,
        headerFormat: '<small>{point.key}</small><table>',
        pointFormat: '<tr><td style="color: {series.color}">{series.name}: </td>' +
                     '<td style="text-align: right"><b>{point.y}</b></td></tr>',
        footerFormat: '</table>',
        valueSuffix: ' unit',
        backgroundColor: 'rgba(255, 255, 255, 0.95)',
        borderRadius: 10,
        shadow: true,
        borderWidth: 0
      },
      plotOptions: {
        areaspline: {
          fillOpacity: 0.1, // Transparansi warna area
          marker: {
              radius: 4,
              lineColor: '#fff',
              lineWidth: 2
          }
        }
      },
      series: [{
        name: 'Aset Bergerak',
        data: dataAsetBergerak,
        color: '#4154f1',
        fillColor: {
            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
            stops: [
                [0, 'rgba(65, 84, 241, 0.5)'],
                [1, 'rgba(65, 84, 241, 0.0)']
            ]
        }
      }, {
        name: 'Aset Tetap',
        data: dataAsetTetap,
        color: '#2eca6a',
        fillColor: {
            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
            stops: [
                [0, 'rgba(46, 202, 106, 0.5)'],
                [1, 'rgba(46, 202, 106, 0.0)']
            ]
        }
      }],
      credits: { enabled: false }
    });

    // --- 2. Render Grafik Barang Habis Pakai (Column Chart) ---
    Highcharts.chart('grafikBarang', {
      chart: {
        type: 'column',
        backgroundColor: 'transparent'
      },
      title: {
        text: '',
        style: { display: 'none' }
      },
      xAxis: {
        categories: labelBulanBarang,
        crosshair: true
      },
      yAxis: {
        min: 0,
        title: {
          text: 'Stok Terpakai'
        },
        gridLineDashStyle: 'LongDash',
        gridLineColor: '#f0f0f0'
      },
      tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
          '<td style="padding:0"><b>{point.y}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true,
        backgroundColor: 'rgba(255, 255, 255, 0.95)',
        borderRadius: 10,
        shadow: true,
        borderWidth: 0
      },
      plotOptions: {
        column: {
          pointPadding: 0.2,
          borderWidth: 0,
          borderRadius: 3
        }
      },
      series: [{
        name: 'Rumah Tangga',
        data: dataRT,
        color: '#ff771d'
      }, {
        name: 'Laboratorium',
        data: dataLab,
        color: '#4154f1'
      }, {
        name: 'ATK',
        data: dataATK,
        color: '#2eca6a'
      }],
      credits: { enabled: false }
    });

  });
</script>

@endsection