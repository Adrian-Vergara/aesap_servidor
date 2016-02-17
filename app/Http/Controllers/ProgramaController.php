<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Programa;
use App\Facultad;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\programa_universidadController;
use App\User;

class ProgramaController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['index']]);
    }

    public function index()
    {
        try {
            $programas = DB::table('programas')
                ->join('facultades', 'facultades.id_facultad', '=', 'programas.id_facultad')
                ->where('facultades.estado', '=', 'activo')
                ->where('programas.estado', '=', 'activo')
                ->select('programas.id_programa', 'programas.id_facultad', 'programas.nombre_programa', 'facultades.nombre_facultad')
                ->get();
            if ($programas) {
                return response()->json([
                    'error' => false,
                    'Programas' => $programas
                ]);
            }
            else{
                return response()->json([
                    'error' => true,
                    'mensaje' => 'No hay Programas registrados'
                ]);
            }
        }catch(\Exception $e){
            return response()->json([
                'error' => true,
                'mensaje' => 'error al ejecutar consulta'
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
                $id_facultad = $request->get('id_facultad');
                $facultad = Facultad::find($id_facultad);
                if($facultad->estado == "activo")
                {
                    $programa = new Programa();
                    $programa->nombre_programa = $request->get('nombre_programa');
                    $programa->id_facultad = $facultad->id_facultad;
                    if($programa->save())
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Se registraron los datos Exitosamente',
                            'programa' => $programa
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error al registrar los datos'
                        ]);
                    }
                }

                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al registrar los datos, la facultad a la cual quiere asociar el programa no existe!'
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

    public function show($id_usuario, $id_facultad)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $facultad = DB::table('facultades')
                    ->where('id_facultad', '=', $id_facultad)
                    ->where('estado', '=', 'activo')
                    ->get();
                if($facultad)
                {
                    $programas = DB::table('programas')
                        ->join('facultades', 'facultades.id_facultad', '=', 'programas.id_facultad')
                        ->where('facultades.estado', '=', 'activo')
                        ->where('programas.id_facultad', '=', $id_facultad)
                        ->where('programas.estado', '=', 'activo')
                        ->select('programas.id_programa', 'programas.id_facultad', 'programas.nombre_programa', 'facultades.nombre_facultad')
                        ->get();
                    if($programas)
                    {
                        return response()->json([
                            'error' => false,
                            'programas' => $programas
                        ]);
                    }
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'no hay programas pertenecientes a la facultad'
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'La facultad no existe'
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
                $programa = Programa::find($request->get('id_programa'));
                if($facultad->estado == "activo")
                {
                    if($programa->estado == "activo")
                    {
                        $programa->id_facultad = $facultad->id_facultad;
                        $programa->nombre_programa = $request->get('nombre_programa');
                        if($programa->save())
                        {
                            return response()->json([
                                'error' => false,
                                'mensaje' => 'Datos actualizados exitosamente',
                                'programa' => $programa
                            ]);
                        }
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, el Programa que desea actualizar no existe'
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error, la facultad a la cual quiere asociar el programa no existe'
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

    public function get_programa($id_programa)
    {
        $programa = Programa::find($id_programa);
        if($programa->estado == "activo")
        {
            return response()->json([
                'error' => false,
                'programa' => $programa
            ]);
        }
        return response()->json([
            'error' => true,
            'mensaje' => 'El programa que desea consultar no existe'
        ]);
    }

    public function cambiar_estado($id_usuario, $id_programa)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $programa = Programa::find($id_programa);
                if($programa->estado == "activo")
                {
                    $programa->estado = "inactivo";
                    $programa_universidad = new programa_universidadController();
                    if($programa->save() && $programa_universidad->cambiar_estado_programa_inactivo($id_programa))
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Programa eliminado exitosamente'
                        ]);
                    }
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al eliminar el Programa'
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, el Programa que desea eliminar no existe'
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
