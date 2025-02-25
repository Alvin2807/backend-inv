<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
    public    $table = "inv_colores";
    protected $primarykey = "id_color";
    protected $fillable = ['id_color','color'];
    public $timestamps   = false;
    public $incrementing = true;

    protected $casts = 
    [
        'id_color'=>'integer',
        'color'   =>'string'
    ];
}
