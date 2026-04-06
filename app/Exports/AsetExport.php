<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class AsetExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected $ids;

    public function __construct(array $ids = [])
    {
        $this->ids = $ids;
    }

    public function title(): string
    {
        return 'Master Aset';
    }

    public function collection()
    {
        // Gunakan DB::table karena nama kolom DB menggunakan spasi
        $query = DB::table('materials');
        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }
        $data = $query->orderBy('id', 'asc')->get();

        return $data->map(function ($row, $index) {
            // Helper tanggal
            $tgl = function($val) {
                if (!$val) return '-';
                try { return Carbon::parse($val)->format('d/m/Y'); }
                catch (\Exception $e) { return '-'; }
            };

            // Ambil kolom DB spasi
            $kode            = $row->{'Kode Barang'}             ?? '-';
            $nama            = $row->{'Nama Barang'}             ?? '-';
            $jenisBmn        = $row->{'Jenis BMN'}               ?? '-';
            $statusBmn       = $row->{'Status BMN'}              ?? '-';
            $nilaiPertama    = $row->{'Nilai Perolehan Pertama'} ?? 0;
            $nilaiPerolehan  = $row->{'Nilai Perolehan'}         ?? 0;
            $nilaiPenyusutan = $row->{'Nilai Penyusutan'}        ?? 0;
            $nilaiBuku       = $row->{'Nilai Buku'}              ?? 0;
            $tglPerolehan    = $row->{'Tanggal Perolehan'}       ?? null;
            $tglBukuPertama  = $row->{'Tanggal Buku Pertama'}    ?? null;
            $noPsp           = $row->{'No PSP'}                  ?? '-';
            $tglPsp          = $row->{'Tanggal PSP'}             ?? null;
            $jumlahFoto      = $row->{'Jumlah Foto'}             ?? 0;

            return [
                $index + 1,                     // No

                // ── Identitas ──
                $kode,                          // Kode Barang
                $row->nup          ?? '-',      // NUP
                $nama,                          // Nama Barang
                $row->merk         ?? '-',      // Merk
                $row->tipe         ?? '-',      // Tipe

                // ── Klasifikasi ──
                $jenisBmn,                      // Jenis BMN
                $row->kondisi      ?? 'Baik',   // Kondisi
                $statusBmn,                     // Status BMN

                // ── Nilai ──
                $nilaiPertama,                  // Nilai Perolehan Pertama
                $nilaiPerolehan,                // Nilai Perolehan
                $nilaiPenyusutan,               // Nilai Penyusutan
                $nilaiBuku,                     // Nilai Buku

                // ── Tanggal ──
                $tgl($tglPerolehan),            // Tgl Perolehan
                $tgl($tglBukuPertama),          // Tgl Buku Pertama

                // ── Dokumen PSP ──
                $noPsp,                         // No PSP
                $tgl($tglPsp),                  // Tgl PSP

                // ── Foto ──
                $jumlahFoto,                    // Jumlah Foto
            ];
        });
    }

    public function headings(): array
    {
        return [
            // Baris 1: Grup header
            [
                'No',
                'IDENTITAS', '', '', '', '',
                'KLASIFIKASI BMN', '', '',
                'NILAI', '', '', '',
                'TANGGAL', '',
                'DOKUMEN PSP', '',
                'FOTO',
            ],
            // Baris 2: Header kolom detail
            [
                'No',
                'Kode Barang',
                'NUP',
                'Nama Barang',
                'Merk',
                'Tipe',
                'Jenis BMN',
                'Kondisi',
                'Status BMN',
                'Nilai Perolehan Pertama (Rp)',
                'Nilai Perolehan (Rp)',
                'Nilai Penyusutan (Rp)',
                'Nilai Buku (Rp)',
                'Tgl Perolehan',
                'Tgl Buku Pertama',
                'No PSP',
                'Tgl PSP',
                'Jumlah Foto',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'R'; // 18 kolom (A–R)

        // ── Merge baris 1 (grup header) ──
        $merges = [
            'A1:A2',   // No
            'B1:G1',   // IDENTITAS
            'H1:J1',   // KLASIFIKASI BMN
            'K1:N1',   // NILAI
            'O1:P1',   // TANGGAL
            'Q1:R1',   // DOKUMEN PSP
            'S1:S2',   // FOTO  ← tambah jika perlu
        ];
        // Cek dulu sebelum merge agar tidak error
        foreach ($merges as $merge) {
            try { $sheet->mergeCells($merge); } catch (\Exception $e) {}
        }

        // ── Warna grup header baris 1 ──
        $groupColors = [
            'B1:G1'  => '1F4E79',  // biru tua  – Identitas
            'H1:J1'  => '375623',  // hijau tua – Klasifikasi
            'K1:N1'  => '7B2C2C',  // merah tua – Nilai
            'O1:P1'  => '4A3070',  // ungu      – Tanggal
            'Q1:R1'  => '7B3B00',  // coklat    – PSP
        ];
        foreach ($groupColors as $range => $color) {
            $sheet->getStyle($range)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Arial'],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
            ]);
        }

        // ── Kolom No (A1:A2) ──
        $sheet->getStyle('A1:A2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '012970']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // ── Header baris 2 ──
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
            'font' => ['bold' => true, 'size' => 9, 'name' => 'Arial', 'color' => ['rgb' => '012970']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'B8CCE4']],
            ],
        ]);

        // ── Baris data ──
        if ($lastRow >= 3) {
            $sheet->getStyle("A3:{$lastCol}{$lastRow}")->applyFromArray([
                'font'      => ['size' => 9, 'name' => 'Arial'],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                ],
            ]);

            // No: center
            $sheet->getStyle("A3:A{$lastRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Kolom nilai (J, K, L, M) — format angka
            foreach (['J', 'K', 'L', 'M'] as $col) {
                $sheet->getStyle("{$col}3:{$col}{$lastRow}")
                      ->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("{$col}3:{$col}{$lastRow}")
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // Zebra striping
            for ($r = 3; $r <= $lastRow; $r++) {
                if ($r % 2 == 0) {
                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F6FC']],
                    ]);
                }
            }
        }

        // ── Freeze header ──
        $sheet->freezePane('B3');

        // ── Tinggi baris header ──
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(40);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,    // No
            'B' => 16,   // Kode Barang
            'C' => 7,    // NUP
            'D' => 35,   // Nama Barang
            'E' => 22,   // Merk
            'F' => 15,   // Tipe
            'G' => 22,   // Jenis BMN
            'H' => 14,   // Kondisi
            'I' => 13,   // Status BMN
            'J' => 22,   // Nilai Perolehan Pertama
            'K' => 20,   // Nilai Perolehan
            'L' => 20,   // Nilai Penyusutan
            'M' => 18,   // Nilai Buku
            'N' => 14,   // Tgl Perolehan
            'O' => 15,   // Tgl Buku Pertama
            'P' => 22,   // No PSP
            'Q' => 13,   // Tgl PSP
            'R' => 10,   // Jumlah Foto
        ];
    }
}