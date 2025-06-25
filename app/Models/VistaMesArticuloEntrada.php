<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaMesArticuloEntrada extends Model
{
    use HasFactory;
    public    $table = 'vista_meses_entrada_articulos';
    protected $fillable = ['id_mes','fk_articulo','codigo','referencia','marca','modelo','categoria','color','mes','cantidad'];
    protected $casts = 
    [
        'id_mes'=>'integer',
        'fk_articulo'=>'integer',
        'cantidad'=>'integer'
    ];
}
