<?php

namespace App\Http\Controllers;

use App\Models\ConsumoDevolucionReactivo;
use App\Models\ConsumoReactivo;
use App\Models\DevolucionReactivo;
use App\Models\PlProgramacion;
use App\Models\Proceso;
use App\Models\ProcesoPeso;
use App\Models\Reactivo;
use App\Models\StockReactivo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class ProcesoController extends Controller

{

    public function __construct()
    {
        $this->middleware('permission:ver procesos')->only(['index', 'show', 'edit']);
        $this->middleware('permission:crear procesos')->only(['create', 'store', 'guardar_consumo']);
        $this->middleware('permission:editar procesos')->only([
            'update',
            'actualizar_consumo',
            'actualizar_devolucion',
            'finalizar'
        ]);
        $this->middleware('permission:eliminar procesos')->only(['destroy']);
        $this->middleware('permission:gestionar procesos');
        
    }

    public function index()
    {
        $procesos = Proceso::with(['lote', 'programacion'])
            ->where("molienda", false)
            ->leftJoin('pl_programaciones', 'procesos.id', '=', 'pl_programaciones.proceso_id')
            ->orderByRaw("CASE WHEN procesos.estado = 'F' THEN 1 ELSE 0 END ASC")
            ->orderBy('pl_programaciones.fecha_inicio', 'asc')
            ->select('procesos.*')
            ->paginate(100);

        return view('procesos.index', compact('procesos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $estado_programado = PsEstado::where('nombre', 'PROGRAMADO');

        $proceso = Proceso::Create([
            'peso_total' => $request->peso_total,
            'lote_id' => $request->lote_id,
            'circuito' => $request->circuito
        ]);


        $pesosIds = json_decode($request->pesosIds, true);


        foreach ($pesosIds as $pesoId) {

            // $estadopeso = PsEstadoPeso::updateOrCreate(
            //     ['peso_id' => $pesoId],
            //     [
            //         'peso_id' => $request->pesoId,
            //         'estado_id' => $estado_programado->id,
            //     ]
            // );}

            // Manually create entries in the intermediate table or structure
            ProcesoPeso::create([
                'proceso_id' => $proceso->id,
                'peso_id' => $pesoId
            ]);
        }


        $programacion = PlProgramacion::Create([
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'proceso_id' => $proceso->id,

        ]);



        return response()->json(['message' => 'Data saved successfully']);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {

        $proceso = Proceso::findOrFail($id);
        $reactivos = Reactivo::all();
        $consumidos_ids = $proceso->consumosreactivos->pluck('reactivo_id');

        $stock_reactivos = StockReactivo::where("circuito", $proceso->circuito)
            ->whereNotIn("reactivo_id", $consumidos_ids)
            ->get();
        $todos_reactivos = StockReactivo::where("circuito", $proceso->circuito)->get();

        if ($proceso->pesosotrabal && $proceso->pesosotrabal->count() > 0) {
            $proceso->peso_total = $proceso->pesos->pluck('Neto')->sum() + $proceso->pesosotrabal->pluck('Neto')->sum();
            $proceso->save();
        }

        return view('procesos.edit', compact('proceso', 'reactivos', 'stock_reactivos', 'todos_reactivos'));
    }

    public function update(Request $request, string $id) {}

    public function destroy(Request $request)
    {

        $proceso = Proceso::findOrFail($request->procesoId);

        $proceso->programacion->delete();

        ProcesoPeso::where('proceso_id', $proceso->id)->delete();

        foreach ($proceso->consumosreactivos as $consumoreactivo) {
            $consumoreactivo->delete();
        }

        foreach ($proceso->devolucionesReactivos as $devolucionreactivo) {
            $devolucionreactivo->delete();
        }


        foreach ($proceso->pesosotrabal as $peso) {
            $peso->delete();
        }


        $proceso->delete();
    }

    public function stock_disponible($reactivo_id, $circuito)
    {
        $stock = StockReactivo::where('id', $reactivo_id)
            ->where('circuito', $circuito)
            ->sum('stock');

        return response()->json(['stock' => $stock]);
    }

    public function guardar_consumo(Request $request)
    {
        $user_id = Auth::user()->id;

        $request->validate([
            'proceso_id' => 'required',
            'reactivo_id' => 'required|exists:stock_reactivos_cancha,id',
            'cantidad' => 'required|numeric|min:0.0001',
            'fecha' => 'required|date'
        ]);

        $proceso = Proceso::find($request->proceso_id);
        $stock_reactivo = StockReactivo::findOrFail($request->reactivo_id);
        $stock = StockReactivo::where('id', $request->reactivo_id)
            ->where('circuito', $proceso->circuito)
            ->sum('stock');

        if ($request->cantidad > $stock) {
            return response()->json([
                'success' => false,
                'message' => "Solo puedes usar $stock kg del reactivo seleccionado."
            ]);
        }

        $consumo = ConsumoReactivo::create([
            'cantidad' => $request->cantidad,
            'proceso_id' => $proceso->id,
            'reactivo_id' => $stock_reactivo->reactivo_id,
            'fecha' => $request->fecha,
            'usuario_id' => $user_id,
        ]);

        $devolucion = DevolucionReactivo::create([
            'cantidad' => $stock - $request->cantidad,
            'proceso_id' => $proceso->id,
            'reactivo_id' => $stock_reactivo->reactivo_id,
            'fecha' => $request->fecha,
            'usuario_id' => $user_id,

        ]);

        StockReactivo::where('id', $request->reactivo_id)
            ->where('circuito', $proceso->circuito)
            ->decrement('stock', $request->cantidad);

        return response()->json(['success' => true]);
    }

    public function actualizar_consumo(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0.0001',
            'fecha' => 'required|date',
        ]);

        $consumo = ConsumoReactivo::findOrFail($id);
        $proceso = Proceso::findOrFail($consumo->proceso_id);

        $stock = StockReactivo::where('reactivo_id', $consumo->reactivo_id)
            ->where('circuito', $proceso->circuito);

        $stock_actual = $stock->sum('stock');
        $consumo_anterior = $consumo->cantidad;

        $devolucion = DevolucionReactivo::where('proceso_id', $proceso->id)
            ->where('reactivo_id', $consumo->reactivo_id)
            ->first();

        $cantidad_devuelta_anterior = $devolucion ? $devolucion->cantidad : 0;
        $stock_anterior = $consumo_anterior + $cantidad_devuelta_anterior;
        $diferencia_consumo = $request->cantidad - $consumo_anterior;
        $producto = $consumo->reactivo->producto->nombre_producto;
        if ($request->cantidad > $stock_anterior) {
            return response()->json([
                'success' => false,
                'message' => "Tu stock  del reactivo $producto en ese entonces era solo de $stock_anterior, no puedes superar el consumo"
            ], 400);
        }

        DB::transaction(function () use ($consumo, $request, $stock, $diferencia_consumo, $devolucion) {

            if ($diferencia_consumo > 0) {
                $stock->decrement('stock', $diferencia_consumo);
            } elseif ($diferencia_consumo < 0) {
                $stock->increment('stock', abs($diferencia_consumo));
            }

            $consumo->update([
                'cantidad' => $request->cantidad,
                'fecha' => $request->fecha,
            ]);

            if ($devolucion) {
                if ($diferencia_consumo > 0) {
                    $devolucion->decrement('cantidad', $diferencia_consumo);
                } elseif ($diferencia_consumo < 0) {
                    $devolucion->increment('cantidad', abs($diferencia_consumo));
                }
            }
        });

        return response()->json(['success' => true]);
    }

    public function actualizar_devolucion(Request $request, $id)
    {
        $request->validate([
            'reactivo_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'proceso_id' => 'required|integer'
        ]);

        $devolucion = DevolucionReactivo::findOrFail($id);

        $devolucion->update([
            'reactivo_id' => $request->reactivo_id,
            'cantidad' => $request->cantidad,
            'fecha' => $request->fecha,
        ]);

        return response()->json(['success' => true]);
    }

    public function finalizar($id)
    {
        $proceso = Proceso::findOrFail($id);

        if ($proceso->estado === 'F') {
            return back()->with('error', 'El proceso ya está finalizado.');
        }

        $proceso->estado = 'F';
        $proceso->save();

        return redirect()
            ->route("procesos")
            ->with('success', 'Proceso finalizado correctamente.');
    }
}
