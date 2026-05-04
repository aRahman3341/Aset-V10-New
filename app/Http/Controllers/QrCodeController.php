<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\AsetKeluar;
use App\Models\Materials;
use App\Models\MaterialPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QrCodeController extends Controller
{
    // ══════════════════════════════════════════════════════════
    //  ASET TETAP
    // ══════════════════════════════════════════════════════════

    public function generateQRCodes(Request $request)
    {
        $ids = $request->input('id_aset', []);

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu aset untuk cetak QR.');
        }

        $dataproduk = DB::table('materials')->whereIn('id', $ids)->get();

        if ($dataproduk->isEmpty()) {
            return back()->with('error', 'Tidak ada data aset yang valid.');
        }

        return view('asetTetap.qrcode', compact('dataproduk'));
    }

    /**
     * API scanning aset tetap — dipanggil scane.blade.php via fetch()
     */
    public function scanning(Request $request)
    {
        $code = '';
        $nup  = '';

        $raw = $request->input('code', '');
        if (str_contains($raw, '*')) {
            $parts = explode('*', $raw);
            $code  = trim($parts[0] ?? '');
            $nup   = trim($parts[1] ?? '');
        } else {
            $code = trim($raw);
            $nup  = trim($request->input('nup', ''));
        }

        if (empty($code)) {
            return response()->json(['error' => 'Kode tidak boleh kosong.'], 422);
        }

        $query = DB::table('materials')->where('Kode Barang', $code);
        if (!empty($nup)) {
            $query->where('nup', $nup);
        }
        $items = $query->get();

        $result = $items->map(function ($item) {
            return [
                'id'             => $item->id,
                'code'           => $item->{'Kode Barang'}    ?? '-',
                'nup'            => $item->nup                ?? '-',
                'name'           => $item->{'Nama Barang'}    ?? '-',
                'name_fix'       => $item->merk               ?? '-',
                'condition'      => $item->kondisi            ?? '-',
                'status'         => $item->{'Status BMN'}     ?? '-',
                'status_bmn'     => $item->{'Status BMN'}     ?? '-',
                'jenis_bmn'      => $item->{'Jenis BMN'}      ?? '-',
                'nilai_perolehan'=> $item->{'Nilai Perolehan'} ?? 0,
                'edit_url'       => url('/asetTetap/' . $item->id . '/edit'),
            ];
        });

        return response()->json(['items' => $result]);
    }

    public function scanningResult(Request $request)
    {
        $items = DB::table('materials')->paginate(20);
        return view('asetTetap.index', compact('items'));
    }

    // ══════════════════════════════════════════════════════════
    //  HALAMAN PUBLIK QR — ASET TETAP
    // ══════════════════════════════════════════════════════════

    /**
     * Halaman publik saat QR Code Aset Tetap di-scan.
     * Tidak perlu login. Login → tampil tombol Edit.
     */
    public function showAset($id)
    {
        $item = DB::table('materials')->where('id', $id)->first();

        if (!$item) {
            abort(404, 'Aset tidak ditemukan.');
        }

        // Ambil hanya foto pertama
        $photo      = MaterialPhoto::where('material_id', $id)->orderBy('id')->first();
        $isLoggedIn = Auth::check();

        return view('qr.aset', compact('item', 'photo', 'isLoggedIn'));
    }

    // ══════════════════════════════════════════════════════════
    //  BARANG HABIS PAKAI
    // ══════════════════════════════════════════════════════════

    public function generateQRCodesItems(Request $request)
    {
        $selectedIds = $request->input('id_items', []);

        if (empty($selectedIds)) {
            return redirect()->route('items.index')
                ->with('error', 'Pilih minimal satu barang untuk cetak QR');
        }

        $dataproduk = Items::whereIn('id', $selectedIds)->get();

        return view('asetHabisPakai.qrcode', compact('dataproduk'));
    }

    // ══════════════════════════════════════════════════════════
    //  HALAMAN PUBLIK QR — BARANG HABIS PAKAI
    // ══════════════════════════════════════════════════════════

    /**
     * Halaman publik saat QR Code Barang Habis Pakai di-scan.
     * Tidak ada foto. Login → tampil tombol Edit.
     */
    public function showItem($id)
    {
        $item = Items::find($id);

        if (!$item) {
            abort(404, 'Barang tidak ditemukan.');
        }

        $isLoggedIn = Auth::check();

        return view('qr.item', compact('item', 'isLoggedIn'));
    }

    // ══════════════════════════════════════════════════════════
    //  ASET KELUAR
    // ══════════════════════════════════════════════════════════

    public function generateQRKeluar(Request $request, $id)
    {
        $asetKeluar = AsetKeluar::findOrFail($id);

        $asetIds   = json_decode($asetKeluar->aset, true) ?? [];
        $matIds    = array_column($asetIds, 'name');
        $materials = Materials::whereIn('id', $matIds)->get();

        $dataproduk = $materials->map(function ($mat) use ($asetKeluar) {
            $mat->bast_nomor  = $asetKeluar->nomor;
            $mat->bast_kepada = $asetKeluar->kepada;
            $mat->pihakSatu   = $asetKeluar->pihakSatu;
            $mat->pihakDua    = $asetKeluar->pihakDua;
            return $mat;
        });

        return view('asetKeluar.qrcode', compact('dataproduk', 'asetKeluar'));
    }
}