<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Facultad;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\User;

class FacultadController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth');
    }

    public function index($id_usuario)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if($usuario && $usuario->estado == "activo")
            {
                $facultades = DB::table('facultades')
                    ->where('estado', '=', 'activo')
                    ->get();
                if($facultades)
                {
                    return response()->json([
                        'error' => false,
                        'Facultad' => $facultades
                    ]);
                }

                return response()->json([
                    'error' => true,
                    'mensaje' => 'No hay Facultades registradas'
                ]);
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El usuario que desea hacer la petición no se encuentra Registrado'
                ]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del Usuario que realiza la peticion',
                'excepcion' => $e
            ]);
        }
    }

    public function store(Request $request, $id_usuario)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                if(empty($request->get('nombre_facultad')))
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'EL nombre de la Facultad esta vacio, verifique los datos'
                    ]);
                }
                else
                {
                    $facultad = Facultad::create($request->all());
                    if($facultad)
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Se registraron los datos Exitosamente',
                            'Facultad' => $facultad
                        ]);
                    }
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al registrar los datos'
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El usuario que desea hacer la petición no se encuentra Registrado'
                ]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del Usuario que realiza la peticion',
                'excepcion' => $e
            ]);
        }
    }

    public function show($id_usuario, $id_facultad)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $facultad = Facultad::find($id_facultad);
                if($facultad && $facultad->estado == "activo")
                {
                    return response()->json([
                        'error' => false,
                        'facultad' => $facultad
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, la Facultad que desea consultar no existe'
                ]);
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El usuario que desea hacer la petición no se encuentra Registrado'
                ]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del Usuario que realiza la peticion',
                'excepcion' => $e
            ]);
        }
    }

    public function update(Request $request, $id_usuario)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $facultad = Facultad::find($request->get('id_facultad'));
                if($facultad && $facultad->estado == "activo")
                {
                    $facultad->nombre_facultad = $request->get('nombre_facultad');
                    if($facultad->save())
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Datos actualizados exitosamente',
                            'facultad' => $facultad
                        ]);
                    }
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, la facultad que desea actualizar no existe'
                ]);
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El usuario que desea hacer la petición no se encuentra Registrado'
                ]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del Usuario que realiza la peticion',
                'excepcion' => $e
            ]);
        }
    }

    public function cambiar_estado($id_usuario, $id_facultad)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $facultad = Facultad::find($id_facultad);
                if($facultad && $facultad->estado == "activo")
                {
                    $facultad->estado = "inactivo";
                    if($facultad->save())
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Facultad eliminada exitosamente'
                        ]);
                    }
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al eliminar la facultad'
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, la facultad que desea eliminar no existe'
                ]);
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El usuario que desea hacer la petición no se encuentra Registrado'
                ]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del Usuario que realiza la peticion',
                'excepcion' => $e
            ]);
        }
    }
}
