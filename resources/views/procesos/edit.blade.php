@extends('admin.layout')

@section('title', 'Editar Proceso')

@section('content')
    <br>
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-3">
            <form id="formFinalizarProceso" action="{{ route('proceso.finalizar', $proceso->id) }}" method="POST">
                @csrf
                @method('PUT')
                @if ($proceso->estado == 'P')
                    <button type="submit" class="btn btn-success" id="btnFinalizarProceso">Finalizar proceso</button>
                @endif
            </form>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <h6 class="mb-0 flex-grow-1">CONSUMO DE REACTIVOS</h6>
                        @if ($proceso->estado == 'P')
                            <button class="btn btn-light btn-sm" id="btnNuevoConsumo">
                                Agregar Consumo
                            </button>
                        @endif
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered text-sm" id="tablaConsumos">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Reactivo</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proceso->consumosreactivos as $consumo)
                                    <tr data-id="{{ $consumo->id }}" data-reactivo-id="{{ $consumo->reactivo_id }}">
                                        <td>{{ $consumo->id }}</td>
                                        <td>{{ $consumo->reactivo->producto->nombre_producto ?? '' }}</td>
                                        <td>{{ $consumo->cantidad }}</td>
                                        <td>{{ $consumo->fecha }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btnEditarConsumo">Editar</button>
                                            <button class="btn btn-danger btn-sm btnEliminarConsumo">Eliminar</button>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <h6 class="mb-0 flex-grow-1">DEVOLUCIÓN DE REACTIVOS {{ $proceso->codigo }}</h6>
                        @if ($proceso->estado == 'P')
                            <button class="btn btn-light btn-sm" id="btnNuevaDevolucion">
                                Agregar Devolución
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered text-sm" id="tablaDevoluciones">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Reactivo</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proceso->devolucionesReactivos as $dev)
                                    <tr data-id="{{ $dev->id }}" data-reactivo-id="{{ $dev->reactivo_id }}">
                                        <td>{{ $dev->id }}</td>
                                        <td>{{ $dev->reactivo->producto->nombre_producto ?? '' }}</td>
                                        <td>{{ $dev->cantidad }}</td>
                                        <td>{{ $dev->fecha }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php $now = now()->format('Y-m-d\TH:i'); @endphp
    @include('procesos.modals.agregar_consumo')
    @include('procesos.modals.agregar_devolucion')
    @include('procesos.modals.editar_consumo')
    @include('procesos.modals.editar_devolucion')

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            document.getElementById('btnNuevoConsumo').addEventListener('click', () => {
                $('#modalConsumo').modal('show');
            });

            document.getElementById('btnGuardarConsumo').addEventListener('click', async () => {
                const reactivo_id = document.getElementById('reactivo_id').value;
                const cantidad = document.getElementById('cantidad').value;
                const fecha = document.getElementById('fecha').value;

                if (!reactivo_id || !cantidad || !fecha) {
                    Swal.fire('Campos incompletos', 'Complete todos los campos', 'warning');
                    return;
                }
                console.log("ENVIANDO FETCH...");

                const res = await fetch("{{ route('reactivo.consumo') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        proceso_id: {{ $proceso->id }},
                        reactivo_id,
                        cantidad,
                        fecha
                    })
                });

                const data = await res.json();

                if (data.success) {
                    Swal.fire('Correcto', 'Consumo agregado', 'success');
                    location.reload();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });


            document.querySelectorAll('.btnEliminarConsumo').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const tr = e.target.closest('tr');
                    const id = tr.dataset.id;
                    const confirm = await Swal.fire({
                        title: '¿Eliminar consumo?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar'
                    });
                    if (!confirm.isConfirmed) return;

                    const res = await fetch("{{ route('procesos') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            consumoId: id
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Eliminado', 'Consumo eliminado', 'success');
                        tr.remove();
                    }
                });
            });

            document.querySelectorAll('.btnEliminarDevolucion').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const tr = e.target.closest('tr');
                    const id = tr.dataset.id;
                    const confirm = await Swal.fire({
                        title: '¿Eliminar devolución?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar'
                    });
                    if (!confirm.isConfirmed) return;

                    const res = await fetch("{{ route('procesos') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            devolucionId: id
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Eliminado', 'Devolución eliminada', 'success');
                        tr.remove();
                    }
                });
            });

        });
        document.getElementById('btnNuevaDevolucion').addEventListener('click', () => {
            $('#modalDevolucion').modal('show');
        });
        document.getElementById('btnGuardarDevolucion').addEventListener('click', async () => {
            const reactivo_id = document.getElementById('dev_reactivo_id').value;
            const cantidad = document.getElementById('dev_cantidad').value;
            const fecha = document.getElementById('dev_fecha').value;

            if (!reactivo_id || !cantidad || !fecha) {
                Swal.fire('Campos incompletos', 'Complete todos los campos', 'warning');
                return;
            }

            const res = await fetch("{{ route('reactivo.devolucion') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    proceso_id: {{ $proceso->id }},
                    reactivo_id,
                    cantidad,
                    fecha
                })
            });

            const data = await res.json();

            if (data.success) {
                Swal.fire('Correcto', 'Devolución agregada', 'success');
                location.reload();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });

        document.querySelectorAll('.btnEditarConsumo').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tr = e.target.closest('tr');
                const id = tr.dataset.id;

                const reactivoId = tr.dataset.reactivoId;

                const cantidad = tr.cells[2].innerText.trim();
                const fechaText = tr.cells[3].innerText.trim();
                document.getElementById('edit_consumo_id').value = id;

                const select = document.getElementById('edit_reactivo_id');
                select.value = reactivoId || '';
                select.dispatchEvent(new Event('change'));

                document.getElementById('edit_cantidad').value = parseFloat(cantidad);
                document.getElementById('edit_fecha').value = fechaText.replace(' ', 'T').slice(0, 16);

                $('#modalEditarConsumo').modal('show');
            });
        });

        document.getElementById('btnActualizarConsumo').addEventListener('click', async () => {
            const id = document.getElementById('edit_consumo_id').value;
            const reactivo_id = document.getElementById('edit_reactivo_id').value;
            const cantidad = parseFloat(document.getElementById('edit_cantidad').value);
            const fecha = document.getElementById('edit_fecha').value;

            if (!reactivo_id || !cantidad || !fecha) {
                Swal.fire('Campos incompletos', 'Complete todos los campos', 'warning');
                return;
            }

            const res = await fetch(`/reactivo/consumo/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reactivo_id,
                    cantidad,
                    fecha,
                    proceso_id: {{ $proceso->id }}
                })
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire('Actualizado', 'Consumo actualizado', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Error al actualizar', 'error');
            }
        });

        document.querySelectorAll('.btnEditarDevolucion').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tr = e.target.closest('tr');
                const id = tr.dataset.id;
                const reactivoId = tr.querySelector('td:nth-child(2)')?.dataset.reactivoId || '';
                const cantidad = tr.cells[2].innerText.trim();
                const fechaText = tr.cells[3].innerText.trim();

                document.getElementById('edit_devolucion_id').value = id;
                document.getElementById('edit_dev_reactivo_id').value = reactivoId || '';
                document.getElementById('edit_dev_cantidad').value = parseFloat(cantidad);
                document.getElementById('edit_dev_fecha').value = fechaText.replace(' ', 'T').slice(0, 16);

                $('#modalEditarDevolucion').modal('show');
            });
        });

        document.getElementById('btnActualizarDevolucion').addEventListener('click', async () => {
            const id = document.getElementById('edit_devolucion_id').value;
            const reactivo_id = document.getElementById('edit_dev_reactivo_id').value;
            const cantidad = parseFloat(document.getElementById('edit_dev_cantidad').value);
            const fecha = document.getElementById('edit_dev_fecha').value;

            if (!reactivo_id || !cantidad || !fecha) {
                Swal.fire('Campos incompletos', 'Complete todos los campos', 'warning');
                return;
            }

            const res = await fetch(`/reactivo/devolucion/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reactivo_id,
                    cantidad,
                    fecha,
                    proceso_id: {{ $proceso->id }}
                })
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire('Actualizado', 'Devolución actualizada', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Error al actualizar', 'error');
            }
        });

        document.getElementById('formFinalizarProceso').addEventListener('submit', async (e) => {
            e.preventDefault(); // Evita que el form envíe directamente

            const confirm = await Swal.fire({
                title: '¿Finalizar proceso?',
                text: 'Una vez finalizado no podrás modificar consumos ni devoluciones.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, finalizar',
                cancelButtonText: 'Cancelar'
            });

            if (!confirm.isConfirmed) return;

            e.target.submit(); // Enviar formulario REAL
        });
    </script>
@endpush
