<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected  $fillable = array('primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'email', 'password', 'tipo_identificacion', 'identificacion', 'sexo'. 'fecha_nacimiento', 'estado', 'rol', 'id_programa', 'id_universidad');
    public $timestamps = false;
}
