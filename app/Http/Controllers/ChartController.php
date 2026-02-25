<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Materials;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
	public function index()
{
    // --- Data Aset Tetap & Bergerak (jumlah per bulan) ---
    $asetTetapPerBulan = Materials::where('type', 'Tetap')
        ->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
        ->groupByRaw("MONTH(created_at)")
        ->pluck('jumlah', 'bulan')
        ->toArray();

    $asetBergerakPerBulan = Materials::where('type', 'Bergerak')
        ->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
        ->groupBy('bulan')
        ->pluck('jumlah', 'bulan')
        ->toArray();

    // Ambil label bulan (1-12) → ubah ke nama bulan
    $bulanLabels = [];
    for ($i = 1; $i <= 12; $i++) {
        $bulanLabels[$i] = Carbon::create()->month($i)->monthName;
    }

    // Isi data chart (0 jika bulan tidak ada)
    $quantityTetap   = [];
    $quantityBergerak = [];
    foreach ($bulanLabels as $bulanNum => $namaBulan) {
        $quantityTetap[]   = $asetTetapPerBulan[$bulanNum]   ?? 0;
        $quantityBergerak[] = $asetBergerakPerBulan[$bulanNum] ?? 0;
    }

    // --- Data Barang Habis Pakai (jumlah per bulan) ---
    $atkPerBulan = Items::where('categories', 'ATK')
        ->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
        ->groupBy('bulan')
        ->pluck('jumlah', 'bulan')
        ->toArray();

    $rtPerBulan = Items::where('categories', 'Rumah Tangga')
        ->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
        ->groupBy('bulan')
        ->pluck('jumlah', 'bulan')
        ->toArray();

    $labPerBulan = Items::where('categories', 'Laboratorium')
        ->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
        ->groupBy('bulan')
        ->pluck('jumlah', 'bulan')
        ->toArray();

    $quantityATK = [];
    $quantityRT  = [];
    $quantityLab = [];
    foreach ($bulanLabels as $bulanNum => $namaBulan) {
        $quantityATK[] = $atkPerBulan[$bulanNum] ?? 0;
        $quantityRT[]  = $rtPerBulan[$bulanNum]  ?? 0;
        $quantityLab[] = $labPerBulan[$bulanNum] ?? 0;
    }

    // Label bulan untuk chart
    $bulan  = array_values($bulanLabels);  // ['January', 'February', ...]
    $bulan1 = $bulan;                      // sama untuk barang

    return view('home', compact(
        'quantityTetap',
        'quantityBergerak',
        'bulan',
        'quantityATK',
        'quantityRT',
        'quantityLab',
        'bulan1'
    ));
}
}
