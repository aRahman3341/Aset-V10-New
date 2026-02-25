<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsetOut extends Model
{

	protected $fillable = [
		'name',
		'spek',
		'qty',
		'satuan',
	];

	public function itemskeluar(): BelongsTo
	{
		return $this->belongsTo(Items::class, 'name', 'id');
	}
	public function asetOuts()
    {
        return $this->belongsTo(AsetOut::class, 'faktur_id', 'id');
    }
	public function ajuan()
    {
        return $this->belongsTo(Ajuan::class, 'id', 'faktur_id');
    }
	
}
