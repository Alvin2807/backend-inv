<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vistaDetalleEntradasArticulos extends Model
{
    use HasFactory;

    public $table = "vista_detalles_articulos_entradas";
    protected $fillable = ['id_detalle','fecha_entrada','entregado_por','num_solicitud','tipo_entrada',
    'id_articulo','codigo','referencia','marca','color','cantidad_solicitada','mes','tipo_solicitud','despacho'];

    protected $casts = [
        'id_detalle' =>'integer',
        'fecha_entrada'=>'datetime:d/m/Y',
        'cantidad_solicitada'=>'integer',
        'id_articulo'=>'integer'
    ];
}
