<?php

namespace App\Http\Controllers;

use App\Exports\ItemsExport;
use App\Models\Items;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemsRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateItemsRequest;
use App\Imports\ItemsImport;
use App\Models\Ajuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ItemsController extends Controller
{
	public function index(Request $request)
	{
		//$items = Items::all();
		// Kode untuk fitur pencarian data
		$query = $request->input('query');

		$items = Items::where('name', 'LIKE', '%' . $query . '%')
			->orWhere('code', 'LIKE', '%' . $query . '%')
			->orWhere('categories', 'LIKE', '%' . $query . '%')
			->orderBy('saldo', 'desc') // Mengurutkan berdasarkan saldo (asc untuk ascending, desc untuk descending)
			->paginate(20);
		$countRT = Items::where('categories', 'Rumah Tangga')->sum('saldo');
		$countLab = Items::where('categories', 'Laboratorium')->sum('saldo');
		$countATK = Items::where('categories', 'ATK')->sum('saldo');

		return view('asetHabisPakai.index', compact('items', 'countRT', 'countLab', 'countATK'));
	}
	
	public function filter(Request $request)
	{
		// Kode untuk fitur filter data
		$tabel = DB::table('items');

        $categories = $request->input('categories');
        if ($categories !== 'all') {
            $tabel->where('categories', $categories);
        }
        $status = $request->input('status');
        if ($status !== 'all') {
            $tabel->where('status', $status);
        }
		
		$items = $tabel->paginate(20);
		//dd($items);

		$countRT = Items::where('categories', 'Rumah Tangga')->sum('saldo');
		$opsikRT = Items::where('categories', 'Rumah Tangga')->sum('opsik');
		$countLab = Items::where('categories', 'Laboratorium')->sum('saldo');
		$opsikLab = Items::where('categories', 'Laboratorium')->sum('opsik');
		$countATK = Items::where('categories', 'ATK')->sum('saldo');
		$opsikATK = Items::where('categories', 'ATK')->sum('opsik');

		return view('asetHabisPakai.index', compact('items', 'countRT', 'countLab', 'countATK', 'opsikRT', 'opsikLab', 'opsikATK'));
	}

	public function create()
	{
		return view('asetHabisPakai.create');
	}

	public function store(Request $request)
	{
		$validator = $request->validate([
			'code'	=> 'required|numeric',
			'name'	=> 'required',
			'satuan'	=> 'required',
			'categories'	=> 'required',
			'saldo'	=> 'required|numeric',
			//'opsik'	=> 'required|numeric',
		], [
			'code.required'	=> 'Kode barang tidak boleh kosong',
			'name.required'	=> 'Nama barang tidak boleh kosong',
			'categories.required'	=> 'Kategori barang tidak boleh kosong',
			'satuan.required'	=> 'Satuan tidak boleh kosong',
			'saldo.required'	=> 'Saldo tidak boleh kosong',
			//'opsik.required'	=> 'Hasil opsik tidak boleh kosong',
			'code.numeric'	=> 'Kode barang harus berupa nomor',
			'saldo.numeric'	=> 'Saldo barang harus berupa nomor',
			//'opsik.numeric'	=> 'Hasil opsik harus berupa nomor',
		]);
		//$saldo = $request->input('saldo') ?? 0;
		//$opsik = $request->input('opsik') ?? 0;
		
		$item = new Items();
		$item->code = $validator['code'];
		$item->name = $validator['name'];
		$item->categories = $validator['categories'];
		$item->saldo = $validator['saldo'];
		//$item->opsik = $validator['opsik'];
		$item->satuan = $validator['satuan'];
		$item->status = $request->input('status');
		$item->save();

		return redirect('/items');
	}

	public function edit($id)
	{
		$item = Items::find($id);

		return view('asetHabisPakai.edit', [
			'item' => $item,
		]);
	}

	public function update(Request $request, $id)
	{
		$validator = $request->validate([
			'code'	=> 'required|numeric',
			'name'	=> 'required',
			'satuan'	=> 'required',
			'categories'	=> 'required',
			'saldo'	=> 'required|numeric',
			//'opsik'	=> 'required|numeric',
		], [
			'code.required'	=> 'Kode barang tidak boleh kosong',
			'name.required'	=> 'Nama barang tidak boleh kosong',
			'categories.required'	=> 'Kategori barang tidak boleh kosong',
			'satuan.required'	=> 'Satuan tidak boleh kosong',
			'saldo.required'	=> 'Saldo tidak boleh kosong',
			//'opsik.required'	=> 'Hasil opsik tidak boleh kosong',
			'code.numeric'	=> 'Kode barang harus berupa nomor',
			'saldo.numeric'	=> 'Saldo barang harus berupa nomor',
			//'opsik.numeric'	=> 'Hasil opsik harus berupa nomor',
		]);

		$item = Items::find($id);
		$item->code = $validator['code'];
		$item->name = $validator['name'];
		$item->categories = $validator['categories'];
		$item->saldo = $validator['saldo'];
		$item->satuan = $validator['satuan'];
		$item->status = $request->input('status');
		$item->save();

		return redirect('/items');
	}

	public function destroy($id)
{
    $item = Items::findOrFail($id);
    $item->delete();
    return redirect()->route('items.index')->with('success', 'Item berhasil dihapus');
}

	public function checkCodeBExists(Request $request)
	{
		$kodeBarang = $request->input('kode_barang');

		// Check if the code already exists in the database
		$exists = Items::where('code', $kodeBarang)->exists();

		if ($exists) {
			return response()->json(['message' => 'Code already exists'], 400);
		}

		return response()->json(['message' => 'Code is valid'], 200);
	}

	public function fileImport(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'file' => 'required|mimes:xls,xlsx',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator->errors()->first());
		}

		//dd($request);
		try {
			$import = new ItemsImport;
			//Excel::import($import, $request->file('file')->getRealPath());
			Excel::import($import, $request->file('file'));

			// Tampilkan pesan berhasil atau lakukan redirect ke halaman lain

			return redirect()->back()->with('success', 'Data berhasil diimpor');
		} catch (\Throwable $th) {
			return redirect()->back()->withErrors('Terjadi kesalahan saat mengimpor data: ' . $th->getMessage());
		}
	}

	public function export(Request $request)
	{
		$validator = $request->validate([
			'categories'	=> 'required',
		], [
			'categories.required'	=> 'Tolong Pilih Kategori Barang',
		]);
		$categories = $validator['categories'];

		// Mengambil data yang sesuai dengan kategori yang dipilih
		$data = Items::where('categories', $categories)->get();

		// Menghitung jumlah saldo dari data yang sesuai kategori
		$totalBalance = Items::where('categories', $categories)->sum('saldo');
		//$totalOpsik = Items::where('categories', $categories)->sum('opsik');

		// Memanggil class export dan menyertakan data yang ingin diekspor
		return Excel::download(new ItemsExport($data, $totalBalance, $categories), 'Bahan Opname Fisik '.$categories.'.xlsx');
	}

	public function ajuan()
	{
		$ajuan = Ajuan::all();

		return view('pengajuan.getData', compact('ajuan'));
	}

	public function pengajuan()
	{
		return view('pengajuan.add');
	}

	public function addPengajuan(Request $request)
	{
		$validator = $request->validate([
			'code'   => 'required|numeric',
			'name'   => 'required',
			'satuan'   => 'required',
			'saldo'   => 'required|numeric',
			//'opsik'   => 'required|numeric',
		], [
			'code.required'	=> 'Kode barang harus diisi',
			'code.numeric'	=> 'Kode barang harus nomor',
			'name.required'	=> 'Nama barang harus diisi',
			'satuan.required'	=> 'Satuan harus diisi',
			'saldo.required'	=> 'Saldo harus diisi',
			'saldo.numeric'	=> 'Saldo harus nomor',
			//'opsik.numeric'	=> 'Hasil opsik harus nomor',
			//'opsik.required'	=> 'Hasil opsik harus diisi',
		]);
		$user_id = Auth::id();

		$item = new Ajuan();
		$item->pengaju = $user_id;
		$item->code = $validator['code'];
		$item->name = $validator['name'];
		$item->satuan = $validator['satuan'];
		$item->saldo = $validator['saldo'];
		//$item->opsik = $validator['opsik'];
		$item->save();

		return redirect('/pengajuan/ajuan');
	}

public function qrcodes(Request $request)
{
    $selectedIds = $request->input('id_items', []);
    $dataproduk = Items::whereIn('id', $selectedIds)->get();
    return view('asetHabisPakai.qrcode', compact('dataproduk'));
}

public function multiDelete(Request $request)
{
    $selectedIds = $request->input('id_items', []);
    Items::whereIn('id', $selectedIds)->delete();
    return redirect()->route('items.index')->with('success', 'Data terpilih berhasil dihapus');
}

}