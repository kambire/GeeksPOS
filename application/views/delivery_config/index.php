<link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/vendors/css/extensions/toastr.css">
<script src="<?= assets_url() ?>app-assets/vendors/js/extensions/toastr.min.js"></script>

<style>
.option-tarifa-card {
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}
.option-tarifa-card:hover {
    border-color: #007bff !important;
    background-color: #f8faff !important;
}
.bg-blue-light {
    background-color: #eff6ff !important;
}
.shadow-xs {
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.tramo-row td {
    vertical-align: middle !important;
}
.promo-card {
    border-radius: 12px;
    transition: all 0.2s ease-in-out;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff;
}
.promo-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.08) !important;
    border-color: #cbd5e1 !important;
}
.promo-card.promo-inactive {
    opacity: 0.75;
    background: #f8fafc;
}
.btn-day-pill {
    font-size: 11px;
    padding: 3px 8px;
    font-weight: 600;
    border-radius: 6px !important;
    margin-right: 2px;
}
.btn-day-pill.active {
    background-color: #e11d48 !important;
    border-color: #e11d48 !important;
    color: #ffffff !important;
}
.promo-img-preview-wrapper {
    position: relative;
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    transition: border-color 0.2s;
    background: #f8fafc;
}
.promo-img-preview-wrapper:hover {
    border-color: #e11d48;
}
.promo-preview-img {
    border-radius: 6px;
    max-height: 165px;
    object-fit: contain;
    transition: transform 0.2s ease;
}
.promo-preview-img:hover {
    transform: scale(1.03);
}

/* UI/UX REVOLUTION - EXECUTIVE SAAS HEADER & MODERN SEGMENTED TABS */
.header-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
}
.avatar-store-chip {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #e0f2fe;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.segmented-tabs-wrapper {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 5px;
    margin-bottom: 22px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.segmented-tabs-wrapper::-webkit-scrollbar {
    display: none;
}
.modern-segmented-pills {
    display: flex;
    flex-wrap: nowrap;
    gap: 4px;
    border: none;
    margin: 0;
    padding: 0;
    list-style: none;
}
@media (min-width: 1200px) {
    .modern-segmented-pills {
        flex-wrap: wrap;
    }
}
.modern-segmented-pills .nav-item {
    margin: 0;
}
.modern-segmented-pills .nav-link {
    border: 1px solid transparent !important;
    border-radius: 10px !important;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    background: transparent !important;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    gap: 7px;
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
}
.modern-segmented-pills .nav-link:hover {
    background: rgba(255, 255, 255, 0.85) !important;
    color: #0f172a !important;
    transform: translateY(-1px);
}
.modern-segmented-pills .nav-link.active {
    background: #ffffff !important;
    color: #0284c7 !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
    font-weight: 700 !important;
}
.modern-segmented-pills .nav-link i {
    font-size: 14px;
    transition: transform 0.2s;
}
.modern-segmented-pills .nav-link.active i {
    transform: scale(1.12);
}
.tab-counter-badge {
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 10px;
    font-weight: 700;
}
</style>

<div class="content-body">
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
        <!-- Top Executive Command Header -->
        <div class="card-header border-bottom py-3 px-3 px-md-4" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
            <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
                <div class="d-flex align-items-center">
                    <div class="header-icon-box mr-3">
                        <i class="fa fa-sliders text-white" style="font-size: 20px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <h4 class="font-weight-bold mb-0 text-dark" style="letter-spacing: -0.3px;">
                                Configuración de Delivery, WhatsApp & Bot IA
                            </h4>
                            <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 11px; border-radius: 6px;">PRODUCCIÓN</span>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Control integral de turnos, teléfonos, tarifas, métodos de pago, motor conversacional y catálogo digital.
                        </small>
                    </div>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <?php if ($is_superadmin && !empty($all_locations)): ?>
                        <div class="d-inline-flex align-items-center bg-white border rounded px-2 py-1 shadow-xs" style="border-color: #cbd5e1 !important;">
                            <i class="fa fa-building-o text-muted mr-1" style="font-size: 13px;"></i>
                            <select id="select_location_store" class="border-0 font-weight-bold text-dark" style="outline: none; font-size: 12.5px; background: transparent; cursor: pointer;" onchange="window.location.href='<?= base_url('delivery_config/index/') ?>' + this.value;">
                                <?php foreach ($all_locations as $loc_item): ?>
                                    <option value="<?= $loc_item['id'] ?>" <?= ($loc_item['id'] == $loc_id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc_item['cname']) ?> (ID: <?= $loc_item['id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                                <option value="0" <?= ($loc_id == 0) ? 'selected' : '' ?>>🏢 Principal / Matriz (0)</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <button type="button" class="btn btn-sm btn-success font-weight-bold shadow-xs px-3" onclick="$('#btn_save_config').click();" title="Guardar todos los cambios realizados en cualquier pestaña">
                        <i class="fa fa-save mr-1"></i> Guardar Todo
                    </button>

                    <a href="<?= base_url('delivery_config/orders/' . $loc_id) ?>" class="btn btn-sm btn-warning font-weight-bold shadow-xs">
                        <i class="fa fa-motorcycle mr-1"></i> Monitor & Reasignar
                    </a>

                    <a href="<?= base_url('saas') ?>" class="btn btn-sm btn-outline-secondary font-weight-bold">
                        <i class="fa fa-arrow-left mr-1"></i> Volver a SaaS
                    </a>
                </div>
            </div>
        </div>

        <!-- Integrated Unified Store Info & WebApp Ribbon -->
        <?php 
        $loc_slug = $loc_id > 0 ? (url_title($location['cname'] ?? '', 'dash', true) ?: $loc_id) : '1';
        $full_p_url = base_url('p/' . ($loc_id > 0 ? $loc_id : '1'));
        $friendly_p_url = base_url('p/' . $loc_slug);
        ?>
        <div class="card-body py-2 px-3 px-md-4 border-bottom" style="background: #f8fafc;">
            <div class="row align-items-center">
                <!-- Local Info & Status -->
                <div class="col-xl-6 col-lg-7 mb-2 mb-xl-0">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                        <div class="d-flex align-items-center">
                            <span class="avatar-store-chip mr-2">
                                <i class="fa fa-cutlery text-primary"></i>
                            </span>
                            <div>
                                <strong class="text-dark font-medium-1 d-block mb-0"><?= htmlspecialchars($location['cname'] ?? 'Local ' . $loc_id) ?></strong>
                                <span class="text-muted small">Sucursal ID: <strong class="text-dark">#<?= $loc_id ?></strong></span>
                            </div>
                        </div>

                        <span class="badge badge-<?= $rubro_info['badge_color'] ?? 'primary' ?> px-2 py-1 font-weight-bold" style="font-size:11.5px; border-radius: 8px;">
                            <i class="<?= $rubro_info['icon'] ?? 'fa fa-sitemap' ?> mr-1"></i> <?= $rubro_info['badge'] ?? '🍔 Gastronomía' ?>
                        </span>

                        <span class="badge badge-light border text-success px-2 py-1 font-weight-bold shadow-xs" style="font-size:11.5px; border-radius: 8px;" title="Instancia activa de WhatsApp conectada">
                            <i class="fa fa-whatsapp mr-1 text-success"></i> <?= htmlspecialchars($location['whatsapp_instance'] ?? 'pos_whatsapp') ?>
                        </span>

                        <?php if ($is_superadmin && $loc_id > 0): ?>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('saas/edit/' . $loc_id) ?>" class="btn btn-xs btn-outline-warning font-weight-bold" title="Editar Perfil, Usuario y Contraseña del Local">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold" onclick="modalEliminarLocalActual(<?= (int)$loc_id ?>, <?= htmlspecialchars(json_encode($location['cname'] ?? ('Local ' . $loc_id)), ENT_QUOTES, 'UTF-8') ?>)" title="Eliminar permanentemente este local y todos sus datos">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- WebApp Link & API Key Controls -->
                <div class="col-xl-6 col-lg-5">
                    <div class="d-flex align-items-center justify-content-xl-end flex-wrap" style="gap: 8px;">
                        <!-- WebApp Input & Copy -->
                        <div class="d-inline-flex align-items-center bg-white border rounded px-2 py-1 shadow-xs" style="max-width: 320px; border-color: #cbd5e1 !important;">
                            <i class="fa fa-globe text-primary mr-1" style="font-size: 13px;"></i>
                            <input type="text" class="border-0 font-weight-bold text-dark font-monospace" id="inp_webapp_url" value="<?= $friendly_p_url ?>" readonly style="font-size: 11.5px; width: 180px; outline: none; background: transparent;">
                            <button class="btn btn-xs btn-light font-weight-bold border-0 text-primary px-2" type="button" onclick="navigator.clipboard.writeText($('#inp_webapp_url').val()); toastr.success('¡Enlace de la WebApp copiado!');" title="Copiar enlace">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>

                        <a href="<?= $friendly_p_url ?>" target="_blank" class="btn btn-sm btn-outline-success font-weight-bold shadow-xs px-2" title="Abrir WebApp del cliente">
                            <i class="fa fa-external-link mr-1"></i> WebApp
                        </a>

                        <!-- API Key masked -->
                        <div class="d-inline-flex align-items-center bg-white border rounded px-2 py-1 shadow-xs" style="border-color: #cbd5e1 !important;" title="API Key de este local para GeeksIA">
                            <span class="text-muted mr-1 font-monospace" style="font-size: 11px;">API:</span>
                            <code class="font-weight-bold text-dark" id="txt_api_key_masked" style="font-size: 11px;"><?= !empty($location['api_key']) ? '••••' . htmlspecialchars(substr($location['api_key'], -4)) : 'sin clave' ?></code>
                            <?php if ($loc_id > 0): ?>
                                <button type="button" class="btn btn-xs btn-link text-danger p-0 ml-1" id="btn_rotate_api_key" title="Rotar API Key">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <form id="form_delivery_config" method="post" action="<?= base_url('delivery_config/save') ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="loc_id" value="<?= $loc_id ?>">

                <!-- SEGMENTED MODERN NAVIGATION TABS -->
                <div class="segmented-tabs-wrapper mb-3" id="configTabsWrapper">
                    <ul class="nav nav-pills modern-segmented-pills" id="configTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-turnos-btn" data-toggle="tab" href="#tab-turnos" role="tab">
                                <i class="fa fa-motorcycle text-danger"></i>
                                <span>Repartidores</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-telefonos-btn" data-toggle="tab" href="#tab-telefonos" role="tab">
                                <i class="fa fa-phone text-primary"></i>
                                <span>Teléfonos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-horarios-btn" data-toggle="tab" href="#tab-horarios" role="tab">
                                <i class="fa fa-clock-o text-warning"></i>
                                <span>Horarios</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-tarifas-btn" data-toggle="tab" href="#tab-tarifas" role="tab">
                                <i class="fa fa-truck text-info"></i>
                                <span>Tarifas & Radio</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-banco-btn" data-toggle="tab" href="#tab-banco" role="tab">
                                <i class="fa fa-bank text-success"></i>
                                <span>Pagos & Bancos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-ubicacion-btn" data-toggle="tab" href="#tab-ubicacion" role="tab">
                                <i class="fa fa-map-marker text-danger"></i>
                                <span>Ubicación GPS</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-ia-btn" data-toggle="tab" href="#tab-ia" role="tab">
                                <i class="fa fa-android" style="color: #7c3aed;"></i>
                                <span>Bot IA & Motor</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-promos-btn" data-toggle="tab" href="#tab-promos" role="tab">
                                <i class="fa fa-bullhorn text-danger"></i>
                                <span>Promociones & Flyers</span>
                                <span class="badge badge-pill badge-danger tab-counter-badge" id="tab_promos_badge"><?= count($config['promos_list'] ?? []) ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-geeksia-btn" data-toggle="tab" href="#tab-geeksia" role="tab">
                                <i class="fa fa-code text-dark"></i>
                                <span>Live JSON</span>
                            </a>
                        </li>
                    </ul>
                </div>

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
                                                <th style="min-width: 180px;">Nombre del Repartidor</th>
                                                <th style="min-width: 140px;">Usuario App</th>
                                                <th style="min-width: 170px;">Correo Electrónico</th>
                                                <th style="min-width: 150px;">Contraseña App</th>
                                                <th style="min-width: 160px;">Teléfono WhatsApp</th>
                                                <th style="width: 100px;" class="text-center">Estado</th>
                                                <th style="width: 120px;" class="text-center">Disponibilidad</th>
                                                <th style="width: 100px;" class="text-center">Pedidos Hoy</th>
                                                <th style="min-width: 140px;">Última Salida</th>
                                                <th style="width: 130px;" class="text-center">Acciones</th>
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
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user text-primary"></i></span></div>
                                                        <input type="text" class="form-control form-control-sm font-weight-bold"
                                                               name="slot_<?= $s ?>_usuario"
                                                               value="<?= htmlspecialchars($d['username'] ?? ('delivery' . $s)) ?>"
                                                               placeholder="delivery<?= $s ?>">
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-envelope text-info"></i></span></div>
                                                        <input type="email" class="form-control form-control-sm"
                                                               name="slot_<?= $s ?>_email"
                                                               value="<?= htmlspecialchars($d['email'] ?? ('delivery' . $s . '@geekspos.com')) ?>"
                                                               placeholder="delivery<?= $s ?>@geekspos.com">
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock text-warning"></i></span></div>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="slot_<?= $s ?>_password"
                                                               placeholder="Dejar en blanco para no cambiar">
                                                    </div>
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
                                                <td class="text-center align-middle" style="white-space: nowrap;">
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="showDriverQr(<?= $s ?>)" title="Ver Código QR para escanear en la App">
                                                        <i class="fa fa-qrcode mr-1"></i> QR
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success ml-1" onclick="sendDriverWhatsapp(<?= $s ?>)" title="Enviar credenciales por WhatsApp">
                                                        <i class="fa fa-whatsapp"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ml-1" onclick="clearSlot(<?= $s ?>)" title="Vaciar y desvincular repartidor de este slot">
                                                        <i class="fa fa-trash-o"></i>
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 text-white font-weight-bold">
                                        <i class="fa fa-truck mr-1"></i> 4. Tarifas de Delivery, Rangos de Distancia & Cobertura
                                    </h5>
                                    <span class="badge badge-light text-dark font-weight-bold">Configuración GeeksIA</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- 1. SELECTOR DE MODALIDAD DE TARIFA -->
                                <div class="mb-4 p-3 bg-light rounded border">
                                    <label class="font-weight-bold text-dark d-block mb-2" style="font-size:15px;">
                                        <i class="fa fa-sliders text-primary mr-1"></i> Modalidad de Cálculo de Tarifa de Envío:
                                    </label>
                                    <div class="row">
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <div class="custom-control custom-radio p-3 border rounded bg-white h-100 shadow-xs cursor-pointer option-tarifa-card" id="card_tarifa_tramos">
                                                <input type="radio" id="tarifa_tramos" name="tarifa_tipo" value="tramos" class="custom-control-input" <?= (($config['tarifa_tipo'] ?? 'tramos') === 'tramos') ? 'checked' : '' ?>>
                                                <label class="custom-control-label font-weight-bold text-dark cursor-pointer d-block" for="tarifa_tramos">
                                                    <span class="badge badge-success mb-1">Recomendado</span><br>
                                                    Por Rangos / Tramos Escalonados
                                                </label>
                                                <small class="text-muted d-block mt-1">
                                                    Tarifas fijas por tramos de distancia (ej: 0 a 1.5 km = 5.000 Gs, 1.5 a 4 km = 10.000 Gs, etc.).
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <div class="custom-control custom-radio p-3 border rounded bg-white h-100 shadow-xs cursor-pointer option-tarifa-card" id="card_tarifa_km">
                                                <input type="radio" id="tarifa_km" name="tarifa_tipo" value="km" class="custom-control-input" <?= (($config['tarifa_tipo'] ?? '') === 'km') ? 'checked' : '' ?>>
                                                <label class="custom-control-label font-weight-bold text-dark cursor-pointer d-block" for="tarifa_km">
                                                    Tarifa Base + Costo por Km
                                                </label>
                                                <small class="text-muted d-block mt-1">
                                                    Cobra un monto base hasta X km y luego una tarifa fija por cada km adicional recorrido en ruta.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="custom-control custom-radio p-3 border rounded bg-white h-100 shadow-xs cursor-pointer option-tarifa-card" id="card_tarifa_hibrido">
                                                <input type="radio" id="tarifa_hibrido" name="tarifa_tipo" value="hibrido" class="custom-control-input" <?= (($config['tarifa_tipo'] ?? '') === 'hibrido') ? 'checked' : '' ?>>
                                                <label class="custom-control-label font-weight-bold text-dark cursor-pointer d-block" for="tarifa_hibrido">
                                                    Híbrido (Tramos + Km Extra)
                                                </label>
                                                <small class="text-muted d-block mt-1">
                                                    Aplica tramos fijos y, si el cliente supera el último tramo dentro del radio de cobertura, suma por km.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. PANEL: TRAMOS DE DISTANCIA ESCALONADOS -->
                                <div id="panel_tramos_container" class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-0">
                                                <i class="fa fa-list-ol text-info mr-1"></i> Tramos Escalonados de Distancia (De X a X Distancia = X Monto)
                                            </h6>
                                            <small class="text-muted">Define los rangos de entrega y el precio exacto en guaraníes para cada distancia.</small>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" id="btn_reset_default_tramos">
                                                <i class="fa fa-refresh mr-1"></i> Cargar Tramos Sugeridos
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary font-weight-bold shadow-sm" id="btn_add_tramo">
                                                <i class="fa fa-plus-circle mr-1"></i> Agregar Tramo
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover bg-white mb-2 shadow-xs" id="table_tramos_delivery">
                                            <thead class="thead-light">
                                                <tr class="text-center font-weight-bold" style="font-size:13px;">
                                                    <th style="width: 50px;">#</th>
                                                    <th style="width: 140px;">Desde (Km)</th>
                                                    <th style="width: 140px;">Hasta (Km)</th>
                                                    <th style="width: 160px;">Rango Cobertura</th>
                                                    <th style="width: 200px;">Costo de Envío (Gs)</th>
                                                    <th>Zona / Referencia (Opcional)</th>
                                                    <th style="width: 70px;">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_tramos_delivery">
                                                <?php 
                                                $tramos_list = $config['tarifas_tramos_list'] ?? [];
                                                if (empty($tramos_list)) {
                                                    $tramos_list = [
                                                        ['desde' => 0.0, 'hasta' => 1.5, 'costo' => 5000, 'etiqueta' => 'Zona 1 (Barrio cercano)'],
                                                        ['desde' => 1.5, 'hasta' => 4.0, 'costo' => 10000, 'etiqueta' => 'Zona 2 (Estándar)'],
                                                        ['desde' => 4.0, 'hasta' => 7.0, 'costo' => 15000, 'etiqueta' => 'Zona 3 (Media distancia)'],
                                                        ['desde' => 7.0, 'hasta' => 10.0, 'costo' => 20000, 'etiqueta' => 'Zona 4 (Límite urbano)'],
                                                        ['desde' => 10.0, 'hasta' => 15.0, 'costo' => 25000, 'etiqueta' => 'Zona 5 (Interurbano / Lejano)']
                                                    ];
                                                }
                                                foreach ($tramos_list as $i => $tr): 
                                                    $idx = $i + 1;
                                                ?>
                                                <tr class="tramo-row align-middle text-center" data-index="<?= $idx ?>">
                                                    <td class="font-weight-bold text-muted tramo-num"><?= $idx ?></td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" step="0.1" min="0" class="form-control text-center font-weight-bold input-tramo-desde" name="tramos_desde[]" value="<?= htmlspecialchars($tr['desde']) ?>" required>
                                                            <div class="input-group-append"><span class="input-group-text">km</span></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" step="0.1" min="0.1" class="form-control text-center font-weight-bold input-tramo-hasta" name="tramos_hasta[]" value="<?= htmlspecialchars($tr['hasta']) ?>" required>
                                                            <div class="input-group-append"><span class="input-group-text">km</span></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info p-2 font-weight-bold badge-tramo-rango" style="font-size:12px;">
                                                            <?= number_format((float)$tr['desde'], 1) ?> a <?= number_format((float)$tr['hasta'], 1) ?> km
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" step="500" min="0" class="form-control text-right font-weight-bold text-success input-tramo-costo" name="tramos_costo[]" value="<?= (int)$tr['costo'] ?>" required style="font-size:14px;">
                                                            <div class="input-group-append"><span class="input-group-text font-weight-bold">Gs</span></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" name="tramos_etiqueta[]" value="<?= htmlspecialchars($tr['etiqueta'] ?? '') ?>" placeholder="Ej: Zona 1, Barrio Centro, Luque Centro...">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-tramo" title="Eliminar este tramo">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="fa fa-info-circle mr-1 text-info"></i> La distancia se calcula automáticamente por ruta real en calles (GPS) cuando el cliente envía su ubicación en WhatsApp o en la WebApp.
                                    </small>
                                </div>

                                <!-- 3. PANEL: TARIFA POR KILÓMETRO -->
                                <div id="panel_por_km_container" class="mb-4 p-3 bg-light border rounded" style="display:none;">
                                    <h6 class="font-weight-bold text-dark mb-2">
                                        <i class="fa fa-tachometer text-warning mr-1"></i> Parámetros de Tarifa Por Kilómetro
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="font-weight-bold text-dark">Costo Base de Envío (Gs):</label>
                                            <div class="input-group">
                                                <input type="number" step="500" class="form-control font-weight-bold" name="costo_envio_base" id="costo_envio_base" value="<?= htmlspecialchars($config['costo_envio_base'] ?? '10000') ?>">
                                                <div class="input-group-append"><span class="input-group-text">Gs</span></div>
                                            </div>
                                            <small class="text-muted">Monto mínimo cobrado por el delivery.</small>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="font-weight-bold text-dark">Distancia Base Incluida (Km):</label>
                                            <div class="input-group">
                                                <input type="number" step="0.5" min="0" class="form-control font-weight-bold" name="km_base" id="km_base" value="<?= htmlspecialchars($config['km_base'] ?? '3.00') ?>">
                                                <div class="input-group-append"><span class="input-group-text">Km</span></div>
                                            </div>
                                            <small class="text-muted">Hasta esta distancia solo se cobra el costo base.</small>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="font-weight-bold text-dark">Costo Adicional por Km (Gs):</label>
                                            <div class="input-group">
                                                <input type="number" step="500" class="form-control font-weight-bold text-primary" name="costo_por_km" id="costo_por_km" value="<?= htmlspecialchars($config['costo_por_km'] ?? '2000') ?>">
                                                <div class="input-group-append"><span class="input-group-text">Gs / Km</span></div>
                                            </div>
                                            <small class="text-muted">Por cada km adicional que supere la distancia base.</small>
                                        </div>
                                    </div>
                                    <div class="p-2 mt-2 bg-white rounded border border-warning">
                                        <small class="text-dark font-weight-bold">
                                            <i class="fa fa-lightbulb-o text-warning mr-1"></i> Fórmula de Cobro:
                                        </small>
                                        <small class="text-muted">
                                            Si el cliente está a <span class="font-weight-bold text-dark">5.0 km</span> y la base cubre <span class="font-weight-bold text-dark">3.0 km</span> por <span class="font-weight-bold text-dark">10.000 Gs</span>:
                                            10.000 Gs base + (2.0 km extra x 2.000 Gs) = <span class="badge badge-success font-weight-bold">14.000 Gs Total</span>.
                                        </small>
                                    </div>
                                </div>

                                <!-- 4. PROBADOR / SIMULADOR INTERACTIVO EN TIEMPO REAL -->
                                <div class="card border border-info shadow-xs mb-4" style="background-color: #f0fdf4;">
                                    <div class="card-body py-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-5">
                                                <h6 class="font-weight-bold text-dark mb-1">
                                                    <i class="fa fa-calculator text-success mr-1"></i> Probador / Simulador en Vivo de Tarifas
                                                </h6>
                                                <p class="text-muted small mb-0">Escribe o desliza una distancia para comprobar cómo responderá el bot a tus clientes:</p>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-map-marker text-danger"></i></span></div>
                                                    <input type="number" step="0.1" min="0" max="30" class="form-control font-weight-bold text-center" id="sim_km_input" value="3.5">
                                                    <div class="input-group-append"><span class="input-group-text">Km</span></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                                                <div class="d-inline-block text-left p-2 bg-white rounded border border-success shadow-xs">
                                                    <div class="small text-muted font-weight-bold">Tarifa Calculada:</div>
                                                    <div class="h5 mb-0 font-weight-bold text-success" id="sim_resultado_costo">10.000 Gs</div>
                                                    <small class="text-muted d-block" id="sim_resultado_detalle">Tramo de 1.5 a 4.0 km (Zona 2)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. COBERTURA GLOBAL Y TIEMPOS ESTIMADOS -->
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Radio Máximo de Cobertura (Km):</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" class="form-control font-weight-bold" name="radio_max_km" id="radio_max_km" value="<?= htmlspecialchars($config['radio_max_km'] ?? '10.00') ?>">
                                            <div class="input-group-append"><span class="input-group-text">Km</span></div>
                                        </div>
                                        <small class="text-muted">Distancia máxima. Si el cliente está más lejos, la IA le ofrecerá retiro en el local.</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Tiempo Estimado de Entrega (Delivery):</label>
                                        <input type="text" class="form-control font-weight-bold" name="tiempo_entrega_min" value="<?= htmlspecialchars($config['tiempo_entrega_min'] ?? '35 a 45 minutos') ?>" placeholder="Ej: 35 a 45 minutos">
                                        <small class="text-muted">Tiempo promedio que la IA promete al cliente para envíos.</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark">Tiempo Estimado de Retiro (Take Away):</label>
                                        <input type="text" class="form-control font-weight-bold" name="tiempo_retiro_min" value="<?= htmlspecialchars($config['tiempo_retiro_min'] ?? '15 a 20 minutos') ?>" placeholder="Ej: 15 a 20 minutos">
                                        <small class="text-muted">Tiempo para pedidos retirados en mostrador.</small>
                                    </div>

                                    <div class="col-md-12 mb-2">
                                        <div class="custom-control custom-switch p-3 bg-light border rounded">
                                            <input type="checkbox" class="custom-control-input" id="switch_delivery_cerrado" name="is_delivery_cerrado" value="1" <?= (!empty($config['is_delivery_cerrado'])) ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-bold text-warning" for="switch_delivery_cerrado" style="font-size:14px;">
                                                🛵 Pausar Solo Delivery Hoy (Aceptar únicamente pedidos para retiro / pasar a buscar)
                                            </label>
                                            <small class="d-block text-muted">Útil en días de lluvia torrencial, contingencia o falta temporal de repartidores.</small>
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

                    <!-- ==================== TAB 7: BOT IA & PROVEEDORES ==================== -->
                    <div class="tab-pane fade" id="tab-ia" role="tabpanel">
                        <div class="card shadow-sm border mb-3" style="border-radius: 14px; overflow: hidden;">
                            <div class="card-header text-white py-2 px-3" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <h5 class="mb-0 text-white font-weight-bold">
                                        <i class="fa fa-android mr-1"></i> Configuración de Inteligencia Artificial & Motor GeeksIA
                                    </h5>
                                    <span class="badge badge-light font-weight-bold px-2 py-1 shadow-xs" style="color: #6d28d9; font-size: 11px;">
                                        MULTIMODAL & AGENTES
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-3 p-md-4">

                                <!-- SECCIÓN 1: PROVEEDOR Y API KEY EXCLUSIVA DE ESTE LOCAL -->
                                <div class="card mb-3 border shadow-xs" style="border-radius: 12px; background: #faf5ff; border-color: #e9d5ff !important;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                                            <div>
                                                <h6 class="font-weight-bold mb-0" style="color:#6b21a8;">
                                                    <i class="fa fa-key mr-1"></i> Proveedor de IA & Credenciales API
                                                </h6>
                                                <small class="text-muted">Cada sucursal puede utilizar su propio proveedor y clave API independiente.</small>
                                            </div>
                                            <div class="mt-1 mt-md-0">
                                                <span class="badge badge-primary px-3 py-1 font-weight-bold shadow-xs" id="lbl_current_provider" style="font-size:12px; letter-spacing: 0.5px;">
                                                    <?= strtoupper($config['ia_proveedor'] ?? 'GEMINI') ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <!-- Proveedor de IA -->
                                            <div class="col-md-4 mb-3">
                                                <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">Proveedor de IA:</label>
                                                <select class="form-control font-weight-bold" name="ia_proveedor" id="sel_ia_proveedor" onchange="handleAiProviderChange(this.value)">
                                                    <option value="gemini" <?= (@$config['ia_proveedor'] == 'gemini') ? 'selected' : '' ?>>🔵 Google Gemini (Recomendado / Económico)</option>
                                                    <option value="openai" <?= (@$config['ia_proveedor'] == 'openai') ? 'selected' : '' ?>>🟢 OpenAI (ChatGPT / GPT-4o / GPT-4.1-mini)</option>
                                                    <option value="qwen" <?= (@$config['ia_proveedor'] == 'qwen') ? 'selected' : '' ?>>🟣 Alibaba Qwen (DashScope / OpenRouter)</option>
                                                    <option value="deepseek" <?= (@$config['ia_proveedor'] == 'deepseek') ? 'selected' : '' ?>>🐋 DeepSeek (DeepSeek-V3 / R1)</option>
                                                    <option value="anthropic" <?= (@$config['ia_proveedor'] == 'anthropic') ? 'selected' : '' ?>>🟠 Anthropic Claude (Claude 3.5 Sonnet)</option>
                                                    <option value="groq" <?= (@$config['ia_proveedor'] == 'groq') ? 'selected' : '' ?>>⚡ Groq (Llama 3.3 Ultra Rápido)</option>
                                                    <option value="custom" <?= (@$config['ia_proveedor'] == 'custom') ? 'selected' : '' ?>>⚙️ Ollama / Servidor Propio (Compatible OpenAI)</option>
                                                </select>
                                            </div>

                                            <!-- API Key -->
                                            <div class="col-md-5 mb-3">
                                                <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">API Key Secreta del Proveedor:</label>
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
                                                <small class="text-muted d-block mt-1" id="help_api_key">Clave privada de la IA para este local.</small>
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
                                                <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">Modelo de IA:</label>
                                                <input type="text" class="form-control font-monospace font-weight-bold" name="ia_modelo" id="inp_ia_modelo" list="list_ia_modelos"
                                                       value="<?= htmlspecialchars($config['ia_modelo'] ?? 'gemini-1.5-flash') ?>"
                                                       placeholder="Ej: gemini-1.5-flash">
                                                <datalist id="list_ia_modelos">
                                                    <!-- Opciones dinámicas según proveedor -->
                                                </datalist>
                                            </div>

                                            <!-- URL Base / Endpoint -->
                                            <div class="col-md-6 mb-2" id="wrap_ia_base_url">
                                                <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">URL Base / Endpoint Personalizado (Opcional):</label>
                                                <input type="text" class="form-control font-monospace" name="ia_base_url" id="inp_ia_base_url"
                                                       value="<?= htmlspecialchars($config['ia_base_url'] ?? '') ?>"
                                                       placeholder="https://api.openai.com/v1 o http://localhost:11434/v1">
                                                <small class="text-muted">Dejar vacío para usar la URL oficial del proveedor.</small>
                                            </div>

                                            <!-- Temperatura y Max Tokens -->
                                            <div class="col-md-3 mb-2">
                                                <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">Temperatura (Precisión):</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.05" min="0" max="1.5" class="form-control" name="ia_temperatura"
                                                           value="<?= htmlspecialchars($config['ia_temperatura'] ?? '0.70') ?>">
                                                    <div class="input-group-append"><span class="input-group-text"><i class="fa fa-thermometer-half"></i></span></div>
                                                </div>
                                                <small class="text-muted">0.3 = Precisión Alta | 0.7 = Conversacional</small>
                                            </div>

                                            <div class="col-md-3 mb-2">
                                                <label class="font-weight-bold text-dark small text-uppercase" style="letter-spacing: 0.3px;">Máximo de Tokens:</label>
                                                <input type="number" class="form-control" name="ia_max_tokens"
                                                       value="<?= htmlspecialchars($config['ia_max_tokens'] ?? '1000') ?>" min="100" max="8000">
                                                <small class="text-muted">Límite por turno (1000 default).</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECCIÓN 2: PERSONALIDAD, RUBRO & REGLAS DE ATENCIÓN -->
                                <div class="card mb-3 border shadow-xs" style="border-radius: 12px; background: #f0fdf4; border-color: #bbf7d0 !important;">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                            <div>
                                                <h6 class="font-weight-bold text-success mb-0">
                                                    <i class="fa fa-sitemap mr-1"></i> Rubro, Personalidad y Comportamiento del Local
                                                </h6>
                                                <small class="text-muted">Adapta el tono de voz paraguayo, modismos y reglas operativas del bot para este negocio.</small>
                                            </div>
                                            <div class="mt-2 mt-md-0">
                                                <button type="button" class="btn btn-outline-success font-weight-bold btn-sm shadow-xs" onclick="cargarPromptsRecomendados()">
                                                    <i class="fa fa-magic mr-1"></i> Cargar Instrucciones & Tono Recomendados
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold text-dark small text-uppercase">Rubro del Negocio & Plantilla GeeksIA:</label>
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

                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold text-dark small text-uppercase">Modo de Operación del Bot:</label>
                                                <select class="form-control font-weight-bold" name="bot_modo">
                                                    <option value="bot" <?= (@$config['bot_modo'] == 'bot') ? 'selected' : '' ?>>🤖 Bot Automático (Producción Normal)</option>
                                                    <option value="humano" <?= (@$config['bot_modo'] == 'humano') ? 'selected' : '' ?>>👨‍💻 Humano (Bot Pausado - Atención Manual)</option>
                                                    <option value="desarrollo" <?= (@$config['bot_modo'] == 'desarrollo') ? 'selected' : '' ?>>🧪 Modo Desarrollo (Solo Números de Prueba)</option>
                                                </select>
                                                <small class="text-muted">Controla si el bot atiende a todos los usuarios o solo a números autorizados.</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold text-dark small text-uppercase">Tono de Voz / Personalidad de la IA:</label>
                                                <input type="text" class="form-control" name="ia_tono" value="<?= htmlspecialchars($config['ia_tono'] ?? 'Cálido, amigable, paraguayo informal con emojis moderados') ?>" placeholder="Ej: Cálido, amigable, formal, etc.">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold text-dark small text-uppercase">Números de Prueba (Whitelist de Desarrollo):</label>
                                                <input type="text" class="form-control" name="numeros_prueba" value="<?= htmlspecialchars($config['numeros_prueba'] ?? '') ?>" placeholder="Ej: 595981000098, 595981000099">
                                                <small class="text-muted">Separados por comas. Reciben atención aún en modo desarrollo o local cerrado.</small>
                                            </div>

                                            <div class="col-12 mb-1">
                                                <label class="font-weight-bold text-dark small text-uppercase">Instrucciones Especiales del Local para la IA:</label>
                                                <textarea class="form-control" name="ia_instrucciones_extra" rows="2" placeholder="Ej: Incluir cubiertos descartables solo a pedido del cliente."><?= htmlspecialchars($config['ia_instrucciones_extra'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECCIÓN 3: ARQUITECTURA MULTIMODAL Y ROLES DEL MOTOR (COLAPSABLE) -->
                                <div class="card mb-3 border shadow-xs" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
                                    <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center flex-wrap" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #ffffff;">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2" style="font-size: 18px;">⚡</span>
                                            <div>
                                                <strong class="text-white d-block" style="font-size: 13.5px;">Arquitectura Multimodal GeeksIA: 4 Roles Especializados</strong>
                                                <small style="color: #c7d2fe;">Agentes modulares de alta velocidad con caché inteligente de tokens.</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-2 mt-md-0" style="gap: 8px;">
                                            <button type="button" class="btn btn-xs btn-success font-weight-bold shadow-xs" onclick="aplicarPresetOficialOpenAI()">
                                                <i class="fa fa-bolt mr-1"></i> Preset Oficial OpenAI (gpt-4.1-mini)
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-light font-weight-bold" data-toggle="collapse" data-target="#collapseAiArchitecture" aria-expanded="false" aria-controls="collapseAiArchitecture">
                                                <i class="fa fa-sliders mr-1"></i> Ver Roles & Caché
                                            </button>
                                        </div>
                                    </div>

                                    <div class="collapse" id="collapseAiArchitecture">
                                        <div class="card-body p-3" style="background: #0f172a; color: #ffffff;">
                                            <div class="row">
                                                <!-- Rol 1: Atención -->
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="p-3 rounded h-100" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="badge badge-warning px-2 py-1 font-weight-bold" style="font-size: 10px;">AGENTE PRINCIPAL</span>
                                                            <i class="fa fa-comments text-warning" style="font-size: 18px;"></i>
                                                        </div>
                                                        <h6 class="font-weight-bold text-white mb-1" style="font-size: 13px;">Atención y Toma de Pedidos</h6>
                                                        <div style="font-size: 11px; color: #a5b4fc; margin-bottom: 6px;">
                                                            <strong>Proveedor:</strong> OpenAI<br>
                                                            <strong>Modelo:</strong> <code style="color:#fef08a; background:rgba(0,0,0,0.3); padding:1px 4px; border-radius:3px;">gpt-4.1-mini</code><br>
                                                            <strong>Temperatura:</strong> <span class="badge badge-light" style="font-size:10px;">0.3</span> (Precisión alta)
                                                        </div>
                                                        <p class="mb-0" style="font-size: 11px; color: #cbd5e1; line-height: 1.35;">
                                                            Manejo conversacional en WhatsApp y WebApp, armado del carrito, cálculo de totales en Guaraníes y emisión del ticket de pedido.
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Rol 2: Visión -->
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="p-3 rounded h-100" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 10px;">VISION MULTIMODAL</span>
                                                            <i class="fa fa-eye text-info" style="font-size: 18px;"></i>
                                                        </div>
                                                        <h6 class="font-weight-bold text-white mb-1" style="font-size: 13px;">Visión Computacional</h6>
                                                        <div style="font-size: 11px; color: #a5b4fc; margin-bottom: 6px;">
                                                            <strong>Proveedor:</strong> OpenAI<br>
                                                            <strong>Modelo:</strong> <code style="color:#fef08a; background:rgba(0,0,0,0.3); padding:1px 4px; border-radius:3px;">gpt-4.1-mini</code><br>
                                                            <strong>Modo:</strong> Multimodal Vision
                                                        </div>
                                                        <p class="mb-0" style="font-size: 11px; color: #cbd5e1; line-height: 1.35;">
                                                            Lectura y análisis de fotos enviadas por clientes (flyers de promos, fotos de la carta física, comprobantes bancarios).
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Rol 3: Audio -->
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="p-3 rounded h-100" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 10px;">SPEECH-TO-TEXT</span>
                                                            <i class="fa fa-microphone text-success" style="font-size: 18px;"></i>
                                                        </div>
                                                        <h6 class="font-weight-bold text-white mb-1" style="font-size: 13px;">Transcripción de Audios</h6>
                                                        <div style="font-size: 11px; color: #a5b4fc; margin-bottom: 6px;">
                                                            <strong>Proveedor:</strong> OpenAI<br>
                                                            <strong>Modelo:</strong> <code style="color:#fef08a; background:rgba(0,0,0,0.3); padding:1px 4px; border-radius:3px;">gpt-4o-mini-transcribe</code><br>
                                                            <strong>Idioma:</strong> Español (es)
                                                        </div>
                                                        <p class="mb-0" style="font-size: 11px; color: #cbd5e1; line-height: 1.35;">
                                                            Conversión instantánea de audios de WhatsApp a texto para que el cliente pueda pedir enviando notas de voz.
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Rol 4: Gerencial -->
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="p-3 rounded h-100" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 10px;">AGENTE GERENTE</span>
                                                            <i class="fa fa-user-secret text-primary" style="font-size: 18px;"></i>
                                                        </div>
                                                        <h6 class="font-weight-bold text-white mb-1" style="font-size: 13px;">Panel Gerencial</h6>
                                                        <div style="font-size: 11px; color: #a5b4fc; margin-bottom: 6px;">
                                                            <strong>Proveedor:</strong> OpenAI<br>
                                                            <strong>Modelo:</strong> <code style="color:#fef08a; background:rgba(0,0,0,0.3); padding:1px 4px; border-radius:3px;">gpt-4.1-mini</code><br>
                                                            <strong>Temperatura:</strong> <span class="badge badge-light" style="font-size:10px;">0.3</span> (Comandos precisos)
                                                        </div>
                                                        <p class="mb-0" style="font-size: 11px; color: #cbd5e1; line-height: 1.35;">
                                                            Procesamiento de órdenes y comandos de administración para gerentes y administradores (reportes de ventas y stock).
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Caché -->
                                            <div class="mt-2 p-2 rounded" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <span class="badge badge-success px-2 py-1 mr-2"><i class="fa fa-shield mr-1"></i> CACHÉ INTELIGENTE ACTIVO</span>
                                                    <span style="font-size: 12px; color: #e2e8f0;">
                                                        <strong>Doble Capa de Ahorro:</strong> 1) <em>OpenAI Prompt Caching</em> (reduce automáticamente hasta 50% el costo de tokens en la carta y menú) + 2) <em>Caché Local de Respuestas Frecuentes</em> (0 tokens para ubicación, horarios y alias).
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECCIÓN 4: ACCESO RÁPIDO A PROMOCIONES -->
                                <div class="alert alert-light border p-3 d-flex align-items-center justify-content-between flex-wrap shadow-xs mt-2" style="border-radius:12px; background:#fff7ed; border-color:#fed7aa !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3" style="width:44px; height:44px; border-radius:10px; background:#ffedd5; display:flex; align-items:center; justify-content:center; color:#ea580c; font-size:20px;">
                                            <i class="fa fa-bullhorn"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Módulo Avanzado de Promociones & Flyers</h6>
                                            <p class="text-muted small mb-0">Configura promociones ilimitadas, imágenes en alta resolución y días de vigencia en la pestaña de Promociones.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-warning font-weight-bold btn-sm mt-2 mt-md-0 shadow-xs" onclick="$('#tab-promos-btn').tab('show');">
                                        <i class="fa fa-tags mr-1"></i> Ir a Promociones & Flyers &rarr;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 8: PROMOS ILIMITADAS & FLYERS ==================== -->
                    <div class="tab-pane fade" id="tab-promos" role="tabpanel">
                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-gradient-directional-danger text-white py-2" style="background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h5 class="mb-0 text-white font-weight-bold">
                                            <i class="fa fa-bullhorn mr-1"></i> Gestor de Promociones, Combos y Flyers de la Carta
                                        </h5>
                                        <small class="text-white-50">Crea promociones ilimitadas con imágenes. Se sincronizan en vivo con la WebApp de pedidos y el Bot IA de WhatsApp.</small>
                                    </div>
                                    <div class="mt-2 mt-md-0">
                                        <button type="button" class="btn btn-light text-danger font-weight-bold shadow-sm" id="btn_add_promo" onclick="addNewPromoCard()">
                                            <i class="fa fa-plus-circle mr-1"></i> + Añadir Nueva Promoción
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Barra de Filtros y Búsqueda -->
                                <div class="bg-light p-3 rounded mb-3 border">
                                    <div class="row align-items-center">
                                        <div class="col-md-5 mb-2 mb-md-0">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="search_promos_input" placeholder="Buscar promoción por título o detalle..." oninput="filterPromosList()">
                                            </div>
                                        </div>
                                        <div class="col-md-7 text-md-right">
                                            <span class="small font-weight-bold text-muted mr-2">Filtrar por día:</span>
                                            <div class="btn-group btn-group-sm flex-wrap" role="group" id="promo_day_filters">
                                                <button type="button" class="btn btn-outline-danger active font-weight-bold" data-day-filter="all" onclick="setDayFilter('all', this)">Todas</button>
                                                <button type="button" class="btn btn-outline-secondary font-weight-bold" data-day-filter="today" onclick="setDayFilter('today', this)">🔥 Hoy</button>
                                                <button type="button" class="btn btn-outline-secondary" data-day-filter="mon" onclick="setDayFilter('mon', this)">Lun</button>
                                                <button type="button" class="btn btn-outline-secondary" data-day-filter="tue" onclick="setDayFilter('tue', this)">Mar</button>
                                                <button type="button" class="btn btn-outline-secondary" data-day-filter="wed" onclick="setDayFilter('wed', this)">Mié</button>
                                                <button type="button" class="btn btn-outline-secondary" data-day-filter="thu" onclick="setDayFilter('thu', this)">Jue</button>
                                                <button type="button" class="btn btn-outline-secondary" data-day-filter="fri" onclick="setDayFilter('fri', this)">Vie</button>
                                                <button type="button" class="btn btn-outline-secondary" data-day-filter="sat" onclick="setDayFilter('sat', this)">Sáb</button>
                                                <button type="button" class="btn btn-outline-secondary" data-day-filter="sun" onclick="setDayFilter('sun', this)">Dom</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resumen Informativo en Píldoras -->
                                <div class="d-flex align-items-center flex-wrap mb-3" style="gap: 10px;">
                                    <span class="badge badge-light border text-dark p-2" style="font-size: 13px;">
                                        <i class="fa fa-list mr-1 text-primary"></i> Total de Promociones: <strong id="lbl_total_promos">0</strong>
                                    </span>
                                    <span class="badge badge-light border text-dark p-2" style="font-size: 13px;">
                                        <i class="fa fa-fire mr-1 text-danger"></i> Vigentes Hoy (<strong id="lbl_current_day_name"><?= date('l') ?></strong>): <strong id="lbl_today_promos" class="text-danger">0</strong>
                                    </span>
                                    <span class="badge badge-light border text-dark p-2" style="font-size: 13px;">
                                        <i class="fa fa-check-circle mr-1 text-success"></i> Promociones Activas: <strong id="lbl_active_promos" class="text-success">0</strong>
                                    </span>
                                </div>

                                <!-- Sugerencias de Badges -->
                                <datalist id="promo_badges_list">
                                    <option value="2x1">
                                    <option value="30% OFF">
                                    <option value="20% OFF">
                                    <option value="50% OFF en 2da unidad">
                                    <option value="Combo Especial">
                                    <option value="Envío Gratis">
                                    <option value="Bebida de Regalo">
                                    <option value="Especial del Día">
                                    <option value="Combo Familiar">
                                    <option value="Happy Hour">
                                </datalist>

                                <!-- Input Oculto Principal con el JSON serializado de todas las promos -->
                                <input type="hidden" name="promos_json" id="promos_json_field" value='<?= htmlspecialchars(json_encode($config['promos_list'] ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>'>

                                <!-- Contenedor Dinámico de Cards de Promoción -->
                                <div id="promos_cards_container">
                                    <!-- Generado dinámicamente por JavaScript -->
                                </div>

                                <!-- Estado Vacío -->
                                <div id="promos_empty_state" class="text-center p-5 border rounded bg-light" style="display: none;">
                                    <div class="mb-3">
                                        <span style="width: 70px; height: 70px; border-radius: 50%; background: #fee2e2; color: #e11d48; display: inline-flex; align-items: center; justify-content: center; font-size: 32px;">
                                            <i class="fa fa-bullhorn"></i>
                                        </span>
                                    </div>
                                    <h4 class="font-weight-bold text-dark mb-2">No hay promociones registradas</h4>
                                    <p class="text-muted mx-auto mb-4" style="max-width: 520px; font-size: 14px;">
                                        Crea combos, ofertas 2x1, descuentos por día o envíos gratis. Sube imágenes llamativas y el Bot IA de WhatsApp y la WebApp se encargarán de ofrecérselas a tus clientes automáticamente.
                                    </p>
                                    <button type="button" class="btn btn-danger font-weight-bold px-4 py-2 shadow-sm" onclick="addNewPromoCard()">
                                        <i class="fa fa-plus-circle mr-1"></i> Crear Primera Promoción
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TAB 9: LIVE GEEKSIA JSON ==================== -->
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
                                                <button class="btn btn-outline-primary" type="button" onclick="navigator.clipboard.writeText($('#api_endpoint_url').val()); if (typeof toastr !== 'undefined') toastr.success('¡URL copiada al portapapeles!', 'Portapapeles');">
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
    // Configuración de Toastr
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: "4000",
            extendedTimeOut: "1500"
        };
    }

    // Modal Dinámico Moderno de Confirmación
    window.modernConfirm = function(options) {
        var title = options.title || '¿Confirmar Acción?';
        var message = options.message || '';
        var iconType = options.iconType || 'warning';
        var confirmBtnText = options.confirmBtnText || 'Confirmar';
        var confirmBtnClass = options.confirmBtnClass || 'btn-warning';

        var iconClass = 'fa-exclamation-triangle';
        var iconBg = '#fef3c7';
        var iconColor = '#d97706';

        if (iconType === 'danger') {
            iconClass = 'fa-trash-o';
            iconBg = '#fee2e2';
            iconColor = '#dc2626';
            if (!options.confirmBtnClass) confirmBtnClass = 'btn-danger';
        } else if (iconType === 'info') {
            iconClass = 'fa-info-circle';
            iconBg = '#e0f2fe';
            iconColor = '#0284c7';
            if (!options.confirmBtnClass) confirmBtnClass = 'btn-info';
        } else if (iconType === 'question') {
            iconClass = 'fa-question';
            iconBg = '#ede9fe';
            iconColor = '#7c3aed';
            if (!options.confirmBtnClass) confirmBtnClass = 'btn-primary';
        }

        $('#modernConfirmIcon').attr('class', 'fa ' + iconClass + ' fa-2x').css('color', iconColor);
        $('#modernConfirmIconWrapper').css('background-color', iconBg);
        $('#modernConfirmTitle').text(title);
        $('#modernConfirmMessage').html(message);

        var $btn = $('#btnModernConfirmAction');
        $btn.attr('class', 'btn ' + confirmBtnClass + ' px-3 py-2 font-weight-bold shadow-sm')
            .html('<i class="fa fa-check mr-1"></i> ' + confirmBtnText);

        $btn.off('click').on('click', function() {
            $('#modalModernConfirm').modal('hide');
            if (typeof options.onConfirm === 'function') {
                options.onConfirm();
            }
        });

        $('#modalModernConfirm').modal('show');
    };

    // Helper de notificaciones dinámicas
    window.modernNotice = function(type, title, message) {
        if (typeof toastr !== 'undefined') {
            if (type === 'success') toastr.success(message, title || 'Éxito');
            else if (type === 'error') toastr.error(message, title || 'Error');
            else if (type === 'warning') toastr.warning(message, title || 'Atención');
            else toastr.info(message, title || 'Información');
        }
    };

    // Persistencia y navegación por Hash en URL
    $('#configTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var hash = $(e.target).attr('href');
        if (history.replaceState) {
            history.replaceState(null, null, hash);
        }
    });
    var initialTabHash = window.location.hash;
    if (initialTabHash && $('#configTabs a[href="' + initialTabHash + '"]').length) {
        $('#configTabs a[href="' + initialTabHash + '"]').tab('show');
    }

    // Ajax Form Submit
    $('#form_delivery_config').on('submit', function(e) {
        e.preventDefault();
        if (typeof syncPromosJson === 'function') {
            syncPromosJson();
        }
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
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(resp.message || 'Error al guardar', 'Error');
                    }
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Guardar Toda la Configuración');
                if (typeof toastr !== 'undefined') {
                    toastr.error('Ocurrió un error al procesar la solicitud.', 'Error de Servidor');
                }
            }
        });
    });

    // Reset Counters con diálogo dinámico moderno
    $('#btn_reset_counters').on('click', function() {
        window.modernConfirm({
            title: 'Reiniciar Contadores',
            message: '¿Está seguro de reiniciar los contadores de pedidos de hoy a <strong>0</strong> para este local?<br><small class="text-muted">Los turnos volverán a empezar de forma equitativa.</small>',
            iconType: 'warning',
            confirmBtnText: 'Sí, reiniciar a 0',
            confirmBtnClass: 'btn-warning',
            onConfirm: function() {
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
                                toastr.success(resp.message, 'Contadores Reiniciados');
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(resp.message || 'No se pudieron reiniciar los contadores', 'Error');
                            }
                        }
                    },
                    error: function() {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Error de comunicación con el servidor', 'Error');
                        }
                    }
                });
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

    // Randomize All Pins con diálogo dinámico moderno
    $('#btn_random_pins').on('click', function() {
        window.modernConfirm({
            title: 'Generar Nuevos PINs',
            message: '¿Desea generar nuevos <strong>PINs aleatorios de 4 dígitos</strong> para todos los repartidores?<br><small class="text-muted">Recuerde guardar los cambios para aplicarlos.</small>',
            iconType: 'info',
            confirmBtnText: 'Generar PINs',
            confirmBtnClass: 'btn-primary',
            onConfirm: function() {
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
                                toastr.success(resp.message, 'PINs Generados');
                            }
                        }
                    },
                    error: function() {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Error al generar PINs', 'Error');
                        }
                    }
                });
            }
        });
    });

    // Test Round-Robin Turn Simulation con Modal Dinámico
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
                    $('#simSlotBadge').text('Slot #' + d.slot);
                    $('#simDriverName').text(d.nombre);
                    $('#simDriverPhone').text(d.telefono || 'Sin teléfono');
                    $('#simDriverOrders').text(d.pedidos_hoy + ' pedidos');
                    $('#modalSimulationResult').modal('show');
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.warning(resp.message || 'No hay repartidores activos o disponibles', 'Atención');
                    }
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-play mr-1"></i> Simular Asignación de Turno');
                if (typeof toastr !== 'undefined') {
                    toastr.error('Error al simular turno', 'Error');
                }
            }
        });
    });

    // Inspect live JSON
    $('#btn_fetch_live_json').on('click', function() {
        $.getJSON('<?= base_url('delivery_config/live_json') ?>', { loc_id: '<?= (int)$loc_id ?>' }, function(data) {
            $('#live_json_output').text(JSON.stringify(data, null, 2));
            $('#live_json_container').slideDown();
            if (typeof toastr !== 'undefined') toastr.info('JSON obtenido exitosamente');
        }).fail(function() {
            if (typeof toastr !== 'undefined') toastr.error('Error al obtener JSON desde el API');
        });
    });

    // Rotar la API key del local con Modal Dinámico
    $('#btn_rotate_api_key').on('click', function() {
        window.modernConfirm({
            title: 'Rotar API Key',
            message: 'Se generará una API key nueva y la actual <strong>dejará de funcionar de inmediato</strong>.<br><strong class="text-danger">Esta acción no se puede deshacer.</strong> ¿Desea continuar?',
            iconType: 'danger',
            confirmBtnText: 'Sí, rotar clave',
            confirmBtnClass: 'btn-danger',
            onConfirm: function() {
                var $btn = $('#btn_rotate_api_key').prop('disabled', true);
                $.ajax({
                    url: '<?= base_url('delivery_config/rotate_api_key') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: { loc_id: '<?= (int)$loc_id ?>' },
                    success: function(resp) {
                        $btn.prop('disabled', false);
                        if (resp.status === 'Success') {
                            $('#txt_api_key_masked').text('••••' + resp.api_key.slice(-4));
                            $('#modalNewApiKeyInput').val(resp.api_key);
                            $('#modalNewApiKey').modal('show');
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(resp.message || 'No se pudo rotar la clave.', 'Error');
                            }
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Error al rotar la clave.', 'Error');
                        }
                    }
                });
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
            models: ['gpt-4.1-mini', 'gpt-4o-mini', 'gpt-4o', 'gpt-4.1'],
            defaultModel: 'gpt-4.1-mini',
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

    window.aplicarPresetOficialOpenAI = function() {
        $('#sel_ia_proveedor').val('openai');
        window.handleAiProviderChange('openai', false);
        $('#inp_ia_modelo').val('gpt-4.1-mini');
        $('input[name="ia_temperatura"]').val('0.30');
        $('input[name="ia_max_tokens"]').val('1000');
        if (typeof toastr !== 'undefined') {
            toastr.success('Configuración oficial OpenAI cargada: gpt-4.1-mini con temperatura 0.30. Recuerda presionar "Guardar Toda la Configuración".', 'Configuración Oficial OpenAI');
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
        window.modernConfirm({
            title: 'Recomendaciones de Rubro',
            message: '¿Deseas reemplazar el tono y las instrucciones de IA actuales por las recomendaciones oficiales para <strong>' + tpl.name + '</strong>?',
            iconType: 'info',
            confirmBtnText: 'Reemplazar Instrucciones',
            confirmBtnClass: 'btn-primary',
            onConfirm: function() {
                $('input[name="ia_tono"]').val(tpl.default_tono);
                $('textarea[name="ia_instrucciones_extra"]').val(tpl.default_instrucciones);
                if (typeof toastr !== 'undefined') {
                    toastr.info('Instrucciones cargadas para ' + tpl.name + '. Recuerda presionar "Guardar Toda la Configuración".', 'Instrucciones Aplicadas');
                }
            }
        });
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

        window.modernConfirm({
            title: 'Copiar Horarios',
            message: '¿Copiar el horario de <strong>' + sourceNombre + '</strong> (' + ap + ' a ' + ci + ') a todos los demás días activos?',
            iconType: 'question',
            confirmBtnText: 'Copiar a Todos',
            confirmBtnClass: 'btn-primary',
            onConfirm: function() {
                diasKeys.forEach(function(k) {
                    if (k !== sourceKey) {
                        $('#hora_apertura_' + k).val(ap);
                        $('#hora_cierre_' + k).val(ci);
                        window.recalcularObservacionDia(k);
                    }
                });
                if (typeof toastr !== 'undefined') {
                    toastr.success('Horario de ' + sourceNombre + ' copiado a los demás días.', 'Horarios Actualizados');
                }
            }
        });
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
                if (typeof toastr !== 'undefined') toastr.warning('No hay ningún día abierto para tomar como referencia.', 'Atención');
            }
            return;
        } else if (preset === 'cerrar_todos') {
            window.modernConfirm({
                title: 'Cerrar Todos los Días',
                message: '¿Deseas marcar todos los días de la semana como <strong>Cerrados</strong>?',
                iconType: 'warning',
                confirmBtnText: 'Marcar Cerrados',
                confirmBtnClass: 'btn-danger',
                onConfirm: function() {
                    diasKeys.forEach(function(k) {
                        $('#switch_dia_' + k).prop('checked', false);
                        window.toggleDiaEstado(k);
                    });
                    window.generarTextoResumenHorarios(false);
                    if (typeof toastr !== 'undefined') toastr.info('Todos los días marcados como cerrados.');
                }
            });
            return;
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
        var usuario = $('input[name="slot_' + slot + '_usuario"]').val() || ('delivery' + slot);
        var email = $('input[name="slot_' + slot + '_email"]').val() || ('delivery' + slot + '@geekspos.com');
        var password = $('input[name="slot_' + slot + '_password"]').val() || 'delivery123';
        var telefono = $('input[name="slot_' + slot + '_telefono"]').val() || '';
        var serverUrl = '<?= rtrim(base_url(), "/") ?>';
        if (serverUrl.indexOf('localhost') !== -1 || serverUrl.indexOf('127.0.0.1') !== -1 || serverUrl.indexOf('192.168.') !== -1 || serverUrl.indexOf('trycloudflare.com') !== -1) {
            serverUrl = 'https://pos.geeks.com.py';
        }

        // Formato QR JSON para la App
        var qrObj = {
            url: serverUrl,
            username: usuario,
            email: email,
            password: password,
            phone: telefono || slot
        };
        var payload = JSON.stringify(qrObj);
        var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&margin=10&data=' + encodeURIComponent(payload);

        $('#qrModalTitle').html('<i class="fa fa-qrcode mr-2"></i> Credenciales Slot #' + slot + ': ' + nombre);
        $('#qrDriverName').text(nombre + ' (Slot #' + slot + ')');
        $('#qrDriverUser').text(usuario);
        $('#qrDriverEmail').text(email);
        $('#qrDriverPassword').text(password);
        $('#qrDriverPhone').text(telefono ? telefono : ('Slot #' + slot));
        $('#qrImageDisplay').attr('src', qrUrl);

        // Prepara mensaje de WhatsApp
        var cleanPhone = telefono.replace(/[^0-9]/g, '');
        if (cleanPhone.indexOf('09') === 0) cleanPhone = '595' + cleanPhone.substring(1);

        var msg = '🛵 *Geeks Delivery - Credenciales de Repartidor*\n\n' +
                  'Hola *' + nombre + '*, estos son tus datos de acceso a la App Móvil de Delivery:\n\n' +
                  '👤 *Usuario:* ' + usuario + '\n' +
                  '📧 *Correo:* ' + email + '\n' +
                  '🔑 *Contraseña:* ' + password + '\n' +
                  '🌐 *Servidor:* ' + serverUrl + '\n' +
                  '📲 *Descargar App (APK):* ' + serverUrl + '/delivery.apk\n\n' +
                  '👉 *Puedes ingresar escribiendo tu usuario/correo y contraseña, o tocando "Escanear Código QR" en la pantalla de inicio.*';

        var waLink = 'https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(msg);
        $('#btnSendWhatsappMsg').attr('href', waLink);

        $('#modalDriverQr').modal('show');
    };

    // Función para vaciar / desvincular un slot de repartidor con diálogo dinámico moderno
    window.clearSlot = function(slot) {
        window.modernConfirm({
            title: 'Desvincular Repartidor',
            message: '¿Desea vaciar y desvincular el repartidor del <strong>Slot #' + slot + '</strong> de este local?<br><small class="text-muted">Los datos del repartidor serán reseteados. Recuerde guardar la configuración.</small>',
            iconType: 'danger',
            confirmBtnText: 'Sí, vaciar slot',
            confirmBtnClass: 'btn-danger',
            onConfirm: function() {
                $('input[name="slot_' + slot + '_nombre"]').val('Repartidor ' + slot);
                $('input[name="slot_' + slot + '_usuario"]').val('delivery' + slot);
                $('input[name="slot_' + slot + '_email"]').val('delivery' + slot + '@geekspos.com');
                $('input[name="slot_' + slot + '_password"]').val('');
                $('input[name="slot_' + slot + '_telefono"]').val('');
                $('#switch_activo_' + slot).prop('checked', false).next('label').removeClass('text-success').addClass('text-muted').text('Inactivo');
                $('#switch_ocupado_' + slot).prop('checked', false).next('label').removeClass('text-danger').addClass('text-primary').text('Libre');

                if (typeof toastr !== 'undefined') {
                    toastr.info('Slot #' + slot + ' vaciado. Recuerde presionar "Guardar Toda la Configuración" para confirmar los cambios.', 'Slot Vaciado');
                }
            }
        });
    };

    // Cambio de local seguro (soporta Select2 y select nativo)
    $('#select_location_store').on('change select2:select', function() {
        var targetVal = $(this).val();
        if (targetVal !== undefined && targetVal !== null) {
            window.location.href = '<?= base_url('delivery_config/index/') ?>' + targetVal;
        }
    });

    // =========================================================================
    // GESTIÓN DINÁMICA DE TARIFAS DE DELIVERY & TRAMOS DE DISTANCIA
    // =========================================================================
    function updateTarifaModeUI() {
        var mode = $('input[name="tarifa_tipo"]:checked').val() || 'tramos';
        $('.option-tarifa-card').removeClass('border-primary bg-blue-light shadow-sm').addClass('border');

        if (mode === 'tramos') {
            $('#card_tarifa_tramos').addClass('border-primary bg-blue-light shadow-sm').removeClass('border');
            $('#panel_tramos_container').slideDown(200);
            $('#panel_por_km_container').slideUp(200);
        } else if (mode === 'km') {
            $('#card_tarifa_km').addClass('border-primary bg-blue-light shadow-sm').removeClass('border');
            $('#panel_tramos_container').slideUp(200);
            $('#panel_por_km_container').slideDown(200);
        } else if (mode === 'hibrido') {
            $('#card_tarifa_hibrido').addClass('border-primary bg-blue-light shadow-sm').removeClass('border');
            $('#panel_tramos_container').slideDown(200);
            $('#panel_por_km_container').slideDown(200);
        }
        recalculateLiveSimulator();
    }

    $('input[name="tarifa_tipo"]').on('change', function() {
        updateTarifaModeUI();
    });
    updateTarifaModeUI();

    function reindexTramos() {
        $('#tbody_tramos_delivery tr.tramo-row').each(function(index) {
            var num = index + 1;
            $(this).attr('data-index', num);
            $(this).find('.tramo-num').text(num);
        });
        updateTramoBadges();
        recalculateLiveSimulator();
    }

    function updateTramoBadges() {
        $('#tbody_tramos_delivery tr.tramo-row').each(function() {
            var desde = parseFloat($(this).find('.input-tramo-desde').val()) || 0;
            var hasta = parseFloat($(this).find('.input-tramo-hasta').val()) || 0;
            $(this).find('.badge-tramo-rango').text(desde.toFixed(1) + ' a ' + hasta.toFixed(1) + ' km');
        });
    }

    // Agregar nuevo tramo de distancia
    $('#btn_add_tramo').on('click', function() {
        var $rows = $('#tbody_tramos_delivery tr.tramo-row');
        var newIndex = $rows.length + 1;
        var newDesde = 0.0;
        var newHasta = 5.0;
        var newCosto = 10000;

        if ($rows.length > 0) {
            var $lastRow = $rows.last();
            var lastHasta = parseFloat($lastRow.find('.input-tramo-hasta').val()) || 0;
            var lastCosto = parseInt($lastRow.find('.input-tramo-costo').val()) || 10000;
            newDesde = lastHasta;
            newHasta = parseFloat((lastHasta + 3.0).toFixed(1));
            newCosto = lastCosto + 5000;
        }

        var html = '<tr class="tramo-row align-middle text-center" data-index="' + newIndex + '" style="display:none;">' +
            '<td class="font-weight-bold text-muted tramo-num">' + newIndex + '</td>' +
            '<td>' +
                '<div class="input-group input-group-sm">' +
                    '<input type="number" step="0.1" min="0" class="form-control text-center font-weight-bold input-tramo-desde" name="tramos_desde[]" value="' + newDesde + '" required>' +
                    '<div class="input-group-append"><span class="input-group-text">km</span></div>' +
                '</div>' +
            '</td>' +
            '<td>' +
                '<div class="input-group input-group-sm">' +
                    '<input type="number" step="0.1" min="0.1" class="form-control text-center font-weight-bold input-tramo-hasta" name="tramos_hasta[]" value="' + newHasta + '" required>' +
                    '<div class="input-group-append"><span class="input-group-text">km</span></div>' +
                '</div>' +
            '</td>' +
            '<td>' +
                '<span class="badge badge-info p-2 font-weight-bold badge-tramo-rango" style="font-size:12px;">' +
                    newDesde.toFixed(1) + ' a ' + newHasta.toFixed(1) + ' km' +
                '</span>' +
            '</td>' +
            '<td>' +
                '<div class="input-group input-group-sm">' +
                    '<input type="number" step="500" min="0" class="form-control text-right font-weight-bold text-success input-tramo-costo" name="tramos_costo[]" value="' + newCosto + '" required style="font-size:14px;">' +
                    '<div class="input-group-append"><span class="input-group-text font-weight-bold">Gs</span></div>' +
                '</div>' +
            '</td>' +
            '<td>' +
                '<input type="text" class="form-control form-control-sm" name="tramos_etiqueta[]" value="Zona ' + newIndex + '" placeholder="Ej: Zona ' + newIndex + '...">' +
            '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-tramo" title="Eliminar este tramo">' +
                    '<i class="fa fa-trash-o"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';

        var $newRow = $(html);
        $('#tbody_tramos_delivery').append($newRow);
        $newRow.fadeIn(250);
        reindexTramos();

        if (typeof toastr !== 'undefined') {
            toastr.success('Nuevo tramo #' + newIndex + ' agregado (' + newDesde + ' a ' + newHasta + ' km)', 'Tramo Agregado');
        }
    });

    // Eliminar tramo
    $(document).on('click', '.btn-remove-tramo', function() {
        var $rows = $('#tbody_tramos_delivery tr.tramo-row');
        if ($rows.length <= 1) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Debes mantener al menos un tramo de distancia configurado.', 'Atención');
            }
            return;
        }
        var $row = $(this).closest('tr.tramo-row');
        $row.fadeOut(200, function() {
            $(this).remove();
            reindexTramos();
        });
    });

    // Actualizar rangos al escribir
    $(document).on('input change', '.input-tramo-desde, .input-tramo-hasta, .input-tramo-costo, #costo_envio_base, #km_base, #costo_por_km, #radio_max_km', function() {
        updateTramoBadges();
        recalculateLiveSimulator();
    });

    // Resetear a 5 tramos de ejemplo sugeridos
    $('#btn_reset_default_tramos').on('click', function() {
        window.modernConfirm({
            title: 'Cargar Tramos Sugeridos',
            message: '¿Deseas reemplazar los tramos actuales por la <strong>configuración recomendada de 5 zonas estándar</strong> (5.000, 10.000, 15.000, 20.000 y 25.000 Gs)?',
            iconType: 'question',
            confirmBtnText: 'Sí, Cargar Sugeridos',
            confirmBtnClass: 'btn-primary',
            onConfirm: function() {
                var defaults = [
                    { desde: 0.0, hasta: 1.5, costo: 5000, label: 'Zona 1 (Barrio cercano)' },
                    { desde: 1.5, hasta: 4.0, costo: 10000, label: 'Zona 2 (Estándar)' },
                    { desde: 4.0, hasta: 7.0, costo: 15000, label: 'Zona 3 (Media distancia)' },
                    { desde: 7.0, hasta: 10.0, costo: 20000, label: 'Zona 4 (Límite urbano)' },
                    { desde: 10.0, hasta: 15.0, costo: 25000, label: 'Zona 5 (Interurbano / Lejano)' }
                ];
                var html = '';
                defaults.forEach(function(tr, i) {
                    var idx = i + 1;
                    html += '<tr class="tramo-row align-middle text-center" data-index="' + idx + '">' +
                        '<td class="font-weight-bold text-muted tramo-num">' + idx + '</td>' +
                        '<td><div class="input-group input-group-sm"><input type="number" step="0.1" min="0" class="form-control text-center font-weight-bold input-tramo-desde" name="tramos_desde[]" value="' + tr.desde + '" required><div class="input-group-append"><span class="input-group-text">km</span></div></div></td>' +
                        '<td><div class="input-group input-group-sm"><input type="number" step="0.1" min="0.1" class="form-control text-center font-weight-bold input-tramo-hasta" name="tramos_hasta[]" value="' + tr.hasta + '" required><div class="input-group-append"><span class="input-group-text">km</span></div></div></td>' +
                        '<td><span class="badge badge-info p-2 font-weight-bold badge-tramo-rango" style="font-size:12px;">' + tr.desde.toFixed(1) + ' a ' + tr.hasta.toFixed(1) + ' km</span></td>' +
                        '<td><div class="input-group input-group-sm"><input type="number" step="500" min="0" class="form-control text-right font-weight-bold text-success input-tramo-costo" name="tramos_costo[]" value="' + tr.costo + '" required style="font-size:14px;"><div class="input-group-append"><span class="input-group-text font-weight-bold">Gs</span></div></div></td>' +
                        '<td><input type="text" class="form-control form-control-sm" name="tramos_etiqueta[]" value="' + tr.label + '"></td>' +
                        '<td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-tramo"><i class="fa fa-trash-o"></i></button></td>' +
                    '</tr>';
                });
                $('#tbody_tramos_delivery').html(html);
                reindexTramos();
                if (typeof toastr !== 'undefined') {
                    toastr.success('Se cargaron los 5 tramos recomendados. Recuerda guardar los cambios.', 'Tramos Cargados');
                }
            }
        });
    });

    // Simulador interactivo en tiempo real
    function recalculateLiveSimulator() {
        var km = parseFloat($('#sim_km_input').val()) || 0;
        var maxKm = parseFloat($('#radio_max_km').val()) || 10.0;
        var mode = $('input[name="tarifa_tipo"]:checked').val() || 'tramos';

        if (km > maxKm) {
            $('#sim_resultado_costo').attr('class', 'h5 mb-0 font-weight-bold text-danger').text('Fuera de Cobertura');
            $('#sim_resultado_detalle').html('<span class="text-danger font-weight-bold">⛔ Distancia (' + km.toFixed(1) + ' km) supera radio máx (' + maxKm + ' km). IA ofrecerá retiro en local.</span>');
            return;
        }

        function formatNumber(num) {
            return (num || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        var costo = null;
        var detalle = '';
        var matchedTramo = null;

        if (mode === 'tramos' || mode === 'hibrido') {
            $('#tbody_tramos_delivery tr.tramo-row').each(function() {
                var d = parseFloat($(this).find('.input-tramo-desde').val()) || 0;
                var h = parseFloat($(this).find('.input-tramo-hasta').val()) || 0;
                if (km >= d && km <= h) {
                    costo = parseInt($(this).find('.input-tramo-costo').val()) || 0;
                    var label = $(this).find('input[name="tramos_etiqueta[]"]').val();
                    matchedTramo = { desde: d, hasta: h, label: label, num: $(this).attr('data-index') };
                    return false;
                }
            });
        }

        if (costo === null || mode === 'km') {
            var costoBase = parseInt($('#costo_envio_base').val()) || 10000;
            var kmBase = parseFloat($('#km_base').val()) || 3.0;
            var costoKm = parseInt($('#costo_por_km').val()) || 2000;

            if (mode === 'hibrido') {
                var $lastRow = $('#tbody_tramos_delivery tr.tramo-row').last();
                var lastHasta = parseFloat($lastRow.find('.input-tramo-hasta').val()) || 0;
                var lastCosto = parseInt($lastRow.find('.input-tramo-costo').val()) || 10000;
                var extraKm = Math.max(0, km - lastHasta);
                costo = lastCosto + Math.ceil(extraKm) * costoKm;
                detalle = 'Tramo máx (' + formatNumber(lastCosto) + ' Gs) + ' + extraKm.toFixed(1) + ' km extra x ' + formatNumber(costoKm) + ' Gs/km';
            } else {
                if (km <= kmBase) {
                    costo = costoBase;
                    detalle = 'Tarifa base fija (' + formatNumber(costoBase) + ' Gs) dentro de ' + kmBase + ' km';
                } else {
                    var extraKm = km - kmBase;
                    costo = costoBase + Math.ceil(extraKm) * costoKm;
                    detalle = 'Base ' + formatNumber(costoBase) + ' Gs + ' + Math.ceil(extraKm) + ' km extra x ' + formatNumber(costoKm) + ' Gs/km';
                }
            }
        } else {
            var etiq = matchedTramo && matchedTramo.label ? ' (' + matchedTramo.label + ')' : '';
            detalle = 'Tramo #' + (matchedTramo ? matchedTramo.num : '') + ': De ' + (matchedTramo ? matchedTramo.desde.toFixed(1) : '') + ' a ' + (matchedTramo ? matchedTramo.hasta.toFixed(1) : '') + ' km' + etiq;
        }

        $('#sim_resultado_costo').attr('class', 'h5 mb-0 font-weight-bold text-success').text(formatNumber(costo) + ' Gs');
        $('#sim_resultado_detalle').html(detalle);
    }

    $('#sim_km_input').on('input change', function() {
        recalculateLiveSimulator();
    });
    recalculateLiveSimulator();

    // =========================================================================
    // MODULO DINAMICO DE PROMOCIONES ILIMITADAS & FLYERS
    // =========================================================================
    var baseUrl = '<?= base_url() ?>';
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var currentLocId = '<?= $loc_id ?>';
    var currentDayCode = '<?= strtolower(date("D")) ?>'; // 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'
    var activeDayFilter = 'all';

    var dayCatalog = [
        { code: 'all', label: 'Todos los días', isAll: true },
        { code: 'mon', label: 'Lun' },
        { code: 'tue', label: 'Mar' },
        { code: 'wed', label: 'Mié' },
        { code: 'thu', label: 'Jue' },
        { code: 'fri', label: 'Vie' },
        { code: 'sat', label: 'Sáb' },
        { code: 'sun', label: 'Dom' }
    ];

    var initialPromosData = [];
    try {
        var rawJson = $('#promos_json_field').val();
        if (rawJson && rawJson.trim() !== '') {
            initialPromosData = JSON.parse(rawJson);
        }
    } catch (e) {
        console.error('Error parseando promociones iniciales:', e);
        initialPromosData = [];
    }

    // Modal para previsualizar flyer en grande
    window.previewPromoImageModal = function(imgSrc, title, badge) {
        if (!imgSrc) return;
        $('#modalPromoFlyerImage').attr('src', imgSrc);
        $('#modalPromoFlyerTitle').html('<i class="fa fa-image mr-2"></i> ' + (title || 'Flyer Promocional'));
        if (badge) {
            $('#modalPromoFlyerBadge').text(badge).show();
        } else {
            $('#modalPromoFlyerBadge').hide();
        }
        $('#modalPromoFlyerPreview').modal('show');
    };

    function buildPromoCardHtml(p) {
        var promoId = p.id || ('promo_' + Date.now() + '_' + Math.floor(Math.random() * 1000));
        var titulo = p.titulo || '';
        var badge = p.badge || '';
        var precio = p.precio_promo || '';
        var desc = p.descripcion || '';
        var dias = Array.isArray(p.dias) ? p.dias.map(function(d){ return d.toLowerCase(); }) : [];
        var imagen = p.imagen || '';
        var activo = (p.activo !== undefined && p.activo !== null) ? (parseInt(p.activo) ? 1 : 0) : 1;

        var isAllDays = (dias.length === 0 || dias.indexOf('all') !== -1 || dias.indexOf('todos') !== -1 || dias.length === 7);

        var fullImgUrl = '';
        if (imagen) {
            fullImgUrl = (imagen.indexOf('http://') === 0 || imagen.indexOf('https://') === 0) ? imagen : (baseUrl + imagen);
        }

        var html = '<div class="promo-card card shadow-sm mb-3 border ' + (activo ? '' : 'promo-inactive') + '" data-promo-id="' + promoId + '">' +
            '<div class="card-body p-3">' +
                '<div class="row">' +
                    '<!-- Columna Flyer -->' +
                    '<div class="col-lg-3 col-md-4 mb-3 mb-md-0">' +
                        '<div class="promo-image-box border rounded p-2 text-center bg-light position-relative">' +
                            '<div class="promo-img-preview-wrapper mb-2" style="height: 165px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #ffffff; border-radius: 8px;">' +
                                '<img src="' + fullImgUrl + '" class="promo-preview-img img-fluid" style="' + (imagen ? '' : 'display:none;') + ' max-height: 100%; object-fit: contain; cursor: pointer;" title="Clic para ampliar flyer" onclick="previewPromoImageModal(this.src, $(this).closest(\'.promo-card\').find(\'.promo-title-input\').val(), $(this).closest(\'.promo-card\').find(\'.promo-badge-input\').val())">' +
                                '<div class="promo-no-img-placeholder text-muted py-3" style="' + (imagen ? 'display:none;' : '') + '">' +
                                    '<i class="fa fa-picture-o fa-3x text-muted mb-2 d-block" style="opacity: 0.5;"></i>' +
                                    '<span class="small font-weight-bold d-block text-secondary">Sin flyer promocional</span>' +
                                    '<span style="font-size: 11px;" class="text-muted">JPG, PNG o WEBP (Máx 8MB)</span>' +
                                '</div>' +
                            '</div>' +
                            '<div class="d-flex justify-content-center" style="gap: 5px;">' +
                                '<button type="button" class="btn btn-sm btn-outline-primary font-weight-bold btn-upload-promo-trigger">' +
                                    '<i class="fa fa-camera mr-1"></i> <span class="lbl-upload-text">' + (imagen ? 'Cambiar Flyer' : 'Subir Flyer') + '</span>' +
                                '</button>' +
                                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-promo-img" style="' + (imagen ? '' : 'display:none;') + '" title="Quitar flyer">' +
                                    '<i class="fa fa-trash-o"></i>' +
                                '</button>' +
                            '</div>' +
                            '<input type="file" class="promo-file-input" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">' +
                            '<input type="hidden" class="promo-image-path-input" value="' + imagen + '">' +
                            '<div class="promo-upload-spinner text-primary mt-2 small font-weight-bold" style="display:none;">' +
                                '<i class="fa fa-spinner fa-spin mr-1"></i> Subiendo imagen...' +
                            '</div>' +
                        '</div>' +
                    '</div>' +

                    '<!-- Columna Datos -->' +
                    '<div class="col-lg-9 col-md-8">' +
                        '<div class="row">' +
                            '<div class="col-md-6 mb-2">' +
                                '<label class="small font-weight-bold text-dark mb-1">Título de la Promoción: <span class="text-danger">*</span></label>' +
                                '<input type="text" class="form-control form-control-sm font-weight-bold text-primary promo-title-input" placeholder="Ej: 2x1 en Hamburguesas Dobles" value="' + $('<div>').text(titulo).html() + '" required>' +
                            '</div>' +
                            '<div class="col-md-3 mb-2">' +
                                '<label class="small font-weight-bold text-dark mb-1">Badge / Destacado:</label>' +
                                '<input type="text" list="promo_badges_list" class="form-control form-control-sm promo-badge-input" placeholder="Ej: 2x1, -30%, Combo" value="' + $('<div>').text(badge).html() + '">' +
                            '</div>' +
                            '<div class="col-md-3 mb-2">' +
                                '<label class="small font-weight-bold text-dark mb-1">Precio Promocional (Opcional):</label>' +
                                '<input type="text" class="form-control form-control-sm text-success font-weight-bold promo-price-input" placeholder="Ej: Gs. 35.000" value="' + $('<div>').text(precio).html() + '">' +
                            '</div>' +
                        '</div>' +

                        '<div class="mb-2">' +
                            '<label class="small font-weight-bold text-dark mb-1">Descripción de la Oferta / Condiciones (Se inyecta a la IA y WebApp):</label>' +
                            '<textarea class="form-control form-control-sm promo-desc-input" rows="2" placeholder="Ej: Comprando una Hamburguesa Clásica Doble Cheddar te regalamos la segunda. Incluye papas fritas y gaseosa de 500ml. Válido para delivery y consumo en local.">' + $('<div>').text(desc).html() + '</textarea>' +
                        '</div>' +

                        '<div class="d-flex align-items-center justify-content-between flex-wrap mt-2 pt-2 border-top" style="gap: 10px;">' +
                            '<div>' +
                                '<span class="small font-weight-bold text-muted d-block mb-1">Días de la semana vigentes:</span>' +
                                '<div class="btn-group btn-group-sm flex-wrap promo-days-group" role="group">';

        dayCatalog.forEach(function(dc) {
            var isSel = false;
            if (dc.isAll) {
                isSel = isAllDays;
            } else {
                isSel = !isAllDays && (dias.indexOf(dc.code) !== -1);
            }
            html += '<button type="button" class="btn btn-outline-secondary btn-day-pill ' + (isSel ? 'active' : '') + '" data-day="' + dc.code + '">' + dc.label + '</button>';
        });

        html +=                 '</div>' +
                            '</div>' +

                            '<div class="d-flex align-items-center" style="gap: 15px;">' +
                                '<div class="custom-control custom-switch">' +
                                    '<input type="checkbox" class="custom-control-input promo-active-switch" id="switch_' + promoId + '" ' + (activo ? 'checked' : '') + '>' +
                                    '<label class="custom-control-label font-weight-bold small text-dark" for="switch_' + promoId + '">' +
                                        '<span class="promo-status-label ' + (activo ? 'text-success' : 'text-muted') + '">' + (activo ? 'Activa' : 'Pausada') + '</span>' +
                                    '</label>' +
                                '</div>' +

                                '<button type="button" class="btn btn-sm btn-outline-danger font-weight-bold btn-delete-promo-card" title="Eliminar Promoción">' +
                                    '<i class="fa fa-trash-o mr-1"></i> Eliminar' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

        return html;
    }

    function initPromosModule() {
        var $container = $('#promos_cards_container');
        $container.empty();

        if (initialPromosData.length > 0) {
            initialPromosData.forEach(function(p) {
                $container.append(buildPromoCardHtml(p));
            });
            $('#promos_empty_state').hide();
        } else {
            $('#promos_empty_state').show();
        }

        syncPromosJson();
    }

    window.addNewPromoCard = function() {
        var newPromo = {
            id: 'promo_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
            titulo: '',
            badge: '2x1',
            precio_promo: '',
            descripcion: '',
            dias: ['all'],
            imagen: '',
            activo: 1
        };

        var cardHtml = $(buildPromoCardHtml(newPromo));
        $('#promos_cards_container').prepend(cardHtml);
        $('#promos_empty_state').hide();

        cardHtml.hide().fadeIn(300);
        cardHtml.find('.promo-title-input').focus();

        syncPromosJson();
        if (typeof toastr !== 'undefined') {
            toastr.info('Nueva tarjeta de promoción agregada. Completa los datos y sube tu flyer.', 'Promoción');
        }
    };

    // Subida de imagen / flyer vía AJAX
    $(document).on('click', '.btn-upload-promo-trigger', function() {
        $(this).closest('.promo-image-box').find('.promo-file-input').click();
    });

    $(document).on('change', '.promo-file-input', function() {
        var file = this.files[0];
        if (!file) return;

        var $box = $(this).closest('.promo-image-box');
        var $card = $(this).closest('.promo-card');
        var $spinner = $box.find('.promo-upload-spinner');
        var $img = $box.find('.promo-preview-img');
        var $placeholder = $box.find('.promo-no-img-placeholder');
        var $removeBtn = $box.find('.btn-remove-promo-img');
        var $uploadLbl = $box.find('.lbl-upload-text');
        var $pathInput = $box.find('.promo-image-path-input');

        // Validar tamaño (máx 8MB)
        if (file.size > 8 * 1024 * 1024) {
            if (typeof toastr !== 'undefined') toastr.error('La imagen no debe superar los 8 MB.', 'Archivo muy pesado');
            else alert('La imagen no debe superar los 8 MB.');
            return;
        }

        var fd = new FormData();
        fd.append('promo_image', file);
        fd.append('loc_id', currentLocId);
        if (csrfTokenName && csrfHash) {
            fd.append(csrfTokenName, csrfHash);
        }

        $spinner.show();

        $.ajax({
            url: baseUrl + 'delivery_config/upload_promo_image',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(resp) {
                $spinner.hide();
                if (resp.status === 'Success') {
                    $img.attr('src', resp.file_url).show();
                    $placeholder.hide();
                    $removeBtn.show();
                    $uploadLbl.text('Cambiar Flyer');
                    $pathInput.val(resp.file_path);

                    syncPromosJson();
                    if (typeof toastr !== 'undefined') {
                        toastr.success(resp.message || 'Flyer subido correctamente.', 'Flyer Actualizado');
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(resp.message || 'Error al subir la imagen.', 'Error');
                    else alert(resp.message || 'Error al subir la imagen.');
                }
            },
            error: function() {
                $spinner.hide();
                if (typeof toastr !== 'undefined') toastr.error('Error de conexión al subir la imagen.', 'Error');
                else alert('Error de conexión al subir la imagen.');
            }
        });
    });

    // Quitar imagen
    $(document).on('click', '.btn-remove-promo-img', function() {
        var $box = $(this).closest('.promo-image-box');
        $box.find('.promo-preview-img').attr('src', '').hide();
        $box.find('.promo-no-img-placeholder').show();
        $box.find('.promo-image-path-input').val('');
        $box.find('.lbl-upload-text').text('Subir Flyer');
        $(this).hide();

        syncPromosJson();
    });

    // Toggle de días de la semana
    $(document).on('click', '.btn-day-pill', function() {
        var $btn = $(this);
        var dayCode = $btn.attr('data-day');
        var $group = $btn.closest('.promo-days-group');

        if (dayCode === 'all') {
            var wasActive = $btn.hasClass('active');
            if (!wasActive) {
                $group.find('.btn-day-pill').removeClass('active');
                $btn.addClass('active');
            }
        } else {
            // Se desactiva "Todos los días"
            $group.find('.btn-day-pill[data-day="all"]').removeClass('active');
            $btn.toggleClass('active');

            // Si ningún día quedó activo, volvemos a marcar "Todos los días"
            var activeDaysCount = $group.find('.btn-day-pill:not([data-day="all"]).active').length;
            if (activeDaysCount === 0 || activeDaysCount === 7) {
                $group.find('.btn-day-pill').removeClass('active');
                $group.find('.btn-day-pill[data-day="all"]').addClass('active');
            }
        }

        syncPromosJson();
    });

    // Switch Activa / Pausada
    $(document).on('change', '.promo-active-switch', function() {
        var isChecked = $(this).is(':checked');
        var $card = $(this).closest('.promo-card');
        var $lbl = $card.find('.promo-status-label');

        if (isChecked) {
            $lbl.text('Activa').removeClass('text-muted').addClass('text-success');
            $card.removeClass('promo-inactive');
        } else {
            $lbl.text('Pausada').removeClass('text-success').addClass('text-muted');
            $card.addClass('promo-inactive');
        }

        syncPromosJson();
    });

    // Eliminar tarjeta de promoción
    $(document).on('click', '.btn-delete-promo-card', function() {
        var $card = $(this).closest('.promo-card');
        var promoTitle = $card.find('.promo-title-input').val().trim() || 'esta promoción';

        window.modernConfirm({
            title: 'Eliminar Promoción',
            message: '¿Estás seguro de que deseas eliminar <strong>' + $('<div>').text(promoTitle).html() + '</strong>?<br><small class="text-muted">Dejará de aparecer en la WebApp y en el bot de WhatsApp.</small>',
            iconType: 'danger',
            confirmBtnText: 'Sí, Eliminar Promoción',
            confirmBtnClass: 'btn-danger',
            onConfirm: function() {
                $card.slideUp(250, function() {
                    $(this).remove();
                    syncPromosJson();
                    if ($('#promos_cards_container .promo-card').length === 0) {
                        $('#promos_empty_state').fadeIn(200);
                    }
                });
            }
        });
    });

    // Cambios en inputs de texto
    $(document).on('input change', '.promo-title-input, .promo-badge-input, .promo-price-input, .promo-desc-input', function() {
        syncPromosJson();
    });

    // Filtros de lista de promociones
    window.setDayFilter = function(dayCode, btnEl) {
        activeDayFilter = dayCode;
        $('#promo_day_filters button').removeClass('btn-danger active font-weight-bold').addClass('btn-outline-secondary');
        $(btnEl).removeClass('btn-outline-secondary').addClass('btn-danger active font-weight-bold');
        filterPromosList();
    };

    window.filterPromosList = function() {
        var term = ($('#search_promos_input').val() || '').toLowerCase().trim();

        $('#promos_cards_container .promo-card').each(function() {
            var $c = $(this);
            var title = ($c.find('.promo-title-input').val() || '').toLowerCase();
            var desc = ($c.find('.promo-desc-input').val() || '').toLowerCase();
            var badge = ($c.find('.promo-badge-input').val() || '').toLowerCase();

            var matchesTerm = (term === '' || title.indexOf(term) !== -1 || desc.indexOf(term) !== -1 || badge.indexOf(term) !== -1);

            var matchesDay = true;
            if (activeDayFilter !== 'all') {
                var targetDay = (activeDayFilter === 'today') ? currentDayCode : activeDayFilter;
                var isAll = $c.find('.btn-day-pill[data-day="all"]').hasClass('active');
                var isSpecific = $c.find('.btn-day-pill[data-day="' + targetDay + '"]').hasClass('active');
                matchesDay = (isAll || isSpecific);
            }

            if (matchesTerm && matchesDay) {
                $c.show();
            } else {
                $c.hide();
            }
        });
    };

    // Sincronizador de JSON y Actualizador de Estadísticas
    window.syncPromosJson = function() {
        var promosList = [];
        var totalPromos = 0;
        var activePromos = 0;
        var todayPromos = 0;

        $('#promos_cards_container .promo-card').each(function() {
            var $c = $(this);
            totalPromos++;

            var pId = $c.attr('data-promo-id');
            var pTitulo = $c.find('.promo-title-input').val().trim();
            var pBadge = $c.find('.promo-badge-input').val().trim();
            var pPrecio = $c.find('.promo-price-input').val().trim();
            var pDesc = $c.find('.promo-desc-input').val().trim();
            var pImg = $c.find('.promo-image-path-input').val().trim();
            var pActivo = $c.find('.promo-active-switch').is(':checked') ? 1 : 0;

            var pDias = [];
            var isAll = $c.find('.btn-day-pill[data-day="all"]').hasClass('active');
            if (isAll) {
                pDias = ['all'];
            } else {
                $c.find('.btn-day-pill:not([data-day="all"]).active').each(function() {
                    pDias.push($(this).attr('data-day'));
                });
            }

            if (pActivo) {
                activePromos++;
                if (isAll || pDias.indexOf(currentDayCode) !== -1) {
                    todayPromos++;
                }
            }

            promosList.push({
                id: pId,
                titulo: pTitulo,
                badge: pBadge,
                precio_promo: pPrecio,
                descripcion: pDesc,
                dias: pDias,
                imagen: pImg,
                activo: pActivo
            });
        });

        $('#promos_json_field').val(JSON.stringify(promosList));
        $('#lbl_total_promos').text(totalPromos);
        $('#lbl_active_promos').text(activePromos);
        $('#lbl_today_promos').text(todayPromos);
        $('#tab_promos_badge').text(totalPromos);

        if (totalPromos === 0) {
            $('#promos_empty_state').show();
        } else {
            $('#promos_empty_state').hide();
        }
    };

    initPromosModule();
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
                <p class="text-muted small mb-3">Escanea este código o usa las credenciales en la App <strong>Geeks Delivery</strong>.</p>
                
                <div class="row text-left bg-light rounded p-2 mx-1 mb-3 border small">
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block font-weight-bold">Usuario App:</small>
                        <strong class="text-primary font-medium-1" id="qrDriverUser">delivery1</strong>
                    </div>
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block font-weight-bold">Contraseña:</small>
                        <strong class="text-danger font-medium-1" id="qrDriverPassword">delivery123</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block font-weight-bold">Correo:</small>
                        <span class="text-dark font-weight-bold" id="qrDriverEmail">delivery1@geekspos.com</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block font-weight-bold">Teléfono:</small>
                        <span class="text-dark font-weight-bold" id="qrDriverPhone">595981...</span>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <a id="btnSendWhatsappMsg" href="#" target="_blank" class="btn btn-success font-weight-bold">
                        <i class="fa fa-whatsapp mr-1"></i> Enviar Credenciales por WhatsApp
                    </a>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dinámico Moderno de Confirmación -->
<div class="modal fade" id="modalModernConfirm" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div id="modernConfirmIconWrapper" class="mb-3 d-inline-flex align-items-center justify-content-center" style="width: 68px; height: 68px; border-radius: 50%;">
                    <i id="modernConfirmIcon" class="fa fa-exclamation-triangle fa-2x"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2" id="modernConfirmTitle">¿Está seguro?</h4>
                <p class="text-muted mb-4" id="modernConfirmMessage" style="font-size: 14px; line-height: 1.5;"></p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-3 py-2 font-weight-bold mr-2" data-dismiss="modal" style="border-radius: 10px; min-width: 110px;">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-warning px-3 py-2 font-weight-bold shadow-sm" id="btnModernConfirmAction" style="border-radius: 10px; min-width: 130px;">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dinámico de Simulación de Turno -->
<div class="modal fade" id="modalSimulationResult" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 450px;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 18px;">
            <div class="modal-header bg-primary text-white py-2" style="border-top-left-radius: 18px; border-top-right-radius: 18px;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-play-circle mr-1"></i> Simulación de Asignación de Turno
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="badge badge-light border text-primary px-3 py-1 mb-3 font-weight-bold" style="font-size: 12px;">
                    🎯 Simulación de solo lectura (no altera contadores)
                </div>
                <div class="p-3 bg-light rounded-lg border mb-3 text-left">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-dark mr-2 px-2 py-1" style="font-size: 13px;" id="simSlotBadge">Slot #1</span>
                        <h5 class="font-weight-bold text-dark mb-0" id="simDriverName">Repartidor</h5>
                    </div>
                    <div class="small text-muted mb-1">
                        <i class="fa fa-phone mr-1"></i> <strong>Teléfono:</strong> <span id="simDriverPhone">-</span>
                    </div>
                    <div class="small text-muted">
                        <i class="fa fa-motorcycle mr-1"></i> <strong>Pedidos entregados hoy:</strong> <span id="simDriverOrders" class="badge badge-info">0</span>
                    </div>
                </div>
                <p class="text-muted small mb-0">
                    El motor Round-Robin asignaría el próximo pedido a este repartidor por encontrarse activo, libre y con menor carga.
                </p>
            </div>
            <div class="modal-footer bg-light py-2" style="border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                <button type="button" class="btn btn-primary btn-sm px-3" data-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Nueva API Key Rotada -->
<div class="modal fade" id="modalNewApiKey" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 18px;">
            <div class="modal-header bg-success text-white py-2" style="border-top-left-radius: 18px; border-top-right-radius: 18px;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-key mr-1"></i> Nueva API Key Generada
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="alert alert-warning text-left small mb-3">
                    <i class="fa fa-exclamation-triangle mr-1"></i> <strong>Importante:</strong> Esta clave sólo se muestra una vez por motivos de seguridad. Cópiala y actualízala en tus integraciones.
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control font-weight-bold font-monospace bg-light text-primary" id="modalNewApiKeyInput" readonly style="font-size: 14px;">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success font-weight-bold" onclick="navigator.clipboard.writeText($('#modalNewApiKeyInput').val()); toastr.success('¡API Key copiada al portapapeles!');">
                            <i class="fa fa-copy mr-1"></i> Copiar
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2" style="border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa de Flyer Promocional -->
<div class="modal fade" id="modalPromoFlyerPreview" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header bg-gradient-directional-danger text-white py-2" style="background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);">
                <h5 class="modal-title font-weight-bold text-white mb-0" id="modalPromoFlyerTitle">
                    <i class="fa fa-image mr-2"></i> Flyer Promocional
                </h5>
                <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 text-center bg-dark" style="min-height: 250px; display: flex; align-items: center; justify-content: center;">
                <img id="modalPromoFlyerImage" src="" alt="Flyer Promocional" style="max-width: 100%; max-height: 75vh; object-fit: contain;">
            </div>
            <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                <span id="modalPromoFlyerBadge" class="badge badge-danger p-2 font-weight-bold" style="font-size: 13px;"></span>
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación de Local desde Delivery Config -->
<div class="modal fade" id="modalEliminarLocalActual" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-danger text-white p-3">
                <h5 class="modal-title font-weight-bold d-flex align-items-center mb-0 text-white">
                    <i class="fa fa-exclamation-triangle mr-2"></i> Eliminar Local / Sucursal
                </h5>
                <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 28px;">
                        <i class="fa fa-trash"></i>
                    </div>
                </div>
                <h5 class="font-weight-bold text-dark mb-2">¿Estás seguro de eliminar este local?</h5>
                <p class="text-muted mb-3" style="font-size: 14px;">
                    Estás a punto de eliminar permanentemente a: <br>
                    <span class="badge badge-danger px-3 py-2 mt-1" style="font-size: 15px;" id="lbl_del_config_local_name">Local</span>
                </p>

                <div class="alert alert-warning text-left p-3 mb-0" style="font-size: 13px; border-radius: 10px; line-height: 1.5;">
                    <i class="fa fa-warning mr-1 font-weight-bold text-danger"></i> <strong>Esta acción es irreversible.</strong> Se eliminarán en cascada:
                    <ul class="mb-0 pl-3 mt-1 text-muted" style="font-size: 12.5px;">
                        <li>Almacén, catálogo de productos y stock del local.</li>
                        <li>Usuarios y empleados asignados a esta sucursal.</li>
                        <li>Configuración del Bot IA, teléfonos y repartidores.</li>
                        <li>Historial de pedidos delivery y cola de cocina.</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary font-weight-bold px-3" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger font-weight-bold px-4" id="btn_confirmar_eliminar_config_local" onclick="ejecutarEliminacionLocalActual()">
                    <i class="fa fa-trash mr-1"></i> Sí, Eliminar Local Definitivamente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var _localIdActualToDelete = 0;

function modalEliminarLocalActual(locId, locName) {
    _localIdActualToDelete = locId;
    $('#lbl_del_config_local_name').text(locName + ' (ID #' + locId + ')');
    $('#modalEliminarLocalActual').modal('show');
}

function ejecutarEliminacionLocalActual() {
    if (!_localIdActualToDelete || _localIdActualToDelete <= 0) return;

    var $btn = $('#btn_confirmar_eliminar_config_local');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Eliminando...');

    var postData = {
        loc_id: _localIdActualToDelete
    };
    if (typeof crsf_token !== 'undefined' && typeof crsf_hash !== 'undefined') {
        postData[crsf_token] = crsf_hash;
    }

    $.ajax({
        url: '<?= base_url("saas/delete/") ?>' + _localIdActualToDelete,
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(res) {
            $('#modalEliminarLocalActual').modal('hide');
            $btn.prop('disabled', false).html('<i class="fa fa-trash mr-1"></i> Sí, Eliminar Local Definitivamente');
            if (res.status === 'Success') {
                if (typeof toastr !== 'undefined') {
                    toastr.success(res.message);
                }
                setTimeout(function() {
                    window.location.href = '<?= base_url("saas") ?>';
                }, 900);
            } else {
                alert(res.message || 'Error al eliminar');
            }
        },
        error: function(xhr) {
            $('#modalEliminarLocalActual').modal('hide');
            $btn.prop('disabled', false).html('<i class="fa fa-trash mr-1"></i> Sí, Eliminar Local Definitivamente');
            alert('Error de conexión al intentar eliminar el local.');
        }
    });
}
</script>
