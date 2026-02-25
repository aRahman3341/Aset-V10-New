<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materials extends Model
{
    use HasFactory;

    protected $table = 'materials';

    protected $guarded = ['id'];

    protected $fillable = [
        // ── Identitas Utama ──────────────────────────────────────────
        'code',
        'nup',
        'name',
        'name_fix',
        'no_seri',

        // ── Klasifikasi ──────────────────────────────────────────────
        'category',
        'condition',
        'status',
        'type',
        'registered',
        'bulan',

        // ── Kolom BMN Baru (dari daftar-aset-1) ─────────────────────
        'jenis_bmn',          // Jenis BMN  (Alat Besar, dll)
        'status_bmn',         // Status BMN (Aktif / Tidak Aktif)
        'intra_extra',        // Intra / Extra
        'henti_guna',         // Henti Guna
        'status_sbsn',        // Status SBSN
        'status_bmn_idle',    // Status BMN Idle
        'status_kemitraan',   // Status Kemitraan

        // ── Nilai ────────────────────────────────────────────────────
        'nilai',                    // nilai perolehan (kolom lama, tetap dipakai)
        'nilai_perolehan_pertama',  // Nilai Perolehan Pertama
        'nilai_mutasi',             // Nilai Mutasi
        'nilai_perolehan',          // Nilai Perolehan
        'nilai_penyusutan',         // Nilai Penyusutan
        'nilai_buku',               // Nilai Buku

        // ── Waktu ────────────────────────────────────────────────────
        'years',
        'tanggal_buku_pertama',   // Tanggal Buku Pertama
        'tanggal_perolehan',      // Tanggal Perolehan
        'tanggal_pengapusan',     // Tanggal Pengapusan
        'life_time',              // Umur aset (date, kolom lama)

        // ── Fisik ────────────────────────────────────────────────────
        'quantity',
        'satuan',
        'umur_aset',           // Umur Aset (integer tahun, kolom baru)
        'specification',
        'description',
        'documentation',

        // ── Lokasi Fisik ─────────────────────────────────────────────
        'store_location',

        // ── Lokasi / Data Satker (BMN) ───────────────────────────────
        'kode_satker',
        'nama_satker',
        'kode_register',
        'nama_kl',
        'nama_e1',
        'alamat',
        'kab_kota',
        'provinsi',

        // ── Dokumen BMN ──────────────────────────────────────────────
        'no_polisi',
        'status_sertifikasi',
        'no_psp',
        'tanggal_psp',
        'status_penggunaan',
        'no_stnk',
        'nama_pengguna',

        // ── Kalibrasi ────────────────────────────────────────────────
        'dikalibrasi',
        'last_kalibrasi',
        'schadule_kalibrasi',
        'kalibrasi_by',

        // ── Penanggung Jawab ─────────────────────────────────────────
        'supervisor',
    ];

    // Cast kolom tanggal agar bisa dipanggil ->format() langsung
    protected $casts = [
        'tanggal_perolehan'    => 'date',
        'tanggal_buku_pertama' => 'date',
        'tanggal_pengapusan'   => 'date',
        'tanggal_psp'          => 'date',
        'life_time'            => 'date',
        'nilai'                => 'decimal:2',
        'nilai_perolehan'      => 'decimal:2',
        'nilai_perolehan_pertama' => 'decimal:2',
        'nilai_mutasi'         => 'decimal:2',
        'nilai_penyusutan'     => 'decimal:2',
        'nilai_buku'           => 'decimal:2',
    ];

    // ── Relasi ───────────────────────────────────────────────────────

    /** Lokasi (gedung / lantai / ruangan) */
    public function location()
    {
        return $this->belongsTo(\App\Models\locations::class, 'store_location', 'id');
    }

    /** Kategori */
    public function categoryRelation()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category', 'id');
    }

    /** Penanggung jawab */
    public function employee()
    {
        return $this->belongsTo(\App\Models\employee::class, 'supervisor', 'id');
    }
}