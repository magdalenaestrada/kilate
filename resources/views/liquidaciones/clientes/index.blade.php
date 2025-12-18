@extends('admin.layout')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/areas.css') }}">
@endpush

@section('content')
    <div class="container">

        <br>
        <div class="row d-flex justify-content-between align-items-center">
            <div class="loader">
                {{ __('CLIENTES REGISTRADOS') }}
            </div>
            <div class="text-right col-md-6">
                <a class="" href="#" data-toggle="modal" data-target="#ModalCreate">
                    <button class="button-create">
                        {{ __('REGISTRAR CLIENTE') }}
                    </button>
                </a>
            </div>
        </div>

        <br>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="products-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        {{ __('CODIGO') }}
                                    </th>
                                    <th scope="col">
                                        {{ __('DOCUMENTO') }}
                                    </th>
                                    <th scope="col">
                                        {{ __('NOMBRE') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody style="font-size: 13px">
                                @if (count($clientes) > 0)
                                    @foreach ($clientes as $cliente)
                                        <tr>
                                            <td scope="row">
                                                {{ strtoupper($cliente->codigo) }}
                                            </td>
                                            <td scope="row">
                                                {{ strtoupper($cliente->documento) }}
                                            </td>
                                            <td scope="row">
                                                {{ strtoupper($cliente->nombre) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            {{ __('NO HAY DATOS DISPONIBLES') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-between">
                            <div>
                                {{ $clientes->links('pagination::bootstrap-4') }}
                            </div>
                            <div>
                                {{ __('MOSTRANDO DEL') }} {{ $clientes->firstItem() }} {{ __('AL') }}
                                {{ $clientes->lastItem() }} {{ __('DE') }} {{ $clientes->total() }}
                                {{ __('REGISTROS') }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('liquidaciones.clientes.modal.create')

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/updateadvice.js') }}"></script>

        <script>
            @if (session('status'))
                Swal.fire('ÉXITO', '{{ session('status') }}', 'success');
            @elseif (session('error'))
                Swal.fire('ERROR', '{{ session('error') }}', 'error');
            @endif
        </script>
    @endpush

@endsection
