<div class="content-body">

    <!-- Mensajes Flash de Éxito / Error -->
    <?php if ($this->session->flashdata('success_msg')): ?>
        <div class="alert alert-success alert-dismissible mb-2" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fa fa-check-circle"></i> <?= $this->session->flashdata('success_msg') ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error_msg')): ?>
        <div class="alert alert-danger alert-dismissible mb-2" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fa fa-exclamation-triangle"></i> <?= $this->session->flashdata('error_msg') ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-gradient-directional-blue white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title text-white mb-0">
                    <i class="fa fa-sitemap"></i> Gestión y Control de Flujo GeeksIA Restaurantes: <strong><?= htmlspecialchars($loc['cname'] ?? 'Local') ?></strong>
                </h4>
                <small class="text-white-50">Administra, actualiza, inicia o detiene el flujo de automatización GeeksIA de este negocio</small>
            </div>
            <div>
                <a href="<?= base_url('saas') ?>" class="btn btn-outline-light btn-sm font-weight-bold">
                    <i class="fa fa-arrow-left"></i> Volver a SaaS
                </a>
            </div>
        </div>

        <div class="card-body">

            <!-- 1. ESTADO EN VIVO Y ACCIONES RÁPIDAS -->
            <div class="row mb-3">
                <div class="col-lg-8 mb-2">
                    <div class="p-3 rounded" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge badge-secondary" style="font-size:11px;">LOCAL ID #<?= $loc_id ?></span>
                                <span class="badge badge-<?= $current_tpl['badge_color'] ?? 'primary' ?> ml-1 font-weight-bold" style="font-size:11px;">
                                    <i class="<?= $current_tpl['icon'] ?? 'fa fa-sitemap' ?> mr-1"></i> <?= $current_tpl['badge'] ?? 'Rubro' ?>
                                </span>
                                <h4 class="font-weight-bold mb-0 mt-1" style="color:#0f172a;">
                                    <?= htmlspecialchars($current_wf['name'] ?? 'Flujo WhatsApp') ?>
                                </h4>
                                <small class="text-muted">
                                    ID de Flujo GeeksIA: <code id="txt_wf_id" class="text-primary font-weight-bold"><?= htmlspecialchars((string)$workflow_id, ENT_QUOTES) ?></code>
                                    <button class="btn btn-xs btn-outline-secondary ml-1" type="button" onclick="navigator.clipboard.writeText(document.getElementById('txt_wf_id').textContent); alert('ID copiado');" title="Copiar ID"><i class="fa fa-copy"></i></button>
                                </small>
                            </div>
                            <div class="text-right">
                                <?php if (!empty($current_wf) && !empty($current_wf['active'])): ?>
                                    <span class="badge badge-success px-2 py-1" style="font-size:13px; border-radius:20px;">
                                        <i class="fa fa-circle"></i> 🟢 ACTIVO & EJECUTANDO
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger px-2 py-1" style="font-size:13px; border-radius:20px;">
                                        <i class="fa fa-circle"></i> 🔴 DETENIDO / PAUSADO
                                    </span>
                                <?php endif; ?>
                                <small class="d-block text-muted mt-1">
                                    Total de Nodos: <strong><?= count($current_wf['nodes'] ?? []) ?></strong>
                                </small>
                            </div>
                        </div>

                        <hr style="margin:12px 0; border-color:#e2e8f0;">

                        <!-- Botones de Acción Inmediata -->
                        <div class="d-flex flex-wrap gap-2 align-items-center" style="gap:8px;">
                            <?php if (!empty($current_wf) && !empty($current_wf['active'])): ?>
                                <a href="<?= base_url('saas/workflow_action/deactivate/' . $loc_id) ?>" class="btn btn-danger btn-sm font-weight-bold" onclick="return confirm('¿Seguro que deseas pausar el bot de este negocio?');">
                                    <i class="fa fa-pause"></i> ⏸️ Detener Flujo
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('saas/workflow_action/activate/' . $loc_id) ?>" class="btn btn-success btn-sm font-weight-bold">
                                    <i class="fa fa-play"></i> ▶️ Iniciar / Activar Flujo
                                </a>
                            <?php endif; ?>

                            <a href="<?= base_url('saas/workflow_action/restart/' . $loc_id) ?>" class="btn btn-warning btn-sm font-weight-bold text-dark" title="Reinicia el flujo en caliente en GeeksIA">
                                <i class="fa fa-refresh"></i> 🔄 Reiniciar Flujo
                            </a>

                            <a href="http://localhost:5678/workflow/<?= rawurlencode((string)$workflow_id) ?>" target="_blank" class="btn btn-outline-primary btn-sm font-weight-bold">
                                <i class="fa fa-external-link"></i> Abrir en Editor Visual GeeksIA (:5678)
                            </a>

                            <a href="<?= base_url('delivery_config?loc_id=' . $loc_id) ?>" target="_blank" class="btn btn-outline-info btn-sm font-weight-bold">
                                <i class="fa fa-motorcycle"></i> Parámetros Operativos (1-10)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vincular a otro flujo existente -->
                <div class="col-lg-4 mb-2">
                    <div class="p-3 rounded h-100" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;">
                        <h6 class="font-weight-bold text-dark mb-2">
                            <i class="fa fa-link text-primary"></i> Cambiar Flujo Asignado
                        </h6>
                        <small class="text-muted d-block mb-2">Selecciona un flujo ya existente en el servidor GeeksIA para este local:</small>

                        <form method="post" action="<?= base_url('saas/workflow_select/' . $loc_id) ?>">
                            <div class="form-group mb-2">
                                <select name="selected_workflow_id" class="form-control form-control-sm">
                                    <?php foreach ($all_workflows as $wf): ?>
                                        <option value="<?= htmlspecialchars((string)$wf['id'], ENT_QUOTES) ?>" <?= ($wf['id'] == $workflow_id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($wf['name']) ?> (<?= !empty($wf['active']) ? 'Activo' : 'Pausado' ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-secondary btn-sm btn-block font-weight-bold">
                                <i class="fa fa-exchange"></i> Asignar a este Negocio
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 2. GALERÍA DE PLANTILLAS DE FLUJO POR RUBRO -->
            <div class="card shadow-sm border mb-3">
                <div class="card-header bg-gradient-directional-purple text-white py-2" style="background:#7c3aed;">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h5 class="mb-0 text-white font-weight-bold">
                                <i class="fa fa-th-large mr-1"></i> Galería de Plantillas de Flujo por Rubro (Restaurante, Óptica, Tienda, Barbería, etc.)
                            </h5>
                            <small class="text-white-50">
                                Asigna el tipo de flujo y comportamiento de IA especializado para este cliente.
                            </small>
                        </div>
                        <div class="col-md-5 text-md-right mt-1 mt-md-0">
                            <span class="badge badge-light px-3 py-1 font-weight-bold text-dark" style="font-size:12px;">
                                Rubro Actual: <span class="text-primary font-weight-bold"><?= $current_tpl['badge'] ?? 'Restaurante' ?></span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="row">
                        <?php if (!empty($templates)): ?>
                            <?php foreach ($templates as $key => $tpl): ?>
                                <?php $isActive = ($key === $current_tpl_key); ?>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <div class="card h-100 mb-0 shadow-sm" style="border: 1.5px solid <?= $isActive ? '#7c3aed' : '#e2e8f0' ?>; border-radius:10px; background: <?= $isActive ? '#faf5ff' : '#ffffff' ?>;">
                                        <div class="card-body p-3 d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge badge-<?= $tpl['badge_color'] ?> font-weight-bold" style="font-size:11px;">
                                                    <i class="<?= $tpl['icon'] ?> mr-1"></i> <?= $tpl['badge'] ?>
                                                </span>
                                                <?php if ($isActive): ?>
                                                    <span class="badge badge-success" style="font-size:10px;">
                                                        <i class="fa fa-check"></i> Activo
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <h6 class="font-weight-bold text-dark mb-1" style="font-size:13.5px;">
                                                <?= $tpl['name'] ?>
                                            </h6>

                                            <p class="text-muted small mb-2" style="font-size:11.5px; line-height:1.35; min-height:46px;">
                                                <?= $tpl['short_desc'] ?>
                                            </p>

                                            <!-- Features -->
                                            <div class="mb-2" style="font-size:11px;">
                                                <?php foreach (array_slice($tpl['features'], 0, 3) as $f): ?>
                                                    <div class="text-truncate mb-1 text-secondary" title="<?= htmlspecialchars($f) ?>">
                                                        <i class="fa fa-check text-success mr-1"></i> <?= htmlspecialchars($f) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="mt-auto pt-2 border-top">
                                                <?php if ($isActive): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-success btn-block font-weight-bold disabled" disabled>
                                                        <i class="fa fa-check-circle"></i> Plantilla Actual
                                                    </button>
                                                <?php else: ?>
                                                    <form method="post" action="<?= base_url('saas/apply_template/' . $loc_id) ?>">
                                                        <input type="hidden" name="template_key" value="<?= $key ?>">
                                                        <div class="mb-1">
                                                            <label class="small text-muted mb-0" style="font-size:10.5px; cursor:pointer;">
                                                                <input type="checkbox" name="overwrite_prompts" value="1" checked> Sobrescribir prompts IA
                                                            </label>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary btn-block font-weight-bold shadow-sm" style="font-size:11.5px;" onclick="return confirm('¿Asignar la plantilla \'<?= addslashes($tpl['name']) ?>\' a este negocio?');">
                                                            <i class="fa fa-bolt"></i> Asignar este Rubro
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 3. ACTUALIZAR FLUJO DE DATOS / CARGAR OTRO JSON -->
            <div class="card shadow-none mb-0" style="border:1.5px dashed #0284c7; border-radius:10px; background:#f0f9ff;">
                <div class="card-header bg-transparent py-2">
                    <h5 class="mb-0 font-weight-bold text-primary">
                        <i class="fa fa-cloud-upload"></i> Actualizar o Cargar Nuevo Flujo de Datos (JSON)
                    </h5>
                    <small class="text-muted">Sube o pega un nuevo archivo JSON exportado para actualizar o reemplazar la lógica de este negocio en GeeksIA Restaurantes.</small>
                </div>

                <div class="card-body p-3">
                    <form method="post" action="<?= base_url('saas/workflow_upload/' . $loc_id) ?>" enctype="multipart/form-data">

                        <!-- Nav tabs para subir archivo o pegar JSON -->
                        <ul class="nav nav-tabs mb-2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active font-weight-bold" id="tab_file_btn" data-toggle="tab" href="#pane_file" role="tab">
                                    <i class="fa fa-file-code-o"></i> Subir Archivo .JSON
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="tab_raw_btn" data-toggle="tab" href="#pane_raw" role="tab">
                                    <i class="fa fa-code"></i> Pegar Código JSON
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mb-3">
                            <!-- Pestaña 1: Archivo .JSON -->
                            <div class="tab-pane active" id="pane_file" role="tabpanel">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark" style="font-size:13px;">Seleccionar archivo .json:</label>
                                    <input type="file" name="workflow_file" class="form-control-file p-2 bg-white rounded border" accept=".json,application/json">
                                    <small class="text-muted mt-1 d-block">
                                        Compatible con archivos de exportación de flujos GeeksIA (ej: <code>flujo_restaurante.json</code>).
                                    </small>
                                </div>
                            </div>

                            <!-- Pestaña 2: Pegar Código Raw -->
                            <div class="tab-pane" id="pane_raw" role="tabpanel">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark" style="font-size:13px;">Pegar contenido JSON:</label>
                                    <textarea name="workflow_raw" rows="6" class="form-control font-monospace" placeholder='{ "name": "Mi Flujo", "nodes": [...], "connections": {...} }' style="font-size:12px; font-family:monospace; background:#fff;"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Opciones de actualización -->
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="font-weight-bold text-dark" style="font-size:12.5px;">Nombre del Flujo (Opcional):</label>
                                <input type="text" name="workflow_name" class="form-control form-control-sm" placeholder="Dejar en blanco para mantener el nombre del archivo">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="font-weight-bold text-dark" style="font-size:12.5px;">Modo de Carga:</label>
                                <div class="d-flex align-items-center" style="gap:15px; margin-top:5px;">
                                    <label class="mb-0 font-weight-bold" style="font-size:12.5px; cursor:pointer;">
                                        <input type="radio" name="upload_mode" value="update" checked> Actualizar flujo actual (ID: <?= htmlspecialchars((string)$workflow_id, ENT_QUOTES) ?>)
                                    </label>
                                    <label class="mb-0 font-weight-bold" style="font-size:12.5px; cursor:pointer;">
                                        <input type="radio" name="upload_mode" value="create_new"> Crear como nuevo flujo exclusivo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-0" style="font-size:13px; cursor:pointer;">
                                <input type="checkbox" name="auto_activate" value="1" checked> ⚡ Activar automáticamente en vivo inmediatamente después de actualizar
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-md font-weight-bold px-3" style="box-shadow:0 2px 6px rgba(0,0,0,0.15);">
                            <i class="fa fa-save"></i> 💾 Aplicar y Actualizar Flujo en GeeksIA
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
