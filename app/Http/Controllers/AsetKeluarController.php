<?php
namespace App\Http\Controllers;

use App\Exports\AsetKeluarExport;
use App\Models\AsetKeluar;
use App\Models\Materials;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;

class AsetKeluarController extends Controller
{
    public function index()
    {
        $paging     = 10;
        $asetkeluar = AsetKeluar::paginate($paging);
        $asets      = [];

        foreach ($asetkeluar as $aset) {
            $asetId      = json_decode($aset->aset);
            $nameValues  = array_column($asetId, 'name');
            $relatedMaterials = Materials::whereIn('id', $nameValues)->get();
            $asets[$aset->id] = $relatedMaterials;
        }

        return view('asetKeluar.index', ['asetkeluar' => $asetkeluar, 'asets' => $asets]);
    }

    public function addData()
    {
        $items = Materials::where(function ($q) {
            $q->where('status', '!=', 'Diserahkan')
              ->orWhereNull('status');
        })->get();

        return view('asetKeluar.add', ['items' => $items]);
    }

    public function dataStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (AsetKeluar::where('nomor', $value)->exists()) {
                        $fail('Nomor sudah ada');
                    }
                },
            ],
            'name' => 'required',
        ], [
            'nomor.required' => 'Nomor harus diisi',
            'name.required'  => 'Nama harus diisi',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $name = $request->input('name');
        $data = [];

        foreach ($name as $index => $item) {
            $data[] = ['name' => $item];
            DB::table('materials')->where('id', $item)->update(['status' => 'Diserahkan']);
        }

        $asetKeluar = new AsetKeluar();
        $asetKeluar->nomor            = $request->input('nomor');
        $asetKeluar->aset             = json_encode($data);
        $asetKeluar->pihakSatu        = $request->input('pihakSatu');
        $asetKeluar->pihakSatuNip     = $request->input('nipSatu');
        $asetKeluar->pihakSatuJabatan = $request->input('jabatanSatu');
        $asetKeluar->pihakDua         = $request->input('pihakDua');
        $asetKeluar->pihakDuaNIP      = $request->input('nipDua');
        $asetKeluar->pihakDuaJabatan  = $request->input('jabatanDua');
        $asetKeluar->kepada           = $request->input('kepada');
        $asetKeluar->created_at       = Carbon::now();
        $asetKeluar->updated_at       = Carbon::now();
        $asetKeluar->save();

        return redirect()->route('asetkeluar.index')->with('success', 'Data aset keluar berhasil disimpan.');
    }

    public function editData(Request $request, $id)
    {
        try {
            $asetKeluar = AsetKeluar::findOrFail($id);

            $asetIds    = json_decode($asetKeluar->aset);
            $currentIds = array_column($asetIds, 'name');

            $items = Materials::where(function ($q) use ($currentIds) {
                $q->where(function ($sub) {
                    $sub->where('status', '!=', 'Diserahkan')
                        ->orWhereNull('status');
                })
                ->orWhereIn('id', $currentIds);
            })->get();

            $asets            = [];
            $relatedMaterials = Materials::whereIn('id', $currentIds)->get();
            $asets[$asetKeluar->id] = $relatedMaterials;

            return view('asetKeluar.update', compact('asetKeluar', 'asets', 'items'));
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('asetkeluar.index')->with('error', 'Aset Keluar tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nomor' => [
                'required',
                function ($attribute, $value, $fail) use ($id) {
                    if (AsetKeluar::where('nomor', $value)->where('id', '!=', $id)->exists()) {
                        $fail('Nomor already exists');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $asetKeluar = AsetKeluar::findOrFail($id);

        // Kembalikan status aset LAMA
        $oldAsetIds    = json_decode($asetKeluar->aset);
        $oldNameValues = array_column($oldAsetIds, 'name');
        DB::table('materials')
            ->whereIn('id', $oldNameValues)
            ->update(['status' => 'Tidak Dipakai']);

        $name = $request->input('name');
        $data = [];

        foreach ($name as $index => $item) {
            $data[] = ['name' => $item];
        }

        $asetKeluar->nomor            = $request->input('nomor');
        $asetKeluar->aset             = json_encode($data);
        $asetKeluar->pihakSatu        = $request->input('pihakSatu');
        $asetKeluar->pihakSatuNip     = $request->input('nipSatu');
        $asetKeluar->pihakSatuJabatan = $request->input('jabatanSatu');
        $asetKeluar->pihakDua         = $request->input('pihakDua');
        $asetKeluar->pihakDuaNIP      = $request->input('nipDua');
        $asetKeluar->pihakDuaJabatan  = $request->input('jabatanDua');
        $asetKeluar->kepada           = $request->input('kepada');
        $asetKeluar->updated_at       = Carbon::now();
        $asetKeluar->save();

        // Set status aset BARU ke Diserahkan
        foreach ($name as $item) {
            DB::table('materials')->where('id', $item)->update(['status' => 'Diserahkan']);
        }

        return redirect('/asetkeluar')->with('success', 'Data aset keluar berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $asetKeluar = AsetKeluar::findOrFail($id);

        // Kembalikan status aset ke 'Tidak Dipakai'
        $asetIds    = json_decode($asetKeluar->aset);
        $nameValues = array_column($asetIds, 'name');
        DB::table('materials')
            ->whereIn('id', $nameValues)
            ->update(['status' => 'Tidak Dipakai']);

        $asetKeluar->delete();

        return redirect('/asetkeluar')->with('success', 'Data aset keluar berhasil dihapus dan aset dikembalikan ke daftar Aset Tetap.');
    }

    public function download($id)
    {
        $item = AsetKeluar::findOrFail($id);

        $asetId           = json_decode($item->aset);
        $nameValues       = array_column($asetId, 'name');
        $relatedMaterials = Materials::whereIn('id', $nameValues)->get();
        $asets[$item->id] = $relatedMaterials;

        $templatePath      = public_path('assets/templates/bast.docx');
        $templateProcessor = new TemplateProcessor($templatePath);

        Settings::setOutputEscapingEnabled(false);

        $bulanIndo = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
        ];
        $tgl        = $item->created_at;
        $dateString = $tgl->day . ' ' . $bulanIndo[$tgl->month] . ' ' . $tgl->year;

        $templateProcessor->setValue('nomor',            $item->nomor);
        $templateProcessor->setValue('date',             $dateString);
        $templateProcessor->setValue('tanggal',          $this->formatDate($item->created_at));
        $templateProcessor->setValue('kepada',           strtoupper($item->kepada));
        $templateProcessor->setValue('pihaksatu',        $item->pihakSatu);
        $templateProcessor->setValue('pihaksatunip',     $item->pihakSatuNip);
        $templateProcessor->setValue('pihaksatujabatan', $item->pihakSatuJabatan);
        $templateProcessor->setValue('pihakdua',         $item->pihakDua);
        $templateProcessor->setValue('pihakduanip',      $item->pihakDuaNIP);
        $templateProcessor->setValue('pihakduajabatan',  $item->pihakDuaJabatan);
        $templateProcessor->setValue('dokumentasi',      '');

        $rowCount = count($relatedMaterials);
        $templateProcessor->cloneRow('No', $rowCount);

        $i = $i1 = $i2 = $i3 = $i4 = $i5 = $i6 = 0;
        foreach ($relatedMaterials as $itemBarang) {
            $templateProcessor->setValue('No#'              . ++$i,  $i);
            $templateProcessor->setValue('Kode#'            . ++$i1, $itemBarang->{'Kode Barang'} ?? '-');
            $templateProcessor->setValue('NUP#'             . ++$i2, $itemBarang->nup ?? '-');
            $templateProcessor->setValue('Nama#'            . ++$i3, $itemBarang->{'Nama Barang'} ?? '-');
            $templateProcessor->setValue('Nilai#'           . ++$i4, $itemBarang->{'Nilai Perolehan'} ?? '-');
            $templateProcessor->setValue('Tahun_Perolehan#' . ++$i5, $itemBarang->{'Tanggal Perolehan'} ?? '-');
            $templateProcessor->setValue('Kondisi#'         . ++$i6, $itemBarang->kondisi ?? '-');
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'aset_keluar') . '.docx';
        $templateProcessor->saveAs($tempFilePath);

        return response()->download($tempFilePath, 'aset_keluar.docx')->deleteFileAfterSend();
    }

    // ===================== EXPORT BY RANGE TANGGAL =====================
    public function export(Request $request)
    {
        $from_date = $request->from_date;
        $to_date   = $request->to_date;

        if (!$from_date || !$to_date) {
            return redirect()->back()->with('error', 'Tolong isi range tanggal');
        }

        $data = AsetKeluar::whereBetween('created_at', [$from_date, $to_date])->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'No data available in the selected date range');
        }

        return Excel::download(
            new AsetKeluarExport($from_date, $to_date),
            'aset_keluar_' . $from_date . '_sd_' . $to_date . '.xlsx'
        );
    }

    // ===================== EXPORT SEMUA DATA =====================
    public function exportAll()
    {
        $data = AsetKeluar::orderBy('created_at', 'desc')->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada data aset keluar.');
        }

        return Excel::download(
            new AsetKeluarExport(null, null),
            'semua_aset_keluar_' . Carbon::now()->format('Ymd_His') . '.xlsx'
        );
    }

    // ===================== SEARCH =====================
    public function search(Request $request)
    {
        $query    = $request->input('query');
        $pageSize = 10;

        $asetkeluar = AsetKeluar::where('nomor', 'LIKE', '%' . $query . '%')
            ->orWhere('pihakSatu',    'LIKE', '%' . $query . '%')
            ->orWhere('pihakSatuNip', 'LIKE', '%' . $query . '%')
            ->orWhere('pihakDua',     'LIKE', '%' . $query . '%')
            ->orWhere('pihakDuaNIP',  'LIKE', '%' . $query . '%')
            ->orWhere('kepada',       'LIKE', '%' . $query . '%')
            ->paginate($pageSize);

        return view('asetKeluar.index', compact('asetkeluar'));
    }

    // ===================== PRIVATE HELPERS =====================
    private function formatDate($dateString)
    {
        $date          = new DateTime($dateString);
        $digits        = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $tensPrefixes  = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh'];
        $teensPrefixes = ['', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas',
                          'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        $days   = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $dayOfWeek  = $days[$date->format('w')];
        $dayOfMonth = (int)$date->format('d');
        $month      = $months[(int)$date->format('m')];
        $year       = $date->format('Y');

        if ($dayOfMonth >= 1 && $dayOfMonth <= 9) {
            $formattedDay = $digits[$dayOfMonth] . ' ';
        } elseif ($dayOfMonth >= 11 && $dayOfMonth <= 19) {
            $formattedDay = $teensPrefixes[$dayOfMonth - 10] . ' ';
        } else {
            $tensDigit    = (int)($dayOfMonth / 10);
            $onesDigit    = $dayOfMonth % 10;
            $formattedDay = $tensPrefixes[$tensDigit] . ' ';
            if ($onesDigit > 0) $formattedDay .= $digits[$onesDigit] . ' ';
        }

        return "Pada hari ini $dayOfWeek, tanggal $formattedDay Bulan $month Tahun " . $this->formatYear($year);
    }

    private function formatYear($year)
    {
        $formattedYear     = '';
        $digits            = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $tensPrefixes      = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh',
                              'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
        $teensPrefixes     = ['', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas',
                              'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        $hundredsPrefixes  = ['', 'Seratus', 'Dua Ratus', 'Tiga Ratus', 'Empat Ratus', 'Lima Ratus',
                              'Enam Ratus', 'Tujuh Ratus', 'Delapan Ratus', 'Sembilan Ratus'];
        $thousandsPrefixes = ['', 'Seribu', 'Dua Ribu', 'Tiga Ribu', 'Empat Ribu', 'Lima Ribu',
                              'Enam Ribu', 'Tujuh Ribu', 'Delapan Ribu', 'Sembilan Ribu'];

        $yearString = (string)$year;
        $length     = strlen($yearString);

        if ($length >= 4) $formattedYear .= $thousandsPrefixes[(int)$yearString[$length - 4]] . ' ';
        if ($length >= 3) $formattedYear .= $hundredsPrefixes[(int)$yearString[$length - 3]] . ' ';

        if ($length >= 2) {
            $tensDigit = (int)$yearString[$length - 2];
            $onesDigit = (int)$yearString[$length - 1];
            if ($tensDigit === 1) {
                $formattedYear .= $teensPrefixes[$onesDigit] . ' ';
            } else {
                $formattedYear .= $tensPrefixes[$tensDigit] . ' ';
                if ($onesDigit !== 0) $formattedYear .= $digits[$onesDigit] . ' ';
            }
        }

        return $formattedYear;
    }
}