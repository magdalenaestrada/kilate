  <div class="modal fade" id="modalTiempo" tabindex="-1" aria-labelledby="modalTiempoLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content border-0 shadow-lg rounded-4">
              <div class="modal-body bg-light">
                  <form id="formTiempo">
                      @csrf
                      <div class=" rounded-3 shadow-sm">
                          <div class="d-flex justify-content-between align-items-center">
                              <h5 class="modal-title fw-semibold" id="modalTiempoLabel"></h5>
                              <button type="button" style="font-size: 30px" class="close" data-dismiss="modal"
                                  aria-label="Close">
                                  <img style="width: 15px" src="{{ asset('images/icon/close.png') }}" alt="cerrar">
                              </button>
                          </div>
                          <br>
                          <input type="hidden" id="proceso_id" name="proceso_id">
                          <div class="row mt-3">
                              <div class="col-md-4">
                                  <div class="p-3 bg-white rounded-3 shadow-sm text-center">
                                      <h6 class="fw-bold mb-1">Toneladas Iniciales</h6>
                                      <span id="ton_iniciales" class="fs-5 text-primary">0</span>
                                  </div>
                              </div>

                              <div class="col-md-4">
                                  <div class="p-3 bg-white rounded-3 shadow-sm text-center">
                                      <h6 class="fw-bold mb-1">Toneladas Molidas</h6>
                                      <span id="ton_molidas" class="fs-5 text-success">0</span>
                                  </div>
                              </div>

                              <div class="col-md-4">
                                  <div class="p-3 bg-white rounded-3 shadow-sm text-center">
                                      <h6 class="fw-bold mb-1">Toneladas Restantes</h6>
                                      <span id="ton_restantes" class="fs-5 text-danger">0</span>
                                  </div>
                              </div>
                          </div>

                          <br>

                          <div class="row g-3">
                              <div class="col-md-3">
                                  <label class="form-label fw-semibold">Fecha inicio</label>
                                  <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                      max="{{ $hoy }}" value="{{ $hoy }}" required>
                              </div>
                              <div class="col-md-2">
                                  <label class="form-label fw-semibold">Hora inicio</label>
                                  <input type="time" class="form-control" id="hora_inicio" name="hora_inicio"
                                      required>
                              </div>
                              <div class="col-md-3">
                                  <label class="form-label fw-semibold">Fecha fin</label>
                                  <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                                      max="{{ $hoy }}" value="{{ $hoy }}" required>
                              </div>
                              <div class="col-md-2">
                                  <label class="form-label fw-semibold">Hora fin</label>
                                  <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                              </div>
                              <div class="col-md-2">
                                  <label class="form-label">Tonelaje</label>
                                  <input class="form-control" name="tonelaje" id="tonelaje" required>
                              </div>
                          </div>
                          <br>
                          <div class="d-flex justify-content-end align-items-center">
                              <button class="btn btn-primary" id="registrarTiempoBtn" type="button">
                                  <i class="bi bi-plus-lg me-1"></i> Registrar tiempo
                              </button>
                          </div>
                          <br>
                          <div class="row">
                              <div class="table-responsive bg-white rounded-3 shadow-sm p-3">
                                  <table class="table table-sm align-middle text-center" id="tablaTiempos">
                                      <thead class="table-light">
                                          <tr>
                                              <th>Fecha Inicio</th>
                                              <th>Hora Inicio</th>
                                              <th>Fecha Fin</th>
                                              <th>Hora Fin</th>
                                              <th>Tonelaje</th>
                                          </tr>
                                      </thead>
                                      <tbody id="tbodyTiempos">
                                          <tr>
                                              <td colspan="5" class="text-muted">Cargando...</td>
                                          </tr>
                                      </tbody>

                                  </table>
                              </div>
                          </div>
                      </div>
                  </form>
              </div>

              <div class="modal-footer bg-light border-0">

              </div>
          </div>
      </div>
  </div>
