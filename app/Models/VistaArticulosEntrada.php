<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaArticulosEntrada extends Model
{
    use HasFactory;

    public    $table = "vista_articulos_disponibles_entrada";
    protected $fillable = ['id_articulo','codigo','marca','modelo','color','categoria','referencia'];
    protected $casts = [
        'id_articulo' => 'integer'
    ];
}
