<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AsetImport implements ToCollection, WithStartRow, SkipsEmptyRows
{
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $headers = null;

        foreach ($rows as $index => $row) {
            if ($index === 0) {
                $headers = $row->toArray();
                continue;
            }

            if ($row->filter()->isEmpty()) continue;

            $data = [];
            foreach ($headers as $i => $header) {
                $data[trim((string)$header)] = $row[$i] ?? null;
            }

            $get = function(string $key) use ($data) {
                $val = $data[$key] ?? null;
                if ($val === '' || $val === '-') return null;
                return $val;
            };

            $tgl = function($val) {
                if (!$val) return null;
                try {
                    if (is_numeric($val)) {
                        return Carbon::instance(
                            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)
                        )->format('Y-m-d');
                    }
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $val)) {
                        return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d');
                    }
                    return Carbon::parse($val)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };

            $num = function($val) {
                if ($val === null || $val === '' || $val === '-') return null;
                $clean = str_replace(['.', ',', ' '], ['', '.', ''], (string)$val);
                return is_numeric($clean) ? (float)$clean : null;
            };

            $kode = $get('Kode Barang');
            $nama = $get('Nama Barang');
            if (!$kode && !$nama) continue;

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