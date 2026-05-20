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
        $users    = User::whereIn('jabatan', ['Admin', 'Manager', 'Operator', 'admin', 'manager', 'operator'])
                        ->orderBy('name')->get();
        $material = Materials::where('kondisi', '!=', 'Rusak Berat')->orderBy('Nama Barang')->get();
        $kopOptions = self::KOP_OPTIONS;          // ← kirim ke view

        return view('peminjaman.add', compact('material', 'users', 'kopOptions'));
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
            'kop_surat'     => 'required|in:BTSB,SATKER',   // ← validasi
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
            'kop_surat'   => $request->kop_surat,    // ← simpan pilihan kop
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
        $loan       = peminjaman::findOrFail($id);
        $material   = Materials::where('kondisi', '!=', 'Rusak Berat')->get();
        $kopOptions = self::KOP_OPTIONS;           // ← kirim ke view

        return view('peminjaman.edit', compact('loan', 'material', 'kopOptions'));
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
            'kop_surat'     => 'required|in:BTSB,SATKER',   // ← validasi
        ]);

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
            'kop_surat'   => $request->kop_surat,    // ← simpan pilihan kop
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
    $loan      = Peminjaman::with('materials', 'employee')->findOrFail($id);
    $materials = $loan->materials;           // Collection
    $count     = $materials->count();
 
    // ── Tentukan tier ─────────────────────────────────────
    if ($count <= 8) {
        $tier         = 'normal';
        $fontSize     = 9;          // pt
        $rowHeight    = 0.7;        // cm
        $pageBreakTTD = false;
    } elseif ($count <= 15) {
        $tier         = 'compact';
        $fontSize     = 7.5;
        $rowHeight    = 0.5;
        $pageBreakTTD = false;
    } else {
        $tier         = 'multipage';
        $fontSize     = 7.5;
        $rowHeight    = 0.5;
        $pageBreakTTD = true;
    }
 
    // ── Bootstrap PhpWord ─────────────────────────────────
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $phpWord->setDefaultFontName('Times New Roman');
    $phpWord->setDefaultFontSize(11);
 
    $section = $phpWord->addSection([
        'marginTop'    => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
        'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
        'marginLeft'   => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.5),
        'marginRight'  => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.5),
    ]);
 
    // ── Helper closure: buat cell tabel ───────────────────
    $cell = fn(array $texts, array $opt = []) => $texts; // lihat penggunaan di bawah
 
    // ────────────────────────────────────────────────────────
    //  A. KOP SURAT  (sesuaikan dengan logika kop_surat Anda)
    // ────────────────────────────────────────────────────────
    // ... (kop surat Anda yang sudah ada) ...
 
    // ────────────────────────────────────────────────────────
    //  B. NOMOR SURAT
    // ────────────────────────────────────────────────────────
    $section->addParagraph(); // spasi
    $run = $section->addTextRun(['alignment' => 'left']);
    $run->addText('NOMOR: ', ['bold' => true, 'size' => 11]);
    $run->addText($loan->code,  ['size' => 11]);
 
    // ────────────────────────────────────────────────────────
    //  C. DATA PEMINJAM  (tabel 3 kolom)
    // ────────────────────────────────────────────────────────
    $section->addParagraph();
    $section->addText('Yang bertanda tangan di bawah ini:', ['size' => 11]);
    $section->addParagraph();
 
    $tblPeminjam = $section->addTable(['unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO]);
    $rowsPeminjam = [
        ['Nama',    ':', $loan->peminjam],
        ['NIP/ID',  ':', $loan->employee->nip ?? '-'],
        ['Jabatan', ':', $loan->employee->jabatan ?? '-'],
        ['Bagian',  ':', $loan->employee->bagian ?? '-'],
    ];
    foreach ($rowsPeminjam as $r) {
        $tr = $tblPeminjam->addRow();
        $tr->addCell(2000)->addText($r[0], ['size' => 11]);
        $tr->addCell(300) ->addText($r[1], ['size' => 11]);
        $tr->addCell(5000)->addText($r[2], ['size' => 11]);
    }
 
    // ────────────────────────────────────────────────────────
    //  D. TABEL BARANG — ukuran menyesuaikan $tier
    // ────────────────────────────────────────────────────────
    $section->addParagraph();
    $section->addText(
        'Mengajukan peminjaman alat sebagai berikut:',
        ['size' => 11]
    );
    $section->addParagraph();
 
    // Lebar kolom (twip)  No | Jenis BMN | Nama Barang | Kode | NUP | Kondisi
    $colWidths = [500, 2000, 2500, 1500, 700, 1000];
 
    $tblBarang = $section->addTable([
        'unit'        => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO,
        'borderSize'  => 6,
        'borderColor' => '000000',
    ]);
 
    // Header
    $headerRow  = $tblBarang->addRow(
        \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.8)
    );
    $headerCols = ['No', 'Jenis BMN', 'Nama Barang', 'Kode Barang', 'NUP', 'Kondisi'];
    foreach (array_map(null, $colWidths, $headerCols) as [$w, $label]) {
        $headerRow->addCell($w, ['bgColor' => 'D9E1F2'])
                  ->addText($label, ['bold' => true, 'size' => $fontSize, 'alignment' => 'center']);
    }
 
    // Baris data
    foreach ($materials as $i => $item) {
        $dataRow = $tblBarang->addRow(
            \PhpOffice\PhpWord\Shared\Converter::cmToTwip($rowHeight)
        );
        $cols = [
            $i + 1,
            $item->jenis_bmn    ?? '-',
            $item->nama_barang  ?? '-',
            $item->kode_barang  ?? '-',
            $item->nup          ?? '-',
            $item->kondisi      ?? 'Baik',
        ];
        foreach (array_map(null, $colWidths, $cols) as [$w, $val]) {
            $dataRow->addCell($w)->addText((string)$val, ['size' => $fontSize]);
        }
    }
 
    // ────────────────────────────────────────────────────────
    //  E. TANGGAL PINJAM / KEMBALI
    // ────────────────────────────────────────────────────────
    $section->addParagraph();
 
    $tblTgl = $section->addTable([
        'unit'       => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO,
        'borderSize' => 6, 'borderColor' => '000000',
    ]);
    $thRow = $tblTgl->addRow();
    $thRow->addCell(3500, ['bgColor' => 'D9E1F2'])
          ->addText('Tanggal Pinjam',  ['bold' => true, 'size' => 10, 'alignment' => 'center']);
    $thRow->addCell(3500, ['bgColor' => 'D9E1F2'])
          ->addText('Tanggal Kembali', ['bold' => true, 'size' => 10, 'alignment' => 'center']);
    $tdRow = $tblTgl->addRow();
    $tdRow->addCell(3500)->addText(
        \Carbon\Carbon::parse($loan->tgl_pinjam)->isoFormat('D MMMM YYYY'),
        ['size' => 10, 'alignment' => 'center']
    );
    $tdRow->addCell(3500)->addText(
        \Carbon\Carbon::parse($loan->tgl_kembali)->isoFormat('D MMMM YYYY'),
        ['size' => 10, 'alignment' => 'center']
    );
 
    // ────────────────────────────────────────────────────────
    //  F. PERNYATAAN
    // ────────────────────────────────────────────────────────
    $section->addParagraph();
    $section->addText('Saya menyatakan bahwa:', ['size' => 11]);
 
    $pernyataan = [
        'Benar mengajukan peminjaman alat sebagaimana tercantum pada tabel di atas.',
        'Bersedia menjaga dan mengembalikan alat dalam kondisi baik atau sesuai kondisi awal.',
        'Bertanggung jawab apabila terjadi kerusakan atau kehilangan selama masa pinjam.',
        'Siap mengikuti ketentuan yang berlaku dalam peminjaman alat.',
    ];
    foreach ($pernyataan as $no => $txt) {
        $pr = $section->addTextRun(['indentation' => ['left' => 360]]);
        $pr->addText(($no + 1) . '.  ', ['size' => 11]);
        $pr->addText($txt,             ['size' => 11]);
    }
 
    // ────────────────────────────────────────────────────────
    //  G. TTD — page break jika TIER 3
    // ────────────────────────────────────────────────────────
    if ($pageBreakTTD) {
        // Tier 3: TTD selalu mulai di halaman baru → rapi & profesional
        $section->addPageBreak();
    } else {
        $section->addParagraph(); // spasi normal
    }
 
    $section->addParagraph();
 
    // Tabel TTD (Peminjam kiri | kosong tengah | Petugas Gudang kanan)
    $tblTTD = $section->addTable(['unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO]);
    $ttdRow = $tblTTD->addRow();
 
    $ttdRow->addCell(3000)->addText('Peminjam,',       ['size' => 11]);
    $ttdRow->addCell(1200)->addText('',                ['size' => 11]);   // spacer
    $ttdRow->addCell(3000)->addText('Petugas Gudang,', ['size' => 11]);
 
    // Baris kosong (ruang tanda tangan) × 4
    foreach (range(1, 4) as $_) {
        $tr = $tblTTD->addRow(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.8));
        $tr->addCell(3000)->addText('', ['size' => 11]);
        $tr->addCell(1200)->addText('', ['size' => 11]);
        $tr->addCell(3000)->addText('', ['size' => 11]);
    }
 
    // Nama di bawah TTD
    $nameRow = $tblTTD->addRow();
    $nameRow->addCell(3000)->addText(
        '( ' . $loan->peminjam . ' )',
        ['size' => 11, 'alignment' => 'center']
    );
    $nameRow->addCell(1200)->addText('', ['size' => 11]);
    $nameRow->addCell(3000)->addText(
        '( ' . ($loan->employee->name ?? '-') . ' )',
        ['size' => 11, 'alignment' => 'center']
    );
 
    // ────────────────────────────────────────────────────────
    //  H. SIMPAN & DOWNLOAD
    // ────────────────────────────────────────────────────────
    $filename = 'Surat_Peminjaman_' . $loan->code . '.docx';
    $tmpPath  = storage_path('app/temp/' . $filename);
 
    if (!file_exists(storage_path('app/temp'))) {
        mkdir(storage_path('app/temp'), 0755, true);
    }
 
    $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($tmpPath);
 
    return response()->download($tmpPath, $filename)->deleteFileAfterSend(true);
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
        $query   = peminjaman::query()->with(['user']);
        $today   = \Carbon\Carbon::today();

        $employe = $request->input('code');
        if ($employe && $employe !== 'all') {
            $query->where('employee_id', $employe);
        }

        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        if ($start && $end) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($start)->startOfDay(),
                \Carbon\Carbon::parse($end)->endOfDay(),
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