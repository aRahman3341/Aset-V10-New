<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class peminjaman extends Model
{
    protected $table = 'peminjamen';

    protected $fillable = [
        'code',
        'material_id',
        'tgl_pinjam',
        'tgl_kembali',
        'employee_id',
        'peminjam',
        'status',
    ];

    /**
     * Relasi ke banyak Materials berdasarkan JSON array material_id.
     * Mengembalikan Collection of Materials.
     */
    public function getMaterialsAttribute()
    {
        $ids = json_decode($this->material_id, true);

        // Backward-compatible: jika material_id bukan JSON (integer lama)
        if (!is_array($ids)) {
            $ids = [$this->material_id];
        }

        $ids = array_filter(array_map('intval', $ids));

        return Materials::whereIn('id', $ids)->get();
    }

    /**
     * Relasi material tunggal (backward-compatible untuk view lama).
     * Mengembalikan material pertama.
     */
    public function getMaterialAttribute()
    {
        return $this->materials->first();
    }

    /**
     * Eager load helper — dipakai di with(['materials'])
     * Catatan: karena ini JSON, tidak bisa pakai Eloquent relation biasa.
     * Gunakan $loan->materials (accessor) di view.
     */
    public function material(): BelongsTo
    {
        // Fallback: ambil ID pertama dari JSON untuk kompatibilitas
        $firstId = is_array(json_decode($this->material_id ?? '[]', true))
            ? (json_decode($this->material_id, true)[0] ?? $this->material_id)
            : $this->material_id;

        return $this->belongsTo(Materials::class, 'material_id', 'id')
                    ->withDefault();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(employee::class, 'employee_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }
}