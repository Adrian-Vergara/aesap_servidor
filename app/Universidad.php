<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Universidad extends Model
{
    protected $table = 'universidades';
    protected $primaryKey = 'id_universidad';
    protected  $fillable = array('nombre_universidad','estado');
    public $timestamps = false;
}
