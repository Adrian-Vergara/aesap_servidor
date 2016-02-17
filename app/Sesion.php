<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    protected $table = 'sesiones';
    protected $primaryKey = 'id_sesion';
    protected  $fillable = array('id_usuario');
    public $timestamps = false;
}
