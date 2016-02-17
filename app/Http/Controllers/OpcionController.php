<?php

namespace App\Http\Controllers;

use App\Opcion;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Pregunta;

class OpcionController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth');
    }

    private function usuario_activo($id_usuario)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $this->usuario = $usuario;
                return true;
            }
            else
            {
                return false;
            }
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    public function registrar_opcion($id_pregunta, $enunciado_opcion, $tipo_multimedia, $validez)
    {
        try{
            $opcion = new Opcion();
            $opcion->id_pregunta = $id_pregunta;
            $opcion->enunciado_opcion = $enunciado_opcion;
            $opcion->tipo_multimedia = $tipo_multimedia;
            $opcion->validez = $validez;
            if($opcion->save())
            {
                return true;
            }
            else {
                return false;
            }
        }catch (\Exception $e)
        {
            return false;
        }
    }

    public function actualizar_opcion($id_opcion, $id_pregunta, $enunciado_opcion, $tipo_multimedia, $validez)
    {
        try{
            $opcion = Opcion::find($id_opcion);
            if($opcion && $opcion->estado == "activo")
            {
                $opcion->id_pregunta = $id_pregunta;
                $opcion->enunciado_opcion = $enunciado_opcion;
                $opcion->tipo_multimedia = $tipo_multimedia;
                $opcion->validez = $validez;
                if($opcion->save())
                {
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }catch (\Exception $e)
        {
            return false;
        }
    }

    public function show($id_usuario, $id_opcion)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($id_opcion))
            {
                try{
                    $opcion = Opcion::find($id_opcion);
                    if($opcion && $opcion->estado == "activo")
                    {
                        return response()->json([
                            'error' => false,
                            'opcion' => $opcion
                        ]);
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, la opcion que desea consultar no existe'
                        ]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al ejecutar la consulta',
                        'excepcion' => $e
                    ]);
                }
            }
            else{
                return response()->json([
                    'error' => true,
                    'mensaje' => 'verifique que los campos no esten vacios'
                ]);
            }
        }
        else
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del usuario que hace la peticion'
            ]);
        }
    }
}
