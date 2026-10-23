<div class="content-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0">
                    <i class="fa fa-certificate text-danger"></i> Facturación Electrónica SIFEN (Paraguay)
                </h4>
                <small class="text-muted">Cumplimiento oficial con la Dirección Nacional de Ingresos Tributarios (DNIT / e-Kuatia v150)</small>
            </div>
            <div>
                <span class="badge badge-<?= (!empty($config['habilitado']) ? 'success' : 'secondary') ?> p-1 font-medium-1">
                    <?= (!empty($config['habilitado']) ? '● SIFEN ACTIVO' : '○ SIFEN INACTIVO') ?>
                </span>
                <span class="badge badge-<?= ($config['ambiente'] === 'prod' ? 'danger' : 'info') ?> p-1 font-medium-1">
                    <?= ($config['ambiente'] === 'prod' ? 'PRODUCCIÓN' : 'TEST / HOMOLOGACIÓN') ?>
                </span>
                <a href="<?= base_url('sifen_config/pruebas') ?>" class="btn btn-sm btn-primary ml-1">
                    <i class="fa fa-flask"></i> Pruebas y envíos
                </a>
            </div>
        </div>

        <div class="card-content">
            <div class="card-body">

                <!-- Mensajes flash -->
                <?php if ($this->session->flashdata('success_msg')) { ?>
                    <div class="alert alert-success alert-dismissible mb-2" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>¡Éxito!</strong> <?= $this->session->flashdata('success_msg') ?>
                    </div>
                <?php } ?>

                <?php if ($this->session->flashdata('error_msg')) { ?>
                    <div class="alert alert-danger alert-dismissible mb-2" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>¡Error!</strong> <?= $this->session->flashdata('error_msg') ?>
                    </div>
                <?php } ?>

                <?php if ($this->session->flashdata('warning_msg')) { ?>
                    <div class="alert alert-warning alert-dismissible mb-2" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Atención:</strong> <?= $this->session->flashdata('warning_msg') ?>
                    </div>
                <?php } ?>

                <form action="<?= base_url('sifen_config/save') ?>" method="post" enctype="multipart/form-data" class="form">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                    <div class="row">
                        <!-- COLUMNA IZQUIERDA: DATOS FISCALES -->
                        <div class="col-md-6 border-right">
                            <h5 class="text-primary font-weight-bold mb-2">
                                <i class="fa fa-building"></i> 1. Datos del Contribuyente y Timbrado
                            </h5>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Activar SIFEN</label>
                                <div class="col-sm-8">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="habilitado" name="habilitado" value="1" <?= (!empty($config['habilitado']) ? 'checked' : '') ?>>
                                        <label class="custom-control-label" for="habilitado">Emitir Factura Electrónica en POS y API</label>
                                    </div>
                                    <small class="form-text text-muted">Al activarse, las ventas generarán CDC de 44 dígitos, XML DTE y ticket térmico KuDE.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Ambiente SIFEN</label>
                                <div class="col-sm-8">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="ambiente" id="amb_test" value="test" <?= ($config['ambiente'] !== 'prod' ? 'checked' : '') ?>>
                                        <label class="form-check-label" for="amb_test">Pruebas (Test / Homologación)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="ambiente" id="amb_prod" value="prod" <?= ($config['ambiente'] === 'prod' ? 'checked' : '') ?>>
                                        <label class="form-check-label text-danger font-weight-bold" for="amb_prod">Producción (DNIT Oficial)</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">RUC Emisor y DV</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="ruc" name="ruc" value="<?= htmlspecialchars($config['ruc']) ?>" placeholder="Ej: 80012345" required onkeyup="calcularDV()">
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">-</span></div>
                                        <input type="text" class="form-control text-center font-weight-bold" id="dv" name="dv" value="<?= htmlspecialchars($config['dv']) ?>" maxlength="1" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Razón Social</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="razon_social" value="<?= htmlspecialchars($config['razon_social']) ?>" placeholder="Nombre legal registrado en DNIT" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Nombre de Fantasía</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="nombre_fantasia" value="<?= htmlspecialchars($config['nombre_fantasia']) ?>" placeholder="Nombre comercial del local">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Timbrado N°</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control font-weight-bold" name="timbrado" value="<?= htmlspecialchars($config['timbrado']) ?>" maxlength="8" placeholder="8 dígitos" required>
                                </div>
                                <label class="col-sm-1 col-form-label">Inicio</label>
                                <div class="col-sm-3">
                                    <input type="date" class="form-control" name="timbrado_fecha" value="<?= htmlspecialchars($config['timbrado_fecha']) ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Establecimiento / Punto</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Est.</span></div>
                                        <input type="text" class="form-control text-center" name="establecimiento" value="<?= htmlspecialchars($config['establecimiento']) ?>" maxlength="3" placeholder="001" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Pto.</span></div>
                                        <input type="text" class="form-control text-center" name="punto_expedicion" value="<?= htmlspecialchars($config['punto_expedicion']) ?>" maxlength="3" placeholder="001" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Siguiente Secuencia</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold text-primary">N°</span>
                                        </div>
                                        <input type="number" class="form-control font-weight-bold" name="secuencia_actual" value="<?= (int)$config['secuencia_actual'] ?>" min="1" max="9999999" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text text-muted" id="preview_factura_num">
                                                001-001-<?= sprintf('%07d', $config['secuencia_actual']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">El sistema incrementará automáticamente este número en cada factura emitida.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Actividad Económica</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="actividad_economica" value="<?= htmlspecialchars($config['actividad_economica']) ?>" placeholder="56101">
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="actividad_descripcion" value="<?= htmlspecialchars($config['actividad_descripcion']) ?>" placeholder="Servicios de restaurantes">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Dirección Fiscal</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="direccion" value="<?= htmlspecialchars($config['direccion']) ?>" placeholder="Calle, Número, Ciudad">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Teléfono y Email</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($config['telefono']) ?>" placeholder="021 000000">
                                </div>
                                <div class="col-sm-4">
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($config['email']) ?>" placeholder="facturacion@empresa.com.py">
                                </div>
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA: FIRMA DIGITAL Y PARÁMETROS SIFEN -->
                        <div class="col-md-6">
                            <h5 class="text-primary font-weight-bold mb-2">
                                <i class="fa fa-key"></i> 2. Firma Digital y Código Secreto (CSC)
                            </h5>

                            <!-- Estado del Certificado -->
                            <div class="card bg-light mb-3 border">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark"><i class="fa fa-shield"></i> Certificado Digital (.p12 / .pfx):</strong>
                                        <?php if (!empty($cert_info) && $cert_info['status']) { ?>
                                            <span class="badge badge-success"><i class="fa fa-check-circle"></i> VÁLIDO</span>
                                        <?php } elseif (!empty($config['cert_file'])) { ?>
                                            <span class="badge badge-warning"><i class="fa fa-exclamation-triangle"></i> REVISAR CONTRASEÑA</span>
                                        <?php } else { ?>
                                            <span class="badge badge-secondary">NO CARGADO</span>
                                        <?php } ?>
                                    </div>

                                    <?php if (!empty($cert_info) && $cert_info['status']) { ?>
                                        <div class="font-small-3">
                                            <div><strong>Titular:</strong> <?= htmlspecialchars($cert_info['titular']) ?></div>
                                            <div><strong>Emisor CA:</strong> <?= htmlspecialchars($cert_info['emisor']) ?></div>
                                            <div><strong>Válido hasta:</strong> <?= $cert_info['valido_hasta'] ?> 
                                                <span class="badge badge-info"><?= $cert_info['dias_restantes'] ?> días restantes</span>
                                            </div>
                                        </div>
                                    <?php } elseif (!empty($cert_info) && !$cert_info['status']) { ?>
                                        <div class="text-danger font-small-3">
                                            <i class="fa fa-times-circle"></i> <?= htmlspecialchars($cert_info['message']) ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="text-muted font-small-3">
                                            Cargue su certificado digital tributario emitido por una Prestadora de Servicios de Certificación acreditada en Paraguay (Code100, Digifirm, etc.).
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Subir Certificado</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control-file" name="cert_file" accept=".p12,.pfx">
                                    <small class="form-text text-muted">Archivo con extensión <strong>.p12</strong> o <strong>.pfx</strong>. Se almacenará en un directorio privado con acceso protegido.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Contraseña Certificado</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="cert_pass" name="cert_pass" placeholder="<?= (!empty($config['cert_pass']) ? '•••••••••••• (Conservar actual)' : 'Contraseña del archivo .p12') ?>">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassVisibility()">
                                                <i class="fa fa-eye" id="pass_icon"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info" onclick="testCertificadoAJAX()">
                                                <i class="fa fa-check"></i> Probar
                                            </button>
                                        </div>
                                    </div>
                                    <div id="cert_test_result" class="mt-1" style="display:none;"></div>
                                </div>
                            </div>

                            <hr>

                            <h5 class="text-primary font-weight-bold mb-2">
                                <i class="fa fa-qrcode"></i> 3. Código Secreto del Contribuyente (CSC)
                            </h5>
                            <p class="font-small-3 text-muted">
                                Requerido para la generación de la firma hash SHA-256 del Código QR oficial que se imprime en el ticket térmico KuDE. Este código se obtiene en el Sistema Marangatú de la DNIT.
                            </p>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">ID del CSC</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control font-weight-bold" name="csc_id" value="<?= htmlspecialchars($config['csc_id'] ?: '0001') ?>" placeholder="Ej: 0001" required>
                                    <small class="form-text text-muted">Identificador del CSC asignado por la DNIT (generalmente 0001 o 1).</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Valor del CSC</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control font-family-monospace" name="csc_val" value="<?= htmlspecialchars($config['csc_val']) ?>" placeholder="Ej: ABCD0000000000000000000000000000" required>
                                    <small class="form-text text-muted">Clave alfanumérica de 32 caracteres generada en Marangatú.</small>
                                </div>
                            </div>

                            <!-- Herramienta de Prueba KuDE -->
                            <div class="card border-info bg-light-info mt-3">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-info"><i class="fa fa-flask"></i> Verificador KuDE & QR:</strong>
                                            <div class="font-small-3 text-muted">Simula la generación de un CDC de 44 dígitos y el QR de SIFEN con los datos configurados.</div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-info" onclick="probarSampleKuDE()">
                                            <i class="fa fa-play"></i> Simular KuDE
                                        </button>
                                    </div>
                                    <div id="sample_kude_output" class="mt-2" style="display:none;"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-lg btn-success font-weight-bold px-3">
                                <i class="fa fa-save"></i> Guardar Configuración SIFEN
                            </button>
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-lg btn-secondary ml-1">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Algoritmo oficial de Módulo 11 de Paraguay para calcular DV del RUC
    function calcularDV() {
        var ruc = document.getElementById('ruc').value.replace(/[^0-9]/g, '');
        if (ruc.length === 0) {
            document.getElementById('dv').value = '';
            return;
        }
        var k = 2;
        var total = 0;
        for (var i = ruc.length - 1; i >= 0; i--) {
            total += parseInt(ruc.charAt(i), 10) * k;
            k++;
            if (k > 11) k = 2;
        }
        var resto = total % 11;
        var dv = (resto > 1) ? (11 - resto) : 0;
        document.getElementById('dv').value = dv;
    }

    function togglePassVisibility() {
        var input = document.getElementById('cert_pass');
        var icon = document.getElementById('pass_icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function testCertificadoAJAX() {
        var resultDiv = document.getElementById('cert_test_result');
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<span class="text-muted"><i class="fa fa-spinner fa-spin"></i> Verificando certificado...</span>';

        fetch('<?= base_url("sifen_config/test_cert") ?>')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.status) {
                    resultDiv.innerHTML = '<div class="alert alert-success p-1 font-small-3 mb-0">' +
                        '<strong><i class="fa fa-check"></i> Certificado Válido</strong><br>' +
                        'Titular: ' + data.titular + '<br>' +
                        'Emisor: ' + data.emisor + '<br>' +
                        'Vence: ' + data.valido_hasta + ' (' + data.dias_restantes + ' días)' +
                        '</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger p-1 font-small-3 mb-0">' +
                        '<strong><i class="fa fa-times"></i> Error:</strong> ' + data.message +
                        '</div>';
                }
            })
            .catch(function(err) {
                resultDiv.innerHTML = '<div class="alert alert-danger p-1 font-small-3 mb-0">Error de conexión: ' + err + '</div>';
            });
    }

    function probarSampleKuDE() {
        var out = document.getElementById('sample_kude_output');
        out.style.display = 'block';
        out.innerHTML = '<span class="text-muted"><i class="fa fa-spinner fa-spin"></i> Generando prueba KuDE...</span>';

        fetch('<?= base_url("sifen_config/test_sample") ?>')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.status) {
                    var html = '<div class="card bg-white p-2 border">' +
                        '<div class="row align-items-center">' +
                        '<div class="col-8">' +
                        '<div class="font-small-3"><strong>N° Factura:</strong> ' + data.numero + '</div>' +
                        '<div class="font-small-3"><strong>CDC (44 dígitos):</strong></div>' +
                        '<div style="font-family: monospace; font-size: 11px; background:#f4f4f4; padding: 4px; border-radius: 4px; word-break: break-all;">' + data.cdc_formato + '</div>' +
                        '<div class="mt-1 font-small-2 text-muted"><a href="' + data.qr_url + '" target="_blank" rel="noopener">Ver enlace de consulta DNIT</a></div>' +
                        '</div>' +
                        '<div class="col-4 text-center">' +
                        (data.qr_preview ? '<img src="' + data.qr_preview + '" alt="QR KuDE" style="max-width:120px; border:1px solid #ccc; padding:2px;" />' : '') +
                        '<div class="font-small-2 text-muted mt-1">QR KuDE Oficial</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    out.innerHTML = html;
                } else {
                    out.innerHTML = '<div class="text-danger font-small-3">Error al generar prueba</div>';
                }
            })
            .catch(function(err) {
                out.innerHTML = '<div class="text-danger font-small-3">Error: ' + err + '</div>';
            });
    }
</script>
