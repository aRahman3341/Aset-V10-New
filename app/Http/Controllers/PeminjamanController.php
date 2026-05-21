<?php

namespace App\Http\Controllers;

use App\Exports\PeminjamanExport;
use App\Exports\PeminjamanExportAll;
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
    // ─── Daftar pilihan Kop Surat ───────────────────────────────────────────────
    private const KOP_OPTIONS = [
        'BTSB'   => 'Balai Teknik Sains Bangunan (BTSB)',
        'SATKER' => 'Satuan Kerja Balai Teknik Sains Bangunan',
    ];

    // ─── Path template per kop ──────────────────────────────────────────────────
    private function templatePath(string $kop): string
    {
        $map = [
            'BTSB'   => public_path('assets/templates/surat_peminjaman_btsb.docx'),
            'SATKER' => public_path('assets/templates/surat_peminjaman_satker.docx'),
        ];
        return $map[$kop] ?? $map['BTSB'];
    }

    // ===================== HELPER: ID BARANG SEDANG DIPINJAM/TERLAMBAT =====================
    /**
     * Kembalikan array ID material yang sedang dipinjam atau terlambat.
     * $excludeLoanId: abaikan 1 peminjaman (dipakai saat edit agar item milik sendiri bisa dipilih).
     */
    private function getUnavailableIds(int $excludeLoanId = 0): array
    {
        $activeLoans = DB::table('peminjamen')
            ->whereIn('status', ['Dipinjam', 'Terlambat'])
            ->when($excludeLoanId, fn($q) => $q->where('id', '!=', $excludeLoanId))
            ->pluck('material_id');

        $ids = [];
        foreach ($activeLoans as $json) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $ids = array_merge($ids, $decoded);
            }
        }

        return array_unique(array_map('intval', $ids));
    }

    // ===================== SYNC LOAN STATUSES =====================
    private function syncLoanStatuses(): void
    {
        $today = Carbon::today();

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
        $this->syncLoanStatuses();

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
        $users          = User::whereIn('jabatan', ['Admin', 'Manager', 'Operator', 'admin', 'manager', 'operator'])
                              ->orderBy('name')->get();
        $material       = Materials::where('kondisi', '!=', 'Rusak Berat')->orderBy('Nama Barang')->get();
        $kopOptions     = self::KOP_OPTIONS;
        $unavailableIds = $this->getUnavailableIds();   // ← ID yang sedang dipinjam

        return view('peminjaman.add', compact('material', 'users', 'kopOptions', 'unavailableIds'));
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
            'kop_surat'     => 'required|in:BTSB,SATKER',
        ], [
            'material_id.required'   => 'Pilih minimal 1 barang',
            'material_id.*.required' => 'Barang tidak boleh kosong',
            'tgl_pinjam.required'    => 'Tanggal pinjam harus diisi',
            'tgl_kembali.required'   => 'Tanggal kembali harus diisi',
            'peminjam.required'      => 'Peminjam harus diisi',
            'employee_id.required'   => 'Petugas gudang harus dipilih',
            'employee_id.exists'     => 'Petugas tidak valid',
            'kop_surat.required'     => 'Pilih kop surat',
            'kop_surat.in'           => 'Kop surat tidak valid',
        ]);

        // ── Cek barang yang sedang dipinjam / terlambat ──────────────────────
        $selectedIds    = array_map('intval', $request->material_id);
        $unavailableIds = $this->getUnavailableIds();
        $konflik        = array_intersect($selectedIds, $unavailableIds);

        if (!empty($konflik)) {
            $namaKonflik = Materials::whereIn('id', $konflik)
                ->pluck('Nama Barang')
                ->implode(', ');
            return back()
                ->withInput()
                ->with('error', 'Barang berikut sedang dipinjam/terlambat dan tidak bisa dipilih: ' . $namaKonflik);
        }

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
            'kop_surat'   => $request->kop_surat,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        $today      = Carbon::today();
        $tglKembali = Carbon::parse($request->tgl_kembali)->startOfDay();
        $statusBmn  = $tglKembali->lt($today) ? 'Terlambat' : 'Dipinjam';

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
        $loan           = peminjaman::findOrFail($id);
        $material       = Materials::where('kondisi', '!=', 'Rusak Berat')->get();
        $kopOptions     = self::KOP_OPTIONS;
        // Kecualikan peminjaman ini sendiri agar item milik loan ini tetap bisa dipilih ulang
        $unavailableIds = $this->getUnavailableIds((int) $id);

        return view('peminjaman.edit', compact('loan', 'material', 'kopOptions', 'unavailableIds'));
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
            'kop_surat'     => 'required|in:BTSB,SATKER',
        ]);

        // ── Cek konflik (exclude loan ini sendiri) ────────────────────────────
        $selectedIds    = array_map('intval', $request->material_id);
        $unavailableIds = $this->getUnavailableIds((int) $id);
        $konflik        = array_intersect($selectedIds, $unavailableIds);

        if (!empty($konflik)) {
            $namaKonflik = Materials::whereIn('id', $konflik)
                ->pluck('Nama Barang')
                ->implode(', ');
            return back()
                ->withInput()
                ->with('error', 'Barang berikut sedang dipinjam/terlambat dan tidak bisa dipilih: ' . $namaKonflik);
        }

        $loanLama = peminjaman::findOrFail($id);
        $idsLama  = json_decode($loanLama->material_id, true) ?? [];
        $idsBaru  = array_values($request->material_id);

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
            'kop_surat'   => $request->kop_surat,
            'updated_at'  => Carbon::now(),
        ]);

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
        // ── 1. Ambil data ──────────────────────────────────────────────────────
        $loan        = peminjaman::with('user')->findOrFail($id);
        $materialIds = json_decode($loan->material_id, true) ?? [];
        $materials   = Materials::whereIn('id', $materialIds)->get();
        $petugas     = $loan->user;

        // ── 2. Pilih template berdasarkan kop_surat ────────────────────────────
        $kop          = $loan->kop_surat ?? 'BTSB';
        $templatePath = $this->templatePath($kop);

        if (!file_exists($templatePath)) {
            return back()->with('error',
                'Template surat tidak ditemukan: ' . basename($templatePath) .
                ' — pastikan file ada di public/assets/templates/'
            );
        }

        // ── 3. Isi placeholder header ──────────────────────────────────────────
        Carbon::setLocale('id');

        $template = new TemplateProcessor($templatePath);

        $template->setValue('nomor',       $loan->code ?? '-');
        $template->setValue('peminjam',    $loan->peminjam ?? '-');
        $template->setValue('tgl_pinjam',  $loan->tgl_pinjam
            ? Carbon::parse($loan->tgl_pinjam)->translatedFormat('d F Y') : '-');
        $template->setValue('tgl_kembali', $loan->tgl_kembali
            ? Carbon::parse($loan->tgl_kembali)->translatedFormat('d F Y') : '-');

        // Data petugas
        $template->setValue('nama_petugas', $petugas->name    ?? '-');
        $template->setValue('nip_petugas',  $petugas->nip     ?? '-');
        $template->setValue('jabatan',      $petugas->jabatan ?? 'Petugas Gudang');
        $template->setValue('bagian',       $petugas->bagian  ?? '-');

        // ── 4. Clone baris tabel barang ───────────────────────────────────────
        // Coba berbagai kemungkinan nama placeholder pertama di kolom tabel
        // (sesuaikan dengan nama di template .docx Anda)
        $rowCount       = max($materials->count(), 1);
        $cloneCandidates = ['no', 'jenis_bmn', 'nama_barang', 'kode_barang', 'nup'];
        $cloneSuccess    = false;

        foreach ($cloneCandidates as $candidate) {
            try {
                $template->cloneRow($candidate, $rowCount);
                $cloneSuccess = true;
                break;
            } catch (\Exception $e) {
                // coba kandidat berikutnya
            }
        }

        if (!$cloneSuccess) {
            // Fallback: tidak clone, langsung set nilai biasa (1 baris saja)
        }

        foreach ($materials as $i => $m) {
            $idx      = $i + 1;
            $nama     = $m->{'Nama Barang'} ?? ($m->nama_barang ?? '-');
            $kode     = $m->{'Kode Barang'} ?? ($m->kode_barang ?? '-');
            $jenis    = $m->{'Jenis BMN'}   ?? ($m->jenis_bmn   ?? 'MESIN PERALATAN NON TIK');
            $nup      = $m->nup             ?? '-';
            $kondisi  = $m->kondisi         ?? 'Baik';

            // Set dengan berbagai kemungkinan nama placeholder
            // ── Nomor urut ──
            foreach (['no', 'nomor_urut', 'num'] as $k) {
                try { $template->setValue("{$k}#{$idx}", (string) $idx); } catch (\Exception $e) {}
            }
            // ── Jenis BMN ──
            foreach (['jenis_bmn', 'jenis', 'jenis_barang'] as $k) {
                try { $template->setValue("{$k}#{$idx}", $jenis); } catch (\Exception $e) {}
            }
            // ── Nama Barang ──
            foreach (['nama_barang', 'nama', 'namabarang'] as $k) {
                try { $template->setValue("{$k}#{$idx}", $nama); } catch (\Exception $e) {}
            }
            // ── Kode Barang ──
            foreach (['kode_barang', 'kode', 'kodebarang'] as $k) {
                try { $template->setValue("{$k}#{$idx}", $kode); } catch (\Exception $e) {}
            }
            // ── NUP ──
            foreach (['nup'] as $k) {
                try { $template->setValue("{$k}#{$idx}", $nup); } catch (\Exception $e) {}
            }
            // ── Kondisi ──
            foreach (['kondisi', 'condition'] as $k) {
                try { $template->setValue("{$k}#{$idx}", $kondisi); } catch (\Exception $e) {}
            }
        }

        // ── 5. Simpan & download ───────────────────────────────────────────────
        $filename = 'Surat_Peminjaman_' . ($loan->code ?? $id) . '.docx';
        $tempDir  = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . $filename;
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
        return Excel::download(
            new PeminjamanExport($from_date, $to_date),
            'report_peminjaman_' . Carbon::now()->timestamp . '.xlsx'
        );
    }

    public function exportAll()
    {
        return Excel::download(
            new PeminjamanExportAll,
            'report_peminjaman_' . Carbon::now()->timestamp . '.xlsx'
        );
    }

    // ===================== FILTER =====================
    public function filter(Request $request)
    {
        $query = peminjaman::query()->with(['user']);
        $today = Carbon::today();

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

        $status = $request->input('status');
        if ($status && $status !== 'all') {
            if ($status === 'Terlambat') {
                $query->where('status', '!=', 'Dikembalikan')
                      ->whereDate('tgl_kembali', '<', $today);
            } elseif ($status === 'Dipinjam') {
                $query->where('status', 'Dipinjam')
                      ->whereDate('tgl_kembali', '>=', $today);
            } else {
                $query->where('status', $status);
            }
        }

        $loan  = $query->orderBy('created_at', 'desc')->paginate(10);
        $codes = peminjaman::select('employee_id')->distinct()->get();
        $sess  = Session::all();

        return view('peminjaman.getData', compact('loan', 'codes', 'sess'));
    }
}