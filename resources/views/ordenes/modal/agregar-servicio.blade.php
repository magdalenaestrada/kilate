<div class="modal fade text-left" id="ModalServicio" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mt-2">
                            {{ __('AÑADIR SERVICIO') }}
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
                <form method="POST" action="{{ route('invsalidasrapidas.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <label for="descripcion" name="descrpcion">DESCRIPCION:</label>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-3">
                            <label for="cantidad" class="form-label">CANTIDAD</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control form-control-sm">
                        </div>

                        <div class="col-3">
                            <label for="precio_unitario" class="form-label">PRECIO UNITARIO</label>
                            <input type="number" name="precio_unitario" id="precio_unitario"
                                class="form-control form-control-sm">
                        </div>

                        <div class="col-3">
                            <label for="subtotal" class="form-label">SUBTOTAL</label>
                            <input type="number" name="subtotal" id="subtotal" class="form-control form-control-sm"
                                readonly>
                        </div>
                    </div>

                    <button class="btn btn-sm btn-secondary pull-right" type="submit" id="saveOrder">Guardar</button>

                </form>

            </div>
        </div>
    </div>
</div>
