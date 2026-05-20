<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class peminjaman extends Model
{
    protected $table = 'peminjamen';
 
    protected $fillable = [
        'employee_id',
        'peminjam',
        'code',
        'material_id',
        'tgl_pinjam',
        'tgl_kembali',
        'status',
        'kop_surat',     // ← tambahan baru
    ];
 
    // Relasi ke tabel users (petugas)
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
 
    // Relasi ke materials (decode JSON material_id)
    public function getMaterialsAttribute()
    {
        $ids = json_decode($this->material_id, true);
        if (!is_array($ids) || empty($ids)) {
            return collect();
        }
        return Materials::whereIn('id', $ids)->get();
    }
}
 