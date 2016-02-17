<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class competencia_programa extends Model
{
    protected $table = 'competencia_programa';
    protected  $fillable = array('id_competencia','id_programa', 'estado');
    public $timestamps = false;
}
