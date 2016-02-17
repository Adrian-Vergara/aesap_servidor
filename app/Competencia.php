<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    protected $table = 'competencias';
    protected $primaryKey = 'id_competencia';
    protected  $fillable = array('nombre_competencia', 'categoria', 'estado');
    public $timestamps = false;
}
