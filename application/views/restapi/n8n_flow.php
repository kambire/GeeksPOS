<div class="content-body">
    <div class="card">
        <div class="card-header bg-gradient-directional-blue white">
            <h4 class="card-title text-white mb-0">
                <i class="fa fa-sitemap"></i> Centro de Integración: WhatsApp + GeeksIA Restaurantes + GeeksPOS
            </h4>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>

        <div class="card-body">

            <!-- 1. BARRA DE ESTADO DE LOS SERVICIOS -->
            <div class="row mb-3">
                <div class="col-md-4 mb-1">
                    <div class="p-2 rounded" style="background:#f0fdf4; border:1px solid #bbf7d0; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <strong style="color:#166534; font-size:14px; display:block;"><i class="fa fa-server"></i> GeeksIA Restaurantes</strong>
                            <small style="color:#15803d;">Motor de Flujos & Agente IA</small>
                        </div>
                        <span class="badge badge-success" style="font-size:12px; padding:6px 10px;">🟢 Activo :5678</span>
                    </div>
                </div>
                <div class="col-md-4 mb-1">
                    <div class="p-2 rounded" style="background:#f0f9ff; border:1px solid #bae6fd; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <strong style="color:#0369a1; font-size:14px; display:block;"><i class="fa fa-whatsapp"></i> Evolution API</strong>
                            <small style="color:#0284c7;">Pasarela WhatsApp Baileys</small>
                        </div>
                        <span class="badge badge-info" style="font-size:12px; padding:6px 10px;">🔵 Activo :8081</span>
                    </div>
                </div>
                <div class="col-md-4 mb-1">
                    <div class="p-2 rounded" style="background:#faf5ff; border:1px solid #e9d5ff; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <strong style="color:#6b21a8; font-size:14px; display:block;"><i class="fa fa-plug"></i> GeeksPOS REST API</strong>
                            <small style="color:#7e22ce;">Endpoints y Webhooks</small>
                        </div>
                        <span class="badge badge-primary" style="font-size:12px; padding:6px 10px;">🟣 Online :8080</span>
                    </div>
                </div>
            </div>

            <div class="row">

                <!-- 2. DIAGRAMA VISUAL DEL FLUJO COMPLETO -->
                <div class="col-lg-7 mb-3">
                    <div class="card shadow-none" style="border:1px solid #e2e8f0; border-radius:10px;">
                        <div class="card-header bg-light py-2">
                            <h5 class="mb-0 font-weight-bold" style="font-size:14.5px; color:#1e293b;">
                                <i class="fa fa-project-diagram text-primary"></i> Arquitectura del Flujo Automatizado
                            </h5>
                        </div>
                        <div class="card-body p-3">

                            <!-- Paso 1 -->
                            <div class="p-2 rounded mb-2" style="background:#f0fdf4; border-left:4px solid #25d366; display:flex; align-items:center; gap:12px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#25d366; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                                    <i class="fa fa-commenting"></i>
                                </div>
                                <div style="flex:1;">
                                    <strong style="color:#14532d; font-size:13.5px; display:block;">1. Mensaje Entrante del Cliente</strong>
                                    <small style="color:#166534;">El cliente escribe por WhatsApp: <em>"Hola, quiero ver el menú y pedir 2 hamburguesas para delivery"</em>.</small>
                                </div>
                            </div>

                            <div class="text-center text-muted mb-2"><i class="fa fa-arrow-down fa-lg"></i></div>

                            <!-- Paso 2 -->
                            <div class="p-2 rounded mb-2" style="background:#f0f9ff; border-left:4px solid #0284c7; display:flex; align-items:center; gap:12px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#0284c7; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                                    <i class="fa fa-whatsapp"></i>
                                </div>
                                <div style="flex:1;">
                                    <strong style="color:#0369a1; font-size:13.5px; display:block;">2. Evolution API (Puerto 8081)</strong>
                                    <small style="color:#075985;">La pasarela recibe el mensaje en milisegundos y dispara el Webhook HTTP hacia GeeksIA.</small>
                                </div>
                            </div>

                            <div class="text-center text-muted mb-2"><i class="fa fa-arrow-down fa-lg"></i></div>

                            <!-- Paso 3 -->
                            <div class="p-2 rounded mb-2" style="background:#faf5ff; border-left:4px solid #9333ea; display:flex; align-items:center; gap:12px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#9333ea; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                                    <i class="fa fa-cogs"></i>
                                </div>
                                <div style="flex:1;">
                                    <strong style="color:#6b21a8; font-size:13.5px; display:block;">3. GeeksIA Restaurantes (Agente IA / Flujo Inteligente)</strong>
                                    <small style="color:#7e22ce;">Analiza la intención, consulta los productos disponibles con precio en tiempo real y arma el pedido.</small>
                                </div>
                            </div>

                            <div class="text-center text-muted mb-2"><i class="fa fa-arrow-down fa-lg"></i></div>

                            <!-- Paso 4 -->
                            <div class="p-2 rounded mb-2" style="background:#fffbeb; border-left:4px solid #d97706; display:flex; align-items:center; gap:12px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#d97706; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                                    <i class="fa fa-calculator"></i>
                                </div>
                                <div style="flex:1;">
                                    <strong style="color:#92400e; font-size:13.5px; display:block;">4. GeeksPOS Engine (POST /api/orders)</strong>
                                    <small style="color:#b45309;">Registra al cliente, descuenta el stock de inventario y genera el comprobante / ticket térmico oficial.</small>
                                </div>
                            </div>

                            <div class="text-center text-muted mb-2"><i class="fa fa-arrow-down fa-lg"></i></div>

                            <!-- Paso 5 -->
                            <div class="p-2 rounded" style="background:#f8fafc; border-left:4px solid #475569; display:flex; align-items:center; gap:12px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#475569; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                                    <i class="fa fa-print"></i>
                                </div>
                                <div style="flex:1;">
                                    <strong style="color:#1e293b; font-size:13.5px; display:block;">5. Alerta en POS + Impresora Térmica</strong>
                                    <small style="color:#475569;">El POS emite alerta sonora y enciende el contador de pedidos, mientras se imprime la comanda para cocina.</small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- 3. PANEL DE VINCULACIÓN WHATSAPP (CÓDIGO QR) Y CREDENCIALES -->
                <div class="col-lg-5 mb-3">

                    <!-- Widget Código QR -->
                    <div class="card shadow-none mb-3" style="border:1px solid #e2e8f0; border-radius:10px;">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 font-weight-bold" style="font-size:14.5px; color:#1e293b;">
                                <i class="fa fa-qrcode text-success"></i> Vincular WhatsApp Oficial
                            </h5>
                            <span id="page_wa_badge" class="badge badge-info">Consultando...</span>
                        </div>
                        <div class="card-body p-3 text-center">

                            <div id="page_wa_box" style="padding:15px; background:#f8fafc; border-radius:8px; border:1.5px dashed #cbd5e1; margin-bottom:12px;">
                                <div id="page_wa_spinner" style="padding:25px 0;">
                                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                                    <p class="text-muted mt-2 mb-0" style="font-size:12.5px;">Cargando estado de la pasarela...</p>
                                </div>
                                <div id="page_wa_qr_wrap" style="display:none;">
                                    <img id="page_wa_qr_img" src="" alt="WhatsApp QR Code" style="width:230px; height:230px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); background:#fff; padding:8px; margin:0 auto; display:block;">
                                    <p class="text-muted mt-2 mb-0" style="font-size:12px; line-height:1.4;">
                                        Abre WhatsApp en tu teléfono ➔ <strong>Dispositivos vinculados</strong> ➔ <strong>Vincular dispositivo</strong> y escanea este código.
                                    </p>
                                </div>
                                <div id="page_wa_connected_wrap" style="display:none; padding:25px 10px; color:#065f46;">
                                    <i class="fa fa-check-circle fa-3x text-success mb-2"></i>
                                    <h5 class="font-weight-bold mb-1">¡WhatsApp Conectado con Éxito!</h5>
                                    <p class="text-muted mb-0" style="font-size:12px;">La pasarela está recibiendo mensajes y lista para el bot de GeeksIA.</p>
                                </div>
                            </div>

                            <button type="button" id="btn_page_fetch_qr" class="btn btn-outline-success btn-block font-weight-bold">
                                <i class="fa fa-refresh"></i> Generar / Refrescar Código QR
                            </button>
                        </div>
                    </div>

                    <!-- Widget Accesos Rápidos GeeksIA -->
                    <div class="card shadow-none" style="border:1px solid #e2e8f0; border-radius:10px;">
                        <div class="card-header bg-light py-2">
                            <h5 class="mb-0 font-weight-bold" style="font-size:14.5px; color:#1e293b;">
                                <i class="fa fa-key text-amber"></i> Accesos y Webhooks
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold mb-1" style="font-size:12px;">Webhook URL para WhatsApp:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm font-weight-bold" value="http://localhost:5678/webhook/whatsapp-pos" readonly id="inp_webhook_url" style="background:#f8fafc;">
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText($('#inp_webhook_url').val()); alert('¡URL de Webhook copiada!');"><i class="fa fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1" style="font-size:12px;">GeeksPOS API Key:</label>
                                <div class="small text-muted" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:6px 8px;">
                                    La clave de plataforma para n8n está en el archivo <code>.env</code> del servidor
                                    (<code>GEEKSPOS_PLATFORM_API_KEY</code>). Cargala como credencial de n8n y enviala en el encabezado <code>X-API-KEY</code>.
                                </div>
                            </div>

                            <div class="row m-0">
                                <a href="http://localhost:5678" target="_blank" class="btn btn-primary btn-block font-weight-bold mb-2">
                                    <i class="fa fa-external-link"></i> Abrir Editor de Flujos GeeksIA (:5678)
                                </a>
                                <a href="<?= base_url('delivery_config') ?>" class="btn btn-outline-success btn-block font-weight-bold">
                                    <i class="fa fa-motorcycle"></i> Configuración Operativa & Deliveries (1-10)
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function loadPageQR() {
        $('#page_wa_spinner').show();
        $('#page_wa_qr_wrap').hide();
        $('#page_wa_connected_wrap').hide();
        $('#page_wa_badge').removeClass('badge-success badge-warning badge-danger').addClass('badge-info').text('Consultando...');

        $.ajax({
            url: '<?= base_url("pos_invoices/get_whatsapp_qr") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#page_wa_spinner').hide();
                if (res && res.status) {
                    if (res.state === 'open') {
                        $('#page_wa_connected_wrap').show();
                        $('#page_wa_badge').removeClass('badge-info badge-warning badge-danger').addClass('badge-success').html('<i class="fa fa-check"></i> Conectado');
                    } else if (res.qrcode) {
                        $('#page_wa_qr_img').attr('src', res.qrcode);
                        $('#page_wa_qr_wrap').show();
                        $('#page_wa_badge').removeClass('badge-info badge-success badge-danger').addClass('badge-warning').text('Esperando escaneo...');
                    } else {
                        $('#page_wa_badge').removeClass('badge-info badge-success badge-warning').addClass('badge-secondary').text('Iniciando instancia...');
                    }
                } else {
                    $('#page_wa_badge').addClass('badge-danger').text('Error');
                }
            },
            error: function() {
                $('#page_wa_spinner').hide();
                $('#page_wa_badge').removeClass('badge-info badge-success badge-warning').addClass('badge-danger').text('Desconectado');
            }
        });
    }

    $('#btn_page_fetch_qr').on('click', loadPageQR);
    loadPageQR();
});
</script>
