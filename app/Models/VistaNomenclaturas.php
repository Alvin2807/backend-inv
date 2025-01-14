<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaNomenclaturas extends Model
{
    use HasFactory;

    public    $table = "vista_nomenclaturas";
    protected $casts = 
    [
        'id_nomenclatura' =>'integer',
        'fk_despacho'     =>'integer'
    ];
}
