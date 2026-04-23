<?php

namespace App\Http\Controllers;

use App\Models\Items;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        $tahun     = $request->get('tahun', null);
        $tahunList = range(Carbon::now()->year, 2020);

        // ── Label Bulan ──
        $bulanLabels = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulanLabels[$i] = Carbon::create()->month($i)->monthName;
        }

        // ── Aset Tetap — per Jenis BMN ──
        $jenisBmnList = [
            'ALAT BESAR',
            'ALAT ANGKUTAN BERMOTOR',
            'BANGUNAN DAN GEDUNG',
            'JALAN DAN JEMBATAN',
            'MESIN PERALATAN KHUSUS TIK',
            'MESIN PERALATAN NON TIK',
        ];

        $dataPerJenis = [];
        foreach ($jenisBmnList as $jenis) {
            $q = DB::table('materials')->where('Jenis BMN', $jenis);
            if ($tahun) $q->whereYear('created_at', $tahun);

            $perBulan = $q
                ->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
                ->groupByRaw("MONTH(created_at)")
                ->pluck('jumlah', 'bulan')
                ->toArray();

            $series = [];
            foreach ($bulanLabels as $bulanNum => $namaBulan) {
                $series[] = $perBulan[$bulanNum] ?? 0;
            }

            $dataPerJenis[$jenis] = $series;
        }

        // ── Barang Habis Pakai per bulan ──
        $queryATK = Items::where('categories', 'ATK');
        $queryRT  = Items::where('categories', 'Rumah Tangga');
        $queryLab = Items::where('categories', 'Laboratorium');

        if ($tahun) {
            $queryATK->whereYear('created_at', $tahun);
            $queryRT->whereYear('created_at', $tahun);
            $queryLab->whereYear('created_at', $tahun);
        }

        $atkPerBulan = $queryATK->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
            ->groupBy('bulan')->pluck('jumlah', 'bulan')->toArray();
        $rtPerBulan  = $queryRT->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
            ->groupBy('bulan')->pluck('jumlah', 'bulan')->toArray();
        $labPerBulan = $queryLab->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
            ->groupBy('bulan')->pluck('jumlah', 'bulan')->toArray();

        $quantityATK = $quantityRT = $quantityLab = [];
        foreach ($bulanLabels as $bulanNum => $namaBulan) {
            $quantityATK[] = $atkPerBulan[$bulanNum] ?? 0;
            $quantityRT[]  = $rtPerBulan[$bulanNum]  ?? 0;
            $quantityLab[] = $labPerBulan[$bulanNum] ?? 0;
        }

        $bulan  = array_values($bulanLabels);
        $bulan1 = $bulan;

        return view('home', compact(
            'dataPerJenis',
            'bulan', 'bulan1',
            'quantityATK', 'quantityRT', 'quantityLab',
            'tahun', 'tahunList'
        ));
    }
}