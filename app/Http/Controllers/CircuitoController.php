<?php

namespace App\Http\Controllers;

use App\Models\Circuito;
use Illuminate\Http\Request;

class CircuitoController extends Controller
{
    public function index()
    {
        $circuitos = Circuito::orderBy('id', 'desc')->paginate(10);
        return view('circuitos.index', compact('circuitos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        Circuito::create($request->all());

        return response()->json(['success' => true, 'message' => 'Circuito creado correctamente']);
    }

    public function edit($id)
    {
        $circuito = Circuito::findOrFail($id);
        return response()->json($circuito);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        $circuito = Circuito::findOrFail($id);
        $circuito->update($request->all());

        return response()->json(['success' => true, 'message' => 'Circuito actualizado correctamente']);
    }

    public function destroy($id)
    {
        $circuito = Circuito::findOrFail($id);

        if ($circuito->procesos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar este circuito porque tiene procesos asociados.'
            ], 400);
        }

        $circuito->delete();

        return response()->json([
            'success' => true,
            'message' => 'Circuito eliminado correctamente.'
        ]);
    }
}
