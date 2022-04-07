<?php

namespace App\Clases;

class Respuesta
{
	public $datos;
	public $mensaje;
	public $exito;
	private function __construct($datos = null, $mensaje = '' , $exito = true){
		$this->mensaje = $mensaje;
		$this->datos = $datos;
		$this->exito = $exito;
	}
	public static function enviar($datos = null, $mensaje = '', $exito = null){
		if (is_null($exito))
			$exito = ($mensaje == '');
		$objMensaje = new Respuesta($datos, $mensaje, $exito);
		//Log::guardar_response($objMensaje);
		return json_encode($objMensaje, TRUE);
	}
}
