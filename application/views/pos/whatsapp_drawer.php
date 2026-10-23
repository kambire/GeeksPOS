<!-- WhatsApp & Pedidos Online Slide-Over Drawer -->
<div id="pos_wa_backdrop" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.45); z-index:1050; backdrop-filter:blur(2px); transition:opacity 0.3s;"></div>

<div id="pos_whatsapp_drawer" style="position:fixed; top:0; right:0; width:460px; max-width:95vw; height:100vh; background:#ffffff; z-index:1055; box-shadow:-8px 0 35px rgba(0,0,0,0.2); transform:translateX(100%); transition:transform 0.3s cubic-bezier(0.16, 1, 0.3, 1); display:flex; flex-direction:column; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

    <!-- Drawer Header -->
    <div style="background:linear-gradient(135deg, #075e54 0%, #128c7e 100%); color:#fff; padding:16px 20px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="background:#25d366; width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(0,0,0,0.2);">
                <i class="fa fa-whatsapp" style="font-size:24px; color:#fff;"></i>
            </div>
            <div>
                <h5 style="margin:0; font-size:16px; font-weight:700; color:#fff; line-height:1.2;">WhatsApp & Pedidos Online</h5>
                <small style="color:#d1fae5; font-size:11.5px; display:flex; align-items:center; gap:4px;">
                    <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:#25d366;"></span>
                    Canal Bot GeeksIA Restaurantes Sincronizado
                </small>
            </div>
        </div>
        <button type="button" onclick="toggleWhatsAppDrawer(false)" style="background:transparent; border:none; color:#fff; font-size:24px; cursor:pointer; opacity:0.85; padding:0 5px;" title="Cerrar panel">&times;</button>
    </div>

    <!-- Drawer Tabs -->
    <div style="display:flex; border-bottom:1px solid #e2e8f0; background:#f8fafc; padding:4px 8px 0;">
        <button type="button" class="wa-nav-tab active" data-tab="tab_pedidos" style="flex:1; padding:10px 8px; border:none; background:transparent; font-size:13px; font-weight:600; color:#0f766e; border-bottom:3px solid #0f766e; cursor:pointer;">
            <i class="fa fa-bell-o"></i> Pedidos <span id="wa_drawer_badge" class="badge badge-danger badge-pill" style="display:none; font-size:10px; margin-left:3px;">0</span>
        </button>
        <button type="button" class="wa-nav-tab" data-tab="tab_enviar" style="flex:1; padding:10px 8px; border:none; background:transparent; font-size:13px; font-weight:600; color:#64748b; border-bottom:3px solid transparent; cursor:pointer;">
            <i class="fa fa-paper-plane-o"></i> Enviar Ticket
        </button>
        <button type="button" class="wa-nav-tab" data-tab="tab_estado" style="flex:1; padding:10px 8px; border:none; background:transparent; font-size:13px; font-weight:600; color:#64748b; border-bottom:3px solid transparent; cursor:pointer;">
            <i class="fa fa-sitemap"></i> Flujo & Conectar
        </button>
    </div>

    <!-- Drawer Body -->
    <div style="flex:1; overflow-y:auto; padding:16px; background:#f1f5f9;">

        <!-- TAB 1: PEDIDOS ENTRANTES DE WHATSAPP -->
        <div id="tab_pedidos" class="wa-tab-content" style="display:block;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <span style="font-size:12.5px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">
                    Pedidos Recientes (Bot WhatsApp)
                </span>
                <button type="button" id="btn_refresh_wa_orders" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:3px 8px;">
                    <i class="fa fa-refresh"></i> Actualizar
                </button>
            </div>

            <!-- Contenedor dinámico de pedidos -->
            <div id="wa_orders_container">
                <div style="text-align:center; padding:35px 20px; color:#94a3b8;">
                    <i class="fa fa-spinner fa-spin" style="font-size:28px; margin-bottom:8px;"></i>
                    <p style="font-size:13px; margin:0;">Cargando pedidos de WhatsApp...</p>
                </div>
            </div>
        </div>

        <!-- TAB 2: ENVIAR COMPROBANTE / MENSAJE RÁPIDO -->
        <div id="tab_enviar" class="wa-tab-content" style="display:none;">
            <div style="background:#ffffff; border-radius:10px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:15px; border:1px solid #e2e8f0;">
                <h6 style="margin:0 0 12px 0; font-weight:700; color:#0f172a; font-size:14px;">
                    <i class="fa fa-ticket text-success"></i> Enviar Comprobante Digital
                </h6>

                <div class="form-group mb-2">
                    <label style="font-size:12px; font-weight:600; color:#475569; margin-bottom:4px; display:block;">Número de WhatsApp del Cliente:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="font-size:12px; background:#f8fafc;"><i class="fa fa-phone"></i></span>
                        </div>
                        <input type="text" id="wa_quick_phone" class="form-control" placeholder="Ej: 0981000000 o +595981000000" style="font-size:13px;">
                    </div>
                    <small class="text-muted" style="font-size:11px;">Se toma automáticamente del cliente seleccionado en caja.</small>
                </div>

                <div class="form-group mb-2">
                    <label style="font-size:12px; font-weight:600; color:#475569; margin-bottom:4px; display:block;">Plantilla de Mensaje:</label>
                    <select id="wa_template_select" class="form-control" style="font-size:12.5px;">
                        <option value="ticket">📄 Enviar último Ticket / Factura de compra</option>
                        <option value="camino">🛵 "Tu pedido ya está en camino con el delivery"</option>
                        <option value="listo">🍽️ "Tu pedido ya está listo para retirar en el local"</option>
                        <option value="custom">✍️ Mensaje personalizado</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label style="font-size:12px; font-weight:600; color:#475569; margin-bottom:4px; display:block;">Mensaje a enviar:</label>
                    <textarea id="wa_quick_msg" class="form-control" rows="4" style="font-size:12.5px; line-height:1.4;" placeholder="Escriba su mensaje..."></textarea>
                </div>

                <button type="button" id="btn_send_wa_quick" class="btn btn-success btn-block" style="background:#25d366; border-color:#25d366; font-weight:700; padding:10px; font-size:13.5px; border-radius:6px; box-shadow:0 2px 6px rgba(37,211,102,0.3);">
                    <i class="fa fa-whatsapp"></i> Enviar por WhatsApp
                </button>
            </div>
        </div>

        <!-- TAB 3: FLUJO COMPLETO GEEKSIA & CONECTAR WHATSAPP -->
        <div id="tab_estado" class="wa-tab-content" style="display:none;">

            <!-- SECCIÓN A: CÓDIGO QR PARA VINCULAR WHATSAPP -->
            <div style="background:#ffffff; border-radius:10px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:14px; border:1px solid #e2e8f0;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <h6 style="margin:0; font-weight:700; color:#0f172a; font-size:13.5px;">
                        <i class="fa fa-qrcode text-success"></i> Vincular WhatsApp Oficial
                    </h6>
                    <span id="wa_qr_state_badge" class="badge badge-info" style="font-size:11px;">Consultando...</span>
                </div>

                <div id="wa_qr_box" style="text-align:center; padding:12px 10px; background:#f8fafc; border-radius:8px; border:1.5px dashed #cbd5e1; margin-bottom:12px;">
                    <div id="wa_qr_spinner" style="display:none; padding:20px 0;">
                        <i class="fa fa-spinner fa-spin" style="font-size:28px; color:#0f766e;"></i>
                        <p style="font-size:12px; color:#64748b; margin-top:8px;">Generando código QR con Evolution API...</p>
                    </div>
                    <div id="wa_qr_image_wrap" style="display:none;">
                        <img id="wa_qr_img" src="" alt="WhatsApp QR Code" style="width:210px; height:210px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); background:#fff; padding:6px; margin:0 auto; display:block;">
                        <p style="font-size:11.5px; color:#475569; margin:10px 0 0 0; line-height:1.35;">
                            Escanea este código desde WhatsApp en tu celular: <br>
                            <strong>Ajustes ➔ Dispositivos vinculados ➔ Vincular dispositivo</strong>
                        </p>
                    </div>
                    <div id="wa_qr_connected_wrap" style="display:none; padding:18px 10px; color:#065f46;">
                        <i class="fa fa-check-circle" style="font-size:36px; color:#10b981; margin-bottom:8px;"></i>
                        <h6 style="font-weight:700; margin:0; font-size:14px; color:#065f46;">¡WhatsApp Conectado!</h6>
                        <small style="color:#047857; font-size:11.5px;">La pasarela Evolution API está activa y lista para recibir mensajes.</small>
                    </div>
                </div>

                <button type="button" id="btn_fetch_wa_qr" class="btn btn-outline-success btn-block" style="font-size:12.5px; font-weight:600; padding:8px;">
                    <i class="fa fa-refresh"></i> Obtener / Refrescar Código QR
                </button>
            </div>

            <!-- SECCIÓN B: DIAGRAMA VISUAL DEL FLUJO GEEKSIA -->
            <div style="background:#ffffff; border-radius:10px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:14px; border:1px solid #e2e8f0;">
                <h6 style="margin:0 0 12px 0; font-weight:700; color:#0f172a; font-size:13.5px;">
                    <i class="fa fa-sitemap text-primary"></i> Flujo Completo del Sistema
                </h6>

                <!-- Diagrama de pasos en tarjetas verticales -->
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <!-- Paso 1 -->
                    <div style="display:flex; align-items:center; gap:10px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:8px 12px;">
                        <span style="background:#25d366; color:#fff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold; flex-shrink:0;">1</span>
                        <div style="flex:1;">
                            <strong style="font-size:12px; color:#14532d; display:block;">Cliente WhatsApp</strong>
                            <span style="font-size:11px; color:#166534;">Envía mensaje pidiendo el menú o confirmando pedido.</span>
                        </div>
                    </div>
                    <div style="text-align:center; color:#94a3b8; font-size:13px; line-height:1;"><i class="fa fa-arrow-down"></i></div>

                    <!-- Paso 2 -->
                    <div style="display:flex; align-items:center; gap:10px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:8px 12px;">
                        <span style="background:#0284c7; color:#fff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold; flex-shrink:0;">2</span>
                        <div style="flex:1;">
                            <strong style="font-size:12px; color:#0369a1; display:block;">Evolution API (:8081)</strong>
                            <span style="font-size:11px; color:#075985;">Recibe el webhook en tiempo real y lo redirige a GeeksIA.</span>
                        </div>
                    </div>
                    <div style="text-align:center; color:#94a3b8; font-size:13px; line-height:1;"><i class="fa fa-arrow-down"></i></div>

                    <!-- Paso 3 -->
                    <div style="display:flex; align-items:center; gap:10px; background:#faf5ff; border:1px solid #e9d5ff; border-radius:8px; padding:8px 12px;">
                        <span style="background:#9333ea; color:#fff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold; flex-shrink:0;">3</span>
                        <div style="flex:1;">
                            <strong style="font-size:12px; color:#6b21a8; display:block;">GeeksIA Restaurantes (:5678)</strong>
                            <span style="font-size:11px; color:#7e22ce;">Procesa la intención con IA, responde catálogo y arma el pedido.</span>
                        </div>
                    </div>
                    <div style="text-align:center; color:#94a3b8; font-size:13px; line-height:1;"><i class="fa fa-arrow-down"></i></div>

                    <!-- Paso 4 -->
                    <div style="display:flex; align-items:center; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:8px 12px;">
                        <span style="background:#d97706; color:#fff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold; flex-shrink:0;">4</span>
                        <div style="flex:1;">
                            <strong style="font-size:12px; color:#92400e; display:block;">GeeksPOS API (:8080)</strong>
                            <span style="font-size:11px; color:#b45309;">Registra venta, descuenta inventario y emite factura SIFEN.</span>
                        </div>
                    </div>
                    <div style="text-align:center; color:#94a3b8; font-size:13px; line-height:1;"><i class="fa fa-arrow-down"></i></div>

                    <!-- Paso 5 -->
                    <div style="display:flex; align-items:center; gap:10px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:8px 12px;">
                        <span style="background:#475569; color:#fff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold; flex-shrink:0;">5</span>
                        <div style="flex:1;">
                            <strong style="font-size:12px; color:#1e293b; display:block;">Alerta en POS + Impresora Térmica</strong>
                            <span style="font-size:11px; color:#475569;">Alerta sonora al cajero y comanda para la cocina.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN C: ACCESOS Y CONFIGURACIÓN -->
            <?php $is_superadmin = ($this->aauth->get_user()->loc == 0 && $this->aauth->get_user()->roleid == 5); ?>
            <div style="background:#ffffff; border-radius:10px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:12px; border:1px solid #e2e8f0;">
                <h6 style="margin:0 0 10px 0; font-weight:700; color:#0f172a; font-size:13.5px;">
                    <i class="fa fa-sliders text-primary"></i> Operativa & Entregas
                </h6>

                <?php if ($is_superadmin): ?>
                <div style="margin-bottom:8px;">
                    <small style="font-size:11px; color:#64748b; font-weight:600; display:block;">Webhook URL (Para Evolution API):</small>
                    <code style="display:block; background:#f1f5f9; padding:5px 8px; border-radius:4px; font-size:11px; color:#0f172a; word-break:break-all;">http://n8n:5678/webhook/whatsapp-pos</code>
                </div>


                <div style="margin-bottom:10px;">
                    <a href="http://localhost:5678" target="_blank" class="btn btn-outline-primary btn-sm btn-block" style="font-weight:700; padding:8px; font-size:12px; border-radius:6px;">
                        <i class="fa fa-external-link"></i> Abrir Servidor de Flujos GeeksIA (:5678)
                    </a>
                </div>
                <?php endif; ?>

                <div>
                    <a href="<?= base_url('delivery_config') ?>" target="_blank" class="btn btn-success btn-sm btn-block" style="font-weight:700; padding:10px 8px; font-size:12.5px; border-radius:6px; color:#ffffff; background:#0f766e; border-color:#0f766e; box-shadow:0 2px 5px rgba(15,118,110,0.25);">
                        <i class="fa fa-motorcycle"></i> ⚙️ Configuración Operativa & Deliveries (1-10)
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

    </div>
</div>

<script>
(function() {
    // Sonido suave de notificación al entrar nuevo pedido de WhatsApp
    var waAudioNotification = new Audio("data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU"+Array(300).join("10101010"));

    var lastWaOrderId = 0;
    var lastTicketUrl = '';

    // Función global para abrir/cerrar el drawer
    window.toggleWhatsAppDrawer = function(show) {
        var $drawer = $('#pos_whatsapp_drawer');
        var $backdrop = $('#pos_wa_backdrop');
        var isCurrentlyOpen = $drawer.css('transform') === 'matrix(1, 0, 0, 1, 0, 0)' || $drawer.hasClass('open');

        var shouldOpen = (show !== undefined) ? show : !isCurrentlyOpen;

        if (shouldOpen) {
            $backdrop.fadeIn(200);
            $drawer.addClass('open').css('transform', 'translateX(0)');
            loadWhatsAppOrders();
            prefillClientPhone();
        } else {
            $backdrop.fadeOut(200);
            $drawer.removeClass('open').css('transform', 'translateX(100%)');
        }
    };

    $('#pos_wa_backdrop').on('click', function() {
        toggleWhatsAppDrawer(false);
    });

    // Pestañas del Drawer
    $('.wa-nav-tab').on('click', function() {
        var targetTab = $(this).data('tab');
        $('.wa-nav-tab').removeClass('active').css({
            'color': '#64748b',
            'border-bottom-color': 'transparent'
        });
        $(this).addClass('active').css({
            'color': '#0f766e',
            'border-bottom-color': '#0f766e'
        });

        $('.wa-tab-content').hide();
        $('#' + targetTab).show();
    });

    // Cargar teléfono del cliente en caja
    function prefillClientPhone() {
        var phone = '';
        if ($('#mcustomer_phone').length && $('#mcustomer_phone').val()) {
            phone = $('#mcustomer_phone').val();
        }
        if (phone) {
            $('#wa_quick_phone').val(phone);
        }
        updateTemplateText();
    }

    // Actualizar plantilla de texto
    function updateTemplateText() {
        var sel = $('#wa_template_select').val();
        var clientName = $('#order_name').val() || $('#pay_customer_name').val() || 'Estimado cliente';
        var baseMsg = '';

        if (sel === 'ticket') {
            baseMsg = '¡Hola ' + clientName + '! 👋 Gracias por su compra en ' + (window.company_name || 'nuestro local') + '.\n\nPuede ver o descargar su comprobante digital en el siguiente enlace:\n' + (lastTicketUrl || (window.location.origin + '/pos_invoices')) + '\n\n¡Que lo disfrute!';
        } else if (sel === 'camino') {
            baseMsg = '¡Hola ' + clientName + '! 🛵 Le informamos que su pedido ya salió en camino con el repartidor hacia su dirección. ¡Muchas gracias por su preferencia!';
        } else if (sel === 'listo') {
            baseMsg = '¡Hola ' + clientName + '! 🍽️ Su pedido ya está listo para ser retirado en caja. ¡Le esperamos!';
        }
        if (sel !== 'custom') {
            $('#wa_quick_msg').val(baseMsg);
        }
    }

    $('#wa_template_select').on('change', updateTemplateText);

    // Enviar mensaje rápido por WhatsApp
    $('#btn_send_wa_quick').on('click', function() {
        var phone = $('#wa_quick_phone').val().replace(/[^0-9]/g, '');
        var msg = encodeURIComponent($('#wa_quick_msg').val());

        if (!phone) {
            alert('Por favor ingrese el número de WhatsApp del cliente.');
            $('#wa_quick_phone').focus();
            return;
        }

        // Si empieza con 09 (Paraguay), convertir a formato internacional 5959...
        if (phone.indexOf('09') === 0) {
            phone = '595' + phone.substring(1);
        } else if (phone.indexOf('9') === 0 && phone.length === 9) {
            phone = '595' + phone;
        }

        var waUrl = 'https://wa.me/' + phone + '?text=' + msg;
        window.open(waUrl, '_blank', 'width=900,height=700');
    });

    // Consultar pedidos pendientes de WhatsApp (Bot GeeksIA Restaurantes)
    function loadWhatsAppOrders() {
        // Endpoint con sesión: solo devuelve los pedidos del local del cajero (sin API key en el navegador)
        $.ajax({
            url: window.baseurl + 'pos_invoices/wa_orders',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res && res.status && res.orders) {
                    renderWhatsAppOrders(res.orders);
                } else {
                    $('#wa_orders_container').html('<div style="text-align:center; padding:30px 15px; color:#64748b; background:#fff; border-radius:8px; border:1px dashed #cbd5e1;"><i class="fa fa-inbox" style="font-size:32px; color:#94a3b8; margin-bottom:8px;"></i><p style="margin:0; font-size:13px; font-weight:600;">No hay pedidos pendientes de WhatsApp</p><small class="text-muted">Los nuevos pedidos del bot aparecerán aquí automáticamente.</small></div>');
                    updateBadges(0);
                }
            },
            error: function() {
                $('#wa_orders_container').html('<div style="text-align:center; padding:20px; color:#ef4444;"><i class="fa fa-exclamation-circle"></i> Error al conectar con el servicio de pedidos.</div>');
            }
        });
    }

    // Renderizar listado de tarjetas de pedidos
    function renderWhatsAppOrders(orders) {
        if (!orders || !orders.length) {
            $('#wa_orders_container').html('<div style="text-align:center; padding:30px 15px; color:#64748b; background:#fff; border-radius:8px; border:1px dashed #cbd5e1;"><i class="fa fa-check-circle text-success" style="font-size:32px; margin-bottom:8px;"></i><p style="margin:0; font-size:13px; font-weight:600;">Todos los pedidos están atendidos</p></div>');
            updateBadges(0);
            return;
        }

        var count = orders.length;
        updateBadges(count);

        // Si hay un ID nuevo mayor al anterior, emitir alerta sonora
        var maxId = Math.max.apply(Math, orders.map(function(o) { return parseInt(o.id || 0); }));
        if (lastWaOrderId > 0 && maxId > lastWaOrderId) {
            try { waAudioNotification.play(); } catch(e){}
        }
        lastWaOrderId = maxId;

        // Los datos vienen de clientes de WhatsApp: se escapan antes de insertarlos como HTML
        function esc(v) {
            return String(v == null ? '' : v).replace(/[&<>"'`]/g, function(c) {
                return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '`': '&#96;'}[c];
            });
        }

        var html = '';
        orders.forEach(function(ord) {
            var total = Number(ord.total || 0).toLocaleString('es-PY');
            var custName = esc(ord.customer_name || 'Cliente WhatsApp');
            var custPhone = esc(ord.customer_phone || '');
            ord = $.extend({}, ord, { id: parseInt(ord.id, 10) || 0, tid: esc(ord.tid || ''), notes: esc(ord.notes || ''), order_type: esc(ord.order_type || '') });
            var typeBadge = (ord.order_type === 'delivery') ? '<span class="badge badge-warning" style="background:#f59e0b; color:#fff;">🛵 Delivery</span>' : '<span class="badge badge-info">🍽️ ' + (ord.order_type || 'Local') + '</span>';

            html += '<div style="background:#ffffff; border-radius:10px; padding:14px; margin-bottom:12px; border:1px solid #e2e8f0; box-shadow:0 2px 5px rgba(0,0,0,0.03);">' +
                '<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">' +
                    '<div>' +
                        '<strong style="font-size:14px; color:#0f172a; display:block;">' + custName + '</strong>' +
                        '<small style="color:#64748b; font-size:11.5px;"><i class="fa fa-phone"></i> ' + (custPhone || 'Sin teléfono') + '</small>' +
                    '</div>' +
                    '<div>' + typeBadge + '</div>' +
                '</div>' +
                '<div style="background:#f8fafc; padding:8px 10px; border-radius:6px; font-size:12px; color:#334155; margin-bottom:10px; border-left:3px solid #0f766e;">' +
                    '<div><strong>Pedido #' + (ord.tid || ord.id) + '</strong> | <strong>Total: Gs. ' + total + '</strong></div>' +
                    '<div style="color:#64748b; font-size:11px; margin-top:2px;">' + (ord.notes || 'Pedido ingresado vía WhatsApp Bot') + '</div>' +
                '</div>' +
                '<div style="display:flex; gap:6px;">' +
                    '<button type="button" class="btn btn-sm btn-success btn-wa-accept" data-id="' + ord.id + '" style="flex:1; font-size:11.5px; font-weight:600; padding:6px 4px; background:#10b981; border-color:#10b981;">' +
                        '<i class="fa fa-check"></i> Aceptar' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-info btn-wa-print" data-id="' + ord.id + '" style="font-size:11.5px; font-weight:600; padding:6px 8px;">' +
                        '<i class="fa fa-print"></i> Ticket' +
                    '</button>' +
                    (custPhone ? '<button type="button" class="btn btn-sm btn-outline-success btn-wa-chat" data-phone="' + custPhone + '" data-name="' + custName + '" style="font-size:11.5px; padding:6px 8px;"><i class="fa fa-whatsapp"></i></button>' : '') +
                '</div>' +
            '</div>';
        });

        $('#wa_orders_container').html(html);
    }

    // Actualizar badges numéricos
    function updateBadges(count) {
        if (count > 0) {
            $('#wa_header_badge').text(count).show();
            $('#wa_drawer_badge').text(count).show();
        } else {
            $('#wa_header_badge').hide();
            $('#wa_drawer_badge').hide();
        }
    }

    // Botón Aceptar Pedido (cambia estado a 'paid' o notifica cocina)
    $(document).on('click', '.btn-wa-accept', function() {
        var orderId = $(this).data('id');
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: window.baseurl + 'pos_invoices/wa_order_accept',
            type: 'POST',
            dataType: 'json',
            data: { id: orderId },
            success: function() {
                loadWhatsAppOrders();
            },
            error: function() {
                alert('No se pudo actualizar el estado.');
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Aceptar');
            }
        });
    });

    // Botón Imprimir Ticket de pedido
    $(document).on('click', '.btn-wa-print', function() {
        var orderId = $(this).data('id');
        var printUrl = window.baseurl + 'pos_invoices/ticket?id=' + orderId + '&print=1&width=80';
        if ($('#gp_auto_print_frame').length === 0) {
            $('body').append('<iframe id="gp_auto_print_frame" style="position:fixed;width:0;height:0;border:0;visibility:hidden;"></iframe>');
        }
        $('#gp_auto_print_frame').attr('src', printUrl);
    });

    // Botón Chatear con cliente de la tarjeta
    $(document).on('click', '.btn-wa-chat', function() {
        var phone = String($(this).data('phone')).replace(/[^0-9]/g, '');
        var name = $(this).data('name') || '';
        if (phone.indexOf('09') === 0) phone = '595' + phone.substring(1);
        var msg = encodeURIComponent('Hola ' + name + ', te escribimos desde ' + (window.company_name || 'el local') + ' sobre tu pedido.');
        window.open('https://wa.me/' + phone + '?text=' + msg, '_blank', 'width=900,height=700');
    });

    // Refrescar manualmente
    $('#btn_refresh_wa_orders').on('click', loadWhatsAppOrders);

    // Monitoreo en segundo plano cada 20 segundos
    setInterval(loadWhatsAppOrders, 20000);
    // Primera carga al iniciar
    setTimeout(loadWhatsAppOrders, 3000);

    // Consultar estado y código QR de WhatsApp desde Evolution API
    function fetchWhatsAppQR() {
        $('#wa_qr_spinner').show();
        $('#wa_qr_image_wrap').hide();
        $('#wa_qr_connected_wrap').hide();
        $('#wa_qr_state_badge').removeClass('badge-success badge-warning badge-danger').addClass('badge-info').text('Consultando...');

        $.ajax({
            url: window.baseurl + 'pos_invoices/get_whatsapp_qr',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#wa_qr_spinner').hide();
                if (res && res.status) {
                    if (res.state === 'open') {
                        $('#wa_qr_connected_wrap').show();
                        $('#wa_qr_state_badge').removeClass('badge-info badge-warning badge-danger').addClass('badge-success').html('<i class="fa fa-check"></i> Conectado');
                    } else if (res.qrcode) {
                        $('#wa_qr_img').attr('src', res.qrcode);
                        $('#wa_qr_image_wrap').show();
                        $('#wa_qr_state_badge').removeClass('badge-info badge-success badge-danger').addClass('badge-warning').text('Esperando escaneo...');
                    } else {
                        $('#wa_qr_state_badge').removeClass('badge-info badge-success badge-warning').addClass('badge-secondary').text('Iniciando instancia...');
                    }
                } else {
                    $('#wa_qr_state_badge').addClass('badge-danger').text('Error');
                }
            },
            error: function() {
                $('#wa_qr_spinner').hide();
                $('#wa_qr_state_badge').removeClass('badge-info badge-success badge-warning').addClass('badge-danger').text('Desconectado');
            }
        });
    }

    $('#btn_fetch_wa_qr').on('click', fetchWhatsAppQR);

    // Cuando se hace clic en la pestaña "Flujo & Conectar", consultar el QR y estado
    $(document).on('click', '.wa-nav-tab[data-tab="tab_estado"]', function() {
        fetchWhatsAppQR();
    });

    // Escuchar cuando el POS genera una venta para capturar el ticket y ofrecérselo al WhatsApp
    window.addEventListener('pos_sale_completed', function(e) {
        if (e.detail && e.detail.ticket_url) {
            lastTicketUrl = e.detail.ticket_url;
            updateTemplateText();
        }
    });

})();
</script>
