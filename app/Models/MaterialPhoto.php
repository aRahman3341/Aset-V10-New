<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialPhoto extends Model
{
    protected $table    = 'material_photos';
    protected $fillable = ['material_id', 'filename', 'original_name'];

    public function material()
    {
        return $this->belongsTo(Materials::class, 'material_id');
    }

    /**
     * URL publik foto
     */
    public function getUrlAttribute(): string
    {
        return asset('assets/upload_asset_tetap/' . $this->filename);
    }
}
