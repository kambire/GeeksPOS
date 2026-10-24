<link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/vendors/css/extensions/toastr.css">
<script src="<?= assets_url() ?>app-assets/vendors/js/extensions/toastr.min.js"></script>

<div class="content-body">
    <div class="card">
        <!-- Card Header -->
        <div class="card-header border-bottom py-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="card-title font-weight-bold mb-0 text-primary">
                        <i class="fa fa-motorcycle mr-1 text-warning"></i> Monitor de Pedidos & Reasignación de Deliveries
                    </h3>
                    <small class="text-muted">
                        Visualice en tiempo real los pedidos asignados a cada repartidor y reasigne pedidos con notificación instantánea.
                    </small>
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    <?php if ($is_superadmin && !empty($all_locations)): ?>
                        <div class="d-inline-block text-left mr-2">
                            <select id="select_location_orders" class="form-control form-control-sm select2" onchange="window.location.href='<?= base_url('delivery_config/orders/') ?>' + this.value;">
                                <?php foreach ($all_locations as $loc_item): ?>
                                    <option value="<?= $loc_item['id'] ?>" <?= ($loc_item['id'] == $loc_id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc_item['cname']) ?> (ID: <?= $loc_item['id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                                <option value="0" <?= ($loc_id == 0) ? 'selected' : '' ?>>🏢 Principal / Matriz (0)</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <button type="button" class="btn btn-sm btn-outline-info font-weight-bold" onclick="refreshOrdersTable(true);">
                        <i class="fa fa-refresh" id="refreshIcon"></i> Actualizar
                    </button>
                    <a href="<?= base_url('delivery_config/index/' . $loc_id) ?>" class="btn btn-sm btn-outline-primary ml-1">
                        <i class="fa fa-cogs"></i> Turnos & WhatsApp
                    </a>
                    <a href="<?= base_url('kitchen') ?>" target="_blank" class="btn btn-sm btn-outline-danger ml-1">
                        <i class="fa fa-cutlery"></i> Pantalla Cocina
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="card-body bg-light border-bottom py-2">
            <div class="row">
                <?php
                $cnt_activos = 0;
                $cnt_camino = 0;
                $cnt_entregados = 0;
                $total_monto_hoy = 0;
                foreach ($orders as $o) {
                    if (in_array($o['estado'], ['asignado', 'en_camino', 'en_puerta'])) {
                        $cnt_activos++;
                        if ($o['estado'] === 'en_camino') $cnt_camino++;
                    } elseif ($o['estado'] === 'entregado') {
                        $cnt_entregados++;
                        $total_monto_hoy += (float)$o['monto_total'];
                    }
                }

                $drivers_activos = 0;
                foreach ($drivers as $d) {
                    if (!empty($d['telefono'])) $drivers_activos++;
                }
                ?>
                <div class="col-md-3 col-sm-6 mb-1 mb-md-0">
                    <div class="d-flex align-items-center p-2 bg-white rounded shadow-sm border">
                        <div class="p-2 bg-warning-light rounded mr-2 text-warning">
                            <i class="fa fa-clock-o fa-2x"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small">Pedidos en Curso</span>
                            <h4 class="mb-0 font-weight-bold text-dark" id="kpiActivos"><?= $cnt_activos ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-1 mb-md-0">
                    <div class="d-flex align-items-center p-2 bg-white rounded shadow-sm border">
                        <div class="p-2 bg-info-light rounded mr-2 text-info">
                            <i class="fa fa-motorcycle fa-2x"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small">En Camino / En Puerta</span>
                            <h4 class="mb-0 font-weight-bold text-info" id="kpiCamino"><?= $cnt_camino ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-1 mb-md-0">
                    <div class="d-flex align-items-center p-2 bg-white rounded shadow-sm border">
                        <div class="p-2 bg-success-light rounded mr-2 text-success">
                            <i class="fa fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small">Entregados Hoy</span>
                            <h4 class="mb-0 font-weight-bold text-success" id="kpiEntregados"><?= $cnt_entregados ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-1 mb-md-0">
                    <div class="d-flex align-items-center p-2 bg-white rounded shadow-sm border">
                        <div class="p-2 bg-primary-light rounded mr-2 text-primary">
                            <i class="fa fa-users fa-2x"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small">Repartidores en Local</span>
                            <h4 class="mb-0 font-weight-bold text-primary" id="kpiDrivers"><?= $drivers_activos ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Buttons and Live Indicator -->
        <div class="card-body py-2 border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                    <button type="button" class="btn btn-primary active filter-btn" data-status="todos" onclick="filterOrders('todos', this);">
                        Todos (<span class="cnt-all"><?= count($orders) ?></span>)
                    </button>
                    <button type="button" class="btn btn-outline-warning filter-btn" data-status="asignado" onclick="filterOrders('asignado', this);">
                        Asignados
                    </button>
                    <button type="button" class="btn btn-outline-info filter-btn" data-status="en_camino" onclick="filterOrders('en_camino', this);">
                        En Camino
                    </button>
                    <button type="button" class="btn btn-outline-purple filter-btn" data-status="en_puerta" onclick="filterOrders('en_puerta', this);" style="border-color:#8b5cf6; color:#8b5cf6;">
                        En Puerta
                    </button>
                    <button type="button" class="btn btn-outline-success filter-btn" data-status="entregado" onclick="filterOrders('entregado', this);">
                        Entregados
                    </button>
                </div>
                <div class="small text-muted">
                    <span class="badge badge-success badge-pill mr-1">●</span> Auto-refresco en vivo activo (<span id="autoTimer">8</span>s)
                </div>
            </div>
        </div>

        <!-- Orders Table Card Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="ordersTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 130px;">Pedido</th>
                            <th style="min-width: 190px;">Repartidor Asignado</th>
                            <th style="min-width: 200px;">Cliente & Destino</th>
                            <th style="width: 140px;">Monto & Pago</th>
                            <th style="width: 130px;">Estado</th>
                            <th style="width: 120px;">Tiempo</th>
                            <th style="width: 160px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTbody">
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fa fa-inbox fa-3x d-block mb-2 text-muted"></i>
                                    No hay pedidos de delivery registrados para este local hoy.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $ord): ?>
                                <?php
                                $estado_class = 'badge-secondary';
                                $estado_lbl = ucfirst($ord['estado']);
                                if ($ord['estado'] === 'asignado') {
                                    $estado_class = 'badge-warning text-dark';
                                    $estado_lbl = '🟡 Asignado';
                                } elseif ($ord['estado'] === 'en_camino') {
                                    $estado_class = 'badge-info';
                                    $estado_lbl = '🛵 En Camino';
                                } elseif ($ord['estado'] === 'en_puerta') {
                                    $estado_class = 'badge-primary';
                                    $estado_lbl = '📍 En Puerta';
                                } elseif ($ord['estado'] === 'entregado') {
                                    $estado_class = 'badge-success';
                                    $estado_lbl = '✅ Entregado';
                                } elseif ($ord['estado'] === 'cancelado') {
                                    $estado_class = 'badge-danger';
                                    $estado_lbl = '❌ Cancelado';
                                }

                                $min = (int)$ord['minutos_transcurridos'];
                                $time_badge = 'badge-success';
                                if ($min > 35) {
                                    $time_badge = 'badge-danger';
                                } elseif ($min >= 20) {
                                    $time_badge = 'badge-warning text-dark';
                                }
                                $is_active = in_array($ord['estado'], ['asignado', 'en_camino', 'en_puerta']);
                                ?>
                                <tr class="order-row order-status-<?= $ord['estado'] ?>" data-status="<?= $ord['estado'] ?>" id="row-<?= $ord['id'] ?>">
                                    <td>
                                        <strong class="text-primary font-weight-bold" style="font-size: 15px;">
                                            <?= htmlspecialchars($ord['order_number']) ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= !empty($ord['assigned_at']) ? date('H:i', strtotime($ord['assigned_at'])) : date('H:i', strtotime($ord['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-dark mr-2" style="font-size: 12px; padding: 5px 8px;">
                                                Slot #<?= $ord['delivery_slot'] ?>
                                            </span>
                                            <div>
                                                <strong class="d-block text-dark font-weight-bold">
                                                    <?= htmlspecialchars($ord['delivery_nombre'] ?: 'Repartidor ' . $ord['delivery_slot']) ?>
                                                </strong>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($ord['delivery_telefono'] ?: 'Sin teléfono') ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="d-block text-dark">
                                            <i class="fa fa-user text-muted mr-1"></i><?= htmlspecialchars($ord['cliente_nombre']) ?>
                                        </strong>
                                        <small class="text-muted d-block text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($ord['direccion_entrega']) ?>">
                                            <i class="fa fa-map-marker text-danger mr-1"></i><?= htmlspecialchars($ord['direccion_entrega']) ?>
                                        </small>
                                        <?php if (!empty($ord['maps_link'])): ?>
                                            <a href="<?= htmlspecialchars($ord['maps_link']) ?>" target="_blank" class="badge badge-outline-info" style="font-size: 10px;">
                                                <i class="fa fa-external-link"></i> Abrir GPS
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-dark font-weight-bold" style="font-size: 14px;">
                                            <?= number_format((float)$ord['monto_total'], 0, ',', '.') ?> Gs.
                                        </strong>
                                        <br>
                                        <span class="badge badge-light border text-uppercase" style="font-size: 10px;">
                                            <?= htmlspecialchars($ord['metodo_pago']) ?>
                                        </span>
                                        <?php if ($ord['paga_con'] && $ord['paga_con'] > 0): ?>
                                            <small class="d-block text-warning font-weight-bold">
                                                Paga: <?= number_format((float)$ord['paga_con'], 0, ',', '.') ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $estado_class ?>" style="font-size: 11.5px; padding: 5px 8px;">
                                            <?= $estado_lbl ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $time_badge ?>" style="font-size: 11.5px; padding: 4px 7px;">
                                            <i class="fa fa-clock-o mr-1"></i><?= $min ?> min
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($is_active): ?>
                                            <button type="button" class="btn btn-sm btn-warning font-weight-bold shadow-sm"
                                                    onclick="openReassignModal(<?= $ord['id'] ?>, '<?= htmlspecialchars(addslashes($ord['order_number'])) ?>', '<?= htmlspecialchars(addslashes($ord['delivery_nombre'] ?: 'Repartidor ' . $ord['delivery_slot'])) ?>', <?= $ord['delivery_slot'] ?>, '<?= htmlspecialchars(addslashes($ord['cliente_nombre'])) ?>', '<?= number_format((float)$ord['monto_total'], 0, ',', '.') ?> Gs.');">
                                                <i class="fa fa-exchange"></i> Reasignar
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="fa fa-check text-success"></i> Completado</span>
                                        <?php endif; ?>

                                        <?php if (!empty($ord['tracking_url'])): ?>
                                            <a href="<?= $ord['tracking_url'] ?>" target="_blank" class="btn btn-sm btn-outline-info ml-1" title="Ver Tracking en Vivo">
                                                <i class="fa fa-map-marker"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Reasignación de Repartidor -->
<div class="modal fade" id="modalReassign" tabindex="-1" role="dialog" aria-labelledby="modalReassignLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 14px;">
            <div class="modal-header bg-primary text-white py-2" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                <h5 class="modal-title font-weight-bold" id="modalReassignLabel">
                    <i class="fa fa-exchange mr-1"></i> Reasignar Repartidor
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formReassign" onsubmit="submitReassign(event);">
                <input type="hidden" name="order_id" id="reassign_order_id">
                <input type="hidden" name="loc_id" value="<?= $loc_id ?>">
                <div class="modal-body py-3">
                    <!-- Resumen del pedido -->
                    <div class="p-2 mb-3 bg-light rounded border">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Pedido:</small>
                                <strong class="text-primary font-weight-bold" id="modal_order_num" style="font-size: 16px;">-</strong>
                            </div>
                            <div class="col-6 text-right">
                                <small class="text-muted d-block">Total:</small>
                                <strong class="text-success font-weight-bold" id="modal_order_total" style="font-size: 16px;">-</strong>
                            </div>
                        </div>
                        <div class="mt-1">
                            <small class="text-muted">Cliente: </small>
                            <span class="font-weight-bold text-dark" id="modal_order_cliente">-</span>
                        </div>
                    </div>

                    <!-- Repartidor actual -->
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Repartidor Actual:</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white"><i class="fa fa-user-times text-danger"></i></span>
                            </div>
                            <input type="text" class="form-control" id="modal_current_driver" readonly style="background-color: #f8fafc; font-weight: bold;">
                        </div>
                    </div>

                    <!-- Selector de nuevo repartidor -->
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold text-dark mb-1">
                            Seleccionar Nuevo Repartidor: <span class="text-danger">*</span>
                        </label>
                        <select name="new_slot" id="modal_new_slot" class="form-control" required style="font-weight: 600;">
                            <option value="">-- Seleccionar repartidor disponible --</option>
                            <?php foreach ($drivers as $dr): ?>
                                <option value="<?= $dr['slot'] ?>" data-slot="<?= $dr['slot'] ?>">
                                    [Slot #<?= $dr['slot'] ?>] <?= htmlspecialchars($dr['nombre']) ?> - <?= $dr['estado_texto'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            Se enviará una alerta automática por WhatsApp y sonará la alarma en su app móvil.
                        </small>
                    </div>

                    <!-- Motivo de reasignación -->
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted mb-1">Motivo / Observación (Opcional):</label>
                        <input type="text" name="motivo" id="modal_motivo" class="form-control form-control-sm" placeholder="Ej: Pinchó rueda, demora en tránsito, reasignación por zona...">
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold" id="btnSubmitReassign">
                        <i class="fa fa-check mr-1"></i> Confirmar Reasignación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var currentLocId = <?= $loc_id ?>;
var currentStatusFilter = 'todos';
var autoRefreshSeconds = 8;
var timerInterval = null;
var currentDriversList = <?= json_encode($drivers) ?>;

function openReassignModal(orderId, orderNum, currentDriver, currentSlot, cliente, total) {
    $('#reassign_order_id').val(orderId);
    $('#modal_order_num').text(orderNum);
    $('#modal_order_total').text(total);
    $('#modal_order_cliente').text(cliente);
    $('#modal_current_driver').val('Slot #' + currentSlot + ' - ' + currentDriver);
    $('#modal_motivo').val('');

    // Deshabilitar la opción del repartidor actual en el selector
    $('#modal_new_slot option').each(function() {
        if ($(this).data('slot') == currentSlot) {
            $(this).prop('disabled', true).text($(this).text().replace(' (Actual)', '') + ' (Actual)');
        } else {
            $(this).prop('disabled', false).text($(this).text().replace(' (Actual)', ''));
        }
    });
    $('#modal_new_slot').val('');

    $('#modalReassign').modal('show');
}

function submitReassign(e) {
    e.preventDefault();
    var orderId = $('#reassign_order_id').val();
    var newSlot = $('#modal_new_slot').val();
    var motivo = $('#modal_motivo').val();

    if (!newSlot) {
        if (typeof toastr !== 'undefined') toastr.error('Por favor selecciona un nuevo repartidor');
        else alert('Por favor selecciona un nuevo repartidor');
        return;
    }

    var btn = $('#btnSubmitReassign');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Reasignando...');

    $.ajax({
        url: '<?= base_url("delivery_config/ajax_reassign") ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            order_id: orderId,
            new_slot: newSlot,
            motivo: motivo,
            loc_id: currentLocId
        },
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirmar Reasignación');
            if (res.status) {
                $('#modalReassign').modal('hide');
                if (typeof toastr !== 'undefined') {
                    toastr.success(res.message, '¡Reasignación Exitosa!');
                }
                refreshOrdersTable(false);
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(res.message || 'Error al reasignar', 'Error');
                } else {
                    alert(res.message || 'Error al reasignar');
                }
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirmar Reasignación');
            if (typeof toastr !== 'undefined') {
                toastr.error('Error de conexión con el servidor', 'Error');
            } else {
                alert('Error de conexión con el servidor');
            }
        }
    });
}

function filterOrders(status, btnElement) {
    currentStatusFilter = status;
    $('.filter-btn').removeClass('btn-primary btn-warning btn-info btn-success').addClass('btn-outline-secondary');
    $(btnElement).removeClass('btn-outline-secondary').addClass('btn-primary');

    if (status === 'todos') {
        $('.order-row').show();
    } else {
        $('.order-row').hide();
        $('.order-status-' + status).show();
    }
}

function refreshOrdersTable(showToast) {
    var icon = $('#refreshIcon');
    icon.addClass('fa-spin');

    $.ajax({
        url: '<?= base_url("delivery_config/ajax_orders") ?>',
        type: 'GET',
        dataType: 'json',
        data: {
            loc_id: currentLocId,
            estado: 'todos'
        },
        success: function(data) {
            icon.removeClass('fa-spin');
            if (data && data.status) {
                currentDriversList = data.drivers || [];
                renderOrdersTable(data.orders || []);
                if (showToast && typeof toastr !== 'undefined') {
                    toastr.info('Pedidos actualizados (' + data.time + ')', 'Sincronizado');
                }
            }
        },
        error: function() {
            icon.removeClass('fa-spin');
        }
    });
}

function renderOrdersTable(orders) {
    var tbody = $('#ordersTbody');
    if (!orders || orders.length === 0) {
        tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fa fa-inbox fa-3x d-block mb-2 text-muted"></i>No hay pedidos de delivery registrados para este local hoy.</td></tr>');
        $('#kpiActivos').text(0);
        $('#kpiCamino').text(0);
        $('#kpiEntregados').text(0);
        return;
    }

    var html = '';
    var cntActivos = 0;
    var cntCamino = 0;
    var cntEntregados = 0;

    for (var i = 0; i < orders.length; i++) {
        var ord = orders[i];
        var isAct = (ord.estado === 'asignado' || ord.estado === 'en_camino' || ord.estado === 'en_puerta');
        if (isAct) {
            cntActivos++;
            if (ord.estado === 'en_camino') cntCamino++;
        } else if (ord.estado === 'entregado') {
            cntEntregados++;
        }

        var estadoClass = 'badge-secondary';
        var estadoLbl = ord.estado;
        if (ord.estado === 'asignado') {
            estadoClass = 'badge-warning text-dark';
            estadoLbl = '🟡 Asignado';
        } else if (ord.estado === 'en_camino') {
            estadoClass = 'badge-info';
            estadoLbl = '🛵 En Camino';
        } else if (ord.estado === 'en_puerta') {
            estadoClass = 'badge-primary';
            estadoLbl = '📍 En Puerta';
        } else if (ord.estado === 'entregado') {
            estadoClass = 'badge-success';
            estadoLbl = '✅ Entregado';
        } else if (ord.estado === 'cancelado') {
            estadoClass = 'badge-danger';
            estadoLbl = '❌ Cancelado';
        }

        var min = ord.minutos_transcurridos || 0;
        var timeBadge = 'badge-success';
        if (min > 35) timeBadge = 'badge-danger';
        else if (min >= 20) timeBadge = 'badge-warning text-dark';

        var totalFmt = Number(ord.monto_total || 0).toLocaleString('es-PY') + ' Gs.';
        var pagaConHtml = '';
        if (ord.pagaCon && ord.pagaCon > 0) {
            pagaConHtml = '<small class="d-block text-warning font-weight-bold">Paga: ' + Number(ord.pagaCon).toLocaleString('es-PY') + '</small>';
        }

        var horaStr = (ord.assigned_at || ord.created_at || '').substring(11, 16);
        var drvNombre = ord.delivery_nombre || ('Repartidor ' + ord.delivery_slot);
        var drvTel = ord.delivery_telefono || 'Sin teléfono';

        var mapsBtn = ord.maps_link ? ('<a href="' + ord.maps_link + '" target="_blank" class="badge badge-outline-info" style="font-size:10px;"><i class="fa fa-external-link"></i> Abrir GPS</a>') : '';
        var trackBtn = ord.tracking_url ? ('<a href="' + ord.tracking_url + '" target="_blank" class="btn btn-sm btn-outline-info ml-1" title="Ver Tracking en Vivo"><i class="fa fa-map-marker"></i></a>') : '';

        var reassignBtn = '';
        if (isAct) {
            reassignBtn = '<button type="button" class="btn btn-sm btn-warning font-weight-bold shadow-sm" onclick="openReassignModal(' + ord.id + ', \'' + ord.order_number + '\', \'' + drvNombre.replace(/'/g, "\\'") + '\', ' + ord.delivery_slot + ', \'' + (ord.cliente_nombre || '').replace(/'/g, "\\'") + '\', \'' + totalFmt + '\');"><i class="fa fa-exchange"></i> Reasignar</button>';
        } else {
            reassignBtn = '<span class="text-muted small"><i class="fa fa-check text-success"></i> Completado</span>';
        }

        html += '<tr class="order-row order-status-' + ord.estado + '" data-status="' + ord.estado + '" id="row-' + ord.id + '">';
        html += '<td><strong class="text-primary font-weight-bold" style="font-size:15px;">' + ord.order_number + '</strong><br><small class="text-muted">' + horaStr + '</small></td>';
        html += '<td><div class="d-flex align-items-center"><span class="badge badge-dark mr-2" style="font-size:12px; padding:5px 8px;">Slot #' + ord.delivery_slot + '</span><div><strong class="d-block text-dark font-weight-bold">' + drvNombre + '</strong><small class="text-muted">' + drvTel + '</small></div></div></td>';
        html += '<td><strong class="d-block text-dark"><i class="fa fa-user text-muted mr-1"></i>' + (ord.cliente_nombre || '') + '</strong><small class="text-muted d-block text-truncate" style="max-width:250px;"><i class="fa fa-map-marker text-danger mr-1"></i>' + (ord.direccion_entrega || '') + '</small>' + mapsBtn + '</td>';
        html += '<td><strong class="text-dark font-weight-bold" style="font-size:14px;">' + totalFmt + '</strong><br><span class="badge badge-light border text-uppercase" style="font-size:10px;">' + (ord.metodo_pago || '') + '</span>' + pagaConHtml + '</td>';
        html += '<td><span class="badge ' + estadoClass + '" style="font-size:11.5px; padding:5px 8px;">' + estadoLbl + '</span></td>';
        html += '<td><span class="badge ' + timeBadge + '" style="font-size:11.5px; padding:4px 7px;"><i class="fa fa-clock-o mr-1"></i>' + min + ' min</span></td>';
        html += '<td class="text-center">' + reassignBtn + trackBtn + '</td>';
        html += '</tr>';
    }

    tbody.html(html);
    $('#kpiActivos').text(cntActivos);
    $('#kpiCamino').text(cntCamino);
    $('#kpiEntregados').text(cntEntregados);
    $('.cnt-all').text(orders.length);

    if (currentStatusFilter !== 'todos') {
        $('.order-row').hide();
        $('.order-status-' + currentStatusFilter).show();
    }
}

// Inicializar temporizador de actualización automática
$(document).ready(function() {
    var timerCountdown = autoRefreshSeconds;
    setInterval(function() {
        timerCountdown--;
        if (timerCountdown <= 0) {
            refreshOrdersTable(false);
            timerCountdown = autoRefreshSeconds;
        }
        $('#autoTimer').text(timerCountdown);
    }, 1000);
});
</script>
