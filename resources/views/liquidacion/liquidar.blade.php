@extends('admin.layout')

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-body">
                <form id="liquidacionForm" method="POST" action="{{ route('liquidaciones.store') }}">
                    @csrf
                    <div class="col-12 d-flex justify-content-end align-items-end py-2">
                        <B> PROCESO: {{ $proceso->codigo }}</B>
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
                                    <td class="border border-gray-200 p-2">{{ $proceso->circuito->descripcion }}</td>
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

                    <!-- Total de Toneladas -->
                    @php
                        $totalTon = $proceso->peso_total / 1000;
                    @endphp
                    <div class="col-12 d-flex justify-content-end align-items-end py-2">
                        TON. TOTAL: <span id="totalTon">{{ number_format($totalTon, 2) }}</span>
                    </div>

                    <div class="table-responsive col-12">
                        <table class="table table-bordered text-sm align-middle">
                            <thead>
                                <tr>
                                <tr>
                                    <th colspan="3" class="border p-2 text-center bg-gray-50">GASTOS</th>
                                    <th colspan="1" class="border  p-2 bg-gray-50 text-right">
                                        DOLAR:
                                        <input type="number" step="0.01" value="{{ $ultimoDolar ?? 3.3 }}"
                                            id="dolarInput" name="dolar">
                                    </th>
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
                                    <td class="border p-2">TRATAMIENTO</td>
                                    <td class="border p-2">
                                        <input type="number" step="0.01" name="precio_unitario_proceso" value="30"
                                            class="precio-input border rounded p-1 text-xs w-20" data-tipo="tratamiento">
                                    </td>
                                    <td class="border p-2">{{ number_format($totalTon, 3) }} TONELADAS</td>
                                    <td class="border p-2 costo-total" data-tipo="tratamiento">0.00</td>
                                </tr>
                                <tr>
                                    <td class="border p-2">ANÁLISIS</td>
                                    <td class="border p-2">
                                        <input type="number" step="0.01" value="0"
                                            class="precio-input-sol border rounded p-1 text-xs w-16 mr-1">
                                        <span class="precio-muestra">0.00</span>
                                        <input type="hidden" value="0" name="precio_unitario_laboratorio"
                                            class="precio-muestra-hidden">
                                    </td>
                                    <td class="border p-2">
                                        <input type="number" name="cantidad_muestras" value="0"
                                            class="cantidad-input border rounded p-1 text-xs w-20" data-tipo="muestra">
                                    </td>
                                    <td class="border p-2 costo-total" data-tipo="muestra">0.00</td>
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
                                <tr>
                                    <td class="border p-2">PRUEBAS METALÚRGICAS</td>
                                    <td class="border p-2">
                                        <input type="number" step="0.01" name="precio_prueba_metalurgica"
                                            value="0" class="precio-input border rounded p-1 text-xs w-20"
                                            data-tipo="prueba">
                                    </td>
                                    <td class="border p-2">
                                        <input type="number" name="cantidad_pruebas_metalurgicas" value="0"
                                            class="cantidad-input border rounded p-1 text-xs w-20" data-tipo="prueba">
                                    </td>
                                    <td class="border p-2 costo-total" data-tipo="prueba">0.00</td>
                                </tr>
                                <tr>
                                    <td class="border p-2">DESCOCHE</td>
                                    <td class="border p-2">
                                        <input type="number" step="0.01" name="precio_descoche" value="0"
                                            class="precio-input border rounded p-1 text-xs w-20" data-tipo="descoche">
                                    </td>
                                    <td class="border p-2">
                                        <input type="number" name="cantidad_descoche" value="0"
                                            class="cantidad-input border rounded p-1 text-xs w-20" data-tipo="descoche">
                                    </td>
                                    <td class="border p-2 costo-total" data-tipo="descoche">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="table-responsive col-8">
                            <table class="table table-bordered text-sm align-middle">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border p-2">NOMBRE</th>
                                        <th class="border p-2">PRECIO</th>
                                        <th class="border p-2">LÍMITE</th>
                                        <th class="border p-2">CONSUMO</th>
                                        <th class="border p-2">KG EN EXCESO</th>
                                        <th class="border p-2">TOTALES $</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @php
                                        $costoTotalReactivos = 0;
                                    @endphp
                                    @foreach ($reactivos as $reactivo)
                                        @php
                                            $totalConsumos = $proceso->consumosreactivos
                                                ->where('reactivo_id', $reactivo->id)
                                                ->sum('cantidad');

                                            $totalDevoluciones = $proceso->devolucionesReactivos
                                                ->where('reactivo_id', $reactivo->id)
                                                ->sum('cantidad');

                                            $netConsumo = $totalConsumos - $totalDevoluciones;
                                            $limiteTotalProceso = round(
                                                ($reactivo->detalles->limite ?? 0) * $totalTon,
                                                6,
                                            );
                                            $kgEnExceso = max(0, round($netConsumo - $limiteTotalProceso, 6));
                                            $costoConsumo = round($kgEnExceso * ($reactivo->detalles->precio ?? 0), 2);
                                            $costoTotalReactivos += $costoConsumo;
                                        @endphp
                                        <tr>
                                            <td class="border p-2">{{ $reactivo->producto->nombre_producto }}</td>
                                            <td class="border p-2">
                                                {{ number_format($reactivo->detalles->precio ?? 0, 2) }}</td>
                                            <td class="border p-2">{{ number_format($limiteTotalProceso, 6) }}
                                            </td>
                                            <td class="border p-2">{{ number_format($netConsumo, 2) }}</td>
                                            <td class="border p-2">{{ number_format($kgEnExceso, 2) }}</td>
                                            <td class="border p-2">{{ number_format($costoConsumo, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr style="background: #faffe0">
                                        <td colspan="5" class="border p-2 text-right font-semibold">TOTAL
                                            REACTIVOS:</td>
                                        <td class="border p-2 font-semibold" id="costoTotalReactivos">
                                            {{ number_format($costoTotalReactivos, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Reactivos - Límites -->
                        <div class="table-responsive col-4">
                            <table class="table table-bordered text-sm align-middle">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="border p-2">NOMBRE</th>
                                        <th class="border p-2">LÍMITE X TON</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach ($reactivos as $reactivo)
                                        <tr>
                                            <td class="border p-2">{{ $reactivo->producto->nombre_producto }}</td>
                                            <td class="border p-2">{{ $reactivo->detalles->limite ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
                                        <td class="border p-2">COSTO EXCESO REACTIVOS</td>
                                        <td class="border p-2 text-right" id="resumenReactivos">
                                            {{ number_format($costoTotalReactivos, 2) }}
                                        </td>
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
                        <input type="hidden" name="suma_exceso_reactivos" value="{{ $costoTotalReactivos }}">
                        <input type="hidden" name="suma_balanza" id="suma_balanza">
                        <input type="hidden" name="suma_comedor" id="suma_comedor">
                        <input type="hidden" name="suma_laboratorio" id="suma_laboratorio">
                        <input type="hidden" name="suma_prueba_metalurgica" id="suma_prueba_metalurgica">
                        <input type="hidden" name="suma_descoche" id="suma_descoche">
                        <input type="hidden" name="subtotal" id="subtotal">
                        <input type="hidden" name="igv" id="igv">
                        <input type="hidden" name="total" id="total">
                        <input type="hidden" name="gastos_adicionales" id="gastos_adicionales">
                    </div> <!-- Botón Guardar -->
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
            const costoReactivos = {{ $costoTotalReactivos }};

            document.querySelector('.precio-input-sol').addEventListener('input', function() {
                const solPrecio = parseFloat(this.value) || 0;
                const tipoCambio = parseFloat(document.getElementById('dolarInput').value) || 1;
                const precioMuestra = (solPrecio * 1.2 / tipoCambio).toFixed(2);
                document.querySelector('.precio-muestra').textContent = precioMuestra;
                document.querySelector('.precio-muestra-hidden').value = precioMuestra;
                calcularTotales();
            });

            document.querySelectorAll('.precio-input, .cantidad-input').forEach(input => {
                input.addEventListener('input', calcularTotales);
            });

            document.getElementById('igvPercentage').addEventListener('input', calcularTotales);

            function calcularTotales() {
                const totalTon = {{ $totalTon }};
                const costoReactivos = {{ $costoTotalReactivos }};
                const tipoCambio = parseFloat(document.getElementById('dolarInput').value) || 1;

                let costos = {};

                const precioTratamiento = parseFloat(document.querySelector(
                    '[data-tipo="tratamiento"].precio-input').value) || 0;
                costos.tratamiento = Math.round(precioTratamiento * totalTon * 100) / 100;

                const solPrecio = parseFloat(document.querySelector('.precio-input-sol').value) || 0;
                const precioMuestra = solPrecio * 1.2 / tipoCambio;
                document.querySelector('.precio-muestra').textContent = precioMuestra.toFixed(2);
                document.querySelector('.precio-muestra-hidden').value = precioMuestra.toFixed(2);

                const cantMuestras = parseFloat(document.querySelector('[data-tipo="muestra"].cantidad-input')
                    .value) || 0;
                costos.muestra = Math.round(precioMuestra * cantMuestras * 100) / 100;

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

                const precioDescoche = parseFloat(document.querySelector('[data-tipo="descoche"].precio-input')
                    .value) || 0;
                const cantDescoche = parseFloat(document.querySelector('[data-tipo="descoche"].cantidad-input')
                    .value) || 0;
                costos.descoche = Math.round(precioDescoche * cantDescoche * 100) / 100;

                document.querySelector('[data-tipo="tratamiento"].costo-total').textContent = costos.tratamiento
                    .toFixed(2);
                document.querySelector('[data-tipo="muestra"].costo-total').textContent = costos.muestra.toFixed(2);
                document.querySelector('[data-tipo="comida"].costo-total').textContent = costos.comida.toFixed(2);
                document.querySelector('[data-tipo="balanza"].costo-total').textContent = costos.balanza.toFixed(2);
                document.querySelector('[data-tipo="prueba"].costo-total').textContent = costos.prueba.toFixed(2);
                document.querySelector('[data-tipo="descoche"].costo-total').textContent = costos.descoche.toFixed(
                    2);

                const otrosCostos = costos.muestra + costos.comida + costos.balanza + costos.prueba + costos
                    .descoche;
                const subtotal = costos.tratamiento + otrosCostos + costoReactivos;
                const igvPercentage = parseFloat(document.getElementById('igvPercentage').value) || 0;
                const igv = Math.round(subtotal * igvPercentage) / 100;
                const total = Math.round(subtotal * (100 + igvPercentage)) / 100;

                document.getElementById('resumenTratamiento').textContent = costos.tratamiento.toFixed(2);
                document.getElementById('resumenReactivos').textContent = costoReactivos.toFixed(2);
                document.getElementById('resumenOtros').textContent = otrosCostos.toFixed(2);
                document.getElementById('resumenSubtotal').textContent = subtotal.toFixed(2);
                document.getElementById('resumenIgv').textContent = igv.toFixed(2);
                document.getElementById('resumenTotal').textContent = total.toFixed(2);

                document.getElementById('suma_proceso').value = costos.tratamiento;
                document.getElementById('suma_balanza').value = costos.balanza;
                document.getElementById('suma_comedor').value = costos.comida;
                document.getElementById('suma_laboratorio').value = costos.muestra;
                document.getElementById('suma_prueba_metalurgica').value = costos.prueba;
                document.getElementById('suma_descoche').value = costos.descoche;
                document.getElementById('gastos_adicionales').value = otrosCostos;
                document.getElementById('subtotal').value = subtotal;
                document.getElementById('igv').value = igv;
                document.getElementById('total').value = total;
            }
            document.getElementById('dolarInput').addEventListener('input', function() {
                calcularTotales();
            });

            calcularTotales();
        });
    </script>
