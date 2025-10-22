<?php

namespace App\Http\Controllers\OrdenServicio;

use App\Enums\Response\ResponseStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenServicio\SubmitOrdenServicioRequest;
use App\Models\OrdenServicio;
use App\Services\OrdenServicioService;
use Illuminate\Http\Request;

class OrdenServicioController extends Controller
{
    public function index()
    {
        return view('ordenes.index');
    }
    public function formulario()
    {
        return view('ordenes.formulario');
    }
    public function data_table(Request $request, OrdenServicioService $service)
    {
        try {
            if (!request()->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }
            return $service->datatable($request)->toJson();
        } catch (\Exception $exception) {
            return response()->json([
                "status" => ResponseStatusEnum::ERROR,
                "message" => $exception->getMessage(),
                "data" => []
            ]);
        }
    }
    public function guardar(SubmitOrdenServicioRequest $request, OrdenServicioService $service)
    {
        try {
            if (!$request->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }
            $orden = $service->guardar($request);
            return response()->json([
                "status" => ResponseStatusEnum::SUCCESS,
                "orden" => $orden
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "status" => ResponseStatusEnum::ERROR,
                "message" => $exception->getMessage()
            ]);
        }
    }
    public function editar($orden, SubmitOrdenServicioRequest $request, OrdenServicioService  $service)
    {

        try {
            if (!$request->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }

            $orden = $service->editar($orden, $request);
            return response()->json([
                "status" => ResponseStatusEnum::SUCCESS,
                "orden" => $orden
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "status" => ResponseStatusEnum::ERROR,
                "message" => $exception->getMessage()
            ]);
        }
    }
    public function eliminar(OrdenServicio $orden, OrdenServicioService $service)
    {
        try {
            if (!request()->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }
            $service->eliminar($orden);
            return response()->json([
                "status" => ResponseStatusEnum::SUCCESS,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "status" => ResponseStatusEnum::ERROR,
                "message" => $exception->getMessage()
            ]);
        }
    }
}
