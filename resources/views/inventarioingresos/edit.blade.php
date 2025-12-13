@extends('admin.layout')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container">
        <br>
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ __('Editar cotización de la Orden de Compra') }}</h6>
                        <div class="d-flex gap-2">

                        </div>
                    </div>
                    @php
                        use Carbon\Carbon;
                        $hoy = Carbon::now('America/Lima')->format('Y-m-d');
                    @endphp
                    <div class="card-body">
                        {{-- Cabecera compacta --}}
                        <div class="alert alert-info mb-3">
                            <strong>{{ __('ID:') }}</strong> {{ $inventarioingreso->id }} &nbsp;|&nbsp;
                            <strong>{{ __('Creado:') }}</strong> {{ $inventarioingreso->created_at->format('d/m/Y H:i:s') }}
                            &nbsp;|&nbsp;
                            <strong>{{ __('Estado:') }}</strong> {{ $inventarioingreso->estado }} &nbsp;|&nbsp;
                            <strong>{{ __('Estado de pago:') }}</strong> {{ $inventarioingreso->estado_pago }}
                        </div>

                        {{-- Form: solo se edita la cotización --}}
                        <form method="POST" action="{{ route('inventarioingresos.update', $inventarioingreso->id) }}"
                            class="mb-4">
                            @csrf
                            @method('PATCH')

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label" for="cotizacion">{{ __('Cotización') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            {{-- ícono lápiz inline para no depender de librerías --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 20h9" />
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                            </svg>
                                        </span>
                                        <input type="text" id="cotizacion" name="cotizacion"
                                            class="form-control @error('cotizacion') is-invalid @enderror"
                                            value="{{ old('cotizacion', $inventarioingreso->cotizacion) }}" maxlength="120"
                                            placeholder="Ej. COT-00123, RFQ-ABC-2025, etc." autofocus>
                                        @error('cotizacion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">
                                        Se actualizará únicamente este campo; los demás datos son de solo lectura.
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Fecha de creación') }}</label>
                                    <input type="date" class="form-control"
                                        value="{{ $inventarioingreso->created_at->format('Y-m-d') }}"
                                        max="{{ $hoy }}" id="fecha_creacion" name="fecha_creacion">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Moneda') }}</label>
                                    <input class="form-control" value="{{ $inventarioingreso->tipomoneda }}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Usuario OC') }}</label>
                                    <input class="form-control" value="{{ $inventarioingreso->usuario_ordencompra }}"
                                        disabled>
                                </div>

                                <div class="row g-3 mt-2">

                                </div>
                            </div>
                            <hr class="my-4">

                            {{-- Proveedor --}}
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">{{ __('Proveedor') }}</label>
                                    <input class="form-control"
                                        value="{{ optional($inventarioingreso->proveedor)->razon_social }}" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('RUC') }}</label>
                                    <input class="form-control" value="{{ optional($inventarioingreso->proveedor)->ruc }}"
                                        disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Dirección') }}</label>
                                    <input class="form-control"
                                        value="{{ optional($inventarioingreso->proveedor)->direccion }}" disabled>
                                </div>
                            </div>

                            {{-- Totales --}}
                            @php
                                $simbolo = $inventarioingreso->tipomoneda === 'DOLARES' ? '$' : 'S/.';
                            @endphp
                            <div class="mt-3 text-center">
                                <p class="h6 mb-1">SUBTOTAL: {{ $simbolo }}
                                    {{ number_format($inventarioingreso->subtotal, 2) }}</p>
                                <p class="h5">TOTAL: {{ $simbolo }}
                                    {{ number_format($inventarioingreso->total, 2) }}
                                </p>
                            </div>

                            <hr class="my-4">

                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Producto') }}</th>
                                            <th>{{ __('Precio') }}</th>
                                            <th>{{ __('Cant. Pedida') }}</th>
                                            <th>{{ __('Cant. Ingresada') }}</th>
                                            <th>{{ __('Pendiente') }}</th>
                                            <th>{{ __('Subtotal') }}</th>
                                            <th>{{ __('Estado Detalle') }}</th>
                                            <th>{{ __('Guía Ingreso') }}</th>
                                            <th>{{ __('Acciones') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:13px;">
                                        @forelse($inventarioingreso->productos as $i => $prod)
                                            @php
                                                $pedido = (int) $prod->pivot->cantidad;
                                                $ingresada = (int) $prod->pivot->cantidad_ingresada;
                                                $pendiente = $pedido - $ingresada;
                                            @endphp
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td class="text-start">{{ $prod->nombre_producto }}</td>
                                                <td>
                                                    <input type="number" step="0.01"
                                                        class="form-control form-control-sm text-end"
                                                        name="items[{{ $prod->id }}][precio]"
                                                        value="{{ $prod->pivot->precio }}">
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control form-control-sm text-center"
                                                        name="items[{{ $prod->id }}][cantidad]"
                                                        value="{{ $pedido }}">
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control form-control-sm text-center"
                                                        name="items[{{ $prod->id }}][cantidad_ingresada]"
                                                        value="{{ $ingresada }}">
                                                </td>

                                                <td class="{{ $pendiente > 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ $pendiente }}
                                                </td>
                                                <td>{{ number_format($prod->pivot->subtotal, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $prod->pivot->estado === 'INGRESADO' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $prod->pivot->estado }}
                                                    </span>
                                                </td>
                                                <td>{{ $prod->pivot->guiaingresoalmacen }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar"
                                                        data-detalle-id="{{ $prod->pivot->id }}"
                                                        data-producto-nombre="{{ $prod->nombre_producto }}"
                                                        @if ($ingresada > 0) disabled @endif> Eliminar

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-muted">{{ __('Sin productos.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex mt-3 justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary mr-2">
                                    {{ __('Guardar cambios') }}
                                </button>
                                <a href="{{ route('inventarioingresos.index') }}" class="btn btn-secondary">
                                    {{ __('Volver') }}
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const botones = document.querySelectorAll('.btn-eliminar');

            botones.forEach((button, index) => {


                button.addEventListener('click', function() {

                    const detalleId = this.dataset.detalleId;
                    const productoNombre = this.dataset.productoNombre;
                    const fila = this.closest('tr');

                    const totalProductos = document.querySelectorAll('tbody tr').length;

                    if (totalProductos <= 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No permitido',
                            text: 'No puedes dejar esta orden de compra vacía'
                        });
                        return;
                    }


                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: `¿Deseas eliminar "${productoNombre}" de esta orden?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {

                        if (result.isConfirmed) {

                            Swal.fire({
                                title: 'Eliminando...',
                                text: 'Por favor espera',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const url = "{{ url('inventarioingresos/detalle') }}/" +
                                detalleId;

                            const csrfToken = document.querySelector(
                                    'meta[name="csrf-token"]')?.content ||
                                '{{ csrf_token() }}';

                            fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => {


                                    if (!response.ok) {
                                        throw new Error(
                                            'Error en la respuesta del servidor: ' +
                                            response.status);
                                    }
                                    return response.json();
                                })
                                .then(data => {

                                    if (data.success) {
                                        fila.style.transition = 'opacity 0.3s';
                                        fila.style.opacity = '0';

                                        setTimeout(() => {
                                            fila.remove();


                                            if (data.subtotal !== undefined) {
                                                const simbolo =
                                                    '{{ $simbolo }}';
                                                document.querySelector(
                                                        '.h6.mb-1')
                                                    .textContent =
                                                    `SUBTOTAL: ${simbolo} ${parseFloat(data.subtotal).toFixed(2)}`;
                                                document.querySelector('.h5')
                                                    .textContent =
                                                    `TOTAL: ${simbolo} ${parseFloat(data.total).toFixed(2)}`;

                                            }

                                            document.querySelectorAll(
                                                'tbody tr').forEach((tr,
                                                index) => {
                                                const firstTd = tr
                                                    .querySelector(
                                                        'td:first-child'
                                                    );
                                                if (firstTd) firstTd
                                                    .textContent =
                                                    index + 1;
                                            });

                                            Swal.fire({
                                                icon: 'success',
                                                title: '¡Eliminado!',
                                                text: data.message ||
                                                    'El producto ha sido eliminado correctamente',
                                                timer: 2000,
                                                showConfirmButton: false
                                            });
                                        }, 300);
                                    } else {
                                        console.error('❌ Eliminación falló:', data
                                            .message);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message ||
                                                'No se pudo eliminar el producto'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('💥 ERROR CATCH:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Ocurrió un error: ' + error
                                            .message
                                    });
                                });
                        } else {}
                    });
                });
            });
        });
    </script>
