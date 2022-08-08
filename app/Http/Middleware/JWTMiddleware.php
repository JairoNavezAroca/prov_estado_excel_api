<?php

namespace App\Http\Middleware;

use App\Clases\ConstantesUsuario;
use App\Clases\UsuarioJWT;
use Closure;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class JWTMiddleware
{
    public function handle($request, Closure $next)
    {
		$authorization = $request->headers->get('authorization');
		if ($authorization == null)
		return response('No autorizado', 401)->header('Content-Type', 'text/plain');
		try{
			$jwt = str_replace('Bearer ', '', $authorization);
			$user = UsuarioJWT::obtenerUsuarioConToken($jwt);
		}
		catch(Exception $e){
			return response('Token inválido', 401)->header('Content-Type', 'text/plain');
		}
		Session::put(ConstantesUsuario::KeySessionJWT, $user);
		return $next($request);
    }
}