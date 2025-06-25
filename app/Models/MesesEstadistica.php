<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesesEstadistica extends Model
{
    use HasFactory;
    public    $table = "inv_meses_estadisticas";
    protected $primarykey = "id_mes";
    protected $fillable = ['id_mes','fk_articulo','fk_tipo_solicitud','mes','cantidad','usuario_crea','fecha_crea','usuario_modifica','fecha_modifica'];
    public $timestamps   = false;
    public $incrementing = true;

    protected $casts = 
    [
        'id_mes'=>'integer',
        'fk_articulo'=>'integer',
        'fk_tipo_solicitud'=>'integer',
        'mes'=>'string',
        'cantidad'=>'integer'
    ];
}
