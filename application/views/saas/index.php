<div class="content-body">
    <div class="card">
        <div class="card-header bg-gradient-directional-blue white d-flex justify-content-between align-items-center">
            <h4 class="card-title text-white mb-0">
                <i class="fa fa-cubes"></i> Panel SaaS Multi-Sitio | Negocios & Clientes Activos
            </h4>
            <div>
                <a href="<?= base_url('saas/create') ?>" class="btn btn-warning btn-md font-weight-bold" style="box-shadow:0 2px 6px rgba(0,0,0,0.2);">
                    <i class="fa fa-plus-circle"></i> Nuevo Negocio en 1 Clic
                </a>
            </div>
        </div>

        <div class="card-body">
            <div id="saas_flash_alert"></div>

            <!-- Métricas Generales -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="p-2 rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                        <small class="text-muted font-weight-bold">NEGOCIOS ACTIVOS</small>
                        <h3 class="mb-0 font-weight-bold text-primary"><?= count($locations) + 1 ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-2 rounded" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                        <small class="text-success font-weight-bold">PASARELA WHATSAPP</small>
                        <h3 class="mb-0 font-weight-bold text-success">Evolution API :8081</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-2 rounded" style="background:#f0f9ff; border:1px solid #bae6fd;">
                        <small class="text-info font-weight-bold">AUTOMATIZACIÓN IA</small>
                        <h3 class="mb-0 font-weight-bold text-info">GeeksIA Restaurantes</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-2 rounded" style="background:#faf5ff; border:1px solid #e9d5ff;">
                        <small class="text-purple font-weight-bold">MODALIDAD</small>
                        <h3 class="mb-0 font-weight-bold text-purple" style="color:#7e22ce;">Multi-Tenant SaaS</h3>
                    </div>
                </div>
            </div>

            <!-- Tabla de Negocios / Clientes -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered" style="vertical-align:middle;">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:60px;">ID</th>
                            <th>Negocio / Empresa</th>
                            <th>Rubro / Flujo GeeksIA</th>
                            <th>RUC / Cédula</th>
                            <th>Ciudad / Teléfono</th>
                            <th>Usuario Admin</th>
                            <th>Artículos</th>
                            <th>Ventas</th>
                            <th>WhatsApp Bot</th>
                            <th style="width:160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Escapado: estos datos los pueden editar otros usuarios (XSS almacenado)
                        $e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
                        $js = function ($v) { return json_encode((string)$v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); };
                        ?>
                        <!-- Negocio Principal (Sede Matriz / Nicole) -->
                        <tr style="background:#f8fafc;">
                            <td class="font-weight-bold text-center">
                                <span class="badge badge-primary">Matriz (0)</span>
                            </td>
                            <td>
                                <strong style="font-size:14px; color:#0f172a;"><?= $e($main_company['cname']) ?></strong>
                                <small class="text-muted d-block">Sede Principal / Cuenta Maestra</small>
                            </td>
                            <td>
                                <span class="badge badge-danger font-weight-bold" style="font-size:11.5px; padding:4px 8px;">
                                    <i class="fa fa-cutlery mr-1"></i> 🍔 Gastronomía
                                </span>
                            </td>
                            <td><?= $e($main_company['taxid']) ?></td>
                            <td><?= $e($main_company['city']) ?> <br><small><?= $e($main_company['phone']) ?></small></td>
                            <td><span class="badge badge-info">admin</span></td>
                            <td><span class="badge badge-secondary">Catálogo General</span></td>
                            <td><span class="badge badge-secondary">Ventas Globales</span></td>
                            <td>
                                <span class="badge badge-success">pos_whatsapp</span>
                            </td>
                            <td>
                                <a href="<?= base_url('saas/workflow/0') ?>" class="btn btn-sm btn-outline-primary btn-block mb-1" title="Gestionar Flujo Maestro GeeksIA">
                                    <i class="fa fa-sitemap"></i> Flujo GeeksIA
                                </a>
                                <a href="<?= base_url('pos_invoices/create') ?>" class="btn btn-sm btn-outline-secondary btn-block">
                                    <i class="fa fa-desktop"></i> Ir al POS
                                </a>
                            </td>
                        </tr>

                        <!-- Negocios Tenants Creados -->
                        <?php if (!empty($locations)) {
                            foreach ($locations as $loc) { ?>
                                <tr>
                                    <td class="font-weight-bold text-center">
                                        <span class="badge badge-secondary">#<?= (int)$loc['id'] ?></span>
                                    </td>
                                    <td>
                                        <strong style="font-size:14px; color:#0f172a;"><?= $e($loc['cname']) ?></strong>
                                        <small class="text-muted d-block"><?= $e($loc['address']) ?></small>
                                    </td>
                                    <td>
                                        <?php $r = $loc['rubro_info'] ?? null; ?>
                                        <a href="<?= base_url('saas/workflow/' . $loc['id']) ?>" title="Clic para cambiar de plantilla">
                                            <span class="badge badge-<?= $r['badge_color'] ?? 'primary' ?> font-weight-bold" style="font-size:11.5px; padding:4px 8px;">
                                                <i class="<?= $r['icon'] ?? 'fa fa-sitemap' ?> mr-1"></i> <?= $r['badge'] ?? '🍔 Gastronomía' ?>
                                            </span>
                                        </a>
                                    </td>
                                    <td><strong><?= $loc['taxid'] ? $e($loc['taxid']) : 'Sin RUC' ?></strong></td>
                                    <td><?= $e($loc['city']) ?> <br><small class="text-muted"><?= $e($loc['phone']) ?></small></td>
                                    <td>
                                        <strong class="text-primary"><?= $e($loc['admin_user']) ?></strong>
                                        <small class="text-muted d-block"><?= $e($loc['admin_email']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info font-weight-bold" style="font-size:12px;"><?= $loc['products_count'] ?> platos</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success font-weight-bold" style="font-size:12px;"><?= $loc['invoices_count'] ?> ventas</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success font-weight-bold mb-1" style="display:block;"><?= $loc['whatsapp_instance'] ? $e($loc['whatsapp_instance']) : 'Sin bot' ?></span>
                                        <?php if (!empty($loc['whatsapp_instance'])) { ?>
                                            <a href="javascript:void(0)" onclick="openTenantQR(<?= $e($js($loc['whatsapp_instance'])) ?>)" class="btn btn-xs btn-outline-success btn-block" style="font-size:10.5px; padding:2px 4px;">
                                                <i class="fa fa-qrcode"></i> Ver QR
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('saas/edit/' . $loc['id']) ?>" class="btn btn-sm btn-warning btn-block mb-1 font-weight-bold" title="Editar datos del local, correo, usuario y contraseña">
                                            <i class="fa fa-pencil"></i> Editar Perfil
                                        </a>
                                        <a href="<?= base_url('saas/workflow/' . $loc['id']) ?>" class="btn btn-sm btn-primary btn-block mb-1" title="Gestionar y actualizar flujo de datos GeeksIA">
                                            <i class="fa fa-sitemap"></i> Flujo GeeksIA
                                        </a>
                                        <a href="<?= base_url('saas/impersonate/' . $loc['id']) ?>" class="btn btn-sm btn-success btn-block mb-1" title="Iniciar sesión como este local">
                                            <i class="fa fa-sign-in"></i> Entrar al POS
                                        </a>
                                        <a href="<?= base_url('delivery_config/index/' . $loc['id']) ?>" class="btn btn-sm btn-info btn-block mb-1" title="Configurar teléfonos, bot y repartidores 1-10">
                                            <i class="fa fa-motorcycle"></i> Bot & Deliveries
                                        </a>
                                        <a href="<?= base_url('p/' . (int)$loc['id']) ?>" target="_blank" class="btn btn-sm btn-outline-success btn-block mb-1 font-weight-bold" title="Abrir Web de Pedidos Online con Chat IA">
                                            <i class="fa fa-shopping-bag"></i> Web de Pedidos
                                        </a>
                                        <a href="<?= base_url('delivery_config/index/' . (int)$loc['id']) ?>#tab-geeksia" class="btn btn-sm btn-outline-secondary btn-block mb-1" title="La API key se ve y se rota desde Bot & Deliveries">
                                            <i class="fa fa-key"></i> API Key (••••<?= $e(substr((string)$loc['api_key'], -4)) ?>)
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-block font-weight-bold" onclick="modalEliminarLocal(<?= (int)$loc['id'] ?>, <?= $e($js($loc['cname'])) ?>)" title="Eliminar permanentemente este local y todos sus datos">
                                            <i class="fa fa-trash"></i> Borrar Local
                                        </button>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fa fa-info-circle fa-2x mb-2 d-block"></i>
                                    No hay locales clientes creados todavía. ¡Crea el primero con el botón <strong>"Nuevo Negocio en 1 Clic"</strong>!
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Modal para ver Código QR de cualquier negocio -->
<div class="modal fade" id="modalTenantQR" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center p-3" style="border-radius:12px;">
            <h5 class="font-weight-bold mb-1"><i class="fa fa-whatsapp text-success"></i> Vincular WhatsApp</h5>
            <small class="text-muted d-block mb-3" id="qr_modal_tenant_name">Instancia</small>
            <div id="modal_qr_spinner" style="padding:30px 0;"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i></div>
            <img id="modal_qr_img" src="" alt="QR Code" style="display:none; width:220px; height:220px; margin:0 auto; border-radius:8px; border:1px solid #e2e8f0; padding:6px;">
            <button type="button" class="btn btn-secondary btn-block mt-3" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación de Local -->
<div class="modal fade" id="modalEliminarLocal" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <span class="badge badge-danger px-3 py-2 mt-1" style="font-size: 15px;" id="lbl_del_local_name">Local</span>
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
                <button type="button" class="btn btn-danger font-weight-bold px-4" id="btn_confirmar_eliminar_local" onclick="ejecutarEliminacionLocal()">
                    <i class="fa fa-trash mr-1"></i> Sí, Eliminar Local Definitivamente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var _localIdToDelete = 0;

function modalEliminarLocal(locId, locName) {
    _localIdToDelete = locId;
    $('#lbl_del_local_name').text(locName + ' (ID #' + locId + ')');
    $('#modalEliminarLocal').modal('show');
}

function ejecutarEliminacionLocal() {
    if (!_localIdToDelete || _localIdToDelete <= 0) return;

    var $btn = $('#btn_confirmar_eliminar_local');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Eliminando...');

    var postData = {
        loc_id: _localIdToDelete
    };
    if (typeof crsf_token !== 'undefined' && typeof crsf_hash !== 'undefined') {
        postData[crsf_token] = crsf_hash;
    }

    $.ajax({
        url: '<?= base_url("saas/delete/") ?>' + _localIdToDelete,
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(res) {
            $('#modalEliminarLocal').modal('hide');
            $btn.prop('disabled', false).html('<i class="fa fa-trash mr-1"></i> Sí, Eliminar Local Definitivamente');
            if (res.status === 'Success') {
                $('#saas_flash_alert').html(
                    '<div class="alert alert-success alert-dismissible fade show p-3 mb-3" style="border-radius:12px; font-size:14px;" role="alert">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<strong><i class="fa fa-check-circle mr-1"></i> ¡Local eliminado!</strong> ' + res.message +
                    '</div>'
                );
                $('html, body').animate({ scrollTop: 0 }, 300);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                $('#saas_flash_alert').html(
                    '<div class="alert alert-danger alert-dismissible fade show p-3 mb-3" style="border-radius:12px; font-size:14px;" role="alert">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<strong><i class="fa fa-exclamation-triangle mr-1"></i> Error:</strong> ' + (res.message || 'Error al eliminar') +
                    '</div>'
                );
                $('html, body').animate({ scrollTop: 0 }, 300);
            }
        },
        error: function(xhr) {
            $('#modalEliminarLocal').modal('hide');
            $btn.prop('disabled', false).html('<i class="fa fa-trash mr-1"></i> Sí, Eliminar Local Definitivamente');
            alert('Error de conexión al intentar eliminar el local.');
        }
    });
}

function openTenantQR(instance) {
    $('#qr_modal_tenant_name').text('Instancia: ' + instance);
    $('#modalTenantQR').modal('show');
    $('#modal_qr_spinner').show();
    $('#modal_qr_img').hide();

    $.ajax({
        url: '<?= base_url("pos_invoices/get_whatsapp_qr") ?>',
        type: 'GET',
        data: { instance: instance },
        dataType: 'json',
        success: function(res) {
            $('#modal_qr_spinner').hide();
            if (res && res.qrcode) {
                $('#modal_qr_img').attr('src', res.qrcode).show();
            } else if (res.state === 'open') {
                alert('¡Este WhatsApp ya está conectado y activo!');
                $('#modalTenantQR').modal('hide');
            } else {
                alert('No se pudo obtener el QR. Verifique Evolution API.');
            }
        },
        error: function() {
            $('#modal_qr_spinner').hide();
            alert('Error de conexión.');
        }
    });
}
</script>
