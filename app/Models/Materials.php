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
        'code',
        'nup',
        'name',
        'name_fix',
        'no_seri',
        'category',
        'condition',
        'status',
        'type',
        'registered',
        'bulan',
        'jenis_bmn',
        'status_bmn',
        'intra_extra',
        'henti_guna',
        'status_sbsn',
        'status_bmn_idle',
        'status_kemitraan',
        'nilai',
        'nilai_perolehan_pertama',
        'nilai_mutasi',
        'nilai_perolehan',
        'nilai_penyusutan',
        'nilai_buku',
        'years',
        'tanggal_buku_pertama',
        'tanggal_perolehan',
        'tanggal_pengapusan',
        'life_time',
        'quantity',
        'satuan',
        'umur_aset',
        'specification',
        'description',
        'documentation',
        'store_location',
        'kode_satker',
        'nama_satker',
        'kode_register',
        'nama_kl',
        'nama_e1',
        'alamat',
        'kab_kota',
        'provinsi',
        'no_polisi',
        'status_sertifikasi',
        'no_psp',
        'tanggal_psp',
        'status_penggunaan',
        'no_stnk',
        'nama_pengguna',
        'dikalibrasi',
        'last_kalibrasi',
        'schadule_kalibrasi',
        'kalibrasi_by',
        'supervisor',
    ];

    protected $casts = [
        'tanggal_perolehan'       => 'date',
        'tanggal_buku_pertama'    => 'date',
        'tanggal_pengapusan'      => 'date',
        'tanggal_psp'             => 'date',
        'life_time'               => 'date',
        'nilai'                   => 'decimal:2',
        'nilai_perolehan'         => 'decimal:2',
        'nilai_perolehan_pertama' => 'decimal:2',
        'nilai_mutasi'            => 'decimal:2',
        'nilai_penyusutan'        => 'decimal:2',
        'nilai_buku'              => 'decimal:2',
    ];

    // =========================================================
    // ACCESSOR — mapping kolom DB (spasi/kapital) ke snake_case
    // Ini agar $material->nama_barang, ->kode_barang, dll bisa
    // dipakai di seluruh aplikasi tanpa ubah file lain.
    // =========================================================

    public function getNamaBarangAttribute(): ?string
    {
        return $this->attributes['Nama Barang'] ?? $this->attributes['name'] ?? null;
    }

    public function getKodeBarangAttribute(): ?string
    {
        return $this->attributes['Kode Barang'] ?? $this->attributes['code'] ?? null;
    }

    public function getJenisBmnAttribute(): ?string
    {
        return $this->attributes['Jenis BMN'] ?? $this->attributes['jenis_bmn'] ?? null;
    }

    public function getStatusBmnAttribute(): ?string
    {
        return $this->attributes['Status BMN'] ?? $this->attributes['status_bmn'] ?? null;
    }

    public function getNilaiPerolehanPertamaAttribute(): ?string
    {
        return $this->attributes['Nilai Perolehan Pertama']
            ?? $this->attributes['nilai_perolehan_pertama']
            ?? null;
    }

    public function getNilaiMutasiAttribute(): ?string
    {
        return $this->attributes['Nilai Mutasi'] ?? $this->attributes['nilai_mutasi'] ?? null;
    }

    public function getNilaiPerolehanAttribute(): ?string
    {
        return $this->attributes['Nilai Perolehan'] ?? $this->attributes['nilai_perolehan'] ?? null;
    }

    public function getNilaiPenyusutanAttribute(): ?string
    {
        return $this->attributes['Nilai Penyusutan'] ?? $this->attributes['nilai_penyusutan'] ?? null;
    }

    public function getNilaiBukuAttribute(): ?string
    {
        return $this->attributes['Nilai Buku'] ?? $this->attributes['nilai_buku'] ?? null;
    }

    public function getTanggalBukuPertamaAttribute(): ?string
    {
        return $this->attributes['Tanggal Buku Pertama']
            ?? $this->attributes['tanggal_buku_pertama']
            ?? null;
    }

    public function getTanggalPerolehanAttribute(): ?string
    {
        return $this->attributes['Tanggal Perolehan']
            ?? $this->attributes['tanggal_perolehan']
            ?? null;
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['name']
            ?? $this->attributes['Nama Barang']
            ?? null;
    }

    public function getCodeAttribute(): ?string
    {
        return $this->attributes['code']
            ?? $this->attributes['Kode Barang']
            ?? null;
    }

    // ── Relasi ───────────────────────────────────────────────────────

    public function location()
    {
        return $this->belongsTo(\App\Models\locations::class, 'store_location', 'id');
    }

    public function categoryRelation()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\employee::class, 'supervisor', 'id');
    }
}