<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Materials;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        // ── Filter Tahun (null = semua tahun) ──
        $tahun     = $request->get('tahun', null);  // null = tampilkan semua
        $tahunList = range(Carbon::now()->year, 2020);

        // ── Label Bulan ──
        $bulanLabels = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulanLabels[$i] = Carbon::create()->month($i)->monthName;
        }

        // ── Aset Tetap & Bergerak per bulan ──
        $queryTetap    = Materials::where('type', 'Tetap');
        $queryBergerak = Materials::where('type', 'Bergerak');

        if ($tahun) {
            $queryTetap->whereYear('created_at', $tahun);
            $queryBergerak->whereYear('created_at', $tahun);
        }

        $asetTetapPerBulan = $queryTetap
            ->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
            ->groupByRaw("MONTH(created_at)")
            ->pluck('jumlah', 'bulan')
            ->toArray();

        $asetBergerakPerBulan = $queryBergerak
            ->selectRaw("MONTH(created_at) as bulan, COUNT(*) as jumlah")
            ->groupByRaw("MONTH(created_at)")
            ->pluck('jumlah', 'bulan')
            ->toArray();

        $quantityTetap    = [];
        $quantityBergerak = [];
        foreach ($bulanLabels as $bulanNum => $namaBulan) {
            $quantityTetap[]    = $asetTetapPerBulan[$bulanNum]    ?? 0;
            $quantityBergerak[] = $asetBergerakPerBulan[$bulanNum] ?? 0;
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

        $quantityATK = [];
        $quantityRT  = [];
        $quantityLab = [];
        foreach ($bulanLabels as $bulanNum => $namaBulan) {
            $quantityATK[] = $atkPerBulan[$bulanNum] ?? 0;
            $quantityRT[]  = $rtPerBulan[$bulanNum]  ?? 0;
            $quantityLab[] = $labPerBulan[$bulanNum] ?? 0;
        }

        $bulan  = array_values($bulanLabels);
        $bulan1 = $bulan;

        return view('home', compact(
            'quantityTetap', 'quantityBergerak',
            'bulan', 'bulan1',
            'quantityATK', 'quantityRT', 'quantityLab',
            'tahun', 'tahunList'
        ));
    }
}