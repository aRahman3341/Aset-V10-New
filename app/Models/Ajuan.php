<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ajuan extends Model
{
	protected $fillable = [
		'name',
		'status',
		'pengaju',
		'faktur_id',
		'qty',
	];

	public function item()
    {
        return $this->belongsTo(Items::class, 'name', 'id');
    }
	public function asetOuts()
    {
        return $this->belongsTo(AsetOut::class, 'faktur_id', 'id');
    }
	public function user(): BelongsTo
    {
        return $this->belongsTo(user::class, 'pengaju', 'id');
    }
}
