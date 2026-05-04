<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncStatusBmn extends Command
{
    protected $signature   = 'bmn:sync-status';
    protected $description = 'Sinkronisasi Status BMN berdasarkan data peminjaman (Aktif / Dipinjam / Terlambat)';

    public function handle(): void
    {
        $today = Carbon::today();
        $this->info("Sinkronisasi Status BMN — " . $today->toDateString());

        // ── 1. Reset semua aset yang sedang dalam pinjaman aktif ────────
        //    Ambil semua loan yang BELUM dikembalikan
        $activeLoans = DB::table('peminjamen')
            ->where('status', 'Dipinjam')
            ->get();

        $terlambat = 0;
        $dipinjam  = 0;

        foreach ($activeLoans as $loan) {
            $ids = json_decode($loan->material_id, true);
            if (!is_array($ids) || empty($ids)) continue;

            $tglKembali = Carbon::parse($loan->tgl_kembali)->startOfDay();
            $statusBmn  = $tglKembali->lt($today) ? 'Terlambat' : 'Dipinjam';

            DB::table('materials')
                ->whereIn('id', $ids)
                ->update(['Status BMN' => $statusBmn, 'updated_at' => Carbon::now()]);

            if ($statusBmn === 'Terlambat') {
                $terlambat += count($ids);
            } else {
                $dipinjam += count($ids);
            }
        }

        // ── 2. Aset yang sudah dikembalikan → pastikan kembali Aktif ────
        //    Kumpulkan semua ID yang masih dalam pinjaman aktif
        $allActiveMaterialIds = [];
        foreach ($activeLoans as $loan) {
            $ids = json_decode($loan->material_id, true);
            if (is_array($ids)) {
                $allActiveMaterialIds = array_merge($allActiveMaterialIds, $ids);
            }
        }
        $allActiveMaterialIds = array_unique(array_filter(array_map('intval', $allActiveMaterialIds)));

        // Aset yang statusnya Dipinjam/Terlambat tapi tidak ada di pinjaman aktif
        $aktifFixed = DB::table('materials')
            ->whereIn('Status BMN', ['Dipinjam', 'Terlambat'])
            ->when(!empty($allActiveMaterialIds), function ($q) use ($allActiveMaterialIds) {
                $q->whereNotIn('id', $allActiveMaterialIds);
            })
            ->update(['Status BMN' => 'Aktif', 'updated_at' => Carbon::now()]);

        $this->table(
            ['Status', 'Jumlah Aset'],
            [
                ['Terlambat → diperbarui',  $terlambat],
                ['Dipinjam → diperbarui',   $dipinjam],
                ['Aktif → dipulihkan',      $aktifFixed],
            ]
        );

        $this->info('✅ Selesai.');
    }
}