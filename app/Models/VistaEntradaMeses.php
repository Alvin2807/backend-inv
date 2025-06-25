<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VistaEntradaMeses extends Model
{
    use HasFactory;
    public $table = 'vista_meses_entrada';
    protected $fillable = ['mes'];
}
