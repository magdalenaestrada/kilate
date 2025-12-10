<div class="modal fade text-left" id="ModalEdit{{ $sociedad->id }}" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">


            <div class="card-header row justify-content-between">
                <div class="col-md-6">
                    <h6 class="mt-2">
                        {{ __('EDITAR SOCIEDAD') }}
                    </h6>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" style="font-size: 30px" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <img style="width: 15px" src="{{ asset('images/icon/close.png') }}" alt="cerrar">
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form class="editar-cuenta" action="{{ route('lqsociedades.update', $sociedad->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">

                        <div class="form mb-1 col-md-6">
                            <label for="codigo" class="text-sm">{{ __('CODIGO') }}</label>
                            <input name="codigo" value="{{ $sociedad->codigo ? $sociedad->codigo : '' }}"
                                id="nombre" class=" form-control form-control-sm"
                                placeholder="Documento del beneficiario..." type="text" disabled>
                        </div>

                        <!-- For Documento Beneficiario -->
                        <div class="form mb-1 col-md-6">
                            <label for="nombre" class="text-sm">{{ __('NOMBRE') }}</label>
                            <input name="nombre" value="{{ $sociedad->nombre ? $sociedad->nombre : '' }}"
                                id="nombre" class=" form-control form-control-sm"
                                placeholder="Documento del beneficiario..." type="text">
                        </div>

                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ __('CLIENTES') }}</th>
                                    <th>
                                        <button class="btn-add" type="button"
                                            onclick="create_tr('table_body_edit_{{ $sociedad->id }}')">
                                            <div class="add-sign">+</div>
                                            <div class="add-text">AÑADIR</div>
                                        </button>
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="table_body_edit_{{ $sociedad->id }}">
                                @forelse ($sociedad->clientes as $cliente)
                                    <tr>
                                        <td>
                                            <select name="clientes[]" class="form-control form-control-sm buscador"
                                                style="width: 400px">
                                                <option value="">-- SELECCIONE UNA OPCIÓN --</option>
                                                @foreach ($clientes as $c)
                                                    <option value="{{ $c->id }}"
                                                        {{ $c->id == $cliente->id ? 'selected' : '' }}>
                                                        {{ strtoupper($c->nombre) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button class="button-remove" type="button" onclick="remove_tr(this)">
                                                <svg viewBox="0 0 448 512" class="remove-icon">
                                                    <path
                                                        d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>
                                            <select name="clientes[]" class="form-control form-control-sm buscador"
                                                style="width: 400px">
                                                <option value="">-- SELECCIONE UNA OPCIÓN --</option>
                                                @foreach ($clientes as $c)
                                                    <option value="{{ $c->id }}">{{ strtoupper($c->nombre) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button class="button-remove" type="button" onclick="remove_tr(this)">
                                                <svg viewBox="0 0 448 512" class="remove-icon">
                                                    <path
                                                        d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>


                        <div class="col-md-12 text-right g-3 mt-2">
                            <button type="submit" class="btn btn-secondary btn-sm">
                                {{ __('Guardar') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
