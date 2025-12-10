<!-- Botón Ver Precios -->
<button class="btn btn-sm btn-outline-primary mr-1" data-toggle="modal" data-target="#ModalViewPrecios{{ $reactivo->id }}">
    <i class="fas fa-eye"></i>
</button>

<div class="modal fade" id="ModalViewPrecios{{ $reactivo->id }}" tabindex="-1" role="dialog"
    aria-labelledby="viewPreciosLabel{{ $reactivo->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Precios de {{ $reactivo->producto->nombre_producto }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Precio: {{ $reactivo->detalles?->precio ?? 'N/A' }} | Límite:
                            {{ $reactivo->detalles?->limite ?? 'N/A' }}

                            <button class="btn btn-danger btn-sm btn-eliminar-detalle"
                                data-id="{{ $reactivo->detalles?->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </li>
                    </ul>
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
