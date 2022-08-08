<?php

namespace App\Http\Controllers;

use App\Clases\Respuesta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Webpatser\Uuid\Uuid;
use App\Models\CargadoProveedores;
use App\Models\LogTrazadoApiProveedores;
use App\Models\LogAplicacion;
use App\Models\LogProveedores;
use Carbon\Carbon;
use App\Clases\Constantes;
use App\Clases\ConstantesUsuario;
use Illuminate\Support\Facades\DB;
use App\Exports\ReporteProveedores;
use App\Clases\Funciones;
use App\Clases\UsuarioJWT;
use App\Models\Usuario;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
	public function login(Request $request)
	{
		try {
			$usuario = $request->usuario;
			$contrasena = $request->contrasena;
			$loginExitoso = false;
			$usu = Usuario::where('usuario', $usuario)->first();

			if (is_null($usu))
				$mensaje = "Usuario no encontrado";
			else {
				$loginExitoso = $this->validarContrasena($contrasena, $usu);

				if ($loginExitoso) {

					if ($this->validarEstado($usu)) {
						$data = $this->generarJWT($usu);
					} else {
						$mensaje = "El nombre de usuario se encuentra temporalmente desactivado, comuníquese con el Administrador de la web para regularizar su estado. Gracias.";
					}
				} else {
					$mensaje = "Contraseña incorrecta";
				}
			}
		} catch (\Exception $e) {
			dd($e);
			$mensaje = 'Ha ocurrido un error, intentelo nuevamente';
		}
		return Respuesta::enviar($data ?? null, $mensaje ?? null);
	}

	public function cambiarContrasena(Request $request)
	{
		try {
			$usuarioJWT = Session::get(ConstantesUsuario::KeySessionJWT);
			$contrasenaActual = $request->contrasenaActual;
			$contrasenaNueva = $request->contrasenaNueva;
			$usuario = Usuario::find($usuarioJWT->idUsuario);

			$loginExitoso = $this->validarContrasena($contrasenaActual, $usuario);
			if ($loginExitoso) {
				$usuario = $this->cambiarContrasenaUsuario($usuario, $contrasenaNueva);
				$data = $this->generarJWT($usuario);
			} else {
				$mensaje = "Contraseña incorrecta";
			}
		} catch (\Exception $e) {
			dd($e);
			$mensaje = 'Ha ocurrido un error, intentelo nuevamente';
		}
		return Respuesta::enviar($data ?? null, $mensaje ?? null);
	}

	private function validarContrasena(String $contrasena, Usuario $usu): Bool
	{
		if ($usu->flagObligarCambiarContrasena == true) {
			if ($contrasena == $usu->contrasenaInicial)
				return true;
			else
				return false;
		} else {
			if ($contrasena == Crypt::decryptString($usu->contrasena))
				return true;
			else
				return false;
		}
	}
	private function validarEstado(Usuario $usu): Bool
	{
		if ($usu->idEstadoUsuario == ConstantesUsuario::EstadoActivo)
			return true;
		else if ($usu->idEstadoUsuario == ConstantesUsuario::EstadoDesactivado)
			return false;
		return false;
	}
	private function cambiarContrasenaUsuario(Usuario $usuario, String $contrasenaNueva): Usuario
	{
		$usuario->contrasena = Crypt::encryptString($contrasenaNueva);
		$usuario->flagObligarCambiarContrasena = false;
		$usuario->save();
		return $usuario;
	}
	private function generarJWT(Usuario $usu): array
	{
		return [
			'jwt' => UsuarioJWT::obtenerConUsuario($usu)->generarToken(),
			'usuario' => $usu
		];
	}
}
