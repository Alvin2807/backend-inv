<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSolicitud extends Model
{
    use HasFactory;
    public    $table = ' inv_tipo_solicitud';
    protected $primarykey = 'id_tipo_solicitud';
    protected $fillable = ['id_tipo_solicitud','tipo_solicitud'];
    public $incrementing = true;
    public $timestamps   = false;

    protected $casts =
    [
        'id_tipo_solicitud' =>'integer'
    ];

}
