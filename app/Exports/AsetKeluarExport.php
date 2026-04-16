<?php

namespace App\Exports;

use App\Models\AsetKeluar;
use App\Models\Materials;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AsetKeluarExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $from_date;
    protected $to_date;

    public function __construct($from_date, $to_date)
    {
        $this->from_date = $from_date;
        $this->to_date   = $to_date;
    }

    /**
     * Parse kolom `aset` dan kembalikan array of integer ID.
     */
    private function parseAsetIds(?string $asetJson): array
    {
        if (empty($asetJson)) {
            return [];
        }

        $decoded = json_decode($asetJson, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return [];
        }

        $ids = [];
        foreach ($decoded as $item) {
            if (is_array($item) && isset($item['name'])) {
                $id = intval($item['name']);
                if ($id > 0) {
                    $ids[] = $id;
                }
            } elseif (is_numeric($item)) {
                $id = intval($item);
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
        }

        return array_unique($ids);
    }

    public function collection()
    {
        $asetKeluarList = AsetKeluar::whereBetween('created_at', [
            $this->from_date . ' 00:00:00',
            $this->to_date   . ' 23:59:59',
        ])->get();

        $rows = collect();
        $no   = 1;

        foreach ($asetKeluarList as $item) {
            $nameValues = $this->parseAsetIds($item->aset);

            $relatedMaterials = !empty($nameValues)
                ? Materials::whereIn('id', $nameValues)->get()
                : collect();

            $namaAset = $relatedMaterials->pluck('Nama Barang')->filter()->implode(', ');
            $kodeAset = $relatedMaterials->pluck('Kode Barang')->filter()->implode(', ');
            $nupAset  = $relatedMaterials->pluck('nup')->filter()->implode(', ');

            $rows->push([
                'No'                   => $no++,
                'Nomor Surat'          => $item->nomor,
                'Nama Aset'            => $namaAset ?: '-',
                'Kode Aset'            => $kodeAset ?: '-',
                'NUP'                  => $nupAset  ?: '-',
                'Diserahkan Kepada'    => $item->kepada,
                'Pihak Kesatu'         => $item->pihakSatu,
                'NIP Pihak Kesatu'     => $item->pihakSatuNip,
                'Jabatan Pihak Kesatu' => $item->pihakSatuJabatan,
                'Pihak Kedua'          => $item->pihakDua,
                'NIP Pihak Kedua'      => $item->pihakDuaNIP,
                'Jabatan Pihak Kedua'  => $item->pihakDuaJabatan,
                'Tanggal'              => $item->created_at ? $item->created_at->format('d/m/Y') : '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Surat',
            'Nama Aset',
            'Kode Aset',
            'NUP',
            'Diserahkan Kepada',
            'Pihak Kesatu',
            'NIP Pihak Kesatu',
            'Jabatan Pihak Kesatu',
            'Pihak Kedua',
            'NIP Pihak Kedua',
            'Jabatan Pihak Kedua',
            'Tanggal',
        ];
    }

    public function title(): string
    {
        return 'Rekap Aset Keluar';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E3A5F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD0D7E2'],
                ],
            ],
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastCol . $lastRow)->applyFromArray([
                'font'      => ['size' => 10],
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFE2E8F0'],
                    ],
                ],
            ]);

            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFF8FAFC'],
                        ],
                    ]);
                }
            }

            $sheet->getStyle('A2:A' . $lastRow)->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }
}