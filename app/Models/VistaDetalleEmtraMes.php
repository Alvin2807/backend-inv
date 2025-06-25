<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaDetalleEmtraMes extends Model
{
    use HasFactory;
    public    $table    = 'vista_detalle_entrada_x_mes';
    protected $fillable = ['id_detalle','mes','id_mes','fecha_entrada','entregado_por','num_solicitud','tipo_entrada',
    'tipo_solicitud','despacho','cantidad_solicitada','fk_articulo'];
    protected $casts = 
    [
        'id_detalle'=>'integer',
        'id_mes'=>'integer',
        'fk_articulo'=>'integer',
        'cantidad_solicitada'=>'integer',
        'fecha_entrada'=>'datetime:d/m/Y'
    ];
}
