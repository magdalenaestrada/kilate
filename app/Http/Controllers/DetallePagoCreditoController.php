<?php

namespace App\Http\Controllers\OrdenServicio;

use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Request;

class DetallePagoCreditoController extends Controller
{
    public function index()
    {
        return view('proveedores.index');
    }

    public function busqueda_global(Request $request, ProveedorService $service): \Illuminate\Http\JsonResponse
    {
        try {
            if (!$request->ajax()){
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }
            $proveedor = $service->busqueda_global($request->all());
            return response()
                ->json([
                   "status" => ResponseStatusEnum::SUCCESS,
                   "proveedor" => $proveedor
                ]);
        }catch (\Exception $exception){
            return response()
                ->json([
                    "status" => ResponseStatusEnum::ERROR,
                    "message" => $exception->getMessage(),
                ]);
        }
    }
    public function data_table(Request $request, ProveedorService $service)
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
    public function guardar(SubmitProveedorRequest $request, ProveedorService $service)
    {
        try {
            if (!$request->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }
            $proveedor = $service->guardar($request);
            return response()->json([
                "status" => ResponseStatusEnum::SUCCESS,
                "proveedor" => $proveedor
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "status" => ResponseStatusEnum::ERROR,
                "message" => $exception->getMessage()
            ]);
        }
    }
    public function informacion(Tercero $proveedor, TerceroService $service)
    {
        try {
            if (!request()->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }
            if (!$service->verificarEstado($proveedor->id, ModelStatusEnum::ACTIVO)) {
                throw new \Exception("El proveedor está desactivado.");
            }
            return response()->json([
                "status" => ResponseStatusEnum::SUCCESS,
                "proveedor" => $proveedor
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "status" => ResponseStatusEnum::ERROR,
                "message" => $exception->getMessage()
            ]);
        }
    }
    public function editar($proveedor, SubmitProveedorRequest $request, ProveedorService $service)
    {

        try {
            if (!$request->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }

            $proveedor = $service->editar($proveedor, $request);
            return response()->json([
                "status" => ResponseStatusEnum::SUCCESS,
                "proveedor" => $proveedor
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "status" => ResponseStatusEnum::ERROR,
                "message" => $exception->getMessage()
            ]);
        }
    }
    public function desactivar(Tercero $proveedor, ProveedorService $service)
    {
        try {
            if (!request()->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }
            $service->desactivar($proveedor);
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

    public function activar(Tercero $proveedor, ProveedorService $service)
    {
        try {
            if (!request()->ajax()) {
                throw new \Exception("Solo se permiten consultas por AJAX.");
            }
            $service->activar($proveedor);
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
