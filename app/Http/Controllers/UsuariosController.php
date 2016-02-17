<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\AuthenticateController;

class UsuariosController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['store']]);
    }

    public function index()
    {
        try
        {
            $usuarios = DB::table('usuarios')
                ->join('programas', 'programas.id_programa', '=', 'usuarios.id_programa')
                ->join('universidades', 'universidades.id_universidad', '=', 'usuarios.id_universidad')
                ->where('universidades.estado', '=', 'activo')
                ->where('programas.estado', '=', 'activo')
                ->where('usuarios.estado', '=', 'activo')
                ->where('usuarios.rol', '=', 'estudiante')
                ->select('usuarios.id_usuario', 'usuarios.primer_nombre', 'usuarios.primer_apellido', 'usuarios.email', 'usuarios.tipo_identificacion', 'usuarios.identificacion', 'usuarios.sexo', 'usuarios.fecha_nacimiento', 'programas.nombre_programa', 'universidades.nombre_universidad')
                ->get();
            if($usuarios)
            {
                return response()->json([
                    'error'   => false,
                    'usuarios' => $usuarios
                ]);
            }
            return response()->json([
                'error' => true,
                'mensaje' => 'No hay Usuarios registrados'
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al consultar Usuarios',
                'excepcion' => $e
            ]);
        }
    }

    private function validar_email($email, $id_usuario)
    {
        $usuario = DB::table('usuarios')
            ->where('email', '=', $email)
            ->where('id_usuario', '<>', $id_usuario)
            ->get();
        if($usuario)
        {
            return true;
        }
        return false;
    }

    private function validar_identificacion($identificacion, $id_usuario)
    {
        $usuario = DB::table('usuarios')
            ->where('identificacion', '=', $identificacion)
            ->where('id_usuario', '<>', $id_usuario)
            ->get();
        if($usuario)
        {
            return true;
        }
        return false;
    }

    public function store(Request $request)
    {
        if(empty($request->get('primer_nombre'))  || empty($request->get('primer_apellido'))  || empty($request->get('email'))  || empty($request->get('password'))  || empty($request->get('id_universidad'))  || empty($request->get('id_programa')))
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error, Verifique que los datos no esten vacios'
            ]);
        }
        else
        {
            try
            {
                $programa_universidad = DB::table('programa_universidad')
                    ->join('programas', 'programas.id_programa', '=', 'programa_universidad.id_programa')
                    ->join('universidades', 'universidades.id_universidad', '=', 'programa_universidad.id_universidad')
                    ->where('programa_universidad.id_programa', '=', $request->get('id_programa'))
                    ->where('programa_universidad.id_universidad', '=', $request->get('id_universidad'))
                    ->where('universidades.estado', '=', 'activo')
                    ->where('programas.estado', '=', 'activo')
                    ->select('programa_universidad.id_universidad', 'programa_universidad.id_programa')
                    ->get();
                if($programa_universidad)
                {
                    $user = $request->only('primer_nombre', 'primer_apellido', 'email', 'password', 'id_universidad', 'id_programa');
                    if($this->validar_email($user['email'], 0))
                    {
                       return response()->json([
                           'error' => true,
                           'mensaje' => 'El Email ya se encuentra registrado'
                       ]);
                    }
                    else
                    {
                        $user['password'] = Hash::make($user['password']);
                        $user['rol'] = 'estudiante';
                        $usuario = User::create($user);
                        if($usuario)
                        {
                            $token = JWTAuth::fromUser($usuario);
                            return response()->json(
                                [
                                    'error'   => false,
                                    'mensaje' => 'Usuario Almacenado Exitosamente',
                                    'usuario' => $usuario,
                                    'token' => $token
                                ]
                            );
                        }
                        else
                        {
                            return response()->json([
                                'error' => false,
                                'mensaje' => 'Error al registrar Usuario'
                            ]);
                        }
                    }
                }
                else
                {
                    return response()->json([
                        'error' => false,
                        'mensaje' => 'Error al registrar Usuario, verifique la universidad y programa al cual quiere asociarse'
                    ]);
                }
            }
            catch(\Exception $e)
            {
                return response()->json([
                    'error' => false,
                    'mensaje' => 'Error al registrar Usuario',
                    'excepcion' => $e
                ]);
            }
        }
    }

    public function show($id_usuario)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if($usuario && $usuario->estado == "activo")
            {
                return response()->json([
                    'error' => false,
                    'usuario' => $usuario
                ]);
            }
            return response()->json([
                'error'   => true,
                'mensaje' => 'Usuario no registrado'
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => false,
                'mensaje' => 'Error al consultar Usuario',
                'excepcion' => $e
            ]);
        }
    }

    public function update(Request $request, $id_usuario)
    {
        $usuario = User::find($id_usuario);
        if($usuario && $usuario->estado == "activo")
        {
            try
            {
                $programa_universidad = DB::table('programa_universidad')
                    ->join('programas', 'programas.id_programa', '=', 'programa_universidad.id_programa')
                    ->join('universidades', 'universidades.id_universidad', '=', 'programa_universidad.id_universidad')
                    ->where('programa_universidad.id_programa', '=', $request->get('id_programa'))
                    ->where('programa_universidad.id_universidad', '=', $request->get('id_universidad'))
                    ->where('universidades.estado', '=', 'activo')
                    ->where('programas.estado', '=', 'activo')
                    ->select('programa_universidad.id_universidad', 'programa_universidad.id_programa')
                    ->get();
                if($programa_universidad)
                {
                    if($this->validar_email($request->get('email'), $id_usuario) && $this->validar_identificacion($request->get('identificacion', $id_usuario)))
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, el Email y la Identificacion ya existen'
                        ]);
                    }
                    if($this->validar_email($request->get('email'), $id_usuario) && !$this->validar_identificacion($request->get('identificacion', $id_usuario)))
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, el Email ya existen'
                        ]);
                    }
                    if(!$this->validar_email($request->get('email'), $id_usuario) && $this->validar_identificacion($request->get('identificacion', $id_usuario)))
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, la Identificacion ya existe'
                        ]);
                    }
                    if(!$this->validar_email($request->get('email'), $id_usuario) && !$this->validar_identificacion($request->get('identificacion', $id_usuario)))
                    {
                        $usuario->primer_nombre = $request->get('primer_nombre');
                        $usuario->segundo_nombre = $request->get('segundo_nombre');
                        $usuario->primer_apellido = $request->get('primer_apellido');
                        $usuario->segundo_apellido = $request->get('segundo_apellido');
                        $usuario->email = $request->get('email');
                        $usuario->tipo_identificacion = $request->get('tipo_identificacion');
                        $usuario->identificacion = $request->get('identificacion');
                        $usuario->sexo = $request->get('sexo');
                        $usuario->fecha_nacimiento = $request->get('fecha_nacimiento');
                        $usuario->id_programa = $request->get('id_programa');
                        $usuario->id_universidad = $request->get('id_universidad');
                        if($usuario->save())
                        {
                            return response()->json(
                                [
                                    'error'   => false,
                                    'mensaje' => 'Usuario Actualizado Exitosamente',
                                    'usuario' => $usuario
                                ]
                            );
                        }
                        else
                        {
                            return response()->json([
                                'error' => false,
                                'mensaje' => 'Error al Actualizar Usuario'
                            ]);
                        }
                    }
                }
                else
                {
                    return response()->json([
                        'error' => false,
                        'mensaje' => 'Error al actualizar Usuario, verifique la universidad y programa al cual quiere asociarse'
                    ]);
                }
            }
            catch(\Exception $e)
            {
                return response()->json([
                    'error' => false,
                    'mensaje' => 'Error al actualizar Usuario',
                    'excepcion' => $e
                ]);
            }
        }
        return response()->json([
            'error' => true,
            'mensaje' => 'Error, Usuario no registrado'
        ]);
    }

    public function GetPruebaToken($id_usuario)
    {
        $usuario = User::find($id_usuario);
        if($usuario && $usuario->estado == "activo")
        {
            if ($usuario = JWTAuth::parseToken()->authenticate())
            {
                $user = compact('usuario');
                if($user)
                {
                    foreach($user as $t){
                        $id_user = $t->id_usuario;
                        $estado_user = $t->estado;
                    }
                    if($id_user == $id_usuario && $estado_user == "activo")
                    {

                        return response()->json([
                            'error' => false,
                            'mensaje' => 'Token Coincide con Usuario',
                            'user' => $user,
                            'usuario' => $usuario
                        ]);
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Token no Coincide con Usuario',
                            'user' => $user,
                            'usuario' => $usuario
                        ]);
                    }
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Token no coincide con Usuario'
                ]);
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, el Token no pertenece al usuario'
                ]);
            }
        }
        else
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error, el usuario no existe'
            ]);
        }
        /*try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $user = compact('user');
        if($user)
        {
            foreach($user as $t){
                $id_user = $t->id_usuario;
            }
            $usuario = User::find($id_usuario);
            if($id_user == $id_usuario)
            {
                return response()->json([
                    'error' => false,
                    'mensaje' => 'Token Coincide con Usuario',
                    'user' => $user,
                    'usuario' => $usuario
                ]);
            }
            return response()->json([
                'error' => true,
                'mensaje' => 'Token no Coincide con Usuario',
                'user' => $user,
                'usuario' => $usuario
            ]);
        }*/
    }
}