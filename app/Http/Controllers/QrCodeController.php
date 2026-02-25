<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;

use Illuminate\Http\Request;
use App\Models\Materials;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
// use Barryvdh\DomPDF\Facade\Pdf as PDF;

class QrCodeController extends Controller
{
    public function generateQRCodes(Request $request)
    {
        $locations = DB::table('locations')->get();
        $employees = DB::table('employees')->get();
        $pengguna = '';
        $lokasi = '';

        $dataproduk = [];
        $qrcode = [];
        foreach ($request->id_aset as $id) {
            $produk = Materials::find($id);
            if ($produk) {
                foreach ($locations as $location) {
                    if ($location->id == $produk->store_location) {
                        $lokasi = $location->office .  '- Lt' . $location->floor . '- R' . $location->room;
                    } else {
                        $lokasi = '-';
                    }
                }

                foreach ($employees as $employe) {
                    if ($employe->id == $produk->supervisor) {
                        $pengguna = $employe->name;
                    } else {
                        $pengguna = '';
                    }
                }

                $kalibrasi = "";
                if ($produk->dikalibrasi = 1) {
                    $kalibrasi = $produk->last_kalibrasi;
                }

                $dataproduk[] = $produk;
                $qrcodeContent = $produk->code . '*' . $produk->nup . '*' . $produk->name . '*' . $pengguna . '*' . $lokasi . '*' . $kalibrasi;
                //$qrCodeImage = QrCode::format('png')->size(250)->merge('assets/img/PUPR.png', .3, true)->errorCorrection('H')->generate($qrcodeContent);
                $qrCodeImage = QrCode::format('png')->size(250)->merge('assets/img/PUPR.png', .2, true)->errorCorrection('H')->generate($qrcodeContent);
                $base64QRCode = base64_encode($qrCodeImage);
                $qrcode[] = $base64QRCode;
            }
        }

        $no = 0;
        $pdf = PDF::loadView('asetTetap.qrcode', compact('dataproduk', 'qrcode', 'no'));
        // $qrcode = base64_encode(QrCode::format('svg')->size(90)->errorCorrection('H')->generate('string'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('Aset.pdf');
    }

    public function scanning(Request $request)
    {
        $locations = DB::table('locations')->get();
        $categories = DB::table('categories')->get();
        $employees = DB::table('employees')->get();
        $tahun = DB::table('materials')->get();

        $items = Materials::where('code', 'LIKE', $request->input('code'))
            ->where('nup', 'LIKE', $request->input('nup'))
            ->get();

        $data = [
            'items' => $items,
            'locations' => $locations,
            'employees' => $employees,
            'categories' => $categories,
            'tahun' => $tahun,
        ];

        return response()->json($data);
    }

    public function scanningResult(Request $request)
    {
        $items = json_decode($request->input('items'));
        $locations = DB::table('locations')->get();
        $employees = DB::table('employees')->get();
        $categories = DB::table('categories')->get();
        $tahun = DB::table('materials')->get();

        return view('asetTetap.index', compact('items', 'locations', 'employees', 'categories', 'tahun'));
    }
}
