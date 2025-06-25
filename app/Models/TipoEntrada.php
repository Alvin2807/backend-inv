<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEntrada extends Model
{
    use HasFactory;
    public $table = 'inv_tipos_entradas';
    protected $primarykey = 'id_tipo_entrada';
    protected $fillable = ['id_tipo_entrada','tipo_entrada','fk_despacho'];
    public $increment = true;
    public $timestamps = false;

    protected $casts = [
        'id_tipo_entrada'=> 'integer',
        'fk_despacho'=>'integer'
    ];
}
