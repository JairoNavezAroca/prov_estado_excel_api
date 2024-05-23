<?php

namespace App\Http\Controllers;

use App\Clases\Respuesta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Http;
use App\Models\CargadoProveedores;
use App\Models\Proveedor;
use App\Models\ProveedorAntecedente;
use App\Models\ProveedorEmail;
use App\Models\ProveedorOrganoAdministrativo;
use App\Models\ProveedorRepresentante;
use App\Models\ProveedorSocio;
use App\Models\ProveedorTelefono;
use App\Models\LogTrazadoApiProveedores;
use App\Models\LogAplicacion;
use App\Models\LogProveedores;
use Carbon\Carbon;
use App\Clases\Constantes;
use App\Clases\ConstantesUsuario;
use Illuminate\Support\Facades\DB;
use App\Exports\ReporteProveedores;
use App\Clases\Funciones;
use App\Models\Usuario;
use Exception;
use Illuminate\Support\Facades\Session;

class ProcesamientoController extends Controller
{
	private static $rutaExcel = 'Excel/';
	private $http;
	private static $MAXIMO_INTENTOS = 5;
	public function __construct()
	{
		$this->http = Http::timeout(20)->withOptions(['verify' => false]);
	}

	public function importarExcel()
	{
		error_log($this->obtenerMemoria());
		if (count($_FILES) == 0)
			return;
		$_FILES = current($_FILES);
		//$_FILES['tmp_name']

		$nombreArchivo = $_FILES['name'];
		$extension = explode(".", $nombreArchivo);
		$extension = $extension[count($extension) - 1];
		$nombreArchivoFinal =
			date("y") . //años
			date("m") . //mes
			date("d") . //dia
			date("H") . //hora 24h
			date("i") . //minutos
			date("s") . //segundos
			date("_") .
			substr(microtime(), 2, 4) .
			'.' . $extension;
		$rutaArchivoSubido = static::$rutaExcel . $nombreArchivoFinal;
		move_uploaded_file($_FILES['tmp_name'], $rutaArchivoSubido);
		//return $nombreArchivoFinal;


		try {
			$lista = [];
			$lista_ruc = Excel::toArray(new \stdClass(), $rutaArchivoSubido); // https://www.php.net/manual/es/language.types.object.php
			$lista_ruc = array_shift($lista_ruc); // Obtengo la primera hoja del excel
			array_shift($lista_ruc); // Quito la primera fila de la hoja, la cual debe tener la cabecera 'RUC'
			foreach ($lista_ruc as $item) {
				$cadenaRuc = (string)$item[0];
				$lista[] = ['ruc' => $cadenaRuc];
			}
			unset($lista_ruc);
			$data = $this->registarCargadoProveedores($lista);
			$data = $data['token'];
		} catch (\Exception $e) {
			$mensaje = 'Ha ocurrido un error, intentelo nuevamente';
		}
		error_log($this->obtenerMemoria());
		return Respuesta::enviar($data ?? null, $mensaje ?? null);
	}

	public function registarCargadoProveedores($lista)
	{
		$token = Uuid::generate(4)->string;

		$_cargadoProveedores = new CargadoProveedores();
		$_cargadoProveedores->ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
		$_cargadoProveedores->token = $token;
		$_cargadoProveedores->fechaImportacion = Carbon::now()->toString();
		$_cargadoProveedores->fechaExportacion = null;
		$_cargadoProveedores->save();

		foreach ($lista as $data) {
			$data = (array)$data;
			if ($data['ruc'] != '' && $data['ruc'] != null) {
				$_proveedor = new Proveedor();
				$_proveedor->idCargadoProveedores = $_cargadoProveedores->idCargadoProveedores;
				$_proveedor->token = Uuid::generate(4)->string;
				$_proveedor->fechaRegistroRuc = Carbon::now()->toString();
				$_proveedor->ruc = $data['ruc'];
				$_proveedor->razon = $data['razon'] ?? null;
				$_proveedor->estadoRegistroDatos = Constantes::$PENDIENTE;
				$_proveedor->numeroIntentos = 0;
				$_proveedor->save();
			}
		}
		return ['token' => $token];
	}

	public function actualizarCargadoProveedores(Request $request)
	{
		try {
			$token = $request->token;
			$flagBusquedaCompleta = $request->flagBusquedaCompleta == "true";
			if ($flagBusquedaCompleta){
				$usuarioJWT = Session::get(ConstantesUsuario::KeySessionJWT);
				$usuario = Usuario::find($usuarioJWT->idUsuario);
				if ($usuario->flagPuedeHacerBusquedaCompleta == false){
					LogAplicacion::create([
						'detalle' => "Se cargó archivo con flag busqueda completa, sin embargo usuario no tiene dicho privilegio",
						'fechahora' => Carbon::now()->toString()
					]);
					$flagBusquedaCompleta = false;
				}
			}
			CargadoProveedores::where('token', $token)->update(['flagBusquedaCompleta' => $flagBusquedaCompleta]);
		} catch (\Exception $e) {
			$mensaje = 'Ha ocurrido un error, intentelo nuevamente';
			LogAplicacion::create([
				'detalle' => $e->getMessage(),
				'fechahora' => Carbon::now()->toString()
			]);
		}
		return Respuesta::enviar($data ?? null, $mensaje ?? null);
	}

	public function obtenerCargado(String $token)
	{
		error_log($this->obtenerMemoria());
		try {
			$_cargadoProveedores = CargadoProveedores::where('token', $token)->first();
			$consulta = <<<EOD
			SELECT 
					p.idProveedor, p.token, p.ruc, p.razon, p.estadoRegistroDatos, GROUP_CONCAT(DISTINCT pe.email SEPARATOR ' | ') AS emails,
					GROUP_CONCAT(DISTINCT pt.telefono SEPARATOR ' | ') AS telefonos,
					CASE WHEN p.razon IS NOT NULL THEN p.razon ELSE p.nomRzsProv END AS razon
				FROM proveedor p
				LEFT JOIN proveedor_email pe ON pe.idProveedor = p.idProveedor
				LEFT JOIN proveedor_telefono pt ON pt.idProveedor = p.idProveedor
				WHERE p.idCargadoProveedores = ?
				GROUP BY p.idProveedor
			EOD;
			$data = [
				'token' => $token,
				//'lista_ruc' => Proveedor::where('idCargadoProveedores', $_cargadoProveedores->idCargadoProveedores)->select('token', 'ruc', 'estadoRegistroDatos')->get()->toArray()
				'lista_ruc' => DB::select($consulta, [$_cargadoProveedores->idCargadoProveedores])
			];
		} catch (\Exception $e) {
			$mensaje = 'Ha ocurrido un error, intentelo nuevamente';
		}
		error_log($this->obtenerMemoria());
		return Respuesta::enviar($data ?? null, $mensaje ?? null);
	}

	public function procesarRuc(Request $request)
	{
		error_log($this->obtenerMemoria());
		$token = $request->get('token');
		$flagFallidos = $request->get('flagFallidos', false);
		try {
			$mensaje = null;
			$___INICIO = microtime(true);
			$_proveedor = Proveedor::where('token', $token)->first();
			$_proveedor->numeroIntentos = $_proveedor->numeroIntentos + 1;
			$flagBusquedaCompleta = $_proveedor->cargadoProveedores->flagBusquedaCompleta;

			if ($_proveedor->estadoRegistroDatos != Constantes::$PROCESANDO) {

				if ($_proveedor->numeroIntentos >= static::$MAXIMO_INTENTOS && $flagFallidos == false) {
					$_proveedor->estadoRegistroDatos = Constantes::$FALLIDO;
					$_proveedor->save();
				} else {
					$_proveedor->estadoRegistroDatos = Constantes::$PROCESANDO;
					$_proveedor->save();

					$___consulta_token_ruc = microtime(true);
					$ruc = $_proveedor->ruc;

					if (true) {
						[$response1, $mensaje1] = $this->obtenerResultadoApi1($ruc, $flagBusquedaCompleta);
						$___peticion_api_1 = microtime(true);
						[$response2, $mensaje2] = $this->obtenerResultadoApi2($ruc); // este api tiene los datos principales
						$___peticion_api_2 = microtime(true);

						// LogProveedores::create([
						// 	'idProveedor' => $_proveedor->idProveedor,
						// 	'ruc' => $_proveedor->ruc,
						// 	'api_1' => $response1,
						// 	'api_2' => $response2,
						// 	'fecha_registro' => Carbon::now()->toString()
						// ]);

						$response1 = json_decode($response1);
						$response2 = json_decode($response2);
						$___conversion = microtime(true);

						if (
								($mensaje1 != null && $mensaje1 != '' && $mensaje2 != null && $mensaje2 != '')
								|| ($flagBusquedaCompleta == false && $mensaje2 != null && $mensaje2 != '')
							)
							throw new Exception("Servicio caído");
						else if ($mensaje1 != null && $mensaje1 != '')
							$data = ['warning' => true, 'mensaje' => $mensaje1];
						else if ($mensaje2 != null && $mensaje2 != '')
							$data = ['warning' => true, 'mensaje' => $mensaje2];
					} else {
						$___peticion_api_1 = microtime(true);
						$___peticion_api_2 = microtime(true);
						$response1 = $this->api1();
						$response2 = $this->api2();
						$___conversion = microtime(true);
					}

					DB::beginTransaction();
					$this->RegistrarRepuestaApiATablas($_proveedor, $response1, $response2);
					DB::commit();

					$___registro_bd = microtime(true);

					//dd(get_defined_vars());
					//dd(array_keys(get_defined_vars()));
					//dd($response1,$response2);
					//$data = true;

					$___FIN = microtime(true);

					LogTrazadoApiProveedores::create([
						'idProveedor' => $_proveedor->idProveedor,
						'consulta_token_ruc' => $___consulta_token_ruc - $___INICIO,
						'peticion_api_1' => $___peticion_api_1 - $___consulta_token_ruc,
						'peticion_api_2' => $___peticion_api_2 - $___peticion_api_1,
						'conversion' => $___conversion - $___peticion_api_2,
						'registro_bd' => $___registro_bd - $___conversion,
						'total' => $___FIN - $___INICIO
					]);
				}
			}
		} catch (\Illuminate\Http\Client\ConnectionException $e) {
			$success = false;
			$mensaje = 'El servicio web del estado se encuentra muy lento, intente más tarde.';
			$data = [
				'success' => $success,
				'mensaje' => $mensaje
			];
		} catch (\Exception $e) {
			$success = true;
			$mensaje = 'El servidor está caído, por favor intentar mas tarde';
			$data = [
				'success' => false,
				'mensaje' => $mensaje
			];
		} finally {
			if (isset($e)) {
				DB::rollback();
				$_proveedor = Proveedor::where('token', $token)->first();
				$_proveedor->estadoRegistroDatos = Constantes::$FALLIDO;
				$_proveedor->save();

				LogAplicacion::create([
					'detalle' => $_proveedor->idProveedor . ' - ' . $token . ' - ' . $e->getMessage(),
					//'detalle'=> $_proveedor->idProveedor . ' - ' . $token . ' - ' . $e->__toString(),
					'fechahora' => Carbon::now()->toString()
				]);
			}
		}
		error_log($this->obtenerMemoria());
		return Respuesta::enviar($data ?? null, $mensaje ?? null, $success ?? true);
	}

	private function obtenerResultadoApi1($ruc, $flagBusquedaCompleta)
	{
		$datosPorDefecto = json_encode(
			[
				'conformacion' => [
					'proveedor' => [
						'codigoRegistro' => null
					],
					'socios' => [],
					'representantes' => [],
					'organosAdm' => [],
				],
				'datosSunat' => [
					'respuesta' => null,
					'razon' => null,
					'tipoEmpresa' => null,
					'estado' => null,
					'condicion' => null,
					'departamento' => null,
					'provincia' => null,
					'distrito' => null,
					'personeria' => null,
					'process' => null,
				],
				'antecedentes' => [
					'sanciones' => null,
					'inhsJudicial' => null,
					'inhsAdministrativa' => null,
					'fechaConsultaSancTCE' => '01/01/2000 00:00:00',
					'fechaConsultaInhabAD' => '01/01/2000 00:00:00',
					'fechaConsultaInhabMJ' => '01/01/2000 00:00:00',
					'process' => null,
				],
			]
		);
		try {
			if($flagBusquedaCompleta){
				$datos = $this->http->get("https://eap.osce.gob.pe/ficha-proveedor-cns/1.0/ficha/$ruc/resumen")->body();
				return [$datos, ''];
			}
			else
				return [$datosPorDefecto, ''];
		} catch (\Exception $e) {
			LogAplicacion::create([
				'detalle' => $e->getMessage(),
				'fechahora' => Carbon::now()->toString()
			]);
			return [$datosPorDefecto, 'El portal osce tiene inconvenientes, se intentará importar la data de forma básica'];
		}
	}

	private function obtenerResultadoApi2($ruc)
	{
		$datosPorDefecto = json_encode(
			[
				'proveedorT01' => [
					'codProv' => null,
					'idOrigenProv' => null,
					'numRuc' => null,
					'nomRzsProv' => null,
					'esHabilitado' => null,
					'lscIdTipReg' => null,
					'lscIdTipRegVig' => null,
					'esAptoContratar' => null,
					'cmcTexto' => null,
				]
			]
		);
		try {
			$datos = $this->http->get("https://eap.osce.gob.pe/perfilprov-bus/1.0/ficha/$ruc")->body();
			return [$datos, ''];
		} catch (\Exception $e) {
			LogAplicacion::create([
				'detalle' => $e->getMessage(),
				'fechahora' => Carbon::now()->toString()
			]);
			return [$datosPorDefecto, 'El portal osce tiene inconvenientes, se intentará importar la data de forma básica'];
		}
	}

	private function RegistrarRepuestaApiATablas($_proveedor, $response1, $response2)
	{
		$flagBusquedaCompleta = $_proveedor->cargadoProveedores->flagBusquedaCompleta;

		$_proveedor->ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
		$_proveedor->fechaRegistroDatos = Carbon::now()->toString();
		$_proveedor->estadoRegistroDatos = Constantes::$REGISTRADO;

		$_proveedor->codigoRegistro = $response1->conformacion->proveedor->codigoRegistro;
		$_proveedor->respuesta = $response1->datosSunat->respuesta;
		if ($response1->datosSunat->razon != null && trim($response1->datosSunat->razon) != '')
			$_proveedor->razon = $response1->datosSunat->razon;
		else
			$_proveedor->razon = $response2->proveedorT01?->nomRzsProv;
		$_proveedor->tipoEmpresa = $response1->datosSunat->tipoEmpresa;
		$_proveedor->estado = $response1->datosSunat->estado;
		$_proveedor->condicion = $response1->datosSunat->condicion;
		$_proveedor->departamento = ($flagBusquedaCompleta) ? $response1->datosSunat->departamento : '';
		$_proveedor->provincia = ($flagBusquedaCompleta) ? $response1->datosSunat->provincia : '';
		$_proveedor->distrito = ($flagBusquedaCompleta) ? $response1->datosSunat->distrito : '';
		$_proveedor->personeria = $response1->datosSunat->personeria;
		$_proveedor->process_ = $response1->datosSunat->process;

		$_proveedor->codProv = $response2->proveedorT01?->codProv;
		$_proveedor->idOrigenProv = $response2->proveedorT01?->idOrigenProv;
		$_proveedor->numRuc = $response2->proveedorT01?->numRuc;
		$_proveedor->nomRzsProv = $_proveedor->razon;
		$_proveedor->esHabilitado = ($flagBusquedaCompleta) ? $response2->proveedorT01?->esHabilitado : '';
		$_proveedor->lscIdTipReg = $response2->proveedorT01?->lscIdTipReg;
		$_proveedor->lscIdTipRegVig = $response2->proveedorT01?->lscIdTipRegVig;
		$_proveedor->esAptoContratar = $response2->proveedorT01?->esAptoContratar;
		$_proveedor->cmcTexto = $response2->proveedorT01?->cmcTexto;
		$_proveedor->save();

		//DB::select('CALL RESETEAR_DATOS_PROVEEDOR(?)', [$_proveedor->idProveedor]);
		$lista_telefonos = $response2->proveedorT01->telefonos ?? [];
		foreach ($lista_telefonos as $item) {
			ProveedorTelefono::create([
				'idProveedor' => $_proveedor->idProveedor,
				'telefono' => $item
			]);
		}
		unset($lista_telefonos);

		$lista_emails = $response2->proveedorT01->emails ?? [];
		foreach ($lista_emails as $item) {
			ProveedorEmail::create([
				'idProveedor' => $_proveedor->idProveedor,
				'email' => $item
			]);
		}
		unset($lista_emails);

		if ($flagBusquedaCompleta) {
			$antecedentes = $response1->antecedentes;
			ProveedorAntecedente::create([
				'idProveedor' => $_proveedor->idProveedor,
				'sanciones' => json_encode($antecedentes->sanciones),
				'inhsJudicial' => json_encode($antecedentes->inhsJudicial),
				'inhsAdministrativa' => json_encode($antecedentes->inhsAdministrativa),
				'fechaConsultaSancTCE' => Carbon::createFromFormat('d/m/Y H:i:s', $antecedentes->fechaConsultaSancTCE)->toString(),
				'fechaConsultaInhabAD' => Carbon::createFromFormat('d/m/Y H:i:s', $antecedentes->fechaConsultaInhabAD)->toString(),
				'fechaConsultaInhabMJ' => Carbon::createFromFormat('d/m/Y H:i:s', $antecedentes->fechaConsultaInhabMJ)->toString(),
				'process_' => $antecedentes->process
			]);
			unset($antecedentes);
	
			$lista_socios = $response1->conformacion->socios ?? [];
			foreach ($lista_socios as $item) {
				ProveedorSocio::create([
					'idProveedor' => $_proveedor->idProveedor,
					'idSocio' => $item->idSocio,
					'codigoRegistro' => $item->codigoRegistro,
					'codigoDocIde' => $item->codigoDocIde,
					'descDocIde' => $item->descDocIde,
					'siglaDocIde' => $item->siglaDocIde,
					'nroDocumento' => $item->nroDocumento,
					'numeroAcciones_' => (string)$item->numeroAcciones,
					'numeroAcciones' => $item->numeroAcciones,
					'porcentajeAcciones_' => (string)$item->porcentajeAcciones,
					'porcentajeAcciones' => $item->porcentajeAcciones,
					'razonSocial' => $item->razonSocial,
					'numeroRuc' => ($item->numeroRuc == 'null') ? null : $item->numeroRuc,
					'fechaIngreso' => Funciones::convertirAFecha($item->fechaIngreso)
				]);
			}
			unset($lista_socios);
	
			$lista_representantes = $response1->conformacion->representantes ?? [];
			foreach ($lista_representantes as $item) {
				ProveedorRepresentante::create([
					'idProveedor' => $_proveedor->idProveedor,
					'idRepresentante' => $item->idRepresentante,
					'codigoRegistro' => $item->codigoRegistro,
					'codigoDocIde' => $item->codigoDocIde,
					'descDocIde' => $item->descDocIde,
					'siglaDocIde' => $item->siglaDocIde,
					'nroDocumento' => $item->nroDocumento,
					'razonSocial' => $item->razonSocial,
					'idCargo' => $item->idCargo,
					'descCargo' => ($item->descCargo == 'null') ? null : $item->descCargo,
					'numeroRuc' => ($item->numeroRuc == 'null') ? null : $item->numeroRuc,
					'fechaIngreso' => Funciones::convertirAFecha($item->fechaIngreso)
				]);
			}
			unset($lista_representantes);
	
			$lista_organosAdm = $response1->conformacion->organosAdm ?? [];
			foreach ($lista_organosAdm as $item) {
				ProveedorOrganoAdministrativo::create([
					'idProveedor' => $_proveedor->idProveedor,
					'idOrgano' => $item->idOrgano,
					'codigoRegistro' => $item->codigoRegistro,
					'codigoDocIde' => $item->codigoDocIde,
					'descDocIde' => $item->descDocIde,
					'siglaDocIde' => $item->siglaDocIde,
					'nroDocumento' => $item->nroDocumento,
					'apellidosNomb' => $item->apellidosNomb,
					'idTipoOrgano' => $item->idTipoOrgano,
					'descTipoOrgano' => $item->descTipoOrgano,
					'idCargo' => $item->idCargo,
					'descCargo' => $item->descCargo,
					'fechaIngreso' => Funciones::convertirAFecha($item->fechaIngreso)
				]);
			}
			unset($lista_organosAdm);
		}
	}

	public function exportarExcel(String $token)
	{
		error_log($this->obtenerMemoria());
		try {
			$_cargadoProveedores = CargadoProveedores::where('token', $token)->first();
			header('Access-Control-Expose-Headers: nombre-archivo');
			header('nombre-archivo: reporte.xlsx');
			error_log($this->obtenerMemoria());
			$excel = new ReporteProveedores($_cargadoProveedores);
			error_log($this->obtenerMemoria());
			return ($excel)->download('Reporte.xlsx');
		} catch (\Exception $e) {
			//dd($e);
			$mensaje = 'Ha ocurrido un error, intentelo nuevamente';
			LogAplicacion::create([
				'detalle' => $e->__toString(),
				'fechahora' => Carbon::now()->toString()
			]);
		}
		error_log($this->obtenerMemoria());
		return Respuesta::enviar($data ?? null, $mensaje ?? null);
	}

	private function api1()
	{
		//https://diego.com.es/heredoc-y-nowdoc-en-php
		/*$res = <<<EOD
		{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":null}},"datosSunat":{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":null}},"respuesta":1,"ruc":"20522042910","razon":"FROM COLD SYSTEM SERVICE SAC","tipoEmpresa":"SOCIEDAD ANONIMA CERRADA","estado":"ACTIVO","condicion":"HABIDO","departamento":"PROV. CONST. DEL CALLAO","provincia":"PROV. CONST. DEL CALLAO","distrito":"LA PERLA","personeria":"02","process":true},"conformacion":{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":null}},"proveedor":{"numeroRuc":"20522042910","razonSocial":"FROM COLD SYSTEM SERVICE SAC","codigoRegistro":"S0669532","personeria":"2","esEjecutor":0,"codigoRegistroEjec":"null","cmcTexto":"null","clcTexto":"null","idDocIdent":"null","nroDocIdent":"null","codigoDoc":"null","nombreDoc":"null"},"socios":[{"idSocio":4467574,"codigoRegistro":"S0669532","codigoDocIde":"9","descDocIde":"DOC. NACIONAL DE IDENTIDAD","siglaDocIde":"D.N.I.","nroDocumento":"03685980 ","numeroAcciones":9930.0,"porcentajeAcciones":61.83,"razonSocial":"QUEZADA VALDIVIEZO OMAR","numeroRuc":"null","fechaIngreso":"13/05/2009"},{"idSocio":4467575,"codigoRegistro":"S0669532","codigoDocIde":"1","descDocIde":"DOC. NACIONAL DE IDENTIDAD/LE","siglaDocIde":"L.E.","nroDocumento":"25770252","numeroAcciones":6130.0,"porcentajeAcciones":38.17,"razonSocial":"SANDOVAL TAPIA MARCO ANTONIO FREDDY","numeroRuc":"null","fechaIngreso":"13/05/2009"}],"representantes":[{"idRepresentante":2384654,"codigoRegistro":"S0669532","codigoDocIde":"9","descDocIde":"DOC. NACIONAL DE IDENTIDAD","siglaDocIde":"D.N.I.","nroDocumento":"25770252 ","razonSocial":"SANDOVAL TAPIA MARCO ANTONIO FREDDY","idCargo":null,"descCargo":"null","numeroRuc":"null","fechaIngreso":"13/05/2009"}],"organosAdm":[{"idOrgano":2025465,"codigoRegistro":"S0669532","codigoDocIde":"1","descDocIde":"DOC. NACIONAL DE IDENTIDAD/LE","siglaDocIde":"L.E.","nroDocumento":"25770252","apellidosNomb":"SANDOVAL TAPIA MARCO ANTONIO FREDDY","idTipoOrgano":2,"descTipoOrgano":"GERENCIA","idCargo":18,"descCargo":"Gerente General","fechaIngreso":"13/05/2009"}],"listaDniSocios":"03685980,25770252,","listaDniRepresentantes":"25770252,","listaDniOrganos":"25770252,","fechaConsulta":"22/03/2022","process":true},"antecedentes":{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":["00","00","00"]}},"sanciones":[],"inhsJudicial":[],"inhsAdministrativa":[],"fechaConsultaSancTCE":"22/03/2022 14:10:17","fechaConsultaInhabAD":"22/03/2022 14:10:17","fechaConsultaInhabMJ":"22/03/2022 14:10:17","process":true},"impedimentos":null,"buenasPracticas":null}
		EOD;*/
		$res = <<<EOD
		{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":null}},"datosSunat":{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":null}},"respuesta":1,"ruc":"20486218488","razon":"PERU BOSQUE E.I.R.L.","tipoEmpresa":"EMPRESA INDIVIDUAL DE RESP. LTDA","estado":"ACTIVO","condicion":"HABIDO","departamento":"JUNIN","provincia":"SATIPO","distrito":"SATIPO","personeria":"02","process":true},"conformacion":{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":null}},"proveedor":{"numeroRuc":"20486218488","razonSocial":"PERU BOSQUE E.I.R.L.","codigoRegistro":"56648","personeria":"2","esEjecutor":1,"codigoRegistroEjec":"56648","cmcTexto":"S/ 500,000.00","clcTexto":"S/ 500,000.00","idDocIdent":"null","nroDocIdent":"null","codigoDoc":"null","nombreDoc":"null"},"socios":[{"idSocio":4466585,"codigoRegistro":"56648","codigoDocIde":"9","descDocIde":"DOC. NACIONAL DE IDENTIDAD","siglaDocIde":"D.N.I.","nroDocumento":"40866844","numeroAcciones":1.0,"porcentajeAcciones":100.0,"razonSocial":"PEREZ GAMARRA ANGEL LINCOLN","numeroRuc":"null","fechaIngreso":"26/07/2004"}],"representantes":[{"idRepresentante":2384083,"codigoRegistro":"56648","codigoDocIde":"9","descDocIde":"DOC. NACIONAL DE IDENTIDAD","siglaDocIde":"D.N.I.","nroDocumento":"40866844 ","razonSocial":"PEREZ GAMARRA ANGEL LINCOLN ","idCargo":null,"descCargo":"null","numeroRuc":"null","fechaIngreso":"26/07/2004"}],"organosAdm":[{"idOrgano":2024838,"codigoRegistro":"56648","codigoDocIde":"9","descDocIde":"DOC. NACIONAL DE IDENTIDAD","siglaDocIde":"D.N.I.","nroDocumento":"40866844","apellidosNomb":"PEREZ GAMARRA ANGEL LINCOLN","idTipoOrgano":2,"descTipoOrgano":"GERENCIA","idCargo":18,"descCargo":"Gerente General","fechaIngreso":"26/07/2004"}],"listaDniSocios":"40866844,","listaDniRepresentantes":"40866844,","listaDniOrganos":"40866844,","fechaConsulta":"22/03/2022","process":true},"antecedentes":{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":["00","00","00"]}},"sanciones":[],"inhsJudicial":[],"inhsAdministrativa":[],"fechaConsultaSancTCE":"22/03/2022 23:46:54","fechaConsultaInhabAD":"22/03/2022 23:46:54","fechaConsultaInhabMJ":"22/03/2022 23:46:54","process":true},"impedimentos":null,"buenasPracticas":null}
		EOD;
		return json_decode($res);
	}
	private function api2()
	{
		/*$res = <<<EOD
		{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":null}},"dataInfo":{"updatedTo":"2022-03-21T05:00:00.000+0000"},"proveedorT01":{"codProv":"20522042910","idOrigenProv":10,"numRuc":"20522042910","nomRzsProv":"FROM COLD SYSTEM SERVICE SAC","esHabilitado":true,"lscIdTipReg":"1 2","lscIdTipRegVig":"2","esAptoContratar":true,"emails":["mafsandovalt@gmail.com"],"telefonos":["017766039-999800321"],"cmcTexto":null,"espProvT01s":[]},"totalContrataciones":null,"contratacionesT01":null,"estadisticaT01":{"seccionPenalidadF01":{"datosEstadisticaT01":{"esVisible":true,"descripcion":"Penalidades","valor":"0"},"penalidadesT01":null},"seccionSancionF01":{"datosEstadisticaT01":null,"sancionesT01":null}}}
		EOD;*/
		$res = <<<EOD
		{"resultadoT01":{"codigo":"00","mensaje":"Procesamiento completado.","nivel":5,"mensajes":{"data":null}},"dataInfo":{"updatedTo":"2022-03-21T05:00:00.000+0000"},"proveedorT01":{"codProv":"20486218488","idOrigenProv":10,"numRuc":"20486218488","nomRzsProv":"PERU BOSQUE E.I.R.L.","esHabilitado":true,"lscIdTipReg":"3 2 1 4","lscIdTipRegVig":"3 2 1 4","esAptoContratar":true,"emails":["edithriveramartinez@gmail.COM"],"telefonos":["964703299"],"cmcTexto":"S/ 500,000.00","espProvT01s":[{"desEsp":"Consultoría en obras de saneamiento y afines","desCat":"A"},{"desEsp":"Consultoría en obras electromecánicas, energéticas, telecomunicaciones y afines","desCat":"A"},{"desEsp":"Consultoría en obras de represas , irrigaciones y afines","desCat":"A"},{"desEsp":"Consultoría en obras urbanas edificaciones y afines","desCat":"A"},{"desEsp":"Consultoría en obras viales, puertos y afines","desCat":"A"}]},"totalContrataciones":null,"contratacionesT01":null,"estadisticaT01":{"seccionPenalidadF01":{"datosEstadisticaT01":{"esVisible":true,"descripcion":"Penalidades","valor":"0"},"penalidadesT01":null},"seccionSancionF01":{"datosEstadisticaT01":null,"sancionesT01":null}}}
		EOD;
		return json_decode($res);
	}

	function obtenerMemoria()
	{
		return memory_get_usage() / (1024 * 1024) . ' MB';
	}
}
