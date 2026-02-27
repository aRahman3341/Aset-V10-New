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

class ItemsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    protected $data;         // Collection Items
    protected $totalBalance; // null jika export terpilih
    protected $categories;   // label judul sheet
    protected $no = 0;

    /**
     * Konstruktor fleksibel:
     *   - export per kategori : new ItemsExport($collection, $totalBalance, 'ATK')
     *   - export terpilih     : new ItemsExport($collection, null, 'Terpilih')
     */
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

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Saldo',
            'Status',
            'Tanggal Dibuat',
            'Terakhir Diupdate',
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
            'A' => 6,
            'B' => 22,
            'C' => 40,
            'D' => 18,
            'E' => 12,
            'F' => 10,
            'G' => 14,
            'H' => 18,
            'I' => 18,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $thin    = Border::BORDER_THIN;

        // Header row
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => ['allBorders' => ['borderStyle' => $thin, 'color' => ['rgb' => '2D5A8E']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Data rows
        if ($lastRow > 1) {
            $sheet->getStyle("A2:I{$lastRow}")->applyFromArray([
                'font'      => ['size' => 9],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => $thin, 'color' => ['rgb' => 'DEE2E6']]],
            ]);

            for ($r = 2; $r <= $lastRow; $r++) {
                if ($r % 2 === 0) {
                    $sheet->getStyle("A{$r}:I{$r}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setRGB('F8FAFD');
                }
                $sheet->getRowDimension($r)->setRowHeight(16);
            }

            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}