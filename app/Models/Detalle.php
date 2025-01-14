<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    use HasFactory;
    public    $table      = 'inv_detalles';
    protected $primarykey = 'id_detalle';
    protected $fillable   = ['id_detalle','fk_tipo_solicitud','fk_articulo','fk_solicitud','cantidad_solicitada','cantidad_pendiente',
    'cantidad_confirmada','estado'];
    public $incrementing  = true;
    public $timestamps    = false;

    protected $casts = 
    [
        'id_detalle'        => 'integer',
        'fk_tipo_solicitud' =>'integer',
        'fk_articulo'       =>'integer',
        'fk_solicitud'      =>'integer',
        'cantidad_solicitada' =>'integer',
        'cantidad_pendiente'  =>'integer',
        'cantidad_confirmada' =>'integer'

    ];
}
