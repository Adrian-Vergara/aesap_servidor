<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $table = 'programas';
    protected $primaryKey = 'id_programa';
    protected  $fillable = array('nombre_programa','estado');
    public $timestamps = false;
}
