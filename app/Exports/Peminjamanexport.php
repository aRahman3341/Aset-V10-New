<?php

namespace App\Exports;

use App\Models\peminjaman;
use App\Models\Materials;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $from_date;
    protected $to_date;
    protected $status;

    public function __construct($from_date, $to_date, $status = null)
    {
        $this->from_date = $from_date;
        $this->to_date   = $to_date;
        $this->status    = $status;
    }

    public function collection()
    {
        $query = peminjaman::with(['user'])
            ->whereBetween('tgl_pinjam', [
                Carbon::parse($this->from_date)->startOfDay(),
                Carbon::parse($this->to_date)->endOfDay(),
            ])
            ->orderBy('tgl_pinjam', 'asc');

        if ($this->status && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Peminjaman',
            'Nama Barang',
            'Kode Barang',
            'NUP',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Peminjam',
            'Petugas',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        // material_id adalah JSON array
        $decoded     = json_decode($row->material_id, true);
        $materialIds = is_array($decoded) ? $decoded : [];
        $firstId     = $materialIds[0] ?? null;
        $material    = $firstId ? Materials::find($firstId) : null;

        $namaBarang = '-';
        $kodeBarang = '-';
        $nup        = '-';

        if ($material) {
            $namaBarang = $material->{'Nama Barang'} ?? ($material->nama_barang ?? '-');
            $kodeBarang = $material->{'Kode Barang'} ?? ($material->kode_barang ?? '-');
            $nup        = $material->nup ?? '-';
        }

        $extraCount = count($materialIds) - 1;
        if ($extraCount > 0) {
            $namaBarang .= ' (+' . $extraCount . ' barang lain)';
        }

        return [
            $no,
            $row->code,
            $namaBarang,
            $kodeBarang,
            $nup,
            $row->tgl_pinjam  ? Carbon::parse($row->tgl_pinjam)->format('d/m/Y')  : '-',
            $row->tgl_kembali ? Carbon::parse($row->tgl_kembali)->format('d/m/Y') : '-',
            $row->peminjam ?? '-',
            $row->user->name ?? '-',
            $row->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
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
            ],
        ]);

        $highestRow = $sheet->getHighestRow();
        for ($i = 2; $i <= $highestRow; $i++) {
            $color = ($i % 2 === 0) ? 'FFF0F4FA' : 'FFFFFFFF';
            $sheet->getStyle("A{$i}:J{$i}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $color],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        $sheet->getRowDimension(1)->setRowHeight(22);

        return [];
    }

    public function title(): string
    {
        return 'Report Peminjaman';
    }
}