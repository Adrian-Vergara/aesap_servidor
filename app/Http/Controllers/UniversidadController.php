<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Universidad;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\programa_universidadController;

class UniversidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['index']]);
    }

    public function index()
    {
        try
        {
            $universidades = DB::table('universidades')
                ->where('estado', '=', 'activo')
                ->get();
            if($universidades)
            {
                return response()->json([
                    'error' => false,
                    'universidades' => $universidades
                ]);
            }
            return response()->json([
                'error' => true,
                'mensaje' => 'no se encuentran universidades registradas'
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del Usuario que realiza la peticion',
                'mensaje' => 'Error al consultar las universidades',
                'excepcion' => $e
            ]);
        }
    }

    public function show($id_usuario, $id_universidad)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $universidad = Universidad::find($id_universidad);
                if($universidad && $universidad->estado == "activo")
                {
                    return response()->json([
                        'error' => false,
                        'universidad' => $universidad
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'La universidad que desea consultar no se encuentra registrada'
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
                if(empty($request->get('nombre_universidad')))
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error, veirifique que los datos no esten vacios'
                    ]);
                }
                else
                {
                    $universidad = Universidad::create($request->all());
                    if($universidad)
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'datos registrados exitosamente',
                            'universidad' => $universidad
                        ]);
                    }
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al registrar universidad, verifique los datos'
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

    public function update(Request $request, $id_usuario)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $universidad = Universidad::find($request->get('id_universidad'));
                if($universidad && $universidad->estado == "activo")
                {
                    if(empty($request->get('nombre_universidad')))
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, veirifique que los datos no esten vacios'
                        ]);
                    }
                    else
                    {
                        $universidad->nombre_universidad = $request->get('nombre_universidad');
                        if($universidad->save())
                        {
                            return response()->json([
                                'error' => false,
                                'mensaje' => 'Universidad actualizada exitosamente',
                                'universidad' => $universidad
                            ]);
                        }
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Error al actualizar Universidad'
                        ]);
                    }
                }
                return response()->json([
                    'error' => false,
                    'mensaje' => 'la Universidad que desea actualizar no existe'
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

    public function cambiar_estado($id_usuario, $id_universidad)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $universidad = Universidad::find($id_universidad);
                if($universidad && $universidad->estado == "activo")
                {
                    $universidad->estado = 'inactivo';
                    $programa_universidad = new programa_universidadController();
                    if($universidad->save() && $programa_universidad->cambiar_estado_universidad_inactiva($id_universidad))
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Universidad eliminada exitosamente'
                        ]);
                    }
                    return response()->json([
                        'error' => false,
                        'mensaje' => 'Error al eliminar Universidad'
                    ]);
                }
                return response()->json([
                    'error' => false,
                    'mensaje' => 'la Universidad que desea eliminar no existe'
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
