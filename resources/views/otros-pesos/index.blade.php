@extends('admin.layout')

@section('content')
    <br>
    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <h4 class="mb-0 ">
                            <i class="bi bi-clipboard-data me-2"></i>
                            PESOS DE OTRAS BALANZAS
                        </h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" id="btnAbrirModal">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Peso
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    use Carbon\Carbon;
                    $hoy = Carbon::now('America/Lima')->format('Y-m-d');
                @endphp

                <form id="filtros" method="POST" action="{{ route('pesos') }}" class="mb-4">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold"><i class="bi bi-calendar-date text-primary"></i> Fecha
                                Desde</label>
                            <input type="date" name="desde" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold"><i class="bi bi-calendar-check text-primary"></i> Fecha
                                Hasta</label>
                            <input type="date" name="hasta" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Razón Social</label>
                            <input type="text" name="razon" class="form-control" placeholder="Buscar Razón Social">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Origen</label>
                            <input type="text" name="origen" class="form-control" placeholder="Buscar Origen">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Destino</label>
                            <input type="text" name="destino" class="form-control" placeholder="Buscar Destino">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="estado_id" class="form-control form-select-sm estado-select w-150">
                                <option value="">TODOS</option>
                                @foreach ($estados as $estadoOpt)
                                    <option value="{{ $estadoOpt->id }}">
                                        {{ $estadoOpt->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-semibold">Ticket</label>
                            <input type="text" name="id" class="form-control" placeholder="Buscar">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Producto</label>
                            <input type="text" name="producto" class="form-control" value="MINERAL A GRANEL"
                                placeholder="Buscar Producto">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Conductor</label>
                            <input type="text" name="conductor" class="form-control" placeholder="Buscar Conductor">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-semibold">Placa</label>
                            <input type="text" name="placa" class="form-control" placeholder="Buscar">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Observación</label>
                            <input type="text" name="observacion" class="form-control" placeholder="Buscar Observación">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Lote</label>
                            <input type="text" name="lote" class="form-control" placeholder="Buscar lote">
                        </div>
                        <div class="col-md-1 d-flex align-items-end gap-2">
                            <a href="{{ route('pesos.index') }}" class="btn btn-light flex-fill"><i
                                    class="bi bi-funnel-fill me-1"></i> Limpiar</a>
                        </div>
                    </div>
                </form>
                <hr class="my-4">
                <div id="tabla-container">
                </div>

            </div>
        </div>
    </div>
    @include('otros-pesos.modals.create')
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const btn = document.getElementById("btnAbrirModal");
            const modalEl = document.getElementById("modalPesoOtraBalanza");

            const modal = new bootstrap.Modal(modalEl);

            btn.addEventListener("click", () => {
                modal.show();
            });
        });

        document.addEventListener("DOMContentLoaded", () => {
            const filtrosForm = document.getElementById("filtros");
            const tablaContainer = document.querySelector("#tabla-container");

            async function cargarTabla(url = "{{ route('otrasBalanza') }}") {
                const formData = new FormData(filtrosForm);
                try {
                    const res = await fetch(url, {
                        method: "POST",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: formData
                    });
                    const html = await res.text();
                    tablaContainer.innerHTML = html;
                } catch (err) {
                    console.error("Error al recargar tabla:", err);
                }
            }

            filtrosForm.querySelectorAll("input, select").forEach(input => {
                input.addEventListener("input", () => cargarTabla());
            });

            filtrosForm.addEventListener("submit", e => {
                e.preventDefault();
                cargarTabla();
            });

            document.getElementById('btnCerrarModal')?.addEventListener('click', () => {
                $('#modalPesoOtraBalanza').modal('close');
            });
            document.getElementById('btnGuardar').addEventListener('click', async () => {
                const loteId = document.getElementById('lote_id_real').value;

                if (!loteId) {
                    return Swal.fire(
                        'Lote inválido',
                        'Debes seleccionar un lote de la lista',
                        'warning'
                    );
                }

                const form = document.getElementById('formPesoManual');
                const formData = new FormData(form);

                try {
                    const res = await fetch("{{ route('otrasBalanza.guardar') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (data.success) {
                        Swal.fire('Guardado', 'Peso registrado correctamente', 'success');
                        form.reset();
                        document.getElementById('lote_id_real').value = '';
                    }

                } catch (e) {
                    Swal.fire('Error', 'No se pudo guardar', 'error');
                }
            });



            $(document).on('input', '#formPesoManual input[name="bruto"], #formPesoManual input[name="tara"]',
                function() {
                    const bruto = parseFloat($('#formPesoManual input[name="bruto"]').val()) || 0;
                    const tara = parseFloat($('#formPesoManual input[name="tara"]').val()) || 0;
                    const neto = bruto - tara;
                    $('#formPesoManual input[name="neto"]').val(neto >= 0 ? neto.toFixed(2) : 0);
                });

            document.addEventListener("click", async e => {
                const link = e.target.closest(".pagination a");
                if (link) {
                    e.preventDefault();
                    return cargarTabla(link.href);
                }
            });

            document.addEventListener("click", async e => {
                const btn = e.target.closest('.guardar-btn');
                if (!btn) return;

                const pesoId = btn.dataset.peso;
                const estadoSelect = document.querySelector(`.estado-select[data-peso="${pesoId}"]`);
                const inputLote = document.querySelector(`.lote-input[data-peso="${pesoId}"]`);
                const selectLote = inputLote.closest('.combo').querySelector('.lote-select');
                const estadoId = estadoSelect?.value;
                const loteNombre = inputLote?.value.trim();
                const option = Array.from(selectLote.options)
                    .find(opt => opt.text.trim().toLowerCase() === loteNombre.toLowerCase());

                const loteId = option ? option.value : null;

                if (!estadoId || !loteNombre) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos incompletos',
                        text: 'Por favor selecciona un estado y escribe un lote válido.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                if (!loteId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lote no encontrado',
                        text: 'El lote ingresado no coincide con ninguno de la lista.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                try {
                    const res = await fetch(`{{ route('pesos.update', '') }}/${pesoId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            estadoId,
                            loteId
                        })
                    });

                    const result = await res.json();

                    if (res.ok && result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Guardado correctamente',
                            text: 'El registro se actualizó exitosamente.',
                            timer: 1300,
                            showConfirmButton: false
                        });

                        const fila = btn.closest('tr');
                        fila.classList.add('table-success');

                        estadoSelect.disabled = true;
                        inputLote.disabled = true;
                        btn.disabled = true;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-secondary');
                        btn.innerHTML = '<i class="bi bi-check-circle"></i> Guardado';

                    } else {
                        throw new Error(result.message || 'Error desconocido');
                    }

                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message || 'Error al guardar los datos.',
                        confirmButtonColor: '#d33'
                    });
                }
            });

            $(document).on('input', '#lote_id_input', function() {
                const filtro = $(this).val().toLowerCase().trim();
                const select = $('#lote_id');
                let visibles = 0;

                if (!filtro) {
                    select.hide();
                    return;
                }

                select.find('option').each(function() {
                    const match = $(this).text().toLowerCase().includes(filtro);
                    $(this).toggle(match);
                    if (match) visibles++;
                });

                visibles > 0 ? select.show() : select.hide();
            });

            $(document).on('change', '#lote_id', function() {
                const option = $(this).find('option:selected');

                $('#lote_id_input').val(option.text().trim());
                $('#lote_id_real').val(option.val());
                $(this).hide();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#lote_id, #lote_id_input').length) {
                    $('#lote_id').hide();
                }
            });


            $(document).ready(function() {
                $(document).on('input', '.lote-input', function() {
                    const input = $(this);
                    const search = input.val().toLowerCase();
                    const select = input.siblings('.lote-select');

                    if (search.length > 0) {
                        select.show();
                        select.find('option').each(function() {
                            const optionText = $(this).text().toLowerCase();
                            $(this).toggle(optionText.includes(search));
                        });
                    } else {
                        select.hide();
                    }
                });

                $(document).on('change', '.lote-select', function() {
                    const select = $(this);
                    const option = select.find('option:selected');
                    const input = select.siblings('.lote-input');
                    const hidden = select.siblings('.lote-hidden');

                    input.val(option.text().trim());
                    hidden.val(option.val());

                    const codigoBase = option.data('codigo');
                    if (codigoBase) {
                        const nuevoNumero = parseInt(option.data('lotes') || 0) + 1;
                        const codigoGenerado = `${codigoBase}-${nuevoNumero}`;
                        input.closest('tr').find('#codigo_lote').val(codigoGenerado);
                    }

                    select.hide();
                });
            });

            cargarTabla();

            document.addEventListener("click", (e) => {
                if (e.target.id === "check-all") {
                    document.querySelectorAll(".check-item")
                        .forEach(c => c.checked = e.target.checked);
                }
            });

            document.addEventListener("click", async e => {

                if (e.target.id !== "btn-asignar-masivo") return;

                const seleccionados = [...document.querySelectorAll(".check-item:checked")]
                    .map(c => c.value);

                const estado = document.getElementById("estado-masivo").value;
                const lote = document.getElementById("lote-masivo-id").value;

                if (seleccionados.length === 0) {
                    return Swal.fire("Sin selección", "Selecciona al menos un registro.", "warning");
                }

                if (!estado || !lote) {
                    return Swal.fire("Campos incompletos", "Debe elegir lote y estado.", "warning");
                }

                try {
                    const res = await fetch("{{ route('pesos.massUpdate') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            pesos: seleccionados,
                            estado,
                            lote
                        })
                    });

                    const data = await res.json();

                    Swal.fire("Éxito", data.message, "success");

                    cargarTabla();

                } catch (err) {
                    Swal.fire("Error", "Ocurrió un error inesperado", "error");
                }
            });

            $(document).on("input", "#lote-masivo-input", function() {
                let filtro = $(this).val().toLowerCase();

                $("#lote-masivo-select").show();

                $("#lote-masivo-select option").each(function() {
                    let texto = $(this).text().toLowerCase();
                    $(this).toggle(texto.includes(filtro));
                });
            });

            $(document).on("focus", "#lote-masivo-input", function() {
                $("#lote-masivo-select").show();
            });

            $(document).on("click", "#lote-masivo-select option", function() {
                let nombre = $(this).text();
                let id = $(this).val();

                $("#lote-masivo-input").val(nombre);
                $("#lote-masivo-id").val(id);

                $("#lote-masivo-select").hide();
            });

            $(document).on("blur", "#lote-masivo-input", function() {
                setTimeout(() => $("#lote-masivo-select").hide(), 200);
            });
        });
    </script>
@endpush
