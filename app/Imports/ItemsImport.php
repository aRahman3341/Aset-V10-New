<?php

namespace App\Imports;

use App\Models\Items;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ItemsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    /**
     * Kolom di file Excel (heading row):
     *   kode_barang | nama_barang | kategori | satuan | saldo | status
     *
     * Heading row diubah otomatis ke snake_case lowercase oleh WithHeadingRow.
     */
    public function model(array $row)
    {
        // Lewati baris kosong
        if (empty($row['kode_barang']) && empty($row['nama_barang'])) {
            return null;
        }

        // Normalisasi status
        $status = 0;
        $rawStatus = strtolower(trim($row['status'] ?? ''));
        if (in_array($rawStatus, ['1', 'teregister', 'ya', 'yes', 'true'])) {
            $status = 1;
        }

        // Normalisasi kategori
        $categories = trim($row['kategori'] ?? '');
        $catMap = [
            'atk'           => 'ATK',
            'rumah tangga'  => 'Rumah Tangga',
            'rt'            => 'Rumah Tangga',
            'laboratorium'  => 'Laboratorium',
            'lab'           => 'Laboratorium',
        ];
        $categories = $catMap[strtolower($categories)] ?? $categories;

        return new Items([
            'code'       => trim($row['kode_barang'] ?? ''),
            'name'       => trim($row['nama_barang'] ?? ''),
            'categories' => $categories,
            'satuan'     => trim($row['satuan'] ?? ''),
            'saldo'      => is_numeric($row['saldo'] ?? '') ? (int) $row['saldo'] : 0,
            'status'     => $status,
        ]);
    }
}