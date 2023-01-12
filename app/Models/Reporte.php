<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Reporte
{
	public static function obtenerDatosReporte($idCargadoProveedores){
		return (object)[
			'proveedor_resumen' =>
				DB::select(static::consultaProveedoresResumen(), [$idCargadoProveedores]),
			'proveedor' => 
				DB::select(static::consultaProveedoresMaestro(), [$idCargadoProveedores]),
			'proveedor_socio' => 
				DB::select(static::consultaProveedoresSocios(), [$idCargadoProveedores]),
			'proveedor_representante' => 
				DB::select(static::consultaProveedoresRepresentantes(), [$idCargadoProveedores]),
			'proveedor_organo_administrativo' => 
				DB::select(static::consultaProveedoresOrganosAdministrativos(), [$idCargadoProveedores])
		];
	}

	private static function consultaProveedoresResumen(){
		return <<<EOD
			SELECT 
				p.ruc,
				CASE WHEN p.razon IS NOT NULL THEN p.razon ELSE p.nomRzsProv END AS razon,
				p.departamento,
				GROUP_CONCAT(DISTINCT pe.email SEPARATOR ' | ') AS emails,
				GROUP_CONCAT(DISTINCT pt.telefono SEPARATOR ' | ') AS telefonos,
				pr.razonSocial AS representante
			FROM proveedor p 
			LEFT JOIN proveedor_email pe ON pe.idProveedor = p.idProveedor
			LEFT JOIN proveedor_telefono pt ON pt.idProveedor = p.idProveedor
			LEFT JOIN proveedor_representante pr ON pr.idProveedor = p.idProveedor
			WHERE p.idCargadoProveedores = ?
			GROUP BY p.idProveedor;
		EOD;
	}

	private static function consultaProveedoresMaestro(){
		return <<<EOD
			SELECT 
				p.ruc, p.tipoEmpresa,
				p.departamento, p.provincia, p.distrito, p.esHabilitado,
				GROUP_CONCAT(DISTINCT pe.email SEPARATOR ' | ') AS emails,
				GROUP_CONCAT(DISTINCT pt.telefono SEPARATOR ' | ') AS telefonos,
				CASE WHEN p.razon IS NOT NULL THEN p.razon ELSE p.nomRzsProv END AS razon
			FROM proveedor p 
			LEFT JOIN proveedor_email pe ON pe.idProveedor = p.idProveedor
			LEFT JOIN proveedor_telefono pt ON pt.idProveedor = p.idProveedor
			WHERE p.idCargadoProveedores = ?
			GROUP BY p.idProveedor;
		EOD;
	}
	
	private static function consultaProveedoresSocios(){
		return <<<EOD
			SELECT DISTINCT p.ruc, ps.razonSocial
			FROM proveedor p 
			JOIN proveedor_socio ps ON ps.idProveedor = p.idProveedor
			WHERE p.idCargadoProveedores = ?;
		EOD;
	}
	private static function consultaProveedoresRepresentantes(){
		return <<<EOD
			SELECT DISTINCT p.ruc, pr.razonSocial
			FROM proveedor p 
			JOIN proveedor_representante pr ON pr.idProveedor = p.idProveedor
			WHERE p.idCargadoProveedores = ?;
		EOD;
	}
	private static function consultaProveedoresOrganosAdministrativos(){
		return <<<EOD
			SELECT DISTINCT p.ruc, poa.apellidosNomb
			FROM proveedor p
			JOIN proveedor_organo_administrativo poa ON poa.idProveedor = p.idProveedor
			WHERE p.idCargadoProveedores = ?;
		EOD;
	}
}
