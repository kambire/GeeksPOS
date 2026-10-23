<div class="content-body">
    <div class="card">
        <!-- Card Header -->
        <div class="card-header border-bottom py-2">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h3 class="card-title font-weight-bold mb-0 text-primary">
                        <i class="fa fa-cogs mr-1 text-danger"></i> Configuración Operativa de WhatsApp, Bot IA & Turnos de Delivery
                    </h3>
                    <small class="text-muted">
                        Personalice al 100% el comportamiento del bot de WhatsApp, teléfonos, horarios, tarifas, cuentas bancarias y turnos rotativos.
                    </small>
                </div>
                <div class="col-md-5 text-md-right mt-2 mt-md-0">
                    <?php if ($is_superadmin && !empty($all_locations)): ?>
                        <div class="d-inline-block text-left mr-2">
                            <label class="small font-weight-bold mb-0 text-muted">Negocio / Sucursal:</label>
                            <select class="form-control form-control-sm select2" onchange="window.location.href='<?= base_url('delivery_config?loc_id=') ?>' + this.value;">
                                <?php foreach ($all_locations as $loc_item): ?>
                                    <option value="<?= $loc_item['id'] ?>" <?= ($loc_item['id'] == $loc_id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc_item['cname']) ?> (ID: <?= $loc_item['id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                                <option value="0" <?= ($loc_id == 0) ? 'selected' : '' ?>>🏢 Principal / Matriz (0)</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <a href="<?= base_url('saas') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Volver a SaaS
                    </a>
                </div>
            </div>
        </div>

        <!-- Tenant Info Ribbon -->
        <div class="card-body bg-light border-bottom py-2">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <span class="text-muted">Local Seleccionado:</span>
                    <strong class="text-dark ml-1"><?= htmlspecialchars($location['cname'] ?? 'Local ' . $loc_id) ?></strong>
                    <span class="badge badge-primary ml-1">ID: <?= $loc_id ?></span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">Rubro GeeksIA:</span>
                    <span class="badge badge-<?= $rubro_info['badge_color'] ?? 'primary' ?> ml-1 font-weight-bold" style="font-size:11.5px;">
                        <i class="<?= $rubro_info['icon'] ?? 'fa fa-sitemap' ?> mr-1"></i> <?= $rubro_info['badge'] ?? '🍔 Gastronomía' ?>
                    </span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">Instancia WhatsApp:</span>
                    <span class="badge badge-success ml-1 font-monospace">
                        <i class="fa fa-whatsapp"></i> <?= htmlspecialchars($location['whatsapp_instance'] ?? 'pos_whatsapp') ?>
                    </span>
                </div>
                <div class="col-md-3 text-md-right">
                    <span class="text-muted">API Key GeeksIA:</span>
                    <code class="p-1 bg-white border rounded font-weight-bold ml-1" id="txt_api_key_masked"><?= !empty($location['api_key']) ? '••••' . htmlspecialchars(substr($location['api_key'], -4)) : 'sin clave' ?></code>
                    <?php if ($loc_id > 0): ?>
                        <button type="button" class="btn btn-xs btn-outline-danger ml-1" id="btn_rotate_api_key" title="Genera una clave nueva y anula la anterior">
                            <i class="fa fa-refresh"></i> Rotar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card-body p-3">
            <form id="form_delivery_config" method="post" action="<?= base_url('delivery_config/save') ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="loc_id" value="<?= $loc_id ?>">

                <!-- TABS NAVIGATION -->
                <ul class="nav nav-tabs nav-linetriangle mb-3" id="configTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-turnos-btn" data-toggle="tab" href="#tab-turnos" role="tab">
                            <i class="fa fa-motorcycle mr-1 text-danger"></i> 1. Repartidores (1 al 10)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-telefonos-btn" data-toggle="tab" href="#tab-telefonos" role="tab">
                            <i class="fa fa-phone mr-1 text-primary"></i> 2. Teléfonos Operativos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-horarios-btn" data-toggle="tab" href="#tab-horarios" role="tab">
                            <i class="fa fa-clock-o mr-1 text-warning"></i> 3. Horarios & Cierre
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-tarifas-btn" data-toggle="tab" href="#tab-tarifas" role="tab">
                            <i class="fa fa-truck mr-1 text-info"></i> 4. Tarifas & Radio Delivery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-banco-btn" data-toggle="tab" href="#tab-banco" role="tab">
                            <i class="fa fa-bank mr-1 text-success"></i> 5. Pagos & Transferencias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-ubicacion-btn" data-toggle="tab" href="#tab-ubicacion" role="tab">
                            <i class="fa fa-map-marker mr-1 text-danger"></i> 6. Ubicación & Maps
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-ia-btn" data-toggle="tab" href="#tab-ia" role="tab">
                            <i class="fa fa-android mr-1 text-purple"></i> 7. Bot IA & Promos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-geeksia-btn" data-toggle="tab" href="#tab-geeksia" role="tab">
                            <i class="fa fa-code mr-1 text-dark"></i> 8. Live GeeksIA JSON
                        </a>
                    </li>
                </ul>

                <!-- TAB CONTENT PANELS -->
                <div class="tab-content border-0 p-0" id="configTabsContent">

                    <!-- ==================== TAB 1: REPARTIDORES 1 AL 10 ==================== -->
                    <div class="tab-pane fade show active" id="tab-turnos" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-gradient-directional-danger text-white py-2">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="mb-0 text-white font-weight-bold">
                                            <i class="fa fa-motorcycle mr-1"></i> Asignación Equitativa de Repartidores por Turno (Slots 1 al 10)
                                        </h5>
                                    </div>
                                    <div class="col-md-6 text-md-right mt-2 mt-md-0">
                                        <button type="button" class="btn btn-sm btn-warning font-weight-bold" id="btn_test_turn">
                                            <i class="fa fa-play mr-1"></i> Simular Asignación de Turno
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info ml-1 font-weight-bold" id="btn_random_pins" title="Generar nuevos PINs aleatorios para todos los slots">
                                            <i class="fa fa-random mr-1"></i> Generar PINs Aleatorios
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-light ml-1" id="btn_reset_counters">
                                            <i class="fa fa-refresh mr-1"></i> Resetear Pedidos Hoy
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-secondary py-2 mb-3">
                                    <i class="fa fa-balance-scale mr-1 text-primary"></i>
                                    <strong>Regla de Turnos:</strong> El motor GeeksIA Restaurantes asigna cada pedido al repartidor <strong>Activo</strong> y <strong>Disponible</strong> con <strong>menor cantidad de pedidos hoy</strong>. En caso de empate, le toca al que lleva más tiempo esperando.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle mb-0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width: 70px;" class="text-center">Slot</th>
                                                <th style="min-width: 200px;">Nombre del Repartidor</th>
                                                <th style="min-width: 170px;">Teléfono WhatsApp</th>
                                                <th style="min-width: 165px; width: 165px;" class="text-center">PIN App (4 dígitos)</th>
                                                <th style="width: 110px;" class="text-center">Estado</th>
                                                <th style="width: 130px;" class="text-center">Disponibilidad</th>
                                                <th style="width: 110px;" class="text-center">Pedidos Hoy</th>
                                                <th style="min-width: 150px;">Última Salida</th>
                                                <th style="width: 140px;" class="text-center">Vincular App</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deliveries as $d): $s = $d['slot']; ?>
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-pill badge-dark px-2 py-1 font-weight-bold font-medium-1">
                                                        #<?= $s ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="text" class="form-control form-control-sm font-weight-bold"
                                                           name="slot_<?= $s ?>_nombre"
                                                           value="<?= htmlspecialchars($d['nombre'] ?? ('Repartidor ' . $s)) ?>"
                                                           placeholder="Ej: Repartidor <?= $s ?>">
                                                </td>
                                                <td class="align-middle">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-whatsapp text-success"></i></span></div>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="slot_<?= $s ?>_telefono"
                                                               value="<?= htmlspecialchars($d['telefono'] ?? '') ?>"
                                                               placeholder="Ej: 59598100000<?= $s ?>">
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-key text-warning"></i></span></div>
                                                        <input type="text" class="form-control form-control-sm text-center font-weight-bold"
                                                               id="slot_<?= $s ?>_pin"
                                                               name="slot_<?= $s ?>_pin"
                                                               maxlength="4"
                                                               style="font-size: 15px; letter-spacing: 2px; min-width: 65px;"
                                                               value="<?= htmlspecialchars($d['pin'] ?? '1234') ?>"
                                                               placeholder="1234">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-secondary" onclick="randomizeSlotPin(<?= $s ?>)" title="Generar PIN aleatorio de 4 dígitos">
                                                                <i class="fa fa-random"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="switch_activo_<?= $s ?>"
                                                               name="slot_<?= $s ?>_activo" value="1" <?= (!empty($d['activo'])) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label font-weight-bold <?= (!empty($d['activo'])) ? 'text-success' : 'text-muted' ?>" for="switch_activo_<?= $s ?>">
                                                            <?= (!empty($d['activo'])) ? 'Activo' : 'Inactivo' ?>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="switch_ocupado_<?= $s ?>"
                                                               name="slot_<?= $s ?>_ocupado" value="1" <?= (!empty($d['ocupado'])) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label font-weight-bold <?= (!empty($d['ocupado'])) ? 'text-danger' : 'text-primary' ?>" for="switch_ocupado_<?= $s ?>">
                                                            <?= (!empty($d['ocupado'])) ? 'Ocupado' : 'Libre' ?>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-primary px-3 py-1 font-weight-bold font-medium-1" id="badge_pedidos_<?= $s ?>">
                                                        <?= (int)$d['pedidos_hoy'] ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle small">
                                                    <?php if (!empty($d['ultimo_pedido_at'])): ?>
                                                        <span class="text-dark font-weight-bold"><i class="fa fa-clock-o text-muted mr-1"></i><?= $d['ultimo_pedido_at'] ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Sin salidas hoy</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="showDriverQr(<?= $s ?>)" title="Ver Código QR para escanear en la App">
                                                        <i class="fa fa-qrcode mr-1"></i> QR
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success ml-1" onclick="sendDriverWhatsapp(<?= $s ?>)" title="Enviar PIN y acceso por WhatsApp">
                                                        <i class="fa fa-whatsapp"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 2: TELÉFONOS OPERATIVOS ==================== -->
                    <div class="tab-pane fade" id="tab-telefonos" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-gradient-directional-primary text-white py-2">
                                <h5 class="mb-0 text-white font-weight-bold">
                                    <i class="fa fa-phone mr-1"></i> Roles Operativos de WhatsApp & Silenciamiento (Staff Silent)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info py-2 mb-3">
                                    <i class="fa fa-info-circle mr-1"></i>
                                    <strong>Staff Silent:</strong> Los teléfonos de <strong>Caja</strong> y <strong>Cocina</strong> están silenciados para la IA. Si escriben al WhatsApp del restaurante desde estos celulares, el bot no interfiere.
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">
                                            <i class="fa fa-desktop text-info mr-1"></i> Teléfono Caja (WhatsApp)
                                            <span class="badge badge-warning ml-1">Staff Silenciado</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-phone"></i></span></div>
                                            <input type="text" class="form-control" name="phone_caja" value="<?= htmlspecialchars($config['phone_caja'] ?? '') ?>" placeholder="Ej: 595981000001">
                                        </div>
                                        <small class="text-muted">Caja principal del local.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">
                                            <i class="fa fa-cutlery text-danger mr-1"></i> Teléfono Cocina (WhatsApp)
                                            <span class="badge badge-warning ml-1">Comandas</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-cutlery"></i></span></div>
                                            <input type="text" class="form-control" name="phone_cocina" value="<?= htmlspecialchars($config['phone_cocina'] ?? '') ?>" placeholder="Ej: 595981000002">
                                        </div>
                                        <small class="text-muted">Recibe los pedidos confirmados para preparación.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">
                                            <i class="fa fa-user-circle text-primary mr-1"></i> Teléfono Gerente (WhatsApp)
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user"></i></span></div>
                                            <input type="text" class="form-control" name="phone_gerente" value="<?= htmlspecialchars($config['phone_gerente'] ?? '') ?>" placeholder="Ej: 595981000003">
                                        </div>
                                        <small class="text-muted">Acceso a reportes diarios, comandos ejecutivos y ventas.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">
                                            <i class="fa fa-user text-secondary mr-1"></i> Teléfono Encargado (WhatsApp)
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-id-badge"></i></span></div>
                                            <input type="text" class="form-control" name="phone_encargado" value="<?= htmlspecialchars($config['phone_encargado'] ?? '') ?>" placeholder="Ej: 595981000004">
                                        </div>
                                        <small class="text-muted">Supervisor o encargado de turno.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">
                                            <i class="fa fa-shield text-warning mr-1"></i> Super Administradores (WhatsApp)
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i></span></div>
                                            <input type="text" class="form-control" name="phone_admins" value="<?= htmlspecialchars($config['phone_admins'] ?? '') ?>" placeholder="Ej: 595981000005, 595981000006">
                                        </div>
                                        <small class="text-muted">Separados por coma si hay más de un número.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">
                                            <i class="fa fa-users text-success mr-1"></i> Grupo de WhatsApp Deliveries (JID)
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-whatsapp"></i></span></div>
                                            <input type="text" class="form-control" name="delivery_group" value="<?= htmlspecialchars($config['delivery_group'] ?? '') ?>" placeholder="Ej: 120363000000000000@g.us">
                                        </div>
                                        <small class="text-muted">JID del grupo de WhatsApp donde caen las salidas y demoras.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 3: HORARIOS DÍA POR DÍA & APERTURA ==================== -->
                    <div class="tab-pane fade" id="tab-horarios" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-gradient-directional-amber text-white py-2 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white font-weight-bold">
                                    <i class="fa fa-calendar-check-o mr-1"></i> Horarios de Atención Día por Día & Control de Apertura
                                </h5>
                                <span class="badge badge-light text-dark font-weight-bold px-2 py-1">
                                    <i class="fa fa-clock-o mr-1 text-primary"></i> Hora Asunción: <?= date('H:i') ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <!-- Banner explicativo -->
                                <div class="alert alert-light border-left-amber p-2 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-info-circle fa-2x text-warning mr-3"></i>
                                        <div>
                                            <strong class="text-dark">Configuración Independiente Día por Día:</strong>
                                            <span class="text-muted d-block small">
                                                Active o desactive cada día de la semana y defina su horario exacto de apertura y cierre. Si cierra de madrugada (ej: 18:00 a 02:00 hs), el bot GeeksIA lo reconoce automáticamente sin considerar cerrado el local.
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Toolbar Accesos Rápidos -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between p-2 mb-3 bg-light border rounded">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <span class="font-weight-bold text-dark mr-2 small"><i class="fa fa-bolt text-warning mr-1"></i> Accesos Rápidos:</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold mr-1 mb-1" onclick="aplicarPresetHorarios('todos')">
                                            <i class="fa fa-calendar mr-1"></i> Todos los Días (Lun a Dom)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info font-weight-bold mr-1 mb-1" onclick="aplicarPresetHorarios('semana')">
                                            <i class="fa fa-briefcase mr-1"></i> Lunes a Viernes
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning font-weight-bold mr-1 mb-1" onclick="aplicarPresetHorarios('finde')">
                                            <i class="fa fa-glass mr-1"></i> Jueves a Domingo
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold mr-1 mb-1" onclick="aplicarPresetHorarios('copiar_primer_abierto')">
                                            <i class="fa fa-copy mr-1"></i> Igualar Todos al Primer Abierto
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold mr-1 mb-1" onclick="aplicarPresetHorarios('cerrar_todos')">
                                            <i class="fa fa-ban mr-1"></i> Cerrar Todos
                                        </button>
                                    </div>
                                </div>

                                <!-- Tabla Día por Día -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0" id="tabla_horarios_semana">
                                        <thead class="bg-light text-dark">
                                            <tr>
                                                <th style="min-width: 150px;"><i class="fa fa-calendar mr-1 text-primary"></i> Día de la Semana</th>
                                                <th style="min-width: 160px;" class="text-center"><i class="fa fa-toggle-on mr-1 text-success"></i> Estado</th>
                                                <th style="min-width: 180px;"><i class="fa fa-sign-in mr-1 text-success"></i> Hora Apertura</th>
                                                <th style="width: 30px;" class="text-center font-weight-bold text-muted">a</th>
                                                <th style="min-width: 180px;"><i class="fa fa-sign-out mr-1 text-danger"></i> Hora Cierre</th>
                                                <th style="min-width: 200px;" class="text-center"><i class="fa fa-moon-o mr-1 text-warning"></i> Observación</th>
                                                <th style="min-width: 120px;" class="text-center"><i class="fa fa-clone mr-1 text-muted"></i> Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($horarios_semana as $key => $d): ?>
                                                <?php $is_act = !empty($d['activo']); ?>
                                                <tr id="row_dia_<?= $key ?>" class="dia-row <?= $is_act ? '' : 'table-secondary opacity-75' ?>">
                                                    <td class="font-weight-bold text-dark align-middle">
                                                        <i class="<?= $d['icon'] ?> text-primary mr-1"></i>
                                                        <span class="dia-nombre-txt"><?= $d['nombre'] ?></span>
                                                        <input type="hidden" name="horario[<?= $key ?>][nombre]" value="<?= $d['nombre'] ?>">
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="custom-control custom-switch d-inline-block">
                                                            <input type="checkbox" class="custom-control-input dia-switch" id="switch_dia_<?= $key ?>" name="horario[<?= $key ?>][activo]" value="1" <?= $is_act ? 'checked' : '' ?> onchange="toggleDiaEstado('<?= $key ?>')">
                                                            <label class="custom-control-label font-weight-bold" for="switch_dia_<?= $key ?>" id="label_dia_<?= $key ?>">
                                                                <?= $is_act ? '<span class="badge badge-success px-2 py-1">🟢 Abierto</span>' : '<span class="badge badge-secondary px-2 py-1">⛔ Cerrado</span>' ?>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-clock-o text-success"></i></span>
                                                            </div>
                                                            <input type="time" class="form-control font-weight-bold dia-input dia-apertura" id="hora_apertura_<?= $key ?>" name="horario[<?= $key ?>][apertura]" value="<?= htmlspecialchars($d['apertura']) ?>" <?= $is_act ? '' : 'disabled' ?> onchange="recalcularObservacionDia('<?= $key ?>')">
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle text-muted font-weight-bold">a</td>
                                                    <td class="align-middle">
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-clock-o text-danger"></i></span>
                                                            </div>
                                                            <input type="time" class="form-control font-weight-bold dia-input dia-cierre" id="hora_cierre_<?= $key ?>" name="horario[<?= $key ?>][cierre]" value="<?= htmlspecialchars($d['cierre']) ?>" <?= $is_act ? '' : 'disabled' ?> onchange="recalcularObservacionDia('<?= $key ?>')">
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle" id="obs_dia_<?= $key ?>">
                                                        <!-- Evaluado dinámicamente con JS -->
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-sm btn-outline-info font-weight-bold py-1 px-2" title="Copiar este horario a todos los demás días" onclick="copiarHorarioDia('<?= $key ?>')">
                                                            <i class="fa fa-clone mr-1"></i> Copiar
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Resumen de Horario y Mensajes -->
                                <div class="row mt-3">
                                    <div class="col-md-12 mb-3">
                                        <div class="p-3 bg-light border rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="font-weight-bold text-dark mb-0">
                                                    <i class="fa fa-commenting-o text-primary mr-1"></i> Resumen de Horario (Texto que lee el cliente en WhatsApp):
                                                </label>
                                                <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold" onclick="generarTextoResumenHorarios(true)">
                                                    <i class="fa fa-magic mr-1"></i> Auto-generar Resumen desde la tabla
                                                </button>
                                            </div>
                                            <div class="input-group mt-1">
                                                <input type="text" class="form-control font-weight-bold" id="horario_atencion_input" name="horario_atencion" value="<?= htmlspecialchars($config['horario_atencion'] ?? 'Lunes a Domingo de 18:00 a 23:59') ?>" placeholder="Ej: Lunes y Miércoles 18:00 a 23:00, Jueves a Domingo 18:00 a 00:00 (Martes cerrado)">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="generarTextoResumenHorarios(true)" title="Regenerar texto automático">
                                                        <i class="fa fa-refresh mr-1"></i> Regenerar
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                Este es el texto que la IA responderá cuando el cliente pregunte: <em>"¿Qué días y horarios atienden?"</em>. Puede personalizarlo libremente o actualizarlo con el botón mágico.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold text-dark">
                                            <i class="fa fa-bullhorn text-warning mr-1"></i> Mensaje Automático cuando el Local está CERRADO:
                                        </label>
                                        <textarea class="form-control" name="mensaje_cerrado" rows="3" placeholder="En este momento nuestro local se encuentra cerrado..."><?= htmlspecialchars($config['mensaje_cerrado'] ?? '') ?></textarea>
                                        <small class="text-muted">Si el cliente escribe fuera de horario o en día cerrado, el bot le enviará este mensaje y no tomará pedidos.</small>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-switch p-2 bg-light border border-danger rounded h-100">
                                            <input type="checkbox" class="custom-control-input" id="switch_local_cerrado" name="is_local_cerrado" value="1" <?= (!empty($config['is_local_cerrado'])) ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-bold text-danger" for="switch_local_cerrado" style="font-size:14px;">
                                                ⛔ Pausar Local Hoy (Cerrado por Emergencia / Feriado)
                                            </label>
                                            <small class="d-block text-muted">Activa esta casilla si hoy no van a abrir temporalmente sin alterar el horario habitual de la tabla.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-switch p-2 bg-light border border-warning rounded h-100">
                                            <input type="checkbox" class="custom-control-input" id="switch_delivery_cerrado" name="is_delivery_cerrado" value="1" <?= (!empty($config['is_delivery_cerrado'])) ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-bold text-warning" for="switch_delivery_cerrado" style="font-size:14px;">
                                                🛵 Pausar solo Envíos / Delivery (Solo Retiro en Local)
                                            </label>
                                            <small class="d-block text-muted">Si hay lluvia torrencial o falta de repartidores, la IA solo tomará pedidos para retirar (Take Away).</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 4: TARIFAS & RADIO ==================== -->
                    <div class="tab-pane fade" id="tab-tarifas" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-gradient-directional-teal text-white py-2">
                                <h5 class="mb-0 text-white font-weight-bold">
                                    <i class="fa fa-truck mr-1"></i> Tarifas de Delivery, Radio Máximo & Tiempos Estimados
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Radio Máximo de Cobertura (Km):</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" class="form-control font-weight-bold" name="radio_max_km" value="<?= htmlspecialchars($config['radio_max_km'] ?? '10.00') ?>">
                                            <div class="input-group-append"><span class="input-group-text">Km</span></div>
                                        </div>
                                        <small class="text-muted">Si el cliente está más lejos, la IA le ofrecerá retiro en el local.</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Costo Base de Envío (Gs):</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control font-weight-bold" name="costo_envio_base" value="<?= htmlspecialchars($config['costo_envio_base'] ?? '10000') ?>">
                                            <div class="input-group-append"><span class="input-group-text">Gs</span></div>
                                        </div>
                                        <small class="text-muted">Tarifa mínima dentro de la zona base.</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Costo Adicional por Km (Gs):</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control font-weight-bold" name="costo_por_km" value="<?= htmlspecialchars($config['costo_por_km'] ?? '2000') ?>">
                                            <div class="input-group-append"><span class="input-group-text">Gs / Km</span></div>
                                        </div>
                                        <small class="text-muted">Calculado según la distancia real en ruta.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">Tiempo Estimado de Entrega (Delivery):</label>
                                        <input type="text" class="form-control" name="tiempo_entrega_min" value="<?= htmlspecialchars($config['tiempo_entrega_min'] ?? '35 a 45 minutos') ?>" placeholder="Ej: 35 a 45 minutos">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">Tiempo Estimado de Retiro (Take Away):</label>
                                        <input type="text" class="form-control" name="tiempo_retiro_min" value="<?= htmlspecialchars($config['tiempo_retiro_min'] ?? '15 a 20 minutos') ?>" placeholder="Ej: 15 a 20 minutos">
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="custom-control custom-switch p-2 bg-light border rounded">
                                            <input type="checkbox" class="custom-control-input" id="switch_delivery_cerrado" name="is_delivery_cerrado" value="1" <?= (!empty($config['is_delivery_cerrado'])) ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-bold text-warning" for="switch_delivery_cerrado" style="font-size:14px;">
                                                🛵 Pausar Solo Delivery Hoy (Aceptar únicamente pedidos para retiro / pasar a buscar)
                                            </label>
                                            <small class="d-block text-muted">Útil en días de lluvia torrencial o falta temporal de repartidores.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 5: BANCO & PAGOS ==================== -->
                    <div class="tab-pane fade" id="tab-banco" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-gradient-directional-success text-white py-2">
                                <h5 class="mb-0 text-white font-weight-bold">
                                    <i class="fa fa-bank mr-1"></i> Datos Bancarios para Pagos con Transferencia (SIPAP / QR)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success py-2 mb-3">
                                    <i class="fa fa-check-circle mr-1"></i>
                                    <strong>Validación Automática de Comprobantes:</strong> Cuando el cliente pide pagar con transferencia, la IA le envía estos datos exactos. Al recibir la foto de la transferencia, la IA verifica que el titular y el monto coincidan con el total.
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Entidad Bancaria:</label>
                                        <input type="text" class="form-control font-weight-bold" name="banco_nombre" value="<?= htmlspecialchars($config['banco_nombre'] ?? '') ?>" placeholder="Ej: Banco Demo S.A.">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Titular / Razón Social de la Cuenta:</label>
                                        <input type="text" class="form-control font-weight-bold" name="banco_titular" value="<?= htmlspecialchars($config['banco_titular'] ?? '') ?>" placeholder="Ej: Mi Restaurante Demo S.A.">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">RUC o Cédula del Titular:</label>
                                        <input type="text" class="form-control font-weight-bold" name="banco_ruc_ci" value="<?= htmlspecialchars($config['banco_ruc_ci'] ?? '') ?>" placeholder="Ej: 80000000-1">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">Número de Cuenta Bancaria:</label>
                                        <input type="text" class="form-control font-weight-bold font-monospace" name="banco_cuenta" value="<?= htmlspecialchars($config['banco_cuenta'] ?? '') ?>" placeholder="Ej: 00-000000-0">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">Alias / Teléfono SIPAP:</label>
                                        <input type="text" class="form-control font-weight-bold" name="banco_alias" value="<?= htmlspecialchars($config['banco_alias'] ?? '') ?>" placeholder="Ej: 0981000000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 6: UBICACIÓN & GOOGLE MAPS ==================== -->
                    <div class="tab-pane fade" id="tab-ubicacion" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-gradient-directional-danger text-white py-2">
                                <h5 class="mb-0 text-white font-weight-bold">
                                    <i class="fa fa-map-marker mr-1"></i> Ubicación del Local & Enlaces de Google Maps
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold text-dark">Dirección Física del Local:</label>
                                        <input type="text" class="form-control" name="maps_direccion" value="<?= htmlspecialchars($config['maps_direccion'] ?? '') ?>" placeholder="Ej: Calle Principal 123 c/ Avda. Central (Ciudad Demo)">
                                        <small class="text-muted">La IA le pasa esta dirección exacta al cliente cuando pide retirar su pedido.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">Link de Google Maps (Ubicación para Llegar):</label>
                                        <input type="url" class="form-control" name="maps_link" value="<?= htmlspecialchars($config['maps_link'] ?? '') ?>" placeholder="https://maps.app.goo.gl/ejemploDemo123">
                                        <small class="text-muted">Enlace que se le envía al cliente que retira en mostrador.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">Link de Google Maps para Reseñas (Encuesta 5 Estrellas):</label>
                                        <input type="url" class="form-control" name="maps_resenas_link" value="<?= htmlspecialchars($config['maps_resenas_link'] ?? '') ?>" placeholder="https://g.page/r/ejemploDemo/review">
                                        <small class="text-muted">Se envía automáticamente tras la entrega cuando el cliente califica con 4 o 5 estrellas.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 7: BOT IA & PROMOS ==================== -->
                    <!-- ==================== TAB 7: BOT IA & PROVEEDORES ==================== -->
                    <div class="tab-pane fade" id="tab-ia" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-gradient-directional-purple text-white py-2" style="background:#7c3aed;">
                                <h5 class="mb-0 text-white font-weight-bold">
                                    <i class="fa fa-android mr-1"></i> Configuración de IA Preferida (Gemini, OpenAI, Qwen, DeepSeek, Claude, Groq)
                                </h5>
                            </div>
                            <div class="card-body">

                                <!-- SECCIÓN A: PROVEEDOR Y API KEY EXCLUSIVA DE ESTE LOCAL -->
                                <div class="p-3 mb-3 rounded" style="background:#f5f3ff; border:1px solid #ddd6fe; border-radius:10px;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-0" style="color:#6b21a8;">
                                            <i class="fa fa-key"></i> Proveedor de Inteligencia Artificial & Credenciales API
                                        </h6>
                                        <span class="badge badge-primary px-2 py-1" id="lbl_current_provider" style="font-size:11px;">
                                            <?= strtoupper($config['ia_proveedor'] ?? 'GEMINI') ?>
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mb-3">
                                        Cada local puede utilizar su propia clave de API y su proveedor de IA preferido. El motor de WhatsApp y GeeksIA Restaurantes consumirá exactamente esta configuración para este negocio.
                                    </small>

                                    <div class="row">
                                        <!-- Proveedor de IA -->
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold text-dark">Proveedor de IA:</label>
                                            <select class="form-control font-weight-bold" name="ia_proveedor" id="sel_ia_proveedor" onchange="handleAiProviderChange(this.value)">
                                                <option value="gemini" <?= (@$config['ia_proveedor'] == 'gemini') ? 'selected' : '' ?>>🔵 Google Gemini (Recomendado / Económico)</option>
                                                <option value="openai" <?= (@$config['ia_proveedor'] == 'openai') ? 'selected' : '' ?>>🟢 OpenAI (ChatGPT / GPT-4o)</option>
                                                <option value="qwen" <?= (@$config['ia_proveedor'] == 'qwen') ? 'selected' : '' ?>>🟣 Alibaba Qwen (DashScope / OpenRouter)</option>
                                                <option value="deepseek" <?= (@$config['ia_proveedor'] == 'deepseek') ? 'selected' : '' ?>>🐋 DeepSeek (DeepSeek-V3 / R1)</option>
                                                <option value="anthropic" <?= (@$config['ia_proveedor'] == 'anthropic') ? 'selected' : '' ?>>🟠 Anthropic Claude (Claude 3.5 Sonnet)</option>
                                                <option value="groq" <?= (@$config['ia_proveedor'] == 'groq') ? 'selected' : '' ?>>⚡ Groq (Llama 3.3 Ultra Rápido)</option>
                                                <option value="custom" <?= (@$config['ia_proveedor'] == 'custom') ? 'selected' : '' ?>>⚙️ Ollama / Servidor Propio (Compatible OpenAI)</option>
                                            </select>
                                        </div>

                                        <!-- API Key -->
                                        <div class="col-md-5 mb-3">
                                            <label class="font-weight-bold text-dark">API Key del Proveedor:</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control font-monospace font-weight-bold" name="ia_api_key" id="inp_ia_api_key"
                                                       value="" autocomplete="new-password"
                                                       placeholder="Pegue aquí su API Key secreta">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleApiKeyVisibility()" id="btn_toggle_key" title="Ver / Ocultar clave">
                                                        <i class="fa fa-eye" id="icon_toggle_key"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted" id="help_api_key">Clave privada de la IA para este local.</small>
                                            <small class="d-block font-weight-bold <?= !empty($config['ia_api_key_hint']) ? 'text-success' : 'text-muted' ?>">
                                                <?php if (!empty($config['ia_api_key_hint'])): ?>
                                                    <i class="fa fa-lock"></i> Clave guardada cifrada (termina en <?= htmlspecialchars($config['ia_api_key_hint']) ?>). Dejá el campo vacío para conservarla.
                                                <?php else: ?>
                                                    <i class="fa fa-unlock"></i> Sin clave guardada.
                                                <?php endif; ?>
                                            </small>
                                        </div>

                                        <!-- Modelo de IA -->
                                        <div class="col-md-3 mb-3">
                                            <label class="font-weight-bold text-dark">Modelo de IA:</label>
                                            <input type="text" class="form-control font-monospace font-weight-bold" name="ia_modelo" id="inp_ia_modelo" list="list_ia_modelos"
                                                   value="<?= htmlspecialchars($config['ia_modelo'] ?? 'gemini-1.5-flash') ?>"
                                                   placeholder="Ej: gemini-1.5-flash">
                                            <datalist id="list_ia_modelos">
                                                <!-- Opciones dinámicas según proveedor -->
                                            </datalist>
                                        </div>

                                        <!-- URL Base / Endpoint (Para Ollama, DeepSeek, Qwen o Custom) -->
                                        <div class="col-md-6 mb-2" id="wrap_ia_base_url">
                                            <label class="font-weight-bold text-dark">URL Base / Endpoint Personalizado (Opcional):</label>
                                            <input type="text" class="form-control font-monospace" name="ia_base_url" id="inp_ia_base_url"
                                                   value="<?= htmlspecialchars($config['ia_base_url'] ?? '') ?>"
                                                   placeholder="https://api.openai.com/v1 o http://localhost:11434/v1">
                                            <small class="text-muted">Dejar vacío para usar la URL oficial del proveedor.</small>
                                        </div>

                                        <!-- Temperatura y Max Tokens -->
                                        <div class="col-md-3 mb-2">
                                            <label class="font-weight-bold text-dark">Temperatura (Creatividad):</label>
                                            <div class="input-group">
                                                <input type="number" step="0.05" min="0" max="1.5" class="form-control" name="ia_temperatura"
                                                       value="<?= htmlspecialchars($config['ia_temperatura'] ?? '0.70') ?>">
                                                <div class="input-group-append"><span class="input-group-text"><i class="fa fa-thermometer-half"></i></span></div>
                                            </div>
                                            <small class="text-muted">0.3 = Estricto | 0.7 = Equilibrado</small>
                                        </div>

                                        <div class="col-md-3 mb-2">
                                            <label class="font-weight-bold text-dark">Máximo de Tokens:</label>
                                            <input type="number" class="form-control" name="ia_max_tokens"
                                                   value="<?= htmlspecialchars($config['ia_max_tokens'] ?? '1000') ?>" min="100" max="8000">
                                            <small class="text-muted">Límite de respuesta (1000 default).</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECCIÓN B: RUBRO DEL NEGOCIO & PLANTILLA DE COMPORTAMIENTO -->
                                <div class="p-3 mb-3 rounded" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px;">
                                    <div class="row align-items-center">
                                        <div class="col-md-7 mb-2 mb-md-0">
                                            <label class="font-weight-bold text-dark mb-1">
                                                <i class="fa fa-sitemap text-success mr-1"></i> Rubro del Negocio & Plantilla de Flujo GeeksIA:
                                            </label>
                                            <select class="form-control font-weight-bold" name="tipo_flujo" id="sel_tipo_flujo_config" onchange="handleRubroConfigChange(this.value)">
                                                <?php if (!empty($templates)): ?>
                                                    <?php foreach ($templates as $k => $t): ?>
                                                        <option value="<?= $k ?>" <?= ($k == ($config['tipo_flujo'] ?? $tipo_flujo)) ? 'selected' : '' ?>>
                                                            <?= $t['badge'] ?> - <?= $t['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <small class="text-muted d-block mt-1" id="txt_rubro_desc">
                                                <?= htmlspecialchars($rubro_info['short_desc'] ?? '') ?>
                                            </small>
                                        </div>
                                        <div class="col-md-5 text-md-right">
                                            <button type="button" class="btn btn-outline-success font-weight-bold btn-sm shadow-sm" onclick="cargarPromptsRecomendados()">
                                                <i class="fa fa-magic mr-1"></i> Cargar Instrucciones & Tono Recomendados
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECCIÓN C: MODO Y WHITELIST -->
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Modo de Operación del Bot:</label>
                                        <select class="form-control font-weight-bold" name="bot_modo">
                                            <option value="bot" <?= (@$config['bot_modo'] == 'bot') ? 'selected' : '' ?>>🤖 Bot Automático (Producción Normal)</option>
                                            <option value="humano" <?= (@$config['bot_modo'] == 'humano') ? 'selected' : '' ?>>👨‍💻 Humano (Bot Pausado - Atención Manual)</option>
                                            <option value="desarrollo" <?= (@$config['bot_modo'] == 'desarrollo') ? 'selected' : '' ?>>🧪 Modo Desarrollo (Solo Números de Prueba)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label class="font-weight-bold text-dark">Números de Prueba (Whitelist de Desarrollo):</label>
                                        <input type="text" class="form-control" name="numeros_prueba" value="<?= htmlspecialchars($config['numeros_prueba'] ?? '') ?>" placeholder="Ej: 595981000098, 595981000099">
                                        <small class="text-muted">Separados por comas. Reciben atención aún en modo desarrollo o cuando el local está cerrado.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">Tono de Voz / Personalidad de la IA:</label>
                                        <input type="text" class="form-control" name="ia_tono" value="<?= htmlspecialchars($config['ia_tono'] ?? 'Cálido, amigable, paraguayo informal con emojis moderados') ?>" placeholder="Ej: Cálido, amigable, formal, etc.">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold text-dark">Instrucciones Especiales del Local para la IA:</label>
                                        <textarea class="form-control" name="ia_instrucciones_extra" rows="2" placeholder="Ej: Incluir cubiertos descartables y servilletas solo a pedido del cliente."><?= htmlspecialchars($config['ia_instrucciones_extra'] ?? '') ?></textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <h6 class="font-weight-bold text-primary mb-2 mt-2">
                                            <i class="fa fa-tag mr-1"></i> Promociones Fijas por Día de la Semana (Se inyectan solas según el día):
                                        </h6>
                                        <?php
                                        $promos = $config['promos_decoded'] ?? [];
                                        $dias_keys = [
                                            'mon' => 'Lunes',
                                            'tue' => 'Martes',
                                            'wed' => 'Miércoles',
                                            'thu' => 'Jueves',
                                            'fri' => 'Viernes',
                                            'sat' => 'Sábado',
                                            'sun' => 'Domingo'
                                        ];
                                        ?>
                                        <div class="row">
                                            <?php foreach ($dias_keys as $k => $day_label): ?>
                                                <div class="col-md-6 mb-2">
                                                    <label class="small font-weight-bold mb-0 text-dark"><?= $day_label ?>:</label>
                                                    <input type="text" class="form-control form-control-sm" name="promo_<?= $k ?>"
                                                           value="<?= htmlspecialchars($promos[$k] ?? '') ?>"
                                                           placeholder="Ej: 2x1 en Platos Seleccionados o Descuento 15%">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 8: LIVE GEEKSIA JSON ==================== -->
                    <div class="tab-pane fade" id="tab-geeksia" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-dark text-white py-2">
                                <h5 class="mb-0 text-white font-weight-bold">
                                    <i class="fa fa-code mr-1"></i> Inspección en Tiempo Real del Endpoint para GeeksIA
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Tu flujo de GeeksIA Restaurantes consume este endpoint en cada mensaje recibido. Al guardar cualquier cambio en estas pestañas, GeeksIA lo refleja inmediatamente.
                                </p>
                                <div class="row">
                                    <div class="col-md-8 mb-2">
                                        <label class="font-weight-bold text-dark">URL del Endpoint para GeeksIA (GET):</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control font-monospace bg-light" id="api_endpoint_url" readonly
                                                   value="<?= htmlspecialchars($api_base_url) ?>workflow_config">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" onclick="navigator.clipboard.writeText($('#api_endpoint_url').val()); alert('¡URL copiada al portapapeles!');">
                                                    <i class="fa fa-copy"></i> Copiar URL
                                                </button>
                                                <button class="btn btn-primary" type="button" id="btn_fetch_live_json">
                                                    <i class="fa fa-eye"></i> Inspeccionar JSON
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="font-weight-bold text-dark">Asignación de Delivery (POST):</label>
                                        <input type="text" class="form-control font-monospace bg-light" readonly
                                               value="<?= htmlspecialchars($api_base_url) ?>assign_delivery">
                                    </div>
                                </div>
                                <div class="alert alert-secondary py-2 small mb-0">
                                    <i class="fa fa-shield mr-1"></i> La API key va siempre en el encabezado <code>X-API-KEY</code> (nunca en la URL) y solo da acceso a este local.
                                </div>

                                <div id="live_json_container" style="display:none;" class="mt-3">
                                    <h6 class="font-weight-bold text-primary">Datos JSON en vivo generados para GeeksIA Restaurantes:</h6>
                                    <pre id="live_json_output" class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto; font-size: 12px;"></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- SAVE FLOATING BAR -->
                <div class="card shadow-sm border bg-white mb-4 mt-2">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="font-weight-bold text-dark">
                                    <i class="fa fa-check-circle text-success mr-1"></i>
                                    Al presionar <strong>"Guardar Toda la Configuración"</strong>, todos los cambios en cualquiera de las pestañas se aplican en vivo en GeeksPOS y en GeeksIA Restaurantes.
                                </span>
                            </div>
                            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                                <button type="submit" class="btn btn-success btn-lg px-4 shadow font-weight-bold" id="btn_save_config">
                                    <i class="fa fa-save mr-1"></i> Guardar Toda la Configuración
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Ajax Form Submit
    $('#form_delivery_config').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn_save_config');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Guardando toda la configuración...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Guardar Toda la Configuración');
                if (resp.status === 'Success') {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(resp.message, 'Configuración Actualizada');
                    } else {
                        alert(resp.message);
                    }
                } else {
                    alert(resp.message || 'Error al guardar');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Guardar Toda la Configuración');
                alert('Ocurrió un error al procesar la solicitud.');
            }
        });
    });

    // Reset Counters
    $('#btn_reset_counters').on('click', function() {
        if (!confirm('¿Está seguro de reiniciar los contadores de pedidos de hoy a 0 para este local?')) {
            return;
        }
        $.ajax({
            url: '<?= base_url('delivery_config/reset_counters') ?>',
            type: 'POST',
            data: {
                loc_id: '<?= $loc_id ?>',
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(resp) {
                if (resp.status === 'Success') {
                    for (var i = 1; i <= 10; i++) {
                        $('#badge_pedidos_' + i).text('0');
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.success(resp.message);
                    } else {
                        alert(resp.message);
                    }
                }
            }
        });
    });

    // Randomize Single Slot PIN
    window.randomizeSlotPin = function(slot) {
        var randomPin = Math.floor(1000 + Math.random() * 9000);
        $('#slot_' + slot + '_pin').val(randomPin).css('background-color', '#fff3cd');
        setTimeout(function() {
            $('#slot_' + slot + '_pin').css('background-color', '');
        }, 1200);
    };

    // Randomize All Pins
    $('#btn_random_pins').on('click', function() {
        if (!confirm('¿Desea generar nuevos PINs aleatorios de 4 dígitos para todos los repartidores?')) {
            return;
        }
        $.ajax({
            url: '<?= base_url('delivery_config/randomize_pins') ?>',
            type: 'POST',
            data: {
                loc_id: '<?= $loc_id ?>',
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(resp) {
                if (resp.status === 'Success' && resp.pins) {
                    for (var s in resp.pins) {
                        $('#slot_' + s + '_pin').val(resp.pins[s]).css('background-color', '#d1e7dd');
                    }
                    setTimeout(function() {
                        $('input[id$="_pin"]').css('background-color', '');
                    }, 1500);

                    if (typeof toastr !== 'undefined') {
                        toastr.success(resp.message);
                    } else {
                        alert(resp.message);
                    }
                }
            }
        });
    });

    // Test Round-Robin Turn Simulation
    $('#btn_test_turn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Evaluando...');

        $.ajax({
            url: '<?= base_url('delivery_config/test_assignment') ?>',
            type: 'POST',
            data: {
                loc_id: '<?= $loc_id ?>',
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fa fa-play mr-1"></i> Simular Asignación de Turno');
                if (resp.assigned && resp.delivery) {
                    var d = resp.delivery;
                    // Simulación de solo lectura: no cambia contadores ni marca ocupado al repartidor
                    alert('🎯 Simulación (no se asignó ningún pedido real)\nLe tocaría al siguiente repartidor:\n\n' +
                          '• Slot #' + d.slot + ': ' + d.nombre + '\n' +
                          '• Teléfono: ' + d.telefono + '\n' +
                          '• Total pedidos hoy: ' + d.pedidos_hoy);
                } else {
                    alert('⚠️ ' + (resp.message || 'No hay repartidores activos o disponibles'));
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-play mr-1"></i> Simular Asignación de Turno');
                alert('Error al simular turno.');
            }
        });
    });

    // Inspect live JSON
    $('#btn_fetch_live_json').on('click', function() {
        // Vista previa con la sesión del panel (sin exponer la API key en el navegador)
        $.getJSON('<?= base_url('delivery_config/live_json') ?>', { loc_id: '<?= (int)$loc_id ?>' }, function(data) {
            $('#live_json_output').text(JSON.stringify(data, null, 2));
            $('#live_json_container').slideDown();
        }).fail(function() {
            alert('Error al obtener JSON desde el API');
        });
    });

    // Rotar la API key del local: la nueva se muestra una sola vez
    $('#btn_rotate_api_key').on('click', function() {
        if (!confirm('Se generará una API key nueva y la actual dejará de funcionar de inmediato. ¿Continuar?')) {
            return;
        }
        var $btn = $(this).prop('disabled', true);
        $.ajax({
            url: '<?= base_url('delivery_config/rotate_api_key') ?>',
            type: 'POST',
            dataType: 'json',
            data: { loc_id: '<?= (int)$loc_id ?>' },
            success: function(resp) {
                $btn.prop('disabled', false);
                if (resp.status === 'Success') {
                    $('#txt_api_key_masked').text('••••' + resp.api_key.slice(-4));
                    window.prompt(resp.message + '\n\nCopiala ahora (no se vuelve a mostrar):', resp.api_key);
                } else {
                    alert(resp.message || 'No se pudo rotar la clave.');
                }
            },
            error: function() {
                $btn.prop('disabled', false);
                alert('Error al rotar la clave.');
            }
        });
    });

    // Presets de proveedores de IA
    window.aiProviderPresets = {
        gemini: {
            name: 'Google Gemini',
            models: ['gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.0-flash'],
            defaultModel: 'gemini-1.5-flash',
            keyPlaceholder: 'AIzaSy...',
            baseUrl: 'https://generativelanguage.googleapis.com',
            helpText: 'Obtenga su API Key gratuita o de pago en Google AI Studio (aistudio.google.com).'
        },
        openai: {
            name: 'OpenAI (ChatGPT)',
            models: ['gpt-4o-mini', 'gpt-4o', 'gpt-3.5-turbo'],
            defaultModel: 'gpt-4o-mini',
            keyPlaceholder: 'sk-proj-...',
            baseUrl: 'https://api.openai.com/v1',
            helpText: 'Clave secreta de OpenAI (platform.openai.com/api-keys).'
        },
        qwen: {
            name: 'Alibaba Qwen',
            models: ['qwen-turbo', 'qwen-plus', 'qwen-max', 'qwen/qwen-2.5-72b-instruct'],
            defaultModel: 'qwen-turbo',
            keyPlaceholder: 'sk-...',
            baseUrl: 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1',
            helpText: 'DashScope Alibaba Cloud o OpenRouter API Key para modelos Qwen.'
        },
        deepseek: {
            name: 'DeepSeek',
            models: ['deepseek-chat', 'deepseek-reasoner'],
            defaultModel: 'deepseek-chat',
            keyPlaceholder: 'sk-...',
            baseUrl: 'https://api.deepseek.com/v1',
            helpText: 'DeepSeek API Key (platform.deepseek.com) - Costo ultrabajo y alta calidad.'
        },
        anthropic: {
            name: 'Anthropic Claude',
            models: ['claude-3-5-sonnet-20241022', 'claude-3-5-haiku-20241022'],
            defaultModel: 'claude-3-5-sonnet-20241022',
            keyPlaceholder: 'sk-ant-...',
            baseUrl: 'https://api.anthropic.com/v1',
            helpText: 'Anthropic Console API Key (console.anthropic.com).'
        },
        groq: {
            name: 'Groq',
            models: ['llama-3.3-70b-versatile', 'mixtral-8x7b-32768', 'gemma2-9b-it'],
            defaultModel: 'llama-3.3-70b-versatile',
            keyPlaceholder: 'gsk_...',
            baseUrl: 'https://api.groq.com/openai/v1',
            helpText: 'Groq Console API Key (console.groq.com) - Velocidad instantánea en Llama 3.3.'
        },
        custom: {
            name: 'Ollama / Servidor Propio',
            models: ['qwen2.5:7b', 'llama3.2', 'mistral'],
            defaultModel: 'qwen2.5:7b',
            keyPlaceholder: 'ollama (o dejar vacío)',
            baseUrl: 'http://localhost:11434/v1',
            helpText: 'Servidor local o endpoint compatible con OpenAI (vLLM, Ollama, LM Studio, etc.).'
        }
    };

    window.handleAiProviderChange = function(provider, preserveModel) {
        var preset = window.aiProviderPresets[provider] || window.aiProviderPresets.gemini;
        $('#lbl_current_provider').text(provider.toUpperCase());
        $('#help_api_key').text(preset.helpText);
        $('#inp_ia_api_key').attr('placeholder', preset.keyPlaceholder);

        var $dl = $('#list_ia_modelos');
        $dl.empty();
        preset.models.forEach(function(m) {
            $dl.append('<option value="' + m + '">');
        });

        if (!preserveModel && preset.defaultModel) {
            $('#inp_ia_modelo').val(preset.defaultModel);
        }

        if (preset.baseUrl && !$('#inp_ia_base_url').val()) {
            $('#inp_ia_base_url').attr('placeholder', preset.baseUrl);
        }
    };

    window.toggleApiKeyVisibility = function() {
        var $inp = $('#inp_ia_api_key');
        var $ico = $('#icon_toggle_key');
        if ($inp.attr('type') === 'password') {
            $inp.attr('type', 'text');
            $ico.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $inp.attr('type', 'password');
            $ico.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    };

    // Inicializar presets según el proveedor guardado
    var initialProvider = $('#sel_ia_proveedor').val() || 'gemini';
    window.handleAiProviderChange(initialProvider, true);

    // Helpers de Plantillas y Rubros GeeksIA
    window.templatesData = <?= json_encode($templates ?? []) ?>;

    window.handleRubroConfigChange = function(key) {
        var tpl = window.templatesData[key];
        if (tpl) {
            $('#txt_rubro_desc').text(tpl.short_desc);
        }
    };

    window.cargarPromptsRecomendados = function() {
        var key = $('#sel_tipo_flujo_config').val();
        var tpl = window.templatesData[key];
        if (!tpl) return;
        if (confirm('¿Deseas reemplazar el tono y las instrucciones de IA actuales por las recomendaciones oficiales para ' + tpl.name + '?')) {
            $('input[name="ia_tono"]').val(tpl.default_tono);
            $('textarea[name="ia_instrucciones_extra"]').val(tpl.default_instrucciones);
            if (typeof toastr !== 'undefined') {
                toastr.info('Instrucciones cargadas para ' + tpl.name + '. Recuerda presionar "Guardar Toda la Configuración".');
            } else {
                alert('Instrucciones cargadas para ' + tpl.name + '. Recuerda presionar "Guardar Toda la Configuración".');
            }
        }
    };

    // ==================== HORARIOS DÍA POR DÍA ====================
    var diasKeys = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
    var diasInfo = {
        mon: { nombre: 'Lunes', corto: 'Lun' },
        tue: { nombre: 'Martes', corto: 'Mar' },
        wed: { nombre: 'Miércoles', corto: 'Mié' },
        thu: { nombre: 'Jueves', corto: 'Jue' },
        fri: { nombre: 'Viernes', corto: 'Vie' },
        sat: { nombre: 'Sábado', corto: 'Sáb' },
        sun: { nombre: 'Domingo', corto: 'Dom' }
    };

    window.toggleDiaEstado = function(key) {
        var $sw = $('#switch_dia_' + key);
        var isActivo = $sw.is(':checked');
        var $row = $('#row_dia_' + key);
        var $lbl = $('#label_dia_' + key);
        var $ap = $('#hora_apertura_' + key);
        var $ci = $('#hora_cierre_' + key);

        if (isActivo) {
            $row.removeClass('table-secondary opacity-75');
            $lbl.html('<span class="badge badge-success px-2 py-1">🟢 Abierto</span>');
            $ap.prop('disabled', false);
            $ci.prop('disabled', false);
        } else {
            $row.addClass('table-secondary opacity-75');
            $lbl.html('<span class="badge badge-secondary px-2 py-1">⛔ Cerrado</span>');
            $ap.prop('disabled', true);
            $ci.prop('disabled', true);
        }
        window.recalcularObservacionDia(key);
    };

    window.recalcularObservacionDia = function(key) {
        var $sw = $('#switch_dia_' + key);
        var $obs = $('#obs_dia_' + key);
        if (!$sw.is(':checked')) {
            $obs.html('<span class="badge badge-secondary px-2 py-1 font-weight-normal">⛔ Cerrado este día</span>');
            return;
        }

        var ap = $('#hora_apertura_' + key).val() || '18:00';
        var ci = $('#hora_cierre_' + key).val() || '23:59';

        if (ci < ap) {
            $obs.html('<span class="badge badge-warning text-dark px-2 py-1 font-weight-bold" title="Cierra en la madrugada del día siguiente"><i class="fa fa-moon-o mr-1"></i> Cierre Madrugada (+1 día)</span>');
        } else {
            $obs.html('<span class="badge badge-light border text-success px-2 py-1 font-weight-bold"><i class="fa fa-sun-o mr-1"></i> Turno Regular (' + ap + ' - ' + ci + ')</span>');
        }
    };

    window.copiarHorarioDia = function(sourceKey) {
        var ap = $('#hora_apertura_' + sourceKey).val();
        var ci = $('#hora_cierre_' + sourceKey).val();
        var sourceNombre = diasInfo[sourceKey] ? diasInfo[sourceKey].nombre : sourceKey;

        if (!confirm('¿Copiar el horario de ' + sourceNombre + ' (' + ap + ' a ' + ci + ') a todos los demás días activos?')) {
            return;
        }

        diasKeys.forEach(function(k) {
            if (k !== sourceKey) {
                $('#hora_apertura_' + k).val(ap);
                $('#hora_cierre_' + k).val(ci);
                window.recalcularObservacionDia(k);
            }
        });

        if (typeof toastr !== 'undefined') {
            toastr.success('Horario de ' + sourceNombre + ' copiado a los demás días.');
        } else {
            alert('Horario de ' + sourceNombre + ' copiado a los demás días.');
        }
    };

    window.aplicarPresetHorarios = function(preset) {
        if (preset === 'todos') {
            diasKeys.forEach(function(k) {
                $('#switch_dia_' + k).prop('checked', true);
                window.toggleDiaEstado(k);
            });
        } else if (preset === 'semana') {
            ['mon', 'tue', 'wed', 'thu', 'fri'].forEach(function(k) {
                $('#switch_dia_' + k).prop('checked', true);
                window.toggleDiaEstado(k);
            });
            ['sat', 'sun'].forEach(function(k) {
                $('#switch_dia_' + k).prop('checked', false);
                window.toggleDiaEstado(k);
            });
        } else if (preset === 'finde') {
            ['thu', 'fri', 'sat', 'sun'].forEach(function(k) {
                $('#switch_dia_' + k).prop('checked', true);
                window.toggleDiaEstado(k);
            });
            ['mon', 'tue', 'wed'].forEach(function(k) {
                $('#switch_dia_' + k).prop('checked', false);
                window.toggleDiaEstado(k);
            });
        } else if (preset === 'copiar_primer_abierto') {
            var firstKey = null;
            for (var i = 0; i < diasKeys.length; i++) {
                if ($('#switch_dia_' + diasKeys[i]).is(':checked')) {
                    firstKey = diasKeys[i];
                    break;
                }
            }
            if (firstKey) {
                window.copiarHorarioDia(firstKey);
            } else {
                alert('No hay ningún día abierto para tomar como referencia.');
            }
            return;
        } else if (preset === 'cerrar_todos') {
            if (confirm('¿Deseas marcar todos los días como cerrados?')) {
                diasKeys.forEach(function(k) {
                    $('#switch_dia_' + k).prop('checked', false);
                    window.toggleDiaEstado(k);
                });
            }
        }

        window.generarTextoResumenHorarios(false);
    };

    window.generarTextoResumenHorarios = function(userAction) {
        var grupos = {};
        var cerrados = [];

        diasKeys.forEach(function(k) {
            var isAct = $('#switch_dia_' + k).is(':checked');
            var nombre = diasInfo[k].nombre;
            if (!isAct) {
                cerrados.push(nombre);
            } else {
                var ap = $('#hora_apertura_' + k).val() || '18:00';
                var ci = $('#hora_cierre_' + k).val() || '23:59';
                var keyHoras = ap + ' a ' + ci;
                if (!grupos[keyHoras]) {
                    grupos[keyHoras] = [];
                }
                grupos[keyHoras].push(nombre);
            }
        });

        var resumenPartes = [];
        var keysHoras = Object.keys(grupos);
        if (cerrados.length === 0 && keysHoras.length === 1) {
            resumenPartes.push('Lunes a Domingo de ' + keysHoras[0]);
        } else if (cerrados.length === 7) {
            resumenPartes.push('Cerrado temporalmente');
        } else {
            keysHoras.forEach(function(h) {
                var dList = grupos[h];
                var diasTxt = '';
                if (dList.length === 5 && dList[0] === 'Lunes' && dList[4] === 'Viernes') {
                    diasTxt = 'Lunes a Viernes';
                } else if (dList.length === 4 && dList[0] === 'Jueves' && dList[3] === 'Domingo') {
                    diasTxt = 'Jueves a Domingo';
                } else if (dList.length === 2) {
                    diasTxt = dList[0] + ' y ' + dList[1];
                } else if (dList.length > 2) {
                    diasTxt = dList.slice(0, -1).join(', ') + ' y ' + dList[dList.length - 1];
                } else {
                    diasTxt = dList[0];
                }
                resumenPartes.push(diasTxt + ' ' + h);
            });

            if (cerrados.length > 0 && cerrados.length < 7) {
                if (cerrados.length === 1) {
                    resumenPartes.push('(' + cerrados[0] + ' cerrado)');
                } else if (cerrados.length === 2) {
                    resumenPartes.push('(' + cerrados[0] + ' y ' + cerrados[1] + ' cerrado)');
                } else {
                    resumenPartes.push('(' + cerrados.join(', ') + ' cerrado)');
                }
            }
        }

        var textoFinal = resumenPartes.join(', ');
        if (userAction || !$('#horario_atencion_input').val()) {
            $('#horario_atencion_input').val(textoFinal);
            if (userAction) {
                if (typeof toastr !== 'undefined') {
                    toastr.info('Resumen generado: ' + textoFinal);
                }
            }
        }
        return textoFinal;
    };

    // Inicializar observaciones de cada día al cargar la vista
    diasKeys.forEach(function(k) {
        window.recalcularObservacionDia(k);
    });

    // Función para mostrar Modal QR de vinculación
    window.showDriverQr = function(slot) {
        var nombre = $('input[name="slot_' + slot + '_nombre"]').val() || ('Repartidor ' + slot);
        var telefono = $('input[name="slot_' + slot + '_telefono"]').val() || '';
        var pin = $('#slot_' + slot + '_pin').val() || '1234';
        var tunnelUrl = 'https://compatible-hood-profiles-savannah.trycloudflare.com';

        // Formato: GEEKS_DELIVERY|url|phone|pin|slot
        var payload = 'GEEKS_DELIVERY|' + tunnelUrl + '|' + (telefono || slot) + '|' + pin + '|' + slot;
        var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&margin=10&data=' + encodeURIComponent(payload);

        $('#qrModalTitle').html('<i class="fa fa-qrcode mr-2"></i> Vincular Slot #' + slot + ': ' + nombre);
        $('#qrDriverName').text(nombre + ' (Slot #' + slot + ')');
        $('#qrDriverPhone').text(telefono ? telefono : ('Slot #' + slot));
        $('#qrDriverPin').text(pin);
        $('#qrImageDisplay').attr('src', qrUrl);

        // Prepara mensaje de WhatsApp
        var cleanPhone = telefono.replace(/[^0-9]/g, '');
        if (cleanPhone.indexOf('09') === 0) cleanPhone = '595' + cleanPhone.substring(1);

        var msg = '🛵 *Geeks Delivery - Acceso de Repartidor*\n\n' +
                  'Hola *' + nombre + '*, estos son tus datos para ingresar a la App Móvil de Delivery:\n\n' +
                  '📱 *Teléfono o Slot:* ' + (telefono || slot) + '\n' +
                  '🔑 *Tu PIN:* ' + pin + '\n' +
                  '🌐 *Servidor:* ' + tunnelUrl + '\n\n' +
                  '👉 *O abre la App y presiona "INGRESAR CON CÓDIGO QR" para entrar automáticamente.*';

        var waLink = 'https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(msg);
        $('#btnSendWhatsappMsg').attr('href', waLink);

        $('#modalDriverQr').modal('show');
    };

    // Función para enviar credenciales por WhatsApp directamente
    window.sendDriverWhatsapp = function(slot) {
        window.showDriverQr(slot);
        var telefono = $('input[name="slot_' + slot + '_telefono"]').val() || '';
        if (telefono) {
            window.open($('#btnSendWhatsappMsg').attr('href'), '_blank');
        }
    };
});
</script>

<!-- Modal QR Vinculación Repartidor -->
<div class="modal fade" id="modalDriverQr" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-directional-primary text-white py-2">
                <h5 class="modal-title font-weight-bold" id="qrModalTitle"><i class="fa fa-qrcode mr-2"></i> Vincular App Repartidor</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="bg-light p-3 rounded-lg border mb-3 d-inline-block shadow-sm">
                    <img id="qrImageDisplay" src="" alt="Código QR" style="width: 230px; height: 230px; display: block; margin: 0 auto;">
                </div>
                <h5 class="font-weight-bold text-dark mb-1" id="qrDriverName">Repartidor 1</h5>
                <p class="text-muted small mb-3">Escanea este código con la App <strong>Geeks Delivery</strong> para iniciar sesión al instante sin escribir nada.</p>
                
                <div class="row text-left bg-light rounded p-2 mx-1 mb-3 border">
                    <div class="col-6">
                        <small class="text-muted d-block font-weight-bold">Teléfono o Slot:</small>
                        <strong class="text-primary font-medium-1" id="qrDriverPhone">595981...</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block font-weight-bold">PIN Secreto (4 dígitos):</small>
                        <strong class="text-danger font-medium-2" id="qrDriverPin">1234</strong>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <a id="btnSendWhatsappMsg" href="#" target="_blank" class="btn btn-success font-weight-bold">
                        <i class="fa fa-whatsapp mr-1"></i> Enviar Acceso por WhatsApp
                    </a>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
