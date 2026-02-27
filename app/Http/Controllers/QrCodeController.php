<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materials;
use App\Models\Items;
use Illuminate\Support\Facades\DB;

class QrCodeController extends Controller
{
    // ══════════════════════════════════════════════════════════
    //  ASET TETAP
    // ══════════════════════════════════════════════════════════

    /**
     * Cetak QR Code aset tetap (dipanggil dari asetTetap/index.blade.php)
     * Route: POST /asetTetap/qrcodes  → name: generate_qrcodes
     */
    public function generateQRCodes(Request $request)
    {
        $request->validate([
            'id_aset'   => 'required|array|min:1',
            'id_aset.*' => 'integer|exists:materials,id',
        ]);

        $dataproduk = Materials::whereIn('id', $request->id_aset)->get();

        if ($dataproduk->isEmpty()) {
            return back()->with('error', 'Tidak ada data aset yang valid.');
        }

        // QR di-generate oleh QRCode.js di browser
        // Konten QR: "kode*nup*nama" → scane.blade.php parse format ini
        return view('asetTetap.qrcode', compact('dataproduk'));
    }

    /**
     * API scanning aset tetap — dipanggil scane.blade.php via fetch()
     * Route: POST /scanning  → name: scanning
     */
    public function scanning(Request $request)
    {
        $locations  = DB::table('locations')->get();
        $categories = DB::table('categories')->get();
        $employees  = DB::table('employees')->get();

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

        $query = Materials::where('code', $code);
        if (! empty($nup)) {
            $query->where('nup', $nup);
        }
        $items = $query->get();

        $result = $items->map(function ($item) use ($locations, $employees, $categories) {
            $loc = $locations->firstWhere('id', $item->store_location);
            $emp = $employees->firstWhere('id', $item->supervisor);
            $cat = $categories->firstWhere('id', $item->category);

            return array_merge($item->toArray(), [
                'lokasi_label'    => $loc
                    ? trim($loc->office) . ' / Lt.' . trim($loc->floor) . ' / R.' . trim($loc->room)
                    : '-',
                'supervisor_name' => $emp->name ?? '-',
                'category_name'   => $cat->name ?? '-',
            ]);
        });

        return response()->json([
            'items'      => $result,
            'locations'  => $locations,
            'employees'  => $employees,
            'categories' => $categories,
        ]);
    }

    public function scanningResult(Request $request)
    {
        $items      = json_decode($request->input('items', '[]'));
        $locations  = DB::table('locations')->get();
        $employees  = DB::table('employees')->get();
        $categories = DB::table('categories')->get();
        $tahun      = DB::table('materials')->select('years')->distinct()->orderBy('years')->get();

        return view('asetTetap.index', compact('items', 'locations', 'employees', 'categories', 'tahun'));
    }


    // ══════════════════════════════════════════════════════════
    //  BARANG HABIS PAKAI
    // ══════════════════════════════════════════════════════════

    /**
     * Cetak QR Code barang habis pakai (dipanggil dari asetHabisPakai/index.blade.php)
     * Route: POST /items/qrcodes  → name: items.qrcodes
     */
    public function generateQRCodesItems(Request $request)
    {
        $selectedIds = $request->input('id_items', []);

        if (empty($selectedIds)) {
            return redirect()->route('items.index')
                ->with('error', 'Pilih minimal satu barang untuk cetak QR');
        }

        // QR di-generate oleh QRCode.js di browser
        // Konten QR: kode barang ($item->code)
        $dataproduk = Items::whereIn('id', $selectedIds)->get();

        return view('asetHabisPakai.qrcode', compact('dataproduk'));
    }
}