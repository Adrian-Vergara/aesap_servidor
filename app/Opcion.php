<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\DB;


class Opcion extends Model
{
    protected $table = 'opciones';
    protected $primaryKey = 'id_opcion';
    protected  $fillable = array('id_pregunta', 'enunciado_opcion', 'tipo_multimedia', 'validez', 'estado', 'imagen');
    public $timestamps = false;

    public function setImagenAttribute($imagen){
        $this->attributes['imagen'] = Carbon::now()->hour.Carbon::now()->minute.Carbon::now()->second.$imagen->getClientOriginalName();
        $name = Carbon::now()->hour.Carbon::now()->minute.Carbon::now()->second.$imagen->getClientOriginalName();
        Storage::disk('opciones')->put($name, \File::get($imagen));
    }
}
