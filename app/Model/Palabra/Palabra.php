<?php

namespace App\Model\Palabra;

use App\Model\OrderItemContent\OrderItemContent;
use Illuminate\Database\Eloquent\Model;
use DB;

class Palabra extends Model
{

    protected $table = 'palabras';
    protected $primaryKey = 'palabra_id';

    protected $fillable = [
        'palabra',
        'sin_acentos',
        'sensible'
    ];

}
