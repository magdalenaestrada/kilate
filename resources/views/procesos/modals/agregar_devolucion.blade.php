<div class="modal fade" id="modalDevolucion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Agregar Devolución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formDevolucion">

                    <div class="mb-3">
                        <label class="form-label">Reactivo</label>
                        <select id="dev_reactivo_id" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach ($proceso->consumosreactivos as $c)
                                <option value="{{ $c->reactivo_id }}">
                                    {{ $c->reactivo->producto->nombre_producto }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Cantidad</label>
                        <input type="number" id="dev_cantidad" class="form-control" step="0.01">
                    </div>

                    @php
                        $now = now()->format('Y-m-d\TH:i');
                    @endphp

                    <div class="mb-3">
                        <label>Fecha</label>
                        <input type="datetime-local" id="dev_fecha" class="form-control" value="{{ $now }}"
                            max="{{ $now }}">
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnGuardarDevolucion" class="btn btn-primary">Guardar</button>
            </div>

        </div>
    </div>
</div>
