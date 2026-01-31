@extends('admin.layout')

@section('content')
    <div class="container py-4">
        @php
            use Carbon\Carbon;
            $hoy = Carbon::now('America/Lima')->format('Y-m-d\TH:i');
        @endphp
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-semibold text-secondary mb-0">
                        <i class="bi bi-calendar3 me-2 text-muted"></i>Molienda
                    </h4>
                    <button class="btn btn-primary" id="btnNuevaMolienda">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Molienda
                    </button>
                </div>
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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-sm align-middle">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">CODIGO</th>
                                <th class="text-center">LOTE</th>
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
                                    <td class="text-center">{{ $proceso->circuito->descripcion ?? '' }}</td>
                                    <td class="text-center">{{ intval($proceso->peso_total) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            @if ($proceso->estado == 'P')
                                                <button class="btn btn-primary btnVer" data-id="{{ $proceso->id }}">
                                                    Ver
                                                </button>
                                                <button class="btn btn-info btnRegistrarTiempo"
                                                    data-id="{{ $proceso->id }}"
                                                    data-nombre="{{ $proceso->lote->nombre ?? '' }}"
                                                    data-codigo="{{ $proceso->codigo }}">
                                                    Registrar tiempo
                                                </button>
                                                <a href="{{ route('molienda.liquidar', $proceso->id) }}"
                                                    class="btn btn-warning btn-sm px-3 py-2 mx-1">
                                                    Liquidar
                                                </a>
                                                @if (!$proceso->liquidacion)
                                                    <form action="{{ route('procesos.edit', $proceso->id) }}"
                                                        method="POST" onsubmit="return confirmarEliminacion(event)"
                                                        class="mx-1">
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
                                                <button onclick="imprimirMolienda({{ $proceso->id }})"
                                                    class="btn btn-success">
                                                    IMPRIMIR
                                                </button>
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
        @include('programaciones.molienda.modals.create')
        @include('programaciones.molienda.modals.tiempos')

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/adminlte/js/adminlte.min.js"></script>
    <script src="/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="/plugins/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        @push('scripts')
    </script>
    <script>
        let calendar;

        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('modalMolienda'));
            const modalTiempo = new bootstrap.Modal(document.getElementById('modalTiempo'));
            const tablaBody = $('#tablaPesos tbody');
            const tablaBodyTiempos = $('#tbodyTiempos tbody');

            $('#btnNuevaMolienda').click(() => {
                $('#formMolienda')[0].reset();
                $('#formPesoManual')[0].reset();
                $('#programacion_id').val('');
                $('#peso_total').val('');
                $('#lote_id').prop('disabled', false);
                $('#otra_balanza').hide();

                tablaBody.html(
                    '<tr><td colspan="8" class="text-muted">Seleccione un lote...</td></tr>');
                modal.show();
            });

            $('#btnNuevaTiempo').click(() => {
                $('#formTiempo')[0].reset();
                $('#formPesoManual')[0].reset();
                $('#programacion_id').val('');
                $('#peso_total').val('');
                $('#lote_id').prop('disabled', false);
                $('#otra_balanza').hide();

                tablaBody.html(
                    '<tr><td colspan="8" class="text-muted">Seleccione un lote...</td></tr>');
                modalTiempo.show();
            });

            $('#btnCancelar').click(() => {
                modal.hide();
            });

            $(document).on('click', '.btnVer', function() {
                const procesoId = $(this).data('id');
                $('#proceso_id').val(procesoId);

                axios.get(`/molienda/${procesoId}/pesos`)
                    .then(res => {
                        const proceso = res.data.proceso ?? {};
                        const pesos = res.data.pesos ?? [];

                        const loteNombre = proceso.lote_id ?
                            $("#lote_id option[value='" + proceso.lote_id + "']").text() : '';
                        $('#lote_id_input').val(loteNombre);
                        $('#lote_id_real').val(proceso.lote_id ?? '');
                        $('#lote_id_input').prop('disabled', true);
                        $('#lote_id').hide();

                        $('#circuito').val(proceso.circuito_id ?? '');

                        const tablaPesosBody = $('#tablaPesos tbody');
                        const tablaOtrasBody = $('#tablaOtrasBalanzas tbody');
                        tablaPesosBody.html('');
                        tablaOtrasBody.html('');

                        pesos.forEach(peso => {
                            if (peso.tipo === 'balanza') {
                                tablaPesosBody.append(`
                        <tr>
                            <td><input type="checkbox" class="peso-check" value="${peso.NroSalida}" data-neto="${peso.Neto || 0}" checked></td>
                            <td>${peso.NroSalida}</td>
                            <td>${peso.Fechas ?? '-'}</td>
                            <td>${peso.Bruto ?? '-'}</td>
                            <td>${peso.Tara ?? '-'}</td>
                            <td>${peso.Neto ?? '-'}</td>
                            <td>${peso.Horai ?? '-'}</td>
                            <td>${peso.Horas ?? '-'}</td>
                        </tr>
                    `);
                            } else if (peso.tipo === 'manual') {
                                tablaOtrasBody.append(`
                        <tr>
                            <td>${peso.id}</td>
                            <td>${peso.fechai ?? '-'}</td>
                            <td>${peso.fechas ?? '-'}</td>
                            <td>${peso.placa ?? '-'}</td>
                            <td>${peso.conductor ?? '-'}</td>
                            <td>${peso.bruto ?? '-'}</td>
                            <td>${peso.tara ?? '-'}</td>
                            <td>${peso.neto ?? '-'}</td>
                            <td>${peso.balanza ?? '-'}</td>
                            <td>${peso.producto ?? '-'}</td>
                            <td>${peso.estado_nombre ?? '-'}</td>
                            <td>
                                <button class="btn btn-danger btn-sm btnEliminarManual" data-id="${peso.id}">Eliminar</button>
                            </td>
                        </tr>
                    `);
                            }
                        });

                        calcularTotal();
                        modal.show();
                    })
                    .catch(err => {
                        console.error('Error al cargar pesos:', err);
                        Swal.fire('Error', 'No se pudieron cargar los pesos del proceso', 'error');
                    });
            });


            $(document).on('change', '.peso-check', function() {
                let total = 0;
                $('.peso-check:checked').each(function() {
                    total += parseFloat($(this).data('neto') || 0);
                });
                $('#peso_total').val(total.toFixed(2));
            });

            $('#lote_id_input').on('input', function() {
                let filtro = $(this).val().toLowerCase().trim();

                if (filtro.length === 0) {
                    $('#lote_id').hide();
                    return;
                }

                let coincidencias = 0;

                $('#lote_id option').each(function() {
                    let texto = $(this).text().toLowerCase();

                    if (texto.includes(filtro)) {
                        $(this).show();
                        coincidencias++;
                    } else {
                        $(this).hide();
                    }
                });

                if (coincidencias > 0) {
                    $('#lote_id').show();
                } else {
                    $('#lote_id').hide();
                }
            });

            $('#lote_id').on('change', function() {
                const loteId = $(this).val();
                const procesoId = $('#programacion_id').val();

                const loteTexto = $("#lote_id option:selected").text().trim();
                $('#lote_id_input').val(loteTexto);
                $('#lote_id_real').val(loteId);
                $('#lote_id').hide();

                if (!loteId) {
                    tablaBody.html(
                        '<tr><td colspan="8" class="text-muted">Seleccione un lote...</td></tr>');
                    return;
                }

                if (!procesoId) {
                    axios.get(`/lotes/${loteId}/pesos`)
                        .then(res => {
                            const pesos = res.data;
                            tablaBody.html('');
                            pesos.forEach(peso => {
                                tablaBody.append(`
                        <tr>
                            <td><input type="checkbox" class="peso-check" value="${peso.NroSalida}" data-neto="${peso.Neto}"></td>
                            <td>${peso.NroSalida}</td>
                            <td>${peso.Fechas ?? '-'}</td>
                            <td>${peso.Bruto ?? '-'}</td>
                            <td>${peso.Tara ?? '-'}</td>
                            <td>${peso.Neto ?? '-'}</td>
                            <td>${peso.Horai ?? '-'}</td>
                            <td>${peso.Horas ?? '-'}</td>
                        </tr>
                    `);
                            });
                            calcularTotal();
                        })
                        .catch(() => {
                            tablaBody.html(
                                '<tr><td colspan="8" class="text-danger">Error al cargar los pesos.</td></tr>'
                            );
                        });
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#lote_id, #lote_id_input').length) {
                    $('#lote_id').hide();
                }
            });

            $('#btnGuardar').click(function() {
                const procesoId = $('#programacion_id').val();
                const data = {
                    lote_id: $('#lote_id_real').val(),
                    circuito: $('#circuito').val(),
                    pesos: $('.peso-check:checked').map(function() {
                        return this.value;
                    }).get(),
                    peso_total: $('#peso_total').val()
                };

                if (!data.lote_id || !data.circuito) {
                    return Swal.fire('Campos incompletos', 'Por favor complete todos los campos.',
                        'warning');
                }

                if (procesoId && data.pesos.length === 0) {
                    Swal.fire({
                        title: '¿Eliminar programación?',
                        text: 'No hay pesos seleccionados. ¿Desea eliminar esta programación?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete(`/molienda/${procesoId}`)
                                .then(() => {
                                    Swal.fire({
                                        title: 'Eliminado',
                                        text: 'La programación fue eliminada correctamente.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {

                                        modal.hide();
                                        location.reload();
                                        calendar.refetchEvents();

                                    });
                                })
                                .catch(() => {
                                    Swal.fire('Error',
                                        'No se pudo eliminar la programación.',
                                        'error');
                                });
                        }
                    });
                    return;
                }

                if (!procesoId && data.pesos.length === 0) {
                    return Swal.fire('Sin pesos seleccionados', 'Debe seleccionar al menos un peso.',
                        'warning');
                }


                const url = procesoId ? `/molienda/${procesoId}` : '/molienda';
                const method = procesoId ? 'put' : 'post';

                axios({
                        method,
                        url,
                        data
                    })
                    .then(res => {
                        const deleted = res.data.deleted === true || res.data.deleted === 'true';

                        if (deleted) {
                            Swal.fire({
                                title: 'Eliminado',
                                text: 'La programación fue eliminada correctamente.',
                                icon: 'info',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                modal.hide();
                                location.reload();

                            });
                        } else {
                            const msg = procesoId ?
                                'Programación actualizada correctamente.' :
                                'Programación registrada correctamente.';
                            Swal.fire({
                                title: 'Éxito',
                                text: msg,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                modal.hide();
                                location.reload();

                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Hubo un problema al guardar la programación.', 'error');
                    });
            });

            $(document).on('click', '.btnNuevaTiempo', function() {
                const procesoId = $(this).data('id');
                $('#programacion_id').val(procesoId);

                axios.get(`/molienda/${procesoId}/pesos`)
                    .then(res => {

                        const p = res.data.proceso;
                        const pesosTodos = res.data.pesos;

                        const pesosBalanza = pesosTodos.filter(peso => peso.tipo === 'balanza');
                        const pesosOtrasBalanzas = pesosTodos.filter(peso => peso.tipo ===
                            'manual');


                        if (p.lote) {
                            $('#lote_id').val(p.lote.id).trigger('change');
                        }
                        $('#lote_id').prop('disabled', true);

                        $('#circuito').val(p.circuito).trigger('change');


                        const tablaBody = $('#tablaPesos tbody');
                        tablaBody.html('');

                        if (pesosBalanza.length === 0) {
                            tablaBody.html(
                                '<tr><td colspan="8" class="text-muted">Aún no se han registrado horarios</td></tr>'
                            );
                        } else {
                            pesosBalanza.forEach(peso => {

                                const disabled = (peso.estado_id === 3 || peso.estado_id ===
                                    4) ? 'disabled' : '';

                                tablaBody.append(`
                        <tr>
                            <td>
                                <input type="checkbox" 
                                    class="peso-check" 
                                    value="${peso.NroSalida}" 
                                    data-neto="${peso.Neto}" 
                                    checked ${disabled}>
                            </td>
                            <td>${peso.NroSalida}</td>
                            <td>${peso.Fechas ?? '-'}</td>
                            <td>${peso.Bruto ?? '-'}</td>
                            <td>${peso.Tara ?? '-'}</td>
                            <td class="fw-bold">${peso.Neto ?? '-'}</td>
                            <td>${peso.Horai ?? '-'}</td>
                            <td>${peso.Horas ?? '-'}</td>
                        </tr>
                    `);
                            });
                        }

                        cargarTablaOtrasBalanzas(pesosOtrasBalanzas);

                        calcularTotal();

                        modalTiempo.show();
                    })
                    .catch((error) => {
                        console.error('Error al cargar datos:', error);
                        Swal.fire('Error', 'No se pudieron cargar los pesos del proceso', 'error');
                    });
            });

            function cargarTablaOtrasBalanzas(pesosOtrasBalanzas) {
                const tablaOtrasBody = $('#tablaOtrasBalanzas tbody');
                tablaOtrasBody.html('');

                if (pesosOtrasBalanzas.length === 0) {
                    tablaOtrasBody.html(
                        '<tr><td colspan="9" class="text-muted">No hay pesos de otras balanzas registrados</td></tr>'
                    );
                } else {
                    pesosOtrasBalanzas.forEach(peso => {
                        const estadoBadge = peso.estado_nombre ?
                            `<span class="badge bg-info">${peso.estado_nombre}</span>` :
                            '<span class="badge bg-secondary">-</span>';

                        let botonEliminar = '';
                        if (peso.estado_id === 1) {
                            botonEliminar = `
                    <button class="btn btn-sm btn-danger btn-eliminar-otra-balanza" 
                            data-id="${peso.NroSalida}"
                            title="Eliminar peso">
                        <i class="fa fa-trash"></i>
                    </button>
                `;
                        } else {
                            botonEliminar = `
                    <button class="btn btn-sm btn-secondary" 
                            disabled 
                            title="No se puede eliminar. Estado: ${peso.estado_nombre}">
                        <i class="fa fa-lock"></i>
                    </button>
                `;
                        }

                        tablaOtrasBody.append(`
                <tr>
                    <td>${peso.id}</td>
                  
                    <td>${peso.placa ?? '-'}</td>
                    <td>${peso.conductor ?? '-'}</td>
                    <td>${peso.bruto ?? '-'}</td>
                    <td>${peso.tara ?? '-'}</td>
                    <td class="fw-bold text-success">${peso.neto ?? '-'}</td>
                    <td>${peso.balanza ?? '-'}</td>
                    <td>${peso.producto ?? '-'}</td>
                    <td>${estadoBadge}</td>
                    <td>${botonEliminar}</td>

                </tr>
            `);
                    });
                }
            }

            $(document).on('click', '#btnAgregarPesoManual', function() {
                const procesoId = $('#proceso_id').val();

                if (!procesoId) {
                    return Swal.fire('Atención', 'Primero debes crear o seleccionar una programación.',
                        'warning');
                }

                const form = document.getElementById('formPesoManual');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                Swal.fire({
                    title: '¿Agregar este peso?',
                    html: `
            <div class="text-start">
                <p><strong>Neto:</strong> ${data.neto} kg</p>
                <p><strong>Balanza:</strong> ${data.balanza || 'N/A'}</p>
                <p><strong>Placa:</strong> ${data.placa || 'N/A'}</p>
            </div>
        `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, agregar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`/otras-balanza/${procesoId}/molienda`, data)
                            .then(response => {
                                Swal.fire({
                                    title: 'Éxito',
                                    text: 'Peso de otra balanza agregado correctamente.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    form.reset();
                                    axios.get(`/molienda/${procesoId}/pesos`)
                                        .then(res => {
                                            const pesosTodos = res.data.pesos;
                                            const pesosBalanza = pesosTodos
                                                .filter(
                                                    p => p.tipo === 'balanza');
                                            const pesosOtrasBalanzas =
                                                pesosTodos
                                                .filter(p => p.tipo ===
                                                    'manual');

                                            const tablaBody = $(
                                                '#tablaPesos tbody');
                                            tablaBody.html('');

                                            if (pesosBalanza.length === 0) {
                                                tablaBody.html(
                                                    '<tr><td colspan="8" class="text-muted">No hay pesos disponibles</td></tr>'
                                                );
                                            } else {
                                                pesosBalanza.forEach(peso => {
                                                    const disabled = (
                                                            peso
                                                            .estado_id ===
                                                            3 || peso
                                                            .estado_id ===
                                                            4
                                                        ) ? 'disabled' :
                                                        '';
                                                    tablaBody.append(`
                                            <tr>
                                                <td><input type="checkbox" class="peso-check" value="${peso.NroSalida}" data-neto="${peso.Neto}" checked ${disabled}></td>
                                                <td>${peso.NroSalida}</td>
                                                <td>${peso.Fechas ?? '-'}</td>
                                                <td>${peso.Bruto ?? '-'}</td>
                                                <td>${peso.Tara ?? '-'}</td>
                                                <td class="fw-bold">${peso.Neto ?? '-'}</td>
                                                <td>${peso.Horai ?? '-'}</td>
                                                <td>${peso.Horas ?? '-'}</td>
                                            </tr>
                                        `);
                                                });
                                            }

                                            cargarTablaOtrasBalanzas(
                                                pesosOtrasBalanzas);

                                            calcularTotal();
                                        })
                                        .catch(err => {
                                            console.error(
                                                "Error al recargar pesos:",
                                                err);
                                        });
                                });
                            })
                            .catch(error => {
                                console.error('Error al guardar:', error);
                                const mensaje = error.response?.data?.message ||
                                    'No se pudo guardar el peso manual.';
                                Swal.fire('Error', mensaje, 'error');
                            });
                    }
                });
            });


            function cargarTablaTiempos(id) {
                $.get(`/molienda/${id}/tiempos`, function(res) {

                    let tbody = $("#tbodyTiempos");
                    tbody.empty();

                    let tonIniciales = res.tonelaje_inicial ?? 0;
                    let tonMolidas = 0;

                    if (res.tiempos.length === 0) {

                        $("#ton_iniciales").text(tonIniciales);
                        $("#ton_molidas").text(0);
                        $("#ton_restantes").text(tonIniciales);

                        tbody.append(`
                <tr><td colspan="5" class="text-muted">Sin tiempos registrados</td></tr>
            `);

                        return;
                    }

                    res.tiempos.forEach(t => {

                        tonMolidas += parseFloat(t.tonelaje);

                        tbody.append(`
                <tr>
                    <td>${t.fecha_inicio}</td>
                    <td>${t.hora_inicio}</td>
                    <td>${t.fecha_fin ?? '-'}</td>
                    <td>${t.hora_fin}</td>
                    <td>${t.tonelaje}</td>
                </tr>
            `);
                    });

                    let tonRestantes = tonIniciales - tonMolidas;

                    $("#ton_iniciales").text(tonIniciales);
                    $("#ton_molidas").text(tonMolidas.toFixed(2));
                    $("#ton_restantes").text(tonRestantes.toFixed(2));
                });
            }

            $(document).on('click', '.btnRegistrarTiempo', function() {
                const procesoId = $(this).data('id');
                const nombre = $(this).data('nombre');
                const codigo = $(this).data('codigo');

                $("#proceso_id").val(procesoId);
                $('#modalTiempoLabel').text(`Registrar tiempos para: ${nombre} - ${codigo}`);

                cargarTablaTiempos(procesoId);

                modalTiempo.show();
            });

            $("#registrarTiempoBtn").on("click", function() {
                const procesoId = $("#proceso_id").val();

                if (!procesoId) {
                    Swal.fire('Error', 'No se ha seleccionado un proceso', 'error');
                    return;
                }

                const fechaInicio = $("#fecha_inicio").val();
                const horaInicio = $("#hora_inicio").val();
                const fechaFin = $("#fecha_fin").val();
                const horaFin = $("#hora_fin").val();
                const tonelaje = $("#tonelaje").val();

                if (!fechaInicio || !horaInicio || !fechaFin || !horaFin || !tonelaje) {
                    Swal.fire('Error', 'Por favor complete todos los campos', 'warning');
                    return;
                }

                const data = {
                    _token: $("meta[name='csrf-token']").attr("content"),
                    proceso_id: procesoId,
                    fecha_inicio: fechaInicio,
                    hora_inicio: horaInicio,
                    fecha_fin: fechaFin,
                    hora_fin: horaFin,
                    tonelaje: tonelaje
                };

                $.ajax({
                    url: `/molienda/${procesoId}/guardar-tiempo`,
                    type: "POST",
                    data: data,
                    success: function(res) {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'Tiempo registrado correctamente',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        $("#formTiempo")[0].reset();
                        $("#fecha_inicio").val("{{ $hoy }}");
                        $("#fecha_fin").val("{{ $hoy }}");

                        cargarTablaTiempos(procesoId);
                    },
                    error: function(error) {
                        console.error("Error al guardar tiempo:", error);
                        Swal.fire({
                            title: 'Error',
                            text: error.responseJSON?.message ||
                                'No se pudo guardar el tiempo',
                            icon: 'error'
                        });
                    }
                });
            });

            $(document).on('click', '.btn-eliminar-otra-balanza', function() {
                const pesoId = $(this).data('id');
                const procesoId = $('#programacion_id').val();

                Swal.fire({
                    title: '¿Eliminar este peso?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`/otras-balanza/${pesoId}`)
                            .then(() => {
                                Swal.fire({
                                    title: 'Eliminado',
                                    text: 'Peso eliminado correctamente',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                axios.get(`/molienda/${procesoId}/pesos`)
                                    .then(res => {
                                        const pesosTodos = res.data.pesos;
                                        const pesosOtrasBalanzas = pesosTodos.filter(
                                            p => p
                                            .tipo === 'manual');
                                        cargarTablaOtrasBalanzas(pesosOtrasBalanzas);
                                        calcularTotal();
                                    });
                            })
                            .catch((error) => {
                                console.error('Error al eliminar:', error);
                                Swal.fire('Error', 'No se pudo eliminar el peso', 'error');
                            });
                    }
                });
            });
            $(document).on('change', '.peso-check', function() {
                calcularTotal();
            });

            function calcularTotal() {
                let total = 0;
                $('.peso-check:checked').each(function() {
                    total += parseFloat($(this).data('neto') || 0);
                });

                $('#tablaOtrasBalanzas tbody tr').each(function() {
                    const neto = parseFloat($(this).find('td:nth-child(8)').text()) || 0;
                    total += neto;
                });
                $('#peso_total').val(total.toFixed(2));
            }
        });

        $(document).on('input', '#formPesoManual input[name="bruto"], #formPesoManual input[name="tara"]', function() {
            const bruto = parseFloat($('#formPesoManual input[name="bruto"]').val()) || 0;
            const tara = parseFloat($('#formPesoManual input[name="tara"]').val()) || 0;
            const neto = bruto - tara;
            $('#formPesoManual input[name="neto"]').val(neto >= 0 ? neto.toFixed(2) : 0);
        });

        let filtroCircuito = "todos";
        let filtroEstado = "todos";

        function aplicarFiltros() {
            calendar.getEvents().forEach(e => {
                const c = e.extendedProps.circuito;
                const s = e.extendedProps.estado;

                const visibleCircuito = (filtroCircuito === "todos" || filtroCircuito === c);
                const visibleEstado = (filtroEstado === "todos" || filtroEstado === s);

                if (visibleCircuito && visibleEstado) {
                    e.setProp('display', 'auto');
                } else {
                    e.setProp('display', 'none');
                }
            });
        }

        function recolorearPorCircuito() {
            calendar.getEvents().forEach(e => {
                const circuito = e.extendedProps.circuito;
                let color = '#4dabf7';

                switch (circuito) {
                    case 'A':
                        color = '#AADC77';
                        break; // azul
                    case 'B':
                        color = '#F9E6AE';
                        break; // naranja
                    case 'C':
                        color = '#20c997';
                        break; // verde
                    default:
                        color = '#4dabf7';
                }

                e.setProp('color', color);
                e.setProp('textColor', '#000');
            });
        }

        $('.filtro-circuito').on('click', function() {
            filtroCircuito = $(this).data('circuito');
            aplicarFiltros();
        });

        $('#filtroEstado').on('change', function() {
            filtroEstado = $(this).val();
            recolorearPorEstado();
            aplicarFiltros();
        });

        function imprimirMolienda(moliendaId) {
            const iframe = document.getElementById('printFrame');
            const url = "{{ route('molienda.imprimir', ':id') }}".replace(':id', moliendaId);
            iframe.src = url;
            iframe.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            };
        }
    </script>
<iframe id="printFrame" style="display:none;"></iframe>@endpush

@endsection
