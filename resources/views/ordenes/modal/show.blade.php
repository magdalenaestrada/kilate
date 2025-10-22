<div class="modal fade text-left" id="ModalShow{{ $ordenServicio->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered custom-modal-width" role="document">
        <div class="modal-content">

            {{-- CABECERA --}}
            <div class="card-header">
                <div class="row justify-content-between">
                    <div class="col-md-6">
                        <h6 class="mt-2">
                            {{ __('ORDEN DE SERVICIO') }}
                        </h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" style="font-size: 30px" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <img style="width: 15px" src="{{ asset('images/icon/close.png') }}" alt="cerrar">
                        </button>
                    </div>
                </div>
            </div>

            {{-- CUERPO --}}
            <div class="card-body">
                <div class="form-group">

                    <div class="row">
                        <div class="form-group col-md-4 g-3">
                            <label class="text-sm">{{ __('CÓDIGO') }}</label>
                            <input class="form-control form-control-sm" value="{{ $ordenServicio->codigo }}" disabled>
                        </div>

                        <div class="form-group col-md-4 g-3">
                            <label class="text-sm">{{ __('FECHA DE CREACIÓN') }}</label>
                            <input class="form-control form-control-sm"
                                value="{{ \Carbon\Carbon::parse($ordenServicio->created_at)->format('d/m/Y') }}"
                                disabled>
                        </div>

                        <div class="form-group col-md-4 g-3">
                            <label class="text-sm">{{ __('ESTADO') }}</label>
                            <input class="form-control form-control-sm" value="{{ $ordenServicio->estado }}" disabled>
                        </div>

                        <div class="form-group col-md-4 g-3">
                            <label class="text-sm">{{ __('FECHA INICIO') }}</label>
                            <input class="form-control form-control-sm" value="{{ $ordenServicio->fecha_inicio }}"
                                disabled>
                        </div>

                        <div class="form-group col-md-4 g-3">
                            <label class="text-sm">{{ __('FECHA FIN') }}</label>
                            <input class="form-control form-control-sm" value="{{ $ordenServicio->fecha_fin }}"
                                disabled>
                        </div>
                    </div>

                    {{-- PROVEEDOR --}}
                    @if ($ordenServicio->proveedor)
                        <div class="row mb-3 mt-2">
                            <div class="form-group col-md-4 g-3">
                                <label class="text-sm">{{ __('RUC PROVEEDOR') }}</label>
                                <input class="form-control form-control-sm"
                                    value="{{ $ordenServicio->proveedor->ruc }}" disabled>
                            </div>

                            <div class="form-group col-md-8 g-3">
                                <label class="text-sm">{{ __('RAZÓN SOCIAL PROVEEDOR') }}</label>
                                <input class="form-control form-control-sm"
                                    value="{{ $ordenServicio->proveedor->razon_social }}" disabled>
                            </div>
                        </div>
                    @endif

                    {{-- COSTOS --}}
                    <div class="row">
                        <div class="form-group col-md-6 g-3">
                            <label class="text-sm">{{ __('COSTO ESTIMADO (S/)') }}</label>
                            <input class="form-control form-control-sm"
                                value="{{ number_format($ordenServicio->costo_estimado, 2) }}" disabled>
                        </div>

                        <div class="form-group col-md-6 g-3">
                            <label class="text-sm">{{ __('COSTO FINAL (S/)') }}</label>
                            <input class="form-control form-control-sm"
                                value="{{ number_format($ordenServicio->costo_final, 2) }}" disabled>
                        </div>
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="form-group col-md-12 g-3">
                        <label class="text-sm">{{ __('DESCRIPCIÓN / OBSERVACIÓN') }}</label>
                        <textarea class="form-control form-control-sm" rows="3" disabled>{{ $ordenServicio->descripcion ?? 'Sin descripción registrada' }}</textarea>
                    </div>

                    {{-- TABLA DE SERVICIOS --}}
                    <div class="mt-3 table-responsive">
                        @if ($ordenServicio->detalles && $ordenServicio->detalles->count() > 0)
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr class="text-center" style="font-size: 14px">
                                        <th>{{ __('SERVICIO') }}</th>
                                        <th>{{ __('CANTIDAD') }}</th>
                                        <th>{{ __('PRECIO UNITARIO (S/)') }}</th>
                                        <th>{{ __('SUBTOTAL (S/)') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenServicio->detalles as $detalle)
                                        <tr class="text-center" style="font-size: 13px">
                                            <td>{{ $detalle->descripcion }}</td>
                                            <td>{{ $detalle->cantidad }}</td>
                                            <td>{{ number_format($detalle->precio_unitario, 2) }}</td>
                                            <td>{{ number_format($detalle->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-warning text-right">
                                        <td colspan="3" class="text-right font-weight-bold">TOTAL:</td>
                                        <td class="text-center font-weight-bold">
                                            {{ number_format($ordenServicio->costo_final, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <p class="text-center mt-3">No hay detalles de servicio registrados.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
