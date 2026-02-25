<?php

namespace App\Http\Controllers;

use App\Exports\AsetOutExport;
use App\Models\Ajuan;
use App\Models\AsetOut;
use App\Models\employee;
use App\Models\Items;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as Pdf;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Termwind\Components\Dd;

class AsetOutController extends Controller
{
	public function get_data(Request $request)
	{
		$sess = Session::all();
		$query = $request->input('query');

		//$asetout = AsetOut::where('no_faktur', 'LIKE', '%' . $query . '%')
		//	->with(['asetOuts' => function ($query) {
		//		$query->select('id');
		//		}])
		//	->get();
		//$asetout = AsetOut::where('no_faktur', 'LIKE', '%' . $query . '%')
		//	->with(['asetOuts' => function ($query) {
		//		$query->select('id');
		//	}])
		//	->with('ajuan') // Tambahkan relasi ajuan di sini
		//	->get();
		$asetout = AsetOut::where('no_faktur', 'LIKE', '%' . $query . '%')
			->with(['asetOuts' => function ($query) {
				$query->select('id');
			}])
			->with('ajuan')
			->get();

		$status = 'tes';

		//foreach ($asetout as $aset) {
		//	// Loop melalui data AsetOut yang telah diambil
		//	foreach ($aset->ajuan as $ajuan) {
		//		// Loop melalui data Ajuan yang terkait dengan AsetOut
		//		if ($ajuan->status == 'Disetujui' || $ajuan->status == 'Ditolak') {
		//			// Jika status adalah 'Disetujui' atau 'Ditolak', ubah $status menjadi 'coba'
		//			$status = 'coba';
		//			break; // Keluar dari loop jika status sudah sesuai
		//		}
		//	}
			
		//	// Jika $status telah diubah menjadi 'coba', keluar dari loop utama
		//	if ($status == 'coba') {
		//		break;
		//	}
		//}
		//foreach ($asetout as $aset) {
		//	//foreach ($aset->ajuan as $ajuan) {
		//		//if ($ajuan instanceof Ajuan) { // Gantilah YourAjuanModel dengan nama model Ajuan Anda
		//			//if ($ajuan->status == 'Disetujui' || $ajuan->status == 'Ditolak') {
		//			// Ajuan adalah objek yang valid, sekarang Anda dapat mengakses properti
		//				$status = 'coba';
		//				//break;
		//			}
		//		//}
		//	//}
			
		//	//if ($status == 'coba') {
		//	//	break;
		//	//}
		//}
		foreach ($asetout as $aset) {
			//dd($aset->ajuan->status);
			if ($aset->ajuan->status == 'Disetujui' || $aset->ajuan->status == 'Ditolak') {
				$status = 'coba';
			}
		}
			
		return view('asetout.getData', compact('asetout', 'sess', 'status'));
	}

	//public function get_data(Request $request)
	//{
	//	$query = $request->input('query');

	//	$asetout = AsetOut::where('no_faktur', 'LIKE', '%' . $query . '%')
	//		->orWhereHas('itemskeluar', function ($q) use ($query) {
	//			$q->where('name', 'LIKE', '%' . $query . '%');
	//		})
	//		->paginate(20);

	//	$faktur = AsetOut::select('no_faktur')->distinct()->get();

	//	return view('asetout.getData', compact('asetout','faktur'));
	//}

	public function getList(Request $request)
	{
		$query = $request->input('query');

		$asetout = AsetOut::where('no_faktur', 'LIKE', '%' . $query . '%')
			->orWhereHas('itemskeluar', function ($q) use ($query) {
				$q->where('name', 'LIKE', '%' . $query . '%');
			})
			->paginate(20);

		$faktur = AsetOut::select('no_faktur')->distinct()->get();

		return view('asetout.listData', compact('asetout','faktur'));
	}
	
	public function filter(Request $request)
	{
		// Kode untuk fitur filter data
		$tabel = AsetOut::query(); // Menggunakan model AsetOut sebagai basis query

		$no_faktur = $request->input('no_faktur');
		if ($no_faktur !== 'all') {
			$tabel->where('no_faktur', $no_faktur);
		}
		// Mengambil input tanggal awal dan akhir dari form
		$start_date = $request->input('start_date');
		$end_date = $request->input('end_date');
	
		// Jika kedua tanggal diinputkan, gunakan filter untuk rentang tanggal
		if ($start_date && $end_date) {
			$start_date = Carbon::parse($start_date)->startOfDay();
			$end_date = Carbon::parse($end_date)->endOfDay();
			$tabel->whereBetween('created_at', [$start_date, $end_date]);
		}

		// Tambahkan bagian ini untuk memfilter data berdasarkan relasi dengan model ItemsKeluar
		$query = $request->input('query'); // Pastikan variabel $query sudah tersedia di bagian atas

		if ($query) {
			$tabel->whereHas('itemskeluar', function ($q) use ($query) {
				$q->where('name', 'LIKE', '%' . $query . '%');
			});
		}

		$asetout = $tabel->paginate(20);

		$faktur = AsetOut::select('no_faktur')->distinct()->get();

		return view('asetout.getData', compact('asetout', 'faktur'));
	}

	public function addData()
	{
		$sess = Session::all();
		$employe = employee::all();
		$itemshabis = Items::all();

		return view('asetout.add', ['employe' => $employe,'itemhabis' => $itemshabis,'sess' => $sess]);
	}

	//public function dataStore(Request $request)
	//{
	//	$validator = $request->validate([
	//		'no_faktur' => 'required',
	//		'name.*' => 'required', // Use name.* to validate all name inputs as an array
	//		'qty.*' => 'required|numeric', // Use qty.* to validate all qty inputs as an array
	//	], [
	//		'no_faktur.required' => 'No Faktur harus diisi',
	//		'name.*.required' => 'Nama harus diisi',
	//		'qty.*.required' => 'Qty harus diisi',
	//		'qty.*.numeric' => 'Qty harus diisi angka',
	//	]);

	//	// isi data ke tabel aset_outs
	//	$asetOutId = DB::table('aset_outs')->insertGetId([
	//		'no_faktur' => $validator['no_faktur'],
	//		'created_at' => Carbon::now(),
	//		'updated_at' => Carbon::now(),
	//	]);
	
	//	// Memproses data untuk tabel 'ajuans'
	//	$status = $request->input('status');
	//	$dataAjuans = [];
	//	$count = count($validator['name']);
	//	for ($i = 0; $i < $count; $i++) {
	//		$dataAjuans[] = [
	//			'faktur_id' => $asetOutId, // Mengisi kolom faktur_id dengan nilai dari aset_outs yang sesuai
	//			'name' => $validator['name'][$i],
	//			'qty' => $validator['qty'][$i],
	//			'total_qty' => 0,
	//			'pengaju' => 'orang',
	//			'status' => $status[$i],
	//			'created_at' => Carbon::now(),
	//			'updated_at' => Carbon::now(),
	//		];
	//	}
		
	//	DB::table('ajuans')->insert($dataAjuans);

	//	// Memproses data untuk tabel 'items'
	//	foreach ($dataAjuans as $data) {
	//		$name = $data['name'];
	//		$qty = $data['qty'];
			
	//		// Mengupdate kolom saldo pada tabel 'items'
	//		DB::table('items')
	//			->where('id', '=', $name) // Memastikan value id pada tabel 'items' sama dengan value pada inputan 'name'
	//			->decrement('saldo', $qty); // Mengurangkan nilai kolom saldo dengan value dari kolom qty
	//	}

	//	return redirect('/asetout');
	//}
	public function dataStore(Request $request)
	{
		$validator = Validator::make($request->all(), [
			//'no_faktur' => 'required',
			'mak' => 'required',
			'no_nd' => 'required',
			'name.*' => 'required',
			'qty.*' => 'required|numeric',
		], [
			//'no_faktur.required' => 'No Faktur harus diisi',
			'mak.required' => 'MAK harus diisi',
			'no_nd.required' => 'Nomor Nota Dinas harus diisi',
			'name.*.required' => 'Nama harus diisi',
			'qty.*.required' => 'Qty harus diisi',
			'qty.*.numeric' => 'Qty harus diisi angka',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		DB::beginTransaction(); // Mulai transaksi database

		try {
			// Mengganti simbol '/' menjadi '-'
			$noFaktur = str_replace('/', '^^', $request->input('no_faktur'));
			$noND = str_replace('/', '^^', $request->input('no_nd'));

			// isi data ke tabel aset_outs
			$asetOutId = DB::table('aset_outs')->insertGetId([
				//'no_faktur' => $request->input('no_faktur'),
				'mak' => $request->input('mak'),
				'no_nd' => $noND,
				'no_faktur' => $noFaktur,
				//'mak' => $validator['mak'],
				//'no_nd' => $validator['no_nd'],
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);

			// Memproses data untuk tabel 'ajuans'
			$operator = Session::all();
			$status = $request->input('status');
			$dataAjuans = [];
			$count = count($request->input('name'));
			for ($i = 0; $i < $count; $i++) {
				$dataAjuans[] = [
					'faktur_id' => $asetOutId,
					'name' => $request->input('name')[$i],
					'qty' => $request->input('qty')[$i],
					'total_qty' => 0,
					'pengaju' => $operator['id'],
					'status' => $status[$i],
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now(),
				];
			}

			DB::table('ajuans')->insert($dataAjuans);

			// Memproses data untuk tabel 'items' dan validasi saldo
			//foreach ($dataAjuans as $data) {
			//	$name = $data['name'];
			//	$qty = $data['qty'];

			//	$item = DB::table('items')
			//		->where('id', '=', $name)
			//		->first();

			//	if ($item->saldo < $qty) {
			//		DB::rollBack(); // Rollback transaksi jika qty melebihi saldo
			//		return redirect()->back()->with('error', 'Qty melebihi saldo barang');
			//	}

			//	DB::table('items')
			//		->where('id', '=', $name)
			//		->decrement('saldo', $qty);
			//}

			DB::commit(); // Commit transaksi jika semua proses berhasil

			return redirect('/asetout')->with('success', 'Data berhasil disimpan');
		} catch (\Exception $e) {
			DB::rollBack(); // Rollback transaksi jika terjadi exception atau kesalahan

			return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
		}
	}


	public function editData(Request $request, $id)
	{
		$asetout = AsetOut::findOrFail($id);
		//dd();
		$ajuan = Ajuan::where('faktur_id', $id)->get();
		$user = User::all();
		$itemshabis = Items::all();
		return view('asetout.update', ['itemhabis' => $itemshabis], compact('asetout','ajuan', 'user'));
	}

	public function update(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'no_faktur' => 'required',
			'name.*' => 'required',
			'qty.*' => 'required|numeric',
		], [
			'no_faktur.required' => 'No Faktur harus diisi',
			'name.*.required' => 'Nama harus diisi',
			'qty.*.required' => 'Qty harus diisi',
			'qty.*.numeric' => 'Qty harus diisi angka',
		]);
		
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}
		
		DB::beginTransaction(); // Mulai transaksi database
		
		try {
			// Mengganti simbol '/' menjadi '-'
			$noFaktur = str_replace('/', '^^', $request->input('no_faktur'));

			// isi data ke tabel aset_outs
			DB::table('aset_outs')
			->where('id', $id)
			->update([
				'no_faktur' => $noFaktur,
				'updated_at' => Carbon::now(),
			]);
			
			// Memproses data untuk tabel 'ajuans'
			$operator = Session::all();
			$status = $request->input('status');
			$dataAjuans = [];
			$count = count($request->input('name'));
			$coun = count($request->input('name1'));
			for ($i=0; $i < $coun; $i++) {
				$dataAjuans[] = [
					'faktur_id' => $id,
					'name' => $request->input('name1')[$i],
					'qty' => $request->input('qty1')[$i],
					'total_qty' => 0,
					'pengaju' => $operator['id'],
					'status' => $status[$i],
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now(),
				];
				DB::table('ajuans')->insert($dataAjuans);
			}
			for ($i = 0; $i < $count; $i++) {

				DB::table('ajuans')->where('id', $request->input('id')[$i])->update([
					'name' => $request->input('name')[$i],
					'qty' => $request->input('qty')[$i],
					'updated_at' => Carbon::now(),
				]);
			}
			

			DB::commit(); // Commit transaksi jika semua proses berhasil

			return redirect('/asetout')->with('success', 'Data berhasil disimpan');
		} catch (\Exception $e) {
			DB::rollBack(); // Rollback transaksi jika terjadi exception atau kesalahan

			return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
		}
	}

	public function editDataND(Request $request, $id)
	{
		$asetout = AsetOut::findOrFail($id);
		//dd();
		$ajuan = Ajuan::where('faktur_id', $id)->get();
		$user = User::all();
		$itemshabis = Items::all();
		return view('asetout.updateND', ['itemhabis' => $itemshabis], compact('asetout','ajuan', 'user'));
	}

	public function updateND(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			//'no_faktur' => 'required',
			'name.*' => 'required',
			'qty.*' => 'required|numeric',
		], [
			//'no_faktur.required' => 'No Faktur harus diisi',
			'name.*.required' => 'Nama harus diisi',
			'qty.*.required' => 'Qty harus diisi',
			'qty.*.numeric' => 'Qty harus diisi angka',
		]);
		
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}
		
		DB::beginTransaction(); // Mulai transaksi database
		
		try {
			// Mengganti simbol '/' menjadi '-'
			$mak = str_replace('/', '^^', $request->input('mak'));
			$noND = str_replace('/', '^^', $request->input('no_nd'));

			// isi data ke tabel aset_outs
			DB::table('aset_outs')
			->where('id', $id)
			->update([
				'no_nd' => $noND,
				'mak' => $mak,
				'updated_at' => Carbon::now(),
			]);
			
			// Memproses data untuk tabel 'ajuans'
			$operator = Session::all();
			$status = $request->input('status');
			$dataAjuans = [];
			$count = count($request->input('name'));
			$coun = count($request->input('name1'));
			for ($i=0; $i < $coun; $i++) {
				$dataAjuans[] = [
					'faktur_id' => $id,
					'name' => $request->input('name1')[$i],
					'qty' => $request->input('qty1')[$i],
					'total_qty' => 0,
					'pengaju' => $operator['id'],
					'status' => $status[$i],
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now(),
				];
				DB::table('ajuans')->insert($dataAjuans);
			}
			for ($i = 0; $i < $count; $i++) {

				DB::table('ajuans')->where('id', $request->input('id')[$i])->update([
					'name' => $request->input('name')[$i],
					'qty' => $request->input('qty')[$i],
					'updated_at' => Carbon::now(),
				]);
			}
			

			DB::commit(); // Commit transaksi jika semua proses berhasil

			return redirect('/asetout')->with('success', 'Data berhasil disimpan');
		} catch (\Exception $e) {
			DB::rollBack(); // Rollback transaksi jika terjadi exception atau kesalahan

			return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
		}
	}

	public function destroy($id)
	{
		AsetOut::destroy($id);
		return redirect('/asetout');
	}

	public function report()
	{
		$paging = 10;
		$ajuan = Ajuan::with(['item', 'asetOuts'])->paginate($paging);
		//$asetout = AsetOut::with(['itemskeluar'])->paginate($paging);

		return view('asetout.report', [ 'ajuan' => $ajuan]);
	}
	
	public function exportAll()
	{
		return Excel::download(new AsetOutExport, 'report Barang Keluar '.Carbon::now()->timestamp.'.xlsx');
	}

	public function cetakFaktur($noFaktur)
	{
		$cetak = AsetOut::with('itemskeluar')->where('no_faktur', [$noFaktur])->first();
		$data = Ajuan::with('item')->where('faktur_id', $cetak->id)->get();
		//$barang = Items::with('')
		//dd($data->item->name);
		return view('asetout.lampiran', compact('cetak','data'));
	}

	public function cetakNota($noFaktur)
	{
		$cetak = AsetOut::with('itemskeluar')->where('no_faktur', [$noFaktur])->first();
		$data = Ajuan::with('item')->where('faktur_id', $cetak->id)->get();
		//$barang = Items::with('')
		//dd($data->item->name);
		return view('asetout.lampiranNota', compact('cetak','data'));
	}

	public function download($noND)
    {
        // Get the specific data entry by id
		$item = AsetOut::where('no_nd', $noND)->first();
		$ajuan = Ajuan::with('item')->where('faktur_id', $item['id'])->first();
		//$ajuan1 = Ajuan::with('item')->where('faktur_id', $item['id'])->groupBy('faktur_id')->get();

		$ajuan1 = Ajuan::with('item')->where('faktur_id', $item['id'])->get();
		
        // Load the template from public/assets/templates/BAST.dotx
        $templatePath = public_path('assets/templates/ND.docx');
        $templateProcessor = new TemplateProcessor($templatePath);

        Settings::setOutputEscapingEnabled(false);
		// Tanggal dalam format awal (contoh: '2023-08-09 15:30:00')
		$created_at = $item['created_at'];

		// Ubah format tanggal
		//$formatted_date = date('d M Y', strtotime($created_at));
		$formatted_date = date('j F Y', strtotime($created_at));

        // Replace the placeholders in the template with the actual values
        $templateProcessor->setValue('mak', str_replace('^^', '/', $item['mak']));
        $templateProcessor->setValue('no_nd', str_replace('^^', '/', $item['no_nd']));
        $templateProcessor->setValue('date', $formatted_date);
        //$templateProcessor->setValue('name', $ajuan->item->name);
        //$templateProcessor->setValue('qty', $ajuan['qty']);
        //$templateProcessor->setValue('satuan', $ajuan->item->satuan);
        if(!empty($item->documentation)) {
        }else {
            $templateProcessor->setValue('dokumentasi', '');
        }

		$rowCount = count($ajuan1);
		$templateProcessor->cloneRow('no', $rowCount);
		
            $i = 0;
            $i1 = 1;
            $i2 = 1;
            $i3 = 1;
            foreach ($ajuan1 as $itemBarang) {
				// $rowIndex = $index + 1;
                $templateProcessor->setValue("no#".++$i, $i);
                $templateProcessor->setValue("name#".$i1++, $itemBarang->item->name);
                //$templateProcessor->setValue("name#".$i1++, $itemBarang['qty']);
                $templateProcessor->setValue("qty#".$i2++, $itemBarang['qty']);
                $templateProcessor->setValue("satuan#".$i3++, $itemBarang->item->satuan);
            }

        // Save the processed template to a temporary file
        $tempFilePath = tempnam(sys_get_temp_dir(), 'Nota Dinas') . '.docx';
        $templateProcessor->saveAs($tempFilePath);

        // Download the temporary file as response
        return response()->download($tempFilePath, 'Nota Dinas.docx')->deleteFileAfterSend();
    }
}
