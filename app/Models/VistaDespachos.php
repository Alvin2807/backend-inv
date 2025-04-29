<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaDespachos extends Model
{
    use HasFactory;

    public    $table = 'vista_despachos';
    protected $fillable = ['id_despacho','fk_provincia','provincia','despacho','estado'];
    protected $casts = 
    [
        'id_despacho'=>'integer',
        'fk_provincia'=>'integer'
    ];
}
