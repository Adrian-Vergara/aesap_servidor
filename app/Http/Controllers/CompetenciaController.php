<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Competencia;
use App\User;
use Illuminate\Support\Facades\DB;

class CompetenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
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

    public function index($id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            $competencias = DB::table('competencias')
                ->where('estado', '=', 'activo')
                ->get();
            if($competencias)
            {
                return response()->json([
                    'error' => false,
                    'competencias' => $competencias
                ]);
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'no se encuentran Competencias Registradas'
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

    public function store(Request $request, $id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            try
            {
                if(!empty($request->get('nombre_competencia')) && !empty($request->get('categoria')))
                {
                    $competencia = Competencia::create($request->all());
                    if($competencia)
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Competencia almacenada exitosamente',
                            'competencia' => $competencia
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error al registrar la competencia'
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => "Error, verifique que los campos no esten vacios"
                    ]);
                }
            }
            catch(\Exception $e)
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al registrar la competencia',
                    'excepcion' => $e
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

    public function show($id_usuario, $id_competencia)
    {
        if ($this->usuario_activo($id_usuario, $id_competencia))
        {
            try
            {
                $competencia = Competencia::find($id_competencia);
                if ($competencia && $competencia->estado == "activo") {
                    return response()->json([
                        'error' => false,
                        'competencia' => $competencia
                    ]);
                } else {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error, la Competencia que desea consultar no existe'
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al consultar la competencia',
                    'excepcion' => $e
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

    public function update(Request $request, $id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            try
            {
                $competencia = Competencia::find($request->get('id_competencia'));
                if ($competencia && $competencia->estado == "activo") {
                    if(!empty($request->get('nombre_competencia')) && !empty($request->get('categoria')))
                    {
                        $competencia->nombre_competencia = $request->get('nombre_competencia');
                        $competencia->categoria = $request->get('categoria');
                        if ($competencia->save()) {
                            return response()->json([
                                'error' => false,
                                'mensaje' => 'Competencia Actualizada Exitosamente',
                                'competencia' => $competencia
                            ]);
                        }
                        else
                        {
                            return response()->json([
                                'error' => true,
                                'mensaje' => "Error al registrar Competencia"
                            ]);
                        }
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => "Error, verifique que los campos no esten vacios"
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => "Error, la Competencia que desea actualizar no existe"
                    ]);
                }
            }
            catch(\Exception $e)
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al actualizar la competencia',
                    'excepcion' => $e
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

    public function get_categoria($id_usuario, $categoria)
    {
        if($this->usuario_activo($id_usuario))
        {
            try {
                if(!empty($categoria)) {
                    $competencias = DB::table('competencias')
                        ->where('categoria', '=', $categoria)
                        ->where('estado', '=', 'activo')
                        ->get();
                    if($competencias) {
                        return response()->json([
                            'error' => false,
                            'competencias' => $competencias
                        ]);
                    } else {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'no hay Competencias pertenecientes a esa categoria'
                        ]);
                    }
                } else {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Verifique que el campo no este vacio'
                    ]);
                }
            }catch (\Exception $e) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al ejecutar consulta, intente nuevamente'
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

    public function cambiar_estado($id_usuario, $id_competencia)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($id_competencia))
            {
                $competencia = Competencia::find($id_competencia);
                if($competencia && $competencia->estado == "activo")
                {
                    $competencia->estado = "inactivo";
                    if($competencia->save())
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Competencia eliminada extiosamente'
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error al eliminar la competencia'
                        ]);
                    }
                }
                else{
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error, la Competencia que desea eliminar no existe'
                    ]);
                }
            }
            else{
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, verifique que haya seleccionado una competencia'
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
