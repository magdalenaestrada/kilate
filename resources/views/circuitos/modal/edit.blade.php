<div class="modal fade" id="ModalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Editar Circuito') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCircuitoEdit">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="circuito_id_edit">
                    <div class="mb-3">
                        <label>{{ __('Descripción') }}</label>
                        <input type="text" class="form-control" id="descripcion_edit" name="descripcion" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Actualizar') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
