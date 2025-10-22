@extends('admin.layout')

@section('title', 'Editar Orden de Servicio')

@section('content')
    <style>
        .modern-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            margin-bottom: 1.5rem;
        }

        .modern-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .modern-card-body {
            padding: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #667eea;
            display: inline-block;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .modern-input {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            transition: all 0.2s;
        }

        .modern-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .modern-input:disabled {
            background-color: #f1f5f9;
            cursor: not-allowed;
        }

        .modern-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin-top: 1rem;
        }

        .modern-table thead th {
            background: #f8fafc;
            padding: 1rem;
            font-weight: 700;
            color: #475569;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        .modern-table tbody td {
            padding: 0.75rem;
        }

        .subtotal-display {
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            border: 2px solid #93c5fd;
            border-radius: 8px;
            padding: 0.625rem;
            text-align: right;
            font-weight: 700;
            color: #1e40af;
            min-height: 42px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .btn-add {
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.2s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(71, 85, 105, 0.4);
        }

        .btn-delete {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-delete:hover {
            background: #b91c1c;
            transform: scale(1.1);
        }

        .totales-card {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            margin-top: 1.5rem;
        }

        .total-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-success-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
        }

        .badge-codigo {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }
    </style>

    <div class="container-fluid mt-4">
        {{-- HEADER --}}
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">Editar Orden de Servicio</h4>
                    <span class="badge-codigo">{{ $orden->codigo }}</span>
                </div>
            </div>
        </div>
        <form id="formEditarOrden" method="POST" action="{{ route('orden-servicio.update', $orden->id) }}">
            @csrf
            @method('PUT')
            <?php
            use Carbon\Carbon;
            $hoy = Carbon::now('America/Lima');
            ?>

            {{-- DATOS DEL PROVEEDOR --}}
            <div class="modern-card">
                <div class="modern-card-body">
                    <h5 class="section-title">📋 Datos del Proveedor</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RUC</label>
                            <input type="text" name="documento_proveedor" value="{{ $orden->proveedor->ruc ?? '' }}"
                                class="form-control modern-input">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Razón Social</label>
                            <input type="text" name="proveedor" value="{{ $orden->proveedor->razon_social ?? '' }}"
                                class="form-control modern-input">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_proveedor" value="{{ $orden->proveedor->telefono ?? '' }}"
                                class="form-control modern-input">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion_proveedor"
                                value="{{ $orden->proveedor->direccion ?? '' }}" class="form-control modern-input">
                        </div>
                    </div>
                </div>
            </div>

            {{-- DATOS DE LA ORDEN --}}
            <div class="modern-card">
                <div class="modern-card-body">
                    <h5 class="section-title">📅 Datos de la Orden</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio"
                                value="{{ optional($orden->fecha_inicio)->format('Y-m-d') }}"
                                class="form-control modern-input" min="{{ now('America/Lima')->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin"
                                value="{{ optional($orden->fecha_fin)->format('Y-m-d') }}" class="form-control modern-input"
                                min="{{ now('America/Lima')->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Costo Estimado</label>
                            <input type="number" step="0.01" name="costo_estimado" value="{{ $orden->costo_estimado }}"
                                class="form-control modern-input" id="costoEstimadoInput" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Costo Final</label>
                            <input type="number" step="0.01" name="costo_final" value="{{ $orden->costo_final }}"
                                class="form-control modern-input" id="costoFinalInput" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control modern-input" rows="3">{{ $orden->descripcion }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETALLES DEL SERVICIO --}}
            <div class="modern-card">
                <div class="modern-card-body">
                    <h5 class="section-title">🛠️ Detalles del Servicio</h5>

                    <div class="table-responsive">
                        <table class="modern-table" id="tablaDetalles">
                            <thead>
                                <tr>
                                    <th style="text-align: left;">Descripción</th>
                                    <th style="text-align: center; width: 120px;">Cantidad</th>
                                    <th style="text-align: right; width: 150px;">Precio Unitario</th>
                                    <th style="text-align: right; width: 150px;">Subtotal</th>
                                    <th style="text-align: center; width: 80px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orden->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control modern-input descripcion"
                                                value="{{ $detalle->descripcion }}" placeholder="Descripción del servicio">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control modern-input cantidad text-center"
                                                min="0" step="1" value="{{ $detalle->cantidad }}">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control modern-input precio_unitario text-end"
                                                step="0.01" min="0" value="{{ $detalle->precio_unitario }}">
                                        </td>
                                        <td>
                                            <div class="subtotal-display">
                                                S/ <span
                                                    class="subtotal-valor">{{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn-delete eliminarFila" title="Eliminar">
                                                <svg width="18" height="18" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                    <path fill-rule="evenodd"
                                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="agregarFila" class="btn-add mt-3">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path
                                d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                        </svg>
                        Agregar Detalle
                    </button>
                </div>
            </div>

            {{-- TOTALES --}}
            <div class="totales-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3">
                            <svg width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135z" />
                                <path
                                    d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866zM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5z" />
                            </svg>
                            <div>
                                <p class="total-label mb-1">Costo Total del Servicio</p>
                                <p class="total-amount mb-0" id="totalDisplay">S/ 0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <p class="total-label mb-2">Items: <span id="itemCount">{{ count($orden->detalles) }}</span></p>
                        <button type="submit" class="btn-success-modern">
                            💾 Guardar Cambios
                        </button>
                        <a href="{{ route('orden-servicio.index') }}" class="btn btn-light ms-2 px-4 py-2 rounded-3">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <input type="hidden" name="detalles" id="detalles">
        </form>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("✅ Script actualizado cargado correctamente");

            // 🔹 Recalcular subtotal visualmente
            function recalcularFila(fila) {
                const cantidad = parseFloat(fila.querySelector('.cantidad').value) || 0;
                const precio = parseFloat(fila.querySelector('.precio_unitario').value) || 0;
                const subtotal = cantidad * precio;

                // Mostrar en el span
                const subtotalElemento = fila.querySelector('.subtotal-valor');
                if (subtotalElemento) {
                    subtotalElemento.textContent = subtotal.toFixed(2);
                }
                return subtotal;
            }

            // 🔹 Recalcular totales (estimado y final)
            function recalcularTotales() {
                let total = 0;
                document.querySelectorAll('#tablaDetalles tbody tr').forEach(fila => {
                    total += recalcularFila(fila);
                });

                document.querySelector('input[name="costo_estimado"]').value = total.toFixed(2);
                document.querySelector('input[name="costo_final"]').value = total.toFixed(2);

                const totalDisplay = document.getElementById('totalDisplay');
                if (totalDisplay) totalDisplay.textContent = "S/ " + total.toFixed(2);
            }

            // 🔹 Al cambiar cantidad o precio
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('cantidad') || e.target.classList.contains(
                        'precio_unitario')) {
                    const fila = e.target.closest('tr');
                    recalcularFila(fila);
                    recalcularTotales();
                }
            });

            // 🔹 Agregar nueva fila visual
            document.getElementById('agregarFila').addEventListener('click', function() {
                const nuevaFila = document.createElement('tr');
                nuevaFila.innerHTML = `
            <td><input type="text" class="form-control modern-input descripcion" placeholder="Descripción del servicio"></td>
            <td><input type="number" class="form-control modern-input cantidad text-center" value="1" min="0"></td>
            <td><input type="number" class="form-control modern-input precio_unitario text-end" step="0.01" value="0" min="0"></td>
            <td>
                <div class="subtotal-display">S/ <span class="subtotal-valor">0.00</span></div>
            </td>
            <td class="text-center">
                <button type="button" class="btn-delete eliminarFila" title="Eliminar">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM2.5 3V2h11v1h-11z" />
                    </svg>
                </button>
            </td>`;
                document.querySelector('#tablaDetalles tbody').appendChild(nuevaFila);
                recalcularTotales();
            });

            // 🔹 Eliminar fila
            document.addEventListener('click', function(e) {
                if (e.target.closest('.eliminarFila')) {
                    e.target.closest('tr').remove();
                    recalcularTotales();
                }
            });

            // 🔹 Al enviar: serializar detalles
            document.getElementById('formEditarOrden').addEventListener('submit', function() {
                const detalles = [];
                document.querySelectorAll('#tablaDetalles tbody tr').forEach(fila => {
                    const descripcion = fila.querySelector('.descripcion').value.trim();
                    const cantidad = fila.querySelector('.cantidad').value || 0;
                    const precio = fila.querySelector('.precio_unitario').value || 0;
                    const subtotal = parseFloat(fila.querySelector('.subtotal-valor')
                        .textContent) || 0;

                    if (descripcion !== '') {
                        detalles.push({
                            descripcion,
                            cantidad,
                            precio_unitario: precio,
                            subtotal
                        });
                    }
                });
                document.getElementById('detalles').value = JSON.stringify({
                    detalles
                });
            });

            // Inicializa al cargar
            recalcularTotales();
        });
    </script>
@endsection
