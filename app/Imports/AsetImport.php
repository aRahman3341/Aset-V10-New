<?php

namespace App\Imports;

use App\Models\Materials;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Carbon\Carbon;

class AsetImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnError
{
    use SkipsErrors;

    /**
     * Baris pertama dianggap header (WithHeadingRow).
     * Nama kolom di Excel harus cocok (case-insensitive, spasi → underscore).
     *
     * Kolom minimal yang harus ada di file Excel:
     * code / kode_barang, nup, name / nama_barang
     */
    public function model(array $row)
    {
        // Helper: ambil nilai dari beberapa kemungkinan nama kolom
        $get = function(array $keys) use ($row) {
            foreach ($keys as $key) {
                $k = strtolower(str_replace([' ', '/', '-'], '_', $key));
                if (isset($row[$k]) && $row[$k] !== '' && $row[$k] !== null) {
                    return $row[$k];
                }
            }
            return null;
        };

        // Helper: parse tanggal dengan aman
        $date = function($val) {
            if (!$val) return null;
            try {
                // Excel numeric date
                if (is_numeric($val)) {
                    return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val))->format('Y-m-d');
                }
                return Carbon::parse($val)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        // Helper: parse angka
        $num = function($val) {
            if ($val === null || $val === '' || $val === '-') return null;
            return is_numeric(str_replace([',', '.'], '', $val)) 
                   ? (float) str_replace(',', '', $val) 
                   : null;
        };

        // ── Cari location ID dari gedung/lantai/ruangan ──────────────
        $gedung  = $get(['gedung', 'office', 'lokasi_gedung']);
        $lantai  = $get(['lantai', 'floor', 'lokasi_lantai']);
        $ruangan = $get(['ruangan', 'room', 'lokasi_ruangan']);

        $locationId = null;
        if ($gedung) {
            $locQuery = DB::table('locations')->where('office', $gedung);
            if ($lantai)  $locQuery->where('floor', $lantai);
            if ($ruangan) $locQuery->where('room', $ruangan);
            $locationId = $locQuery->value('id');
        }

        // ── Cari category ID ─────────────────────────────────────────
        $categoryName = $get(['kategori', 'category', 'nama_kategori']);
        $categoryId   = null;
        if ($categoryName) {
            $categoryId = DB::table('categories')
                ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
                ->value('id');
        }

        // ── Cari supervisor ID ───────────────────────────────────────
        $supervisorName = $get(['penanggung_jawab', 'supervisor', 'nama_penanggung_jawab']);
        $supervisorId   = null;
        if ($supervisorName) {
            $supervisorId = DB::table('employees')
                ->whereRaw('LOWER(name) = ?', [strtolower($supervisorName)])
                ->value('id');
        }

        // ── Map status ke enum yang valid ────────────────────────────
        $statusRaw = $get(['status', 'status_aset']);
        $statusMap = [
            'aktif'        => 'Dipakai',
            'dipakai'      => 'Dipakai',
            'tidak dipakai'=> 'Tidak Dipakai',
            'tidak_dipakai'=> 'Tidak Dipakai',
            'maintenance'  => 'Maintenance',
            'diserahkan'   => 'Diserahkan',
        ];
        $status = $statusMap[strtolower($statusRaw ?? '')] ?? 'Tidak Dipakai';

        // ── Map condition ────────────────────────────────────────────
        $condRaw = $get(['kondisi', 'condition', 'kondisi_bmn']);
        $condMap = [
            'baik'         => 'Baik',
            'rusak ringan' => 'Rusak Ringan',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak berat'  => 'Rusak',
            'rusak'        => 'Rusak',
        ];
        $condition = $condMap[strtolower($condRaw ?? '')] ?? 'Baik';

        // ── Map type ─────────────────────────────────────────────────
        $typeRaw = $get(['tipe_aset', 'type', 'tipe']);
        $type    = (strtolower($typeRaw ?? '') === 'bergerak') ? 'Bergerak' : 'Tetap';

        // ── Kode & NUP — wajib ada ───────────────────────────────────
        $code = $get(['kode_barang', 'code', 'kode', 'no_barang']);
        $nup  = $get(['nup', 'no_urut_pendaftaran']);
        $name = $get(['nama_barang', 'name', 'nama']);

        // Skip baris kalau tidak ada kode atau nama
        if (!$code && !$name) return null;

        return new Materials([
            // ── Identitas ──────────────────────────────────────────
            'code'         => (string) ($code ?? '-'),
            'nup'          => (string) ($nup  ?? '1'),
            'name'         => (string) ($name ?? '-'),
            'name_fix'     => $get(['nama_fix', 'merk', 'uraian_barang', 'nama_fix___merk']),
            'no_seri'      => $get(['no_seri', 'serial_number', 'no__seri']),

            // ── Klasifikasi ────────────────────────────────────────
            'category'     => $categoryId,
            'condition'    => $condition,
            'status'       => $status,
            'type'         => $type,
            'jenis_bmn'    => $get(['jenis_bmn', 'jenis']),
            'status_bmn'   => $get(['status_bmn']),
            'intra_extra'  => $get(['intra_extra', 'intra___extra']),
            'bulan'        => date('n'),
            'registered'   => 'tidak',

            // ── Nilai ──────────────────────────────────────────────
            'nilai'              => $num($get(['nilai', 'nilai_perolehan_rp_', 'nilai_perolehan'])),
            'nilai_perolehan'    => $num($get(['nilai_perolehan', 'nilai_perolehan_rp_'])),
            'nilai_penyusutan'   => $num($get(['nilai_penyusutan', 'nilai_penyusutan_rp_'])),
            'nilai_buku'         => $num($get(['nilai_buku', 'nilai_buku_rp_'])),

            // ── Waktu ──────────────────────────────────────────────
            'years'                => (int) ($get(['tahun', 'years', 'tahun_perolehan']) ?? date('Y')),
            'tanggal_perolehan'    => $date($get(['tgl_perolehan', 'tanggal_perolehan'])),
            'tanggal_buku_pertama' => $date($get(['tgl_buku_pertama', 'tanggal_buku_pertama'])),
            'tanggal_pengapusan'   => $date($get(['tgl_pengapusan', 'tanggal_pengapusan'])),

            // ── Fisik ──────────────────────────────────────────────
            'quantity'       => (int) ($get(['qty', 'quantity', 'jumlah']) ?? 1),
            'satuan'         => $get(['satuan', 'unit']),
            'umur_aset'      => (int) ($get(['umur_thn_', 'umur_aset', 'umur']) ?? 0) ?: null,
            'specification'  => $get(['spesifikasi', 'specification', 'spec']),
            'description'    => $get(['deskripsi___keterangan', 'description', 'keterangan', 'deskripsi']),

            // ── Lokasi Fisik ───────────────────────────────────────
            'store_location' => $locationId,

            // ── Lokasi BMN ─────────────────────────────────────────
            'kode_satker'    => $get(['kode_satker']),
            'nama_satker'    => $get(['nama_satker']),
            'kode_register'  => $get(['kode_register']),
            'nama_kl'        => $get(['nama_k_l', 'nama_kl']),
            'nama_e1'        => $get(['nama_unit_e1_', 'nama_e1']),
            'alamat'         => $get(['alamat']),
            'kab_kota'       => $get(['kab_kota']),
            'provinsi'       => $get(['provinsi']),

            // ── Dokumen BMN ────────────────────────────────────────
            'no_polisi'          => $get(['no_polisi']),
            'status_sertifikasi' => $get(['status_sertifikasi']),
            'no_psp'             => $get(['no_psp']),
            'tanggal_psp'        => $date($get(['tgl_psp', 'tanggal_psp'])),
            'status_penggunaan'  => $get(['status_penggunaan']),
            'no_stnk'            => $get(['no_stnk']),
            'nama_pengguna'      => $get(['nama_pengguna']),

            // ── Kalibrasi ──────────────────────────────────────────
            'dikalibrasi'        => strtolower($get(['perlu_kalibrasi', 'dikalibrasi']) ?? '') === 'perlu' ? 1 : 0,
            'last_kalibrasi'     => $date($get(['kalibrasi_terakhir', 'last_kalibrasi'])),
            'schadule_kalibrasi' => $date($get(['jadwal_kalibrasi', 'schadule_kalibrasi'])),
            'kalibrasi_by'       => $get(['dikalibrasi_oleh', 'kalibrasi_by']),

            // ── Penanggung Jawab ───────────────────────────────────
            'supervisor'     => $supervisorId ?? $supervisorName,
        ]);
    }
}