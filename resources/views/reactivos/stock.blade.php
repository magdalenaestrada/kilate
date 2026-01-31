@extends('admin.layout')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endpush

@section('content')
    <div class="container">
        <br>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="row card-header d-flex justify-content-between align-items-center">
                        <div class="col-md-6">{{ __('STOCK DE REACTIVOS') }}</div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-sm btn-special" style="border: 1px solid black; border-radius: 6px;"
                                data-toggle="modal" data-target="#ModalCreateReactivo">
                                {{ __('CREAR REACTIVO') }}
                            </button>
                        </div>
                    </div>

                    <div class="row align-items-center justify-content-between p-3">
                        <div class="col-md-6 input-container">
                            <input type="text" name="search" id="search" class="input-search form-control"
                                placeholder="Buscar aquí...">
                        </div>
                    </div>
                    <div class="row">
                        @forelse ($stockPorCircuito as $circuito => $stocks)
                            <div class="card-body table-responsive col-md-6">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <strong>CIRCUITO {{ $circuito }}</strong>
                                    </h5>

                                    <button class="btn btn-primary btn-sm" onclick="resetCircuito('{{ $circuito }}')">
                                        DEVOLVER REACTIVOS
                                    </button>

                                    <form id="form-reset-{{ $circuito }}"
                                        action="{{ route('reactivos.reset', $circuito) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </div>

                                <table class="table table-striped table-hover text-center mb-5">
                                    <thead>
                                        <tr>
                                            <th>NOMBRE</th>
                                            <th>CIRCUITO</th>
                                            <th>STOCK</th>
                                        </tr>
                                    </thead>

                                    <tbody style="font-size:13px">
                                        @forelse ($stocks as $stock_reactivo)
                                            <tr>
                                                <td>{{ $stock_reactivo->reactivo->producto->nombre_producto }}</td>
                                                <td>{{ $stock_reactivo->circuito->descripcion }}</td>
                                                <td>{{ $stock_reactivo->stock }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    No hay reactivos registrados para este circuito
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">
                                No hay circuitos disponibles
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop

<script>
    function resetCircuito(circuito) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Este cambio es irreversible, todo el stock regresará a 0",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, reiniciar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-reset-' + circuito).submit();
            }
        });
    }
</script>
