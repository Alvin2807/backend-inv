<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vistaArticulosEntradas extends Model
{
    use HasFactory;
    
    public $table = "vista_articulos_entradas";
    protected $fillable = ['id_articulo','codigo','referencia','categoria','marca','modelo','color','cantidad_entrada','fecha_ult_entrada'];
    
    protected $casts = 
    [
        'id_articulo'=>'integer',
        'fecha_ult_entrada'=>'datetime:d/m/Y',
        'cantidad_entrada'=>'integer'
    ];
}
