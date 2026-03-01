<?php

namespace App\Http\Controllers;

use App\Exports\PeminjamanExport;
use App\Exports\PeminjamanExportAll;
use App\Models\employee;
use App\Models\Materials;
use App\Models\peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $sess   = Session::all();
        $paging = 10;
        $query  = $request->input('query');

        $loan = peminjaman::with(['material', 'employee', 'user'])
            ->where('code', 'LIKE', '%' . $query . '%')
            ->orWhere('peminjam', 'LIKE', '%' . $query . '%')
            ->orWhereHas('material', fn($q) => $q->where('name', 'LIKE', '%' . $query . '%'))
            ->orWhereHas('user',     fn($q) => $q->where('name', 'LIKE', '%' . $query . '%'))
            ->paginate($paging);

        $items = peminjaman::where('code', 'LIKE', '%' . $query . '%')->get();
        $codes = peminjaman::where('status', 1)->get();

        return view('peminjaman.getData', compact('items', 'loan', 'codes', 'sess'));
    }

    public function search(Request $request)
    {
        $paging = 10;
        $query  = $request->input('query');

        $loan = peminjaman::with(['material', 'employee', 'user'])
            ->where('code', 'LIKE', '%' . $query . '%')
            ->orWhere('peminjam', 'LIKE', '%' . $query . '%')
            ->orWhereHas('material', fn($q) => $q->where('name', 'LIKE', '%' . $query . '%'))
            ->orWhereHas('user',     fn($q) => $q->where('name', 'LIKE', '%' . $query . '%'))
            ->paginate($paging);

        $items = peminjaman::where('code', 'LIKE', '%' . $query . '%')->get();
        $codes = peminjaman::where('status', 1)->get();

        return view('peminjaman.report', ['loan' => $loan]);
    }

    public function create()
    {
        $users    = User::all();
        $material = Materials::whereNotIn('status', ['Diserahkan', 'Dipakai'])->get();

        return view('peminjaman.add', ['users' => $users, 'material' => $material]);
    }

    // ✅ Method 'store' — dipanggil oleh Route::resource & route('peminjaman.store')
    public function store(Request $request)
    {
        return $this->dataStore($request);
    }

    public function dataStore(Request $request)
    {
        function generateCode()
        {
            $lastCode = DB::table('peminjamen')->max('code');
            if ($lastCode) {
                $lastNumber = intval(substr($lastCode, 1));
                $newNumber  = $lastNumber + 1;
                return 'P' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }
            return 'P001';
        }

        $operator = Session::all();

        $validator = $request->validate([
            'material_id' => 'required',
            'tgl_pinjam'  => 'required',
            'tgl_kembali' => 'required',
            'peminjam'    => 'required',
        ], [
            'material_id.required' => 'Nama aset harus diisi',
            'tgl_pinjam.required'  => 'Tanggal pinjam harus diisi',
            'tgl_kembali.required' => 'Tanggal kembali harus diisi',
            'peminjam.required'    => 'Peminjam harus diisi',
        ]);

        DB::table('peminjamen')->insert([
            'code'        => generateCode(),
            'material_id' => $validator['material_id'],
            'tgl_pinjam'  => $validator['tgl_pinjam'],
            'tgl_kembali' => $validator['tgl_kembali'],
            'employee_id' => $operator['id'],
            'peminjam'    => $validator['peminjam'],
            'status'      => $request['status'],
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        Materials::where('id', $validator['material_id'])->update(['status' => 'Dipakai']);

        return redirect('/peminjaman');
    }

    public function kembali(Request $request, $id)
    {
        $loan    = peminjaman::findOrFail($id);
        $user    = User::all();
        $employe = Employee::all();
        return view('peminjaman.update', compact('loan', 'user', 'employe'));
    }

    public function pengembalian(Request $request, $id)
    {
        $validator = $request->validate([
            'tgl_kembali' => 'required',
        ], [
            'tgl_kembali.required' => 'Tanggal kembali harus diisi',
        ]);

        peminjaman::where('id', $id)->update([
            'tgl_kembali' => $validator['tgl_kembali'],
            'status'      => $request->status,
            'updated_at'  => Carbon::now(),
        ]);

        Materials::where('id', $request->input('material_id'))->update(['status' => 'Tidak Dipakai']);

        return redirect('/peminjaman');
    }

    public function edit(Request $request, $id)
    {
        $loan     = peminjaman::findOrFail($id);
        $material = Materials::whereNotIn('status', ['Diserahkan'])->get();
        return view('peminjaman.edit', compact('loan', 'material'));
    }

    // Alias untuk route::resource yang panggil edit
    public function editData(Request $request, $id)
    {
        return $this->edit($request, $id);
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'material_id' => 'required',
            'tgl_pinjam'  => 'required',
            'tgl_kembali' => 'required',
            'peminjam'    => 'required',
        ], [
            'material_id.required' => 'Nama aset harus diisi',
            'tgl_pinjam.required'  => 'Tanggal pinjam harus diisi',
            'tgl_kembali.required' => 'Tanggal kembali harus diisi',
            'peminjam.required'    => 'Peminjam harus diisi',
        ]);

        $operator = Session::all();

        peminjaman::where('id', $id)->update([
            'material_id' => $validator['material_id'],
            'tgl_pinjam'  => $validator['tgl_pinjam'],
            'tgl_kembali' => $validator['tgl_kembali'],
            'employee_id' => $operator['id'],
            'peminjam'    => $validator['peminjam'],
            'updated_at'  => Carbon::now(),
        ]);

        return redirect('/peminjaman');
    }

    public function destroy($id)
    {
        peminjaman::destroy($id);
        return redirect('/peminjaman');
    }

    public function report()
    {
        $paging = 10;
        $loan   = peminjaman::with(['material', 'employee'])->paginate($paging);
        return view('peminjaman.report', ['loan' => $loan]);
    }

    public function export(Request $request)
    {
        $from_date = $request->from_date;
        $to_date   = $request->to_date;

        if (!$from_date || !$to_date) {
            return redirect()->back()->with('error', 'Tolong isi range tanggal');
        }

        return Excel::download(
            new PeminjamanExport($from_date, $to_date),
            'report peminjaman ' . Carbon::now()->timestamp . '.xlsx'
        );
    }

    public function exportAll()
    {
        return Excel::download(
            new PeminjamanExportAll,
            'report peminjaman ' . Carbon::now()->timestamp . '.xlsx'
        );
    }

    public function filter(Request $request)
    {
        $tabel   = peminjaman::query();
        $employe = $request->input('code');

        if ($employe !== 'all') {
            $tabel->where('employee_id', $employe);
        }

        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        if ($start_date && $end_date) {
            $tabel->whereBetween('created_at', [
                Carbon::parse($start_date)->startOfDay(),
                Carbon::parse($end_date)->endOfDay(),
            ]);
        }

        $loan   = $tabel->paginate(20);
        $faktur = peminjaman::select('employee_id')->distinct()->get();
        $codes  = peminjaman::where('status', 1)->get();

        return view('peminjaman.getData', compact('loan', 'faktur', 'codes'));
    }
}