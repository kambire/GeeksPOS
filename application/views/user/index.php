<body class="horizontal-layout horizontal-menu 1-column bg-full-screen-image menu-expanded blank-page blank-page"
      data-open="hover" data-menu="horizontal-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<style>
    :root {
        --gp-navy: #090e24;
        --gp-blue: #2563eb;
        --gp-blue-light: #38bdf8;
        --gp-purple: #7c3aed;
        --gp-pink: #ec4899;
        --gp-ink: #0f172a;
        --gp-muted: #64748b;
        --gp-border: #e2e8f0;
        --gp-bg-soft: #f8fafc;
    }

    body.bg-full-screen-image {
        margin: 0;
        padding: 0;
        background: #030712;
        overflow-x: hidden;
        min-height: 100vh;
        font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        position: relative;
    }

    /* =========================================================================
       DYNAMIC AMBIENT BACKGROUND WITH LIVING COLOR ORBS & CANVAS
       ========================================================================= */
    .ambient-bg-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    #ambientCanvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        opacity: 0.85;
    }

    .ambient-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(85px);
        opacity: 0.6;
        mix-blend-mode: screen;
        will-change: transform;
    }

    .orb-cyan {
        width: 550px;
        height: 550px;
        top: -150px;
        right: -100px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.75) 0%, rgba(6, 182, 212, 0.1) 65%, transparent 75%);
        animation: orbMotion1 22s ease-in-out infinite alternate;
    }

    .orb-purple {
        width: 600px;
        height: 600px;
        bottom: -180px;
        left: -120px;
        background: radial-gradient(circle, rgba(124, 58, 237, 0.75) 0%, rgba(79, 70, 229, 0.1) 65%, transparent 75%);
        animation: orbMotion2 26s ease-in-out infinite alternate;
    }

    .orb-pink {
        width: 450px;
        height: 450px;
        top: 35%;
        left: 45%;
        background: radial-gradient(circle, rgba(236, 72, 153, 0.55) 0%, rgba(219, 39, 119, 0.1) 65%, transparent 75%);
        animation: orbMotion3 18s ease-in-out infinite alternate;
    }

    .orb-emerald {
        width: 480px;
        height: 480px;
        bottom: 10%;
        right: 15%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.5) 0%, rgba(20, 184, 166, 0.08) 65%, transparent 75%);
        animation: orbMotion4 20s ease-in-out infinite alternate;
    }

    @keyframes orbMotion1 {
        0% { transform: translate(0, 0) scale(1) rotate(0deg); }
        50% { transform: translate(-120px, 90px) scale(1.15) rotate(180deg); }
        100% { transform: translate(60px, -40px) scale(0.95) rotate(360deg); }
    }

    @keyframes orbMotion2 {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(110px, -90px) scale(1.2); }
        100% { transform: translate(-70px, 60px) scale(0.92); }
    }

    @keyframes orbMotion3 {
        0% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-30%, -70%) scale(1.25); }
        100% { transform: translate(-70%, -30%) scale(0.9); }
    }

    @keyframes orbMotion4 {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-80px, -60px) scale(1.18); }
        100% { transform: translate(50px, 70px) scale(0.95); }
    }

    /* Grid de fondo con sutil brillo */
    .ambient-grid {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 40px 40px;
        z-index: 2;
        opacity: 0.6;
    }

    /* =========================================================================
       MAIN LOGIN CARD & LAYOUT
       ========================================================================= */
    .gp-login-wrap {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        position: relative;
        z-index: 10;
    }

    .gp-card {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 980px;
        background: #ffffff;
        border-radius: 26px;
        box-shadow: 0 30px 100px -20px rgba(0, 0, 0, 0.75),
                    0 0 0 1px rgba(255, 255, 255, 0.15);
        overflow: hidden;
        display: flex;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* =========================================================================
       LEFT BRAND SHOWCASE - MODERN CYBER GRADIENT & FLOATING ELEMENTS
       ========================================================================= */
    .gp-brand {
        flex: 0 0 46%;
        background: linear-gradient(155deg, #090e24 0%, #0d1b3e 42%, #181048 100%);
        color: #ffffff;
        padding: 48px 40px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    /* Líneas decorativas sutiles en el panel de marca */
    .gp-brand::before {
        content: "";
        position: absolute;
        width: 320px;
        height: 320px;
        top: -90px;
        right: -90px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }

    .gp-brand::after {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        bottom: -80px;
        left: -80px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(168, 85, 247, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Logo Chip con halo de luz animado */
    .gp-logo-chip {
        position: relative;
        z-index: 3;
        background: #ffffff;
        border-radius: 20px;
        padding: 20px 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35),
                    0 0 35px rgba(56, 189, 248, 0.3);
        margin-bottom: 26px;
        max-width: 290px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        animation: logoGlow 4s ease-in-out infinite alternate;
    }

    @keyframes logoGlow {
        0% { box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35), 0 0 25px rgba(56, 189, 248, 0.25); }
        100% { box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4), 0 0 45px rgba(124, 58, 237, 0.45); }
    }

    .gp-logo-chip img {
        width: 230px;
        max-width: 100%;
        height: auto;
        display: block;
        transition: transform 0.3s ease;
    }

    .gp-logo-chip:hover img {
        transform: scale(1.03);
    }

    .gp-brand-header {
        position: relative;
        z-index: 3;
    }

    .gp-brand-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(56, 189, 248, 0.15);
        border: 1px solid rgba(56, 189, 248, 0.3);
        color: #38bdf8;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .gp-brand h3 {
        position: relative;
        z-index: 3;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 23px;
        line-height: 1.25;
        margin: 0 0 8px;
        color: #ffffff;
        letter-spacing: -0.3px;
    }

    .gp-brand p.gp-lead {
        position: relative;
        z-index: 3;
        color: #cbd5e1;
        font-size: 13.5px;
        line-height: 1.45;
        margin-bottom: 24px;
    }

    /* Tarjetas dinámicas de características con animación flotante */
    .gp-feature-cards {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 24px;
    }

    .gp-feature-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        background: rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: default;
    }

    .gp-feature-item:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(56, 189, 248, 0.4);
        transform: translateX(4px);
    }

    .gp-feature-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        color: #ffffff;
        flex-shrink: 0;
    }

    .icon-bot { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); }
    .icon-sifen { background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); }
    .icon-delivery { background: linear-gradient(135deg, #e11d48 0%, #fb7185 100%); }

    .gp-feature-info {
        flex: 1;
        min-width: 0;
    }

    .gp-feature-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 2px;
    }

    .gp-feature-badge {
        font-size: 9.5px;
        padding: 2px 6px;
        border-radius: 6px;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .badge-bot { background: rgba(168, 85, 247, 0.25); color: #e9d5ff; border: 1px solid rgba(168, 85, 247, 0.4); }
    .badge-sifen { background: rgba(56, 189, 248, 0.25); color: #bae6fd; border: 1px solid rgba(56, 189, 248, 0.4); }
    .badge-delivery { background: rgba(244, 63, 94, 0.25); color: #fecdd3; border: 1px solid rgba(244, 63, 94, 0.4); }

    .gp-feature-desc {
        font-size: 11.5px;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Live Status Radar Pill */
    .gp-live-status {
        position: relative;
        z-index: 3;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 30px;
        background: rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 11.5px;
        color: #cbd5e1;
        width: fit-content;
    }

    .status-dot-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        position: relative;
    }

    .status-dot-pulse::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #22c55e;
        animation: radarPulse 1.8s infinite;
    }

    @keyframes radarPulse {
        0% { transform: scale(1); opacity: 0.9; }
        100% { transform: scale(2.6); opacity: 0; }
    }

    /* =========================================================================
       RIGHT FORM PANEL - CRISP & ERGONOMIC
       ========================================================================= */
    .gp-form {
        flex: 1 1 54%;
        padding: 50px 48px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
    }

    .gp-form-header {
        margin-bottom: 28px;
    }

    .gp-form-tag {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        background: #eff6ff;
        color: #2563eb;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .gp-form .gp-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: var(--gp-ink);
        font-size: 25px;
        line-height: 1.2;
        margin: 0 0 6px;
        letter-spacing: -0.3px;
    }

    .gp-form .gp-subtitle {
        color: var(--gp-muted);
        font-size: 14px;
        margin: 0;
    }

    .gp-form fieldset.form-group {
        margin-bottom: 18px;
        position: relative;
    }

    .gp-form .form-control {
        height: 52px;
        border-radius: 12px;
        border: 1.5px solid var(--gp-border);
        background: var(--gp-bg-soft);
        padding-left: 46px;
        padding-right: 44px;
        font-size: 14.5px;
        color: var(--gp-ink);
        transition: all 0.2s ease;
    }

    .gp-form .form-control::placeholder {
        color: #94a3b8;
    }

    .gp-form .form-control:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        outline: none;
    }

    .gp-form .has-icon-left .form-control-position {
        top: 50%;
        transform: translateY(-50%);
        left: 0;
        width: 46px;
        text-align: center;
        color: #3b82f6;
        font-size: 16px;
        pointer-events: none;
    }

    /* Botón ojito para ver/ocultar contraseña */
    .btn-toggle-pwd {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 15px;
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
        z-index: 5;
    }

    .btn-toggle-pwd:hover {
        color: #2563eb;
    }

    .gp-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        font-size: 13.5px;
    }

    .gp-options label {
        color: var(--gp-ink);
        margin: 0 0 0 6px;
        cursor: pointer;
        font-weight: 500;
    }

    .gp-options a {
        color: #2563eb;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }

    .gp-options a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    /* Botón Principal con Gradiente Animado y Shimmer */
    .gp-btn-primary {
        width: 100%;
        height: 52px;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: 0.4px;
        color: #ffffff;
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 50%, #06b6d4 100%);
        background-size: 200% 200%;
        animation: gradButton 6s ease infinite;
        box-shadow: 0 12px 28px -8px rgba(37, 99, 235, 0.6);
        transition: all 0.25s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    @keyframes gradButton {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .gp-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -8px rgba(124, 58, 237, 0.65);
        color: #ffffff;
    }

    .gp-btn-primary:active {
        transform: translateY(0);
    }

    .gp-divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #94a3b8;
        font-size: 12.5px;
        margin: 22px 0 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .gp-divider::before,
    .gp-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--gp-border);
    }

    .gp-divider span {
        padding: 0 12px;
    }

    .gp-btn-ghost {
        width: 100%;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 12px;
        border: 1.5px solid var(--gp-border);
        background: #ffffff;
        color: var(--gp-ink);
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .gp-btn-ghost:hover {
        border-color: #2563eb;
        color: #2563eb;
        background: #f8fafc;
        transform: translateY(-1px);
    }

    .gp-form .alert {
        border-radius: 12px;
        margin-bottom: 18px;
    }

    /* =========================================================================
       RESPONSIVE ADAPTATION
       ========================================================================= */
    @media (max-width: 900px) {
        .gp-card {
            flex-direction: column;
            max-width: 480px;
        }

        .gp-brand {
            flex: none;
            padding: 36px 28px;
            text-align: center;
            align-items: center;
        }

        .gp-feature-cards {
            width: 100%;
            text-align: left;
        }

        .gp-logo-chip {
            margin-left: auto;
            margin-right: auto;
        }

        .gp-form {
            padding: 36px 28px;
        }
    }
</style>

<!-- LIVING AMBIENT MOTION BACKGROUND -->
<div class="ambient-bg-wrapper">
    <div class="ambient-grid"></div>
    <div class="ambient-orb orb-cyan"></div>
    <div class="ambient-orb orb-purple"></div>
    <div class="ambient-orb orb-pink"></div>
    <div class="ambient-orb orb-emerald"></div>
    <canvas id="ambientCanvas"></canvas>
</div>

<!-- LOGIN CONTENT -->
<div class="gp-login-wrap">
    <div class="gp-card">
        <!-- Panel de marca dinámico con movimiento -->
        <div class="gp-brand">
            <div class="gp-brand-header">
                <div class="gp-logo-chip">
                    <img src="<?php echo base_url('userfiles/company/') . $this->config->item('logo'); ?>" alt="GeeksPOS Logo">
                </div>

                <div class="gp-brand-tag">
                    <i class="fa fa-bolt"></i> Sistema SaaS Empresarial
                </div>

                <h3>Control Operativo Inteligente</h3>
                <p class="gp-lead">Gestión de ventas, inventario en tiempo real, facturación SIFEN y delivery automatizado.</p>

                <!-- Tarjetas dinámicas interactivas -->
                <div class="gp-feature-cards">
                    <div class="gp-feature-item">
                        <div class="gp-feature-icon icon-sifen">
                            <i class="fa fa-qrcode"></i>
                        </div>
                        <div class="gp-feature-info">
                            <div class="gp-feature-title">
                                <span>Facturación SIFEN</span>
                                <span class="gp-feature-badge badge-sifen">Oficial</span>
                            </div>
                            <div class="gp-feature-desc">DTE firmado y consultas en tiempo real</div>
                        </div>
                    </div>

                    <div class="gp-feature-item">
                        <div class="gp-feature-icon icon-bot">
                            <i class="fa fa-android"></i>
                        </div>
                        <div class="gp-feature-info">
                            <div class="gp-feature-title">
                                <span>GeeksIA & WhatsApp</span>
                                <span class="gp-feature-badge badge-bot">Multimodal</span>
                            </div>
                            <div class="gp-feature-desc">Bot autónomo, visión y toma de pedidos</div>
                        </div>
                    </div>

                    <div class="gp-feature-item">
                        <div class="gp-feature-icon icon-delivery">
                            <i class="fa fa-motorcycle"></i>
                        </div>
                        <div class="gp-feature-info">
                            <div class="gp-feature-title">
                                <span>KDS Cocina & Delivery</span>
                                <span class="gp-feature-badge badge-delivery">GPS Live</span>
                            </div>
                            <div class="gp-feature-desc">Monitoreo de turnos y comandas en vivo</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado del sistema con radar verde -->
            <div class="gp-live-status">
                <span class="status-dot-pulse"></span>
                <span>GeeksPOS Cloud Platform • Conectado</span>
            </div>
        </div>

        <!-- Formulario de acceso -->
        <div class="gp-form">
            <div class="gp-form-header">
                <span class="gp-form-tag">Acceso Seguro</span>
                <h2 class="gp-title"><?php echo $this->lang->line('employee_login_panel') ?></h2>
                <p class="gp-subtitle">Ingresa tus credenciales autorizadas para continuar</p>
            </div>

            <?php
            $attributes = array('class' => 'form-horizontal form-simple', 'id' => 'login_form');
            echo form_open('user/checklogin', $attributes);
            ?>
            <?php if ($response) {
                echo '<div id="notify" class="alert alert-danger" >
                        <a href="#" class="close" data-dismiss="alert">&times;</a> <div class="message">' . $response . '</div>
                    </div>';
            } ?>

            <fieldset class="form-group position-relative has-icon-left">
                <input type="text" class="form-control" id="user-name" name="username"
                       placeholder="<?php echo $this->lang->line('Your Email') ?>" required autocomplete="username">
                <div class="form-control-position">
                    <i class="ft-user"></i>
                </div>
            </fieldset>

            <fieldset class="form-group position-relative has-icon-left">
                <input type="password" class="form-control" id="user-password" name="password"
                       placeholder="<?php echo $this->lang->line('Your Password') ?>" required autocomplete="current-password">
                <div class="form-control-position">
                    <i class="fa fa-key"></i>
                </div>
                <button type="button" class="btn-toggle-pwd" id="btnTogglePassword" title="Mostrar u ocultar contraseña">
                    <i class="fa fa-eye" id="iconTogglePwd"></i>
                </button>
            </fieldset>

            <?php if ($this->aauth->get_login_attempts() > 1 && $captcha_on) {
                echo '<script src="https://www.google.com/recaptcha/api.js"></script>
                    <fieldset class="form-group position-relative has-icon-left">
                      <div class="g-recaptcha" data-sitekey="' . $captcha . '"></div>
                    </fieldset>';
            } ?>

            <div class="gp-options">
                <div>
                    <input type="checkbox" id="remember-me" class="chk-remember" name="remember_me">
                    <label for="remember-me"><?php echo $this->lang->line('remember_me') ?></label>
                </div>
                <a href="<?php echo base_url('user/forgot'); ?>"><?php echo $this->lang->line('forgot_password') ?> ?</a>
            </div>

            <button type="submit" class="gp-btn-primary">
                <i class="ft-unlock"></i> <?php echo $this->lang->line('login') ?>
            </button>
            </form>

            <div class="gp-divider"><span>Acceso de Clientes</span></div>

            <a href="<?php echo base_url('crm'); ?>" class="gp-btn-ghost">
                <i class="ft-user"></i> <?php echo $this->lang->line('customer_login') ?>
            </a>
        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->

<script src="<?= assets_url(); ?>app-assets/vendors/js/vendors.min.js"></script>
<script type="text/javascript" src="<?= assets_url(); ?>app-assets/vendors/js/ui/jquery.sticky.js"></script>
<script type="text/javascript" src="<?= assets_url(); ?>app-assets/vendors/js/charts/jquery.sparkline.min.js"></script>
<script src="<?= assets_url(); ?>app-assets/vendors/js/forms/validation/jqBootstrapValidation.js"></script>
<script src="<?= assets_url(); ?>app-assets/vendors/js/forms/icheck/icheck.min.js"></script>
<script src="<?= assets_url(); ?>app-assets/js/core/app-menu.js"></script>
<script src="<?= assets_url(); ?>app-assets/js/core/app.js"></script>
<script type="text/javascript" src="<?= assets_url(); ?>app-assets/js/scripts/ui/breadcrumbs-with-stats.js"></script>
<script src="<?= assets_url(); ?>app-assets/js/scripts/forms/form-login-register.js"></script>

<!-- LIGHTWEIGHT PARTICLE CANVAS & LOGIN ENHANCEMENTS -->
<script>
(function() {
    // 1. Toggle Mostrar / Ocultar Contraseña
    var btnToggle = document.getElementById('btnTogglePassword');
    var inputPwd = document.getElementById('user-password');
    var iconToggle = document.getElementById('iconTogglePwd');

    if (btnToggle && inputPwd && iconToggle) {
        btnToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (inputPwd.type === 'password') {
                inputPwd.type = 'text';
                iconToggle.className = 'fa fa-eye-slash';
            } else {
                inputPwd.type = 'password';
                iconToggle.className = 'fa fa-eye';
            }
        });
    }

    // 2. Canvas interactivo de partículas en vivo
    var canvas = document.getElementById('ambientCanvas');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    if (!ctx) return;

    var width, height;
    var particles = [];
    var particleCount = window.innerWidth < 768 ? 24 : 50;
    var maxDistance = 140;
    var mouse = { x: null, y: null, radius: 120 };

    function resize() {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    window.addEventListener('mousemove', function(e) {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    });

    window.addEventListener('mouseout', function() {
        mouse.x = null;
        mouse.y = null;
    });

    function Particle() {
        this.x = Math.random() * width;
        this.y = Math.random() * height;
        this.vx = (Math.random() - 0.5) * 0.7;
        this.vy = (Math.random() - 0.5) * 0.7;
        this.radius = Math.random() * 2 + 1;
        this.color = Math.random() > 0.5 ? 'rgba(56, 189, 248, ' : 'rgba(168, 85, 247, ';
        this.alpha = Math.random() * 0.5 + 0.2;
    }

    Particle.prototype.update = function() {
        this.x += this.vx;
        this.y += this.vy;

        if (this.x < 0 || this.x > width) this.vx = -this.vx;
        if (this.y < 0 || this.y > height) this.vy = -this.vy;

        // Reacción sutil al cursor del ratón
        if (mouse.x !== null && mouse.y !== null) {
            var dx = mouse.x - this.x;
            var dy = mouse.y - this.y;
            var dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < mouse.radius) {
                var force = (mouse.radius - dist) / mouse.radius;
                this.x -= (dx / dist) * force * 1.5;
                this.y -= (dy / dist) * force * 1.5;
            }
        }
    };

    Particle.prototype.draw = function() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
        ctx.fillStyle = this.color + this.alpha + ')';
        ctx.shadowBlur = 8;
        ctx.shadowColor = this.color + '0.8)';
        ctx.fill();
        ctx.shadowBlur = 0;
    };

    for (var i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }

    var isTabActive = true;
    document.addEventListener('visibilitychange', function() {
        isTabActive = !document.hidden;
    });

    function animate() {
        if (isTabActive) {
            ctx.clearRect(0, 0, width, height);

            for (var i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();

                // Conectar nodos cercanos con líneas de luz
                for (var j = i + 1; j < particles.length; j++) {
                    var dx = particles[i].x - particles[j].x;
                    var dy = particles[i].y - particles[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < maxDistance) {
                        var lineAlpha = (1 - dist / maxDistance) * 0.22;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(56, 189, 248, ' + lineAlpha + ')';
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }
        }
        requestAnimationFrame(animate);
    }
    animate();
})();
</script>
