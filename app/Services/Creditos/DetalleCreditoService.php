<?php

namespace App\Services;

use App\Enums\Models\EstadosProcesosEnum;
use App\Enums\Models\ModelStatusEnum;
use App\Helpers\HTMLHelper;
use App\Http\Requests\Credito\SubmitDetallePagoCreditoRequest;
use App\Http\Requests\Credito\SubmitPagoCreditoRequest;
use App\Models\DetallePagoCredito;
use App\Models\PagoCredito;
use Yajra\DataTables\Facades\DataTables;

class DetalleCreditoService
{
    public function datatable($credito_id)
    {
        $model = DetallePagoCredito::where("credito_id", $credito_id)->orderBy("id", "asc");
        return DataTables::of($model)
            ->addIndexColumn()
            ->only([
                "credito_id",
                "fecha_pago",
                "fecha_cancelado",
                "monto_pagar",
                "estado",
            ])->addColumn("estado", function ($model) {
                $estado = EstadosProcesosEnum::from($model->estado_crédito);
                return match ($estado) {
                    EstadosProcesosEnum::PENDIENTE => HTMLHelper::badge('PENDIENTE', 'light'),
                    EstadosProcesosEnum::PROCESO => HTMLHelper::badge('PROCESO', 'warning'),
                    EstadosProcesosEnum::FINALIZADO => HTMLHelper::badge('FINALIZADO', 'success'),
                    EstadosProcesosEnum::CANCELADO => HTMLHelper::badge('CANCELADO', 'danger'),
                };
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
                            "action" => route("creditos.detalle.eliminar", ["detalle" => $model->id]),
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

    public function guardar(string|int $credito_id, SubmitDetallePagoCreditoRequest $request)
    {
        $credito = PagoCredito::findOrFail($credito_id);
        $insert = [
            "credito_id" => $credito_id,
            "descripcion" => $request->descripcion,
            "cantidad" => $request->cantidad,
            "precio_unitario" => $request->precio_unitario,
            "subtotal" => $request->subtotal,
        ];

        $detalle = DB::transaction(function () use ($insert) {
            return DetallePagoCredito::create($insert);
        });
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($detalle)
            ->log("Se agregó una cuota al crédito {$credito->id}");
        return $detalle;
    }
    public function editar(string|int $detalle_orden_id, SubmitDetallePagoCreditoRequest $request)
    {
        $detalle = DetallePagoCredito::findOrFail($detalle_orden_id);
        $update = [
            "fecha_pago"=>$request->fecha_pago,
            "fecha_cancelado"=>$request->fecha_cancelado,
            "monto_pagar"=>$request->monto_pagar,
            "estado"=>$request->estado,
        ];
        $detalle->update($update);
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($detalle)
            ->log("Se editó un detalle al crédito {$detalle->credito->id}");
        return $detalle;
    }
    public function eliminar(DetallePagoCredito $detalle)
    {
        // Hacer el log antes de eliminar, para obtener el ID y datos
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($detalle)
            ->log("Se eliminó un detalle de crédito a la orden {$detalle->credito->id}");

        $detalle->delete();
    }
}
