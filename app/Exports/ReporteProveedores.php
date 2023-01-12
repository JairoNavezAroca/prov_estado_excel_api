<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Reporte;

class ReporteProveedores implements WithMultipleSheets
{
	use Exportable;

	private $idCargadoProveedores;

	public function __construct($idCargadoProveedores)
	{
		$this->idCargadoProveedores = $idCargadoProveedores;
	}

	public function sheets(): array
	{
		$res = Reporte::obtenerDatosReporte($this->idCargadoProveedores);
		$sheets = [];
		array_push($sheets, new ProveedoresResumen($res->proveedor_resumen));
		array_push($sheets, new ProveedoresMaestro($res->proveedor));
		array_push($sheets, new ProveedoresDetalleSocios($res->proveedor_socio));
		array_push($sheets, new ProveedoresDetalleRepresentantes($res->proveedor_representante));
		array_push($sheets, new ProveedoresDetalleOrganosAdministrativos($res->proveedor_organo_administrativo));
		return $sheets;
	}
}
