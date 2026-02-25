<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom-kolom baru dari daftar-aset-1 (BMN) ke tabel materials.
     * Jalankan: php artisan migrate
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {

            // ── Klasifikasi BMN ──────────────────────────────────────
            $table->string('jenis_bmn', 100)->nullable()->after('bulan');
            $table->string('status_bmn', 50)->nullable()->after('jenis_bmn');
            $table->string('intra_extra', 20)->nullable()->after('status_bmn');
            $table->string('henti_guna', 10)->nullable()->after('intra_extra');
            $table->string('status_sbsn', 50)->nullable()->after('henti_guna');
            $table->string('status_bmn_idle', 20)->nullable()->after('status_sbsn');
            $table->string('status_kemitraan', 20)->nullable()->after('status_bmn_idle');

            // ── Nilai ────────────────────────────────────────────────
            $table->decimal('nilai_perolehan_pertama', 15, 2)->nullable()->after('nilai');
            $table->decimal('nilai_mutasi', 15, 2)->nullable()->after('nilai_perolehan_pertama');
            $table->decimal('nilai_perolehan', 15, 2)->nullable()->after('nilai_mutasi');
            $table->decimal('nilai_penyusutan', 15, 2)->nullable()->after('nilai_perolehan');
            $table->decimal('nilai_buku', 15, 2)->nullable()->after('nilai_penyusutan');

            // ── Waktu ────────────────────────────────────────────────
            $table->date('tanggal_buku_pertama')->nullable()->after('life_time');
            $table->date('tanggal_perolehan')->nullable()->after('tanggal_buku_pertama');
            $table->date('tanggal_pengapusan')->nullable()->after('tanggal_perolehan');

            // ── Fisik ────────────────────────────────────────────────
            $table->integer('umur_aset')->nullable()->after('quantity');

            // ── Lokasi / Data Satker (BMN) ───────────────────────────
            $table->string('kode_satker', 100)->nullable()->after('store_location');
            $table->string('nama_satker', 200)->nullable()->after('kode_satker');
            $table->string('kode_register', 100)->nullable()->after('nama_satker');
            $table->string('nama_kl', 200)->nullable()->after('kode_register');
            $table->string('nama_e1', 200)->nullable()->after('nama_kl');
            $table->text('alamat')->nullable()->after('nama_e1');
            $table->string('kab_kota', 100)->nullable()->after('alamat');
            $table->string('provinsi', 100)->nullable()->after('kab_kota');

            // ── Dokumen BMN ──────────────────────────────────────────
            $table->string('no_polisi', 50)->nullable()->after('provinsi');
            $table->string('status_sertifikasi', 100)->nullable()->after('no_polisi');
            $table->string('no_psp', 100)->nullable()->after('status_sertifikasi');
            $table->date('tanggal_psp')->nullable()->after('no_psp');
            $table->string('status_penggunaan', 200)->nullable()->after('tanggal_psp');
            $table->string('no_stnk', 100)->nullable()->after('status_penggunaan');
            $table->string('nama_pengguna', 200)->nullable()->after('no_stnk');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_bmn', 'status_bmn', 'intra_extra', 'henti_guna',
                'status_sbsn', 'status_bmn_idle', 'status_kemitraan',
                'nilai_perolehan_pertama', 'nilai_mutasi', 'nilai_perolehan',
                'nilai_penyusutan', 'nilai_buku',
                'tanggal_buku_pertama', 'tanggal_perolehan', 'tanggal_pengapusan',
                'umur_aset',
                'kode_satker', 'nama_satker', 'kode_register',
                'nama_kl', 'nama_e1', 'alamat', 'kab_kota', 'provinsi',
                'no_polisi', 'status_sertifikasi', 'no_psp', 'tanggal_psp',
                'status_penggunaan', 'no_stnk', 'nama_pengguna',
            ]);
        });
    }
};