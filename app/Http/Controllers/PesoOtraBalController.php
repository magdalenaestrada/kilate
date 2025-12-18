<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\PesoOtraBal;
use App\Models\PlProgramacion;
use App\Models\Proceso;
use App\Models\PsEstado;
use Illuminate\Http\Request;

class PesoOtraBalController extends Controller
{

    public function index()
    {
        $estados = PsEstado::all();
        $lotes = Lote::all();
        return view('otros-pesos.index', compact('estados', 'lotes'));
    }

    public function pesos(Request $request)
    {
        $query = PesoOtraBal::query();

        if ($request->filled('id')) {
            $query->where('id', 'like', $request->id . '%');
        }
        if ($request->filled('producto')) {
            $query->where('producto', 'like', '%' . $request->producto . '%');
        }
        if ($request->filled('placa')) {
            $query->where('placa', 'like', '%' . $request->placa . '%');
        }
        if ($request->filled('conductor')) {
            $query->where('conductor', 'like', '%' . $request->conductor . '%');
        }
        if ($request->filled('razon')) {
            $query->whereHas('lote.cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->razon . '%');
            });
        }

        if ($request->filled('origen')) {
            $query->where('origen', 'like', '%' . $request->origen . '%');
        }
        if ($request->filled('destino')) {
            $query->where('destino', 'like', '%' . $request->destino . '%');
        }
        if ($request->filled('neto')) {
            $query->where('neto', $request->neto);
        }
        if ($request->filled('observacion')) {
            $query->where('observacion', 'like', '%' . $request->observacion . '%');
        }
        if ($request->filled('desde')) {
            $query->whereDate('fechai', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fechas', '<=', $request->hasta);
        }

        if ($request->filled('lote')) {
            $query->whereHas('lote', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->lote . '%');
            });
        }

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        $sumaNetos = $query->sum('Neto');

        $pesos = $query->orderBy('id', 'desc')->paginate(100);

        $estados = PsEstado::all();
        $lotes = Lote::all();

        if ($request->ajax()) {
            return view('otros-pesos.partials.tabla', compact('pesos', 'estados', 'lotes', 'sumaNetos'))->render();
        }

        return view('otros-pesos.index', compact('pesos', 'estados', 'lotes', 'sumaNetos'));
    }

    public function guardar(Request $request)
    {

        $razon_id = Lote::findOrFail("$request->lote_id")->lq_cliente_id;
        PesoOtraBal::create([
            'fechai' => str_replace('T', ' ', $request->fechai),
            'fechas' => $request->fechas
                ? str_replace('T', ' ', $request->fechas)
                : null,
            'lote_id' => $request->lote_id,
            'bruto' => $request->bruto,
            'tara' => $request->tara,
            'neto' => $request->neto,
            'placa' => $request->placa,
            'observacion' => $request->observacion,
            'producto' => $request->producto,
            'conductor' => $request->conductor,
            'guia' => $request->guia,
            'guiat' => $request->guiat,
            'origen' => $request->origen,
            'destino' => $request->destino,
            'balanza' => $request->balanza,
            'razon_social_id' => $razon_id,
            'estado_id' => 1,
        ]);

        return response()->json([
            'success' => true
        ]);
    }


    public function store(Request $request, $id)
    {
        $programacion = PlProgramacion::findOrFail($id);
        $validated = $request->validate([
            'neto' => 'required',
        ]);

        $peso_otra_bal = PesoOtraBal::create([
            'fechai' => $request->fechai,
            'fechas' => $request->fechas,
            'bruto' => $request->bruto,
            'tara' => $request->tara,
            'neto' => $request->neto,
            'placa' => $request->placa,
            'observacion' => $request->observacion,
            'producto' => $request->producto,
            'conductor' => $request->conductor,
            'razon_social_id' => $programacion->proceso->lote->lq_cliente_id,
            'guia' => $request->guia,
            'guiat' => $request->guiat,
            'origen' => $request->origen,
            'destino' => $request->destino,
            'balanza' => $request->balanza,
            'lote_id' => $programacion->proceso->lote_id,
            'proceso_id' => $programacion->proceso_id,
            'programacion_id' => $id,
            'estado_id' => 1,
        ]);

        $proceso = Proceso::findOrFail($programacion->proceso_id);
        $proceso->increment('peso_total', $request->neto);
    }

    public function guardar_molienda(Request $request, $id)
    {
        $proceso = Proceso::findOrFail($id);
        $validated = $request->validate(rules: [
            'neto' => 'required',
        ]);

        $peso_otra_bal = PesoOtraBal::create([
            'fechai' => $request->fechai,
            'fechas' => $request->fechas,
            'bruto' => $request->bruto,
            'tara' => $request->tara,
            'neto' => $request->neto,
            'placa' => $request->placa,
            'observacion' => $request->observacion,
            'producto' => $request->producto,
            'conductor' => $request->conductor,
            'razon_social_id' => $proceso->lote->lq_cliente_id,
            'guia' => $request->guia,
            'guiat' => $request->guiat,
            'origen' => $request->origen,
            'destino' => $request->destino,
            'balanza' => $request->balanza,
            'lote_id' => $proceso->lote_id,
            'proceso_id' => $id,
            'estado_id' => 1,
        ]);

        $proceso->increment('peso_total', $request->neto);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {


        $pesootrabal = PesoOtraBal::findOrFail($request->pesoId);

        $validated = $request->validate(rules: [
            'neto' => 'required',
        ]);



        $pesootrabal->update([
            'Fechai'       => $request->fechai,
            'Fechas'       => $request->fechas,
            'Bruto'        => $request->bruto,
            'Tara'         => $request->tara,
            'Neto'         => $request->neto,
            'Placa'        => $request->placa,
            'Observacion'  => $request->observacion,
            'Producto'     => $request->producto,
            'Conductor'    => $request->conductor,
            'Razonsocial'  => $request->razonsocial,
            'Guiar'        => $request->guiar,
            'Guiat'        => $request->guiat,
            'Origen'       => $request->origen,
            'Destino'      => $request->destino,
            'Balanza'      => $request->balanza,
        ]);

        // Optionally, return a response or redirect after update
        $pesootrabal->save();
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $pesoOtraBal = PesoOtraBal::findOrFail($id);

            if (in_array($pesoOtraBal->estado_id, [3, 4])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un peso en estado procesado o finalizado'
                ], 422);
            }

            $pesoOtraBal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Peso eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el peso: ' . $e->getMessage()
            ], 500);
        }
    }
    public function pesosOtrasLote($loteId)
    {
        return PesoOtraBal::with('estado')
            ->where('lote_id', $loteId)
            ->whereNull('programacion_id')
            ->where('estado_id', 1) // Solo los que están en CANCHA
            ->get()
            ->map(function ($otra_balanza) {
                return [
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
    }
}
