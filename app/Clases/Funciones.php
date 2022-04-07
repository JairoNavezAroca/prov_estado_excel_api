<?php

namespace App\Clases;

use Carbon\Carbon;

class Funciones
{
	public static function convertirAFecha(String $fecha){
		try	{
			$fecha = Carbon::createFromFormat('d/m/Y', $fecha);
			if (!is_null($fecha))
				$fecha = $fecha->toString();
		} catch (\Exception $e) {
			$fecha = null;
		}
		return $fecha;
	}
}
