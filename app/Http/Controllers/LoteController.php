<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Lote;
use App\Models\LqCliente;
use App\Models\Peso;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class LoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver lotes')->only(['index', 'show', 'pesosEnCancha']);
        $this->middleware('permission:crear lotes')->only(['create', 'store']);
        $this->middleware('permission:editar lotes')->only(['update']);
        $this->middleware('permission:eliminar lotes')->only(['destroy']);
        $this->middleware('permission:gestionar lotes');
    }
    public function index()
    {
        $lotes = Lote::with('cliente')
            ->where('codigo', 'NOT LIKE', 'COM-%')
            ->orderBy('created_at', "desc")
            ->paginate(10);
        $clientes = LqCliente::withCount('lotes')->get();
        return view('lotes.index', compact('lotes', 'clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'codigo' => 'required|string|unique:lotes,codigo',
                'nombre' => 'required|string|unique:lotes,nombre',
            ]);

            $lote = Lote::create([
                "lq_cliente_id" => $request->lq_cliente_id,
                'codigo' => strtoupper($request->codigo),
                'nombre' => strtoupper($request->nombre),
                'usuario_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lote creado exitosamente.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado: ' . $e->getMessage()
            ], 500);
        }
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
    public function update(Request $request, $id)
    {
        $lote = Lote::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|unique:lotes,nombre,' . $lote->id,
            'codigo' => 'required|unique:lotes,codigo,' . $lote->id,
            'lq_cliente_id' => 'nullable|exists:lq_clientes,id',

        ]);

        $lote->update([
            'codigo' => strtoupper($request->codigo),
            'nombre' => strtoupper($request->nombre),
            'lq_cliente_id' => $request->lq_cliente_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lote actualizado correctamente.'
        ]);
    }

    public function destroy(Request $request)
    {
        try {
            $lote = Lote::findOrFail($request->loteId);

            if ($lote->pesos()->exists() || $lote->procesos()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el lote porque tiene registros asociados (pesos o procesos).'
                ]);
            }

            $lote->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lote eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el lote: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pesosEnCancha($loteId)
    {
        $pesos = Peso::whereHas('estado', function ($q) {
            $q->where('ps_estados_pesos.estado_id', 1);
        })
            ->whereHas('lote', function ($q) use ($loteId) {
                $q->where('lotes.id', $loteId);
            })
            ->with(['estado', 'lote'])
            ->distinct()
            ->get();

        return response()->json($pesos);
    }
    public function findLote(Request $request)
    {
        return Lote::where('codigo', 'LIKE', 'COM -%')
            ->where('nombre', 'LIKE', '%' . $request->term . '%')
            ->limit(10)
            ->get(['id', 'nombre']);
    }
}
