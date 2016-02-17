<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Pregunta extends Model
{
    protected $table = 'preguntas';
    protected $primaryKey = 'id_pregunta';
    protected  $fillable = array('id_encabezado', 'id_competencia', 'enunciado_pregunta', 'tipo_multimedia', 'tipo_explicacion', 'explicacion', 'estado', 'imagen');
    public $timestamps = false;

    public function setImagenAttribute($imagen){
        $this->attributes['imagen'] = Carbon::now()->hour.Carbon::now()->minute.Carbon::now()->second.$imagen->getClientOriginalName();
        $name = Carbon::now()->hour.Carbon::now()->minute.Carbon::now()->second.$imagen->getClientOriginalName();
        Storage::disk('preguntas')->put($name, \File::get($imagen));
    }
}
