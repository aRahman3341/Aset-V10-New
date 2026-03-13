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

        // FIX: kolom 'name' tidak ada di materials → pakai nama_barang & kode_barang
        $loan = peminjaman::with(['material', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('code',      'LIKE', '%' . $query . '%')
                  ->orWhere('peminjam','LIKE', '%' . $query . '%')
                  ->orWhereHas('material', fn($m) =>
                        $m->where('nama_barang', 'LIKE', '%' . $query . '%')
                          ->orWhere('kode_barang','LIKE', '%' . $query . '%')
                  );
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

        $loan = peminjaman::with(['material', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('code',      'LIKE', '%' . $query . '%')
                  ->orWhere('peminjam','LIKE', '%' . $query . '%')
                  ->orWhereHas('material', fn($m) =>
                        $m->where('nama_barang', 'LIKE', '%' . $query . '%')
                          ->orWhere('kode_barang','LIKE', '%' . $query . '%')
                  );
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paging);

        return view('peminjaman.report', ['loan' => $loan]);
    }

    public function create()
    {
        $users    = User::all();
        // FIX: kolom kondisi = 'kondisi', bukan 'condition'
        $material = Materials::where('kondisi', 'Baik')->get();
        return view('peminjaman.add', ['users' => $users, 'material' => $material]);
    }

    public function store(Request $request)
    {
        return $this->dataStore($request);
    }

    public function dataStore(Request $request)
    {
        $operator  = Session::all();
        $validated = $request->validate([
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

        $lastCode  = DB::table('peminjamen')->max('code');
        $newNumber = $lastCode ? intval(substr($lastCode, 1)) + 1 : 1;
        $code      = 'P' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        DB::table('peminjamen')->insert([
            'code'        => $code,
            'material_id' => $validated['material_id'],
            'tgl_pinjam'  => $validated['tgl_pinjam'],
            'tgl_kembali' => $validated['tgl_kembali'],
            'employee_id' => $operator['id'] ?? null,
            'peminjam'    => $validated['peminjam'],
            'status'      => 'Dipinjam',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        return redirect('/peminjaman')->with('success', 'Peminjaman berhasil disimpan.');
    }

    public function kembali(Request $request, $id)
    {
        $loan  = peminjaman::with('material')->findOrFail($id);
        $users = User::orderBy('name')->get();
        return view('peminjaman.update', compact('loan', 'users'));
    }

    public function pengembalian(Request $request, $id)
    {
        $validated = $request->validate(['tgl_kembali' => 'required']);

        peminjaman::where('id', $id)->update([
            'tgl_kembali' => $validated['tgl_kembali'],
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
        $validated = $request->validate([
            'material_id' => 'required',
            'tgl_pinjam'  => 'required',
            'tgl_kembali' => 'required',
            'peminjam'    => 'required',
        ]);

        $operator = Session::all();

        peminjaman::where('id', $id)->update([
            'material_id' => $validated['material_id'],
            'tgl_pinjam'  => $validated['tgl_pinjam'],
            'tgl_kembali' => $validated['tgl_kembali'],
            'employee_id' => $operator['id'] ?? null,
            'peminjam'    => $validated['peminjam'],
            'updated_at'  => Carbon::now(),
        ]);

        return redirect('/peminjaman')->with('success', 'Data peminjaman berhasil diubah.');
    }

    public function destroy($id)
    {
        peminjaman::destroy($id);
        return redirect('/peminjaman')->with('success', 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════════
    //  CETAK SURAT PEMINJAMAN (download .docx)
    // ══════════════════════════════════════════════
    public function cetakSurat($id)
    {
        $loan = peminjaman::with(['material', 'user'])->findOrFail($id);

        $templatePath = public_path('assets/templates/surat_peminjaman.docx');

        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template surat tidak ditemukan. Pastikan file ada di: public/assets/templates/surat_peminjaman.docx');
        }

        $template = new TemplateProcessor($templatePath);

        // Petugas / operator
        $user = $loan->user;
        $template->setValue('nama_petugas', $user->name    ?? '-');
        $template->setValue('nip_petugas',  $user->nip     ?? '-');
        $template->setValue('jabatan',      $user->jabatan ?? 'Petugas Gudang');
        $template->setValue('bagian',       $user->bagian  ?? '-');

        // Nomor & tanggal surat
        $template->setValue('nomor',       $loan->code ?? '-');
        $template->setValue('tgl_pinjam',  $loan->tgl_pinjam  ? Carbon::parse($loan->tgl_pinjam)->locale('id')->isoFormat('D MMMM Y')  : '-');
        $template->setValue('tgl_kembali', $loan->tgl_kembali ? Carbon::parse($loan->tgl_kembali)->locale('id')->isoFormat('D MMMM Y') : '-');

        // Peminjam
        $template->setValue('peminjam', $loan->peminjam ?? '-');

        // Barang
        $m = $loan->material;
        $template->setValue('jenis_bmn',   $m->jenis_bmn   ?? '-');
        $template->setValue('nama_barang', $m->nama_barang ?? '-');
        $template->setValue('kode_barang', $m->kode_barang ?? '-');
        $template->setValue('nup',         $m->nup         ?? '-');
        $template->setValue('kondisi',     $m->kondisi     ?? 'Baik');

        $filename = 'Surat_Peminjaman_' . ($loan->code ?? $id) . '.docx';
        $tempPath = storage_path('app/public/' . $filename);

        if (!is_dir(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }

        $template->saveAs($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    // ══════════════════════════════════════════════
    //  REPORT & EXPORT
    // ══════════════════════════════════════════════
    public function report()
    {
        $loan = peminjaman::with(['material'])->paginate(10);
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
        $query  = peminjaman::query();
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
        $loan  = $query->with(['material', 'user'])->paginate(20);
        $codes = peminjaman::select('employee_id')->distinct()->get();
        $sess  = Session::all();
        return view('peminjaman.getData', compact('loan', 'codes', 'sess'));
    }
}