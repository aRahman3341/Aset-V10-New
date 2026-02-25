<?php
namespace App\Http\Controllers;

use App\Exports\AsetKeluarExport;
use App\Models\AsetKeluar;
use App\Models\AsetOut;
use App\Models\Materials;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PDO;
use PhpOffice\PhpSpreadsheet\IOFactory as PhpSpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;




class AsetKeluarController extends Controller
{
    public function index()
	{
		$paging = 10;
		// $asetkeluar = AsetKeluar::with(['asetskeluar'])->paginate($paging);
		$asetkeluar = AsetKeluar::paginate($paging);

        $asets = [];

        foreach ($asetkeluar as $aset) {
            $asetId = json_decode($aset->aset);
            $nameValues = array_column($asetId, 'name');
            // dd($asetId);
            // $relatedMaterials = Materials::whereIn('id', $asetId)->get();
            $relatedMaterials = Materials::whereIn('id', $nameValues)->get();
            $asets[$aset->id] = $relatedMaterials;
        }

		return view('asetKeluar.index', ['asetkeluar' => $asetkeluar, 'asets' => $asets]);
	}

    public function addData()
	{
		$items = Materials::all();

		return view('asetKeluar.add', ['items' => $items]);
	}

    public function dataStore(Request $request)
{
    // dd($request);
     $validator = Validator::make($request->all(), [
        'nomor' => [
            'required',
            function ($attribute, $value, $fail) {
                // Custom validation logic to check if "nomor" exists in the "aset_keluars" table
                $exists = AsetKeluar::where('nomor', $value)->exists();
                if ($exists) {
                    $fail('Nomor sudah ada');
                }
            },
        ],
        'name' => 'required',
        // 'itemBarang' => 'required|array',
        // 'itemBarang.*' => 'required',
        // 'quantity' => 'required|array',
        // 'quantity.*' => 'required|numeric',
    ], [
        'nomor.required' => 'Nomor harus diisi',
        'nomor.exists' => 'Nomor sudah digunakan',
        'name.required' => 'Nama harus diisi',
        // 'itemBarang.required' => 'Item Barang harus diisi',
        // 'quantity.required' => 'Qty harus diisi',
        // 'quantity.numeric' => 'Qty harus diisi angka',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Process each itemBarang and quantity
    $name = $request->input('name');
    // $quantity = $request->input('quantity');

    $data = [];

    // Loop through aset and quantity to create the data array
    foreach ($name as $index => $item) {
        $data[] = [
            'name' => $item,
        ];

        Materials::where('id', $item)->update(['status' => 'Diserahkan']);

    }


    // Insert data into the database
    $asetKeluar = new AsetKeluar();
    $asetKeluar->nomor = $request->input('nomor');
    $asetKeluar->aset = json_encode($data);
    $asetKeluar->pihakSatu = $request->input('pihakSatu');
    $asetKeluar->pihakSatuNip = $request->input('nipSatu');
    $asetKeluar->pihakSatuJabatan = $request->input('jabatanSatu');
    // $asetKeluar->itemBarang = json_encode($data);
    $asetKeluar->pihakDua = $request->input('pihakDua');
    $asetKeluar->pihakDuaNIP = $request->input('nipDua');
    $asetKeluar->pihakDuaJabatan = $request->input('jabatanDua');
    $asetKeluar->kepada = $request->input('kepada');
    // $asetKeluar->technicalData = $request->input('technicalData');
    $asetKeluar->created_at = Carbon::now();
    $asetKeluar->updated_at = Carbon::now();
    $asetKeluar->save();


    // Insert itemBarang and quantity data
    // Update Materials table status to "Diserahkan"

    return redirect()->route('asetkeluar.index');
}

public function editData(Request $request, $id)
{
    try {
        $asetKeluar = AsetKeluar::findOrFail($id);
        $items = Materials::all();

        $asets = [];

        $asetIds = json_decode($asetKeluar->aset);
        $nameValues = array_column($asetIds, 'name');
        // Fetch related materials based on the names from the selected Asets
        $relatedMaterials = Materials::whereIn('id', $nameValues)->get();

        $asets[$asetKeluar->id] = $relatedMaterials;
        // dd($asetKeluar->aset);
        return view('asetKeluar.update', compact('asetKeluar', 'asets', 'items'));
    } catch (ModelNotFoundException $exception) {
        // Handle the case where the AsetKeluar record with the given ID is not found
        return redirect()->route('your.redirect.route')->with('error', 'Aset Keluar not found.');
    }
}


public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'nomor' => [
            'required',
            function ($attribute, $value, $fail) use ($id) {
                // Custom validation logic to check if "nomor" exists in the "aset_keluars" table
                $exists = AsetKeluar::where('nomor', $value)->where('id', '!=', $id)->exists();
                if ($exists) {
                    $fail('Nomor already exists');
                }
            },
        ],
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $name = $request->input('name');
    $data = [];

    foreach ($name as $index => $item) {
        $data[] = [
            'name' => $item,
        ];
    }

    $asetKeluar = AsetKeluar::findOrFail($id);
    $asetKeluar->nomor = $request->input('nomor');
    $asetKeluar->aset = json_encode($data);
    $asetKeluar->pihakSatu = $request->input('pihakSatu');
    $asetKeluar->pihakSatuNip = $request->input('nipSatu');
    $asetKeluar->pihakSatuJabatan = $request->input('jabatanSatu');
    $asetKeluar->pihakDua = $request->input('pihakDua');
    $asetKeluar->pihakDuaNIP = $request->input('nipDua');
    $asetKeluar->pihakDuaJabatan = $request->input('jabatanDua');
    $asetKeluar->kepada = $request->input('kepada');
    $asetKeluar->updated_at = Carbon::now();
    $asetKeluar->save();

    // Update Materials table status to "Diserahkan"
    foreach ($name as $item) {
        Materials::where('id', $item)->update(['status' => 'Diserahkan']);
    }

    return redirect('/asetkeluar');
}




    public function destroy(Request $request, $id)
	{
		AsetKeluar::destroy($id);
		return redirect('/asetkeluar');
	}




    public function download($id)
    {
        // Get the specific data entry by id
        $item = AsetKeluar::findOrFail($id);

        $asetId = json_decode($item->aset);
        $nameValues = array_column($asetId, 'name');
            // dd($asetId);
            // $relatedMaterials = Materials::whereIn('id', $asetId)->get();
        $relatedMaterials = Materials::whereIn('id', $nameValues)->get();
        $asets[$item->id] = $relatedMaterials;


        // Load the template from public/assets/templates/BAST.dotx
        $templatePath = public_path('assets/templates/bast.docx');
        $templateProcessor = new TemplateProcessor($templatePath);

        Settings::setOutputEscapingEnabled(false);

        $formattedDate = $this->formatDate($item->created_at);
        // Replace the placeholders in the template with the actual values
        $templateProcessor->setValue('nomor', $item->nomor);
        setlocale(LC_TIME, 'id_ID.utf8');

        // Format the date using the Indonesian locale
        $dateString = $item->created_at->formatLocalized('%e %B %Y');

        // Restore the original locale if needed
        setlocale(LC_TIME, ''); // This will reset the locale to the default setting

        // Now you can use the formatted date string as needed
        $templateProcessor->setValue('date', $dateString);
        $templateProcessor->setValue('tanggal', $formattedDate);
        $templateProcessor->setValue('kepada', strtoupper($item->kepada));
        $templateProcessor->setValue('kode', $item->aset);
        // $templateProcessor->setValue('nup', $aset->nup);
        // $templateProcessor->setValue('name', $aset->name);
        // $templateProcessor->setValue('nilai', number_format($aset->nilai, 0, ',', '.'));
        // $templateProcessor->setValue('tahun_perolehan', $aset->years);
        // $templateProcessor->setValue('kondisi', $aset->condition);
        $templateProcessor->setValue('pihaksatu', $item->pihakSatu);
        $templateProcessor->setValue('pihaksatunip', $item->pihakSatuNip);
        $templateProcessor->setValue('pihaksatujabatan', $item->pihakSatuJabatan);
        $templateProcessor->setValue('pihakdua', $item->pihakDua);
        $templateProcessor->setValue('pihakduanip', $item->pihakDuaNIP);
        $templateProcessor->setValue('pihakduajabatan', $item->pihakDuaJabatan);
        //dd($aset->documentation);
        // $templateProcessor->setValue('dokumentasi', $aset->documentation);
        if(!empty($item->documentation)) {
            // $templateProcessor->setImageValue('dokumentasi', array('path' => public_path('uploads/' . $aset->documentation), 'width' => 200, 'height' => 150));
        }else {
            $templateProcessor->setValue('dokumentasi', '');
        }

        // foreach ($asets as $asetId => $relatedMaterials) {
            $rowCount = count($relatedMaterials);
            $templateProcessor->cloneRow('No', $rowCount);
            // // Remove extra rows if needed
            // for ($i = $rowCount + 1; $i <= 3; $i++) {
            //     $templateProcessor->setValue("No#". $i, '');
            //     $templateProcessor->setValue("Kode#". $i, '');
            //     $templateProcessor->setValue("NUP#$i", '');
            //     $templateProcessor->setValue("Nama#$i", '');
            //     $templateProcessor->setValue("Nilai#$i", '');
            //     $templateProcessor->setValue("Tahun_Perolehan#$i", '');
            //     $templateProcessor->setValue("Kondisi#$i", '');
            // }
            $i = 0;
            $i1 = 1;
            $i2 = 1;
            $i3 = 1;
            $i4 = 1;
            $i5 = 1;
            $i6 = 1;
            foreach ($relatedMaterials as $index => $itemBarang) {
                // $rowIndex = $index + 1;
                $templateProcessor->setValue("No#".++$i, $i);
                $templateProcessor->setValue("Kode#".$i1++, $itemBarang['code']);
                $templateProcessor->setValue("NUP#".$i2++, $itemBarang['nup']);
                $templateProcessor->setValue("Nama#".$i3++, $itemBarang['name']);
                $templateProcessor->setValue("Nilai#".$i4++, $itemBarang['nilai']);
                $templateProcessor->setValue("Tahun_Perolehan#".$i5++, $itemBarang['years']);
                $templateProcessor->setValue("Kondisi#".$i6++, $itemBarang['condition']);
            }
        // }



        // Loop through the items and add them to the table in the template
        // $items = json_decode($item->itemBarang, true);
        // $tableRows = [];
        // foreach ($items as $index => $itemBarang) {
        //     $row = [
        //         'No' => $index + 1,
        //         'Nama Barang' => $itemBarang['itemBarang'],
        //         'Jumlah' => $itemBarang['qty'],
        //         'Keterangan' => "\t" . '☐ Sesuai <w:br/>' . "\t" . '☐ Tidak Sesuai',
        //     ];
        //     $tableRows[] = $row;
        // }
        // $templateProcessor->cloneRowAndSetValues('No', $tableRows);

        // // If technicalData exists, add it to the template
        // if ($item && isset($item->technicalData)) {
        //     $templateProcessor->setValue('technicalData', '- ' . $item->technicalData);
        // }

        // Save the processed template to a temporary file
        $tempFilePath = tempnam(sys_get_temp_dir(), 'aset_keluar') . '.docx';
        $templateProcessor->saveAs($tempFilePath);

        // Download the temporary file as response
        return response()->download($tempFilePath, 'aset_keluar.docx')->deleteFileAfterSend();
    }

    private function formatDate($dateString)
{
    // Convert the date string to a DateTime object
    $date = new DateTime($dateString);

    $digits = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
    $tensPrefixes = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh'];
    $teensPrefixes = ['', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $yearPrefixes = ['Tiga Dua Ribu', ''];

    // Get the day, month, and year components
    $dayOfWeek = $days[$date->format('w')];
    $dayOfMonth = (int)$date->format('d');
    $month = $months[(int)$date->format('m')];
    $year = $date->format('Y');

    // Format the day of the month in the desired format
    $formattedDayOfMonth = '';
    if ($dayOfMonth >= 1 && $dayOfMonth <= 9) {
        $formattedDayOfMonth .= $digits[$dayOfMonth] . ' ';
    } elseif ($dayOfMonth >= 11 && $dayOfMonth <= 19) {
        $formattedDayOfMonth .= $teensPrefixes[$dayOfMonth - 10] . ' ';
    } else {
        $tensDigit = (int)($dayOfMonth / 10);
        $onesDigit = $dayOfMonth % 10;
        $formattedDayOfMonth .= $tensPrefixes[$tensDigit] . ' ';
        if ($onesDigit > 0) {
            $formattedDayOfMonth .= $digits[$onesDigit] . ' ';
        }
    }

    // Format the year in the desired format
    $formattedYear = $this->formatYear($year, $yearPrefixes);

    // Construct the formatted date string
    $formattedDate = "Pada hari ini $dayOfWeek, tanggal $formattedDayOfMonth Bulan $month Tahun $formattedYear";

    return $formattedDate;
}

private function formatYear($year)
{
    $formattedYear = '';

    $digits = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
    $tensPrefixes = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
    $teensPrefixes = ['', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
    $hundredsPrefixes = ['', 'Seratus', 'Dua Ratus', 'Tiga Ratus', 'Empat Ratus', 'Lima Ratus', 'Enam Ratus', 'Tujuh Ratus', 'Delapan Ratus', 'Sembilan Ratus'];
    $thousandsPrefixes = ['', 'Seribu', 'Dua Ribu', 'Tiga Ribu', 'Empat Ribu', 'Lima Ribu', 'Enam Ribu', 'Tujuh Ribu', 'Delapan Ribu', 'Sembilan Ribu'];

    // Convert the year to a string and calculate its length
    $yearString = (string)$year;
    $length = strlen($yearString);

    // Handle the special cases for the first three digits (thousands, hundreds, and tens)
    if ($length >= 4) {
        $thousandsDigit = (int)$yearString[$length - 4];
        $formattedYear .= $thousandsPrefixes[$thousandsDigit] . ' ';
    }

    if ($length >= 3) {
        $hundredsDigit = (int)$yearString[$length - 3];
        $formattedYear .= $hundredsPrefixes[$hundredsDigit] . ' ';
    }

    if ($length >= 2) {
        $tensDigit = (int)$yearString[$length - 2];
        $onesDigit = (int)$yearString[$length - 1];

        if ($tensDigit === 1) {
            // Handle the case for '10' to '19'
            $formattedYear .= $teensPrefixes[$onesDigit] . ' ';
        } else {
            // Handle other tens places (20, 30, ..., 90)
            $formattedYear .= $tensPrefixes[$tensDigit] . ' ';

            // Handle the ones place (if not zero)
            if ($onesDigit !== 0) {
                $formattedYear .= $digits[$onesDigit] . ' ';
            }
        }
    }

    return $formattedYear;
}




    public function export(Request $request)
	{
		$from_date=$request->from_date;
		$to_date=$request->to_date;

		if (!$from_date || !$to_date) {
			return redirect()->back()->with('error', 'Tolong isi range tanggal');
		}

        $data = AsetKeluar::whereBetween('created_at', [$from_date, $to_date])->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'No data available in the selected date range');
        }

		return Excel::download(new AsetKeluarExport($from_date,$to_date), 'report Barang Keluar'.Carbon::now()->timestamp.'.xlsx');
	}

    public function search(Request $request)
{
    $query = $request->input('query');

    $pageSize = 10;
        $asetkeluar = AsetKeluar::where('nomor', 'LIKE', '%' . $query . '%')
                            ->orWhere('pihakSatu', 'LIKE', '%' . $query . '%')
                            ->orWhere('pihakSatuNip', 'LIKE', '%' . $query . '%')
                            ->orWhere('pihakDua', 'LIKE', '%' . $query . '%')
                            ->orWhere('pihakDuaNIP', 'LIKE', '%' . $query . '%')
                            ->orWhere('kepada', 'LIKE', '%' . $query . '%')
                            ->paginate($pageSize);

    return view('asetKeluar.index', compact('asetkeluar'));
}
}
