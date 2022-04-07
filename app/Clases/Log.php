<?php

namespace App\Clases;

use DB;
use Illuminate\Support\Facades\Session;

class Log
{
	/*
	public static function guardar_log($accion, $detalle, $comentario)
	{
		if ($accion != 'ERROR') return;
		try {
			$id_usuario = Session::get('user')?->id_usuario;
			if (!is_string($detalle))
				$detalle = json_encode($detalle);
			DB::connection('mysql2')->select(
				'insert into log_api(
					metodo, ruta, parametros_entrada, id_usuario, ip_usuario, ip_servidor,
					comentario, tipo, detalle, fechahora) 
					values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				array(
					$_SERVER['REQUEST_METHOD'],
					$_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'],
					$_SERVER['QUERY_STRING'] ?? null,
					$id_usuario,
					$_SERVER['REMOTE_ADDR'],
					$_SERVER['SERVER_NAME'] ?? $_SERVER['SERVER_ADDR'],
					$comentario,
					$accion,
					$detalle,
					\Carbon\Carbon::now()->toDateTimeString()
				)
			);
		} catch (\Exception $e) {
		} finally {
		}
	}

	public static function guardar_error($detalle = null, $comentario = null)
	{
		static::guardar_log('ERROR', $detalle, $comentario);
	}

	public static function guardar_exito($detalle = null, $comentario = null)
	{
		static::guardar_log('EXITO', $detalle, $comentario);
	}

	public static function guardar_informacion($detalle = null, $comentario = null)
	{
		static::guardar_log('INFORMACION', $detalle, $comentario);
	}

	public static function guardar_request($detalle = null, $comentario = null)
	{
		static::guardar_log('REQUEST', $detalle, $comentario);
	}

	public static function guardar_response($detalle = null, $comentario = null)
	{
		static::guardar_log('RESPONSE', $detalle, $comentario);
	}
	*/
}
