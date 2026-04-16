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
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $sess   = Session::all();
        $paging = 10;
        $query  = $request->input('query', '');

        $loan = peminjaman::with(['user'])
            ->where(function ($q) use ($query) {
                $q->where('code',       'LIKE', '%' . $query . '%')
                  ->orWhere('peminjam', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paging);

        $codes = peminjaman::select('employee_id')->distinct()->get();

        return view('peminjaman.getData', compact('loan', 'codes', 'sess'));
    }

    public function search(Request $request)
    {
        $paging = 10;
        $query  = $request->input('query', '');

        $loan = peminjaman::with(['user'])
            ->where(function ($q) use ($query) {
                $q->where('code',       'LIKE', '%' . $query . '%')
                  ->orWhere('peminjam', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paging);

        return view('peminjaman.report', ['loan' => $loan]);
    }

    public function create()
    {
        $users    = User::whereIn('jabatan', ['Admin', 'Manager', 'Operator', 'admin', 'manager', 'operator'])
                        ->orderBy('name')->get();
        $material = Materials::where('kondisi', '!=', 'Rusak Berat')->orderBy('Nama Barang')->get();
        return view('peminjaman.add', compact('material', 'users'));
    }

    public function store(Request $request)
    {
        return $this->dataStore($request);
    }

    public function dataStore(Request $request)
    {
        $request->validate([
            'material_id'   => 'required|array|min:1',
            'material_id.*' => 'required',
            'tgl_pinjam'    => 'required',
            'tgl_kembali'   => 'required',
            'peminjam'      => 'required',
            'employee_id'   => 'required|exists:users,id',
        ], [
            'material_id.required'   => 'Pilih minimal 1 barang',
            'material_id.*.required' => 'Barang tidak boleh kosong',
            'tgl_pinjam.required'    => 'Tanggal pinjam harus diisi',
            'tgl_kembali.required'   => 'Tanggal kembali harus diisi',
            'peminjam.required'      => 'Peminjam harus diisi',
            'employee_id.required'   => 'Petugas gudang harus dipilih',
            'employee_id.exists'     => 'Petugas tidak valid',
        ]);

        $lastCode  = DB::table('peminjamen')->max('code');
        $newNumber = $lastCode ? intval(substr($lastCode, 1)) + 1 : 1;
        $code      = 'P' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        DB::table('peminjamen')->insert([
            'code'        => $code,
            'material_id' => json_encode(array_values($request->material_id)),
            'tgl_pinjam'  => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'employee_id' => $request->employee_id,
            'peminjam'    => $request->peminjam,
            'status'      => 'Dipinjam',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        return redirect('/peminjaman')->with('success', 'Peminjaman berhasil disimpan.');
    }

    public function kembali(Request $request, $id)
    {
        $loan  = peminjaman::with('user')->findOrFail($id);
        $users = User::whereIn('jabatan', ['Admin', 'Manager', 'Operator', 'admin', 'manager', 'operator'])
                     ->orderBy('name')->get();
        return view('peminjaman.update', compact('loan', 'users'));
    }

    public function pengembalian(Request $request, $id)
    {
        $validated = $request->validate([
            'tgl_kembali' => 'required',
            'employee_id' => 'required',
        ], [
            'tgl_kembali.required' => 'Tanggal kembali harus diisi',
            'employee_id.required' => 'Petugas gudang harus dipilih',
        ]);

        peminjaman::where('id', $id)->update([
            'tgl_kembali' => $validated['tgl_kembali'],
            'employee_id' => $validated['employee_id'],
            'status'      => 'Dikembalikan',
            'updated_at'  => Carbon::now(),
        ]);

        return redirect('/peminjaman')->with('success', 'Pengembalian berhasil dicatat.');
    }

    public function edit(Request $request, $id)
    {
        $loan     = peminjaman::findOrFail($id);
        $material = Materials::where('kondisi', '!=', 'Rusak Berat')->get();
        return view('peminjaman.edit', compact('loan', 'material'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'material_id'   => 'required|array|min:1',
            'material_id.*' => 'required',
            'tgl_pinjam'    => 'required',
            'tgl_kembali'   => 'required',
            'peminjam'      => 'required',
        ]);

        peminjaman::where('id', $id)->update([
            'material_id' => json_encode(array_values($request->material_id)),
            'tgl_pinjam'  => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'peminjam'    => $request->peminjam,
            'updated_at'  => Carbon::now(),
        ]);

        return redirect('/peminjaman')->with('success', 'Data peminjaman berhasil diubah.');
    }

    public function destroy($id)
    {
        peminjaman::destroy($id);
        return redirect('/peminjaman')->with('success', 'Data berhasil dihapus.');
    }

    public function cetakSurat($id)
    {
        // Gunakan with('user') saja, materials diakses via accessor
        $loan = peminjaman::with(['user'])->findOrFail($id);

        $templatePath = public_path('assets/templates/surat_peminjaman.docx');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template surat tidak ditemukan.');
        }

        $template = new TemplateProcessor($templatePath);

        $user = $loan->user;
        $template->setValue('nama_petugas', $user->name    ?? '-');
        $template->setValue('nip_petugas',  $user->nip     ?? '-');
        $template->setValue('jabatan',      $user->jabatan ?? 'Petugas Gudang');
        $template->setValue('bagian',       $user->bagian  ?? '-');

        $template->setValue('nomor',       $loan->code ?? '-');
        $template->setValue('tgl_pinjam',  $loan->tgl_pinjam  ? Carbon::parse($loan->tgl_pinjam)->locale('id')->isoFormat('D MMMM Y')  : '-');
        $template->setValue('tgl_kembali', $loan->tgl_kembali ? Carbon::parse($loan->tgl_kembali)->locale('id')->isoFormat('D MMMM Y') : '-');
        $template->setValue('peminjam',    $loan->peminjam ?? '-');

        $materials = $loan->materials; // accessor dari model
        $rowCount  = max($materials->count(), 1);
        $template->cloneRow('jenis_bmn', $rowCount);

        foreach ($materials as $i => $m) {
            $idx = $i + 1;
            $template->setValue("jenis_bmn#{$idx}",  $m->{'Jenis BMN'}   ?? '-');
            $template->setValue("nama_barang#{$idx}", $m->{'Nama Barang'} ?? '-');
            $template->setValue("kode_barang#{$idx}", $m->{'Kode Barang'} ?? '-');
            $template->setValue("nup#{$idx}",         $m->nup             ?? '-');
            $template->setValue("kondisi#{$idx}",     $m->kondisi         ?? 'Baik');
        }

        $filename = 'Surat_Peminjaman_' . ($loan->code ?? $id) . '.docx';
        $tempPath = storage_path('app/public/' . $filename);

        if (!is_dir(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }

        $template->saveAs($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function report()
    {
        $loan = peminjaman::paginate(10);
        return view('peminjaman.report', ['loan' => $loan]);
    }

    public function export(Request $request)
    {
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        if (!$from_date || !$to_date) {
            return redirect()->back()->with('error', 'Tolong isi range tanggal');
        }
        return Excel::download(new PeminjamanExport($from_date, $to_date),
            'report_peminjaman_' . Carbon::now()->timestamp . '.xlsx');
    }

    public function exportAll()
    {
        return Excel::download(new PeminjamanExportAll,
            'report_peminjaman_' . Carbon::now()->timestamp . '.xlsx');
    }

    public function filter(Request $request)
    {
        $query   = peminjaman::query();
        $employe = $request->input('code');
        if ($employe && $employe !== 'all') {
            $query->where('employee_id', $employe);
        }
        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        if ($start && $end) {
            $query->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay(),
            ]);
        }
        $loan  = $query->with(['user'])->paginate(20);
        $codes = peminjaman::select('employee_id')->distinct()->get();
        $sess  = Session::all();
        return view('peminjaman.getData', compact('loan', 'codes', 'sess'));
    }
}