<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($store_name) ?> • Pedidos Online con IA</title>
    <link rel="icon" type="image/png" href="<?= assets_url() ?>app-assets/images/ico/favicon-32.png">

    <!-- PWA Manifest & Meta Tags -->
    <link rel="manifest" href="<?= base_url('p/manifest/' . $loc_id) ?>">
    <meta name="theme-color" content="#008069">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($store_name) ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('app-assets/images/ico/apple-icon-180.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('app-assets/images/ico/icon-192.png') ?>">

    <!-- Fuentes y FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet Map CSS & JS (100% Gratuito y Móvil-Optimizado) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --wa-teal: #008069;
            --wa-teal-dark: #075e54;
            --wa-teal-light: #00a884;
            --wa-bg: #efeae2;
            --wa-bubble-user: #d9fdd3;
            --wa-bubble-bot: #ffffff;
            --wa-text: #111b21;
            --wa-muted: #667781;
            --wa-check: #53bdeb;
            --wa-gray-bar: #f0f2f5;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif;
            background-color: #d1d7db;
            color: var(--wa-text);
            height: 100vh;
            height: 100dvh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Contenedor Principal (App Frame) */
        .app-container {
            width: 100%;
            max-width: 680px;
            height: 100%;
            background-color: var(--wa-bg);
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .app-container {
                height: 94vh;
                height: 94dvh;
                border-radius: 16px;
            }
        }

        /* 1. Header estilo WhatsApp */
        .chat-header {
            background-color: var(--wa-teal);
            color: #ffffff;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 20;
            box-shadow: 0 1px 3px rgba(0,0,0,0.18);
        }

        .header-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
            cursor: pointer;
        }

        .avatar-wrapper {
            position: relative;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--wa-teal-dark);
            font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        .online-dot {
            position: absolute;
            bottom: 0px;
            right: 0px;
            width: 12px;
            height: 12px;
            background-color: #25d366;
            border: 2px solid #ffffff;
            border-radius: 50%;
        }

        .header-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .store-title {
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .store-status {
            font-size: 11.5px;
            color: rgba(255,255,255,0.85);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-menu-drawer {
            background-color: rgba(255,255,255,0.2);
            color: #ffffff;
            border: none;
            border-radius: 20px;
            padding: 6px 11px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-menu-drawer:hover {
            background-color: rgba(255,255,255,0.3);
        }

        .btn-whatsapp-direct {
            background-color: #25d366;
            color: #ffffff;
            border: none;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.2s ease;
        }

        .btn-whatsapp-direct:hover {
            background-color: #1ebd5b;
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* Banner Superior PWA Instalable */
        .pwa-install-banner {
            background: linear-gradient(90deg, #075e54 0%, #008069 100%);
            color: #ffffff;
            padding: 8px 14px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 25;
            box-shadow: 0 2px 6px rgba(0,0,0,0.18);
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .pwa-banner-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .pwa-badge-icon {
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
            color: #25d366;
        }

        .pwa-banner-text {
            min-width: 0;
        }

        .pwa-banner-title {
            font-weight: 700;
            font-size: 12.5px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pwa-banner-sub {
            font-size: 10.5px;
            opacity: 0.85;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pwa-banner-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            margin-left: 10px;
        }

        .btn-pwa-cta {
            background: #25d366;
            color: #ffffff;
            border: none;
            padding: 5px 12px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 11.5px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.2s;
        }

        .btn-pwa-cta:hover {
            background: #1ebd5b;
            transform: translateY(-1px);
        }

        .btn-pwa-close {
            background: transparent;
            color: rgba(255,255,255,0.7);
            border: none;
            cursor: pointer;
            font-size: 14px;
            padding: 4px 6px;
            line-height: 1;
        }

        .btn-pwa-close:hover {
            color: #ffffff;
        }

        /* Botón de PWA en el Header */
        .btn-pwa-header {
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.3);
            color: #ffffff;
            padding: 5px 9px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .btn-pwa-header:hover {
            background: rgba(255,255,255,0.28);
        }

        /* Botón Flotante para Instalar PWA */
        .floating-pwa-pill {
            position: absolute;
            bottom: 108px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #075e54 0%, #008069 50%, #25d366 100%);
            color: #ffffff;
            padding: 8px 14px;
            border-radius: 24px;
            font-size: 12.5px;
            font-weight: 700;
            box-shadow: 0 4px 18px rgba(0, 128, 105, 0.45);
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            z-index: 25;
            white-space: nowrap;
            transition: all 0.25s ease;
            animation: bouncePwa 3.5s infinite;
        }

        @keyframes bouncePwa {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-6px); }
            60% { transform: translateX(-50%) translateY(-3px); }
        }

        .floating-pwa-pill:hover {
            box-shadow: 0 6px 22px rgba(0, 128, 105, 0.65);
            transform: translateX(-50%) scale(1.03);
        }

        .floating-pwa-close {
            background: rgba(0, 0, 0, 0.25);
            color: #ffffff;
            border-radius: 50%;
            width: 17px;
            height: 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            margin-left: 6px;
            cursor: pointer;
            opacity: 0.8;
        }

        .floating-pwa-close:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.45);
        }

        .pwa-pulse-dot {
            width: 8px;
            height: 8px;
            background: #25d366;
            border: 1.5px solid #ffffff;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 6px #25d366;
            animation: pulsePwaDot 1.4s infinite;
        }

        @keyframes pulsePwaDot {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        .chip-btn.highlight-pwa {
            background-color: #e8f5e9;
            color: #1b5e20;
            border-color: #a5d6a7;
            font-weight: 600;
        }

        /* 2. Banner de Información / Alerta */
        .notice-banner {
            background-color: #fff3cd;
            color: #856404;
            padding: 6px 12px;
            font-size: 11.5px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-bottom: 1px solid #ffeeba;
            z-index: 10;
        }

        /* Barra de Promociones de Hoy (Carousel Horizontal) */
        .promos-carousel-strip {
            background: linear-gradient(180deg, #fff1f2 0%, #ffe4e6 100%);
            border-bottom: 1px solid #fecdd3;
            padding: 8px 12px;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            display: flex;
            gap: 10px;
            align-items: center;
            z-index: 10;
        }
        .promos-carousel-strip::-webkit-scrollbar {
            display: none;
        }
        .promo-chip-card {
            display: inline-flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #fda4af;
            border-radius: 20px;
            padding: 4px 10px 4px 6px;
            box-shadow: 0 2px 6px rgba(225, 29, 72, 0.08);
            flex-shrink: 0;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .promo-chip-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(225, 29, 72, 0.15);
        }
        .promo-chip-img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
            border: 1px solid #f43f5e;
            background: #fff;
            flex-shrink: 0;
        }
        .promo-chip-badge {
            background: #e11d48;
            color: #fff;
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            margin-right: 6px;
            text-transform: uppercase;
        }
        .promo-chip-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #111b21;
            margin-right: 6px;
        }
        .promo-chip-price {
            font-size: 11.5px;
            font-weight: 700;
            color: #059669;
        }

        /* 3. Área de Conversación con Fondo WhatsApp */
        .chat-messages {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
            background-color: var(--wa-bg);
            background-image: radial-gradient(#d5cebe 1.2px, transparent 1.2px);
            background-size: 20px 20px;
            scroll-behavior: smooth;
        }

        .encryption-badge {
            align-self: center;
            background-color: rgba(255, 245, 196, 0.95);
            color: #54656f;
            font-size: 11px;
            padding: 6px 12px;
            border-radius: 8px;
            text-align: center;
            max-width: 90%;
            margin-bottom: 10px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1.3;
        }

        /* Burbujas de Chat */
        .message-row {
            display: flex;
            width: 100%;
        }

        .message-row.user {
            justify-content: flex-end;
        }

        .message-row.bot {
            justify-content: flex-start;
        }

        .bubble {
            max-width: 86%;
            padding: 7px 11px 6px 11px;
            border-radius: 8px;
            position: relative;
            font-size: 13.8px;
            line-height: 1.4;
            word-wrap: break-word;
            box-shadow: 0 1px 1.5px rgba(0,0,0,0.13);
        }

        .message-row.user .bubble {
            background-color: var(--wa-bubble-user);
            border-top-right-radius: 0px;
        }

        .message-row.bot .bubble {
            background-color: var(--wa-bubble-bot);
            border-top-left-radius: 0px;
        }

        .message-row.user .bubble::after {
            content: "";
            position: absolute;
            top: 0;
            right: -8px;
            width: 0;
            height: 0;
            border: 8px solid transparent;
            border-top-color: var(--wa-bubble-user);
            border-right: 0;
        }

        .message-row.bot .bubble::before {
            content: "";
            position: absolute;
            top: 0;
            left: -8px;
            width: 0;
            height: 0;
            border: 8px solid transparent;
            border-top-color: var(--wa-bubble-bot);
            border-left: 0;
        }

        .bubble-text {
            color: var(--wa-text);
            white-space: pre-wrap;
        }

        .bubble-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            font-size: 10.5px;
            color: var(--wa-muted);
            margin-top: 2px;
            float: right;
            margin-left: 8px;
        }

        .bubble-check {
            color: var(--wa-check);
            font-size: 11px;
        }

        /* Tarjeta de Ticket de Pedido Confirmado & Tracking */
        .order-ticket-card {
            background: #ffffff;
            border: 2px solid #22c55e;
            border-radius: 12px;
            padding: 12px;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .order-ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .tracking-status-bar {
            display: flex;
            justify-content: space-between;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 8px 6px;
            margin: 10px 0;
            font-size: 10.5px;
            text-align: center;
            font-weight: 600;
        }

        .tracking-step {
            flex: 1;
            color: #94a3b8;
            position: relative;
        }

        .tracking-step.active {
            color: #059669;
            font-weight: 700;
        }

        .btn-live-gps {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #008069 0%, #059669 100%);
            color: #ffffff;
            text-align: center;
            text-decoration: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13.5px;
            margin-top: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
            transition: all 0.2s ease;
        }

        .btn-live-gps:hover {
            background: #00a884;
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* Tarjeta de Transferencia Bancaria SIPAP */
        .bank-info-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px;
            margin-top: 8px;
            font-size: 12px;
        }

        .bank-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 0;
        }

        .btn-copy-chip {
            background: #e2e8f0;
            border: none;
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            color: #334155;
        }

        .btn-copy-chip:hover {
            background: #cbd5e1;
        }

        /* Barra Flotante de Carrito / Pedido en Curso */
        .floating-cart-bar {
            display: none;
            background: #111827;
            color: #ffffff;
            padding: 9px 14px;
            position: absolute;
            bottom: 95px;
            left: 12px;
            right: 12px;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            z-index: 30;
            justify-content: space-between;
            align-items: center;
            animation: slideUpCart 0.2s ease;
        }

        @keyframes slideUpCart {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .btn-checkout-cart {
            background: #25d366;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 6px 14px;
            font-weight: 700;
            font-size: 12.5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Tarjeta de Comprobante de Pago */
        .receipt-card-bubble {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            font-size: 13px;
            width: 100%;
        }

        .receipt-card-header {
            background: #f8fafc;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
        }

        .receipt-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .receipt-badge.badge-ok {
            background: #dcfce7;
            color: #15803d;
        }

        .receipt-badge.badge-warn {
            background: #fef3c7;
            color: #b45309;
        }

        .receipt-card-body {
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            color: #334155;
            padding-bottom: 3px;
            border-bottom: 1px dashed #f1f5f9;
        }

        .receipt-warning-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 600;
            margin-top: 4px;
        }

        .receipt-card-footer {
            background: #f8fafc;
            padding: 8px 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        .btn-view-receipt {
            color: var(--wa-teal);
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-view-receipt:hover {
            text-decoration: underline;
        }

        /* Indicador de escribiendo */
        .typing-indicator {
            display: none;
            align-self: flex-start;
            background-color: #ffffff;
            padding: 8px 14px;
            border-radius: 8px;
            border-top-left-radius: 0px;
            box-shadow: 0 1px 1.5px rgba(0,0,0,0.13);
            margin-bottom: 6px;
        }

        .typing-indicator span {
            height: 7px;
            width: 7px;
            background: #8696a0;
            display: inline-block;
            border-radius: 50%;
            animation: bounce 1.3s infinite ease-in-out;
            margin-right: 3px;
        }

        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; margin-right: 0; }

        @keyframes bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }

        /* 4. Barra de Atajos Rápidos (Chips) */
        .quick-chips-wrapper {
            background-color: rgba(240, 242, 245, 0.95);
            padding: 8px 10px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: none;
            border-top: 1px solid #e9edef;
            z-index: 15;
        }

        .quick-chips-wrapper::-webkit-scrollbar {
            display: none;
        }

        .chip-btn {
            background-color: #ffffff;
            border: 1px solid #d1d7db;
            color: #111b21;
            padding: 5px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .chip-btn:hover, .chip-btn:active {
            background-color: #e7ffdb;
            border-color: #25d366;
            color: #075e54;
        }

        .chip-btn.highlight {
            background-color: #008069;
            color: #ffffff;
            border-color: #008069;
            font-weight: 600;
        }

        .chip-btn.highlight-location {
            background-color: #ef4444;
            color: #ffffff;
            border-color: #dc2626;
            font-weight: 600;
        }

        /* 5. Barra de Entrada Inferior */
        .chat-input-bar {
            background-color: var(--wa-gray-bar);
            padding: 8px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-top: 1px solid #e9edef;
            z-index: 20;
        }

        .btn-input-action {
            background: none;
            border: none;
            color: #54656f;
            font-size: 18px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s ease;
            flex-shrink: 0;
        }

        .btn-input-action:hover {
            background-color: rgba(0,0,0,0.06);
            color: var(--wa-teal);
        }

        .chat-input-wrapper {
            flex: 1;
            background-color: #ffffff;
            border-radius: 20px;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .chat-input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 14px;
            font-family: inherit;
            color: var(--wa-text);
            background: transparent;
        }

        .chat-input::placeholder {
            color: #8696a0;
            font-size: 13.5px;
        }

        .btn-send {
            background-color: var(--wa-teal);
            color: #ffffff;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.18);
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .btn-send:hover {
            background-color: var(--wa-teal-light);
            transform: scale(1.05);
        }

        /* 6. Menú Visual / Drawer de Platos */
        .drawer-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 60;
            align-items: flex-end;
            justify-content: center;
        }

        .drawer-content {
            background: #ffffff;
            width: 100%;
            max-height: 85%;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 -5px 25px rgba(0,0,0,0.25);
            animation: slideUp 0.25s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .drawer-header {
            padding: 14px 16px;
            border-bottom: 1px solid #e9edef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--wa-teal);
            color: #ffffff;
        }

        .drawer-title {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 14px;
        }

        .category-block {
            margin-bottom: 18px;
        }

        .category-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--wa-teal-dark);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .product-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 8px;
            gap: 10px;
        }

        .product-info {
            flex: 1;
            min-width: 0;
        }

        .product-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        .product-desc {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .product-price {
            font-size: 13.5px;
            font-weight: 700;
            color: #059669;
            margin-top: 4px;
        }

        .btn-add-product {
            background-color: var(--wa-teal);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .btn-add-product:hover {
            background-color: var(--wa-teal-light);
        }

        /* 7. Modal Selector de Ubicación en el Mapa */
        .modal-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 70;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            overflow: hidden;
            animation: popIn 0.2s ease;
        }

        @keyframes popIn {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #54656f;
            margin-bottom: 4px;
        }

        .form-control-custom {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d7db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            font-family: inherit;
        }

        .form-control-custom:focus {
            border-color: var(--wa-teal);
            box-shadow: 0 0 0 2px rgba(0,128,105,0.15);
        }

        .btn-modal-save {
            width: 100%;
            background-color: var(--wa-teal);
            color: #ffffff;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            transition: all 0.15s ease;
        }

        .btn-modal-save:hover {
            background-color: var(--wa-teal-light);
        }
    </style>
</head>
<body>

<div class="app-container">
    <!-- 1. Header estilo WhatsApp -->
    <header class="chat-header">
        <div class="header-profile" onclick="openPhoneModal()">
            <div class="avatar-wrapper">
                <?= strtoupper(substr($store_name, 0, 1)) ?>
                <div class="online-dot"></div>
            </div>
            <div class="header-info">
                <span class="store-title"><?= htmlspecialchars($store_name) ?></span>
                <span class="store-status">
                    <i class="fa fa-circle" style="font-size: 7px; color: #25d366;"></i> en línea • Pedidos Online con IA
                </span>
            </div>
        </div>
        <div class="header-actions">
            <button type="button" class="btn-menu-drawer" onclick="toggleMenuDrawer()" title="Ver la carta con fotos y precios">
                <i class="fa fa-book"></i> Menú
            </button>
            <button type="button" class="btn-pwa-header" id="btn_header_pwa" onclick="installPwaApp()" title="Instalar App en tu celular">
                <i class="fa fa-mobile" style="font-size: 15px;"></i> <span>App</span>
            </button>
            <?php if (!empty($wa_number)): ?>
                <a href="https://wa.me/<?= $wa_number ?>?text=<?= urlencode('Hola, estuve viendo la web de ' . $store_name . ' y quisiera hacer un pedido.') ?>" target="_blank" class="btn-whatsapp-direct" id="btn_header_wa" title="Pasar a WhatsApp">
                    <i class="fa fa-whatsapp"></i> WhatsApp
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Banner Superior PWA Instalable -->
    <div class="pwa-install-banner" id="pwa_install_banner">
        <div class="pwa-banner-left">
            <div class="pwa-badge-icon">
                <i class="fa fa-mobile"></i>
            </div>
            <div class="pwa-banner-text">
                <div class="pwa-banner-title">📲 Instalar App de <?= htmlspecialchars($store_name) ?></div>
                <div class="pwa-banner-sub">Acceso directo en 1 toque • Sin Play Store</div>
            </div>
        </div>
        <div class="pwa-banner-actions">
            <button type="button" class="btn-pwa-cta" onclick="installPwaApp()">Instalar</button>
            <button type="button" class="btn-pwa-close" onclick="dismissPwaBanner()" title="Cerrar"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <!-- 2. Banner de Estado / Cierre Si Aplica -->
    <?php if ($is_local_cerrado): ?>
        <div class="notice-banner" style="background:#fee2e2; color:#b91c1c;">
            <i class="fa fa-clock-o"></i> <strong>Local Cerrado:</strong> <?= htmlspecialchars($mensaje_cerrado) ?>
        </div>
    <?php elseif ($is_delivery_cerrado): ?>
        <div class="notice-banner">
            <i class="fa fa-motorcycle"></i> <strong>Aviso:</strong> Envíos en pausa temporal (Solo retiro en local / Take Away).
        </div>
    <?php endif; ?>

    <!-- 2.5 Barra de Promociones y Flyers Vigentes Hoy -->
    <?php if (!empty($promos_hoy)): ?>
        <div class="promos-carousel-strip" id="promos_today_strip">
            <div style="display:flex; align-items:center; gap:4px; font-size:11px; font-weight:700; color:#e11d48; padding-right:6px; border-right:1px solid #fecdd3; flex-shrink:0;">
                <i class="fa fa-fire"></i> PROMOS HOY
            </div>
            <?php foreach ($promos_hoy as $pr): ?>
                <div class="promo-chip-card" onclick="openPromoDetailModal(<?= htmlspecialchars(json_encode($pr, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)">
                    <?php if (!empty($pr['imagen_url'])): ?>
                        <img src="<?= htmlspecialchars($pr['imagen_url']) ?>" class="promo-chip-img" alt="Promo">
                    <?php else: ?>
                        <div class="promo-chip-img" style="display:flex; align-items:center; justify-content:center; background:#fee2e2; color:#e11d48; font-size:12px;">
                            <i class="fa fa-tag"></i>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($pr['badge'])): ?>
                        <span class="promo-chip-badge"><?= htmlspecialchars($pr['badge']) ?></span>
                    <?php endif; ?>
                    <span class="promo-chip-title"><?= htmlspecialchars($pr['titulo']) ?></span>
                    <?php if (!empty($pr['precio_promo'])): ?>
                        <span class="promo-chip-price"><?= htmlspecialchars($pr['precio_promo']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 3. Mensajes del Chat -->
    <div class="chat-messages" id="chat_messages">
        <div class="encryption-badge">
            <i class="fa fa-shield" style="color: #059669; font-size: 13px;"></i>
            <span>Conectado al sistema de cocina y delivery en vivo de <strong><?= htmlspecialchars($store_name) ?></strong>.</span>
        </div>

        <!-- Mensaje de Bienvenida Inteligente -->
        <div class="message-row bot" id="welcome_message_row">
            <div class="bubble">
                <div class="bubble-text" id="welcome_text">¡Hola! Bienvenido a la web de pedidos de <strong><?= htmlspecialchars($store_name) ?></strong> 🍔🛵

¿Qué te gustaría comer hoy? Puedes escribir lo que desees, pulsar en **'Ver Menú'** arriba para ver la carta, o tocar **'📍 Elegir Ubicación'** para calcular tu costo de delivery exacto en el mapa.</div>
                <div class="bubble-meta">
                    <span class="msg-time"></span>
                </div>
            </div>
        </div>

        <div class="typing-indicator" id="typing_indicator">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <!-- Barra Flotante de Carrito en Vivo -->
    <div class="floating-cart-bar" id="floating_cart_bar">
        <div>
            <div style="font-size:11px; color:#9ca3af;">Tu Pedido Actual:</div>
            <strong id="cart_summary_text" style="font-size:13.5px; color:#ffffff;">0 items • 0 Gs</strong>
        </div>
        <button type="button" class="btn-checkout-cart" onclick="openCheckoutModal()">
            <i class="fa fa-shopping-cart"></i> Confirmar Pedido
        </button>
    </div>

    <!-- 4. Barra de Atajos Rápidos (Chips) -->
    <div class="quick-chips-wrapper">
        <button type="button" class="chip-btn highlight-pwa" id="chip_install_pwa" onclick="installPwaApp()">
            <i class="fa fa-mobile text-success" style="font-size:14px;"></i> 📲 Instalar App
        </button>
        <button type="button" class="chip-btn highlight-location" onclick="openMapPicker()">
            <i class="fa fa-map-marker"></i> 📍 Elegir Ubicación en el Mapa
        </button>
        <button type="button" class="chip-btn highlight" onclick="toggleMenuDrawer()">
            <i class="fa fa-cutlery"></i> 🍔 Ver Menú & Platos
        </button>
        <button type="button" class="chip-btn" id="chip_saved_address" style="display:none;" onclick="useSavedAddress()">
            <i class="fa fa-home text-success"></i> 🏠 Mi Dirección Guardada
        </button>
        <button type="button" class="chip-btn" onclick="openPaymentInfoCard()">
            <i class="fa fa-credit-card text-info"></i> 💳 Formas de Pago & Alias
        </button>
        <button type="button" class="chip-btn" onclick="$('#file_receipt_input').click()">
            <i class="fa fa-paperclip text-primary"></i> 📤 Subir Comprobante
        </button>
        <button type="button" class="chip-btn" onclick="sendQuickMessage('¿Cuáles son las promos de hoy?')">
            <i class="fa fa-fire text-danger"></i> 🔥 Promos de Hoy
        </button>
        <button type="button" class="chip-btn" onclick="sendQuickMessage('¿Cuánto cuesta el delivery según la distancia?')">
            <i class="fa fa-motorcycle text-primary"></i> 🛵 Costo Delivery
        </button>
    </div>

    <!-- 5. Barra de Entrada Inferior -->
    <div class="chat-input-bar">
        <button type="button" class="btn-input-action" onclick="openMapPicker()" title="Elegir mi Ubicación en el Mapa">
            <i class="fa fa-map-marker" style="color: #ef4444;"></i>
        </button>

        <button type="button" class="btn-input-action" id="btn_receipt_clip" onclick="$('#file_receipt_input').click()" title="Adjuntar comprobante de pago (SIPAP o foto)">
            <i class="fa fa-paperclip" style="color: #0284c7; font-size: 19px;"></i>
        </button>
        <input type="file" id="file_receipt_input" accept="image/*,application/pdf" style="display:none;" onchange="handleReceiptFileSelect(this.files[0], false)">

        <button type="button" class="btn-input-action" id="btn_voice" onclick="toggleVoiceRecording()" title="Dictar por voz">
            <i class="fa fa-microphone"></i>
        </button>

        <div class="chat-input-wrapper">
            <input type="text" id="chat_input" class="chat-input" placeholder="Escribe tu pedido o consulta..." autocomplete="off">
        </div>

        <button type="button" class="btn-send" id="btn_send" onclick="sendMessage()" title="Enviar">
            <i class="fa fa-paper-plane"></i>
        </button>
    </div>

    <!-- 6. Menú Visual / Drawer de Platos -->
    <div class="drawer-overlay" id="menu_drawer" onclick="if(event.target===this) toggleMenuDrawer()">
        <div class="drawer-content">
            <div class="drawer-header">
                <div class="drawer-title">
                    <i class="fa fa-cutlery"></i> Carta de <?= htmlspecialchars($store_name) ?>
                </div>
                <button type="button" style="background:none; border:none; color:#ffffff; font-size:18px; cursor:pointer;" onclick="toggleMenuDrawer()">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <?php if (!empty($promos_hoy)): ?>
                    <div class="category-block mb-3" style="background:#fff1f2; border: 1px solid #fecdd3; border-radius: 14px; padding: 12px;">
                        <div class="category-name text-danger font-weight-bold mb-2" style="font-size:14px;">
                            <i class="fa fa-fire mr-1"></i> 🔥 Promociones Especiales de Hoy
                        </div>
                        <?php foreach ($promos_hoy as $pr): ?>
                            <div class="product-card" style="background:#ffffff; border-radius:10px; margin-bottom:8px; border:1px solid #fda4af; padding:8px 10px; display:flex; align-items:center;">
                                <?php if (!empty($pr['imagen_url'])): ?>
                                    <img src="<?= htmlspecialchars($pr['imagen_url']) ?>" style="width:55px; height:55px; border-radius:8px; object-fit:cover; margin-right:10px; cursor:pointer; flex-shrink:0;" alt="Flyer" title="Toca para ver el flyer completo" onclick="openFlyerLightbox('<?= htmlspecialchars($pr['imagen_url']) ?>', '<?= htmlspecialchars(addslashes($pr['titulo'])) ?>')">
                                <?php else: ?>
                                    <div style="width:55px; height:55px; border-radius:8px; background:#fee2e2; color:#e11d48; display:flex; align-items:center; justify-content:center; margin-right:10px; flex-shrink:0; font-size:18px;">
                                        <i class="fa fa-bullhorn"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="product-info" style="flex:1;">
                                    <div class="product-title" style="color:#be123c; font-size:13.5px; font-weight:700;">
                                        <?php if (!empty($pr['badge'])): ?>
                                            <span style="background:#e11d48; color:#fff; font-size:9.5px; padding:2px 5px; border-radius:6px; margin-right:4px;"><?= htmlspecialchars($pr['badge']) ?></span>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($pr['titulo']) ?>
                                    </div>
                                    <?php if (!empty($pr['descripcion'])): ?>
                                        <div class="product-desc" style="font-size:11px; color:#64748b; margin-top:2px; line-height:1.25;"><?= htmlspecialchars($pr['descripcion']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($pr['precio_promo'])): ?>
                                        <div class="product-price text-success font-weight-bold" style="font-size:13px; margin-top:3px;"><?= htmlspecialchars($pr['precio_promo']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn-add-product" style="background:#e11d48; border-color:#e11d48; padding:6px 12px; font-size:12px; flex-shrink:0;" onclick="toggleMenuDrawer(); sendQuickMessage('Quiero pedir la promo: <?= htmlspecialchars(addslashes($pr['titulo'])) ?>')">
                                    <i class="fa fa-shopping-cart"></i> Pedir
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($menu)): ?>
                    <p class="text-muted text-center py-4">No hay productos registrados en el menú actualmente.</p>
                <?php else: ?>
                    <?php foreach ($menu as $cat_name => $products): ?>
                        <div class="category-block">
                            <div class="category-name">
                                <i class="fa fa-tag mr-1"></i> <?= htmlspecialchars($cat_name) ?>
                            </div>
                            <?php foreach ($products as $prod): ?>
                                <div class="product-card">
                                    <div class="product-info">
                                        <div class="product-title"><?= htmlspecialchars($prod['name']) ?></div>
                                        <?php if (!empty($prod['description'])): ?>
                                            <div class="product-desc"><?= htmlspecialchars($prod['description']) ?></div>
                                        <?php endif; ?>
                                        <div class="product-price"><?= $prod['price_fmt'] ?></div>
                                    </div>
                                    <button type="button" class="btn-add-product" onclick="addItemToOrder(<?= $prod['id'] ?>, '<?= addslashes($prod['name']) ?>', <?= (float)$prod['price'] ?>)">
                                        <i class="fa fa-plus"></i> Pedir
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 7. MODAL SELECTOR INTERACTIVO DE UBICACIÓN EN EL MAPA -->
    <div class="modal-overlay" id="modal_map_picker" style="z-index: 90;" onclick="if(event.target===this) closeMapPicker()">
        <div class="modal-card" style="max-width: 500px; padding:0; border-radius:18px; overflow:hidden;">
            <div style="background:var(--wa-teal); color:#ffffff; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                <div style="font-weight:700; font-size:15px; display:flex; align-items:center; gap:8px;">
                    <i class="fa fa-map-marker" style="color:#ef4444; font-size:19px;"></i>
                    <span>Elegir Ubicación de Entrega</span>
                </div>
                <button type="button" onclick="closeMapPicker()" style="background:none; border:none; color:#ffffff; font-size:18px; cursor:pointer;">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div style="background:#f0fdf4; border-bottom:1px solid #bbf7d0; color:#166534; padding:8px 14px; font-size:12px; display:flex; align-items:center; justify-content:space-between;">
                <span><i class="fa fa-hand-pointer-o mr-1"></i> Mové el mapa hasta tu casa o dirección</span>
                <button type="button" onclick="centerOnUserGps()" style="background:#22c55e; color:#ffffff; border:none; border-radius:12px; padding:3px 10px; font-weight:700; font-size:11px; cursor:pointer;">
                    <i class="fa fa-crosshairs"></i> Mi GPS
                </button>
            </div>

            <div style="position:relative; width:100%; height:310px; background:#e2e8f0;">
                <div id="leaflet_map" style="width:100%; height:100%;"></div>
                
                <div id="map_center_pin" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -100%); pointer-events:none; z-index:1000; text-align:center;">
                    <div style="background:#111827; color:#ffffff; font-size:11px; font-weight:700; padding:3px 9px; border-radius:12px; white-space:nowrap; margin-bottom:2px; box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                        📍 Entregar aquí
                    </div>
                    <i class="fa fa-map-marker" style="font-size:38px; color:#ef4444; filter:drop-shadow(0 3px 4px rgba(0,0,0,0.45));"></i>
                </div>
            </div>

            <div style="background:#f8fafc; padding:12px 16px; border-top:1px solid #e2e8f0;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <div>
                        <span style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:0.5px;">Distancia al Local</span>
                        <div id="picker_dist_label" style="font-size:15px; font-weight:700; color:#1e293b;">Calculando...</div>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:0.5px;">Costo de Envío</span>
                        <div id="picker_fee_label" style="font-size:17px; font-weight:800; color:#059669;">-</div>
                    </div>
                </div>

                <div id="picker_zone_badge" style="font-size:12px; margin-bottom:10px;">
                    <span style="color:#64748b;"><i class="fa fa-info-circle mr-1"></i> Arrastra el mapa para ubicar tu casa</span>
                </div>

                <button type="button" id="btn_confirm_map_location" onclick="confirmSelectedLocation()" class="btn-modal-save" style="margin:0; background:var(--wa-teal); font-size:14px; font-weight:700; display:flex; align-items:center; justify-content:center; gap:6px;">
                    <i class="fa fa-check-circle"></i> Confirmar esta Ubicación
                </button>
            </div>
        </div>
    </div>

    <!-- 8. MODAL DE CIERRE DE PEDIDO Y MÉTODOS DE PAGO -->
    <div class="modal-overlay" id="modal_checkout" style="z-index: 85;" onclick="if(event.target===this) closeCheckoutModal()">
        <div class="modal-card" style="max-width: 500px; padding:0; border-radius:18px; overflow:hidden;">
            <div style="background:var(--wa-teal); color:#ffffff; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                <div style="font-weight:700; font-size:15px; display:flex; align-items:center; gap:8px;">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Confirmar tu Pedido</span>
                </div>
                <button type="button" onclick="closeCheckoutModal()" style="background:none; border:none; color:#ffffff; font-size:18px; cursor:pointer;">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div style="padding:16px; max-height:75vh; overflow-y:auto;">
                <!-- Lista de platos seleccionados -->
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px; margin-bottom:12px;">
                    <strong style="font-size:12.5px; color:#334155; display:block; margin-bottom:6px;">Detalle de Platos:</strong>
                    <div id="checkout_items_list"></div>
                    <div style="border-top:1px dashed #cbd5e1; margin-top:8px; padding-top:8px; display:flex; justify-content:space-between; font-size:13px;">
                        <span>Subtotal:</span>
                        <strong id="checkout_subtotal_text">0 Gs</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:13px; color:#059669; margin-top:2px;">
                        <span>Delivery (<span id="checkout_km_text">0 km</span>):</span>
                        <strong id="checkout_shipping_text">0 Gs</strong>
                    </div>
                    <div style="border-top:2px solid #e2e8f0; margin-top:6px; padding-top:6px; display:flex; justify-content:space-between; font-size:15px; font-weight:800; color:#1e293b;">
                        <span>Total a Pagar:</span>
                        <span id="checkout_total_text" style="color:#059669;">0 Gs</span>
                    </div>
                </div>

                <!-- Tipo de entrega -->
                <div class="form-group">
                    <label>Tipo de Entrega:</label>
                    <div style="display:flex; gap:8px;">
                        <label style="flex:1; border:1px solid #d1d7db; border-radius:8px; padding:8px; text-align:center; font-size:12.5px; font-weight:600; cursor:pointer;" id="label_opt_delivery">
                            <input type="radio" name="checkout_delivery_type" value="delivery" checked onchange="toggleDeliveryType('delivery')"> 🛵 Delivery
                        </label>
                        <label style="flex:1; border:1px solid #d1d7db; border-radius:8px; padding:8px; text-align:center; font-size:12.5px; font-weight:600; cursor:pointer;" id="label_opt_takeaway">
                            <input type="radio" name="checkout_delivery_type" value="takeaway" onchange="toggleDeliveryType('takeaway')"> 🏪 Retiro en Local
                        </label>
                    </div>
                </div>

                <!-- Datos de contacto -->
                <div class="form-group">
                    <label>Tu Nombre y Teléfono:</label>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="order_cust_name" class="form-control-custom" placeholder="Nombre" style="flex:1;">
                        <input type="tel" id="order_cust_phone" class="form-control-custom" placeholder="WhatsApp / Teléfono" style="flex:1;">
                    </div>
                </div>

                <!-- Dirección / Ubicación -->
                <div class="form-group" id="group_delivery_address">
                    <label style="display:flex; justify-content:space-between;">
                        <span>Dirección de Entrega:</span>
                        <a href="javascript:void(0)" onclick="closeCheckoutModal(); openMapPicker();" style="color:var(--wa-teal); font-weight:600; font-size:11.5px;">📍 Cambiar en mapa</a>
                    </label>
                    <input type="text" id="order_cust_address" class="form-control-custom" placeholder="Ej: Calle 12 de Junio c/ Av. Fernando de la Mora">
                </div>

                <!-- Método de Pago -->
                <div class="form-group">
                    <label>Forma de Pago:</label>
                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <!-- Opción 1: Efectivo -->
                        <label style="border:1px solid #cbd5e1; border-radius:8px; padding:10px; cursor:pointer; background:#ffffff;">
                            <div style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700;">
                                <input type="radio" name="order_payment_method" value="efectivo" checked onchange="togglePaymentMethod('efectivo')">
                                <span>💵 Efectivo al recibir</span>
                            </div>
                            <div id="panel_vuelto_calc" style="margin-top:8px; padding-top:8px; border-top:1px dashed #e2e8f0;">
                                <span style="font-size:11.5px; color:#64748b; font-weight:600;">¿Con cuánto vas a pagar?</span>
                                <div style="display:flex; gap:6px; margin-top:4px;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setCashAmount('exacto')" style="border-radius:6px; font-size:11px; padding:4px 8px;">Monto Exacto</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setCashAmount(50000)" style="border-radius:6px; font-size:11px; padding:4px 8px;">50.000 Gs</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setCashAmount(100000)" style="border-radius:6px; font-size:11px; padding:4px 8px;">100.000 Gs</button>
                                </div>
                                <input type="number" id="order_cash_paga_con" class="form-control-custom" placeholder="O ingresa monto aquí" style="margin-top:6px;" oninput="recalcVuelto()">
                                <div id="vuelto_result_label" style="font-size:12px; font-weight:700; color:#059669; margin-top:4px;"></div>
                            </div>
                        </label>

                        <!-- Opción 2: Transferencia Bancaria SIPAP -->
                        <label style="border:1px solid #cbd5e1; border-radius:8px; padding:10px; cursor:pointer; background:#ffffff;">
                            <div style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700;">
                                <input type="radio" name="order_payment_method" value="transferencia" onchange="togglePaymentMethod('transferencia')">
                                <span>🏦 Transferencia SIPAP / Bancaria</span>
                            </div>
                            <div id="panel_sipap_info" style="display:none; margin-top:8px; padding-top:8px; border-top:1px dashed #e2e8f0; font-size:12px; color:#475569;">
                                <div class="bank-row">
                                    <span><strong>Banco:</strong> <?= htmlspecialchars($banco_info['nombre']) ?></span>
                                </div>
                                <div class="bank-row">
                                    <span><strong>Titular:</strong> <?= htmlspecialchars($banco_info['titular']) ?></span>
                                </div>
                                <?php if (!empty($banco_info['ruc_ci'])): ?>
                                    <div class="bank-row">
                                        <span><strong>RUC / CI:</strong> <?= htmlspecialchars($banco_info['ruc_ci']) ?></span>
                                        <button type="button" class="btn-copy-chip" onclick="copyToClipboard('<?= addslashes($banco_info['ruc_ci']) ?>')"><i class="fa fa-copy"></i> Copiar</button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($banco_info['alias'])): ?>
                                    <div class="bank-row" style="background:#e0f2fe; padding:4px 6px; border-radius:6px; margin-top:4px;">
                                        <span style="color:#0369a1;"><strong>Alias:</strong> <?= htmlspecialchars($banco_info['alias']) ?></span>
                                        <button type="button" class="btn-copy-chip" style="background:#0284c7; color:#fff;" onclick="copyToClipboard('<?= addslashes($banco_info['alias']) ?>')"><i class="fa fa-copy"></i> Copiar Alias</button>
                                    </div>
                                <?php endif; ?>

                                <!-- Dropzone para adjuntar comprobante en Checkout -->
                                <div style="margin-top:10px; padding:10px; background:#f8fafc; border:1.5px dashed #38bdf8; border-radius:10px; text-align:center;">
                                    <div id="checkout_receipt_dropzone" style="cursor:pointer;" onclick="$('#checkout_file_receipt').click()">
                                        <i class="fa fa-cloud-upload" style="font-size:24px; color:#0284c7; margin-bottom:4px;"></i>
                                        <div style="font-size:12.5px; font-weight:700; color:#0369a1;">Adjuntar Comprobante de Pago</div>
                                        <div style="font-size:11px; color:#64748b;">Toca aquí para subir foto o PDF (la IA lo analizará)</div>
                                    </div>
                                    <input type="file" id="checkout_file_receipt" accept="image/*,application/pdf" style="display:none;" onchange="handleReceiptFileSelect(this.files[0], true)">
                                    
                                    <div id="checkout_receipt_status" style="display:none; margin-top:4px; font-size:11.5px; text-align:left;"></div>
                                </div>
                            </div>
                        </label>

                        <!-- Opción 3: QR / Tarjeta -->
                        <label style="border:1px solid #cbd5e1; border-radius:8px; padding:10px; cursor:pointer; background:#ffffff;">
                            <div style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700;">
                                <input type="radio" name="order_payment_method" value="qr" onchange="togglePaymentMethod('qr')">
                                <span>📱 Pago por QR / Tarjeta en Puerta</span>
                            </div>
                            <div id="panel_qr_info" style="display:none; margin-top:6px; font-size:11.5px; color:#64748b;">
                                El repartidor llevará el POS para cobro con QR (Bancard, Zimple, Pix) o tarjeta.
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Observaciones para cocina -->
                <div class="form-group">
                    <label>Notas para Cocina (Opcional):</label>
                    <input type="text" id="order_notes" class="form-control-custom" placeholder="Ej: Sin cebolla, extra aderezo, casa portón verde...">
                </div>

                <!-- Botón Confirmar Final -->
                <button type="button" id="btn_submit_order" onclick="submitFinalOrder()" class="btn-modal-save" style="background:#008069; font-size:15px; font-weight:700; padding:12px; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <i class="fa fa-check-circle"></i> Confirmar Pedido Ahora
                </button>
            </div>
        </div>
    </div>

    <!-- 9. Modal de Bienvenida: Nombre & Celular -->
    <div class="modal-overlay" id="modal_phone" style="z-index: 98;" onclick="if(event.target===this) closePhoneModal()">
        <div class="modal-card" style="max-width: 420px; padding: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
            <div style="background: linear-gradient(135deg, #075e54 0%, #008069 100%); color: #ffffff; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 9px;">
                    <i class="fa fa-user-circle" style="font-size: 20px;"></i>
                    <span>¡Te damos la bienvenida! 👋</span>
                </div>
                <button type="button" class="btn-modal-close" style="color: #ffffff; opacity: 0.85; font-size: 22px; line-height: 1;" onclick="closePhoneModal()">&times;</button>
            </div>
            <div style="padding: 20px;">
                <p style="font-size: 13px; color: #4b5563; margin-bottom: 16px; line-height: 1.45;">
                    Para atenderte de forma personalizada y coordinar la entrega de tus pedidos en <strong><?= htmlspecialchars($store_name) ?></strong>, por favor completá tus datos:
                </p>

                <div id="cust_data_error" style="display: none; color: #dc2626; background: #fee2e2; border: 1px solid #fca5a5; padding: 8px 12px; border-radius: 8px; font-size: 12px; margin-bottom: 14px;"></div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label style="font-size: 12.5px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px;">
                        <i class="fa fa-user text-primary mr-1"></i> Tu Nombre y Apellido: <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="cust_name" class="form-control-custom" placeholder="Ej: Juan Carlos Pérez" style="font-size: 14px; padding: 10px 14px; border-radius: 10px;">
                </div>

                <div class="form-group" style="margin-bottom: 18px;">
                    <label style="font-size: 12.5px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px;">
                        <i class="fa fa-whatsapp text-success mr-1"></i> WhatsApp / Celular: <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="tel" id="cust_phone" class="form-control-custom" placeholder="Ej: 0981 123 456" style="font-size: 14px; padding: 10px 14px; border-radius: 10px;">
                    <small style="color: #6b7280; font-size: 11px; display: block; margin-top: 4px;">Lo usaremos para avisarte cuando tu pedido salga en camino.</small>
                </div>

                <button type="button" class="btn-modal-save" style="width: 100%; padding: 12px; font-size: 14px; font-weight: 700; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 128, 105, 0.35);" onclick="saveCustomerData()">
                    <i class="fa fa-check mr-1"></i> Empezar a Pedir
                </button>

                <div style="text-align: center; margin-top: 12px;">
                    <a href="javascript:void(0)" onclick="closePhoneModal()" style="font-size: 12px; color: #6b7280; text-decoration: none;">
                        Explorar el menú primero <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón Flotante para Instalar PWA -->
    <div class="floating-pwa-pill" id="floating_pwa_pill" onclick="installPwaApp()">
        <span class="pwa-pulse-dot"></span>
        <i class="fa fa-mobile" style="font-size: 16px;"></i>
        <span>📲 Instalar App de <?= htmlspecialchars($store_name) ?></span>
        <span class="floating-pwa-close" onclick="event.stopPropagation(); dismissFloatingPwa();" title="Ocultar">✕</span>
    </div>

    <!-- Modal Guía de Instalación PWA para iOS (iPhone / iPad) -->
    <div class="modal-overlay" id="modal_pwa_ios" style="z-index: 95;" onclick="if(event.target===this) closePwaIosModal()">
        <div class="modal-card" style="max-width: 420px; padding: 0; border-radius: 18px; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #075e54 0%, #008069 100%); color: #ffffff; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-apple" style="font-size: 18px;"></i>
                    <span>Instalar App en tu iPhone</span>
                </div>
                <button type="button" class="btn-modal-close" style="color: #ffffff; opacity: 0.8; font-size: 20px;" onclick="closePwaIosModal()">&times;</button>
            </div>
            <div style="padding: 18px; font-size: 13px; line-height: 1.5; color: #374151;">
                <p style="margin-bottom: 14px; font-weight: 500;">
                    Agregá <strong><?= htmlspecialchars($store_name) ?></strong> a tu pantalla de inicio en 3 simples pasos:
                </p>
                <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 12px; background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="width: 26px; height: 26px; border-radius: 50%; background: #008069; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">1</div>
                    <div>Toca el botón <strong>Compartir</strong> <i class="fa fa-share-square-o" style="color: #007aff; font-size: 16px;"></i> (el cuadrado con flecha hacia arriba) en la barra inferior de Safari.</div>
                </div>
                <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 12px; background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="width: 26px; height: 26px; border-radius: 50%; background: #008069; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">2</div>
                    <div>Desliza hacia abajo en la lista y toca en <strong>"Agregar a pantalla de inicio"</strong> ➕.</div>
                </div>
                <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 16px; background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="width: 26px; height: 26px; border-radius: 50%; background: #008069; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">3</div>
                    <div>Toca <strong>"Agregar"</strong> arriba a la derecha. ¡Listo! Tendrás el ícono en tu pantalla sin ocupar memoria ni pasar por la App Store.</div>
                </div>
                <button type="button" class="btn-modal-save" style="width: 100%; border-radius: 12px; padding: 11px;" onclick="closePwaIosModal()">¡Entendido!</button>
            </div>
        </div>
    </div>

    <!-- Modal Guía de Instalación PWA para Android / PC -->
    <div class="modal-overlay" id="modal_pwa_generic" style="z-index: 95;" onclick="if(event.target===this) closePwaGenericModal()">
        <div class="modal-card" style="max-width: 420px; padding: 0; border-radius: 18px; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #075e54 0%, #008069 100%); color: #ffffff; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-mobile" style="font-size: 18px;"></i>
                    <span>Instalar App de <?= htmlspecialchars($store_name) ?></span>
                </div>
                <button type="button" class="btn-modal-close" style="color: #ffffff; opacity: 0.8; font-size: 20px;" onclick="closePwaGenericModal()">&times;</button>
            </div>
            <div style="padding: 18px; font-size: 13px; line-height: 1.5; color: #374151;">
                <p style="margin-bottom: 12px; font-weight: 500;">
                    Podés instalar la aplicación en 1 paso según tu navegador:
                </p>
                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 12px;">
                    <strong><i class="fa fa-android text-success"></i> En Chrome / Navegador para Android:</strong>
                    <p style="margin-top: 4px; font-size: 12.5px; color: #4b5563;">
                        Toca los <strong>3 puntos (⋮)</strong> arriba a la derecha y selecciona <strong>"Instalar aplicación"</strong> o <strong>"Agregar a la pantalla principal"</strong>.
                    </p>
                </div>
                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 16px;">
                    <strong><i class="fa fa-desktop text-primary"></i> En la Computadora (Chrome / Edge):</strong>
                    <p style="margin-top: 4px; font-size: 12.5px; color: #4b5563;">
                        Haz clic en el ícono de instalar <strong>⊕</strong> al final de la barra de direcciones de tu navegador.
                    </p>
                </div>
                <button type="button" class="btn-modal-save" style="width: 100%; border-radius: 12px; padding: 11px;" onclick="closePwaGenericModal()">¡Entendido!</button>
            </div>
        </div>
    </div>

    <!-- Modal Lightbox para Flyer en WebApp -->
    <div class="modal-overlay" id="modal_flyer_lightbox" style="display:none; z-index:9999; background:rgba(0,0,0,0.85); align-items:center; justify-content:center; padding:15px;" onclick="if(event.target===this) closeFlyerLightbox()">
        <div style="max-width: 92vw; max-height: 90vh; text-align: center; position: relative;">
            <button type="button" onclick="closeFlyerLightbox()" style="position:absolute; top:-38px; right:0; background:none; border:none; color:#ffffff; font-size:26px; cursor:pointer; padding:5px;" title="Cerrar">
                <i class="fa fa-times"></i>
            </button>
            <img id="lightbox_flyer_img" src="" style="max-width:100%; max-height:78vh; border-radius:12px; box-shadow:0 12px 35px rgba(0,0,0,0.6); object-fit:contain; display:block; margin:0 auto;" alt="Flyer">
            <div id="lightbox_flyer_caption" style="color:#ffffff; font-weight:700; margin-top:12px; font-size:14px; text-shadow:0 1px 3px rgba(0,0,0,0.8);"></div>
            <div style="margin-top:10px;">
                <button type="button" id="btn_lightbox_order_promo" class="btn-checkout-cart" style="background:#e11d48; border-color:#e11d48; display:inline-block; padding:8px 18px; font-size:13px;" onclick="orderPromoFromLightbox()">
                    <i class="fa fa-shopping-cart mr-1"></i> ¡Pedir esta Promo!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const LOC_ID = <?= (int)$loc_id ?>;
    const STORE_NAME = <?= json_encode($store_name) ?>;
    const STORE_LAT = <?= (float)$store_lat ?>;
    const STORE_LNG = <?= (float)$store_lng ?>;
    const MAX_RADIO_KM = <?= (float)$radio_max_km ?>;
    const TRAMOS_LIST = <?= json_encode($tramos) ?>;
    const WA_NUMBER = <?= json_encode($wa_number) ?>;
    const BANCO_INFO = <?= json_encode($banco_info) ?>;
    const CHAT_API_URL = "<?= base_url('p/chat') ?>";
    const CONFIRM_ORDER_URL = "<?= base_url('p/confirm_order') ?>";
    const UPLOAD_COMPROBANTE_URL = "<?= base_url('p/upload_comprobante') ?>";

    let currentLightboxPromoTitle = '';

    function openFlyerLightbox(imgUrl, caption, promoTitle) {
        if (!imgUrl) return;
        $('#lightbox_flyer_img').attr('src', imgUrl);
        $('#lightbox_flyer_caption').text(caption || promoTitle || '');
        currentLightboxPromoTitle = promoTitle || caption || '';
        $('#modal_flyer_lightbox').css('display', 'flex');
    }

    function closeFlyerLightbox() {
        $('#modal_flyer_lightbox').hide();
    }

    function openPromoDetailModal(promo) {
        if (!promo) return;
        var caption = promo.titulo + (promo.precio_promo ? ' (' + promo.precio_promo + ')' : '');
        if (promo.imagen_url) {
            openFlyerLightbox(promo.imagen_url, caption, promo.titulo);
        } else {
            var msg = "Quiero pedir la promo: " + promo.titulo;
            sendMessage(msg);
        }
    }

    function orderPromoFromLightbox() {
        closeFlyerLightbox();
        if (currentLightboxPromoTitle) {
            sendMessage("Quiero pedir la promo: " + currentLightboxPromoTitle);
        }
    }

    let uploadedReceiptData = null;
    let activeInvoiceId = null;

    // 1. Session ID & Memoria del Cliente (CRM)
    let sessionId = localStorage.getItem('geekspos_web_session');
    if (!sessionId) {
        sessionId = 'web_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
        localStorage.setItem('geekspos_web_session', sessionId);
    }

    let customerName = localStorage.getItem('geekspos_cust_name') || '';
    let customerPhone = localStorage.getItem('geekspos_cust_phone') || '';
    let savedAddress = localStorage.getItem('geekspos_cust_address') || '';
    let savedLat = parseFloat(localStorage.getItem('geekspos_cust_lat')) || null;
    let savedLng = parseFloat(localStorage.getItem('geekspos_cust_lng')) || null;

    if (customerName) {
        $('#cust_name').val(customerName);
        $('#order_cust_name').val(customerName);
    }
    if (customerPhone) {
        $('#cust_phone').val(customerPhone);
        $('#order_cust_phone').val(customerPhone);
    }
    if (savedAddress) {
        $('#order_cust_address').val(savedAddress);
        $('#chip_saved_address').show();
    }

    // Carrito de compras en memoria
    let currentCart = [];
    let currentDeliveryKm = 0;
    let currentShippingFee = 0;
    let selectedDeliveryLat = savedLat || STORE_LAT;
    let selectedDeliveryLng = savedLng || STORE_LNG;

    function getCurrentTime() {
        const d = new Date();
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    $('.msg-time').first().text(getCurrentTime());

    function scrollToBottom() {
        const el = document.getElementById('chat_messages');
        el.scrollTop = el.scrollHeight;
    }

    function appendBubble(text, sender, time) {
        const timeStr = time || getCurrentTime();
        const checkHtml = sender === 'user' ? ' <i class="fa fa-check bubble-check"></i><i class="fa fa-check bubble-check" style="margin-left:-3px;"></i>' : '';

        let formatted = (text || '')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');

        const row = document.createElement('div');
        row.className = 'message-row ' + sender;
        row.innerHTML = 
            '<div class="bubble">' +
                '<div class="bubble-text">' + formatted + '</div>' +
                '<div class="bubble-meta">' +
                    '<span>' + timeStr + '</span>' + checkHtml +
                '</div>' +
            '</div>';

        const indicator = document.getElementById('typing_indicator');
        indicator.parentNode.insertBefore(row, indicator);
        scrollToBottom();
    }

    function appendCustomBubble(htmlContent, sender, time) {
        const timeStr = time || getCurrentTime();
        const checkHtml = sender === 'user' ? ' <i class="fa fa-check bubble-check"></i><i class="fa fa-check bubble-check" style="margin-left:-3px;"></i>' : '';

        const row = document.createElement('div');
        row.className = 'message-row ' + sender;
        row.innerHTML = 
            '<div class="bubble" style="max-width:92%;">' +
                '<div class="bubble-text">' + htmlContent + '</div>' +
                '<div class="bubble-meta">' +
                    '<span>' + timeStr + '</span>' + checkHtml +
                '</div>' +
            '</div>';

        const indicator = document.getElementById('typing_indicator');
        indicator.parentNode.insertBefore(row, indicator);
        scrollToBottom();
    }

    // Agregar items al carrito
    function addItemToOrder(prodId, prodName, prodPrice) {
        let existing = currentCart.find(i => i.id === prodId);
        if (existing) {
            existing.qty += 1;
        } else {
            currentCart.push({ id: prodId, name: prodName, price: prodPrice, qty: 1 });
        }
        updateCartBar();
        toggleMenuDrawer();

        const msg = "Quiero pedir: 1x " + prodName + " (" + formatNumber(prodPrice) + " Gs)";
        sendMessage(msg);
    }

    function updateCartBar() {
        if (currentCart.length === 0) {
            $('#floating_cart_bar').hide();
            $('#floating_pwa_pill').css('bottom', '108px');
            return;
        }
        let totalItems = 0;
        let subtotal = 0;
        currentCart.forEach(i => {
            totalItems += i.qty;
            subtotal += (i.qty * i.price);
        });

        $('#cart_summary_text').text(totalItems + (totalItems === 1 ? ' plato • ' : ' platos • ') + formatNumber(subtotal) + ' Gs');
        $('#floating_cart_bar').css('display', 'flex');
        $('#floating_pwa_pill').css('bottom', '155px');
    }

    // Modal de Checkout / Cierre de Pedido
    function openCheckoutModal() {
        if (currentCart.length === 0) {
            alert('Aún no has seleccionado ningún plato. Pulsa en Menú para agregar.');
            return;
        }

        // Renderizar items
        let html = '';
        let subtotal = 0;
        currentCart.forEach(function(it, idx) {
            let itemTotal = it.qty * it.price;
            subtotal += itemTotal;
            html += '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; font-size:12.5px;">' +
                '<span><strong>' + it.qty + 'x</strong> ' + it.name + '</span>' +
                '<span>' + formatNumber(itemTotal) + ' Gs ' +
                '<button type="button" onclick="removeCartItem(' + idx + ')" style="background:none; border:none; color:#ef4444; margin-left:6px; cursor:pointer;"><i class="fa fa-trash-o"></i></button>' +
                '</span>' +
            '</div>';
        });

        $('#checkout_items_list').html(html);
        $('#checkout_subtotal_text').text(formatNumber(subtotal) + ' Gs');
        $('#checkout_shipping_text').text(formatNumber(currentShippingFee) + ' Gs');
        $('#checkout_km_text').text(currentDeliveryKm ? currentDeliveryKm.toFixed(1) + ' km' : 'según zona');
        $('#checkout_total_text').text(formatNumber(subtotal + currentShippingFee) + ' Gs');

        $('#modal_checkout').css('display', 'flex');
        recalcVuelto();
    }

    function removeCartItem(idx) {
        currentCart.splice(idx, 1);
        updateCartBar();
        openCheckoutModal();
    }

    function closeCheckoutModal() {
        $('#modal_checkout').hide();
    }

    function toggleDeliveryType(type) {
        if (type === 'takeaway') {
            currentShippingFee = 0;
            $('#group_delivery_address').hide();
        } else {
            $('#group_delivery_address').show();
            // recalcular shipping con distancia
            if (currentDeliveryKm) {
                const fee = calculateDeliveryFee(currentDeliveryKm);
                currentShippingFee = fee.entregable ? fee.costo : 0;
            }
        }
        let subtotal = currentCart.reduce((acc, it) => acc + (it.qty * it.price), 0);
        $('#checkout_shipping_text').text(formatNumber(currentShippingFee) + ' Gs');
        $('#checkout_total_text').text(formatNumber(subtotal + currentShippingFee) + ' Gs');
        recalcVuelto();
    }

    function togglePaymentMethod(method) {
        if (method === 'efectivo') {
            $('#panel_vuelto_calc').show();
            $('#panel_sipap_info').hide();
            $('#panel_qr_info').hide();
        } else if (method === 'transferencia') {
            $('#panel_vuelto_calc').hide();
            $('#panel_sipap_info').show();
            $('#panel_qr_info').hide();
        } else {
            $('#panel_vuelto_calc').hide();
            $('#panel_sipap_info').hide();
            $('#panel_qr_info').show();
        }
    }

    function setCashAmount(amt) {
        let subtotal = currentCart.reduce((acc, it) => acc + (it.qty * it.price), 0);
        let total = subtotal + currentShippingFee;
        if (amt === 'exacto') {
            $('#order_cash_paga_con').val(total);
        } else {
            $('#order_cash_paga_con').val(amt);
        }
        recalcVuelto();
    }

    function recalcVuelto() {
        let subtotal = currentCart.reduce((acc, it) => acc + (it.qty * it.price), 0);
        let total = subtotal + currentShippingFee;
        let pagaCon = parseFloat($('#order_cash_paga_con').val()) || 0;
        if (pagaCon > total) {
            let vuelto = pagaCon - total;
            $('#vuelto_result_label').html('✓ Vuelto a preparar: <strong>' + formatNumber(vuelto) + ' Gs</strong>');
        } else if (pagaCon > 0 && pagaCon === total) {
            $('#vuelto_result_label').html('✓ Monto exacto (sin vuelto)');
        } else if (pagaCon > 0 && pagaCon < total) {
            $('#vuelto_result_label').html('<span style="color:#ef4444;">⚠️ El monto es menor al total (' + formatNumber(total) + ' Gs)</span>');
        } else {
            $('#vuelto_result_label').text('');
        }
    }

    // Confirmación final del pedido
    function submitFinalOrder() {
        const custName = $('#order_cust_name').val().trim() || 'Cliente Web';
        const custPhone = $('#order_cust_phone').val().trim();
        const delType = $('input[name="checkout_delivery_type"]:checked').val() || 'delivery';
        const address = $('#order_cust_address').val().trim();
        const pMethod = $('input[name="order_payment_method"]:checked').val() || 'efectivo';
        const pagaCon = parseFloat($('#order_cash_paga_con').val()) || 0;
        const notes = $('#order_notes').val().trim();

        if (!custPhone) {
            alert('Por favor ingresa tu número de WhatsApp / teléfono para que el delivery pueda avisarte.');
            $('#order_cust_phone').focus();
            return;
        }

        // Guardar en memoria de cliente
        localStorage.setItem('geekspos_cust_name', custName);
        localStorage.setItem('geekspos_cust_phone', custPhone);
        if (address) localStorage.setItem('geekspos_cust_address', address);

        let subtotal = currentCart.reduce((acc, it) => acc + (it.qty * it.price), 0);
        let total = subtotal + currentShippingFee;
        let vuelto = (pMethod === 'efectivo' && pagaCon > total) ? (pagaCon - total) : 0;

        $('#btn_submit_order').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Registrando Pedido...');

        const payload = {
            loc_id: LOC_ID,
            customer_name: custName,
            customer_phone: custPhone,
            delivery_type: delType,
            delivery_address: address,
            lat: selectedDeliveryLat,
            lng: selectedDeliveryLng,
            payment_method: pMethod,
            paga_con: pagaCon,
            vuelto: vuelto,
            notes: notes,
            items: currentCart
        };

        if (uploadedReceiptData && uploadedReceiptData.file_url) {
            payload.comprobante_url = uploadedReceiptData.file_url;
            payload.comprobante_nro = (uploadedReceiptData.datos && uploadedReceiptData.datos.nro) ? uploadedReceiptData.datos.nro : '';
            payload.comprobante_monto = (uploadedReceiptData.datos && uploadedReceiptData.datos.monto) ? uploadedReceiptData.datos.monto : 0;
            payload.comprobante_valido = uploadedReceiptData.valido ? 1 : 0;
        }

        $.ajax({
            url: CONFIRM_ORDER_URL,
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function(resp) {
                $('#btn_submit_order').prop('disabled', false).html('<i class="fa fa-check-circle"></i> Confirmar Pedido Ahora');
                closeCheckoutModal();

                if (resp && resp.status === 'success') {
                    // Limpiar carrito
                    currentCart = [];
                    updateCartBar();
                    activeInvoiceId = resp.invoice_id;

                    // Renderizar tarjeta de ticket y seguimiento GPS en vivo
                    let trackingBtnHtml = '';
                    if (resp.tracking && resp.tracking.tracking_url) {
                        trackingBtnHtml = '<a href="' + resp.tracking.tracking_url + '" target="_blank" class="btn-live-gps">' +
                            '<i class="fa fa-motorcycle mr-1"></i> Seguir a mi Repartidor en Vivo (GPS)' +
                        '</a>';
                    }

                    let receiptActionHtml = '';
                    if (pMethod === 'transferencia') {
                        if (uploadedReceiptData && uploadedReceiptData.valido) {
                            receiptActionHtml = '<div style="margin-top:8px; padding:6px 10px; background:#dcfce7; border-radius:6px; font-size:11.5px; color:#15803d; font-weight:700;">' +
                                '<i class="fa fa-check-circle mr-1"></i> Comprobante verificado con IA (' + (uploadedReceiptData.datos ? uploadedReceiptData.datos.monto_fmt : '') + ')' +
                            '</div>';
                        } else {
                            receiptActionHtml = '<div style="margin-top:8px;">' +
                                '<button type="button" onclick="$(\'#file_receipt_input\').click()" style="background:#0284c7; color:#ffffff; border:none; border-radius:6px; width:100%; font-weight:700; font-size:12px; padding:8px; cursor:pointer;">' +
                                    '<i class="fa fa-paperclip mr-1"></i> Adjuntar Comprobante de Transferencia' +
                                '</button>' +
                            '</div>';
                        }
                    }

                    let pMethodLabel = pMethod === 'efectivo' ? 'Efectivo' : (pMethod === 'transferencia' ? 'Transferencia SIPAP' : 'QR / Tarjeta');
                    let vueltoHtml = (pMethod === 'efectivo' && pagaCon > 0) ? '<br><small>Paga con: ' + formatNumber(pagaCon) + ' Gs (Vuelto: ' + formatNumber(vuelto) + ' Gs)</small>' : '';

                    let ticketHtml = '<div class="order-ticket-card">' +
                        '<div class="order-ticket-header">' +
                            '<div>' +
                                '<strong style="color:var(--wa-teal); font-size:14px;"><i class="fa fa-check-circle mr-1"></i> ¡Pedido Confirmado!</strong>' +
                                '<div style="font-size:11px; color:#64748b;">' + resp.order_number + ' • ' + STORE_NAME + '</div>' +
                            '</div>' +
                            '<span class="badge badge-success" style="background:#22c55e; color:#ffffff; font-size:11px; padding:4px 8px; border-radius:6px;">En Cocina</span>' +
                        '</div>' +
                        '<div class="tracking-status-bar">' +
                            '<div class="tracking-step active">1. Recibido</div>' +
                            '<div class="tracking-step active">2. En Cocina</div>' +
                            '<div class="tracking-step">3. En Camino 🛵</div>' +
                            '<div class="tracking-step">4. Entregado</div>' +
                        '</div>' +
                        '<div style="font-size:12.5px; color:#334155; line-height:1.5;">' +
                            '<strong>Cliente:</strong> ' + custName + ' (' + custPhone + ')<br>' +
                            '<strong>Modalidad:</strong> ' + (delType === 'delivery' ? '🛵 Envío a Domicilio' : '🏪 Retiro en Local') + '<br>' +
                            '<strong>Pago:</strong> ' + pMethodLabel + vueltoHtml + '<br>' +
                            '<strong style="font-size:14px; color:#059669;">Total: ' + resp.total_fmt + '</strong>' +
                        '</div>' +
                        receiptActionHtml +
                        trackingBtnHtml +
                        '<div style="margin-top:8px; text-align:center;">' +
                            '<a href="https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent('Hola, acabo de confirmar el pedido ' + resp.order_number + ' en la web. Mi nombre es ' + custName) + '" target="_blank" style="color:var(--wa-teal); font-weight:700; font-size:12px; text-decoration:none;">' +
                                '<i class="fa fa-whatsapp mr-1"></i> Abrir pedido en WhatsApp' +
                            '</a>' +
                        '</div>' +
                    '</div>';

                    appendBubble("✅ ¡Tu pedido **" + resp.order_number + "** ha sido registrado con éxito y ya está en la pantalla de cocina!", 'bot');
                    
                    // Inyectar tarjeta interactiva en el chat
                    const row = document.createElement('div');
                    row.className = 'message-row bot';
                    row.innerHTML = '<div class="bubble" style="max-width:92%;"><div class="bubble-text">' + ticketHtml + '</div><div class="bubble-meta"><span>' + getCurrentTime() + '</span></div></div>';
                    const indicator = document.getElementById('typing_indicator');
                    indicator.parentNode.insertBefore(row, indicator);
                    scrollToBottom();
                } else {
                    alert('Hubo un inconveniente al confirmar: ' + (resp.message || 'Intente de nuevo.'));
                }
            },
            error: function() {
                $('#btn_submit_order').prop('disabled', false).html('<i class="fa fa-check-circle"></i> Confirmar Pedido Ahora');
                alert('Demora en la conexión. Puedes enviar tu pedido directamente por WhatsApp pulsando el botón superior.');
            }
        });
    }

    // Utilizar dirección guardada con 1 toque
    function useSavedAddress() {
        if (savedLat && savedLng) {
            selectedDeliveryLat = savedLat;
            selectedDeliveryLng = savedLng;
            const km = calculateHaversineKm(STORE_LAT, STORE_LNG, savedLat, savedLng);
            const fee = calculateDeliveryFee(km);
            currentDeliveryKm = km;
            currentShippingFee = fee.entregable ? fee.costo : 0;
            updateCartBar();

            sendMessage("📍 Usar mi dirección guardada: " + savedAddress + " (Distancia: " + km.toFixed(1) + " km • Envío: " + formatNumber(currentShippingFee) + " Gs)", { lat: savedLat, lng: savedLng });
        }
    }

    // Tarjeta rápida de Formas de Pago y Alias en el chat
    function openPaymentInfoCard() {
        let msg = "💳 *Formas de Pago Aceptadas en " + STORE_NAME + "*:\n\n" +
            "💵 *1. Efectivo al recibir:* Puedes pedir vuelto exacto.\n\n" +
            "🏦 *2. Transferencia Bancaria SIPAP:*\n" +
            "• Banco: " + BANCO_INFO.nombre + "\n" +
            "• Titular: " + BANCO_INFO.titular + "\n" +
            (BANCO_INFO.ruc_ci ? "• RUC / CI: " + BANCO_INFO.ruc_ci + "\n" : "") +
            (BANCO_INFO.alias ? "• Alias: *" + BANCO_INFO.alias + "*\n" : "") +
            "\n📱 *3. QR / Tarjetas:* Cobro con POS en puerta.";

        appendBubble(msg, 'bot');
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('Copiado al portapapeles: ' + text);
    }

    // =========================================================================
    // CARGA Y ANÁLISIS DE COMPROBANTES DE PAGO CON IA (ESTILO WHATSAPP)
    // =========================================================================
    function handleReceiptFileSelect(file, fromCheckoutModal = false) {
        if (!file) return;

        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

        if (!validTypes.includes(file.type) && !file.name.match(/\.(jpg|jpeg|png|webp|pdf)$/i)) {
            alert('Por favor selecciona una imagen (JPG, PNG, WEBP) o un documento PDF del comprobante.');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            alert('El archivo supera el tamaño máximo permitido (10 MB).');
            return;
        }

        let subtotal = currentCart.reduce((acc, it) => acc + (it.qty * it.price), 0);
        let expectedTotal = subtotal + currentShippingFee;

        // Actualizar UI del modal si se subió desde checkout
        if (fromCheckoutModal) {
            $('#checkout_receipt_dropzone').hide();
            $('#checkout_receipt_status').show().html(
                '<div style="display:flex; align-items:center; gap:8px; color:#0284c7; padding:6px;">' +
                    '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
                    '<div>' +
                        '<strong>Subiendo y analizando con IA...</strong><br>' +
                        '<span style="font-size:11px; color:#64748b;">Verificando titular, monto y operación</span>' +
                    '</div>' +
                '</div>'
            );
        }

        // Mostrar burbuja del archivo en el chat
        const localPreviewUrl = !isPdf ? URL.createObjectURL(file) : '';
        let userBubbleHtml = '<div style="font-weight:700; margin-bottom:4px; font-size:12.5px;"><i class="fa fa-paperclip mr-1"></i> Comprobante adjuntado:</div>';
        if (!isPdf && localPreviewUrl) {
            userBubbleHtml += '<div style="max-width:220px; max-height:160px; overflow:hidden; border-radius:8px; margin-bottom:4px; box-shadow:0 1px 3px rgba(0,0,0,0.2);">' +
                '<img src="' + localPreviewUrl + '" style="width:100%; height:auto; display:block; object-fit:cover;">' +
            '</div>';
        } else {
            userBubbleHtml += '<div style="background:#f1f5f9; padding:6px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:8px; font-size:12px; margin-bottom:4px;">' +
                '<i class="fa fa-file-pdf-o text-danger" style="font-size:18px;"></i>' +
                '<span>' + file.name + '</span>' +
            '</div>';
        }

        appendCustomBubble(userBubbleHtml, 'user');
        $('#typing_indicator').show();
        scrollToBottom();

        const formData = new FormData();
        formData.append('file', file);
        formData.append('loc_id', LOC_ID);
        formData.append('session_id', sessionId);
        formData.append('phone', customerPhone);
        formData.append('name', customerName);
        formData.append('expected_total', expectedTotal);
        if (activeInvoiceId) {
            formData.append('invoice_id', activeInvoiceId);
        }

        $.ajax({
            url: UPLOAD_COMPROBANTE_URL,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 35000,
            success: function(resp) {
                $('#typing_indicator').hide();

                if (resp && resp.status === 'success') {
                    uploadedReceiptData = resp;

                    if (fromCheckoutModal) {
                        let statusIcon = resp.valido ? 'fa-check-circle' : 'fa-info-circle';
                        let statusColor = resp.valido ? '#059669' : '#d97706';
                        let statusTitle = resp.valido ? 'Comprobante Verificado con IA' : 'Comprobante Recibido';

                        let previewHtml = '<div style="display:flex; justify-content:space-between; align-items:center; background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:6px 10px;">' +
                            '<div>' +
                                '<strong style="color:' + statusColor + '; font-size:12px;"><i class="fa ' + statusIcon + ' mr-1"></i> ' + statusTitle + '</strong>' +
                                '<div style="font-size:11px; color:#475569; margin-top:2px;">' +
                                    'Monto: <strong>' + (resp.datos && resp.datos.monto_fmt ? resp.datos.monto_fmt : 'Legible') + '</strong> • Op: #' + (resp.datos && resp.datos.nro ? resp.datos.nro : 'S/N') +
                                '</div>' +
                            '</div>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeUploadedReceipt()" style="font-size:11px; padding:2px 6px;">Cambiar</button>' +
                        '</div>';

                        if (resp.motivo) {
                            previewHtml += '<div style="margin-top:4px; font-size:11px; color:#d97706; font-weight:600;"><i class="fa fa-info-circle mr-1"></i> ' + resp.motivo + '</div>';
                        }

                        $('#checkout_receipt_status').html(previewHtml);
                    }

                    // Tarjeta interactiva en el chat estilo WhatsApp
                    let cardHtml = '<div class="receipt-card-bubble">' +
                        '<div class="receipt-card-header">' +
                            '<div class="receipt-badge ' + (resp.valido ? 'badge-ok' : 'badge-warn') + '">' +
                                '<i class="fa ' + (resp.valido ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i> ' +
                                (resp.valido ? 'Comprobante Verificado' : 'Revisión en Caja') +
                            '</div>' +
                            '<span style="font-size:11px; color:#64748b; font-weight:600;">' + (resp.datos.banco || 'SIPAP') + '</span>' +
                        '</div>' +
                        '<div class="receipt-card-body">' +
                            '<div class="receipt-row"><span>Monto Detectado:</span><strong style="color:#059669; font-size:13.5px;">' + (resp.datos.monto_fmt || (resp.datos.monto ? formatNumber(resp.datos.monto) + ' Gs' : 'No legible')) + '</strong></div>' +
                            '<div class="receipt-row"><span>Destinatario:</span><span>' + (resp.datos.destinatario || BANCO_INFO.titular) + ' ' + (resp.destino_ok ? '<i class="fa fa-check text-success" title="Coincide con el local"></i>' : '<i class="fa fa-exclamation text-warning"></i>') + '</span></div>' +
                            (resp.datos.nro ? '<div class="receipt-row"><span>Nro. Operación:</span><span>#' + resp.datos.nro + '</span></div>' : '') +
                            '<div class="receipt-row"><span>Fecha:</span><span>' + (resp.datos.fecha || 'Hoy') + '</span></div>' +
                            (resp.motivo ? '<div class="receipt-warning-box"><i class="fa fa-info-circle"></i> ' + resp.motivo + '</div>' : '') +
                        '</div>' +
                        '<div class="receipt-card-footer">' +
                            '<a href="' + resp.file_url + '" target="_blank" class="btn-view-receipt"><i class="fa fa-eye mr-1"></i> Ver Comprobante Original</a>' +
                        '</div>' +
                    '</div>';

                    appendCustomBubble(cardHtml, 'bot');

                    if (activeInvoiceId && resp.valido) {
                        appendBubble("✅ ¡Excelente! Tu comprobante fue anexado a la orden **#PED-" + activeInvoiceId + "** y notificado a la cocina.", 'bot');
                    }
                } else {
                    if (fromCheckoutModal) {
                        $('#checkout_receipt_dropzone').show();
                        $('#checkout_receipt_status').hide();
                    }
                    appendBubble("⚠️ No se pudo procesar el comprobante: " + (resp.message || 'Intente de nuevo.'), 'bot');
                }
            },
            error: function() {
                $('#typing_indicator').hide();
                if (fromCheckoutModal) {
                    $('#checkout_receipt_dropzone').show();
                    $('#checkout_receipt_status').hide();
                }
                appendBubble("Tuvimos una demora subiendo el archivo. Si lo prefieres, puedes enviarlo directamente por WhatsApp tocando el botón verde superior.", 'bot');
            }
        });
    }

    function removeUploadedReceipt() {
        uploadedReceiptData = null;
        $('#checkout_receipt_dropzone').show();
        $('#checkout_receipt_status').hide().html('');
        $('#checkout_file_receipt').val('');
        $('#file_receipt_input').val('');
    }

    // Soporte para pegar capturas de pantalla (Ctrl+V)
    document.addEventListener('paste', function(e) {
        if (e.clipboardData && e.clipboardData.files && e.clipboardData.files.length > 0) {
            const f = e.clipboardData.files[0];
            if (f.type.startsWith('image/') || f.type === 'application/pdf') {
                handleReceiptFileSelect(f, false);
            }
        }
    });

    // Chat Message Sender
    function sendMessage(overrideText = null, locationData = null) {
        const input = document.getElementById('chat_input');
        const text = overrideText !== null ? overrideText.trim() : input.value.trim();

        if (!text && !locationData) return;

        if (overrideText === null) {
            input.value = '';
        }

        appendBubble(text, 'user');
        $('#typing_indicator').show();
        scrollToBottom();

        const postData = {
            message: text,
            session_id: sessionId,
            phone: customerPhone,
            name: customerName,
            loc_id: LOC_ID
        };

        if (locationData) {
            postData.lat = locationData.lat;
            postData.lng = locationData.lng;
        }

        $.ajax({
            url: CHAT_API_URL,
            type: 'POST',
            data: postData,
            dataType: 'json',
            timeout: 25000,
            success: function(resp) {
                $('#typing_indicator').hide();
                if (resp && resp.reply) {
                    appendBubble(resp.reply, 'bot');
                } else {
                    appendBubble("Recibimos tu solicitud. Nuestro equipo te responderá en instantes.", 'bot');
                }
            },
            error: function() {
                $('#typing_indicator').hide();
                appendBubble("Tuvimos una pequeña demora conectando con el servidor. Si deseas enviar tu pedido ya mismo, pulsa arriba en 'WhatsApp'.", 'bot');
            }
        });
    }

    function sendQuickMessage(text) {
        sendMessage(text);
    }

    function toggleMenuDrawer() {
        const d = $('#menu_drawer');
        if (d.css('display') === 'none' || d.css('display') === '') {
            d.css('display', 'flex');
        } else {
            d.hide();
        }
    }

    // =========================================================================
    // MAPA SELECTOR DE UBICACIÓN LEAFLET
    // =========================================================================
    let mapInstance = null;
    let selectedLat = STORE_LAT;
    let selectedLng = STORE_LNG;
    let currentFeeCalc = null;

    function openMapPicker() {
        $('#modal_map_picker').css('display', 'flex');

        setTimeout(function() {
            if (!mapInstance) {
                mapInstance = L.map('leaflet_map', {
                    center: [selectedDeliveryLat || STORE_LAT, selectedDeliveryLng || STORE_LNG],
                    zoom: 14,
                    zoomControl: true
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap',
                    maxZoom: 19
                }).addTo(mapInstance);

                const storeIcon = L.divIcon({
                    className: 'store-map-icon',
                    html: '<div style="background:#075e54; color:#ffffff; border-radius:50%; width:30px; height:30px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,0.3); font-size:13px; border:2px solid #ffffff;"><i class="fa fa-home"></i></div>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
                L.marker([STORE_LAT, STORE_LNG], { icon: storeIcon }).addTo(mapInstance)
                    .bindPopup('<b>' + STORE_NAME + '</b><br>Nuestro local')
                    .openPopup();

                mapInstance.on('move', function() { updateMapCenterCalc(); });
                mapInstance.on('moveend', function() { updateMapCenterCalc(); });

                if (!savedLat && navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(pos) {
                        mapInstance.flyTo([pos.coords.latitude, pos.coords.longitude], 16);
                    }, function() {});
                }
            } else {
                mapInstance.invalidateSize();
            }

            updateMapCenterCalc();
        }, 220);
    }

    function closeMapPicker() {
        $('#modal_map_picker').hide();
    }

    function centerOnUserGps() {
        if (!navigator.geolocation) {
            alert('Geolocalización no disponible.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function(pos) {
            if (mapInstance) {
                mapInstance.flyTo([pos.coords.latitude, pos.coords.longitude], 17);
            }
        }, function(err) {
            alert('No pudimos acceder a tu GPS. Puedes mover el mapa manualmente.');
        }, { enableHighAccuracy: true });
    }

    function updateMapCenterCalc() {
        if (!mapInstance) return;
        const center = mapInstance.getCenter();
        selectedLat = center.lat;
        selectedLng = center.lng;

        const km = calculateHaversineKm(STORE_LAT, STORE_LNG, selectedLat, selectedLng);
        currentFeeCalc = calculateDeliveryFee(km);

        $('#picker_dist_label').text(km.toFixed(1) + ' km');

        if (currentFeeCalc.entregable) {
            $('#picker_fee_label').css('color', '#059669').text(formatNumber(currentFeeCalc.costo) + ' Gs');
            $('#picker_zone_badge').html('<span style="background:#dcfce7; color:#15803d; font-weight:700; font-size:11.5px; padding:3px 8px; border-radius:6px; display:inline-block;"><i class="fa fa-check mr-1"></i> ' + currentFeeCalc.label + '</span>');
            $('#btn_confirm_map_location').prop('disabled', false).css('background', 'var(--wa-teal)').html('<i class="fa fa-check-circle mr-1"></i> Confirmar esta Ubicación (' + formatNumber(currentFeeCalc.costo) + ' Gs)');
        } else {
            $('#picker_fee_label').css('color', '#dc2626').text('Fuera de Cobertura');
            $('#picker_zone_badge').html('<span style="background:#fee2e2; color:#b91c1c; font-weight:700; font-size:11.5px; padding:3px 8px; border-radius:6px; display:inline-block;"><i class="fa fa-exclamation-triangle mr-1"></i> Supera radio máx (' + MAX_RADIO_KM + ' km)</span>');
            $('#btn_confirm_map_location').prop('disabled', false).css('background', '#f59e0b').html('<i class="fa fa-shopping-bag mr-1"></i> Pedir con Retiro en Local');
        }
    }

    function calculateHaversineKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return parseFloat((R * c * 1.3).toFixed(1));
    }

    function calculateDeliveryFee(km) {
        if (km > MAX_RADIO_KM) {
            return { entregable: false, costo: null, label: 'Fuera de zona' };
        }
        if (Array.isArray(TRAMOS_LIST) && TRAMOS_LIST.length > 0) {
            for (let tr of TRAMOS_LIST) {
                const d = parseFloat(tr.desde) || 0;
                const h = parseFloat(tr.hasta) || 0;
                if (km >= d && km <= h) {
                    return { entregable: true, costo: parseInt(tr.costo) || 0, label: tr.etiqueta || ('Tramo ' + d + ' a ' + h + ' km') };
                }
            }
        }
        let costo = km <= 1.5 ? 5000 : (km <= 4 ? 10000 : (km <= 7 ? 15000 : (km <= 10 ? 20000 : 25000)));
        return { entregable: true, costo: costo, label: 'Tarifa estándar' };
    }

    function formatNumber(num) {
        return (num || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function confirmSelectedLocation() {
        closeMapPicker();
        selectedDeliveryLat = selectedLat;
        selectedDeliveryLng = selectedLng;
        const km = calculateHaversineKm(STORE_LAT, STORE_LNG, selectedLat, selectedLng);
        const fee = currentFeeCalc;
        currentDeliveryKm = km;
        currentShippingFee = fee.entregable ? fee.costo : 0;
        updateCartBar();

        localStorage.setItem('geekspos_cust_lat', selectedLat);
        localStorage.setItem('geekspos_cust_lng', selectedLng);

        const mapUrl = "https://maps.google.com/?q=" + selectedLat.toFixed(6) + "," + selectedLng.toFixed(6);

        let msg = "📍 Ubicación de entrega seleccionada: " + mapUrl;
        if (fee && fee.entregable) {
            msg += "\n(Distancia: " + km.toFixed(1) + " km • Costo de Envío: " + formatNumber(fee.costo) + " Gs • " + fee.label + ")";
        } else {
            msg += "\n(Distancia: " + km.toFixed(1) + " km • Fuera de radio de delivery, disponible para retiro en local)";
        }

        sendMessage(msg, { lat: selectedLat, lng: selectedLng });
    }

    // Web Speech Recognition
    let recognition = null;
    let isRecording = false;

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRec();
        recognition.lang = 'es-PY';
        recognition.continuous = false;
        recognition.interimResults = false;

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            $('#chat_input').val(transcript);
            sendMessage();
        };

        recognition.onend = function() {
            isRecording = false;
            $('#btn_voice').css('color', '#54656f');
        };

        recognition.onerror = function() {
            isRecording = false;
            $('#btn_voice').css('color', '#54656f');
        };
    }

    function toggleVoiceRecording() {
        if (!recognition) {
            alert('Dictado por voz no compatible con este navegador.');
            return;
        }
        if (isRecording) {
            recognition.stop();
            isRecording = false;
            $('#btn_voice').css('color', '#54656f');
        } else {
            recognition.start();
            isRecording = true;
            $('#btn_voice').css('color', '#ef4444');
        }
    }

    // Modal de Teléfono / Perfil (Identificación del Cliente)
    function openPhoneModal() {
        $('#cust_name').val(customerName || '');
        $('#cust_phone').val(customerPhone || '');
        $('#cust_data_error').hide();
        $('#modal_phone').css('display', 'flex');
        setTimeout(function() {
            if (!$('#cust_name').val()) {
                $('#cust_name').focus();
            } else {
                $('#cust_phone').focus();
            }
        }, 150);
    }

    function closePhoneModal() {
        $('#modal_phone').hide();
    }

    function saveCustomerData() {
        const nameVal = $('#cust_name').val().trim();
        const phoneVal = $('#cust_phone').val().trim();

        if (!nameVal) {
            $('#cust_data_error').html('<i class="fa fa-exclamation-circle mr-1"></i> Por favor ingresá tu nombre para poder identificarte.').show();
            $('#cust_name').focus();
            return;
        }

        const cleanDigits = phoneVal.replace(/\D/g, '');
        if (!phoneVal || cleanDigits.length < 6) {
            $('#cust_data_error').html('<i class="fa fa-exclamation-circle mr-1"></i> Por favor ingresá un número de WhatsApp o celular válido.').show();
            $('#cust_phone').focus();
            return;
        }

        $('#cust_data_error').hide();

        customerName = nameVal;
        customerPhone = phoneVal;

        localStorage.setItem('geekspos_cust_name', customerName);
        localStorage.setItem('geekspos_cust_phone', customerPhone);

        $('#order_cust_name').val(customerName);
        $('#order_cust_phone').val(customerPhone);

        closePhoneModal();

        // Actualizar saludo de bienvenida con el nombre del cliente
        const greeting = "¡Hola, **" + customerName + "**! 🍔 Qué gusto tenerte en **" + STORE_NAME + "**.\n\n" +
            (savedAddress ? "¿Te enviamos a tu dirección habitual (**" + savedAddress + "**)? " : "¿Qué se te antoja pedir hoy?") +
            " Tocá en **'Ver Menú'** para ver la carta o elegí tu ubicación en el mapa.";
        $('#welcome_text').html(greeting.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>'));

        appendBubble("✅ ¡Datos guardados! Hola **" + customerName + "** (" + customerPhone + "). Ya podés escribirnos lo que desees o ver la carta completa.", 'bot');
    }

    $('#cust_name, #cust_phone').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (this.id === 'cust_name' && !$('#cust_phone').val().trim()) {
                $('#cust_phone').focus();
            } else {
                saveCustomerData();
            }
        }
    });

    // ==========================================
    // GESTIÓN COMPLETA DE PWA (APP INSTALABLE)
    // ==========================================
    let deferredPrompt = null;
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent.toLowerCase());
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    // 1. Registro del Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('<?= base_url("sw.js") ?>', { scope: '/' })
                .then(function(reg) {
                    console.log('[PWA] ServiceWorker registrado con éxito con scope:', reg.scope);
                })
                .catch(function(err) {
                    console.warn('[PWA] ServiceWorker no pudo registrarse:', err);
                });
        });
    }

    // 2. Evento beforeinstallprompt (Android / Chrome / Edge)
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        console.log('[PWA] beforeinstallprompt listo');
        if (!isStandalone) {
            if (!sessionStorage.getItem('pwa_banner_dismissed')) {
                $('#pwa_install_banner').slideDown(200);
            }
            if (!sessionStorage.getItem('pwa_floating_dismissed')) {
                $('#floating_pwa_pill').fadeIn(200);
            }
            $('#btn_header_pwa').show();
            $('#chip_install_pwa').show();
        }
    });

    // 3. Evento appinstalled (disparado cuando el usuario termina de instalar la PWA)
    window.addEventListener('appinstalled', (evt) => {
        console.log('[PWA] App instalada con éxito en el dispositivo');
        $('#pwa_install_banner').slideUp(200);
        $('#floating_pwa_pill').fadeOut(200);
        $('#btn_header_pwa').hide();
        $('#chip_install_pwa').hide();
        appendBubble("🎉 ¡Excelente! La aplicación de **" + STORE_NAME + "** se instaló en tu pantalla de inicio. Ya podés abrirla en cualquier momento como una app nativa.", 'bot');
    });

    // 4. Acción principal al pulsar cualquier botón de instalar
    function installPwaApp() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('[PWA] Usuario aceptó la instalación');
                    $('#pwa_install_banner').slideUp(200);
                    $('#floating_pwa_pill').fadeOut(200);
                    $('#btn_header_pwa').hide();
                    $('#chip_install_pwa').hide();
                }
                deferredPrompt = null;
            });
        } else if (isIos) {
            openPwaIosModal();
        } else {
            openPwaGenericModal();
        }
    }

    function dismissPwaBanner() {
        $('#pwa_install_banner').slideUp(200);
        sessionStorage.setItem('pwa_banner_dismissed', '1');
    }

    function dismissFloatingPwa() {
        $('#floating_pwa_pill').fadeOut(200);
        sessionStorage.setItem('pwa_floating_dismissed', '1');
    }

    function openPwaIosModal() {
        $('#modal_pwa_ios').css('display', 'flex');
    }

    function closePwaIosModal() {
        $('#modal_pwa_ios').hide();
    }

    function openPwaGenericModal() {
        $('#modal_pwa_generic').css('display', 'flex');
    }

    function closePwaGenericModal() {
        $('#modal_pwa_generic').hide();
    }

    $('#chat_input').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    $(document).ready(function() {
        scrollToBottom();

        // Control de Visibilidad PWA
        if (isStandalone) {
            // Ya está instalada y corriendo como App independiente
            $('#pwa_install_banner').hide();
            $('#floating_pwa_pill').hide();
            $('#btn_header_pwa').hide();
            $('#chip_install_pwa').hide();
        } else {
            // Mostrar elementos PWA de forma proactiva
            if (!sessionStorage.getItem('pwa_banner_dismissed')) {
                $('#pwa_install_banner').css('display', 'flex');
            } else {
                $('#pwa_install_banner').hide();
            }

            if (!sessionStorage.getItem('pwa_floating_dismissed')) {
                $('#floating_pwa_pill').css('display', 'flex');
            } else {
                $('#floating_pwa_pill').hide();
            }

            $('#btn_header_pwa').show();
            $('#chip_install_pwa').show();
        }

        // 1. Pedir automáticamente Nombre y Celular al entrar si aún no están guardados
        if (!customerName || !customerPhone) {
            setTimeout(function() {
                openPhoneModal();
            }, 350);
        } else {
            // Saludo personalizado con reconocimiento automático si ya es cliente recurrente
            const greeting = "¡Hola de nuevo, **" + customerName + "**! 🍔 Qué gusto tenerte en **" + STORE_NAME + "**.\n\n" +
                (savedAddress ? "¿Te enviamos a tu dirección habitual (**" + savedAddress + "**)? " : "¿Qué se te antoja pedir hoy?") +
                " Tocá en **'Ver Menú'** para ver la carta o elegí tu ubicación en el mapa.";
            $('#welcome_text').html(greeting.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>'));
        }
    });
</script>
</body>
</html>
