<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Sesion;
use App\Http\Controllers\SesionController;

class AuthenticateController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    public function index()
    {
        $users = User::all();
        if($users)
        {
            return response()->json(['usuarios' => $users]);
        }
    }

    public function authenticate(Request $request)
    {
        $credenciales = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credenciales))
            {
                return response()->json(
                    ['error' => 'Credenciales Invalidas'], 401
                );
            }
            $usuario = DB::table('usuarios')
                ->where('email', '=', $request->get('email'))
                ->first();
            if($usuario)
            {
                if($usuario->estado == "activo")
                {
                    if(Hash::check($credenciales['password'], $usuario->password))
                    {
                        $sesion = new SesionController();
                        if($sesion->registrar_sesion($usuario->id_usuario))
                        {
                            return response()->json(['usuario' => $usuario, compact('token')], 201);
                            //return response()->json(compact('token'));
                            //return response()->json(['usuario' => $usuario, compact('token')], 201);
                            //return response()->json(compact('token'));
                        }
                        else
                        {
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'Error, intente iniciar sesion nuevamente'
                            ]);
                        }
                    }
                    else
                    {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, Password Incorrecta'
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
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Verifique email y password'
                ]);
            }
        } catch (JWTException $e) {
            return response()->json
            (['error' => 'Falta Token'], 500);
        }
    }

    public function getAuthenticatedUser()
    {
        try {

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

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }
}
