<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Export Barang Habis Pakai
 *
 * Format header KONSISTEN dengan ItemsImport:
 *   Baris 1 = kolom header (headingRow: 1 di import)
 *   Baris 2+ = data
 *
 * Kolom yang bisa di-reimport: Kode Barang, Nama Barang,
 *   Kategori, Satuan, Saldo, Status
 * Kolom info tambahan (diabaikan saat import): No,
 *   Tanggal Dibuat, Terakhir Diupdate
 */
class ItemsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    protected $data;
    protected $totalBalance;
    protected $categories;
    protected $no = 0;

    public function __construct($data, $totalBalance, string $categories)
    {
        $this->data         = $data;
        $this->totalBalance = $totalBalance;
        $this->categories   = $categories;
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Export ' . $this->categories;
    }

    /**
     * Heading baris 1 — HARUS sama dengan yang dibaca import.
     * Import (headingRow:1) mengonversi: "Kode Barang" → kode_barang, dst.
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',     // → kode_barang  (dibaca import)
            'Nama Barang',     // → nama_barang   (dibaca import)
            'Kategori',        // → kategori      (dibaca import)
            'Satuan',          // → satuan        (dibaca import)
            'Saldo',           // → saldo         (dibaca import)
            'Status',          // → status        (dibaca import)
            'Tanggal Dibuat',  // informasi saja, diabaikan import
            'Terakhir Diupdate', // informasi saja, diabaikan import
        ];
    }

    public function map($item): array
    {
        $this->no++;
        return [
            $this->no,
            $item->code,
            $item->name,
            $item->categories,
            $item->satuan,
            $item->saldo,
            $item->status ? 'Teregister' : 'Belum',
            $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : '-',
            $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 22,  // Kode Barang
            'C' => 40,  // Nama Barang
            'D' => 18,  // Kategori
            'E' => 12,  // Satuan
            'F' => 10,  // Saldo
            'G' => 14,  // Status
            'H' => 18,  // Tanggal Dibuat
            'I' => 18,  // Terakhir Diupdate
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $thin    = Border::BORDER_THIN;

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
                'name'  => 'Arial',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A5F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => $thin, 'color' => ['rgb' => '2D5A8E']],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->getStyle('H1:I1')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A5568'], 
            ],
            'font' => ['italic' => true],
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle("A2:I{$lastRow}")->applyFromArray([
                'font'      => ['size' => 9, 'name' => 'Arial'],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => [
                    'allBorders' => ['borderStyle' => $thin, 'color' => ['rgb' => 'DEE2E6']],
                ],
            ]);

            for ($r = 2; $r <= $lastRow; $r++) {
                if ($r % 2 === 0) {
                    $sheet->getStyle("A{$r}:I{$r}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setRGB('F8FAFD');
                }
                $sheet->getRowDimension($r)->setRowHeight(16);
            }

            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F2:F{$lastRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:G{$lastRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H2:I{$lastRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("H2:I{$lastRow}")->getFont()
                  ->getColor()->setRGB('6B7280');
        }

        $sheet->freezePane('B2');

        return [];
    }
}