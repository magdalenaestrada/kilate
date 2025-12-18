<div class="modal fade" id="modalPesoOtraBalanza" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-speedometer2 me-1"></i> Pesos de Otras Balanzas
                </h5>
                <button type="button" class="btn-close" id="btnCerrarModal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <form id="formPesoManual">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lote</label>
                            <div class="combo position-relative">
                                <input type="text" id="lote_id_input" class="form-control"
                                    placeholder="Escriba o seleccione..." autocomplete="off">

                                <select id="lote_id" class="form-control position-absolute w-100" size="5"
                                    style="display:none; z-index: 2000;">
                                    @foreach ($lotes as $lote)
                                        <option value="{{ $lote->id }}">{{ $lote->nombre }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="lote_id" id="lote_id_real">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fecha inicio</label>
                            <input type="datetime-local" class="form-control" id="fechai" name="fechai"
                                max="{{ $hoy }}" value="{{ $hoy }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fecha salida</label>
                            <input type="datetime-local" class="form-control" id="fechas" name="fechas"
                                value="{{ $hoy }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Producto</label>
                            <input type="text" class="form-control" name="producto" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Conductor</label>
                            <input type="text" class="form-control" name="conductor" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Placa</label>
                            <input type="text" class="form-control" name="placa" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Origen</label>
                            <input type="text" class="form-control" name="origen" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Destino</label>
                            <input type="text" class="form-control" name="destino" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Balanza</label>
                            <input type="text" class="form-control" name="balanza" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Bruto</label>
                            <input type="number" class="form-control" name="bruto" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tara</label>
                            <input type="number" class="form-control" name="tara" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Neto</label>
                            <input type="number" class="form-control" name="neto" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Guia T</label>
                            <input type="text" class="form-control" name="guiat" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Guia</label>
                            <input type="text" class="form-control" name="guia" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Observación</label>
                            <input class="form-control" name="observacion">
                        </div>

                </form>
                <hr>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary px-4" id="btnCancelar">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" id="btnGuardar">Guardar</button>
            </div>
        </div>
    </div>
</div>
