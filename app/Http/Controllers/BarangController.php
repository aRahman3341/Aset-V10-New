<?php

namespace App\Http\Controllers;

use App\Exports\AsetExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AsetImport;
use Carbon\Exceptions\InvalidFormatException as ExceptionsInvalidFormatException;
use Exception;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class BarangController extends Controller
{
    // ===================== INDEX =====================
    public function index(Request $request)
    {
        $query     = $request->input('query');
        $jenisBmn  = $request->input('jenis_bmn');
        $kondisi   = $request->input('kondisi');
        $statusBmn = $request->input('status_bmn');

        $items = DB::table('materials')
            // ✅ Sembunyikan aset yang sudah diserahkan (masuk Aset Keluar)
            ->where(function ($q) {
                $q->where('status', '!=', 'Diserahkan')
                  ->orWhereNull('status');
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('Kode Barang', 'LIKE', '%' . $query . '%')
                        ->orWhere('Nama Barang', 'LIKE', '%' . $query . '%')
                        ->orWhere('Jenis BMN',   'LIKE', '%' . $query . '%')
                        ->orWhere('nup',         'LIKE', '%' . $query . '%')
                        ->orWhere('merk',        'LIKE', '%' . $query . '%');
                });
            })
            ->when($jenisBmn, function ($q) use ($jenisBmn) {
                $q->where('Jenis BMN', $jenisBmn);
            })
            ->when($kondisi, function ($q) use ($kondisi) {
                $q->where('kondisi', $kondisi);
            })
            ->when($statusBmn, function ($q) use ($statusBmn) {
                $q->where('Status BMN', $statusBmn);
            })
            ->orderBy('id', 'asc')
            ->paginate(20);

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'table'      => view('asetTetap.table',      compact('items'))->render(),
                'pagination' => view('asetTetap.pagenation', compact('items'))->render(),
            ]);
        }

        return view('asetTetap.index', compact('items'));
    }

    // ===================== CREATE =====================
    public function create()
    {
        return view('asetTetap.create');
    }

    // ===================== STORE =====================
    public function store(Request $request)
    {
        $request->validate([
            'code'     => 'required',
            'nup'      => 'required',
            'name'     => 'required',
            'jenis_bmn'=> 'required',
        ], [
            'code.required'      => 'Kode Barang wajib diisi',
            'nup.required'       => 'NUP wajib diisi',
            'name.required'      => 'Nama Barang wajib diisi',
            'jenis_bmn.required' => 'Jenis BMN wajib dipilih',
        ]);

        DB::table('materials')->insert([
            'Kode Barang'              => $request->input('code'),
            'nup'                      => $request->input('nup'),
            'Nama Barang'              => $request->input('name'),
            'merk'                     => $request->input('name_fix'),
            'tipe'                     => $request->input('type'),
            'Jenis BMN'                => $request->input('jenis_bmn'),
            'kondisi'                  => $request->input('condition', 'Baik'),
            'Status BMN'               => $request->input('status_bmn', 'Aktif'),
            'Nilai Perolehan Pertama'  => $request->input('nilai')             ?: null,
            'Nilai Perolehan'          => $request->input('nilai_perolehan')   ?: null,
            'Nilai Penyusutan'         => $request->input('nilai_penyusutan')  ?: null,
            'Nilai Buku'               => $request->input('nilai_buku')        ?: null,
            'Tanggal Perolehan'        => $request->input('tanggal_perolehan') ?: null,
            'Tanggal Buku Pertama'     => $request->input('tanggal_buku_pertama') ?: null,
            'No PSP'                   => $request->input('no_psp'),
            'Tanggal PSP'              => $request->input('tanggal_psp') ?: null,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);

        return redirect()->route('asetTetap.index')->with('success', 'Data aset berhasil disimpan.');
    }

    // ===================== EDIT =====================
    public function edit($id)
    {
        $item = DB::table('materials')->where('id', $id)->first();
        return view('asetTetap.edit', compact('item'));
    }

    // ===================== UPDATE =====================
    public function update(Request $request, $id)
    {
        $request->validate([
            'code'     => 'required',
            'nup'      => 'required',
            'name'     => 'required',
            'jenis_bmn'=> 'required',
        ]);

        DB::table('materials')->where('id', $id)->update([
            'Kode Barang'              => $request->input('code'),
            'nup'                      => $request->input('nup'),
            'Nama Barang'              => $request->input('name'),
            'merk'                     => $request->input('name_fix'),
            'tipe'                     => $request->input('type'),
            'Jenis BMN'                => $request->input('jenis_bmn'),
            'kondisi'                  => $request->input('condition', 'Baik'),
            'Status BMN'               => $request->input('status_bmn', 'Aktif'),
            'Nilai Perolehan Pertama'  => $request->input('nilai')             ?: null,
            'Nilai Perolehan'          => $request->input('nilai_perolehan')   ?: null,
            'Nilai Penyusutan'         => $request->input('nilai_penyusutan')  ?: null,
            'Nilai Buku'               => $request->input('nilai_buku')        ?: null,
            'Tanggal Perolehan'        => $request->input('tanggal_perolehan') ?: null,
            'Tanggal Buku Pertama'     => $request->input('tanggal_buku_pertama') ?: null,
            'No PSP'                   => $request->input('no_psp'),
            'Tanggal PSP'              => $request->input('tanggal_psp') ?: null,
            'updated_at'               => now(),
        ]);

        return redirect()->route('asetTetap.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    // ===================== DESTROY =====================
    public function destroy($id)
    {
        DB::table('materials')->where('id', $id)->delete();
        return redirect()->route('asetTetap.index')->with('success', 'Data berhasil dihapus.');
    }

    // ===================== MULTI DELETE =====================
    public function multiDelete(Request $request)
    {
        $ids = $request->input('id_aset', []);
        if (!empty($ids)) {
            DB::table('materials')->whereIn('id', $ids)->delete();
        }
        return redirect()->route('asetTetap.index')->with('success', count($ids) . ' data berhasil dihapus');
    }

    // ===================== EXPORT =====================
    public function export(Request $request)
    {
        $selectedAsets = $request->input('id_aset', []);
        $fileName      = 'export_aset_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new AsetExport($selectedAsets), $fileName);
    }

    // ===================== SEARCH =====================
    public function search(Request $request)
    {
        $query = $request->input('query');

        $items = DB::table('materials')
            // ✅ Sembunyikan aset yang sudah diserahkan
            ->where(function ($q) {
                $q->where('status', '!=', 'Diserahkan')
                  ->orWhereNull('status');
            })
            ->where(function ($q) use ($query) {
                $q->where('Kode Barang', 'LIKE', '%' . $query . '%')
                  ->orWhere('Nama Barang', 'LIKE', '%' . $query . '%')
                  ->orWhere('Jenis BMN',   'LIKE', '%' . $query . '%')
                  ->orWhere('nup',         'LIKE', '%' . $query . '%')
                  ->orWhere('merk',        'LIKE', '%' . $query . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate(20);

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'table'      => view('asetTetap.table',      compact('items'))->render(),
                'pagination' => view('asetTetap.pagenation', compact('items'))->render(),
            ]);
        }

        return view('asetTetap.index', compact('items'));
    }

    // ===================== FILTER =====================
    public function filter(Request $request)
    {
        $q = DB::table('materials')
            // ✅ Sembunyikan aset yang sudah diserahkan
            ->where(function ($sub) {
                $sub->where('status', '!=', 'Diserahkan')
                    ->orWhereNull('status');
            });

        $jenisBmn = $request->input('jenis_bmn');
        if ($jenisBmn && $jenisBmn !== 'all') {
            $q->where('Jenis BMN', $jenisBmn);
        }

        $kondisi = $request->input('kondisi');
        if ($kondisi && $kondisi !== 'all') {
            $q->where('kondisi', $kondisi);
        }

        $statusBmn = $request->input('status_bmn');
        if ($statusBmn && $statusBmn !== 'all') {
            $q->where('Status BMN', $statusBmn);
        }

        $yearsDari   = $request->input('tahun_dari');
        $yearsSampai = $request->input('tahun_sampai');
        if ($yearsDari)   $q->whereYear('Tanggal Perolehan', '>=', $yearsDari);
        if ($yearsSampai) $q->whereYear('Tanggal Perolehan', '<=', $yearsSampai);

        $items = $q->orderBy('id', 'asc')->paginate(20);

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'table'      => view('asetTetap.table',      compact('items'))->render(),
                'pagination' => view('asetTetap.pagenation', compact('items'))->render(),
            ]);
        }

        return view('asetTetap.index', compact('items'));
    }

    // ===================== CHECK NUP =====================
    public function checkNupExists(Request $request)
    {
        $exists = DB::table('materials')
            ->where('nup', $request->input('nup'))
            ->where('Kode Barang', $request->input('code'))
            ->exists();

        return $exists
            ? response()->json(['message' => 'NUP already exists'], 400)
            : response()->json(['message' => 'NUP is valid'], 200);
    }

    // ===================== CHECK NO SERI =====================
    public function checkNoSeriExists(Request $request)
    {
        return response()->json(['message' => 'No Seri valid'], 200);
    }

    // ===================== IMPORT =====================
    public function import()
    {
        return view('asetTetap.import-file');
    }

    public function importStore(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xls,xlsx']);

        try {
            Excel::import(new AsetImport, $request->file('file'));
            return back()->withStatus('Import Berhasil');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors());
        } catch (ReaderException $e) {
            return back()->withErrors(['file' => 'Error loading the Excel file.']);
        } catch (ExceptionsInvalidFormatException $e) {
            return back()->withErrors(['file' => 'Invalid file format.']);
        } catch (Exception $e) {
            return back()->withErrors(['file' => 'Error: ' . $e->getMessage()]);
        }
    }
}