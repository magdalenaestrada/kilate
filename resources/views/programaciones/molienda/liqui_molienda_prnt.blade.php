<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Molienda {{ $proceso->codigo }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        body {
            zoom: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
        }

        th {
            background: #d5e3f2;
            font-weight: bold;
        }

        .title-section {
            font-weight: bold;
            text-align: rgb(212, 214, 243) background: #d5e3f2;
        }

        .subtitle-section {
            background: #e8eaf9;
        }

        img.logo {
            width: 100px;
            height: 65px;
            object-fit: contain;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 6px;
        }
    </style>

</head>

<body>

    <table>
        <tbody>
            <tr class="subtitle-section">
                <td colspan="10" style="text-align: right; font-size: 12px;">
                    MOLIENDA:<b> {{ $proceso->codigo }}
                    </b>
                </td>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
            <tr>
                <td rowspan="2" colspan="1" style="text-align: center;">
                    <img src="{{ asset('images/terra_mining.jpg') }}" class="logo">
                </td>

                <td colspan="5" class="font-semibold">
                    <b>LIQUIDACIÓN DE MOLIENDA EN PLANTA DE BENEFICIO
                        MINERA ALFA GOLDEN S.A.C.</b>
                </td>

                <td class="font-semibold">RUC</td>
                <td colspan="1">20606034629</td>
            </tr>

            {{-- Dirección --}}
            <tr>
                <td colspan="5">Carretera Pampa de Chauchilla km 1 Fundo Santa Cirila Ica Nasca</td>
                <td class="font-semibold">FECHA</td>
                <td colspan="1">{{ date('d/m/Y') }}</td>
            </tr>

            <tr>
                <td class="font-semibold">RAZÓN SOCIAL</td>
                <td colspan="4">{{ $proceso->lote->cliente->nombre }}</td>

                <td class="font-semibold">RUC</td>
                <td colspan="3">{{ $proceso->lote->cliente->documento }}</td>
            </tr>

            <tr>
                <td class="font-semibold">LOTE</td>
                <td colspan="4">{{ $proceso->pesos->first()->lote->nombre ?? '' }}</td>
                <td class="font-semibold">TON. TOTAL</td>
                <td colspan="4">{{ number_format($proceso->peso_total / 1000, 2) }}</td>
            </tr>

            <tr>
                <td class="font-semibold">CIRCUITO</td>
                <td colspan="4">{{ $proceso->circuito }}</td>

                <td class="font-semibold">ORIGEN DEL MINERAL</td>
                <td colspan="4"></td>

            </tr>

        </tbody>
    </table>


    <br>

    {{-- Tabla de Pesos --}}
    <table>
        <thead class="subtitle-section">
            <tr>
                <th>NRO</th>
                <th>BALANZA</th>
                <th>FECHA I.</th>
                <th>FECHA S.</th>
                <th>HORA I.</th>
                <th>HORA S.</th>
                <th>PLACA</th>
                <th>PRODUCTO</th>
                <th>CONDUCTOR</th>
                <th>GUÍA</th>
                <th>GUÍA T.</th>
                <th>NETO</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($proceso->pesos as $peso)
                <tr>
                    <td>{{ $peso->NroSalida }}</td>
                    <td>ALFA</td>
                    <td>{{ \Carbon\Carbon::parse($peso->Fechai)->format('d/m/Y') }}</td>
                    <td>{{ $peso->Fechas ? \Carbon\Carbon::parse($peso->Fechas)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $peso->Horai ? \Carbon\Carbon::parse($peso->Horai)->format('H:i:s') : '-' }}</td>
                    <td>{{ $peso->Horas ? \Carbon\Carbon::parse($peso->Horas)->format('H:i:s') : '-' }}</td>
                    <td>{{ $peso->Placa }}</td>
                    <td>{{ $peso->Producto }}</td>
                    <td>{{ $peso->Conductor }}</td>
                    <td>{{ $peso->guia }}</td>
                    <td>{{ $peso->guiat }}</td>
                    <td>{{ $peso->Neto }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Otras Balanzas --}}
    @if ($proceso->pesosOtrasBal->count() > 0)
        <br>
        <table>
            <thead class="subtitle-section">
                <tr>
                    <th colspan="12">OTRAS BALANZAS</th>
                </tr>
                <tr>
                    <th>NRO</th>
                    <th>BALANZA</th>
                    <th>FECHA I.</th>
                    <th>FECHA S.</th>
                    <th>HORA I.</th>
                    <th>HORA S.</th>
                    <th>PLACA</th>
                    <th>PRODUCTO</th>
                    <th>CONDUCTOR</th>
                    <th>GUÍA</th>
                    <th>GUÍA T.</th>
                    <th>NETO</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($proceso->pesosOtrasBal as $peso)
                    <tr>
                        <td>{{ $peso->id }}</td>
                        <td>{{ $peso->balanza }}</td>
                        <td>{{ \Carbon\Carbon::parse($peso->fechai)->format('d/m/Y') }}</td>
                        <td>{{ $peso->fechas ? \Carbon\Carbon::parse($peso->fechas)->format('d/m/Y') : '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($peso->fechai)->format('H:i:s') }}</td>
                        <td>{{ $peso->fechas ? \Carbon\Carbon::parse($peso->fechas)->format('H:i:s') : '-' }}</td>
                        <td>{{ $peso->placa }}</td>
                        <td>{{ $peso->producto }}</td>
                        <td>{{ $peso->conductor }}</td>
                        <td>{{ $peso->guia }}</td>
                        <td>{{ $peso->guiat }}</td>
                        <td>{{ $peso->neto }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif


    {{-- Horas Trabajadas --}}
    <br>
    <table>
        <thead class="subtitle-section">
            <tr>
                <th>FECHA INICIO</th>
                <th>HORA INICIO</th>
                <th>FECHA FIN</th>
                <th>HORA FIN</th>
                <th>TONELAJE</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($fechas as $f)
                <tr>
                    <td>{{ $f->fecha_inicio }}</td>
                    <td>{{ $f->hora_inicio }}</td>
                    <td>{{ $f->fecha_fin }}</td>
                    <td>{{ $f->hora_fin }}</td>
                    <td>{{ $f->tonelaje }}</td>
                </tr>
            @endforeach

            <tr class="title-section">
                <td colspan="4">TOTAL HORAS</td>
                <td>{{ number_format($fechas->sum('tonelaje'), 2) }}</td>
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
                <td>MOLIENDA</td>
                <td>{{ $molienda->cantidad_procesada_tn }}</td>
                <td> ${{ $molienda->precio_unitario_proceso }}</td>
                <td> ${{ $molienda->suma_proceso }}</td>
            </tr>
        </tbody>
    </table>

    <br>
    <div>
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
                    <td>ALIMENTACIÓN</td>
                    <td> ${{ $molienda->precio_unitario_comida }}</td>
                    <td>{{ $molienda->cantidad_comidas }}</td>
                    <td> ${{ $molienda->suma_comedor }}</td>
                </tr>
                <tr>
                    <td>BALANZA</td>
                    <td> ${{ $molienda->precio_unitario_balanza }}</td>
                    <td>{{ $molienda->cantidad_pesajes }}</td>
                    <td> ${{ $molienda->suma_balanza }}</td>
                </tr>
                <tr>
                    <td>ANALISIS DE LABORATORIO</td>
                    <td> ${{ $molienda->precio_prueba_metalurgica }}</td>
                    <td>{{ $molienda->cantidad_pruebas_metalurgicas }}</td>
                    <td> ${{ $molienda->suma_prueba_metalurgica }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" class="font-semibold">TOTAL GASTOS ADICIONALES</td>
                    <td class="font-semibold"> ${{ $molienda->gastos_adicionales }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>

    <div style="display: flex; width: 100%; gap: 20px;">

        <div style="width: 50%; font-size: 12px;">
            <p><strong>Facilitador: {{ $molienda->user->name }}</strong></p>
        </div>

        <div style="width: 50%;">
            <table>
                <tbody>
                    <tr>
                        <td>Costo Tratamiento</td>
                        <td> ${{ $molienda->suma_proceso }}</td>
                    </tr>

                    <tr>
                        <td>Gastos Adicionales</td>
                        <td> ${{ $molienda->gastos_adicionales }}</td>
                    </tr>

                    <tr class="font-semibold">
                        <td>Subtotal</td>
                        <td> ${{ $molienda->subtotal }}</td>
                    </tr>

                    <tr class="font-semibold">
                        <td>IGV</td>
                        <td> ${{ $molienda->igv }}</td>
                    </tr>

                    <tr class="font-semibold" style="background:#d5e0f2;">
                        <td>Total General</td>
                        <td> ${{ $molienda->total }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
<script>
    function imprimirMolienda(moliendaId) {
        const iframe = document.getElementById('printFrame');
        iframe.src = '/molienda/imprimir/' + moliendaId;
        iframe.onload = function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    }
</script>
