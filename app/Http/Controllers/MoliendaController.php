<?php

namespace App\Http\Controllers;

use App\Models\FechaMoliendaProceso;
use App\Models\Molienda;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\LqCliente;
use App\Models\Peso;
use App\Models\PesoOtraBal;
use App\Models\PlProgramacion;
use App\Models\Proceso;
use App\Models\ProcesoPeso;
use App\Models\ProgramacionDetalle;
use App\Models\PsEstadoPeso;
use App\Models\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MoliendaController extends Controller
{
    public function index()
    {
        $procesos = Proceso::with(['lote', 'pesos', 'molienda'])
            ->where("molienda", true)
            ->orderByRaw("CASE WHEN procesos.estado = 'F' THEN 1 ELSE 0 END ASC")
            ->paginate(100);
        $lotes = Lote::with('pesos', 'pesos.estado', 'pesos.proceso')->get();
        $clientes = LqCliente::all();
        $tiempos = FechaMoliendaProceso::all();
        return view('programaciones.molienda.index', compact('procesos', 'lotes', 'clientes', 'tiempos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lote_id'       => 'required|exists:lotes,id',
            'pesos'         => 'required|array|min:1',
        ], [
            'lote_id.required'      => 'Debes seleccionar un lote.',
            'pesos.required'        => 'Debes seleccionar al menos un peso.',
        ]);

        try {
            DB::beginTransaction();

            $proceso = Proceso::create([
                'codigo' => $this->GenerarCodigoProceso($request),
                'lote_id' => $request->lote_id,
                'peso_total' => $request->peso_total,
                'circuito' => $request->circuito,
                'molienda' => 1,

            ]);


            foreach ($request->pesos as $pesoId) {
                ProcesoPeso::create([
                    'proceso_id' => $proceso->id,
                    'peso_id' => $pesoId,
                ]);

                $estado_peso = PsEstadoPeso::where('peso_id', $pesoId)->first();
                if ($estado_peso) {
                    $estado_peso->estado_id = 2;
                    $estado_peso->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Programación registrada correctamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la programación.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function pesosByProceso($id)
    {
        $proceso = Proceso::with(['pesos.loteAsignado'])->findOrFail($id);

        $pesos = Peso::whereHas('proceso', function ($q) use ($id) {
            $q->where('procesos.id', $id);
        })->with('estado', 'loteAsignado')->get();

        $pesosAsignados = $pesos->map(function ($peso) {
            $lote = $peso->loteAsignado->first();

            return [
                'tipo' => 'balanza',
                'NroSalida' => $peso->NroSalida,
                'Fechai' => $peso->Fechai,
                'Fechas' => $peso->Fechas,
                'Bruto' => $peso->Bruto,
                'Tara' => $peso->Tara,
                'Neto' => $peso->Neto,
                'Conductor' => $peso->Conductor,
                'estado_nombre' => $peso->estado->nombre ?? null,
                'estado_id' => $peso->estado->id ?? null,
                'Producto' => $peso->Producto,
                'Placa' => $peso->Placa,
                'lote_id' => $lote?->id,
            ];
        });

        $pesosOtrasBal = PesoOtraBal::with('estado')
            ->where('proceso_id', $id)
            ->get()
            ->map(function ($otra_balanza) {
                return [
                    'tipo' => 'manual',
                    'id' => $otra_balanza->id,
                    'fechai' => $otra_balanza->fechai,
                    'fechas' => $otra_balanza->fechas,
                    'bruto' => $otra_balanza->bruto,
                    'tara' => $otra_balanza->tara,
                    'neto' => $otra_balanza->neto,
                    'conductor' => $otra_balanza->conductor,
                    'estado_nombre' => $otra_balanza->estado->nombre ?? null,
                    'estado_id' => $otra_balanza->estado->id ?? null,
                    'balanza' => $otra_balanza->balanza,
                    'producto' => $otra_balanza->producto,
                    'placa' => $otra_balanza->placa,
                ];
            });

        $pesosTotales = $pesosAsignados->merge($pesosOtrasBal);

        return response()->json([
            'proceso' => [
                'id' => $proceso->id,
                'lote_id' => $proceso->lote_id,
                'circuito' => $proceso->circuito,
            ],
            'pesos' => $pesosTotales,
        ]);
    }


    public function imprimir($id)
    {
        $proceso = Proceso::with([
            'lote',
            'pesos',
            'pesosOtrasBal',
            'tiempos'
        ])->findOrFail($id);

        $molienda = Molienda::with('cliente', 'user')
            ->where('proceso_id', $id)->first();

        $fechas = $proceso->tiempos;

        return view('programaciones.molienda.liqui_molienda_prnt', compact(
            'proceso',
            'fechas',
            'molienda'
        ));
    }



    public function getTiempos($id)
    {
        $tiempos = FechaMoliendaProceso::where('proceso_id', $id)
            ->orderBy('fecha_inicio', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();
        $proceso = Proceso::findOrFail($id);

        return response()->json([
            'tiempos' => $tiempos,
            'tonelaje_inicial' => $proceso->peso_total
        ]);
    }

    public function liquidar($procesoId)
    {
        $proceso = Proceso::with([
            'pesos.lote',
            'pesosOtrasBal.lote',
        ])->findOrFail($procesoId);

        $ultimoDolar = TipoCambio::latest('created_at')
            ->value('valor');

        $fechas = FechaMoliendaProceso::where('proceso_id', $procesoId)
            ->orderBy('fecha_inicio', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();
        $clientes = LqCliente::all();

        return view('programaciones.molienda.liquidacion', compact('proceso', 'fechas', 'clientes', 'ultimoDolar'));
    }
    public function guardar_tiempo(Request $request)
    {
        $validated = $request->validate([
            'proceso_id'    => 'required|exists:procesos,id',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date',
            'hora_inicio'   => 'required',
            'hora_fin'      => 'required',
            'tonelaje'      => 'required',
        ]);

        $fechaMolienda = FechaMoliendaProceso::create([
            'proceso_id'    => $request->proceso_id,
            'fecha_inicio'  => $request->fecha_inicio,
            'fecha_fin'     => $request->fecha_fin,
            'hora_inicio'   => $request->hora_inicio,
            'hora_fin'      => $request->hora_fin,
            'tonelaje'      => $request->tonelaje,
        ]);

        return response()->json(['message' => 'Fecha de molienda creada exitosamente.', 'data' => $fechaMolienda], 201);
    }

    public function pesosPorProceso($procesoId)
    {
        $proceso = Proceso::with([
            'lote',
            'pesosOtrasBal.estado',
        ])->findOrFail($procesoId);

        $pesosAsignados = Peso::with('estado')
            ->where('proceso_id', $procesoId)
            ->get()
            ->map(function ($peso) {
                return [
                    'tipo' => 'balanza',
                    'NroSalida' => $peso->NroSalida,
                    'Fechas' => $peso->Fechas,
                    'Bruto' => $peso->Bruto,
                    'Tara' => $peso->Tara,
                    'Neto' => $peso->Neto,
                    'Horai' => $peso->Horai,
                    'Horas' => $peso->Horas,
                    'estado_id' => $peso->estado->id ?? null,
                    'estado_nombre' => $peso->estado->nombre ?? '-',
                ];
            });

        $pesosOtrasBal = PesoOtraBal::with('estado')
            ->where('proceso_id', $procesoId)
            ->get()
            ->map(function ($otra_balanza) {
                return [
                    'tipo' => 'manual',
                    'id' => $otra_balanza->id,
                    'fechai' => $otra_balanza->fechai,
                    'fechas' => $otra_balanza->fechas,
                    'bruto' => $otra_balanza->bruto,
                    'tara' => $otra_balanza->tara,
                    'neto' => $otra_balanza->neto,
                    'conductor' => $otra_balanza->conductor,
                    'estado_nombre' => $otra_balanza->estado->nombre ?? '-',
                    'estado_id' => $otra_balanza->estado->id ?? null,
                    'balanza' => $otra_balanza->balanza,
                    'producto' => $otra_balanza->producto,
                    'placa' => $otra_balanza->placa,
                ];
            });

        $pesosTotales = $pesosAsignados->merge($pesosOtrasBal);

        return response()->json([
            'proceso' => $proceso,
            'pesos' => $pesosTotales,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lote_id' => 'required|exists:lotes,id',
        ]);

        DB::transaction(function () use ($request, $id) {
            $programacion = PlProgramacion::findOrFail($id);
            $proceso = $programacion->proceso;

            ProcesoPeso::where('proceso_id', $proceso->id)->each(function ($pp) {
                if ($pp->peso) {
                    $estadoPeso = PsEstadoPeso::where('peso_id', $pp->peso->NroSalida)->first();
                    if ($estadoPeso) {
                        $estadoPeso->update(['estado_id' => 1]); // vuelve a CANCHA
                    }
                }
                $pp->delete();
            });

            ProgramacionDetalle::where('programacion_id', $programacion->id)->delete();


            $proceso->update([
                'lote_id' => $request->lote_id,
                'peso_total' => $request->peso_total,
                'circuito' => $request->circuito,
            ]);

            foreach ($request->pesos as $nro) {
                ProcesoPeso::create([
                    'proceso_id' => $proceso->id,
                    'peso_id' => $nro,
                ]);

                ProgramacionDetalle::create([
                    'programacion_id' => $programacion->id,
                    'peso_id' => $nro,
                ]);

                $estadoPeso = PsEstadoPeso::where('peso_id', $nro)->first();
                if ($estadoPeso) {
                    $estadoPeso->update(['estado_id' => 2]); // pasa a PROCESANDO
                }
            }
        });

        return response()->json(['success' => true]);
    }

    public function guardar_liquidacion(Request $request)
    {
        $user_id = Auth::id();

        $proceso = Proceso::findOrFail($request->proceso_id);
        $proceso->update([
            "estado" => "L"
        ]);
        $cliente_id = $proceso->lote->lq_cliente_id;

        Molienda::create([
            'suma_proceso' => $request->suma_proceso,
            'suma_balanza' => $request->suma_balanza,
            'suma_comedor' => $request->suma_comedor,
            'suma_prueba_metalurgica' => $request->suma_prueba_metalurgica,
            'subtotal' => $request->subtotal,
            'igv' => $request->igv,
            'total' => $request->total,
            'fecha_liquidacion' => $request->fecha,
            'cliente_id' => $cliente_id,
            'user_id' => $user_id,
            'proceso_id' => $request->proceso_id,
            'precio_unitario_proceso' => $request->precio_unitario_proceso,
            'cantidad_procesada_tn' => $request->cantidad_procesada_tn,
            'precio_unitario_balanza' => $request->precio_unitario_balanza,
            'cantidad_pesajes' => $request->cantidad_pesajes,
            'precio_prueba_metalurgica' => $request->precio_prueba_metalurgica,
            'cantidad_pruebas_metalurgicas' => $request->cantidad_pruebas_metalurgicas,
            'precio_unitario_comida' => $request->precio_unitario_comida,
            'cantidad_comidas' => $request->cantidad_comidas,
            'gastos_adicionales' => $request->gastos_adicionales,
        ]);

        foreach ($proceso->pesos as $peso) {
            PsEstadoPeso::where('peso_id', $peso->NroSalida)
                ->update(['estado_id' => 3]);
        }

        return redirect()
            ->route('molienda')
            ->with('success', 'Liquidado correctamente');
    }
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $programacion = PlProgramacion::findOrFail($id);
            $proceso = $programacion->proceso;

            ProcesoPeso::where('proceso_id', $proceso->id)->each(function ($pp) {
                if ($pp->peso) {
                    $estadoPeso = PsEstadoPeso::where('peso_id', $pp->peso->NroSalida)->first();
                    if ($estadoPeso) {
                        $estadoPeso->update(['estado_id' => 1]); // vuelve a CANCHA
                    }
                }
                $pp->delete();
            });

            ProgramacionDetalle::where('programacion_id', $programacion->id)->delete();

            $programacion->delete();

            if ($proceso) {
                $proceso->delete();
            }
        });

        return response()->json(['success' => true, 'deleted' => true]);
    }

    public function GenerarCodigoProceso($request)
    {
        $lote = Lote::findOrFail($request->lote_id);
        $numero = Proceso::where("lote_id", $lote->id)
            ->lockForUpdate()
            ->count();

        $nuevo = $numero + 1;

        return $lote->codigo . '.' . $nuevo;
    }
}
