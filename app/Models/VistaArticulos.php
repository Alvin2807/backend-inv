<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaArticulos extends Model
{
    use HasFactory;
    public    $table    = "vista_articulos";
    protected $fillable = ['id_articulo','codigo','referencia','marca','categoria','modelo','color','fk_marca','fk_modelo',
    'fk_categoria','fk_color','cantidad_pedida','stock'];
    protected $casts = 
    [
        'id_articulo'  =>'integer',
        "fk_marca"     =>'integer',
        'fk_modelo'    =>'integer',
        'fk_categoria' =>'integer',
        'fk_color'     =>'integer',
        'cantidad_pedida' =>'integer',
        'stock' =>'integer'
    ];
}
