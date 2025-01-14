<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;
    public    $table      = 'inv_solicitudes';
    protected $primarykey = 'id_solicitud';
    protected $fillable   = ['id_solicitud','fk_tipo_solicitud','fk_despacho','no_entrada','fecha_entrada','no_salida',
    'incidencia','cantidad_solicitada','cantidad_pendiente','cantidad_confirmada','estado','fecha_salida'];
    public $incrementing = true;
    public $timestamps   = false;

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
