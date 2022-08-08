<?php

namespace App\Clases;

use App\Models\Usuario;
use Carbon\Carbon;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsuarioJWT
{
	public $idUsuario;
	public $jwt_fecha_creacion;

	public function __construct($datos)
	{
		$this->idUsuario = null;
		$this->flagObligarCambiarContrasena = null;
		$this->idEstadoUsuario = null;
		$this->jwt_fecha_creacion = Carbon::now()->toDateTimeString();
		if (isset($datos['idUsuario']))
			$this->idUsuario = $datos['idUsuario'];
		if (isset($datos['jwt_fecha_creacion']))
			$this->jwt_fecha_creacion = $datos['jwt_fecha_creacion'];
	}

	public function generarToken()
	{
		$JWT_KEY = env('JWT_KEY');
		$JWT_ALGORITHM = env('JWT_ALGORITHM');
		$fecha_creacion = Carbon::now()->toDateTimeString();
		$payload = [
			'idUsuario' => $this->idUsuario,
			'jwt_fecha_creacion' => $fecha_creacion
		];
		return JWT::encode($payload, $JWT_KEY, $JWT_ALGORITHM);
	}

	public static function obtenerUsuarioConToken($jwt)
	{
		$JWT_KEY = env('JWT_KEY');
		$JWT_ALGORITHM = env('JWT_ALGORITHM');
		$datos = JWT::decode($jwt, new Key($JWT_KEY, $JWT_ALGORITHM));
		//$datos = JWT::decode($jwt, $JWT_KEY, array($JWT_ALGORITHM));
		return new UsuarioJWT([
			'idUsuario' => $datos->idUsuario,
			'jwt_fecha_creacion' => $datos->jwt_fecha_creacion
		]);
	}

	public static function obtenerConUsuario(Usuario $usu): UsuarioJWT
	{
		return new UsuarioJWT([
			'idUsuario' => $usu->idUsuario,
		]);
	}
}
