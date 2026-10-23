<div class="content-body">
    <div class="card" style="max-width:850px; margin:0 auto;">
        <div class="card-header bg-gradient-directional-blue white">
            <h4 class="card-title text-white mb-0">
                <i class="fa fa-magic"></i> Crear Nuevo Negocio / Cliente en 1 Clic (Onboarding SaaS)
            </h4>
        </div>

        <div class="card-body p-3">
            <p class="text-muted mb-3" style="font-size:13.5px;">
                Completa los datos del nuevo cliente. El sistema creará automáticamente su <strong>sucursal exclusiva</strong>, su <strong>almacén de productos</strong>, su <strong>usuario administrador</strong>, su <strong>instancia de WhatsApp</strong> y sus <strong>credenciales de API para GeeksIA</strong>.
            </p>

            <form id="form_create_saas" method="post" action="<?= base_url('saas/create') ?>">
                <div class="row">

                    <!-- Columna 1: Datos del Negocio -->
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary mb-2">
                            <i class="fa fa-building"></i> 1. Datos del Negocio / Local
                        </h6>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:12.5px;">Nombre Comercial: *</label>
                            <input type="text" name="cname" id="cname" class="form-control font-weight-bold" placeholder="Ej: Mi Restaurante Demo" required autocomplete="off">
                            <small class="text-muted">Nombre que saldrá en tickets y facturas.</small>
                        </div>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:12.5px;">RUC o Cédula (DNIT Paraguay):</label>
                            <input type="text" name="taxid" class="form-control" placeholder="Ej: 80000000-1" autocomplete="off">
                        </div>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:12.5px;">Teléfono / WhatsApp Oficial: *</label>
                            <input type="text" name="phone" class="form-control font-weight-bold" placeholder="Ej: 0981000000" required autocomplete="off">
                        </div>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:12.5px;">Dirección del Local:</label>
                            <input type="text" name="address" class="form-control" placeholder="Ej: Calle Principal 123" autocomplete="off">
                        </div>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:12.5px;">Ciudad:</label>
                            <input type="text" name="city" class="form-control" value="Asunción" autocomplete="off">
                        </div>
                    </div>

                    <!-- Columna 2: Acceso de Usuario -->
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-success mb-2">
                            <i class="fa fa-user-circle"></i> 2. Usuario Administrador del Local
                        </h6>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:12.5px;">Nombre de Usuario (Login): *</label>
                            <input type="text" name="username" id="username" class="form-control font-weight-bold" placeholder="Ej: demo_admin" required autocomplete="new-password">
                            <small class="text-muted">Sin espacios (se convertirán automáticamente a guiones bajos).</small>
                        </div>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:12.5px;">Contraseña Inicial: *</label>
                            <div class="input-group">
                                <input type="text" name="password" id="password" class="form-control font-weight-bold" placeholder="Ej: ClaveSegura2026!" required autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="$('#password').val(Math.random().toString(36).slice(-8) + 'P@1');">Generar</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="font-weight-bold" style="font-size:12.5px;">Correo Electrónico del Administrador: *</label>
                            <input type="email" name="email" id="email" class="form-control font-weight-bold" placeholder="Ej: admin@midominiodemo.com" required autocomplete="new-password">
                            <small class="text-info font-weight-bold d-block mt-1">
                                <i class="fa fa-info-circle"></i> Debe ser un correo único para este local. No uses tu propio correo de Superadmin.
                            </small>
                        </div>

                        <!-- Automatizaciones Incluidas -->
                        <div class="p-2 rounded mt-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                            <strong style="color:#166534; font-size:12.5px; display:block;">
                                <i class="fa fa-check-circle"></i> Automatizaciones que se desplegarán:
                            </strong>
                            <ul class="mb-0 pl-3 text-muted" style="font-size:11.5px; line-height:1.5;">
                                <li>Instancia dedicada en <strong>Evolution API</strong>.</li>
                                <li>Acceso exclusivo a su catálogo y POS.</li>
                                <li>Clave API única para su flujo de WhatsApp en <strong>GeeksIA Restaurantes</strong>.</li>
                                <li>Almacén propio para que no se mezclen productos con otros clientes.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Fila 2: Rubro del Negocio & Plantilla de Flujo GeeksIA -->
                    <div class="col-12 mt-3">
                        <div class="p-3 rounded" style="background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:10px;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="font-weight-bold text-primary mb-0">
                                    <i class="fa fa-sitemap"></i> 3. Rubro del Negocio & Plantilla de Flujo GeeksIA
                                </h6>
                                <span class="badge badge-info" id="badge_rubro_activo">Personalizable</span>
                            </div>
                            <p class="text-muted small mb-3">
                                Selecciona el rubro de este negocio. El bot GeeksIA adoptará automáticamente la lógica, turnos, preguntas frecuentes y tono de voz adecuados para ese sector.
                            </p>

                            <div class="row align-items-center">
                                <div class="col-md-5 mb-2">
                                    <label class="font-weight-bold" style="font-size:12.5px;">Seleccionar Tipo de Negocio / Flujo: *</label>
                                    <select name="tipo_flujo" id="sel_tipo_flujo" class="form-control font-weight-bold" style="font-size:13.5px; border-color:#0284c7;">
                                        <?php if (!empty($templates)): ?>
                                            <?php foreach ($templates as $key => $tpl): ?>
                                                <option value="<?= $key ?>" <?= ($key === 'restaurante') ? 'selected' : '' ?>>
                                                    <?= $tpl['badge'] ?> - <?= $tpl['name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small class="text-muted d-block mt-1">Podrás cambiar o afinar su flujo en cualquier momento desde el Gestor de Flujos.</small>
                                </div>

                                <div class="col-md-7 mb-2">
                                    <div id="rubro_preview_box" class="p-2 rounded bg-white border" style="min-height:90px;">
                                        <!-- Dinámico -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('saas') ?>" class="btn btn-secondary font-weight-bold">
                        <i class="fa fa-arrow-left"></i> Cancelar y Volver
                    </a>
                    <button type="submit" id="btn_submit_saas" class="btn btn-primary font-weight-bold px-3 py-1" style="font-size:15px;">
                        <i class="fa fa-rocket"></i> Crear y Desplegar Negocio en 1 Clic
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Éxito / Ficha de Entrega al Cliente -->
<div class="modal fade" id="modalSaaSSuccess" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header bg-success text-white py-2">
                <h5 class="modal-title font-weight-bold mb-0">
                    <i class="fa fa-check-circle"></i> ¡Negocio Creado y Desplegado con Éxito!
                </h5>
            </div>
            <div class="modal-body p-3">
                <p class="text-muted mb-2" style="font-size:13px;">
                    El cliente ya puede iniciar sesión y gestionar sus artículos. Puedes copiar estos datos para enviárselos por WhatsApp:
                </p>

                <div class="p-3 rounded mb-3" style="background:#f8fafc; border:1px solid #cbd5e1; font-size:13px; line-height:1.6;" id="delivery_card_text">
                    <strong style="font-size:14px; color:#0f172a;" id="card_business_name">Pepito Burger</strong><br>
                    🌐 <strong>Link de Acceso:</strong> <span id="card_login_url"></span><br>
                    👤 <strong>Usuario:</strong> <span id="card_username" class="font-weight-bold text-primary"></span><br>
                    🔑 <strong>Contraseña:</strong> <span id="card_password" class="font-weight-bold text-danger"></span><br>
                    📱 <strong>Instancia WhatsApp:</strong> <span id="card_wa_instance"></span><br>
                    🔑 <strong>API Key GeeksIA:</strong> <code id="card_apikey" style="font-size:11px;"></code>
                </div>

                <div class="text-center p-2 rounded mb-3" id="wrap_qr_card" style="display:none; background:#f0fdf4; border:1px dashed #10b981;">
                    <strong style="color:#065f46; font-size:12px; display:block; margin-bottom:6px;">Código QR de WhatsApp Generado:</strong>
                    <img id="card_qr_img" src="" style="width:180px; height:180px; margin:0 auto; display:block; border-radius:6px; background:#fff; padding:4px;">
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-block font-weight-bold" id="btn_copy_welcome">
                        <i class="fa fa-copy"></i> Copiar Mensaje de Bienvenida
                    </button>
                    <a href="<?= base_url('saas') ?>" class="btn btn-secondary font-weight-bold ml-2">
                        Ver Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Autocompletar usuario y correo único si escribe el nombre del negocio
    $('#cname').on('input', function() {
        var raw = $(this).val().toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
        if (raw) {
            if (!$('#username').val() || $('#username').data('auto')) {
                $('#username').val(raw + '_admin').data('auto', true);
            }
            if (!$('#email').val() || $('#email').data('auto')) {
                $('#email').val('admin@' + raw.replace(/_/g, '') + '.com').data('auto', true);
            }
            if (!$('#password').val()) {
                $('#password').val(Math.random().toString(36).slice(-8) + 'P@1');
            }
        }
    });

    // Sanitizar automáticamente el nombre de usuario (reemplaza espacios por guiones bajos)
    $('#username').on('input', function() {
        $(this).data('auto', false);
        var clean = $(this).val().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_.-]/g, '');
        $(this).val(clean);
    });

    $('#email').on('input', function() {
        $(this).data('auto', false);
    });

    // Preview interactivo del rubro seleccionado
    var templatesData = <?= json_encode($templates ?? []) ?>;

    function renderRubroPreview(key) {
        var tpl = templatesData[key] || templatesData['restaurante'];
        if (!tpl) return;
        $('#badge_rubro_activo').text(tpl.badge);
        var badgesHtml = '';
        if (tpl.features && tpl.features.length) {
            tpl.features.slice(0, 3).forEach(function(f) {
                badgesHtml += '<span class="badge badge-light border mr-1 mb-1 text-dark" style="font-size:10.5px;"><i class="fa fa-check text-success mr-1"></i>' + f + '</span>';
            });
        }
        var html = '<div class="d-flex align-items-start">' +
                   '  <div class="mr-2 text-center" style="width:38px; height:38px; border-radius:8px; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">' +
                   '    <i class="' + tpl.icon + ' text-primary" style="font-size:18px;"></i>' +
                   '  </div>' +
                   '  <div style="flex:1;">' +
                   '    <strong class="text-dark d-block" style="font-size:13px;">' + tpl.name + '</strong>' +
                   '    <p class="text-muted mb-1" style="font-size:11.5px; line-height:1.35;">' + tpl.short_desc + '</p>' +
                   '    <div>' + badgesHtml + '</div>' +
                   '  </div>' +
                   '</div>';
        $('#rubro_preview_box').html(html);
    }

    $('#sel_tipo_flujo').on('change', function() {
        renderRubroPreview($(this).val());
    });
    renderRubroPreview($('#sel_tipo_flujo').val());

    $('#form_create_saas').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn_submit_saas');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creando negocio y desplegando servicios...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-rocket"></i> Crear y Desplegar Negocio en 1 Clic');
                if (res.status === 'Success') {
                    var d = res.data;
                    $('#card_business_name').text(d.business_name);
                    $('#card_login_url').text(d.login_url);
                    $('#card_username').text(d.username);
                    $('#card_password').text(d.password);
                    $('#card_wa_instance').text(d.whatsapp_instance);
                    $('#card_apikey').text(d.api_key);

                    if (d.qr_code) {
                        $('#card_qr_img').attr('src', d.qr_code);
                        $('#wrap_qr_card').show();
                    }

                    $('#modalSaaSSuccess').modal('show');
                } else {
                    alert('Error: ' + (res.message || 'No se pudo crear'));
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-rocket"></i> Crear y Desplegar Negocio en 1 Clic');
                alert('Error al conectar con el servidor.');
            }
        });
    });

    $('#btn_copy_welcome').on('click', function() {
        var text = '🎉 ¡Bienvenido a su plataforma de Punto de Venta & Bot de WhatsApp!\n\n' +
                   '🏢 Negocio: ' + $('#card_business_name').text() + '\n' +
                   '🌐 Acceso al sistema: ' + $('#card_login_url').text() + '\n' +
                   '👤 Usuario: ' + $('#card_username').text() + '\n' +
                   '🔑 Contraseña: ' + $('#card_password').text() + '\n\n' +
                   'Ya puede ingresar a cargar sus productos, precios y menú.';
        navigator.clipboard.writeText(text);
        alert('¡Mensaje de bienvenida copiado al portapapeles!');
    });
});
</script>
