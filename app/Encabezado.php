<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Encabezado extends Model
{
    protected $table = 'encabezados';
    protected $primaryKey = 'id_encabezado';
    protected  $fillable = array('enunciado_encabezado', 'tipo_multimedia', 'estado', 'imagen');
    public $timestamps = false;

    public function setImagenAttribute($imagen){
        $this->attributes['imagen'] = Carbon::now()->hour.Carbon::now()->minute.Carbon::now()->second.$imagen->getClientOriginalName();
        $name = Carbon::now()->hour.Carbon::now()->minute.Carbon::now()->second.$imagen->getClientOriginalName();
        Storage::disk('encabezados')->put($name, \File::get($imagen));
    }
}
