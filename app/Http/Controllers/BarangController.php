<?php

namespace App\Http\Controllers;

use App\Exports\AsetExport;
use App\Models\Materials;
use App\Models\locations;
use App\Models\employee;
use App\Models\Category;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AsetImport;
use Carbon\Exceptions\InvalidFormatException as ExceptionsInvalidFormatException;
use Exception;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\InvalidFormatException;

class BarangController extends Controller
{
    public function index()
    {
        $locations   = DB::table('locations')->get();
        $categories  = DB::table('categories')->get();
        $employees   = DB::table('employees')->get();
        $items       = DB::table('materials')->get();
        $tahun       = DB::table('materials')->get();

        return view('asetTetap.index', compact('items', 'locations', 'employees', 'categories', 'tahun'));
    }

    public function create()
    {
        $locations     = DB::table('locations')->get();
        $categories    = DB::table('categories')->get();
        $employees     = DB::table('employees')->get();
        $gedungOptions = DB::table('locations')->distinct('office')->pluck('office');
        $lantaiOptions = DB::table('locations')->distinct('floor')->pluck('floor');

        return view('asetTetap.create', [
            'locations'     => $locations,
            'employees'     => $employees,
            'categories'    => $categories,
            'gedungOptions' => $gedungOptions,
            'lantaiOptions' => $lantaiOptions,
        ]);
    }

    public function store(Request $request)
    {
        $office     = $request->input('gedung');
        $floor      = $request->input('lantai');
        $room       = $request->input('ruangan');

        $locationId = DB::table('locations')
            ->where('office', $office)
            ->where('floor', $floor)
            ->where('room', $room)
            ->value('id');

        $material = new Materials();

        // --- Identitas Utama ---
        $material->code          = $request->input('kode_barang');
        $material->nup           = $request->input('nup');
        $material->name          = $request->input('nama_barang');
        $material->name_fix      = $request->input('merk');
        $material->no_seri       = $request->input('no_seri');

        // --- Klasifikasi ---
        $material->category      = $request->input('category');
        $material->condition     = $request->input('kondisi');
        $material->status        = $request->input('status', 'Tidak Dipakai');
        $material->type          = $request->input('type', 'Tetap');
        $material->jenis_bmn     = $request->input('jenis_bmn');
        $material->intra_extra   = $request->input('intra_extra');
        $material->status_bmn    = $request->input('status_bmn');

        // --- Nilai & Waktu ---
        $material->nilai                   = $request->input('nilai');
        $material->nilai_perolehan         = $request->input('nilai');           // alias nilai
        $material->nilai_penyusutan        = $request->input('nilai_penyusutan');
        $material->nilai_buku              = $request->input('nilai_buku');
        $material->years                   = $request->input('tahun');
        $material->bulan                   = $request->input('bulan') ?? date('n');
        $material->tanggal_perolehan       = $request->input('tanggal_perolehan');
        $material->tanggal_buku_pertama    = $request->input('tanggal_buku_pertama');
        $material->tanggal_pengapusan      = $request->input('tanggal_pengapusan');

        // --- Fisik ---
        $material->satuan        = $request->input('satuan');
        $material->umur_aset     = $request->input('lifetime');
        $material->life_time     = $request->input('lifetime');
        $material->specification = $request->input('spek');

        // --- Lokasi Fisik ---
        $material->store_location = $locationId;

        // --- Lokasi / Data BMN ---
        $material->kode_satker       = $request->input('kode_satker');
        $material->nama_satker       = $request->input('nama_satker');
        $material->kode_register     = $request->input('kode_register');
        $material->nama_kl           = $request->input('nama_kl');
        $material->nama_e1           = $request->input('nama_e1');
        $material->alamat            = $request->input('alamat');
        $material->kab_kota          = $request->input('kab_kota');
        $material->provinsi          = $request->input('provinsi');

        // --- Dokumen BMN ---
        $material->status_sertifikasi = $request->input('status_sertifikasi');
        $material->no_psp             = $request->input('no_psp');
        $material->tanggal_psp        = $request->input('tanggal_psp');
        $material->status_penggunaan  = $request->input('status_penggunaan');
        $material->no_polisi          = $request->input('no_polisi');
        $material->no_stnk            = $request->input('no_stnk');
        $material->nama_pengguna      = $request->input('nama_pengguna');

        // --- Kalibrasi ---
        $material->dikalibrasi        = $request->input('calibrate') === '1' ? 1 : 0;
        $material->kalibrasi_by       = $request->input('kalibrasi_by');
        $material->last_kalibrasi     = $request->input('last_kalibrasi');
        $material->schadule_kalibrasi = $request->input('schedule_kalibrasi');

        // --- Penanggung Jawab & Keterangan ---
        $material->supervisor  = $request->input('supervisor');
        $material->description = $request->input('keterangan');

        // --- Dokumentasi ---
        if ($request->hasFile('dokumentasi')) {
            $image       = $request->file('dokumentasi');
            $filename    = $image->getClientOriginalName();
            $destination = public_path('uploads');
            $image->move($destination, $filename);
            $material->documentation = $filename;
        }

        $material->save();

        return redirect('/asetTetap')->with('success', 'Data aset berhasil disimpan.');
    }

    public function edit($id)
    {
        $item          = Materials::find($id);
        $employees     = DB::table('employees')->get();
        $locations     = DB::table('locations')->get();
        $categories    = DB::table('categories')->get();
        $prevLocation  = DB::table('locations')->where('id', $item->store_location)->first();
        $gedungOptions = DB::table('locations')->distinct('office')->pluck('office');
        $lantaiOptions = DB::table('locations')->distinct('floor')->pluck('floor');

        return view('asetTetap.edit', [
            'item'          => $item,
            'locations'     => $locations,
            'employees'     => $employees,
            'gedungOptions' => $gedungOptions,
            'lantaiOptions' => $lantaiOptions,
            'prevLocation'  => $prevLocation,
            'categories'    => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $office = $request->input('gedung');
        $floor  = $request->input('lantai');
        $room   = $request->input('ruangan');

        $locationId = DB::table('locations')
            ->where('office', $office)
            ->where('floor', $floor)
            ->where('room', $room)
            ->value('id');

        $material = Materials::findOrFail($id);

        // --- Identitas Utama ---
        $material->code     = $request->input('code');
        $material->nup      = $request->input('nup');
        $material->name     = $request->input('name');
        $material->name_fix = $request->input('name_fix');
        $material->no_seri  = $request->input('no_seri');

        // --- Klasifikasi ---
        $material->category    = $request->input('category');
        $material->condition   = $request->input('condition');
        $material->status      = $request->input('status');
        $material->type        = $request->input('type');
        $material->jenis_bmn   = $request->input('jenis_bmn');
        $material->intra_extra = $request->input('intra_extra');
        $material->status_bmn  = $request->input('status_bmn');

        // --- Nilai & Waktu ---
        $material->nilai                = $request->input('nilai');
        $material->nilai_perolehan      = $request->input('nilai');
        $material->nilai_penyusutan     = $request->input('nilai_penyusutan');
        $material->nilai_buku           = $request->input('nilai_buku');
        $material->years                = $request->input('years');
        $material->satuan               = $request->input('satuan');
        $material->store_location       = $locationId;
        $material->tanggal_perolehan    = $request->input('tanggal_perolehan');
        $material->tanggal_buku_pertama = $request->input('tanggal_buku_pertama');
        $material->tanggal_pengapusan   = $request->input('tanggal_pengapusan');

        // --- Fisik ---
        $material->umur_aset     = $request->input('umur_aset');
        $material->life_time     = $request->input('life_time');
        $material->quantity      = $request->input('quantity');
        $material->specification = $request->input('specification');
        $material->description   = $request->input('description');

        // --- Lokasi BMN ---
        $material->kode_satker   = $request->input('kode_satker');
        $material->nama_satker   = $request->input('nama_satker');
        $material->kode_register = $request->input('kode_register');
        $material->nama_kl       = $request->input('nama_kl');
        $material->nama_e1       = $request->input('nama_e1');
        $material->alamat        = $request->input('alamat');
        $material->kab_kota      = $request->input('kab_kota');
        $material->provinsi      = $request->input('provinsi');

        // --- Dokumen BMN ---
        $material->status_sertifikasi = $request->input('status_sertifikasi');
        $material->no_psp             = $request->input('no_psp');
        $material->tanggal_psp        = $request->input('tanggal_psp');
        $material->status_penggunaan  = $request->input('status_penggunaan');
        $material->no_polisi          = $request->input('no_polisi');
        $material->no_stnk            = $request->input('no_stnk');
        $material->nama_pengguna      = $request->input('nama_pengguna');

        // --- Kalibrasi ---
        $material->dikalibrasi        = $request->has('dikalibrasi') ? 1 : 0;
        $material->kalibrasi_by       = $request->input('kalibrasi_by');
        $material->last_kalibrasi     = $request->input('last_kalibrasi');
        $material->schadule_kalibrasi = $request->input('schadule_kalibrasi');

        // --- Dokumentasi ---
        if ($request->hasFile('documentation')) {
            $image       = $request->file('documentation');
            $filename    = time() . '_' . $image->getClientOriginalName();
            $destination = public_path('uploads');
            $image->move($destination, $filename);
            $material->documentation = $filename;
        }

        $material->save();

        return redirect('/asetTetap')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $material = Materials::findOrFail($id);
        $filename = $material->documentation;

        if (!empty($filename)) {
            $destination = public_path('uploads/' . $filename);
            if (file_exists($destination)) {
                unlink($destination);
            }
        }

        $material->delete();
        return response()->json(['message' => 'Data deleted successfully']);
    }

    public function multiDelete(Request $request)
    {
        foreach ($request->id_aset as $id) {
            $material = Materials::findOrFail($id);
            $filename = $material->documentation;

            if (!empty($filename)) {
                $destination = public_path('uploads/' . $filename);
                if (file_exists($destination)) {
                    unlink($destination);
                }
            }
            $material->delete();
        }

        return redirect()->route('asetTetap.index')->with('success', 'Data deleted successfully');
    }

    // ===================== EXPORT (XLSX Rapi) =====================
    public function export(Request $request)
    {
        $selectedAsets = $request->input('id_aset', []);
        $fileName      = 'export_aset_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new AsetExport($selectedAsets), $fileName);
    }

    // ===================== CHECK NUP =====================
    public function checkNupExists(Request $request)
    {
        $nup    = $request->input('nup');
        $code   = $request->input('code');
        $oldNup = $request->input('old_nup');

        if ($oldNup) {
            if ($nup !== $oldNup) {
                $exists = Materials::where('nup', $nup)->where('code', $code)->exists();
            } else {
                $exists = false;
            }
        } else {
            $exists = Materials::where('nup', $nup)->where('code', $code)->exists();
        }

        if ($exists) {
            return response()->json(['message' => 'NUP with code ' . $code . ' already exists or has different values'], 400);
        }

        return response()->json(['message' => 'NUP is valid'], 200);
    }

    // ===================== CHECK NO SERI =====================
    public function checkNoSeriExists(Request $request)
    {
        $noSeri    = $request->input('no_seri');
        $oldNoSeri = $request->input('old_no_seri');

        if ($oldNoSeri) {
            $exists = Materials::where('no_seri', $noSeri)->where('no_seri', '!=', $oldNoSeri)->exists();
        } else {
            $exists = Materials::where('no_seri', $noSeri)->exists();
        }

        if ($exists) {
            return response()->json(['message' => 'No Seri already exists or has different values'], 400);
        }

        return response()->json(['message' => 'No Seri valid'], 200);
    }

    // ===================== SEARCH =====================
    public function search(Request $request)
    {
        $locations  = DB::table('locations')->get();
        $categories = DB::table('categories')->get();
        $employees  = DB::table('employees')->get();
        $tahun      = DB::table('materials')->get();
        $query      = $request->input('query');

        $items = Materials::where('code', 'LIKE', '%' . $query . '%')
            ->orWhere('nup', 'LIKE', '%' . $query . '%')
            ->orWhere('name', 'LIKE', '%' . $query . '%')
            ->orWhere('name_fix', 'LIKE', '%' . $query . '%')
            ->orWhere('years', 'LIKE', '%' . $query . '%')
            ->orWhere('jenis_bmn', 'LIKE', '%' . $query . '%')
            ->orWhere('kode_satker', 'LIKE', '%' . $query . '%')
            ->orWhere('nama_satker', 'LIKE', '%' . $query . '%')
            ->get();

        return view('asetTetap.index', compact('items', 'locations', 'employees', 'categories', 'tahun'));
    }

    // ===================== FILTER =====================
    public function filter(Request $request)
    {
        $locations  = DB::table('locations')->get();
        $categories = DB::table('categories')->get();
        $employees  = DB::table('employees')->get();
        $tahun      = DB::table('materials')->get();

        $query = DB::table('materials');

        $type = $request->input('type');
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        $category = $request->input('category');
        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $years_from = $request->input('years_from');
        if ($years_from && $years_from !== 'dari') {
            $query->where('years', '>=', $years_from);
        }

        $years_till = $request->input('years_till');
        if ($years_till && $years_till !== 'sampai') {
            $query->where('years', '<=', $years_till);
        }

        $office = $request->input('gedung');
        $floor  = $request->input('lantai');
        $room   = $request->input('ruangan');

        if ($office || $floor || $room) {
            $query->whereIn('store_location', function ($q) use ($office, $floor, $room) {
                $q->select('id')->from('locations')->where('office', $office);
                if ($floor) $q->where('floor', $floor);
                if ($room)  $q->where('room', $room);
            });
        }

        $condition = $request->input('condition');
        if ($condition && $condition !== 'all') {
            $query->where('condition', $condition);
        }

        $supervisor = $request->input('supervisor');
        if ($supervisor && $supervisor !== 'all') {
            $query->where('supervisor', $supervisor);
        }

        $calibrated = $request->input('calibrate');
        if ($calibrated !== null && $calibrated !== 'all') {
            $query->where('dikalibrasi', $calibrated);
        }

        $items = $query->get();

        return view('asetTetap.index', compact('items', 'locations', 'employees', 'categories', 'tahun'));
    }

    // ===================== IMPORT =====================
    public function import()
    {
        return view('asetTetap.import-file');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        try {
            $file        = $request->file('file');
            $spreadsheet = IOFactory::load($file);
            $worksheet   = $spreadsheet->getActiveSheet();

            Excel::import(new AsetImport, $file);

            return back()->withStatus('Import Berhasil');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors());
        } catch (ReaderException $e) {
            return back()->withErrors(['file' => 'Error loading the Excel file. Please check the file and try again.']);
        } catch (ExceptionsInvalidFormatException $e) {
            return back()->withErrors(['file' => 'Invalid file format. Please upload a valid Excel file.']);
        } catch (Exception $e) {
            error_log("Error processing Excel file: " . $e->getMessage() . " in " . $e->getFile() . " at line " . $e->getLine());
            return back()->withErrors(['file' => 'There was a problem processing the Excel file: ' . $e->getMessage()]);
        }
    }
}