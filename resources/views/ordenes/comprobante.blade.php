@extends('admin.layout')

@section('content')
    <div class="container">
        <br>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <h6 class="mt-2">
                                    {{ __('CANCELAR ORDEN DE SERVICIO') }}
                                </h6>
                            </div>
                            <div class="col-md-6 text-right">
                                <a class="btn btn-danger btn-sm" href="{{ route('orden-servicio.index') }}">
                                    {{ __('VOLVER') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <div class="row" style="margin-top:-10px">
                                <div class="form-group col-md-3 g-3">
                                    <label for="documento_proveedor" class="text-sm">
                                        {{ __('RUC PROVEEDOR') }}
                                    </label>
                                    <div class="input-group">
                                        <input class="form-control form-control-sm" value="{{ $orden->proveedor->ruc }}"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group col-md-5 g-3">
                                    <label for="datos_proveedor" class="text-sm">
                                        {{ __('RAZÓN SOCIAL PROVEEDOR') }}
                                    </label>
                                    <input class="form-control form-control-sm"
                                        value="{{ $orden->proveedor->razon_social }}" disabled>
                                </div>
                                <div class="form-group col-md-2 g-3">
                                    <label for="inventarioingreso_fin" class="text-sm">
                                        {{ __('ESTADO DE LA ORDEN') }}
                                    </label>
                                    @php
                                        $estados = [
                                            'P' => ['label' => 'PENDIENTE'],
                                            'E' => ['label' => 'EN PROCESO'],
                                            'F' => ['label' => 'COMPLETADO'],
                                            'A' => ['label' => 'CANCELADO'],
                                            'C' => ['label' => 'PAGADO'],
                                        ];
                                        $estado = $estados[$orden->estado_servicio]['label'] ?? 'Desconocido';

                                    @endphp
                                    <input class="form-control form-control-sm" value="{{ $estado }}" disabled>
                                </div>
                                <div class="form-group col-md-2 g-3">
                                    <label for="inventarioingreso_fin" class="text-sm">
                                        {{ __('FECHA DE CREACIÓN') }}
                                    </label>
                                    <input class="form-control form-control-sm" value="{{ $orden->created_at }}" disabled>
                                </div>
                            </div>




                            @if ($orden->proveedor)
                                <div class="row mb-3">
                                    <div class="form-group col-md-12 g-3">
                                        <label for="descripcion" class="text-sm">
                                            {{ __('OBSERVACIÓN') }}
                                        </label>
                                        <textarea class="form-control form-control-sm" disabled>{{ $orden->descripcion ? $orden->descripcion : 'No hay observación' }}</textarea>
                                    </div>


                                </div>
                            @endif






                            <div class="mt-2 table-responsive">
                                @if (count($orden->detalles) > 0)
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr class="text-center">

                                                <th scope="col">
                                                    {{ __('PRODUCTO') }}
                                                </th>
                                                <th scope="col">
                                                    {{ __('FECHA DE CREACIÓN') }}
                                                </th>

                                                <th scope="col">
                                                    {{ __('CANTIDAD') }}
                                                </th>
                                                <th scope="col">
                                                    {{ __('VALOR UNITARIO') }}
                                                </th>
                                                <th scope="col">
                                                    {{ __('SUBTOTAL') }}
                                                </th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orden->detalles as $detalle)
                                                <tr class="text-center">
                                                    <td scope="row">
                                                        {{ $detalle->descripcion }}
                                                    </td>
                                                    <td scope="row">
                                                        {{ $detalle->created_at }}
                                                    </td>
                                                    <td scope="row">
                                                        {{ $detalle->cantidad }}
                                                    </td>
                                                    <td scope="row">
                                                        {{ $detalle->precio_unitario }}
                                                    </td>
                                                    <td scope="row" class="text-right">
                                                        {{ $detalle->subtotal, 2 }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-warning">
                                                <td></td>
                                                <td></td>
                                                <td>SUBTOTAL: </td>
                                                <td></td>

                                                <td class="text-end">
                                                    <div class="text-right">
                                                        {{ number_format($orden->costo_estimado, 2) }}</div>
                                                </td>
                                            </tr>
                                        </tbody>



                                    </table>
                                @endif
                            </div>

                            <p class="text-center h5"> IMPORTE TOTAL: {{ $orden->costo_final }}
                                {{ $orden->tipomoneda }}</p>

                            <form action="{{ route('orden-servicio.cancelar', $orden->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-3 g-3">
                                        <label for="fecha_cancelacion" class="text-sm">
                                            {{ __('FECHA DE CANCELACION') }}
                                        </label>
                                        <input required class="form-control form-control-sm" type="date"
                                            name="fecha_cancelacion">
                                    </div>
                                    @php
                                        use Carbon\Carbon;
                                        $hoy = Carbon::now('America/Lima');
                                    @endphp

                                    <div class="form-group col-md-3 g-3">
                                        <label for="comprobante_correlativo" class="text-sm">
                                            {{ __('COMPROBANTE CORRELATIVO') }}
                                        </label>
                                        <input id="comprobante_correlativo" class="form-control" type="text"
                                            maxlength="13" name="comprobante_correlativo" required>
                                    </div>

                                    <div class="form-group col-md-3 g-3">
                                        <label for="tipopago" class="text-sm">
                                            {{ __('TIPO PAGO') }}
                                        </label>

                                        <select required name="tipopago" id="tipopago"
                                            class="form-select form-control form-control-sm @error('tipopago') is-invalid @enderror"
                                            aria-label="">
                                            <option value="">{{ __('-- Seleccione una opción') }}</option>

                                            <option value="CONTADO" {{ old('tipopago') == 'CONTADO' ? 'selected' : '' }}>
                                                CONTADO
                                            </option>
                                            <option value="CREDITO" {{ old('tipopago') == 'CRÉDITO' ? 'selected' : '' }}>
                                                CRÉDITO
                                            </option>
                                            <option value="A CUENTA" {{ old('tipopago') == 'A CUENTA' ? 'selected' : '' }}>
                                                A CUENTA
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3 g-3">
                                        <label for="fecha_emision" class="text-sm">
                                            {{ __('FECHA DE EMISIÓN COMPROBANTE') }}
                                        </label>
                                        <input class="form-control form-control-sm" required id="fecha_emision"
                                            type="date" name="fecha_emision" max="{{ $hoy->format('Y-m-d') }}">

                                    </div>

                                    @if ($orden->tipomoneda == 'DOLARES')
                                        <div class="form-group col-md-3 g-3">
                                            <label for="cambio_dia" class="text-sm ">
                                                {{ __('CAMBIO DEL DÍA') }}
                                            </label>
                                            <input class="form-control form-control-sm" type="text" name="cambio_dia"
                                                id="cambio_dia">
                                        </div>
                                    @endif

                                    <div class="col-md-12 text-right g-3 ">
                                        <button type="submit" class="btn btn-sm btn-info  ">
                                            {{ __('CANCELAR ORDEN DE COMPRA') }}
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @stop
    @push('js')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#fecha_emision').change(function() {
                    var selectedDate = $(this).val();

                    $.ajax({
                        url: '{{ route('get_selling_price') }}',
                        type: 'GET',
                        data: {
                            fecha: selectedDate
                        },
                        success: function(response) {
                            $('#cambio_dia').val(response.precio_venta);
                        },
                        error: function(xhr, status, error) {
                            // Handle errors
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>
        <script>
            const inp = document.getElementById('comprobante_correlativo');

            inp.addEventListener('input', () => {
                // quitar todo menos letras y dígitos
                let v = inp.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

                // separar serie (primeros 4) y correlativo (siguientes hasta 8)
                const serie = v.slice(0, 4);
                const correl = v.slice(4, 12);

                inp.value = correl ? `${serie}-${correl}` : serie;
            });
        </script>
    @endpush
