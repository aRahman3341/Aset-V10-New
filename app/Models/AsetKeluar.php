<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsetKeluar extends Model
{
    protected $fillable = [
		'nomor',
		'code',
		'nup',
		'pihakSatu',
		'pihakSatuNip',
		'pihakDua',
		'pihakDuaNIP',
		'itemBarang',
		'technicalData',
		'qty',
	];

	public function AsetsKeluar(): BelongsTo
	{
		return $this->belongsTo(Materials::class, 'aset', 'id');
	}
}
