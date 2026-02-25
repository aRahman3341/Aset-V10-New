<?php

namespace App\Http\Controllers;

use App\Models\Ajuan;
use App\Models\AsetOut;
use App\Models\Items;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AjuanController extends Controller
{
	public function getData($id)
	{
		$sess = Session::all();
		if ($sess['jabatan'] !== 'Operator' ) {
            $users = User::all();
        }else{
            $users = User::where('jabatan', "Operator")->get();
        }
		$barangOut = AsetOut::where('id', $id)->get();
		$data = Ajuan::join('items', 'ajuans.name', '=', 'items.id')
		->where('ajuans.faktur_id', $id)
		->select('ajuans.*', 'items.satuan', 'items.name')
		->get($id);

		//$items = Items::all();
		//dd($data);

		return view('asetout.listData', compact(['barangOut','data', 'sess']));
	}

	//public function editData(Request $request,$id)
	//{
	//	$validator = $request->validate([
    //        'qty'   => 'required|numeric',
    //        //'name'   => 'required',
	//	],[
	//		'qty.required' => 'Qty tidak boleh kosong',
	//		'qty.numeric' => 'Qty harus nomor',
	//		//'name.required' => 'Nama kategori harus diisi',
	//	]);
	//	$update = [
	//		'qty' => $validator['qty'],
	//		//'name' => $validator['name'],
	//		'updated_at' => Carbon::now()
	//	];
	//	Ajuan::where('id', $id)->update($update);
	//	return redirect('/category');
	//}
	public function approve(Request $request, $id)
	{
		//dd($request);
		$validator = $request->validate([
			'total_qty' => 'required|numeric',
		], [
			'total_qty.required' => 'Qty tidak boleh kosong',
			'total_qty.numeric' => 'Qty harus nomor',
		]);
		$ajuan = Ajuan::find($id);

		// Ambil data item terkait
		$item = Items::find($ajuan->name);
		// Hitung jumlah yang akan diinputkan ke kolom saldo pada tabel items
		$jumlah_input_saldo = $item->saldo + $ajuan->total_qty;
		
		// Hitung hasil pengurangan qty dari saldo item
		$saldo_setelah_pengurangan = $jumlah_input_saldo - $validator['total_qty'];
		//dd($saldo_setelah_pengurangan);

		// Periksa apakah saldo cukup untuk melakukan pengurangan
		if ($saldo_setelah_pengurangan < 0) {
			return back()->withErrors(['total_qty' => 'Saldo item tidak mencukupi untuk melakukan pengurangan']);
		}

		// Lakukan update pada tabel ajuan
		$update = [
			'total_qty' => $validator['total_qty'],
			'status' => $request->input('status'),
			'updated_at' => Carbon::now()
		];
		Ajuan::where('id', $id)->update($update);

		// Update kolom saldo pada tabel items
		Items::where('id', $ajuan->name)->update(['saldo' => $saldo_setelah_pengurangan]);

		return redirect('/asetout');
	}

	public function reject(Request $request, $id)
	{
		$update = [
			'status' => $request->input('status'),
			'updated_at' => Carbon::now()
		];
		Ajuan::where('id', $id)->update($update);
		return redirect('/asetout');
		
	}
}
