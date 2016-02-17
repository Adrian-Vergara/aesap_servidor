<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pregunta;
use App\Competencia;
use App\Encabezado;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Http\Controllers\OpcionController;

class PreguntaController extends Controller
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

    public function index($id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            try{
                $preguntas = DB::table('preguntas')
                    ->where('estado', '=', 'activo')
                    ->get();
                if($preguntas)
                {
                    return response()->json([
                        'error' => false,
                        'preguntas' => $preguntas
                    ]);
                }
                else{
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'no hay preguntas registradas'
                    ]);
                }
            }catch (\Exception $e)
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error,',
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

    public function filtro_competencia($id_usuario, $id_competencia)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($id_competencia))
            {
                try{
                    $competencia = Competencia::find($id_competencia);
                    if($competencia && $competencia->estado == "activo")
                    {
                        $preguntas = DB::table('preguntas')
                            ->join('competencias', 'competencias.id_competencia', '=', 'preguntas.id_competencia')
                            ->join('encabezados', 'encabezados.id_encabezado', '=', 'preguntas.id_encabezado')
                            ->where('competencias.estado', '=', 'activo')
                            ->where('encabezados.estado', '=', 'activo')
                            ->where('preguntas.id_competencia', '=', $id_competencia)
                            ->where('preguntas.estado', '=', 'activo')
                            ->select('encabezados.id_encabezado', 'encabezados.enunciado_encabezado', 'encabezados.tipo_multimedia', 'encabezados.imagen', 'preguntas.id_pregunta', 'preguntas.enunciado_pregunta', 'preguntas.tipo_multimedia as multimedia_pregunta', 'preguntas.tipo_explicacion', 'preguntas.explicacion', 'preguntas.imagen as imagen_pregunta', 'competencias.id_competencia', 'competencias.nombre_competencia', 'competencias.categoria')
                            //->select('preguntas.*', 'competencias.nombre_competencia', 'competencias.categoria', 'encabezados.enunciado_encabezado', 'encabezados.tipo_multimedia', 'encabezados.imagen')
                            ->get();
                        if($preguntas)
                        {
                            return response()->json([
                                'error' => false,
                                'preguntas' => $preguntas
                            ]);
                        }
                        else{
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'No hay Preguntas registradas pertenecientes a esa Competencia'
                            ]);
                        }
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'La Competencia de la cual quiere hacer el filtro de Preguntas no existe'
                        ]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error,',
                        'excepcion' => $e
                    ]);
                }
            }
            else{
                return response()->json([
                    'error' => true,
                    'mensaje' => 'verifique que los datos no esten vacios'
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

    public function show($id_usuario, $id_pregunta)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($id_pregunta))
            {
                try{
                    $pregunta = DB::table('preguntas')
                        ->join('competencias', 'competencias.id_competencia', '=', 'preguntas.id_competencia')
                        ->join('encabezados', 'encabezados.id_encabezado', '=', 'preguntas.id_encabezado')
                        ->where('competencias.estado', '=', 'activo')
                        ->where('encabezados.estado', '=', 'activo')
                        ->where('preguntas.estado', '=', 'activo')
                        ->where('preguntas.id_pregunta', '=', $id_pregunta)
                        ->select('encabezados.id_encabezado', 'encabezados.enunciado_encabezado', 'encabezados.tipo_multimedia', 'encabezados.imagen', 'preguntas.id_pregunta', 'preguntas.enunciado_pregunta', 'preguntas.tipo_multimedia as multimedia_pregunta', 'preguntas.tipo_explicacion', 'preguntas.explicacion', 'preguntas.imagen as imagen_pregunta', 'competencias.id_competencia', 'competencias.nombre_competencia', 'competencias.categoria')
                        ->get();

                    $opciones = DB::table('opciones')
                        ->join('preguntas', 'preguntas.id_pregunta', '=', 'opciones.id_pregunta')
                        ->where('preguntas.estado', '=', 'activo')
                        ->where('opciones.estado', '=', 'activo')
                        ->where('opciones.id_pregunta', '=', $id_pregunta)
                        ->select('opciones.id_opcion', 'opciones.enunciado_opcion', 'opciones.tipo_multimedia as multimedia_opcion', 'opciones.validez')
                        ->get();
                    if($pregunta)
                    {
                        return response()->json([
                            'error' => false,
                            'pregunta' => $pregunta,
                            'opciones' => $opciones
                        ]);
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'error al consultar la Pregunta, intentelo nuevamente'
                        ]);
                    }

                }catch (\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al consultar Pregunta intentelo nuevamente',
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

    public function store(Request $request, $id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($request->get('id_encabezado')) && !empty($request->get('id_competencia')) && !empty($request->get('enunciado_pregunta')) && !empty($request->get('tipo_multimedia')) && !empty($request->get('tipo_explicacion')) && !empty($request->get('opciones')))
            {
                try{
                    $encabezado = Encabezado::find($request->get('id_encabezado'));
                    if($encabezado && $encabezado->estado == "activo")
                    {
                        $competencia = Competencia::find($request->get('id_competencia'));
                        if($competencia && $competencia->estado == "activo")
                        {
                            $pregunta = new Pregunta();
                            $pregunta->id_encabezado = $request->get('id_encabezado');
                            $pregunta->id_competencia = $request->get('id_competencia');
                            $pregunta->enunciado_pregunta = $request->get('enunciado_pregunta');
                            $pregunta->tipo_multimedia = $request->get('tipo_multimedia');
                            $pregunta->explicacion = $request->get('explicacion');
                            $pregunta->tipo_explicacion = $request->get('tipo_explicacion');
                            if($pregunta->tipo_multimedia == "imagen")
                            {
                                if(!empty($request->file('imagen')))
                                {
                                    $pregunta->imagen = $request->file('imagen');
                                }
                                else{
                                    return response()->json([
                                        'error' => true,
                                        'mensaje' => 'verifique que los campos no esten vacios'
                                    ]);
                                }
                            }
                            if($pregunta->save())
                            {
                                $opcionController = new OpcionController();
                                $bandera = true;
                                foreach($request->get('opciones') as $t){
                                    $bandera = $opcionController->registrar_opcion($pregunta->id_pregunta, $t['enunciado_opcion'], $t['tipo_multimedia'], $t['validez']);
                                }

                                if($bandera)
                                {
                                    return response()->json([
                                        'error' => false,
                                        'mensaje' => 'Datos Registrados Exitosamente',
                                        'pregunta' => $pregunta
                                    ]);
                                }
                                else{
                                    return response()->json([
                                        'error' => true,
                                        'mensaje' => 'Error en el registro de las Opciones'
                                    ]);
                                }
                            }
                            else{
                                return response()->json([
                                    'error' => true,
                                    'mensaje' => 'Error al registrar los datos, intente nuevamente'
                                ]);
                            }
                        }
                        else{
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'La Competencia a la cual desea asociar la pregunta no existe'
                            ]);
                        }
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'El Encabezado al cual desea asociar la pregunta no existe'
                        ]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al registrar datos, intente nuevamente',
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

    public function update(Request $request, $id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($request->get('id_pregunta')) && !empty($request->get('id_encabezado')) && !empty($request->get('id_competencia')) && !empty($request->get('enunciado_pregunta')) && !empty($request->get('tipo_multimedia')) && !empty($request->get('tipo_explicacion')) && !empty($request->get('opciones')))
            {
                try{
                    $pregunta = Pregunta::find($request->get('id_pregunta'));
                    if($pregunta && $pregunta->estado == "activo")
                    {
                        $encabezado = Encabezado::find($request->get('id_encabezado'));
                        if($encabezado && $encabezado->estado == "activo")
                        {
                            $competencia = Competencia::find($request->get('id_competencia'));
                            if($competencia && $competencia->estado == "activo")
                            {
                                $pregunta->id_encabezado = $request->get('id_encabezado');
                                $pregunta->id_competencia = $request->get('id_competencia');
                                $pregunta->enunciado_pregunta = $request->get('enunciado_pregunta');
                                $pregunta->tipo_multimedia = $request->get('tipo_multimedia');
                                $pregunta->tipo_explicacion = $request->get('tipo_explicacion');
                                $pregunta->explicacion = $request->get('explicacion');
                                if($request->get('tipo_multimedia') == "imagen")
                                {
                                    if(!empty($request->file('imagen')))
                                    {
                                        $pregunta->imagen = $request->file('imagen');
                                    }
                                    else{
                                        return response()->json([
                                            'error' => true,
                                            'mensaje' => 'verifique que los campos no esten vacios'
                                        ]);
                                    }
                                }
                                if($pregunta->save())
                                {
                                    $opcionController = new OpcionController();
                                    $bandera = true;
                                    foreach($request->get('opciones') as $t){
                                        $bandera = $opcionController->actualizar_opcion($t['id_opcion'], $pregunta->id_pregunta, $t['enunciado_opcion'], $t['tipo_multimedia'], $t['validez']);
                                    }
                                    if($bandera)
                                    {
                                        return response()->json([
                                            'error' => false,
                                            'mensaje' => 'Pregunta actualizada exitosamente',
                                            'pregunta' => $pregunta
                                        ]);
                                    }
                                    else{
                                        return response()->json([
                                            'error' => true,
                                            'mensaje' => 'Error al actualizar opciones, intente nuevamente'
                                        ]);
                                    }
                                }
                                else{
                                    return response()->json([
                                        'error' => false,
                                        'mensaje' => 'Error al actualizar los datos, intente nuevamente'
                                    ]);
                                }
                            }
                            else{
                                return response()->json([
                                    'error' => true,
                                    'mensaje' => 'La Competencia a la cual desea asociar la pregunta no existe'
                                ]);
                            }
                        }
                        else{
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'El Encabezado al cual desea asociar la pregunta no existe'
                            ]);
                        }
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'La Pregunta que desea actualizar no existe'
                        ]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al actualizar los datos, intente nuevamente',
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
