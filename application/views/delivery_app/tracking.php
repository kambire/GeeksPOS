<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Seguimiento en Vivo: Pedido <?= htmlspecialchars($order['order_number']) ?> - <?= htmlspecialchars($local['nombre']) ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #333;
        }
        .header-bar {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        #map {
            width: 100%;
            height: 380px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1;
        }
        .status-badge-lg {
            font-size: 15px;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 700;
        }
        .moto-marker {
            background: #0d6efd;
            border: 3px solid #fff;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
            box-shadow: 0 4px 10px rgba(13,110,253,0.5);
            transition: all 0.5s ease-in-out;
        }
        .home-marker {
            background: #dc3545;
            border: 3px solid #fff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(220,53,69,0.5);
        }
        .stepper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 20px 0;
        }
        .stepper::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 10%;
            right: 10%;
            height: 4px;
            background: #dee2e6;
            z-index: 0;
        }
        .stepper-progress {
            position: absolute;
            top: 15px;
            left: 10%;
            height: 4px;
            background: #198754;
            z-index: 0;
            transition: width 0.5s ease;
        }
        .step-item {
            position: relative;
            z-index: 1;
            text-align: center;
            width: 25%;
        }
        .step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #dee2e6;
            margin: 0 auto 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: #6c757d;
            font-weight: bold;
        }
        .step-item.active .step-dot {
            border-color: #198754;
            background: #198754;
            color: #fff;
        }
        .step-item.current .step-dot {
            border-color: #0d6efd;
            background: #0d6efd;
            color: #fff;
            animation: pulse 1.5s infinite;
        }
        .step-label {
            font-size: 11px;
            font-weight: 600;
            color: #6c757d;
        }
        .step-item.active .step-label,
        .step-item.current .step-label {
            color: #212529;
            font-weight: 700;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
            100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 font-weight-bold">
                    <i class="fa fa-cutlery mr-1"></i> <?= htmlspecialchars($local['nombre']) ?>
                </h5>
                <small class="text-white-50">Seguimiento en vivo del pedido <?= htmlspecialchars($order['order_number']) ?></small>
            </div>
            <div>
                <span class="badge badge-light px-2 py-1 font-weight-bold text-primary">
                    <i class="fa fa-wifi text-success mr-1"></i> En Vivo
                </span>
            </div>
        </div>
    </div>

    <div class="container py-3">
        <!-- Tarjeta de Estado del Pedido -->
        <div class="card shadow-sm border-0 mb-3 rounded-lg">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Estado Actual:</span>
                    <span id="badge_estado" class="badge status-badge-lg badge-primary">
                        <i class="fa fa-motorcycle mr-1"></i> <?= strtoupper($order['estado']) ?>
                    </span>
                </div>

                <!-- Stepper de 4 Pasos -->
                <div class="stepper">
                    <div class="stepper-progress" id="stepper_line" style="width: 25%;"></div>
                    <div class="step-item active" id="step_asignado">
                        <div class="step-dot"><i class="fa fa-check"></i></div>
                        <div class="step-label">Cocina</div>
                    </div>
                    <div class="step-item <?= in_array($order['estado'], ['en_camino', 'en_puerta', 'entregado']) ? 'active' : '' ?> <?= $order['estado'] === 'en_camino' ? 'current' : '' ?>" id="step_en_camino">
                        <div class="step-dot"><i class="fa fa-motorcycle"></i></div>
                        <div class="step-label">En Camino</div>
                    </div>
                    <div class="step-item <?= in_array($order['estado'], ['en_puerta', 'entregado']) ? 'active' : '' ?> <?= $order['estado'] === 'en_puerta' ? 'current' : '' ?>" id="step_en_puerta">
                        <div class="step-dot"><i class="fa fa-bell"></i></div>
                        <div class="step-label">En Puerta</div>
                    </div>
                    <div class="step-item <?= $order['estado'] === 'entregado' ? 'active current' : '' ?>" id="step_entregado">
                        <div class="step-dot"><i class="fa fa-home"></i></div>
                        <div class="step-label">Entregado</div>
                    </div>
                </div>

                <div class="p-2 bg-light rounded text-center" id="texto_estado">
                    <!-- Se actualiza por JS -->
                    <?php if ($order['estado'] === 'asignado'): ?>
                        <span class="text-dark font-weight-bold">👨‍🍳 Tu pedido está siendo preparado y empaquetado en cocina.</span>
                    <?php elseif ($order['estado'] === 'en_camino'): ?>
                        <span class="text-primary font-weight-bold">🛵 ¡El repartidor ya salió en camino hacia tu domicilio!</span>
                    <?php elseif ($order['estado'] === 'en_puerta'): ?>
                        <span class="text-danger font-weight-bold">📍 ¡El repartidor ya llegó y te espera en la puerta!</span>
                    <?php elseif ($order['estado'] === 'entregado'): ?>
                        <span class="text-success font-weight-bold">🎉 ¡Pedido entregado con éxito! ¡Buen provecho!</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Mapa Interactivo en Vivo -->
        <div class="card shadow-sm border-0 mb-3 rounded-lg overflow-hidden">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark">
                    <i class="fa fa-map-marker text-danger mr-1"></i> Mapa de Ruta en Vivo
                </span>
                <span class="small text-muted" id="last_update_txt">Actualizando cada 6s...</span>
            </div>
            <div class="card-body p-0 position-relative">
                <div id="map"></div>
            </div>
        </div>

        <!-- Tarjeta del Repartidor -->
        <div class="card shadow-sm border-0 mb-3 rounded-lg">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 22px;">
                            <i class="fa fa-user"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <small class="text-muted d-block">Tu Repartidor Asignado:</small>
                        <h6 class="font-weight-bold mb-0 text-dark"><?= htmlspecialchars($delivery['nombre']) ?></h6>
                        <small class="text-success font-weight-bold"><i class="fa fa-check-circle"></i> Repartidor Oficial Verificado</small>
                    </div>
                    <?php if (!empty($delivery['telefono'])): ?>
                        <div>
                            <a href="tel:<?= htmlspecialchars($delivery['telefono']) ?>" class="btn btn-outline-success btn-sm rounded-circle p-2" title="Llamar al repartidor">
                                <i class="fa fa-phone fa-lg"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resumen de Pedido y Pago -->
        <div class="card shadow-sm border-0 mb-3 rounded-lg">
            <div class="card-body p-3">
                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2">
                    <i class="fa fa-file-text-o text-primary mr-1"></i> Detalle de la Orden
                </h6>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Destino:</span>
                    <strong class="text-right text-dark" style="max-width: 65%;"><?= htmlspecialchars($order['direccion_entrega']) ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Forma de Pago:</span>
                    <span class="badge badge-<?= $order['metodo_pago'] === 'efectivo' ? 'warning' : 'success' ?> font-weight-bold text-uppercase">
                        <?= $order['metodo_pago'] ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-1 pt-1 border-top">
                    <span class="font-weight-bold text-dark">Total a Pagar:</span>
                    <h5 class="font-weight-bold text-primary mb-0">Gs. <?= number_format($order['monto_total'], 0, ',', '.') ?></h5>
                </div>
            </div>
        </div>

        <!-- Botón de Contacto con el Local -->
        <?php if (!empty($local['phone_caja'])): ?>
            <div class="text-center mb-4">
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $local['phone_caja']) ?>" target="_blank" class="btn btn-outline-secondary btn-block py-2 font-weight-bold">
                    <i class="fa fa-whatsapp mr-1 text-success"></i> Contactar al Local
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var initialLat = <?= $delivery['lat'] ?: '-25.2867' ?>;
        var initialLng = <?= $delivery['lng'] ?: '-57.6470' ?>;
        var destLat = <?= $order['lat_destino'] ?: 'null' ?>;
        var destLng = <?= $order['lng_destino'] ?: 'null' ?>;
        var trackingHash = '<?= $tracking_hash ?>';
        var estadoActual = '<?= $order['estado'] ?>';

        // Inicializar Mapa centrado en la moto o destino
        var mapCenter = [initialLat, initialLng];
        var map = L.map('map').setView(mapCenter, 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Icono de la moto
        var motoIcon = L.divIcon({
            className: 'moto-icon-wrapper',
            html: '<div class="moto-marker" id="moto_marker_inner"><i class="fa fa-motorcycle"></i></div>',
            iconSize: [44, 44],
            iconAnchor: [22, 22]
        });

        // Icono de la casa
        var homeIcon = L.divIcon({
            className: 'home-icon-wrapper',
            html: '<div class="home-marker"><i class="fa fa-home"></i></div>',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        var motoMarker = L.marker([initialLat, initialLng], { icon: motoIcon }).addTo(map)
            .bindPopup('<b><?= htmlspecialchars($delivery['nombre']) ?></b><br>Repartidor en camino');

        var destMarker = null;
        if (destLat && destLng) {
            destMarker = L.marker([destLat, destLng], { icon: homeIcon }).addTo(map)
                .bindPopup('<b>Tu Domicilio</b><br><?= htmlspecialchars($order['direccion_entrega']) ?>');

            // Ajustar vista para abarcar ambos marcadores
            var group = new L.featureGroup([motoMarker, destMarker]);
            map.fitBounds(group.getBounds().pad(0.2));
        }

        // Polling en vivo cada 6 segundos
        function pollTrackingLive() {
            fetch('<?= base_url("tracking/live/") ?>' + trackingHash)
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (!data || !data.status) return;

                    // Si hay nuevas coordenadas de la moto, mover suavemente
                    if (data.lat && data.lng) {
                        var newLatLng = new L.LatLng(data.lat, data.lng);
                        motoMarker.setLatLng(newLatLng);
                    }

                    // Actualizar estado si cambió
                    if (data.estado !== estadoActual) {
                        estadoActual = data.estado;
                        actualizarVistaEstado(data.estado);
                    }

                    document.getElementById('last_update_txt').innerText = 'Actualizado: ' + new Date().toLocaleTimeString();
                })
                .catch(function(err) {
                    console.log('Polling err:', err);
                });
        }

        function actualizarVistaEstado(estado) {
            var $badge = document.getElementById('badge_estado');
            var $txt = document.getElementById('texto_estado');
            var $stepper = document.getElementById('stepper_line');

            $badge.innerText = estado.toUpperCase();

            // Limpiar clases activas
            document.querySelectorAll('.step-item').forEach(function(el) {
                el.classList.remove('active', 'current');
            });

            if (estado === 'asignado') {
                $stepper.style.width = '25%';
                document.getElementById('step_asignado').classList.add('active', 'current');
                $txt.innerHTML = '<span class="text-dark font-weight-bold">👨‍🍳 Tu pedido está siendo preparado y empaquetado en cocina.</span>';
            } else if (estado === 'en_camino') {
                $stepper.style.width = '50%';
                document.getElementById('step_asignado').classList.add('active');
                document.getElementById('step_en_camino').classList.add('active', 'current');
                $txt.innerHTML = '<span class="text-primary font-weight-bold">🛵 ¡El repartidor ya salió en camino hacia tu domicilio!</span>';
            } else if (estado === 'en_puerta') {
                $stepper.style.width = '75%';
                document.getElementById('step_asignado').classList.add('active');
                document.getElementById('step_en_camino').classList.add('active');
                document.getElementById('step_en_puerta').classList.add('active', 'current');
                $txt.innerHTML = '<span class="text-danger font-weight-bold">📍 ¡El repartidor ya llegó y te espera en la puerta!</span>';
            } else if (estado === 'entregado') {
                $stepper.style.width = '100%';
                document.getElementById('step_asignado').classList.add('active');
                document.getElementById('step_en_camino').classList.add('active');
                document.getElementById('step_en_puerta').classList.add('active');
                document.getElementById('step_entregado').classList.add('active', 'current');
                $txt.innerHTML = '<span class="text-success font-weight-bold">🎉 ¡Pedido entregado con éxito! ¡Buen provecho!</span>';
            }
        }

        setInterval(pollTrackingLive, 6000);
    </script>
</body>
</html>
