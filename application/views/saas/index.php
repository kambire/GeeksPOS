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
                                        <a href="<?= base_url('saas/workflow/' . $loc['id']) ?>" class="btn btn-sm btn-primary btn-block mb-1" title="Gestionar y actualizar flujo de datos GeeksIA">
                                            <i class="fa fa-sitemap"></i> Flujo GeeksIA
                                        </a>
                                        <a href="<?= base_url('saas/impersonate/' . $loc['id']) ?>" class="btn btn-sm btn-success btn-block mb-1" title="Iniciar sesión como este local">
                                            <i class="fa fa-sign-in"></i> Entrar al POS
                                        </a>
                                        <a href="<?= base_url('delivery_config?loc_id=' . $loc['id']) ?>" class="btn btn-sm btn-info btn-block mb-1" title="Configurar teléfonos, bot y repartidores 1-10">
                                            <i class="fa fa-motorcycle"></i> Bot & Deliveries
                                        </a>
                                        <a href="<?= base_url('delivery_config?loc_id=' . (int)$loc['id']) ?>#tab-geeksia" class="btn btn-sm btn-outline-secondary btn-block" title="La API key se ve y se rota desde Bot & Deliveries">
                                            <i class="fa fa-key"></i> API Key (••••<?= $e(substr((string)$loc['api_key'], -4)) ?>)
                                        </a>
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

<script>
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
