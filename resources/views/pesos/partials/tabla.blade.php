<!-- CUADRO AMARILLO GRANDE CON LA SUMA -->
<div class="alert alert-warning border-warning mb-4" style="background-color: #fff3cd; border: 3px solid #ffc107;">
    <div class="d-flex justify-content-center align-items-center">
        <h2 class="mb-0 fw-bold text-dark">
            <i class="bi bi-calculator-fill me-2"></i>
            TOTAL NETO: {{ number_format($sumaNetos, 2) }} KG
        </h2>
    </div>
</div>

<div class="d-flex justify-content-end mb-3 gap-2">
    <div class="col-md-2">
        <select id="estado-masivo" class="form-control">
            <option value="">Seleccionar estado</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <div class="combo">
            <input type="text" id="lote-masivo-input" class="form-control form-control-sm"
                placeholder="Escriba o seleccione..." style="min-width: 150px;" autocomplete="off">

            <select id="lote-masivo-select" class="form-control form-control-sm" size="5"
                style="display: none; min-width: 150px; text-align: left;">
                @foreach ($lotes as $l)
                    <option value="{{ $l->id }}">{{ $l->nombre }}</option>
                @endforeach
            </select>

            <input type="hidden" id="lote-masivo-id">
        </div>
    </div>
    <div class="col-md-auto">
        <button class="btn btn-primary" id="btn-asignar-masivo">
            <i class="bi bi-check2-circle"></i> Asignar Seleccionados
        </button>
    </div>
    <div class="col-md-auto">
        <button type="button" class="btn btn-success" id="btn-exportar">
            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
        </button>
    </div>
</div>

<!-- TABLA DE DATOS -->
<div style="overflow-x: auto;">
    <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-light">
            <tr>
                <th><input type="checkbox" id="check-all"></th>
                <th>N° Salida</th>
                <th>Estado</th>
                <th>Lote</th>
                <th>Acción</th>
                <th>Fecha I.</th>
                <th>Fecha S.</th>
                <th>Producto</th>
                <th>Placa</th>
                <th>Conductor</th>
                <th>Razón Social</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Neto</th>
                <th>Observación</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($pesos as $peso)
                @php
                    $estadoActual = optional($peso->estado);
                    $loteActual = optional($peso->lote);
                    $tieneEstado = !is_null($estadoActual->id);

                    $liquidado = $estadoActual->id === 3;
                    $retirado = $estadoActual->id === 4;
                    $cancha = $estadoActual->id === 1;
                    $procesado = $estadoActual->id === 2;

                    $filaClase = '';
                    if ($liquidado) {
                        $filaClase = 'bg-liquidado';
                    } elseif ($retirado) {
                        $filaClase = 'bg-retirado';
                    } elseif ($cancha) {
                        $filaClase = 'bg-cancha';
                    } elseif ($procesado) {
                        $filaClase = 'bg-procesado';
                    }
                @endphp

                <tr class="{{ $filaClase }}">
                    <td>
                        <input type="checkbox" class="check-item" value="{{ $peso->NroSalida }}"
                            @if ($tieneEstado) disabled @endif>
                    </td>
                    <td>{{ $peso->NroSalida }}</td>
                    <td>
                        <select class="form-select form-select-sm estado-select w-150"
                            data-peso="{{ $peso->NroSalida }}" {{ $tieneEstado ? 'disabled' : '' }}>
                            <option value="">Elegir</option>
                            @foreach ($estados as $estadoOpt)
                                <option value="{{ $estadoOpt->id }}"
                                    {{ $estadoActual->id == $estadoOpt->id ? 'selected' : '' }}>
                                    {{ $estadoOpt->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <div class="combo">
                            <input type="text" class="lote-input form-control form-control-sm"
                                placeholder="Escriba o seleccione..." value="{{ $loteActual->nombre ?? '' }}"
                                data-peso="{{ $peso->NroSalida }}" style="min-width: 150px;"
                                @if ($tieneEstado) disabled @endif>

                            <select class="lote-select form-control form-control-sm" size="5"
                                style="display: none; min-width: 150px; text-align: left;">
                                @foreach ($lotes as $loteOption)
                                    <option value="{{ $loteOption->id }}">
                                        {{ $loteOption->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        @if (!$tieneEstado)
                            <button class="btn btn-success btn-sm guardar-btn" data-peso="{{ $peso->NroSalida }}">
                                <i class="bi bi-save"></i> Guardar
                            </button>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-check-circle"></i> Guardado
                            </span>
                        @endif
                    </td>
                    <td>{{ $peso->Fechai }}</td>
                    <td>{{ $peso->Fechas }}</td>
                    <td>{{ $peso->Producto }}</td>
                    <td>{{ $peso->Placa }}</td>
                    <td>{{ $peso->Conductor }}</td>
                    <td>{{ $peso->RazonSocial }}</td>
                    <td>{{ $peso->origen }}</td>
                    <td>{{ $peso->destino }}</td>
                    <td>{{ number_format($peso->Neto, 2) }}</td>
                    <td>{{ $peso->Observacion }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3"></i>
                        <p class="mb-0 mt-2">No hay registros que coincidan con los filtros</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- PAGINACIÓN -->
    <div class="d-flex justify-content-center">
        {{ $pesos->links() }}
    </div>
</div>