<?php

namespace App\Services;

use App\Enums\Models\EstadosProcesosEnum;
use App\Enums\Models\ModelStatusEnum;
use App\Helpers\HTMLHelper;
use App\Http\Requests\SubmitOrdenServicioRequest;
use App\Models\DetalleOrdenServicio;
use App\Models\OrdenServicio;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class OrdenServicioService.
 */
class OrdenServicioService
{
    public function datatable(Request $request)
    {
        $model = OrdenServicio::with('proveedor')->orderBy("id", "desc");
        return DataTables::of($model)
            ->addIndexColumn()
            ->only([
                "codigo",
                "proveedor_id",
                "fecha_inicio",
                "fecha_fin",
                "descripcion",
                "costo_final",
                "observaciones",
                "estado_servicio",
                "estado",
            ])->addColumn('proveedor_id', function ($model) {
                return $model->proveedor->razon_social;
            })->addColumn("estado_servicio", function ($model) {
                $estado = EstadosProcesosEnum::from($model->estado_servicio);
                return match ($estado) {
                    EstadosProcesosEnum::PENDIENTE => HTMLHelper::badge('PENDIENTE', 'light'),
                    EstadosProcesosEnum::PROCESO => HTMLHelper::badge('PROCESO', 'warning'),
                    EstadosProcesosEnum::FINALIZADO => HTMLHelper::badge('FINALIZADO', 'success'),
                    EstadosProcesosEnum::CANCELADO => HTMLHelper::badge('CANCELADO', 'danger'),
                };
            })->addColumn("estado", function ($model) {
                $estado = ModelStatusEnum::from($model->estado);
                $color = $estado === ModelStatusEnum::INACTIVO ? 'danger' : 'success';
                return HTMLHelper::badge($estado, $color);
            })->addColumn('opciones', function ($model) {
                $is_delete = ModelStatusEnum::from($model->estado) === ModelStatusEnum::INACTIVO;
                $menu = [
                    "edit" => [
                        "title" => "Editar",
                        "element" => "a",
                        "href" => "#!",
                        "class" => "btn-xs pt-1 btn-warning editarOrden",
                        "text" => '<i class="fa-solid fa-pen"></i>',
                        "data" => [
                            "id" => $model->id,
                        ],
                    ],

                    "view" => [
                        "title" => "Ver",
                        "element" => "a",
                        "href" => "#!",
                        "class" => "btn-xs pt-1 btn-warning verOrden",
                        "text" => '<i class="fa-solid fa-eye"></i>',
                        "data" => [
                            "id" => $model->id,
                        ],
                    ],

                    "pagar" => [
                        "title" => "Pagar",
                        "element" => "a",
                        "href" => route("orden-servicio.pagar", ["orden" => $model->id]),
                        "class" => "btn-xs pt-1 btn-info",
                        "text" => '<i class="fa-solid fa-list"></i>',
                    ],

                    "proceso" => [
                        "element" => "a",
                        "href" => "#!",
                        "text" => '<i class="fa-solid fa-trash"></i>',
                        "class" => "btn-xs pt-1 btn-danger procesoOrden",
                        "data" => [
                            "action" => route("orden-servicio.proceso", ["orden" => $model->id]),
                        ],
                    ],

                    "completar" => [
                        "element" => "a",
                        "href" => "#!",
                        "text" => '<i class="fa-solid fa-trash"></i>',
                        "class" => "btn-xs pt-1 btn-danger completarOrden",
                        "data" => [
                            "action" => route("orden-servicio.completar", ["orden" => $model->id]),
                        ],
                    ],

                    "delete" => [
                        "element" => "a",
                        "href" => "#!",
                        "text" => '<i class="fa-solid fa-trash"></i>',
                        "class" => "btn-xs pt-1 btn-danger activarDesactivarDetalle",
                        "data" => [
                            "action" => route("orden-servicio.eliminar", ["orden" => $model->id]),
                            "type-action" => "2",
                        ],
                    ],
                ];
                $btnEditar = "";
                $btnVer = "";
                $btnPagar = "";
                $btnProceso = "";
                $btnCompletar = "";
                $btnEliminar = "";
                if ($model->estado_servicio == "P") {
                    $btnEditar = HTMLHelper::generarButton($menu["edit"]);
                    $btnVer = HTMLHelper::generarButton($menu["view"]);
                    $btnPagar = HTMLHelper::generarButton($menu["pagar"]);
                    $btnProceso = HTMLHelper::generarButton($menu["proceso"]);
                    $btnCompletar = HTMLHelper::generarButton($menu["completar"]);
                    $btnEliminar = HTMLHelper::generarButton($menu["delete"]);
                }
                else if ($model->estado_servicio == "E") {
                    $btnEditar = HTMLHelper::generarButton($menu["edit"]);
                    $btnVer = HTMLHelper::generarButton($menu["view"]);
                    $btnPagar = HTMLHelper::generarButton($menu["pagar"]);
                    $btnCompletar = HTMLHelper::generarButton($menu["completar"]);
                    $btnEliminar = HTMLHelper::generarButton($menu["delete"]);
                }
                else{
                    $btnVer = HTMLHelper::generarButton($menu["view"]);
                    $btnPagar = HTMLHelper::generarButton($menu["pagar"]);
                }

                return "{$btnEditar} {$btnVer} {$btnPagar} {$btnCompletar} {$btnEliminar} {$btnProceso}";
            })->rawColumns(['estado', 'estado_servicio', 'opciones']);
    }

    public function guardar(SubmitOrdenServicioRequest $request)
    {
        $hoy = Carbon::now('America/Lima');
        $proveedor = Proveedor::updateOrCreate(
            ['ruc' => $request->documento_proveedor],
            [
                'razon_social' => $request->proveedor,
                'telefono' => $request->telefono_proveedor,
                'direccion' => $request->direccion_proveedor,
            ]
        );
        $insert = [
            "proveedor_id" => $proveedor->id,
            "fecha_creacion" => $hoy,
            "fecha_inicio" => $request->fecha_inicio,
            "fecha_fin" => $request->fecha_fin,
            "descripcion" => $request->descripcion,
            "costo_estimado" => $request->costo_estimado,
            "costo_final" => $request->costo_final,
            "observaciones" => $request->observaciones,
        ];
        try {
            $orden_servicio = DB::transaction(function () use ($insert, $request) {
                $orden_servicio = OrdenServicio::create($insert);
                $detalles = json_decode($request->detalles, true);
                foreach ($detalles["detalles"] as $detalle) {

                    DetalleOrdenServicio::create([
                        "orden_servicio_id" => $orden_servicio->id,
                        "descripcion" => $detalle['descripcion'],
                        "cantidad" => $detalle['cantidad'],
                        "precio_unitario" => $detalle['precio_unitario'],
                        "subtotal" => $detalle['subtotal'],
                    ]);
                }
                return $orden_servicio;
            });

            activity()
                ->performedOn($orden_servicio)
                ->log('Se guardó un orden_servicio');

            return $orden_servicio;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
    public function editar(string|int $id, SubmitOrdenServicioRequest $request)
    {
        $update = [
            "fecha_inicio" => $request->fecha_inicio,
            "fecha_fin" => $request->fecha_fin,
            "descripcion" => $request->descripcion,
            "costo_estimado" => $request->costo_estimado,
            "costo_final" => $request->costo_final,
            "observaciones" => $request->observaciones,
        ];

        try {
            $orden_servicio = DB::transaction(function () use ($update, $id) {
                $orden_servicio = OrdenServicio::findOrFail($id);
                $orden_servicio->update($update);
                return $orden_servicio;
            });

            activity()
                ->causedBy(auth()->user()) // registra el usuario que hizo la acción
                ->performedOn($orden_servicio)
                ->log("Se editó una orden de servicio código {$orden_servicio->codigo}");

            return $orden_servicio;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function eliminar(OrdenServicio $orden_servicio)
    {
        // Hacer el log antes de eliminar, para obtener el ID y datos
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($orden_servicio)
            ->log("Se eliminó un detalle de servicio a la orden {$orden_servicio->codigo}");

        $orden_servicio->delete();
    }
    public function completar(OrdenServicio $orden_servicio){
        $orden_servicio->update(['estado_servicio' => 'F']);
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($orden_servicio)
            ->log("Se completó el servicio {$orden_servicio->codigo}");

        $orden_servicio->save();
    }
    public function proceso(OrdenServicio $orden_servicio){
        $orden_servicio->update(['estado_servicio' => 'E']);
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($orden_servicio)
            ->log("El servicio {$orden_servicio->codigo} está siendo completado");

        $orden_servicio->save();
    }
}
