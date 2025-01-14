<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaSolicitud extends Model
{
    use HasFactory;
    public    $table    = 'vista_solicitudes';
    protected $fillable = ['id_solicitud','fk_tipo_solicitud','tipo_solicitud','fk_despacho','no_entrada','no_salida','fecha_salida',
    'incidencia','cantidad_solicitada','cantidad_pendiente','cantidad_confirmada','despacho','estado'];

    protected $casts = 
    [
        'id_solicitud'     =>'integer',
        'fk_tipo_solicitud'=>'integer',
        'fk_despacho'      =>'integer',
        'fecha_entrada'    =>'datetime:Y-m-d',
        'fecha_salida'     =>'datetime:Y-m-d',
        'cantidad_solicitada' =>'integer',
        'cantidad_pendiente'  =>'integer',
        'cantidad_confirmada' =>'integer'
    ];
}
