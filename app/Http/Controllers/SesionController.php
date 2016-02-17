<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Sesion;

class SesionController extends Controller
{
    public function registrar_sesion($id_usuario)
    {
        try
        {
            $sesion = new Sesion();
            $sesion->id_usuario = $id_usuario;
            if($sesion->save())
            {
                return true;
            }
            else{
                return false;
            }
        }
        catch(\Exception $e)
        {
            return false;
        }
    }
}
