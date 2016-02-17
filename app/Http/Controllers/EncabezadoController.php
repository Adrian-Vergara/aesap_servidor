<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Encabezado;
use App\Http\Controllers\imagen_encabezadoController;
use Illuminate\Support\Facades\Input;
use App\imagen_encabezado;

class EncabezadoController extends Controller
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

    public function store(Request $request, $id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($request->get('enunciado_encabezado')) && !empty($request->get('tipo_multimedia')))
            {
                try{
                    //$encabezado = Encabezado::create($request->only('enunciado_encabezado', 'tipo_multimedia'));
                    $encabezado = new Encabezado();
                    $encabezado->enunciado_encabezado = $request->get('enunciado_encabezado');
                    $encabezado->tipo_multimedia = $request->get('tipo_multimedia');
                    if($request->get('tipo_multimedia') == "imagen")
                    {
                        if(!empty($request->file('imagen')))
                        {
                            $encabezado->imagen = $request->file('imagen');
                        }
                        else
                        {
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'Error, verifique que los campos no esten vacios'
                            ]);
                        }
                    }
                    if($encabezado->save())
                    {
                        return response()->json([
                            'error' => false,
                            'mensaje' => 'registro existoso',
                            'encabezado' => $encabezado
                        ]);
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error al registrar el Encabezado, intente nuevamente'
                        ]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al registrar Encabezado',
                        'excepcion' => $e
                    ]);
                }
            }
            else{
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, verifique que los datos no esten vacios'
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

    public function index($id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            try{
                $encabezados = DB::table('encabezados')
                    ->where('estado', '=', 'activo')
                    ->get();
                if($encabezados)
                {
                    return response()->json([
                        'error' => false,
                        'encabezados' => $encabezados
                    ]);
                }
                else
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'no hay Encabezados registrados'
                    ]);
                }
            }catch (\Exception $e)
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error al consultar los Encabezados, intentelo nuevamente',
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

    public function filtro_multimedia($id_usuario, $multimedia)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($multimedia))
            {
                try{
                    $encabezados = DB::table('encabezados')
                        ->where('estado', '=', 'activo')
                        ->where('tipo_multimedia', '=', $multimedia)
                        ->get();
                    if($encabezados)
                    {
                        return response()->json([
                            'error' => false,
                            'encabezados' => $encabezados
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'no hay Encabezados registrados pertenecientes a esa multimedia'
                        ]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al consultar los Encabezados, intentelo nuevamente',
                        'excepcion' => $e
                    ]);
                }
            }
            else{
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Verifique que los campos no esten vacios'
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

    public function show($id_usuario, $id_encabezado)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($id_encabezado))
            {
                try{
                    $encabezado = Encabezado::find($id_encabezado);
                    if($encabezado && $encabezado->estado == "activo")
                    {
                        return response()->json([
                            'error' => false,
                            'encabezado' => $encabezado
                        ]);
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'El encabezado que desea consultar no existe'
                        ]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al consultar el encabezado, intentelo nuevamente',
                        'excepcion' => $e
                    ]);
                }
            }
            else{
                return response()->json([
                    'error' => true,
                    'mensaje' => 'verifique que el campo no este vacio'
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
            if(!empty($request->get('enunciado_encabezado')) && !empty($request->get('tipo_multimedia')) && !empty($request->get('id_encabezado')))
            {
                try{
                    $encabezado = Encabezado::find($request->get('id_encabezado'));
                    if($encabezado && $encabezado->estado == "activo")
                    {
                        $encabezado->enunciado_encabezado = $request->get('enunciado_encabezado');
                        $encabezado->tipo_multimedia = $request->get('tipo_multimedia');
                        if($request->get('tipo_multimedia') == "imagen") {
                            if (!empty($request->file('imagen')))
                            {
                                $encabezado->imagen = $request->file('imagen');
                            }
                            else{
                                return response()->json([
                                    'error' => true,
                                    'mensaje' => 'verifique que los datos no esten vacios'
                                ]);
                            }
                        }
                        if($encabezado->save())
                        {
                            return response()->json([
                                'error' => false,
                                'mensaje' => 'Se actualizaron los datos exitosamente',
                                'encabezado' => $encabezado
                            ]);
                        }
                        else{
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'Error, no se han actualizado los datos'
                            ]);
                        }
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'El encabezado que desea actualizar no existe'
                        ]);
                    }
                }catch(\Exception $e) {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error,',
                        'excepcion' => $e
                    ]);
                }
            }
            else {
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
}
