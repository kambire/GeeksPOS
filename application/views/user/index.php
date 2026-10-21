<body class="horizontal-layout horizontal-menu 1-column bg-full-screen-image menu-expanded blank-page blank-page"
      data-open="hover" data-menu="horizontal-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<style>
    :root {
        --gp-navy: #0b2545;
        --gp-blue: #1f6fff;
        --gp-blue-dark: #0b47d6;
        --gp-ink: #1c2a44;
        --gp-muted: #8a94a6;
        --gp-border: #e4e9f2;
        --gp-bg-soft: #f6f8fc;
    }

    body.bg-full-screen-image {
        background: radial-gradient(1200px 600px at 85% -10%, rgba(31, 111, 255, .35), transparent 60%),
                    radial-gradient(900px 500px at 0% 110%, rgba(18, 58, 134, .45), transparent 55%),
                    linear-gradient(135deg, #061024 0%, #0b2545 45%, #10347e 100%);
        background-attachment: fixed;
        font-family: 'Open Sans', sans-serif;
    }

    .gp-login-wrap {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        position: relative;
    }

    .gp-card {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 940px;
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 30px 80px -24px rgba(4, 18, 45, .6);
        overflow: hidden;
        display: flex;
    }

    /* ----- Panel de marca ----- */
    .gp-brand {
        flex: 0 0 44%;
        background: linear-gradient(155deg, var(--gp-navy) 0%, #123a86 58%, var(--gp-blue) 125%);
        color: #fff;
        padding: 48px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .gp-brand::after {
        content: "";
        position: absolute;
        width: 320px;
        height: 320px;
        right: -120px;
        top: -120px;
        border-radius: 50%;
        background: radial-gradient(circle at center, rgba(255, 255, 255, .14), transparent 70%);
    }

    .gp-brand::before {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        left: -90px;
        bottom: -110px;
        border-radius: 50%;
        background: radial-gradient(circle at center, rgba(31, 111, 255, .35), transparent 70%);
    }

    .gp-logo-chip {
        position: relative;
        z-index: 2;
        background: #fff;
        border-radius: 18px;
        padding: 22px 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 16px 40px rgba(0, 0, 0, .22);
        margin-bottom: 26px;
    }

    .gp-logo-chip img {
        width: 230px;
        max-width: 100%;
        height: auto;
        display: block;
    }

    .gp-brand h3 {
        position: relative;
        z-index: 2;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 22px;
        line-height: 1.3;
        margin: 0 0 10px;
        color: #fff;
    }

    .gp-brand p {
        position: relative;
        z-index: 2;
        color: rgba(255, 255, 255, .75);
        font-size: 14px;
        margin-bottom: 22px;
    }

    .gp-features {
        position: relative;
        z-index: 2;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .gp-features li {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: rgba(255, 255, 255, .9);
        margin-bottom: 12px;
    }

    .gp-features li i {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .15);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #fff;
    }

    /* ----- Formulario ----- */
    .gp-form {
        flex: 1 1 56%;
        padding: 54px 48px;
    }

    .gp-form .gp-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: var(--gp-navy);
        font-size: 24px;
        margin: 0 0 6px;
    }

    .gp-form .gp-subtitle {
        color: var(--gp-muted);
        font-size: 14px;
        margin-bottom: 30px;
    }

    .gp-form fieldset.form-group {
        margin-bottom: 20px;
    }

    .gp-form .form-control {
        height: 54px;
        border-radius: 12px;
        border: 1.5px solid var(--gp-border);
        background: var(--gp-bg-soft);
        padding-left: 46px;
        font-size: 15px;
        color: var(--gp-ink);
        transition: all .2s ease;
    }

    .gp-form .form-control::placeholder {
        color: #a9b2c3;
    }

    .gp-form .form-control:focus {
        border-color: var(--gp-blue);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(31, 111, 255, .12);
    }

    .gp-form .has-icon-left .form-control-position {
        top: 50%;
        transform: translateY(-50%);
        left: 0;
        width: 46px;
        text-align: center;
        color: var(--gp-blue);
        font-size: 16px;
    }

    .gp-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 26px;
        font-size: 13.5px;
    }

    .gp-options label {
        color: var(--gp-ink);
        margin: 0 0 0 6px;
        cursor: pointer;
    }

    .gp-options a {
        color: var(--gp-blue);
        font-weight: 600;
        text-decoration: none;
    }

    .gp-options a:hover {
        text-decoration: underline;
    }

    .gp-btn-primary {
        width: 100%;
        height: 54px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        letter-spacing: .3px;
        color: #fff;
        background: linear-gradient(135deg, var(--gp-blue), var(--gp-blue-dark));
        box-shadow: 0 14px 26px -10px rgba(31, 111, 255, .65);
        transition: all .2s ease;
        cursor: pointer;
    }

    .gp-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 32px -10px rgba(31, 111, 255, .75);
        color: #fff;
    }

    .gp-divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: var(--gp-muted);
        font-size: 13px;
        margin: 26px 0 18px;
    }

    .gp-divider::before,
    .gp-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--gp-border);
    }

    .gp-divider span {
        padding: 0 14px;
    }

    .gp-btn-ghost {
        width: 100%;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 12px;
        border: 1.5px solid var(--gp-border);
        background: #fff;
        color: var(--gp-ink);
        font-weight: 600;
        font-size: 14.5px;
        text-decoration: none;
        transition: all .2s ease;
    }

    .gp-btn-ghost:hover {
        border-color: var(--gp-blue);
        color: var(--gp-blue);
        background: var(--gp-bg-soft);
    }

    .gp-form .alert {
        border-radius: 12px;
        margin-bottom: 18px;
    }

    /* ----- Responsive ----- */
    @media (max-width: 860px) {
        .gp-card {
            flex-direction: column;
            max-width: 460px;
        }

        .gp-brand {
            flex: none;
            padding: 36px 32px;
            text-align: center;
            align-items: center;
        }

        .gp-features {
            display: inline-block;
            text-align: left;
        }

        .gp-logo-chip img {
            width: 190px;
        }

        .gp-form {
            padding: 36px 28px;
        }
    }
</style>

<div class="gp-login-wrap">
    <div class="gp-card">
        <!-- Panel de marca -->
        <div class="gp-brand">
            <div class="gp-logo-chip">
                <img src="<?php echo base_url('userfiles/company/') . $this->config->item('logo'); ?>" alt="logo">
            </div>
            <h3>Bienvenido a tu panel de gestión</h3>
            <p>Administra ventas, inventario y clientes desde un solo lugar.</p>
            <ul class="gp-features">
                <li><i class="fa fa-check"></i> Punto de venta rápido e intuitivo</li>
                <li><i class="fa fa-check"></i> Control de inventario en tiempo real</li>
                <li><i class="fa fa-check"></i> Reportes y CRM integrados</li>
            </ul>
        </div>

        <!-- Formulario -->
        <div class="gp-form">
            <h2 class="gp-title"><?php echo $this->lang->line('employee_login_panel') ?></h2>
            <p class="gp-subtitle">Ingresa tus credenciales para continuar</p>

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
                       placeholder="<?php echo $this->lang->line('Your Email') ?>" required>
                <div class="form-control-position">
                    <i class="ft-user"></i>
                </div>
            </fieldset>

            <fieldset class="form-group position-relative has-icon-left">
                <input type="password" class="form-control" id="user-password" name="password"
                       placeholder="<?php echo $this->lang->line('Your Password') ?>" required>
                <div class="form-control-position">
                    <i class="fa fa-key"></i>
                </div>
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

            <button type="submit" class="gp-btn-primary"><i class="ft-unlock"></i> <?php echo $this->lang->line('login') ?></button>
            </form>

            <div class="gp-divider"><span>Are you a client ?</span></div>

            <a href="<?php echo base_url('crm'); ?>" class="gp-btn-ghost"><i class="ft-user"></i> <?php echo $this->lang->line('customer_login') ?></a>
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
