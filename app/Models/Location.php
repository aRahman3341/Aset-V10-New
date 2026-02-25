<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;

class Location extends Model
{
	//use HasFactory;

	protected $fillabel = [
		'office',
		'floor',
		'room',
	];
}
