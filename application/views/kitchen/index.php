<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= assets_url() ?>app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/css/font-awesome/css/font-awesome.min.css">
    
    <style>
        :root {
            --kds-bg: #0d1117;
            --kds-surface: #161b22;
            --kds-border: #30363d;
            --kds-card-bg: #1c2128;
            --kds-text: #f0f6fc;
            --kds-text-muted: #8b949e;
            --kds-accent: #388bfd;
            --kds-green: #2ea043;
            --kds-green-glow: rgba(46, 160, 67, 0.35);
            --kds-yellow: #d29922;
            --kds-yellow-glow: rgba(210, 153, 34, 0.35);
            --kds-red: #f85149;
            --kds-red-glow: rgba(248, 81, 73, 0.45);
            --kds-purple: #a371f7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--kds-bg);
            color: var(--kds-text);
            min-height: 100vh;
            overflow-x: hidden;
            user-select: none;
        }

        /* ----- TOP NAVBAR ----- */
        .kds-navbar {
            background-color: var(--kds-surface);
            border-bottom: 2px solid var(--kds-border);
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 12px;
            flex-wrap: wrap;
        }

        .kds-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kds-brand-logo {
            background: linear-gradient(135deg, #f0883e, #f85149);
            color: #fff;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 12px rgba(240, 136, 62, 0.35);
        }

        .kds-brand-info h1 {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #fff;
            line-height: 1.1;
        }

        .kds-brand-info span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--kds-text-muted);
            font-weight: 600;
        }

        /* Stats counters */
        .kds-stats-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #0d1117;
            padding: 6px 10px;
            border-radius: 12px;
            border: 1px solid var(--kds-border);
        }

        .kds-stat-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }

        .kds-stat-pill.stat-pending {
            background: rgba(210, 153, 34, 0.2);
            color: #e3b341;
            border: 1px solid rgba(210, 153, 34, 0.4);
        }

        .kds-stat-pill.stat-cooking {
            background: rgba(240, 136, 62, 0.2);
            color: #f0883e;
            border: 1px solid rgba(240, 136, 62, 0.4);
        }

        .kds-stat-pill.stat-ready {
            background: rgba(46, 160, 67, 0.2);
            color: #56d364;
            border: 1px solid rgba(46, 160, 67, 0.4);
        }

        .kds-clock {
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            background: #21262d;
            padding: 6px 12px;
            border-radius: 8px;
            letter-spacing: 1px;
        }

        /* Actions */
        .kds-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .kds-btn {
            background: #21262d;
            border: 1px solid var(--kds-border);
            color: var(--kds-text);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .kds-btn:hover {
            background: #30363d;
            color: #fff;
        }

        .kds-btn-sound.muted {
            opacity: 0.6;
            color: var(--kds-text-muted);
        }

        .kds-btn-exit {
            background: rgba(248, 81, 73, 0.15);
            border-color: rgba(248, 81, 73, 0.4);
            color: #ff7b72;
        }

        .kds-btn-exit:hover {
            background: #f85149;
            color: #fff;
        }

        /* ----- FILTER SUB-BAR ----- */
        .kds-filter-bar {
            background: #11161d;
            padding: 8px 18px;
            border-bottom: 1px solid var(--kds-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .kds-tabs {
            display: flex;
            gap: 6px;
        }

        .kds-tab {
            background: transparent;
            border: 1px solid transparent;
            color: var(--kds-text-muted);
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .kds-tab:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .kds-tab.active {
            background: var(--kds-accent);
            color: #fff;
            border-color: var(--kds-accent);
        }

        .kds-tab-badge {
            background: rgba(0, 0, 0, 0.3);
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 11px;
        }

        .kds-sync-indicator {
            font-size: 12px;
            color: var(--kds-text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .kds-sync-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--kds-green);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
            100% { opacity: 1; transform: scale(1); }
        }

        /* ----- MAIN GRID ----- */
        .kds-container {
            padding: 18px;
            max-width: 1800px;
            margin: 0 auto;
        }

        .kds-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 16px;
            align-items: start;
        }

        /* ----- ORDER CARD ----- */
        .kds-card {
            background: var(--kds-card-bg);
            border-radius: 14px;
            border: 2px solid var(--kds-border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            transition: transform 0.15s ease, border-color 0.15s ease;
        }

        .kds-card:hover {
            transform: translateY(-2px);
        }

        /* Status colors on card border & header */
        .kds-card.status-pendiente {
            border-color: #d29922;
        }

        .kds-card.status-en_preparacion {
            border-color: #f0883e;
            box-shadow: 0 0 20px rgba(240, 136, 62, 0.2);
        }

        .kds-card.status-listo {
            border-color: #2ea043;
            box-shadow: 0 0 20px rgba(46, 160, 67, 0.25);
        }

        .kds-card.urgency-critical {
            border-color: #f85149 !important;
            animation: pulse-card-red 2.5s infinite;
        }

        @keyframes pulse-card-red {
            0% { box-shadow: 0 0 0 0 rgba(248, 81, 73, 0.5); }
            70% { box-shadow: 0 0 0 10px rgba(248, 81, 73, 0); }
            100% { box-shadow: 0 0 0 0 rgba(248, 81, 73, 0); }
        }

        /* Card Header */
        .kds-card-header {
            padding: 12px 16px;
            background: #21262d;
            border-bottom: 1px solid var(--kds-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .kds-order-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Insignia destacada para Delivery / Retiro / Consumir en el Local */
        .kds-type-badge {
            font-size: 13.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 6px 14px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            line-height: 1.2;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .kds-type-badge i {
            font-size: 15px;
        }

        .badge-delivery {
            background: linear-gradient(135deg, #1f6fff, #0b47d6);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 12px rgba(31, 111, 255, 0.45);
        }

        .badge-takeaway {
            background: linear-gradient(135deg, #a371f7, #7932e6);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 12px rgba(163, 113, 247, 0.45);
        }

        .badge-mesa {
            background: linear-gradient(135deg, #d29922, #9e6a03);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 12px rgba(210, 153, 34, 0.45);
        }

        .badge-local {
            background: linear-gradient(135deg, #2ea043, #1f7a32);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 12px rgba(46, 160, 67, 0.45);
        }

        /* Timer badge */
        .kds-timer {
            font-family: 'JetBrains Mono', monospace;
            font-size: 15px;
            font-weight: 800;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .timer-normal {
            background: rgba(46, 160, 67, 0.2);
            color: #56d364;
            border: 1px solid rgba(46, 160, 67, 0.4);
        }

        .timer-warning {
            background: rgba(210, 153, 34, 0.25);
            color: #e3b341;
            border: 1px solid rgba(210, 153, 34, 0.5);
        }

        .timer-critical {
            background: rgba(248, 81, 73, 0.25);
            color: #ff7b72;
            border: 1px solid rgba(248, 81, 73, 0.6);
            animation: blink-text 1.2s infinite;
        }

        @keyframes blink-text {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Customer bar */
        .kds-card-customer {
            padding: 8px 16px;
            background: #181d24;
            font-size: 13px;
            color: var(--kds-text-muted);
            border-bottom: 1px dashed var(--kds-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kds-card-customer strong {
            color: #e6edf3;
            font-weight: 600;
        }

        /* Items List */
        .kds-card-items {
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 120px;
        }

        .kds-item-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 8px;
            transition: background 0.15s ease;
        }

        .kds-item-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .kds-item-qty {
            font-family: 'JetBrains Mono', monospace;
            background: #30363d;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            min-width: 36px;
            text-align: center;
        }

        .kds-item-details {
            flex: 1;
        }

        .kds-item-name {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        .kds-item-note {
            font-size: 12px;
            color: #e3b341;
            font-weight: 600;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Strikethrough when cook taps item */
        .kds-item-row.done .kds-item-qty {
            background: #21262d;
            color: #484f58;
        }

        .kds-item-row.done .kds-item-name {
            text-decoration: line-through;
            color: #484f58;
        }

        .kds-item-row.done .kds-item-note {
            color: #484f58;
        }

        /* Kitchen General Note */
        .kds-card-note {
            margin: 0 16px 12px;
            background: rgba(210, 153, 34, 0.12);
            border: 1px dashed rgba(210, 153, 34, 0.35);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            color: #e3b341;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }

        /* Card Footer Action Buttons */
        .kds-card-footer {
            padding: 12px 16px;
            background: #21262d;
            border-top: 1px solid var(--kds-border);
            display: flex;
            gap: 8px;
        }

        .kds-action-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .btn-start-cooking {
            background: linear-gradient(135deg, #f0883e, #d29922);
            color: #fff;
            box-shadow: 0 4px 12px rgba(240, 136, 62, 0.3);
        }

        .btn-start-cooking:hover {
            filter: brightness(1.15);
            transform: translateY(-1px);
        }

        .btn-mark-ready {
            background: linear-gradient(135deg, #2ea043, #238636);
            color: #fff;
            box-shadow: 0 4px 12px rgba(46, 160, 67, 0.3);
        }

        .btn-mark-ready:hover {
            filter: brightness(1.15);
            transform: translateY(-1px);
        }

        .btn-mark-delivered {
            background: #30363d;
            color: #e6edf3;
            border: 1px solid var(--kds-border);
        }

        .btn-mark-delivered:hover {
            background: #2ea043;
            color: #fff;
        }

        /* Empty State */
        .kds-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            background: #161b22;
            border-radius: 16px;
            border: 2px dashed var(--kds-border);
            color: var(--kds-text-muted);
        }

        .kds-empty-icon {
            font-size: 56px;
            color: #30363d;
            margin-bottom: 16px;
        }

        .kds-empty h2 {
            font-size: 20px;
            color: #fff;
            font-weight: 700;
            margin-bottom: 6px;
        }

        /* Toast notifications */
        .kds-toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .kds-toast {
            background: #1f6fff;
            color: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slide-toast 0.25s ease-out;
        }

        @keyframes slide-toast {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .kds-navbar {
                padding: 8px 12px;
            }
            .kds-brand-info h1 {
                font-size: 16px;
            }
            .kds-clock {
                display: none;
            }
            .kds-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION -->
    <header class="kds-navbar">
        <div class="kds-brand">
            <div class="kds-brand-logo">
                <i class="fa fa-cutlery"></i>
            </div>
            <div class="kds-brand-info">
                <h1>PANEL DE COCINA</h1>
                <span>GeeksPOS • KDS en Vivo</span>
            </div>
        </div>

        <div class="kds-stats-bar">
            <div class="kds-stat-pill stat-pending" title="Comandas pendientes por iniciar">
                <i class="fa fa-clock-o"></i> <span id="stat-pending-count"><?= $stats['pendientes'] ?></span> Pendientes
            </div>
            <div class="kds-stat-pill stat-cooking" title="Comandas actualmente en plancha / horno">
                <i class="fa fa-fire"></i> <span id="stat-cooking-count"><?= $stats['en_preparacion'] ?></span> En Marcha
            </div>
            <div class="kds-stat-pill stat-ready" title="Comandas listas para ser retiradas o servidas">
                <i class="fa fa-check-circle"></i> <span id="stat-ready-count"><?= $stats['listos'] ?></span> Listas
            </div>
        </div>

        <div class="kds-clock" id="kds-clock">--:--:--</div>

        <div class="kds-controls">
            <button class="kds-btn kds-btn-sound" id="btn-sound-toggle" title="Activar/Desactivar sonido de comandas">
                <i class="fa fa-volume-up" id="sound-icon"></i> <span id="sound-text">Sonido: ON</span>
            </button>
            <button class="kds-btn" id="btn-fullscreen" title="Pantalla completa para monitor">
                <i class="fa fa-arrows-alt"></i> Pantalla Completa
            </button>
            <button class="kds-btn" id="btn-refresh" title="Refrescar lista ahora">
                <i class="fa fa-refresh"></i>
            </button>
            <?php if (!$is_cocina_user) { ?>
                <a href="<?= base_url('dashboard') ?>" class="kds-btn kds-btn-exit" title="Volver al Dashboard administrativo">
                    <i class="fa fa-sign-out"></i> Dashboard
                </a>
            <?php } else { ?>
                <a href="<?= base_url('user/logout') ?>" class="kds-btn kds-btn-exit" title="Cerrar Sesión de Cocina">
                    <i class="fa fa-power-off"></i> Salir
                </a>
            <?php } ?>
        </div>
    </header>

    <!-- FILTER BAR -->
    <div class="kds-filter-bar">
        <div class="kds-tabs">
            <button class="kds-tab active" data-filter="active">
                <i class="fa fa-bolt"></i> En Curso <span class="kds-tab-badge" id="tab-badge-active"><?= $stats['total_activos'] ?></span>
            </button>
            <button class="kds-tab" data-filter="pending">
                <i class="fa fa-hourglass-start"></i> Pendientes <span class="kds-tab-badge" id="tab-badge-pending"><?= $stats['pendientes'] ?></span>
            </button>
            <button class="kds-tab" data-filter="cooking">
                <i class="fa fa-fire"></i> En Plancha <span class="kds-tab-badge" id="tab-badge-cooking"><?= $stats['en_preparacion'] ?></span>
            </button>
            <button class="kds-tab" data-filter="ready">
                <i class="fa fa-check"></i> Listas para Entrega <span class="kds-tab-badge" id="tab-badge-ready"><?= $stats['listos'] ?></span>
            </button>
            <button class="kds-tab" data-filter="history">
                <i class="fa fa-history"></i> Historial Hoy
            </button>
        </div>

        <div class="kds-sync-indicator">
            <span class="kds-sync-dot"></span>
            <span id="sync-status">Sincronizado en tiempo real</span>
        </div>
    </div>

    <!-- MAIN ORDERS GRID -->
    <main class="kds-container">
        <div class="kds-grid" id="kds-grid">
            <!-- Dynamic cards injected via JavaScript -->
        </div>
    </main>

    <!-- TOAST CONTAINER -->
    <div class="kds-toast-container" id="toast-container"></div>

    <script>
        const BASE_URL = '<?= base_url() ?>';
        let currentFilter = 'active';
        let soundEnabled = true;
        let lastKnownOrderId = <?= (int)$stats['latest_order_id'] ?>;
        let ordersData = [];
        let pollingInterval = null;

        // Web Audio Synthesizer for Kitchen Bell Chime
        let audioCtx = null;
        function playKitchenChime() {
            if (!soundEnabled) return;
            try {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }

                const now = audioCtx.currentTime;
                // Tone 1 (High bell)
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now); // D5
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.6);

                // Tone 2 (Higher bell chime)
                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880.00, now + 0.15); // A5
                gain2.gain.setValueAtTime(0.4, now + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.9);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start(now + 0.15);
                osc2.stop(now + 0.9);
            } catch (e) {
                console.log('AudioContext error:', e);
            }
        }

        // Clock display
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('kds-clock').textContent = `${h}:${m}:${s}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Sound Toggle
        document.getElementById('btn-sound-toggle').addEventListener('click', () => {
            soundEnabled = !soundEnabled;
            const btn = document.getElementById('btn-sound-toggle');
            const icon = document.getElementById('sound-icon');
            const text = document.getElementById('sound-text');
            if (soundEnabled) {
                btn.classList.remove('muted');
                icon.className = 'fa fa-volume-up';
                text.textContent = 'Sonido: ON';
                playKitchenChime();
            } else {
                btn.classList.add('muted');
                icon.className = 'fa fa-volume-off';
                text.textContent = 'Sonido: OFF';
            }
        });

        // Fullscreen Toggle
        document.getElementById('btn-fullscreen').addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => alert(`Error: ${err.message}`));
            } else {
                document.exitFullscreen();
            }
        });

        // Manual Refresh
        document.getElementById('btn-refresh').addEventListener('click', () => {
            loadOrders();
        });

        // Filter Tabs Click
        document.querySelectorAll('.kds-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.kds-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.getAttribute('data-filter');
                loadOrders();
            });
        });

        // Fetch Orders via AJAX
        function loadOrders() {
            const syncStatus = document.getElementById('sync-status');
            fetch(`${BASE_URL}kitchen/api_orders?filter=${currentFilter}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        ordersData = data.orders || [];
                        updateStats(data.stats);
                        renderGrid(ordersData);

                        // If new order arrived, chime!
                        if (data.latest_id > lastKnownOrderId) {
                            lastKnownOrderId = data.latest_id;
                            playKitchenChime();
                            showToast(`¡Nueva comanda recibida! #${data.latest_id}`);
                        }

                        syncStatus.textContent = 'Sincronizado: ' + new Date().toLocaleTimeString();
                    }
                })
                .catch(err => {
                    console.error('Error fetching orders:', err);
                    syncStatus.textContent = 'Error de conexión';
                });
        }

        // Update top counter badges
        function updateStats(stats) {
            if (!stats) return;
            document.getElementById('stat-pending-count').textContent = stats.pendientes;
            document.getElementById('stat-cooking-count').textContent = stats.en_preparacion;
            document.getElementById('stat-ready-count').textContent = stats.listos;

            document.getElementById('tab-badge-active').textContent = stats.total_activos;
            document.getElementById('tab-badge-pending').textContent = stats.pendientes;
            document.getElementById('tab-badge-cooking').textContent = stats.en_preparacion;
            document.getElementById('tab-badge-ready').textContent = stats.listos;
        }

        // Render Cards in Grid
        function renderGrid(orders) {
            const grid = document.getElementById('kds-grid');
            if (!orders || orders.length === 0) {
                grid.innerHTML = `
                    <div class="kds-empty">
                        <div class="kds-empty-icon"><i class="fa fa-check-circle-o"></i></div>
                        <h2>No hay comandas en este estado</h2>
                        <p>Los nuevos pedidos ingresados desde POS o Delivery aparecerán aquí automáticamente.</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = orders.map(order => {
                const elapsedMin = parseInt(order.elapsed_minutes) || 0;
                let timerClass = 'timer-normal';
                let timerIcon = 'fa-clock-o';
                let urgencyClass = '';

                // Umbrales: 0 a 20m normal, 20 a 35m moderado, 35m+ demorado/crítico
                if (elapsedMin >= 35) {
                    timerClass = 'timer-critical';
                    timerIcon = 'fa-exclamation-triangle';
                    urgencyClass = 'urgency-critical';
                } else if (elapsedMin >= 20) {
                    timerClass = 'timer-warning';
                    timerIcon = 'fa-hourglass-half';
                }

                // Type badge
                let typeBadgeClass = 'badge-local';
                if (order.order_type === 'delivery') typeBadgeClass = 'badge-delivery';
                else if (order.order_type === 'takeaway') typeBadgeClass = 'badge-takeaway';
                else if (order.order_type === 'mesa') typeBadgeClass = 'badge-mesa';

                // Items list
                const itemsHtml = (order.items || []).map((item, idx) => `
                    <div class="kds-item-row" onclick="toggleItemDone(this)">
                        <div class="kds-item-qty">${item.qty}x</div>
                        <div class="kds-item-details">
                            <div class="kds-item-name">${escapeHtml(item.name)}</div>
                            ${item.notes ? `<div class="kds-item-note"><i class="fa fa-info-circle"></i> ${escapeHtml(item.notes)}</div>` : ''}
                        </div>
                    </div>
                `).join('');

                // Action buttons based on status
                let actionBtnHtml = '';
                if (order.status === 'pendiente') {
                    actionBtnHtml = `
                        <button class="kds-action-btn btn-start-cooking" onclick="changeStatus(${order.id}, 'en_preparacion')">
                            <i class="fa fa-fire"></i> Empezar a Preparar
                        </button>
                    `;
                } else if (order.status === 'en_preparacion') {
                    actionBtnHtml = `
                        <button class="kds-action-btn btn-mark-ready" onclick="changeStatus(${order.id}, 'listo')">
                            <i class="fa fa-check-circle"></i> ¡Marcar Listo!
                        </button>
                    `;
                } else if (order.status === 'listo') {
                    actionBtnHtml = `
                        <button class="kds-action-btn btn-mark-delivered" onclick="changeStatus(${order.id}, 'entregado')">
                            <i class="fa fa-cutlery"></i> Entregado / Servir
                        </button>
                    `;
                } else {
                    actionBtnHtml = `<span style="color:var(--kds-text-muted);font-size:12px;padding:8px;"><i class="fa fa-check"></i> Comanda Completada</span>`;
                }

                return `
                    <div class="kds-card status-${order.status} ${urgencyClass}" id="card-${order.id}">
                        <div class="kds-card-header">
                            <div class="kds-order-num">
                                ${escapeHtml(order.order_number)}
                                <span class="kds-type-badge ${typeBadgeClass}">
                                    <i class="fa ${order.type_icon || 'fa-tag'}"></i> ${escapeHtml(order.type_label)}
                                </span>
                            </div>
                            <div class="kds-timer ${timerClass}" id="timer-${order.id}" data-seconds="${order.elapsed_seconds || 0}">
                                <i class="fa ${timerIcon}"></i> <span class="timer-text">${order.elapsed_text}</span>
                            </div>
                        </div>

                        <div class="kds-card-customer">
                            <span><i class="fa fa-user-circle"></i> <strong>${escapeHtml(order.customer_name)}</strong></span>
                            ${order.delivery_slot ? `<span style="color:#58a6ff;"><i class="fa fa-motorcycle"></i> Slot ${order.delivery_slot}</span>` : ''}
                        </div>

                        <div class="kds-card-items">
                            ${itemsHtml}
                        </div>

                        ${order.notes ? `
                            <div class="kds-card-note">
                                <i class="fa fa-commenting-o"></i> <span>${escapeHtml(order.notes)}</span>
                            </div>
                        ` : ''}

                        <div class="kds-card-footer">
                            ${actionBtnHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Toggle item strike-through when cook touches item
        function toggleItemDone(elem) {
            elem.classList.toggle('done');
        }

        // Change Order Status via AJAX
        function changeStatus(id, nextStatus) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', nextStatus);

            fetch(`${BASE_URL}kitchen/update_status`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    showToast(`Comanda actualizada: ${nextStatus.replace('_', ' ').toUpperCase()}`);
                    loadOrders();
                } else {
                    alert(data.message || 'Error al actualizar comanda.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error de red al actualizar estado.');
            });
        }

        // Toast feedback
        function showToast(msg) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'kds-toast';
            toast.innerHTML = `<i class="fa fa-bell"></i> <span>${escapeHtml(msg)}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Fluid local seconds timer tick & dynamic color transition
        setInterval(() => {
            document.querySelectorAll('.kds-timer').forEach(timer => {
                let sec = parseInt(timer.getAttribute('data-seconds')) || 0;
                sec += 1;
                timer.setAttribute('data-seconds', sec);

                const min = Math.floor(sec / 60);
                const span = timer.querySelector('.timer-text');
                const icon = timer.querySelector('i');
                const card = timer.closest('.kds-card');

                // Umbrales dinámicos: 0-20m normal, 20-35m moderado, >=35m demorado
                if (min >= 35) {
                    timer.className = 'kds-timer timer-critical';
                    if (icon) icon.className = 'fa fa-exclamation-triangle';
                    if (card) card.classList.add('urgency-critical');
                } else if (min >= 20) {
                    timer.className = 'kds-timer timer-warning';
                    if (icon) icon.className = 'fa fa-hourglass-half';
                    if (card) card.classList.remove('urgency-critical');
                } else {
                    timer.className = 'kds-timer timer-normal';
                    if (icon) icon.className = 'fa fa-clock-o';
                    if (card) card.classList.remove('urgency-critical');
                }

                if (span) {
                    if (min < 1) {
                        span.textContent = `${sec}s`;
                    } else if (min < 60) {
                        span.textContent = `${min}m ${sec % 60}s`;
                    } else {
                        const h = Math.floor(min / 60);
                        span.textContent = `${h}h ${min % 60}m`;
                    }
                }
            });
        }, 1000);

        // Helper to escape HTML safely
        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Initial load & Polling every 8 seconds
        loadOrders();
        pollingInterval = setInterval(loadOrders, 8000);
    </script>
</body>
</html>
