<?php

namespace App\Services;

use App\Enums\Models\ModelStatusEnum;
use App\Helpers\HTMLHelper;
use App\Http\Requests\SubmmitDetalleOrdenServicioRequest;
use App\Models\DetalleOrdenServicio;
use App\Models\OrdenServicio;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class DetalleServicioService.
 */
class DetalleServicioService
{
    public function datatable($orden_servicio_id)
    {
        $model = DetalleOrdenServicio::where("orden_servicio_id", $orden_servicio_id)->orderBy("id", "asc");
        return DataTables::of($model)
            ->addIndexColumn()
            ->only([
                "orden_servicio_id",
                "descripcion",
                "cantidad",
                "precio_unitario",
                "subtotal",
                "estado"
            ])->addColumn('estado', function ($model) {
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
                        "class" => "btn-xs pt-1 btn-warning editarDetalle",
                        "text" => '<i class="fa-solid fa-pen"></i>',
                        "data" => [
                            "id" => $model->id,
                        ],
                    ],
                    "delete" => [
                        "element" => "a",
                        "href" => "#!",
                        "text" => '<i class="fa-solid fa-trash"></i>',
                        "class" => "btn-xs pt-1 btn-danger activarDesactivarDetalle",
                        "data" => [
                            "action" => route("orden-servicio.detalle.eliminar", ["detalle" => $model->id]),
                            "type-action" => "2",
                        ],
                    ],
                ];
                $btnEditar = "";
                $btnEliminar = "";
                $btnEditar = HTMLHelper::generarButton($menu["edit"]);
                $btnEliminar = HTMLHelper::generarButton($menu["delete"]);
                return "{$btnEditar} {$btnEliminar}";
            })->rawColumns(['estado', 'opciones']);
    }

    public function guardar(string|int $orden_servicio_id, SubmmitDetalleOrdenServicioRequest $request)
    {
        $orden_servicio = OrdenServicio::findOrFail($orden_servicio_id);
        $insert = [
            "orden_servicio_id" => $orden_servicio_id,
            "descripcion" => $request->descripcion,
            "cantidad" => $request->cantidad,
            "precio_unitario" => $request->precio_unitario,
            "subtotal" => $request->subtotal,
        ];

        $detalle = DB::transaction(function () use ($insert) {
            return DetalleOrdenServicio::create($insert);
        });
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($detalle)
            ->log("Se agregó un detalle de servicio a la orden {$orden_servicio->codigo}");
        return $detalle;
    }
    public function editar(string|int $detalle_orden_id, SubmmitDetalleOrdenServicioRequest $request)
    {
        $detalle = DetalleOrdenServicio::findOrFail($detalle_orden_id);
        $update = [
            "descripcion" => $request->descripcion,
            "cantidad" => $request->cantidad,
            "precio_unitario" => $request->precio_unitario,
            "subtotal" => $request->subtotal,
        ];
        $detalle->update($update);
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($detalle)
            ->log("Se editó un detalle de servicio a la orden {$detalle->orden_servicio->codigo}");
        return $detalle;
    }
    public function eliminar(DetalleOrdenServicio $detalle)
    {
        // Hacer el log antes de eliminar, para obtener el ID y datos
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($detalle)
            ->log("Se eliminó un detalle de servicio a la orden {$detalle->orden_servicio->codigo}");

        $detalle->delete();
    }
}
