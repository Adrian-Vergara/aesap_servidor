<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\competencia_programa;
use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Programa;
use App\Competencia;

class competencia_programaController extends Controller
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

    public function get_competencias($id_usuario, $id_programa)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($id_programa))
            {
                $programa = Programa::find($id_programa);
                if($programa && $programa->estado == 'activo')
                {
                    $competencias_programa = DB::table('competencia_programa')
                        ->join('programas', 'programas.id_programa', '=', 'competencia_programa.id_programa')
                        ->join('competencias', 'competencias.id_competencia', '=', 'competencia_programa.id_competencia')
                        ->where('programas.estado', '=', 'activo')
                        ->where('competencias.estado', '=', 'activo')
                        ->where('competencia_programa.id_programa', '=', $id_programa)
                        ->get();
                    if($competencias_programa)
                    {
                        return response()->json([
                            'error' => false,
                            'competencia_programa' => $competencias_programa
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, no hay competencias que pertenezcan a ese programa'
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error, el programa del cual quiere obtener las competencias no existe'
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, verifique que los campos no esten vacios'
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
            if(!empty($request->get('id_competencia')) && !empty($request->get('id_programa')))
            {
                $programa = Programa::find($request->get('id_programa'));
                if($programa && $programa->estado == 'activo')
                {
                    $competencia = Competencia::find($request->get('id_competencia'));
                    if($competencia && $competencia->estado == 'activo')
                    {
                        $competencia_programa = DB::table('competencia_programa')
                            ->insert([
                                'id_programa' => $programa->id_programa,
                                'id_competencia' => $competencia->id_competencia
                            ]);
                        if($competencia_programa)
                        {
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'Competencia asociada exitosamente',
                                'competencia_programa' => $competencia_programa
                            ]);
                        }
                        else{
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'Error al asociar la Competencia'
                            ]);
                        }
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, la Competencia que quiere asociar al programa no existe'
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error, el Programa al cual quiere asociar la Competencia no existe'
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, verifique que los campos no esten vacios'
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
        if($this->usuario_activo($id_usuario)) {
            if (!empty($request->get('id_competencia')) && !empty($request->get('id_programa')))
            {
                $programa = Programa::find($request->get('id_programa'));
                if($programa && $programa->estado == 'activo')
                {
                    $competencia = Competencia::find($request->get('id_competencia'));
                    if($competencia && $competencia->estado == 'activo')
                    {
                        $competencia_programa = DB::table('competencia_programa')
                            ->update([
                                'id_programa' => $programa->id_programa,
                                'id_competencia' => $competencia->id_competencia
                            ]);
                        if($competencia_programa)
                        {
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'Datos actualizados exitosamente',
                                'competencia_programa' => $competencia_programa
                            ]);
                        }
                        else{
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'Error al actualizar los datos'
                            ]);
                        }
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, la Competencia a la cual quiere asociar el programa no existe'
                        ]);
                    }
                }
                else
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error, el Programa al cual quiere asociar la Competencia no existe'
                    ]);
                }
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
