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

class PeminjamanController extends Controller
{
    // ─── Daftar pilihan Kop Surat ───────────────────────────────────────────────
    private const KOP_OPTIONS = [
        'BTSB'   => 'Balai Teknik Sains Bangunan (BTSB)',
        'SATKER' => 'Satuan Kerja Balai Teknik Sains Bangunan',
    ];

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
        $material   = Materials::where('kondisi', '!=', 'Rusak Berat')->orderBy('Nama Barang')->get();
        $kopOptions = self::KOP_OPTIONS;

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
        $loan       = peminjaman::findOrFail($id);
        $material   = Materials::where('kondisi', '!=', 'Rusak Berat')->get();
        $kopOptions = self::KOP_OPTIONS;

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
            'kop_surat'     => 'required|in:BTSB,SATKER',
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
        $loan        = peminjaman::with('user')->findOrFail($id);   // 'user' = relasi ke petugas
        $materialIds = json_decode($loan->material_id, true) ?? [];
        $materials   = Materials::whereIn('id', $materialIds)->get();
        $petugas     = $loan->user;                                   // objek User petugas
        $count       = $materials->count();

        // ── 2. Tentukan tier layout ────────────────────────────────────────────
        // Tier 1 (≤8)  : normal — font 9pt, baris 0.7cm → 1 halaman
        // Tier 2 (9-15): kompak — font 7.5pt, baris 0.45cm → tetap 1 halaman
        // Tier 3 (>15) : multipage — page break sebelum TTD
        if ($count <= 8) {
            $fontSize     = 9;
            $rowHeight    = 0.7;
            $pageBreakTTD = false;
        } elseif ($count <= 15) {
            $fontSize     = 7.5;
            $rowHeight    = 0.45;
            $pageBreakTTD = false;
        } else {
            $fontSize     = 7.5;
            $rowHeight    = 0.45;
            $pageBreakTTD = true;
        }

        // ── 3. Bootstrap PhpWord ──────────────────────────────────────────────
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);

        $twip = fn(float $cm) => \PhpOffice\PhpWord\Shared\Converter::cmToTwip($cm);

        $section = $phpWord->addSection([
            'marginTop'    => $twip(2),
            'marginBottom' => $twip(2),
            'marginLeft'   => $twip(2.5),
            'marginRight'  => $twip(2.5),
        ]);

        // Lebar konten (twip): A4 lebar 21cm - margin 2.5+2.5 = 16cm = 9072 twip
        $contentWidth = 9072;

        // ── 4. KOP SURAT ──────────────────────────────────────────────────────
        // Garis atas kop
        $section->addText('', [], [
            'borderBottomSize'  => 12,
            'borderBottomColor' => '000000',
            'spacing'           => ['before' => 0, 'after' => 40],
        ]);

        // Tabel kop: [logo | teks kementerian]
        $tblKop = $section->addTable(['unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO]);
        $kopRow = $tblKop->addRow($twip(2.8));

        // Kolom logo (ganti dengan ImageRun jika ada file logo)
        $kopRow->addCell($twip(2.5), ['valign' => 'center'])
               ->addText('[LOGO]', ['size' => 9, 'bold' => true]);

        // Kolom teks kop
        $kopCell = $kopRow->addCell($twip(13.5), ['valign' => 'center']);

        $kopCell->addText(
            'KEMENTERIAN PEKERJAAN UMUM',
            ['bold' => true, 'size' => 11, 'alignment' => 'center'],
            ['alignment' => 'center', 'spacing' => ['after' => 0]]
        );
        $kopCell->addText(
            'DIREKTORAT JENDERAL CIPTA KARYA',
            ['bold' => true, 'size' => 11, 'alignment' => 'center'],
            ['alignment' => 'center', 'spacing' => ['after' => 0]]
        );
        $kopCell->addText(
            'DIREKTORAT BINA TEKNIK BANGUNAN GEDUNG DAN PENYEHATAN LINGKUNGAN',
            ['size' => 9, 'alignment' => 'center'],
            ['alignment' => 'center', 'spacing' => ['after' => 0]]
        );

        // Baris terakhir kop berbeda antara BTSB dan SATKER
        if ($loan->kop_surat === 'SATKER') {
            $kopCell->addText(
                'SATUAN KERJA BALAI TEKNIK SAINS BANGUNAN',
                ['bold' => true, 'size' => 11, 'alignment' => 'center'],
                ['alignment' => 'center', 'spacing' => ['after' => 0]]
            );
        } else {
            // Default: BTSB
            $kopCell->addText(
                'BALAI TEKNIK SAINS BANGUNAN',
                ['bold' => true, 'size' => 12, 'alignment' => 'center'],
                ['alignment' => 'center', 'spacing' => ['after' => 0]]
            );
        }

        $kopCell->addText(
            'Jalan Panyaungan, Cileunyi Wetan – Kab. Bandung  |  ditbtpp.bsb@pu.go.id',
            ['size' => 8],
            ['alignment' => 'center', 'spacing' => ['after' => 0]]
        );

        // Garis bawah kop (double)
        $section->addText('', [], [
            'borderTopSize'    => 12,
            'borderTopColor'   => '000000',
            'borderBottomSize' => 4,
            'borderBottomColor'=> '000000',
            'spacing'          => ['before' => 40, 'after' => 120],
        ]);

        // ── 5. JUDUL SURAT ────────────────────────────────────────────────────
        $section->addText(
            'SURAT PEMINJAMAN BARANG MILIK NEGARA',
            ['bold' => true, 'size' => 12],
            ['alignment' => 'center', 'spacing' => ['after' => 80]]
        );

        // ── 6. NOMOR ──────────────────────────────────────────────────────────
        $run = $section->addTextRun(['spacing' => ['after' => 120]]);
        $run->addText('NOMOR: ', ['bold' => true, 'size' => 11]);
        $run->addText($loan->code, ['size' => 11]);

        // ── 7. DATA PEMINJAM ──────────────────────────────────────────────────
        $section->addText(
            'Yang bertanda tangan di bawah ini:',
            ['size' => 11],
            ['spacing' => ['after' => 80]]
        );

        $tblPeminjam = $section->addTable(['unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO]);
        $rowsData = [
            ['Nama',    $loan->peminjam],
            ['NIP/ID',  $petugas->nip      ?? '-'],
            ['Jabatan', $petugas->jabatan  ?? '-'],
            ['Bagian',  $petugas->bagian   ?? '-'],
        ];
        foreach ($rowsData as $r) {
            $tr = $tblPeminjam->addRow($twip(0.6));
            $tr->addCell($twip(3))->addText($r[0], ['size' => 11]);
            $tr->addCell($twip(0.4))->addText(':', ['size' => 11]);
            $tr->addCell($twip(12))->addText($r[1], ['size' => 11]);
        }

        // ── 8. INTRO TABEL BARANG ─────────────────────────────────────────────
        $section->addText(
            'Mengajukan peminjaman alat sebagai berikut:',
            ['size' => 11],
            ['spacing' => ['before' => 120, 'after' => 80]]
        );

        // ── 9. TABEL BARANG (tier-aware) ──────────────────────────────────────
        // Lebar kolom: No | Jenis BMN | Nama Barang | Kode Barang | NUP | Kondisi
        // Total harus = $contentWidth = 9072
        $colWidths = [500, 1800, 2772, 1800, 800, 1400];   // total = 9072

        $tblBarang = $section->addTable([
            'unit'        => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO,
            'borderSize'  => 6,
            'borderColor' => '000000',
        ]);

        // Header baris
        $hRow = $tblBarang->addRow($twip(0.7));
        $headers = ['No', 'Jenis BMN', 'Nama Barang', 'Kode Barang', 'NUP', 'Kondisi'];
        foreach (array_map(null, $colWidths, $headers) as [$w, $lbl]) {
            $hRow->addCell($w, ['bgColor' => 'D9E1F2', 'valign' => 'center'])
                 ->addText($lbl, ['bold' => true, 'size' => $fontSize], ['alignment' => 'center']);
        }

        // Baris data barang
        foreach ($materials as $i => $item) {
            $dRow = $tblBarang->addRow($twip($rowHeight));
            $values = [
                $i + 1,
                $item->{'Jenis BMN'}   ?? ($item->jenis_bmn   ?? 'MESIN PERALATAN NON TIK'),
                $item->{'Nama Barang'} ?? ($item->nama_barang ?? '-'),
                $item->{'Kode Barang'} ?? ($item->kode_barang ?? '-'),
                $item->nup             ?? '-',
                $item->kondisi         ?? 'Baik',
            ];
            foreach (array_map(null, $colWidths, $values) as [$w, $val]) {
                $dRow->addCell($w, ['valign' => 'center'])
                     ->addText((string) $val, ['size' => $fontSize]);
            }
        }

        // ── 10. TANGGAL PINJAM / KEMBALI ──────────────────────────────────────
        $section->addText('', [], ['spacing' => ['before' => 80, 'after' => 0]]);

        $tblTgl = $section->addTable([
            'unit'       => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO,
            'borderSize' => 6,
            'borderColor'=> '000000',
        ]);
        $tglH = $tblTgl->addRow($twip(0.7));
        $tglH->addCell($twip(8), ['bgColor' => 'D9E1F2'])
             ->addText('Tanggal Pinjam',  ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        $tglH->addCell($twip(8), ['bgColor' => 'D9E1F2'])
             ->addText('Tanggal Kembali', ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        $tglD = $tblTgl->addRow($twip(0.7));
        $tglD->addCell($twip(8))
             ->addText(
                 Carbon::parse($loan->tgl_pinjam)->isoFormat('D MMMM YYYY'),
                 ['size' => 10],
                 ['alignment' => 'center']
             );
        $tglD->addCell($twip(8))
             ->addText(
                 Carbon::parse($loan->tgl_kembali)->isoFormat('D MMMM YYYY'),
                 ['size' => 10],
                 ['alignment' => 'center']
             );

        // ── 11. PERNYATAAN ────────────────────────────────────────────────────
        $section->addText(
            'Saya menyatakan bahwa:',
            ['size' => 11],
            ['spacing' => ['before' => 120, 'after' => 60]]
        );

        $pernyataan = [
            'Benar mengajukan peminjaman alat sebagaimana tercantum pada tabel di atas.',
            'Bersedia menjaga dan mengembalikan alat dalam kondisi baik atau sesuai kondisi awal.',
            'Bertanggung jawab apabila terjadi kerusakan atau kehilangan selama masa pinjam.',
            'Siap mengikuti ketentuan yang berlaku dalam peminjaman alat.',
        ];
        foreach ($pernyataan as $no => $txt) {
            $pr = $section->addTextRun([
                'indentation' => ['left' => 360, 'hanging' => 360],
                'spacing'     => ['before' => 0, 'after' => 40],
            ]);
            $pr->addText(($no + 1) . '.  ', ['size' => 11]);
            $pr->addText($txt,              ['size' => 11]);
        }

        // ── 12. TANDA TANGAN ──────────────────────────────────────────────────
        if ($pageBreakTTD) {
            // Tier 3: TTD di halaman baru → selalu rapi
            $section->addPageBreak();
            $section->addParagraph();
        } else {
            // Tier 1 & 2: spasi biasa
            $section->addText('', [], ['spacing' => ['before' => 160, 'after' => 0]]);
        }

        $tblTTD = $section->addTable(['unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_AUTO]);

        // Baris label
        $r1 = $tblTTD->addRow($twip(0.6));
        $r1->addCell($twip(6))->addText('Peminjam,',       ['size' => 11]);
        $r1->addCell($twip(3))->addText('',                ['size' => 11]);
        $r1->addCell($twip(6))->addText('Petugas Gudang,', ['size' => 11]);

        // Ruang tanda tangan (4 baris kosong)
        for ($x = 0; $x < 4; $x++) {
            $rx = $tblTTD->addRow($twip(0.8));
            $rx->addCell($twip(6))->addText('', ['size' => 11]);
            $rx->addCell($twip(3))->addText('', ['size' => 11]);
            $rx->addCell($twip(6))->addText('', ['size' => 11]);
        }

        // Nama
        $r2 = $tblTTD->addRow($twip(0.6));
        $r2->addCell($twip(6))
           ->addText('( ' . $loan->peminjam . ' )', ['size' => 11], ['alignment' => 'center']);
        $r2->addCell($twip(3))->addText('', ['size' => 11]);
        $r2->addCell($twip(6))
           ->addText('( ' . ($petugas->name ?? '-') . ' )', ['size' => 11], ['alignment' => 'center']);

        // ── 13. SIMPAN & DOWNLOAD ─────────────────────────────────────────────
        $filename = 'Surat_Peminjaman_' . $loan->code . '.docx';
        $tempDir  = storage_path('app/temp');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tmpPath = $tempDir . '/' . $filename;

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