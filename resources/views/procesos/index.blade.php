@extends('admin.layout')

@section('content')
    <div class="container py-4">

        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <label for="filtroLote" class="col-form-label fw-semibold">Filtrar por lote:</label>
                    </div>
                    <div class="col">
                        <input type="text" id="filtroLote" class="form-control form-control-lg"
                            placeholder="Escribe el nombre o código del lote...">
                    </div>
                </div>
            </div>
        </div>


        {{-- LISTA DE PROCESOS --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-sm align-middle">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">CODIGO</th>
                                <th class="text-center">LOTE</th>
                                <th class="text-center">FECHA INICIO</th>
                                <th class="text-center">FECHA FIN</th>
                                <th class="text-center">CIRCUITO</th>
                                <th class="text-center">PESO TOTAL</th>
                                <th class="text-center">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($procesos as $proceso)
                                <tr>
                                    <td class="text-center">{{ $proceso->id }}</td>
                                    <td class="text-center">{{ $proceso->codigo }}</td>
                                    <td class="text-center">{{ $proceso->lote->nombre ?? '' }}</td>
                                    <td class="text-center">{{ $proceso->programacion->fecha_inicio ?? '' }}</td>
                                    <td class="text-center">{{ $proceso->programacion->fecha_fin ?? '' }}</td>
                                    <td class="text-center">{{ $proceso->circuito }}</td>
                                    <td class="text-center">{{ intval($proceso->peso_total) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            @if ($proceso->estado == 'P')
                                                <a href="{{ route('procesos.edit', $proceso->id) }}"
                                                    class="btn btn-sm btn-outline-dark bg-indigo-100 border hover:bg-indigo-200 mx-1">
                                                    Proceso
                                                </a>
                                                @if (!$proceso->liquidacion)
                                                    <form action="{{ route('procesos.edit', $proceso->id) }}" method="POST"
                                                        onsubmit="return confirmarEliminacion(event)" class="mx-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="procesoId" value="{{ $proceso->id }}">

                                                        <button type="submit"
                                                            class="btn btn-sm btn-danger text-white px-2 rounded-md">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <a href="{{ route('procesos.edit', $proceso->id) }}"
                                                    class="btn btn-sm btn-outline-dark bg-indigo-100 border hover:bg-indigo-200 mx-1">
                                                    Ver
                                                </a>
                                                <span class="badge bg-success px-3 py-2 mx-1">
                                                    Proceso Finalizado
                                                </span>
                                            @endif
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-gray-500">No hay procesos registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {!! $procesos->links('pagination::bootstrap-5') !!}

                </div>
            </div>
        </div>

    </div>

    {{-- SWEETALERT PARA CONFIRMAR --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarEliminacion(event) {
            event.preventDefault();
            const form = event.target;

            Swal.fire({
                title: '¿Eliminar este proceso?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
            });
        @endif

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('filtroLote');
            const tabla = document.querySelector('.table tbody');

            input.addEventListener('input', function() {
                const filtro = this.value.toLowerCase();

                tabla.querySelectorAll('tr').forEach(row => {
                    const lote = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    if (lote.includes(filtro)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>

@endsection
