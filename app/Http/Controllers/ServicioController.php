<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrdenServicio;
use App\Models\Inventariopagoacuenta;
use App\Models\OrdenServicio;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicioController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:ver ordenes', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear ordenes', ['only' => ['create', 'store']]);
        $this->middleware('permission:cancelar ordenes', ['only' => ['cancelar', 'updatecancelar', 'cancelaralcredito', 'updatecancelaralcredito']]);
        $this->middleware('permission:recepcionar ordenes', ['only' => ['recepcionar', 'updaterecepcionar']]);
        //$this->middleware('permission:anular ordenes', ['only' => ['anular']]);
    }
    public function index()
    {
        $orden_servicio = OrdenServicio::orderBy('created_at', 'desc')->paginate(20);
        return view('orden_servicio.index', compact('orden_servicio'));
    }
    public function create()
    {
        return view('orden_servicio.create', compact('productos'));
    }

    public function store(Request $request)
    {
        try {
            $hoy = Carbon::now('America/Lima');
            $fecha = $hoy->format('Y-m-d');

            $request->validate([
                "proveedor_id" => "required|exists:proveedors,id",
                "descripcion" => "required",
                "costo_estimado" => "required|numeric",
                "observaciones" => "nullable",
                'proveedor' => 'required',
                'documento_proveedor' => 'required',
                'tipomoneda' => 'required',
                'ruc' => "required|max:11"
            ]);

            $proveedor = Proveedor::updateOrCreate(
                ['ruc' => $request->documento_proveedor],
                [
                    'razon_social' => $request->proveedor,
                    'telefono' => $request->telefono_proveedor,
                    'direccion' => $request->direccion_proveedor,
                ]
            );

            $orden_servicio = OrdenServicio::create([
                "proveedor_id" => $proveedor->id,
                "fecha_creacion" => $hoy,
                "fecha_inicio" => $request->fecha_inicio,
                "fecha_fin" => $request->fecha_fin,
                "descripcion" => $request->descripcion,
                "costo_estimado" => $request->costo_estimado,
                "costo_final" => $request->costo_final,
                "observaciones" => $request->observaciones,
            ]);

            $orden_servicio->codigo = $this->GenerarCodigo($orden_servicio->id);
            $orden_servicio->save();

            $detalles = $request->input('detalles');
            $index = 0;
            // Create order items
            foreach ($detalles as $detalle) {
                DetalleOrdenServicio::create([
                    "orden_servicio_id" => $orden_servicio->id,
                    "descripcion" => $request->descripcion,
                    "cantidad" => $request->cantidad,
                    "precio_unitario" => $request->precio_unitario,
                    "subtotal" => $request->subtotal,

                ]);
            }

            return redirect()->route('orden_servicio.index')->with('crear-orden', 'Orden de servicio creada con éxito.');
        } catch (ValidationException $e) {
            // If validation fails during the transaction, handle the error
            return back()->withInput()->withErrors($e->validator->errors())->with('error', 'Error al crear la orden.');
        } catch (\Exception $e) {


            // Handle other unexpected errors
            return back()->withInput()->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OrdenServicio $orden_servicio)
    {

        $orden_servicio = OrdenServicio::with('detalle_orden')->find($orden_servicio->id);
        return view('orden_servicio.show', compact('orden_servicio'));
    }

    public function edit(string $id)
    {
        $orden_servicio = OrdenServicio::with(['proveedor', 'detalle_orden'])->findOrFail($id);
        return view('orden_servicio.edit', compact('orden_servicio'));
    }

    public function actualizar_detalle(Request $request, $detalle_servicio_id)
    {
        $detalles = DetalleOrdenServicio::findOrFail($detalle_servicio_id);
        $detalles->update([
            "descripcion" => $request->descripcion,
            "cantidad" => $request->cantidad,
            "precio_unitario" => $request->precio_unitario,
            "subtotal" => $request->subtotal,
        ]);
        return redirect()->back()->with('success', 'Detalle actualizado con éxito.');
    }

    public function actualizar_orden_servicio(Request $request, string $id)
    {
        try {
            $hoy = Carbon::now('America/Lima');
            $fecha = $hoy->format('Y-m-d');
            $orden_servicio = OrdenServicio::findOrFail($id);

            if ($orden_servicio->estado === 'ANULADO') {
                throw new \Symfony\Component\HttpKernel\Exception\HttpException(
                    403,
                    'No se puede editar una orden de servicio ANULADO.'
                );
            }
            $request->validate([
                "proveedor_id" => "required|exists:proveedors,id",
                "descripcion" => "required",
                "costo_estimado" => "required|numeric",
                "observaciones" => "nullable",
                'proveedor' => 'required',
                'documento_proveedor' => 'required',
                'tipomoneda' => 'required',
                'ruc' => "required|max:11"
            ]);

            $proveedor = Proveedor::updateOrCreate(
                ['ruc' => $request->documento_proveedor],
                [
                    'razon_social' => $request->proveedor,
                    'telefono' => $request->telefono_proveedor,
                    'direccion' => $request->direccion_proveedor,
                ]
            );

            $orden_servicio = OrdenServicio::update([
                "proveedor_id" => $proveedor->id,
                "fecha_inicio" => $request->fecha_inicio,
                "fecha_fin" => $request->fecha_fin,
                "descripcion" => $request->descripcion,
                "costo_estimado" => $request->costo_estimado,
                "costo_final" => $request->costo_final,
                "observaciones" => $request->observaciones,
            ]);

        } catch (ValidationException $e) {
            // If validation fails during the transaction, handle the error
            return back()->withInput()->withErrors($e->validator->errors())->with('error', 'Error al crear la orden.');
        } catch (\Exception $e) {

            // Handle other unexpected errors
            return back()->withInput()->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }

        return redirect()
            ->route('orden_servicio.index')
            ->with('actualizar-cotizacion', 'Cotización actualizada con éxito.');
    }
    public function cancelar(string $id)
    {
        $orden_servicio = OrdenServicio::findOrFail($id);
        return view('orden_servicio.cancelar', compact('orden_servicio'));
    }
    public function update_cancelar(Request $request, string $id)
    {
        $request->validate([
            'comprobante_correlativo' => 'required',
            'fecha_cancelacion' => 'required',
            'fecha_emision_comprobante' => 'required',
            'tipopago' => 'required',

        ]);
        $orden_servicio = OrdenServicio::findOrFail($id);


        if ($orden_servicio->estado !== 'PENDIENTE') {
            throw new HttpException(403, 'No puedes acceder a esta página');
        }

        $orden_servicio->comprobante_correlativo = $request->input('comprobante_correlativo');
        $orden_servicio->fecha_cancelacion = $request->input('fecha_cancelacion');
        $orden_servicio->tipocomprobante = 'FACTURA';
        $orden_servicio->tipopago = $request->input('tipopago');
        $orden_servicio->fecha_emision_comprobante = $request->input('fecha_emision_comprobante');
        $orden_servicio->usuario_cancelacion = auth()->user()->name;
        $orden_servicio->estado = 'F';
        $orden_servicio->cambio_dolar_precio_venta =  $request->input('cambio_dia');

        if ($request->input('tipopago') == 'CONTADO') {
            $orden_servicio->estado_pago = 'CANCELADO AL CONTADO';
        } elseif ($request->input('tipopago') == 'A CUENTA') {
            $orden_servicio->estado_pago = 'PENDIENTE A CUENTA';
        } else {
            $orden_servicio->estado_pago = 'PENDIENTE AL CRÉDITO';
        }

        $orden_servicio->save();

        return redirect()->route('orden_servicio.index')->with('cancelar-orden-servicio', 'Orden de servicio cancelada exitosamente.');
    }

    public function cancelaralcredito(string $id)
    {
        $orden_servicio = OrdenServicio::findOrFail($id);

        return view('orden_servicio.cancelaralcredito', compact('orden_servicio'));
    }

    public function updatecancelaralcredito(Request $request, string $id)
    {
        $orden_servicio = OrdenServicio::findOrFail($id);

        if ($orden_servicio->estado_pago == 'PENDIENTE AL CRÉDITO') {
            $request->validate([
                'fecha_pago_al_credito' => 'required',
            ]);

            $orden_servicio->fecha_pago_al_credito = $request->input('fecha_pago_al_credito');
            $orden_servicio->usuario_pago_al_credito = auth()->user()->name;

            $orden_servicio->estado_pago = 'CANCELADO AL CRÉDITO';

            $orden_servicio->save();

            return redirect()->route('orden_servicio.index')->with('cancelar-al-credito', 'Orden cancelada al crédito con éxito.');
        }
    }
    public function cancelaracuenta(string $id)
    {
        $orden_servicio = OrdenServicio::findOrFail($id);
        if ($orden_servicio->estado_pago !== 'PENDIENTE A CUENTA') {
            throw new HttpException(403, 'NO ESTÁ PENDIENTE A CUENTA.');
        }

        $today = now()->toDateString();
        return view('orden_servicio.cancelaracuenta', compact('orden_servicio', 'today'));
    }
    public function updatecancelaracuenta(Request $request, string $id)
    {
        try {
            $request->validate([
                'fechas_pagos.*' => 'required',
                'montos.*' => 'required',
                'comprobantes.*' => 'required',

            ]);

            $orden_servicio = OrdenServicio::findOrFail($id);

            if ($orden_servicio->estado_pago !== 'PENDIENTE A CUENTA') {
                throw new HttpException(403, 'NO ESTÁ PENDIENTE A CUENTA.');
            }

            $countFechasPagos = count($request->fechas_pagos);
            $fechas_pagos = $request->input('fechas_pagos');
            $index = 0;
            // Create order items
            for ($i = 0; $i < $countFechasPagos; $i++) {
                Inventariopagoacuenta::create([
                    'orden_servicio_id' => $orden_servicio->id,
                    'fecha_pago' => $request->fechas_pagos[$index],
                    'monto' => $request->montos[$index],
                    'comprobante_correlativo' => $request->comprobantes[$index],
                    'usuario' => auth()->user()->name,
                ]);
                $index = $index + 1;
            }

            $cerrar_pago = False;
            $monto_total_pagado = 0;
            foreach ($orden_servicio->pagosacuenta as $pago) {
                $monto_total_pagado += $pago->monto;
            }

            if ($monto_total_pagado >= $orden_servicio->total) {
                $orden_servicio->estado_pago = 'CANCELADO A CUENTA';
            }

            $orden_servicio->save();

            return redirect()->route('orden_servicio.index')->with('cancelar-al-credito', 'Orden cancelada al crédito con éxito.');
        } catch (ValidationException $e) {
            // If validation fails during the transaction, handle the error
            return back()->withInput()->withErrors($e->validator->errors())->with('error', 'Error al cancelar la orden al crédito.');
        } catch (\Exception $e) {
            // Handle other unexpected errors
            return back()->withInput()->with('error', 'Error al procesar la solicitud.');
        }
    }



    public function updaterecepcionar(Request $request, string $id)
    {
        $request->validate([
            'guiaingresoalmacen' => 'nullable|string|max:120',
        ]);

        $orden_servicio = OrdenServicio::with('productos')->findOrFail($id);

        if ($orden_servicio->estado !== 'POR RECOGER') {
            throw new HttpException(403, 'La orden no está en estado POR RECOGER.');
        }

        $selected   = collect($request->input('selected_products', []))->map(fn($v) => (int)$v)->all();
        $qtyArrived = $request->input('qty_arrived', []);

        // Normaliza la guía: null o '', según prefieras
        $guia = $request->filled('guiaingresoalmacen')
            ? Str::limit(trim((string)$request->guiaingresoalmacen), 120)
            : ''; // <- usa '' para evitar errores si alguna columna es NOT NULL

        DB::transaction(function () use ($orden_servicio, $selected, $qtyArrived, $guia) {
            $closing = true;

            foreach ($orden_servicio->productos as $producto) {
                $pivot   = $producto->pivot;
                $pivotId = (int) $pivot->id;

                if (!in_array($pivotId, $selected, true)) {
                    if ($pivot->estado !== 'INGRESADO') $closing = false;
                    continue;
                }

                $cantidad_recepcionada = (int) ($qtyArrived[$pivotId] ?? 0);
                if ($cantidad_recepcionada <= 0) {
                    throw new HttpException(422, 'Cantidad inválida para uno de los productos.');
                }

                $pedido    = (int) $pivot->cantidad;
                $ingresado = (int) $pivot->cantidad_ingresada;
                $pendiente = $pedido - $ingresado;
                if ($cantidad_recepcionada > $pendiente) {
                    throw new HttpException(403, 'NO PUEDES INGRESAR MÁS PRODUCTOS DE LOS QUE HAY POR RECIBIR.');
                }

                $pivot->cantidad_ingresada = $ingresado + $cantidad_recepcionada;
                $pivot->guiaingresoalmacen = $guia; // '' si vacío
                $producto->stock = (int) $producto->stock + $cantidad_recepcionada;

                $log = new Logdetallesinvingreso();
                $log->detalleorden_servicio_id = $pivotId;
                $log->usuario = auth()->user()->name;
                $log->cantidad_ingresada = $cantidad_recepcionada;
                $log->guiaingresoalmacen = $guia;     // '' si vacío
                $log->save();

                if ($orden_servicio->tipomoneda === 'SOLES') {
                    $producto->ultimoprecio = $pivot->precio;
                } else {
                    $producto->ultimoprecio = $pivot->precio * (float) $orden_servicio->cambio_dolar_precio_venta;
                }

                $pivot->estado = $pivot->cantidad_ingresada >= $pedido ? 'INGRESADO' : $pivot->estado;
                if ($pivot->estado !== 'INGRESADO') $closing = false;

                $pivot->save();
                $producto->save();

                // (opcional) recalcular promedio ponderado...
            }

            if ($closing) $orden_servicio->estado = 'INGRESADO AL ALMACEN';
            $orden_servicio->usuario_recepcionista = auth()->user()->name;
            $orden_servicio->save();
        });

        return redirect()->route('orden_servicio.index')
            ->with('actualizar-recepcion', 'Recepción exitosa de productos.');
    }

    public function prnpriview(string $id)
    {

        $orden_servicio = OrdenServicio::with('productos')->findOrFail($id);


        return view('orden_servicio.printticket', compact('orden_servicio'));
    }


    public function anular(string $id)
    {
        DB::beginTransaction();
        try {
            $orden_servicio = OrdenServicio::findOrFail($id);

            foreach ($orden_servicio->productos as $producto) {
                if ($producto->stock < $producto->pivot->cantidad_ingresada) {
                    throw new HttpException(403, 'NO HAY SUFICIENTES PRODUCTOS EN EL ALMACEN PARA CANCELAR LA ORDEN.');
                }


                $producto->stock -= $producto->pivot->cantidad_ingresada;
                $producto->save();
            }

            $orden_servicio->estado = 'ANULADO';
            $orden_servicio->save();
            DB::commit();
            return redirect()->route('orden_servicio.index')->with('anular-orden-compra', 'Orden de compra anulada con éxito.');
        } catch (QueryException $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error desconocido');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    //excel for detalles of this
    public function export_excel()
    {
        return Excel::download(new DetalleInventarioIngresoExport, 'detalleordenesdecompra.xlsx');
    }

    public function getProductByBarcode($barcode)
    {
        $product = Producto::where('barcode', $barcode)->first();

        if ($product) {
            return response()->json(['success' => true, 'product' => $product]);
        } else {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }
    }

    public function getProductImageByProduct($product)
    {
        $product = Producto::with('unidad')->find($product);


        if ($product) {
            return response()->json(['success' => true, 'product' => $product]);
        } else {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }
    }

    public function getSellingPrice(Request $request)
    {
        try {
            $token = env('APIS_TOKEN');
            $fecha = $request->input('fecha'); // Get the selected date from the request

            // Make API call to get selling price
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/tipo-cambio?date=' . $fecha,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 2,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => ['Referer: https://apis.net.pe/tipo-de-cambio-sunat-api', 'Authorization: Bearer ' . $token],
            ]);

            $response = curl_exec($curl);

            if ($response === false) {
                throw new Exception('Error fetching data from the API');
            }

            curl_close($curl);

            // Decode API response and extract selling price
            $tipoCambioSunat = json_decode($response);
            $precio_venta = $tipoCambioSunat->precioVenta;

            // Return selling price as JSON response
            return response()->json(['precio_venta' => $precio_venta]);
        } catch (Exception $e) {
            // Handle exception
            // Log error or show a friendly message to the user
            // You might want to return an error response
            return response()->json(['error' => 'Error fetching data from the API'], 500);
        }
    }





    //search ingreso
    // public function searchIngreso(Request $request)
    //{
    //    $searchString = $request->search_string;

    //    $orden_servicio = OrdenServicio::where('comprobante_correlativo', 'like', '%' . $searchString . '%')
    //        ->orWhereHas('productos', function ($query) use ($searchString) {
    //            $query->where('nombre_producto', 'like', '%' . $searchString . '%');
    //        })
    //        ->orderBy('id', 'desc')
    //         ->paginate(100);

    //    return view('orden_servicio.search-results', compact('orden_servicio'));
    //  }
    public function searchIngreso(Request $request)
    {
        $searchString = $request->search_string;

        $orden_servicio = OrdenServicio::where('comprobante_correlativo', 'like', "%{$searchString}%")
            ->orWhere('cotizacion', 'like', "%{$searchString}%") // <--- NUEVO
            ->orWhereHas('productos', function ($query) use ($searchString) {
                $query->where('nombre_producto', 'like', "%{$searchString}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(100);

        return view('orden_servicio.search-results', compact('orden_servicio'));
    }

    public function GenerarCodigo($numero)
    {
        $año = date('Y');
        $codigo = str_pad($numero, 5, '0', STR_PAD_LEFT);
        return $año . "-OS-" - $numero;
    }
}
