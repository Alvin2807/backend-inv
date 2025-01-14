<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomenclatura extends Model
{
    use HasFactory;
    
    public    $table      = "inv_nomenclaturas";
    protected $primarykey = "id_nomenclatura";
    protected $fillable   = ['id_nomenclatura','nomenclatura','fk_despacho'];
    public    $incrementing = true;
    public    $timestamps   = false;
    protected $casts        = 
    [
        'id_nomenclatura' =>'integer',
        'fk_despacho'    =>'integer'

    ];
}
