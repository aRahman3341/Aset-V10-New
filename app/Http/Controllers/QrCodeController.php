<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materials;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeController extends Controller
{
    /**
     * Generate QR Code PDF untuk aset yang dipilih
     */
    public function generateQRCodes(Request $request)
    {
        $request->validate([
            'id_aset'   => 'required|array|min:1',
            'id_aset.*' => 'integer|exists:materials,id',
        ]);

        $locations = DB::table('locations')->get()->keyBy('id');
        $employees = DB::table('employees')->get()->keyBy('id');

        $dataproduk = [];
        $qrcode     = [];

        foreach ($request->id_aset as $id) {
            $produk = Materials::find($id);

            if (! $produk) {
                continue;
            }

            // Lokasi
            $lokasi = '-';
            if (isset($locations[$produk->store_location])) {
                $loc    = $locations[$produk->store_location];
                $lokasi = trim($loc->office) . ' - Lt.' . trim($loc->floor) . ' - R.' . trim($loc->room);
            }

            // Pengguna / Supervisor
            $pengguna = '-';
            if (isset($employees[$produk->supervisor])) {
                $pengguna = $employees[$produk->supervisor]->name;
            }

            // Kalibrasi
            $kalibrasi = '-';
            if ($produk->dikalibrasi == 1 && $produk->last_kalibrasi) {
                $kalibrasi = $produk->last_kalibrasi;
            }

            // Konten QR
            $qrcodeContent = implode('*', [
                $produk->code     ?? '',
                $produk->nup      ?? '',
                $produk->name     ?? '',
                $pengguna,
                $lokasi,
                $kalibrasi,
            ]);

            // Generate QR pakai chillerlan/php-qrcode
            $options = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'    => QRCode::ECC_H,
                'scale'       => 6,
                'imageBase64' => false,
            ]);

            $qrCodeImage  = (new QRCode($options))->render($qrcodeContent);
            $dataproduk[] = $produk;
            $qrcode[]     = base64_encode($qrCodeImage);
        }

        if (empty($dataproduk)) {
            return back()->with('error', 'Tidak ada data aset yang valid.');
        }

        $no  = 0;
        $pdf = PDF::loadView('asetTetap.qrcode', compact('dataproduk', 'qrcode', 'no'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('QR-Aset-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * API: Cari aset berdasarkan hasil scan QR
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
}