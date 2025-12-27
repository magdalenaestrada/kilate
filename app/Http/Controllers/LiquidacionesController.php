<?php

namespace App\Http\Controllers;

use App\Models\Liquidacion;
use App\Models\LqCliente;
use App\Models\Proceso;
use App\Models\PsEstadoPeso;
use App\Models\Reactivo;
use App\Models\TipoCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Termwind\Components\Li;

class LiquidacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:gestionar liquidaciones');
    }

    public function index()
    {
        $procesos = Proceso::with(['lote', 'programacion'])
            ->leftJoin('pl_programaciones', 'procesos.id', '=', 'pl_programaciones.proceso_id')
            ->orderBy('pl_programaciones.fecha_inicio', 'asc')
            ->where(function ($q) {
                $q->where('estado', 'F')
                    ->orWhere('estado', 'L');
            })
            ->where('molienda', false)
            ->select('procesos.*')
            ->paginate(100);

        return view('liquidacion.index', compact('procesos'));
    }

    public function liquidar($procesoId)
    {
        $proceso = Proceso::with([
            'pesos.lote',
            'pesosOtrasBal.lote',
            'consumosreactivos.reactivo.detalles',
            'devolucionesReactivos.reactivo.detalles'
        ])->findOrFail($procesoId);

        $ultimoDolar = TipoCambio::latest('created_at')
            ->value('valor');

        $reactivos = Reactivo::with('detalles')->get();
        $clientes = LqCliente::all();

        return view('liquidacion.liquidar', compact('proceso', 'reactivos', 'clientes', 'ultimoDolar'));
    }
    public function store(Request $request)
    {
        $user = Auth::id();

        $ultimoDolar = TipoCambio::latest('created_at')
            ->value('valor');

        if ($request->dolar != $ultimoDolar) {
            $tipo = TipoCambio::create([
                'valor' => $request->dolar,
                'usuario_id' => $user,
            ]);
        }
        $validated = $request->validate([
            'suma_proceso' => 'required|numeric',
            'suma_exceso_reactivos' => 'required|numeric',
            'suma_balanza' => 'required|numeric',
            'suma_comedor' => 'required|numeric',
            'suma_laboratorio' => 'required|numeric',
            'suma_prueba_metalurgica' => 'required|numeric',
            'suma_descoche' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'igv' => 'required|numeric',
            'total' => 'required|numeric',
            'fecha' => 'required|date',
            'precio_unitario_proceso' => 'required|numeric',
            'cantidad_procesada_tn' => 'required|numeric',
            'precio_unitario_laboratorio' => 'required|numeric',
            'cantidad_muestras' => 'required|numeric',
            'precio_unitario_balanza' => 'required|numeric',
            'cantidad_pesajes' => 'required|numeric',
            'precio_prueba_metalurgica' => 'required|numeric',
            'cantidad_pruebas_metalurgicas' => 'required|numeric',
            'precio_descoche' => 'required|numeric',
            'cantidad_descoche' => 'required|numeric',
            'precio_unitario_comida' => 'required|numeric',
            'cantidad_comidas' => 'required|numeric',
            'gastos_adicionales' => 'required|numeric',
        ]);

        $proceso = Proceso::findOrFail($request->proceso_id);
        $proceso->update([
            "estado" => "L"
        ]);
        $cliente_id = $proceso->lote->lq_cliente_id;

        Liquidacion::create([
            'suma_proceso' => $request->suma_proceso,
            'suma_exceso_reactivos' => $request->suma_exceso_reactivos,
            'suma_balanza' => $request->suma_balanza,
            'user_id' => $user,
            'suma_comedor' => $request->suma_comedor,
            'suma_laboratorio' => $request->suma_laboratorio,
            'suma_prueba_metalurgica' => $request->suma_prueba_metalurgica,
            'suma_descoche' => $request->suma_descoche,
            'subtotal' => $request->subtotal,
            'igv' => $request->igv,
            'total' => $request->total,
            'fecha' => $request->fecha,
            'cliente_id' => $cliente_id,
            'proceso_id' => $request->proceso_id,
            'precio_unitario_proceso' => $request->precio_unitario_proceso,
            'cantidad_procesada_tn' => $request->cantidad_procesada_tn,
            'precio_unitario_laboratorio' => $request->precio_unitario_laboratorio,
            'cantidad_muestras' => $request->cantidad_muestras,
            'precio_unitario_balanza' => $request->precio_unitario_balanza,
            'cantidad_pesajes' => $request->cantidad_pesajes,
            'precio_prueba_metalurgica' => $request->precio_prueba_metalurgica,
            'cantidad_pruebas_metalurgicas' => $request->cantidad_pruebas_metalurgicas,
            'precio_descoche' => $request->precio_descoche,
            'cantidad_descoche' => $request->cantidad_descoche,
            'precio_unitario_comida' => $request->precio_unitario_comida,
            'cantidad_comidas' => $request->cantidad_comidas,
            'gastos_adicionales' => $request->gastos_adicionales,
        ]);

        foreach ($proceso->pesos as $peso) {
            PsEstadoPeso::where('peso_id', $peso->NroSalida)
                ->update(['estado_id' => 3]);
        }

        return redirect()
            ->route('liquidaciones')
            ->with('success', 'Liquidado correctamente');
    }

    public function print($id)
    {
        $liquidacion = Liquidacion::with([
            'proceso.consumosreactivos',
            'proceso.devolucionesReactivos',
            'proceso.pesosOtrasBal'
        ])->findOrFail($id);

        $reactivos = Reactivo::all();

        return view('liquidacion.print', compact('liquidacion', 'reactivos'));
    }
}
