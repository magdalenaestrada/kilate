<div class="modal fade text-left" id="ModalEditar{{ $reposicioncaja->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="card-header">
                <div class="row justify-content-between">
                    <div class="col-md-6">
                        <h6 class="mt-2">
                            {{ __('EDITAR SALIDA DE LA CUENTA') }}
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

            <div class="card-body">
                <form class="editar-salidacuenta"
                    action="{{ route('tsreposicionescajas.update', $reposicioncaja->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6 g-3">
                            <label for="caja" class="text-sm">
                                {{ __('CAJA') }}
                            </label>
                            <br>
                            <select name="caja_id" id="caja"
                                class="form-control form-control-sm buscador @error('caja') is-invalid @enderror"
                                aria-label="" style="width: 100%" required="">
                                @foreach ($cajas as $caja)
                                    <option value="{{ $caja->id }}"
                                        {{ $reposicioncaja->caja_id == $caja->id ? 'selected' : '' }}>
                                        {{ $caja->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('caja')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- CUENTA DE PROCEDENCIA -->
                        <div class="form-group col-md-6 g-3">
                            <label for="cuenta_procedencia" class="text-sm">
                                {{ __('CUENTA DE PROCEDENCIA') }}
                            </label>
                            <br>
                            <select name="cuenta_procedencia_id" id="cuenta_procedencia"
                                class="form-control form-control-sm buscador @error('cuenta_procedencia') is-invalid @enderror"
                                aria-label="" style="width: 100%" required="">
                                @foreach ($cuentas as $cuenta)
                                    @if ($cuenta->tipomoneda->nombre == 'SOLES')
                                        <option value="{{ $cuenta->id }}"
                                            {{ $reposicioncaja->cuenta_procedencia_id == $cuenta->id ? 'selected' : '' }}>
                                            {{ $cuenta->nombre }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('cuenta_procedencia')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- TIPO DE COMPROBANTE -->
                        <div class="form-group col-md-6 g-3">
                            <label for="tipo_comprobante" class="text-sm">
                                {{ __('TIPO DE COMPROBANTE') }}
                            </label>
                            <br>
                            <select name="tipo_comprobante_id" id="tipo_comprobante"
                                class="form-control form-control-sm buscador @error('tipo_comprobante') is-invalid @enderror"
                                aria-label="" style="width: 100%">
                                @foreach ($tiposcomprobantes as $tipo)
                                    <option value="{{ $tipo->id }}"
                                        {{ $reposicioncaja->tipo_comprobante_id == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_comprobante')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- NRO COMPROBANTE -->
                        <div class="form col-md-6 mb-3" style="margin-top: 30px">
                            <input name="comprobante_correlativo" id="comprobante_correlativo"
                                class="form-control form-control-sm"
                                value="{{ $reposicioncaja->comprobante_correlativo }}"
                                placeholder="Ingrese el correlativo del comprobante" type="text">
                            <span class="input-border"></span>
                        </div>

                        <!-- MOTIVO -->
                        <div class="form-group col-md-12 g-3">
                            <label for="motivo" class="text-sm">
                                {{ __('MOTIVO') }}
                            </label>
                            <br>
                            <select name="motivo_id" id="motivo"
                                class="form-control form-control-sm buscador @error('motivo') is-invalid @enderror"
                                aria-label="" style="width: 100%" required="">
                                @foreach ($motivos as $motivo)
                                    <option value="{{ $motivo->id }}"
                                        {{ $reposicioncaja->motivo_id == $motivo->id ? 'selected' : '' }}>
                                        {{ $motivo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('motivo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div class="form col-md-12 mb-3">
                            <input name="descripcion" id="descripcion"
                                value="{{ $reposicioncaja->descripcion ? $reposicioncaja->descripcion : '' }}"
                                class="form-control form-control-sm" placeholder="Ingrese la descripción"
                                type="text">
                            <span class="input-border"></span>
                        </div>

                        <!-- FECHA -->
                        <div class="form col-md-6 mb-3">
                            <input name="fecha" id="fecha" class="form-control form-control-sm"
                                placeholder="Ingrese la fecha"
                                value="{{ $reposicioncaja->salidacuenta->fecha ? \Carbon\Carbon::parse($reposicioncaja->salidacuenta->fecha)->format('Y-m-d') : '' }}"
                                type="date">
                            <span class="input-border"></span>
                        </div>

                        <!-- MONTO -->
                        <div class="form col-md-6 mb-3">
                            <input name="monto" id="monto" class="form-control form-control-sm"
                                value="{{ $reposicioncaja->monto }}" placeholder="Ingrese el monto" required=""
                                type="text">
                            <span class="input-border"></span>
                        </div>

                        <div class="col-md-12 text-right g-3">
                            <button type="submit" class="btn btn-secondary btn-sm">
                                {{ __('GUARDAR') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
