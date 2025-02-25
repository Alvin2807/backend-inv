<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaPerfil extends Model
{
    use HasFactory;

    public    $table = "vista_perfil_usuario";
    protected $fillable  = ['name','apellido','usuario','email','fk_rol','despacho','rol'];
    protected $casts = 
    [
        'fk_rol' =>'integer'
    ];
}
