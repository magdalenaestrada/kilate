<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquidación {{ $liquidacion->proceso->codigo }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        body {
            zoom: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            text-align: center;
            /* Agregar esta línea */

        }

        th,
        td {
            border: 1px solid #d1d5db;
            /* gris tailwind */
            padding: 4px 6px;
        }

        th {
            background: #D5F2DC;
            /* bg-gray-50 */
            font-weight: bold;
        }

        .title-section {
            font-weight: bold;
            text-align: center;
            background: #e5f3d4;
            /* equivalente a bg-green-200 */
        }

        .subtitle-section {
            background: #f0f9e8;
            /* equivalente a bg-green-100 */
        }

        .flex {
            display: flex;
        }

        .w-1-2 {
            width: 50%;
        }

        .w-2-3 {
            width: 66%;
        }

        .w-1-3 {
            width: 34%;
        }

        img.logo {
            width: 130px;
            height: 95px;
            object-fit: contain;
        }

        .font-semibold {
            font-weight: bold;
        }

        @media print {

            table,
            th,
            td {
                font-size: 10px !important;
                padding: 3px;
            }
        }
    </style>

</head>

<body>

    {{-- Encabezado --}}
    <table>
        <tbody>
            <tr>
                <td>Proceso: {{ $liquidacion->proceso->codigo }}</td>
                <td>Liquidación: KC-{{ $liquidacion->id }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Bloque principal --}}
    <table>
        <tbody>

            {{-- Fila logo + título --}}
            <tr>
                <td rowspan="2" colspan="2" style="text-align: center;">
                    <img src="{{ asset('images/kilate.png') }}" class="logo">
                </td>

                <td colspan="4" class="text-center font-semibold">
                    LIQUIDACIÓN DE PLANTA DE BENEFICIO <br>
                    KILATE CORPORATION.
                </td>

                <td class="font-semibold">RUC</td>
                <td colspan="2">20605932054</td>
            </tr>

            <tr>
                <td colspan="4">Carretera Poroma s/n, Fundo Don Alfredo, Panamericana Sur km 468, Vista Alegre</td>
                <td class="font-semibold">FECHA LIQUIDACIÓN</td>
                <td colspan="2">{{ $liquidacion->fecha->format('d/m/Y') }}</td>
            </tr>

            {{-- Razón social --}}
            <tr>
                <td rowspan="2" class="font-semibold">RAZÓN SOCIAL</td>
                <td rowspan="2" colspan="3">{{ $liquidacion->cliente->nombre }}</td>

                <td class="font-semibold">RUC</td>
                <td colspan="4">{{ $liquidacion->cliente->documento }}</td>
            </tr>

            {{-- Tonelaje --}}
            <tr>
                <td class="font-semibold">TONELAJE HUMEDO</td>
                <td colspan="3">{{ number_format($liquidacion->proceso->peso_total / 1000, 2) }}</td>
                <td class="text-center">TN</td>
            </tr>

            {{-- Lote --}}
            <tr>
                <td class="font-semibold">LOTE</td>
                <td colspan="3" class="text-center">{{ $liquidacion->proceso->lote->codigo }}</td>

                <td class="font-semibold">INICIO DEL PROCESO</td>
                <td colspan="4">
                    {{ $liquidacion->proceso->programacion->fecha_inicio }}
                </td>
            </tr>

            {{-- Fin proceso --}}
            <tr>
                <td class="font-semibold">CIRCUITO</td>
                <td colspan="3" class="text-center">{{ $liquidacion->proceso->circuito->descripcion }}</td>

                <td class="font-semibold">FIN DEL PROCESO</td>
                <td colspan="4">
                    {{ $liquidacion->proceso->programacion->fecha_fin }}
                </td>
            </tr>

        </tbody>
    </table>

    <br>
    <table>
        <thead>
            <tr class="subtitle-section">
                <th>FECHA INGRESO</th>
                <th>N/PLACA</th>
                <th>CONDUCTOR</th>
                <th>GRR</th>
                <th>GRT</th>
                <th>BALANZA</th>
                <th>T/BALANZA</th>
                <th>TMHB</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($liquidacion->proceso->pesos as $peso)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($peso->Fechai)) }}</td>
                    <td>{{ $peso->Placa }}</td>
                    <td>{{ $peso->Conductor }}</td>
                    <td>{{ $peso->guia }}</td>
                    <td>{{ $peso->guiat }}</td>
                    <td>ALFA</td>
                    <td>{{ $peso->NroSalida }}</td>
                    <td>{{ number_format($peso->Neto / 1000, 2) }}</td>
                </tr>
            @endforeach
            @if ($liquidacion->proceso->pesosOtrasBal->count() > 0)
                @foreach ($liquidacion->proceso->pesosOtrasBal as $peso)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($peso->Fechai)->format('d/m/Y') }}</td>
                        <td>{{ $peso->placa }}</td>
                        <td>{{ $peso->conductor }}</td>
                        <td>{{ $peso->guia }}</td>
                        <td>{{ $peso->guiat }}</td>
                        <td>{{ $peso->balanza }}</td>
                        <td>{{ $peso->id }}</td>
                        <td>{{ number_format($peso->neto / 1000, 2) }}</td>
                    </tr>
                @endforeach
            @endif

            <tr>
                <td colspan="6"></td>
                <td class="font-semibold">TON TOTAL</td>
                <td class="font-semibold">
                    {{ number_format($liquidacion->proceso->peso_total / 1000, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <br>
    <table>
        <thead>
            <tr class="subtitle-section">
                <th>SERVICIO</th>
                <th>TMHB</th>
                <th>$ /TON</th>
                <th>US$ TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>TRATAMIENTO</td>
                <td>{{ $liquidacion->cantidad_procesada_tn }}</td>
                <td>{{ $liquidacion->precio_unitario_proceso }}</td>
                <td>{{ $liquidacion->suma_proceso }}</td>
            </tr>
        </tbody>
    </table>

    <br>
    <table>
        <thead>
            <tr class="title-section">
                <th colspan="4">GASTOS ADICIONALES</th>
            </tr>
            <tr class="subtitle-section">
                <th>SERVICIO</th>
                <th>PRECIO</th>
                <th>CANTIDAD</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ANÁLISIS DE LABORATORIO</td>
                <td>{{ $liquidacion->precio_unitario_laboratorio }}</td>
                <td>{{ $liquidacion->cantidad_muestras }}</td>
                <td>{{ $liquidacion->suma_laboratorio }}</td>
            </tr>
            <tr>
                <td>ALIMENTACIÓN</td>
                <td>{{ $liquidacion->precio_unitario_comida }}</td>
                <td>{{ $liquidacion->cantidad_comidas }}</td>
                <td>{{ $liquidacion->suma_comedor }}</td>
            </tr>
            <tr>
                <td>BALANZA</td>
                <td>{{ $liquidacion->precio_unitario_balanza }}</td>
                <td>{{ $liquidacion->cantidad_pesajes }}</td>
                <td>{{ $liquidacion->suma_balanza }}</td>
            </tr>
            <tr>
                <td>PRUEBA METALURGICA</td>
                <td>{{ $liquidacion->precio_prueba_metalurgica }}</td>
                <td>{{ $liquidacion->cantidad_pruebas_metalurgicas }}</td>
                <td>{{ $liquidacion->suma_prueba_metalurgica }}</td>
            </tr>
            <tr>
                <td>DESCOCHE</td>
                <td>{{ $liquidacion->precio_descoche }}</td>
                <td>{{ $liquidacion->cantidad_descoche }}</td>
                <td>{{ $liquidacion->suma_descoche }}</td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2" class="font-semibold">TOTAL GASTOS ADICIONALES</td>
                <td class="font-semibold">{{ $liquidacion->gastos_adicionales }}</td>
            </tr>
        </tbody>
    </table>

    <br>
    <table>
        <thead>
            <tr class="title-section">
                <th colspan="7">CUADRO DE CONSUMO DE REACTIVOS</th>
            </tr>
            <tr class="subtitle-section">
                <th>REACTIVO</th>
                <th>PRECIO</th>
                <th>CONSUMO NETO</th>
                <th>LIM/TON</th>
                <th>LIM PROCESO</th>
                <th>EXCESO</th>
                <th>TOTAL</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($reactivos as $reactivo)
                @php
                    $total = $liquidacion->proceso->consumosreactivos
                        ->where('reactivo_id', $reactivo->id)
                        ->sum('cantidad');
                    $dev = $liquidacion->proceso->devolucionesReactivos
                        ->where('reactivo_id', $reactivo->id)
                        ->sum('cantidad');
                    $net = $total - $dev;

                    $price = $reactivo->detalles->precio ?? 0;
                    $lim = $reactivo->detalles->limite ?? 0;

                    $limTot = $lim * ($liquidacion->proceso->peso_total / 1000);
                    $exc = max(0, $net - $limTot);
                @endphp

                @if ($total > 0)
                    <tr>
                        <td>{{ $reactivo->producto->nombre_producto }}</td>
                        <td>{{ number_format($price, 2) }}</td>
                        <td>{{ number_format($net, 2) }}</td>
                        <td>{{ number_format($lim, 2) }}</td>
                        <td>{{ number_format($limTot, 2) }}</td>
                        <td>{{ number_format($exc, 2) }}</td>
                        <td>{{ number_format($exc * $price, 2) }}</td>
                    </tr>
                @endif
            @endforeach

            <tr>
                <td colspan="5"></td>
                <td class="font-semibold">TOTAL EXCESO</td>
                <td class="font-semibold">{{ $liquidacion->suma_exceso_reactivos }}</td>
            </tr>

        </tbody>
    </table>


    <br>
    <br>

    <div style="display: flex; width: 100%; gap: 20px;">

        <div style="width: 50%; font-size: 9px;">
            La empresa KILATE CORPORATION S.A.C cubre los gastos de:
            <br>
            20 gr/ton de D250 DOWFROTH
            <br>
            20 gr/ton de Z-6 DOWFROTH
            <br>
            5 kg/ton de cal

            </p>

            <p><strong>Facilitador: {{ $liquidacion->user->name ?? '' }}</strong></p>
        </div>

        <div style="width: 50%;">
            <table>
                <tbody>
                    <tr>
                        <td>Costo Tratamiento</td>
                        <td>{{ $liquidacion->suma_proceso }}</td>
                    </tr>
                    <tr>
                        <td>Costo Exceso Reactivos</td>
                        <td>{{ $liquidacion->suma_exceso_reactivos }}</td>
                    </tr>
                    <tr>
                        <td>Gastos Adicionales</td>
                        <td>{{ $liquidacion->gastos_adicionales }}</td>
                    </tr>
                    <tr class="font-semibold">
                        <td>Subtotal</td>
                        <td>{{ $liquidacion->subtotal }}</td>
                    </tr>
                    <tr class="font-semibold">
                        <td>IGV</td>
                        <td>{{ $liquidacion->igv }}</td>
                    </tr>
                    <tr class="font-semibold" style="background:#D5F2DC;">
                        <td>Total General</td>
                        <td>{{ $liquidacion->total }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        function imprimirLiquidacion(liquidacionId) {
            const iframe = document.getElementById('printFrame');
            iframe.src = '/liquidaciones/print/' + liquidacionId;
            iframe.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            };
        }
    </script>
</body>

</html>
