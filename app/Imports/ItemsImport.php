<?php

namespace App\Imports;

use App\Models\Items;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ItemsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    /**
     * WithHeadingRow membaca baris pertama sebagai key.
     * Key dikonversi otomatis: spasi→underscore, huruf kecil semua.
     *
     * Heading di template: kode_barang | nama_barang | kategori | satuan | saldo | status
     * Key yang terbaca   : kode_barang | nama_barang | kategori | satuan | saldo | status
     */
    public function headingRow(): int
    {
        // Heading ada di baris 3 template (baris 1=judul, baris 2=instruksi)
        return 3;
    }

    public function model(array $row)
    {
        // Debug: uncomment baris ini jika masih ada masalah
        // \Log::info('Import row:', $row);

        // Lewati baris yang semua kolomnya kosong
        $kode = trim($row['kode_barang'] ?? $row['kode barang'] ?? '');
        $nama = trim($row['nama_barang'] ?? $row['nama barang'] ?? '');

        if (empty($kode) && empty($nama)) {
            return null;
        }

        // Normalisasi kategori (toleran terhadap variasi penulisan)
        $rawKat = strtolower(trim($row['kategori'] ?? ''));
        $catMap = [
            'atk'          => 'ATK',
            'rumah tangga' => 'Rumah Tangga',
            'rumahtangga'  => 'Rumah Tangga',
            'rt'           => 'Rumah Tangga',
            'laboratorium' => 'Laboratorium',
            'lab'          => 'Laboratorium',
        ];
        $categories = $catMap[$rawKat] ?? ucwords($rawKat);

        // Normalisasi status
        $rawStatus = strtolower(trim($row['status'] ?? ''));
        $status    = in_array($rawStatus, ['1', 'teregister', 'ya', 'yes', 'true', 'aktif']) ? 1 : 0;

        // Normalisasi saldo
        $saldo = preg_replace('/[^0-9]/', '', (string)($row['saldo'] ?? '0'));
        $saldo = (int)($saldo ?: 0);

        return new Items([
            'code'       => $kode,
            'name'       => $nama,
            'categories' => $categories,
            'satuan'     => trim($row['satuan'] ?? ''),
            'saldo'      => $saldo,
            'status'     => $status,
        ]);
    }
}