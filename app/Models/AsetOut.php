<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsetOut extends Model
{
    protected $table = 'aset_outs';

    protected $fillable = [
        'no_faktur',
        'mak',
        'no_nd',
    ];

    /**
     * Relasi ke Items (barang habis pakai)
     * Digunakan untuk cetak faktur/lampiran
     */
    public function itemskeluar(): BelongsTo
    {
        return $this->belongsTo(Items::class, 'name', 'id');
    }

    /**
     * Relasi ke tabel ajuans 
     * Menggunakan HasMany karena 1 AsetOut punya banyak Ajuan
     */
    public function ajuan(): HasMany
    {
        return $this->hasMany(Ajuan::class, 'faktur_id', 'id');
    }
}