<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class programa_universidad extends Model
{
    protected $table = 'programa_universidad';
    protected  $fillable = array('id_universidad','id_programa', 'estado');
    public $timestamps = false;
}
