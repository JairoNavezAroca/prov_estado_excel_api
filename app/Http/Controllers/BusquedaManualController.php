<?php

namespace App\Http\Controllers;

use App\Clases\ConstantesUsuario;
use App\Clases\Respuesta;
use Illuminate\Support\Facades\Http;
use App\Models\LogAplicacion;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BusquedaManualController extends Controller
{
    private $http;
    private $_procesamientoController;
    public function __construct(ProcesamientoController $_procesamientoController)
    {
        $this->http = Http::timeout(20)->withOptions(['verify' => false]);
        $this->_procesamientoController = $_procesamientoController;
    }

    public function consultarPersona(Request $request)
    {
        $page_size = 100;
        $razonSocial = $request->razonSocial ?? '';
        $index = $request->index ?? 1;
        try {
            $usuarioJWT = Session::get(ConstantesUsuario::KeySessionJWT);
            $usuario = Usuario::find($usuarioJWT->idUsuario);
            if (!$usuario->flagPuedeHacerBusquedaManual) {
                return Respuesta::enviar($data ?? null, "El usuario no tiene permiso para hacer este tipo de búsqueda");
            }

            if (strlen($razonSocial) < 3) {
                $exito = true;
                $mensaje = 'Debe ingresar palabras de al menos 03 caracteres.';
                $data = [
                    'paginaActual' => 1,
                    'totalPaginas' => 1,
                    'totalRegistros' => 0,
                    'lista' => [],
                ];
            } else {
                $data = $this->http->get("https://eap.osce.gob.pe/perfilprov-bus/1.0/tarjetas?searchText=$razonSocial&pageSize=$page_size&pageNumber=$index&export=1&langTag=es")->body();
                $data = json_decode($data);

                $lista = [];
                foreach ($data->tarjetasProvT01 as $ix => $value) {
                    $lista[] = [
                        'ruc' => $value->numRuc,
                        'razon' => $value->nomRzsProv,
                        'esHabilitado' => $value->esHabilitado,
                    ];
                }
                $lista = collect($lista);
                $lista = $lista->sortBy(function ($data, $ix) {
                    return $data['esHabilitado'];
                }, SORT_REGULAR, true)->values(); // https://dev.to/sharan/sorting-laravel-collection-in-a-custom-order-5g60

                if ($data->searchInfo->hitsTotal == 0) {
                    $exito = true;
                    $mensaje = 'No hay resultados.';
                }
                $resultado = [
                    'paginaActual' => $data->searchInfo->pageNumber,
                    'totalPaginas' => $data->searchInfo->pageTotal,
                    'totalRegistros' => $data->searchInfo->hitsTotal,
                    'lista' => $lista,
                ];

                $data = $resultado;
            }
        } catch (\Exception $e) {
            $mensaje = 'El servidor está caído, por favor intentar mas tarde';
            LogAplicacion::create([
                'detalle' => $e->getMessage(),
                'fechahora' => Carbon::now()->toString()
            ]);
        }

        return Respuesta::enviar($data ?? null, $mensaje ?? null, $exito ?? null);
    }

    public function importarRucsManualmente(Request $request)
    {
        try {
            $listaRuc = $request->get('listaRuc');
            if (count($listaRuc) <= 0) {
                $mensaje = "Es necesario ingresar al menos un ruc";
            } else {
                $data = $this->_procesamientoController->registarCargadoProveedores($listaRuc);
                $data = $data['token'];
            }
        } catch (\Exception $e) {
            $mensaje = 'Error inesperado';
            LogAplicacion::create([
                'detalle' => $e->getMessage(),
                'fechahora' => Carbon::now()->toString()
            ]);
        }

        return Respuesta::enviar($data ?? null, $mensaje ?? null);
    }
}
