<div class="content-body">
    <div class="card" style="max-width:980px; margin:0 auto;">
        <!-- Header -->
        <div class="card-header bg-gradient-directional-warning white py-2">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h4 class="card-title text-white mb-0 font-weight-bold">
                        <i class="fa fa-pencil-square-o mr-1"></i> Editar Perfil del Negocio: <?= htmlspecialchars($loc['cname']) ?>
                    </h4>
                    <small class="text-white-50">
                        Administre la información comercial, correo, usuario y contraseña de acceso de este local.
                    </small>
                </div>
                <div class="col-md-5 text-md-right mt-2 mt-md-0">
                    <a href="<?= base_url('saas') ?>" class="btn btn-sm btn-outline-light mr-1">
                        <i class="fa fa-arrow-left"></i> Volver a SaaS
                    </a>
                    <a href="<?= base_url('saas/impersonate/' . $loc_id) ?>" class="btn btn-sm btn-success font-weight-bold" title="Iniciar sesión como este local">
                        <i class="fa fa-sign-in"></i> Entrar al POS
                    </a>
                </div>
            </div>
        </div>

        <!-- Ribbon de Estado Rápido -->
        <div class="card-body bg-light border-bottom py-2">
            <div class="row align-items-center small font-weight-bold">
                <div class="col-md-3">
                    <span class="text-muted">ID Local:</span>
                    <span class="badge badge-secondary ml-1">#<?= $loc_id ?></span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">Instancia WhatsApp:</span>
                    <span class="badge badge-success ml-1"><?= htmlspecialchars($loc['whatsapp_instance'] ?: 'Sin bot') ?></span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted">Usuario Admin:</span>
                    <span class="badge badge-primary ml-1"><?= htmlspecialchars($admin_user['username'] ?? 'Sin asignar') ?></span>
                </div>
                <div class="col-md-3 text-md-right">
                    <a href="<?= base_url('p/' . $loc_id) ?>" class="text-success font-weight-bold mr-2" target="_blank" title="Abrir Web de Pedidos Online con IA">
                        <i class="fa fa-shopping-bag"></i> Web Pedidos <i class="fa fa-external-link"></i>
                    </a>
                    <a href="<?= base_url('delivery_config/index/' . $loc_id) ?>" class="text-info font-weight-bold" target="_blank">
                        <i class="fa fa-motorcycle"></i> Bot & Deliveries <i class="fa fa-external-link"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <?php if ($this->session->flashdata('error_msg')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle mr-1"></i> <?= $this->session->flashdata('error_msg') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success_msg')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle mr-1"></i> <?= $this->session->flashdata('success_msg') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form id="form_edit_saas" method="post" action="<?= base_url('saas/edit/' . $loc_id) ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="loc_id" value="<?= $loc_id ?>">

                <div class="row">
                    <!-- ==================== COLUMNA 1: DATOS DEL LOCAL ==================== -->
                    <div class="col-md-6 pr-md-3 border-right">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-primary p-2 mr-2"><i class="fa fa-building fa-lg"></i></span>
                            <div>
                                <h5 class="font-weight-bold mb-0 text-primary">1. Datos Comerciales del Local</h5>
                                <small class="text-muted">Nombre comercial, fiscal y dirección</small>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">Nombre Comercial del Negocio: *</label>
                            <input type="text" name="cname" id="cname" class="form-control font-weight-bold" 
                                   value="<?= htmlspecialchars($loc['cname']) ?>" required autocomplete="off">
                            <small class="text-muted">Aparece en tickets, facturas y mensajes de WhatsApp.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">Rubro / Plantilla GeeksIA: *</label>
                            <select name="tipo_flujo" id="tipo_flujo" class="form-control font-weight-bold">
                                <?php 
                                $current_tf = !empty($loc['tipo_flujo']) ? $loc['tipo_flujo'] : ($wa_config['tipo_flujo'] ?? 'restaurante');
                                foreach ($templates as $t_key => $t_info): 
                                ?>
                                    <option value="<?= $t_key ?>" <?= ($t_key === $current_tf) ? 'selected' : '' ?>>
                                        <?= $t_info['badge'] ?> - <?= htmlspecialchars($t_info['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Ajusta las respuestas y catálogo del bot de WhatsApp para este negocio.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">RUC o Cédula (DNIT Paraguay):</label>
                            <input type="text" name="taxid" class="form-control" 
                                   value="<?= htmlspecialchars($loc['taxid'] ?? '') ?>" placeholder="Ej: 80012345-6" autocomplete="off">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">Teléfono / WhatsApp de Contacto: *</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-phone"></i></span></div>
                                <input type="text" name="phone" class="form-control font-weight-bold" 
                                       value="<?= htmlspecialchars($loc['phone'] ?? '') ?>" placeholder="Ej: 0981123456" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">Dirección del Local:</label>
                            <input type="text" name="address" class="form-control" 
                                   value="<?= htmlspecialchars($loc['address'] ?? '') ?>" placeholder="Ej: Avda. Principal 456" autocomplete="off">
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold" style="font-size:13px;">Ciudad:</label>
                                <input type="text" name="city" class="form-control" 
                                       value="<?= htmlspecialchars($loc['city'] ?? 'Asunción') ?>" autocomplete="off">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold" style="font-size:13px;">Instancia WhatsApp:</label>
                                <input type="text" name="whatsapp_instance" class="form-control font-monospace" 
                                       value="<?= htmlspecialchars($loc['whatsapp_instance'] ?? '') ?>" placeholder="slug_local" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- ==================== COLUMNA 2: USUARIO Y CREDENCIALES ==================== -->
                    <div class="col-md-6 pl-md-3">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-success p-2 mr-2"><i class="fa fa-user-circle fa-lg"></i></span>
                            <div>
                                <h5 class="font-weight-bold mb-0 text-success">2. Credenciales del Administrador</h5>
                                <small class="text-muted">Usuario, correo y contraseña para ingresar al POS</small>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">Nombre de Usuario (Login): *</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user text-primary"></i></span></div>
                                <input type="text" name="username" id="username" class="form-control font-weight-bold" 
                                       value="<?= htmlspecialchars($admin_user['username'] ?? '') ?>" required autocomplete="off">
                            </div>
                            <small class="text-muted">Nombre de usuario con el que inicia sesión en la pantalla de login.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">Correo Electrónico de Acceso: *</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-envelope text-info"></i></span></div>
                                <input type="email" name="email" id="email" class="form-control font-weight-bold" 
                                       value="<?= htmlspecialchars($admin_user['email'] ?? $loc['email']) ?>" required autocomplete="off">
                            </div>
                            <small class="text-muted">Se utiliza para recuperación de contraseña y login alternativo.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">Cambiar Contraseña de Acceso:</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock text-warning"></i></span></div>
                                <input type="password" name="password" id="admin_password" class="form-control font-weight-bold" 
                                       placeholder="Dejar en blanco para no cambiar" autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btn_toggle_pass" title="Ver contraseña">
                                        <i class="fa fa-eye" id="eye_icon"></i>
                                    </button>
                                    <button class="btn btn-outline-primary" type="button" onclick="generateSecurePass()" title="Generar contraseña segura">
                                        <i class="fa fa-random"></i> Generar
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger font-weight-bold">
                                <i class="fa fa-info-circle"></i> Solo escribe en este campo si deseas cambiar la contraseña del local.
                            </small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold" style="font-size:13px;">Estado de la Cuenta:</label>
                            <select name="banned" class="form-control font-weight-bold">
                                <option value="0" <?= (empty($admin_user['banned'])) ? 'selected' : '' ?>>
                                    🟢 Activo (Acceso habilitado al sistema)
                                </option>
                                <option value="1" <?= (!empty($admin_user['banned'])) ? 'selected' : '' ?>>
                                    🔴 Suspendido / Bloqueado (Acceso revocado temporalmente)
                                </option>
                            </select>
                        </div>

                        <!-- Card de resumen de acceso -->
                        <div class="card bg-light border p-3 mt-3">
                            <h6 class="font-weight-bold text-dark mb-2">
                                <i class="fa fa-key text-warning mr-1"></i> Resumen de Inicio de Sesión:
                            </h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1"><strong>🔗 URL de Login:</strong> <a href="<?= base_url('user/login') ?>" target="_blank"><?= base_url('user/login') ?></a></li>
                                <li class="mb-1"><strong>👤 Usuario:</strong> <span class="badge badge-dark"><?= htmlspecialchars($admin_user['username'] ?? 'Sin asignar') ?></span></li>
                                <li><strong>🏢 Almacén Vinculado:</strong> ID #<?= (int)($loc['ware'] ?? 0) ?> (Exclusivo para este negocio)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Botones de Acción -->
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <a href="<?= base_url('saas') ?>" class="btn btn-secondary">
                            <i class="fa fa-times mr-1"></i> Cancelar
                        </a>
                    </div>
                    <div class="col-md-6 text-md-right mt-2 mt-md-0">
                        <button type="submit" class="btn btn-warning btn-lg px-4 font-weight-bold shadow" id="btn_save_edit">
                            <i class="fa fa-save mr-1"></i> Guardar Cambios del Perfil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Alternar visibilidad de contraseña
    $('#btn_toggle_pass').on('click', function() {
        var $input = $('#admin_password');
        var $icon = $('#eye_icon');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Envío por AJAX
    $('#form_edit_saas').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn_save_edit');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Guardando cambios...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Guardar Cambios del Perfil');
                if (resp.status === 'Success') {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(resp.message, 'Perfil Actualizado');
                    } else {
                        alert(resp.message);
                    }
                    setTimeout(function() {
                        window.location.href = '<?= base_url('saas') ?>';
                    }, 1200);
                } else {
                    alert(resp.message || 'Error al guardar los cambios.');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Guardar Cambios del Perfil');
                alert('Ocurrió un error al procesar la solicitud.');
            }
        });
    });
});

function generateSecurePass() {
    var chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#$%";
    var pass = "";
    for (var i = 0; i < 10; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    pass += "26!";
    $('#admin_password').attr('type', 'text').val(pass);
    $('#eye_icon').removeClass('fa-eye').addClass('fa-eye-slash');
}
</script>
