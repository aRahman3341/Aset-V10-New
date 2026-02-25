<?php

namespace App\Exports;

use App\Models\Materials;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
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
        $categories = DB::table('categories')->pluck('name', 'id');
        $employees  = DB::table('employees')->pluck('name', 'id');
        $locations  = DB::table('locations')->get()->keyBy('id');

        $query = Materials::query();
        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }
        $data = $query->get();

        return $data->map(function ($row, $index) use ($categories, $employees, $locations) {
            $loc = $locations->get($row->store_location);

            return [
                // ── Identitas ──────────────────────────────────────
                $index + 1,
                $row->code                    ?? '-',
                $row->nup                     ?? '-',
                $row->name                    ?? '-',
                $row->name_fix                ?? '-',
                $row->no_seri                 ?? '-',

                // ── Klasifikasi ────────────────────────────────────
                $row->jenis_bmn               ?? '-',
                $categories[$row->category]   ?? '-',
                $row->type                    ?? '-',
                $row->condition               ?? '-',
                $row->status                  ?? '-',
                $row->status_bmn              ?? '-',
                $row->intra_extra             ?? '-',

                // ── Nilai ──────────────────────────────────────────
                $row->years                   ?? '-',
                $row->nilai_perolehan         ?? $row->nilai ?? 0,
                $row->nilai_penyusutan        ?? 0,
                $row->nilai_buku              ?? 0,

                // ── Tanggal ────────────────────────────────────────
                $row->tanggal_perolehan    ? Carbon::parse($row->tanggal_perolehan)->format('d/m/Y')    : '-',
                $row->tanggal_buku_pertama ? Carbon::parse($row->tanggal_buku_pertama)->format('d/m/Y') : '-',
                $row->tanggal_pengapusan   ? Carbon::parse($row->tanggal_pengapusan)->format('d/m/Y')   : '-',

                // ── Fisik ──────────────────────────────────────────
                $row->quantity                ?? 1,
                $row->satuan                  ?? '-',
                $row->umur_aset               ?? $row->life_time ?? '-',
                $row->specification           ?? '-',

                // ── Lokasi Fisik ───────────────────────────────────
                $loc->office                  ?? '-',
                $loc->floor                   ?? '-',
                $loc->room                    ?? '-',

                // ── Lokasi BMN ─────────────────────────────────────
                $row->kode_satker             ?? '-',
                $row->nama_satker             ?? '-',
                $row->kode_register           ?? '-',
                $row->nama_kl                 ?? '-',
                $row->nama_e1                 ?? '-',
                $row->alamat                  ?? '-',
                $row->kab_kota                ?? '-',
                $row->provinsi                ?? '-',

                // ── Dokumen BMN ────────────────────────────────────
                $row->no_polisi               ?? '-',
                $row->status_sertifikasi      ?? '-',
                $row->no_psp                  ?? '-',
                $row->tanggal_psp          ? Carbon::parse($row->tanggal_psp)->format('d/m/Y') : '-',
                $row->status_penggunaan       ?? '-',
                $row->no_stnk                 ?? '-',
                $row->nama_pengguna           ?? '-',

                // ── Kalibrasi ──────────────────────────────────────
                $row->dikalibrasi == 1 ? 'Perlu' : 'Tidak',
                $row->last_kalibrasi     ? Carbon::parse($row->last_kalibrasi)->format('d/m/Y')     : '-',
                $row->schadule_kalibrasi ? Carbon::parse($row->schadule_kalibrasi)->format('d/m/Y') : '-',
                $row->kalibrasi_by            ?? '-',

                // ── Penanggung Jawab & Keterangan ──────────────────
                $employees[$row->supervisor]  ?? $row->supervisor ?? '-',
                $row->description             ?? '-',
                $row->documentation           ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            // Row 1: Grup header (akan di-merge di styles())
            [
                'No',
                'IDENTITAS BARANG', '', '', '', '',
                'KLASIFIKASI', '', '', '', '', '', '',
                'NILAI & WAKTU', '', '', '', '', '', '',
                'FISIK', '', '', '',
                'LOKASI FISIK', '', '',
                'LOKASI BMN / SATKER', '', '', '', '', '', '', '',
                'DOKUMEN BMN', '', '', '', '', '', '',
                'KALIBRASI', '', '', '',
                'KETERANGAN', '', '',
            ],
            // Row 2: Header kolom detail
            [
                'No',
                'Kode Barang', 'NUP', 'Nama Barang', 'Nama Fix / Merk', 'No Seri',
                'Jenis BMN', 'Kategori', 'Tipe Aset', 'Kondisi', 'Status', 'Status BMN', 'Intra/Extra',
                'Tahun', 'Nilai Perolehan (Rp)', 'Nilai Penyusutan (Rp)', 'Nilai Buku (Rp)',
                'Tgl Perolehan', 'Tgl Buku Pertama', 'Tgl Pengapusan',
                'Qty', 'Satuan', 'Umur (Thn)', 'Spesifikasi',
                'Gedung', 'Lantai', 'Ruangan',
                'Kode Satker', 'Nama Satker', 'Kode Register', 'Nama K/L', 'Nama Unit (E1)',
                'Alamat', 'Kab/Kota', 'Provinsi',
                'No Polisi', 'Status Sertifikasi', 'No PSP', 'Tgl PSP',
                'Status Penggunaan', 'No STNK', 'Nama Pengguna',
                'Perlu Kalibrasi', 'Kalibrasi Terakhir', 'Jadwal Kalibrasi', 'Dikalibrasi Oleh',
                'Penanggung Jawab', 'Deskripsi / Keterangan', 'Dokumentasi',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol  = 'AW'; // kolom terakhir (49 kolom)
        $lastRow  = $sheet->getHighestRow();

        // ── Merge baris 1 (grup header) ──────────────────────────────
        $merges = [
            'A1:A2',   // No
            'B1:G1',   // IDENTITAS
            'H1:N1',   // KLASIFIKASI
            'O1:U1',   // NILAI & WAKTU
            'V1:Y1',   // FISIK
            'Z1:AB1',  // LOKASI FISIK
            'AC1:AJ1', // LOKASI BMN
            'AK1:AQ1', // DOKUMEN BMN
            'AR1:AU1', // KALIBRASI
            'AV1:AX1', // KETERANGAN
        ];
        foreach ($merges as $merge) {
            $sheet->mergeCells($merge);
        }

        // ── Warna grup header baris 1 ────────────────────────────────
        $groupColors = [
            'B1:G1'  => '1F4E79',  // biru tua  – Identitas
            'H1:N1'  => '375623',  // hijau tua – Klasifikasi
            'O1:U1'  => '7B2C2C',  // merah tua – Nilai
            'V1:Y1'  => '4A3070',  // ungu      – Fisik
            'Z1:AB1' => '1F4E79',  // biru      – Lokasi Fisik
            'AC1:AJ1'=> '1C5C3A',  // hijau     – Lokasi BMN
            'AK1:AQ1'=> '7B3B00',  // coklat    – Dokumen BMN
            'AR1:AU1'=> '005050',  // teal      – Kalibrasi
            'AV1:AX1'=> '404040',  // abu       – Keterangan
        ];
        foreach ($groupColors as $range => $color) {
            $sheet->getStyle($range)->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 10,
                    'name'  => 'Arial',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
            ]);
        }

        // ── Style kolom No (A1:A2) ───────────────────────────────────
        $sheet->getStyle('A1:A2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '012970']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // ── Header baris 2 ────────────────────────────────────────────
        $sheet->getStyle('A2:AX2')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2'],
            ],
            'font' => [
                'bold' => true,
                'size' => 9,
                'name' => 'Arial',
                'color' => ['rgb' => '012970'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'B8CCE4'],
                ],
            ],
        ]);

        // ── Baris data ────────────────────────────────────────────────
        if ($lastRow >= 3) {
            $sheet->getStyle("A3:AX{$lastRow}")->applyFromArray([
                'font' => ['size' => 9, 'name' => 'Arial'],
                'alignment' => [
                    'vertical'  => Alignment::VERTICAL_CENTER,
                    'wrapText'  => false,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D9D9D9'],
                    ],
                ],
            ]);

            // Kolom No: center
            $sheet->getStyle("A3:A{$lastRow}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Kolom nilai (O, P, Q): format angka ribuan
            foreach (['O', 'P', 'Q'] as $col) {
                $sheet->getStyle("{$col}3:{$col}{$lastRow}")
                      ->getNumberFormat()
                      ->setFormatCode('#,##0');
                $sheet->getStyle("{$col}3:{$col}{$lastRow}")
                      ->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // Baris alternating (zebra striping)
            for ($r = 3; $r <= $lastRow; $r++) {
                if ($r % 2 == 0) {
                    $sheet->getStyle("A{$r}:AX{$r}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F2F6FC'],
                        ],
                    ]);
                }
            }
        }

        // ── Freeze panes (header tetap saat scroll) ───────────────────
        $sheet->freezePane('B3');

        // ── Tinggi baris header ───────────────────────────────────────
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(35);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A'  => 5,   // No
            'B'  => 16,  // Kode Barang
            'C'  => 7,   // NUP
            'D'  => 30,  // Nama Barang
            'E'  => 22,  // Nama Fix
            'F'  => 15,  // No Seri
            'G'  => 18,  // Jenis BMN
            'H'  => 18,  // Kategori
            'I'  => 10,  // Tipe
            'J'  => 12,  // Kondisi
            'K'  => 13,  // Status
            'L'  => 13,  // Status BMN
            'M'  => 12,  // Intra/Extra
            'N'  => 7,   // Tahun
            'O'  => 20,  // Nilai Perolehan
            'P'  => 20,  // Nilai Penyusutan
            'Q'  => 18,  // Nilai Buku
            'R'  => 13,  // Tgl Perolehan
            'S'  => 15,  // Tgl Buku Pertama
            'T'  => 14,  // Tgl Pengapusan
            'U'  => 6,   // Qty
            'V'  => 10,  // Satuan
            'W'  => 10,  // Umur
            'X'  => 25,  // Spesifikasi
            'Y'  => 18,  // Gedung
            'Z'  => 10,  // Lantai
            'AA' => 18,  // Ruangan
            'AB' => 16,  // Kode Satker
            'AC' => 28,  // Nama Satker
            'AD' => 16,  // Kode Register
            'AE' => 28,  // Nama K/L
            'AF' => 28,  // Nama Unit
            'AG' => 30,  // Alamat
            'AH' => 18,  // Kab/Kota
            'AI' => 16,  // Provinsi
            'AJ' => 12,  // No Polisi
            'AK' => 22,  // Status Sertifikasi
            'AL' => 18,  // No PSP
            'AM' => 13,  // Tgl PSP
            'AN' => 30,  // Status Penggunaan
            'AO' => 12,  // No STNK
            'AP' => 20,  // Nama Pengguna
            'AQ' => 12,  // Perlu Kalibrasi
            'AR' => 15,  // Kalibrasi Terakhir
            'AS' => 15,  // Jadwal Kalibrasi
            'AT' => 18,  // Dikalibrasi Oleh
            'AU' => 20,  // Penanggung Jawab
            'AV' => 30,  // Deskripsi
            'AW' => 20,  // Dokumentasi
        ];
    }
}