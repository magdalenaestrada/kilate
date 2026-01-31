@extends('admin.layout')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/areas.css') }}">
@endpush

@section('content')
    <div class="container">
        <br>
        <div class="row d-flex justify-content-between align-items-center">
            <div class="loader">
                {{ __('CIRCUITOS REGISTRADOS') }}
            </div>
            <div class="text-right col-md-6">
                <a href="#" data-toggle="modal" data-target="#ModalCreate">
                    <button class="button-create">
                        {{ __('REGISTRAR CIRCUITO') }}
                    </button>
                </a>
            </div>
        </div>

        <br>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body ">
                        <table id="circuitos-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('ID') }}</th>
                                    <th scope="col">{{ __('DESCRIPCIÓN') }}</th>
                                    <th scope="col">{{ __('ACCIONES') }}</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 13px">
                                @if (count($circuitos) > 0)
                                    @foreach ($circuitos as $c)
                                        <tr>
                                            <td>{{ $c->id }}</td>
                                            <td>{{ strtoupper($c->descripcion) }}</td>
                                            <td>
                                                <button class="btn btn-info btn-sm btnEditar" data-id="{{ $c->id }}">
                                                    {{ __('Editar') }}
                                                </button>
                                                <button class="btn btn-danger btn-sm btnEliminar"
                                                    data-id="{{ $c->id }}">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            {{ __('No hay datos disponibles') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between mt-2">
                            <div>
                                {{ $circuitos->links('pagination::bootstrap-4') }}
                            </div>
                            <div>
                                Mostrando del {{ $circuitos->firstItem() }} al {{ $circuitos->lastItem() }} de
                                {{ $circuitos->total() }} registros
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('circuitos.modal.create')
    @include('circuitos.modal.edit')

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Guardar circuito
                $('#formCircuitoCreate').submit(function(e) {
                    e.preventDefault();
                    $.post("{{ route('circuitos.store') }}", $(this).serialize(), function(res) {
                        location.reload();
                    }).fail(function(err) {
                        Swal.fire('Error', err.responseJSON.message, 'error');
                    });
                });

                // Editar circuito
                $('.btnEditar').click(function() {
                    let id = $(this).data('id');
                    $.get(`/circuitos/${id}/edit`, function(data) {
                        $('#circuito_id_edit').val(data.id);
                        $('#descripcion_edit').val(data.descripcion);

                        // Bootstrap 5
                        var modalEdit = new bootstrap.Modal(document.getElementById('ModalEdit'));
                        modalEdit.show();
                    });
                });


                $('#formCircuitoEdit').submit(function(e) {
                    e.preventDefault();
                    let id = $('#circuito_id_edit').val();
                    $.ajax({
                        url: `/circuitos/${id}`,
                        type: 'PUT',
                        data: $(this).serialize(),
                        success: function(res) {
                            location.reload();
                        },
                        error: function(err) {
                            Swal.fire('Error', err.responseJSON.message, 'error');
                        }
                    });
                });

                // Eliminar circuito
                $('.btnEliminar').click(function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "No podrá revertir esto",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/circuitos/${id}`,
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(res) {
                                    location.reload();
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
