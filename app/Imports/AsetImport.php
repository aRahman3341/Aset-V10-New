<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * AsetImport
 *
 * Template Excel yang diimport adalah HASIL EXPORT — format kolom:
 * Row 1 : Grup header (diabaikan)
 * Row 2 : Header kolom detail → dijadikan key
 * Row 3+ : Data
 *
 * Urutan kolom export (A–R):
 * A=No, B=Kode Barang, C=NUP, D=Nama Barang, E=Merk, F=Tipe,
 * G=Jenis BMN, H=Kondisi, I=Status BMN,
 * J=Nilai Perolehan Pertama, K=Nilai Perolehan, L=Nilai Penyusutan, M=Nilai Buku,
 * N=Tgl Perolehan, O=Tgl Buku Pertama,
 * P=No PSP, Q=Tgl PSP, R=Jumlah Foto
 */
class AsetImport implements ToCollection, WithStartRow, SkipsEmptyRows
{
    // Mulai baca dari baris 2 (baris 1 = grup header, baris 2 = header kolom)
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        // Baris pertama yang dibaca (row 2 di Excel) = header kolom
        $headers = null;

        foreach ($rows as $index => $row) {
            // Baris pertama = header
            if ($index === 0) {
                $headers = $row->toArray();
                continue;
            }

            // Skip baris kosong
            if ($row->filter()->isEmpty()) continue;

            // Map kolom berdasarkan header
            $data = [];
            foreach ($headers as $i => $header) {
                $data[trim((string)$header)] = $row[$i] ?? null;
            }

            // Helper: ambil nilai dari data
            $get = function(string $key) use ($data) {
                $val = $data[$key] ?? null;
                if ($val === '' || $val === '-') return null;
                return $val;
            };

            // Helper: parse tanggal (format d/m/Y dari export atau Y-m-d)
            $tgl = function($val) {
                if (!$val) return null;
                try {
                    if (is_numeric($val)) {
                        return Carbon::instance(
                            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)
                        )->format('Y-m-d');
                    }
                    // Format d/m/Y (dari export)
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $val)) {
                        return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d');
                    }
                    return Carbon::parse($val)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };

            // Helper: parse angka (bisa ada format ribuan)
            $num = function($val) {
                if ($val === null || $val === '' || $val === '-') return null;
                $clean = str_replace(['.', ',', ' '], ['', '.', ''], (string)$val);
                return is_numeric($clean) ? (float)$clean : null;
            };

            // Skip jika tidak ada kode dan nama
            $kode = $get('Kode Barang');
            $nama = $get('Nama Barang');
            if (!$kode && !$nama) continue;

            // Cek apakah sudah ada (update) atau baru (insert)
            $existing = DB::table('materials')
                ->where('Kode Barang', $kode)
                ->where('nup', $get('NUP'))
                ->first();

            $payload = [
                // ── Identitas ──
                'Kode Barang'              => $kode,
                'nup'                      => $get('NUP')              ?? '1',
                'Nama Barang'              => $nama,
                'merk'                     => $get('Merk'),
                'tipe'                     => $get('Tipe'),

                // ── Klasifikasi ──
                'Jenis BMN'                => $get('Jenis BMN'),
                'kondisi'                  => $get('Kondisi')          ?? 'Baik',
                'Status BMN'               => $get('Status BMN')       ?? 'Aktif',

                // ── Nilai ──
                'Nilai Perolehan Pertama'  => $num($get('Nilai Perolehan Pertama (Rp)')),
                'Nilai Perolehan'          => $num($get('Nilai Perolehan (Rp)')),
                'Nilai Penyusutan'         => $num($get('Nilai Penyusutan (Rp)')),
                'Nilai Buku'               => $num($get('Nilai Buku (Rp)')),

                // ── Tanggal ──
                'Tanggal Perolehan'        => $tgl($get('Tgl Perolehan')),
                'Tanggal Buku Pertama'     => $tgl($get('Tgl Buku Pertama')),

                // ── Dokumen PSP ──
                'No PSP'                   => $get('No PSP'),
                'Tanggal PSP'              => $tgl($get('Tgl PSP')),

                // ── Foto ──
                'Jumlah Foto'              => (int)($get('Jumlah Foto') ?? 0),

                'updated_at'               => now(),
            ];

            if ($existing) {
                // Update baris yang sudah ada
                DB::table('materials')
                    ->where('id', $existing->id)
                    ->update($payload);
            } else {
                // Insert baris baru
                $payload['created_at'] = now();
                DB::table('materials')->insert($payload);
            }
        }
    }
}