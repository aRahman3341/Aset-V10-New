<?php

namespace App\Http\Controllers;

use App\Exports\AsetOutExport;
use App\Models\Ajuan;
use App\Models\AsetOut;
use App\Models\employee;
use App\Models\Items;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class AsetOutController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX — tampilkan daftar barang keluar
    // ══════════════════════════════════════════
    public function get_data(Request $request)
    {
        $sess  = Session::all();
        $query = $request->input('query', '');

        $asetout = AsetOut::where('no_faktur', 'LIKE', "%{$query}%")
            ->orWhere('no_nd', 'LIKE', "%{$query}%")
            ->orWhere('mak',   'LIKE', "%{$query}%")
            ->with('ajuan')
            ->get();

        return view('asetout.getData', compact('asetout', 'sess'));
    }

    // ══════════════════════════════════════════
    //  LIST — daftar detail per faktur
    // ══════════════════════════════════════════
    public function getList(Request $request)
    {
        $query   = $request->input('query', '');
        $asetout = AsetOut::where('no_faktur', 'LIKE', "%{$query}%")->paginate(20);
        $faktur  = AsetOut::select('no_faktur')->distinct()->get();

        return view('asetout.listData', compact('asetout', 'faktur'));
    }

    // ══════════════════════════════════════════
    //  FILTER
    // ══════════════════════════════════════════
    public function filter(Request $request)
    {
        $tabel     = AsetOut::query();
        $no_faktur = $request->input('no_faktur');

        if ($no_faktur && $no_faktur !== 'all') {
            $tabel->where('no_faktur', $no_faktur);
        }

        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        if ($start && $end) {
            $tabel->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay(),
            ]);
        }

        $asetout = $tabel->paginate(20);
        $faktur  = AsetOut::select('no_faktur')->distinct()->get();
        $sess    = Session::all();

        return view('asetout.getData', compact('asetout', 'faktur', 'sess'));
    }

    // ══════════════════════════════════════════
    //  ADD FORM
    // ══════════════════════════════════════════
    public function addData()
    {
        $sess       = Session::all();
        $employe    = employee::all();
        $itemshabis = Items::all();

        return view('asetout.add', [
            'employe'   => $employe,
            'itemhabis' => $itemshabis,
            'sess'      => $sess,
        ]);
    }

    // ══════════════════════════════════════════
    //  STORE — simpan barang keluar baru
    // ══════════════════════════════════════════
    public function dataStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mak'   => 'required',
            'no_nd' => 'required',
            'name.*'=> 'required',
            'qty.*' => 'required|numeric',
        ], [
            'mak.required'          => 'MAK harus diisi',
            'no_nd.required'        => 'Nomor Nota Dinas harus diisi',
            'name.*.required'       => 'Nama barang harus dipilih',
            'qty.*.required'        => 'Qty harus diisi',
            'qty.*.numeric'         => 'Qty harus berupa angka',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $noFaktur = str_replace('/', '^^', $request->input('no_faktur', ''));
            $noND     = str_replace('/', '^^', $request->input('no_nd'));
            $mak      = str_replace('/', '^^', $request->input('mak'));

            $asetOutId = DB::table('aset_outs')->insertGetId([
                'no_faktur'  => $noFaktur,
                'mak'        => $mak,
                'no_nd'      => $noND,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $operator    = Session::all();
            $names       = $request->input('name');
            $qtys        = $request->input('qty');
            $statuses    = $request->input('status', []);
            $dataAjuans  = [];

            foreach ($names as $i => $name) {
                $dataAjuans[] = [
                    'faktur_id'  => $asetOutId,
                    'name'       => $name,
                    'qty'        => $qtys[$i],
                    'total_qty'  => 0,
                    'pengaju'    => $operator['id'] ?? null,
                    'status'     => $statuses[$i] ?? 'Diproses',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            DB::table('ajuans')->insert($dataAjuans);
            DB::commit();

            return redirect('/asetout')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ══════════════════════════════════════════
    //  EDIT FORM (Admin — edit no_faktur)
    // ══════════════════════════════════════════
    public function editData(Request $request, $id)
    {
        $asetout    = AsetOut::findOrFail($id);
        $ajuan      = Ajuan::where('faktur_id', $id)->get();
        $itemshabis = Items::all();

        return view('asetout.update', compact('asetout', 'ajuan', 'itemshabis'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'no_faktur' => 'required',
        ], ['no_faktur.required' => 'No Faktur harus diisi']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $noFaktur = str_replace('/', '^^', $request->input('no_faktur'));

            DB::table('aset_outs')->where('id', $id)->update([
                'no_faktur'  => $noFaktur,
                'updated_at' => Carbon::now(),
            ]);

            // Update item ajuan yang sudah ada
            $names = $request->input('name', []);
            $qtys  = $request->input('qty', []);
            $ids   = $request->input('id', []);

            foreach ($ids as $i => $ajuanId) {
                DB::table('ajuans')->where('id', $ajuanId)->update([
                    'name'       => $names[$i] ?? null,
                    'qty'        => $qtys[$i] ?? 0,
                    'updated_at' => Carbon::now(),
                ]);
            }

            // Tambah item baru jika ada
            $newNames    = $request->input('name1', []);
            $newQtys     = $request->input('qty1', []);
            $operator    = Session::all();
            $newAjuans   = [];

            foreach ($newNames as $i => $name) {
                $newAjuans[] = [
                    'faktur_id'  => $id,
                    'name'       => $name,
                    'qty'        => $newQtys[$i] ?? 0,
                    'total_qty'  => 0,
                    'pengaju'    => $operator['id'] ?? null,
                    'status'     => 'Diproses',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            if (!empty($newAjuans)) {
                DB::table('ajuans')->insert($newAjuans);
            }

            DB::commit();
            return redirect('/asetout')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ══════════════════════════════════════════
    //  EDIT FORM (Operator — edit nota dinas)
    // ══════════════════════════════════════════
    public function editDataND(Request $request, $id)
    {
        $asetout    = AsetOut::findOrFail($id);
        $ajuan      = Ajuan::where('faktur_id', $id)->get();
        $itemshabis = Items::all();

        return view('asetout.updateND', compact('asetout', 'ajuan', 'itemshabis'));
    }

    public function updateND(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'no_nd' => 'required',
            'mak'   => 'required',
            'name.*'=> 'required',
            'qty.*' => 'required|numeric',
        ], [
            'no_nd.required'  => 'Nomor Nota Dinas harus diisi',
            'mak.required'    => 'MAK harus diisi',
            'name.*.required' => 'Nama barang harus dipilih',
            'qty.*.required'  => 'Qty harus diisi',
            'qty.*.numeric'   => 'Qty harus berupa angka',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $mak  = str_replace('/', '^^', $request->input('mak'));
            $noND = str_replace('/', '^^', $request->input('no_nd'));

            DB::table('aset_outs')->where('id', $id)->update([
                'no_nd'      => $noND,
                'mak'        => $mak,
                'updated_at' => Carbon::now(),
            ]);

            // Update item ajuan yang sudah ada
            $names = $request->input('name', []);
            $qtys  = $request->input('qty', []);
            $ids   = $request->input('id', []);

            foreach ($ids as $i => $ajuanId) {
                DB::table('ajuans')->where('id', $ajuanId)->update([
                    'name'       => $names[$i] ?? null,
                    'qty'        => $qtys[$i] ?? 0,
                    'updated_at' => Carbon::now(),
                ]);
            }

            // Tambah item baru
            $newNames  = $request->input('name1', []);
            $newQtys   = $request->input('qty1', []);
            $operator  = Session::all();
            $newAjuans = [];

            foreach ($newNames as $i => $name) {
                $newAjuans[] = [
                    'faktur_id'  => $id,
                    'name'       => $name,
                    'qty'        => $newQtys[$i] ?? 0,
                    'total_qty'  => 0,
                    'pengaju'    => $operator['id'] ?? null,
                    'status'     => 'Diproses',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            if (!empty($newAjuans)) {
                DB::table('ajuans')->insert($newAjuans);
            }

            DB::commit();
            return redirect('/asetout')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ══════════════════════════════════════════
    //  AJUAN — form approval
    // ══════════════════════════════════════════
    public function ajuan($id)
    {
        $asetout    = AsetOut::findOrFail($id);
        $itemshabis = Items::all();

        // Ambil item pertama untuk tampilan ajuan lama
        $ajuanItem = Ajuan::with('item')->where('faktur_id', $id)->first();
        $itemshabisFirst = $ajuanItem ? $ajuanItem->item : null;

        return view('asetout.ajuan', compact('asetout', 'itemshabis', 'itemshabis', 'itemshabisFirst'));
    }

    // ══════════════════════════════════════════
    //  LIST DETAIL per faktur (approval page)
    // ══════════════════════════════════════════
    public function listData($id)
    {
        $sess      = Session::all();
        $barangOut = AsetOut::where('id', $id)->get();
        $data      = Ajuan::with(['item', 'user'])->where('faktur_id', $id)->get();

        return view('asetout.listData', compact('barangOut', 'data', 'sess'));
    }

    // ══════════════════════════════════════════
    //  DESTROY
    // ══════════════════════════════════════════
    public function destroy($id)
    {
        AsetOut::destroy($id);
        return redirect('/asetout')->with('success', 'Data berhasil dihapus.');
    }

    // ══════════════════════════════════════════
    //  REPORT
    // ══════════════════════════════════════════
    public function report()
    {
        $ajuan = Ajuan::with(['item', 'asetOuts'])->paginate(10);
        return view('asetout.report', compact('ajuan'));
    }

    public function exportAll()
    {
        return Excel::download(
            new AsetOutExport,
            'report_barang_keluar_' . Carbon::now()->timestamp . '.xlsx'
        );
    }

    // ══════════════════════════════════════════
    //  CETAK FAKTUR (lampiran pengeluaran)
    // ══════════════════════════════════════════
    public function cetakFaktur($noFaktur)
    {
        $cetak = AsetOut::where('no_faktur', $noFaktur)->firstOrFail();
        $data  = Ajuan::with('item')->where('faktur_id', $cetak->id)->get();

        return view('asetout.lampiran', compact('cetak', 'data'));
    }

    // ══════════════════════════════════════════
    //  CETAK NOTA DINAS
    // ══════════════════════════════════════════
    public function cetakNota($noFaktur)
    {
        $cetak = AsetOut::where('no_faktur', $noFaktur)->firstOrFail();
        $data  = Ajuan::with('item')->where('faktur_id', $cetak->id)->get();

        return view('asetout.lampiranNota', compact('cetak', 'data'));
    }

    // ══════════════════════════════════════════
    //  DOWNLOAD NOTA DINAS (.docx)
    // ══════════════════════════════════════════
    public function download($noND)
    {
        $item    = AsetOut::where('no_nd', $noND)->firstOrFail();
        $ajuan1  = Ajuan::with('item')->where('faktur_id', $item->id)->get();

        $templatePath = public_path('assets/templates/ND.docx');

        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template ND.docx tidak ditemukan di public/assets/templates/');
        }

        $templateProcessor = new TemplateProcessor($templatePath);
        Settings::setOutputEscapingEnabled(false);

        $formattedDate = date('j F Y', strtotime($item->created_at));

        $templateProcessor->setValue('mak',    str_replace('^^', '/', $item->mak));
        $templateProcessor->setValue('no_nd',  str_replace('^^', '/', $item->no_nd));
        $templateProcessor->setValue('date',   $formattedDate);
        $templateProcessor->setValue('dokumentasi', '');

        $rowCount = $ajuan1->count();
        $templateProcessor->cloneRow('no', $rowCount);

        $i = 0;
        foreach ($ajuan1 as $itemBarang) {
            $no = ++$i;
            $templateProcessor->setValue("no#{$no}",     $no);
            $templateProcessor->setValue("name#{$no}",   $itemBarang->item->name ?? '-');
            $templateProcessor->setValue("qty#{$no}",    $itemBarang->qty);
            $templateProcessor->setValue("satuan#{$no}", $itemBarang->item->satuan ?? '-');
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'nota_dinas') . '.docx';
        $templateProcessor->saveAs($tempFilePath);

        return response()->download($tempFilePath, 'Nota_Dinas.docx')->deleteFileAfterSend();
    }
}