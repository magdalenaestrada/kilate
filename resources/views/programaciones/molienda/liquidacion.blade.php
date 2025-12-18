@extends('admin.layout')

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-body">
                <form id="liquidacionForm" method="POST" action="{{ route('molienda.guardar_liquidacion') }}">
                    @csrf
                    <div class="col-12 d-flex justify-content-end align-items-end py-2">
                        <B> MOLIENDA: {{ $proceso->codigo }}</B>
                    </div>
                    <div class="table-responsive col-12">
                        <table class="table table-bordered text-sm align-middle">
                            <tbody>
                                <tr>
                                    <td class="border border-gray-200 p-2 font-semibold">Razón social</td>
                                    <td class="border border-gray-200 p-2">
                                        {{ $proceso->lote->cliente->nombre ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-200 p-2 font-semibold">Lote</td>
                                    <td class="border border-gray-200 p-2">
                                        {{ $proceso->pesos->first()->lote->nombre ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-200 p-2 font-semibold">Circuito</td>
                                    <td class="border border-gray-200 p-2">{{ $proceso->circuito }}</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-200 p-2 font-semibold">Fecha</td>
                                    <td class="border border-gray-200 p-2">
                                        <input type="date" name="fecha" value="{{ date('Y-m-d') }}"
                                            class="border border-gray-300 rounded text-xs p-1" required>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabla de Pesos -->
                    <div class="table-responsive col-12">
                        <table class="table table-bordered text-sm align-middle">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border p-2">Nro</th>
                                    <th class="border p-2">Fecha I.</th>
                                    <th class="border p-2">Fecha S.</th>
                                    <th class="border p-2">Neto</th>
                                    <th class="border p-2">Hora I.</th>
                                    <th class="border p-2">Hora S.</th>
                                    <th class="border p-2">Placa</th>
                                    <th class="border p-2">Producto</th>
                                    <th class="border p-2">Conductor</th>
                                    <th class="border p-2">Guía</th>
                                    <th class="border p-2">Guía T.</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach ($proceso->pesos as $peso)
                                    <tr>
                                        <td class="border p-2">{{ $peso->NroSalida }}</td>
                                        <td class="border p-2">
                                            {{ \Carbon\Carbon::parse($peso->Fechai)->format('d/m/Y') }}</td>
                                        <td class="border p-2">
                                            {{ $peso->Fechas ? \Carbon\Carbon::parse($peso->Fechas)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="border p-2">{{ $peso->Neto }}</td>
                                        <td class="border p-2">
                                            {{ $peso->Horai ? \Carbon\Carbon::parse($peso->Horai)->format('H:i:s') : '-' }}
                                        </td>
                                        <td class="border p-2">
                                            {{ $peso->Horas ? \Carbon\Carbon::parse($peso->Horas)->format('H:i:s') : '-' }}
                                        </td>
                                        <td class="border p-2">{{ $peso->Placa }}</td>
                                        <td class="border p-2">{{ $peso->Producto }}</td>
                                        <td class="border p-2">{{ $peso->Conductor }}</td>
                                        <td class="border p-2">{{ $peso->guia }}</td>
                                        <td class="border p-2">{{ $peso->guiat }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabla de Otras Balanzas -->
                    @if ($proceso->pesosOtrasBal && count($proceso->pesosOtrasBal) > 0)
                        <div class="table-responsive col-12">
                            <table class="table table-bordered text-sm align-middle">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th colspan="11" class="border p-2 text-left text-sm">Otras Balanzas</th>
                                    </tr>
                                    <tr>
                                        <th class="border p-2">Nro</th>
                                        <th class="border p-2">Fecha I.</th>
                                        <th class="border p-2">Fecha S.</th>
                                        <th class="border p-2">Neto</th>
                                        <th class="border p-2">Hora I.</th>
                                        <th class="border p-2">Hora S.</th>
                                        <th class="border p-2">Placa</th>
                                        <th class="border p-2">Producto</th>
                                        <th class="border p-2">Conductor</th>
                                        <th class="border p-2">Guía</th>
                                        <th class="border p-2">Guía T.</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach ($proceso->pesosOtrasBal as $peso)
                                        <tr>
                                            <td class="border p-2">{{ $peso->id }}</td>
                                            <td class="border p-2">
                                                {{ \Carbon\Carbon::parse($peso->fechai)->format('d/m/Y') }}</td>
                                            <td class="border p-2">
                                                {{ $peso->fechas ? \Carbon\Carbon::parse($peso->fechas)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="border p-2">{{ $peso->neto }}</td>
                                            <td class="border p-2">
                                                {{ $peso->fechai ? \Carbon\Carbon::parse($peso->fechai)->format('H:i:s') : '-' }}
                                            </td>
                                            <td class="border p-2">
                                                {{ $peso->fechas ? \Carbon\Carbon::parse($peso->fechas)->format('H:i:s') : '-' }}
                                            </td>
                                            <td class="border p-2">{{ $peso->placa }}</td>
                                            <td class="border p-2">{{ $peso->producto }}</td>
                                            <td class="border p-2">{{ $peso->conductor }}</td>
                                            <td class="border p-2">{{ $peso->guia }}</td>
                                            <td class="border p-2">{{ $peso->guiat }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @php
                        use Carbon\Carbon;
                        $fechasOrdenadas = $fechas->sortBy(function ($fecha) {
                            return Carbon::parse($fecha->fecha_inicio . ' ' . $fecha->hora_inicio);
                        });
                        $totalMinutos = 0;
                        foreach ($fechasOrdenadas as $fecha) {
                            $inicio = Carbon::parse($fecha->fecha_inicio . ' ' . $fecha->hora_inicio);
                            $fin = Carbon::parse($fecha->fecha_fin . ' ' . $fecha->hora_fin);
                            if ($fin->lessThan($inicio)) {
                                $fin = $inicio->copy();
                            }
                            $totalMinutos += $inicio->diffInMinutes($fin);
                        }
                        $totalHorasFormat = intdiv($totalMinutos, 60) . 'h ' . $totalMinutos % 60 . 'm';
                        $sumaTonelaje = $fechas->sum('tonelaje');
                    @endphp
                    @php
                        $totalTon = $proceso->peso_total / 1000;
                    @endphp
                    <div class="col-12 d-flex justify-content-end align-items-end py-2">
                        TON. TOTAL: <span id="totalTon">{{ number_format($totalTon, 2) }}</span>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <B>
                                <p class="fw-bold">HORAS TRABAJADAS</p>
                            </B>
                        </div>
                        <div class="table-responsive col-12">
                            <table class="table table-bordered text-sm align-middle">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border p-2">FECHA DE INICIO MOLIENDA</th>
                                        <th class="border p-2">HORA DE INICIO MOLIENDA</th>
                                        <th class="border p-2">FECHA DE FIN MOLIENDA</th>
                                        <th class="border p-2">HORA DE FIN MOLIENDA</th>
                                        <th class="border p-2">TONELAJE</th>

                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @php
                                        $costoTotalReactivos = 0;
                                    @endphp
                                    @foreach ($fechas as $fecha)
                                        <tr>
                                            <td class="border p-2">{{ $fecha->fecha_inicio }}</td>
                                            <td class="border p-2">{{ $fecha->hora_inicio }}</td>
                                            <td class="border p-2">{{ $fecha->fecha_fin }}</td>
                                            <td class="border p-2">{{ $fecha->hora_fin }}</td>
                                            <td class="border p-2">{{ $fecha->tonelaje }}</td>
                                        </tr>
                                    @endforeach
                                    <tr style="background: #faffe0">
                                        <td colspan="4" class="border p-2 text-center fw-bold">
                                            <b>
                                                TOTAL HORAS TRABAJADAS: {{ $totalHorasFormat }}
                                            </b>
                                        </td>
                                        <td class="border p-2 text-center fw-bold">
                                            <b>
                                                Total: {{ number_format($sumaTonelaje, 2) }}
                                            </b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Total de Toneladas -->


                    <!-- Tabla de Gastos -->
                    <div class="table-responsive col-12">
                        <table class="table table-bordered text-sm align-middle">
                            <thead>
                                <tr>
                                <tr>
                                    <th colspan="4" class="border p-2 text-center bg-gray-50">GASTOS</th>
                                </tr>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="border p-2">SERVICIO</th>
                                    <th class="border p-2">PRECIO</th>
                                    <th class="border p-2">CANTIDAD</th>
                                    <th class="border p-2">US$ TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background: #eafaf1">
                                    <td class="border p-2">MOLIENDA</td>
                                    <td class="border p-2">
                                        <input type="number" step="0.01" name="precio_unitario_proceso"
                                            value="30" class="precio-input border rounded p-1 text-xs w-20"
                                            data-tipo="tratamiento">
                                    </td>
                                    <td class="border p-2">{{ number_format($totalTon, 3) }} TONELADAS</td>
                                    <td class="border p-2 costo-total" data-tipo="tratamiento">0.00</td>
                                </tr>
                                <tr>
                                    <td class="border p-2">ANALISIS DE LABORATORIO</td>
                                    <td class="border p-2">
                                        <input type="number" step="0.01" name="precio_prueba_metalurgica"
                                            value="40" class="precio-input border rounded p-1 text-xs w-20"
                                            data-tipo="prueba">
                                    </td>
                                    <td class="border p-2">
                                        <input type="number" name="cantidad_pruebas_metalurgicas" value="0"
                                            class="cantidad-input border rounded p-1 text-xs w-20" data-tipo="prueba">
                                    </td>
                                    <td class="border p-2 costo-total" data-tipo="prueba">0.00</td>
                                </tr>
                                <tr>
                                    <td class="border p-2">ALIMENTACIÓN</td>
                                    <td class="border p-2">
                                        <input type="number" step="0.01" name="precio_unitario_comida"
                                            value="3.9" class="precio-input border rounded p-1 text-xs w-20"
                                            data-tipo="comida">
                                    </td>
                                    <td class="border p-2">
                                        <input type="number" name="cantidad_comidas" value="0"
                                            class="cantidad-input border rounded p-1 text-xs w-20" data-tipo="comida">
                                    </td>
                                    <td class="border p-2 costo-total" data-tipo="comida">0.00</td>
                                </tr>
                                <tr>
                                    <td class="border p-2">BALANZA</td>
                                    <td class="border p-2">
                                        <input type="number" step="0.01" name="precio_unitario_balanza"
                                            value="2.74" class="precio-input border rounded p-1 text-xs w-20"
                                            data-tipo="balanza">
                                    </td>
                                    <td class="border p-2">
                                        <input type="number" name="cantidad_pesajes"
                                            value="{{ count($proceso->pesos) }}"
                                            class="cantidad-input border rounded p-1 text-xs w-20" data-tipo="balanza">
                                    </td>
                                    <td class="border p-2 costo-total" data-tipo="balanza">0.00</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div class="table-responsive col-4">
                            <table class="table table-bordered text-sm align-middle">
                                <tbody>
                                    <tr>
                                        <td class="border p-2">COSTO TRATAMIENTO</td>
                                        <td class="border p-2 text-right" id="resumenTratamiento">0.00</td>
                                    </tr>

                                    <tr>
                                        <td class="border p-2">OTROS COSTOS</td>
                                        <td class="border p-2 text-right" id="resumenOtros">0.00</td>
                                    </tr>
                                    <tr class="font-semibold">
                                        <td class="border border-gray-300 p-2">SUBTOTAL</td>
                                        <td class="border border-gray-300 p-2 text-right" id="resumenSubtotal">
                                            0.00</td>
                                    </tr>
                                    <tr class="font-semibold">
                                        <td class="border border-gray-300 p-2">
                                            IMPUESTO
                                            <input type="number" step="0.01" value="18" id="igvPercentage"
                                                class="border rounded p-1 text-xs ml-2">%
                                        </td>
                                        <td class="border border-gray-300 p-2 text-right" id="resumenIgv">0.00
                                        </td>
                                    </tr>
                                    <tr class="font-bold text-base">
                                        <td class="border border-gray-300 p-2">TOTAL</td>
                                        <td class="border border-gray-300 p-2 text-right" id="resumenTotal">0.00
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Hidden Inputs -->
                        <input type="hidden" name="proceso_id" value="{{ $proceso->id }}">
                        <input type="hidden" name="cantidad_procesada_tn" value="{{ $totalTon }}">
                        <input type="hidden" name="suma_proceso" id="suma_proceso">
                        <input type="hidden" name="suma_balanza" id="suma_balanza">
                        <input type="hidden" name="suma_comedor" id="suma_comedor">
                        <input type="hidden" name="suma_prueba_metalurgica" id="suma_prueba_metalurgica">
                        <input type="hidden" name="subtotal" id="subtotal">
                        <input type="hidden" name="igv" id="igv">
                        <input type="hidden" name="total" id="total">
                        <input type="hidden" name="gastos_adicionales" id="gastos_adicionales">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-primary">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endsection

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalTon = {{ $totalTon }};

            document.querySelectorAll('.precio-input, .cantidad-input').forEach(input => {
                input.addEventListener('input', calcularTotales);
            });

            document.getElementById('igvPercentage').addEventListener('input', calcularTotales);

            function calcularTotales() {
                const totalTon = {{ $totalTon }};

                let costos = {};

                const precioTratamiento = parseFloat(document.querySelector(
                    '[data-tipo="tratamiento"].precio-input').value) || 0;
                costos.tratamiento = Math.round(precioTratamiento * totalTon * 100) / 100;

                const precioComida = parseFloat(document.querySelector('[data-tipo="comida"].precio-input')
                    .value) || 0;
                const cantComidas = parseFloat(document.querySelector('[data-tipo="comida"].cantidad-input')
                    .value) || 0;
                costos.comida = Math.round(precioComida * cantComidas * 100) / 100;

                const precioBalanza = parseFloat(document.querySelector('[data-tipo="balanza"].precio-input')
                    .value) || 0;
                const cantPesajes = parseFloat(document.querySelector('[data-tipo="balanza"].cantidad-input')
                    .value) || 0;
                costos.balanza = Math.round(precioBalanza * cantPesajes * 100) / 100;

                const precioPrueba = parseFloat(document.querySelector('[data-tipo="prueba"].precio-input')
                    .value) || 0;
                const cantPruebas = parseFloat(document.querySelector('[data-tipo="prueba"].cantidad-input')
                    .value) || 0;
                costos.prueba = Math.round(precioPrueba * cantPruebas * 100) / 100;


                document.querySelector('[data-tipo="tratamiento"].costo-total').textContent = costos.tratamiento
                    .toFixed(2);
                document.querySelector('[data-tipo="comida"].costo-total').textContent = costos.comida.toFixed(2);
                document.querySelector('[data-tipo="balanza"].costo-total').textContent = costos.balanza.toFixed(2);
                document.querySelector('[data-tipo="prueba"].costo-total').textContent = costos.prueba.toFixed(2);


                const otrosCostos = costos.comida + costos.balanza + costos.prueba;
                const subtotal = costos.tratamiento + otrosCostos;
                const igvPercentage = parseFloat(document.getElementById('igvPercentage').value) || 0;
                const igv = Math.round(subtotal * igvPercentage) / 100;
                const total = Math.round(subtotal * (100 + igvPercentage)) / 100;

                document.getElementById('resumenTratamiento').textContent = costos.tratamiento.toFixed(2);
                document.getElementById('resumenOtros').textContent = otrosCostos.toFixed(2);
                document.getElementById('resumenSubtotal').textContent = subtotal.toFixed(2);
                document.getElementById('resumenIgv').textContent = igv.toFixed(2);
                document.getElementById('resumenTotal').textContent = total.toFixed(2);

                document.getElementById('suma_proceso').value = costos.tratamiento;
                document.getElementById('suma_balanza').value = costos.balanza;
                document.getElementById('suma_comedor').value = costos.comida;
                document.getElementById('suma_prueba_metalurgica').value = costos.prueba;
                document.getElementById('gastos_adicionales').value = otrosCostos;
                document.getElementById('subtotal').value = subtotal;
                document.getElementById('igv').value = igv;
                document.getElementById('total').value = total;
            }
    

            calcularTotales();
        });
    </script>
