<?php

namespace App\Services;

use App\Enums\Models\EstadosProcesosEnum;
use App\Enums\Models\ModelStatusEnum;
use App\Enums\TiemposEnum;
use App\Helpers\HTMLHelper;
use App\Http\Requests\Credito\SubmitPagoCreditoRequest;
use App\Models\DetallePagoCredito;
use App\Models\PagoCredito;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class OrdenServicioService.
 */
class CreditoService
{
    public function datatable()
    {
        $model = PagoCredito::orderBy("id", "desc");
        return DataTables::of($model)
            ->addIndexColumn()
            ->only([
                "codigo",
                "modelo_type",
                "modelo_id",
                "monto_total",
                "monto_restante",
                "fecha_inicio",
                "fecha_final",
                "tiempo",
                "estado"
            ])->addColumn("tiempo", function ($model) {
                $estado = TiemposEnum::from($model->tiempo);
                return match ($estado) {
                    TiemposEnum::SEMANAL => HTMLHelper::badge('SEMANAL', 'light'),
                    TiemposEnum::QUINCENAL => HTMLHelper::badge('QUINCENAL', 'light'),
                    TiemposEnum::MENSUAL => HTMLHelper::badge('MENSUAL', 'light'),
                };
            })->addColumn("estado", function ($model) {
                $estado = EstadosProcesosEnum::from($model->estado_servicio);
                return match ($estado) {
                    EstadosProcesosEnum::PENDIENTE => HTMLHelper::badge('PENDIENTE', 'light'),
                    EstadosProcesosEnum::PROCESO => HTMLHelper::badge('PROCESO', 'warning'),
                    EstadosProcesosEnum::FINALIZADO => HTMLHelper::badge('FINALIZADO', 'success'),
                    EstadosProcesosEnum::CANCELADO => HTMLHelper::badge('CANCELADO', 'danger'),
                };
            })->addColumn('opciones', function ($model) {
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
                } else if ($model->estado_servicio == "E") {
                    $btnEditar = HTMLHelper::generarButton($menu["edit"]);
                    $btnVer = HTMLHelper::generarButton($menu["view"]);
                    $btnPagar = HTMLHelper::generarButton($menu["pagar"]);
                    $btnCompletar = HTMLHelper::generarButton($menu["completar"]);
                    $btnEliminar = HTMLHelper::generarButton($menu["delete"]);
                } else {
                    $btnVer = HTMLHelper::generarButton($menu["view"]);
                    $btnPagar = HTMLHelper::generarButton($menu["pagar"]);
                }

                return "{$btnEditar} {$btnVer} {$btnPagar} {$btnCompletar} {$btnEliminar} {$btnProceso}";
            })->rawColumns(['estado', 'estado_servicio', 'opciones']);
    }
    public function guardar(SubmitPagoCreditoRequest $request)
    {
        $hoy = Carbon::now('America/Lima');
        $insert = [
            "monto_total"=>$request->monto_total,
            "monto_restante"=>$request->monto_restante,
            "fecha_inicio"=>$request->fecha_inicio,
            "fecha_final"=>$request->fecha_final,
            "tiempo"=>$request->tiempo
        ];
        try {
            $credito = DB::transaction(function () use ($insert, $request) {
                $credito = PagoCredito::create($insert);
                $montos = json_decode($request->montos, true);
                foreach ($montos["montos"] as $monto) {

                    DetallePagoCredito::create([
                        "credito_id" => $credito->id,
                        "descripcion" => $monto['descripcion'],
                        "cantidad" => $monto['cantidad'],
                        "precio_unitario" => $monto['precio_unitario'],
                        "subtotal" => $monto['subtotal'],
                    ]);
                }
                return $credito;
            });

            activity()
                ->performedOn($credito)
                ->log('Se guardó un credito');

            return $credito;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
    public function editar(string|int $id, SubmitPagoCreditoRequest $request)
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
            $credito = DB::transaction(function () use ($update, $id) {
                $credito = PagoCredito::findOrFail($id);
                $credito->update($update);
                return $credito;
            });

            activity()
                ->causedBy(auth()->user()) // registra el usuario que hizo la acción
                ->performedOn($credito)
                ->log("Se editó una orden de servicio código {$credito->codigo}");

            return $credito;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function eliminar(PagoCredito $credito)
    {
        // Hacer el log antes de eliminar, para obtener el ID y datos
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($credito)
            ->log("Se eliminó un crédito {$credito->codigo}");

        $credito->delete();
    }
    public function completar(PagoCredito $credito)
    {
        $credito->update(['estado_servicio' => 'F']);
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($credito)
            ->log("Se completó el crédito {$credito->codigo}");

        $credito->save();
    }
    public function proceso(PagoCredito $credito)
    {
        $credito->update(['estado_servicio' => 'E']);
        activity()
            ->causedBy(auth()->user()) // registra el usuario que hizo la acción
            ->performedOn($credito)
            ->log("El crédito {$credito->id} está siendo pagado");

        $credito->save();
    }
}
