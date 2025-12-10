<div class="modal fade" id="modalEditarDevolucion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Devolución</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarDevolucion">
          @csrf
          @method('PUT')
          <input type="hidden" id="edit_devolucion_id">
          <div class="mb-3">
            <label class="form-label">Reactivo</label>
            <select id="edit_dev_reactivo_id" class="form-control" required>
              <option value="">Seleccione</option>
              @foreach($proceso->consumosreactivos as $c)
                <option value="{{ $c->reactivo_id }}">{{ $c->reactivo->producto->nombre_producto }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" step="0.01" id="edit_dev_cantidad" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="datetime-local" id="edit_dev_fecha" class="form-control" max="{{ $now }}" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button id="btnActualizarDevolucion" class="btn btn-primary">Actualizar</button>
      </div>
    </div>
  </div>
</div>
