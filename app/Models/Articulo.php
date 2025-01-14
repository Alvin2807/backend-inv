<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    public    $table      = "inv_articulos";
    protected $primarykey = "id_articulo";
    protected $fillable   = ['id_articulo','fk_marca','fk_modelo','fk_categoria','fk_color','stock','cantidad_pedida','estado'];
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $casts = 
    [
        'id_articulo' =>'integer',
        'fk_marca'    =>'integer',
        'fk_modelo'   =>'integer',
        'fk_categoria' =>'integer',
        'fk_color'     =>'integer',
        'stock'        =>'integer',
        'cantidad_pedida' =>'integer'
    ];
}
