@extends('admin.layout')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endpush

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">CREAR ORDEN DE SERVICIO</h5>
                <a href="{{ route('orden-servicio.index') }}" class="btn btn-secondary btn-sm">Volver</a>
            </div>

            <div class="card-body">
                <form action="{{ route('orden-servicio.store') }}" method="POST" id="formOrdenServicio">
                    @csrf

                    {{-- DATOS DEL PROVEEDOR --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="documento_proveedor" class="form-label">RUC / Documento</label>
                            <div class="input-group">
                                <input type="text" name="documento_proveedor" id="documento_proveedor"
                                    class="form-control" required placeholder="Ingrese RUC o DNI">
                                <button class="btn btn-success" type="button" id="buscar_proveedor_btn">Buscar</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="proveedor" class="form-label">Proveedor / Razón Social</label>
                            <input type="text" name="proveedor" id="proveedor" class="form-control" required
                                placeholder="Nombre del proveedor">
                        </div>

                        <div class="col-md-4">
                            <label for="telefono_proveedor" class="form-label">Teléfono</label>
                            <input type="text" name="telefono_proveedor" id="telefono_proveedor" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="direccion_proveedor" class="form-label">Dirección</label>
                            <input type="text" name="direccion_proveedor" id="direccion_proveedor" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción general</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="2"
                            placeholder="Ej. Servicio de mantenimiento de equipos"></textarea>
                    </div>

                    <hr>
                    <h6>Servicios / Detalles</h6>

                    {{-- TABLA DE SERVICIOS --}}
                    <table class="table table-bordered" id="tablaServicios">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario (S/)</th>
                                <th>Subtotal (S/)</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyServicios">
                            <tr>
                                <td><input type="text" class="form-control descripcion"
                                        placeholder="Descripción del servicio" required></td>
                                <td><input type="number" class="form-control cantidad" min="1" value="1"
                                        required></td>
                                <td><input type="number" class="form-control precio_unitario" step="0.01" value="0.00"
                                        required></td>
                                <td><input type="number" class="form-control subtotal" step="0.01" readonly></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm eliminar-fila">✕</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-end mb-3">
                        <button type="button" id="agregarFila" class="btn btn-outline-primary btn-sm">+ Agregar
                            Servicio</button>
                    </div>

                    {{-- TOTALES --}}
                    <div class="row justify-content-end mb-3">
                        <div class="col-md-3">
                            <label for="costo_estimado" class="form-label">Subtotal (S/)</label>
                            <input type="number" value="0.00" name="costo_estimado" id="costo_estimado"
                                class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="costo_final" class="form-label">Total con IGV (S/)</label>
                            <input type="number" step="0.01" name="costo_final" id="costo_final"
                                class="form-control" readonly>
                        </div>
                    </div>


                    <input type="hidden" name="detalles" id="inputDetalles">



                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Guardar Orden de Servicio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let bloqueandoAutocompletar = false;

        // === Calcular Subtotal de cada fila ===
        function calcularSubtotal(fila) {
            const cantidad = parseFloat(fila.find('.cantidad').val()) || 0;
            const precio = parseFloat(fila.find('.precio_unitario').val()) || 0;
            const subtotal = cantidad * precio;
            fila.find('.subtotal').val(subtotal.toFixed(2));
            calcularTotales();
        }

        // === Calcular total general con IGV ===
        function calcularTotales() {
            let subtotal = 0;
            $('.subtotal').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });
            const totalConIGV = subtotal * 1.18;
            $('#costo_estimado').val(subtotal.toFixed(2));
            $('#costo_final').val(totalConIGV.toFixed(2));
        }

        // === Eventos dinámicos ===
        $(document).on('input', '.cantidad, .precio_unitario', function() {
            calcularSubtotal($(this).closest('tr'));
        });

        $('#agregarFila').click(function() {
            const nuevaFila = `
        <tr>
            <td><input type="text" class="form-control descripcion" placeholder="Descripción del servicio" required></td>
            <td><input type="number" class="form-control cantidad" min="1" value="1" required></td>
            <td><input type="number" class="form-control precio_unitario" step="0.01" value="0.00" required></td>
            <td><input type="number" class="form-control subtotal" step="0.01" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm eliminar-fila">✕</button></td>
        </tr>`;
            $('#tbodyServicios').append(nuevaFila);
        });

        $(document).on('click', '.eliminar-fila', function() {
            $(this).closest('tr').remove();
            calcularTotales();
        });


        $('#formOrdenServicio').on('submit', function(e) {
            e.preventDefault();
            let errores = false;
            let mensajeError = "";

            $('#tbodyServicios tr').each(function(index) {
                let descripcion = $(this).find('.descripcion').val().trim();
                let cantidad = $(this).find('.cantidad').val();
                let precio = $(this).find('.precio_unitario').val();
                let subtotal = $(this).find('.subtotal').val();

                if (!descripcion || cantidad <= 0 || precio <= 0 || subtotal <= 0) {
                    errores = true;
                    mensajeError = `La fila ${index + 1} contiene datos incompletos o inválidos.`;
                    return false;
                }
            });

            if (errores) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Datos incompletos',
                    text: mensajeError,
                    confirmButtonText: 'Entendido',
                });
                return;
            }

            const detalles = [];
            $('#tbodyServicios tr').each(function() {
                detalles.push({
                    descripcion: $(this).find('.descripcion').val(),
                    cantidad: $(this).find('.cantidad').val(),
                    precio_unitario: $(this).find('.precio_unitario').val(),
                    subtotal: $(this).find('.subtotal').val()
                });
            });

            $('#inputDetalles').val(JSON.stringify({
                detalles
            }));

            this.submit();
        });


        // === Buscar proveedor manualmente ===
        $('#buscar_proveedor_btn').click(function() {
            const documento = $('#documento_proveedor').val().trim();
            if (documento.length < 7) return;

            bloqueandoAutocompletar = true;

            $.ajax({
                url: "{{ route('buscar.documento') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    documento: documento,
                    tipo_documento: documento.length === 8 ? 'dni' : 'ruc'
                },
                success: function(response) {
                    if (response.razonSocial) {
                        $('#proveedor').val(response.razonSocial);
                    } else if (response.nombres) {
                        $('#proveedor').val(
                            `${response.nombres} ${response.apellidoPaterno} ${response.apellidoMaterno}`
                        );
                    }

                    // 🔹 NO se limpia el RUC, solo completamos si falta
                    if (!$('#documento_proveedor').val()) {
                        $('#documento_proveedor').val(documento);
                    }

                    // Si no existe, crear automáticamente (opcional)
                    if (!response.id) {
                        console.log(
                            "Proveedor no encontrado, se puede crear uno nuevo aquí si lo deseas");
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se encontró el proveedor.', 'error');
                },
                complete: function() {
                    setTimeout(() => bloqueandoAutocompletar = false, 500);
                }
            });
        });

        // === Autocompletado (por nombre o RUC) ===
        $('#proveedor, #documento_proveedor').on('input', function() {
            if (bloqueandoAutocompletar) return;

            let valor = $(this).val().trim();
            if (valor.length < 5) return;

            let ruta = this.id === 'proveedor' ?
                "{{ route('autocomp.proveedor') }}" :
                "{{ route('autocompbyruc.proveedor') }}";

            $.ajax({
                url: ruta,
                method: 'GET',
                data: {
                    search_string: valor
                },
                success: function(response) {
                    if (!response) return;
                    $('#documento_proveedor').val(response.ruc || $('#documento_proveedor').val());
                    $('#direccion_proveedor').val(response.direccion || '');
                    $('#telefono_proveedor').val(response.telefono || '');
                    $('#proveedor').val(response.razon_social || response.nombre || '');
                }
            });
        });

        // === Validación de fechas ===
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');
            fechaInicio.addEventListener('change', function() {
                fechaFin.min = this.value;
                if (fechaFin.value && fechaFin.value < this.value) {
                    fechaFin.value = this.value;
                }
            });
        });
    </script>
@endsection
