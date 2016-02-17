<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    protected $table = 'facultades';
    protected $primaryKey = 'id_facultad';
    protected  $fillable = array('nombre_facultad', 'estado');
    public $timestamps = false;
}
