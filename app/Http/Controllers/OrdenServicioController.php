<?php

namespace App\Http\Controllers;

use App\Exports\OrdenServicioExport;
use App\Exports\OrdenServicioFullExport;
use App\Http\Requests\OrdenServicio\SubmitOrdenServicioRequest;
use App\Models\DetalleOrdenServicio;
use App\Models\OrdenServicio;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class OrdenServicioController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:ver ordenes', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear ordenes', ['only' => ['create', 'store']]);
        $this->middleware('permission:cancelar ordenes', ['only' => ['cancelar', 'updatecancelar', 'cancelaralcredito', 'updatecancelaralcredito']]);
        $this->middleware('permission:recepcionar ordenes', ['only' => ['recepcionar', 'updaterecepcionar']]);
        //$this->middleware('permission:anular ordenes', ['only' => ['anular']]);
    }


    /**
     * Actualiza únicamente 'cotizacion' (sin tocar otros campos).
     */

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = OrdenServicio::orderBy('created_at', 'desc')->paginate(100);
        return view('ordenes.index', compact('ordenes'));
    }

    public function formulario()
    {
        return view('ordenes.formulario');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::all();
        return view('orden-servicio.create', compact('productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubmitOrdenServicioRequest $request)
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
            "codigo" => $this->generarCodigo()
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

            return redirect()
                ->route('orden-servicio.index')
                ->with('success', 'La orden de servicio se creó correctamente.');
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function generarCodigo()
    {
        $count = OrdenServicio::count() + 1; // cuenta todas las órdenes y suma 1
        $codigo = str_pad($count, 3, '0', STR_PAD_LEFT); // rellena con ceros a la izquierda (001, 002, 010, etc.)
        return "OS-" . $codigo;
    }


    public function datatable()
    {
        $model = OrdenServicio::with('proveedor')->orderBy('id', 'desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->make(true);
    }

    public function exportExcel()
    {
        return Excel::download(new OrdenServicioFullExport, 'ordenes_servicio_completo.xlsx');
    }

    /**
     * Display the specified resource.
     */

    public function show(OrdenServicio $ordenServicio)
    {
        // Carga relaciones necesarias
        $ordenServicio->load('proveedor', 'detalles');

        return view('ordenes.show', compact('ordenServicio'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $orden = OrdenServicio::with(['proveedor', 'detalles'])->findOrFail($id);
        return view('ordenes.edit', compact('orden'));
    }

    public function update(SubmitOrdenServicioRequest $request, $id)
    {
        $hoy = Carbon::now('America/Lima');

        // Validación básica (puedes reemplazar con tu FormRequest)
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string',
            'costo_estimado' => 'required|numeric|min:0',
            'costo_final' => 'required|numeric|min:0',
            'detalles' => 'required|json',
        ]);

        try {
            $orden_servicio = DB::transaction(function () use ($request, $id, $hoy) {

                // 1️⃣ Buscar la orden
                $orden = OrdenServicio::findOrFail($id);

                // 2️⃣ Actualizar proveedor (por si cambia)
                $proveedor = Proveedor::updateOrCreate(
                    ['ruc' => $request->documento_proveedor],
                    [
                        'razon_social' => $request->proveedor,
                        'telefono' => $request->telefono_proveedor,
                        'direccion' => $request->direccion_proveedor,
                    ]
                );

                // 3️⃣ Actualizar datos principales de la orden
                $orden->update([
                    "proveedor_id" => $proveedor->id,
                    "fecha_inicio" => $request->fecha_inicio,
                    "fecha_fin" => $request->fecha_fin,
                    "descripcion" => $request->descripcion,
                    "costo_estimado" => $request->costo_estimado,
                    "costo_final" => $request->costo_final,
                    "updated_at" => $hoy,
                ]);

                // 4️⃣ Actualizar detalles
                $detalles = json_decode($request->detalles, true);

                // Eliminar los detalles antiguos
                $orden->detalles()->delete();

                // Crear los nuevos detalles
                foreach ($detalles["detalles"] as $detalle) {
                    DetalleOrdenServicio::create([
                        "orden_servicio_id" => $orden->id,
                        "descripcion" => $detalle['descripcion'],
                        "cantidad" => $detalle['cantidad'],
                        "precio_unitario" => $detalle['precio_unitario'],
                        "subtotal" => $detalle['subtotal'],
                    ]);
                }

                return $orden;
            });

            // 5️⃣ Registrar en logs (activity)
            activity()
                ->performedOn($orden_servicio)
                ->log('Se actualizó una orden de servicio');

            return redirect()
                ->route('orden-servicio.index')
                ->with('success', 'La orden de servicio se actualizó correctamente.');
        } catch (\Exception $e) {
            // Si hay error, lo mostramos (opcional: puedes redirigir con mensaje)
            return redirect()
                ->back()
                ->with('error', 'Ocurrió un error al actualizar la orden: ' . $e->getMessage());
        }
    }

    public function anular($id)
    {
        try {
            $orden = OrdenServicio::findOrFail($id);

            // Cambia el estado
            $orden->estado_servicio = 'A'; // o 'ANULADO' según tu convención
            $orden->save();

            return redirect()
                ->route('orden-servicio.index')
                ->with('success', 'La orden de servicio ha sido anulada correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->route('orden-servicio.index')
                ->with('error', 'Ocurrió un error al anular la orden: ' . $e->getMessage());
        }
    }



    public function print(string $id)
    {

        $orden = OrdenServicio::with('detalles')->findOrFail($id);


        return view('ordenes.ticket', compact('orden'));
    }

    public function cancelar(string $id)
    {
        $orden = OrdenServicio::findOrFail($id);
        return view('ordenes.cancelar', compact('orden'));
    }

    public function updatecancelar(SubmitOrdenServicioRequest $request, string $id)
    {
        $request->validate([
            'comprobante_correlativo' => 'required',
            'fecha_cancelacion' => 'required',
            'fecha_emision_comprobante' => 'required',
            'tipopago' => 'required',

        ]);
        $orden = Inventarioingreso::findOrFail($id);


        if ($orden->estado !== 'PENDIENTE') {
            throw new HttpException(403, 'No puedes acceder a esta página');
        }



        $orden->comprobante_correlativo = $request->input('comprobante_correlativo');
        $orden->fecha_cancelacion = $request->input('fecha_cancelacion');
        $orden->tipocomprobante = 'FACTURA';
        $orden->tipopago = $request->input('tipopago');
        $orden->fecha_emision_comprobante = $request->input('fecha_emision_comprobante');
        $orden->usuario_cancelacion = auth()->user()->name;
        $orden->estado = 'POR RECOGER';
        $orden->cambio_dolar_precio_venta =  $request->input('cambio_dia');

        if ($request->input('tipopago') == 'CONTADO') {
            $orden->estado_pago = 'CANCELADO AL CONTADO';
        } elseif ($request->input('tipopago') == 'A CUENTA') {
            $orden->estado_pago = 'PENDIENTE A CUENTA';
        } else {
            $orden->estado_pago = 'PENDIENTE AL CRÉDITO';
        }

        $orden->save();

        return redirect()->route('ordenes.index')->with('cancelar-orden-compra', 'Orden de compra cancelada exitosamente.');
    }
    public function search(SubmitOrdenServicioRequest $request)
    {
        $search = $request->get('search');

        $ordenes = OrdenServicio::with('proveedor')
            ->when($search, function ($query, $search) {
                $query->where('codigo', 'like', "%{$search}%")
                    ->orWhereHas('proveedor', function ($q) use ($search) {
                        $q->where('razon_social', 'like', "%{$search}%")
                            ->orWhere('ruc', 'like', "%{$search}%");
                    });
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('orden-servicio.partials._rows', compact('ordenes'));
    }
}
