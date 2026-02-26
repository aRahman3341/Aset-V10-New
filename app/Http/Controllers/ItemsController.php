<?php

namespace App\Http\Controllers;

use App\Exports\ItemsExport;
use App\Models\Items;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\ItemsImport;
use App\Models\Ajuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $query      = $request->input('query');
        $categories = $request->input('categories');
        $status     = $request->input('status');

        $items = Items::query()
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%')
                  ->orWhere('code', 'LIKE', '%' . $query . '%')
                  ->orWhere('categories', 'LIKE', '%' . $query . '%');
            })
            ->when($categories, function ($q) use ($categories) {
                $q->where('categories', $categories);
            })
            ->when($status !== null && $status !== '', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('saldo', 'desc')
            ->paginate(20)
            ->withQueryString();

        // COUNT (jumlah item) — sama dengan dashboard
        $countRT  = Items::where('categories', 'Rumah Tangga')->count();
        $countLab = Items::where('categories', 'Laboratorium')->count();
        $countATK = Items::where('categories', 'ATK')->count();

        // Jika AJAX request
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'table'      => view('asetHabisPakai.table', compact('items'))->render(),
                'pagination' => view('asetHabisPakai.pagenation', compact('items'))->render(),
            ]);
        }

        return view('asetHabisPakai.index', compact('items', 'countRT', 'countLab', 'countATK'));
    }

    public function filter(Request $request)
    {
        if ($request->isMethod('post')) {
            session([
                'filter_categories' => $request->input('categories'),
                'filter_status'     => $request->input('status'),
            ]);
        }

        $categories = session('filter_categories', 'all');
        $status     = session('filter_status', 'all');

        $tabel = DB::table('items');
        if ($categories && $categories !== 'all') $tabel->where('categories', $categories);
        if ($status !== null && $status !== 'all') $tabel->where('status', $status);

        $items    = $tabel->paginate(20)->withQueryString();
        $countRT  = Items::where('categories', 'Rumah Tangga')->count();
        $countLab = Items::where('categories', 'Laboratorium')->count();
        $countATK = Items::where('categories', 'ATK')->count();

        return view('asetHabisPakai.index', compact('items', 'countRT', 'countLab', 'countATK'));
    }

    public function create()
    {
        return view('asetHabisPakai.create');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'code'       => 'required|numeric',
            'name'       => 'required',
            'satuan'     => 'required',
            'categories' => 'required',
            'saldo'      => 'required|numeric',
        ], [
            'code.required'       => 'Kode barang tidak boleh kosong',
            'name.required'       => 'Nama barang tidak boleh kosong',
            'categories.required' => 'Kategori tidak boleh kosong',
            'satuan.required'     => 'Satuan tidak boleh kosong',
            'saldo.required'      => 'Saldo tidak boleh kosong',
            'code.numeric'        => 'Kode harus berupa nomor',
            'saldo.numeric'       => 'Saldo harus berupa nomor',
        ]);

        $item             = new Items();
        $item->code       = $validator['code'];
        $item->name       = $validator['name'];
        $item->categories = $validator['categories'];
        $item->saldo      = $validator['saldo'];
        $item->satuan     = $validator['satuan'];
        $item->status     = $request->input('status') ? 1 : 0;
        $item->save();

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = Items::findOrFail($id);
        return view('asetHabisPakai.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'code'       => 'required|numeric',
            'name'       => 'required',
            'satuan'     => 'required',
            'categories' => 'required',
            'saldo'      => 'required|numeric',
        ]);

        $item             = Items::findOrFail($id);
        $item->code       = $validator['code'];
        $item->name       = $validator['name'];
        $item->categories = $validator['categories'];
        $item->saldo      = $validator['saldo'];
        $item->satuan     = $validator['satuan'];
        $item->status     = $request->input('status') ? 1 : 0;
        $item->save();

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        Items::findOrFail($id)->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus');
    }

    public function checkCodeBExists(Request $request)
    {
        $exists = Items::where('code', $request->input('kode_barang'))->exists();
        return $exists
            ? response()->json(['message' => 'Code already exists'], 400)
            : response()->json(['message' => 'Code is valid'], 200);
    }

    public function fileImport(Request $request)
    {
        $validator = Validator::make($request->all(), ['file' => 'required|mimes:xls,xlsx']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->first());
        }
        try {
            Excel::import(new ItemsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimpor');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Gagal import: ' . $th->getMessage());
        }
    }

    public function export(Request $request)
    {
        $validator = $request->validate(['categories' => 'required'], ['categories.required' => 'Pilih kategori']);
        $categories   = $validator['categories'];
        $data         = Items::where('categories', $categories)->get();
        $totalBalance = Items::where('categories', $categories)->sum('saldo');
        return Excel::download(new ItemsExport($data, $totalBalance, $categories), 'Bahan Opname ' . $categories . '.xlsx');
    }

    public function qrcodes(Request $request)
    {
        $selectedIds = $request->input('id_items', []);
        $dataproduk  = Items::whereIn('id', $selectedIds)->get();
        return view('asetHabisPakai.qrcode', compact('dataproduk'));
    }

    public function multiDelete(Request $request)
    {
        $selectedIds = $request->input('id_items', []);
        if (empty($selectedIds)) {
            return redirect()->route('items.index')->with('error', 'Tidak ada data yang dipilih');
        }
        Items::whereIn('id', $selectedIds)->delete();
        return redirect()->route('items.index')->with('success', count($selectedIds) . ' data berhasil dihapus');
    }

    public function ajuan()
    {
        return view('pengajuan.getData', ['ajuan' => Ajuan::all()]);
    }

    public function pengajuan()
    {
        return view('pengajuan.add');
    }

    public function addPengajuan(Request $request)
    {
        $v = $request->validate([
            'code' => 'required|numeric', 'name' => 'required',
            'satuan' => 'required', 'saldo' => 'required|numeric',
        ]);
        $item = new Ajuan();
        $item->pengaju = Auth::id();
        $item->code    = $v['code'];
        $item->name    = $v['name'];
        $item->satuan  = $v['satuan'];
        $item->saldo   = $v['saldo'];
        $item->save();
        return redirect('/pengajuan/ajuan');
    }
}