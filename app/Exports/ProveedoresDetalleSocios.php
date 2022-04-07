<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProveedoresDetalleSocios implements FromView, WithTitle
{
	private $datos;

	public function __construct($datos)
	{
		$this->datos = $datos;
	}

	public function view(): View
	{
		return view('excel_proveedores_socios',  [
			'proveedor' => $this->datos
		]);
	}

	public function title(): string
	{
		return 'Socios';
	}
}
