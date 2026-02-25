<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class peminjaman extends Model
{

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
     * Get the user that owns the peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Materials::class, 'material_id', 'id');
    }

    /**
     * Get the employee that owns the peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(employee::class, 'employee_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }
}
