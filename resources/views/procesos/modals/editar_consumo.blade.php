<div class="modal fade" id="modalEditarConsumo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Consumo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <form id="formEditarConsumo">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_consumo_id">
                    <div class="mb-3">
                        <label class="form-label">Reactivo</label>
                        <select id="edit_reactivo_id" class="form-control" disabled>
                            <option value="">Seleccione</option>
                            @foreach ($todos_reactivos as $reactivo)
                                <option value="{{ $reactivo->reactivo_id }}">
                                    {{ $reactivo->reactivo->producto->nombre_producto }}
                                </option>
                            @endforeach
                        </select>


                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" step="0.01" id="edit_cantidad" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="datetime-local" id="edit_fecha" class="form-control" max="{{ $now }}"
                            required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnActualizarConsumo" class="btn btn-primary">Actualizar</button>
            </div>
        </div>
    </div>
</div>
