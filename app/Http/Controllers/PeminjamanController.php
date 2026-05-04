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
    // ===================== SYNC LOAN STATUSES =====================
    /**
     * Sinkronisasi Status BMN berdasarkan status pinjaman aktif:
     *  - Terlambat : masih Dipinjam & tgl_kembali < hari ini
     *  - Dipinjam  : masih Dipinjam & tgl_kembali >= hari ini
     */
    private function syncLoanStatuses(): void
    {
        $today = Carbon::today();

        // ── Terlambat ─────────────────────────────────────────
        $overdueLoans = DB::table('peminjamen')
            ->where('status', 'Dipinjam')
            ->whereDate('tgl_kembali', '<', $today)
            ->get();

        foreach ($overdueLoans as $loan) {
            $ids = json_decode($loan->material_id, true);
            if (is_array($ids) && !empty($ids)) {
                DB::table('materials')
                    ->whereIn('id', $ids)
                    ->update(['Status BMN' => 'Terlambat', 'updated_at' => Carbon::now()]);
            }
        }

        // ── Dipinjam (masih tepat waktu) ───────────────────────
        $activeLoans = DB::table('peminjamen')
            ->where('status', 'Dipinjam')
            ->whereDate('tgl_kembali', '>=', $today)
            ->get();

        foreach ($activeLoans as $loan) {
            $ids = json_decode($loan->material_id, true);
            if (is_array($ids) && !empty($ids)) {
                DB::table('materials')
                    ->whereIn('id', $ids)
                    ->update(['Status BMN' => 'Dipinjam', 'updated_at' => Carbon::now()]);
            }
        }
    }

    // ===================== INDEX =====================
    public function index(Request $request)
    {
        $this->syncLoanStatuses();   // ← sinkronisasi setiap kali halaman dimuat

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

    // ===================== SEARCH =====================
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

    // ===================== CREATE =====================
    public function create()
    {
        $users    = User::whereIn('jabatan', ['Admin', 'Manager', 'Operator', 'admin', 'manager', 'operator'])
                        ->orderBy('name')->get();
        $material = Materials::where('kondisi', '!=', 'Rusak Berat')->orderBy('Nama Barang')->get();
        return view('peminjaman.add', compact('material', 'users'));
    }

    // ===================== STORE =====================
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

        $materialIds = array_values($request->material_id);

        DB::table('peminjamen')->insert([
            'code'        => $code,
            'material_id' => json_encode($materialIds),
            'tgl_pinjam'  => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'employee_id' => $request->employee_id,
            'peminjam'    => $request->peminjam,
            'status'      => 'Dipinjam',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        // ── Tentukan Status BMN: langsung cek apakah sudah terlambat ──
        $today       = Carbon::today();
        $tglKembali  = Carbon::parse($request->tgl_kembali)->startOfDay();
        $statusBmn   = $tglKembali->lt($today) ? 'Terlambat' : 'Dipinjam';

        DB::table('materials')
            ->whereIn('id', $materialIds)
            ->update([
                'Status BMN' => $statusBmn,
                'updated_at' => Carbon::now(),
            ]);

        return redirect('/peminjaman')->with('success', 'Peminjaman berhasil disimpan.');
    }

    // ===================== KEMBALI (form) =====================
    public function kembali(Request $request, $id)
    {
        $loan  = peminjaman::with('user')->findOrFail($id);
        $users = User::whereIn('jabatan', ['Admin', 'Manager', 'Operator', 'admin', 'manager', 'operator'])
                     ->orderBy('name')->get();
        return view('peminjaman.update', compact('loan', 'users'));
    }

    // ===================== PENGEMBALIAN =====================
    public function pengembalian(Request $request, $id)
    {
        $validated = $request->validate([
            'tgl_kembali' => 'required',
            'employee_id' => 'required',
        ], [
            'tgl_kembali.required' => 'Tanggal kembali harus diisi',
            'employee_id.required' => 'Petugas gudang harus dipilih',
        ]);

        $loan = peminjaman::findOrFail($id);

        peminjaman::where('id', $id)->update([
            'tgl_kembali' => $validated['tgl_kembali'],
            'employee_id' => $validated['employee_id'],
            'status'      => 'Dikembalikan',
            'updated_at'  => Carbon::now(),
        ]);

        // ── Kembalikan Status BMN ke Aktif ──
        $materialIds = json_decode($loan->material_id, true) ?? [];
        if (!empty($materialIds)) {
            DB::table('materials')
                ->whereIn('id', $materialIds)
                ->update([
                    'Status BMN' => 'Aktif',
                    'updated_at' => Carbon::now(),
                ]);
        }

        return redirect('/peminjaman')->with('success', 'Pengembalian berhasil dicatat.');
    }

    // ===================== EDIT =====================
    public function edit(Request $request, $id)
    {
        $loan     = peminjaman::findOrFail($id);
        $material = Materials::where('kondisi', '!=', 'Rusak Berat')->get();
        return view('peminjaman.edit', compact('loan', 'material'));
    }

    // ===================== UPDATE =====================
    public function update(Request $request, $id)
    {
        $request->validate([
            'material_id'   => 'required|array|min:1',
            'material_id.*' => 'required',
            'tgl_pinjam'    => 'required',
            'tgl_kembali'   => 'required',
            'peminjam'      => 'required',
        ]);

        $loanLama = peminjaman::findOrFail($id);
        $idsLama  = json_decode($loanLama->material_id, true) ?? [];
        $idsBaru  = array_values($request->material_id);

        // Kembalikan status aset lama ke Aktif
        if (!empty($idsLama)) {
            DB::table('materials')
                ->whereIn('id', $idsLama)
                ->update(['Status BMN' => 'Aktif', 'updated_at' => Carbon::now()]);
        }

        peminjaman::where('id', $id)->update([
            'material_id' => json_encode($idsBaru),
            'tgl_pinjam'  => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'peminjam'    => $request->peminjam,
            'updated_at'  => Carbon::now(),
        ]);

        // Set Status BMN aset baru (cek apakah terlambat)
        $today      = Carbon::today();
        $tglKembali = Carbon::parse($request->tgl_kembali)->startOfDay();
        $statusBmn  = $tglKembali->lt($today) ? 'Terlambat' : 'Dipinjam';

        DB::table('materials')
            ->whereIn('id', $idsBaru)
            ->update(['Status BMN' => $statusBmn, 'updated_at' => Carbon::now()]);

        return redirect('/peminjaman')->with('success', 'Data peminjaman berhasil diubah.');
    }

    // ===================== DESTROY =====================
    public function destroy($id)
    {
        $loan        = peminjaman::find($id);
        $materialIds = $loan ? (json_decode($loan->material_id, true) ?? []) : [];

        peminjaman::destroy($id);

        if (!empty($materialIds)) {
            DB::table('materials')
                ->whereIn('id', $materialIds)
                ->update(['Status BMN' => 'Aktif', 'updated_at' => Carbon::now()]);
        }

        return redirect('/peminjaman')->with('success', 'Data berhasil dihapus.');
    }

    // ===================== CETAK SURAT =====================
    public function cetakSurat($id)
    {
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

        $materials = $loan->materials;
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

    // ===================== REPORT =====================
    public function report()
    {
        $loan = peminjaman::paginate(10);
        return view('peminjaman.report', ['loan' => $loan]);
    }

    // ===================== EXPORT =====================
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

    // ===================== FILTER =====================
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